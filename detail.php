<?php
include 'config/database.php';
include 'includes/header.php';

// #PERBAIKAN: Escape parameter id agar tidak terkena SQL Injection
$fotoID = mysqli_real_escape_string($conn, $_GET['id'] ?? '');
$userID = $_SESSION['UserID'] ?? null;
// PERBAIKAN: Gunakan Level atau Role sesuai sistem loginmu (disamakan dengan user_space)
$role   = $_SESSION['Role'] ?? 'user'; 

// Mengambil detail foto
$query_post = mysqli_query($conn, "SELECT foto.*, user.Username, user.FotoProfil, user.UserID 
    FROM foto 
    JOIN user ON foto.UserID = user.UserID 
    WHERE FotoID = '$fotoID'");
$data = mysqli_fetch_array($query_post);

// Jika foto tidak ada
if (!$data) {
    echo "<div class='text-center py-20 font-bold'>Foto tidak ditemukan.</div>";
    include 'includes/footer.php';
    exit;
}

// Cek Album
$albumName = "Tanpa Album";
if (!empty($data['AlbumID'])) {
    $albumQuery = mysqli_query($conn, "SELECT NamaAlbum FROM album WHERE AlbumID = '".$data['AlbumID']."'");
    if ($albumData = mysqli_fetch_array($albumQuery)) {
        $albumName = $albumData['NamaAlbum'];
    }
}

// Hitung Like
$countLike = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM likefoto WHERE FotoID = '$fotoID'"));

// Cek apakah user sudah like
$isLiked = false;
if ($userID) {
    $cekLike = mysqli_query($conn, "SELECT * FROM likefoto WHERE FotoID='$fotoID' AND UserID='$userID'");
    if (mysqli_num_rows($cekLike) > 0) $isLiked = true;
}

// =========================================================================
// LOGIKA HASHTAG (Mengubah #tag menjadi link yang bisa diklik)
// =========================================================================
$deskripsi_aman = htmlspecialchars($data['DeskripsiFoto']); 
// Regex untuk mendeteksi kata yang dimulai dengan #
$deskripsi_berformat = preg_replace('/#(\w+)/', '<a href="index.php?cari=%23$1" class="text-[#FFB700] font-extrabold hover:underline hover:text-[#e6a500] transition-all" title="Cari hashtag ini">#$1</a>', $deskripsi_aman);
// =========================================================================

?>

<style>
    body {
        background-color: #F4F3EE !important; 
    }
    
    /* Custom Scrollbar untuk Komentar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #BCB8B1;
        border-radius: 20px;
    }
</style>

<div class="container mx-auto px-4 py-8 max-w-6xl" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white/70 backdrop-blur-md rounded-[2rem] overflow-hidden shadow-2xl border border-[#BCB8B1] h-fit hover:shadow-3xl transition-all">
            <img src="assets/uploads/<?php echo htmlspecialchars($data['LokasiFile']); ?>" class="w-full h-auto object-contain max-h-[75vh] bg-[#463F3A]/5" alt="<?= htmlspecialchars($data['JudulFoto']) ?>">

            <div class="p-6 flex justify-between items-center bg-[#F4F3EE]/90 border-t border-[#BCB8B1]">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <a href="like.php?id=<?= $fotoID ?>" class="text-3xl transition-transform hover:scale-125 drop-shadow-md">
                            <?= $isLiked ? '❤️' : '🤍' ?>
                        </a>
                        <span class="font-black text-xl" style="color: #463F3A;"><?= $countLike ?></span>
                    </div>

                    <?php if ($role === 'admin' || $data['UserID'] == $userID): ?>
                        <a href="hapus.php?id=<?= $fotoID ?>"
                            onclick="return confirm('Yakin ingin menghapus foto ini selamanya?')"
                            class="bg-[#e11d48]/10 text-[#e11d48] px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#e11d48] hover:text-white transition flex items-center shadow-sm">
                            <span class="mr-2">🗑️</span> Hapus
                        </a>
                    <?php endif; ?>
                </div>
                
                <span class="font-bold text-[10px] uppercase tracking-widest" style="color: #8A817C;">📅 <?= date('d M Y', strtotime($data['TanggalUnggah'])) ?></span>
            </div>
        </div>

        <div class="flex flex-col space-y-6">
            
            <div class="bg-white/70 backdrop-blur-md p-8 rounded-[2rem] border border-[#BCB8B1] shadow-xl hover:shadow-2xl transition-all">
                <h1 class="text-4xl md:text-5xl font-black mb-3 tracking-tighter" style="color: #463F3A;"><?= htmlspecialchars($data['JudulFoto']) ?></h1>
                
                <div class="flex items-center space-x-3 mb-5 border-b border-[#BCB8B1] pb-4">
                    <a href="user_space.php?UserID=<?= $data['UserID'] ?>" class="flex items-center space-x-3 group">
                        <div class="relative">
                            <?php 
                                $fotoProfil = !empty($data['FotoProfil']) ? $data['FotoProfil'] : 'default.png';
                            ?>
                            <img src="assets/profiles/<?= $fotoProfil ?>" 
                                 class="w-10 h-10 rounded-full object-cover border-2 transition-all hover:scale-105 shadow-md" style="border-color: #BCB8B1; group-hover:border-color #FFB700;">
                        </div>
                        <p class="font-bold italic transition-colors" style="color: #8A817C;">
                            Oleh: <span class="group-hover:text-[#463F3A]">@<?= htmlspecialchars($data['Username']) ?></span>
                        </p>
                    </a>
                </div>
                
                <p class="font-medium text-sm leading-relaxed mb-4 whitespace-pre-wrap" style="color: #463F3A;"><?= $deskripsi_berformat ?></p>
                
                <div class="mt-4 p-3 bg-[#F4F3EE] rounded-xl border border-[#BCB8B1] inline-flex items-center gap-2 shadow-sm">
                    <span class="text-xl">📁</span>
                    <span class="font-bold text-xs uppercase tracking-widest" style="color: #8A817C;"><?= htmlspecialchars($albumName) ?></span>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-md p-6 rounded-[2rem] border border-[#BCB8B1] shadow-xl hover:shadow-2xl transition-all flex-1 min-h-[300px] flex flex-col">
                <h3 class="font-black mb-6 border-b pb-2 uppercase tracking-tight text-xl" style="color: #463F3A; border-color: #BCB8B1;">Diskusi</h3>
                
                <div id="list-komentar" class="space-y-4 overflow-y-auto max-h-[400px] pr-2 flex-1 custom-scrollbar">
                    <div class="h-full flex flex-col items-center justify-center py-10 animate-pulse">
                        <span class="text-4xl mb-2 block">⏳</span>
                        <p class='italic font-bold text-sm' style="color: #8A817C;">Memuat komentar...</p>
                    </div>
                </div>
            
                <?php if ($userID): ?>
                    <form id="formKomentar" class="mt-6 flex items-end space-x-2 pt-4 border-t border-[#BCB8B1]/50">
                        <input type="hidden" name="foto_id" value="<?= $fotoID ?>">
                        <textarea id="isiKomentar" name="isi_komentar" 
                                  placeholder="Tulis komentar..." 
                                  class="flex-1 bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#FFB700] focus:border-transparent text-[#463F3A] placeholder-[#BCB8B1] transition-all shadow-sm resize-none overflow-hidden min-h-[56px]"
                                  style="height: 56px;"
                                  oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                  required></textarea>
                        <button type="submit" class="bg-[#FFB700] hover:bg-[#e6a500] px-6 h-[56px] rounded-2xl font-black text-[#463F3A] uppercase tracking-widest text-[10px] transition shadow-md shadow-[#FFB700]/20 active:scale-95 flex items-center justify-center">Kirim</button>
                    </form>
                <?php else: ?>
                    <div class="mt-6 p-5 bg-[#F4F3EE]/50 rounded-2xl border border-dashed border-[#BCB8B1] text-center shadow-inner">
                        <p class="text-sm font-bold" style="color: #8A817C;">Ingin ikut berdiskusi? <a href="login.php" class="underline hover:text-[#463F3A] transition-colors" style="color: #463F3A;">Login sekarang</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    const fotoID = "<?= $fotoID ?>";
    const listKomentar = document.getElementById('list-komentar');
    const formKomentar = document.getElementById('formKomentar');
    const inputKomentar = document.getElementById('isiKomentar');

    // Fungsi untuk mengambil komentar terbaru tanpa refresh
    function loadKomentar() {
        // Ganti path 'config/' di bawah ini kalau file ajax-nya ada di folder lain
        fetch(`config/ambil_komentar_ajax.php?id=${fotoID}`)
            .then(response => response.text())
            .then(html => {
                listKomentar.innerHTML = html;
            })
            .catch(error => console.error('Error fetching comments:', error));
    }

    // Panggil langsung saat halaman dibuka
    loadKomentar();

    // POLING: Ambil data setiap 2 detik (2000 ms) biar kaya WhatsApp!
    setInterval(loadKomentar, 2000);

    // Kirim komentar via AJAX
    if (formKomentar) {
        formKomentar.addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah halaman nge-refresh!
            
            const formData = new FormData(this);
            
            // Tombol dan text area di-disable sementara saat mengirim
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '...';

            fetch('config/kirim_komentar_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    inputKomentar.value = ''; // Kosongkan text area
                    inputKomentar.style.height = '56px'; // Kembalikan ke ukuran semula
                    loadKomentar(); // Langsung tampilkan komentar baru
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Kirim';
            });
        });
        
        // Fitur Enter untuk mengirim langsung (tekan Shift+Enter untuk baris baru)
        inputKomentar.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim() !== '') {
                    formKomentar.dispatchEvent(new Event('submit'));
                }
            }
        });
    }
</script>