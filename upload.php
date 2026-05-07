<?php
include 'config/database.php';
include 'includes/header.php';

// Cek sesi login
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit; 
}

$userID = $_SESSION['UserID'];

if (isset($_POST['upload'])) {
    // Mencegah error jika judul kosong
    $judul = mysqli_real_escape_string($conn, $_POST['judul'] ?? '');
    
    // PERBAIKAN 1: Mengakomodasi "deskripsi" maupun "DeskripsiFoto" agar tidak undefined/null
    $deskripsiRaw = $_POST['deskripsi'] ?? $_POST['DeskripsiFoto'] ?? '';
    $deskripsi = mysqli_real_escape_string($conn, $deskripsiRaw);

    // Logika Tanpa Album: Jika tidak pilih album, set NULL
    $albumID = !empty($_POST['album_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['album_id']) . "'" : "NULL";
    $tanggal = date('Y-m-d');

    $filename = $_FILES['foto']['name'];
    $tmp_name = $_FILES['foto']['tmp_name'];
    $new_filename = time() . "_" . $filename;
    
    // PERBAIKAN 2: Mengamankan nama file yang mungkin mengandung tanda kutip (') agar SQL tidak error
    $new_filename_safe = mysqli_real_escape_string($conn, $new_filename);

    if (move_uploaded_file($tmp_name, 'assets/uploads/' . $new_filename)) {
        // Query menggunakan $new_filename_safe yang sudah di-escape
        $sql = "INSERT INTO foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) 
                VALUES ('$judul', '$deskripsi', '$tanggal', '$new_filename_safe', $albumID, '$userID')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Foto berhasil diupload!'); window.location='index.php';</script>";
        } else {
            // Menampilkan alert jika database masih gagal memproses
            echo "<script>alert('Gagal menyimpan ke database: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Gagal memindahkan file foto ke folder server!');</script>";
    }
}
?>

<style>
    body {
        background-color: #F4F3EE !important;
    }
</style>

<div class="container mx-auto px-4 max-w-2xl py-10" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="bg-white/70 backdrop-blur-md p-10 rounded-[2.5rem] border border-[#BCB8B1] shadow-xl hover:shadow-2xl transition-all">

        <div class="text-center mb-8">
            <h2 class="text-3xl font-black uppercase tracking-tighter" style="color: #463F3A;">
                Upload <span style="color: #FFB700;">Karya</span>
            </h2>
            <p class="font-semibold text-sm mt-2" style="color: #8A817C;">Bagikan momen terbaikmu ke dunia.</p>
        </div>

        <form action="" method="post" enctype="multipart/form-data" class="space-y-6">

            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest ml-4 block mb-1" style="color: #8A817C;">Pilih Album (Opsional)</label>
                <select name="album_id" class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] transition-all shadow-inner cursor-pointer">
                    <option value="">-- Tanpa Album --</option>
                    <?php
                    $albumQuery = mysqli_query($conn, "SELECT * FROM album WHERE UserID = '$userID'");
                    while ($a = mysqli_fetch_array($albumQuery)): ?>
                        <option value="<?= $a['AlbumID'] ?>"><?= $a['NamaAlbum'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest ml-4 block mb-1" style="color: #8A817C;">File Foto</label>
                <div class="bg-white border border-[#BCB8B1] p-2 rounded-2xl shadow-inner flex items-center">
                    <input type="file" name="foto" class="w-full text-[#8A817C] file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:bg-[#F4F3EE] file:text-[#463F3A] file:font-black file:uppercase file:tracking-widest file:text-[10px] hover:file:bg-[#FFB700] transition-all cursor-pointer" required>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest ml-4 block mb-1" style="color: #8A817C;">Judul Foto</label>
                <input type="text" name="judul" placeholder="Masukkan judul foto" class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#BCB8B1] transition-all shadow-inner" required>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest ml-4 block mb-1" style="color: #8A817C;">Deskripsi</label>
                <textarea
                    name="deskripsi"
                    placeholder="Ceritakan tentang foto ini..."
                    class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#FFB700] focus:border-transparent text-[#463F3A] placeholder-[#BCB8B1] transition-all shadow-sm resize-none overflow-hidden min-h-[120px]"
                    oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                    required></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" name="upload" class="w-full bg-[#FFB700] hover:bg-[#e6a500] p-4 rounded-2xl font-black text-[#463F3A] uppercase tracking-widest transition-all hover:scale-[1.02] active:scale-95 shadow-md hover:shadow-lg">
                    PUBLISH KARYA
                </button>
            </div>
        </form>

    </div>
</div>

<?php include 'includes/footer.php'; ?>