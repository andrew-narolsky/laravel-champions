<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('nickname')->nullable();
            $table->string('description')->nullable();
            $table->text('content')->nullable();
            $table->string('founded_at')->nullable();
            $table->string('destroyed_at')->nullable();
            $table->string('stadium')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
