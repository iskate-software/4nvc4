<?php
session_start();
//unset($_SESSION['']);
require_once('../../tryconnection.php');
require_once('../../ASSETS/tax.php');

$client=$_SESSION['client'];

mysql_select_db($database_tryconnection, $tryconnection);


if (!isset($_SESSION['tea'])){
$query_INVNO = "SELECT LASTINV FROM CRITDATA ";
$INVNO = mysql_query($query_INVNO, $tryconnection) or die(mysql_error());
$row_INVNO = mysql_fetch_assoc($INVNO);
$_SESSION['minvno'] = $row_INVNO['LASTINV'] + 1 ;
$query_INVNO = "UPDATE CRITDATA SET LASTINV = '$_SESSION[minvno]'" ;
$INVNO = mysql_query($query_INVNO,$tryconnection) or die(mysql_error()) ;
$_SESSION['tea']='1';
$_SESSION['amtpaid'] = 0 ;
} else { if ($_SESSION['tea']=='1') { $_SESSION['tea']++ ;} else {unset($_SESSION['tea']);}}

$query_Staff = "SELECT * FROM STAFF WHERE SIGNEDIN=1";
$Staff = mysql_query($query_Staff, $tryconnection) or die(mysql_error());
$row_Staff = mysql_fetch_assoc($Staff);

$query_Doctor = "SELECT * FROM DOCTOR WHERE SIGNEDIN=1";
$Doctor = mysql_query($query_Doctor, $tryconnection) or die(mysql_error());
$row_Doctor = mysql_fetch_assoc($Doctor);

// use today's date to figure out the taxname and rate.

$STRUC_TAX = "SELECT DATE_FORMAT(NOW(),'%m/%d/%Y') AS TODAY" ;
$get_Date = mysql_query($STRUC_TAX, $tryconnection) or die(mysql_error()) ;
$minvdte = mysql_fetch_array($get_Date) ;

$query_TAX = "SELECT HTAXNAME, HOTAXNAME, HGST, HOGST, DATE_FORMAT(HGSTDATE,'%m/%d/%Y') AS HGSTDATE, HGSTNO FROM CRITDATA";
$TAX = mysql_query($query_TAX, $tryconnection) or die(mysql_error());
$row_TAX = mysql_fetch_assoc($TAX);

$hgstdate=strtotime($row_TAX['HGSTDATE']);

$taxnumber = $row_TAX['HGSTNO'] ;
if ($minvdte < $hgstdate){
$nametax =$row_TAX['HOTAXNAME'];
$taxvalue= $row_TAX['HOGST'] ;
}

else if ($minvdte >= $hgstdate){
$nametax=$row_TAX['HTAXNAME'];
$taxvalue=$row_TAX['HGST'];
}

//ADD GST
if (isset($_POST['addgst'])){
$_SESSION['minvdte']=$_POST['minvdte'];
$tax=($taxvalue)/100 * $_POST['invamount'];
$tax=number_format(round($tax,2),2);
$_SESSION['realamount'] = $_POST['invamount'] ;

$invamount=$_POST['invamount'];
$invamount=number_format(round($invamount,2),2);

$_POST['itotal'] = $invamount + $tax ;
$ponum=$_POST['ponum'];
$show='1';
}
//FEE INCLUDES GST
else if (isset($_POST['inclgst'])){
$_SESSION['minvdte']=$_POST['minvdte'];
// calculate the real invoice amount
$_SESSION['realamount'] = $_POST['invamount'] / (1+($taxvalue/100.00)) ;
$_SESSION['realamount'] = number_format(round($_SESSION['realamount'],2),2) ;
$_POST['invamount'] = $_SESSION['realamount'] ;
$tax=($taxvalue)/100 * $_POST['invamount'];
$tax=number_format(round($tax,2),2);

$invamount= $_SESSION['realamount'];
$invamount=number_format(round($invamount,2),2);

$_POST['itotal'] = $invamount ;

$ponum=$_POST['ponum'];
$show='1';
}
//NO GST
else if (isset($_POST['nogst'])){
$_SESSION['minvdte']=$_POST['minvdte'];
$tax="0.00";

$invamount=$_POST['invamount'];
$invamount=number_format(round($invamount,2),2);

$_SESSION['realamount'] = $_POST['invamount'];

$_POST['itotal'] = $invamount ;

$ponum=$_POST['ponum'];
$show='1';
}

