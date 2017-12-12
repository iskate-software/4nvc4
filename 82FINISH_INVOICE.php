<?php 
session_start();
require_once('../../tryconnection.php'); 
include("../../ASSETS/age.php");
include("../../ASSETS/tax.php");

if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
elseif (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}

if (isset($_GET['client'])){
$client=$_GET['client'];
$_SESSION['client']=$_GET['client'];
}
elseif (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}

$taxname=taxname($database_tryconnection, $tryconnection, $_SESSION['minvdte']);


mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient' LIMIT 1";
$PATIENT_CLIENT = mysql_query($query_PATIENT_CLIENT, $tryconnection) or die(mysql_error());
$row_PATIENT_CLIENT = mysql_fetch_assoc($PATIENT_CLIENT);
$pdob=$row_PATIENT_CLIENT['PDOB'];
$psex=$row_PATIENT_CLIENT['PSEX'];

function findnonestimate($var){
return($var['INVEST']=='0');
}
$foundnonestimate=(array_filter($_SESSION['invline'], 'findnonestimate'));
$howmany=count($foundnonestimate);

/////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['anotherpet'])){
$_SESSION['round']='1';
//unlock the client
$query_LOCK = "UPDATE ARCUSTO SET LOCKED='0' WHERE CUSTNO = '$client' LIMIT 1";
$LOCK = mysql_query($query_LOCK, $tryconnection) or die(mysql_error());
$openwindow="document.location='../../CLIENT/CLIENT_PATIENT_FILE.php';";
}

