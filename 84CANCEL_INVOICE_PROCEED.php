<?php 
session_start();
require_once('../../tryconnection.php');
require_once('../../ASSETS/tax.php');

$invno=$_GET['invno'];
$unique1=$_GET['unique1'] ;
mysqli_select_db($tryconnection, $database_tryconnection);
$query_Staff = "SELECT STAFF, SIGNEDIN,PRIORITY FROM STAFF WHERE SIGNEDIN=1 ORDER BY PRIORITY";
$Staff = mysqli_query($tryconnection, $query_Staff) or die(mysqli_error($mysqli_link));
$row_Staff = mysqli_fetch_assoc($Staff);

$query_Doctor = "SELECT DOCTOR, SIGNEDIN, PRIORITY FROM DOCTOR WHERE SIGNEDIN=1 ORDER BY PRIORITY";
$Doctor = mysqli_query($tryconnection, $query_Doctor) or die(mysqli_error($mysqli_link));
$row_Doctor = mysqli_fetch_assoc($Doctor);

$query_ARARECV = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE FROM ARARECV WHERE UNIQUE1='$unique1' AND CUSTNO='$_SESSION[client]' LIMIT 1";
$ARARECV = mysqli_query($tryconnection, $query_ARARECV) or die(mysqli_error($mysqli_link));
$row_ARARECV = mysqli_fetch_assoc($ARARECV);


//OK TO CANCEL INVOICE
if (isset($_POST['ok'])){

$invdte=$_POST['minvdte'].' '.date('H:i:s');

//unpaid invoice (non-zero balance) - for all months. If PARTIALLY paid, this process simply cancels the unpaid component.
//UPDATE ARCUSTO
$adjust = $row_ARARECV['IBAL'] ;
$update_ARCUSTO = "UPDATE ARCUSTO SET BALANCE=BALANCE-'$adjust' WHERE CUSTNO='$_SESSION[client]' LIMIT 1";
$RESULT = mysqli_query($tryconnection, $update_ARCUSTO) or die(mysqli_error($mysqli_link));
//UPDATE ARIVOI IF IT IS IN THE CURRENT MONTH -> if the unpaid invoice is from previous month, copy the record from ARRECV to ARINVOI with negative value
$query_ARINVOIX = "SELECT * FROM ARINVOI WHERE UNIQUE1='$unique1' AND CUSTNO='$_SESSION[client]' LIMIT 1";
$ARINVOIX = mysqli_query($tryconnection, $query_ARINVOIX) or die(mysqli_error($mysqli_link));
$row_ARINVOIX = mysqli_fetch_assoc($ARINVOIX);
$ponum="CANC. ".$row_ARARECV['ITOTAL'];
$refno = ' '; 
$invno = $row_ARARECV['INVNO'] ;

	//if the invoice is not there, just copy the ARARECV into ARINVOI reversing the signs as we need a negative invoice to offset the old one.
	// To allow for partially paid invoices, if the user has not already cancelled the payment, just take the unpaid portion of the receivable.	if (empty($row_ARINVOI)){
if (empty($row_ARINVOIX)){
	$query_ARINVOI = "INSERT INTO ARINVOI (INVNO, INVDTE, INVTIME, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, DATETIME, UNIQUE1)
	 SELECT INVNO, INVDTE, INVTIME, CUSTNO, '".mysqli_real_escape_string($mysqli_link, $row_ARARECV['COMPANY'])."', SALESMN,  '".mysqli_real_escape_string($mysqli_link, $row_ARARECV['PONUM'])."', 
	 REFNO, DTEPAID, 0 - TAX, 0 - PTAX, 0 - ITOTAL, 0- DISCOUNT, 0 - AMTPAID, 0 - IBAL, DATETIME, UNIQUE1 FROM ARARECV 
	 WHERE UNIQUE1='$unique1' AND CUSTNO='$_SESSION[client]' LIMIT 1";	 
	 $ARINVOI = mysqli_query($tryconnection, $query_ARINVOI) or die(mysqli_error($mysqli_link));
	
	}
	//if the invoice exists in the current ARINVOI, update the record by zeroing ITOTAL
	else {
	 $update_ARINVOI = "UPDATE ARINVOI SET PONUM='".mysqli_real_escape_string($mysqli_link, $ponum)."', TAX=0.00, PTAX=0.00, AMTPAID=0.00, IBAL=0.00, ITOTAL=0.00 
	                   WHERE UNIQUE1='$unique1' AND CUSTNO='$_SESSION[client]' LIMIT 1";
	 $RESULT = mysqli_query($tryconnection, $update_ARINVOI) or die(mysqli_error($mysqli_link));	
	}


//FULLY paid invoice - will be only for the current month. Generate offsetting cash record for the total amount paid
//                     over the life of the receivable.
if ($row_ARARECV['IBAL']==0.00){
//If it was paid with split payments, too bad. There will only be one offsetting payment created for the total of what was paid, using the last-in method of payment.
$insert_ARCASHR = "INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, AMTPAID, DTEPAID) 
                VALUES ('$row_ARARECV[INVNO]', STR_TO_DATE('$row_ARARECV[INVDTE]', '%m/%d/%Y %H:%i:%s'), '".mysqli_real_escape_string($mysqli_link, $row_ARARECV['CUSTNO'])."',
                '".mysqli_real_escape_string($mysqli_link, $row_ARARECV['COMPANY'])."', '".mysqli_real_escape_string($mysqli_link, $_POST['salesmn'])."',
                '".mysqli_real_escape_string($mysqli_link, $_POST['ponum'])."', '$row_ARARECV[REFNO]', '-$row_ARARECV[AMTPAID]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'))  ";
//    $RESULT = mysql_query($insert_ARCASHR, $tryconnection) or die(mysql_error());
	$update_ARINVOI = "UPDATE ARINVOI SET PONUM='$ponum', TAX=0.00, PTAX=0.00, AMTPAID=0.00, IBAL=0.00, ITOTAL=0.00 WHERE UNIQUE1='$unique1' LIMIT 1";
	$RESULT1 = mysqli_query($tryconnection, $update_ARINVOI) or die(mysqli_error($mysqli_link));	
}

