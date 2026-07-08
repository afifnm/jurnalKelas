<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

class Jurnal extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal';

    protected $fillable = [
        'jadwal_id', 'guru_id', 'kelas_id', 'mapel_id', 'tahun_ajaran_id',
        'tanggal', 'materi', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class)->withTrashed();
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id')->withTrashed();
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class)->withTrashed();
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class)->withTrashed();
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class)->withTrashed();
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(JurnalLampiran::class);
    }

    public function log(): HasMany
    {
        return $this->hasMany(JurnalLog::class);
    }

    /**
     * Cek apakah jurnal diisi dalam rentang sesi mengajar.
     *
     * Satu sesi dapat terdiri dari beberapa jam pelajaran untuk guru, kelas,
     * dan mapel yang sama. Rentangnya dimulai dari JP pertama sampai JP
     * terakhir, dengan toleransi 15 menit sebelum dan 30 menit sesudah sesi.
     *
     * @param  array{mulai:string, selesai:string, jumlah?:int}|null  $jamSesi
     */
    public function isInputDalamJamMengajar(?array $jamSesi = null): bool
    {
        if (! $this->created_at || ! $this->jadwal || ! $this->jadwal->jamPelajaran) {
            return false;
        }

        if ($jamSesi === null && $this->exists) {
            $jamSesiMap = self::buildJamSesiMap(collect([$this]));
            $jamSesi = $jamSesiMap[$this->getKey()] ?? null;
        }

        $jamMulaiSesi = $jamSesi['mulai'] ?? substr($this->jadwal->jamPelajaran->jam_mulai, 0, 5);
        $jamSelesaiSesi = $jamSesi['selesai'] ?? substr($this->jadwal->jamPelajaran->jam_selesai, 0, 5);
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $createdAt = $this->waktuInputLokal();

        if (! $createdAt) {
            return false;
        }

        $tanggalInput = $createdAt->toDateString();
        $jamMulai = Carbon::createFromFormat('Y-m-d H:i', "{$tanggalInput} {$jamMulaiSesi}", $timezone);
        $jamSelesai = Carbon::createFromFormat('Y-m-d H:i', "{$tanggalInput} {$jamSelesaiSesi}", $timezone);

        $windowMulai = $jamMulai->copy()->subMinutes(15);
        $windowSelesai = $jamSelesai->copy()->addMinutes(30);

        return $createdAt->between($windowMulai, $windowSelesai);
    }

    /**
     * Build map [jurnal_id => ['mulai'=>'HH:MM','selesai'=>'HH:MM','jumlah'=>n]]
     * untuk seluruh collection jurnal tanpa N+1.
     * Jadwal pada jurnal harus sudah di-eager-load dengan jamPelajaran.
     *
     * @param  Collection|AbstractPaginator  $jurnal
     */
    public static function buildJamSesiMap($jurnal): array
    {
        // Kumpulkan kombinasi unik (mapel+kelas+tahun+hari) dari semua jurnal di halaman
        $kombo = collect();
        foreach ($jurnal as $j) {
            if ($j->jadwal && $j->jadwal->jamPelajaran) {
                $kombo->push([
                    'mapel_id' => $j->jadwal->mapel_id,
                    'kelas_id' => $j->jadwal->kelas_id,
                    'tahun_ajaran_id' => $j->jadwal->tahun_ajaran_id,
                    'hari' => $j->jadwal->jamPelajaran->hari,
                ]);
            }
        }
        $kombo = $kombo->unique(fn ($k) => implode('-', $k));

        if ($kombo->isEmpty()) {
            return [];
        }

        // Satu query untuk semua jadwal yang dibutuhkan
        $allJadwal = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with('jamPelajaran')
            ->where(function ($q) use ($kombo) {
                foreach ($kombo as $k) {
                    $q->orWhere(function ($q2) use ($k) {
                        $q2->where('jadwal.mapel_id', $k['mapel_id'])
                            ->where('jadwal.kelas_id', $k['kelas_id'])
                            ->where('jadwal.tahun_ajaran_id', $k['tahun_ajaran_id'])
                            ->where('jam_pelajaran.hari', $k['hari']);
                    });
                }
            })
            ->orderBy('jam_pelajaran.jam_ke')
            ->get()
            ->groupBy(fn ($j) => $j->mapel_id.'-'.$j->kelas_id.'-'.$j->tahun_ajaran_id.'-'.$j->jamPelajaran->hari);

        // Pre-compute grup untuk setiap kombo
        $grupMap = [];
        foreach ($allJadwal as $key => $jadwalGroup) {
            $grups = Jadwal::grupkanBerurutan($jadwalGroup);
            foreach ($grups as $grup) {
                foreach ($grup['ids'] as $jadwalId) {
                    $first = $grup['jadwal']->first();
                    $last = $grup['jadwal']->last();
                    $grupMap[$jadwalId] = [
                        'mulai' => substr($first->jamPelajaran->jam_mulai, 0, 5),
                        'selesai' => substr($last->jamPelajaran->jam_selesai, 0, 5),
                        'jumlah' => $grup['jadwal']->count(),
                        'jam_ke_mulai' => $first->jamPelajaran->jam_ke,
                        'jam_ke_selesai' => $last->jamPelajaran->jam_ke,
                    ];
                }
            }
        }

        // Susun map per jurnal_id
        $result = [];
        foreach ($jurnal as $j) {
            if ($j->jadwal_id && isset($grupMap[$j->jadwal_id])) {
                $result[$j->id] = $grupMap[$j->jadwal_id];
            }
        }

        return $result;
    }

    /**
     * Data jurnal untuk modal detail dengan waktu dalam timezone aplikasi.
     */
    public function toDetailArray(): array
    {
        $jamSesiMap = self::buildJamSesiMap(collect([$this]));
        $jamSesi = $jamSesiMap[$this->getKey()] ?? null;
        $dalamJam = $this->isInputDalamJamMengajar($jamSesi);
        $waktuInputMentah = (string) ($this->getRawOriginal('created_at') ?? '');
        $waktuInput = $this->waktuInputLokal();
        $data = $this->toArray();

        $data['jam_sesi'] = $jamSesi;
        $data['dalam_jam'] = $dalamJam;
        $data['created_at_database'] = $waktuInputMentah ?: null;
        $data['tanggal_input'] = $waktuInput?->copy()->locale('id')->translatedFormat('l, j F Y');
        $data['jam_input'] = strlen($waktuInputMentah) >= 16
            ? substr($waktuInputMentah, 11, 5)
            : $waktuInput?->format('H:i');
        $data['status_keterlambatan'] = $dalamJam ? 'Dalam jam' : 'Di luar jam';

        return $data;
    }

    /**
     * Timestamp database sudah disimpan dalam waktu lokal Asia/Jakarta.
     * Parse ulang nilai mentah agar offset +07:00 tidak diterapkan dua kali.
     */
    private function waktuInputLokal(): ?Carbon
    {
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $waktuMentah = $this->getRawOriginal('created_at');

        if ($waktuMentah) {
            return Carbon::parse((string) $waktuMentah, $timezone);
        }

        if (! $this->created_at) {
            return null;
        }

        return Carbon::parse($this->created_at->format('Y-m-d H:i:s'), $timezone);
    }

    public function isEditableByGuru(): bool
    {
        return true;
    }
}
