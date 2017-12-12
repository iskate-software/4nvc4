<?php 
session_start();
require_once('../../tryconnection.php');
require_once('../../ASSETS/tax.php');

$invno=$_GET['invno'];
$unique1=$_GET['unique1'];

mysql_select_db($database_tryconnection, $tryconnection);
$query_Staff = "SELECT * FROM STAFF WHERE SIGNEDIN=1";
$Staff = mysql_query($query_Staff, $tryconnection) or die(mysql_error());
$row_Staff = mysql_fetch_assoc($Staff);

$query_Doctor = "SELECT * FROM DOCTOR WHERE SIGNEDIN=1 AND INSTR(DOCTOR,'TBA') = 0 AND INSTR(DOCTOR,'TECHNICIAN') = 0 AND INSTR(DOCTOR,'HOSPITAL') = 0 ";
$Doctor = mysql_query($query_Doctor, $tryconnection) or die(mysql_error());
$row_Doctor = mysql_fetch_assoc($Doctor);


if ($_GET['todo']=='create' || $_GET['todo']=='apply'){
//$query_ARARECV = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE FROM ARARECV WHERE UNIQUE1 = '$unique1' AND CUSTNO='$_SESSION[client]'";
$query_ARARECV = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE FROM ARARECV WHERE UNIQUE1 = '$unique1' ";
$ARARECV = mysql_query($query_ARARECV, $tryconnection) or die(mysql_error());
$row_ARARECV = mysql_fetch_assoc($ARARECV);

	if ($_GET['todo']=='apply'){
	$query_ARCUSTO = "SELECT BALANCE, CREDIT FROM ARCUSTO WHERE CUSTNO='$_SESSION[client]'";
	$ARCUSTO = mysql_query($query_ARCUSTO, $tryconnection) or die(mysql_error());
	$row_ARCUSTO = mysql_fetch_assoc($ARCUSTO);
	$dep2payment = 0 ;
		if ($row_ARCUSTO['CREDIT'] <= $row_ARARECV['IBAL']){
		$dep2payment = $row_ARCUSTO['CREDIT'];
		}
		else if ($row_ARCUSTO['CREDIT'] > $row_ARARECV['IBAL']){
		$dep2payment = $row_ARARECV['IBAL'];
		}
	}

}//if ($_GET['todo']=='create')


else if ($_GET['todo']=='cancel'){
$file2look=$_GET['file2look'];

	if ($file2look=='ARRECHS'){
		$query_ARARECV = "SELECT * FROM ARARECV WHERE  CUSTNO='$_SESSION[client]'AND UNIQUE1 = '$unique1' ";
		$ARARECV = mysql_query($query_ARARECV, $tryconnection) or die(mysql_error());
		$row_ARARECV = mysql_fetch_assoc($ARARECV);
		
		if (empty($row_ARARECV)){
		$copy_ARRECHS="INSERT INTO ARARECV SELECT * FROM ARRECHS WHERE UNIQUE1 = '$unique1' AND CUSTNO='$_SESSION[client]'";
		$RESULT = mysql_query($copy_ARRECHS, $tryconnection) or die(mysql_error());
			}
		}//if ($file2look=='ARRECHS')
		
$query_ARARECV = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE FROM $file2look WHERE UNIQUE1 = '$unique1' AND CUSTNO='$_SESSION[client]'";
$ARARECV = mysql_query($query_ARARECV, $tryconnection) or die(mysql_error());
$row_ARARECV = mysql_fetch_assoc($ARARECV);

}//else if ($_GET['todo']=='cancel')


