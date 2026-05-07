<?php 
include 'config/database.php';
include 'includes/header.php';

// Ambil ID User dari URL
$target_user_id = isset($_GET['UserID']) ? mysqli_real_escape_string($conn, $_GET['UserID']) : '';
$album_id_filter = $_GET['album'] ?? ''; 

// Cek apakah UserID ada
if (empty($target_user_id)) {
    echo "<script>location.href='index.php';</script>";
    exit;
}

// Ambil data user
$user_query = mysqli_query($conn, "SELECT * FROM user WHERE UserID = '$target_user_id'");
$user_data = mysqli_fetch_assoc($user_query);

if (!$user_data) {
    echo "<div class='py-20 text-center font-bold text-[#463F3A]'>User tidak ditemukan.</div>";
    include 'includes/footer.php';
    exit;
}

$query_album_list = mysqli_query($conn, "SELECT * FROM album WHERE UserID = '$target_user_id'");

// Query Foto Filter
$where_foto = "WHERE UserID = '$target_user_id'";
if (!empty($album_id_filter)) { 
    $where_foto .= " AND AlbumID = '$album_id_filter'"; 
}
$query_foto = mysqli_query($conn, "SELECT * FROM foto $where_foto ORDER BY FotoID DESC");

// Variabel pengecekan Admin
$isAdmin = (isset($_SESSION['Role']) && $_SESSION['Role'] === 'admin');
?>

