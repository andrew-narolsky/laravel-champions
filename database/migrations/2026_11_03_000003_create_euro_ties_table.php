<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A knockout pairing (one or more matches). Group-stage matches have no tie.
     */
    public function up(): void
    {
        Schema::create('euro_ties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('euro_season_id')->constrained()->cascadeOnDelete();
            $table->string('stage', 30);
            $table->foreignId('home_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('away_club_id')->nullable()->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('winner_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->string('note')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['euro_season_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('euro_ties');
    }
};
