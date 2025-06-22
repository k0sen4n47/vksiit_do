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
            if (!Schema::hasColumn('assignment_pages', 'order')) {
                $table->integer('order')->default(0)->after('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_pages', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_pages', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
}; 