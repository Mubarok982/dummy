<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | WBS Fakultas Teknik</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      /* Background Gradient Modern */
      background: linear-gradient(135deg, #eef2f3 0%, #8e9eab 100%);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-box {
      width: 100%;
      max-width: 1000px; /* Lebih lebar agar lega */
    }

    .card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.2); /* Shadow lebih lembut tapi dalam */
      overflow: hidden;
    }

    /* --- BAGIAN KIRI (GAMBAR) --- */
    .login-image {
        background: url('<?php echo base_url('assets/image/bg.jpg'); ?>') center center no-repeat;
        background-size: cover;
        min-height: 550px;
        position: relative;
    }
    
    /* Overlay Gradient Biru Elegan */
    .overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(to bottom right, rgba(0, 83, 176, 0.9), rgba(0, 123, 255, 0.7));
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        padding: 40px;
    }

    .overlay h2 { font-weight: 700; font-size: 3rem; margin-bottom: 10px; }
    .overlay h3 { font-weight: 600; letter-spacing: 1px; margin-bottom: 5px; }
    .overlay p { font-weight: 300; opacity: 0.9; font-size: 1.1rem; }
    .overlay .separator { width: 50px; height: 4px; background: #fff; margin: 20px 0; border-radius: 2px; }

    /* --- BAGIAN KANAN (FORM) --- */
    .login-card-body {
        padding: 50px;
    }

    .login-header {
        margin-bottom: 40px;
    }
    .login-header h4 { color: #333; font-weight: 700; }
    .login-header span { color: #007bff; }

    /* Input Styles */
    .input-group {
        margin-bottom: 25px;
    }
    .form-control {
        height: 50px;
        background-color: #f4f6f9;
        border: 1px solid transparent;
        border-radius: 10px 0 0 10px !important;
        padding-left: 20px;
        font-size: 0.95rem;
        transition: all 0.3s;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }
    .input-group-text {
        border: none;
        background-color: #f4f6f9;
        border-radius: 0 10px 10px 0 !important;
        color: #007bff;
        padding-right: 20px;
    }

    /* Tombol Login */
    .btn-primary {
        height: 50px;
        border-radius: 10px;
        background: linear-gradient(to right, #0062cc, #007bff);
        border: none;
        font-weight: 600;
        letter-spacing: 1px;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        transition: transform 0.2s;
    }
    .btn-primary:hover {
        background: linear-gradient(to right, #0056b3, #0069d9);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 123, 255, 0.4);
    }

    .alert { border-radius: 10px; font-size: 0.9rem; }

    /* Responsive */
    @media (max-width: 768px) {
        .login-image { display: none; }
        .login-card-body { padding: 30px; }
    }
  </style>
</head>
<body class="hold-transition">

<div class="login-box animate__animated animate__fadeInUp">
  <div class="card">
    <div class="row no-gutters">
        
        <div class="col-md-6 login-image">
            <div class="overlay">
                <div class="animate__animated animate__fadeInDown">
                    <i class="fas fa-graduation-cap fa-4x mb-3"></i>
                    <h3>WBS SYSTEM</h3>
                    <div class="separator"></div>
                    <p>Fakultas Teknik</p>
                </div>
                <div class="mt-4 small animate__animated animate__fadeInUp animate__delay-1s" style="opacity: 0.8;">
                    "Manajemen skripsi yang transparan, cepat, dan akurat untuk masa depan yang lebih baik."
                </div>
            </div>
        </div>

        <div class="col-md-6 bg-white">
            <div class="login-card-body">
                
                <div class="login-header">
                    <h4>Selamat Datang <br><span>Silakan Login</span></h4>
                    <p class="text-muted text-sm mt-2">Masukkan kredensial akun Anda</p>
                </div>

                <?php if ($this->session->flashdata('pesan_error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span><?php echo $this->session->flashdata('pesan_error'); ?></span>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php echo validation_errors('<div class="alert alert-warning shadow-sm"><i class="fas fa-exclamation-triangle mr-1"></i> ', '</div>'); ?>

                <?php echo form_open('auth/login_aksi'); ?>
                    <div class="form-group mb-4">
                        <label class="text-muted small font-weight-bold ml-1">USERNAME / NPM / NIDK</label>
                        <div class="input-group">
                            <input type="text" name="username" class="form-control" placeholder="Masukkan ID Pengguna" value="<?php echo set_value('username'); ?>" required autocomplete="off">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="text-muted small font-weight-bold ml-1">PASSWORD</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-5">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                MASUK KE SISTEM <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                <?php echo form_close(); ?>

                <div class="mt-5 text-center border-top pt-4">
                    <small class="text-muted">Mengalami masalah login? <a href="#" class="text-primary font-weight-bold">Bantuan Tata Usaha</a></small>
                </div>
            </div>
        </div>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>
</html>