//OK TO CREATE A CASH RECEIPT
if (isset($_POST['ok'])){

$invdte=$_POST['minvdte'].' '.date('H:i:s');
$convt_date = "SELECT STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s')" ;
$get_date = mysql_query($convt_date, $tryconnection) or die(mysql_error()) ;
$row_datex = mysql_fetch_array($get_date) ;
$ibal=$row_ARARECV['IBAL']-$_POST['amtpaid'];
//$amtpaid=$row_ARARECV['AMTPAID']+$_POST['amtpaid'];
$amtpaid = $_POST['amtpaid'];
$realamt = $_POST['amtpaid'];

		if ($ibal < 0){
		$credit=$_POST['amtpaid']-$row_ARARECV['IBAL'];
		$ibal=0;
        $amtpaid = $row_ARARECV['IBAL'] ;
        $_POST['amtpaid'] = $amtpaid ;
		}

if ($_GET['todo']=='apply'){
 $refno = "DEP.APP.";
}
else {
 $refno = $_POST['refno'];
}

$update_ARARECV = "UPDATE ARARECV SET SALESMN='".mysql_real_escape_string($_POST['salesmn'])."', PONUM='".mysql_real_escape_string($_POST['ponum'])."', REFNO='$refno', AMTPAID='$row_ARARECV[AMTPAID]'+'$amtpaid', DTEPAID=STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'), IBAL='$ibal' WHERE UNIQUE1 = '$unique1' AND CUSTNO='$_SESSION[client]'";
$RESULT = mysql_query($update_ARARECV, $tryconnection) or die(mysql_error());

	if ($_GET['todo']=='create'){
	$insert_ARCASHR = "INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, AMTPAID, DTEPAID) VALUES 
	('$row_ARARECV[INVNO]', STR_TO_DATE('$row_ARARECV[INVDTE]', '%m/%d/%Y %H:%i:%s'), '$row_ARARECV[CUSTNO]', '".mysql_real_escape_string($row_ARARECV['COMPANY'])."', '".mysql_real_escape_string($_POST['salesmn'])."', '".mysql_real_escape_string($_POST['ponum'])."', '$_POST[refno]', '$_POST[amtpaid]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s')) ";
	$RESULT = mysql_query($insert_ARCASHR, $tryconnection) or die(mysql_error());
	}
	
	else if ($_GET['todo']=='apply'){
	$insert_ARCASHR = "INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, AMTPAID, DTEPAID) VALUES ('$row_ARARECV[INVNO]', STR_TO_DATE('$row_ARARECV[INVDTE]', '%m/%d/%Y %H:%i:%s'), '$row_ARARECV[CUSTNO]', '".mysql_real_escape_string($row_ARARECV['COMPANY'])."', '".mysql_real_escape_string($_POST['salesmn'])."', '".mysql_real_escape_string($_POST['ponum'])."', 'DEP.APP.', '-$_POST[amtpaid]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s')) ";
	$RESULT = mysql_query($insert_ARCASHR, $tryconnection) or die(mysql_error());	
	$insert_ARCASHR = "INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, AMTPAID, DTEPAID) VALUES ('$row_ARARECV[INVNO]', STR_TO_DATE('$row_ARARECV[INVDTE]', '%m/%d/%Y %H:%i:%s'), '$row_ARARECV[CUSTNO]', '".mysql_real_escape_string($row_ARARECV['COMPANY'])."', '".mysql_real_escape_string($_POST['salesmn'])."', '".mysql_real_escape_string($_POST['ponum'])."', 'DEP.APP.', '$_POST[amtpaid]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s')) ";
	$RESULT = mysql_query($insert_ARCASHR, $tryconnection) or die(mysql_error());
	}
	
		if (isset($credit)){
		  $remainingpayment = $credit ;	
		  $refno = $_POST['refno'] ;
		  $query_ARARECV2="SELECT * FROM ARARECV WHERE CUSTNO='$_SESSION[client]' AND IBAL > 0 ORDER BY invdte, UNIQUE1 ASC";
				$ARARECV2=mysql_query($query_ARARECV2, $tryconnection) or die(mysql_error());
				$row_ARARECV2=mysql_fetch_assoc($ARARECV2);
// AND AUTOPAY
				do {
					
					if ($remainingpayment <= $row_ARARECV2['IBAL']){
						$amtpaid = $remainingpayment;
					}
					else if ($remainingpayment > $row_ARARECV2['IBAL']){
						$amtpaid = $row_ARARECV2['IBAL'];
					}
					
				    $credit = $credit - $amtpaid ;
					if ($amtpaid > 0){
					$update_ARARECV2="UPDATE ARARECV SET AMTPAID=AMTPAID+$amtpaid, IBAL=(IBAL-$amtpaid), DTEPAID='$row_datex[0]', REFNO = '$refno' WHERE INVNO='$row_ARARECV2[INVNO]' AND UNIQUE1 = '$row_ARARECV2[UNIQUE1]'";
					mysql_query($update_ARARECV2, $tryconnection) or die(mysql_error());
				
					$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
							$row_ARARECV2['INVNO'],
							$row_ARARECV2['INVDTE'],
							$row_ARARECV['CUSTNO'],
							mysql_real_escape_string($row_ARARECV['COMPANY']),
							mysql_real_escape_string($_POST['salesmn']),
							mysql_real_escape_string($row_ARARECV2['PONUM']),
							$row_ARARECV2['DISCOUNT'],
							$amtpaid,
							$row_datex[0],
							$refno);
					mysql_query($insert_ARCASHR, $tryconnection) or die(mysql_error());


					$remainingpayment=$remainingpayment - $amtpaid;

					}//if ($remainingpayment > 0){
				} while ($row_ARARECV2=mysql_fetch_assoc($ARARECV2) AND $remainingpayment > 0 );
			
		}

