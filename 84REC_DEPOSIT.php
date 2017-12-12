<?php 
session_start();
require_once('../../tryconnection.php');
require_once('../../ASSETS/tax.php');

$client=$_SESSION['client'];

mysql_select_db($database_tryconnection, $tryconnection);
$query_Staff = "SELECT * FROM STAFF WHERE SIGNEDIN=1";
$Staff = mysql_query($query_Staff, $tryconnection) or die(mysql_error());
$row_Staff = mysql_fetch_assoc($Staff);

$query_Doctor = "SELECT * FROM DOCTOR WHERE SIGNEDIN=1";
$Doctor = mysql_query($query_Doctor, $tryconnection) or die(mysql_error());
$row_Doctor = mysql_fetch_assoc($Doctor);

$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO = '$client'";
$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
$row_CLIENT = mysql_fetch_assoc($CLIENT);


if (isset($_POST['ok'])){
$insert_ARCASHR = "INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, REFNO, AMTPAID, DTEPAID) VALUES ('DEP.', NOW(), '$client', '".mysql_real_escape_string($row_CLIENT['TITLE']." ".$row_CLIENT['CONTACT']." ".$row_CLIENT['COMPANY'])."', '".mysql_real_escape_string($_POST['salesmn'])."', '$_POST[refno]', '$_POST[amtpaid]', STR_TO_DATE('$_POST[minvdte]', '%m/%d/%Y %H:%i:%s')) ";
$RESULT = mysql_query($insert_ARCASHR, $tryconnection) or die(mysql_error());

$query_ARCUSTO = "SELECT BALANCE, CREDIT FROM ARCUSTO WHERE CUSTNO='$_SESSION[client]'";
$ARCUSTO = mysql_query($query_ARCUSTO, $tryconnection) or die(mysql_error());
$row_ARCUSTO = mysql_fetch_assoc($ARCUSTO);

$balance=$row_ARCUSTO['BALANCE']-$_POST['amtpaid'];

$update_ARCUSTO = "UPDATE ARCUSTO SET BALANCE='$balance', CREDIT=CREDIT+$_POST[amtpaid] WHERE CUSTNO='$_SESSION[client]' ";
$RESULT = mysql_query($update_ARCUSTO, $tryconnection) or die(mysql_error());

//		if ($balance<=0){
//		$credit=-($balance);
//		$update_ARCUSTO = "UPDATE ARCUSTO SET CREDIT='$credit' WHERE CUSTNO='$_SESSION[client]' ";
//		$RESULT = mysql_query($update_ARCUSTO, $tryconnection) or die(mysql_error());
//		}
		
$_SESSION['cashcalculator']=$_SESSION['cashcalculator']+$_POST['amtpaid'];
header("Location:CASH_REC_DIR_IFRAME.php");
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<style type="text/css">
<!--
#apDiv1 {
	position:absolute;
	width:237px;
	height:26px;
	z-index:2;
	left: 486px;
	top: 28px;
}
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
document.rec_dep.amtpaid.focus();

}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
</script>

<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">

<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>

<script type="text/javascript" src="../../ASSETS/calendar.js"></script>

<!-- InstanceBeginEditable name="EditRegion1" -->
<div id="WindowBody" style="text-align:center;">
<form method="post" action="" name="rec_dep">

<table width="733" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="405" colspan="8" align="center" valign="middle">
		
        <table width="50%" border="1" cellspacing="0" cellpadding="0" frame="box" rules="none">
  <tr>
    <td width="4%">&nbsp;</td>
    <td width="35%">&nbsp;</td>
    <td width="61%">&nbsp;</td>
  </tr>
  <tr>
    <td class="Verdana12">&nbsp;</td>
    <td height="35" class="Verdana12">Amount of Payment:</td>
    <td height="35" class="Verdana12"><span class="Verdana12B">
      <input name="amtpaid" type="text" class="Inputright" id="amtpaid"  onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);"  size="10" title="Enter the amount without -"/>
    </span></td>
  </tr>
  <tr>
    <td class="Verdana12">&nbsp;</td>
    <td height="35" class="Verdana12">Payment Method:</td>
    <td height="35" class="Verdana12"><span class="Verdana11">
      <select name="refno" id="refno" <?php if ($_GET['todo']=='cancel'){echo "style='display:none'";} ?>>
        <option value="Cash">&nbsp;&nbsp;Cash</option>
        <option value="Cheque">&nbsp;&nbsp;Cheque</option>
        <option value="DCrd">&nbsp;&nbsp;DCrd</option>
        <option value="Visa">&nbsp;&nbsp;Visa</option>
        <option value="MC">&nbsp;&nbsp;M/C</option>
        <option value="Amex">&nbsp;&nbsp;Amex</option>
        <option value="Diners">&nbsp;&nbsp;Diners</option>
        <option value="GE">&nbsp;&nbsp;GE</option>
        <option value="ONAC" id="onac2" onclick="document.routine.payment.value='0';">&nbsp;&nbsp;On Account</option>
        <option value="PDC">&nbsp;&nbsp;Post Dated Cheque</option>
        <option value="Pound">&nbsp;&nbsp;Pound</option>
        <option value="Cell">&nbsp;&nbsp;Cell</option>
        <option value="Corrn">&nbsp;&nbsp;Corrn</option>
      </select>
    </span></td>
  </tr>
  <tr>
    <td class="Verdana12">&nbsp;</td>
    <td height="35" class="Verdana12">Payment Date:</td>
    <td height="35" class="Verdana12"><input name="minvdte" type="text" class="Input" id="minvdte" value="<?php echo date('m/d/Y'); ?>" size="10" maxlength="12" onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);"  onclick="ds_sh(this)" /></td>
  </tr>
  <tr>
    <td class="Verdana12">&nbsp;</td>
    <td height="35" class="Verdana12">Staff:</td>
    <td height="35" class="Verdana12"><select name="salesmn" id="salesmn">
      <?php
			do { echo '<option value="'.$row_Staff['STAFF'].'">'.$row_Staff['STAFF'].'</option>'; 
					} while ($row_Staff = mysql_fetch_assoc($Staff));
			do { echo '<option value="'.$row_Doctor['DOCTOR'].'">'.$row_Doctor['DOCTOR'].'</option>';
					} while ($row_Doctor = mysql_fetch_assoc($Doctor));
			?>
    </select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

        <div id="apDiv1"><span class="Verdana11B">Cash Receipts: <?php echo number_format($_SESSION['cashcalculator'],2); ?></span></div></td>
    </tr>
  <tr>
    <td colspan="8" align="center" class="ButtonsTable">
    <input name="ok" type="submit" class="button" id="ok" value="OK" />
    <input name="button3" type="button" class="button" id="button3" value="CANCEL" onclick="history.back();" /></td>
  </tr>
</table>

</form>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
