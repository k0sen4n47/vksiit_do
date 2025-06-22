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
        Schema::create('assignment_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->integer('page_number');
            $table->string('type'); // text, code, file, presentation
            $table->json('content'); // Содержимое страницы в зависимости от типа
            $table->timestamps();

            // Уникальный индекс для порядка страниц в задании
            $table->unique(['assignment_id', 'page_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_pages');
    }
}; 