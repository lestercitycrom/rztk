<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->unsignedInteger('request_delay')->default(1000);
    $table->unsignedInteger('details_per_category')->default(5);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
