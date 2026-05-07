<?php
session_start();
include 'database.php';

$fotoID = $_GET['id'] ?? '';
$userID = $_SESSION['UserID'] ?? null;
$role   = $_SESSION['Role'] ?? 'user';

if (!$fotoID) exit;

$komentar = mysqli_query($conn, "SELECT komentarfoto.*, user.Username, user.FotoProfil, user.UserID 
    FROM komentarfoto 
    JOIN user ON komentarfoto.UserID = user.UserID 
    WHERE FotoID = '$fotoID' 
    ORDER BY KomentarID DESC");

if (mysqli_num_rows($komentar) == 0) {
    echo '<div class="h-full flex flex-col items-center justify-center opacity-50 py-10">
            <span class="text-4xl mb-2 block">💬</span>
            <p class="italic font-bold text-sm" style="color: #8A817C;">Belum ada komentar. Jadilah yang pertama!</p>
          </div>';
} else {
    while ($k = mysqli_fetch_array($komentar)) {
        $fotoProfil = !empty($k['FotoProfil']) ? $k['FotoProfil'] : 'default.png';
        $isOwner = ($role === 'admin' || $k['UserID'] == $userID);
        
        echo '<div class="bg-[#F4F3EE] p-4 rounded-2xl border border-[#BCB8B1]/50 flex justify-between items-start group hover:border-[#BCB8B1] transition-all mb-4">
                <div class="flex space-x-4 items-center">
                    <a href="user_space.php?UserID='.$k['UserID'].'" class="flex-shrink-0 relative">
                        <img src="assets/profiles/'.$fotoProfil.'" class="w-10 h-10 rounded-full object-cover border-2 shadow-sm hover:scale-110 transition-all" style="border-color: #BCB8B1;">
                    </a>
                    <div class="flex flex-col">
                        <a href="user_space.php?UserID='.$k['UserID'].'" class="text-[11px] font-extrabold hover:underline tracking-wide uppercase transition-colors" style="color: #8A817C;">@'.htmlspecialchars($k['Username']).'</a>
                        <p class="text-sm mt-0.5 font-medium leading-tight whitespace-pre-wrap" style="color: #463F3A;">'.htmlspecialchars($k['IsiKomentar']).'</p>
                    </div>
                </div>';
        
        // Tombol Hapus Tetap Aman dan Hanya Muncul Sesuai Hak Akses
        if ($isOwner) {
            echo '<a href="hapus_komentar.php?id='.$k['KomentarID'].'&foto_id='.$fotoID.'" onclick="return confirm(\'Hapus komentar ini?\')" class="text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all p-2 opacity-0 group-hover:opacity-100" title="Hapus Komentar">🗑️</a>';
        }
        echo '</div>';
    }
}
?>