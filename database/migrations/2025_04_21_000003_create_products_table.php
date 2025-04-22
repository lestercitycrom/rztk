<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('rozetka_id')->unique();
    $table->string('title');
    $table->string('url');
    $table->foreignId('category_id')->nullable()->constrained('categories');
    $table->integer('price')->nullable();
    $table->integer('old_price')->nullable();
    $table->string('currency',5)->default('UAH');
    $table->boolean('in_stock')->default(false);
    $table->string('brand')->nullable();
    $table->string('image_url')->nullable();
    $table->longText('description')->nullable();
    $table->timestamp('last_detail_parsed_at')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
