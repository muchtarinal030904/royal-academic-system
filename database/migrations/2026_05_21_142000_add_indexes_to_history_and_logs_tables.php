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
        Schema::table('print_histories', function (Blueprint $table) {
            $table->index('certificate_number');
            $table->index('printed_at');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_histories', function (Blueprint $table) {
            $table->dropIndex(['certificate_number']);
            $table->dropIndex(['printed_at']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['action']);
            $table->dropIndex(['created_at']);
        });
    }
};
