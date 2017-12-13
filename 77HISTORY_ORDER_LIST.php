<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);

if (!empty($_POST['startdate'])){
$startdate=$_POST['startdate'];
}
else {
$startdate='00/00/0000';
}

$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysql_query($startdate, $tryconnection) or die(mysql_error());
$startdate=mysqli_fetch_array($startdate);

if (!empty($_POST['enddate'])){
$enddate=$_POST['enddate'];
}
else {
$enddate=date('m/d/Y');
}

$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysqli_fetch_array($enddate);


$query_INVTHIST = "SELECT * FROM INVTHIST WHERE VPCCODE = '$_GET[vpccode]' AND ORDERED >= '$startdate[0]' AND ORDERED <= '$enddate[0]'";
$INVTHIST = mysql_query($query_INVTHIST, $tryconnection) or die(mysql_error());
$row_INVTHIST = mysqli_fetch_assoc($INVTHIST);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>INVENTORY DETAIL SALES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
resizeTo(650,500);
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+60,toppos+10);
document.editsl.invunits.focus();
}
</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="editsl" id="editsl" style="position:absolute; top:0px; left:0px;">
    <table width="650" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td height="20" colspan="6" align="center" class="Verdana12B"><span style="background-color:#FFFF00">&nbsp;<?php echo $row_INVTHIST['DESCRIP']; ?>&nbsp;</span></td>
      </tr>
      <tr class="Verdana11Bwhite" bgcolor="#000000">
        <td width="50"></td>
        <td width=150"" align="center">&nbsp;Date Ordered</td>
        <td width="150" align="center">&nbsp;Date Received</td>
        <td width="100" align="center">Units</td>
        <td width="100" align="center">Unit Price</td>
        <td width="50"></td>
      </tr>
      <tr>
       <td colspan="6">
       <div style="height:407px; overflow:auto;">
     <table width="100%" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">
      <tr>
        <td></td>
        <td width="150" height="0"></td>
        <td width="150"></td>
        <td width="100"></td>
        <td width="100"></td>
        <td></td>
      </tr>
     <?php do { ?>   
      <tr>
        <td></td>
        <td height="18" align="center" class="Verdana12">&nbsp;<?php echo $row_INVTHIST['ORDERED']; ?></td>
        <td height="18" align="center" class="Verdana12">&nbsp;<?php echo $row_INVTHIST['RECTDATE']; ?></td>
        <td align="right" class="Verdana12"><?php echo $row_INVTHIST['UNITS']; ?></td>
        <td align="right" class="Verdana12"><?php echo $row_INVTHIST['DRUGCOST']; ?></td>
        <td></td>
      </tr>
  <?php } while ($row_INVTHIST = mysqli_fetch_assoc($INVTHIST)); ?> 
        </table> 
        </div> 
       </td>
      </tr>
      <tr>
        <td colspan="6" align="center" class="ButtonsTable">
        	<input name="cancel" type="reset" class="button" id="cancel" value="OK" onclick="self.close();" />
        </td>
      </tr>
    </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
