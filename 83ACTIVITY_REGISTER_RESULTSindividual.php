<?php 
session_start();
require_once('../../tryconnection.php');

if (!empty($_GET['startdate'])){
$startdate=$_GET['startdate'];
}
else {
$startdate='00/00/0000';
}
$stdum = $startdate ;

mysql_select_db($database_tryconnection, $tryconnection);
$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysql_query($startdate, $tryconnection) or die(mysql_error());
$startdate=mysql_fetch_array($startdate);

if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}
$enddum = $enddate ;

$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysql_fetch_array($enddate);

$file2search=$_GET['file2search'];

$search_DVMINV="SELECT *, DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDTE FROM $file2search WHERE invcust = 413 and INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]' ORDER BY INVNO,INVSEQ";
$DVMINV=mysql_query($search_DVMINV, $tryconnection ) or die(mysql_error());
$row_DVMINV=mysql_fetch_assoc($DVMINV);

$search_GRANDTOTAL="SELECT SUM(INVTOT) AS GRANDTOTAL FROM $file2search WHERE  invcust = 413 and INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]'";
$GRANDTOTAL=mysql_query($search_GRANDTOTAL, $tryconnection ) or die(mysql_error());
$row_GRANDTOTAL=mysql_fetch_assoc($GRANDTOTAL);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ACTIVITY REGISTER FROM <?php echo $_GET['startdate'].' TO '.$_GET['enddate']; ?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;

var irresults=document.getElementById('irresults');
irresults.scrollTop = irresults.scrollHeight;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function highliteline(x,y){
document.getElementById(x).style.cursor='default';
document.getElementById(x).style.backgroundColor=y;
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
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
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr id="prthospname">
    <td colspan="8" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>
    </td>
    </tr>
    </tr>
    <tr id="prtpurpose">
    <td colspan="8" height="15" align="center" class="Verdana13">Invoice Summaries for Statements <?php if ($startdate == $enddate) {echo $stdum ;} else {echo $stdum .' - '. $enddum ;}?></td>
    </tr>
  <tr bgcolor="#000000" class="Verdana11Bwhite">
    <td width="160" height="10" align="left">Client</td>
    <td width="160" align="left">Patient</td>
    <td width="110" align="center">Date</td>
    <td width="250" align="left">Details</td>
    <td align="right">Total&nbsp;</td>
  </tr>
  <tr>
    <td colspan="8" align="left">
    
    <div id="irresults3">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php 
  $newinvno=0;
  $totals=array();
  do {
  
	if ($newinvno!=$row_DVMINV['INVNO'] && isset($newinvno)){
echo '<tr class="Verdana11B">
<td colspan="3" height="8" align="right"></td>
<td width="250" align="center">TOTAL</td>
<td align="right">'.number_format(array_sum($totals),2).'&nbsp;</td>
</tr>';

$totals=array();

echo '<tr><td colspan="5" height="8"></td></tr>';
	}
echo '
  <tr';

if ($row_DVMINV['INVDESCR']=='TOTAL'){
	echo " class=Verdana11B";
	}

echo '>
    <td width="160" height="15" align="left" class="Verdana12B">';
	if ($newcust!=$row_DVMINV['INVCUST']){
		if ($row_DVMINV['INVCUST']=='0'){
		echo "CASUAL SALE";
		}
		else {
	$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO = '".$row_DVMINV['INVCUST']."' LIMIT 1";
	$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
	$row_CLIENT = mysql_fetch_assoc($CLIENT);
echo substr($row_CLIENT['COMPANY'].", ".$row_CLIENT['CONTACT'],0,29);
$newpet = 0 ;
		}
	}
	
echo '&nbsp;</td>
    <td width="160" align="left" class="Verdana12">';
	if ($newpet!=$row_DVMINV['INVPET'] && $row_DVMINV['INVDESCR']!='TOTAL' && $row_DVMINV['INVDESCR']!='GST' && $row_DVMINV['INVDESCR']!='HST' && $row_DVMINV['INVDESCR']!='PST'){
echo substr($row_DVMINV['PETNAME'],0,29);
	}
echo '</td>
    <td width="110" align="center" class="Verdana11">';
if ($newinvno!=$row_DVMINV['INVNO'] && isset($newinvno)){echo "<br />";}
echo $row_DVMINV['INVDTE'];
echo '</td>
    <td width="250" align="';

if ($row_DVMINV['INVDESCR']=='TOTAL'){
	echo "center";
	}
else {echo "left";}
echo '" class="Verdana11">';

if ($newinvno!=$row_DVMINV['INVNO'] && isset($newinvno)){
echo "<strong>Inv#".$row_DVMINV['INVNO']."</strong><br />";
}
echo $row_DVMINV['INVDESCR'];

echo '</td>
    <td align="right" class="Verdana11">';
if ($newinvno!=$row_DVMINV['INVNO'] && isset($newinvno)){echo "<br />";}
if ($row_DVMINV['INVDESCR']!='1') {echo number_format($row_DVMINV['INVTOT'],2);}
echo '&nbsp;</td>
  </tr>';
  
  $newcust=$row_DVMINV['INVCUST'];
  $newpet=$row_DVMINV['INVPET'];
  $newinvno=$row_DVMINV['INVNO'];
  
if (!empty($row_DVMINV['INVOICECOMMENT'])){
//echo '<tr>
//<td></td>
//<td colspan="4" class="Verdana11Grey"><em>*'.$row_DVMINV['INVOICECOMMENT'].'</em></td></tr>';
}  
else if (!empty($row_DVMINV['LCOMMENT'])){
echo '<tr>
<td></td>
<td></td>
<td></td>
<td colspan="2" class="Verdana11Grey"><em>*'.$row_DVMINV['LCOMMENT'].'</em></td></tr>';
}  
  $totals[]=$row_DVMINV['INVTOT'];
  }
  while ($row_DVMINV=mysql_fetch_assoc($DVMINV));

echo '<tr class="Verdana11B">
<td colspan="3" align="right"></td>
<td align="center">TOTAL</td>
<td align="right">'.number_format(array_sum($totals),2).'&nbsp;</td>
</tr>';
$newpet = 0 ;
echo '<tr>
<td></td>
<td></td>
<td></td>
<td class="Verdana13B"></td>
<td align="right" class="Verdana13B"><hr size="1"/></td>
</tr>';
echo '<tr>
<td></td>
<td></td>
<td></td>
<td class="Verdana13B">Grand Total</td>
<td align="right" class="Verdana13B">'.$row_GRANDTOTAL['GRANDTOTAL'].'&nbsp;</td>
</tr>';
  
  ?>
  
</table>
    </div>
    
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="8  ">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='INV_REPORTS_DIR.php'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
