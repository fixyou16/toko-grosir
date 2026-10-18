<?php


date_default_timezone_set("Asia/Jakarta");
error_reporting(0);

	$host 	= 'sql313.infinityfree.com'; 
	$user 	= 'if0_43206000'; 
	$pass 	= 'Sindanglengo1234'; 
	$dbname = 'if0_43206000_tokogrosir'; 
	
	try{
		$config = new PDO("mysql:host=$host;dbname=$dbname;charset=latin1", $user, $pass);
		//echo 'sukses';
	}catch(PDOException $e){
		echo 'KONEKSI GAGAL' .$e -> getMessage();
	}
	
	$view = 'fungsi/view/view.php'; 
?>

