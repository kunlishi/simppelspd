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
        Schema::create('apel_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apel_id')->constrained('apel')->onDelete('cascade');
            // Asumsi kamu punya tabel kelas. Jika kelas hanya berupa string, ganti jadi $table->string('kelas');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apel_kelas');
    }
};
