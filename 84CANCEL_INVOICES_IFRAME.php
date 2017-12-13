<?php 
session_start();
require_once('../../tryconnection.php');
require_once('../../ASSETS/tax.php');

$client=$_SESSION['client'];

mysql_select_db($database_tryconnection, $tryconnection);

//need to know what files to look into and what condition to retrieve by

$query_ARARECV = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM ARARECV WHERE CUSTNO='$client'";
$ARARECV = mysql_query($query_ARARECV, $tryconnection) or die(mysql_error());
$row_ARARECV = mysqli_fetch_assoc($ARARECV);

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
document.getElementById(document.cancel_invoices.unique1.value).style.backgroundColor="#00E684";
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
document.getElementById(document.cancel_invoices.unique1.value).style.backgroundColor="#00E684";
}

function setinvoice(unique1,invno, client, ibal){
	var tablename=document.getElementById('invlist');
	var trname=tablename.getElementsByTagName('tr');
	for (var i=0; i<trname.length; i++){
	trname[i].style.backgroundColor="#FFFFFF";
	}
document.cancel_invoices.unique1.value=unique1;
document.cancel_invoices.invno.value=invno;
document.cancel_invoices.ibal.value=ibal;
document.getElementById(unique1).style.backgroundColor="#00E684";
}

function cancelinv(){
var unique1=document.cancel_invoices.unique1.value;
var invno=document.cancel_invoices.invno.value;
var ibal=document.cancel_invoices.ibal.value;
if (ibal=='no'){
	if (confirm('Payment made on this invoice must be cancelled before the invoice can be deleted. Do you want to cancel the payment now?')){
	//document.cancel_invoices.submit();
	window.open('CANCEL_INVOICE_PROCEED.php?ibal=no&unique1='+unique1,'&invno='+invno,'_blank', 'width=700, height=287')
		}
	}
else {
	window.open('CANCEL_INVOICE_PROCEED.php?ibal=yes&unique1='+unique1,'&invno='+invno,'_blank', 'width=700, height=287')
	}
}

function viewinv(){
var invno=document.cancel_invoices.invno.value;
window.open("../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search=ARINVOI&invno="+invno, '_blank');
}
</script>

<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->
<div id="WindowBody" style="text-align:center;">
<form method="post" action="" name="cancel_invoices">

<input type="hidden"  name="invno" value="" />
<input type="hidden"  name="ibal" value="" />
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
			echo "<tr id='".$row_ARARECV['UNIQUE1']."' onmouseover='highliteline(this.id)' onmouseout='whiteoutline(this.id)' onclick=\"setinvoice(this.id, '".$row_ARARECV['UNIQUE1']."', '";
			if ($row_ARARECV['IBAL']=='0.00'){echo "no";} else {echo "yes";}
			echo "');\">";
			echo "<td width='80' height='15'>".$row_ARARECV['INVNO']."</td>";
			echo "<td width='80'>".$row_ARARECV['INVDTE']."</td>";
			echo "<td width='90'>".$row_ARARECV['ITOTAL']."</td>";
			echo "<td width='85'>".$row_ARARECV['AMTPAID']."</td>";
			echo "<td width='85'>".$row_ARARECV['IBAL']."</td>";
			echo "<td width='70'>".$row_ARARECV['DTEPAID']."</td>";
			echo "<td width='75'>".$row_ARARECV['REFNO']."</td>";
			echo "<td width='120'>".$row_ARARECV['PONUM']."</td>";
			echo "<td width=''>".$row_ARARECV['UNIQUE1']."</td>";
			echo "</tr>";
			} while ($row_ARARECV = mysqli_fetch_assoc($ARARECV));
		?>
    	</table>
    </div>
    </td>
    </tr>
  <tr>
    <td colspan="8" align="center" class="ButtonsTable">
    <input name="button" type="button" class="button" id="button" value="CANCEL INVOICE" onclick="cancelinv();" style="width:140px;"/>
    <input name="button4" type="button" class="button" id="button4" value="FINISHED"  style="width:140px;" onclick="sessionStorage.removeItem('refID'); window.open('../../CLIENT/CLIENT_PATIENT_FILE.php','_parent')"/>
    <input name="button2" type="button" class="button" id="button2" value="VIEW"  style="width:140px;" onclick="viewinv();"/>
    <input name="button3" type="button" class="button" id="button3" value="FAMILY"  style="width:140px;" onclick="history.back();"/>
    </td>
  </tr>
</table>
<input type="hidden" value="1" name="check"  />
</form>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
