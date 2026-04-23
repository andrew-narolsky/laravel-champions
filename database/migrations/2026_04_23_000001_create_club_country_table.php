<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_country', function (Blueprint $table) {
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->primary(['club_id', 'country_id']);
        });

        DB::table('clubs')
            ->whereNotNull('country_id')
            ->get()
            ->each(fn($club) => DB::table('club_country')->insert([
                'club_id'    => $club->id,
                'country_id' => $club->country_id,
            ]));

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->constrained()->cascadeOnDelete();
        });

        DB::table('club_country')->get()->each(fn($row) => DB::table('clubs')
            ->where('id', $row->club_id)
            ->update(['country_id' => $row->country_id])
        );

        Schema::dropIfExists('club_country');
    }
};