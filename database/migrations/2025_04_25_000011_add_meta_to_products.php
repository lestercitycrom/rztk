<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::table('products', function (Blueprint $t) {
			$t->json('images')->nullable();
			$t->string('h1')->nullable();
			$t->string('meta_title')->nullable();
			$t->text('meta_description')->nullable();
			$t->text('meta_keywords')->nullable();
			$t->text('short_description')->nullable();
		});
	}
	public function down(): void {
		Schema::table('products', function (Blueprint $t) {
			$t->dropColumn([
				'images','h1','meta_title',
				'meta_description','meta_keywords','short_description'
			]);
		});
	}
};
