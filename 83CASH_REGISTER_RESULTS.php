<?php 
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);


if (!isset($_SESSION['expense']) && $_GET['expenses'] > 0){
$insertEXPENSE=sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, AMTPAID, DTEPAID, REFNO) VALUES ('%s', NOW(), '%s', '%s', '%s', NOW(), '%s')",
					"EXPENSE",
					0,
					$_GET['company'],
					-$_GET['expenses'],
					"Cash");
$insertEXPENSE=mysqli_query($tryconnection, $insertEXPENSE) or die(mysqli_error($mysqli_link));
$_SESSION['expense'] = 1;
}

$file2search=$_GET['file2search'];

$type = $_GET['typeofreport'] ;

$iclient = $_GET['client'] ;

if (!empty($_GET['startdate'])){
$startdate=$_GET['startdate'];
}
else {
$startdate='00/00/0000';
}
$stdum = $startdate ;

$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysqli_query($tryconnection, $startdate) or die(mysqli_error($mysqli_link));
$startdate=mysqli_fetch_array($startdate);

if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}
$enddum = $enddate ;

$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysqli_query($tryconnection, $enddate) or die(mysqli_error($mysqli_link));
$enddate=mysqli_fetch_array($enddate);
// Create a temporary table

if ($type == 1) { // the detailed report option.
$temp_table1 = "DROP TEMPORARY TABLE IF EXISTS ARTEMP" ;
$temp_table2 = "CREATE TEMPORARY TABLE ARTEMP SELECT * FROM $file2search WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'" ;
$temp_table3 = "INSERT INTO ARTEMP SELECT * FROM CASHDEP WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'" ;
$TAB1 = mysqli_query($tryconnection, $temp_table1) or die(mysqli_error($mysqli_link)) ;
$TAB2 = mysqli_query($tryconnection, $temp_table2) or die(mysqli_error($mysqli_link)) ;
$TAB3 = mysqli_query($tryconnection, $temp_table3) or die(mysqli_error($mysqli_link)) ;

$search_ARCASHR="SELECT INVNO,ARTEMP.CUSTNO,ARTEMP.SALESMN,ARTEMP.COMPANY,REFNO,AMTPAID, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM ARTEMP LEFT JOIN ARCUSTO ON ARTEMP.CUSTNO = ARCUSTO.CUSTNO WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]' AND INSTR(REFNO , 'DEP.AP') = 0 ORDER BY REFNO,ARCUSTO.COMPANY ASC";
}

else if ($type == 2){ // The summary report option.
$temp_table1 = "DROP TEMPORARY TABLE IF EXISTS ARTEMP1" ;
$temp_table2 = "CREATE TEMPORARY TABLE ARTEMP1 (INVNO CHAR(7),CUSTNO INT(7), SALESMN CHAR(3), COMPANY VARCHAR(35),INVDTE DATE,DTEPAID DATE,AMTPAID FLOAT(8,2),REFNO CHAR(8),PONUM CHAR(15))" ;
$temp_table3 = "INSERT INTO ARTEMP1 SELECT INVNO,CUSTNO,SALESMN,COMPANY, INVDTE,DTEPAID,AMTPAID,REFNO,PONUM FROM $file2search WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]' "  ;
$temp_table4 = "INSERT INTO ARTEMP1 SELECT INVNO,CUSTNO,SALESMN,COMPANY, INVDTE,DTEPAID,AMTPAID,REFNO,PONUM FROM CASHDEP WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'" ;
$temp_table5 = "DROP TEMPORARY TABLE IF EXISTS ARTEMP" ;
$temp_table6 = "CREATE TEMPORARY TABLE ARTEMP LIKE ARTEMP1" ;
$temp_table7 = "INSERT INTO ARTEMP SELECT INVNO,CUSTNO,SALESMN,COMPANY,INVDTE,DTEPAID, SUM(AMTPAID),REFNO,PONUM FROM ARTEMP1 GROUP BY REFNO, DTEPAID,CUSTNO" ;

$search_ARCASHR="SELECT INVNO,ARTEMP.CUSTNO,ARTEMP.SALESMN,ARTEMP.COMPANY,REFNO,AMTPAID, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM ARTEMP LEFT JOIN ARCUSTO ON ARTEMP.CUSTNO = ARCUSTO.CUSTNO WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]' AND INSTR(REFNO , 'DEP.AP') = 0 ORDER BY refno, ARCUSTO.COMPANY,REFNO ASC";


$TAB1 = mysqli_query($tryconnection, $temp_table1) or die(mysqli_error($mysqli_link)) ;
$TAB2 = mysqli_query($tryconnection, $temp_table2) or die(mysqli_error($mysqli_link)) ;
$TAB3 = mysqli_query($tryconnection, $temp_table3) or die(mysqli_error($mysqli_link)) ;
$TAB4 = mysqli_query($tryconnection, $temp_table4) or die(mysqli_error($mysqli_link)) ;
$TAB5 = mysqli_query($tryconnection, $temp_table5) or die(mysqli_error($mysqli_link)) ;
$TAB6 = mysqli_query($tryconnection, $temp_table6) or die(mysqli_error($mysqli_link)) ;
$TAB7 = mysqli_query($tryconnection, $temp_table7) or die(mysqli_error($mysqli_link)) ;
      }
