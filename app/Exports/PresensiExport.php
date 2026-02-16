<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class PresensiExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    protected $data;
    protected $tanggal;

    public function __construct($data, $tanggal)
    {
        $this->data = $data->map(function ($item) {
            return [
                'Hari/Tanggal' => $item->tanggal ? Carbon::parse($item->tanggal)->translatedFormat('l, d-m-Y') : '-',
                'NIM' => $item->nim,
                'Nama Mahasiswa' => $item->nama,
                'Kelas' => $item->kelas,
                'Status' => strtoupper(str_replace('_', ' ', $item->status)),
                'Petugas Scanner' => $item->nama_petugas ?? 'Sistem',
            ];
        });

        $this->tanggal = $tanggal;
    }

    public function collection() { return $this->data; }

    public function headings(): array
    {
        return ['Hari/Tanggal', 'NIM', 'Nama Mahasiswa', 'Kelas', 'Status', 'Petugas Scanner'];
    }

    public function startCell(): string { return 'A3'; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $title = 'Laporan Kehadiran Apel - Tanggal: ' . ($this->tanggal ?? 'Semua');
                $sheet->setCellValue('A1', $title);
                $sheet->mergeCells('A1:F1'); 
                $sheet->getStyle('A1')->getFont()->setBold(true);
            },
        ];
    }
}