<?php 

	@ob_start();
	session_start();

	if(!empty($_SESSION['admin'])){
		require 'config.php';
		include $view;
		$lihat = new view($config);
		$toko = $lihat -> toko();
		include 'admin/template/header.php';
		include 'admin/template/sidebar.php';
		if(!empty($_GET['page'])){
			// Batasi akses hanya untuk kasir
			if ($_SESSION['role_id'] == 2) {
				$allowed = ['jual', 'laporan', 'user'];
				if (in_array($_GET['page'], $allowed)) {
					include 'admin/module/'.$_GET['page'].'/index.php';
				} else {
					echo '<div class="alert alert-danger">Akses ditolak untuk kasir.</div>';
				}
			} else {
				// Admin/full akses
				if ($_GET['page'] == 'manajemen_user') {
					include 'admin/module/manajemen_user/index.php';
				} else {
					include 'admin/module/'.$_GET['page'].'/index.php';
				}
			}
		}else{
			include 'admin/template/home.php';
		}
		include 'admin/template/footer.php';
	}else{
		echo '<script>window.location="login.php";</script>';
		exit;
	}
?>

