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
            $table->longText('answer_html')->nullable()->after('answer_text');
            $table->longText('answer_css')->nullable()->after('answer_html');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_student_answers', function (Blueprint $table) {
            $table->dropColumn(['answer_html', 'answer_css']);
        });
    }
};
