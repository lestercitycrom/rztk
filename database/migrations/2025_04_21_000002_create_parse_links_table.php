<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('parse_links', function (Blueprint $table) {
    $table->id();
    $table->string('url');
    $table->enum('type', ['category','vendor']);
    $table->string('title')->nullable();
    $table->unsignedInteger('total_pages')->default(0);
    $table->unsignedInteger('last_parsed_page')->default(0);
    $table->string('status')->default('pending');
    $table->string('status_message')->nullable();
    $table->timestamp('last_parsed_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('parse_links');
    }
};
