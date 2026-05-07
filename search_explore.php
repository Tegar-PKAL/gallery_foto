<?php 
include 'config/database.php';
include 'includes/header.php';

$keyword = mysqli_real_escape_string($conn, $_GET['query'] ?? '');

// 1. QUERY UNTUK CARI USER
$query_user = mysqli_query($conn, "SELECT * FROM user WHERE Username LIKE '%$keyword%' LIMIT 5");

// 2. QUERY UNTUK CARI FOTO (Berdasarkan Judul, Deskripsi, atau Tagar)
$query_foto = mysqli_query($conn, "SELECT foto.*, user.Username, user.FotoProfil as Avatar 
    FROM foto 
    JOIN user ON foto.UserID = user.UserID 
    WHERE JudulFoto LIKE '%$keyword%' 
    OR DeskripsiFoto LIKE '%$keyword%' 
    ORDER BY FotoID DESC");

// Fungsi untuk membuat hashtag jadi link klik-able
function formatHashtag($text) {
    return preg_replace('/#(\w+)/', '<a href="search_explore.php?query=%23$1" class="text-blue-400 hover:underline">#$1</a>', $text);
}
?>

<div class="container mx-auto px-4 py-10 min-h-screen">
    <div class="mb-12">
        <h2 class="text-slate-500 uppercase tracking-[0.3em] text-xs font-bold mb-2">Hasil pencarian untuk:</h2>
        <h1 class="text-4xl font-black text-white italic">"<?= htmlspecialchars($keyword) ?>"</h1>
    </div>

    <?php if(mysqli_num_rows($query_user) > 0 && !empty($keyword)): ?>
    <div class="mb-12">
        <h3 class="text-white font-bold mb-6 flex items-center">
            <span class="bg-blue-600 w-2 h-6 rounded-full mr-3"></span> Pengguna
        </h3>
        <div class="flex flex-wrap gap-4">
            <?php while($u = mysqli_fetch_array($query_user)): ?>
            <a href="user_profile.php?id=<?= $u['UserID'] ?>" class="glass p-4 rounded-3xl flex items-center space-x-4 hover:bg-slate-800 transition min-w-[200px]">
                <img src="<?= !empty($u['FotoProfil']) ? 'assets/profiles/'.$u['FotoProfil'] : 'https://ui-avatars.com/api/?name='.$u['Username'] ?>" class="w-12 h-12 rounded-full object-cover border-2 border-blue-500">
                <div>
                    <p class="text-white font-bold text-sm">@<?= $u['Username'] ?></p>
                    <p class="text-slate-500 text-[10px] uppercase"><?= $u['NamaLengkap'] ?></p>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <div>
        <h3 class="text-white font-bold mb-6 flex items-center">
            <span class="bg-emerald-500 w-2 h-6 rounded-full mr-3"></span> Postingan & Tagar
        </h3>
        
        <?php if(mysqli_num_rows($query_foto) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php while($f = mysqli_fetch_array($query_foto)): ?>
                <div class="glass rounded-[2.5rem] overflow-hidden group border border-white/5 shadow-2xl">
                    <div class="relative h-64">
                        <img src="assets/uploads/<?= $f['LokasiFile'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                        <a href="detail.php?id=<?= $f['FotoID'] ?>" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="bg-white text-black px-6 py-2 rounded-full font-bold text-xs">LIHAT</span>
                        </a>
                    </div>
                    <div class="p-6">
                        <p class="text-white font-bold mb-2"><?= $f['JudulFoto'] ?></p>
                        <p class="text-slate-400 text-xs leading-relaxed line-clamp-2">
                            <?= formatHashtag($f['DeskripsiFoto']) ?>
                        </p>
                        <div class="mt-4 pt-4 border-t border-white/5 flex justify-between items-center">
                            <span class="text-[10px] text-emerald-400 font-bold uppercase">@<?= $f['Username'] ?></span>
                            <span class="text-[10px] text-slate-600"><?= $f['TanggalUnggah'] ?></span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20 glass rounded-[3rem]">
                <p class="text-slate-500 italic">Tidak menemukan apapun yang cocok dengan "<?= $keyword ?>"</p>
                <a href="index.php" class="text-blue-400 text-xs font-bold mt-4 inline-block hover:underline">KEMBALI KE BERANDA</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>