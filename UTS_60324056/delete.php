<?php
require_once 'config/database.php';
// Ambil dan validasi ID kategori
$id_kategori = $_GET['id'] ?? null;
if (!$id_kategori || !is_numeric($id_kategori)) {
    header("Location: index.php?pesan=" . urlencode("ID Kategori tidak valid!"));
    exit;
}
// Pastikan data ada di database
$query_cek = "SELECT id_kategori FROM kategori WHERE id_kategori = ?";
$stmt_cek = $conn->prepare($query_cek);
$stmt_cek->bind_param("i", $id_kategori);
$stmt_cek->execute();
$stmt_cek->store_result();
if ($stmt_cek->num_rows === 0) {
    $stmt_cek->close();
    header("Location: index.php?pesan=" . urlencode("Data kategori tidak ditemukan!"));
    exit;
}
$stmt_cek->close();
// Hapus data kategori
$query_delete = "DELETE FROM kategori WHERE id_kategori = ?";
$stmt_delete = $conn->prepare($query_delete);
$stmt_delete->bind_param("i", $id_kategori);
$stmt_delete->execute();
if ($stmt_delete->affected_rows > 0) {
    $pesan = "Data kategori berhasil dihapus.";
} else {
    $pesan = "Gagal menghapus data kategori.";
}
$stmt_delete->close();
header("Location: index.php?pesan=" . urlencode($pesan));
exit;
?>