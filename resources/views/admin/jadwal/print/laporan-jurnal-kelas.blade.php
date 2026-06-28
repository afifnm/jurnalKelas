<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Jurnal – Kelas {{ $kelas->nama }}</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 12px;
    color: #1a1a1a;
    background: #f5f5f5;
  }

  .page-wrapper {
    max-width: 1160px;
    margin: 0 auto;
    padding: 20px;
  }

  /* Toolbar */
  .toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #1e293b;
    color: #fff;
    padding: 10px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    gap: 12px;
  }
  .toolbar span { font-size: 13px; opacity: .8; }
  .toolbar-btns { display: flex; gap: 8px; flex-shrink: 0; }
  .btn-print, .btn-close {
    padding: 6px 14px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .btn-print { background: #7c3aed; color: #fff; }
  .btn-print:hover { background: #6d28d9; }
  .btn-close { background: #334155; color: #e2e8f0; }
  .btn-close:hover { background: #475569; }

  /* Document */
  .doc {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.10);
  }

  /* Header */
  .doc-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 28px 14px;
    border-bottom: 3px double #1e293b;
  }
  .doc-header img { width: 56px; height: 56px; object-fit: contain; flex-shrink: 0; }
  .doc-header .logo-placeholder {
    width: 56px; height: 56px; border-radius: 8px;
    background: #f1f5f9; display: flex; align-items: center;
    justify-content: center; font-size: 22px; color: #94a3b8; flex-shrink: 0;
  }
  .school-info { flex: 1; }
  .school-name { font-size: 16px; font-weight: 700; line-height: 1.2; color: #0f172a; }
  .school-sub  { font-size: 11px; color: #64748b; margin-top: 2px; }
  .doc-title { text-align: right; flex-shrink: 0; }
  .doc-title h1 { font-size: 14px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: .5px; }
  .doc-title .sub { font-size: 11px; color: #475569; margin-top: 3px; }
  .tahun-badge {
    display: inline-block; margin-top: 4px; padding: 2px 10px;
    background: #f3e8ff; color: #6b21a8; border-radius: 20px;
    font-size: 11px; font-weight: 600; border: 1px solid #e9d5ff;
  }

  /* Info bar */
  .info-bar {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 10px 28px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    flex-wrap: wrap;
  }
  .info-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #475569; }
  .info-item strong { color: #0f172a; }

  /* Summary stats */
  .stats-bar {
    display: flex;
    gap: 0;
    border-bottom: 1px solid #e2e8f0;
  }
  .stat {
    flex: 1;
    text-align: center;
    padding: 10px 8px;
    border-right: 1px solid #e2e8f0;
  }
  .stat:last-child { border-right: none; }
  .stat-num { font-size: 20px; font-weight: 700; line-height: 1; }
  .stat-label { font-size: 10px; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: .4px; }
  .stat.diisi .stat-num { color: #16a34a; }
  .stat.kosong .stat-num { color: #dc2626; }
  .stat.total  .stat-num { color: #1e293b; }
  .stat.hari   .stat-num { color: #7c3aed; }

  /* Content */
  .content { padding: 16px 28px 20px; }

  /* Date section */
  .date-section { margin-bottom: 18px; }
  .date-section:last-child { margin-bottom: 0; }
  .date-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 0 5px 10px;
    border-left: 3px solid #7c3aed;
    margin-bottom: 6px;
  }
  .date-header .hari-nama {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #7c3aed;
  }
  .date-header .tanggal-full {
    font-size: 11px; color: #475569;
  }

  /* Table */
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  thead th {
    background: #f8fafc; color: #475569; font-weight: 600;
    text-align: left; padding: 5px 8px; border: 1px solid #e2e8f0;
    font-size: 10px; text-transform: uppercase; letter-spacing: .3px;
  }
  tbody td {
    padding: 6px 8px; border: 1px solid #e2e8f0;
    color: #1e293b; vertical-align: top;
  }
  tbody tr:nth-child(even) td { background: #fafafa; }

  .td-jam    { white-space: nowrap; font-family: 'Courier New', monospace; font-size: 11px; color: #7c3aed; font-weight: 700; width: 90px; }
  .td-mapel  { font-weight: 600; width: 130px; }
  .td-guru   { color: #475569; width: 130px; }
  .td-jam-aktual { font-family: 'Courier New', monospace; font-size: 10px; width: 80px; color: #475569; white-space: nowrap; }
  .td-status { width: 90px; }
  .td-materi { min-width: 180px; }
  .td-catatan { color: #475569; min-width: 120px; }

  .badge-ok {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 7px; border-radius: 10px;
    background: #dcfce7; color: #16a34a;
    font-size: 10px; font-weight: 600; white-space: nowrap;
  }
  .badge-late {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 7px; border-radius: 10px;
    background: #fef3c7; color: #d97706;
    font-size: 10px; font-weight: 600; white-space: nowrap;
  }
  .badge-missing {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 7px; border-radius: 10px;
    background: #fee2e2; color: #dc2626;
    font-size: 10px; font-weight: 600; white-space: nowrap;
  }

  /* Row not filled */
  tr.row-missing td { background: #fff5f5 !important; }
  .missing-note { color: #dc2626; font-style: italic; font-size: 10px; }

  /* Empty */
  .empty-state { padding: 40px 28px; text-align: center; color: #94a3b8; }

  /* Footer */
  .doc-footer {
    padding: 10px 28px; background: #f8fafc; border-top: 1px solid #e2e8f0;
    display: flex; justify-content: space-between; align-items: center;
    font-size: 10px; color: #94a3b8;
  }

  /* PRINT */
  @page { size: A4 landscape; margin: 10mm 12mm; }
  @media print {
    body { background: #fff; font-size: 10pt; }
    .toolbar { display: none !important; }
    .page-wrapper { padding: 0; max-width: 100%; }
    .doc { box-shadow: none; border-radius: 0; }
    thead th { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    tbody tr:nth-child(even) td { background: #f8f8f8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    tr.row-missing td { background: #fff0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .info-bar, .stats-bar .stat { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .doc-footer { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .badge-ok     { background: #d1fae5 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .badge-late   { background: #fef3c7 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .badge-missing { background: #fee2e2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .date-section { break-inside: avoid; }
  }
</style>
</head>
<body>
<div class="page-wrapper">

  {{-- Toolbar --}}
  <div class="toolbar">
    <span>Pratinjau Laporan Jurnal &mdash; Kelas {{ $kelas->nama }}</span>
    <div class="toolbar-btns">
      <button class="btn-print" onclick="window.print()">&#128438; Cetak / Simpan PDF</button>
      <button class="btn-close" onclick="window.close()">&#10005; Tutup</button>
    </div>
  </div>

  <div class="doc">

    {{-- Header --}}
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
      <div class="doc-title">
        <h1>Laporan Jurnal Mengajar</h1>
        <div class="sub">Per Kelas</div>
        @if($tahunAktif)
        <span class="tahun-badge">{{ $tahunAktif->nama }} &mdash; {{ $tahunAktif->semester }}</span>
        @endif
      </div>
    </div>

    {{-- Info bar --}}
    <div class="info-bar">
      <div class="info-item">
        &#127979; <span><strong>Kelas {{ $kelas->nama }}</strong></span>
      </div>
      <div class="info-item">
        &#128197; <span>Periode: <strong>{{ $dari->translatedFormat('d M Y') }} &ndash; {{ $sampai->translatedFormat('d M Y') }}</strong></span>
      </div>
    </div>

    {{-- Stats --}}
    <div class="stats-bar">
      <div class="stat hari">
        <div class="stat-num">{{ count($laporan) }}</div>
        <div class="stat-label">Hari Efektif</div>
      </div>
      <div class="stat total">
        <div class="stat-num">{{ $totalSesi }}</div>
        <div class="stat-label">Total Jam</div>
      </div>
      <div class="stat diisi">
        <div class="stat-num">{{ $totalDiisi }}</div>
        <div class="stat-label">Jurnal Diisi</div>
      </div>
      <div class="stat kosong">
        <div class="stat-num">{{ $totalKosong }}</div>
        <div class="stat-label">Tidak Diisi</div>
      </div>
      @if($totalSesi > 0)
      <div class="stat">
        <div class="stat-num" style="color:#0891b2">{{ number_format($totalDiisi / $totalSesi * 100, 0) }}%</div>
        <div class="stat-label">Kepatuhan</div>
      </div>
      @endif
    </div>

    {{-- Content --}}
    @if(empty($laporan))
    <div class="empty-state">
      <p>Tidak ada jadwal mengajar pada periode ini untuk kelas {{ $kelas->nama }}.</p>
    </div>
    @else
    <div class="content">
      @foreach($laporan as $hari)
      <div class="date-section">
        <div class="date-header">
          <span class="hari-nama">{{ $namaHari[$hari['hari']] }}</span>
          <span class="tanggal-full">{{ $hari['tanggal']->translatedFormat('d F Y') }}</span>
        </div>
        <table>
          <thead>
            <tr>
              <th style="width:90px">Jam Jadwal</th>
              <th style="width:130px">Mata Pelajaran</th>
              <th style="width:130px">Guru</th>
              <th style="width:85px">Jam Masuk</th>
              <th style="width:85px">Jam Keluar</th>
              <th style="width:90px">Status</th>
              <th>Materi</th>
              <th style="width:150px">Catatan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($hari['entries'] as $entry)
            @php $j = $entry['jadwal']; $jr = $entry['jurnal']; @endphp
            <tr class="{{ $jr ? '' : 'row-missing' }}">
              <td class="td-jam">{{ substr($j->jamPelajaran->jam_mulai,0,5) }}&ndash;{{ substr($j->jamPelajaran->jam_selesai,0,5) }}</td>
              <td class="td-mapel">{{ $j->mapel->nama }}</td>
              <td class="td-guru">{{ $j->guru->nama }}</td>
              @if($jr)
              <td class="td-jam-aktual">{{ $jr->jam_masuk_aktual ? substr($jr->jam_masuk_aktual,0,5) : '—' }}</td>
              <td class="td-jam-aktual">{{ $jr->jam_keluar_aktual ? substr($jr->jam_keluar_aktual,0,5) : '—' }}</td>
              <td class="td-status">
                @if($jr->is_terlambat)
                <span class="badge-late">&#9888; Terlambat +{{ $jr->menit_terlambat }} mnt</span>
                @else
                <span class="badge-ok">&#10003; Tepat Waktu</span>
                @endif
              </td>
              <td class="td-materi">{{ $jr->materi ?: '—' }}</td>
              <td class="td-catatan">{{ $jr->catatan ?: '—' }}</td>
              @else
              <td class="td-jam-aktual" style="color:#94a3b8">—</td>
              <td class="td-jam-aktual" style="color:#94a3b8">—</td>
              <td class="td-status"><span class="badge-missing">&#10007; Tidak Diisi</span></td>
              <td class="td-materi"><span class="missing-note">Guru tidak mengisi jurnal pada jam mengajar ini</span></td>
              <td class="td-catatan" style="color:#94a3b8">—</td>
              @endif
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endforeach
    </div>
    @endif

    {{-- Footer --}}
    <div class="doc-footer">
      <span>Dicetak pada: {{ now()->translatedFormat('l, d F Y H:i') }}</span>
      <span>{{ $sekolah?->nama }}</span>
    </div>

  </div>
</div>
</body>
</html>
