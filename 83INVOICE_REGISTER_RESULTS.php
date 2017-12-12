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

$iclient = $_GET['client'] ;
$can = $_GET['checkcanbox'] ;

$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 
$file2search=$_GET['file2search'];

$canc = "" ;
if ($can == 1) {
$canc = " AND INSTR(PONUM,'CANC') <> 0 " ;
$get_Canc = "select ROUND(sum(substr(ponum,5,16)),2) as CANCEL from $file2search where instr(ponum,'CANC') <> 0 AND INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]' " ;
$Cancget = mysql_query($get_Canc, $tryconnection) or die(mysql_error()) ;
$row_cancsum = mysql_fetch_assoc($Cancget) ;
$cancsum = $row_cancsum['CANCEL'] ;
}
if (!isset($iclient)) {
$search_ARINVOI="SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE FROM $file2search WHERE INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]' $canc ORDER BY INVNO ASC";
$search_NET = "SELECT SUM(ITOTAL - PTAX - TAX) AS Total_NET FROM $file2search WHERE INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]'";
$search_TAX = "SELECT SUM(TAX) AS Total_TAX FROM $file2search WHERE  INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]'";
$search_PST = "SELECT SUM(PTAX) AS Total_PST FROM $file2search WHERE  INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]'";
}
else {
echo ' Starting temps ' ;
 $drop_it = "DROP TEMPORARY TABLE IF EXISTS TEMPI" ;
 $do_drop = mysql_query($drop_it, $tryconnection) or die(mysql_error()) ;
 $mktemp = "CREATE TEMPORARY TABLE TEMPI (INVNO CHAR(8), SALESMN CHAR(3), CUSTNO INT(7), COMPANY CHAR(40),INVDTE DATE, DUMMY DATE, PONUM CHAR(20),ITOTAL FLOAT(8,2),TAX FLOAT(7,2),PTAX FLOAT(7,2), IBAL FLOAT(8,2), AMTPAID FLOAT(8,2))" ;
 $do_mkt = mysql_query($mktemp, $tryconnection ) or die(mysql_error()) ;
 echo ' and stuffing it ' ;
 $temp1 = "INSERT INTO TEMPI SELECT INVNO, SALESMN, CUSTNO,COMPANY, INVDTE, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS DUMMY,PONUM,ITOTAL,TAX,PTAX, IBAL, AMTPAID FROM ARINVOI WHERE INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]' $canc AND CUSTNO = '$iclient' " ;
 $temp2 = "INSERT INTO TEMPI SELECT INVNO, SALESMN, CUSTNO,COMPANY, INVDTE, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS DUMMY,PONUM,ITOTAL,TAX,PTAX, IBAL, AMTPAID   FROM INVLAST WHERE INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]' $canc AND CUSTNO = '$iclient' " ;
 $temp3 = "INSERT INTO TEMPI SELECT INVNO, SALESMN, CUSTNO,COMPANY, INVDTE, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS DUMMY,PONUM,ITOTAL,TAX,PTAX, IBAL, AMTPAID  FROM ARYINVO WHERE INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]' $canc AND CUSTNO = '$iclient' " ;
 echo ' Executing ' ;
 $Do_t1 = mysql_query($temp1, $tryconnection) or die(mysql_error()) ;
 $Do_t2 = mysql_query($temp2, $tryconnection) or die(mysql_error()) ;
 $Do_t3 = mysql_query($temp3, $tryconnection) or die(mysql_error()) ;
 echo ' and the totals ' ;
 $search_ARINVOI="SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE FROM TEMPI ORDER BY DUMMY, INVNO ASC";
 $search_NET = "SELECT SUM(ITOTAL - PTAX - TAX) AS Total_NET FROM TEMPI ";
 $search_TAX = "SELECT SUM(TAX) AS Total_TAX FROM TEMPI ";
 $search_PST = "SELECT SUM(PTAX) AS Total_PST FROM TEMPI";
}
$ARINVOI=mysql_query($search_ARINVOI, $tryconnection ) or die(mysql_error());
$row_ARINVOI=mysql_fetch_assoc($ARINVOI);
$NET = mysql_query($search_NET, $tryconnection ) or die(mysql_error()) ;
$TAX = mysql_query($search_TAX, $tryconnection ) or die(mysql_error()) ;
$PST = mysql_query($search_PST, $tryconnection ) or die(mysql_error()) ;
$row_NET = mysql_fetch_array($NET) ;
$row_TAX = mysql_fetch_array($TAX) ;
$row_PST = mysql_fetch_array($PST) ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>INVOICE REGISTER FROM <?php echo $_GET['startdate'].' TO '.$_GET['enddate']; ?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload(){

window.resizeTo(790,725) ;
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
    <td colspan="9" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>
    </td>
    </tr>
    <tr id="prtpurpose">
    <td colspan="9" height="15" align="center" class="Verdana13"><?php if ($can == 1) {echo 'Cancelled ';} if (isset($iclient)) {echo 'Single Client ' ;}?>Invoice Register for <?php if ($startdate == $enddate) {echo $stdum ;} else {echo $stdum .' - '. $enddum ;}?><br />&nbsp;</td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="20" align="right">&nbsp;Staff</td>
    <td width="50" align="right">&nbsp;&nbsp;Inv.#</td>
    <td width="120" align="center">Date</td>
    <td width="194" align="center">Client&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td width="60" align="right">Amount&nbsp;&nbsp;</td>
    <td width="60" align="right"><?php echo substr($taxname,0,3); ?>&nbsp;&nbsp;&nbsp;</td>
    <td width="60" align="right"><?php if ($can == 1) {echo 'Reason ';} else {echo 'Total' ;} ?>&nbsp;&nbsp;&nbsp;</td>
    <td width="65" align="right">On Acct.&nbsp;&nbsp;&nbsp;</td>
    <td width="65" align="right">Payment&nbsp;</td>
  </tr>
  <tr>
    <td colspan="9" class="Verdana12" align="center">
    
    <div id="irresults2">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  
  do {
  echo '
  <tr id="'.$row_ARINVOI['INVNO'].'" onmouseover="highliteline(this.id,\'#DCF6DD\'); CursorToPointer(this.id);" onmouseout="whiteoutline(this.id)" onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search='.$_GET['file2search'].'&invdte='.$row_ARINVOI['INVDTE'].'&invno='.$row_ARINVOI['INVNO'].'\',\'_blank\')">
    <td width="" align="left" class="Verdana13">'.$row_ARINVOI['SALESMN'].'&nbsp;</td>
    <td width="" align="right" class="Verdana13">'.$row_ARINVOI['INVNO'].'&nbsp;</td>
    <td width="120" align="center" class="Verdana13">'.$row_ARINVOI['INVDTE'].'</td>
    <td width="204" class="Verdana13">'.substr($row_ARINVOI['COMPANY'],0,29).'</td>
    <td width="65" align="right" class="Verdana13">'.number_format(($row_ARINVOI['ITOTAL']-$row_ARINVOI['TAX']),2).'</td>
    <td width="65" align="right" class="Verdana13">'.$row_ARINVOI['TAX'].'</td>' ;
    if ($can == 1) {
    echo '<td width="65" align="right" class="Verdana13">'.$row_ARINVOI['PONUM'].'</td>' ;
    }
    else {
    echo '<td width="65" align="right" class="Verdana13">'.$row_ARINVOI['ITOTAL'].'</td>' ;
    }
    echo '<td width="65" align="right" class="Verdana13">'.$row_ARINVOI['IBAL'].'</td>
    <td width="65" align="right" class="Verdana13">'.$row_ARINVOI['AMTPAID'].'</td>
  </tr>';
  }
  while ($row_ARINVOI=mysql_fetch_assoc($ARINVOI));
  
  ?>
  
</table>
    </div>
    
    <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">&nbsp;<br />Invoice Register Summary <?php if ($can == 1) {echo ' (Cancelled Invoices = ' . $cancsum .')' ;} ?></td>
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
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_TAX[0]); ?></td>
    <td class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" class="Verdana12">&nbsp;</td>
    <td class="Verdana12">PST</td>
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_PST[0]); ?></td>
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
    <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0] + $row_TAX[0] + $row_PST[0]); ?></td>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="9">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="history.back()'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
