<?php

namespace App\Imports;

use App\Models\Mapel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MapelImport implements ToCollection, WithHeadingRow
{
    public int $successCount = 0;
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        $validData = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $namaMapel = trim((string) ($row['nama_mapel'] ?? ''));
            $kodeMapel = trim((string) ($row['kode_mapel'] ?? ''));

            if ($namaMapel === '' && $kodeMapel === '') {
                continue;
            }

            // Abaikan baris panduan/instruksi dari template
            if (str_starts_with(strtolower($namaMapel), 'nama mata pelajaran') || str_starts_with(strtolower($namaMapel), 'nama mapel') || str_starts_with($namaMapel, 'Nama ')) {
                continue;
            }

            if ($namaMapel === '') {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Kolom nama_mapel wajib diisi.'];
                continue;
            }
            if ($kodeMapel === '') {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Kolom kode_mapel wajib diisi.'];
                continue;
            }
            if (strlen($namaMapel) > 100) {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Nama mapel terlalu panjang (maks. 100 karakter).'];
                continue;
            }
            if (strlen($kodeMapel) > 20) {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Kode mapel terlalu panjang (maks. 20 karakter).'];
                continue;
            }


            $validData[] = ['nama' => $namaMapel, 'kode' => $kodeMapel];
        }

        if (empty($this->errors)) {
            foreach ($validData as $data) {
                Mapel::create($data);
                $this->successCount++;
            }
        }
    }
}
