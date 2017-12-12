<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$search_ESTHOLD="SELECT *, DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDTE FROM ESTHOLD";
$ESTHOLD=mysql_query($search_ESTHOLD, $tryconnection ) or die(mysql_error());
$row_ESTHOLD=mysql_fetch_assoc($ESTHOLD);

$search_GRANDTOTAL="SELECT SUM(INVTOT) AS GRANDTOTAL FROM ESTHOLD WHERE INVDESCR='TOTAL'";
$GRANDTOTAL=mysql_query($search_GRANDTOTAL, $tryconnection ) or die(mysql_error());
$row_GRANDTOTAL=mysql_fetch_assoc($GRANDTOTAL);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ESTIMATE FILE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

</script>

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
<div id="LogoHead" onclick="window.open('/'+localStorage.xdatabase+'/INDEX.php','_self');" onmouseover="CursorToPointer(this.id)" title="Home">DVM</div>
<!-- InstanceEndEditable -->

<div id="MenuBar">

	<ul id="navlist">
                
<!--FILE-->                
                
		<li><a href="#" id="current">File</a> 
			<ul id="subnavlist">
                <li><a href="#"><span class="">About DV Manager</span></a></li>
                <li><a onclick="utilities();">Utilities</a></li>
			</ul>
		</li>
                
<!--INVOICE-->                
                
		<li><a href="#" id="current">Invoice</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self'/'+localStorage.xdatabase+'/INVOICE/CASUAL_SALE_INVOICING/STAFF.php?refID=SCI)"><span class="">Casual Sale Invoicing</span></a></li>
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick="nav0();">Regular Invoicing</a><!-- InstanceEndEditable --></li>
                <li><a href="#" onclick="nav11();">Estimate</a></li>
                <li><a href="#" onclick=""><span class="">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick="suminvoices()"><span class="">Summary Invoices</span></a></li>
                <li><a href="#" onclick="cashreceipts()"><span class="">Cash Receipts</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Cancel Invoices</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/INVOICE/COMMENTS/COMMENTS_LIST.php?path=DIRECTORY','_blank','width=733,height=553,toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no')">Comments</a></li>
                <li><a href="#" onclick="tffdirectory()">Treatment and Fee File</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Worksheet File</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Procedure Invoicing File</span></a></li>
                <li><a href="#" onclick="invreports();"><span class="">Invoicing Reports</span></a></li>
			</ul>
		</li>
                
<!--RECEPTION-->                
                
		<li><a href="#" id="current">Reception</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self')"><span class="">Appointment Scheduling</span></a></li>
                <li><a href="#" onclick="reception();">Patient Registration</a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/RECEPTION/USING_REG_FILE.php','_blank','width=550,height=535')">Using Reception File</a></li>
                <li><a href="#" onclick="nav2();"><span class="hidden"></span>Examination Sheets</a></li>
                <li><a href="#" onclick="gexamsheets()"><span class="">Generic Examination Sheets</span></a></li>
                <li><a href="#" onclick="nav3();">Duty Log</a></li>
                <li><a href="#" onclick="staffsiso()">Staff Sign In &amp; Out</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">End of Day Accounting Reports</span></a></li>
                    </ul>
                </li>
                
<!--PATIENT-->                
                
                <li><a href="#" id="current">Patient</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="nav4();">Processing Menu</a> </li>
                <li><a href="#" onclick="nav5();">Review Patient Medical History</a></li>
                <li><a href="#" onclick="nav6();">Enter New Medical History</a></li>
                <li><a href="#" onclick="nav7();">Enter Patient Lab Results</a></li>
                <li><a href="#" onclick=""window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=ENTER SURG. TEMPLATES','_self')><span class="">Enter Surgical Templates</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=CREATE NEW CLIENT','_self','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no');">Create New Client</a></li>
                <li><a href="#" onclick="movepatient();">Move Patient to a New Client</a></li>
                <li><a href="#" onclick="searchpatient()">Rabies Tags</a></li>
                <li><a href="#" onclick="searchpatient()">Tattoo Numbers</a></li>
                <li><a href="#" onclick="nav8();"><span class="">Certificates</span></a></li>
                <li><a href="#" onclick="nav9();"><span class="">Clinical Logs</span></a></li>
                <li><a href="#" onclick="nav10();"><span class="">Patient Categorization</span></a></li>
                <li><a href="#" onclick="">Laboratory Templates</a></li>
                <li><a href="#" onclick="nav1();"><span class="">Quick Weight</span></a></li>
<!--                <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
-->			</ul>
		</li>
        
