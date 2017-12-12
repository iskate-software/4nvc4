<?php
session_start();
require_once('../../tryconnection.php'); 
include("../../ASSETS/age.php");
//include("../../ASSETS/tax.php");

function nickelround($x,$y)
{
  // The whole thing is done in integers, then converted back to the correct decimals
   $dollars = intval($y) ;
  $pennies = round(($y - $dollars) * 100) ; 
  $dimes = intval($pennies/10) ;
  $diff  = abs(round($pennies - ($dimes * 10)));
  if ($diff < 3) {$penny = 0 ;}  
  else if ($diff < 8) {$penny = 5;} 
  else {$penny = 10;} 
  if ($y > 0) {
    $result = $dollars + $dimes/10 + $penny/100 ;}
  else {
    $result = $dollars + $dimes/10 - $penny/100 ;}

// $result = number_format($result,2);
 return $result ;
}

//EDIT PAYMENT ROUTINE
if (isset($_POST['clear'])){
unset($_SESSION['payments']);
unset($_SESSION['methods']);
unset($_SESSION['onaccount']);
header("Location:PAYMENT_ROUTINE.php");
}
//GO BACK
else if (isset($_POST['goback'])){
unset($_SESSION['payments']);
unset($_SESSION['methods']);
unset($_SESSION['onaccount']);
$wingoback="window.open('FINISH_INVOICE.php','_self');";
}

if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
elseif (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}

mysql_select_db($database_tryconnection, $tryconnection);
$query_DISCOUNT = "SELECT * FROM DISCOUNT ORDER BY DISCID";
$DISCOUNT = mysql_query($query_DISCOUNT, $tryconnection) or die(mysql_error());
// $row_DISCOUNT = mysql_fetch_assoc($DISCOUNT);

$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient' LIMIT 1";
$PATIENT_CLIENT = mysql_query($query_PATIENT_CLIENT, $tryconnection) or die(mysql_error());
$row_PATIENT_CLIENT = mysql_fetch_assoc($PATIENT_CLIENT);

$pdob=$row_PATIENT_CLIENT['PDOB'];
$psex=$row_PATIENT_CLIENT['PSEX'];

$previousbalance=0;

// New logic to recalc the client balance every time, in case of previous errors.
$custno = $_SESSION['client'] ;
$GET_BAL1 = "SELECT SUM(IBAL) AS RECVBL FROM ARARECV WHERE CUSTNO = '$custno' " ;
$QUERY_BAL = mysql_query($GET_BAL1, $tryconnection) or die(mysql_error()) ;
$row_QUERY_BAL = mysql_fetch_assoc($QUERY_BAL) ;

//if (isset($_POST['save']) || isset($_POST['prtsave'])){
//$_SESSION['prevbal'] = $row_PATIENT_CLIENT['BALANCE'];	
if (!empty($row_QUERY_BAL)) {
$_SESSION['prevbal'] = $row_QUERY_BAL['RECVBL'] - $row_PATIENT_CLIENT['CREDIT'];
}
else { 
$_SESSION['prevbal'] =  -$row_PATIENT_CLIENT['CREDIT'];
}

if ($_SESSION['prevbal'] > '0.00' && !isset($_POST['ok2']) &&!isset($_POST['clear']) && !isset($_POST['goback'])){
$balalert="alert('There is a previous balance. You must manually enter the correct amount that the client is paying today.');";
$previousbalance=1;
}


$INVtotal = array();
$GSTtotal = array();
$PSTtotal = array();
$INVdiscount=array();

$calc_disc = 1 ;
//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
foreach ($_SESSION['invline'] as $invtot)
{
	if(($invtot['INVEST']!='1' || $_SESSION['minvno']=='0') && $invtot['INVDECLINE']!='1'){
	if (strpos(strtoupper($invtot['INVDESCR']),'DISCOUNT') !== FALSE ) {$calc_disc = 0 ;}
	$INVtotal[]=round($invtot['INVTOT'],2);
	$GSTtotal[]=round($invtot['INVGST'],2);
	$PSTtotal[]=round($invtot['INVTAX'],2);
	$INVdiscount[]=round($invtot['INVDISC'],2);
	}
}
//SUM UP THE INDIVIDUAL PRICES
$INVtotal=array_sum($INVtotal);
$INVtotal=round($INVtotal,2);

