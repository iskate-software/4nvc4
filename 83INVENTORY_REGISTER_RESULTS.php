<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

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
$startdate=mysqli_fetch_array($startdate);

if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}
$enddum = $enddate ;
$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysqli_fetch_array($enddate);


$can = $_GET['checkcanbox'] ;

$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 
$taxrate = taxvalue($database_tryconnection, $tryconnection, $startdate[0])/100.00;
echo $startdate . ' as of ' . $taxrate . ' tax rate ' ;
$file2search=$_GET['file2search'];

$canc = "" ;
if ($can == 1) {
$canc = " AND INVUNITS < 0 " ;
$get_Canc = "SELECT SUM(INVTOT) as CANCEL from $file2search where INVUNITS < 0 AND INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]' " ;
$Cancget = mysql_query($get_Canc, $tryconnection) or die(mysql_error()) ;
$row_cancsum = mysqli_fetch_assoc($Cancget) ;
$cancsum = $row_cancsum['CANCEL'] ;   
}

$search_DVMINV="SELECT INVNO, DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME, CONCAT(TITLE, ' ', CONTACT, ' ', COMPANY) AS CLIENT,
INVTOT,  INVUNITS, INVDESCR FROM $file2search JOIN ARCUSTO ON DVMINV.INVCUST = ARCUSTO.CUSTNO WHERE INVDATETIME >= '$startdate[0]' 
AND INVDATETIME <= '$enddate[0]' AND INVVPC <> '       ' $canc ORDER BY INVNO ASC";
$search_NET = "SELECT SUM(INVTOT) AS Total_NET FROM $file2search WHERE INVVPC <> '       ' $canc AND  INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]' $canc";
$search_TAX = "SELECT SUM(INVTOT) AS Total_TAX FROM $file2search WHERE INVVPC <> '       ' $canc AND  INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]'";
//$search_PST = "SELECT SUM(PTAX) AS Total_PST FROM $file2search WHERE INVVPC <> '       ' $canc  INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]'";
$DVMINV=mysql_query($search_DVMINV, $tryconnection ) or die(mysql_error());
$row_DVMINV=mysqli_fetch_assoc($DVMINV);
$NET = mysql_query($search_NET, $tryconnection ) or die(mysql_error()) ;
$TAX = mysql_query($search_TAX, $tryconnection ) or die(mysql_error()) ;
//$PST = mysql_query($search_PST, $tryconnection ) or die(mysql_error()) ;
$row_NET = mysqli_fetch_array($NET) ;
$row_TAX = mysqli_fetch_array($TAX) ;
//$row_PST = mysql_fetch_array($PST) ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>INVVENTORY SALES REGISTER FROM <?php echo $_GET['startdate'].' TO '.$_GET['enddate']; ?></title>
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
                <li><a href="#" onclick="searchpatient()">Tatoo Numbers</a></li>
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
    <td colspan="7" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>
    </td>
    </tr>
    <tr id="prtpurpose">
    <td colspan="7" height="15" align="center" class="Verdana13"><?php if ($can == 1) {echo 'Returned ';} ?>Inventory Register for <?php if ($startdate == $enddate) {echo $stdum ;} else {echo $stdum .' - '. $enddum ;}?><br />&nbsp;</td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="" align="right">Inv.#&nbsp;</td>
    <td width="100" align="center">Date</td>
    <td width="140" align="center">Client</td>
    <td width="65" align="right">Qty</td>
    <td width="65" align="right">Total</td>
    <td width="65" align="center"><?php echo substr($taxname,0,3); ?></td>
    <td width="225" align="center">Product</td>
   
  </tr>
  <tr>
    <td colspan="8" class="Verdana12" align="center">
    
    <div id="irresults2">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  
  do {
  echo '
  <tr id="'.$row_DVMINV['INVNO'].'" onmouseover="highliteline(this.id,\'#DCF6DD\'); CursorToPointer(this.id);" onmouseout="whiteoutline(this.id)" onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search='.$_GET['file2search'].'&INVDATETIME='.$row_DVMINV['INVDATETIME'].'&invno='.$row_DVMINV['INVNO'].'\',\'_blank\')">
    <td width="" align="left" class="Verdana13">'.$row_DVMINV['INVNO'].'&nbsp;</td>
    <td width="100" align="center" class="Verdana13">'.$row_DVMINV['INVDATETIME'].'</td>
    <td width="140" class="Verdana13">'.substr($row_DVMINV['CLIENT'],0,29).'</td>
    <td width="65" align="right" class="Verdana13">'.$row_DVMINV['INVUNITS'].'</td>
    <td width="65" align="right" class="Verdana13">'.number_format(($row_DVMINV['INVTOT']),2).'</td>' ;
    
    echo '<td width="65" align="center" class="Verdana13">'.number_format(($row_DVMINV['INVTOT']*$taxrate),2).'</td>' ;
    
    echo '<td width="225" align="left" class="Verdana13">'.$row_DVMINV['INVDESCR'].'</td>
  
  </tr>';
  }
  while ($row_DVMINV=mysqli_fetch_assoc($DVMINV));
  
  ?>
  
</table>
    </div>
    
    <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">&nbsp;<br />Inventory Register Summary <?php if ($can == 1) {echo ' (Returned Product = ' . $cancsum .')' ;} ?></td>
    </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td width="22%" height="18" class="Verdana12">&nbsp;</td>
    <td width="28%" class="Verdana12">Net</td>
    <td width="26%" align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0]); ?></td>
    <td width="24%" class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" class="Verdana12">&nbsp;</td>
    <td class="Verdana12">Tax</td>
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0]*$taxrate); ?></td>
    <td class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="22" valign="top" class="Verdana13B">Grand Total</td>
    <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n', $row_NET[0]* (1+$taxrate)); ?></td>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>
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
