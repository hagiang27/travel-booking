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
        Schema::table('don_dat', function (Blueprint $table) {
            $table->dateTime('hold_expires_at')
                ->nullable()
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_dat', function (Blueprint $table) {
            $table->dropIndex(['hold_expires_at']);
            $table->dropColumn('hold_expires_at');
        });
    }
};