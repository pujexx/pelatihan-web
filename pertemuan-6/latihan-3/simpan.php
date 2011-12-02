<?php
include 'config/koneksi.php';
$judul = $_POST['judul'];
$isi = $_POST['isi'];
$date = date("y-m-d");
$query = "INSERT INTO  berita (judul ,isi ,tanggal)VALUES ( '$judul',  '$isi',  '$date')";
mysql_query($query);
header("location:tampil.php");
?>
