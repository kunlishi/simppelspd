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
            $table->foreignId("apel_id")->constrained("apel")->onDelete("cascade");
            $table->string("nim")->constrained("mahasiswas")->onDelete("cascade");
            $table->string("nama")->constrained("mahasiswas", "nim")->onDelete("cascade");
            $table->string("kelas")->constrained("mahasiswas", "nim")->onDelete("cascade");
            $table->enum("status", ["hadir", "terlambat", "izin", "kurang_cukup_bukti_izin", "sakit", "kurang_cukup_bukti_sakit", "tidak_hadir"])->default("tidak_hadir");
            $table->string("nama_petugas")->nullable()->constrained("users")->onDelete("set null");
            $table->timestamps();
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