<style>
    body { background-color: #F4F3EE !important; }
    
    /* Styling khusus untuk checkbox admin */
    .admin-checkbox {
        appearance: none;
        background-color: white;
        margin: 0;
        font: inherit;
        color: currentColor;
        width: 1.5em;
        height: 1.5em;
        border: 2px solid #BCB8B1;
        border-radius: 0.35em;
        display: grid;
        place-content: center;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    .admin-checkbox::before {
        content: "";
        width: 0.8em;
        height: 0.8em;
        transform: scale(0);
        transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em white;
        background-color: transform;
        transform-origin: center;
        clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
    }
    .admin-checkbox:checked {
        background-color: #e11d48; /* Red */
        border-color: #e11d48;
    }
    .admin-checkbox:checked::before {
        transform: scale(1);
    }
</style>

<div class="w-full px-4 md:px-8 py-12" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    
    <div class="max-w-6xl mx-auto mb-16 p-8 md:p-10 rounded-[2.5rem] bg-white/80 backdrop-blur-xl border border-[#BCB8B1] shadow-xl flex flex-col md:flex-row items-center gap-8 relative overflow-hidden group">
        
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#FFB700]/10 rounded-full blur-3xl group-hover:bg-[#FFB700]/20 transition-all duration-700 pointer-events-none"></div>

        <div class="w-32 h-32 md:w-36 md:h-36 rounded-full border-4 border-[#FFB700] shadow-lg p-1 overflow-hidden bg-white shrink-0 z-10">
            <?php 
                $fotoPath = "assets/profiles/" . $user_data['FotoProfil'];
                $displayFoto = (!empty($user_data['FotoProfil']) && file_exists($fotoPath)) ? $fotoPath : 'assets/profiles/default.png';
            ?>
            <img src="<?= $displayFoto ?>" class="w-full h-full rounded-full object-cover">
        </div>
        
        <div class="flex-1 text-center md:text-left z-10">
            <h2 class="text-4xl md:text-5xl font-black text-[#463F3A] uppercase tracking-tighter italic">
                <?= htmlspecialchars($user_data['Username']) ?>'S <span style="color: #FFB700;">SPACE.</span>
            </h2>
            <p class="font-medium text-sm mt-2 text-[#8A817C]">Jelajahi karya dan galeri pengguna ini.</p>
        </div>

        <?php if($isAdmin): ?>
        <div class="flex flex-col gap-3 w-full md:w-auto shrink-0 z-10 mt-4 md:mt-0 border-l-0 md:border-l-2 border-[#BCB8B1] md:pl-8">
            <p class="text-[10px] font-black uppercase tracking-widest text-[#e11d48] text-center md:text-left mb-1">🛠️ Admin Zone</p>
            <a href="config/proses_hapus_album.php?action=all&user=<?= $target_user_id ?>" 
               onclick="return confirm('PERINGATAN KERAS: Yakin ingin menghapus SEMUA album dan foto milik user ini secara permanen?')"
               class="w-full md:w-64 px-6 py-3.5 bg-[#e11d48] hover:bg-[#be123c] text-white text-[11px] font-black rounded-xl transition-all shadow-lg hover:shadow-xl shadow-red-500/20 uppercase tracking-widest text-center flex items-center justify-center gap-2">
               <span>⚠️</span> Remove All Content
            </a>
        </div>
        <?php endif; ?>
    </div>


    <div class="mb-16 max-w-6xl mx-auto">
        <h3 class="text-center font-black tracking-[0.3em] uppercase text-sm mb-6" style="color: #463F3A;">
            Koleksi <span style="color: #FFB700;">Album</span>
        </h3>
        
        <form id="quickDeleteForm" action="config/proses_hapus_album.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $target_user_id ?>">
            <input type="hidden" name="action" value="selected">
            
            <div class="flex flex-wrap justify-center gap-4 relative">
                
                <a href="user_space.php?UserID=<?= $target_user_id ?>" 
                   class="px-6 py-3 rounded-xl text-xs font-black transition-all uppercase tracking-widest flex items-center justify-center <?= empty($album_id_filter) ? 'bg-[#463F3A] text-white shadow-lg' : 'bg-white text-[#8A817C] border border-[#BCB8B1] hover:border-[#463F3A] hover:text-[#463F3A]' ?>">
                   SEMUA FOTO
                </a>

                <?php mysqli_data_seek($query_album_list, 0); ?>
                <?php while($alb = mysqli_fetch_array($query_album_list)): ?>
                    <div class="relative group flex items-center bg-white border <?= ($album_id_filter == $alb['AlbumID']) ? 'border-[#FFB700] ring-2 ring-[#FFB700]/30' : 'border-[#BCB8B1] hover:border-[#FFB700]' ?> rounded-xl pr-1 transition-all overflow-hidden shadow-sm">
                        
                        <?php if($isAdmin): ?>
                        <div class="pl-3 pr-2 py-3 border-r border-[#BCB8B1]/50 bg-[#F4F3EE]/50 flex items-center justify-center">
                            <input type="checkbox" name="album_ids[]" value="<?= $alb['AlbumID'] ?>" class="admin-checkbox" onchange="checkSelectedAlbums()">
                        </div>
                        <?php endif; ?>

                        <a href="user_space.php?UserID=<?= $target_user_id ?>&album=<?= $alb['AlbumID'] ?>" 
                           class="px-5 py-3 text-xs font-black uppercase tracking-widest block flex-1 <?= ($album_id_filter == $alb['AlbumID']) ? 'text-[#463F3A] bg-[#FFB700]/10' : 'text-[#8A817C] hover:text-[#463F3A]' ?>">
                           📁 <?= htmlspecialchars($alb['NamaAlbum']) ?>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if($isAdmin): ?>
            <div id="deleteActionContainer" class="mt-8 flex justify-center opacity-0 translate-y-4 pointer-events-none transition-all duration-300 h-0 overflow-hidden">
                <button type="submit" onclick="return confirm('Yakin ingin menghapus semua album yang dipilih beserta isinya secara permanen?')"
                        class="px-8 py-3 bg-[#e11d48] hover:bg-[#be123c] text-white rounded-xl text-[11px] font-black uppercase tracking-widest transition-all shadow-lg flex items-center gap-2">
                        <span>🗑️</span> Hapus <span id="selectedCount" class="bg-white text-[#e11d48] px-2 py-0.5 rounded-md">0</span> Album Terpilih
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 max-w-7xl mx-auto">
        <?php if(mysqli_num_rows($query_foto) == 0): ?>
            <div class="col-span-full py-20 text-center bg-white/50 backdrop-blur-sm rounded-[2.5rem] border-dashed border-2 border-[#BCB8B1] shadow-sm">
                <span class="text-4xl mb-4 block opacity-50">📷</span>
                <p class="font-bold text-sm text-[#8A817C]">Tidak ada foto yang ditemukan di area ini.</p>
            </div>
        <?php endif; ?>

        <?php while($f = mysqli_fetch_array($query_foto)): ?>
            <div class="relative group aspect-square rounded-[1.5rem] overflow-hidden bg-white border border-[#BCB8B1] shadow-sm hover:shadow-2xl transition-all duration-500">
                <img src="assets/uploads/<?= $f['LokasiFile'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                
                <div class="absolute inset-0 bg-gradient-to-t from-[#463F3A]/90 via-[#463F3A]/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end items-center p-5">
                    <p class="text-white font-black text-center mb-4 uppercase tracking-tighter leading-tight drop-shadow-md"><?= htmlspecialchars($f['JudulFoto']) ?></p>
                    
                    <div class="flex gap-2 w-full px-1">
                        <a href="detail.php?id=<?= $f['FotoID'] ?>" class="flex-1 text-center py-2.5 bg-[#FFB700] hover:bg-[#e6a500] rounded-xl text-[10px] font-black uppercase text-[#463F3A] transition-colors shadow-md active:scale-95">VIEW</a>
                        
                        <?php if($isAdmin): ?>
                            <a href="hapus.php?id=<?= $f['FotoID'] ?>" onclick="return confirm('Hapus foto ini?')" class="flex-1 text-center py-2.5 bg-[#e11d48] hover:bg-[#be123c] text-white rounded-xl text-[10px] font-black uppercase transition-colors shadow-md active:scale-95">DELETE</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php if($isAdmin): ?>
<script>
// Logic untuk memunculkan tombol hapus jika ada album yang dicentang
function checkSelectedAlbums() {
    const checkboxes = document.querySelectorAll('.admin-checkbox:checked');
    const count = checkboxes.length;
    const actionContainer = document.getElementById('deleteActionContainer');
    const countSpan = document.getElementById('selectedCount');
    
    countSpan.innerText = count;

    if (count > 0) {
        actionContainer.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none', 'h-0');
        actionContainer.classList.add('opacity-100', 'translate-y-0', 'h-auto');
    } else {
        actionContainer.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none', 'h-0');
        actionContainer.classList.remove('opacity-100', 'translate-y-0', 'h-auto');
    }
}
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>