<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Hanken Grotesk', sans-serif;
            background: #0A1128;
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { text-align: center; padding: 2rem; }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 1rem;
        }
        h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: #e2e8f0; }
        p { color: #94a3b8; font-size: 0.95rem; margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto; }
        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1.5rem; background: #3b82f6; color: white;
            text-decoration: none; border-radius: 0.75rem; font-weight: 600; font-size: 0.875rem; transition: background 0.2s;
        }
        .btn:hover { background: #2563eb; }
        .decoration { position: fixed; border-radius: 50%; opacity: 0.08; filter: blur(60px); }
        .d1 { width: 300px; height: 300px; background: #f59e0b; top: -100px; right: -100px; }
        .d2 { width: 200px; height: 200px; background: #ef4444; bottom: -50px; left: -50px; }
    </style>
</head>
<body>
    <div class="decoration d1"></div>
    <div class="decoration d2"></div>
    <div class="container">
        <div class="error-code">403</div>
        <h1>Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="/" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
