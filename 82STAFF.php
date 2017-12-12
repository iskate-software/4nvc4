<?php
session_start();
require_once('../../tryconnection.php'); 
include("../../ASSETS/age.php");

$psex=$_GET['psex'];
$pdob=$_GET['pdob'];
$llocalid=$_GET['llocalid'];
$pettype=$_GET['pettype'];

$client=$_SESSION['client'];


mysql_select_db($database_tryconnection, $tryconnection);
$query_Staff = "SELECT * FROM STAFF WHERE SIGNEDIN=1 ORDER BY PRIORITY";
$Staff = mysql_query($query_Staff, $tryconnection) or die(mysql_error());
$row_Staff = mysql_fetch_assoc($Staff);

$query_Doctor = "SELECT * FROM DOCTOR WHERE SIGNEDIN=1 AND (SUBSTR(DOCTOR,1,3) = 'Dr ' OR INSTR(DOCTOR,'DVM')<> 0) ORDER BY PRIORITY";
$Doctor = mysql_query($query_Doctor, $tryconnection) or die(mysql_error());
$row_Doctor = mysql_fetch_assoc($Doctor);


if (isset($_POST['check']) && !isset($_POST['cancel'])) {

///////////////////////////////////////////////////////////////////////
//create an empty row in PETHOLD 
$query_PETHOLD = "SELECT * FROM PETHOLD WHERE PHPETID='$_SESSION[patient]'";
$PETHOLD = mysql_query($query_PETHOLD, $tryconnection) or die(mysql_error());
$row_PETHOLD = mysql_fetch_assoc($PETHOLD);
//if there is no record in PETHOLD for this patient, create one
if (empty($row_PETHOLD['PHPETID'])){
$query_PETHOLD="INSERT INTO PETHOLD (PHCUSTNO, PHPETID, PHPETNAME) VALUES ('$_SESSION[client]','$_SESSION[patient]','$_SESSION[petname]')";
$PETHOLD = mysql_unbuffered_query($query_PETHOLD, $tryconnection) or die(mysql_error());
}


///////////////////////////////////////////////////////////////////////

//select the estimates from ESTHOLD where the INVCUST = custno
$query_ESTHOLD = "SELECT DISTINCT INVHYPE FROM ESTHOLD WHERE INVCUST=$_SESSION[client]";
$ESTHOLD = mysql_query($query_ESTHOLD, $tryconnection) or die(mysql_error());
$row_ESTHOLD = mysql_fetch_assoc($ESTHOLD);
$totalRows_ESTHOLD = mysql_num_rows($ESTHOLD);
$query_INVHOLD = "SELECT INVNO FROM INVHOLD WHERE INVCUST=$_SESSION[client] LIMIT 1";
$INVHOLD = mysql_query($query_INVHOLD, $tryconnection) or die(mysql_error());
$row_INVHOLD = mysql_fetch_assoc($INVHOLD);
$totalRows_INVHOLD = mysql_num_rows($INVHOLD);

   if (!isset($_SESSION['round']) && $_SESSION['refID']!='EST' && $totalRows_INVHOLD==0) {     
    $query_lockc = "LOCK TABLES CRITDATA WRITE" ;
    $get_it = mysql_query($query_lockc, $tryconnection) or die(mysql_error()) ;    
	$query_INVNO = "SELECT LASTINV FROM CRITDATA LIMIT 1";
	$INVNO = mysql_query($query_INVNO, $tryconnection) or die(mysql_error());
	$row_INVNO = mysql_fetch_assoc($INVNO);
	$_SESSION['minvno'] = $row_INVNO['LASTINV'] + 1 ;
	$query_INVNO = "UPDATE CRITDATA SET LASTINV = '$_SESSION[minvno]'" ;
	$INVNO = mysql_query($query_INVNO,$tryconnection) or die(mysql_error()) ;
	$query_unlockc = "UNLOCK TABLES" ;
	$let_it_go = mysql_query($query_unlockc, $tryconnection) or die(mysql_error()) ;
   }
   else if (!isset($_SESSION['round']) && $_SESSION['refID']!='EST' && $totalRows_INVHOLD!=0){
	$_SESSION['minvno'] = $row_INVHOLD['INVNO'];
   }
   else if (isset($_SESSION['round']) && $_SESSION['refID']!='EST') {
	$_SESSION['minvno'] = $_SESSION['minvno'];
   }  
   else {
	$_SESSION['minvno'] = '0';
   }
   $_SESSION['staff'] = $_POST['invstaff'];
   $_SESSION['minvdte'] = $_POST['minvdte'];
   
   
	if (($totalRows_ESTHOLD==0 && $totalRows_INVHOLD==0) || ($totalRows_ESTHOLD==0 && $_SESSION['refID']=='EST')){
	$openwindow="window.open('REGULAR_INVOICING.php?record=k&subcat=i&product=j&psex=".$psex."&pdob=".$pdob."&pettype=".$pettype."','_self');";
	}
	//if num of rows is 0, there is no estimate
	else {
	$openwindow="window.open('EST_RES_PREVIEW.php?record=k&subcat=i&product=j&psex=$psex&pdob=$pdob&pettype=$pettype','_blank','width=400,height=320');";
	}

}

