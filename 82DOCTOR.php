<?php
session_start();
if (!isset($_SESSION['round'])) {
   unset($_SESSION['invline']);
}

// Added to make sure that a secondary invoice does not get muddied with the first.

unset($_SESSION['payments']);
unset($_SESSION['methods']);
unset($_SESSION['onaccount']);


require_once('../../tryconnection.php'); 
include("../../ASSETS/age.php");

$psex=$_GET['psex'];
$pdob=$_GET['pdob'];
$llocalid=$_GET['llocalid'];
$pettype=$_GET['pettype'];

$client=$_SESSION['client'];

mysql_select_db($database_tryconnection, $tryconnection);

$query_LOCK_CLIENT = "LOCK TABLES ARCUSTO WRITE" ;
$LOCK_CLIENT = mysql_query($query_LOCK_CLIENT) or die(mysql_error());
$query_CLIENT = "SELECT LOCKED FROM ARCUSTO WHERE CUSTNO = '$client' LIMIT 1";
$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
$row_CLIENT = mysql_fetch_assoc($CLIENT);

if ($row_CLIENT['LOCKED']=='1' && $_GET['refID']!='EST' && !isset($_SESSION['round'])){
$query_UNLOCK = "UNLOCK TABLES" ;
$UNLOCK = mysql_query($query_UNLOCK, $tryconnection) or die(mysql_error()) ;
$gobackwin="alert('This client\'s file is locked for invoicing.'); document.location='../../CLIENT/CLIENT_PATIENT_FILE.php?refID=REG';";
}
else if ($row_CLIENT['LOCKED']!='1' && $_GET['refID']!='EST'){
	$query_LOCK = "UPDATE ARCUSTO SET LOCKED='1' WHERE CUSTNO = '$client' LIMIT 1";
    $LOCK = mysql_query($query_LOCK, $tryconnection) or die(mysql_error()) ;
}

$query_UNLOCK = "UNLOCK TABLES" ;
$UNLOCK = mysql_query($query_UNLOCK, $tryconnection) or die(mysql_error()) ;

$query_Doctor = "SELECT * FROM DOCTOR WHERE SIGNEDIN=1";
$Doctor = mysql_query($query_Doctor, $tryconnection) or die(mysql_error());
$row_Doctor = mysql_fetch_assoc($Doctor);
$totalRows_Doctor = mysql_num_rows($Doctor);


if (isset($_POST['cancel']) && $_GET['refID']!='EST'){
	if ($row_CLIENT['LOCKED']=='1'){
        $query_LOCK_CLIENT = "LOCK TABLES ARCUSTO WRITE" ;
        $LOCK_CLIENT = mysql_query($query_LOCK_CLIENT) or die(mysql_error()); 
		$query_LOCK1 = "UPDATE ARCUSTO SET LOCKED='0' WHERE CUSTNO = '$client'  LIMIT 1";
		$LOCK1 = mysql_query($query_LOCK1, $tryconnection) or die(mysql_error());
        $query_UNLOCK = "UNLOCK TABLES" ;
        $UNLOCK = mysql_query($query_UNLOCK, $tryconnection) or die(mysql_error()) ;
	}
$gobackwin="document.location='../../CLIENT/CLIENT_PATIENT_FILE.php';";
}
else if (isset($_POST['cancel']) && $_GET['refID']=='EST'){
$gobackwin="document.location='../../CLIENT/CLIENT_PATIENT_FILE.php';";
}


