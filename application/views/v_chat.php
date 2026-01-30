<div class="content-wrapper">
    <section class="content-header pb-1">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ruang Diskusi</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content h-100">
        <div class="container-fluid h-100 pb-3">
            <div class="chat-container shadow-sm border">

                <div class="kontak-list">
                    <div class="p-3 bg-light border-bottom">
                        <input type="text" id="searchContact" class="form-control" placeholder="Cari kontak..." autocomplete="off">
                    </div>

                    <div class="kontak-scroll" id="kontakListContainer">
                        <?php if(!empty($kontak)): ?>
                            <?php foreach ($kontak as $k): ?>
                                <?php 
                                    // LOGIKA FOTO PROFIL
                                    // Cek apakah ada file foto di database dan filenya benar-benar ada di folder
                                    $foto_db = isset($k['foto']) ? $k['foto'] : '';
                                    $path_fisik = FCPATH . 'uploads/profile/' . $foto_db;
                                    
                                    if (!empty($foto_db) && file_exists($path_fisik)) {
                                        // Gunakan foto asli + time() biar tidak cache saat update
                                        $foto_url = base_url('uploads/profile/' . $foto_db . '?t=' . time());
                                    } else {
                                        // Fallback ke inisial nama jika foto tidak ada
                                        $foto_url = 'https://ui-avatars.com/api/?name=' . urlencode($k['nama']) . '&background=random&color=fff&size=128';
                                    }
                                ?>

                                <div class="kontak-link searchable-item" 
                                     data-id="<?php echo $k['id']; ?>" 
                                     data-nama="<?php echo $k['nama']; ?>"
                                     data-foto="<?php echo $foto_url; ?>">
                                    
                                    <img src="<?php echo $foto_url; ?>" class="kontak-avatar">
                                    
                                    <div style="overflow: hidden;">
                                        <h6 class="mb-0 font-weight-bold contact-name"><?php echo $k['nama']; ?></h6>
                                        <small class="text-muted text-truncate d-block" style="max-width: 200px;">
                                            <?php echo $k['sub_info'] ?? ucfirst($k['role']); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center p-4 text-muted"><small>Belum ada kontak.</small></div>
                        <?php endif; ?>

                        <div id="noContactFound" class="text-center p-3 text-muted" style="display: none;">
                            <small>Tidak ditemukan</small>
                        </div>
                    </div>
                </div>

                <div class="chat-wrapper">
                    <div id="chatPlaceholder">
                        <div class="text-center">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h4 class="font-weight-light text-muted">Pilih Kontak untuk Memulai Diskusi</h4>
                        </div>
                    </div>

                    <div id="chatBox" class="chat-area" style="display: none !important;">
                        <div class="chat-header">
                            <div class="d-flex align-items-center">
                                <img src="" class="kontak-avatar mr-2" id="headerAvatar" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 font-weight-bold" id="namaLawanBicara">User</h6>
                                    <small class="text-success"><i class="fas fa-circle" style="font-size: 8px;"></i> Online</small>
                                </div>
                            </div>
                        </div>

                        <div class="chat-messages" id="isiChat"></div>

                        <div id="preview-container" style="display:none; padding:10px; background:#f1f1f1; border-top: 1px solid #ddd;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fas fa-image mr-2 text-primary"></i> 
                                    <span id="file-name" class="font-weight-bold text-sm"></span>
                                </div>
                                <button type="button" id="cancel-img" class="btn btn-xs btn-danger"><i class="fas fa-times"></i> Batal</button>
                            </div>
                        </div>

                        <div class="chat-footer">
                            <form id="formKirim" enctype="multipart/form-data" style="width: 100%; display: flex; align-items: center;">
                                <input type="hidden" id="id_penerima" name="id_penerima">

                                <label for="fileGambar" class="btn btn-light text-secondary mr-2 mb-0 border" title="Kirim Gambar">
                                    <i class="fas fa-paperclip"></i> 
                                    <input type="file" id="fileGambar" name="gambar" accept="image/*" style="display: none;">
                                </label>

                                <input type="text" id="pesanInput" name="pesan" class="form-control border-0 bg-light" placeholder="Ketik pesan..." autocomplete="off" style="border-radius: 20px;">

                                <button type="submit" class="btn btn-primary ml-2 btn-send rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<style>
    .chat-container { height: 80vh; background: #fff; display: flex; border-radius: 8px; overflow: hidden; }
    
    /* Kontak List */
    .kontak-list { width: 30%; border-right: 1px solid #ddd; display: flex; flex-direction: column; background: #fff; }
    .kontak-scroll { flex-grow: 1; overflow-y: auto; }
    .kontak-link { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; display: flex; align-items: center; transition: 0.2s; }
    .kontak-link:hover { background: #f8f9fa; }
    .kontak-link.active { background: #e3f2fd; border-left: 4px solid #007bff; }
    
    /* Avatar Styling */
    .kontak-avatar { width: 45px; height: 45px; border-radius: 50%; margin-right: 12px; object-fit: cover; border: 1px solid #eee; }

    /* Chat Area */
    .chat-wrapper { width: 70%; position: relative; background: #f0f2f5; }
    .chat-area { height: 100%; display: flex; flex-direction: column; }
    #chatPlaceholder { position: absolute; top:0; left:0; width:100%; height:100%; display:flex; justify-content:center; align-items:center; z-index:5; background:#f0f2f5;}

    .chat-header { padding: 10px 20px; background: #fff; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between; height: 65px; }
    .chat-messages { flex-grow: 1; overflow-y: auto; padding: 20px; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); /* Pattern WhatsApp-like */ background-color: #e5ddd5; }
    .chat-footer { padding: 10px 15px; background: #fff; }

    /* Chat Bubbles */
    .bubble { max-width: 75%; padding: 8px 15px; border-radius: 15px; margin-bottom: 8px; font-size: 14px; position: relative; word-wrap: break-word; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
    .bubble.me { float: right; background: #dcf8c6; border-top-right-radius: 0; clear: both; }
    .bubble.you { float: left; background: #fff; border-top-left-radius: 0; clear: both; }
    .chat-time { font-size: 10px; color: #999; float: right; margin-top: 4px; margin-left: 8px; }
    .direct-chat-img { max-width: 250px; border-radius: 8px; margin-bottom: 5px; cursor: pointer; border: 1px solid #ddd; }

    /* Scrollbar Cantik */
    .kontak-scroll::-webkit-scrollbar, .chat-messages::-webkit-scrollbar { width: 6px; }
    .kontak-scroll::-webkit-scrollbar-thumb, .chat-messages::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 3px; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // --- 1. FILTER KONTAK ---
    var searchInput = document.getElementById('searchContact');
    if(searchInput){
        searchInput.addEventListener('keyup', function() {
            var filter = this.value.toUpperCase();
            var items = document.getElementsByClassName('searchable-item');
            var visibleCount = 0;

            for (var i = 0; i < items.length; i++) {
                var nameEl = items[i].getElementsByClassName('contact-name')[0];
                var txtValue = nameEl.textContent || nameEl.innerText;

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    items[i].style.display = "flex"; // Gunakan flex agar layout tidak rusak
                    visibleCount++;
                } else {
                    items[i].style.display = "none";
                }
            }
            var noRes = document.getElementById('noContactFound');
            if(noRes) noRes.style.display = (visibleCount === 0) ? 'block' : 'none';
        });
    }

    // --- JQUERY UNTUK LOGIKA CHAT ---
    if (typeof jQuery != 'undefined') {
        $(document).ready(function() {
            let idLawan = null;
            let intervalChat = null;

            // Klik Kontak
            $('.kontak-link').click(function() {
                // Style Active
                $('.kontak-link').removeClass('active');
                $(this).addClass('active');

                // Ambil Data dari Atribut HTML
                idLawan = $(this).data('id');
                let nama = $(this).data('nama');
                let foto = $(this).data('foto'); // Ambil URL Foto

                // Set Data ke Chat Header & Form
                $('#id_penerima').val(idLawan);
                $('#namaLawanBicara').text(nama);
                $('#headerAvatar').attr('src', foto); // Update Foto Header

                // Tampilkan Box Chat
                $('#chatPlaceholder').hide();
                $('#chatBox').attr('style', 'display: flex !important;');

                // Load Pesan Pertama Kali
                loadPesan(true);

                // Jalankan Interval Realtime
                if(intervalChat) clearInterval(intervalChat);
                intervalChat = setInterval(function() { loadPesan(false); }, 3000);
            });

            // Fungsi Load Pesan
            function loadPesan(autoScroll) {
                if(!idLawan) return;
                $.post("<?php echo base_url('chat/load_pesan'); ?>", {id_lawan: idLawan}, function(data){
                    $('#isiChat').html(data);
                    // Scroll ke bawah hanya jika user baru buka chat atau mengirim pesan
                    if(autoScroll) {
                        var d = document.getElementById("isiChat");
                        d.scrollTop = d.scrollHeight;
                    }
                });
            }

            // Preview Gambar sebelum kirim
            $('#fileGambar').change(function() {
                if(this.files.length > 0) {
                    $('#file-name').text(this.files[0].name);
                    $('#preview-container').slideDown();
                }
            });
            
            // Batal Kirim Gambar
            $('#cancel-img').click(function(){
                $('#fileGambar').val('');
                $('#preview-container').slideUp();
            });

            // Submit Form Kirim Pesan
            $('#formKirim').submit(function(e) {
                e.preventDefault();
                let pesan = $('#pesanInput').val();
                let gambar = $('#fileGambar').val();

                if($.trim(pesan) == "" && gambar == "") return;

                let btn = $('.btn-send');
                let icon = btn.html(); // Simpan icon asli
                btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true); // Loading state

                $.ajax({
                    url: "<?php echo base_url('chat/kirim_pesan'); ?>",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(res) {
                        if(res.status) {
                            $('#pesanInput').val('');
                            $('#fileGambar').val('');
                            $('#preview-container').hide();
                            loadPesan(true); // Reload dan scroll ke bawah
                        } else {
                            alert(res.msg);
                        }
                    },
                    error: function() {
                        alert('Gagal mengirim pesan. Cek koneksi internet.');
                    },
                    complete: function() {
                        btn.html(icon).prop('disabled', false); // Kembalikan tombol
                    }
                });
            });
        });
    }
});
</script>