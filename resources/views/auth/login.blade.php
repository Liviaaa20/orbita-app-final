<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ORBITA | Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">

    <style>
        body.login-page {
        background: linear-gradient(135deg, #003366 0%, #006699 100%); 
        height: 100vh; 
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        }
        .login-box { width: 400px; margin: 0; }
        .orbita-title { color: white; font-weight: 800; letter-spacing: 2px; font-size: 2.5rem; margin-bottom: 0; }
        .orbita-subtitle { color: white; margin-bottom: 20px; font-size: 0.9rem; line-height: 1.4; }
        .login-card-body { border-radius: 15px; padding: 2rem !important; }
        .form-group label { font-weight: bold; color: #444; margin-bottom: 5px; }
        .form-control { 
            background-color: #e9ecef !important; 
            border: none !important; 
            border-radius: 8px !important;
            height: 45px;
        }
        .btn-primary { 
            background-color: #003366; 
            border: none; 
            border-radius: 8px;
            font-weight: bold;
            padding: 10px 40px;
        }
        .btn-primary:hover { background-color: #002244; }
        .input-group-text { background-color: #e9ecef; border: none; border-radius: 8px 0 0 8px; }
        /* Memperbaiki alignment */
        .custom-control-label { cursor: pointer; padding-top: 2px; }
        .app-watermark{
            margin-top:18px;
    text-align:center;

    color:#ffffffcc;
    font-size:11px;
    line-height:1.6;
}
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box text-center">

    <h1 class="orbita-title">ORBITA</h1>
    <p class="orbita-subtitle">Operational Reporting & <br>BMKG Instrument Technical Asset</p>

    <div class="card card-outline shadow-lg">
    <div class="card-body login-card-body"> 
        <div class="text-center mb-4">
            <img src="{{ asset('assets/dist/img/logo.png') }}" style="width: 70px;" alt="Logo BMKG">
                <p class="mt-2 mb-0 font-weight-bold" style="font-size: 0.9rem; line-height: 1.4;">
                    Stasiun Meteorologi Kelas II Maritim<br>
                    Tanjung Emas Semarang
                </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2" style="font-size: 0.85rem;">
            Username atau password salah
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="form-group mb-3 text-left"> <label class="d-block font-weight-bold mb-1" style="color: #333; text-align: left !important;">Username</label>
            <input 
                type="text" 
                name="username" 
                class="form-control" 
                placeholder="Masukkan Username" 
                required
            >
        </div>

        <div class="form-group mb-2 text-left"> <label class="d-block font-weight-bold mb-1" style="color: #333; text-align: left !important;">Password</label>
            <input 
                type="password" 
                name="password" 
                class="form-control" 
                placeholder="Masukkan Password" 
                required
            >
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="remember" class="custom-control-input" id="remember">
                <label class="custom-control-label small text-muted" for="remember">Ingat saya</label>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-block font-weight-bold py-2">
                Login
            </button>
        </div>
    </form>

    <div class="text-center mt-4">
        <span class="small text-muted">Belum Punya Akun?</span> 
        <a href="{{ route('register') }}" class="small font-weight-bold text-primary" style="text-decoration: underline;">Register</a>
    </div>
</div>
    </div>
</div>
<div class="app-watermark">
    <strong>ORBITA v1.0</strong><br>
    Developed by<br>
    Felina Devi Maharani • Rossa Livia Monika Hardianti • Ivana Purbaningtyas<br>
    <small>Information Systems • Universitas Semarang</small>
</div>

<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
</body>
</html>