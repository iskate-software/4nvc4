<?php
session_start() ;
require_once('../../tryconnection.php');
mysqli_select_db($tryconnection, $database_tryconnection);

$reqsupplier = $_SESSION['supplier'] ;

$select_INVSOLD = "SELECT INVVPC, INVDESC, SUPPLIER, SUM(INVUNITS) AS INVUNITS FROM INVSOLD JOIN ARINVT ON (INVVPC = VPARTNO) WHERE ARINVT.SUPPLIER ='$reqsupplier' GROUP BY INVVPC ";
$INVSOLD = mysqli_query($mysqli_link, $select_INVSOLD) or die(mysqli_error($mysqli_link));
$row_INVSOLD = mysqli_fetch_assoc($INVSOLD);

// Now go through the list, check against the inventory, and order it if the sold amount >= minimum qty

while ($row_INVSOLD = mysqli_fetch_assoc($INVSOLD)) {
 $id = $row_INVSOLD['INVVPC'] ;
 $qty = $row_INVSOLD['INVUNITS'] ;
 
 if ($id <> '       ') {
   $chk_Qty = "SELECT ITEM, ONHAND, ONORDER, ORDERPT, ORDERQTY, ORDERED, MONITOR, SAFETY, PKGQTY, COST, SEQ, DESCRIP, SUPPLIER, VPARTNO FROM ARINVT WHERE VPARTNO = '$id' LIMIT 1" ;
   $query_Qty = mysqli_query($tryconnection, $chk_Qty) or die(mysqli_error($mysqli_link)) ;
   $row_Qty = mysqli_fetch_assoc($query_Qty) ;
 
 // is it to be monitored, and if so, is the sold quantity >= orderqty?
 // take into consideration there may already be an outstanding order for this.
 
   $chk_Ordered = "SELECT CODE, VPCCODE, UNITS, RECEIVED FROM INVTHIST WHERE VPCCODE = '$id' AND RECEIVED = 0 " ;
   $query_hx = mysqli_query($tryconnection, $chk_Ordered) or die(mysqli_error($mysqli_link)) ;
   $row_hx = mysqli_fetch_assoc($query_hx) ;
   if (!empty($row_hx)) {
     $oldorder = $row_hx['UNITS'] ;
   }
   else {
     $oldorder = 0 ;
   }

   if ($row_Qty['MONITOR'] == 1 && ($qty - $oldorder >= $row_Qty['ORDERQTY']*$row_Qty['PKGQTY']) && $row_Qty['ONHAND'] <= $row_Qty['ORDERPT'] ) {
   
   // Just in case some trigger happy fool has already done this, then a bunch more have been sold, check to see if already on the Current Order list
     $is_Order = "SELECT CODE, VPCCODE, SUM(UNITS) AS UNITS FROM INVENTOR WHERE VPCCODE = '$id' GROUP BY VPCCODE " ;
     $query_Order = mysqli_query($tryconnection, $is_Order) or die(mysqli_error($mysqli_link)) ;
     $row_Order = mysqli_fetch_assoc($query_Order) ;
    
     if (!empty($row_Order)) {
     // now to clean up the mess, trash all the existing orders for this product, to be replaced by this new one.
       $exist = $row_Order['UNITS'] ;
       $delete_dups = "DELETE FROM INVENTOR WHERE VPCCODE = '$id' " ;
       $execute_it = mysqli_query($tryconnection, $delete_dups) or die(mysqli_error($mysqli_link)) ;
       }
     else {
       $exist = 0 ;
     }
     $newqty = round(($qty - ($row_Qty['ONHAND'] - $row_Qty['ORDERPT']) + $exist - $oldorder) / $row_Qty['PKGQTY'],0) ;
     
     if ($newqty > 0) {
      $add_Order = "INSERT INTO INVENTOR (UNITS,CODE, DESCRIP, SUPPLIER, VPCCODE, DRUGCOST, LOCN, BACKORDER, ORDERED, RECEIVED) 
                   VALUES ('$newqty', '".mysqli_real_escape_string($mysqli_link, $row_Qty['ITEM'])."', '".mysqli_real_escape_string($mysqli_link, $row_Qty['DESCRIP'])."',  '$row_Qty[SUPPLIER]','$row_Qty[VPARTNO]',
                   '$row_Qty[COST]', '$row_Qty[SEQ]', '0', 'NOW()', '0')" ;
                        
      $execute_it = mysqli_query($tryconnection, $add_Order) or die(mysqli_error($mysqli_link)) ;
   
     // update the inventory "ordered" field
     
      $update_ordered = "UPDATE ARINVT SET ORDERED = ORDERED + ('$newqty' * PKGQTY), LDATE = NOW() WHERE VPARTNO = '$id' LIMIT 1" ;
      $query_ordered = mysqli_query($tryconnection, $update_ordered) or die(mysqli_error($mysqli_link))  ;
     
     // and delete all of this item from the sold list
     
      $clean_up = "DELETE FROM INVSOLD WHERE INVVPC = '$id'" ;
      $execute_clean = mysqli_query($tryconnection, $clean_up) or die(mysqli_error($mysqli_link)) ;
     
     //and see if there is any left over, in which case it has to go back into the sold list. (It may be negative, if the last package on the shelf was half empty..)
     
      $leftover = $qty - round($newqty* $row_Qty['PKGQTY'],0) ;
      if ( $leftover != 0 ) {
        $put_back = "INSERT INTO INVSOLD (INVVPC,INVDESC,INVUNITS) VALUES ('$id', '".mysqli_real_escape_string($mysqli_link, $row_Qty['DESCRIP'])."', '$leftover') ";
        $execute_put = mysqli_query($tryconnection, $put_back) or die(mysqli_error($mysqli_link)) ;
      }  //   $leftover != 0  
     }
    
   } //  $row_Qty['MONITOR'] == 1 &&
 
 } //  $id <> '       '

} //  while $row_INVSOLD = mysql_fetch_assoc($INVSOLD) ;
unset($_SESSION['supplier']) ;
header("Location:../STOCK/ORDER_LIST.php?supplier=$reqsupplier");
?>