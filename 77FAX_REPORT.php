<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$select_FAXREP = "SELECT * FROM FAXREP LIMIT 1";
$FAXREP = mysql_query($select_FAXREP) or die(mysql_error());
$row_FAXREP = mysql_fetch_assoc($FAXREP);

if (!empty($_POST['area'])) {$area=$_POST['area'];}
if (!empty($_POST['phonea'])){$phone=$_POST['phonea'].'-'.$_POST['phoneb'];}
if (!empty($_POST['faxnoa'])){$faxno=$_POST['faxnoa'].'-'.$_POST['faxnob'];}
//.'-'.$_POST['faxnoc'];}


if (isset($_POST['ok'])){
$query_FAXREP = sprintf("UPDATE FAXREP SET CLINIC='%s', AREA='%s', PHONE='%s', FAXNO='%s', SHIPTO='%s', CODE='%s', FAXTO='%s', SENTBY='%s'",
					mysql_real_escape_string($_POST['clinic']),
					$area,
					$phone,
					$faxno,
					mysql_real_escape_string($_POST['shipto']),
					$_POST['code'],
					mysql_real_escape_string($_POST['faxto']),
					mysql_real_escape_string($_POST['sentby'])
					);
$FAXREP = mysql_query($query_FAXREP, $tryconnection) or die(mysql_error());
$closewin = "window.open('FAX_ORDER_SHEET','_blank'); document.location='ORDER_LIST.php';";
}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>FAXREPY ORDER LIST</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/JavaScript">
function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
<?php echo $closewin; ?>
}


function skip(x,y){
	if (y.length==x.maxLength){
	next=x.tabIndex;
	document.forms[0].elements[next+2].focus();
	document.forms[0].elements[next+2].select();
	}
}
</script>

<style type="text/css">
#irresults2 {
height:400px; 
overflow:auto;
}
</style>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<!-- InstanceEndEditable -->

<!-- InstanceBeginEditable name="HOME" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>
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
<form method="post" action="" name="soldlist" id="soldlist">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="100" colspan="8" align="center" valign="middle" class="Verdana13B">FAX ORDER FORM</td>
    </tr>
  <tr>
    <td colspan="9">
    </td>
  </tr>
  <tr>
    <td height="418" colspan="9" align="center" valign="top">
    <table width="70%" border="1" cellspacing="0" cellpadding="0" frame="box" rules="none">
      <tr>
        <td width="14" class="Verdana12">&nbsp;</td>
        <td width="76" height="30" class="Verdana12">&nbsp;Date:</td>
        <td colspan="2"><input name="date" type="text" class="Input" id="date" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onclick="ds_sh(this);" value="<?php echo date('m/d/Y'); ?>"/></td>
        </tr>
      <tr>
        <td class="Verdana12">&nbsp;</td>
        <td height="30" class="Verdana12">&nbsp;Fax to:</td>
        <td colspan="2"><input name="faxto" type="text" class="Input" id="faxto" size="60" maxlength="60" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_FAXREP['FAXTO']; ?>"/></td>
        </tr>
      <tr>
        <td height="30" align="left" class="Verdana12">&nbsp;</td>
        <td height="30" colspan="2" align="left" class="Verdana12">&nbsp;From: Clinic Code:</td>
        <td width="335"><input name="code" type="text" class="Input" id="code" size="4" maxlength="4"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_FAXREP['CODE']; ?>"/></td>
      </tr>
      <tr>
        <td height="30" colspan="3" align="right" class="Verdana12">&nbsp;Name:&nbsp;&nbsp;&nbsp;</td>
        <td><input name="clinic" type="text" class="Input" id="clinic" size="30"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_FAXREP['CLINIC']; ?>"/></td>
      </tr>
      <tr>
        <td height="30" colspan="3" align="right" class="Verdana12">&nbsp;Phone #:&nbsp;&nbsp;&nbsp;</td>
        <td class="Verdana12">
        <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="area" type="text" class="Input" id="area" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_FAXREP['AREA']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="5"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="phonea" type="text" class="Input" id="phonea" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_FAXREP['PHONE'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="7"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phoneb" type="text" class="Input" id="phoneb" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_FAXREP['PHONE'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="9"/>
        
        
        &nbsp; Fax#:&nbsp;
        <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="area" type="text" class="Input" id="area" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_FAXREP['AREA']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="11"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="faxnoa" type="text" class="Input" id="faxnoa" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_FAXREP['FAXNO'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="13"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="faxnob" type="text" class="Input" id="faxnob" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_FAXREP['FAXNO'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="14"/>
        </td>
      </tr>
      <tr>
        <td class="Verdana12">&nbsp;</td>
        <td height="30" class="Verdana12">&nbsp;Sent by:</td>
        <td colspan="2"><input name="sentby" type="text" class="Input" id="sentby" size="30" maxlength="30"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_FAXREP['SENTBY']; ?>"/></td>
        </tr>
      <tr>
        <td class="Verdana12">&nbsp;</td>
        <td height="30" class="Verdana12">&nbsp;Ship to:</td>
        <td colspan="2"><input name="shipto" type="text" class="Input" id="shipto" size="50" maxlength="50"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_FAXREP['SHIPTO']; ?>"/></td>
        </tr>
      <tr>
        <td></td>
        <td height="0"></td>
        <td width="78"></td>
        <td></td>
      </tr>
    </table></td>
  </tr>
  <tr id="buttons">
    <td colspan="9" align="center" class="ButtonsTable">
    	<input name="ok" type="submit" class="button" id="ok" value="OK" onclick="history.back();" />
      	<input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back();" /></td>
  </tr>
</table>
</form>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
