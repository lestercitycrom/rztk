<?php
namespace App\Services;

use App\Models\{ParseLink, Product, Category, Brand, Attribute, ProductAttribute, Setting, ParseError};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;


class RozetkaParser
{
	
	public function run(): void
	{
		$delay   = (int)Setting::value('request_delay', 1000);			// ms
		$details = (int)Setting::value('details_per_category', 5);		// det.pages
		$window  = (int)config('rozetka.runner_window', 50);		

		$started = microtime(true);										

		while (microtime(true) - $started < $window) {			
			// всегда берём самую голодную ссылку
			$link = ParseLink::where('is_active', true)
				->orderBy('last_parsed_at')
				->first();

			if (!$link) break;	// нет активных ссылок

			try {
				$this->parseCategoryPage($link);
				usleep($delay * 1000);
				$this->parseProductDetails($details, $delay);
			} catch (\Throwable $e) {
				$this->handleError($link,'category',$e);
			}
		}
	}
	
	/**
	 * Parse next page of category or seller link,
	 * с универсальным JSON-фолбэком для плиток товаров.
	 */
	public function parseCategoryPage(ParseLink $link): void
	{
		$page = $link->last_parsed_page + 1;
		$url  = $link->url . ($page > 1 ? (Str::contains($link->url, '?') ? '&' : '?')."page={$page}" : '');

		$html = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url)->body();
		$crawler = new Crawler($html);

		// meta
		$meta = [
			'h1'				=> trim($crawler->filter('h1')->text('')),
			'meta_title'		=> trim($crawler->filter('title')->text('')),
			'title'		=> trim($crawler->filter('title')->text('')),
			'meta_description'	=> trim($crawler->filter('meta[name=description]')->attr('content') ?? ''),
			'meta_keywords'		=> trim($crawler->filter('meta[name=keywords]')->attr('content') ?? ''),
		];
		$link->fill($meta);

		$data = $this->fetchRozetkaJson( $html );
		$data = $this->decodeKeysAndValues( $data );

		// === SEARCH FOR BLOCKS ===
		$sellerData = $categoryData = $type = null;
		
		foreach ($data as $key => $item) {
			// seller page block
			if (strpos($key, 'G.https://search.rozetka/ua/seller/api/v7/') === 0) {
				if (!isset($item['body'])) {
					throw new \Exception('Seller block has no body');
				}
				$sellerData = json_decode($item['body'], true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					throw new \Exception('Seller body JSON error: ' . json_last_error_msg());
				}
				
				$type = 'vendor';
				$goods = $sellerData["data"]["goods"];
				
				break;
			}
			// category page block
			if (strpos($key, 'G.https://xl-catalog-api.rozetka/v4/goods/getDetails') === 0) {
				if (!isset($item['body'])) {
					throw new \Exception('Category block has no body');
				}
				$categoryData = json_decode($item['body'], true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					throw new \Exception('Category body JSON error: ' . json_last_error_msg());
				}
				
				$type = 'category';
				$goods = $categoryData["data"];
				
				break;
			}
		}

		if ( is_null( $type )) throw new \Exception('Neither seller nor category block found');

		$link->type = $type;

		// сохраняем товары 
		foreach ($goods as $g) {

			Product::updateOrCreate(
				['rozetka_id' => $g['id']],
				[
					'title'		   => $g['title'],
					'url'		   => $g['href'],
					'parse_link_id' => $link->id,
					'price'		   => $g['price'] ?? 0,
					'old_price'	   => $g['old_price'] ?? 0,
					'image_url'	   => $g['image_main'] ?? null,
				]
			);

			// бренд
			if (!empty($g['brand_id'])) {
				$brand = Brand::firstOrCreate(
					['rozetka_id' => $g['brand_id']],
					['name' => $g['brand']]
				);
				Product::where('rozetka_id',$g['id'])->update(['brand_id'=>$brand->id]);
			}
		}

		$link->total_pages		= $this->getLastPageNumber($html);
		$link->last_parsed_page = $page >= $link->total_pages ? 0 : $page;
		$link->last_parsed_at	= now();
		$link->status			= 'success';
		$link->status_message			= '';
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
				try {
					$this->parseProduct($p);
				} catch (\Throwable $e) {
					$this->handleError($p->parseLink,'product',$e);
					
				}
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

		$crawler = new Crawler($html);


		// ── Все URL изображений ────────────────────────────────
		$images = collect($info['images'] ?? [])
			->pluck('original.url')
			->map(fn($u) => $this->decodeLinksInData($u))
			->values()
			->all();