//RESERVE INVOICE - INSERT INTO INVHOLD 
else if (isset($_POST['reserve'])) {
unset($_SESSION['round']);

//delete from INVHOLD
$query_lockc = "LOCK TABLES INVHOLD WRITE, PETHOLD WRITE, ARCUSTO WRITE" ;
$get_it = mysql_query($query_lockc, $tryconnection) or die(mysql_error()) ;
$deleteSQL = "DELETE  FROM INVHOLD WHERE INVCUST='$_SESSION[client]'";
mysql_query($deleteSQL, $tryconnection);
$optimize = "OPTIMIZE TABLE INVHOLD";
mysql_query($optimize, $tryconnection) or die(mysql_error()) ;

//update PETHOLD invno
$query_INVNO_PETHOLD = "UPDATE PETHOLD SET PHINVNO='$_SESSION[minvno]' WHERE PHPETID='$_SESSION[patient]'";
$INVNO_PETHOLD = mysql_query($query_INVNO_PETHOLD, $tryconnection) or die(mysql_error());

//unlock the client
$query_LOCK = "UPDATE ARCUSTO SET LOCKED='0' WHERE CUSTNO = '$client' LIMIT 1";
$LOCK = mysql_query($query_LOCK, $tryconnection) or die(mysql_error());

$query_unlockc = "UNLOCK TABLES" ;
$let_it_go = mysql_query($query_unlockc, $tryconnection) or die(mysql_error()) ;

//check if there is any such patient in the RECEP
$query_RECEP = "SELECT * FROM RECEP WHERE RFPETID='$patient' LIMIT 1";
$RECEP = mysql_query($query_RECEP, $tryconnection) or die(mysql_error());
$row_RECEP = mysql_fetch_assoc($RECEP);

//if there is NO such patient, insert into the DISCHARGED section
if (empty($row_RECEP)){
$query_insertSQL="INSERT INTO RECEP (CUSTNO, NAME, RFPETID, PETNAME, PSEX, RFPETTYPE, LOCATION, DESCRIP, FNAME, AREA1, PH1, AREA2, PH2, AREA3, PH3, DATEIN, TIME, DATETIME) VALUES ('$client', '".mysql_real_escape_string($row_PATIENT_CLIENT['COMPANY'])."', '$patient', '".mysql_real_escape_string($row_PATIENT_CLIENT['PETNAME'])."', '$row_PATIENT_CLIENT[PSEX]', '$row_PATIENT_CLIENT[PETTYPE]', '2', '".mysql_real_escape_string($row_PATIENT_CLIENT['PETBREED'])."','".mysql_real_escape_string($row_PATIENT_CLIENT['CONTACT'])."','$row_PATIENT_CLIENT[AREA]','$row_PATIENT_CLIENT[PHONE]','$row_PATIENT_CLIENT[CAREA2]','$row_PATIENT_CLIENT[PHONE2]','$row_PATIENT_CLIENT[CAREA3]','$row_PATIENT_CLIENT[PHONE3]', STR_TO_DATE('$_SESSION[minvdte]','%m/%d/%Y'), NOW(), NOW())";
$insertSQL=mysql_query($query_insertSQL,$tryconnection) or die(mysql_error());
}
else {
//move to discharged within the reception file
$query_admit="UPDATE RECEP SET LOCATION='3' WHERE RFPETID='$patient' LIMIT 1";
$admit=mysql_query($query_admit,$tryconnection) or die(mysql_error());
}

$query_xnow="SELECT NOW()";
$xnow= mysql_unbuffered_query($query_xnow, $tryconnection) or die(mysql_error());
$row_xnow=mysql_fetch_array($xnow);

//if there is at least ONE item that is NOT an estimate, insert into invhold
if ($howmany!=0){
 $iseq = 0 ;
 $query_lockc = "LOCK TABLES INVHOLD WRITE" ;
 $get_it = mysql_query($query_lockc, $tryconnection) or die(mysql_error()) ;
$insertSQL = sprintf("INSERT INTO INVHOLD (INVNO, ISORTCODE, INVCUST, INVPET, INVDESCR, DATETIME, PETNAME) VALUES ('%s','%s','%s', '%s', '%s', '%s', '%s')",
 							  $_SESSION['invline'][0]['INVNO'],
 							  $iseq,
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  "1",
							  $row_xnow[0],
							  mysql_real_escape_string($_SESSION['invline'][0]['PETNAME'])
							  );
mysql_query($insertSQL, $tryconnection) or die(mysql_error());
                              $iseq++ ;

foreach ($foundnonestimate as $item) {
//format the date into the mysql format
$query_invdatetime="SELECT STR_TO_DATE('$item[INVDATETIME]','%m/%d/%Y %H:%i:%s')";
$invdatetime= mysql_unbuffered_query($query_invdatetime, $tryconnection) or die(mysql_error());
$row_invdatetime=mysql_fetch_array($invdatetime);

$insertSQL2 = sprintf("INSERT INTO INVHOLD (INVNO,ISORTCODE, INVCUST, INVPET, INVDATETIME, INVMAJ, INVMIN, INVDOC, INVSTAFF, INVUNITS, INVDESCR, 
INVPRICE, INVTOT, INVINCM, INVDISC, INVLGSM, INVREVCAT, INVGST, INVTAX, REFCLIN, REFVET, INVUPDTE, INVFLAGS, INVDISP, INVGET, INVPERCNT, 
INVHYPE, AUTOCOMM, INVCOMM, HISTCOMM, MODICODE, INVNARC, INVVPC, INVUPRICE, INVPKGQTY, DATETIME, MEMO, INARCLOG, IRADLOG, ISURGLOG, IUAC, 
INVSERUM, INVEST, INVDECLINE, PETNAME, INVOICECOMMENT, INVPRU, XDISC, MTAXRATE, TUNITS, TFLOAT, INVSTAT, TENTER, LCODE, LCOMMENT, INVNOHST, 
INVPAYDISC, INVHXCAT) 
VALUES ('%s','%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s', 
'%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
 							  $item['INVNO'],
 							  $iseq,
							  $item['INVCUST'],
							  $item['INVPET'],
							  $row_invdatetime[0],
							  $item['INVMAJ'],
							  $item['INVMIN'],
							  mysql_real_escape_string($item['INVDOC']),
							  mysql_real_escape_string($item['INVSTAFF']),
							  $item['INVUNITS'],
							  mysql_real_escape_string($item['INVDESCR']),
							  $item['INVPRICE'],
							  $item['INVTOT'],
							  $item['INVINCM'],
							  $item['INVDISC'],
							  $item['INVLGSM'],
							  $item['INVREVCAT'],
							  $item['INVGST'],
							  $item['INVTAX'],
							  mysql_real_escape_string($item['REFCLIN']),
							  mysql_real_escape_string($item['REFVET']),
							  $item['INVUPDTE'],
							  $item['INVFLAGS'],
							  $item['INVDISP'],
							  $item['INVGET'],
							  $item['INVPERCNT'],
							  mysql_real_escape_string($item['INVHYPE']),
							  mysql_real_escape_string($item['AUTOCOMM']),
							  $item['INVCOMM'],
							  $item['HISTCOMM'],
							  $item['MODICODE'],
							  $item['INVNARC'],
							  $item['INVVPC'],
							  $item['INVUPRICE'],
							  $item['INVPKGQTY'],
							  $row_xnow[0],
							  $item['MEMO'],
							  $item['INARCLOG'],
							  $item['IRADLOG'],
							  $item['ISURGLOG'],
							  $item['IUAC'],
							  $item['INVSERUM'],
							  $item['INVEST'],
							  $item['INVDECLINE'],
							  mysql_real_escape_string($item['PETNAME']),
							  mysql_real_escape_string($item['INVOICECOMMENT']),
							  $item['INVPRU'],
							  $item['XDISC'],
							  $item['MTAXRATE'],
							  $item['TUNITS'],
							  $item['TFLOAT'],
							  $item['INVSTAT'],
							  $item['TENTER'],
							  mysql_real_escape_string($item['LCODE']),
							  mysql_real_escape_string($item['LCOMMENT']),
							  $item['INVNOHST'],
							  $item['INVPAYDISC'],
							  $item['INVHXCAT']
							  );
mysql_query($insertSQL2, $tryconnection) or die(mysql_error());
		                      $iseq++ ;
		}
		
 $insertSQL3 = sprintf("INSERT INTO INVHOLD (INVNO, ISORTCODE,INVCUST, INVPET, INVDESCR, INVTOT, DATETIME, PETNAME) VALUES ('%s','%s','%s', '%s', '%s', '%s', '%s', '%s')",
 							  $_SESSION['invline'][0]['INVNO'],
 							  $iseq,
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  substr($taxname,0,3),
							  $_POST['xgst'],
							  $row_xnow[0],
							  mysql_real_escape_string($_SESSION['invline'][0]['PETNAME'])
							  );
mysql_query($insertSQL3, $tryconnection) or die(mysql_error());
                              $iseq++ ;
 $insertSQL4 = sprintf("INSERT INTO INVHOLD (INVNO, ISORTCODE,INVCUST, INVPET, INVDESCR, INVTOT, DATETIME, PETNAME) VALUES ('%s','%s','%s', '%s', '%s', '%s', '%s', '%s')",
 							  $_SESSION['invline'][0]['INVNO'],
 							  $iseq,
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  "PST",
							  $_POST['xpst'],
							  $row_xnow[0],
							  mysql_real_escape_string($_SESSION['invline'][0]['PETNAME'])
							  );
mysql_query($insertSQL4, $tryconnection) or die(mysql_error());
		                      $iseq++ ;

 $insertSQL5 = sprintf("INSERT INTO INVHOLD (INVNO, ISORTCODE,INVCUST, INVPET, INVDESCR, INVTOT, DATETIME, PETNAME) VALUES ('%s','%s','%s', '%s', '%s', '%s', '%s', '%s')",
 							  $_SESSION['invline'][0]['INVNO'],
 							  $iseq,
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  "TOTAL",
							  $_POST['xtotal'],
							  $row_xnow[0],
							  mysql_real_escape_string($_SESSION['invline'][0]['PETNAME'])
							  );
  mysql_query($insertSQL5, $tryconnection) or die(mysql_error());
}
  $query_unlockc = "UNLOCK TABLES" ;
  $let_it_go = mysql_query($query_unlockc, $tryconnection) or die(mysql_error()) ;
//if the number of nonestimate items = the number of invline items, go straight to the confirmation screen
if ($howmany == count($_SESSION['invline'])){
header("Location:PRINT_INVOICE.php");
}
//if there is at least one estimate item, the above condition won't be true and it will open the EST_NAME screen
else {
$estimatename="window.open('EST_NAME.php','_blank','width=400,height=270');";
}
}
///////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['reserve'])) {
 unset($_SESSION['invline']) ;
}
else if (isset($_POST['finish'])) {
unset($_SESSION['round']);
//if the item in the invoice is an estimate, the est name window opens
	if ($howmany == count($_SESSION['invline'])){
	header("Location:PAYMENT_ROUTINE.php?pettype=$_SESSION[pettype]");
	}
	else {
	$estimatename="window.open('EST_NAME.php','_blank','width=400,height=270');";
	}
}

