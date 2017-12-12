<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

$tffid=$_GET['tffid'];
$category=$_GET['category'];
$species = $_GET['species'] ;

mysql_select_db($database_tryconnection, $tryconnection);


$tcats = "SELECT TCATGRY,TTYPE FROM VETCAN WHERE TNO = 1 AND TSPECIES = $species ORDER BY TCATGRY" ;
$query_tcats = mysql_query($tcats, $tryconnection) or die(mysql_error()) ;

/* 
if (!isset($_POST["save"])) {
  $cats = array() ;
  while ($row_cats = mysql_fetch_assoc($query_tcats)) {
   $cats[] = 0 ;
  }
 $query_tcats = mysql_query($tcats, $tryconnection) or die(mysql_error()) ;
}
*/ 

if (isset($_POST["save"]) //&& $category != "0"
                         ) {
                         
 // Make a backup
      	$latest = date('YmdHis') ;
		$tblname = 'VETCAN'.$latest ;
		$backup = "CREATE TABLE $tblname like VETCAN" ;
		$query_back = mysql_query($backup, $tryconnection) or die(mysql_error()) ;
		$fill_it = "INSERT INTO $tblname SELECT * FROM VETCAN ORDER BY TSPECIES, TCATGRY, TNO" ;
		$query_fill = mysql_query($fill_it, $tryconnection) or die(mysql_error()) ;

 
 if ($_POST['radio'] == 1) {
  $function = '+' ;
 }
 elseif ($_POST['radio'] == 2) {
  $function = '-' ;
 }
 else {
  $function = '*' ;
 }

 $amount = round($_POST['b'],2) ;
  echo ' Amount is ' . $amount ;
  echo 'C1 etc ' . $_POST['C1']  . $_POST['C2'] . $_POST['C2'];
 
 // now check to see which category(s) have been selected.
 
 $query_tcats = mysql_query($tcats, $tryconnection) or die(mysql_error()) ;
 $i = 1 ;
 
 while ($row_cats = mysql_fetch_assoc($query_tcats)) {
    $chk = 'C'.$i ;
    echo $chk . ' ' ;
    if (!empty($_POST["$chk"])) {
      $category = $i ;
      $updateSQL = "UPDATE VETCAN SET TFEE = TFEE $function $amount WHERE TSPECIES = $species AND TCATGRY = $category " ;
      $Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());

// update any associated procedure 


       $QUERY_Proc = "UPDATE PROCEDUR JOIN VETCAN ON PROCEDUR.FEEFILE = VETCAN.TSPECIES AND PROCEDUR.INVMAJ = VETCAN.TCATGRY AND PROCEDUR.INVTNO = VETCAN.TNO SET INVPRICE = VETCAN.TFEE, INVTOT = VETCAN.TFEE * INVUNITS 
       WHERE PROCEDUR.INVMAJ = $category AND PROCEDUR.FEEFILE = $species AND PROCEDUR.FEEUPDTE = '1' ";
    $UPDATE_Proc = mysql_query($QUERY_Proc, $tryconnection) or die(mysql_error()) ;
    }
   $i++ ;
  }
 
  header("Location: TFF_DIRECTORY.php?species=$_GET[species]&category=$row_TFF[TCATGRY]");
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>UPDATE TREATMENT &amp; FEE FILE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}


function showbutton(){
if (document.tff.tupdate.checked){
document.getElementById('patientfile').style.display='';
}
else {
document.getElementById('patientfile').style.display='none';
document.tff.tserum.checked=false;
}
}

/*
function openpatientfile(x){
var serum='<?php echo $row_TFF['TSERUM']; ?>';
if (x=='1'){
	if (!document.tff.tserum.checked){serum='0';} else {serum='1';}
}
else {
	if (!document.tff.tserum.checked){serum='0';} else {serum='1';}
window.open('UPDATE_PATIENT_FILE.php?species=<?php echo $row_TFF['TSPECIES']; ?>&cocktail=<?php echo $row_TFF['TDESCR']; ?>&tffid=<?php echo $_GET['tffid']; ?>&tserum='+serum,'_blank','width=700,height=500');
}
}
*/
</script>

