<?php 
include 'includes/header.php';
include 'config/database.php'; 
?>

<style>
    /* Menyamakan warna background seluruh halaman agar menyatu dengan footer */
    body {
        background-color: #F4F3EE !important; 
    }
</style>

<div class="w-full min-h-[80vh] flex justify-center items-center py-12 bg-[#F4F3EE]">
    
    <div class="bg-white/70 backdrop-blur-md border border-[#BCB8B1] shadow-2xl p-8 rounded-3xl w-full max-w-md transition-all hover:shadow-xl">
        <h2 class="text-3xl font-black mb-6 text-center" style="color: #463F3A;">Buat Akun</h2>

        <?php
        if (isset($_POST['register'])) {
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $email = $_POST['email'];
            $nama = $_POST['nama_lengkap'];
            $alamat = $_POST['alamat'];

            $insert = mysqli_query($conn, "INSERT INTO user (Username, Password, Email, NamaLengkap, Alamat) 
          VALUES ('$username', '$password', '$email', '$nama', '$alamat')");
            if ($insert) {
                // Notifikasi sukses juga disesuaikan agar lebih elegan
                echo "<div class='bg-emerald-100 border border-emerald-400 text-emerald-800 p-3 rounded-xl mb-6 text-center text-sm font-semibold shadow-sm'>Registrasi Berhasil! <a href='login.php' class='underline font-bold hover:text-emerald-600'>Login sekarang</a></div>";
            }
        }
        ?>

        <form action="" method="post" class="space-y-4">
            <input type="text" name="username" placeholder="Username" class="w-full bg-white border border-[#BCB8B1] p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#8A817C] transition-all shadow-inner" required>
            
            <input type="password" name="password" placeholder="Password" class="w-full bg-white border border-[#BCB8B1] p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#8A817C] transition-all shadow-inner" required>
            
            <input type="email" name="email" placeholder="Email" class="w-full bg-white border border-[#BCB8B1] p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#8A817C] transition-all shadow-inner" required>
            
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" class="w-full bg-white border border-[#BCB8B1] p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#8A817C] transition-all shadow-inner" required>
            
            <textarea name="alamat" placeholder="Alamat Lengkap" class="w-full bg-white border border-[#BCB8B1] p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#8A817C] transition-all shadow-inner h-24 resize-none"></textarea>
            
            <button type="submit" name="register" class="w-full bg-[#FFB700] hover:bg-[#e6a500] text-black p-3 rounded-xl font-black uppercase tracking-widest transition-all shadow-md hover:shadow-lg mt-2">
                Daftar
            </button>
        </form>
        
        <p class="text-center text-xs mt-6 font-semibold" style="color: #8A817C;">
            Sudah punya akun? <a href="login.php" class="underline hover:text-[#463F3A] transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>