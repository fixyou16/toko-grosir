<?php
require_once 'config.php';

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$type = $_GET['type'] ?? 'xls';

$where = '';
$params = [];
if ($tgl_awal && $tgl_akhir) {
    $where = "WHERE so.tanggal BETWEEN ? AND ?";
    $params = [$tgl_awal . " 00:00:00", $tgl_akhir . " 23:59:59"];
}

$sql = "SELECT so.*, b.nama_barang, b.harga_beli 
        FROM stock_opname so 
        LEFT JOIN barang b ON so.id_barang = b.id_barang
        $where
        ORDER BY so.tanggal DESC";
$row = $config->prepare($sql);
$row->execute($params);
$data = $row->fetchAll();

$total_rugi = 0;
$no = 1;
$rows = '';
foreach ($data as $row) {
    $rugi = $row['harga_beli'] * $row['jumlah'];
    $total_rugi += $rugi;
    $rows .= "<tr>
        <td>{$no}</td>
        <td>{$row['id_barang']}</td>
        <td>{$row['nama_barang']}</td>
        <td>{$row['jenis']}</td>
        <td>{$row['jumlah']}</td>
        <td>Rp." . number_format($row['harga_beli']) . ",-</td>
        <td>Rp." . number_format($rugi) . ",-</td>
        <td>{$row['tanggal']}</td>
        <td>{$row['keterangan']}</td>
    </tr>";
    $no++;
}
$rows .= "<tr>
    <td colspan='6' style='text-align:right;'><b>Total Kerugian</b></td>
    <td colspan='3' style='background:#dc3545;color:#fff;'><b>Rp." . number_format($total_rugi) . ",-</b></td>
</tr>";

$judul = "Laporan Kerugian Stock Opname";
if ($tgl_awal && $tgl_akhir) {
    $judul .= " ($tgl_awal s/d $tgl_akhir)";
}

// Export XLS
if ($type == 'xls') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=laporan-kerugian.xls");
    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<h3 style='text-align:center;'>$judul</h3>";
    echo "<table border='1' width='100%' cellpadding='3' cellspacing='4'>
        <thead>
            <tr bgcolor='yellow'>
                <th>No</th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Kerugian</th>
                <th>Jumlah</th>
                <th>Harga Beli</th>
                <th>Total Rugi</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            $rows
        </tbody>
    </table>";
    echo "</body></html>";
    exit;
}

// Export PDF
if ($type == 'pdf') {
    require_once __DIR__ . '/vendor/autoload.php'; // pastikan mPDF sudah diinstall via composer
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
    $html = "<h3 style='text-align:center;'>$judul</h3>
    <table border='1' width='100%' cellpadding='3' cellspacing='4'>
        <thead>
            <tr bgcolor='yellow'>
                <th>No</th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Kerugian</th>
                <th>Jumlah</th>
                <th>Harga Beli</th>
                <th>Total Rugi</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            $rows
        </tbody>
    </table>";
    $mpdf->WriteHTML($html);
    $mpdf->Output('laporan-kerugian.pdf', 'I');
    exit;
}

echo "Tipe export tidak dikenali.";
