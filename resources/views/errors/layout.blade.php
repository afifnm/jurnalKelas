<!DOCTYPE html>
<html lang="id"
    x-data="{ dark: localStorage.getItem('dark') === 'true' }"
    :class="dark ? 'dark' : ''"
    class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} — {{ $title }}</title>
    <link rel="icon" type="image/webp" href="{{ asset('logo.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('dark') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(-2deg); }
            50%       { transform: translateY(-20px) rotate(2deg); }
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-12px); }
        }
        @keyframes spin-slow    { from { transform: rotate(0deg); }   to { transform: rotate(360deg); } }
        @keyframes spin-reverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }
        @keyframes bounce-in {
            0%   { opacity: 0; transform: scale(0.4) translateY(40px); }
            60%  { transform: scale(1.08) translateY(-8px); }
            80%  { transform: scale(0.96) translateY(4px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse-ring {
            0%   { transform: scale(0.85); opacity: 0.7; }
            100% { transform: scale(1.7);  opacity: 0; }
        }
        @keyframes drift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33%       { transform: translate(10px, -14px) rotate(5deg); }
            66%       { transform: translate(-8px, 9px) rotate(-4deg); }
        }
        @keyframes blob-move {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -20px) scale(1.05); }
            66%       { transform: translate(-20px, 15px) scale(0.97); }
        }

        .anim-float       { animation: float 4s ease-in-out infinite; }
        .anim-float-slow  { animation: float-slow 6s ease-in-out infinite; }
        .anim-spin-slow   { animation: spin-slow 14s linear infinite; }
        .anim-spin-rev    { animation: spin-reverse 20s linear infinite; }
        .anim-bounce-in   { animation: bounce-in 0.75s cubic-bezier(.36,.07,.19,.97) both; }
        .anim-slide-up    { animation: slide-up 0.55s ease both; }
        .anim-pulse-ring  { animation: pulse-ring 2s ease-out infinite; }
        .anim-drift       { animation: drift 7s ease-in-out infinite; }
        .anim-blob        { animation: blob-move 10s ease-in-out infinite; }

        .d2{animation-delay:.2s}.d3{animation-delay:.3s}.d4{animation-delay:.4s}
        .d5{animation-delay:.5s}.d6{animation-delay:.7s}
        .ds1{animation-delay:1s}.ds2{animation-delay:2s}.ds3{animation-delay:3s}.ds4{animation-delay:4s}

        .text-error {
            font-size: clamp(7rem, 22vw, 14rem);
            font-weight: 800; line-height: 1;
            background: linear-gradient(135deg, #FBBF24 0%, #F59E0B 40%, #EAB308 70%, #F97316 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 8px 32px rgba(251,191,36,0.45));
            user-select: none;
        }
        .dark .text-error {
            background: linear-gradient(135deg, #FCD34D 0%, #FBBF24 40%, #F59E0B 80%, #FB923C 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 8px 40px rgba(251,191,36,0.35));
        }

        .blob { position:fixed;border-radius:9999px;filter:blur(80px);pointer-events:none; }
        .blob-1 { width:420px;height:420px;top:-120px;left:-100px;background:rgba(253,224,71,0.28); }
        .dark .blob-1 { background:rgba(202,138,4,0.12); }
        .blob-2 { width:500px;height:500px;bottom:-150px;right:-120px;background:rgba(251,191,36,0.22); }
        .dark .blob-2 { background:rgba(161,98,7,0.12); }
        .blob-3 { width:300px;height:300px;top:40%;left:30%;background:rgba(253,224,71,0.15); }
        .dark .blob-3 { background:rgba(63,63,70,0.4); }

        .orbit-ring { position:fixed;top:50%;left:50%;border-radius:9999px;border:1px solid rgba(251,191,36,0.18);pointer-events:none; }
        .dark .orbit-ring { border-color:rgba(202,138,4,0.15); }

        .particle { position:fixed;border-radius:9999px;pointer-events:none;background:rgba(251,191,36,0.55); }
        .dark .particle { background:rgba(251,191,36,0.35); }

        .pulse-ring { position:absolute;border-radius:9999px;background:rgba(251,191,36,0.18);width:180px;height:180px;top:50%;left:50%;transform:translate(-50%,-50%); }
        .dark .pulse-ring { background:rgba(202,138,4,0.14); }

        .btn-back {
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            padding:10px 22px;background:#fff;border:1px solid #e2e8f0;color:#374151;
            border-radius:12px;font-weight:600;font-size:14px;
            box-shadow:0 1px 3px rgba(0,0,0,0.08);text-decoration:none;
            transition:background .2s,transform .2s,box-shadow .2s;
        }
        .dark .btn-back { background:#27272a;border-color:#3f3f46;color:#e4e4e7; }
        .btn-back:hover { background:#f8fafc;transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,0.1); }
        .dark .btn-back:hover { background:#3f3f46; }

        .btn-home {
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            padding:10px 22px;background:#FBBF24;color:#1c1917;
            border-radius:12px;font-weight:600;font-size:14px;
            box-shadow:0 4px 14px rgba(251,191,36,0.35);text-decoration:none;
            transition:background .2s,transform .2s,box-shadow .2s;
        }
        .dark .btn-home { background:#F59E0B;box-shadow:0 4px 14px rgba(245,158,11,0.25); }
        .btn-home:hover { background:#F59E0B;transform:translateY(-2px);box-shadow:0 6px 20px rgba(251,191,36,0.45); }
        .dark .btn-home:hover { background:#FBBF24; }
    </style>
</head>
<body class="min-h-screen bg-amber-50 dark:bg-zinc-950 font-sans antialiased overflow-hidden"
      style="font-family: 'Plus Jakarta Sans', sans-serif;">

<div class="blob blob-1 anim-blob" aria-hidden="true"></div>
<div class="blob blob-2 anim-blob ds2" aria-hidden="true"></div>
<div class="blob blob-3 anim-blob ds1" aria-hidden="true"></div>

<div class="orbit-ring anim-spin-slow" style="width:560px;height:560px;margin-top:-280px;margin-left:-280px;" aria-hidden="true"></div>
<div class="orbit-ring anim-spin-rev"  style="width:380px;height:380px;margin-top:-190px;margin-left:-190px;border-style:dashed;opacity:.7;" aria-hidden="true"></div>
<div class="orbit-ring anim-spin-slow" style="width:740px;height:740px;margin-top:-370px;margin-left:-370px;opacity:.5;" aria-hidden="true"></div>

<div class="particle anim-drift"          style="width:12px;height:12px;top:15%;left:10%;"    aria-hidden="true"></div>
<div class="particle anim-drift ds2"      style="width:8px;height:8px;top:22%;right:14%;"     aria-hidden="true"></div>
<div class="particle anim-drift ds1"      style="width:16px;height:16px;bottom:22%;left:18%;" aria-hidden="true"></div>
<div class="particle anim-drift ds3"      style="width:10px;height:10px;bottom:28%;right:12%;" aria-hidden="true"></div>
<div class="particle anim-float-slow ds4" style="width:8px;height:8px;top:62%;left:6%;"      aria-hidden="true"></div>
<div class="particle anim-float-slow ds2" style="width:14px;height:14px;top:8%;right:28%;"   aria-hidden="true"></div>
<div class="particle anim-drift ds3"      style="width:6px;height:6px;top:45%;right:5%;"     aria-hidden="true"></div>
<div class="particle anim-float-slow ds1" style="width:10px;height:10px;bottom:12%;left:40%;" aria-hidden="true"></div>

<div class="relative min-h-screen flex flex-col items-center justify-center px-6 py-12 text-center">

    <div class="relative flex items-center justify-center mb-6">
        <div class="pulse-ring anim-pulse-ring" aria-hidden="true"></div>
        <div class="pulse-ring anim-pulse-ring d5" aria-hidden="true"></div>

        <div class="relative anim-bounce-in anim-float">
            <span class="text-error block">{{ $code }}</span>
            <span class="absolute -top-4 -right-8 text-5xl anim-float d2 select-none" role="img" aria-label="ikon">{{ $emoji }}</span>
        </div>
    </div>

    <div class="anim-slide-up d3 max-w-md">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mb-3">
            {{ $title }}
        </h1>
        <p class="text-slate-500 dark:text-zinc-400 text-base leading-relaxed mb-8">
            {{ $description }}
        </p>
    </div>

    <div class="anim-slide-up d4 flex flex-col sm:flex-row gap-3 justify-center">
        <a href="javascript:history.back()" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <a href="{{ url('/') }}" class="btn-home">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Ke Beranda
        </a>
    </div>

    <div class="anim-slide-up d6 mt-16 flex items-center gap-2" style="opacity:.45;">
        <img src="{{ asset('logo.webp') }}" alt="Logo" class="w-5 h-5 object-contain">
        <span class="text-xs text-slate-500 dark:text-zinc-500 font-medium">SMK Pemnas Sukoharjo · Jurnal Kelas</span>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
