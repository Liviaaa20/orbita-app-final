<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ORBITA | Register</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    body { 
        background: linear-gradient(135deg, #003366 0%, #006699 100%); 
        height: 100vh; 
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .orbita-title { color: white; font-weight: bold; font-size: 3rem; margin-bottom: 5px; }
    .orbita-subtitle { color: white; font-size: 1.2rem; margin-bottom: 30px; text-align: center; }
    
    .register-box { width: 400px; }
    .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    .card-body { padding: 2rem; }
    
    .form-group label { font-weight: 500; color: #333; margin-bottom: 5px; }
    .form-control { 
        background-color: #e9ecef; 
        border: none; 
        border-radius: 8px; 
        height: 45px;
    }
    .input-group-text { background-color: #e9ecef; border: none; border-radius: 8px 0 0 8px; }
    .form-control:focus { background-color: #dee2e6; box-shadow: none; }
    
    .btn-register { 
        background-color: transparent; 
        border: 2px solid #003366; 
        color: #003366; 
        font-weight: bold;
        border-radius: 8px;
        height: 45px;
        margin-top: 20px;
    }
    .btn-register:hover { background-color: #003366; color: white; }
    .placeholder-img { width: 80px; height: 80px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 15px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; }
  </style>
</head>
<body>

  <h1 class="orbita-title">ORBITA</h1>
  <p class="orbita-subtitle">Operational Reporting &<br>BMKG Instrument Technical Asset.</p>

  <div class="register-box">
    <div class="card">
      <div class="card-body">
        <div class="placeholder-img">
        <img src="{{ asset('assets/dist/img/logo.png') }}" style="width: 70px;" alt="Logo BMKG">
        </div>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <form action="{{ route('register.store') }}" method="post">
          @csrf
          <div class="form-group">
            <label>Nama Pengguna (Username)</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
            </div>
          </div>
          <div class="form-group">
            <label>Nomor Induk Pegawai (NIP)</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                </div>
                <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP" required>
            </div>
          </div>
          <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required>
</div>

          <div class="form-group">
            <label>Kata Sandi (Password)</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div>
                <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
            </div>
          </div>

          <div class="form-group">
            <label>Kata Sandi (Password)</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi Password" required>
            </div>
          </div>

          <div class="form-group">
    <label>Role</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
        </div>
        <select name="role_id" class="form-control" required>
            <option value="">-- Pilih Role --</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
            @endforeach
        </select>
    </div>
</div>

          <button type="submit" class="btn btn-register btn-block">Register</button>
        </form>
      </div>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>