<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jurnal Guru</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef2f7; color: #172033; font: 12px Arial, sans-serif; }
        .wrapper { max-width: 1400px; margin: auto; padding: 20px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; padding: 12px 16px; border-radius: 10px; background: #172033; color: white; }
        .toolbar-actions { display: flex; gap: 8px; }
        button { border: 0; border-radius: 7px; padding: 8px 14px; cursor: pointer; font-weight: 700; }
        .print-button { background: #facc15; color: #172033; }
        .close-button { background: #334155; color: white; }
        .document { overflow: hidden; border-radius: 12px; background: white; box-shadow: 0 5px 20px rgba(15, 23, 42, .10); }
        .header { display: flex; align-items: center; gap: 16px; padding: 22px 26px 16px; border-bottom: 3px double #334155; }
        .logo { width: 58px; height: 58px; object-fit: contain; }
        .school { flex: 1; }
        .school h1 { margin: 0 0 4px; font-size: 18px; }
        .school p, .title p { margin: 2px 0; color: #64748b; }
        .title { text-align: right; }
        .title h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .filters { display: flex; flex-wrap: wrap; gap: 8px 18px; padding: 12px 26px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .filter strong { color: #0f172a; }
        .summary { display: flex; gap: 10px; padding: 12px 26px; }
        .stat { min-width: 120px; padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }
        .stat span { display: block; color: #64748b; font-size: 10px; text-transform: uppercase; }
        .stat strong { display: block; margin-top: 2px; font-size: 17px; }
        .table-wrap { padding: 0 26px 24px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 8px; vertical-align: top; }
        th { background: #e2e8f0; font-size: 10px; text-align: left; text-transform: uppercase; }
        td.center, th.center { text-align: center; }
        .sub { margin-top: 2px; color: #64748b; font-size: 10px; }
        .time { font-family: Consolas, monospace; font-weight: 700; white-space: nowrap; }
        .status { display: inline-block; border-radius: 20px; padding: 3px 8px; font-size: 10px; font-weight: 700; white-space: nowrap; }
        .status-ok { background: #dcfce7; color: #166534; }
        .status-late { background: #fee2e2; color: #991b1b; }
        .empty { padding: 35px; text-align: center; color: #64748b; }
        .footer { padding: 10px 26px; border-top: 1px solid #e2e8f0; background: #f8fafc; color: #64748b; font-size: 10px; text-align: right; }
        @page { size: A4 landscape; margin: 10mm; }
        @media print {
            body { background: white; font-size: 9px; }
            .wrapper { max-width: none; padding: 0; }
            .toolbar { display: none; }
            .document { border-radius: 0; box-shadow: none; }
            .header { padding: 0 0 10px; }
            .filters, .summary { padding-left: 0; padding-right: 0; }
            .table-wrap { padding: 0; overflow: visible; }
            th, td { padding: 5px; }
            th { background: #e5e7eb !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .status { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
            .footer { padding: 8px 0 0; background: white; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="toolbar">
        <span>Pratinjau cetak sesuai filter halaman Jurnal Guru</span>
        <div class="toolbar-actions">
            <button type="button" class="print-button" onclick="window.print()">Cetak / Simpan PDF</button>
            <button type="button" class="close-button" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <main class="document">
        <header class="header">
            <img class="logo" src="{{ asset('logo.webp') }}" alt="Logo sekolah">
            <div class="school">
                <h1>{{ $sekolah?->nama ?? config('app.name') }}</h1>
                @if($sekolah?->alamat)<p>{{ $sekolah->alamat }}</p>@endif
                @if($sekolah?->npsn)<p>NPSN: {{ $sekolah->npsn }}</p>@endif
            </div>
            <div class="title">
                <h2>Laporan Jurnal Guru</h2>
                <p>Semua jurnal mengajar sesuai filter</p>
                @if($tahunAktif)<p><strong>{{ $tahunAktif->nama }} · {{ $tahunAktif->semester }}</strong></p>@endif
            </div>
        </header>

        <section class="filters">
            <div class="filter">Periode: <strong>{{ $periodeLabel }}</strong></div>
            <div class="filter">Guru: <strong>{{ $guruFilter?->nama ?? 'Semua Guru' }}</strong></div>
            <div class="filter">Kelas: <strong>{{ $kelasFilter?->nama ?? 'Semua Kelas' }}</strong></div>
            <div class="filter">Dicetak: <strong>{{ now()->locale('id')->translatedFormat('j F Y, H:i') }} WIB</strong></div>
        </section>

        <section class="summary">
            <div class="stat"><span>Total Jurnal</span><strong>{{ $jurnal->count() }}</strong></div>
            <div class="stat"><span>Dalam Jam</span><strong>{{ $totalDalamJam }}</strong></div>
            <div class="stat"><span>Di Luar Jam</span><strong>{{ $totalLuarJam }}</strong></div>
        </section>

        <div class="table-wrap">
            @if($jurnal->isEmpty())
                <div class="empty">Tidak ada jurnal yang sesuai dengan filter.</div>
            @else
                <table>
                    <thead>
                    <tr>
                        <th class="center" style="width:34px">No.</th>
                        <th style="width:92px">Tanggal</th>
                        <th style="width:130px">Guru</th>
                        <th style="width:115px">Kelas / Mapel</th>
                        <th style="width:110px">Jam Pelajaran</th>
                        <th style="width:78px">Jam Input</th>
                        <th style="width:82px">Status</th>
                        <th>Materi</th>
                        <th>Catatan</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($jurnal as $item)
                        @php
                            $jamSesi = $jamSesiMap[$item->id] ?? null;
                            $dalamJam = $item->isInputDalamJamMengajar($jamSesi);
                            $jamInputMentah = (string) $item->getRawOriginal('created_at');
                        @endphp
                        <tr>
                            <td class="center">{{ $loop->iteration }}</td>
                            <td>{{ $item->tanggal->copy()->locale('id')->translatedFormat('d M Y') }}</td>
                            <td><strong>{{ $item->guru->nama }}</strong></td>
                            <td>
                                <strong>{{ $item->kelas->nama }}</strong>
                                <div class="sub">{{ $item->mapel->nama }}</div>
                            </td>
                            <td>
                                <div class="time">{{ $jamSesi ? $jamSesi['mulai'].'–'.$jamSesi['selesai'] : '-' }}</div>
                                @if($jamSesi)
                                    <div class="sub">Jam ke-{{ $jamSesi['jam_ke_mulai'] }}–{{ $jamSesi['jam_ke_selesai'] }} · {{ $jamSesi['jumlah'] }} JP</div>
                                @endif
                            </td>
                            <td class="time">{{ strlen($jamInputMentah) >= 16 ? substr($jamInputMentah, 11, 5) : '-' }} WIB</td>
                            <td><span class="status {{ $dalamJam ? 'status-ok' : 'status-late' }}">{{ $dalamJam ? 'Dalam jam' : 'Di luar jam' }}</span></td>
                            <td>{{ $item->materi }}</td>
                            <td>{{ $item->catatan ?: '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <footer class="footer">Dokumen dibuat oleh {{ config('app.name') }}</footer>
    </main>
</div>
</body>
</html>
