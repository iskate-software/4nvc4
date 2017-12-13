<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);

$get_SUMM = "SELECT ITEM,SUPPLIER,VPARTNO FROM DVManager.ARINVT WHERE SUPPLIER = 'SUMMIT' ORDER BY ITEM" ;
$query_SUMM = mysql_query($get_SUMM, $tryconnection) or die(mysql_error()) ;
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
  $query = mysql_query($update_vpn, $tryconnection) or die(mysql_error()) ;
  $start++ ;
}
?>