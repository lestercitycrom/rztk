<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RozetkaParser;

class RunParser extends Command
{
	protected $signature	= 'rozetka:parse';
	protected $description	= 'Run Rozetka parser cycle';

	public function handle(RozetkaParser $parser)
	{
		$this->info('Parser started');
		$parser->run();
		$this->info('Parser finished');
	}
}
