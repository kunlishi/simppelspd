<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiPresensiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $jenis; // 'scan', 'update', atau 'alpa'
    public $mahasiswa;
    public $apel;
    public $status;

    public function __construct($jenis, $mahasiswa, $apel, $status)
    {
        $this->jenis = $jenis;
        $this->mahasiswa = $mahasiswa;
        $this->apel = $apel;
        $this->status = strtoupper(str_replace('_', ' ', $status));
    }

    public function build()
    {
        $subjek = '';
        if ($this->jenis == 'scan') {
            $subjek = '✅ Berhasil Presensi: ' . $this->apel->nama_apel;
        } elseif ($this->jenis == 'update') {
            $subjek = '⚠️ Perubahan Status Presensi: ' . $this->apel->nama_apel;
        } elseif ($this->jenis == 'alpa') {
            $subjek = '❌ Peringatan Tidak Hadir Apel: ' . $this->apel->nama_apel;
        }

        return $this->subject($subjek)
                    ->view('emails.presensi_notif'); // Mengarah ke file blade di bawah
    }
}
