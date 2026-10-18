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
    // Batasi kasir pada fitur operasional yang diizinkan.
    // Halaman manajemen user dan halaman admin lain tidak tersedia untuk kasir.
    $cashierAllowedPages = ['jual', 'laporan'];

    // Nama halaman hanya boleh berupa identifier sederhana; bukan path file.
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $page)) {
        http_response_code(400);
        echo '<div class="container-fluid"><div class="alert alert-danger" role="alert">Permintaan halaman tidak valid.</div></div>';
    } elseif ($roleId === 2 && !in_array($page, $cashierAllowedPages, true)) {
        http_response_code(403);
        echo '<div class="container-fluid"><div class="alert alert-danger" role="alert">Halaman ini tidak tersedia untuk akun kasir.</div></div>';
    } else {
        // Pertahankan modul yang ada untuk admin, termasuk folder khusus manajemen_user.
        $modulePath = ($page === 'manajemen_user')
            ? 'admin/module/manajemen_user/index.php'
            : 'admin/module/' . $page . '/index.php';

        if (is_file($modulePath)) {
            include $modulePath;
        } else {
            http_response_code(404);
            echo '<div class="container-fluid"><div class="alert alert-warning" role="alert">Halaman yang diminta belum tersedia.</div></div>';
        }
    }
} else {
    // Akun kasir langsung masuk ke halaman penjualan.
    if ($roleId === 2) {
        $cashierHome = 'admin/module/jual/index.php';
        if (is_file($cashierHome)) {
            include $cashierHome;
        } else {
            http_response_code(404);
            echo '<div class="container-fluid"><div class="alert alert-warning" role="alert">Modul penjualan belum ditemukan.</div></div>';
        }
    } else {
        include 'admin/template/home.php';
    }
}

include 'admin/template/footer.php';
