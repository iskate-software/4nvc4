<?php
session_start();
//unset($_SESSION['invline']);
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");
include("../../ASSETS/tax.php");

$client = $_SESSION['client'];
$patient = $_SESSION['patient'];
$payxxx = $_SESSION['payments'] ;
$methodsxxx = $_SESSION['methods'] ;


if (isset($_POST['check'])){
 //session_destroy();
 unset($_SESSION);
 $_SESSION = array() ;
 session_start();
 $_SESSION['client']=$client;
 $_SESSION['patient']=$patient;
 $_SESSION['payments'] = $payxxx ;
 $_SESSION['methods'] = $methodsxxx;

	if (isset($_POST['clinexam'])){
		$closewindow="window.open('../../PATIENT/HISTORY/ADD_NEW_HISTORY.php?path=procmenu2','_self');";
	}
	
	else if (isset($_POST['reviewhx'])){
		$closewindow="window.open('../../PATIENT/HISTORY/REVIEW_HISTORY.php?path=procmenu2','_self');";
	}

	else if (isset($_POST['mednotes'])){
		$closewindow="window.open('../../PATIENT/PROCESSING_MENU/MEDICAL_NOTES.php?path=procmenu','_self');";
	}

	else if (isset($_POST['procmenu'])){
		$closewindow="window.open('../../PATIENT/PROCESSING_MENU/PROCESSING_MENU.php','_self');";
	}

	else if (isset($_POST['family'])){
		$closewindow="sessionStorage.setItem('refID','PROCESSING MENU'); window.open('../../CLIENT/CLIENT_PATIENT_FILE.php','_self');";
	}

	else if (isset($_POST['continue'])){
		$closewindow="window.open('../../CLIENT/CLIENT_SEARCH_SCREEN.php?refID=REGULAR INVOICING','_self');";
	}

	else if (isset($_POST['exit'])){
		$closewindow="window.open('../../INDEX.php','_self');";
	}

}//if (isset($_POST['check'])){

mysqli_select_db($tryconnection, $database_tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$_SESSION[patient]' LIMIT 1";
$PATIENT_CLIENT = mysqli_query($tryconnection, $query_PATIENT_CLIENT) or die(mysqli_error($mysqli_link));
$row_PATIENT_CLIENT = mysqli_fetch_assoc($PATIENT_CLIENT);
$pdob=$row_PATIENT_CLIENT['PDOB'];
$psex=$row_PATIENT_CLIENT['PSEX'];
$isitdun = "SELECT CUSTNO,DATETIME FROM ARINVOI WHERE CUSTNO = $client AND DATETIME > DATE_SUB(NOW(), INTERVAL 1 MINUTE) LIMIT 1" ;
$find_out = mysqli_query($tryconnection, $isitdun) or die(mysqli_error($mysqli_link)) ;
$row_dun = mysqli_fetch_array($find_out) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>DV MANAGER MAC</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
<?php
if (isset($_SESSION['printinvoice'])){
echo "window.open('../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW.php','_blank');";
}
// THIS IS THE CHANGE THAT SEEMS TO HAVE FIXED IT.
//unset($_SESSION['payments']) ;
?>

<?php echo $closewindow; ?>
sessionStorage.removeItem('iwrite');
document.getElementById('inuse').innerText=localStorage.xdatabase;
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
	font-family: "Verdana";
	font-size: 11px;
	border-width: 0px;
	padding: 5 px;
	outline-width: 0px;
}
.style2 {color: #666666}
.style3 {color: #CC6600}
.button1 {	
font-family: "Verdana";
font-size: 20px;
width: 200px;
}



-->
</style><!-- InstanceEndEditable -->
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
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" --><?php // if (empty($_SESSION['fileused'])){echo"&nbsp;"; } else {echo substr($_SESSION['fileused'],0,25);}  ?>
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->

<form action="" name="reg_invoicing" method="post">
<table width="100%" height="557" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td height="60" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
        </span></td>
        <td width="22%" rowspan="2" valign="middle" align="center"><span class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>          
        </span>        </td>
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
</span>        <script type="text/javascript">document.write(sessionStorage.desco);</script>         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">
        <script type="text/javascript">document.write(sessionStorage.desct);</script> (<?php agecalculation($tryconnection,$pdob); ?>)		</td>
      </tr>
    </table>    </td>
    </tr>
    <tr>
      <td height="76" align="center" valign="top" bgcolor="#B1B4FF" class="Verdana10">
      <?php
	  //print_r($_SESSION);
	  ?>      </td>
    </tr>
    <tr>
    <td height="208" align="center" valign="top" bgcolor="#B1B4FF">
    <table width="70%" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="table">
      <tr>
        <td colspan="4" height="200" align="center">
        <span class="Verdana13B">The invoice #<?php echo $_SESSION['invline'][0]['INVNO']; ?> for 
        <br  />
        <br  />
        <span class="Verdana13BBlue">
      <script type="text/javascript">document.write(sessionStorage.custname);</script>
        <br  />
        <br  />
    </span> has been <?php if (!empty($row_dun)){echo "completed.";} else {echo "reserved.";} ?></span>        </td>
       </tr>
    </table>    </td>
    </tr>
    <tr>
      <td height="207" align="center" valign="top" bgcolor="#B1B4FF">
      <input name="clinexam" type="submit" class="button1" id="clinexam" value="ADD NEW HISTORY" />
      <input name="reviewhx" type="submit" class="button1" id="reviewhx" value="REVIEW MEDICAL HISTORY" />
      <br />
      <input name="mednotes" type="submit" class="button1" id="mednotes" value="MEDICAL NOTES" />
      <input name="procmenu" type="submit" class="button1" id="procmenu" value="PROCESSING MENU" />
      <br />
      <input name="family" type="submit" class="button1" id="family" value="FAMILY" />
      <input name="continue" type="submit" class="button1" id="continue" value="CONTINUE REG. INVOICING" />
      <br />
      <input name="exit" type="submit" class="button1" id="exit" value="EXIT" />
	  <?php //print_r($_SESSION); ?>
      <input type="hidden" name="check" value="1" />
      </td>
    </tr>
</table>
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
