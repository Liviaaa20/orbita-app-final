<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ORBITA | Dashboard</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    /* Warna Utama BMKG: Biru Navy */
    :root {
      --bmkg-blue: #003366;
      --bmkg-blue-light: #00509e;
      --bmkg-accent: #f8f9fa;
    }

    /* Sidebar - Biru Navy Profesional */
    .main-sidebar { 
        background-color: var(--bmkg-blue) !important; 
    }
    .brand-link { 
        background-color: var(--bmkg-blue) !important; 
        border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        color: #fff !important;
    }
    
    /* Navigasi Sidebar */
    .nav-sidebar .nav-link { 
        color: #ced4da !important; 
        border-radius: 8px !important; 
        margin: 2px 10px; 
    }
    .nav-sidebar .nav-link:hover {
        background-color: rgba(255,255,255,0.1) !important;
        color: #fff !important;
    }
    .nav-sidebar .nav-link.active { 
        background-color: var(--bmkg-blue-light) !important; 
        color: #fff !important; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }
    .nav-header {
        color: #888 !important;
        text-transform: uppercase;
        font-size: 0.8rem;
    }

    /* Navbar Atas */
    .main-header {
        background-color: #ffffff !important;
        border-bottom: 2px solid var(--bmkg-blue) !important;
    }
    
    /* Content Area */
    .content-wrapper { 
        background-color: #f4f6f9; 
    }

    /* Dropdown Profil Khas */
    .user-menu .dropdown-menu {
        border-radius: 12px !important;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
    }
    .user-header {
        background-color: var(--bmkg-blue) !important;
        color: white !important;
    }
    
    /* Perbaikan Icon */
    .nav-icon {
        color: inherit !important;
    }

    /* ===================================================== */
    /* NOTIFIKASI LONCENG - Permintaan Perbaikan (BARU)       */
    /* ===================================================== */
    .nav-link.notif-bell {
        position: relative;
    }
    .notif-bell .fa-bell {
        font-size: 1.15rem;
        color: var(--bmkg-blue);
    }
    .notif-badge-dot {
        position: absolute;
        top: 4px;
        right: 2px;
        min-width: 16px;
        height: 16px;
        padding: 0 3px;
        border-radius: 999px;
        background-color: #dc3545;
        color: #fff;
        font-size: 10px;
        line-height: 16px;
        text-align: center;
        font-weight: 700;
        box-shadow: 0 0 0 2px #fff;
    }
    .notif-badge-dot.notif-koordinator {
        background-color: #fd7e14;
    }
    .notif-dropdown-menu {
        width: 320px;
        max-width: 90vw;
        padding: 0;
        border-radius: 12px !important;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
        overflow: hidden;
    }
    .notif-dropdown-header {
        background-color: var(--bmkg-blue);
        color: #fff;
        padding: 10px 14px;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .notif-dropdown-list {
        max-height: 280px;
        overflow-y: auto;
    }
    .notif-item {
        display: block;
        padding: 10px 14px;
        border-bottom: 1px solid #f0f0f0;
        white-space: normal;
        color: #333 !important;
    }
    .notif-item:hover {
        background-color: #f8f9fa;
        text-decoration: none;
    }
    .notif-item .notif-title {
        font-weight: 600;
        font-size: 13px;
        color: var(--bmkg-blue);
        margin-bottom: 2px;
    }
    .notif-item .notif-sub {
        font-size: 12px;
        color: #666;
    }
    .notif-item .notif-time {
        font-size: 11px;
        color: #999;
    }
    .notif-empty {
        padding: 24px 14px;
        text-align: center;
        color: #999;
        font-size: 13px;
    }
    .notif-dropdown-footer {
        padding: 8px 14px;
        text-align: center;
        background: #f8f9fa;
    }
    .notif-dropdown-footer a {
        font-size: 12px;
        font-weight: 600;
    }
    /* Watermark */
.app-watermark {
    position: fixed;
    bottom: 15px;
    right: 20px;
    z-index: 9999;

    font-size: 12px;
    line-height: 1.5;
    text-align: right;

    color: rgba(0, 51, 102, 0.35); /* Biru BMKG, lebih terlihat */
    font-weight: 500;
    letter-spacing: 0.3px;

    user-select: none;
    pointer-events: none;
}
  </style>
</head>
<div class="app-watermark">
    ORBITA v1.0<br>
    © 2026 Universitas Semarang<br>
    Developed by<br>
    Felina Devi Maharani<br>
    Rossa Livia Monika Hardianti<br>
    Ivana Purbaningtyas
</div>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <span class="nav-link font-weight-bold text-dark">Dashboard Monitoring Alat BMKG</span>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">

    {{-- ===================================================== --}}
    {{-- NOTIFIKASI LONCENG - Permintaan Perbaikan (BARU)       --}}
    {{-- Tampil hanya untuk role Teknisi & Koordinator.         --}}
    {{-- $notifBell disediakan otomatis via View Composer       --}}
    {{-- (lihat App\Providers\AppServiceProvider).               --}}
    {{-- ===================================================== --}}
    @if(isset($notifBell) && $notifBell['type'])
    <li class="nav-item dropdown">
      <a href="#" class="nav-link notif-bell" data-toggle="dropdown" title="Notifikasi">
        <i class="fas fa-bell"></i>
        @if($notifBell['count'] > 0)
          <span class="notif-badge-dot {{ $notifBell['type'] == 'koordinator' ? 'notif-koordinator' : '' }}">
            {{ $notifBell['count'] > 9 ? '9+' : $notifBell['count'] }}
          </span>
        @endif
      </a>

      <div class="dropdown-menu dropdown-menu-right notif-dropdown-menu">

        <div class="notif-dropdown-header">
          @if($notifBell['type'] == 'koordinator')
            Menunggu Verifikasi
          @else
            Permintaan Perbaikan Baru
          @endif
        </div>

        <div class="notif-dropdown-list">

          @forelse($notifBell['items'] as $item)

            <a href="{{ route('perbaikan.index') }}" class="notif-item">

              @if($notifBell['type'] == 'koordinator')
                <div class="notif-title">{{ $item->no_tiket }}</div>
                <div class="notif-sub">{{ $item->kategori_perbaikan }} &middot; oleh {{ $item->user }}</div>
              @else
                <div class="notif-title">Permintaan dari {{ $item->user }}</div>
                <div class="notif-sub">{{ $item->kategori_perbaikan }}{{ $item->alat ? ' · ' . $item->alat->nama_alat : '' }}</div>
              @endif

              <div class="notif-time">
                <i class="far fa-clock"></i>
                {{ \Carbon\Carbon::parse($item->tgl_permintaan)->diffForHumans() }}
              </div>

            </a>

          @empty

            <div class="notif-empty">
              <i class="far fa-bell-slash fa-lg mb-2 d-block"></i>
              Tidak ada notifikasi baru
            </div>

          @endforelse

        </div>

        <div class="notif-dropdown-footer">
          <a href="{{ route('perbaikan.index') }}">
            Lihat Semua Permintaan Perbaikan
          </a>
        </div>

      </div>
    </li>
    @endif

    <li class="nav-item dropdown user-menu">
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-user-circle fa-lg mr-2 text-secondary"></i>
        <span class="d-none d-md-inline font-weight-bold">{{ Auth::user()->username ?? 'User' }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-sm">
        <li class="user-header bg-light">
          <i class="fas fa-user-circle fa-4x text-secondary mb-2"></i>
          <p>
            {{ Auth::user()->username ?? 'User' }}
<small>Role: {{ Auth::user()->role->nama_role ?? 'Admin' }}</small>          </p>
        </li>
        <li class="user-footer d-flex justify-content-between">
          <a href="{{ route('profile') }}" class="btn btn-default btn-flat border-0 rounded">
            <i class="fas fa-id-card mr-1"></i> Profil
          </a>
          <a href="#" class="btn btn-default btn-flat border-0 rounded text-danger" 
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt mr-1"></i> Keluar
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
          </form>
        </li>
      </ul>
    </li>
  </ul>
</nav>

  <aside class="main-sidebar sidebar-light-primary elevation-1">
    <a href="#" class="brand-link">
      <img src="{{ asset('assets/dist/img/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-bold ml-2">ORBITA</span>
    </a>

    <div class="sidebar">
    
@php
    $userRole = strtolower(trim(Auth::user()->role->nama_role ?? ''));

    $isAdmin = ($userRole == 'admin');

    $isTeknisi = ($userRole == 'teknisi');

    $isKepalaKelompok = in_array($userRole, [
        'kepala kelompok',
        'kepala_kelompok',
        'kapok'
    ]);

    $isKoordinator = ($userRole == 'koordinator');

    $isObserver = ($userRole == 'observer');

    $isTU = in_array($userRole, [
        'tata usaha',
        'tu'
    ]);

    $isForecaster = in_array($userRole, [
        'forecaster',
        'forcaster'
    ]);

    $isStaffOps = $isObserver || $isTU || $isForecaster;
@endphp

<nav class="mt-3">
<ul class="nav nav-pills nav-sidebar flex-column"
    data-widget="treeview"
    role="menu"
    data-accordion="false">

{{-- ===================================================== --}}
{{-- 1. DASHBOARD (SEMUA ROLE) --}}
{{-- ===================================================== --}}
<li class="nav-item">
  <a href="{{ route('dashboard') }}"
     class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
    <i class="nav-icon fas fa-home"></i>
    <p>Dashboard</p>
  </a>
</li>

{{-- ===================================================== --}}
{{-- 2. MASTER USER (ADMIN) --}}
{{-- ===================================================== --}}
@if($isTeknisi)
<li class="nav-item {{ request()->is('role*') || request()->is('user*') ? 'menu-open' : '' }}">

    <a href="#"
       class="nav-link {{ request()->is('role*') || request()->is('user*') ? 'active' : '' }}">
      <i class="nav-icon fas fa-users"></i>
      <p>
        Master User
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>

    <ul class="nav nav-treeview">

      <li class="nav-item">
        <a href="{{ route('user.index') }}"
           class="nav-link px-4 {{ request()->is('user*') ? 'active' : '' }}">
          <i class="far fa-circle nav-icon"></i>
          <p>User</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('role.index') }}"
           class="nav-link px-4 {{ request()->is('role*') ? 'active' : '' }}">
          <i class="far fa-circle nav-icon"></i>
          <p>Role</p>
        </a>
      </li>

    </ul>
</li>
@endif

{{-- ===================================================== --}}
{{-- 3. MASTER DATA (ADMIN, TEKNISI, KOORDINATOR & KEPALA KELOMPOK) --}}
{{-- ===================================================== --}}
@if($isAdmin || $isTeknisi || $isKepalaKelompok || $isKoordinator)

<li class="nav-item {{ request()->is('kategori*')
    || request()->is('sub-kategori*')
    || request()->is('data-alat*')
    || request()->is('pengecekan*')
    ? 'menu-open' : '' }}">

    <a href="#"
       class="nav-link {{ request()->is('kategori*')
          || request()->is('sub-kategori*')
          || request()->is('data-alat*')
          || request()->is('pengecekan*')
          ? 'active' : '' }}">

        <i class="nav-icon fas fa-database"></i>

        <p>
          Master Data
          <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">

        <li class="nav-item">
            <a href="{{ route('kategori.index') }}"
               class="nav-link px-4 {{ request()->is('kategori*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Kategori</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('sub-kategori.index') }}"
               class="nav-link px-4 {{ request()->is('sub-kategori*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Sub Kategori</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('data-alat.index') }}"
               class="nav-link px-4 {{ request()->is('data-alat*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Data Alat</p>
            </a>
        </li>

        @if($isAdmin || $isTeknisi)
        <li class="nav-item">
            <a href="{{ route('pengecekan.index', ['type' => 'harian']) }}"
               class="nav-link px-4 {{ request()->is('pengecekan*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check nav-icon text-info"></i>
                <p>Pengecekan</p>
            </a>
        </li>
        @endif

    </ul>