//CANCEL INVOICE - INSERT INTO REJECTIN
else if (isset($_POST['cancel']))
{
$insertSQL="INSERT INTO REJECTIN (REJINV, REJDATE, DATETIME, CUSTNO, PETID, ITOTAL, STAFF, COMPANY) VALUES ($_SESSION[minvno], NOW(), NOW(),'$_SESSION[client]','$_SESSION[patient]','$_POST[itotal]','$_SESSION[staff]','$_POST[company]')";
mysql_query($insertSQL, $tryconnection);

//delete from INVHOLD
$lock_it = "LOCK TABLES INVHOLD WRITE,RECEP WRITE,ARCUSTO WRITE" ;  
$Qlock = mysql_query($lock_it, $tryconnection) or die(mysql_error()) ;
$deleteSQL = "DELETE FROM INVHOLD WHERE INVCUST='$_SESSION[client]'";
mysql_query($deleteSQL, $tryconnection);
$optimize = "OPTIMIZE TABLE INVHOLD";
mysql_query($optimize, $tryconnection);

//DELETE FROM RECEP FILE
$query_discharge="DELETE FROM RECEP WHERE RFPETID='$_SESSION[patient]'";
$discharge=mysql_query($query_discharge,$tryconnection) or die(mysql_error());
$query_optimize="OPTIMIZE TABLE RECEP ";
$optimize=mysql_query($query_optimize, $tryconnection) or die(mysql_error());

$query_LOCK = "UPDATE ARCUSTO SET LOCKED='0' WHERE CUSTNO = '$client' LIMIT 1";
$LOCK = mysql_query($query_LOCK, $tryconnection) or die(mysql_error());

$unlock_it = "UNLOCK TABLES" ;
$Qunlock = mysql_query($unlock_it, $tryconnection) or die(mysql_error()) ;
$client = $_SESSION['client'];
$patient = $_SESSION['patient'];
 unset($_SESSION['invline']) ;
unset($_SESSION['payments']);
unset($_SESSION['methods']);
unset($_SESSION['onaccount']);
//session_destroy();
//unset($_SESSION);
//session_start();
$_SESSION['client']=$client;
$_SESSION['patient']=$patient;

//$gobackwin="history.go(-4);";
header("Location:../../CLIENT/CLIENT_PATIENT_FILE.php");
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>FINISH INVOICE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
<?php echo $openwindow; ?>
<?php echo $estimatename; ?>
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

</script>

<style type="text/css">
#table2 {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}

#table {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}

.button1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 20px;
	width: 200px;
}
.button11 {	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 20px;
}

