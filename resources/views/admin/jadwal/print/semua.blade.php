<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Pelajaran – {{ $tahunAktif?->nama }} {{ $tahunAktif?->semester }}</title>
<style>
@page {
  size: 330mm 210mm;
  margin: 7mm 10mm;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Segoe UI', Arial, sans-serif;
  font-size: 7.5pt;
  color: #111;
  background: #fff;
}

/* ===== SCREEN WRAPPER ===== */
.screen-wrapper {
  background: #e5e7eb;
  min-height: 100vh;
  padding: 16px;
}

/* Toolbar */
.toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #1e293b;
  color: #fff;
  padding: 9px 14px;
  border-radius: 8px;
  margin-bottom: 14px;
  gap: 10px;
  max-width: 1100px;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 14px;
}
.toolbar-left { font-size: 12px; opacity: .8; }
.toolbar-btns { display: flex; gap: 7px; flex-shrink: 0; }
.btn-print, .btn-close {
  padding: 5px 13px; border: none; border-radius: 5px;
  font-size: 12px; font-weight: 600; cursor: pointer;
  display: inline-flex; align-items: center; gap: 5px;
}
.btn-print { background: #f59e0b; color: #1a1a1a; }
.btn-print:hover { background: #d97706; }
.btn-close { background: #334155; color: #e2e8f0; }
.btn-close:hover { background: #475569; }

/* Screen document shell */
.doc-screen {
  max-width: 1100px;
  margin: 0 auto;
  background: #fff;
  box-shadow: 0 4px 24px rgba(0,0,0,.15);
  border-radius: 6px;
  padding: 10mm 12mm 8mm;
}

/* ===== HEADER ===== */
.doc-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-bottom: 6px;
  border-bottom: 2px solid #111;
  margin-bottom: 6px;
}
.doc-header img {
  width: 44px; height: 44px; object-fit: contain; flex-shrink: 0;
}
.logo-placeholder {
  width: 44px; height: 44px; background: #f1f5f9; border-radius: 6px;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; color: #94a3b8; flex-shrink: 0;
}
.school-info { flex: 1; }
.school-name { font-size: 11pt; font-weight: 700; line-height: 1.2; }
.school-sub  { font-size: 7pt; color: #555; margin-top: 1px; }
.doc-title-block { text-align: center; flex: 1; }
.doc-title-block h1 {
  font-size: 13pt; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
}
.doc-title-block .sub { font-size: 8pt; color: #444; margin-top: 2px; }
.doc-meta { text-align: right; flex: 1; font-size: 7pt; color: #555; line-height: 1.6; }

/* ===== TIMETABLE ===== */
.timetable-wrap { overflow: visible; }

table.timetable {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  font-size: 6.8pt;
}

table.timetable th,
table.timetable td {
  border: 0.4pt solid #aaa;
  padding: 2px 2.5px;
  vertical-align: middle;
  line-height: 1.25;
  overflow: hidden;
}

/* Header row */
table.timetable thead th {
  background: #1e293b;
  color: #fff;
  text-align: center;
  font-size: 6.5pt;
  font-weight: 700;
  padding: 3px 2px;
  text-transform: uppercase;
  letter-spacing: .3px;
}
table.timetable thead th.th-hari { width: 7%; }
table.timetable thead th.th-jam  { width: 11%; }

/* Hari cell */
td.td-hari {
  font-size: 7pt;
  font-weight: 700;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: .5px;
  background: #f8fafc;
  writing-mode: vertical-lr;
  transform: rotate(180deg);
  padding: 4px 2px;
  color: #0f172a;
  border-right: 1.5pt solid #64748b;
}

/* Jam cell */
td.td-jam {
  font-family: 'Courier New', monospace;
  font-size: 6.5pt;
  text-align: center;
  color: #334155;
  white-space: nowrap;
  font-weight: 600;
  background: #f8fafc;
}

/* Jadwal cell */
td.td-jadwal {
  text-align: center;
  padding: 1.5px 2px;
}
td.td-jadwal.has-jadwal { background: #fefce8; }

.cell-kode {
  display: block;
  font-weight: 700;
  font-size: 7pt;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cell-user {
  display: block;
  font-size: 5.8pt;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Separator between hari */
tr.hari-separator td { border-top: 1.2pt solid #334155 !important; }

/* ===== LEGEND ===== */
.legend {
  margin-top: 6px;
  padding-top: 5px;
  border-top: 1pt solid #aaa;
  display: flex;
  gap: 16px;
  align-items: flex-start;
}
.legend-block { flex: 1; }
.legend-title {
  font-size: 6.5pt;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: #1e293b;
  margin-bottom: 2px;
}
.legend-items {
  font-size: 6pt;
  color: #333;
  line-height: 1.6;
  column-count: 2;
  column-gap: 8px;
}
.legend-items span { white-space: nowrap; }
.legend-kode { font-weight: 700; }

/* Printed-at footer */
.print-footer {
  margin-top: 4px;
  font-size: 5.8pt;
  color: #999;
  text-align: right;
}

/* ===== PRINT ===== */
@media print {
  html, body { background: #fff !important; }
  .screen-wrapper { background: #fff !important; padding: 0 !important; }
  .toolbar { display: none !important; }
  .doc-screen { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; max-width: 100% !important; }

  table.timetable thead th { background: #1e293b !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  td.td-hari { background: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  td.td-jam  { background: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  td.td-jadwal.has-jadwal { background: #fefce8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>
@php
/* Helper: kode singkat mapel */
$getKode = function($mapel) {
    if ($mapel->kode) return $mapel->kode;
    $words = preg_split('/[\s\/\-]+/', trim($mapel->nama));
    $abbr  = collect($words)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
    return $abbr ?: strtoupper(substr($mapel->nama, 0, 3));
};
@endphp

<div class="screen-wrapper">

  {{-- Toolbar (screen only) --}}
  <div class="toolbar">
    <span class="toolbar-left">Jadwal Pelajaran Lengkap &mdash; Pratinjau Cetak (F4 Landscape)</span>
    <div class="toolbar-btns">
      <button class="btn-print" onclick="window.print()">&#128438; Cetak</button>
      <button class="btn-close" onclick="window.close()">&#10005; Tutup</button>
    </div>
  </div>

  <div class="doc-screen">

    {{-- Header --}}
    <div class="doc-header">
      <img src="{{ asset('logo.webp') }}" alt="Logo"
           onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="logo-placeholder" style="display:none">&#127979;</div>

      <div class="school-info">
        <div class="school-name">{{ $sekolah?->nama ?? 'Nama Sekolah' }}</div>
        @if($sekolah?->alamat)
        <div class="school-sub">{{ $sekolah->alamat }}</div>
        @endif
        @if($sekolah?->npsn)
        <div class="school-sub">NPSN: {{ $sekolah->npsn }}</div>
        @endif
      </div>

      <div class="doc-title-block">
        <h1>Jadwal Pelajaran</h1>
        @if($tahunAktif)
        <div class="sub">Tahun Ajaran {{ $tahunAktif->nama }} &mdash; Semester {{ $tahunAktif->semester }}</div>
        @endif
      </div>

      <div class="doc-meta">
        @if($sekolah?->kepala_sekolah)
        Kepala Sekolah:<br><strong>{{ $sekolah->kepala_sekolah }}</strong>
        @endif
      </div>
    </div>

    {{-- Timetable --}}
    @if($kelasList->isEmpty() || $jadwalByHari->isEmpty())
    <p style="text-align:center;padding:30px;color:#94a3b8;">Tidak ada jadwal tersedia untuk tahun ajaran ini.</p>
    @else
    <div class="timetable-wrap">
      <table class="timetable">
        <thead>
          <tr>
            <th class="th-hari">Hari</th>
            <th class="th-jam">Jam</th>
            @foreach($kelasList as $kelas)
            <th>{{ $kelas->nama }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($namaHari as $hariNum => $hariNama)
            @if($jadwalByHari->has($hariNum))
              @php
                $slots       = $jadwalByHari[$hariNum];
                $slotCount   = $slots->count();
                $firstSlot   = true;
              @endphp
              @foreach($slots as $slotKey => $byKelas)
                @php [$jamMulai, $jamSelesai] = explode('|', $slotKey); @endphp
                <tr class="{{ $firstSlot ? 'hari-separator' : '' }}">
                  @if($firstSlot)
                  <td class="td-hari" rowspan="{{ $slotCount }}">{{ $hariNama }}</td>
                  @php $firstSlot = false; @endphp
                  @endif

                  <td class="td-jam">{{ substr($jamMulai,0,5) }}&ndash;{{ substr($jamSelesai,0,5) }}</td>

                  @foreach($kelasList as $kelas)
                    @php $j = $byKelas->get($kelas->id); @endphp
                    <td class="td-jadwal {{ $j ? 'has-jadwal' : '' }}">
                      @if($j)
                        <span class="cell-kode">{{ $getKode($j->mapel) }}</span>
                        <span class="cell-user">{{ $j->guru->username }}</span>
                      @endif
                    </td>
                  @endforeach
                </tr>
              @endforeach
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    {{-- Legend --}}
    <div class="legend">
      <div class="legend-block">
        <div class="legend-title">Keterangan Mata Pelajaran</div>
        <div class="legend-items">
          @foreach($mapelUsed as $m)
          <span><span class="legend-kode">{{ $getKode($m) }}</span> = {{ $m->nama }}&nbsp;&nbsp;</span>
          @endforeach
        </div>
      </div>
      <div class="legend-block">
        <div class="legend-title">Keterangan Guru</div>
        <div class="legend-items">
          @foreach($guruUsed as $g)
          <span><span class="legend-kode">{{ $g->username }}</span> = {{ $g->nama }}&nbsp;&nbsp;</span>
          @endforeach
        </div>
      </div>
    </div>

    <div class="print-footer">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</div>

  </div>{{-- .doc-screen --}}
</div>{{-- .screen-wrapper --}}
</body>
</html>
