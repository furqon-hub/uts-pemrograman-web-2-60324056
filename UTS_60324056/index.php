<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    // Query data kategori (order by id_kategori DESC)
    $query = "SELECT * FROM kategori ORDER BY id_kategori DESC";
    $stmt = $conn->prepare($query);
    
    // Cek hasil query
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        die("Query error: " . $conn->error);
    }
    ?>
    
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Kategori Buku</h2>
            <a href="create.php" class="btn btn-primary">Tambah Kategori</a>
        </div>
        
        <!-- Tampilkan pesan sukses/error jika ada -->
        <?php if (isset($_GET['pesan'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['pesan']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th width="100">Kode</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th width="100">Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop data dan tampilkan ke tabel
                        if ($result && $result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                
                                // Status ditampilkan dengan badge
                                if (strtolower($row['status']) == 'aktif') {
                                    $badgeClass = 'bg-success';
                                } else {
                                    $badgeClass = 'bg-danger';
                                }

                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['kode_kategori'] ?? $row['kode']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                                    <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                                    <td>
                                        <span class="badge <?= $badgeClass; ?>">
                                            <?= htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Tombol aksi edit dan hapus -->
                                        <a href="edit.php?id=<?= $row['id_kategori']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <button type="button" onclick="confirmDelete(<?= $row['id_kategori']; ?>)" class="btn btn-danger btn-sm">Hapus</button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Tidak ada data kategori ditemukan.</td></tr>";
                        }
                        
                        // Tutup statement setelah selesai
                        if ($stmt) {
                            $stmt->close();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(id) {
        if (confirm('Yakin ingin menghapus kategori ini?')) {
            window.location.href = 'delete.php?id=' + id;
        }
    }
    </script>
</body>
</html>