$GSTtotal=array_sum($GSTtotal);
$GSTtotal=round($GSTtotal,2);

$PSTtotal=array_sum($PSTtotal);
$PSTtotal=round($PSTtotal,2);

$INVdiscount=array_sum($INVdiscount);
$INVdiscount=round($INVdiscount,2);

$TOTAL=$INVtotal+$GSTtotal+$PSTtotal-$INVdiscount;
$TOTAL=round($TOTAL,2);

//$TOTAL = nickelround(.05,$TOTAL) ;

// sum up the discounted prices according to method of payment.

$paydisc = array() ;
$xi = 0 ;
while ($row_DISCOUNT = mysql_fetch_assoc($DISCOUNT)) {
 if (strlen($row_DISCOUNT['METHOD'] < 4 )) {$paydisc[$xi][1] = '&nbsp;'.$row_DISCOUNT['METHOD'] ;} else {}$paydisc[$xi][1] = $row_DISCOUNT['METHOD'] ;
//  $paydisc[$xi][1] = $row_DISCOUNT['METHOD'] ;
  if ($calc_disc == 1) {
  $paydisc[$xi][2] = $row_DISCOUNT['PERCENTG'] / 100.00 ;}
  else {
  $paydisc[$xi][2] = 0.00 ;
  }
  $paydisc[$xi][3] = $paydisc[$xi][2]  * $INVtotal  ;
  $paydisc[$xi][4] = $paydisc[$xi][2]  * $GSTtotal ;
  $paydisc[$xi][5] = $paydisc[$xi][2]  * $PSTtotal ;
  $paydisc[$xi][6] = $paydisc[$xi][2]  * $INVdiscount ;
  $paydisc[$xi][7] = round(($INVtotal - $paydisc[$xi][3]),2)   + 
                     round(($GSTtotal - $paydisc[$xi][4]),2) +
                     round(($PSTtotal - $paydisc[$xi][5]),2) -
                     round(($INVdiscount - $paydisc[$xi][6]),2) ;
  $paydisc[$xi][8] = round($TOTAL - $paydisc[$xi][7],2) ;
  // force it to zero on nominal zeros so nickel rounding does not confuse it.
  if ($paydisc[$xi][2] == 0) {$paydisc[$xi][8] = 0.00 ;}
 $xi++ ;
}



//if (round($row_PATIENT_CLIENT['BALANCE'],2) > 0 && round($row_PATIENT_CLIENT['CREDIT'],2) > 0){
//$GrandTOTAL=$row_PATIENT_CLIENT['BALANCE']+$row_PATIENT_CLIENT['CREDIT'];
//}
//else {
$GrandTOTAL=$TOTAL+$_SESSION['prevbal'];
//}


$GrandTOTAL=round($GrandTOTAL,2);

//FILLS OUT THE XPAYMENT & PAYMENT INPUT FIELDS
if ($_SESSION['prevbal'] > '0.00'){
$payment='0';
}
else {
$payment=$GrandTOTAL;
}
$xpayment=$GrandTOTAL;


if (isset($_POST['ok2'])){
	
	if (isset($_SESSION['payments'])){
	$_SESSION['payments'][]=$_POST['payment'];
	$_SESSION['methods'][]=$_POST['paymethod'];			
	}
	else {
	$_SESSION['payments']=array($_POST['payment']);
	$_SESSION['methods']=array($_POST['paymethod']);			
	}
		
		
		if (round($_POST['payment'],2)==0){
		$_SESSION['onaccount']=$GrandTOTAL - array_sum($_SESSION['payments']);	
		$openreason="window.open('REASON.php','_blank','width=310,height=210');";
		$paymentdone=1;
		}
		
		else if (round($_POST['payment'],2)==$GrandTOTAL){	
		$paymentdone=1;
		}
		
		else if (round($_POST['payment'],2)<$GrandTOTAL){
		$payment=$GrandTOTAL - array_sum($_SESSION['payments']);
		$paymentdone=2;
			if (round($payment,2)<=0.00){
			$paymentdone=1;
			}
		}
		
		else if (round($_POST['payment'],2) > round($_POST['xpayment'],2)){
			$paymentdone=1;
		}
}