// $_SESSION['amtpaid'] = $_POST['amtpaid'] ;

else if (isset($_POST['ok'])){
$invdte=$_SESSION['minvdte'].' '.date('H:i:s');
$ibal=$_POST['itotal']-$_POST['amtpaid'];
if ($_POST['amtpaid'] <> 0 ) {
    $update_CUSTO = "UPDATE ARCUSTO SET BALANCE = BALANCE + '$ibal', ldate = STR_TO_DATE('$invdte', '%m/%d/%Y'),lastpay =  STR_TO_DATE('$invdte', '%m/%d/%Y') WHERE CUSTNO = '$client' LIMIT 1" ; }
  else {
    $update_CUSTO = "UPDATE ARCUSTO SET BALANCE = BALANCE + '$ibal', ldate = STR_TO_DATE('$invdte', '%m/%d/%Y') WHERE CUSTNO = '$client' LIMIT 1 " ; 
    }
  $query_CUSTO = mysql_query($update_CUSTO, $tryconnection) or die(mysql_error()) ;

$insert_ARARECV = "INSERT INTO ARARECV (INVNO, INVDTE, INVTIME, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID,DTEPAID, IBAL) 
VALUES ('$_SESSION[minvno]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'), STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'), '$client', '".mysql_real_escape_string($_POST['company'])."',
'".mysql_real_escape_string($_POST['salesmn'])."', '".mysql_real_escape_string($_POST['ponum'])."', '$_POST[refno]', '$_POST[tax]', '0.00','$_POST[itotal]','0.00', '$_POST[amtpaid]', '0000-00-00', '$ibal') ";
$RESULT = mysql_query($insert_ARARECV, $tryconnection) or die(mysql_error());
if ($_POST['amtpaid'] <> 0 ) {
 $add_PAYMENT = "UPDATE ARARECV SET DTEPAID =  STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s') WHERE INVNO = '$_SESSION[minvno]'" ;
 $update_PAYMENT = mysql_query($add_PAYMENT, $tryconnection) or die(mysql_error()) ;
}

			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[csminvno] '" ;
		     $FOR_INVOICE = mysql_query($GET_UNIQUE1, $tryconnection) or die(mysql_error()) ;
		     $row_ARFORIN = mysql_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;
		     
$insert_ARINVOI = "INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, TAX, ITOTAL,DISCOUNT,PTAX, AMTPAID,DTEPAID, IBAL, INVPET, UNIQUE1) 
VALUES ('$_SESSION[minvno]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'), '$client', '".mysql_real_escape_string($_POST['company'])."',
'".mysql_real_escape_string($_POST['salesmn'])."', '".mysql_real_escape_string($_POST['ponum'])."', '$_POST[refno]', '$_POST[tax]', '$_POST[itotal]', '0.00','0.00','$_POST[amtpaid]', '0000-00-00', '$ibal' ,'Summary Invoice', '$uni' )";

$RESULT1 = mysql_query($insert_ARINVOI, $tryconnection) or die(mysql_error());
if ($_POST['amtpaid'] <> 0 ) {
 $add_PAYMENT1 = "UPDATE ARINVOI SET DTEPAID =  STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s') WHERE INVNO = '$_SESSION[minvno]'" ;
 $update_PAYMENT1 = mysql_query($add_PAYMENT1, $tryconnection) or die(mysql_error()) ;
 }
 
$insert_ARGST = "INSERT INTO ARGST (INVNO, INVDTE, CUSTNO, GST, PROVTAX, ITOTAL, GSTNO, UNIQUE1) 
VALUES ('$_SESSION[minvno]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'), '$client','$_POST[tax]', '0.00', '$_POST[itotal]', '$taxnumber', '$uni' )";
$RESULT2 = mysql_query($insert_ARGST, $tryconnection) or die(mysql_error());
 
