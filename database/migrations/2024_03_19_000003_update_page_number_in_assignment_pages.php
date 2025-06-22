<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignment_pages', function (Blueprint $table) {
            // Изменяем поле page_number, чтобы оно было nullable
            $table->integer('page_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_pages', function (Blueprint $table) {
            // Возвращаем поле page_number к обязательному
            $table->integer('page_number')->nullable(false)->change();
        });
    }
}; 