#shadow {
	background-color: #446441;
	width: 440px;
	height: auto;
}
#shadowedtable {
	position: relative;
	width: 440px;
	height: auto;
	left: -4px;
	top: -4px;
	background-color:#B1B4FF;
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

<form action="<?php $_SERVER['PHP_SELF']; ?>" name="form" class="FormDisplay" method="post">
<table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td height="60" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
        </span></td>
        <td width="22%" rowspan="2" valign="middle" align="center"><span class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>          
        </span>
        <?php echo $_SESSION['iwrite']['SUBTCOM']; ?>
        </td>
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
        <td height="15" colspan="2" align="left"  class="Labels2"><span class="Verdana12B" style="background-color:#FFFF00">&nbsp;<script type="text/javascript">document.write(sessionStorage.petname);</script>
</span>        <script type="text/javascript">document.write(sessionStorage.desco);</script>
         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">
        <script type="text/javascript">document.write(sessionStorage.desct);</script> (<?php agecalculation($tryconnection,$pdob); ?>)
		</td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td height="" align="center" valign="middle">
    
    
    <div id="shadow">
	<div id="shadowedtable">
    <table width="440" border="1" cellpadding="0" cellspacing="0" frame="box" rules="none">
      
      <tr>
        <td width="74" height="5" align="center" valign="bottom" bgcolor="#B1B4FF" class="Verdana14B"></td>
        <td width="141" height="5" align="center" valign="bottom" bgcolor="#B1B4FF" class="Verdana14B"></td>
        <td width="217" height="5" align="center" valign="bottom" bgcolor="#B1B4FF" class="Verdana14B"></td>
      </tr>
      <tr>
        <td height="40" colspan="2" align="center" bgcolor="#B1B4FF"><input type="button" class="button1" name="button" value="WRITE COMMENTS" onclick="window.open('WRITE_IN.php','_blank','width=500,height=224')" /></td>
        <td height="40" align="center" bgcolor="#B1B4FF"><input type="button" class="button1" name="button8" value="REVIEW HISTORY" onclick="window.open('../../PATIENT/HISTORY/REVIEW_HISTORY.php?path=2close','_blank','width=785,height=700,status=no')" <?php //if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?>/></td>
      </tr>
      <tr>
        <td height="40" colspan="2" align="center" bgcolor="#B1B4FF"><input type="button" class="button1" name="button2" value="INVOICE SAME PATIENT" onclick="document.location='REGULAR_INVOICING.php?product=j&record=k&subcat=i'" /></td>
        <td height="40" align="center" bgcolor="#B1B4FF"><input type="button" class="button1" name="button9" value="ADD NEW HISTORY" onclick="window.open('../../PATIENT/HISTORY/ADD_NEW_HISTORY.php?path=2close','_blank','width=785,height=700,status=no')" <?php //if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?>/></td>
      </tr>
      <tr <?php //if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?>>
        <td height="40" colspan="2" align="center" bgcolor="#B1B4FF"><input type="submit" class="button1" name="anotherpet" value="INVOICE ANOTHER PATIENT" <?php if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?>/></td>
        <td height="40" align="center" bgcolor="#B1B4FF">
        <input name="cancel2" class="button1" type="button" value="CANCEL ENTIRE <?php if($_SESSION['refID']=='EST'){echo "ESTIMATE";} else {echo "INVOICE";} ?>" onclick="confirmation()" <?php //if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?>/>
        </td>
      </tr>
      <tr <?php if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?>>
        <td height="40" colspan="2" align="center" bgcolor="#B1B4FF"><input type="button" class="button1" name="button4" value="EXAMINATION SHEETS" <?php if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?> disabled="disabled"/></td>
        <td height="40" align="center" bgcolor="#B1B4FF"><input type="button" class="button1" name="dlog" value="DUTY LOG" onclick="window.open('../../RECEPTION/DUTY_LOG/ADD_EDIT_DUTY_LOG.php?tea=7&dutylogid=0&client=<?php echo $_SESSION['client']; ?>&patient=<?php echo $_SESSION['patient']; ?>','_blank','width=500, height=500')" /></td>
      </tr>
      <tr <?php /*if($_SESSION['refID']=='EST'){*/echo "style='display:none'";//} ?>>
        <td height="40" colspan="2" align="center" bgcolor="#B1B4FF"><input type="button" class="button1" name="button5" value="EDIT REGISTRATION" disabled="disabled" /></td>
        <td height="40" align="center" bgcolor="#B1B4FF"><input type="button" class="button1" name="button7" value="CATEGORIZATION" disabled="disabled" /></td>
      </tr>
      <tr>
        <td height="60" colspan="3" align="center" bgcolor="#B1B4FF">
        <input type="submit" class="button11" name="reserve" value="RESERVE" <?php if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?>/>
		<input type="submit" class="button11" name="finish" value="FINISHED, START PAYMENT ROUTINE" <?php if($_SESSION['refID']=='EST'){echo "style='display:none'";} ?>/><input type="button" class="button11" name="estimate" value="FINISHED BUILDING ESTIMATE" <?php if($_SESSION['refID']!='EST'){echo "style='display:none'";} ?> onclick="window.open('EST_NAME.php','_blank','width=400,height=270')"/>
		<input type="button" class="button11" name="button12" value="GO BACK" onclick="document.location='REGULAR_INVOICING.php?product=j&amp;record=k&amp;subcat=i&amp;pettype=<?php echo $row_PATIENT_CLIENT['PETTYPE']; ?>'" /></td>
      </tr>
    </table>
    </div>
    </div>
    
    </td>
  </tr>
