<?php 
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);

$get_SUMM = "SELECT ITEM,SUPPLIER,VPARTNO FROM DVManager.ARINVT WHERE SUPPLIER = 'SUMMIT' ORDER BY ITEM" ;
$query_SUMM = mysqli_query($tryconnection, $get_SUMM) or die(mysqli_error($mysqli_link)) ;
$row_SUMM = mysqli_fetch_assoc($query_SUMM) ;

$sum = 'SUMM' ;
$start = 1001 ;
echo $row_SUMM['ITEM'] . ' 14 ' ;
echo $sum.$start ;
echo substr(strval($start),1,3) ;

reset($row_SUMM) ;

while ($row_SUMM = mysqli_fetch_assoc($query_SUMM) ) {
echo '  got into loop ' ;
  $item = $row_SUMM['ITEM'] ;
  $newit = substr(strval($start),1,3) ;
  echo ' next ' . $item ;
  $newvpn = $sum.$newit ;
  echo $newvpn .  ' new ' . $row_SUMM['ITEM'] . ' ' . $row_SUMM['VPARTNO']  ;
  $update_vpn = "UPDATE DVManager.ARINVT SET VPARTNO = '$newvpn' WHERE ITEM = '$item' limit 1 " ;
  $query = mysqli_query($tryconnection, $update_vpn) or die(mysqli_error($mysqli_link)) ;
  $start++ ;
}
?>