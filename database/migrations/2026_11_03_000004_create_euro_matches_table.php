<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('euro_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('euro_season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('euro_tie_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('stage', 30);
            $table->string('group', 5)->nullable();
            $table->unsignedTinyInteger('leg')->default(1);
            $table->date('played_at')->nullable();
            $table->foreignId('home_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('away_club_id')->constrained('clubs')->cascadeOnDelete();
            // Score after regular + extra time. Penalties go to `note`.
            $table->unsignedTinyInteger('home_goals');
            $table->unsignedTinyInteger('away_goals');
            $table->boolean('extra_time')->default(false);
            $table->boolean('neutral')->default(false);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['euro_season_id', 'stage', 'group']);
            $table->index('home_club_id');
            $table->index('away_club_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('euro_matches');
    }
};
