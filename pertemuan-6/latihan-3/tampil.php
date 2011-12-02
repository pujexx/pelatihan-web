<a href="form_tambah.php" >Tambah</a>


<?php
include 'config/koneksi.php';
$query = "select * from berita";
$result= mysql_query($query);
echo "<table border='1'>";
echo "<tr>";
echo "<td>Judul</td><td>Isi</td><td>Tanggal</td><td>Aksi</td>";
echo "</tr>";

while ($row = mysql_fetch_array($result)){
    echo "<tr>";
    echo "<td>".$row['judul']."</td><td>".$row['isi']."</td><td>".$row['tanggal']."</td>";
    echo "<td>";
    echo "<a href='hapus.php?id=".$row['id']."' >hapus</a>";
    echo "|";
    echo "<a href='form_edit.php?id=".$row['id']."'>edit</a>";
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
?>
