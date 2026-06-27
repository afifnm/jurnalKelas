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
        $seenKode = [];

        $existingKode = DB::table('mapel')
            ->whereNull('deleted_at')
            ->whereNotNull('kode')
            ->pluck('kode')
            ->map(fn($k) => strtolower((string) $k))
            ->flip()
            ->toArray();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $namaMapel = trim((string) ($row['nama_mapel'] ?? ''));
            $kodeMapel = trim((string) ($row['kode_mapel'] ?? ''));

            if ($namaMapel === '' && $kodeMapel === '') {
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

            $kodeKey = strtolower($kodeMapel);

            if (isset($seenKode[$kodeKey])) {
                $this->errors[] = ['row' => $rowNumber, 'message' => "Kode mapel \"{$kodeMapel}\" duplikat dalam file (baris {$seenKode[$kodeKey]})."];
                continue;
            }
            if (isset($existingKode[$kodeKey])) {
                $this->errors[] = ['row' => $rowNumber, 'message' => "Kode mapel \"{$kodeMapel}\" sudah ada di database."];
                continue;
            }

            $seenKode[$kodeKey] = $rowNumber;
            $existingKode[$kodeKey] = true;

            Mapel::create(['nama' => $namaMapel, 'kode' => $kodeMapel]);
            $this->successCount++;
        }
    }
}
