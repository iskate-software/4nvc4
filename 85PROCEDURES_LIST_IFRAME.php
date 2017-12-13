<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

mysqli_select_db($tryconnection, $database_tryconnection);

$cat=$_GET['category'];
$spec=$_GET['species'];

$query_CAN1=sprintf("SELECT INVDESC, INVTOT, PROCID FROM PROCEDUR WHERE PROCODE = '%s' AND FEEFILE='%s' ORDER BY ISORTCODE ASC",$cat, $spec);
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
var proc=document.getElementById('procfile').value;
window.open("QUICK_FEE.php?procid="+proc,"_blank", "height=259, width=450");
}

</script>

<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->
<div id="WindowBody">
<form name="prociframe">
<?php
echo"<select id='procfile' multiple='multiple' class='SelectList' ondblclick='tfeefile()'>";
do {
echo"<option value='".$row_CAN1['PROCID']."'>";
echo "&nbsp;".$row_CAN1['INVDESC']."&nbsp;&nbsp;&nbsp;(".$row_CAN1['INVTOT'].")";
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
