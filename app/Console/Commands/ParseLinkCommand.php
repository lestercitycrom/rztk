<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RozetkaParser;
use App\Models\{ParseLink, Setting};

class ParseLinkCommand extends Command
{
	protected $signature	= 'rozetka:parse-link {id}';
	protected $description	= 'Parse single link immediately';

	public function handle(RozetkaParser $parser)
	{
		$link = ParseLink::findOrFail($this->argument('id'));
		$this->info('Parsing ' . $link->url);

		$parser->parseCategoryPage($link);
		$parser->parseProductDetails(
			Setting::value('details_per_category', 5),
			Setting::value('request_delay', 1000)
		);

		$this->info('Done');
	}
}
