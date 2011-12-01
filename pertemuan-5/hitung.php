<?php
 $bilangan1 = $_POST['bilangan1'];
 $bilangan2 = $_POST['bilangan2'];
 
 $jumlah = $bilangan1+$bilangan2;
 $kurang = $bilangan1-$bilangan2;
 $bagi = $bilangan1/$bilangan2;
 $kali = $bilangan1 * $bilangan2;
 
 echo "Hasil Penjumlahan = ".$jumlah;
 echo "<br>";
 echo "Hasil Pengurangan = ".$kurang;
 echo "<br>";
 echo "Hasil Bagi = ".$bagi;
 echo "<br>";
 echo "Hasil kali = ".$kali;
 
 

?>