<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PresensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $data;
    protected $tanggal;

    public function __construct($data, $tanggal)
    {
        $this->data = $data;
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        return $this->data;
    }

    // Mengatur Nama Header Kolom di Excel
    public function headings(): array
    {
        return [
            'Kelas',
            'NIM',
            'Nama Mahasiswa',
            'Waktu Scan',
            'Petugas Scanner',
            'Status'
        ];
    }

    // Mengatur Isi Data per Baris
    public function map($row): array
    {
        // Logika penentuan petugas
        $petugas = $row->nama_petugas ?? ($row->waktu_scan ? 'ADMIN / SISTEM' : '-');
        
        // Format waktu
        $waktu = $row->waktu_scan ? \Carbon\Carbon::parse($row->waktu_scan)->format('H:i:s') : '-';
        
        // Format status agar KAPITAL tanpa underscore
        $status = strtoupper(str_replace('_', ' ', $row->status_kehadiran));

        return [
            $row->kelas,
            // Tambahkan kutip (') di awal NIM agar Excel tidak merubahnya menjadi angka scientific (misal: 4.22E+10)
            $row->nim, 
            $row->nama_mhs,
            $waktu,
            $petugas,
            $status
        ];
    }
}