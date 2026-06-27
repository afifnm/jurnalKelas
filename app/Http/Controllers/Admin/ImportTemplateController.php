<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportTemplateController extends Controller
{
    public function pengguna(): StreamedResponse
    {
        $headers = ['nama', 'kode_guru', 'email', 'telp'];
        $hints   = [
            'Nama lengkap guru, maks. 100 karakter (wajib)',
            'Kode unik guru, maks. 50 karakter (wajib)',
            'Alamat email (opsional)',
            'Nomor HP, maks. 20 karakter (opsional)',
        ];
        $petunjuk = [
            ['Kolom', 'Wajib', 'Keterangan'],
            ['nama', 'Ya', 'Nama lengkap guru, maks. 100 karakter'],
            ['kode_guru', 'Ya', 'Kode unik, maks. 50 karakter. Tidak boleh sama antar baris maupun dengan data existing'],
            ['email', 'Tidak', 'Format email valid. Jika diisi, tidak boleh sama dengan email yang sudah ada'],
            ['telp', 'Tidak', 'Nomor HP, maks. 20 karakter'],
            ['', '', ''],
            ['Catatan:', '', 'Password otomatis: 12345678'],
            ['', '', 'Role otomatis: Guru'],
        ];

        return $this->stream('template_import_pengguna', $headers, $hints, $petunjuk);
    }

    public function kelas(): StreamedResponse
    {
        $headers = ['nama_kelas'];
        $hints   = ['Nama kelas, maks. 50 karakter, harus unik (wajib)'];
        $petunjuk = [
            ['Kolom', 'Wajib', 'Keterangan'],
            ['nama_kelas', 'Ya', 'Nama kelas, maks. 50 karakter. Tidak boleh sama antar baris maupun dengan data existing'],
            ['', '', ''],
            ['Contoh:', '', 'X RA, X RB, XI RA, XII RPL'],
        ];

        return $this->stream('template_import_kelas', $headers, $hints, $petunjuk);
    }

    public function mapel(): StreamedResponse
    {
        $headers = ['nama_mapel', 'kode_mapel'];
        $hints   = [
            'Nama mata pelajaran, maks. 100 karakter (wajib)',
            'Kode unik mapel, maks. 20 karakter (wajib)',
        ];
        $petunjuk = [
            ['Kolom', 'Wajib', 'Keterangan'],
            ['nama_mapel', 'Ya', 'Nama mata pelajaran, maks. 100 karakter'],
            ['kode_mapel', 'Ya', 'Kode unik, maks. 20 karakter. Tidak boleh sama antar baris maupun dengan data existing'],
            ['', '', ''],
            ['Contoh nama_mapel:', '', 'Matematika, Bahasa Indonesia, Fisika'],
            ['Contoh kode_mapel:', '', 'MTK, BI, FIS'],
        ];

        return $this->stream('template_import_mapel', $headers, $hints, $petunjuk);
    }

    private function stream(string $filename, array $headers, array $hints, array $petunjuk): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // ── Sheet 1: Data ───────────────────────────────────────────────
        $dataSheet = $spreadsheet->getActiveSheet();
        $dataSheet->setTitle('Data');

        $colCount  = count($headers);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        // Row 1 — header names
        foreach ($headers as $i => $header) {
            $dataSheet->getCellByColumnAndRow($i + 1, 1)->setValue($header);
        }

        $dataSheet->getStyle("A1:{$lastColLetter}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 11,
                'color' => ['argb' => 'FF1C1917'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFACC15'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD97706'],
                ],
            ],
        ]);

        // Row 2 — hint/description per column
        foreach ($hints as $i => $hint) {
            $dataSheet->getCellByColumnAndRow($i + 1, 2)->setValue($hint);
        }

        $dataSheet->getStyle("A2:{$lastColLetter}2")->applyFromArray([
            'font' => [
                'italic' => true,
                'size'   => 9,
                'color'  => ['argb' => 'FF64748B'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFDBEAFE'],
            ],
            'alignment' => [
                'wrapText'   => true,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['argb' => 'FF93C5FD'],
                ],
            ],
        ]);

        // Rows 3–52 — empty data rows with light border
        $dataSheet->getStyle("A3:{$lastColLetter}52")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color'       => ['argb' => 'FFE2E8F0'],
                ],
            ],
        ]);

        // Row heights
        $dataSheet->getRowDimension(1)->setRowHeight(22);
        $dataSheet->getRowDimension(2)->setRowHeight(36);

        // Column widths
        foreach (range(1, $colCount) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $dataSheet->getColumnDimension($colLetter)->setWidth(28);
        }

        // Freeze rows 1–2 so headers stay visible when scrolling
        $dataSheet->freezePane('A3');

        // ── Sheet 2: Petunjuk ───────────────────────────────────────────
        $petunjukSheet = $spreadsheet->createSheet();
        $petunjukSheet->setTitle('Petunjuk');

        foreach ($petunjuk as $rowIndex => $rowData) {
            foreach ($rowData as $colIndex => $value) {
                $petunjukSheet->getCellByColumnAndRow($colIndex + 1, $rowIndex + 1)->setValue($value);
            }
        }

        // Style header row of petunjuk table
        $petunjukSheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Column widths for petunjuk sheet
        $petunjukSheet->getColumnDimension('A')->setWidth(20);
        $petunjukSheet->getColumnDimension('B')->setWidth(10);
        $petunjukSheet->getColumnDimension('C')->setWidth(70);

        // Auto-wrap for column C
        $petunjukSheet->getStyle('C1:C' . (count($petunjuk)))->getAlignment()->setWrapText(true);

        // Borders for petunjuk table
        $petunjukSheet->getStyle('A1:C' . count($petunjuk))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFCBD5E1'],
                ],
            ],
        ]);

        // Activate Data sheet by default
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
                'Cache-Control'       => 'max-age=0',
            ]
        );
    }
}
