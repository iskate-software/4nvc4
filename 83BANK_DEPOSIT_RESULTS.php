<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);

/* Define the searches for the totals of each payment type.*/
$search_CashT = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE REFNO = 'CASH' ";
$search_ChequeT = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE INSTR(REFNO,'CHEQUE') <> 0 OR INSTR(REFNO,'CHQ') <> 0 ";
$search_DCRDT = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE REFNO = 'DCRD' ";
$search_VISAT = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE REFNO = 'VISA'";
$search_MCRDT = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE INSTR(REFNO,'MC') <> 0 OR INSTR(REFNO,'M/C') <> 0 ";
$search_AMEXT = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE REFNO = 'AMEX'";
$search_DINET = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE REFNO = 'DINER' ";
$search_GET   = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE REFNO = 'GE' ";
$search_CELLT = "SELECT SUM(AMTPAID) FROM ARCASHR WHERE REFNO = 'CELL' ";

/*  and execute them*/
$CASHT = mysql_query($search_CashT, $tryconnection ) or die(mysql_error()) ;
$CHEQUET = mysql_query($search_ChequeT, $tryconnection )  or die(mysql_error()) ;
$DCRDT = mysql_query($search_DCRDT, $tryconnection ) or die(mysql_error()) ;
$VISAT = mysql_query($search_VISAT, $tryconnection ) or die(mysql_error()) ;
$MCRDT = mysql_query($search_MCRDT, $tryconnection ) or die(mysql_error()) ;
$AMEXT = mysql_query($search_AMEXT, $tryconnection ) or die(mysql_error()) ;
$DINET = mysql_query($search_DINET, $tryconnection ) or die(mysql_error()) ;
$GET   = mysql_query($search_GET, $tryconnection )   or die(mysql_error()) ;
$CELLT = mysql_query($search_CELLT, $tryconnection ) or die(mysql_error()) ;


$row_CASHT = mysqli_fetch_array($CASHT) ;
$row_CHEQUET = mysqli_fetch_array($CHEQUET) ;
$row_DCRDT = mysqli_fetch_array($DCRDT) ;
$row_VISAT = mysqli_fetch_array($VISAT) ;
$row_MCRDT = mysqli_fetch_array($MCRDT) ;
$row_AMEXT = mysqli_fetch_array($AMEXT) ;
$row_DINET = mysqli_fetch_array($DINET) ;
$row_GET = mysqli_fetch_array($GET) ;
$row_CELLT = mysqli_fetch_array($CELLT) ;


// Define the individual row data for each type here.
$search_CASH   ="SELECT * FROM ARCASHR WHERE REFNO = 'CASH' ";
$search_CHEQUE ="SELECT CUSTNO,COMPANY,SUM(AMTPAID) AS AMTPAID FROM ARCASHR WHERE REFNO = 'CHEQUE' GROUP BY CUSTNO ";
$search_DCRD   ="SELECT * FROM ARCASHR WHERE REFNO = 'DCRD' ";
$search_VISA   ="SELECT * FROM ARCASHR WHERE REFNO = 'VISA' ";
$search_MC     ="SELECT * FROM ARCASHR WHERE INSTR(REFNO,'MC') <> 0 OR INSTR(REFNO,'M/C')";
$search_AMEX   ="SELECT * FROM ARCASHR WHERE REFNO = 'AMEX' ";
$search_DINE  ="SELECT * FROM ARCASHR WHERE REFNO = 'DINER' ";
$search_GE     ="SELECT * FROM ARCASHR WHERE REFNO = 'GE' ";
$search_CELL   ="SELECT * FROM ARCASHR WHERE REFNO = 'CELL' ";


$CASH=mysql_query($search_CASH, $tryconnection ) or die(mysql_error());
$CHEQUE=mysql_query($search_CHEQUE, $tryconnection ) or die(mysql_error());
$DCRD=mysql_query($search_DCRD, $tryconnection ) or die(mysql_error());
$VISA=mysql_query($search_VISA, $tryconnection ) or die(mysql_error());
$MC=mysql_query($search_MC, $tryconnection ) or die(mysql_error());
$AMEX=mysql_query($search_AMEX, $tryconnection ) or die(mysql_error());
$DINE=mysql_query($search_DINE, $tryconnection ) or die(mysql_error());
$GE=mysql_query($search_GE, $tryconnection ) or die(mysql_error());
$CELL=mysql_query($search_CELL, $tryconnection ) or die(mysql_error());


$row_CASH = mysqli_fetch_assoc($CASH) ;
$row_CHEQUE = mysqli_fetch_assoc($CHEQUE) ;
$row_DCRD = mysqli_fetch_assoc($DCRD) ;
$row_VISA = mysqli_fetch_assoc($VISA) ;
$row_MCRD = mysqli_fetch_assoc($MC) ;
$row_AMEX = mysqli_fetch_assoc($AMEX) ;
$row_DINE = mysqli_fetch_assoc($DINE) ;
$row_GE = mysqli_fetch_assoc($GE) ;
$row_CELL = mysqli_fetch_assoc($CELL) ;


