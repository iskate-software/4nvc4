<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

mysqli_select_db($tryconnection, $database_tryconnection);

$cat=$_GET['category'];
$spec=$_GET['species'];

$query_CAN1=sprintf("SELECT TFFID, TNO, TDESCR, TTYPE, TCATGRY FROM VETCAN WHERE TCATGRY = '%s' AND TSPECIES='%s' ORDER BY TNO ASC",$cat, $spec);
$CAN1 = mysqli_query($tryconnection, $query_CAN1) or die(mysqli_error($mysqli_link));
$row_CAN1 = mysqli_fetch_assoc($CAN1);
$totalRows_CAN1 = mysqli_num_rows($CAN1);
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

<style type="text/css">
.SelectList {
	width: 320px;
	height: 490px;
	font-family: "Verdana";
	font-size: 11px;
	border-width: 0px;
	padding: 5 px;
	outline-width: 0px;
}
</style>

<script type="text/javascript">

function bodyonload()
{
opener.document.getElementById('category11').value="<?php echo $_GET['category']; ?>";
}

function tfeefile()
{
var tff=document.getElementById('tffile').value;
window.open("UPDATE_TFF.php?tffid="+tff+"&category=<?php echo $cat; ?>&species=<?php echo $spec; ?>&repeat=0","_parent");
}

</script>

<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->
<div id="WindowBody">
<form name="tffiframe">
<?php
echo"<select id='tffile' multiple='multiple' class='SelectList' ondblclick='tfeefile()'>";
do {
echo"<option value='".$row_CAN1['TFFID']."'>";
if ($row_CAN1['TNO']<10){echo "&nbsp;&nbsp;";}
echo $row_CAN1['TNO']."&nbsp;".$row_CAN1['TDESCR'];
echo"</option>";
} while ($row_CAN1 = mysqli_fetch_assoc($CAN1));
echo"</select>";		 
?>
<input type="hidden" name="category" value="<?php echo $_GET['category']; ?>"/>
</form>
</div>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
