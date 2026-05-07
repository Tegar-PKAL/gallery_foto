<?php
include 'config/database.php';
include 'includes/header.php';

$uid = $_SESSION['UserID'] ?? null;
$pesan = "";
$albumID = $_GET['id'] ?? null;

if (!$uid) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_album']);
    $desc = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tgl  = date('Y-m-d');

    $query_tambah = "INSERT INTO album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) 
                     VALUES ('$nama', '$desc', '$tgl', '$uid')";

    if (mysqli_query($conn, $query_tambah)) {
        $pesan = "sukses";
    } else {
        $pesan = "gagal";
    }
}
?>

<style>
    body {
        background-color: #F4F3EE !important;
    }
</style>

<div class="container mx-auto px-4 py-10" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <?php if ($albumID): ?>
        <a href="album.php" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest transition mb-6 group" style="color: #8A817C;">
            <span class="mr-2 group-hover:-translate-x-1 transition-transform" style="color: #FFB700;">←</span> <span class="group-hover:text-[#463F3A] transition-colors">Kembali ke Daftar Album</span>
        </a>
    <?php endif; ?>

    <div class="mb-10 text-center lg:text-left">
        <h1 class="text-4xl font-black tracking-tighter uppercase" style="color: #463F3A;">
            <?php
            if ($albumID) {
                $info = mysqli_fetch_array(mysqli_query($conn, "SELECT NamaAlbum FROM album WHERE AlbumID = '$albumID'"));
                echo "Isi Album: <span style='color: #FFB700;'>" . ($info['NamaAlbum'] ?? 'Tidak Ditemukan') . "</span>";
            } else {
                echo "Manajemen <span style='color: #FFB700;'>Album</span>";
            }
            ?>
        </h1>
        <p class="font-semibold text-sm mt-2" style="color: #8A817C;">
            <?= $albumID ? "Melihat koleksi foto di dalam folder ini." : "Kelola koleksi foto kamu dalam folder yang rapi." ?>
        </p>
    </div>

    <?php if (!$albumID): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <div class="bg-white/70 backdrop-blur-md p-8 rounded-[2.5rem] h-fit border border-[#BCB8B1] shadow-xl hover:shadow-2xl transition-all">
                <h2 class="text-2xl font-black mb-6 uppercase tracking-tighter text-center" style="color: #463F3A;">Buat Album <span style="color: #FFB700;">Baru</span></h2>

                <?php if ($pesan == "sukses"): ?>
                    <div class="bg-[#FFB700]/20 p-4 rounded-2xl mb-6 text-sm border border-[#FFB700] text-center font-bold" style="color: #463F3A;">
                        ✅ Album Berhasil Dibuat!
                    </div>
                <?php endif; ?>

                <form action="" method="post" class="space-y-5">
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest ml-4 block mb-1" style="color: #8A817C;">Nama Album</label>
                        <input type="text" name="nama_album" placeholder="Contoh: Liburan 2024" class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#BCB8B1] transition-all shadow-inner" required>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest ml-4 block mb-1" style="color: #8A817C;">Deskripsi Singkat</label>
                        <textarea
                            name="DeskripsiFoto"
                            placeholder="Ceritakan tentang foto ini..."
                            class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#FFB700] focus:border-transparent text-[#463F3A] placeholder-[#BCB8B1] transition-all shadow-sm resize-none overflow-hidden min-h-[120px]"
                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                            required></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" name="tambah" class="w-full bg-[#FFB700] hover:bg-[#e6a500] p-4 rounded-2xl font-black text-[#463F3A] uppercase tracking-widest transition-all shadow-md hover:shadow-lg active:scale-95">
                            SIMPAN ALBUM
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php
                    $albums = mysqli_query($conn, "SELECT * FROM album WHERE UserID = '$uid' ORDER BY AlbumID DESC");
                    while ($a = mysqli_fetch_array($albums)):
                    ?>
                        <div class="bg-white/70 backdrop-blur-md p-6 rounded-[2rem] border border-[#BCB8B1] hover:border-[#FFB700] transition-all group shadow-md hover:shadow-xl flex flex-col h-full justify-between">
                            <div>
                                <div class="text-4xl mb-4 group-hover:scale-110 transition-transform origin-left">📁</div>
                                <h3 class="text-xl font-black transition-colors" style="color: #463F3A;">
                                    <span class="group-hover:text-[#FFB700]"><?= $a['NamaAlbum'] ?></span>
                                </h3>
                                <p class="font-medium text-xs mt-2" style="color: #8A817C;"><?= $a['Deskripsi'] ?></p>
                            </div>
                            <div class="flex gap-3 mt-6">
                                <a href="album.php?id=<?= $a['AlbumID'] ?>" class="flex-1 bg-[#F4F3EE] hover:bg-[#FFB700] border border-[#BCB8B1] hover:border-[#FFB700] text-center py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all" style="color: #463F3A;">BUKA</a>

                                <a href="hapus.php?type=album&id=<?= $a['AlbumID'] ?>" onclick="return confirm('Yakin ingin menghapus album ini beserta isinya?')" class="flex-1 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-center py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">HAPUS</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php
            $fotos = mysqli_query($conn, "SELECT * FROM foto WHERE AlbumID = '$albumID' ORDER BY FotoID DESC");
            if (mysqli_num_rows($fotos) > 0):
                while ($f = mysqli_fetch_array($fotos)):
            ?>
                    <div class="group relative bg-white rounded-[2rem] p-3 transition-all duration-500 hover:-translate-y-2 border border-[#BCB8B1] shadow-md hover:shadow-2xl">
                        <div class="relative overflow-hidden rounded-[1.5rem] h-64 shadow-inner">
                            <img src="assets/uploads/<?= $f['LokasiFile']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">

                            <div class="absolute inset-0 bg-gradient-to-t from-[#463F3A]/90 via-[#463F3A]/40 to-transparent p-5 flex flex-col justify-end opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <p class="text-white font-black text-lg tracking-tight leading-tight mb-3"><?= $f['JudulFoto'] ?></p>
                                <a href="detail.php?id=<?= $f['FotoID'] ?>" class="text-center py-2.5 bg-[#FFB700] hover:bg-[#e6a500] text-[10px] font-black uppercase tracking-widest rounded-xl text-[#463F3A] transition-all shadow-lg active:scale-95">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <div class="col-span-full py-20 text-center bg-white/50 backdrop-blur-sm rounded-[2.5rem] border-dashed border-2 border-[#BCB8B1] shadow-sm">
                    <span class="text-4xl mb-4 block opacity-50">📂</span>
                    <p class="font-bold text-sm" style="color: #8A817C;">Album ini masih kosong. Silakan upload foto dan pilih album ini.</p>
                    <a href="upload.php" class="inline-block mt-4 bg-[#FFB700] hover:bg-[#e6a500] px-6 py-3 rounded-xl font-black text-[#463F3A] uppercase tracking-widest text-[10px] transition-all shadow-md">Upload Karya Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>