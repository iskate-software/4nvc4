<?php
session_start();
require_once('../../tryconnection.php'); 

$lookup = $_GET['lookup'];

if ($_GET['arinvtype']=="F"){
$arinvtype = "F";
}
else if ($_GET['arinvtype']=="O"){
$arinvtype = "O";
}
else  if ($_GET['arinvtype']=="P"){
$arinvtype = "P";
}
else {
$arinvtype= "";
}


$sortby = ITEM;
if (!empty($_GET['sorting'])){
$sortby = $_GET['sorting'];
}
//AND ARINVTYPE='$arinvtype'
mysql_select_db($database_tryconnection, $tryconnection);
$query_INVENTORY = "SELECT *, DATE_FORMAT(EXPDATE, '%m/%d/%Y') AS EXPDATE FROM ARINVT WHERE (ITEM LIKE '$lookup%' OR DESCRIP LIKE '$lookup%' OR VPARTNO LIKE '$lookup%' OR BARCODE LIKE '$lookup%') AND ARINVTYPE LIKE '$arinvtype%' ORDER BY ".$sortby." ASC";
$INVENTORY = mysql_query($query_INVENTORY, $tryconnection) or die(mysql_error());
$row_INVENTORY = mysqli_fetch_assoc($INVENTORY);
$totalRows_INVENTORY = mysqli_num_rows($INVENTORY);

$_SESSION['lookup'] = $_GET['lookup'];
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/IFRAME.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>DV MANAGER MAC</title>

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<style type="text/css">
<!--
#WindowBody {
	position:absolute;
	top:0px;
	width:733px;
	height:553px;
	z-index:1;
	font-family: "Verdana";
	outline-style: ridge;
	outline-color: #FFFFFF;
	outline-width: medium;
	background-color: #FFFFFF;
	left: 0px;
	color: #000000;
	text-align: left;
}
-->
</style>

</head>
<!-- InstanceBeginEditable name="EditRegion2" -->

<script type="text/javascript">

function bodyonload()
{
//parent.frames[0].document.forms[0].item.value="<?php echo $_GET['item']; ?>";
}

function highliteline(x){
document.getElementById(x).style.cursor="pointer";
document.getElementById(x).style.backgroundColor="#DCF6DD";
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
}


function transferitem(itemid){
parent.window.document.location='ADD_ORDER_LIST.php?itemid='+itemid;
}
</script>



<style type="text/css">
</style>
<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->

<div id="WindowBody" style="width:715px;">
<div style="height:100%;">
<form action="" method="post" name="inventory_list" class="FormDisplay">

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows" bgcolor="#FFFFFF">
   
   <?php do { ?> 
   
   <tr class="Verdana12" id="<?php echo $row_INVENTORY['ITEMID']; ?>" onclick="transferitem('<?php echo $row_INVENTORY['ITEMID']; ?>')" onmouseover="highliteline(this.id);" onmouseout="whiteoutline(this.id);">
      <td width="93">&nbsp;&nbsp;<?php echo $row_INVENTORY['ITEM']; ?></td>
      <td width="311" align="left"><?php echo $row_INVENTORY['DESCRIP']; ?></td>
      <td width="136" height="10" align="left"><?php echo $row_INVENTORY['VPARTNO']; ?></td>
      <td height="15" align="left"><?php echo $row_INVENTORY['BARCODE']; ?></td>
    </tr>
    
    <?php } while ($row_INVENTORY = mysqli_fetch_assoc($INVENTORY)); ?>
</table>

</form>
</div>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($INVENTORY);
?>
