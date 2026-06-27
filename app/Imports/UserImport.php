<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToCollection, WithHeadingRow
{
    public int $successCount = 0;
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        $seenKodes = [];

        $existingUsernames = DB::table('users')
            ->whereNull('deleted_at')
            ->pluck('username')
            ->map(fn($u) => strtolower((string) $u))
            ->flip()
            ->toArray();

        $existingEmails = DB::table('users')
            ->whereNull('deleted_at')
            ->whereNotNull('email')
            ->pluck('email')
            ->map(fn($e) => strtolower((string) $e))
            ->flip()
            ->toArray();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $nama     = trim((string) ($row['nama'] ?? ''));
            $kodeGuru = trim((string) ($row['kode_guru'] ?? ''));
            $email    = trim((string) ($row['email'] ?? '')) ?: null;
            $telp     = trim((string) ($row['telp'] ?? '')) ?: null;

            if ($nama === '' && $kodeGuru === '') {
                continue;
            }

            if ($nama === '') {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Kolom nama wajib diisi.'];
                continue;
            }
            if ($kodeGuru === '') {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Kolom kode_guru wajib diisi.'];
                continue;
            }
            if (strlen($nama) > 100) {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Nama terlalu panjang (maks. 100 karakter).'];
                continue;
            }
            if (strlen($kodeGuru) > 50) {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Kode guru terlalu panjang (maks. 50 karakter).'];
                continue;
            }
            if ($email !== null && strlen($email) > 255) {
                $this->errors[] = ['row' => $rowNumber, 'message' => 'Email terlalu panjang.'];
                continue;
            }

            $kodeKey = strtolower($kodeGuru);

            if (isset($seenKodes[$kodeKey])) {
                $this->errors[] = ['row' => $rowNumber, 'message' => "Kode guru \"{$kodeGuru}\" duplikat dalam file (baris {$seenKodes[$kodeKey]})."];
                continue;
            }
            if (isset($existingUsernames[$kodeKey])) {
                $this->errors[] = ['row' => $rowNumber, 'message' => "Kode guru \"{$kodeGuru}\" sudah terdaftar di database."];
                continue;
            }

            if ($email !== null) {
                $emailKey = strtolower($email);
                if (isset($existingEmails[$emailKey])) {
                    $this->errors[] = ['row' => $rowNumber, 'message' => "Email \"{$email}\" sudah terdaftar."];
                    continue;
                }
                $existingEmails[$emailKey] = true;
            }

            $seenKodes[$kodeKey] = $rowNumber;
            $existingUsernames[$kodeKey] = true;

            $user = User::create([
                'nama'      => $nama,
                'username'  => $kodeGuru,
                'email'     => $email,
                'password'  => '12345678',
                'no_hp'     => $telp,
                'is_active' => true,
            ]);

            $user->assignRole('guru');
            $this->successCount++;
        }
    }
}
