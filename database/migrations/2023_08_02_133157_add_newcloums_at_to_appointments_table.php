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
        if (!Schema::hasColumn('appointments', 'status')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->boolean('status')->nullable()->default(0);
            });
        }
        if (!Schema::hasColumn('appointments', 'meeting_status')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->boolean('meeting_status')->nullable()->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('meeting_status');
            $table->dropColumn('status');
        });
    }
};
