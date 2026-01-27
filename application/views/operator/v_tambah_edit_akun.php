<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <?php $is_edit = isset($user); ?>
                    <h1 class="m-0 text-dark"><?php echo $is_edit ? 'Edit Akun Pengguna' : 'Tambah Akun Baru'; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('operator/manajemen_akun'); ?>">Manajemen Akun</a></li>
                        <li class="breadcrumb-item active"><?php echo $is_edit ? 'Edit' : 'Tambah'; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-edit mr-1"></i> Form Data Akun</h3>
                        </div>
                        
                        <?php 
                        $action_url = $is_edit ? base_url('operator/edit_akun/' . $user['id']) : base_url('operator/tambah_akun');
                        // Menambahkan query string source ke URL action juga untuk menjaga state jika ada error validasi
                        if($is_edit && isset($source) && !empty($source)) {
                            $action_url .= '?source=' . $source;
                        }
                        echo form_open($action_url, ['class' => 'form-horizontal']); 
                        ?>
                        
                        <div class="card-body">
                            
                            <input type="hidden" name="redirect_source" value="<?php echo isset($source) ? $source : ''; ?>">
                            
                            <?php if (validation_errors()): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
                                    <?php echo validation_errors(); ?>
                                </div>
                            <?php endif; ?>

                            <h5 class="text-primary mb-3 text-uppercase font-weight-bold" style="font-size: 0.9rem; border-bottom: 1px solid #eee; padding-bottom: 5px;">Informasi Dasar</h5>

                            <div class="form-group row">
                                <label for="nama" class="col-sm-3 col-form-label">Nama Lengkap</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Lengkap" value="<?php echo set_value('nama', $is_edit ? $user['nama'] : ''); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="role" class="col-sm-3 col-form-label">Role Akun</label>
                                <div class="col-sm-9">
                                    <select name="role" id="role" class="form-control" onchange="toggleRoleFields(this.value)" required <?php echo $is_edit ? 'disabled' : ''; ?>>
                                        <option value="">-- Pilih Role --</option>
                                        <option value="operator" <?php echo set_select('role', 'operator', $is_edit && $user['role'] == 'operator'); ?>>Operator</option>
                                        <option value="dosen" <?php echo set_select('role', 'dosen', $is_edit && $user['role'] == 'dosen'); ?>>Dosen</option>
                                        <option value="mahasiswa" <?php echo set_select('role', 'mahasiswa', $is_edit && $user['role'] == 'mahasiswa'); ?>>Mahasiswa</option>
                                    </select>
                                    
                                    <?php if ($is_edit): ?>
                                        <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                                        <small class="text-muted">Role tidak dapat diubah saat mode edit.</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!$is_edit): ?>
                            <div class="form-group row">
                                <label for="username" class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" name="username" id="username" class="form-control" placeholder="Username untuk Login" value="<?php echo set_value('username'); ?>" required>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="form-group row">
                                <label for="password" class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo $is_edit ? 'Kosongkan jika tidak ingin mengubah password' : 'Password'; ?>">
                                    <?php if($is_edit): ?>
                                        <small class="text-muted text-italic">* Kosongkan jika password tidak diubah.</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div id="dosen-fields" style="display: none;">
                                <h5 class="text-primary mt-4 mb-3 text-uppercase font-weight-bold" style="font-size: 0.9rem; border-bottom: 1px solid #eee; padding-bottom: 5px;">Detail Dosen</h5>
                                <div class="form-group row">
                                    <label for="nidk" class="col-sm-3 col-form-label">NIDK / NIDN</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="nidk" id="nidk" class="form-control" value="<?php echo set_value('nidk', $is_edit ? $user['nidk'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="prodi_dosen" class="col-sm-3 col-form-label">Program Studi</label>
                                    <div class="col-sm-9">
                                        <select name="prodi_dosen" id="prodi_dosen" class="form-control">
                                            <option value="Teknik Informatika S1" <?php echo set_select('prodi_dosen', 'Teknik Informatika S1', $is_edit && isset($user['prodi']) && $user['prodi'] == 'Teknik Informatika S1'); ?>>Teknik Informatika S1</option>
                                            <option value="Teknologi Informasi D3" <?php echo set_select('prodi_dosen', 'Teknologi Informasi D3', $is_edit && isset($user['prodi']) && $user['prodi'] == 'Teknologi Informasi D3'); ?>>Teknologi Informasi D3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Jabatan</label>
                                    <div class="col-sm-9">
                                        <div class="custom-control custom-checkbox">
                                            <input type="hidden" name="is_kaprodi" value="0">
                                            <input class="custom-control-input" type="checkbox" id="is_kaprodi" name="is_kaprodi" value="1" <?php
                                                $checked = '';
                                                if ($this->input->post('is_kaprodi')) {
                                                    $checked = 'checked';
                                                } elseif ($is_edit && isset($user['is_kaprodi']) && $user['is_kaprodi'] == 1) {
                                                    $checked = 'checked';
                                                }
                                                echo $checked;
                                            ?>>
                                            <label for="is_kaprodi" class="custom-control-label">Kepala Program Studi (Kaprodi)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="mahasiswa-fields" style="display: none;">
                                <h5 class="text-primary mt-4 mb-3 text-uppercase font-weight-bold" style="font-size: 0.9rem; border-bottom: 1px solid #eee; padding-bottom: 5px;">Detail Mahasiswa</h5>
                                <div class="form-group row">
                                    <label for="npm" class="col-sm-3 col-form-label">NPM</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="npm" id="npm" class="form-control" value="<?php echo set_value('npm', $is_edit ? $user['npm'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="prodi_mhs" class="col-sm-3 col-form-label">Program Studi</label>
                                    <div class="col-sm-9">
                                        <select name="prodi_mhs" id="prodi_mhs" class="form-control">
                                            <option value="Teknik Informatika S1" <?php echo set_select('prodi_mhs', 'Teknik Informatika S1', $is_edit && isset($user['prodi']) && $user['prodi'] == 'Teknik Informatika S1'); ?>>Teknik Informatika S1</option>
                                            <option value="Teknologi Informasi D3" <?php echo set_select('prodi_mhs', 'Teknologi Informasi D3', $is_edit && isset($user['prodi']) && $user['prodi'] == 'Teknologi Informasi D3'); ?>>Teknologi Informasi D3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="angkatan" class="col-sm-3 col-form-label">Angkatan</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="angkatan" id="angkatan" class="form-control" min="2000" max="<?php echo date('Y'); ?>" value="<?php echo set_value('angkatan', $is_edit ? $user['angkatan'] : date('Y')); ?>">
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        <div class="card-footer justify-content-between">
                            <?php 
                            // LOGIKA TOMBOL KEMBALI DINAMIS
                            // Jika source dari data_mahasiswa, tombol kembali mengarah ke sana
                            if (isset($source) && $source == 'data_mahasiswa') {
                                $url_kembali = base_url('operator/data_mahasiswa');
                            } else {
                                $url_kembali = base_url('operator/manajemen_akun');
                            }
                            ?>
                            <a href="<?php echo $url_kembali; ?>" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            
                            <button type="submit" class="btn btn-primary float-right px-4 shadow-sm">
                                <i class="fas fa-save mr-1"></i> <?php echo $is_edit ? 'Simpan Perubahan' : 'Tambah Akun'; ?>
                            </button>
                        </div>
                        
                        <?php echo form_close(); ?>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<script>
    function toggleRoleFields(role) {
        var dosenFields = document.getElementById('dosen-fields');
        var mhsFields = document.getElementById('mahasiswa-fields');
        
        dosenFields.style.display = 'none';
        disableInputsIn(dosenFields);

        mhsFields.style.display = 'none';
        disableInputsIn(mhsFields);

        if (role === 'dosen') {
            dosenFields.style.display = 'block';
            enableInputsIn(dosenFields);
        } else if (role === 'mahasiswa') {
            mhsFields.style.display = 'block';
            enableInputsIn(mhsFields);
        }
    }

    function disableInputsIn(container) {
        var inputs = container.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.disabled = true; 
        });
    }

    function enableInputsIn(container) {
        var inputs = container.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.disabled = false; 
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        var initialRole = document.getElementById('role').value;
        toggleRoleFields(initialRole);
    });
</script>