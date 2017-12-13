<?php 
session_start();
require_once('../../tryconnection.php');
require_once('../../ASSETS/tax.php');

$client=$_SESSION['client'];

mysql_select_db($database_tryconnection, $tryconnection);

if ($_GET['todo']=='create' || $_GET['todo']=='apply'){
$query_ARARECV = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM ARARECV WHERE CUSTNO='$client'";
$ARARECV = mysql_query($query_ARARECV, $tryconnection) or die(mysql_error());
$row_ARARECV = mysqli_fetch_assoc($ARARECV);
}

else if ($_GET['todo']=='cancel'){
$file2look='ARRECHS';
$query_ARARECV = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM ARARECV WHERE CUSTNO='$client'";
$ARARECV = mysql_query($query_ARARECV, $tryconnection) or die(mysql_error());
$row_ARARECV = mysqli_fetch_assoc($ARARECV);
	
	if ($_GET['tea']=='1'){
	$file2look=$_GET['file2look'];
	$query_ARARECV = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM $file2look WHERE CUSTNO='$client'";
	$ARARECV = mysql_query($query_ARARECV, $tryconnection) or die(mysql_error());
	$row_ARARECV = mysqli_fetch_assoc($ARARECV);
		if ($_GET['file2look']=='ARARECV'){
		$file2look='ARRECHS';
		}
		else {
		$file2look='ARARECV';
		}
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
-->
</style>
<!-- InstanceBegin template="/Templates/IFRAME.dwt" codeOutsideHTMLIsLocked="false" -->
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

}


function highliteline(x){
document.getElementById(x).style.cursor="pointer";
document.getElementById(x).style.backgroundColor="#DCF6DD";
document.getElementById(document.cash_receipt.unique1.value).style.backgroundColor="#00E684";
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
document.getElementById(document.cash_receipt.unique1.value).style.backgroundColor="#00E684";
}

function setinvoice(unique1,invno, client){
	var tablename=document.getElementById('invlist');
	var trname=tablename.getElementsByTagName('tr');
	for (var i=0; i<trname.length; i++){
	trname[i].style.backgroundColor="#FFFFFF";
	}
document.cash_receipt.unique1.value=unique1;
document.cash_receipt.invno.value=invno;
document.getElementById(invno).style.backgroundColor="#00E684";
}

function payinvoice(todo){
var unique1=document.cash_receipt.unique1.value;
var invno=document.cash_receipt.invno.value;
window.open('CASH_RECEIPTS_PAY.php?file2look=<?php echo $_GET['file2look']; ?>&todo='+todo+'&unique1='+unique1,'&invno='+invno,'_blank', 'width=700, height=287')}

function look4file(file2look){
document.location="CASH_REC_IFRAME.php?todo=cancel&tea=1&file2look="+file2look;
}
</script>

<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->
<div id="WindowBody" style="text-align:center;">
<form method="post" action="" name="cash_receipt">

<input type="hidden"  name="invno" value="" />
<input type="hidden"  name="unique1" value="" />
<table width="733" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="55" height="10" bgcolor="#000000" class="Verdana11Bwhite">Invoice</td>
    <td width="55" height="10" bgcolor="#000000" class="Verdana11Bwhite">Inv. Date</td>
    <td width="60"  height="10" bgcolor="#000000" class="Verdana11Bwhite">Amount</td>
    <td width="50"  height="10" bgcolor="#000000" class="Verdana11Bwhite">Paid</td>
    <td width="50"  height="10" bgcolor="#000000" class="Verdana11Bwhite">Balance</td>
    <td width="50"  height="10" bgcolor="#000000" class="Verdana11Bwhite">P.Date</td>
    <td width="50"  height="10" bgcolor="#000000" class="Verdana11Bwhite">Method</td>
    <td width="60" height="10" bgcolor="#000000" class="Verdana11Bwhite">Inv. Reason</td>
    <td width="80" height="10" bgcolor="#000000" class="Verdana11Bwhite">Unique</td>
  </tr>
  <tr>
    <td height="393" colspan="9" valign="top">
    <div style="height:392px; overflow:auto;">
		<table width="100%" border="1" bordercolor="#CCCCCC" cellspacing="0" cellpadding="0" class="Verdana12" frame="below" rules="rows" id="invlist">
    	<?php 
        do {
			echo "<tr id='".$row_ARARECV['UNIQUE1']."' onmouseover='highliteline(this.id)' onmouseout='whiteoutline(this.id)' onclick=\"setinvoice(this.id, '".$row_ARARECV['UNIQUE1']."');\" ondblclick=\"window.open('../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search=ARINVOI&invdte=".$row_ARARECV['INVDTE']."&invno=".$row_ARARECV['INVNO']."', '_blank')\" title='Doubleclick to view this invoice'>";
			
			echo "<td width='80' height='15'>".$row_ARARECV['INVNO']."</td>";
			echo "<td width='80'>".$row_ARARECV['INVDTE']."</td>";
			echo "<td width='90'>".$row_ARARECV['ITOTAL']."</td>";
			echo "<td width='85'>".$row_ARARECV['AMTPAID']."</td>";
			echo "<td width='85'>".$row_ARARECV['IBAL']."</td>";
			echo "<td width='70'>".$row_ARARECV['DTEPAID']."</td>";
			echo "<td width='81'>" ;
			if ($row_ARARECV['REFNO']=='ONAC') {echo "&nbsp;";} else {echo $row_ARARECV['REFNO'];}
			echo "</td>";
			echo "<td width='110'>".$row_ARARECV['PONUM']."</td>";
			echo "<td width=''>".$row_ARARECV['UNIQUE1']."</td>";
			echo "</tr>";
			} while ($row_ARARECV = mysqli_fetch_assoc($ARARECV));
		?>
    	</table>
    </div>
    </td>
    </tr>
  <tr <?php if ($_GET['todo']=='cancel'){echo "style='display:none'";} ?>>
    <td colspan="8" align="center" class="ButtonsTable">
    <input name="button" type="button" class="button" id="button" value="PAY" onclick="payinvoice('<?php echo $_GET['todo']; ?>');" />
    <input name="receipt" type="button" class="button" id="receipt" value="RECEIPT" onclick="window.open('../../IMAGES/CUSTOM_DOCUMENTS/CASH_RECEIPT.php?invdte=<?php echo date('m/d/Y'); ?>&amp;custno='+sessionStorage.client,'_blank')" />
<!--<input name="button2" type="button" class="button" id="button2" value="VIEW" />-->    
	<input name="button3" type="button" class="button" id="button3" value="CANCEL" onclick="window.open('CASH_REC_DIR_IFRAME.php','_self')" />
   	</td>
  </tr>
  <tr <?php if ($_GET['todo']=='create'){echo "style='display:none'";} ?>>
    <td colspan="8" align="center" class="ButtonsTable">
    <input name="button12" type="button" class="button" id="button12" value="UNPAY" onclick="payinvoice('cancel');" />
    <input name="button11" type="button" class="button" id="button11" value="<?php if($file2look=='ARARECV'){echo "CUR. MTH";} else {echo "LAST MTH";} ?>" onclick="look4file('<?php echo $file2look; ?>');" />
<!--<input name="button2" type="button" class="button" id="button2" value="VIEW" onclick="window.open('../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?invdte=<?php echo $row_ARARECV['INVDTE']; ?>&invno=<?php echo $row_ARARECV['INVNO']; ?>', '_blank')"/>-->
	<input name="receipt" type="button" class="button" id="receipt" value="RECEIPT" onclick="window.open('../../IMAGES/CUSTOM_DOCUMENTS/CASH_RECEIPT.php?invdte=<?php echo date('m/d/Y'); ?>&amp;custno='+sessionStorage.client,'_blank')" /><input name="cancel2" type="button" class="button" id="cancel2" value="CANCEL" onclick="window.open('CASH_REC_DIR_IFRAME.php','_self')" />
    </td>
  </tr>
</table>

</form>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
