<?php
 mysql_connect("localhost","root","");
 mysql_select_db("latihan_web");
 
 $sql= mysql_query("SELECT * FROM mahasiswa");
 while($row = mysql_fetch_array($sql)){
 
 echo "NIM : ".$row['nim'];
 echo "<br>";
 echo "Nama  : ".$row['nama'];
 echo "<br>";
 
 }

?>