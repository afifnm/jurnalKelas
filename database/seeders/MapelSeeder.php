<?php

namespace Database\Seeders;

use App\Models\Mapel;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapel = [
            ['nama' => 'Pendidikan Agama dan Budi Pekerti', 'kode' => 'A1'],
            ['nama' => 'Pendidikan Pancasila', 'kode' => 'A2'],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'A3'],
            ['nama' => 'Pendidikan Jasmani, Olah Raga dan Kesehatan', 'kode' => 'A4'],
            ['nama' => 'Sejarah', 'kode' => 'A5'],
            ['nama' => 'Seni Budaya', 'kode' => 'A6'],
            ['nama' => 'Matematika', 'kode' => 'B1'],
            ['nama' => 'Bahasa Inggris', 'kode' => 'B2'],
            ['nama' => 'Informatika', 'kode' => 'B3'],
            ['nama' => 'Projek Ilmu Pengetahuan Alam dan Sosial', 'kode' => 'B4'],
            ['nama' => 'Dasar-dasar Program Keahlian', 'kode' => 'B5'],
            ['nama' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'kode' => 'B7-M'],
            ['nama' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'kode' => 'B7-T'],
            ['nama' => 'Proyek Kreatif dan Kewirausahaan', 'kode' => 'B7-R'],
            ['nama' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'kode' => 'B7-O'],
            ['nama' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'kode' => 'B7'],
            ['nama' => 'Mapel Pilihan (GTM)', 'kode' => 'B9-M1'],
            ['nama' => 'Mata Pelajaran Pilihan (Membubut)', 'kode' => 'B9-M2'],
            ['nama' => 'Mapel pilihan (Kriya Tekstil)', 'kode' => 'B9-T1'],
            ['nama' => 'Mapel pilihan (Perajutan)', 'kode' => 'B9-T2'],
            ['nama' => 'Mata Pelajaran Pilihan (Perbk.Sepeda Motor Bensin)', 'kode' => 'B9-O1'],
            ['nama' => 'Mapel Pilihan (Perbk.Sepeda Motor Bensin dan Listrik.)', 'kode' => 'B9-O2'],
            ['nama' => 'Mapel Pilihan (Pemrograman backend dan frontend)', 'kode' => 'B9-R1'],
            ['nama' => 'Mapel Pilihan (Sistem Informasi)', 'kode' => 'B9-R2'],
            ['nama' => 'Muatan Lokal (Bahasa Jawa)', 'kode' => 'ML'],
            ['nama' => 'Bimbingan Konseling', 'kode' => 'BK'],
            ['nama' => 'Dasar-dasar Program Keahlian (Teknik dasar proses produksi pada bidang manufaktur mesin)', 'kode' => 'M1'],
            ['nama' => 'Dasar-dasar Program Keahlian (Pengetahuan Bahan)', 'kode' => 'M2'],
            ['nama' => 'Dasar-dasar Program Keahlian (Gambar Teknik)', 'kode' => 'M3'],
            ['nama' => 'Konsentrasi Keahlian (GTM)', 'kode' => 'M4'],
            ['nama' => 'Konsentrasi Keahlian (T.P. Bubut)', 'kode' => 'M5'],
            ['nama' => 'Konsentrasi Keahlian (T.P. Frais)', 'kode' => 'M6'],
            ['nama' => 'Konsentrasi Keahlian (T.P. Gerinda)', 'kode' => 'M7'],
            ['nama' => 'Konsentrasi Keahlian (T.P. Nonkonvensional)', 'kode' => 'M8'],
            ['nama' => 'Konsentrasi Keahlian (Persiapan PK)', 'kode' => 'T1'],
            ['nama' => 'Konsentrasi Keahlian (Desain Anyaman)', 'kode' => 'T2'],
            ['nama' => 'Konsentrasi Keahlian (Pertenunan)', 'kode' => 'T3'],
            ['nama' => 'Konsentrasi Keahlian (Perajutan)', 'kode' => 'T4'],
            ['nama' => 'Konsentrasi Keahlian (Pengendalian Mutu)', 'kode' => 'T5'],
            ['nama' => 'Dasar-dasar Program Keahlian Teknik Otomotif (Teknik Dasar Peml. Ot)', 'kode' => 'O1'],
            ['nama' => 'Dasar-dasar Program Keahlian Teknik Otomotif (Gambar Teknik)', 'kode' => 'O2'],
            ['nama' => 'Dasar-dasar Program Keahlian Teknik Otomotif (Peml. Komp. Oto)', 'kode' => 'O3'],
            ['nama' => 'Konsentrasi Keahlian Teknik Ototronik (EMS)', 'kode' => 'O4'],
            ['nama' => 'Konsentrasi Keahlian TO (CMS)', 'kode' => 'O5'],
            ['nama' => 'Konsentrasi Keahlian Teknik Ototronik (CSIT)', 'kode' => 'O6'],
            ['nama' => 'Konsentrasi Keahlian Teknik Ototronik (SKK)', 'kode' => 'O7'],
            ['nama' => 'Dasar-dasar Program Keahlian PPLG (Proses bisnis bidang PPLG)', 'kode' => 'R1'],
            ['nama' => 'Dasar-dasar Program Keahlian PPLG (Pemrograman berorientasi objek)', 'kode' => 'R2'],
            ['nama' => 'Konsentrasi Keahlian (Pemrograman Berbasis Teks, Grafis, dan Multimedia)', 'kode' => 'R3'],
            ['nama' => 'Konsentrasi Keahlian (Pemrograman Web)', 'kode' => 'R4'],
            ['nama' => 'Konsentrasi Keahlian (Pemrograman Perangkat Bergerak)', 'kode' => 'R5'],
            ['nama' => 'Konsentrasi Keahlian (Basis Data)', 'kode' => 'R6'],
            ['nama' => 'Koding dan Kecerdasan Artifisial', 'kode' => 'C'],
        ];

        foreach ($mapel as $m) {
            Mapel::create($m);
        }
    }
}
