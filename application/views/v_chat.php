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
                            <div class="kontak-link searchable-item" data-id="<?php echo $k['id']; ?>" data-nama="<?php echo $k['nama']; ?>">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($k['nama']); ?>&background=random" class="kontak-avatar">
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
                        <h4 class="font-weight-light mt-3 text-muted">Pilih Kontak</h4>
                    </div>

                    <div id="chatBox" class="chat-area" style="display: none !important;">
                        <div class="chat-header">
                            <div class="d-flex align-items-center">
                                <img src="" class="kontak-avatar mr-2" id="headerAvatar" style="width: 40px; height: 40px;"> 
                                <div><h6 class="mb-0 font-weight-bold" id="namaLawanBicara">User</h6></div>
                            </div>
                        </div>

                        <div class="chat-messages" id="isiChat"></div>

                        <div id="preview-container" style="display:none; padding:10px; background:#eee;">
                            <span id="file-name"></span> <button type="button" id="cancel-img">X</button>
                        </div>

                        <div class="chat-footer">
                            <form id="formKirim" enctype="multipart/form-data" style="width: 100%; display: flex;">
                                <input type="hidden" id="id_penerima" name="id_penerima">
                                
                                <label for="fileGambar" class="btn btn-secondary mr-2 mb-0">
                                    Img <input type="file" id="fileGambar" name="gambar" accept="image/*" style="display: none;">
                                </label>

                                <input type="text" id="pesanInput" name="pesan" class="form-control" placeholder="Ketik pesan..." autocomplete="off">
                                
                                <button type="submit" class="btn btn-primary ml-2 btn-send">
                                    Kirim
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
    .chat-container { height: 84vh; background: #fff; display: flex; border: 1px solid #ddd; }
    .kontak-list { width: 30%; border-right: 1px solid #ddd; display: flex; flex-direction: column; }
    .kontak-scroll { flex-grow: 1; overflow-y: auto; }
    .kontak-link { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: center; }
    .kontak-link:hover, .kontak-link.active { background: #f0f0f0; }
    .kontak-avatar { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; }
    
    .chat-wrapper { width: 70%; position: relative; background: #f8f9fa; }
    .chat-area { height: 100%; display: flex; flex-direction: column; }
    #chatPlaceholder { position: absolute; top:0; left:0; width:100%; height:100%; display:flex; justify-content:center; align-items:center; z-index:5; background:#fff;}
    
    .chat-header { padding: 10px; background: #fff; border-bottom: 1px solid #ddd; }
    .chat-messages { flex-grow: 1; overflow-y: auto; padding: 15px; background: #e5ddd5; }
    .chat-footer { padding: 10px; background: #fff; border-top: 1px solid #ddd; }
    
    .bubble { max-width: 70%; padding: 8px 12px; border-radius: 10px; margin-bottom: 10px; font-size: 14px; clear: both; }
    .bubble.me { float: right; background: #dcf8c6; }
    .bubble.you { float: left; background: #fff; }
    .chat-time { font-size: 10px; color: #888; float: right; margin-top: 5px; margin-left: 5px; }
    .direct-chat-img { max-width: 200px; border-radius: 5px; margin-bottom: 5px; cursor: pointer;}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. FILTER KONTAK (PURE JS biar lebih aman) ---
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
                    items[i].style.display = ""; // Show
                    visibleCount++;
                } else {
                    items[i].style.display = "none"; // Hide
                }
            }

            var noRes = document.getElementById('noContactFound');
            if(noRes) noRes.style.display = (visibleCount === 0) ? 'block' : 'none';
        });
    }

    // --- JQUERY UNTUK FITUR CHAT ---
    // Pastikan jQuery sudah ready
    if (typeof jQuery != 'undefined') {
        $(document).ready(function() {
            let idLawan = null;
            let intervalChat = null;

            // Klik Kontak
            $('.kontak-link').click(function() {
                $('.kontak-link').removeClass('active');
                $(this).addClass('active');

                idLawan = $(this).data('id');
                let nama = $(this).data('nama');
                
                $('#id_penerima').val(idLawan);
                $('#namaLawanBicara').text(nama);
                $('#headerAvatar').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(nama) + '&background=random');

                $('#chatPlaceholder').hide();
                $('#chatBox').attr('style', 'display: flex !important;');

                loadPesan(true);
                
                if(intervalChat) clearInterval(intervalChat);
                intervalChat = setInterval(function() { loadPesan(false); }, 3000);
            });

            // Load Pesan
            function loadPesan(autoScroll) {
                if(!idLawan) return;
                $.post("<?php echo base_url('chat/load_pesan'); ?>", {id_lawan: idLawan}, function(data){
                    $('#isiChat').html(data);
                    if(autoScroll) {
                        var d = document.getElementById("isiChat");
                        d.scrollTop = d.scrollHeight;
                    }
                });
            }

            // Preview Gambar
            $('#fileGambar').change(function() {
                if(this.files.length > 0) {
                    $('#file-name').text(this.files[0].name);
                    $('#preview-container').show();
                }
            });
            $('#cancel-img').click(function(){
                $('#fileGambar').val('');
                $('#preview-container').hide();
            });

            // Kirim Pesan
            $('#formKirim').submit(function(e) {
                e.preventDefault();
                let pesan = $('#pesanInput').val();
                let gambar = $('#fileGambar').val();

                if($.trim(pesan) == "" && gambar == "") return;

                let btn = $('.btn-send');
                let txt = btn.text();
                btn.text('...').prop('disabled', true);

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
                            loadPesan(true);
                        } else {
                            alert(res.msg);
                        }
                    },
                    complete: function() {
                        btn.text(txt).prop('disabled', false);
                    }
                });
            });
        });
    }
});
</script>


// End of file v_chat.php