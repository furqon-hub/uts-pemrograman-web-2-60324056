<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    $errors = [];
    $kode = '';
    $nama = '';
    $deskripsi = '';
    $status = 'Aktif';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Ambil dan sanitasi data dari form
        $kode      = htmlspecialchars(trim($_POST['kode'] ?? ''));
        $nama      = htmlspecialchars(trim($_POST['nama'] ?? ''));
        $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
        $status    = htmlspecialchars(trim($_POST['status'] ?? 'Aktif'));
        // Validasi kode kategori
        if (empty($kode)) {
            $errors[] = "Kode Kategori wajib diisi.";
        } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
            $errors[] = "Panjang Kode Kategori harus antara 4 hingga 10 karakter.";
        } elseif (substr($kode, 0, 4) !== 'KAT-') {
            $errors[] = "Kode Kategori harus diawali dengan format 'KAT-'.";
        } else {
            // Cek duplikasi kode ke database
            $query_cek = "SELECT id_kategori FROM kategori WHERE kode_kategori = ?";
            $stmt_cek = $conn->prepare($query_cek);
            $stmt_cek->bind_param("s", $kode);
            $stmt_cek->execute();
            $stmt_cek->store_result();
            if ($stmt_cek->num_rows > 0) {
                $errors[] = "Kode Kategori '$kode' sudah terdaftar. Silakan gunakan kode lain.";
            }
            $stmt_cek->close();
        }
        // Validasi nama kategori
        if (empty($nama)) {
            $errors[] = "Nama Kategori wajib diisi.";
        } elseif (strlen($nama) < 3 || strlen($nama) > 50) {
            $errors[] = "Nama Kategori harus antara 3 hingga 50 karakter.";
        }
        // Validasi deskripsi
        if (!empty($deskripsi) && strlen($deskripsi) > 200) {
            $errors[] = "Deskripsi maksimal 200 karakter.";
        }
        // Validasi status
        if (!in_array($status, ['Aktif', 'Nonaktif'])) {
            $errors[] = "Status harus 'Aktif' atau 'Nonaktif'.";
        }
        
        // Insert data jika tidak ada error
        if (empty($errors)) {
            $query_insert = "INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, status) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($query_insert);
            $stmt_insert->bind_param("ssss", $kode, $nama, $deskripsi, $status);
            if ($stmt_insert->execute()) {
                header("Location: index.php?pesan=" . urlencode("Data kategori berhasil ditambahkan!"));
                exit;
            } else {
                $errors[] = "Gagal menyimpan data ke database: " . $conn->error;
            }
            $stmt_insert->close();
        }
    }
    ?>
    
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Tambah Kategori Baru</h4>
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
                        
                        <!-- Form tambah kategori -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode" name="kode" value="<?= $kode ?>" required placeholder="Contoh: KAT-001">
                                <div class="form-text">Wajib diawali 'KAT-' (4-10 karakter).</div>
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= $nama ?>" required placeholder="Masukkan nama kategori">
                                <div class="form-text">Panjang 3-50 karakter.</div>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Opsional (maks 200 karakter)"><?= $deskripsi ?></textarea>
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
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
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