//CANCEL INVOICE - INSERT INTO REJECTIN
if (isset($_POST['cancel']))
{

$lock_it = "LOCK TABLES INVHOLD WRITE, RECEP WRITE, REJECTIN WRITE, ARCUSTO WRITE" ;  
$Qlock = mysql_query($lock_it, $tryconnection) or die(mysql_error()) ;

$insertSQL="INSERT INTO REJECTIN (REJINV, REJDATE, DATETIME, CUSTNO, PETID, ITOTAL, STAFF, COMPANY) VALUES ($_SESSION[minvno], NOW(), NOW(),'$_SESSION[client]','$_SESSION[patient]','$_POST[itotal]','$_SESSION[staff]','$_POST[company]')";
$execute_Insert = mysql_query($insertSQL, $tryconnection) or die(mysql_error());

//delete from INVHOLD


$deleteSQL = "DELETE FROM INVHOLD WHERE INVCUST='$_SESSION[client]'";
mysql_query($deleteSQL, $tryconnection) or die(mysql_error());
$optimize = "OPTIMIZE TABLE INVHOLD";
mysql_query($optimize, $tryconnection) or die(mysql_error());

//DELETE FROM RECEP FILE
$query_discharge="DELETE FROM RECEP WHERE RFPETID='$_SESSION[patient]'";
$discharge=mysql_query($query_discharge,$tryconnection) or die(mysql_error());
$query_optimize="OPTIMIZE TABLE RECEP ";
$optimize=mysql_query($query_optimize, $tryconnection) or die(mysql_error());

$query_LOCK = "UPDATE ARCUSTO SET LOCKED='0' WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
$LOCK = mysql_query($query_LOCK, $tryconnection) or die(mysql_error());

$unlock_it = "UNLOCK TABLES" ;
$Qunlock = mysql_query($unlock_it, $tryconnection) or die(mysql_error()) ;

//$gobackwin="history.go(-4);";
$query_MVETCANDROP="DROP VIEW IF EXISTS MVETCAN";
$MVETCANDROP=mysql_query($query_MVETCANDROP, $tryconnection) or die(mysql_error());

header("Location:../../CLIENT/CLIENT_PATIENT_FILE.php");
}


include("../../ASSETS/saveinvoice.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PAYMENT ROUTINE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
<?php echo $balalert;
	   echo $wingoback; 
	   echo $openreason; ?>
document.getElementById('inuse').innerText=localStorage.xdatabase;
document.forms[0].payment.focus();
document.forms[0].payment.select();
}

function onaccount(){
if (document.forms[0].payment.value=='0'){
 document.getElementById('onac2').selected=true;
 }
}

function isnotchosen() {
            var how = document.getElementById("oaynebtnetgid").value;

        alert(dop);
    }
function setnewtotal(x,y){
newtotal=x;
document.routine.payment.value=parseFloat(newtotal); 
document.routine.discpaymethod.value=y; 
}

function addbalance(){
	if (document.routine.addprevbal.checked){
	var oldbalance='<?php echo $row_PATIENT_CLIENT['BALANCE']; ?>';
	document.routine.payment.value=parseFloat(newtotal) + parseFloat(oldbalance); 
	}
	else {
	document.routine.payment.value=parseFloat(newtotal); 
	}
}


function changeback(){
var GrandTOTAL = '<?php echo $GrandTOTAL; ?>';
if (document.routine.paymethod[0].selected==true && parseFloat(document.routine.payment.value) > parseFloat(GrandTOTAL)){
	document.getElementById('chgback').style.display='';
	}
else {
	document.getElementById('chgback').style.display='none';
	}
}


function calculatechange(){
var GrandTOTAL = <?php echo $GrandTOTAL; ?>;
var chgback = GrandTOTAL - document.routine.payment.value;
var payment = parseFloat(document.routine.payment.value) + parseFloat(chgback);
document.routine.payment.value = Math.round(parseFloat(payment)*Math.pow(10,2))/Math.pow(10,2);
document.getElementById('changebackrow').style.display='';
document.getElementById('changebacktext').innerText=-Math.round(parseFloat(chgback)*Math.pow(10,2))/Math.pow(10,2);
document.getElementById('chgback').style.display='none';
}