<style type="text/css">
<!--

#shadow {
	background-color: #556453;
	width: 332px;
	height: auto;
}
#shadowedtable {
	position: relative;
	width: 330px;
	height: auto;
	left: -4px;
	top: -4px;
	background-color:#FFFFFF;
	border: solid #556453 thin;
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
<form name="tffchange" action="" class="FormDisplay" method="post">
<table width="732" height="553" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><!--DWLayoutTable-->
  <tr>
    <td width="219" height="28"></td>
    <td width="294" align="center" valign="bottom" class="Verdana13B">MODIFY FEES</td>
    <td width="219">&nbsp;</td>
  </tr>
  <tr>
    <td height="337">&nbsp;</td>
    <td align="center" valign="top">
	<br  /><br  />
	<div id="shadow">
	<div id="shadowedtable">
	  <table width="330" border="0" cellpadding="0" cellspacing="0">
	    <!--DWLayoutTable-->
        
        <tr>
          <td width="9" align="right">&nbsp;</td>
          <td colspan="2" align="right">&nbsp;</td>
          <td width="43" align="right">&nbsp;</td>
          <td width="43" align="right">&nbsp;</td>
          <td width="43" align="right">&nbsp;</td>
          <!--
          <td width="33" align="right">&nbsp;</td> -->
          <td width="130" align="right">&nbsp;</td>
          </tr>
        <tr>
          <td height="25" align="left" class="Labels2">&nbsp;</td>
          <td height="25" colspan="6" align="left" class="Verdana11Blue">Enter the amount by which to modify</td>
          </tr>
        <tr>
        <?php while ($row_tcat = mysql_fetch_assoc($query_tcats)) {$tid = 'C'.$row_tcat['TCATGRY'] ;
                    echo '<td height="25" align="right" class="Verdana11Blue"><input type="checkbox" name="'.$tid.'"' . ' id="'.$tid.'" value="1"/>
                    <td height="25" colspan="5" align="left" valign="middle" class="Labels2">'; 
                    echo $row_tcat['TCATGRY'] . '&nbsp;&nbsp;' .$row_tcat['TTYPE'].'</td></tr>' ;
             }
        ?>
        </tr>
        <tr> <!--
          <td height="34" align="right" valign="middle" class="Labels2">&nbsp;</td> -->
          <td width="66" align="right" valign="middle"><input type="radio" name="radio" id="a" value="1" /></td>
          <td width="39" align="left" valign="middle" class="Verdana11Red">Add</td>
          <td align="right" valign="middle"><input type="radio" name="radio" id="a2" value="2" /></td>
          <td colspan="1" align="left" valign="middle" class="Verdana11Red">Subtract</td>
          <td align="right" valign="middle"><input type="radio" name="radio" id="a3" value="3" checked="checked" /></td>
          <td colspan="1" align="left" valign="middle" class="Verdana11Red">Multiply</td>
          </tr>
        <tr>
          <td height="25" align="left" class="Labels2">&nbsp;</td>
          <td height="25" colspan="4" align="left" valign="middle" class="Verdana11Blue">The selected Category(s) fees</td>
          <td height="25" align="left" valign="middle" class="Labels2"><span class="Labels">
            <input name="b" type="text" class="Inputright" id="b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="1.00" size="6" maxlength="6"/>
          </span></td>
        </tr>
        <tr>
          <td align="right">&nbsp;</td>
          <td colspan="2" align="right">&nbsp;</td>
          <td align="right">&nbsp;</td>
          <td align="right">&nbsp;</td>
          <td align="right">&nbsp;</td>
          <td align="right">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="6" align="center" class="ButtonsTable">
            <input class="button" type="submit" name="save" value="SAVE" />
            <input class="button" type="reset" name="cancel" value="CANCEL" onclick="history.back();" /></td>
          </tr>
      </table>
	</div>
	</div>  
	  
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="108">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>


</body>
<!-- InstanceEnd --></html>