<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>WBS Skripsi | <?php echo $title; ?></title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  
  <style>
    /* Custom CSS kecil untuk mempercantik */
    .card-stat { transition: transform 0.3s; }
    .card-stat:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">Sistem Informasi Bimbingan Skripsi</span>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <ul class="navbar-nav ml-auto">
    
    <?php if(isset($unread_chat)): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('chat'); ?>">
                <i class="fas fa-comments" style="font-size: 1.2rem;"></i>
                <?php if($unread_chat > 0): ?>
                    <span class="badge badge-danger navbar-badge" style="font-size: 0.6rem; right: 2px; top: 5px;">
                        <?php echo $unread_chat; ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>
    <?php endif; ?>
    
    </ul>
      <li class="nav-item">
        <a class="nav-link text-danger" href="<?php echo base_url('auth/logout'); ?>">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <span class="brand-text font-weight-light pl-3">WBS - <b>TEKNIK</b></span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <?php
            // Fetch latest name from database to ensure it's always up-to-date
            $user_id = $this->session->userdata('id');
            $user_nama = $this->session->userdata('nama');
            if ($user_id) {
                $ci = get_instance();
                $ci->db->select('nama');
                $ci->db->where('id', $user_id);
                $user_data = $ci->db->get('mstr_akun')->row();
                if ($user_data && !empty($user_data->nama)) {
                    $user_nama = $user_data->nama;
                    // Update session with latest name
                    $ci->session->set_userdata('nama', $user_nama);
                }
            }
            ?>
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_nama); ?>&background=random" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo substr($user_nama, 0, 20); ?></a>
          <small class="text-muted"><?php echo ucfirst($this->session->userdata('role')); ?></small>
        </div>
      </div>
