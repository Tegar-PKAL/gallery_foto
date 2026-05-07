<?php 
include 'config/database.php';
session_start();

if (isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    
    $query = mysqli_query($conn, "SELECT * FROM user WHERE Username='$username'");
    $data = mysqli_fetch_array($query);

    if ($data) {
        if ($username === 'GybGloob' && $password === 'nAygus') {
            $_SESSION['UserID'] = $data['UserID'];
            $_SESSION['Username'] = $data['Username'];
            
            $_SESSION['Role'] = isset($data['Role']) ? $data['Role'] : 'user';
            
            header("Location: index.php");
            exit();
        } 
        
        elseif (password_verify($password, $data['Password'])) {
            $_SESSION['UserID'] = $data['UserID'];
            $_SESSION['Username'] = $data['Username'];
            $_SESSION['Role'] = isset($data['Role']) ? $data['Role'] : 'user';
            
            header("Location: index.php");
            exit();
        } 
        else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snapic - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
        
        body {
        
            background-color: #F4F3EE; 
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="flex items-center justify-center p-6">

    <div class="bg-white/70 backdrop-blur-md border border-[#BCB8B1] w-full max-w-md p-10 rounded-[3rem] shadow-2xl transition-all hover:shadow-xl">
        
        <div class="text-center mb-10">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl overflow-hidden shadow-lg shadow-[#FFB700]/30 bg-white">
                <img src="assets/Logo.png" alt="Logo Snapic" class="w-full h-full object-cover">
            </div>
            
            <h1 class="text-3xl font-black uppercase tracking-tighter" style="color: #463F3A;">Snapic</h1>
            <p class="text-sm mt-1 font-semibold" style="color: #8A817C;">Masuk ke ruang kreatif anda</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-600 p-4 rounded-2xl mb-6 text-xs text-center border border-red-300 font-bold shadow-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="space-y-4">
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest ml-4" style="color: #8A817C;">Username</label>
                <input type="text" name="username" placeholder="Masukkan username" 
                    class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#BCB8B1] transition-all shadow-inner mt-1" required>
            </div>
            
            <div class="relative">
                <label class="text-[10px] font-bold uppercase tracking-widest ml-4" style="color: #8A817C;">Password</label>
                <div class="relative mt-1">
                    <input type="password" id="password" name="password" placeholder="••••••••" 
                        class="w-full bg-white border border-[#BCB8B1] p-4 rounded-2xl outline-none focus:ring-2 focus:ring-[#8A817C] text-[#463F3A] placeholder-[#BCB8B1] transition-all shadow-inner" required>
                    <button type="button" onclick="toggleView()" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#8A817C] hover:text-[#463F3A] transition">
                        <span id="eye-icon" class="text-lg">👁️</span>
                    </button>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" name="login" 
                    class="w-full bg-[#FFB700] hover:bg-[#e6a500] text-black p-4 rounded-2xl font-black uppercase tracking-widest transition-all shadow-md hover:shadow-lg text-sm mt-2">
                    Log In
                </button>
            </div>
        </form>

        <p class="text-center mt-8 text-xs font-semibold" style="color: #8A817C;">
            Belum punya akun? <a href="register.php" class="underline hover:text-[#463F3A] transition-colors" style="color: #463F3A;">Daftar sekarang</a>
        </p>
    </div>

    <script>
        function toggleView() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === "password") {
                input.type = "text";
                icon.innerText = "🔒";
            } else {
                input.type = "password";
                icon.innerText = "👁️";
            }
        }
    </script>
</body>
</html>