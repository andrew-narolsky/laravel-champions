<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('from_year')->nullable();
            $table->unsignedSmallInteger('to_year')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['club_id', 'from_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_names');
    }
};
