<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_clubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('place');
            $table->unsignedTinyInteger('order')->default(0);
            $table->unique(['result_id', 'club_id', 'place']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_clubs');
    }
};