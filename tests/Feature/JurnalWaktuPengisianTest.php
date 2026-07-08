<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\JamPelajaran;
use App\Models\Jurnal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class JurnalWaktuPengisianTest extends TestCase
{
    use RefreshDatabase;

    public function test_aplikasi_menggunakan_timezone_asia_jakarta(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $this->assertSame('Asia/Jakarta', date_default_timezone_get());
    }

    public function test_input_sebelum_jp_terakhir_selesai_masih_dalam_jam_mengajar(): void
    {
        $jurnal = $this->buatJurnalEmpatJamPelajaran('08:54');

        $jamSesi = Jurnal::buildJamSesiMap(collect([$jurnal]))[$jurnal->id];

        $this->assertSame('07:00', $jamSesi['mulai']);
        $this->assertSame('09:00', $jamSesi['selesai']);
        $this->assertSame(4, $jamSesi['jumlah']);
        $this->assertSame(1, $jamSesi['jam_ke_mulai']);
        $this->assertSame(4, $jamSesi['jam_ke_selesai']);
        $this->assertTrue($jurnal->isInputDalamJamMengajar($jamSesi));
        $this->assertTrue($jurnal->isInputDalamJamMengajar());

        $detail = $jurnal->toDetailArray();
        $this->assertSame('2026-07-06 08:54:00', $jurnal->getRawOriginal('created_at'));
        $this->assertSame('Senin, 6 Juli 2026', $detail['tanggal_input']);
        $this->assertSame('08:54', $detail['jam_input']);
        $this->assertSame('07:00', $detail['jam_sesi']['mulai']);
        $this->assertSame('09:00', $detail['jam_sesi']['selesai']);
        $this->assertTrue($detail['dalam_jam']);
        $this->assertSame('Dalam jam', $detail['status_keterlambatan']);
    }

    public function test_input_melewati_toleransi_sesi_dinyatakan_di_luar_jam(): void
    {
        $jurnal = $this->buatJurnalEmpatJamPelajaran('09:31');

        $this->assertFalse($jurnal->isInputDalamJamMengajar());
    }

    public function test_jam_input_database_tidak_ditambah_offset_jakarta_dua_kali(): void
    {
        $jurnal = $this->buatJurnalEmpatJamPelajaran('07:58');

        $detail = $jurnal->toDetailArray();

        $this->assertSame('2026-07-06 07:58:00', $jurnal->getRawOriginal('created_at'));
        $this->assertSame('2026-07-06 07:58:00', $detail['created_at_database']);
        $this->assertSame('07:58', $detail['jam_input']);
        $this->assertTrue($detail['dalam_jam']);
        $this->assertSame('Dalam jam', $detail['status_keterlambatan']);
    }

    public function test_cetak_jurnal_admin_mengikuti_filter_dan_merender_html_standalone(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::create([
            'nama' => 'Admin Penguji',
            'username' => 'admin-penguji',
            'email' => 'admin@example.test',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');
        $jurnal = $this->buatJurnalEmpatJamPelajaran('08:54');

        $response = $this->actingAs($admin)->get(route('admin.jurnal.print', [
            'guru_id' => $jurnal->guru_id,
            'tanggal_dari' => '2026-07-06',
            'tanggal_sampai' => '2026-07-06',
        ]));

        $response->assertOk();
        $response->assertViewHas('jurnal', fn ($items) => $items->pluck('id')->all() === [$jurnal->id]);
        $response->assertSee('<!DOCTYPE html>', false);
        $response->assertSee('07:00–09:00');
        $response->assertSee('Jam ke-1–4');
        $response->assertSee('Persamaan linear');

        $this->actingAs($admin)->get(route('admin.jurnal.print', [
            'tanggal_dari' => '2026-07-07',
        ]))->assertDontSee('Persamaan linear');
    }

    private function buatJurnalEmpatJamPelajaran(string $waktuInput): Jurnal
    {
        $guru = User::create([
            'nama' => 'Guru Penguji',
            'username' => 'guru-penguji',
            'email' => 'guru@example.test',
            'password' => 'password',
            'is_active' => true,
        ]);
        $kelas = Kelas::create(['nama' => 'X-A']);
        $mapel = Mapel::create(['nama' => 'Matematika', 'kode' => 'MTK']);
        $tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);

        $rentangJam = [
            ['07:00', '07:30'],
            ['07:30', '08:00'],
            ['08:00', '08:30'],
            ['08:30', '09:00'],
        ];

        $jadwalPertama = null;
        foreach ($rentangJam as $index => [$mulai, $selesai]) {
            $jamPelajaran = JamPelajaran::create([
                'hari' => 1,
                'jam_ke' => $index + 1,
                'jam_mulai' => $mulai,
                'jam_selesai' => $selesai,
                'is_istirahat' => false,
            ]);
            $jadwal = Jadwal::create([
                'guru_id' => $guru->id,
                'kelas_id' => $kelas->id,
                'mapel_id' => $mapel->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'jam_pelajaran_id' => $jamPelajaran->id,
            ]);
            $jadwalPertama ??= $jadwal;
        }

        $jurnal = new Jurnal([
            'jadwal_id' => $jadwalPertama->id,
            'guru_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'tanggal' => '2026-07-06',
            'materi' => 'Persamaan linear',
        ]);
        $jurnal->created_at = Carbon::createFromFormat('Y-m-d H:i', "2026-07-06 {$waktuInput}", 'Asia/Jakarta');
        $jurnal->updated_at = $jurnal->created_at;
        $jurnal->save();

        return $jurnal->fresh(['jadwal.jamPelajaran']);
    }
}