//SALESCAT
 $query_SALESCAT = "SELECT INVNO FROM SALESCAT WHERE UNIQUE1='$unique1' LIMIT 1";
 $SALESCAT = mysqli_query($tryconnection, $query_SALESCAT) or die(mysqli_error($mysqli_link));
 $row_SALESCAT = mysqli_fetch_assoc($SALESCAT);
if (!empty($row_SALESCAT)){
//if it is CURRENT MONTH -> zero everything for this invoice
 $update_SALESCAT="UPDATE SALESCAT SET INVTOT=0.00 WHERE UNIQUE1='$unique1' AND INVCUST ='$_SESSION[client]'";
 $SALESCAT = mysqli_query($tryconnection, $update_SALESCAT) or die(mysqli_error($mysqli_link));
}

else {

// IF IT IS !!!NOT!!! IN THE CURRENT MONTH -> create 2 new rows in SALESCAT - first is -(ITOTAL -GST - PTAX) (The pre-tax amount) with INVMAJ=97 and;
// the second is for GST: -GST with INVMAJ=90 (if there was pst as well, a third record is required, INVMAJ = 92).
  $totalwas = - ($row_ARARECV['ITOTAL'] - $row_ARARECV['TAX'] - $row_ARECV['PTAX']) ;
  $taxwas = - ($row_ARARECV['TAX'] ) ;
  $ptaxwas = - ($row_ARARECV['PTAX']) ;
  
 $insert_SALESCAT="INSERT INTO SALESCAT (INVMAJ, INVREVCAT, INVTOT, INVDOC, INVDESC, INVDTE, INVNO, INVCUST, UNIQUE1) VALUES ('97', '97', '$totalwas',
          '".mysqli_real_escape_string($mysqli_link, $_POST['salesmn'])."', 'CANCELLED' , STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'),  '$invno', '$row_ARARECV[CUSTNO]',  '$unique1')"; 
 $SALESCAT2 = mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));
 $insert_SALESCAT2="INSERT INTO SALESCAT (INVMAJ, INVREVCAT, INVTOT, INVDOC, INVDESC, INVDTE, INVNO, INVCUST,  UNIQUE1) VALUES ('90','90', '$taxwas', 
          '".mysqli_real_escape_string($mysqli_link, $_POST['salesmn'])."', 'CANCELLED', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'),  '$invno', '$row_ARARECV[CUSTNO]', '$unique1')";
 $SALESCAT3 = mysqli_query($tryconnection, $insert_SALESCAT2) or die(mysqli_error($mysqli_link));
  if ($row_ARARECV['PTAX'] != 0) {
    $insert_SALESCAT3="INSERT INTO SALESCAT (INVMAJ, INVREVCAT, INVTOT, INVDOC, INVDESC, INVDTE, INVNO, INVCUST,  UNIQUE1) VALUES ('92','92', '$ptaxwas' , 
          '".mysqli_real_escape_string($mysqli_link, $_POST['salesmn'])."', 'CANCELLED', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'),  '$invno', '$row_ARARECV[CUSTNO]', '$unique1')";
    $SALESCAT4 = mysqli_query($tryconnection, $insert_SALESCAT3) or die(mysqli_error($mysqli_link)) ;
    
  }
}
//UPDATE RECEIVABLES
if ($row_ARARECV['AMTPAID']!=0.00){

//If it was paid with split payments, too bad. There will only be one offsetting payment created for the total of what was paid, using the last-in method of payment.
$insert_ARCASHR = "INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, AMTPAID, DTEPAID) 
                VALUES ('$row_ARARECV[INVNO]', STR_TO_DATE('$row_ARARECV[INVDTE]', '%m/%d/%Y %H:%i:%s'), '".mysqli_real_escape_string($mysqli_link, $row_ARARECV['CUSTNO'])."',
                '".mysqli_real_escape_string($mysqli_link, $row_ARARECV['COMPANY'])."', '".mysqli_real_escape_string($mysqli_link, $_POST['salesmn'])."',
                '".mysqli_real_escape_string($mysqli_link, $_POST['ponum'])."', '$row_ARARECV[REFNO]', '-$row_ARARECV[AMTPAID]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'))  ";
    $RESULT = mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
}
$update_ARARECV = "UPDATE ARARECV SET SALESMN='".mysqli_real_escape_string($mysqli_link, $_POST['salesmn'])."', PONUM='CANC.', REFNO='$_POST[refno]', AMTPAID=0.00, DTEPAID=STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'), IBAL=0.00, TAX = 0, PTAX = 0, ITOTAL=0.00 WHERE UNIQUE1='$unique1' AND CUSTNO='$_SESSION[client]' LIMIT 1";
$RESULT = mysqli_query($tryconnection, $update_ARARECV) or die(mysqli_error($mysqli_link));

