<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$select_FAXREP = "SELECT * FROM FAXREP LIMIT 1";
$FAXREP = mysql_query($select_FAXREP) or die(mysql_error());
$row_FAXREP = mysql_fetch_assoc($FAXREP);

$select_INVENTOR = "SELECT *, SUM(`UNITS`) AS `UNITS` FROM INVENTOR WHERE UNITS <> 0 AND SUPPLIER = '$_SESSION[supplier]' GROUP BY `DESCRIP` ORDER BY `DESCRIP` ASC";
$INVENTOR = mysql_query($select_INVENTOR) or die(mysql_error());
$row_INVENTOR = mysql_fetch_assoc($INVENTOR);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>FAX ORDER FORM</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
<?php  $select_INVENTOR = "SELECT *, SUM(`UNITS`) AS `UNITS` FROM INVENTOR WHERE UNITS <> 0 AND BACKORDER <> 1 AND SUPPLIER = '$_SESSION[supplier]' GROUP BY `DESCRIP` ORDER BY `DESCRIP` ASC";
$INVENTOR = mysql_query($select_INVENTOR) or die(mysql_error());
$row_INVENTOR = mysql_fetch_assoc($INVENTOR);?>
window.print();
//self.close();
}

</script>
<style type="text/css">
body{
background-color:#FFFFFF;
overflow:auto;
}
.style1 {
	font-size: 17px
}
.style2 {font-size: 16px}
.style3 {
	font-size: 16px;
	font-weight: bold;
}
</style>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="31" colspan="6" align="center" valign="middle" class="Courier11 style3"><script type="text/javascript">document.write(localStorage.hospname);</script></td>
  </tr>
  <tr>
    <td height="64" colspan="6" align="center" valign="top" class="Courier11 style1">INVENTORY ORDER FILE REPORT<br />CLINIC CODE: <?php echo $row_FAXREP['CODE']. ' - ' .date('l j F Y'); ?>	</td>
  </tr>
  <tr>
    <td width="15%" align="center" class="style2 Courier11"><strong>Code</strong></td>
    <td width="10%" align="center" class="style2 Courier11"><strong>Units</strong></td>
    <td width="10%" align="center" class="Courier11 style2"><strong>Vendor#</strong></td>
    <td width="19%" align="center" class="style2 Courier11"><strong>Packaging</strong></td>
    <td width="40%" align="left" class="style2 Courier11"><strong>Description</strong></td>
    <td width="30%" align="center" class="style2 Courier11"><strong>Drug Cost</strong></td>
  </tr>
<?php do { ?>
  <tr>
    <td class="Courier11 style2" align="center"><?php echo $row_INVENTOR['VPCCODE']; ?></td>
    <td class="Courier11 style2" align="right">
	<?php 
	if (number_format($row_INVENTOR['UNITS'],0)==$row_INVENTOR['UNITS']){
		echo  number_format($row_INVENTOR['UNITS'],0)."&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		else {
		echo $row_INVENTOR['UNITS'];
		}
	 ?>    </td>
    <td class="Courier11 style2" align="center"><?php echo $row_INVENTOR['VPCCODE']; ?></td>
    <td class="Courier11 style2" align="center"><?php echo $row_INVENTOR['PACKAGE']; ?></td>
    <td class="Courier11 style2"><?php echo $row_INVENTOR['DESCRIP']; ?></td>
    <td class="Courier11 style2" align="right"><?php echo $row_INVENTOR['DRUGCOST']; ?>&nbsp;&nbsp;</td>
  </tr>
  
 <?php 
$cogs = $cogs + ($row_INVENTOR['DRUGCOST']*$row_INVENTOR['UNITS']);
} while ($row_INVENTOR = mysql_fetch_assoc($INVENTOR)); ?>  
  <tr>
    <td colspan="3" align="right" class="style2 Courier11"><strong>** TOTAL COST **</strong><strong></strong></td>
    <td width="19%" align="center" class="style2 Courier11">&nbsp;</td>
    <td colspan="2" align="right" class="Courier11 style2"><strong><?php echo number_format($cogs, 2); ?>&nbsp;&nbsp;</strong></td>
  </tr>
  <tr>
    <td colspan="3" align="right" class="style2 Courier11">&nbsp;</td>
    <td align="center" class="style2 Courier11">&nbsp;</td>
    <td colspan="2" align="right" class="Courier11 style2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6" align="center" class="Courier11 style2"><strong>Please advise of any backorders. Thanks!</strong></td>
  </tr>
  <tr>
    <td colspan="6" align="center" class="style2 Courier11"><strong>Phone 	Fax </strong></td>
  </tr>
</table>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
