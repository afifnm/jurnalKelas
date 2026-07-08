<?php

namespace App\Console\Commands;

use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\KinerjaGuru;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HitungMetrikJurnal extends Command
{
    protected $signature = 'jurnal:hitung-metrik {--periode= : Periode YYYY-MM, default bulan ini}';

    protected $description = 'Hitung dan simpan metrik kinerja mengajar guru per bulan.';

    /*
     * Formula skor kinerja:
     *   skor = (kepatuhan × 0.5) + (ketepatan_waktu × 0.3) + (rasio_validasi × 0.2)
     *
     * - kepatuhan       = (total_terisi / total_jadwal) × 100
     * - ketepatan_waktu = ((total_terisi - total_terlambat) / total_terisi) × 100
     * - rasio_validasi  = (total_validated / total_terisi) × 100
     */
    public function handle(): int
    {
        $periode = $this->option('periode') ?? now()->format('Y-m');

        if (! preg_match('/^\d{4}-\d{2}$/', $periode)) {
            $this->error('Format periode salah. Gunakan YYYY-MM.');

            return self::FAILURE;
        }

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $guru = User::role('guru')->where('is_active', true)->get();

        [$tahun, $bulan] = explode('-', $periode);
        $awal = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $akhir = $awal->copy()->endOfMonth();

        $this->info("Menghitung metrik periode {$periode}...");
        $bar = $this->output->createProgressBar($guru->count());

        foreach ($guru as $g) {
            $totalJadwalPerMinggu = Jadwal::where('guru_id', $g->id)
                ->when($tahunAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
                ->count();

            $minggu = $awal->copy()->diffInWeeks($akhir) + 1;
            $totalJadwal = $totalJadwalPerMinggu * $minggu;

            $jurnal = Jurnal::where('guru_id', $g->id)
                ->whereBetween('tanggal', [$awal, $akhir])
                ->with(['jadwal.jamPelajaran'])
                ->get();

            $jamSesiMap = Jurnal::buildJamSesiMap($jurnal);
            $totalTerisi = $jurnal->count();
            $totalDalamJam = $jurnal->filter(
                fn ($j) => $j->isInputDalamJamMengajar($jamSesiMap[$j->id] ?? null)
            )->count();
            $totalLuarJam = $totalTerisi - $totalDalamJam;

            $kepatuhan = $totalJadwal > 0 ? ($totalTerisi / $totalJadwal) * 100 : 0;
            $ketepatanWaktu = $totalTerisi > 0 ? ($totalDalamJam / $totalTerisi) * 100 : 0;
            $rasioValidasi = 0;

            $skor = ($kepatuhan * 0.5) + ($ketepatanWaktu * 0.3);

            KinerjaGuru::updateOrCreate(
                ['guru_id' => $g->id, 'periode' => $periode],
                [
                    'total_jadwal' => max($totalJadwal, 0),
                    'total_terisi' => $totalTerisi,
                    'persen_kepatuhan' => round($kepatuhan, 2),
                    'total_terlambat' => $totalLuarJam,
                    'rata_keterlambatan_menit' => 0,
                    'total_validated' => 0,
                    'skor_kinerja' => round($skor, 2),
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Metrik periode {$periode} berhasil dihitung untuk {$guru->count()} guru.");

        return self::SUCCESS;
    }
}