<!--ACCOUNTING-->        
		
        <li><a href="#" id="current">Accounting</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""accreports()>Accounting Reports</a></li>
                <li><a href="#" onclick="inventorydir();" id="inventory" name="inventory">Inventory</a></li>
                <li><a href="#" onclick="" id="busstatreport" name="busstatreport"><span class="">Business Status Report</span></a></li>
                <li><a href="#" onclick="" id="hospstatistics" name="hospstatistics"><span class="">Hospital Statistics</span></a></li>
                <li><a href="#" onclick="" id="monthend" name="monthend"><span class="">Month End Closing</span></a></li>
			</ul>
		</li>
        
<!--MAILING-->        
		
        <li><a href="#" id="current">Mailing</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self')" ><span class="">Recalls and Searches</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Handouts</span></a></li>
                <li><a href="#" onclick="window.open('','_self')MAILING/MAILING_LOG/MAILING_LOG.php?refID=">Mailing Log</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Vaccine Efficiency Report</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/MAILING/REFERRALS/REFERRALS_SEARCH_SCREEN.php?refID=1','_blank','width=567,height=473')">Referring Clinics and Doctors</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Referral Adjustments</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Labels</span></a></li>
			</ul>
		</li>
	</ul>
</div>
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" -->
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->
<table border="0" cellspacing="0" cellpadding="0">
  <tr id="prthospname">
    <td colspan="5" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>    </td>
    </tr>
  <tr bgcolor="#000000" class="Verdana11Bwhite">
    <td width="160" height="10" align="left">Client</td>
    <td width="160" align="left">Patient</td>
    <td width="110" align="center">Date</td>
    <td width="250" align="left">Details</td>
    <td align="right">Total&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" class="Verdana11" align="center">
    
    <div id="irresults3">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php 
  
  do {
  
	if ($newcust!=$row_ESTHOLD['INVCUST']){
echo '<tr><td colspan="5" height="8"></td></tr>';
	}
echo '
  <tr';

if ($row_ESTHOLD['INVDESCR']=='TOTAL'){
	echo " class=Verdana11B";
	}

echo '>
    <td width="160" height="15" align="left" class="Verdana12B">';
	if ($newcust!=$row_ESTHOLD['INVCUST']){
	$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO = '".$row_ESTHOLD['INVCUST']."'";
	$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
	$row_CLIENT = mysql_fetch_assoc($CLIENT);
echo substr($row_CLIENT['COMPANY'].", ".$row_CLIENT['CONTACT'],0,29);
	}
echo '&nbsp;</td>
    <td width="160" align="left" class="Verdana12">';
	if ($newpet!=$row_ESTHOLD['INVPET'] && $row_ESTHOLD['INVDESCR']!='TOTAL' && $row_ESTHOLD['INVDESCR']!='GST'  && $row_ESTHOLD['INVDESCR']!='HST' && $row_ESTHOLD['INVDESCR']!='PST'){
echo substr($row_ESTHOLD['PETNAME'],0,29);
	}
echo '</td>
    <td width="110" align="center" class="Verdana11">'.$row_ESTHOLD['INVDTE'].'</td>
    <td width="250" align="';

if ($row_ESTHOLD['INVDESCR']=='TOTAL'){
	echo "center";
	}
else {echo "left";}
echo '" class="Verdana11">';

if ($row_ESTHOLD['INVDESCR']=='0'){ echo "<strong>".$row_ESTHOLD['INVHYPE']."</strong>";}
else {echo $row_ESTHOLD['INVDESCR'];}

echo '</td>
    <td align="right" class="Verdana11">';
if ($row_ESTHOLD['INVDESCR']!='0') {echo number_format($row_ESTHOLD['INVTOT'],2);}
echo '&nbsp;</td>
  </tr>';
  
  $newcust=$row_ESTHOLD['INVCUST'];
  $newpet=$row_ESTHOLD['INVPET'];
  
if (!empty($row_ESTHOLD['INVOICECOMMENT'])){
//echo '<tr>
//<td></td>
//<td colspan="4" class="Verdana11Grey"><em>*'.$row_ESTHOLD['INVOICECOMMENT'].'</em></td></tr>';
}  
else if (!empty($row_ESTHOLD['LCOMMENT'])){
echo '<tr>
<td></td>
<td></td>
<td></td>
<td colspan="2" class="Verdana11Grey"><em>*'.$row_ESTHOLD['LCOMMENT'].'</em></td></tr>';
}  
  
  }
  while ($row_ESTHOLD=mysql_fetch_assoc($ESTHOLD));
  
  ?>
 
</table>
    </div>
    
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="5">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='INV_REPORTS_DIR.php'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