</li>
@endif

{{-- ===================================================== --}}
{{-- 4. MAINTENANCE --}}
{{-- ===================================================== --}}
@if($isAdmin || $isTeknisi || $isKepalaKelompok || $isKoordinator)
<li class="nav-item {{ request()->is('maintenance*') ? 'menu-open' : '' }}">

    <a href="#"
       class="nav-link {{ request()->is('maintenance*') ? 'active' : '' }}">

        <i class="nav-icon fas fa-tools"></i>

        <p>
          Maintenance
          <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">

        <li class="nav-item">
            <a href="{{ route('maintenance.harian') }}"
               class="nav-link px-4 {{ request()->is('maintenance/harian*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Harian</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('maintenance.mingguan') }}"
               class="nav-link px-4 {{ request()->is('maintenance/mingguan*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Mingguan</p>
            </a>
        </li>

    </ul>
</li>

@endif

{{-- ===================================================== --}}
{{-- 5. JADWAL DINAS --}}
{{-- ===================================================== --}}
@if($isAdmin || $isTeknisi || $isKepalaKelompok || $isKoordinator)

<li class="nav-item {{ request()->is('jadwal-dinas*') ? 'menu-open' : '' }}">

    <a href="#"
       class="nav-link {{ request()->is('jadwal-dinas*') ? 'active' : '' }}">

        <i class="nav-icon fas fa-calendar-alt"></i>

        <p>
            Jadwal Dinas
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">

        {{-- Semua role --}}
        <li class="nav-item">
            <a href="{{ route('jadwal_dinas.index') }}"
               class="nav-link px-4 {{ request()->is('jadwal-dinas') ? 'active' : '' }}">

                <i class="far fa-circle nav-icon text-info"></i>
                <p>Lihat Jadwal</p>
            </a>
        </li>

        {{-- Kepala Kelompok & Koordinator --}}
       @if($isAdmin || $isKepalaKelompok)
        <li class="nav-item">
            <a href="{{ route('jadwal_dinas.create') }}"
               class="nav-link px-4 {{ request()->is('jadwal-dinas/create') ? 'active' : '' }}">

                <i class="far fa-circle nav-icon text-warning"></i>
                <p>Input Jadwal</p>
            </a>
        </li>
        @endif

    </ul>
