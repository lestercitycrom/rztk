<?php
namespace App\Services;

use App\Models\{ParseLink, Product, Category, Attribute, ProductAttribute, Setting};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class RozetkaParser
{
	/** Main scheduler entry */
	public function run(): void
	{
		$delay   = Setting::value('request_delay', 1000);		// ms
		$details = Setting::value('details_per_category', 5);	// det. pages

		ParseLink::where('is_active', true)
			->orderBy('last_parsed_at')
			->each(function (ParseLink $link) use ($delay, $details) {
				try {
					$this->parseCategoryPage($link);
					usleep($delay * 1000);
					$this->parseProductDetails($details, $delay);
				} catch (\Throwable $e) {
					$link->update([
						'status'         => 'error',
						'status_message' => $e->getMessage(),
					]);
					Log::error('Parse error', ['msg' => $e->getMessage(), 'url' => $link->url]);
				}
			});
	}

	/** Parse next page of category / seller */
	public function parseCategoryPage(ParseLink $link): void
	{
		$page = $link->last_parsed_page + 1;
		$url  = $link->url . ($page > 1 ? (Str::contains($link->url, '?') ? '&' : '?') . "page={$page}" : '');

		$response = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
		if (!$response->ok()) throw new \Exception('HTTP ' . $response->status());

		$html	 = $response->body();
		$crawler = new Crawler($html);

		// store title once
		if (blank($link->title)) $link->title = trim($crawler->filter('title')->text(''));

		// detect last page
		$last = (int)$crawler->filter('ul.pagination__list li.pagination__item')->last()->text('');
		$link->total_pages = max($last, 1);

		// tiles
		$crawler->filter('.goods-tile')->each(function (Crawler $node) use ($link) {
			$title = trim($node->filter('.goods-tile__title')->text(''));
			$href  = $node->filter('.goods-tile__title')->closest('a')->attr('href');

			preg_match('/p(\d+)\//', $href, $m);
			$rid = $m[1] ?? null;
			if (!$rid) return;

			$price = (int)preg_replace('/\D/', '', $node->filter('.goods-tile__price-value')->text(''));

			Product::updateOrCreate(
				['rozetka_id' => $rid],
				[
					'title'		   => $title,
					'url'		   => $href,
					'parse_link_id' => $link->id,
					'price'		   => $price,
					'in_stock'	   => Str::contains(Str::lower($node->text()), 'наяв'),
				]
			);
		});

		// update cursor
		$link->last_parsed_page = $page >= $link->total_pages ? 0 : $page;
		$link->last_parsed_at	= now();
		$link->status			= 'success';
		$link->save();
	}

	/** Detail pages */
	public function parseProductDetails(int $limit, int $delay): void
	{
		Product::whereNull('last_detail_parsed_at')
			->orWhere('last_detail_parsed_at', '<', now()->subDays(7))
			->orderBy('last_detail_parsed_at')
			->limit($limit)
			->get()
			->each(function (Product $p) use ($delay) {
				$this->parseProduct($p);
				usleep($delay * 1000);
			});
	}

	/** Parse one product */
	public function parseProduct(Product $product): void
	{
		$html = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($product->url)->body();

		if (!preg_match('/<script\s+id="rz-client-state"[^>]*>(.*?)<\/script>/s', $html, $m))
			throw new \Exception('client‑state not found');

		$state = json_decode(html_entity_decode($m[1]), true);
		if (!$state) throw new \Exception('state json decode');

		$key = collect(array_keys($state))->first(
			fn($k) => Str::contains($k, 'goodsId=' . $product->rozetka_id)
		);
		if (!$key) throw new \Exception('goods key missing');

		$data = json_decode($state[$key]['body'] ?? '', true);
		if (!$data) throw new \Exception('body json decode');

		$data = $this->decodeLinksInData($data);				// ← decode $hs$‑links
		$info = $data['data'] ?? [];

		$product->update([
			'price'				 => (int)($info['price'] ?? $product->price),
			'old_price'			 => (int)($info['old_price'] ?? 0),
			'currency'			 => 'UAH',
			'brand'				 => $info['brand'] ?? $product->brand,
			'image_url'			 => $info['images'][0]['original']['url'] ?? $product->image_url,
			'description'		 => $info['description']['text'] ?? $product->description,
			'in_stock'			 => ($info['sell_status'] ?? '') !== 'unavailable',
			'last_detail_parsed_at' => now(),
		]);

		// category
		if (isset($info['category_id'])) {
			$cat = Category::firstOrCreate(
				['rozetka_id' => $info['category_id']],
				[
					'title' => $info['last_category']['title'] ?? '',
					'url'	=> $info['last_category']['href'] ?? null,
				]
			);
			$product->category()->associate($cat)->save();
		}

		/* ---------- characteristics ---------- */

		// JSON blob
		if (isset($data['characteristics']) && is_array($data['characteristics'])) {
			foreach ($data['characteristics'] as $row) {
				$this->upsertAttribute($product, $row['name'] ?? '', $row['value'] ?? '');
			}
		}

		// HTML fallback
		$crawler = new Crawler($html);
		$crawler->filter('.characteristics__list .characteristics__item')->each(
			function (Crawler $li) use ($product) {
				$name = trim($li->filter('.characteristics__label')->text(''));
				$val  = trim($li->filter('.characteristics__value')->text(''));
				if ($name && $val) $this->upsertAttribute($product, $name, $val);
			}
		);
	}

	/** Store attribute/value */
	private function upsertAttribute(Product $product, string $name, string $value): void
	{
		if ($name === '') return;
		$attr = Attribute::firstOrCreate(['name' => $name]);
		ProductAttribute::updateOrCreate(
			['product_id' => $product->id, 'attribute_id' => $attr->id],
			['value' => $value]
		);
	}

	/** Recursively decode Rozetka links ($hs$ → https://) */
	private function decodeLinksInData($data)
	{
		if (is_string($data) && str_contains($data, '$hs$')) {
			return str_replace(['$hs$', '$dt$', '$sh$'], ['https://', '.', '/'], $data);
		}
		if (is_array($data)) {
			foreach ($data as $k => $v) $data[$k] = $this->decodeLinksInData($v);
		}
		return $data;
	}
}
