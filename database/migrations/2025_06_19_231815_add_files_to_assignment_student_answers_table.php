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
        Schema::table('assignment_student_answers', function (Blueprint $table) {
            $table->json('files')->nullable()->after('answer_text');
            $table->dropColumn('answer_code'); // Удаляем старое поле
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_student_answers', function (Blueprint $table) {
            $table->text('answer_code')->nullable()->after('answer_text');
            $table->dropColumn('files');
        });
    }
};
