<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::create('brands', function (Blueprint $t) {
			$t->id();
			$t->unsignedBigInteger('rozetka_id')->unique();
			$t->string('name');
			$t->string('url')->nullable();
			$t->timestamps();
		});

		Schema::table('products', function (Blueprint $t) {
			$t->foreignId('brand_id')->nullable()->constrained('brands');
		});
	}
	public function down(): void {
		Schema::table('products', fn($t) => $t->dropForeign(['brand_id']));
		Schema::dropIfExists('brands');
	}
};
