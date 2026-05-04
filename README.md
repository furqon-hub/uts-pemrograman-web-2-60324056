# UTS Pemrograman Web 2 - [NIM]

### Identitas Mahasiswa
* **Nama:** Furqon Lanang
* **NIM:** 60324056
* **Kelas:** Pemrograman Web 2

### Deskripsi Aplikasi
Aplikasi Manajemen Kategori Perpustakaan sederhana yang dibangun menggunakan PHP Native dan MySQL. Fitur utama meliputi:
* **Create**: Menambah kategori buku dengan validasi kode (format KAT-).
* **Read**: Menampilkan daftar kategori dalam tabel Bootstrap.
* **Update**: Mengubah data kategori dengan validasi duplikasi.
* **Delete**: Menghapus data dengan konfirmasi JavaScript.

### Struktur Folder
```text
UTS_PEMWEB/
├── config/
│   └── database.php    # Konfigurasi koneksi database
├── index.php           # Halaman utama (Daftar Kategori)
├── create.php          # Form tambah data
├── edit.php            # Form edit data
├── delete.php          # Proses hapus data
└── database_backup.sql # Export database