</script>

<style type="text/css">
#table2 {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}

.table {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}

.SelectList {
	width: 70%;
	font-family: "Andale Mono";
	font-size: 12px;
	border-width: 2px;
	border-color:#CCCCCC;
	border-style: double;
	outline-width: 0px;
	background-color:#FFFFFF
}

</style>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>
<!-- InstanceEndEditable -->

<!-- InstanceBeginEditable name="HOME" -->
<div id="LogoHead" onmouseover="document.getElementById(this.id).style.cursor='default';">DVM</div>
<!-- InstanceEndEditable -->

<div id="MenuBar">

	<ul id="navlist">
                
<!--FILE-->                
                
		<li><a href="#" id="current">File</a> 
			<ul id="subnavlist">
                <li><a href="#"><span class="disabled">About DV Manager</span></a></li>
                <li><a onclick=""><span class="disabled">Utilities</span></a></li>
			</ul>
		</li>
                
<!--INVOICE-->                
                
		<li><a href="#" id="current">Invoice</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Casual Sale Invoicing</span></a></li>
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick=""><span class="disabled">Regular Invoicing</span></a><!-- InstanceEndEditable --></li>
                <li><a href="#" onclick=""><span class="disabled">Estimate</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Summary Invoices</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Cash Receipts</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Cancel Invoices</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Comments</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Treatment and Fee File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Worksheet File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Procedure Invoicing File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Invoicing Reports</span></a></li>
			</ul>
		</li>
                
<!--RECEPTION-->                
                
		<li><a href="#" id="current">Reception</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Appointment Scheduling</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Patient Registration</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Using Reception File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Examination Sheets</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Generic Examination Sheets</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Duty Log</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Staff Sign In &amp; Out</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">End of Day Accounting Reports</span></a></li>
                    </ul>
                </li>
                
<!--PATIENT-->                
                
                <li><a href="#" id="current">Patient</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Processing Menu</span></a> </li>
                <li><a href="#" onclick=""><span class="disabled">Review Patient Medical History</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter New Medical History</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter Patient Lab Results</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter Surgical Templates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Create New Client</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Move Patient to a New Client</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Rabies Tags</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Tattoo Numbers</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Certificates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Clinical Logs</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Patient Categorization</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Laboratory Templates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Quick Weight</span></a></li>
<!--                <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
-->			</ul>
		</li>
        
<!--ACCOUNTING-->        
		
        <li><a href="#" id="current">Accounting</a>
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Accounting Reports</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Inventory</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Business Status Report</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Hospital Statistics</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Month End Closing</span></a></li>
			</ul>
		</li>
        
<!--MAILING-->        
		
        <li><a href="#" id="current">Mailing</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Recalls and Searches</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Handouts</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Mailing Log</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Vaccine Efficiency Report</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Referring Clinics and Doctors</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Referral Adjustments</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Labels</span></a></li>
			</ul>
		</li>
	</ul>
</div>
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" -->
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->

<form action="<?php $_SERVER['PHP_SELF']; ?>" name="routine" class="FormDisplay" method="post">

<table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td height="60" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
        </span></td>
        <td width="22%" rowspan="2" valign="middle" align="center" class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>        </td>
        <td width="19%" colspan="2" rowspan="4" align="center"><table width="100%" border="1" cellspacing="0" cellpadding="0" id="table2">
            <tr>
              <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="18" colspan="2" align="center"><span class="Verdana11B" style="background-color:#FFFF00"><?php echo date('m/d/Y'); ?></span></td>
                  </tr>
                  <tr>
                    <td width="41%" height="18" align="right" class="Labels2">        
					<script type="text/javascript">document.write(sessionStorage.custprevbal);</script></td>
                    <td width="59%" height="18" class="Labels2">&nbsp;Balance</td>
                  </tr>
                  <tr>
                    <td height="18" align="right" class="Labels2">
                    <script type="text/javascript">document.write(sessionStorage.custcurbal);</script></td>
                    <td height="18" class="Labels2">&nbsp;Deposit</td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="15" align="left" class="Labels2">        
		<script type="text/javascript">document.write(sessionStorage.custphone);</script></td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
        <td height="15" colspan="2" align="left"  class="Labels2"><span class="Verdana12B" style="background-color:#FFFF00">
		<script type="text/javascript">document.write(sessionStorage.petname);</script>
