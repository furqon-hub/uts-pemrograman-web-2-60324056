<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    $errors = [];
    
    // Ambil ID kategori dari parameter GET
    $id_kategori = $_GET['id'] ?? null;
    
    if (!$id_kategori) {
        header("Location: index.php?pesan=" . urlencode("ID Kategori tidak valid!"));
        exit;
    }

    // Ambil data kategori dari database berdasarkan ID
    $query_get = "SELECT * FROM kategori WHERE id_kategori = ?";
    $stmt_get = $conn->prepare($query_get);
    $stmt_get->bind_param("i", $id_kategori);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    
    // Jika ID tidak ditemukan di database, redirect
    if ($result->num_rows === 0) {
        header("Location: index.php?pesan=" . urlencode("Data kategori tidak ditemukan!"));
        exit;
    }

    // Isi nilai awal form dari database
    $row = $result->fetch_assoc();
    $kode = $row['kode_kategori'] ?? $row['kode']; // Sesuaikan dengan nama kolom DB
    $nama = $row['nama_kategori'];
    $deskripsi = $row['deskripsi'];
    $status = $row['status'];
    $stmt_get->close();

    // Proses update jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Ambil dan sanitasi data dari form
        $kode_post      = htmlspecialchars(trim($_POST['kode'] ?? ''));
        $nama_post      = htmlspecialchars(trim($_POST['nama'] ?? ''));
        $deskripsi_post = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
        $status_post    = htmlspecialchars(trim($_POST['status'] ?? 'Aktif'));

        // Timpa nilai form dengan inputan baru dari user agar tetap tampil jika error
        $kode = $kode_post;
        $nama = $nama_post;
        $deskripsi = $deskripsi_post;
        $status = $status_post;
        
        // Validasi kode kategori
        if (empty($kode_post)) {
            $errors[] = "Kode Kategori wajib diisi.";
        } elseif (strlen($kode_post) < 4 || strlen($kode_post) > 10) {
            $errors[] = "Panjang Kode Kategori harus antara 4 hingga 10 karakter.";
        } elseif (substr($kode_post, 0, 4) !== 'KAT-') {
            $errors[] = "Kode Kategori harus diawali dengan format 'KAT-'.";
        } else {
            // Cek duplikasi kode, kecuali untuk data yang sedang diedit
            $query_cek = "SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?";
            $stmt_cek = $conn->prepare($query_cek);
            $stmt_cek->bind_param("si", $kode_post, $id_kategori);
            $stmt_cek->execute();
            $stmt_cek->store_result();
            
            if ($stmt_cek->num_rows > 0) {
                $errors[] = "Kode Kategori '$kode_post' sudah dipakai oleh kategori lain.";
            }
            $stmt_cek->close();
        }
        
        // Validasi nama kategori
        if (empty($nama_post)) {
            $errors[] = "Nama Kategori wajib diisi.";
        } elseif (strlen($nama_post) < 3 || strlen($nama_post) > 50) {
            $errors[] = "Nama Kategori harus antara 3 hingga 50 karakter.";
        }
        
        // Validasi deskripsi
        if (!empty($deskripsi_post) && strlen($deskripsi_post) > 200) {
            $errors[] = "Deskripsi maksimal 200 karakter.";
        }
        
        // Validasi status
        if (!in_array($status_post, ['Aktif', 'Nonaktif'])) {
            $errors[] = "Status harus 'Aktif' atau 'Nonaktif'.";
        }
        
        // Proses update ke database jika tidak ada error
        if (empty($errors)) {
            $query_update = "UPDATE kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ? WHERE id_kategori = ?";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bind_param("ssssi", $kode_post, $nama_post, $deskripsi_post, $status_post, $id_kategori);
            
            if ($stmt_update->execute()) {
                // Redirect jika update berhasil
                header("Location: index.php?pesan=" . urlencode("Data kategori berhasil diperbarui!"));
                exit;
            } else {
                $errors[] = "Gagal memperbarui data: " . $conn->error;
            }
            $stmt_update->close();
        }
    }
    ?>
    
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0">Edit Kategori</h4>
                    </div>
                    <div class="card-body">
                        <!-- Tampilkan error validasi jika ada -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Form edit kategori dengan data pre-filled -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode" name="kode" value="<?= htmlspecialchars($kode) ?>" required>
                                <div class="form-text">Wajib diawali 'KAT-' (4-10 karakter).</div>
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
                                <div class="form-text">Panjang 3-50 karakter.</div>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($deskripsi) ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusAktif" value="Aktif" <?= ($status == 'Aktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="statusAktif">Aktif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusNonaktif" value="Nonaktif" <?= ($status == 'Nonaktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="statusNonaktif">Nonaktif</label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">Update</button>
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>