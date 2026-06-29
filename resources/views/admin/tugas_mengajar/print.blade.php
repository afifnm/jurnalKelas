<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembagian Tugas Mengajar – {{ $tahunAjaranAktif?->nama }} {{ $tahunAjaranAktif?->semester }}</title>
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

.screen-wrapper {
  background: #e5e7eb;
  min-height: 100vh;
  padding: 16px;
}

.toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #1e293b;
  color: #fff;
  padding: 9px 14px;
  border-radius: 8px;
  max-width: 1300px;
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

.doc-screen {
  max-width: 1300px;
  margin: 0 auto;
  background: #fff;
  box-shadow: 0 4px 24px rgba(0,0,0,.15);
  border-radius: 6px;
  padding: 10mm 12mm 8mm;
}

.doc-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-bottom: 6px;
  border-bottom: 2px solid #111;
  margin-bottom: 10px;
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

table.timetable {
  width: 100%;
  border-collapse: collapse;
  font-size: 6.8pt;
}

table.timetable th,
table.timetable td {
  border: 0.4pt solid #aaa;
  padding: 3px;
  vertical-align: middle;
  line-height: 1.25;
}

table.timetable thead th {
  background: #1e293b;
  color: #fff;
  text-align: center;
  font-size: 6.5pt;
  font-weight: 700;
  padding: 4px 3px;
  text-transform: uppercase;
}

td.text-center { text-align: center; }
td.text-right { text-align: right; }
td.font-bold { font-weight: 700; }

.bg-slate-50 { background-color: #f8fafc; }

.print-footer {
  margin-top: 8px;
  font-size: 5.8pt;
  color: #999;
  text-align: right;
}

@media print {
  html, body { background: #fff !important; }
  .screen-wrapper { background: #fff !important; padding: 0 !important; }
  .toolbar { display: none !important; }
  .doc-screen { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; max-width: 100% !important; }
  table.timetable thead th { background: #1e293b !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .bg-slate-50 { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>
<div class="screen-wrapper">
  <div class="toolbar">
    <span class="toolbar-left">Pembagian Tugas Mengajar &mdash; Pratinjau Cetak</span>
    <div class="toolbar-btns">
      <button class="btn-print" onclick="window.print()">&#128438; Cetak</button>
      <button class="btn-close" onclick="window.close()">&#10005; Tutup</button>
    </div>
  </div>

  <div class="doc-screen">
    <div class="doc-header">
      <img src="{{ asset('logo.webp') }}" alt="Logo" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
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
        <h1>Pembagian Tugas Mengajar</h1>
        @if($tahunAjaranAktif)
        <div class="sub">Tahun Ajaran {{ $tahunAjaranAktif->nama }} &mdash; Semester {{ $tahunAjaranAktif->semester }}</div>
        @endif
      </div>
      <div class="doc-meta">
        @if($sekolah?->kepala_sekolah)
        Kepala Sekolah:<br><strong>{{ $sekolah->kepala_sekolah }}</strong>
        @endif
      </div>
    </div>

    <table class="timetable">
      <thead>
        <tr>
          <th rowspan="2" style="width: 2%;">No</th>
          <th rowspan="2" style="width: 15%;">Nama Guru</th>
          <th rowspan="2" style="width: 15%;">Mata Pelajaran</th>
          @foreach($kelasGrouped as $tingkat => $kelasArray)
            <th colspan="{{ count($kelasArray) }}">{{ $tingkat }}</th>
          @endforeach
          <th rowspan="2" style="width: 3%;">Jml JP Mengajar</th>
          <th rowspan="2" style="width: 3%;">Jam Tugas</th>
          <th rowspan="2" style="width: 3%;">Total JP</th>
        </tr>
        <tr>
          @foreach($kelasGrouped as $tingkat => $kelasArray)
            @foreach($kelasArray as $kelas)
              <th style="font-size: 6pt;">{{ str_replace($tingkat.' ', '', $kelas->nama) }}</th>
            @endforeach
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php
            $currentGuruId = null;
            $rowNo = 1;
        @endphp
        @foreach($rowsData as $index => $row)
            @php
                $isNewGuru = ($row['guru_id'] !== $currentGuruId);
                if ($isNewGuru) {
                    $currentGuruId = $row['guru_id'];

                    $rowspan = 0;
                    $totalMengajarGuru = 0;
                    foreach ($rowsData as $r) {
                        if ($r['guru_id'] === $currentGuruId) {
                            $rowspan++;
                            foreach ($r['kelas_hours'] as $jp) {
                                $totalMengajarGuru += (int)$jp;
                            }
                        }
                    }

                    $jabatan  = $jabatanData[$currentGuruId] ?? null;
                    $jamTugas = $jabatan ? (int)($jabatan['jumlah_jam'] ?? 0) : 0;
                    $totalSemua = $totalMengajarGuru + $jamTugas;
                }
            @endphp
            <tr>
                @if($isNewGuru)
                    <td class="text-center" rowspan="{{ $rowspan }}">{{ $rowNo++ }}</td>
                    <td rowspan="{{ $rowspan }}">
                        <strong style="font-size: 7.5pt;">{{ $row['guru_nama'] }}</strong><br>
                        <span style="color: #64748b;">{{ $row['guru_kode'] }}</span>
                    </td>
                @endif

                <td>{{ $row['mapel_nama'] }}</td>

                @foreach($kelasGrouped as $tingkat => $kelasArray)
                    @foreach($kelasArray as $kelas)
                        @php $jp = $row['kelas_hours']->{$kelas->id} ?? ''; @endphp
                        <td class="text-center {{ $jp ? 'bg-slate-50 font-bold' : '' }}">{{ $jp }}</td>
                    @endforeach
                @endforeach

                @if($isNewGuru)
                    <td class="text-center font-bold bg-slate-50" rowspan="{{ $rowspan }}">{{ $totalMengajarGuru ?: '' }}</td>
                    <td class="text-center" rowspan="{{ $rowspan }}">{{ $jamTugas ?: '' }}</td>
                    <td class="text-center font-bold" rowspan="{{ $rowspan }}" style="font-size: 8pt;">{{ $totalSemua ?: '' }}</td>
                @endif
            </tr>
        @endforeach
      </tbody>
    </table>

    <div class="footer-meta">
      Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} oleh {{ auth()->user()->nama }}
    </div>

  </div>
  
  <script>
    window.onload = function() {
        window.print();
    }
  </script>
</body>
</html>
