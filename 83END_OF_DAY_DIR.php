<?php 
session_start();
require_once('../../tryconnection.php');
mysqli_select_db($tryconnection, $database_tryconnection);
$careful = "LOCK TABLES INVHOLD WRITE" ;
$step1 = mysqli_query($tryconnection, $careful) or die(mysqli_error($mysqli_link)) ;
$clean_up = "DELETE FROM INVHOLD WHERE INVNO = 0 AND INVCUST = 0" ;
$go = mysqli_query($tryconnection, $clean_up) or die(mysqli_error($mysqli_link)) ;
$shrink = "OPTIMIZE TABLE INVHOLD" ;
$do_shrink = mysqli_query($tryconnection, $shrink) or die(mysqli_error($mysqli_link)) ;
$release = "UNLOCK TABLES" ;
$go2 = mysqli_query($tryconnection, $release) or die(mysqli_error($mysqli_link)) ;
$cl_med = "DELETE FROM MEDNOTES WHERE EXISTS (SELECT CUSTNO FROM ARINVOI WHERE INVNO = MEDNOTES.NCUSTNO)" ;
$do_it = mysqli_query($tryconnection, $cl_med) or die(mysqli_error($mysqli_link)) ;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>END OF DAY ACCOUNTING REPORTS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/JavaScript">
function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}
</script>

<style type="text/css">
<!--
.SphereBg {
	color: #000000;
	font-size: 12px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-weight:400;
	background-image:url(../../IMAGES/svetle_zelena_koule.jpg) ;
	background-repeat: no-repeat;
	padding-left: 25px;
	padding-top: 3px;
}

.newSphereBg {
color: #000000;
	font-size: 12px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-weight:400;
	background-image: url(../../IMAGES/tmave_zelena_koule.jpg);
	background-repeat: no-repeat;
	padding-left: 25px;
	padding-top: 3px;
font-weight:bold;
cursor:pointer;
}

#shadow {
	background-color: #556453;
	width: 254px;
	height: auto;
}
#shadowedtable {
	position: relative;
	width: 254;
	height: auto;
	left: -4px;
	top: -4px;
	background-color:#FFFFFF;
}
-->
</style>
<script type="text/JavaScript">
<!--

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function SphereBgOnMouseOver(x) {
	x.className=(x.className=="SphereBg")?"newSphereBg":"newSphereBg";
}

function SphereBgOnMouseOut(x) {
	x.className=(x.className=="newSphereBg")?"SphereBg":"SphereBg";
}

//-->
</script>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
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
<table width="732" height="553" border="0" cellpadding="0" cellspacing="0" bgcolor="#B1B4FF"><!--DWLayoutTable-->
  <tr>
    <td width="233" height="108"></td>
    <td width="254"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="238">&nbsp;</td>
  </tr>
  <tr>
    <td height="337">&nbsp;</td>
    <td>
	
	<div id="shadow">
	<div id="shadowedtable">
	<table width="254" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" rules="none">
      <!--DWLayoutTable-->
      <tr>
        <td height="30" align="right" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
        <td height="30" align="left" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
      </tr>
      <tr>
        <td width="42" height="30" align="right" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td width="206" height="30" align="left" valign="top" class="SphereBg" onclick="bankdeposit();" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);">Bank Deposit</td>
          </tr>
      <tr>
        <td height="30" align="right" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td width="206" height="30" align="left" valign="top" class="SphereBg" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="document.location='/'+localStorage.xdatabase+'/INVOICE/INVOICING_REPORTS/INVOICE_REGISTER_RESULTS.php?file2search=ARINVOI&startdate=<?php echo date('m/d/Y'); ?>&enddate=<?php echo date('m/d/Y'); ?>&Submit=DISPLAY'"><span class="">Today's Invoices Report</span></td>
          </tr>
      <tr>
        <td height="30" align="right" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td width="206" height="30" align="left" valign="top" class="SphereBg" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="document.location='/'+localStorage.xdatabase+'/INVOICE/INVOICING_REPORTS/CASH_REGISTER_RESULTS.php?file2search=ARCASHR&startdate=<?php echo date('m/d/Y'); ?>&enddate=<?php echo date('m/d/Y'); ?>&expenses=0.00&company=&Submit=DISPLAY&typeofreport=1'"><span class="">Today's Cash Receipts Rep.</span></td>
          </tr>    
          <tr>
        <td height="30" align="right" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
        <td height="30" align="left" valign="top" class="SphereBg" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="document.location='/'+localStorage.xdatabase+'/INVOICE/INVOICING_REPORTS/INVENTORY_REGISTER_RESULTS.php?file2search=DVMINV&startdate=<?php echo date('m/d/Y'); ?>&enddate=<?php echo date('m/d/Y'); ?>&Submit=DISPLAY'">Today's Inventory Sales</td>
      </tr>
      <tr>
        <td height="30" align="right" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
        <td height="30" align="left" valign="top" class="SphereBg" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="document.location='/'+localStorage.xdatabase+'/INVOICE/INVOICING_REPORTS/ACTIVITY_REGISTER_RESULTS.php?file2search=DVMINV&startdate=<?php echo date('m/d/Y'); ?>&enddate=<?php echo date('m/d/Y'); ?>&Submit=DISPLAY'">Today's Activity Register</td>
      </tr>
      <tr>
        <td height="30" align="right" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td width="206" height="30" align="left" valign="top" class="SphereBg" onclick="window.open('','_self','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no')" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);"><span class="">Balance Till</span></td>
          </tr>
      <tr>
        <td height="40" colspan="2" align="center" valign="middle"><input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back();" /></td>
      </tr>
      </table>
	</div>
	</div>	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="108">&nbsp;</td>
    <td align="center" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
