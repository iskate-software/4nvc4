<?php
session_start() ;
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);

$reqsupplier = $_SESSION['supplier'] ;

$select_INVSOLD = "SELECT INVVPC, INVDESC, SUPPLIER, SUM(INVUNITS) AS INVUNITS FROM INVSOLD JOIN ARINVT ON (INVVPC = VPARTNO) WHERE ARINVT.SUPPLIER ='$reqsupplier' GROUP BY INVVPC ";
$INVSOLD = mysql_query($select_INVSOLD) or die(mysql_error());
$row_INVSOLD = mysql_fetch_assoc($INVSOLD);

// Now go through the list, check against the inventory, and order it if the sold amount >= minimum qty

while ($row_INVSOLD = mysql_fetch_assoc($INVSOLD)) {
 $id = $row_INVSOLD['INVVPC'] ;
 $qty = $row_INVSOLD['INVUNITS'] ;
 
 if ($id <> '       ') {
   $chk_Qty = "SELECT ITEM, ONHAND, ONORDER, ORDERPT, ORDERQTY, ORDERED, MONITOR, SAFETY, PKGQTY, COST, SEQ, DESCRIP, SUPPLIER, VPARTNO FROM ARINVT WHERE VPARTNO = '$id' LIMIT 1" ;
   $query_Qty = mysql_query($chk_Qty, $tryconnection) or die(mysql_error()) ;
   $row_Qty = mysql_fetch_assoc($query_Qty) ;
 
 // is it to be monitored, and if so, is the sold quantity >= orderqty?
 // take into consideration there may already be an outstanding order for this.
 
   $chk_Ordered = "SELECT CODE, VPCCODE, UNITS, RECEIVED FROM INVTHIST WHERE VPCCODE = '$id' AND RECEIVED = 0 " ;
   $query_hx = mysql_query($chk_Ordered, $tryconnection) or die(mysql_error()) ;
   $row_hx = mysql_fetch_assoc($query_hx) ;
   if (!empty($row_hx)) {
     $oldorder = $row_hx['UNITS'] ;
   }
   else {
     $oldorder = 0 ;
   }

   if ($row_Qty['MONITOR'] == 1 && ($qty - $oldorder >= $row_Qty['ORDERQTY']*$row_Qty['PKGQTY']) && $row_Qty['ONHAND'] <= $row_Qty['ORDERPT'] ) {
   
   // Just in case some trigger happy fool has already done this, then a bunch more have been sold, check to see if already on the Current Order list
     $is_Order = "SELECT CODE, VPCCODE, SUM(UNITS) AS UNITS FROM INVENTOR WHERE VPCCODE = '$id' GROUP BY VPCCODE " ;
     $query_Order = mysql_query($is_Order, $tryconnection) or die(mysql_error()) ;
     $row_Order = mysql_fetch_assoc($query_Order) ;
    
     if (!empty($row_Order)) {
     // now to clean up the mess, trash all the existing orders for this product, to be replaced by this new one.
       $exist = $row_Order['UNITS'] ;
       $delete_dups = "DELETE FROM INVENTOR WHERE VPCCODE = '$id' " ;
       $execute_it = mysql_query($delete_dups, $tryconnection) or die(mysql_error()) ;
       }
     else {
       $exist = 0 ;
     }
     $newqty = round(($qty - ($row_Qty['ONHAND'] - $row_Qty['ORDERPT']) + $exist - $oldorder) / $row_Qty['PKGQTY'],0) ;
     
     if ($newqty > 0) {
      $add_Order = "INSERT INTO INVENTOR (UNITS,CODE, DESCRIP, SUPPLIER, VPCCODE, DRUGCOST, LOCN, BACKORDER, ORDERED, RECEIVED) 
                   VALUES ('$newqty', '".mysql_real_escape_string($row_Qty['ITEM'])."', '".mysql_real_escape_string($row_Qty['DESCRIP'])."',  '$row_Qty[SUPPLIER]','$row_Qty[VPARTNO]',
                   '$row_Qty[COST]', '$row_Qty[SEQ]', '0', 'NOW()', '0')" ;
                        
      $execute_it = mysql_query($add_Order, $tryconnection) or die(mysql_error()) ;
   
     // update the inventory "ordered" field
     
      $update_ordered = "UPDATE ARINVT SET ORDERED = ORDERED + ('$newqty' * PKGQTY), LDATE = NOW() WHERE VPARTNO = '$id' LIMIT 1" ;
      $query_ordered = mysql_query($update_ordered, $tryconnection) or die(mysql_error())  ;
     
     // and delete all of this item from the sold list
     
      $clean_up = "DELETE FROM INVSOLD WHERE INVVPC = '$id'" ;
      $execute_clean = mysql_query($clean_up, $tryconnection) or die(mysql_error()) ;
     
     //and see if there is any left over, in which case it has to go back into the sold list. (It may be negative, if the last package on the shelf was half empty..)
     
      $leftover = $qty - round($newqty* $row_Qty['PKGQTY'],0) ;
      if ( $leftover != 0 ) {
        $put_back = "INSERT INTO INVSOLD (INVVPC,INVDESC,INVUNITS) VALUES ('$id', '".mysql_real_escape_string($row_Qty['DESCRIP'])."', '$leftover') ";
        $execute_put = mysql_query($put_back, $tryconnection) or die(mysql_error()) ;
      }  //   $leftover != 0  
     }
    
   } //  $row_Qty['MONITOR'] == 1 &&
 
 } //  $id <> '       '

} //  while $row_INVSOLD = mysql_fetch_assoc($INVSOLD) ;
unset($_SESSION['supplier']) ;
header("Location:../STOCK/ORDER_LIST.php?supplier=$reqsupplier");
?>