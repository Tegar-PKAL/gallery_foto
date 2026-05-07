<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LANDSPIC - Capture the Moment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

        body {
            background: #020617;
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: #FFA200;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .logo-shadow {
            filter: drop-shadow(0 0 12px rgba(59, 130, 246, 0.6));
        }

        @keyframes shimmer {
            0% {
                background-position: -200% -200%;
            }

            100% {
                background-position: 200% 200%;
            }
        }

        .nav-link {
            color: #ffffff;
            transition: all 0.3s;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .nav-link:hover {
            color: #ffffff;
        }
    </style>
</head>

<body>

    <nav class="glass sticky top-0 z-[100] px-6 md:px-10 py-4 mb-2 shadow-2xl">
        <div class="w-full flex items-center justify-between">

            <div class="flex items-center space-x-4">
                <a href="index.php?filter=me" class="flex items-center group" title="Filter Foto Saya">
                    <img src="assets/Logo.png" alt="Logo Snapic" class="w-12 h-12 object-contain hover:scale-105 transition-transform duration-300">
                </a>

                <form action="index.php" method="get" class="hidden md:block w-64 lg:w-80">
                    <div class="relative">
                        <input type="text" name="cari" placeholder="Search visuals..." class="w-full bg-[#B2AFAA] border border-slate-700/50 py-2 px-4 rounded-xl text-xs focus:border-blue-500 outline-none text-slate-900 placeholder-slate-600 transition-all">
                    </div>
                </form>
            </div>

            <div class="flex items-center space-x-3 md:space-x-8">
                <a href="index.php" class="nav-link">Explore</a>

                <?php if (isset($_SESSION['UserID'])):
                    include 'config/database.php';
                    $u_id = $_SESSION['UserID'];
                    $user_header = mysqli_fetch_assoc(mysqli_query($conn, "SELECT FotoProfil, Username FROM user WHERE UserID = '$u_id'"));
                ?>
                    <a href="album.php" class="nav-link">Album</a>

                    <a href="upload.php" class="bg-blue-600 px-4 py-2 rounded-xl hover:bg-blue-500 transition text-white text-[10px] font-black uppercase shadow-lg shadow-blue-900/40">
                        Upload
                    </a>

                    <div class="flex items-center gap-4 pl-5 border-l border-slate-800">
                        <a href="profile.php" class="w-9 h-9 rounded-full border-2 border-slate-700 hover:border-emerald-500 transition-all overflow-hidden bg-slate-800 shadow-xl">
                            <?php if (!empty($user_header['FotoProfil']) && file_exists('assets/profiles/' . $user_header['FotoProfil'])): ?>
                                <img src="assets/profiles/<?= $user_header['FotoProfil'] ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_header['Username']) ?>&background=0D9488&color=fff" class="w-full h-full object-cover">
                            <?php endif; ?>
                        </a>

                        <a href="logout.php" class="text-slate-500 hover:text-red-500 transition-transform hover:scale-110" onclick="return confirm('Logout?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Masuk</a>
                    
                    <a href="register.php" class="border border-[#FFB700]/50 text-[#FFB700] px-4 py-2 rounded-xl hover:bg-[#FFB700] hover:text-black transition text-[10px] font-black uppercase">
                        Daftar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>