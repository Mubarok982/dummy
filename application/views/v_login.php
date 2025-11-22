<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WBS</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <style>
        /* Gaya khusus untuk halaman login (optional) */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #333; /* Warna background gelap */
        }
        .login-container {
            width: 350px;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-login {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login WBS - FT</h2>
        
        <?php echo validation_errors('<div style="color:red; margin-bottom: 10px;">', '</div>'); ?>
        <?php 
        if ($this->session->flashdata('pesan_error')) {
            echo '<div style="color:red; margin-bottom: 10px;">' . $this->session->flashdata('pesan_error') . '</div>';
        }
        ?>

        <?php echo form_open('auth/login_aksi'); ?>
            <div class="form-group">
                <label for="username">Username/NIDN/NPM</label>
                <input type="text" id="username" name="username" class="form-control" required value="<?php echo set_value('username'); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        <?php echo form_close(); ?>
    </div>
</body>
</html>