</li>

@endif

{{-- ===================================================== --}}
{{-- 6. PERMINTAAN PERBAIKAN --}}
{{-- ===================================================== --}}
<li class="nav-item">

    <a href="{{ route('perbaikan.index') }}"
       class="nav-link {{ request()->is('perbaikan*') ? 'active' : '' }}">

        <i class="nav-icon fas fa-tools text-warning"></i>
        <p>Permintaan Perbaikan</p>
    </a>

</li>

{{-- ===================================================== --}}
{{-- 7. KALIBRASI & HISTORI --}}
{{-- ===================================================== --}}
@if($isAdmin || $isTeknisi || $isKepalaKelompok || $isKoordinator)

<li class="nav-item">
    <a href="{{ route('kalibrasi.index') }}"
       class="nav-link {{ request()->routeIs('kalibrasi.*') ? 'active' : '' }}">

        <i class="nav-icon fas fa-wave-square"></i>
        <p>Kalibrasi</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('histori.index') }}"
       class="nav-link {{ request()->is('histori-operasional*') ? 'active' : '' }}">

        <i class="nav-icon fas fa-history"></i>
        <p>Histori Operasional</p>
    </a>
</li>

@endif

{{-- ===================================================== --}}
{{-- 8. LOGBOOK --}}
{{-- ===================================================== --}}
@if($isAdmin || $isTeknisi || $isKepalaKelompok || $isKoordinator)

<li class="nav-item">

    <a href="{{ route('logbook.index') }}"
       class="nav-link {{ request()->routeIs('logbook.*') ? 'active' : '' }}">

        <i class="nav-icon fas fa-book"></i>
        <p>Logbook</p>
    </a>

</li>

@endif

</ul>
</nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content pt-4">
      @yield('content')
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

@stack('scripts')

</body>
</html>