if (isset($_POST['printbd'])){
$bank_deposit="INSERT INTO CASHDEP SELECT * FROM ARCASHR";
mysql_query($bank_deposit, $tryconnection ) or die(mysql_error());
$bank_deposit="DELETE FROM ARCASHR";
mysql_query($bank_deposit, $tryconnection ) or die(mysql_error());
$bank_deposit="TRUNCATE TABLE ARCASHR";
mysql_query($bank_deposit, $tryconnection ) or die(mysql_error());
$closewin="window.print(); document.location='../../INDEX.php';";
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>BANK DEPOSIT <?php echo $_POST['startdate']; ?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload(){
<?php echo $closewin; ?>
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
  <tr class="Verdana14B">
    <td width="10" height="40" align="left"></td>
    <td colspan="2" align="center"><script type="text/javascript">document.write(localStorage.hospname);</script></td>
    <td align="right" width="150"></td>
  </tr>
  <tr class="Verdana12">
    <td width="130" align="left"><?php echo date('m/d/Y'); ?></td>
    <td colspan="2" align="center">BANK DEPOSIT REPORT</td>
    <td align="right" width="150"><?php echo date('H:i:s'); ?></td>
  </tr>
<tr><td colspan="4">

<div id="bank_deposit">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  
 <!--CASH--> 
  
  <tr <?php if (empty($row_CASH)){echo " class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12"><strong>Cash</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr<?php if (empty($row_CASH)){echo " class='hidden'";} ?>>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0">
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_CASH['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_CASH['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_CASH=mysqli_fetch_assoc($CASH));
  
  ?>
  <tr<?php if (empty($row_CASHT)){echo " class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if (empty($row_CASHT)){echo " class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12">Subtotal Cash</td>
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CASHT[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>


 <!--CHEQUES--> 
  
  <tr <?php if (empty($row_CHEQUE)){echo " class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12"><strong>Cheques</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr<?php if (empty($row_CHEQUE)){echo " class='hidden'";} ?>>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0">
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_CHEQUE['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_CHEQUE['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_CHEQUE=mysqli_fetch_assoc($CHEQUE));
  
  ?>
  <tr<?php if (empty($row_CHEQUET)){echo " class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if (empty($row_CHEQUET)){echo " class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12">Subtotal Cheques</td>
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CHEQUET[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>


  <tr<?php if ($row_CASHT[0]+$row_CHEQUET[0]==0){echo " class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if ($row_CASHT[0]+$row_CHEQUET[0]==0){echo " class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12B">Total Cash and Cheques</td>
    <td align="right" class="Verdana12B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CASHT[0]+$row_CHEQUET[0]); ?></td>
    <td>&nbsp;</td>
  </tr>



 <!--VISA--> 
  
  <tr <?php if (empty($row_VISA)){echo " class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12"><strong>Visa</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr<?php if (empty($row_VISA)){echo " class='hidden'";} ?>>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0">
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_VISA['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_VISA['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_VISA=mysqli_fetch_assoc($VISA));
  
  ?>
  <tr<?php if (empty($row_VISAT)){echo " class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if (empty($row_VISAT)){echo " class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12B">Total Visa</td>
    <td align="right" class="Verdana12B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_VISAT[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>


 <!--MASTERCARD--> 
  
  <tr <?php if (empty($row_MCRD)){echo " class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12"><strong>Mastercard</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr<?php if (empty($row_MCRD)){echo " class='hidden'";} ?>>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0">
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_MCRD['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_MCRD['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_MCRD=mysqli_fetch_assoc($MC));
  
  ?>
  <tr<?php if (empty($row_MCRDT)){echo " class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if (empty($row_MCRDT)){echo " class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12B">Total Master Card</td>
    <td align="right" class="Verdana12B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_MCRDT[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>

  
  
 <!--AMEX--> 
  
  <tr <?php if (empty($row_AMEX)){echo " class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12"><strong>American Express</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr<?php if (empty($row_AMEX)){echo " class='hidden'";} ?>>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0">
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_AMEX['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_AMEX['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_AMEX=mysqli_fetch_assoc($AMEX));
  
  ?>
  <tr<?php if (empty($row_AMEXT)){echo " class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if (empty($row_AMEXT)){echo " class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12B">Total American Express</td>
    <td align="right" class="Verdana12B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_AMEXT[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>

 <!--DEBIT CARD--> 
  
  <tr <?php if (empty($row_DCRD)){echo " class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12"><strong>Debit Card</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr<?php if (empty($row_DCRD)){echo " class='hidden'";} ?>>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0">
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_DCRD['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_DCRD['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_DCRD=mysqli_fetch_assoc($DCRD));
  
  ?>
  <tr<?php if (empty($row_DCRDT)){echo " class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if (empty($row_DCRDT)){echo " class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12B">Total Debit Card</td>
    <td align="right" class="Verdana12B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_DCRDT[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>




 <!--DINERS--> 
  
  <tr<?php if (empty($row_DINE)){echo " class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12"><strong>Diners</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr<?php if (empty($row_DINE)){echo " class='hidden'";} ?>>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0">
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_DINE['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_DINE['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_DINE=mysqli_fetch_assoc($DINE));
  
  ?>
  <tr<?php if (empty($row_DINET)){echo " class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr<?php if (empty($row_DINET)){echo " class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12B">Total Diners</td>
    <td align="right" class="Verdana12B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_DINET[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>



 <!--GE--> 
  
  <tr <?php if (empty($row_GE)){echo "class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12"><strong>GE Credit Card</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr <?php if (empty($row_GE)){echo "class='hidden'";} ?>>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0" >
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_GE['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_GE['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_GE=mysqli_fetch_assoc($GE));
  
  ?>
  <tr <?php if (empty($row_GET)){echo "class='hidden'";} ?>>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if (empty($row_GET)){echo "class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12B">Total GE Credit Card</td>
    <td align="right" class="Verdana12B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_GET[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>






 <!--CELL--> 
  
  <tr <?php if (empty($row_CELL)){echo "class='hidden'";} ?>>
    <td width="130" height="30" align="right" class="Verdana12B"><strong>Cell</strong></td>
    <td width="300" class="Verdana12B"></td>
    <td width="150" align="right" class="Verdana12B"></td>
    <td align="center" class="Verdana12B"></td>
  </tr>
  <tr>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0" <?php if (empty($row_CELL)){echo "class='hidden'";} ?>>
  <?php 
  // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_CELL['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_CELL['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_CELL=mysqli_fetch_assoc($CELL));
  
  ?>
  <tr <?php if (empty($row_CELLT)){echo "class='hidden'";} ?>>
    <td width="130" align="right" ></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr <?php if (empty($row_CELLT)){echo "class='hidden'";} ?>>
    <td width="130" height="20" align="right" class="Verdana12B"></td>
    <td align="right" class="Verdana12B">Total Cell</td>
    <td align="right"  class="Verdana12B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CELLT[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>

  
 
 <!--POUND 
  
  <tr class="Verdana12">
    <td width="130" height="30" align="right"><strong>Pound</strong></td>
    <td width="300"></td>
    <td align="right" width="150"></td>
    <td align="center"></td>
  </tr>
  <tr>
    <td colspan="6" align="center" class="Verdana12">
        
    <table width="100%" cellspacing="0" cellpadding="0">
  <?php 
 /* // HERE IT IS.
  do {
  echo '
  <tr id="">
    <td height="18" width="130" align="right" class="Verdana13"></td>
    <td width="300" class="Verdana13">'.$row_DCRD['COMPANY'].'</td>
    <td align="right" class="Verdana13" width="150">'.$row_DCRD['AMTPAID'].'</td>
    <td align="center" class="Verdana13" width="150"></td>
  </tr>';
  }
  while ($row_DCRD=mysql_fetch_assoc($DCRD));
*/  
  ?>
  <tr>
    <td width="130" align="right" class="Verdana13"></td>
    <td class="Verdana13" align="right"></td>
    <td align="right"><hr width="80" noshade="noshade" style="margin-right:0px;" color="#000000" size="1"   /></td>
    <td>&nbsp;</td>
  </tr>
  <tr class="Verdana13B">
    <td height="20" width="130" align="right"></td>
    <td align="right">Total</td>
    <td align="right"><?php //setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_DCRDT[0]); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
-->
<tr <?php if ($row_CASHT[0]+$row_CHEQUET[0]+$row_DCRDT[0]+$row_VISAT[0]+$row_MCRDT[0]+$row_AMEXT[0]+$row_DINET[0]+$row_GET[0]+$row_CELLT[0]/*+$row_PND[0]*/==0) {echo "class='hidden'";} ?>>
    <td width="130" height="50" align="right"></td>
    <td width="300" align="right" class="Verdana14B">Grand Total</td>
    <td align="right" width="150" class="Verdana14B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CASHT[0]+$row_CHEQUET[0]+$row_DCRDT[0]+$row_VISAT[0]+$row_MCRDT[0]+$row_AMEXT[0]+$row_DINET[0]+$row_GET[0]+$row_CELLT[0]/*+$row_PND[0]*/); ?></td>
    <td width="150" align="center"></td>
</tr>
</table></div>

</td>
</tr>
 <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="4">
<form method="post" action="">
    <input name="printbd" type="submit" class="button" id="printbd" value="PRINT" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>

</form>  
</tr>
</table>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>