if (isset($_POST['cancel'])){
	$query_LOCK = "UPDATE ARCUSTO SET LOCKED='0' WHERE CUSTNO = '$client' LIMIT 1";
	$LOCK = mysql_query($query_LOCK, $tryconnection) or die(mysql_error());
$gobackwin="document.location='../../CLIENT/CLIENT_PATIENT_FILE.php';";
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

document.getElementById('title').innerText="SELECT STAFF AND DATE FOR "+sessionStorage.refID;

function bodyonload(){
<?php 
//if (!empty($row_PETHOLD['WRITEIN'])){
//echo "sessionStorage.setItem('iwrite','".$row_PETHOLD['WRITEIN']."');";}
//if (!empty($row_PETHOLD['SUBTCOM'])){
//echo "sessionStorage.setItem('writecomment','".$row_PETHOLD['SUBTCOM']."');";}
echo $openwindow; 
?>
<?php echo $gobackwin; ?>
document.forms[0].invstaff.options[0].selected=true;
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

function Staffselect(){
document.Staff.submit();
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

<form action="<?php $_SERVER['PHP_SELF']; ?>" class="FormDisplay" name="Staff" method="post">
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
    </table>    
    </td>
    
    </tr>
  <tr>
    <td height="" colspan="3" align="center" valign="top">
    
    
    <table class="table" width="733" height="457" border="1" cellpadding="0" cellspacing="0" >
    <tr>
    <td>
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="30" colspan="3" align="center" valign="middle" class="Verdana12Blue">
    <strong>Please select Staff:</strong>
    <br  />
    <span class="Verdana11Grey">Doubleclick or click &amp; save.</span>
    </td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="250"><select name="invstaff" size="10" id="select" class='SelectList' ondblclick="Staffselect();">
      <?php
do {  
      echo '<option value="'.$row_Doctor['DOCTOR'].'">'.$row_Doctor['DOCTOR'].'</option>';
} while ($row_Doctor = mysql_fetch_assoc($Doctor));
// Is this where the doctors double up with the staff?  - YES :)
	  
do {  
?>
      <option value="<?php echo $row_Staff['STAFF']?>" ><?php echo $row_Staff['STAFF']?></option>
      <?php
} while ($row_Staff = mysql_fetch_assoc($Staff));
//  $rows = mysql_num_rows($Staff);
//  if($rows > 0) {
//      mysql_data_seek($Staff, 0);
//	  $row_Staff = mysql_fetch_assoc($Staff);
//  }
?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="30" align="center"><label>Date
        <input name="minvdte" type="text" class="Input" id="minvdte" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onclick="ds_sh(this,'<?php echo date('m/d/Y') ?>')" value="<?php  if (isset($_SESSION['minvdte'])) {
					echo $_SESSION['minvdte']; }
					else {
					echo date("m/d/Y");
					}
					?>" />
    </label></td>
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