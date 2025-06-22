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
        // Добавляем новые поля в существующую таблицу assignments
        Schema::table('assignments', function (Blueprint $table) {
            $table->integer('max_score')->after('due_date')->default(100);
            $table->renameColumn('due_date', 'deadline');
        });

        // Создаем таблицу для файлов заданий
        Schema::create('assignment_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->bigInteger('size');
            $table->timestamps();
        });

        // Создаем таблицу связи заданий с группами
        Schema::create('assignment_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('max_score');
            $table->renameColumn('deadline', 'due_date');
        });

        Schema::dropIfExists('assignment_group');
        Schema::dropIfExists('assignment_files');
    }
}; 