		$product->update([
			'price'		 => (int)($info['price'] ?? $product->price),
			'old_price'	 => (int)($info['old_price'] ?? 0),
			'currency'	 => 'UAH',
			'brand'		 => $info['brand'] ?? $product->brand,
			
	'images'		 => $images,
	'image_url'		 => $images[0] ?? null,			
			
			/*
			'images'	 => collect($info['images'] ?? [])
				->pluck('original.url')->values()->all(),
			'image_url'	 => $info['images'][0]['original']['url'] ?? $product->image_url,
			*/
			'title'		 => $info['title'] ?? $product->title,
			'h1'		 => $info['title'] ?? $product->h1,
			'meta_title' => $info['seo']['Product']['name'] ?? null,
			// 'meta_description' => $info['seo']['Product']['description'] ?? null,
			
			'meta_description' => trim($crawler->filter('meta[name=description]')->attr('content') ?? ''),
			'meta_keywords'    => trim($crawler->filter('meta[name=keywords]')->attr('content') ?? ''),			
			
			'short_description' => $info['description']['text'] ?? null,
			'description'		=> $info['description']['html'] ?? $product->description,
			'in_stock'			=> ($info['sell_status'] ?? '') !== 'unavailable',
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



		// -------------- EXTRA ATTRIBUTES (config) --------------
		$extra = config('rozetka.extra_attributes', []);
		foreach ($extra as $jsonKey => $attrName) {
			if (isset($info[$jsonKey]) && $info[$jsonKey] !== '') {
				$this->upsertAttribute($product, $attrName, (string)$info[$jsonKey]);
			}
		}


		/* ---------- characteristics ---------- */


/*
		if (isset($data['data']['attributesValues'])) {
			foreach ($data['data']['attributesValues'] as $row) {
				$this->upsertAttribute($product, $row['title'] ?? '', $row['value'] ?? '');
			}
		}

		// JSON blob
		if (isset($data['characteristics']) && is_array($data['characteristics'])) {
			foreach ($data['characteristics'] as $row) {
				$this->upsertAttribute($product, $row['name'] ?? '', $row['value'] ?? '');
			}
		}
*/
		// HTML fallback
		
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
			return str_replace(
				['$hs$','$ht$','$dt$','$sh$', '$qr$'],
				['https://','http://','.','/','/'],
				$data
			);
		}
		if (is_array($data)) foreach ($data as $k=>$v)
			$data[$k] = $this->decodeLinksInData($v);
		return $data;
	}

	private function decodeLink(string $link): string {
		$map = [
			'$hs$' => 'https://',
			'$ht$' => 'http://',
			'$dt$' => '.',
			'$sh$' => '/',
			'$qr$' => '?',
		];
		return str_replace(array_keys($map), array_values($map), $link);
	}


	private function fetchRozetkaJson(string $html, string $scriptId = 'rz-client-state'): array {

		$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

		// parse DOM
		libxml_use_internal_errors(true);
		$dom   = new \DOMDocument();
		$dom->loadHTML($html);
		libxml_clear_errors();
		$xpath = new \DOMXPath($dom);

		// select only the script with given id
		$node = $xpath->query("//script[@id='{$scriptId}']")->item(0);
		if (!$node) {
			throw new \Exception("Script with id=\"{$scriptId}\" not found");
		}

		// clean JSON
		$jsonRaw = $node->textContent;
		$jsonRaw = str_replace('&q;', '"', $jsonRaw);
		$jsonRaw = html_entity_decode($jsonRaw, ENT_QUOTES | ENT_XML1, 'UTF-8');

		// decode JSON
		$data = json_decode($jsonRaw, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \Exception('JSON decode error: ' . json_last_error_msg());
		}

		return $data;
	}


	/**
	 * Recursively decode keys & values in array.
	 */
	private function decodeKeysAndValues($data) {
		if (!is_array($data)) {
			return is_string($data) ? $this->decodeLink($data) : $data;
		}
		$result = [];
		foreach ($data as $key => $value) {
			$newKey = is_string($key) ? $this->decodeLink($key) : $key;
			$result[$newKey] = $this->decodeKeysAndValues($value);
		}
		return $result;
	}


	/**
	 * Find maximum page number in pagination links.
	 */
	private function getLastPageNumber(string $html): int {

		if ( preg_match_all('#[/?]page=(\d+)[^>]+>\d+#', $html, $sub) ) {
			return (int) $sub[1][ count($sub[1])-1 ];
		} else return 0;		

	}





	private function handleError(ParseLink $link, string $stage, \Throwable $e): void
	{
		$link->update([
			'status'		 => 'error',
			'status_message' => $e->getMessage(),
			'last_parsed_at'     => now(),
		]);
		
		ParseError::create($data_error = [
			'parse_link_id' => $link->id,
			'stage'			=> $stage,
			'message'		=> $e->getMessage(),
			'file' => $e->getFile(),        
			'line' => $e->getLine(),      
		]);
		
		Log::error('Parse error', ['msg' => $e->getMessage(), 'url' => $link->url]);
		var_dump( $data_error );
		// exit();
	}


}
