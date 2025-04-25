<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parse_errors', function (Blueprint $table) {
            $table->string('file')->nullable()->after('message');
            $table->unsignedInteger('line')->nullable()->after('file');
        });
    }

    public function down(): void
    {
        Schema::table('parse_errors', function (Blueprint $table) {
            $table->dropColumn(['file', 'line']);
        });
    }
};
