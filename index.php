<?php

@ob_start();
session_start();

if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require 'config.php';
include $view;

$lihat = new view($config);
$toko = $lihat->toko();

$roleId = (int) ($_SESSION['role_id'] ?? $_SESSION['admin']['role_id'] ?? 0);
$page = isset($_GET['page']) ? (string) $_GET['page'] : '';

include 'admin/template/header.php';
include 'admin/template/sidebar.php';

if ($page !== '') {
    // Kasir hanya dapat membuka halaman operasional yang diizinkan.
    // Manajemen pengguna bukan fitur operasional kasir.
    $allowedPages = ($roleId === 2)
        ? ['jual', 'laporan']
        : [
            'jual',
            'laporan',
            'barang',
            'kategori',
            'stok',
            'stock_opname',
            'user',
            'manajemen_user',
            'pengaturan',
            'pengguna',
        ];

    // Hanya izinkan nama halaman sederhana; mencegah path traversal lewat ?page=.
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $page) || !in_array($page, $allowedPages, true)) {
        http_response_code(403);
        echo '<div class="container-fluid"><div class="alert alert-danger" role="alert">Halaman ini tidak tersedia untuk akun Anda.</div></div>';
    } else {
        $modulePath = 'admin/module/' . $page . '/index.php';

        // Beberapa proyek lama menggunakan nama folder modul khusus.
        if ($page === 'manajemen_user') {
            $modulePath = 'admin/module/manajemen_user/index.php';
        }

        if (is_file($modulePath)) {
            include $modulePath;
        } else {
            http_response_code(404);
            echo '<div class="container-fluid"><div class="alert alert-warning" role="alert">Halaman yang diminta belum tersedia.</div></div>';
        }
    }
} else {
    // Halaman awal kasir diarahkan ke transaksi, bukan dashboard admin.
    if ($roleId === 2) {
        $cashierHome = 'admin/module/jual/index.php';
        if (is_file($cashierHome)) {
            include $cashierHome;
        } else {
            echo '<div class="container-fluid"><div class="alert alert-warning" role="alert">Modul penjualan belum ditemukan.</div></div>';
        }
    } else {
        include 'admin/template/home.php';
    }
}

include 'admin/template/footer.php';
