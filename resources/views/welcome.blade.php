<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BSH Asset — {{ __('Villa Management System') }}</title>
    <meta name="description" content="{{ __('Sistem manajemen aset villa BSH yang modern, aman, dan efisien.') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream: #f8f5ef;
            --dark-green: #2d3d2d;
            --mid-green: #4a6741;
            --light-green: #7a9e70;
            --gold: #c9a84c;
            --gold-light: #e8c96a;
            --dark: #1a2518;
            --text-muted: #7a8c75;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--cream); color: var(--dark-green); overflow-x: hidden; }

        /* NAVBAR */
        nav { position: fixed; top: 0; width: 100%; z-index: 100; padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; transition: background .3s, box-shadow .3s; }
        nav.scrolled { background: rgba(248,245,239,.96); backdrop-filter: blur(12px); box-shadow: 0 2px 20px rgba(0,0,0,.08); }
        .nav-logo img { height: 42px; width: auto; cursor: pointer; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { text-decoration: none; color: white; font-weight: 500; font-size: .9rem; transition: opacity .2s; }
        nav.scrolled .nav-links a { color: var(--dark-green); }
        .nav-links a:hover { opacity: .7; }
        .btn-nav { background: var(--gold); color: white !important; padding: .55rem 1.3rem; border-radius: 50px; font-weight: 600 !important; transition: background .2s, transform .2s !important; }
        .btn-nav:hover { background: var(--gold-light) !important; transform: translateY(-1px); opacity: 1 !important; }

        /* Lang switcher in nav */
        .lang-switcher { display: flex; align-items: center; gap: .3rem; }
        .lang-btn {
            font-size: .75rem; font-weight: 700; padding: .3rem .6rem; border-radius: 6px;
            text-decoration: none; border: 1.5px solid transparent;
            color: rgba(255,255,255,.7); transition: all .2s;
        }
        nav.scrolled .lang-btn { color: var(--text-muted); }
        .lang-btn.active { border-color: var(--gold); color: var(--gold-light) !important; background: rgba(201,168,76,.15); }
        nav.scrolled .lang-btn.active { color: var(--gold) !important; background: rgba(201,168,76,.1); }
        .lang-btn:hover:not(.active) { border-color: rgba(255,255,255,.4); color: white !important; }
        nav.scrolled .lang-btn:hover:not(.active) { border-color: var(--mid-green); color: var(--dark-green) !important; }
        .lang-sep { color: rgba(255,255,255,.3); font-size: .7rem; }
        nav.scrolled .lang-sep { color: #ccc; }

        /* HERO */
        #hero { min-height: 100vh; background: linear-gradient(160deg, var(--dark) 0%, var(--dark-green) 50%, var(--mid-green) 100%); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; text-align: center; padding: 6rem 1.5rem 5rem; }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: .25; pointer-events: none; animation: float 8s ease-in-out infinite; }
        .orb-1 { width: 500px; height: 500px; background: var(--mid-green); top: -150px; left: -150px; }
        .orb-2 { width: 400px; height: 400px; background: var(--gold); bottom: -100px; right: -100px; animation-delay: -3s; }
        .orb-3 { width: 300px; height: 300px; background: var(--light-green); top: 40%; left: 60%; animation-delay: -5s; }
        @keyframes float { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-30px) scale(1.05)} }
        #hero::before { content:''; position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px); background-size:60px 60px; }
        .hero-content { position: relative; z-index: 1; max-width: 800px; }
        .hero-badge { display:inline-flex; align-items:center; gap:.5rem; background:rgba(201,168,76,.15); border:1px solid rgba(201,168,76,.4); color:var(--gold-light); padding:.4rem 1rem; border-radius:50px; font-size:.8rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; margin-bottom:1.8rem; animation:fadeInUp .8s ease both; }
        h1.hero-title { font-family:'Playfair Display',serif; font-size:clamp(2.8rem,6vw,5rem); font-weight:900; line-height:1.1; color:white; margin-bottom:1.5rem; animation:fadeInUp .8s .15s ease both; }
        h1.hero-title em { color:var(--gold-light); font-style:normal; }
        .hero-sub { font-size:1.05rem; color:rgba(255,255,255,.7); line-height:1.7; max-width:560px; margin:0 auto 2.5rem; animation:fadeInUp .8s .3s ease both; }
        .hero-actions { display:flex; gap:1rem; justify-content:center; flex-wrap:wrap; animation:fadeInUp .8s .45s ease both; }
        .btn-primary { background:linear-gradient(135deg,var(--gold),var(--gold-light)); color:white; padding:.9rem 2rem; border-radius:50px; font-weight:600; font-size:1rem; text-decoration:none; box-shadow:0 4px 20px rgba(201,168,76,.4); transition:transform .2s,box-shadow .2s; display:inline-flex; align-items:center; gap:.5rem; }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 30px rgba(201,168,76,.5); }
        .btn-outline { border:2px solid rgba(255,255,255,.3); color:white; padding:.9rem 2rem; border-radius:50px; font-weight:600; font-size:1rem; text-decoration:none; transition:border-color .2s,background .2s; display:inline-flex; align-items:center; gap:.5rem; }
        .btn-outline:hover { border-color:white; background:rgba(255,255,255,.08); }
        .stats-strip { position:absolute; bottom:0; left:0; right:0; background:rgba(255,255,255,.06); backdrop-filter:blur(8px); border-top:1px solid rgba(255,255,255,.1); display:flex; justify-content:center; gap:4rem; padding:1.2rem 2rem; flex-wrap:wrap; }
        .stat { text-align:center; }
        .stat-num { font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:700; color:var(--gold-light); }
        .stat-lbl { font-size:.75rem; color:rgba(255,255,255,.6); letter-spacing:.06em; text-transform:uppercase; }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }

        /* SECTIONS */
        section { padding: 5rem 1.5rem; }
        .container { max-width: 1100px; margin: 0 auto; }
        .section-label { font-size:.75rem; font-weight:700; letter-spacing:.15em; text-transform:uppercase; color:var(--gold); display:block; margin-bottom:.6rem; }
        .section-title { font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,2.8rem); font-weight:700; color:var(--dark-green); line-height:1.2; margin-bottom:1rem; }
        .section-sub { color:var(--text-muted); line-height:1.7; max-width:540px; }

        /* FEATURES */
        #features { background: white; }
        .features-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:2rem; margin-top:3rem; }
        .feature-card { background:var(--cream); border-radius:20px; padding:2rem; border:1px solid rgba(0,0,0,.06); transition:transform .3s,box-shadow .3s; position:relative; overflow:hidden; }
        .feature-card::before { content:''; position:absolute; inset:0; background:linear-gradient(135deg,transparent 60%,rgba(201,168,76,.08)); pointer-events:none; }
        .feature-card:hover { transform:translateY(-6px); box-shadow:0 20px 50px rgba(0,0,0,.1); }
        .feature-icon { width:56px; height:56px; border-radius:16px; background:linear-gradient(135deg,var(--dark-green),var(--mid-green)); display:flex; align-items:center; justify-content:center; font-size:1.6rem; margin-bottom:1.2rem; box-shadow:0 8px 20px rgba(45,61,45,.25); }
        .feature-card h3 { font-family:'Playfair Display',serif; font-size:1.2rem; font-weight:700; color:var(--dark-green); margin-bottom:.5rem; }
        .feature-card p { color:var(--text-muted); font-size:.93rem; line-height:1.6; }

        /* HOW */
        #how { background: var(--cream); }
        .steps { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:2rem; margin-top:3rem; }
        .step { text-align:center; }
        .step-num { width:64px; height:64px; border-radius:50%; background:linear-gradient(135deg,var(--gold),var(--gold-light)); color:white; font-family:'Playfair Display',serif; font-size:1.6rem; font-weight:700; display:flex; align-items:center; justify-content:center; margin:0 auto 1.2rem; box-shadow:0 8px 24px rgba(201,168,76,.35); }
        .step h3 { font-size:1.05rem; font-weight:600; color:var(--dark-green); margin-bottom:.4rem; }
        .step p  { font-size:.88rem; color:var(--text-muted); line-height:1.6; }

        /* MODULES */
        #modules { background: white; }
        .modules-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.2rem; margin-top:3rem; }
        .module-card { border:1.5px solid rgba(0,0,0,.08); border-radius:16px; padding:1.4rem; display:flex; align-items:center; gap:1rem; transition:border-color .2s,background .2s,transform .2s; }
        .module-card:hover { border-color:var(--mid-green); background:rgba(74,103,65,.04); transform:translateY(-2px); }
        .module-icon { font-size:2rem; }
        .module-name { font-weight:600; font-size:.95rem; color:var(--dark-green); }
        .module-desc { font-size:.8rem; color:var(--text-muted); margin-top:.15rem; }

        /* CTA */
        #cta { background:linear-gradient(135deg,var(--dark-green),var(--dark)); text-align:center; padding:6rem 1.5rem; position:relative; overflow:hidden; }
        #cta::before { content:''; position:absolute; inset:0; background:radial-gradient(ellipse at center,rgba(201,168,76,.15) 0%,transparent 70%); }
        #cta h2 { font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.2rem); font-weight:800; color:white; margin-bottom:1rem; position:relative; }
        #cta p { color:rgba(255,255,255,.65); font-size:1.05rem; margin-bottom:2.5rem; position:relative; }

        /* FOOTER */
        footer { background:var(--dark); color:rgba(255,255,255,.5); text-align:center; padding:2rem; font-size:.85rem; }
        footer strong { color:var(--gold-light); }

        /* REVEAL */
        .reveal { opacity:0; transform:translateY(30px); transition:opacity .7s ease,transform .7s ease; }
        .reveal.visible { opacity:1; transform:translateY(0); }

        @media(max-width:600px){ .nav-links { gap: .8rem; } .stats-strip { gap: 2rem; } }
    </style>
