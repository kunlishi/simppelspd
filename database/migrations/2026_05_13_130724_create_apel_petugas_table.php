<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('apel_petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apel_id')->constrained('apel')->onDelete('cascade');
            // Merujuk ke kolom 'nas' di tabel 'spd'
            $table->string('spd_nas');
            $table->foreign('spd_nas')->references('nas')->on('spd')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apel_petugas');
    }
};
