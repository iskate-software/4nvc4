 <?php 
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);
$reqsupplier = $_GET['supplier'] ;
$ALL_RECEIVED = "SELECT DISTINCT ORDERED FROM INVTHIST WHERE RECEIVED <> 1 AND SUPPLIER = '$reqsupplier' ORDER BY ORDERED ASC" ;
$query_RECVD = mysqli_query($tryconnection, $ALL_RECEIVED) or die(mysqli_error($mysqli_link)) ;
$row_RECVD = mysqli_fetch_assoc($query_RECVD) ;

$count = "SELECT COUNT(CODE) AS TOTAL_ROWS FROM INVTHIST WHERE RECEIVED = 0 AND SUPPLIER = '$reqsupplier' AND ORDERED = '$row_RECVD[ORDERED]'" ;
$query_count = mysqli_query($tryconnection, $count) or die(mysqli_error($mysqli_link)) ;
$row_count = mysqli_fetch_assoc($query_count) ;
$num_rows = $row_count['TOTAL_ROWS'] ;

if (isset($_POST['save'])){

 if (!empty($_POST['filedate'])){
   $filedate=$_POST['filedate'];
 }
 else {
 $filedate='00/00/0000';
 }
 
echo ' filedate is ' . $filedate ;
 $indate = $_POST['ordered'] ;

 $filedate="SELECT STR_TO_DATE('$filedate','%m/%d/%Y') AS FILEDATE";
 $filedate=mysqli_query($tryconnection, $filedate) or die(mysqli_error($mysqli_link));

 $row_fd = mysqli_fetch_assoc($filedate);

 $hits = "SELECT SUPPLIER,VPCCODE,UNITS,ORDERED,BACKORDER,RECEIVED FROM INVTHIST WHERE ORDERED = '$indate' AND SUPPLIER = '$reqsupplier' AND BACKORDER <> 1 
        AND RECTDATE = '0000-00-00' " ;
 $query_hits = mysqli_query($tryconnection, $hits) or die(mysqli_error($mysqli_link)) ;
 
 // now loop through and flag the new records as being received, and update the inventory as well
 
 while ($row_hits = mysqli_fetch_assoc($query_hits)) {
   $query_INVENTOR = "UPDATE INVTHIST SET RECEIVED = 1, RECTDATE = '$row_fd[FILEDATE]' 
       WHERE ORDERED = '$indate' AND BACKORDER !='1' AND VPCCODE = '$row_hits[VPCCODE]' AND SUPPLIER = '$reqsupplier' LIMIT 1 ";
   $INVENTOR = mysqli_query($tryconnection, $query_INVENTOR) or die(mysqli_error($mysqli_link));
   
   $query_stockupdate  = "UPDATE ARINVT SET ONHAND = ONHAND + ('$row_hits[UNITS]' * PKGQTY), ORDERED = ORDERED - ('$row_hits[UNITS]' * PKGQTY) 
       WHERE VPARTNO = '$row_hits[VPCCODE]' LIMIT 1" ;
   $do_stock = mysqli_query($tryconnection, $query_stockupdate) or die(mysqli_error($mysqli_link)) ;
   
 } //while ($row_hits = mysql_fetch_assoc($query_hits))
 
 
 $winclose = "self.close();";
}


$select_INVENTOR = "SELECT * FROM INVTHIST WHERE RECEIVED = 0 AND SUPPLIER = '$reqsupplier'  ORDER BY ORDERED, `DESCRIP` ASC";
$INVENTOR = mysqli_query($tryconnection, $select_INVENTOR) or die(mysqli_error($mysqli_link));
//$row_INVENTOR = mysql_fetch_array($INVENTOR);

$cogs = 0 ;
 while ($row_INVENTOR = mysqli_fetch_assoc($INVENTOR)) {
   if ($row_INVENTOR['BACKORDER'] != 1) {
    $cogs = $cogs + ($row_INVENTOR['DRUGCOST']*$row_INVENTOR['UNITS']);
   }
  $backorder = $backorder + $row_INVENTOR['BACKORDER'];
} // while ($row_INVENTOR = mysql_fetch_assoc($INVENTOR))