</head>
<body>

<nav id="navbar">
    <div class="nav-logo">
        <a href="javascript:location.reload()"><img src="{{ asset('images/Primier-Logo.webp') }}" alt="BSH Logo"></a>
    </div>
    <div class="nav-links">
        <a href="#features">{{ __('Fitur') }}</a>
        <a href="#how">{{ __('Cara Kerja') }}</a>
        <a href="#modules">{{ __('Modul') }}</a>

        {{-- Language Switcher --}}
        <div class="lang-switcher">
            <a href="{{ route('lang.switch', 'id') }}" class="lang-btn {{ App::getLocale() == 'id' ? 'active' : '' }}">ID</a>
            <span class="lang-sep">|</span>
            <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ App::getLocale() == 'en' ? 'active' : '' }}">EN</a>
        </div>

        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-nav">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="btn-nav">{{ __('Login') }}</a>
            @endauth
        @endif
    </div>
</nav>

<!-- HERO -->
<section id="hero">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="hero-content">
        <div class="hero-badge"><span>🏡</span> {{ __('Villa Management Asset System') }}</div>
        <h1 class="hero-title">
            {{ __('Kelola Aset Villa') }}<br>{{ __('dengan') }} <em>{{ __('Cerdas & Efisien') }}</em>
        </h1>
        <p class="hero-sub">{{ __('Platform manajemen aset terpadu untuk villa & properti. Pantau inventaris, kelola peminjaman, lacak riwayat, dan atur hak akses tim — semua dalam satu sistem.') }}</p>
        <div class="hero-actions">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary">🚀 {{ __('Buka Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">🔑 {{ __('Masuk Sekarang') }}</a>
                @endauth
            @endif
            <a href="#features" class="btn-outline">✨ {{ __('Lihat Fitur') }}</a>
        </div>
    </div>
    <div class="stats-strip">
        <div class="stat">
            <div class="stat-num">{{ \App\Models\Aset::count() }}+</div>
            <div class="stat-lbl">{{ __('Total Aset') }}</div>
        </div>
        <div class="stat">
            <div class="stat-num">{{ \App\Models\User::where('status','active')->count() }}+</div>
            <div class="stat-lbl">{{ __('Pengguna Aktif') }}</div>
        </div>
        <div class="stat">
            <div class="stat-num">{{ \App\Models\ProductHistory::count() }}+</div>
            <div class="stat-lbl">{{ __('Total Transaksi') }}</div>
        </div>
        <div class="stat">
            <div class="stat-num">{{ \App\Models\Department::count() }}</div>
            <div class="stat-lbl">{{ __('Departemen') }}</div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features">
    <div class="container">
        <span class="section-label reveal">{{ __('Mengapa BSH Asset?') }}</span>
        <h2 class="section-title reveal">{{ __('Fitur Unggulan untuk Manajemen Properti Modern') }}</h2>
        <p class="section-sub reveal">{{ __('Dirancang khusus untuk kebutuhan operasional villa dengan kontrol penuh dari ujung ke ujung.') }}</p>
        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon">📦</div>
                <h3>{{ __('Inventaris Aset Real-Time') }}</h3>
                <p>{{ __('Pantau seluruh aset villa secara real-time. Lacak status, lokasi, dan kondisi setiap barang dengan akurat.') }}</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">🔄</div>
                <h3>{{ __('Peminjaman & Pengembalian') }}</h3>
                <p>{{ __('Proses peminjaman aset yang mudah dengan STTB digital otomatis. Riwayat lengkap tersimpan permanen.') }}</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">📋</div>
                <h3>{{ __('Permintaan Aset Berjenjang') }}</h3>
                <p>{{ __('Alur persetujuan 5 tahap: HR → Departemen → IT → Managing Director untuk kontrol pengeluaran optimal.') }}</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">🔒</div>
                <h3>{{ __('Kontrol Akses Granular') }}</h3>
                <p>{{ __('Atur hak akses per role secara detail. Setiap fitur dapat dikonfigurasi siapa yang boleh mengakses.') }}</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">📊</div>
                <h3>{{ __('Export & Import Data') }}</h3>
                <p>{{ __('Sinkronisasi data aset dan karyawan dengan mudah melalui file Excel. Mendukung format XLSX dan CSV.') }}</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">📱</div>
                <h3>{{ __('QR Code Tracking') }}</h3>
                <p>{{ __('Setiap aset memiliki QR Code unik. Scan untuk melihat detail, spesifikasi, dan pemegang saat ini.') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section id="how">
    <div class="container">
        <span class="section-label reveal">{{ __('Cara Kerja') }}</span>
        <h2 class="section-title reveal">{{ __('Mulai dalam 4 Langkah Mudah') }}</h2>
        <div class="steps">
            <div class="step reveal">
                <div class="step-num">1</div>
                <h3>{{ __('Login & Atur Role') }}</h3>
                <p>{{ __('Masuk ke sistem dan konfigurasikan role serta hak akses untuk setiap anggota tim.') }}</p>
            </div>
            <div class="step reveal">
                <div class="step-num">2</div>
                <h3>{{ __('Daftarkan Aset') }}</h3>
                <p>{{ __('Input atau import data aset villa Anda lengkap dengan spesifikasi dan kategorisasi.') }}</p>
            </div>
            <div class="step reveal">
                <div class="step-num">3</div>
                <h3>{{ __('Kelola Transaksi') }}</h3>
                <p>{{ __('Proses peminjaman, pengembalian, dan transfer aset antar departemen dengan mudah.') }}</p>
            </div>
            <div class="step reveal">
                <div class="step-num">4</div>
                <h3>{{ __('Pantau & Laporkan') }}</h3>
                <p>{{ __('Monitor seluruh aktivitas, cetak STTB, dan ekspor laporan kapan pun dibutuhkan.') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- MODULES -->
<section id="modules">
    <div class="container">
        <span class="section-label reveal">{{ __('Modul Sistem') }}</span>
        <h2 class="section-title reveal">{{ __('Semua yang Anda Butuhkan, Dalam Satu Platform') }}</h2>
        <div class="modules-grid">
            <div class="module-card reveal"><div class="module-icon">🏢</div><div><div class="module-name">{{ __('Master Data') }}</div><div class="module-desc">{{ __('Kantor') }}, {{ __('Departemen') }}, {{ __('Klasifikasi') }}</div></div></div>
            <div class="module-card reveal"><div class="module-icon">📦</div><div><div class="module-name">{{ __('Daftar Aset') }}</div><div class="module-desc">CRUD, QR Code, {{ __('Spesifikasi Teknis') }}</div></div></div>
            <div class="module-card reveal"><div class="module-icon">👥</div><div><div class="module-name">{{ __('Karyawan') }}</div><div class="module-desc">{{ __('Profil') }}, Import/Export</div></div></div>
            <div class="module-card reveal"><div class="module-icon">🔑</div><div><div class="module-name">{{ __('Management Account') }}</div><div class="module-desc">{{ __('Login') }}, Role, Resign</div></div></div>
            <div class="module-card reveal"><div class="module-icon">🤝</div><div><div class="module-name">{{ __('Transaksi') }}</div><div class="module-desc">{{ __('Pinjam Aset') }}, {{ __('Kembali Aset') }}, STTB</div></div></div>
            <div class="module-card reveal"><div class="module-icon">📋</div><div><div class="module-name">{{ __('Request Aset') }}</div><div class="module-desc">{{ __('Persetujuan Berjenjang') }}</div></div></div>
            <div class="module-card reveal"><div class="module-icon">📜</div><div><div class="module-name">{{ __('Riwayat Mutasi') }}</div><div class="module-desc">{{ __('Audit Trail Lengkap') }}</div></div></div>
            <div class="module-card reveal"><div class="module-icon">🛡️</div><div><div class="module-name">{{ __('Role & Hak Akses') }}</div><div class="module-desc">{{ __('Matriks Hak Akses Fitur') }}</div></div></div>
        </div>
    </div>
</section>

<!-- CTA -->
<section id="cta">
    <div class="container" style="position:relative;">
        <h2>{{ __('Siap Mengelola Aset Villa Anda dengan Lebih Baik?') }}</h2>
        <p>{{ __('Masuk sekarang dan rasakan kemudahan manajemen aset yang sesungguhnya.') }}</p>
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-primary" style="display:inline-flex;">🚀 {{ __('Buka Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary" style="display:inline-flex;">🔑 {{ __('Masuk Sekarang') }}</a>
            @endauth
        @endif
    </div>
</section>

<footer>
    <p>&copy; {{ date('Y') }} <strong>BSH Asset Management</strong> — {{ __('Villa Management System') }}. All rights reserved.</p>
</footer>

<script>
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => { navbar.classList.toggle('scrolled', window.scrollY > 60); });
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) { setTimeout(() => entry.target.classList.add('visible'), i * 80); observer.unobserve(entry.target); }
        });
    }, { threshold: 0.12 });
    reveals.forEach(el => observer.observe(el));
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
        });
    });
</script>
</body>
</html>
