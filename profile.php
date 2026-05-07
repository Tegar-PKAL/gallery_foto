<?php
include 'config/database.php';
$halaman_sekarang = 'profile.php'; // Penanda halaman profil
include 'includes/header.php';

// Pastikan user sudah login
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['UserID'];

if (isset($_POST['save_profile'])) {
    $namaLengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username    = mysqli_real_escape_string($conn, $_POST['username']);
    $cropped_image = $_POST['cropped_image']; 

    if (!empty($cropped_image)) {
        // Proses data base64 dari Cropper.js
        list($type, $data) = explode(';', $cropped_image);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        
        $nama_file = "PP_" . $userID . "_" . time() . ".png";
        $folder_path = "assets/profiles/";

        // Cek apakah folder ada, jika tidak ada maka buat otomatis
        if (!is_dir($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        
        if (file_put_contents($folder_path . $nama_file, $data)) {
            // Update database hanya jika file berhasil disimpan
            mysqli_query($conn, "UPDATE user SET FotoProfil = '$nama_file' WHERE UserID = '$userID'");
        }
    }

    // Update data teks
    mysqli_query($conn, "UPDATE user SET NamaLengkap = '$namaLengkap', Username = '$username' WHERE UserID = '$userID'");
    
    // Update session agar perubahan langsung terlihat tanpa logout
    $_SESSION['Username'] = $username;

    // Notifikasi dan redirect otomatis ke Halaman Utama
    echo "<script>
        alert('Profil berhasil diperbarui!');
        window.location.href = 'index.php';
    </script>";
    exit();
}

// Ambil data user terbaru
$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM user WHERE UserID = '$userID'"));
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<style>
    body {
        background-color: #F4F3EE !important; 
    }
</style>

<div class="container mx-auto px-4 py-10 max-w-4xl" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="bg-white/70 backdrop-blur-md p-10 rounded-[3rem] shadow-xl border border-[#BCB8B1] transition-all hover:shadow-2xl">
        
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black uppercase tracking-tighter" style="color: #463F3A;">
                Edit <span style="color: #FFB700;">Profil</span>
            </h2>
            <p class="font-semibold text-sm mt-2" style="color: #8A817C;">Sesuaikan identitas digitalmu.</p>
        </div>

        <form action="" method="post" class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">
            
            <div class="flex flex-col items-center space-y-4">
                <div class="relative w-48 h-48 group cursor-pointer">
                    <div class="w-full h-full rounded-full border-4 border-[#FFB700] overflow-hidden bg-white shadow-lg shadow-[#FFB700]/20">
                        <img id="avatarPreview" src="<?= (!empty($user['FotoProfil']) && file_exists('assets/profiles/'.$user['FotoProfil'])) ? 'assets/profiles/'.$user['FotoProfil'] : 'https://ui-avatars.com/api/?name='.urlencode($user['Username']).'&background=random' ?>" class="w-full h-full object-cover">
                    </div>
                    <label for="fileInput" class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white font-black tracking-widest uppercase text-[10px]">GANTI FOTO</label>
                    <input type="file" id="fileInput" class="hidden" accept="image/*">
                </div>
                <p class="text-[10px] uppercase tracking-widest font-bold" style="color: #8A817C;">Profile Picture</p>
            </div>

            <div class="md:col-span-2 space-y-6">
                <input type="hidden" name="cropped_image" id="cropped_image">
                
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest ml-4 block mb-1" style="color: #8A817C;">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="<?= $user['NamaLengkap'] ?>" 
                           class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] transition-all shadow-inner">
                </div>
                
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest ml-4 block mb-1" style="color: #8A817C;">Username</label>
                    <input type="text" name="username" value="<?= $user['Username'] ?>" 
                           class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] transition-all shadow-inner">
                </div>
                
                <div class="pt-4">
                    <button type="submit" name="save_profile" 
                            class="w-full bg-[#FFB700] hover:bg-[#e6a500] p-4 rounded-2xl font-black text-[#463F3A] uppercase tracking-widest transition-all hover:scale-[1.02] active:scale-95 shadow-md hover:shadow-lg">
                        UPDATE PROFIL
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="cropModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-[#463F3A]/80 backdrop-blur-sm p-6" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="bg-[#F4F3EE] p-6 rounded-[2.5rem] max-w-lg w-full shadow-2xl border border-[#BCB8B1]">
        <h3 class="font-black mb-4 text-center text-xl uppercase tracking-tighter" style="color: #463F3A;">Sesuaikan <span style="color: #FFB700;">Foto</span></h3>
        
        <div class="h-80 w-full bg-white rounded-2xl overflow-hidden border border-[#BCB8B1] shadow-inner mb-6">
            <img id="imageToCrop" class="max-w-full block">
        </div>
        
        <div class="flex gap-4">
            <button type="button" onclick="closeModal()" class="flex-1 py-3 rounded-xl font-black uppercase tracking-widest text-[10px] transition-all bg-white border border-[#BCB8B1] hover:bg-gray-100 shadow-sm" style="color: #8A817C;">Batal</button>
            <button type="button" id="cropButton" class="flex-1 py-3 bg-[#FFB700] hover:bg-[#e6a500] rounded-xl font-black uppercase tracking-widest text-[10px] text-[#463F3A] shadow-md hover:shadow-lg transition-all">Gunakan</button>
        </div>
    </div>
</div>

<script>
let cropper;
const fileInput = document.getElementById('fileInput');
const cropModal = document.getElementById('cropModal');
const imageToCrop = document.getElementById('imageToCrop');

fileInput.onchange = (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            imageToCrop.src = event.target.result;
            cropModal.classList.remove('hidden');
            if (cropper) cropper.destroy();
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                guides: false,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: false
            });
        };
        reader.readAsDataURL(file);
    }
};

document.getElementById('cropButton').onclick = () => {
    const canvas = cropper.getCroppedCanvas({ width: 500, height: 500 });
    document.getElementById('avatarPreview').src = canvas.toDataURL();
    document.getElementById('cropped_image').value = canvas.toDataURL();
    closeModal();
};

function closeModal() {
    cropModal.classList.add('hidden');
    fileInput.value = "";
}
</script>