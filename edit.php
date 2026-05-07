<?php 
include 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['UserID'])) header("Location: login.php");

$fotoID = $_GET['id'];
$userID = $_SESSION['UserID'];

$query = mysqli_query($conn, "SELECT * FROM foto WHERE FotoID = '$fotoID' AND UserID = '$userID'");
$data = mysqli_fetch_array($query);

if (!$data) header("Location: dashboard.php");

if (isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn, "UPDATE foto SET JudulFoto='$judul', DeskripsiFoto='$deskripsi' WHERE FotoID='$fotoID'");
    if ($update) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='dashboard.php';</script>";
    }
}
?>

<div class="container mx-auto px-4 max-w-2xl">
    <div class="glass p-8 rounded-3xl">
        <h2 class="text-2xl font-bold mb-6">Edit Informasi Foto</h2>
        
        <div class="mb-6 rounded-xl overflow-hidden h-40">
            <img src="assets/uploads/<?php echo $data['LokasiFile']; ?>" class="w-full h-full object-cover opacity-50" alt="">
        </div>

        <form action="" method="post" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-2">Judul Foto</label>
                <input type="text" name="judul" value="<?php echo $data['JudulFoto']; ?>" class="w-full bg-slate-800 border border-slate-700 p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-2">Deskripsi</label>
                <textarea name="deskripsi" class="w-full bg-slate-800 border border-slate-700 p-3 rounded-xl h-32 focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $data['DeskripsiFoto']; ?></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" name="update" class="flex-1 bg-blue-600 hover:bg-blue-700 p-3 rounded-xl font-bold transition">Simpan Perubahan</button>
                <a href="dashboard.php" class="flex-1 text-center bg-slate-700 hover:bg-slate-600 p-3 rounded-xl font-bold transition">Batal</a>
            </div>
        </form>
    </div>
</div>