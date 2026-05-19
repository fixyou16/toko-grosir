<?php 
	@ob_start();
	session_start();
	if(!empty($_SESSION['admin'])){ }else{
		echo '<script>window.location="login.php";</script>';
        exit;
	}
	require 'config.php';
	include $view;
	$lihat = new view($config);
	$toko = $lihat -> toko();
	$hsl = $lihat -> penjualan();
?>
<html>
	<head>
		<title>Print Struk Penjualan</title>
		<link rel="stylesheet" href="assets/css/bootstrap.css">
		<style>
			body {
				background: #f8f9fa;
			}
			.print-area {
				background: #fff;
				border-radius: 10px;
				box-shadow: 0 2px 8px rgba(0,0,0,0.08);
				padding: 30px 30px 20px 30px;
				margin-top: 40px;
				margin-bottom: 40px;
			}
			.print-header {
				border-bottom: 2px solid #285c64;
				margin-bottom: 20px;
				padding-bottom: 10px;
			}
			.print-header h3 {
				margin: 0;
				color: #285c64;
				font-weight: bold;
			}
			.print-info {
				font-size: 14px;
				margin-bottom: 10px;
			}
			.table th, .table td {
				font-size: 14px;
				vertical-align: middle !important;
			}
			.summary {
				font-size: 15px;
				margin-top: 20px;
				background: #f1f1f1;
				padding: 10px 15px;
				border-radius: 6px;
			}
			.thankyou {
				margin-top: 25px;
				font-size: 15px;
				font-weight: 500;
				color: #285c64;
			}
			@media print {
				body {
					background: #fff !important;
				}
				.print-area {
					box-shadow: none !important;
					margin: 0;
					padding: 0;
				}
			}
		</style>
	</head>
	<body>
		<script>window.print();</script>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-sm-8 col-md-6">
					<div class="print-area">
						<div class="print-header text-center">
							<h3><?php echo $toko['nama_toko'];?></h3>
							<div><?php echo $toko['alamat_toko'];?></div>
						</div>
						<div class="print-info row">
							<div class="col-6">
								Tanggal : <strong><?php echo date("j F Y, G:i");?></strong>
							</div>
							<div class="col-6 text-right">
								<?php
								// Ambil nama kasir dari id_member jika nm_member tidak ada
								$nama_kasir = '';
								if (!empty($_GET['nm_member'])) {
									$nama_kasir = htmlentities($_GET['nm_member']);
								} elseif (!empty($_GET['id_member'])) {
									$id_member = intval($_GET['id_member']);
									$sql = "SELECT nm_member FROM member WHERE id_member=?";
									$stmt = $config->prepare($sql);
									$stmt->execute([$id_member]);
									$row = $stmt->fetch();
									$nama_kasir = $row ? htmlentities($row['nm_member']) : '';
								}
								?>
								Kasir : <strong><?php echo $nama_kasir;?></strong>
							</div>
						</div>
						<table class="table table-bordered table-striped mt-2">
							<thead class="thead-light">
								<tr>
									<th style="width:5%;">No.</th>
									<th>Barang</th>
									<th style="width:15%;">Jumlah</th>
									<th style="width:20%;">Total</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=1; foreach($hsl as $isi){?>
								<tr>
									<td><?php echo $no;?></td>
									<td><?php echo $isi['nama_barang'];?></td>
									<td><?php echo $isi['jumlah'];?></td>
									<td>Rp.<?php echo number_format($isi['total'],2);?></td>
								</tr>
								<?php $no++; }?>
							</tbody>
						</table>
						<div class="summary">
							<?php $hasil = $lihat -> jumlah(); ?>
							<div>Total : <strong>Rp.<?php echo number_format($hasil['bayar'],2);?></strong></div>
							<div>Bayar : <strong>Rp.<?php echo number_format(floatval($_GET['bayar']),2);?></strong></div>
							<div>Kembali : <strong>Rp.<?php echo number_format(floatval($_GET['kembali']),2);?></strong></div>
						</div>
						<div class="thankyou text-center">
							<p>Terima Kasih Telah Berbelanja di Toko Kami!</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