if ($_POST['amtpaid'] <> 0 ) {
 $insert_ARCASHR = "INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, REFNO, DTEPAID, AMTPAID) 
 VALUES ('$_SESSION[minvno]', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'), '$client', '".mysql_real_escape_string($_POST['company'])."', '$_POST[refno]',
 STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'),'$_POST[amtpaid]' )";
 $RESULT3 = mysql_query($insert_ARCASHR, $tryconnection) or die(mysql_error());
}

$insert_SALESCAT = "INSERT INTO SALESCAT (INVMAJ,INVTOT,INVGST,INVTAX,INVDISC,INVDOC,INVORDDOC,INVDESC,INVPAID,INVAR,INVLGSM,INVREVCAT,INVDTE,INVNO, INVCUST,INVTNO, UNIQUE1) 
VALUES ('99', '$_SESSION[realamount]','$_POST[tax]','0.00','0.00','Hospital', 'Hospital','".mysql_real_escape_string($_POST['ponum'])."','$_POST[amtpaid]','$ibal','0','99', 
STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'),'$_SESSION[minvno]',  '$client','0', '$uni' )";
$RESULT4 = mysql_query($insert_SALESCAT, $tryconnection) or die(mysql_error());
// and do the tax
if ($_POST['tax'] <> 0) {
 $insert_SALESCATT = "INSERT INTO SALESCAT (INVMAJ,INVTOT,INVGST,INVTAX,INVDISC,INVDOC,INVORDDOC,INVDESC,INVPAID,INVAR,INVLGSM,INVREVCAT,INVDTE,INVNO, INVCUST,INVTNO, UNIQUE1) 
 VALUES ('90', '$_POST[tax]','0.00','0.00','0.00','Hospital', 'Hospital','Tax','0','0','0','90', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'),'$_SESSION[minvno]',  '$client','0', '$uni' )";
 $RESULT5 = mysql_query($insert_SALESCATT, $tryconnection) or die(mysql_error());
}

$insert_DVMINV = "INSERT INTO DVMINV (INVNO, INVCUST,INVPET,INVDATETIME,INVMAJ,INVMIN,INVORDDOC,INVDOC,INVSTAFF,INVUNITS,INVDESCR,INVPRICE,INVTOT,INVREVCAT,INVTAX,INVDECLINE,PETNAME, UNIQUE1) 
VALUES ('$_SESSION[minvno]','$client','0', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'),'99','0','Hospital','Hospital','".mysql_real_escape_string($_POST['salesmn'])."', '1','Summary Invoice',
'$_SESSION[realamount]','$_SESSION[realamount]','99','$_POST[tax]','0','Summary Invoice' ,'$uni')";
$RESULT6 = mysql_query($insert_DVMINV, $tryconnection) or die(mysql_error());
// and do the tax
if ($_POST['tax'] <> 0) {
 $insert_DVMINVT = "INSERT INTO DVMINV (INVNO, INVCUST,INVPET,INVDATETIME,INVMAJ,INVMIN,INVORDDOC,INVDOC,INVSTAFF,INVUNITS,INVDESCR,INVPRICE,INVTOT,INVREVCAT,INVTAX,INVDECLINE,PETNAME, UNIQUE1) 
 VALUES ('$_SESSION[minvno]','$client','0', STR_TO_DATE('$invdte', '%m/%d/%Y %H:%i:%s'),'90','0','Hospital','Hospital','".mysql_real_escape_string($_POST['salesmn'])."', '1','Tax','$_POST[tax]','$_POST[tax]','90','0.00','0','Summary Invoice', '$uni' )";
 $RESULT7 = mysql_query($insert_DVMINVT, $tryconnection) or die(mysql_error());
}

header("Location:../../CLIENT/CLIENT_SEARCH_SCREEN.php");
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/ClientPatientTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>SUMMARY INVOICE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>


<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload()
{
<?php if (!isset($show)) {echo "document.summary_invoices.ponum.focus();";} else {echo "document.summary_invoices.amtpaid.focus();";}?>
document.summary_invoices.company.value=sessionStorage.custname;
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

<form action="" class="FormDisplay" name="summary_invoices" method="post">
<input type="hidden" name="company" value=""  />
<table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td height="60" colspan="3" valign="top">
    <table width="733" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="5" rowspan="4" align="left" class="Verdana12B">&nbsp;</td>
        <td height="20" colspan="2" align="left" class="Verdana12B">
          <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
          </span></td>
        <td width="160" rowspan="2" align="center" valign="middle"><span class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>          
        </span>        </td>
        <td width="139" colspan="2" rowspan="4" align="center"><table width="100%" border="1" cellspacing="0" cellpadding="0" id="table2">
            <tr>
              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="20" colspan="2" align="center"><span class="Verdana11B" style="background-color:#FFFF00"><?php echo date('m/d/Y'); ?></span></td>
                  </tr>
                  <tr>
                    <td width="41%" height="20" align="right" class="Labels2">        
					<script type="text/javascript">document.write(sessionStorage.custprevbal);</script></td>
                    <td width="59%" height="20" class="Labels2">&nbsp;Balance</td>
                  </tr>
                  <tr>
                    <td height="20" align="right" class="Labels2">
                    <script type="text/javascript">document.write(sessionStorage.custcurbal);</script></td>
                    <td height="20" class="Labels2">&nbsp;Credit</td>
                  </tr>
                  <tr>
                    <td height="20" align="right" class="Labels2">&nbsp;</td>
                    <td height="20" class="Labels2">&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="20" align="right" class="Labels2">&nbsp;</td>
                    <td height="20" class="Labels2">&nbsp;</td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td width="5" rowspan="2" align="left">&nbsp;</td>
        <td width="424" height="20" align="left" class="Verdana12">        
		<script type="text/javascript">document.write(sessionStorage.custphone);</script></td>
      </tr>
      <tr>
        <td height="20" align="left"  class="Verdana12"><script type="text/javascript">document.write(sessionStorage.address+'<br />'+sessionStorage.city);</script></td>
        <td height="20" align="left"  class="Labels2">&nbsp;</td>
      </tr>
      <tr>
        <td height="20" colspan="2" align="left"><span class="Verdana12B" style="background-color:#FFFF00">&nbsp;Invoice #: <?php echo $_SESSION['minvno']; ?></span></td>
        <td height="20" align="left">&nbsp;</td>
      </tr>
    </table>    </td>
    </tr>
  <tr>
    <td height="" colspan="3" align="center" valign="top">
    
    
    <table class="table" width="733" height="411" border="1" cellpadding="0" cellspacing="0" >
    <tr>
    <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="58%" height="256" align="center" valign="middle">
        <table width="90%" border="1" cellspacing="0" cellpadding="0" frame="box" rules="none">
          <tr>
            <td width="3%" rowspan="6">&nbsp;</td>
            <td height="5" colspan="2" class="Verdana11">&nbsp;</td>
            </tr>
          <tr>
            <td width="29%" height="30" class="Verdana11">Invoice Date:</td>
            <td width="68%" height="30"><input name="minvdte" type="text" class="Input" id="minvdte" value="<?php if (isset($_SESSION['minvdte'])) {echo $_SESSION['minvdte'];} else {echo date('m/d/Y');} ?>" size="10" maxlength="12" onclick="ds_sh(this)" onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);" /></td>
          </tr>
          <tr>
            <td height="30" class="Verdana11">Invoice Reason:</td>
            <td height="30"><input name="ponum" type="text" class="Input" id="ponum"  onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);"  size="30" value="<?php echo $ponum; ?>"/></td>
          </tr>
          <tr>
            <td height="30" class="Verdana11">Invoice Amount:</td>
            <td height="30"><input name="invamount" type="text" class="Inputright" id="invamount"  onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);"  size="8" value="<?php echo $invamount; ?>" <?php if (!isset($show)){echo "readonly'";} ?>/></td>
          </tr>
          <tr>
            <td height="30" class="Verdana11"><?php echo $nametax; ?> Amount:</td>
            <td height="30"><input name="tax" type="text" id="tax"  style="border:none; text-align:right;" readonly="readonly" size="8" value="<?php echo $tax; ?>"/></td>
          </tr>
          <tr>
            <td height="30" class="Verdana11">Total Amount:</td>
            <td height="30"><input name="itotal" type="text" id="itotal"  style="border:none; text-align:right;" readonly="readonly" size="8" value="<?php echo number_format($tax+$invamount,2); ?>"/></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td height="5" colspan="2" class="Verdana11">&nbsp;</td>
            </tr>
        </table></td>
        <td width="42%" align="center">
        <table width="90%" border="1" cellspacing="0" cellpadding="0" frame="box" rules="none" <?php if (!isset($show)){echo "style='display:none;'";} ?>>
          <tr>
            <td height="15" align="center" class="Verdana11">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" align="center" class="Verdana11" valign="bottom">Payment Method</td>
            </tr>
          <tr>
            <td height="30" align="center" class="Verdana11" valign="top">
            <select name="refno" id="refno">
                <option value="Cash">&nbsp;&nbsp;Cash</option>
                <option value="Cheque">&nbsp;&nbsp;Cheque</option>
                <option value="DCrd">&nbsp;&nbsp;DCrd</option>
                <option value="Visa">&nbsp;&nbsp;Visa</option>
                <option value="MC">&nbsp;&nbsp;M/C</option>
                <option value="Amex">&nbsp;&nbsp;Amex</option>
                <option value="Diners">&nbsp;&nbsp;Diners</option>
                <option value="GE">&nbsp;&nbsp;GE</option>
                <option value="ONAC" id="onac2" onclick="document.routine.payment.value='0';" selected="selected">&nbsp;&nbsp;On Account</option>
                <option value="PDC">&nbsp;&nbsp;Post Dated Cheque</option>
                <option value="Pound">&nbsp;&nbsp;Pound</option>
                <option value="Cell">&nbsp;&nbsp;Cell</option>            
            </select>            </td>
            </tr>
          <tr>
            <td height="30" align="center" class="Verdana11" valign="top">Payment Amount:
              <input name="amtpaid" type="text" class="Input" id="amtpaid"  onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);"  size="8"/></td>
            </tr>
          <tr>
            <td height="35" align="center" valign="bottom" class="Verdana11">Staff</td>
          </tr>
          <tr>
            <td height="43" align="center" class="Verdana11" valign="top">
            
            <select name="salesmn" id="salesmn">
      		<?php
			do { echo '<option value="'.$row_Staff['STAFF'].'">'.$row_Staff['STAFF'].'</option>'; 
					} while ($row_Staff = mysql_fetch_assoc($Staff));
			do { echo '<option value="'.$row_Doctor['DOCTOR'].'">'.$row_Doctor['DOCTOR'].'</option>';
					} while ($row_Doctor = mysql_fetch_assoc($Doctor));
			?>
    		</select>
            
            
            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
    </tr>
    </table>    </td>
  </tr>
  <tr <?php if (isset($show)){echo "style='display:none;'";} ?>>
    <td height="35" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF">

     <input name="addgst" class="button" type="submit" value="ADD <?php echo $nametax; ?>" style="width:140px;"/>
     <input name="inclgst" class="button" type="submit" value="FEE INCLUDES <?php echo $nametax;; ?>" style="width:140px;"/>
     <input name="nogst" class="button" type="submit" value="NO <?php echo $nametax; ?>" style="width:140px;"/>
     <input name="cancel" class="button" type="button" value="CANCEL" style="width:140px;" onclick="history.back();"/>
     <input type="hidden" name="check" value="1" />
     </td>
  </tr>
  <tr <?php if (!isset($show)){echo "style='display:none;'";} ?>>
    <td height="35" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF">

     <input name="ok" class="button" type="submit" value="OK" style="width:140px;"/>
     <input name="cancel2" class="button" type="button" value="CANCEL" style="width:140px;" onclick="document.location='SUMMARY_INVOICES.php';"/>

     </td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>