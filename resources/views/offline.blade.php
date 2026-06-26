<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline — Jurnal Kelas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fafafa; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 40px; text-align: center; max-width: 360px; }
        .icon { font-size: 4rem; margin-bottom: 16px; }
        h1 { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        p { color: #94a3b8; font-size: 0.875rem; line-height: 1.6; margin-bottom: 24px; }
        button { background: #FACC15; color: #1e293b; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; font-size: 0.875rem; cursor: pointer; }
        button:hover { background: #EAB308; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">📶</div>
        <h1>Tidak Ada Koneksi</h1>
        <p>Anda sedang offline. Periksa koneksi internet Anda dan coba lagi.</p>
        <button onclick="location.reload()">Coba Lagi</button>
    </div>
</body>
</html>
