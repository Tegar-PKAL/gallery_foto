<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$type = $_GET['type'] ?? 'foto'; 
$userID = $_SESSION['UserID'];
$role = $_SESSION['Role'];

if ($type === 'album') {
    
    $cekAlbum = mysqli_query($conn, "SELECT * FROM album WHERE AlbumID = '$id' AND UserID = '$userID'");
    
    if (mysqli_num_rows($cekAlbum) > 0 || $role === 'admin') {
        
        mysqli_query($conn, "UPDATE foto SET AlbumID = NULL WHERE AlbumID = '$id'");
        
        mysqli_query($conn, "DELETE FROM album WHERE AlbumID = '$id'");
        echo "<script>alert('Album berhasil dihapus!'); window.location='album.php';</script>";
    } else {
        echo "<script>alert('Akses Ditolak!'); window.location='album.php';</script>";
    }

} else {
    
    $queryCek = mysqli_query($conn, "SELECT * FROM foto WHERE FotoID = '$id'");
    $dataFoto = mysqli_fetch_array($queryCek);

    if ($dataFoto) {
        if ($role === 'admin' || $dataFoto['UserID'] == $userID) {
            
            $path = "assets/uploads/" . $dataFoto['LokasiFile'];
            if (file_exists($path)) { unlink($path); }

            
            $delete = mysqli_query($conn, "DELETE FROM foto WHERE FotoID = '$id'");
            if ($delete) {
                echo "<script>alert('Foto dihapus!'); window.location='index.php';</script>";
            }
        } else {
            echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Foto tidak ditemukan!'); window.location='index.php';</script>";
    }
}
?>