// Check to see if the payment was too large.

if ($remainingpayment > 0 ) {
                   $query_DEP = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
							'DEP.',
							$row_ARARECV2['INVDTE'],
							$row_ARARECV['CUSTNO'],
							mysql_real_escape_string($row_ARARECV['COMPANY']),
							mysql_real_escape_string($_POST['salesmn']),
							mysql_real_escape_string($row_ARARECV2['PONUM']),
							0.00,
							$remainingpayment,
							$row_datex[0],
							$refno);
							
							mysql_query($query_DEP, $tryconnection) or die(mysql_error()) ;
}

$query_ARCUSTO = "SELECT BALANCE, CREDIT FROM ARCUSTO WHERE CUSTNO='$_SESSION[client]' LIMIT 1";
$ARCUSTO = mysql_query($query_ARCUSTO, $tryconnection) or die(mysql_error());
$row_ARCUSTO = mysql_fetch_assoc($ARCUSTO);

$balance=$row_ARCUSTO['BALANCE']-$realamt;


	if ($_GET['todo']!='apply'){
	$update_ARCUSTO = "UPDATE ARCUSTO SET BALANCE='$balance' WHERE CUSTNO='$_SESSION[client]' LIMIT 1";
	$RESULT = mysql_query($update_ARCUSTO, $tryconnection) or die(mysql_error());
	
			if (isset($credit)){
			$credit=$row_ARCUSTO['CREDIT']+$credit;
			$update_ARCUSTO = "UPDATE ARCUSTO SET CREDIT='$credit' WHERE CUSTNO='$_SESSION[client]' LIMIT 1";
			$RESULT = mysql_query($update_ARCUSTO, $tryconnection) or die(mysql_error());
			}
	}
	else if ($_GET['todo']=='apply'){
	$update_ARCUSTO = "UPDATE ARCUSTO SET CREDIT=CREDIT-$_POST[amtpaid] WHERE CUSTNO='$_SESSION[client]' LIMIT 1";
	$RESULT = mysql_query($update_ARCUSTO, $tryconnection) or die(mysql_error());
	}

$closewin="opener.document.location.reload(); self.close();";
}

