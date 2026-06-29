<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Pelajaran – {{ $guru->nama }}</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 13px;
    color: #1a1a1a;
    background: #f5f5f5;
  }

  .page-wrapper {
    max-width: 860px;
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
  .btn-print { background: #f59e0b; color: #1a1a1a; }
  .btn-print:hover { background: #d97706; }
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
    padding: 20px 28px 16px;
    border-bottom: 3px double #1e293b;
  }
  .doc-header img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    flex-shrink: 0;
  }
  .doc-header .logo-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #94a3b8;
    flex-shrink: 0;
  }
  .school-info { flex: 1; }
  .school-name { font-size: 17px; font-weight: 700; line-height: 1.2; color: #0f172a; }
  .school-sub  { font-size: 11px; color: #64748b; margin-top: 2px; }
  .doc-title { text-align: right; flex-shrink: 0; }
  .doc-title h1 { font-size: 15px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: .5px; }
  .doc-title .sub { font-size: 12px; color: #475569; margin-top: 3px; }
  .tahun-badge {
    display: inline-block;
    margin-top: 4px;
    padding: 2px 10px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid #fde68a;
  }

  /* Guru info bar */
  .guru-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 28px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
  }
  .guru-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #fef3c7;
    border: 1px solid #fde68a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
    color: #92400e;
    flex-shrink: 0;
  }
  .guru-nama { font-size: 14px; font-weight: 700; color: #0f172a; }
  .guru-meta { font-size: 11px; color: #64748b; margin-top: 1px; }

  /* Content */
  .content { padding: 20px 28px; }

  /* Hari group */
  .hari-group { margin-bottom: 16px; }
  .hari-group:last-child { margin-bottom: 0; }
  .hari-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #475569;
    padding: 4px 0 4px 8px;
    border-left: 3px solid #f59e0b;
    margin-bottom: 6px;
  }

  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    text-align: left;
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .3px;
  }
  tbody td {
    padding: 7px 10px;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    vertical-align: middle;
  }
  tbody tr:nth-child(even) td { background: #f8fafc; }
  .td-jam { white-space: nowrap; font-family: 'Courier New', monospace; font-size: 11px; color: #b45309; font-weight: 700; width: 110px; }
  .td-mapel { font-weight: 600; }
  .td-kelas { color: #475569; }

  /* Empty */
  .empty-state { padding: 40px 28px; text-align: center; color: #94a3b8; }

  /* Footer */
  .doc-footer {
    padding: 12px 28px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 10px;
    color: #94a3b8;
  }

  /* Tanda tangan */
  .ttd-section {
    padding: 20px 28px 28px;
    display: flex;
    justify-content: flex-end;
    gap: 60px;
  }
  .ttd-box { text-align: center; }
  .ttd-label { font-size: 11px; color: #475569; margin-bottom: 48px; }
  .ttd-name { font-size: 12px; font-weight: 700; border-top: 1px solid #1e293b; padding-top: 4px; min-width: 140px; }

  /* PRINT */
  @media print {
    body { background: #fff; font-size: 11pt; }
    .toolbar { display: none !important; }
    .page-wrapper { padding: 0; max-width: 100%; }
    .doc { box-shadow: none; border-radius: 0; }
    thead th { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    tbody tr:nth-child(even) td { background: #f8f8f8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .guru-bar { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .doc-footer { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .tahun-badge { background: #ffe !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>
</head>
<body>
<div class="page-wrapper">

  {{-- Toolbar --}}
  <div class="toolbar">
    <span>Pratinjau Cetak &mdash; Jadwal Guru: {{ $guru->nama }}</span>
    <div class="toolbar-btns">
      <button class="btn-print" onclick="window.print()">&#128438; Cetak</button>
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
        <h1>Jadwal Pelajaran</h1>
        <div class="sub">Per Guru</div>
        @if($tahunAktif)
        <span class="tahun-badge">{{ $tahunAktif->nama }} &mdash; {{ $tahunAktif->semester }}</span>
        @endif
      </div>
    </div>

    {{-- Guru bar --}}
    <div class="guru-bar">
      <div class="guru-avatar">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
      <div>
        <div class="guru-nama">{{ $guru->nama }}</div>
        <div class="guru-meta">
          {{ $jadwal->flatten()->count() }} JP/minggu
          &nbsp;·&nbsp;
          {{ $jadwal->count() }} hari mengajar
          &nbsp;·&nbsp;
          {{ $jadwal->flatten()->pluck('kelas_id')->unique()->count() }} kelas
        </div>
      </div>
    </div>

    {{-- Jadwal per hari --}}
    @if($jadwal->isEmpty())
    <div class="empty-state">
      <p>Tidak ada jadwal untuk guru ini pada tahun ajaran yang dipilih.</p>
    </div>
    @else
    <div class="content">
      @foreach($namaHari as $hariNum => $hariNama)
        @php $jadwalHari = $jadwal->get($hariNum, collect()); @endphp
        @if($jadwalHari->isNotEmpty())
        <div class="hari-group">
          <div class="hari-title">{{ $hariNama }}</div>
          <table>
            <thead>
              <tr>
                <th style="width:110px">Jam</th>
                <th>Mata Pelajaran</th>
                <th style="width:140px">Kelas</th>
              </tr>
            </thead>
            <tbody>
              @foreach($jadwalHari as $j)
              <tr>
                <td class="td-jam">{{ substr($j->jamPelajaran->jam_mulai,0,5) }} – {{ substr($j->jamPelajaran->jam_selesai,0,5) }}</td>
                <td class="td-mapel">{{ $j->mapel->nama }}</td>
                <td class="td-kelas">Kelas {{ $j->kelas->nama }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @endif
      @endforeach
    </div>

    {{-- Tanda tangan --}}
    <div class="ttd-section">
      <div class="ttd-box">
        <div class="ttd-label">Mengetahui,<br>Kepala Sekolah</div>
        <div class="ttd-name">{{ $sekolah?->kepala_sekolah ?? '...........................' }}</div>
      </div>
      <div class="ttd-box">
        <div class="ttd-label">Guru yang bersangkutan</div>
        <div class="ttd-name">{{ $guru->nama }}</div>
      </div>
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
