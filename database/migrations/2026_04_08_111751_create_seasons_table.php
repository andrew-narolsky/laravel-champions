<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['competition_id', 'name']);
            $table->index('competition_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
