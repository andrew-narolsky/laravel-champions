<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Point deductions / bonuses in group tables:
     * [{"club_id": 879, "stage": "group", "points": -2, "note": "..."}]
     */
    public function up(): void
    {
        Schema::table('euro_seasons', function (Blueprint $table) {
            $table->json('point_adjustments')->nullable()->after('points_for_win');
        });
    }

    public function down(): void
    {
        Schema::table('euro_seasons', function (Blueprint $table) {
            $table->dropColumn('point_adjustments');
        });
    }
};
