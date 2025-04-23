<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::create('parse_errors', function (Blueprint $t) {
			$t->id();
			$t->foreignId('parse_link_id')->constrained('parse_links')->cascadeOnDelete();
			$t->string('stage');				// category | product
			$t->text('message');
			$t->timestamps();
		});
	}
	public function down(): void {
		Schema::dropIfExists('parse_errors');
	}
};