// repeat the query because of the num_rows. Use the group this time.

$select_INVENTOR = "SELECT * FROM INVTHIST WHERE RECEIVED = 0 AND SUPPLIER = '$reqsupplier' GROUP BY `DESCRIP` ORDER BY ORDERED, `DESCRIP` ASC";
$INVENTOR = mysqli_query($tryconnection, $select_INVENTOR) or die(mysqli_error($mysqli_link));
$row_INVENTOR = mysqli_fetch_assoc($INVENTOR);
$totalRows_INVENTOR = mysqli_num_rows($INVENTOR);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>FILE RECEIVED ORDER LIST</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
<?php echo $winclose; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+190,toppos+100);
}

function bodyonunload(){
opener.document.location.reload();
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>

<form method="post" action="" name="fileol" id="fileol" style="position:absolute; top:0px; left:0px;">
<input type="hidden" name="vpccode" value="<?php echo $row_INVENTOR['VPCCODE']; ?>"  />
<input type="hidden" name="desc" value="<?php echo $row_INVENTOR['DESCRIP']; ?>"  />
    <table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td width="129" height="20" align="left" class="Verdana12B"></td>
        <td width="161" align="left" class="Verdana12"></td>
        <td width="100" align="center" class="Verdana12B"></td>
        <td width="35" align="center" class="Verdana12B"></td>
      </tr>
      <tr>
        <td height="55" colspan="4" align="center" class="Verdana12Blue">
        ADDING CURRENT ORDER LIST TO HISTORY AND <br />
		DELETING NON-BACK ORDERED ITEMS for&nbsp;<?php echo $reqsupplier ;?></td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left" class="Verdana12B">&nbsp;&nbsp;Number of inventory items ordered:</td>
        <td height="30" align="right" class="Verdana12B"><?php echo $num_rows; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left" class="Verdana12B">&nbsp;&nbsp;Value of inventory ordered:</td>
        <td align="right" class="Verdana12B"><?php echo number_format($cogs,2); ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left" class="Verdana12B">&nbsp;&nbsp;Number of Back-orders:</td>
        <td align="right" class="Verdana12B"><?php echo $backorder; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left" valign="bottom" class="Verdana12B">&nbsp;&nbsp;Date order was received:</td>
        <td align="right" valign="bottom" class="Verdana12B"><span class="Labels">
          <input name="filedate" type="text" class="Input" id="filedate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php $date = date_create(date('m/d/Y')); //date_sub($date, date_interval_create_from_date_string('1 days')); 
          echo date_format($date, 'm/d/Y'); ?>" size="10" onclick="ds_sh(this);"/>
        </span></td>
        <td align="center" valign="bottom" class="Verdana12B">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="center" valign="top" class="Verdana12B">&nbsp;</td>
        <td align="center" valign="top" class="Verdana12B">&nbsp;</td>
        <td align="center" valign="top" class="Verdana12B">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="4" align="center" class="Verdana12Red"><label class="hidden">
          <input type="checkbox" name="checkbox" id="checkbox" />
        Use current month only</label>
          Back-ordered items must be deleted manually<br /> 
          from the current list when they ship.</td>
      </tr>
      <tr>
    <td height="30" colspan="3" align="center" valign="middle" class="Verdana12Blue">
    <strong>Please select shipment date:</strong>
    <br  />
    <span class="Verdana11Grey">Doubleclick or click &amp; save.</span>
    </td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="3" width="350">
    <select name="ordered" size="12" id="ordered" class='SelectList' ">&nbsp;&nbsp;
      <?php
do {  
?>
      <option value="<?php echo $row_RECVD['ORDERED']?>" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_RECVD['ORDERED']?></option>
      <?php
} while ($row_RECVD = mysqli_fetch_assoc($query_RECVD));
?>
    </select></td>
    <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="4" align="center" class="ButtonsTable">
          <input name="save" type="submit" class="button" id="save" value="SAVE"/>
          <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="history.back();" />        </td>
      </tr>
    </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
