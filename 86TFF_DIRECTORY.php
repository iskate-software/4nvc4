<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

mysqli_select_db($tryconnection, $database_tryconnection);
$spec=$_GET['species'];

function categ($tryconnection,$spec)
{
$query_SPECIES = "SELECT DISTINCT TCATGRY, TTYPE FROM VETCAN WHERE TSPECIES='$spec' ORDER BY TCATGRY";
$SPECIES = mysqli_query($tryconnection, $query_SPECIES) or die(mysqli_error($mysqli_link));
$row_SPECIES = mysqli_fetch_assoc($SPECIES);
$totalRows_SPECIES = mysqli_num_rows($SPECIES);

echo"<select name='category1' class='SelectList' id='category1' multiple='multiple' onchange='category();'>";
do {
echo"<option value='".$row_SPECIES['TCATGRY']."'>";
if ($row_SPECIES['TCATGRY']<10){echo "&nbsp;&nbsp;";}
echo $row_SPECIES['TCATGRY']."&nbsp;".$row_SPECIES['TTYPE'];
echo"</option>";
} while ($row_SPECIES = mysqli_fetch_assoc($SPECIES));
echo"</select>";		 
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>TREATMENT FEE FILE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
/*HIGHLIGHTS THE OPTION PREVIOUSLY SELECTED*/
function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;

var loc=<?php echo $_GET['species']; ?>;
var i=loc-1;
	{
	document.tff_directory.specie.options[i].selected="selected";
	}
category();
}

function species()
{
var spec=document.getElementById('specie').value;
self.location='TFF_DIRECTORY.php?species=' + spec;
}

function category()
{
var cat=document.getElementById('category1').value;
var loc=<?php echo $_GET['species']; ?>;
window.open('TFF_LIST_IFRAME.php?category=' + cat + '&species=' +loc ,'tfflist','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

function OpenAddTFF(){
var cat=document.getElementById('category11').value;
if (!cat){
alert("Please select a category.");
}
else{
window.open('UPDATE_TFF.php?tffid=0&category='+cat + '&species=<?php echo $_GET['species']; ?>','_self');
}
}

function OpenAddCategory(){
var loc="<?php echo $_GET['species']; ?>";
if (loc=="i"){
alert("Please select a species.");
}
else{
window.open('UPDATE_TFF.php?tffid=0&category=0&species='+loc,'_self');
}
}

function OpenReport(){
var loc="<?php echo $_GET['species']; ?>";
if (loc=="i"){
alert("Please select a species.");
}
else{
window.open('PRINT_REPORT.php?species='+loc+'&category=i','_blank','width=780,height=600');
}
}


function OpenChange(){
var cat=document.getElementById('category11').value;
if (!cat){
alert("Please select a category.");
}
else{
window.open('CHANGE_TFF.php?tffid=0&category='+cat + '&species=<?php echo $_GET['species']; ?>','_self');
}
}


</script>

<style type="text/css">
<!--
#table {
	border-color: #CCCCCC;
	border-style: ridge;
	border-width: 3px;
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

.CustomizedButton1 {
	font-family: Verdana;
	font-size: 20px;
	width: 120px;
	height: 27px;
	margin-left: 2px;
	margin-right: 2px;
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
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" --><?php // if (empty($_SESSION['fileused'])){echo"&nbsp;"; } else {echo substr($_SESSION['fileused'],0,25);}  ?>
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->
<form action="" method="post" class="FormDisplay" name="tff_directory">
<table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
  <tr height="10">
    <td width="172" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite">Species</td>
    <td width="231" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite">Category</td>
    <td width="330" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite">Sub-treatment</td>
    </tr>
  <tr>
    <td height="483" colspan="3" align="center" valign="top">

	   
    <table class="table" width="100%" height="100%" border="1" cellpadding="0" cellspacing="0" >
      <tr>
        <td width="23%" height="100%" align="center" valign="top">
	    <select name="specie" class="SelectList" multiple="multiple" id="specie" onchange="species();">
         <option class="options" value="1">&nbsp;CANINE</option>
         <option class="options" value="2">&nbsp;FELINE</option>
         <option class="options" value="3">&nbsp;EQUINE</option>
         <option class="options" value="4">&nbsp;BOVINE</option>
         <option class="options" value="5">&nbsp;CAPRINE</option>
         <option class="options" value="6">&nbsp;PORCINE</option>
         <option class="options" value="7">&nbsp;AVIAN</option>
         <option class="options" value="8">&nbsp;OTHER</option>
		</select>        </td>
        <td width="32%" align="center" valign="top">
         <?php
		 categ($tryconnection,$spec);
		 ?>        </td>
        <td width="45%" align="center" valign="top">
         <iframe name="tfflist" scrolling="no" frameborder="0" height="100%" width="100%">         </iframe>        </td>
      </tr>
    </table>    </td>
    </tr>
  <tr>
    <td height="35" colspan="5" align="center" valign="middle" class="ButtonsTable">
    <input name="" class="CustomizedButton1" type="button" value="CATEGORIES" title="Edit category" onclick="window.open('RENUMBER_CATEGORY.php?species=<?php echo $_GET['species']; ?>&category=i','_blank','width=380,height=430');" />
    <input type="button" name="button9" class="CustomizedButton1" value="ADD CATEGORY." onclick="OpenAddCategory();" title="Add category" />
    <input type="button" name="button2" id="button2" class="CustomizedButton1" value="ADD SUBTRTMT." onclick="OpenAddTFF();" title="Add sub-treatment" />
    <input name="" class="CustomizedButton1" type="button" value="REPORT" title="Display report" onclick="OpenReport()" />
    <input name="" class="CustomizedButton1" type="button" value="CATGRY FEE CHG."  title="Global fee changes"  onclick="OpenChange()"/>
    <input name="" class="button" type="button" value="CANCEL" onclick="document.location='../../INDEX.php';" />    
    </td>
    </tr>
</table>

<input type="hidden" name="category11" id="category11" value="" />
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
