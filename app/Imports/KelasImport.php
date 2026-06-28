<?php

namespace App\Imports;

use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToCollection, WithHeadingRow
{
    public int $successCount = 0;
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        $seenNama = [];

        $existingNama = DB::table('kelas')
            ->whereNull('deleted_at')
            ->pluck('nama')
            ->map(fn($n) => strtolower((string) $n))
            ->flip()
            ->toArray();

        $validData = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $namaKelas = trim((string) ($row['nama_kelas'] ?? ''));

            if ($namaKelas === '') {
                continue;
            }

            // Abaikan baris panduan/instruksi dari template
            if (str_starts_with($namaKelas, 'Nama kelas, maks.')) {
                continue;
            }
            if (strlen($namaKelas) > 50) {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Nama kelas terlalu panjang (maks. 50 karakter).'];
                continue;
            }

            $namaKey = strtolower($namaKelas);

            if (isset($seenNama[$namaKey])) {
                $this->errors[] = ['row' => $rowNumber, 'message' => "Kelas \"{$namaKelas}\" duplikat dalam file (baris {$seenNama[$namaKey]})."];
                continue;
            }
            if (isset($existingNama[$namaKey])) {
                $this->errors[] = ['row' => $rowNumber, 'message' => "Kelas \"{$namaKelas}\" sudah ada di database."];
                continue;
            }

            $seenNama[$namaKey] = $rowNumber;
            // Kita tidak perlu mengupdate $existingNama karena kita mengecek file dan db sekaligus
            
            $validData[] = ['nama' => $namaKelas];
        }

        if (empty($this->errors)) {
            foreach ($validData as $data) {
                Kelas::create($data);
                $this->successCount++;
            }
        }
    }
}