else { // The specific client option.

$temp_table1 = "DROP TEMPORARY TABLE IF EXISTS ARTEMP" ;
$temp_table2 = "CREATE TEMPORARY TABLE ARTEMP SELECT * FROM ARCASHR WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]' AND CUSTNO = '$iclient'" ;
$temp_table3 = "INSERT INTO ARTEMP SELECT * FROM CASHDEP WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'  AND CUSTNO = '$iclient'" ;
$temp_table4 = "INSERT INTO ARTEMP SELECT * FROM LASTCASH WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'  AND CUSTNO = '$iclient'" ;
$temp_table5 = "INSERT INTO ARTEMP SELECT * FROM ARYCASH WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'  AND CUSTNO = '$iclient'" ;
$search_ARCASHR="SELECT INVNO,ARTEMP.CUSTNO,ARTEMP.SALESMN,ARTEMP.COMPANY,REFNO,AMTPAID, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM ARTEMP LEFT JOIN ARCUSTO ON ARTEMP.CUSTNO = ARCUSTO.CUSTNO WHERE  INSTR(REFNO , 'DEP.AP') = 0 ORDER BY DTEPAID,REFNO ASC";

$TAB1 = mysqli_query($tryconnection, $temp_table1) or die(mysqli_error($mysqli_link)) ;
$TAB2 = mysqli_query($tryconnection, $temp_table2) or die(mysqli_error($mysqli_link)) ;
$TAB3 = mysqli_query($tryconnection, $temp_table3) or die(mysqli_error($mysqli_link)) ;
$TAB4 = mysqli_query($tryconnection, $temp_table4) or die(mysqli_error($mysqli_link)) ;
$TAB5 = mysqli_query($tryconnection, $temp_table5) or die(mysqli_error($mysqli_link)) ;

}

$search_Cash = "SELECT SUM(AMTPAID) AS Total_Cash FROM ARTEMP WHERE INSTR(UPPER(REFNO),'CASH') <> 0 AND INVNO!='EXPENSE' AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_CHQ = "SELECT SUM(AMTPAID) AS Total_CHQE FROM ARTEMP WHERE (INSTR(UPPER(REFNO),'CHEQUE')<> 0 || INSTR(UPPER(REFNO),'CHQ')<> 0 ) AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_DCRD = "SELECT SUM(AMTPAID) AS Total_DCRD FROM ARTEMP WHERE INSTR(UPPER(REFNO),'DCRD') <> 0 AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_VISA = "SELECT SUM(AMTPAID) AS Total_VISA FROM ARTEMP WHERE INSTR(UPPER(REFNO),'VISA') <> 0 AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_MCRD = "SELECT SUM(AMTPAID) AS Total_MCRD FROM ARTEMP WHERE (INSTR(UPPER(REFNO),'MC') <> 0 || INSTR(UPPER(REFNO),'M/C') <> 0) AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_AMEX = "SELECT SUM(AMTPAID) AS Total_AMEX FROM ARTEMP WHERE INSTR(UPPER(REFNO),'AMEX') <> 0  AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_DINE = "SELECT SUM(AMTPAID) AS Total_DINE FROM ARTEMP WHERE INSTR(UPPER(REFNO),'DINERS') <> 0 AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_GE   = "SELECT SUM(AMTPAID) AS Total_GE FROM ARTEMP WHERE INSTR(UPPER(REFNO),'GE') <> 0 AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_CELL = "SELECT SUM(AMTPAID) AS Total_CELL FROM ARTEMP WHERE INSTR(UPPER(REFNO),'CELL') <> 0 AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$search_PND = "SELECT SUM(AMTPAID) AS Total_PND FROM ARTEMP WHERE INSTR(UPPER(REFNO),'POUND') <> 0 AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$CASH = mysqli_query($tryconnection, $search_Cash) or die(mysqli_error($mysqli_link)) ;
$CHQ = mysqli_query($tryconnection, $search_CHQ) or die(mysqli_error($mysqli_link)) ;
$DCRD = mysqli_query($tryconnection, $search_DCRD) or die(mysqli_error($mysqli_link)) ;
$VISA = mysqli_query($tryconnection, $search_VISA) or die(mysqli_error($mysqli_link)) ;
$MCRD = mysqli_query($tryconnection, $search_MCRD) or die(mysqli_error($mysqli_link)) ;
$AMEX = mysqli_query($tryconnection, $search_AMEX) or die(mysqli_error($mysqli_link)) ;
$DINE = mysqli_query($tryconnection, $search_DINE) or die(mysqli_error($mysqli_link)) ;
$GE = mysqli_query($tryconnection, $search_GE) or die(mysqli_error($mysqli_link)) ;
$CELL = mysqli_query($tryconnection, $search_CELL) or die(mysqli_error($mysqli_link)) ;
$PND = mysqli_query($tryconnection, $search_PND) or die(mysqli_error($mysqli_link)) ;

