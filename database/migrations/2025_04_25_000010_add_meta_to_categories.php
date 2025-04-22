<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::table('categories', function (Blueprint $t) {
			$t->string('h1')->nullable();
			$t->string('meta_title')->nullable();
			$t->text('meta_description')->nullable();
			$t->text('meta_keywords')->nullable();
		});
	}
	public function down(): void {
		Schema::table('categories', function (Blueprint $t) {
			$t->dropColumn(['h1','meta_title','meta_description','meta_keywords']);
		});
	}
};
