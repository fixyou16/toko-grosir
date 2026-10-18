# Toko Grosir — Sistem Informasi Penjualan

Aplikasi web PHP native dan MySQL untuk membantu operasional toko grosir, mulai dari pengelolaan barang hingga transaksi dan laporan.

> **Demo online:** https://toko-grosir.infinityfree.me/  
> **Halaman login:** https://toko-grosir.infinityfree.me/login.php  
> **Repositori:** https://github.com/fixyou16/toko-grosir

## Fitur aplikasi

Fitur yang dirujuk oleh file aplikasi yang tersedia saat dokumentasi ini dibuat:

- Login dan sesi pengguna dengan peran admin/kasir.
- Pengelolaan data barang dan kategori.
- Alur penjualan serta pencetakan struk.
- Laporan penjualan dan ekspor laporan ke Excel.
- Ekspor laporan kerugian/stok opname.
- Pengaturan informasi toko.

Ketersediaan setiap halaman bergantung pada kelengkapan folder modul aplikasi di server. Lihat catatan kelengkapan repositori di bawah sebelum memasang dari GitHub.

## Teknologi

- PHP native
- MySQL/MariaDB
- PDO untuk koneksi database
- HTML, CSS, JavaScript dan aset template Bootstrap/SB Admin yang digunakan aplikasi
- InfinityFree untuk demo hosting

## Login demo

Buka halaman login: https://toko-grosir.infinityfree.me/login.php

Akun admin demo dibuat secara khusus pada database hosting. **Username/password tidak dicantumkan di README publik** agar akun demo tidak disalahgunakan. Gunakan kredensial yang diberikan langsung oleh pemilik demo. Jika tidak memiliki akses, hubungi pemilik repositori.

Untuk keamanan, jangan memakai akun demo untuk data nyata, jangan membagikan kata sandi, dan ganti kredensial sementara setelah login pertama.

## Persyaratan

- Hosting yang mendukung PHP dan MySQL/MariaDB (InfinityFree dapat digunakan untuk demo).
- Ekstensi PHP PDO MySQL aktif.
- Database yang dibuat di panel hosting.
- Seluruh file aplikasi, termasuk folder template, modul, helper, CSS, JavaScript, dan aset gambar.

## Instalasi di InfinityFree

### 1. Siapkan database

1. Buat database MySQL melalui panel InfinityFree.
2. Buka phpMyAdmin untuk database tersebut.
3. Impor file `toko.sql`.
4. Pastikan tabel aplikasi berhasil dibuat. Jangan mengimpor ulang ke database yang sudah berisi transaksi tanpa membuat cadangan terlebih dahulu.

### 2. Unggah semua file aplikasi

Unggah **seluruh proyek** ke folder `htdocs`, sehingga file utama seperti `index.php` dan `login.php` berada di lokasi yang sesuai dengan struktur aplikasi.

Pastikan folder yang dipanggil oleh kode juga tersedia, termasuk `admin/`, `fungsi/`, `sb-admin/`, `vendor/`, dan `assets/` jika digunakan oleh salinan aplikasi lengkap. Jangan hanya mengunggah file PHP di root.

### 3. Konfigurasikan koneksi database

Edit `config.php) pada server dan sesuaikan:

- Host MySQL yang diberikan InfinityFree.
- Nama database lengkap yang diberikan panel.
- Username database.
- Password database.

Jangan menyalin konfigurasi lokal seperti `localhost`, user `root`, atau password kosong ke hosting tanpa memastikan nilai tersebut benar. Jangan pernah commit password database ke GitHub. Simpan kredensial hanya pada konfigurasi server yang aksesnya dibatasi.

### 4. Siapkan akun admin

Login aplikasi memakai tabel `member`, `login`, dan `role`. Tabel `user` bukan tabel autentikasi yang dipakai oleh `login.php` versi repositori ini.

Pastikan ada role `admin` di tabel `role`, lalu buat pasangan profil `member` dan akun `login` dengan `id_member` dan `role_id` yang cocok. Struktur kolom harus mengikuti database `toko.sql`. Hindari membuat akun dengan SQL yang belum disesuaikan atau memakai password sementara di lingkungan publik.

> **Catatan keamanan:** versi login yang ada memakai MD5 untuk membandingkan password. MD5 tidak aman untuk penyimpanan password modern. Sebelum digunakan untuk operasional nyata, migrasikan autentikasi ke `password_hash()` dan `password_verify()` secara terencana, serta ganti password akun demo.

### 5. Uji aplikasi

- Buka halaman login dan pastikan CSS serta JavaScript termuat.
- Login dengan akun yang telah dibuat.
- Uji menu barang, kategori, penjualan, laporan, ekspor, dan cetak struk.
- Lakukan pengujian dengan data percobaan sebelum memakai data toko sebenarnya.

## Troubleshooting

### Halaman putih (blank page)

1. Uji apakah PHP berjalan dengan file diagnostik sederhana, lalu hapus file diagnostik tersebut setelah selesai.
2. Periksa Error Logs di panel hosting.
3. Pastikan semua file yang dipanggil oleh `require` atau `include` benar-benar ada.
4. Pastikan konfigurasi database benar.
5. Jangan menampilkan detail error PHP kepada pengunjung pada situs publik.

### CSS tidak muncul (Network 404)

1. Buka Developer Tools browser → Network → CSS, lalu muat ulang halaman.
2. Periksa Request URL file CSS yang gagal.
3. Cocokkan URL tersebut dengan lokasi file di `htdocs`; perhatikan folder dan huruf besar/kecil pada nama file.
4. Pastikan seluruh folder template, vendor, CSS, JavaScript, dan aset ikut terunggah.

### Login berhasil tetapi dashboard kosong

Periksa Error Logs, lalu pastikan folder `admin/template/`, `admin/module/`, dan `fungsi/view/` tersedia dan cocok dengan struktur yang dirujuk oleh kode. Tidak perlu mengimpor ulang database hanya untuk memperbaiki tampilan.

## Catatan kelengkapan repositori

Pada saat README ini ditulis, daftar file di branch `main` belum menampilkan seluruh folder yang dirujuk oleh `index.php` dan `login.php` (misalnya `admin/`, `fungsi/`, `sb-admin/`, dan `vendor/`). Karena itu, **clone repositori saja mungkin belum cukup untuk menjalankan aplikasi**. Lengkapi dan commit folder aplikasi yang diperlukan sebelum menjadikan repositori ini paket instalasi mandiri. Jangan mengunggah file konfigurasi yang berisi kredensial database.

## Keamanan dan pemeliharaan

- Jangan menyimpan password, token, atau kredensial database di repositori publik.
- Jika kredensial pernah ter-commit, segera rotasi password/secret; menghapus teks dari commit terbaru tidak menghapusnya dari riwayat Git.
- Buat backup database sebelum perubahan skema atau impor SQL.
- Gunakan akun berbeda untuk admin dan kasir, serta password yang kuat dan unik.
- Terapkan validasi input, prepared statements, otorisasi pada setiap halaman, dan perlindungan CSRF sebelum penggunaan produksi.

## Pengembangan

Kontribusi dan perbaikan dapat diajukan melalui issue atau pull request di GitHub.

## Lisensi

Belum ditentukan. Tambahkan file LICENSE jika proyek akan dibagikan dengan ketentuan lisensi tertentu.
