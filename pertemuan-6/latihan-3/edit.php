<?php

include 'config/koneksi.php';
$id = $_POST['id'];
$judul = $_POST['judul'];
$isi = $_POST['isi'];
$date = date('y-m-d');
$query = "update berita set judul='$judul',isi ='$isi',tanggal ='$date' where id = $id";
mysql_query($query);
header("location:tampil.php");
?>