</table>

<div style="display:none">
<input type="hidden" name="subtotal" value=""  />
<script type="application/javascript">
					//CALCULATE THE TOTAL PRICE INCLUDING GST's
					var price =<?php $INVtotal = array();
						//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                		          foreach ($_SESSION['invline'] as $invtot)
								  {
										if(($invtot['INVEST']!='1' || $_SESSION['minvno']=='0') && $invtot['INVDECLINE']!='1' && $invtot['PETNAME']==$_SESSION['petname']){
										$INVtotal[]=round($invtot['INVTOT'],2);
										}
								  }
								  //SUM UP THE INDIVIDUAL PRICES
                                  echo array_sum($INVtotal);
                                 ?> ;
					//CONVERT THE PRICE INTO TWO DECIMAL POINTS
			 		var priceconv = price.toFixed(2);
					//DISPLAY THE RESULT
					document.write(priceconv);
					document.forms[0].subtotal.value=priceconv;
</script>

<input type="hidden" name="xgst" value=""  />
<script type="application/javascript">
	//CALCULATE THE GST TOTAL OF INVOICE ITEMS
	var GST = <?php $GSTtotal = array();
			//TAKE CALCULATED GST's FROM EACH INVOICE ITEM AND INSERT THEM INTO ARRAY 
					foreach ($_SESSION['invline'] as $GSTitem)
					{
						if(($GSTitem['INVEST']!='1' || $_SESSION['minvno']=='0') && $GSTitem['INVDECLINE']!='1'){
						$GSTtotal[]=round($GSTitem['INVGST'],2);
						}
					}
			//SUM UP THE VALUES IN ARRAY & DISPLAY
					echo array_sum($GSTtotal);
				   ?>;
								
	//CONVERT THE DISPLAYED VALUE INTO TWO DECIMAL POINT NUMBER
	var GSTconv = GST.toFixed(2);
	//DISPLAY GST VALUE IN INVOICE PREVIEW
	document.write(GSTconv);
	document.forms[0].xgst.value=GSTconv;