$closewin="opener.document.location.reload(); self.close();";

}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PROCEED WITH CANCELLING INVOICE</title>
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
window.resizeTo(700,400) ;
document.cancinv.amtpaid.focus();
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function bodyonunload()
{

}

</script>

<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="cancinv" id="cancinv" style="position:absolute; top:0px; left:0px;">

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
        <td height="25" class="Verdana12B">Invoice HST:</td>
        <td align="right" class="Verdana12"><?php echo $row_ARARECV['TAX']; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="25" class="Verdana12B">Invoice PST:</td>
        <td align="right" class="Verdana12"><?php echo $row_ARARECV['PTAX']; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="25" class="Verdana12B"><span style="background-color:#FFFF00;">Payment Amount:</span></td>
        <td align="right" class="Verdana12"><span style="background-color:#FFFF00;"><?php echo $row_ARARECV['AMTPAID']; ?></span></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td class="Verdana12B">&nbsp;</td>
        <td height="25" class="Verdana12B">Invoice Balance:</td>
        <td align="right" class="Verdana12"><?php echo $row_ARARECV['IBAL']; ?></td>
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
    <td width="50%" align="center">
    <table width="90%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" frame="box" rules="none" >
      <tr>
        <td height="5"></td>
        <td width="8"></td>
        <td height="5"></td>
      </tr>
      <tr>
        <td height="31" align="right" class="Verdana12"></td>
        <td width="8" align="right" class="Verdana14B"></td>
        <td height="31" align="left" class="Verdana12B"></td>
      </tr>
      <tr>
        <td width="141" height="31" align="right" class="Verdana12"><span "style='display:none'">&nbsp;</span></td>
        <td width="8" align="right" class="Verdana12">&nbsp;</td>
        <td width="170" height="31" align="left" class="Verdana12" "style='display:none'"><span class="Verdana11">
        </span></td>
      </tr>
      <tr>
        <td height="31" align="right" class="Verdana12">Date of Cancellation:</td>
        <td width="8" align="right" class="Verdana12">&nbsp;</td>
        <td height="31" align="left" class="Verdana12"><input name="minvdte" type="text" class="Input" id="minvdte" value="<?php echo date('m/d/Y'); ?>" size="10" maxlength="12" onclick="ds_sh(this)" onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);" /></td>
      </tr>
      <tr>
        <td height="31" align="right" class="hidden">Reason:&nbsp;</td>
        <td width="8" align="right" class="Verdana12">&nbsp;</td>
        <td height="31" align="left" class="Verdana12"><span class="Verdana12B">
          <input name="ponum" type="hidden" class="Input" id="ponum"  onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);"  size="20" value="<?php echo $row_ARARECV['PONUM']; ?>"/>
        </span></td>
      </tr>
      <tr>
        <td height="31" align="right" class="Verdana12">Staff:&nbsp;</td>
        <td width="8" align="right" class="Verdana12">&nbsp;</td>
  <td height="31" align="left" class="Verdana12">
                    <select name="salesmn" id="salesmn">
      		<?php
			do { echo '<option value="'.$row_Staff['STAFF'].'">'.$row_Staff['STAFF'].'</option>'; 
					} while ($row_Staff = mysqli_fetch_assoc($Staff));
			do { echo '<option value="'.$row_Doctor['DOCTOR'].'">'.$row_Doctor['DOCTOR'].'</option>';
					} while ($row_Doctor = mysqli_fetch_assoc($Doctor));
			?>
    		</select>		</td>
      </tr>
      <tr>
        <td height="5"></td>
        <td width="8"></td>
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
    <input name="ok" type="submit" class="button" id="ok" value="OK" />
    <input name="button2" type="button" class="button" id="button2" value="CLOSE" onclick="self.close();" />
    </td>
  </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