</span>        <script type="text/javascript">document.write(sessionStorage.desco);</script>         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">            
        <script type="text/javascript">document.write(sessionStorage.desct);</script> (<?php agecalculation($tryconnection,$pdob); ?>)		</td>
      </tr>
    </table>    </td>
    </tr>
    <tr>
      <td height="29" align="center" valign="top" bgcolor="#B1B4FF"></td>
      </tr>
    <tr>
    <td height="308" align="center" valign="top" bgcolor="#B1B4FF">
    <table width="70%" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="table">
      <tr>
        <td colspan="4">
        
        <table width="509" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="38" colspan="5" align="center" class="Verdana14B"><u>PAYMENT</u></td>
            </tr>
          <tr>
            <td width="27" height="20" valign="top">&nbsp;</td>
            <td width="116" height="20" valign="bottom" class="Verdana12">Subtotal</td>
            <td width="90" height="20" align="right" valign="bottom" class="Verdana12">
			<?php echo number_format($INVtotal,2,'.',''); ?>            </td>
            <td width="44" height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
            <td width="232" rowspan="7" align="center" valign="top" class="Verdana11">
            

<div style="position:absolute; width:232px;">


<div id="3" style="position:absolute; top:0px; display:<?php if ($paymentdone==1 || ($GrandTOTAL==0.00 && $TOTAL>0.00)) {echo "none";} else {echo "";}?>; ">
<strong>Select Payment Method</strong>
<select name="paymethod" size="11" class="SelectList" id="paymethod" onchange="changeback()">
<?php $yi = 0 ;
 while ($yi < $xi) {echo 
'<option value=' .'"'.$paydisc[$yi][1].'">&nbsp;&nbsp;' .$paydisc[$yi][1].'&nbsp;&nbsp;' . money_format('%i',$paydisc[$yi][8]).'</option>' ; $yi++; } ?>
<option value="ONAC" id="onac2" onclick="document.routine.payment.value='0';">&nbsp;&nbsp;On Account</option>
<option value="PDC" id="pdc2" onclick="document.routine.payment.value='0';">&nbsp;&nbsp;Post Dated Cheque</option>
<option value="Pound">&nbsp;&nbsp;Pound</option>
    </select>
<br  />
<input type="button" name="chgback" id="chgbac k" class="button" style="width:75px; display:none;" value="CHANGE" title="Calculate change back on cash payments." onclick="calculatechange();"/>
<input type="submit" name="ok2" id="ok2" class="button" style="width:75px;" value="OK" />
</div>

<input type="hidden" name="ponum" id="ponum" value=""  />
<input type="hidden" name="company" id="company" value="<?php echo $row_PATIENT_CLIENT['TITLE']." ".$row_PATIENT_CLIENT['CONTACT']." ".$row_PATIENT_CLIENT['COMPANY']; ?>"  />
<input type="hidden" name="refvet" id="refvet" value="<?php echo $row_PATIENT_CLIENT['REFVET']; ?>"  />
<input type="hidden" name="refclin" id="refclin" value="<?php echo $row_PATIENT_CLIENT['REFCLIN']; ?>"  />



