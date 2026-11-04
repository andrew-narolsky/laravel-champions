<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('euro_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('euro_competition_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // Historical name of the competition in this season, e.g. "European Cup" for 1955-56.
            $table->string('competition_name')->nullable();
            // Points for a win in group tables of this season (2 before mid-90s, 3 after).
            $table->unsignedTinyInteger('points_for_win')->default(3);
            $table->foreignId('winner_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('runner_up_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->timestamps();

            $table->unique(['euro_competition_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('euro_seasons');
    }
};
