<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

$fotoID = $_GET['id'];
$userID = $_SESSION['UserID'];
$tgl    = date('Y-m-d');


$cek = mysqli_query($conn, "SELECT * FROM likefoto WHERE FotoID='$fotoID' AND  UserID='$userID'");

if (mysqli_num_rows($cek) > 0) {
    
    mysqli_query($conn, "DELETE FROM likefoto WHERE FotoID='$fotoID' AND UserID='$userID'");
} else {
    
    mysqli_query($conn, "INSERT INTO likefoto VALUES (NULL, '$fotoID', '$userID', '$tgl')");
}

header("Location: detail.php?id=" . $fotoID);
?>