//OK TO CANCEL CASH RECEIPT
else if (isset($_POST['ok2'])){

//if they enter an amount with '-', force it to positive
	if ($_POST['amtpaid'] < 0){
	$amtpaid = 0 - $_POST['amtpaid'];
	} 
	else {
	$amtpaid = $_POST['amtpaid'];
	}

$invdte=$_POST['minvdte'].' '.date('H:i:s');
$ibal=$row_ARARECV['IBAL']+$amtpaid;

$update_ARARECV = "UPDATE ARARECV SET SALESMN='".mysql_real_escape_string($_POST['salesmn'])."', PONUM='".mysql_real_escape_string($_POST['ponum'])."', REFNO='$_POST[refno]', AMTPAID=AMTPAID-$amtpaid, DTEPAID=STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'), IBAL='$ibal' WHERE UNIQUE1 = '$unique1' AND CUSTNO='$_SESSION[client]'";
$RESULT = mysql_query($update_ARARECV, $tryconnection) or die(mysql_error());

$insert_ARCASHR = "INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, AMTPAID, DTEPAID) VALUES ('$row_ARARECV[INVNO]', STR_TO_DATE('$row_ARARECV[INVDTE]', '%m/%d/%Y %H:%i:%s'), '$row_ARARECV[CUSTNO]', '".mysql_real_escape_string($row_ARARECV['COMPANY'])."', '".mysql_real_escape_string($_POST['salesmn'])."', '".mysql_real_escape_string($_POST['ponum'])."', '$_POST[refno]', '-$amtpaid', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s')) ";
$RESULT = mysql_query($insert_ARCASHR, $tryconnection) or die(mysql_error());
	
$query_ARCUSTO = "SELECT BALANCE, CREDIT FROM ARCUSTO WHERE CUSTNO='$_SESSION[client]' LIMIT 1";
$ARCUSTO = mysql_query($query_ARCUSTO, $tryconnection) or die(mysql_error());
$row_ARCUSTO = mysql_fetch_assoc($ARCUSTO);

$balance=$row_ARCUSTO['BALANCE']+$amtpaid;
$credit=$row_ARCUSTO['CREDIT']-$amtpaid;
	if ($credit<0){$credit="0.00";}

$update_ARCUSTO = "UPDATE ARCUSTO SET BALANCE='$balance', CREDIT='$credit' WHERE CUSTNO='$_SESSION[client]' LIMIT 1";
$RESULT = mysql_query($update_ARCUSTO, $tryconnection) or die(mysql_error());

$closewin="opener.document.location.reload(); self.close();";
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PAY INVOICE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload()
{
<?php echo $closewin; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+40,toppos+140);

document.paycashr.amtpaid.focus();
}

