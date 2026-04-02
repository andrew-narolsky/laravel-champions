<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('module', 20);
            $table->unsignedInteger('module_id')->nullable();
            $table->string('filename');
            $table->string('path');
            $table->string('ext', 10);
            $table->enum('type', ['image', 'file']);
            $table->unsignedInteger('size');
            $table->timestamps();

            $table->index(['module', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