<div id="4" style="position:absolute; top:0px; z-index:1; display:<?php if ($paymentdone==1 || ($GrandTOTAL==0.00 && $TOTAL>0.00)){echo "";} else {echo "none";} ?>">
<br  />
<br  />
<input class="button" type="submit" name="clear" id="clear" value="EDIT PAYMENT ROUTINE" style="width:180px;"/>
<br  />
<br  />
<input class="button" type="submit" name="save" id="save" value="SAVE INVOICE" onclick="document.getElementById('save').disabled.value=true;" style="width:180px;"/>
<br  />
<br  />
<input class="button" type="submit" name="prtsave" id="prtsave" value="PRINT & SAVE INVOICE" onclick="document.getElementById('prtsave').disabled.value=true; window.open('PRINT_INVOICE.php','_parent')" style="width:180px;"/>
<br  />
<br  />
</div>
    </div>    </td>
          </tr>
          <tr>
            <td height="20" valign="top">&nbsp;</td>
            <td height="20" valign="bottom" class="Verdana12">GST<?php //taxname($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?></td>
            <td height="20" align="right" valign="bottom" class="Verdana12">
            <?php echo number_format($GSTtotal,2,'.',''); ?>			</td>
            <td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
            </tr>
          <tr>
            <td height="20" valign="top">&nbsp;</td>
            <td height="20" valign="bottom" class="Verdana12">PST</td>
            <td height="20" align="right" valign="bottom" class="Verdana12">
            <?php echo number_format($PSTtotal,2,'.',''); ?>			</td>
            <td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
            </tr>
          <tr>
            <td height="20" valign="top">&nbsp;</td>
            <td height="20" valign="bottom" class="Verdana12">Total</td>
            <td height="20" align="right" valign="bottom" class="Verdana12"><hr noshade="noshade" size="1" color="#000000"  />
			<?php echo number_format($TOTAL,2,'.',''); ?>			</td>
            <td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
            </tr>
          <tr>
            <td height="20" valign="top">&nbsp;</td>
            <td height="20" valign="bottom" class="Verdana12"><span <?php if ($row_PATIENT_CLIENT['BALANCE']!='0.00'){echo "class='Verdana12BRed'";/*style='background-color:#FFFF00'*/} ?>>Previous Balance</span></td>
            <td height="20" align="right" valign="bottom" class="Verdana12"><span <?php if ($row_PATIENT_CLIENT['BALANCE']!='0.00'){echo "class='Verdana12BRed'";} ?>><?php echo number_format($row_PATIENT_CLIENT['BALANCE']+$row_PATIENT_CLIENT['CREDIT'],2,'.',''); ?></span></td
            ><td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
            </tr>
          <tr <?php if ($row_PATIENT_CLIENT['BALANCE']!='0.00'){echo "style='display:'";} else {echo "style='display:none'";} ?>>
            <td height="20" valign="top">&nbsp;</td>
            <td height="20" valign="bottom" class="Verdana12"><span <?php if ($row_PATIENT_CLIENT['CREDIT']!='0.00'){echo "class='Verdana12BBlue'";/*style='background-color:#FFFF00'*/} ?>>Credit</span></td>
            <td height="20" align="right" valign="bottom" class="Verdana12"><span <?php if ($row_PATIENT_CLIENT['CREDIT']!='0.00'){echo "class='Verdana12BBlue'";} ?>><?php echo number_format(-$row_PATIENT_CLIENT['CREDIT'],2,'.',''); ?></span></td
            ><td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
            </tr>
            <td height="20" valign="top"></td>
            <td height="20" valign="bottom"><span class="Verdana12B" <?php if ($row_PATIENT_CLIENT['BALANCE']!='0.00'){echo "class='Verdana12B' style='background-color:#FFFF00'";} ?>>Grand Total</span></td>
            <td height="20" align="right" valign="bottom" class="Verdana12B"><span <?php if ($row_PATIENT_CLIENT['BALANCE']!='0.00'){echo "class='Verdana12B' style='background-color:#FFFF00'";} ?>>
            <?php echo number_format($GrandTOTAL,2,'.',''); ?>
            </span>            </td>
            <td height="20" align="right" valign="bottom" class="Verdana12B">&nbsp;</td>
            </tr>

          <tr style="display:<?php if ($GrandTOTAL==0.00  && $TOTAL > 0.00){echo "none";} else {echo "";}//if ($GrandTOTAL>0.00  || $TOTAL < 0.00){echo "";} else {echo "none";} ?>">
            <td height="20" valign="top">&nbsp;</td>
            <td height="20" valign="bottom" class="Verdana12">Payment <?php if ($paymentdone==1){echo "Total";} ?></td>
            <td height="20" align="right" valign="bottom" class="Verdana12"><hr noshade="noshade" size="1" color="#000000"  />
            <input type="hidden" value="<?php echo number_format($xpayment,2,'.',''); ?>" name="xpayment" id="xpayment" size="6"/>
            <input type="text" name="payment" id="payment" value="<?php echo number_format($payment,2,'.',''); ?>" size="8" class="Inputright" onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);" onkeyup="onaccount();" style="display:<?php if ($paymentdone==1){echo "none";} else {echo "";} ?>"/>          <?php if ($paymentdone==1){echo number_format(array_sum($_SESSION['payments']),2);} else {echo "";} ?>            </td>
            <td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
            </tr>

          <tr style="display:<?php if ($GrandTOTAL==0.00 && $TOTAL > 0.00){echo "";} else {echo "none";} ?>">
            <td height="20" valign="bottom" align="center" class="Verdana12BBlue" colspan="4"><br  />No payment required.</td>
          </tr>

          <tr>
            <td height="2" valign="top">&nbsp;</td>
            <td height="2" align="right" valign="bottom" colspan="2"><hr noshade="noshade" size="1" color="#000000"  /></td>
            <td height="2" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
          </tr>

          <tr id="changebackrow" style="display:none;">
            <td height="20">&nbsp;</td>
            <td align="left" valign="middle" class="Verdana12Blue">Change Back</td>
            <td align="right" valign="middle" class="Verdana12Blue"><span id="changebacktext"></span></td>
            <td>&nbsp;</td>
          </tr>

          <tr style="display:none<?php //if ($GrandTOTAL==0.00 && $TOTAL > 0.00){echo "";} else {echo "none";} ?>">
            <td height="20" valign="bottom" align="center" class="Verdana11Grey" colspan="4"><br  />If you wish to record a deposit <br  />or cancel a cash receipt please go to<br  /> 'Invoice'-'Cash Receipts'.</td>
            </tr>