</script>

<input type="hidden" name="xpst" value=""  />
<script type="application/javascript">
    //CALCULATE THE GST TOTAL OF INVOICE ITEMS
    var PST =<?php $PSTtotal = array();
            //TAKE CALCULATED GST's FROM EACH INVOICE ITEM AND INSERT THEM INTO ARRAY 
                    foreach ($_SESSION['invline'] as $PSTitem)
                    {
                        if(($PSTitem['INVEST']!='1' || $_SESSION['minvno']=='0') && $PSTitem['INVDECLINE']!='1'){
                        $PSTtotal[]=round($PSTitem['INVTAX'],2);
                        }
                    }
            //SUM UP THE VALUES IN ARRAY & DISPLAY
                    echo array_sum($PSTtotal);
                   ?>;
                                
    //CONVERT THE DISPLAYED VALUE INTO TWO DECIMAL POINT NUMBER
    var PSTconv = PST.toFixed(2);
    //DISPLAY GST VALUE IN INVOICE PREVIEW
    document.write(PSTconv);
    document.forms[0].xpst.value=PSTconv;
</script>
<input type="hidden" name="xtotal" value=""  />
<strong>
<script type="application/javascript">
    //CALCULATE THE TOTAL PRICE INCLUDING GST's
    var price =<?php $INVtotal = array();
				   $INVdiscount=array();

        //TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                  foreach ($_SESSION['invline'] as $invtot)
                  {
                    if(($invtot['INVEST']!='1' || $_SESSION['minvno']=='0') && $invtot['INVDECLINE']!='1'){
                    $INVtotal[]=round($invtot['INVTOT'],2);
					$INVdiscount[]=round($invtot['INVDISC'],2);
                    }
                  }
                  //SUM UP THE INDIVIDUAL PRICES
                  echo array_sum($INVtotal);
                 ?> 
                 
                 +  //ADD THE GST
                 
                 <?php 
                   echo array_sum($GSTtotal);
                 ?>
                 
                 +
                 
                <?php 
                   echo array_sum($PSTtotal);
                 ?>	
								 
				 -
				 
				 <?php
				 echo array_sum($INVdiscount);
				 ?>;
    //CONVERT THE PRICE INTO TWO DECIMAL POINTS
    var priceconv = price.toFixed(2);
    //DISPLAY THE RESULT
    document.write(priceconv);
    document.forms[0].xtotal.value=priceconv;

</script>
</strong>
</div>
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