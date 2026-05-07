<?php
include 'config/database.php';
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$komentarID = $_GET['id'];
$fotoID = $_GET['foto_id'];
$userID = $_SESSION['UserID'];
$role = $_SESSION['Role'];


$query_cek = mysqli_query($conn, "SELECT UserID FROM komentarfoto WHERE KomentarID = '$komentarID'");
$data_komentar = mysqli_fetch_assoc($query_cek);

if ($data_komentar) {
    if ($role == 'admin' || $data_komentar['UserID'] == $userID) {
        $delete = mysqli_query($conn, "DELETE FROM komentarfoto WHERE KomentarID = '$komentarID'");
        
        if ($delete) {
            header("Location: detail.php?id=" . $fotoID);
        } else {
            echo "<script>alert('Gagal menghapus komentar'); window.history.back();</script>";
        }
    } else {
        
        echo "<script>alert('Anda tidak punya akses menghapus komentar ini!'); window.history.back();</script>";
    }
} else {
    header("Location: index.php");
}
?>