$ARCASHR=mysqli_query($tryconnection, $search_ARCASHR) or die(mysqli_error($mysqli_link));
$row_ARCASHR=mysqli_fetch_assoc($ARCASHR);

$row_CASH = mysqli_fetch_array($CASH) ;
$row_CHQ = mysqli_fetch_array($CHQ) ;
$row_DCRD = mysqli_fetch_array($DCRD) ;
$row_VISA = mysqli_fetch_array($VISA) ;
$row_MCRD = mysqli_fetch_array($MCRD) ;
$row_AMEX = mysqli_fetch_array($AMEX) ;
$row_DINE = mysqli_fetch_array($DINE) ;
$row_GE = mysqli_fetch_array($GE) ;
$row_CELL = mysqli_fetch_array($CELL) ;
$row_PND = mysqli_fetch_array($PND) ;


$query_EXPENSE="SELECT SUM(AMTPAID) AS EXPENSE FROM ARCASHR WHERE INVNO='EXPENSE' AND DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'";
$EXPENSE=mysqli_query($tryconnection, $query_EXPENSE) or die(mysqli_error($mysqli_link));
$row_EXPENSE=mysqli_fetch_assoc($EXPENSE);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CASH RECEIPTS REGISTER FROM <?php echo $_GET['startdate'].' TO '.$_GET['enddate']; ?></title>
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
    <td colspan="7" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>
    </td>
    </tr>
    </tr>
    <tr id="prtpurpose">
    <td colspan="7" height="15" align="center" class="Verdana13"><?php if ($type == 1) {echo 'Detailed Report ';} else if ($type == 2) {echo 'Summary Report ' ;} else {echo 'Single Client ' ;} ?>Cash Receipts Register for <?php if ($startdate == $enddate) {echo $stdum ;} else {echo $stdum .' - '. $enddum ;}?><br />&nbsp;</td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="12" align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Staff</td>
    <td width="69" align="center">Inv.#</td>
    <td width="60" align="center">Date</td>
    <td width="160"align="center">Client</td>
    <td width="90" align="right">Reference</td>
    <td width="120" align="right">Date Paid</td>
    <td align="right">Payment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  </tr>
  <tr>
    <td colspan="7" align="center" class="Verdana12">
    
    <div id="irresults">
    
    <table width="715" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="'.$row_ARCASHR['INVNO'].'" onmouseover="highliteline(this.id,\'#DCF6DD\')" onmouseout="whiteoutline(this.id)"';
  
  if (substr($row_ARCASHR['INVNO'],0,3)=='DEP.'){
  echo 'onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/DEPOSIT_RECEIPT.php?DTEPAID='.$row_ARCASHR['DTEPAID'].'&custno='.$row_ARCASHR['CUSTNO'].'\',\'_blank\')"';
  }
  
  echo '>
    <td width="8" align="left" class="Verdana13">'.$row_ARCASHR['SALESMN'].'</td>
    <td width="69" align="right" class="Verdana13">'.$row_ARCASHR['INVNO'].'&nbsp;</td>
    <td width="120" align="center" class="Verdana13">'.$row_ARCASHR['INVDTE'].'</td>
    <td width="198" class="Verdana13">'.substr($row_ARCASHR['COMPANY'],0,29).'</td>
    <td align="left" class="Verdana13">'.$row_ARCASHR['REFNO'].'</td>
    <td align="center" class="Verdana13">'.$row_ARCASHR['DTEPAID'].'</td>
    <td align="right" class="Verdana13">'.$row_ARCASHR['AMTPAID'].'</td>
  </tr>';
  }
  while ($row_ARCASHR=mysqli_fetch_assoc($ARCASHR));
  
  ?>
</table>
    </div>
    
    <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="25" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">Cash Receipts Summary</td>
    </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td width="22%" height="18">&nbsp;</td>
    <td width="28%">Cash</td>
    <td width="26%" align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CASH[0]); ?></td>
    <td width="24%">&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>Cheques</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CHQ[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>Visa</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_VISA[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>Master Card</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_MCRD[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>Amex</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_AMEX[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>Debit Card</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_DCRD[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>Diners Club</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_DINE[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>GE Credit Card</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_GE[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>Cell</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CELL[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>Pound</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_PND[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr class="Verdana12BRed">
    <td height="18">&nbsp;</td>
    <td>Expenses</td>
    <td align="right"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_EXPENSE['EXPENSE']); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="22" valign="top" class="Verdana13B">Grand Total</td>
    <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CASH[0]+$row_CHQ[0]+$row_DCRD[0]+$row_VISA[0]+$row_MCRD[0]+$row_AMEX[0]+$row_DINE[0]+$row_GE[0]+$row_CELL[0]+$row_PND[0]+$row_EXPENSE['EXPENSE']); ?></td>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="7">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="history.back()" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