/*if (isset($_GET['patient'])){
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

mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient'";
$PATIENT_CLIENT = mysql_query($query_PATIENT_CLIENT, $tryconnection) or die(mysql_error());
$row_PATIENT_CLIENT = mysql_fetch_assoc($PATIENT_CLIENT);


$link1;


//$query_CLIENT = "SELECT * FROM MULTIPET WHERE PETID='$patient'";
//$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
//$row_CLIENT = mysql_fetch_assoc($CLIENT);


if (isset($_POST['check'])) {
   $_SESSION['doctor'] = $_POST['invdoc'];

$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO = '$client'";
$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
$row_CLIENT = mysql_fetch_assoc($CLIENT);

////////////////////---------VALUES-----------//////////////////////
$custname=$row_CLIENT['TITLE'].' '.$row_CLIENT['CONTACT'].' '.$row_CLIENT['COMPANY'];
//////
$custphone=$row_CLIENT['AREA'].'-'.$row_CLIENT['PHONE'].', '.$row_CLIENT['CAREA2'].'-'.$row_CLIENT['PHONE2'].', '.$row_CLIENT['CAREA3'].'-'.$row_CLIENT['PHONE3'].', '.$row_CLIENT['CAREA4'].'-'.$row_CLIENT['PHONE4'];
//////
if ($row_CLIENT['TERMS']=='1'){$custterm = "NORMAL CREDIT";} 
else if ($row_CLIENT['TERMS']=='2'){$custterm = "CASH ONLY";} 
else if ($row_CLIENT['TERMS']=='3'){$custterm = "NO CREDIT";} 
else if ($row_CLIENT['TERMS']=='4'){$custterm = "COLLECTION";} 
else if ($row_CLIENT['TERMS']=='5'){$custterm = "POST DATED CHEQUE";} 
else if ($row_CLIENT['TERMS']=='6'){$custterm = "ACCEPT CHEQUE";}
//////
$custprevbal=$row_CLIENT['BALANCE'];
//////
$custcurbal=$row_CLIENT['BALANCE'];
//////
$petname=$row_PATIENT['PETNAME'];
//////
if ($row_PATIENT['PETTYPE']=='1'){echo "Canine";} 
else if ($row_PATIENT['PETTYPE']=='2'){$pettype = "Feline";} 
else if ($row_PATIENT['PETTYPE']=='3'){$pettype = "Equine";}
else if ($row_PATIENT['PETTYPE']=='4'){$pettype = "Bovine";}
else if ($row_PATIENT['PETTYPE']=='5'){$pettype = "Caprine";}
else if ($row_PATIENT['PETTYPE']=='6'){$pettype = "Porcine";}
else if ($row_PATIENT['PETTYPE']=='7'){$pettype = "Avian";}
else if ($row_PATIENT['PETTYPE']=='8'){$pettype = "Other";}
$desco=$pettype.', '.$row_PATIENT['PETBREED'];
//////
//agecalculation($tryconnection,$row_PATIENT['PDOB']); - NOT WORKING, haven't figure out why yet
if ($row_PATIENT['PNEUTER']=='1' && $row_PATIENT['PSEX']=='M'){$pneuter = "(N)";} 
elseif ($row_PATIENT['PNEUTER']=='1' && $row_PATIENT['PSEX']=='F'){$pneuter = "(S)";}
$desct=$row_PATIENT['PSEX'].$pneuter.', '.$row_PATIENT['PWEIGHT'].' Lbs'.$row_PATIENT['PCOLOUR'].', Born: '. $row_PATIENT['PDOB'].'('.$age.')';
//////
$comment='comment';
$invno='1234';
$rabtag='rabtag';
$rabser='rabser';
$cominv='1';
$medinv='2';
$comno='3';


$insert_PETHOLD = "INSERT INTO PETHOLD (CUSTNO, CUSTNAME, CUSTPHONE, CUSTTERM, CUSTPREVBAL, CUSTCURBAL, PETID, PETNAME, DESCO, DESCT, COMMENT, INVNO, RABTAG, RABSER, COMINV, MEDINV, COMNO) VALUES ('$client', '$custname', '$custphone', '$custterm', '$custprevbal', '$custcurbal', '$patient', '$petname', '$desco', '$desct', '$comment', '$invno', '$rabtag', '$rabser', '$cominv', '$medinv', '$comno')";
$PETHOLD = mysql_query($insert_PETHOLD, $tryconnection) or die(mysql_error());

////?????????????? do we want to copy the entire table or just insert a particular line from pethold?
$query_MULTIPET="CREATE TEMPORARY TABLE IF NOT EXISTS MULTIPET SELECT * FROM PETHOLD";
$MULTIPET=mysql_query($query_MULTIPET, $tryconnection) or die(mysql_error());
  
//header("Location:STAFF.php");
$link1="window.open('STAFF.php','_self')";
}	
*/

else if (isset($_POST['check']) && !isset($_POST['cancel'])) {
   	$_SESSION['doctor'] = $_POST['invdoc'];
	$_SESSION['refID'] = $_GET['refID'];
header("Location:STAFF.php?psex=".$psex."&pdob=".$pdob."&llocalid=".$llocalid."&pettype=".$pettype);
}	

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title id="title"></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

document.getElementById('title').innerText="SELECT DOCTOR FOR "+sessionStorage.refID;

function bodyonload(){
<?php echo $gobackwin; ?>
document.forms[0].invdoc.options[0].selected=true;
document.getElementById('inuse').innerText=localStorage.xdatabase;

<?php //echo $link1; ?>
}

function doctorselect(){
document.doctor.submit();
}


</script>

<style type="text/css">
<!--
#table {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}

#table2 {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}


.SelectList {
	width: 100%;
	height: 100%;
	font-family: "Andale Mono";
	font-size: 13px;
	border-width: 1px;
	padding: 5 px;
	outline-width: 0px;
}
-->
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

<form action="<?php $_SERVER['PHP_SELF']; ?>" class="FormDisplay" name="doctor" method="post">
<table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td height="60" colspan="3" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
        </span></td>
        <td width="22%" rowspan="2" valign="middle" align="center"><span class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>          
        </span>
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
    <td height="" colspan="3" align="center" valign="top">
    
    
    <table class="table" width="733" height="457" border="1" cellpadding="0" cellspacing="0" >
    <tr>
    <td>
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="30" colspan="3" align="center" valign="middle" class="Verdana12Blue">
    <strong>Please select Doctor:</strong>
    <br  />
    <span class="Verdana11Grey">Doubleclick or click &amp; save.</span>
    </td>
    </tr>
  <tr>
    <td>&nbsp;</td><!---->
    <td width="250">
    <select name="invdoc" size="10" id="select" class='SelectList' ondblclick="doctorselect();">
      <?php
do {  
?>
      <option value="<?php echo $row_Doctor['DOCTOR']?>" ><?php echo $row_Doctor['DOCTOR']?></option>
      <?php
} while ($row_Doctor = mysql_fetch_assoc($Doctor));
//  $rows = mysql_num_rows($Doctor);
//  if($rows > 0) {
//      mysql_data_seek($Doctor, 0);
//	  $row_Doctor = mysql_fetch_assoc($Doctor);
//  }
?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center" valign="top" class="Verdana11Grey"></td>
    <td>&nbsp;</td>
  </tr>
</table>
    
    </td>
    </tr>
    </table>
    
    </td>
  </tr>
  <tr>
    <td height="35" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF">
     <input name="save" class="button" type="submit" value="SAVE"/>
     <input name="cancel" class="button" type="submit" value="CANCEL"/>
     <input type="hidden" name="check" value="1"/>
    </td>
  </tr>
</table>

</form>

<form action="" name="cancelinvoice" method="post">
<input type="hidden" name="cancel" value="1" />
</form>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>

<!---->