function bodyonunload()
{

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

<form method="post" action="" name="paycashr" id="paycashr" style="position:absolute; top:0px; left:0px;">

<table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td height="43" align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td width="50%" align="center">
    <table width="90%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" frame="box" rules="none">
      <tr>
        <td height="5" width="5"></td>
        <td height="5" width="140"></td>
        <td height="5" width="75"></td>
        <td height="5"></td>
      </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="25" class="Verdana12B">Invoice #: </td>
        <td colspan="2" align="left" class="Verdana12"><?php echo $row_ARARECV['INVNO']; ?></td>
        </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="25" class="Verdana12B">Invoice Date:</td>
        <td height="25" colspan="2" class="Verdana12"><?php echo $row_ARARECV['INVDTE']; ?></td>
        </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="25" class="Verdana12B">Invoice Amount:</td>
        <td align="right" class="Verdana12"><?php echo $row_ARARECV['ITOTAL']; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="25" class="Verdana12B">Payment Amount:</td>
        <td align="right" class="Verdana12"><?php echo $row_ARARECV['AMTPAID']; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="25" class="Verdana12B"><span style="background-color:#FFFF00;">Invoice Balance:</span></td>
        <td align="right" class="Verdana12"><span style="background-color:#FFFF00;"><?php echo $row_ARARECV['IBAL']; ?></span></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="30" class="Verdana12B">Payment Method</td>
        <td align="right" class="Verdana12"><?php echo $row_ARARECV['REFNO']; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="5" colspan="4"></td>
        </tr>
    </table></td>
    <td width="50%" align="center" valign="middle">
    <span class="Verdana12BBlue"><?php  if ($row_ARARECV['IBAL']==0.00 && ($_GET['todo']=='create' || $_GET['todo']=='apply')) {echo "No cash receipt or deposit <br />required for this invoice.";} ?></span>
    <table width="90%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" frame="box" rules="none" <?php  if ($row_ARARECV['IBAL']==0.00 && ($_GET['todo']=='create' || $_GET['todo']=='apply')) {echo "class='hidden'";} ?>>
      <tr>
        <td height="5"></td>
        <td width="10"></td>
        <td height="5"></td>
      </tr>
      <tr>
        <td height="31" align="right" class="Verdana12">Payment Amount:&nbsp;</td>
        <td width="10" align="right" class="Verdana14B"><span <?php if ($_GET['todo']=='create' || $_GET['todo']=='apply'){echo "style='display:none'";} ?>>-</span></td>
        <td height="31" align="left" class="Verdana12B"><input name="amtpaid" type="text" class="Inputright" id="amtpaid"  onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);"  size="10" title="Enter the amount without -" value="<?php if ($_GET['todo']=='apply'){echo $dep2payment;}
 ?>"/></td>
      </tr>
      <tr>
        <td width="116" height="31" align="right" class="Verdana12"><span <?php if ($_GET['todo']=='cancel'){echo "style='display:'";} ?>>Payment Method:&nbsp;</span></td>
        <td width="10" align="right" class="Verdana12">&nbsp;</td>
        <td width="161" height="31" align="left" class="Verdana12">
        <span class="Verdana11BBlue"><?php if ($_GET['todo']=='apply'){echo "APPLYING DEPOSIT";} ?></span>
          <select name="refno" id="refno" <?php if ($_GET['todo']=='apply'){echo "style='display:none'";} ?>>
            <option value="Cash">&nbsp;&nbsp;Cash</option>
            <option value="Cheque" selected="selected">&nbsp;&nbsp;Cheque</option>
            <option value="DCrd">&nbsp;&nbsp;DCrd</option>
            <option value="Visa">&nbsp;&nbsp;Visa</option>
            <option value="MC">&nbsp;&nbsp;M/C</option>
            <option value="Amex">&nbsp;&nbsp;Amex</option>
            <option value="Diners">&nbsp;&nbsp;Diners</option>
            <option value="GE">&nbsp;&nbsp;GE</option>
            <option value="PDC">&nbsp;&nbsp;Post Dated Cheque</option>
            <option value="Pound">&nbsp;&nbsp;Pound</option>
            <option value="Cell">&nbsp;&nbsp;Cell</option>
            <option value="Corrn">&nbsp;&nbsp;Corrn</option>
            <?php if ($_GET['todo']=='cancel') { echo '<option value="NSF Chq">&nbsp;&nbsp;NSF Chq</option>' ;} ?> 
          </select>
        </td>
      </tr>
      <tr>
        <td height="31" align="right" class="Verdana12">Date of Payment:&nbsp;</td>
        <td width="10" align="right" class="Verdana12">&nbsp;</td>
        <td height="31" align="left" class="Verdana12"><input name="minvdte" type="text" class="Input" id="minvdte" value="<?php echo date('m/d/Y'); ?>" size="10" maxlength="12" onclick="ds_sh(this)" onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);" /></td>
      </tr>
      <tr>
        <td height="31" align="right" class="Verdana12">Reason:&nbsp;</td>
        <td width="10" align="right" class="Verdana12">&nbsp;</td>
        <td height="31" align="left" class="Verdana12"><span class="Verdana12B">
          <input name="ponum" type="text" class="Input" id="ponum"  onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);"  size="20" value="<?php echo $row_ARARECV['PONUM']; ?>"/>
        </span></td>
      </tr>
      <tr>
        <td height="31" align="right" class="Verdana12">Staff:&nbsp;</td>
        <td width="10" align="right" class="Verdana12">&nbsp;</td>
        <td height="31" align="left" class="Verdana12">
                    <select name="salesmn" id="salesmn">
      		<?php
			do { echo '<option value="'.$row_Staff['STAFF'].'">'.$row_Staff['STAFF'].'</option>'; 
					} while ($row_Staff = mysql_fetch_assoc($Staff));
			do { echo '<option value="'.$row_Doctor['DOCTOR'].'">'.$row_Doctor['DOCTOR'].'</option>';
					} while ($row_Doctor = mysql_fetch_assoc($Doctor));
			?>
    		</select>		</td>
      </tr>
      <tr>
        <td height="5"></td>
        <td width="10"></td>
        <td height="5"></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="41" align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="ButtonsTable">
    <input name="ok" type="submit" class="button" id="ok" value="OK" <?php if ($_GET['todo']=='cancel' || ($row_ARARECV['IBAL']==0.00 && ($_GET['todo']=='create' || $_GET['todo']=='apply'))) {echo "style='display:none'";} ?>/>
    <input name="ok2" type="submit" class="button" id="ok2" value="OK" <?php if ($_GET['todo']=='create' || $_GET['todo']=='apply'){echo "style='display:none'";} ?>/>
    <input name="button2" type="button" class="button" id="button2" value="CLOSE" onclick="self.close();" /></td>
  </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
