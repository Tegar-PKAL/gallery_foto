<?php
session_start();
include 'database.php';

// Pastikan yang mengakses adalah admin
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='../index.php';</script>";
    exit;
}

if (isset($_GET['id'])) {
    $albumID = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Ambil semua foto terkait album ini untuk dihapus file fisiknya
    $query_foto = mysqli_query($conn, "SELECT FotoID, LokasiFile FROM foto WHERE AlbumID = '$albumID'");
    
    while ($foto = mysqli_fetch_array($query_foto)) {
        $fotoID = $foto['FotoID'];
        $path_file = "../assets/uploads/" . $foto['LokasiFile'];
        
        // Hapus file fisik dari folder assets/uploads
        if (file_exists($path_file) && !empty($foto['LokasiFile'])) {
            unlink($path_file);
        }

        // Hapus Like dan Komentar terkait foto ini (mencegah error relasi database)
        mysqli_query($conn, "DELETE FROM likefoto WHERE FotoID = '$fotoID'");
        mysqli_query($conn, "DELETE FROM komentarfoto WHERE FotoID = '$fotoID'");
    }

    // 2. Hapus seluruh data foto dari tabel database
    mysqli_query($conn, "DELETE FROM foto WHERE AlbumID = '$albumID'");

    // 3. Terakhir, hapus albumnya
    $hapus_album = mysqli_query($conn, "DELETE FROM album WHERE AlbumID = '$albumID'");

    if ($hapus_album) {
        echo "<script>alert('Album beserta seluruh isinya berhasil dihapus!'); window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus album!'); window.history.back();</script>";
    }
} else {
    header("Location: ../index.php");
}
?>