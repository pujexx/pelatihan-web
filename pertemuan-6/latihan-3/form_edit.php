<?php
include 'config/koneksi.php';
$id = $_GET['id'];
$query ="select * from berita where id=$id";
$result = mysql_query($query);
$row = mysql_fetch_array($result);

?>
<table border="0" cellspacing="2">

    <form action="edit.php" method="post">
        <input type="hidden" name="id" value="<?php echo $row['id'];?>">
        <tr>
            <td>Judul </td><td><input type="text" name="judul" value="<?php echo $row['judul'];?>"/></td>
        </tr>
        <tr>
            <td>Content</td><td><textarea name="isi" ><?php echo $row['isi'];?></textarea></td>
        </tr>
        <tr>
            <td></td><td><input type="submit" value="Ubah" /></td>
        </tr>
    </form>
</table>
