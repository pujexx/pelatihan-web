<?php

include 'config/koneksi.php';
$id = $_GET['id'];
$query = "delete from berita where id=$id";
mysql_query($query);
header('location:tampil.php');
?>