<?php if (!empty($_SESSION['payments'])){ 

		foreach ($_SESSION['payments'] as $key => $value) {
			if ($_SESSION['methods'][$key]=='ONAC'){
			$value=$_SESSION['onaccount'];
			} 
		echo      '<tr class="Verdana11">
					<td height="15">&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right">'.number_format($value,2,'.','').'</td>
					<td align="left" colspan="2">&nbsp;';
			if ($_SESSION['methods'][$key]=='ONAC'){echo "On Account";}
			else {echo $_SESSION['methods'][$key];}
					
		echo		'</td>
				  </tr>';
		  }
} ?>
          <tr>
            <td height="20" valign="top">&nbsp;</td>
            <td height="20" valign="top" class="Verdana12"></td>
            <td height="20" valign="top" class="Verdana12">&nbsp;</td>
            <td height="20" valign="top" class="Verdana12">&nbsp;</td>
            <td height="20" align="center" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td height="20" valign="top">&nbsp;</td>
            <td height="20" valign="top" class="Verdana12">&nbsp;</td>
            <td height="20" valign="top" class="Verdana12">&nbsp;</td>
            <td height="20" valign="top" class="Verdana12"></td>
            <td height="20" align="center" valign="top"></td>
          </tr>
          <tr>
            <td height="20" valign="top" class="Verdana12" colspan="5">            </td>
          </tr>
        </table>        </td>
        </tr>
    </table>    </td>
    </tr>
    <tr>
      <td align="center" valign="top" bgcolor="#B1B4FF"><input name="preview" class="button" type="button" value="PREVIEW" onclick="window.open('../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW.php?preview=PREVIEW','_blank','width=785,height=670')" style="width:110px;"/>
    <input name="cancel2" class="hidden" type="button" value="CANCEL" onclick="confirmation()"  style="width:110px;"/>
      <input class="button" type="submit" name="goback" id="goback" value="GO BACK" style="width:110px;" /></td>
      </tr>
</table>

</form>

<form action="" name="cancelinvoice" method="post">
<input type="hidden" name="cancel" value="1" />
<input type="hidden" name="itotal" value="<?php $INVtotal = array();foreach ($_SESSION['invline'] as $invtot){$INVtotal[]=$invtot['INVTOT'];}echo array_sum($INVtotal);?>" /> 
<!-- OTHER FOR REJECTED INVOICE -->
<input type="hidden" name="rejdate" value="<?php echo date("Y/m/d"); ?>"/>
<input type="hidden" name="company" value="<?php echo $row_PATIENT_CLIENT['TITLE'].' '.$row_PATIENT_CLIENT['CONTACT'].' '.$row_PATIENT_CLIENT['COMPANY']; ?>"/>
</form>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
