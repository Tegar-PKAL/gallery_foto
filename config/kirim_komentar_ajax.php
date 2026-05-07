<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fotoID = $_POST['foto_id'] ?? '';
    $userID = $_SESSION['UserID'] ?? '';
    $isi = mysqli_real_escape_string($conn, $_POST['isi_komentar'] ?? '');
    $tgl = date('Y-m-d');

    if ($fotoID && $userID && !empty(trim($isi))) {
        $query = mysqli_query($conn, "INSERT INTO komentarfoto VALUES (NULL, '$fotoID', '$userID', '$isi', '$tgl')");
        if ($query) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    } else {
        echo json_encode(['status' => 'empty']);
    }
}
?>