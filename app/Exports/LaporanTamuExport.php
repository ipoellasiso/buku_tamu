<?php

namespace App\Exports;

use App\Services\KunjunganGabungan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanTamuExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private int $no = 0;

    public function __construct(private Collection $data) {}

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal Daftar', 'Nama Tamu', 'Instansi', 'No. HP', 'Keperluan',
                'Yang Dituju', 'Waktu', 'Selesai', 'Sumber', 'Status'];
    }

    public function map($v): array
    {
        return [
            ++$this->no,
            $v->dibuat?->format('d/m/Y H:i'),
            $v->nama,
            $v->instansi,
            $v->hp,
            $v->keperluan,
            $v->tujuan,
            $v->waktu?->format('d/m/Y H:i') ?? '-',
            $v->selesai?->format('d/m/Y H:i') ?? '-',
            KunjunganGabungan::sumberLabel($v->sumber),
            KunjunganGabungan::label($v->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E2A78']],
            ],
        ];
    }
}