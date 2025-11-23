<div class="container-fluid">
    
    <div class="card shadow-sm mb-3">
        <div class="card-body p-2"> <form action="<?php echo base_url('operator/manajemen_akun'); ?>" method="GET">
                <div class="form-row align-items-center">
                    
                    <div class="col-auto">
                        <span class="text-muted font-weight-bold mr-2"><i class="fas fa-filter"></i> Filter:</span>
                    </div>

                    <div class="col-md-2 col-sm-4 my-1">
                        <select name="role" class="form-control form-control-sm">
                            <option value="">- Semua Role -</option>
                            <option value="dosen" <?php echo ($this->input->get('role') == 'dosen') ? 'selected' : ''; ?>>Dosen</option>
                            <option value="mahasiswa" <?php echo ($this->input->get('role') == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                            <option value="operator" <?php echo ($this->input->get('role') == 'operator') ? 'selected' : ''; ?>>Operator</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 col-sm-4 my-1">
                        <select name="prodi" class="form-control form-control-sm">
                            <option value="">- Semua Prodi -</option>
                            <option value="Teknik Informatika S1" <?php echo ($this->input->get('prodi') == 'Teknik Informatika S1') ? 'selected' : ''; ?>>Teknik Informatika S1</option>
                            <option value="Teknologi Informasi D3" <?php echo ($this->input->get('prodi') == 'Teknologi Informasi D3') ? 'selected' : ''; ?>>Teknologi Informasi D3</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-12 my-1">
                        <div class="input-group input-group-sm">
                            <input type="text" name="keyword" class="form-control" placeholder="Cari Nama/NPM/NIDK..." value="<?php echo $this->input->get('keyword'); ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if($this->input->get('role') || $this->input->get('prodi') || $this->input->get('keyword')): ?>
                    <div class="col-auto my-1">
                        <a href="<?php echo base_url('operator/manajemen_akun'); ?>" class="btn btn-outline-danger btn-sm" title="Reset Filter">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    <?php endif; ?>

                    <div class="col text-right my-1">
                        <a href="<?php echo base_url('operator/tambah_akun'); ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Baru
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            
            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible py-2">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-check"></i> <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <div class="card card-outline card-primary">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap table-sm"> <thead>
                            <tr class="text-center bg-light">
                                <th style="width: 50px">No</th> <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>NIDN / NPM</th>
                                <th>Program Studi</th>
                                <th style="width: 100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = $start_index + 1; // Nomor urut sesuai halaman
                            foreach ($users as $user): 
                            ?>
                            <tr>
                                <td class="text-center align-middle"><?php echo $no++; ?></td>
                                <td class="align-middle">
                                    <span class="font-weight-bold text-dark"><?php echo $user['nama']; ?></span>
                                </td>
                                <td class="align-middle text-muted"><?php echo $user['username']; ?></td>
                                <td class="align-middle text-center">
                                    <?php 
                                    $badge_color = 'secondary';
                                    if ($user['role'] == 'operator') $badge_color = 'info';
                                    elseif ($user['role'] == 'dosen') $badge_color = 'primary';
                                    elseif ($user['role'] == 'mahasiswa') $badge_color = 'warning';
                                    ?>
                                    <span class="badge badge-<?php echo $badge_color; ?> font-weight-normal px-2">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <?php 
                                    if ($user['role'] == 'dosen') echo $user['nidk'];
                                    else if ($user['role'] == 'mahasiswa') echo $user['npm'];
                                    else echo '-';
                                    ?>
                                </td>
                                <td class="align-middle small">
                                    <?php 
                                    if ($user['role'] == 'dosen') echo $user['prodi_dsn'];
                                    else if ($user['role'] == 'mahasiswa') echo $user['prodi_mhs'];
                                    else echo '-';
                                    ?>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo base_url('operator/edit_akun/' . $user['id']); ?>" class="btn btn-default text-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo base_url('operator/delete_akun/' . $user['id']); ?>" class="btn btn-default text-danger" onclick="return confirm('Hapus akun <?php echo $user['nama']; ?>?');" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-search fa-2x mb-2"></i><br>
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer py-2">
                    <div class="row align-items-center">
                        <div class="col-sm-6 text-muted small">
                            Total Data: <b><?php echo $total_rows; ?></b>
                        </div>
                        <div class="col-sm-6">
                            <?php echo $pagination; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>