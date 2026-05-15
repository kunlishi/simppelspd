<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel apel
            $table->foreignId("apel_id")->constrained("apel")->onDelete("cascade");
            
            // Relasi ke tabel mahasiswas (Menggunakan sintaks foreign key untuk tipe string)
            $table->string("nim");
            $table->foreign("nim")->references("nim")->on("mahasiswas")->onDelete("cascade");
            
            // Kolom status didefinisikan satu kali tanpa nilai default 'tidak_hadir'
            // karena baris data ini hanya tercipta saat mahasiswa benar-benar hadir/terlambat/izin/sakit
            $table->enum("status", ["hadir", "terlambat", "izin", "kurang_cukup_bukti_izin", "sakit", "kurang_cukup_bukti_sakit"]);
            
            // Relasi ke tabel spd untuk mencatat siapa petugas yang men-scan
            $table->string('petugas_nas')->nullable();
            $table->foreign('petugas_nas')->references('nas')->on('spd')->onDelete('set null');
            
            $table->timestamps();
            
            // Mencegah mahasiswa yang sama melakukan scan 2 kali di jadwal apel yang sama
            $table->unique(['apel_id', 'nim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
