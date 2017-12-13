<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$select_FAXREP = "SELECT * FROM FAXREP LIMIT 1";
$FAXREP = mysql_query($select_FAXREP) or die(mysql_error());
$row_FAXREP = mysqli_fetch_assoc($FAXREP);

$select_INVENTOR = "SELECT *, SUM(`UNITS`) AS `UNITS` FROM INVENTOR WHERE BACKORDER <> 1 GROUP BY `DESCRIP` ORDER BY `DESCRIP` ASC";
$INVENTOR = mysql_query($select_INVENTOR) or die(mysql_error());
$row_INVENTOR = mysqli_fetch_assoc($INVENTOR);

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
window.print();
self.close();
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
</style>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<table width="100%" border="1" cellspacing="0" cellpadding="0" frame="box" rules="all">
  <tr>
    <td height="31" colspan="4" align="center" valign="middle" class="Courier11"><span style="font-size: 18px"><strong style="letter-spacing:3px;"> - FAX ORDER FORM -</strong></span></td>
  </tr>
  <tr>
    <td height="64" colspan="4" align="left" valign="top" class="Courier11 style1">
    <div style="line-height:30px;">
    	&nbsp;<strong>Fax To:</strong> <?php echo $row_FAXREP['FAXTO']; ?> &nbsp;<strong>Date:</strong> <?php echo date('m/d/Y'); ?><br />
      	&nbsp;<strong>From: Clinic Code:</strong> <?php echo $row_FAXREP['CODE']; ?>&nbsp;&nbsp;&nbsp;<strong>Name:</strong> <?php echo $row_FAXREP['CLINIC']; ?><br />
      	&nbsp;<strong>Phone#:</strong>&nbsp;&nbsp;&nbsp;<?php echo $row_FAXREP['AREA']; ?>-<?php echo $row_FAXREP['PHONE']; ?> <strong>&nbsp;&nbsp;&nbsp;&nbsp;Fax#:</strong> <?php echo $row_FAXREP['FAXNO']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Sent By:</strong> <?php echo $row_FAXREP['SENTBY']; ?><br />
      	&nbsp;<strong>Ship To:</strong> <?php echo $row_FAXREP['SHIPTO']; ?></div>      </td>
  </tr>
  <tr>
    <td width="19%" align="center" class="Courier11 style2">Item Number</td>
    <td width="17%" align="center" class="Courier11 style2">Quantity</td>
    <td width="40%" align="center" class="Courier11 style2">Description</td>
    <td width="24%" align="center" class="Courier11 style2">Packaging</td>
  </tr>
  <tr>
    <td height="42" colspan="4" align="center" class="style2 Courier11"><strong>Hold Vaccines Until Monday if Placing Order On Friday? - ( Y ) Y/N</strong></td>
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
    <td class="Courier11 style2"><?php echo $row_INVENTOR['DESCRIP']; ?></td>
    <td class="Courier11 style2"><?php echo $row_INVENTOR['PACKAGE']; ?></td>
  </tr>
  
 <?php 
} while ($row_INVENTOR = mysqli_fetch_assoc($INVENTOR)); ?>  

</table>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
