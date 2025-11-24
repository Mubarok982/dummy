<style>
    /* --- Custom Chat Styling --- */
    .chat-container {
        height: 80vh;
        max-height: 700px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        display: flex;
    }

    /* Sidebar Kontak */
    .kontak-list {
        width: 30%;
        height: 100%;
        background: #fff;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
    }

    .kontak-scroll {
        flex-grow: 1;
        overflow-y: auto;
    }

    .kontak-link {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #f5f5f5;
        cursor: pointer;
        transition: all 0.2s;
        color: #333 !important;
        text-decoration: none;
    }

    .kontak-link:hover, .kontak-link.active {
        background-color: #f0f2f5;
        color: #000 !important;
    }

    .kontak-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        object-fit: cover;
    }

    /* Area Chat Kanan */
    .chat-wrapper {
        width: 70%;
        height: 100%;
        position: relative;
    }

    .chat-area {
        height: 100%;
        background: #e5ddd5;
        display: flex;
        flex-direction: column;
    }

    /* Placeholder Screen */
    #chatPlaceholder {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: #f8f9fa;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .chat-header {
        padding: 15px 20px; /* Padding sedikit diperbesar agar proporsional */
        background: #f0f2f5;
        border-bottom: 1px solid #d1d7db;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-messages {
        flex-grow: 1;
        overflow-y: auto;
        padding: 20px;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        background-repeat: repeat;
    }

    /* Bubble Chat */
    .bubble {
        max-width: 70%;
        padding: 8px 12px;
        border-radius: 7.5px;
        position: relative;
        margin-bottom: 10px;
        font-size: 14px;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        clear: both;
        word-wrap: break-word;
    }

    .bubble.me {
        float: right;
        background-color: #dcf8c6;
        border-top-right-radius: 0;
    }

    .bubble.you {
        float: left;
        background-color: #fff;
        border-top-left-radius: 0;
    }

    .chat-time {
        font-size: 10px;
        color: #999;
        float: right;
        margin-top: 5px;
        margin-left: 10px;
    }

    .direct-chat-img { width: 100%; max-width: 250px; border-radius: 5px; cursor: pointer; margin-bottom: 5px; }

    /* Footer Input */
    .chat-footer {
        padding: 10px 15px;
        background: #f0f2f5;
        display: flex;
        align-items: center;
    }

    .btn-attach { font-size: 20px; color: #54656f; cursor: pointer; padding: 10px; margin-right: 10px; }
    .btn-attach:hover { color: #00a884; }

    .input-message {
        flex-grow: 1;
        border: none;
        padding: 10px 15px;
        border-radius: 20px;
        outline: none;
    }

    .btn-send {
        border: none;
        background: none;
        color: #54656f;
        font-size: 20px;
        margin-left: 10px;
        cursor: pointer;
    }
    .btn-send:hover { color: #00a884; }

    /* Preview Upload */
    #preview-container {
        display: none;
        padding: 10px;
        background: #e9edef;
        border-bottom: 1px solid #d1d7db;
    }
</style>

<div class="container-fluid pt-2">
    <div class="chat-container">
        
        <div class="kontak-list">
            <div class="p-3 bg-light border-bottom">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="searchContact" class="form-control border-left-0" placeholder="Cari kontak...">
                </div>
            </div>
            
            <div class="kontak-scroll" id="kontakListContainer">
                <?php foreach ($kontak as $k): ?>
                <div class="kontak-link" data-id="<?php echo $k['id']; ?>" data-nama="<?php echo $k['nama']; ?>">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($k['nama']); ?>&background=random" class="kontak-avatar">
                    <div style="overflow: hidden;">
                        <h6 class="mb-0 font-weight-bold contact-name"><?php echo $k['nama']; ?></h6>
                        <small class="text-muted text-truncate d-block" style="max-width: 200px;"><?php echo $k['sub_info']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div id="noContactFound" class="text-center p-3 text-muted" style="display: none;">
                    <small>Kontak tidak ditemukan</small>
                </div>
            </div>
        </div>

        <div class="chat-wrapper">
            
            <div id="chatPlaceholder">
                <img src="https://img.icons8.com/clouds/200/000000/chat.png" style="opacity: 0.6;">
                <h4 class="font-weight-light mt-3">WBS Chat System</h4>
                <p class="text-muted">Pilih kontak untuk mulai berdiskusi.</p>
            </div>

            <div id="chatBox" class="chat-area" style="display: none !important;">
                
                <div class="chat-header">
                    <div class="d-flex align-items-center">
                        <img src="" class="kontak-avatar" id="headerAvatar" style="width: 45px; height: 45px;"> <div>
                            <h6 class="mb-0 font-weight-bold" id="namaLawanBicara">User Name</h6>
                            </div>
                    </div>
                </div>

                <div class="chat-messages" id="isiChat">
                    </div>

                <div id="preview-container">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-image text-primary mr-2"></i>
                            <span id="file-name" class="font-weight-bold text-dark">nama_file.jpg</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger rounded-circle" id="cancel-img"><i class="fas fa-times"></i></button>
                    </div>
                </div>

                <div class="chat-footer">
                    <form id="formKirim" enctype="multipart/form-data" style="width: 100%; display: flex; align-items: center;">
                        <input type="hidden" id="id_penerima" name="id_penerima">
                        
                        <label for="fileGambar" class="btn-attach mb-0" title="Lampirkan Gambar">
                            <i class="fas fa-paperclip"></i>
                        </label>
                        <input type="file" id="fileGambar" name="gambar" accept="image/*" style="display: none;">

                        <input type="text" id="pesanInput" name="pesan" class="input-message" placeholder="Ketik pesan..." autocomplete="off">
                        
                        <button type="submit" class="btn-send" title="Kirim">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
$(document).ready(function() {
    let idLawan = null;
    let intervalChat = null;

    // --- FITUR PENCARIAN KONTAK (FILTERING) ---
    $('#searchContact').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        var visibleItems = 0;

        $('#kontakListContainer .kontak-link').filter(function() {
            var match = $(this).find('.contact-name').text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(match);
            if (match) visibleItems++;
        });

        if (visibleItems === 0) {
            $('#noContactFound').show();
        } else {
            $('#noContactFound').hide();
        }
    });

    // 1. KLIK KONTAK
    $(document).on('click', '.kontak-link', function(e) {
        e.preventDefault();
        $('.kontak-link').removeClass('active');
        $(this).addClass('active');

        idLawan = $(this).data('id');
        let nama = $(this).data('nama');
        
        $('#id_penerima').val(idLawan);
        $('#namaLawanBicara').text(nama);
        $('#headerAvatar').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(nama) + '&background=random');

        // Ganti Tampilan
        $('#chatPlaceholder').fadeOut(200, function() {
            $('#chatBox').css('display', 'flex').hide().fadeIn(200);
        });

        loadPesan(true); 

        if (intervalChat) clearInterval(intervalChat);
        intervalChat = setInterval(function() { loadPesan(false); }, 3000);
    });

    // 2. LOAD PESAN
    function loadPesan(autoScroll) {
        if(!idLawan) return;

        $.ajax({
            url: "<?php echo base_url('chat/load_pesan'); ?>",
            type: "POST",
            data: {id_lawan: idLawan},
            success: function(response) {
                $('#isiChat').html(response);
                if(autoScroll) {
                    scrollToBottom();
                }
            }
        });
    }

    function scrollToBottom() {
        var chatDiv = document.getElementById("isiChat");
        chatDiv.scrollTop = chatDiv.scrollHeight;
    }

    // 3. PREVIEW GAMBAR
    $('#fileGambar').change(function() {
        if (this.files.length > 0) {
            $('#file-name').text(this.files[0].name);
            $('#preview-container').slideDown();
        }
    });

    $('#cancel-img').click(function() {
        $('#fileGambar').val('');
        $('#preview-container').slideUp();
    });

    // 4. KIRIM PESAN
    $('#formKirim').submit(function(e) {
        e.preventDefault();
        
        let pesan = $('#pesanInput').val();
        let gambar = $('#fileGambar').val();

        if(pesan.trim() == "" && gambar == "") return;

        let btnSend = $('.btn-send');
        let originalIcon = btnSend.html();
        btnSend.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        let formData = new FormData(this);

        $.ajax({
            url: "<?php echo base_url('chat/kirim_pesan'); ?>",
            type: "POST",
            data: formData,
            contentType: false, 
            processData: false,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    $('#pesanInput').val(''); 
                    $('#fileGambar').val(''); 
                    $('#preview-container').hide();
                    loadPesan(true); 
                } else {
                    alert("Gagal Kirim: " + response.msg);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert("Terjadi kesalahan koneksi.");
            },
            complete: function() {
                btnSend.html(originalIcon).prop('disabled', false);
            }
        });
    });
});
</script>