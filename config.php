<?php


date_default_timezone_set("Asia/Jakarta");
error_reporting(0);

	$host 	= 'localhost'; 
	$user 	= 'root'; 
	$pass 	= ''; 
	$dbname = 'toko'; 
	
	try{
		$config = new PDO("mysql:host=$host;dbname=$dbname;charset=latin1", $user, $pass);
		//echo 'sukses';
	}catch(PDOException $e){
		echo 'KONEKSI GAGAL' .$e -> getMessage();
	}
	
	$view = 'fungsi/view/view.php'; 
?>

