<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");


//DELETE ITEM
if (isset($_GET['ref'])){
$keyunset=$_GET['ref'];
unset($_SESSION['procline'][$keyunset]);
$_SESSION['procline']=array_merge($_SESSION['procline']);
header("Location:UPDATE_PROCEDURE.php");
}


mysqli_select_db($tryconnection, $database_tryconnection);

if (!isset($_SESSION['species'])){
$_SESSION['species']=$_GET['species'];
}
$spec=$_SESSION['species'];


if (!isset($_SESSION['category'])){
$_SESSION['category']=$_GET['category'];
}
$category=$_SESSION['category'];


$query_PROCEDURE=sprintf("SELECT * FROM PROCEDUR WHERE PROCODE = '%s' AND FEEFILE='%s' ORDER BY ISORTCODE ASC",$category, $spec);
$PROCEDURE = mysqli_query($tryconnection, $query_PROCEDURE) or die(mysqli_error($mysqli_link));
$row_PROCEDURE = mysqli_fetch_assoc($PROCEDURE);

if (!isset($_SESSION['procname'])){
$_SESSION['procname']=$row_PROCEDURE['PROCEDURE'];
}
$procname=$_SESSION['procname'];


		if (!isset($_SESSION['procline'])){
			do {
				$autcomm=$row_PROCEDURE['AUTOCOMM'];
				$query_TAUTOCOMM = "SELECT * FROM ARSYSCOMM WHERE COMMCODE='$autcomm'";
				$TAUTOCOMM = mysqli_query($tryconnection, $query_TAUTOCOMM) or die(mysqli_error($mysqli_link));
				$row_TAUTOCOMM = mysqli_fetch_assoc($TAUTOCOMM);
				$invoicecomment=$row_TAUTOCOMM['COMMENT'];
						   
						   //CREATE AN ARRAY FROM ENTIRE RECORD FROM PROCEDUR FOR SELECTED ITEM 			
						   $item = array(
										 'INVMAJ' => $row_PROCEDURE['INVMAJ'],
										 'INVMIN' => $row_PROCEDURE['INVTNO'],
										 'INVUNITS' => $row_PROCEDURE['INVUNITS'],
										 'INVDESCR' => $row_PROCEDURE['INVDESC'],
										 'INVPRICE' => $row_PROCEDURE['INVPRICE'],
										 'INVTOT' => round($row_PROCEDURE['INVTOT'],2),
										 'INVINCM' => $row_PROCEDURE['INVINCM'],
										 'INVDISC' => $row_PROCEDURE['INVDISC'],
										 'INVLGSM' => $row_PROCEDURE['FEEFILE'],
										 'INVREVCAT' => $row_PROCEDURE['INVREVCAT'],
										 'INVGST' => round(($row_PROCEDURE['INVPRICE']*$taxvalue),2),
										 'INVTAX' => round(($row_PROCEDURE['INVPRICE']*($row_PROCEDURE['INVTAX']/100)),2), 
										 'INVUPDTE' => $row_PROCEDURE['INVUPDTE'],										
										 'INVFLAGS' => $row_PROCEDURE['INVFLAGS'],
										 'INVDISP' => $row_PROCEDURE['INVDISP'],
										 'INVGET' => $row_PROCEDURE['INVGET'],
										 'INVPERCNT' => $row_PROCEDURE['INVPERCNT'],
										 'INVHYPE' => $row_PROCEDURE['INVHYPE'],
										 'AUTOCOMM' => $row_TAUTOCOMM['COMMCODE'],
										 'INVCOMM' => $row_PROCEDURE['INVCOMM'],
										 'HISTCOMM' => $row_PROCEDURE['HISTCOMM'],
										 'MODICODE' => $row_PROCEDURE['MODICODE'],
										 'INVNARC' => $row_PROCEDURE['INVNARC'],
										 'INVVPC' => $row_PROCEDURE['INVVPC'],
										 'INVUPRICE' => $row_PROCEDURE['INVUPRICE'],
										 'INVPKGQTY' => $row_PROCEDURE['INVPKGQTY'],
										 'IRADLOG' => $row_PROCEDURE['IRADLOG'],
										 'ISURGLOG' => $row_PROCEDURE['ISURGLOG'],
										 'INARCLOG' => $row_PROCEDURE['INARCLOG'],
										 'INVPRU' => $row_PROCEDURE['INVPRU'],
										 'FEEUPDTE' => $row_PROCEDURE['FEEUPDTE'],
										 'INVHXCAT' => $row_PROCEDURE['INVHXCAT']
										 );
						$_SESSION['procline'][] = $item;
						
				} while ($row_PROCEDURE = mysqli_fetch_assoc($PROCEDURE));
		}





if (!isset($_GET['subcat'])){
$cat=1;
}
else {
$cat=$_GET['subcat'];
}


if (!isset($_GET['product'])){
$ps=1;
}
else {
$ps=$_GET['product'];
}

if(isset($_POST['ok'])){
$ps = "j";
}


function categ($tryconnection,$spec)
{
$query_CATEGORY ="SELECT DISTINCT TCATGRY, TTYPE FROM VETCAN WHERE TSPECIES='$spec' ORDER BY TCATGRY ASC";
$CATEGORY = mysqli_query($tryconnection, $query_CATEGORY) or die(mysqli_error($mysqli_link));
$row_CATEGORY = mysqli_fetch_assoc($CATEGORY);
$totalRows_CATEGORY = mysqli_num_rows($CATEGORY);

echo"<select name='category1' class='SelectList' id='category1' multiple='multiple' onchange='category();' >";
do {
echo"<option value='".$row_CATEGORY['TCATGRY']."'>";
echo $row_CATEGORY['TTYPE'];
echo"</option>";
} while ($row_CATEGORY = mysqli_fetch_assoc($CATEGORY));
echo"</select>";		 

}

//////////////////////////LIST PRODUCT SERVICE FROM TREATMENT FEE FILE////////////////////
function subcateg($tryconnection,$cat, $spec)
{
$query_PRODUCTSERVICE = sprintf("SELECT TFFID, TNO, TDESCR, TTYPE, TFEE, TCATGRY, TDISCOUNT FROM VETCAN WHERE TCATGRY = '%s' AND  TSPECIES='$spec' ORDER BY TNO ASC",mysqli_real_escape_string($mysqli_link, $cat));
$PRODUCTSERVICE = mysqli_query($tryconnection, $query_PRODUCTSERVICE) or die(mysqli_error($mysqli_link));
$row_PRODUCTSERVICE = mysqli_fetch_assoc($PRODUCTSERVICE);
$totalRows_PRODUCTSERVICE = mysqli_num_rows($PRODUCTSERVICE);

echo"<select name='prodser' id='prodser' multiple='multiple' class='SelectList' onchange='modifyitem()' >";
do {
echo"<option value='".$row_PRODUCTSERVICE['TFFID']."' id='".$row_PRODUCTSERVICE['TFFID']."'>";
echo $row_PRODUCTSERVICE['TDESCR'];
echo"</option>";
} while ($row_PRODUCTSERVICE = mysqli_fetch_assoc($PRODUCTSERVICE));
echo"</select>";		 

}

//////////////////////SELECTED ITEM FROM TREATMENT FEE FILE/////////////////
//$query_SELECTEDITEM = sprintf("SELECT * FROM VETCAN WHERE TNO = '%s' AND TCATGRY = '%s'",mysql_real_escape_string($ps),mysql_real_escape_string($cat));
$query_SELECTEDITEM = sprintf("SELECT * FROM VETCAN WHERE TFFID = '%s'",$ps);
$SELECTEDITEM = mysqli_query($tryconnection, $query_SELECTEDITEM) or die(mysqli_error($mysqli_link));
$row_SELECTEDITEM = mysqli_fetch_assoc($SELECTEDITEM);



if (!empty($row_SELECTEDITEM['TAUTOCOMM'])){
$query_TAUTOCOMM = "SELECT * FROM ARSYSCOMM WHERE COMMCODE='$row_SELECTEDITEM[TAUTOCOMM]'";
$TAUTOCOMM = mysqli_query($tryconnection, $query_TAUTOCOMM) or die(mysqli_error($mysqli_link));
$row_TAUTOCOMM = mysqli_fetch_assoc($TAUTOCOMM);
}


//////////////////////////////////////////////////////////////////////////////

//INSERT THE SELECTED AND MODIFIED ITEM INTO ITEM LIST
                if (isset($_POST['ok'])) {
						   $invoicecomment=$_POST['commtext'];
						   $invunits=$_POST['invunits'];
						   
						   if (!empty($_POST['thxcat'])){
						   $thxcat = $_POST['thxcat'];
						   }
						   else {
						   $thxcat = 4096;
						   }

						   //CREATE AN ARRAY FROM ENTIRE RECORD FROM VETCAN FOR SELECTED ITEM 			
						   $item = array(
										 'INVMAJ' => $_POST['invmaj'],
										 'INVMIN' => $_POST['invmin'],
										 'INVUNITS' => $invunits,
										 'INVDESCR' => $_POST['invdescr'],
										 'INVPRICE' => $_POST['invprice'],
										 'INVTOT' => round($_POST['invtot'],2),
										 'INVINCM' => $_POST['invincm'],
										 'INVDISC' => $_POST['invdisc'],
										 'INVLGSM' => $_SESSION['species'],
										 'INVREVCAT' => $_POST['invrevcat'],
										 'INVGST' => round($_POST['invgst'],2),
										 'INVTAX' => round($_POST['invtax'],2), 
										 'INVUPDTE' => $_POST['invupdte'],										
										 'INVFLAGS' => $_POST['invflags'],
										 'INVDISP' => $_POST['invdisp'],
										 'INVGET' => $_POST['invget'],
										 'INVPERCNT' => $_POST['invpercnt'],
										 'INVHYPE' => $_POST['invhype'],
										 'FEEUPDTE' => $_POST['feeupdte'],
										 'AUTOCOMM' => $_POST['autocomm'],
										 'INVCOMM' => $_POST['invcomm'],
										 'HISTCOMM' => $_POST['histcomm'],
										 'MODICODE' => $_POST['modicode'],
										 'INVNARC' => $_POST['invnarc'],
										 'INVVPC' => $_POST['invvpc'],
										 'INVUPRICE' => $_POST['invuprice'],
										 'INVPKGQTY' => $_POST['invpkgqty'],
										 'IRADLOG' => $_POST['iradlog'],
										 'ISURGLOG' => $_POST['isurlog'],
										 'INARCLOG' => $_POST['inarclog'],
										 'INVPRU' => $_POST['xlabel'],
										 'INVHXCAT' => $thxcat,
										 'INVTDISCOUNT' => $_POST['invtdiscount']
										 );
						$_SESSION['procline'][] = $item;
			}//if (isset($_POST['ok']))


if (isset($_POST['save'])){

$delete_PROCEDURE="DELETE FROM PROCEDUR WHERE PROCODE = '$category'";
mysqli_query($tryconnection, $delete_PROCEDURE) or die(mysqli_error($mysqli_link));

$optimize_PROCEDURE="OPTIMIZE TABLE PROCEDUR";
mysqli_query($tryconnection, $optimize_PROCEDURE) or die(mysqli_error($mysqli_link));

$i=1;

foreach ($_SESSION['procline'] as $item) {
//INVCOMNO, IPROFC, IPROFREQ, IINTENC, IINTENFREQ, ILASTDONE, MEMO, ICOMPLETE, ISTART, IMULTIPLE, UNIQUE, IAUTOBILL, IDAILY, ICREDITSUR,
$insertSQL2 = sprintf("INSERT INTO PROCEDUR (FEEFILE, PROCODE, `PROCEDURE`, INVMAJ, INVMIN, INVUNITS, INVDESC, INVPRICE, INVTOT, INVPRU, INVINCM, INVDISC, INVLGSM, INVREVCAT, INVGST, INVTAX, INVUPDTE, INVFLAGS, INVDISP, INVGET, INVPERCNT, INVTNO, INVHYPE, FEEUPDTE, AUTOCOMM, INVCOMM, HISTCOMM, MODICODE, INVNARC, INVVPC, INVUPRICE, INVPKGQTY, IRADLOG, ISURGLOG, INARCLOG, ISORTCODE, INVHXCAT, INVTDISCOUNT) 
                       VALUES ('%s', '%s', '%s', '%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%d')",
							  $item['INVLGSM'],
							  $_SESSION['category'],
							  $_SESSION['procname'],
							  $item['INVMAJ'],
							  $item['INVMIN'],
							  $item['INVUNITS'],
							  mysqli_real_escape_string($mysqli_link, $item['INVDESCR']),
							  $item['INVPRICE'],
							  $item['INVTOT'],
							  $item['INVPRU'],
							  $item['INVINCM'],
							  $item['INVDISC'],
							  $item['INVLGSM'],
							  $item['INVREVCAT'],
							  $item['INVGST'],
							  $item['INVTAX'],
							  $item['INVUPDTE'],
							  $item['INVFLAGS'],
							  $item['INVDISP'],
							  $item['INVGET'],
							  $item['INVPERCNT'],
							  $item['INVMIN'],
							  mysqli_real_escape_string($mysqli_link, $item['INVHYPE']),
							  $item['FEEUPDTE'],
							  mysqli_real_escape_string($mysqli_link, $item['AUTOCOMM']),
							  $item['INVCOMM'],
							  $item['HISTCOMM'],
							  $item['MODICODE'],
							  $item['INVNARC'],
							  $item['INVVPC'],
							  $item['INVUPRICE'],
							  $item['INVPKGQTY'],
							  $item['IRADLOG'],
							  $item['ISURGLOG'],
							  $item['INARCLOG'],
							  $i,
							  $item['INVHXCAT'],
							  $item['INVTDISCOUNT']
							  );
mysqli_query($tryconnection, $insertSQL2) or die(mysqli_error($mysqli_link));
$i=$i+1;
}
header("Location:PROCEDURES_DIRECTORY.php?species=$_SESSION[species]");
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>UPDATE PROCEDURE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
var invpreview=document.getElementById('invpreview');
invpreview.scrollTop = invpreview.scrollHeight;

document.getElementById('inuse').innerText=localStorage.xdatabase;

document.getElementById('invprice').focus();
//HIGHLIGHT SELECTED ITEMS IN SELECT LISTS
var loc='<?php echo $cat; ?>';
var i=loc-1;
	{
	document.proc_building.category1.options[i].selected="selected";
	}
var loc2=<?php echo $ps; ?>;
	{
	document.getElementById(loc2).selected="selected";
	}


var commtext=document.proc_building.commtext.value;
document.proc_building.commtext.value=commtext;
}


//LIST PRODCUT SERVICE ON CATEGORY SELECTION
function category(){
var cat=document.getElementById('category1').value;
self.location='UPDATE_PROCEDURE.php?subcat=' + cat + '&product=j';
}

//INSERT SELECTED ITEM INTO THE INPUT FIELDS FOR MODIFICATION
function modifyitem(){
var cat=<?php echo $cat; ?>;
var ps=document.getElementById('prodser').value;
	if (ps==0){ps=document.proc_building.prodser.options[0].value;}
self.location='UPDATE_PROCEDURE.php?product=' + ps + '&subcat=' + cat;
//&record=k
}

function deletion(x){
self.location="UPDATE_PROCEDURE.php?ref=" + x + "&reference=0";
}


function calculateprice(){
//takes quantity
var quantity = document.forms[0].invunits.value;
var invdisp=document.forms[0].invdisp.value;
var unitprice = document.forms[0].invprice.value;

var result = quantity * unitprice;
var result = result+parseFloat(invdisp);			

var resultrounded = Math.round(result*Math.pow(10,2))/Math.pow(10,2);
//converts into two decimal points
var resultfull = resultrounded.toFixed(2);
//inserts it into 'invtot' input field
document.forms[0].invtot.value = resultfull;
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
	font-family: "Verdana";
	font-size: 11px;
	border-width: 0px;
	padding: 5 px;
	outline-width: 0px;
}
.style2 {color: #666666}
.style3 {color: #CC6600}
.button1 {	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 20px;
	width: 200px;
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
<form name="proc_building" action="" class="FormDisplay" id="proc_building" method="POST">
<!-- VETCAN (TREATMENTFEEFILE) -->
<input type="hidden" name="invmaj" value="<?php echo $row_SELECTEDITEM['TCATGRY']; ?>" />
<input type="hidden" name="invmin" value="<?php echo $row_SELECTEDITEM['TNO']; ?>" />
<input type="hidden" name="invincm" value="<?php echo $row_SELECTEDITEM['TINCMAST']; ?>" />
<input type="hidden" name="invrevcat" value="<?php echo $row_SELECTEDITEM['TREVCAT']; ?>" />
<input type="hidden" name="invflags" value="<?php echo $row_SELECTEDITEM['TFLAGS']; ?>" />
<input type="hidden" name="invdisp" value="<?php echo $row_SELECTEDITEM['TDISP']; ?>" />
<input type="hidden" name="invget" value="<?php echo $row_SELECTEDITEM['TGET']; ?>" />
<input type="hidden" name="invcomm" value="<?php echo $row_SELECTEDITEM['TINVCOMM']; ?>" />
<input type="hidden" name="histcomm" value="<?php echo $row_SELECTEDITEM['THISTCOMM']; ?>" />
<input type="hidden" name="modicode" value="<?php echo $row_SELECTEDITEM['TMODICODE']; ?>" />
<input type="hidden" name="iradlog" value="<?php echo $row_SELECTEDITEM['TRADLOG']; ?>" />
<input type="hidden" name="isurlog" value="<?php echo $row_SELECTEDITEM['TSURLOG']; ?>" />
<input type="hidden" name="inarclog" value="<?php echo $row_SELECTEDITEM['TNARCLOG']; ?>" />
<input type="hidden" name="iuac" value="<?php echo $row_SELECTEDITEM['TUAC']; ?>" />
<input type="hidden" name="invserum" value="<?php echo $row_SELECTEDITEM['TSERUM']; ?>" />
<input type="hidden" name="autocomm" value="<?php echo $row_SELECTEDITEM['TAUTOCOMM']; ?>" /><!--at the end of each patient on the invoice - it is the code from TFF, by which the system subsequently attaches appropriate comment from ARSYSCOM-->
<input type="hidden" name="commtext" value="<?php echo $row_TAUTOCOMM['COMMENT']; ?>" />
<input type="hidden" name="invupdte" value="<?php echo $row_SELECTEDITEM['TUPDATE']; ?>" /><!--needed at the end of invoice to say if there is anything to update in the patient file-->
<input type="hidden" name="mtaxrate" value="<?php echo $row_SELECTEDITEM['TTAX']; ?>" />
<input type="hidden" name="tunits" value="<?php echo $row_SELECTEDITEM['TUNITS']; ?>" /><!--if invunits is editable-->  
<input type="hidden" name="tfloat" value="<?php echo $row_SELECTEDITEM['TFLOAT']; ?>" /><!--if invunits is a float or integer-->   
<input type="hidden" name="tenter" value="<?php echo $row_SELECTEDITEM['TENTER']; ?>" /><!--if the description is editable-->
<input type="hidden" name="invhxcat" value="<?php echo $row_SELECTEDITEM['THXCAT']; ?>" />
<input type="hidden" name="invtdiscount" value="<?php echo $row_SELECTEDITEM['TDISCOUNT']; ?>" />

<!-- PHP/JAVASCRIPT GENERATED -->
<input type="hidden" name="invgst" id="invgst" value="" /> <!-- GST TOTAL-->
<input type="hidden" name="invtax" value="" /> <!-- PST TOTAL-->
<!-- OTHER FOR SESSION[procline] -->
<input type="hidden" name="inlinenote" value=""  />
<input type="hidden" name="invhype" value="" />
<input type="hidden" name="invest" value="<?php if ($_SESSION['refID']=='EST'){echo "1";} else {echo "0";} ?>" />
<input type="hidden" name="invdecline" value="0" />

<!-- OTHER FOR CALCULATIONS -->
<input type="hidden" name="invnarc" value="" />
<input type="hidden" name="invvpc" value="" />
<input type="hidden" name="invuprice" value="<?php  echo number_format($row_SELECTEDITEM['TFEE'], 2,'.','');?>" />
<input type="hidden" name="pkgprice" value="0" />
<input type="hidden" name="pkgqty" value="" />
<input type="hidden" name="markup" value="" />
<input type="hidden" name="cost" value="" />
<input type="hidden" name="xlabel" value="" />
<input type="hidden" name="dfyes" value="" />
<input type="hidden" name="result" value="" />
<input type="hidden" name="bulk" value="" />
<input type="hidden" name="dispfee" value="" />
<input type="hidden" name="bdispfee" value="" />
<input type="hidden" name="expdate" value="" />
<input type="hidden" name="xtype" value="" />

<input type="hidden" name="xseq" value="" />
<input type="hidden" name="abctotal" value="" />
<input type="hidden" name="abccost" value="" />
                             
<input type="hidden" name="salmon" value="1" />

  <table width="100%" border="0" cellpadding="0" cellspacing="0">

    <tr>
      <td height="" align="center" valign="top">
      <table id="table" width="733" border="1" cellpadding="0" cellspacing="0" >
          <tr bgcolor="#000000">
            <td width="133" height="13" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite" title='Click to select a category'>Category</td>
            <td width="200" height="13" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite" title='Click to select a product/service'>Product/Service</td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#FFFFFF"><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="15" colspan="3" align="center" valign="top" class="Verdana11BPink">MODIFY PROCEDURE ITEM</td>
                </tr>
                <tr>
                  <td width="50%" height="10" valign="bottom" class="Verdana11B">&nbsp;
                      <!--PRODUCT SERVICE-->
                      <span id="ps">Product/Service</span>
                      <!--DRUG-->
                      <span id="drug" style="display:none">Drug</span> </td>
                  <td width="35%" height="10" align="right" valign="bottom" class="Verdana11B"><!--QTY-->
                    UPrice&nbsp;&nbsp; <span id="spkgs" style="display:none">Pkgs&nbsp;</span> &nbsp; <span id="qty">Qty</span> <span id="dose" style="display:none">Dose</span>
                    <!--UNITS-->
                    <span id="units" style="display:none" >Units</span> </td>
                  <!--PRICE-->
                  <td height="10" align="right" valign="bottom" class="Verdana11B">Price&nbsp;</td>
                </tr>
                <tr>
                  <td height="10" valign="top" class="Labels2"><input name="invdescr" type="text"  id="item" class="Input" size="25" value="<?php echo $row_SELECTEDITEM['TDESCR'];?>" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" />                  </td>
                  <td height="10" align="right" valign="top"><input type="text" name="invprice" id="invprice" value="<?php  echo number_format($row_SELECTEDITEM['TFEE'], 2,'.','');?>" class="Inputright" size="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice()" />
                      <input name="pkgs" id="pkgs" type="text" class="Inputright" value="0" size="4" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice()" title="number of packages" style="display:none"/>
                    <input name="invunits" id="invunits" type="text" class="Inputright" value="1" size="4" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice()" title="number of units" />                  </td>
                  <td width="15%" height="10" align="right" valign="top" class="Labels2"><input name="invtot" id="invtot" type="text" class="Inputright" size="7" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo number_format($row_SELECTEDITEM['TFEE'], 2,'.','');?>" readonly="readonly"/>                  </td>
                </tr>
                <tr id="pharmacy1" style="display:none">
                  <td height="10" valign="bottom" class="Verdana11B">&nbsp;Dosage</td>
                  <td height="10" valign="bottom" class="Labels">&nbsp;</td>
                  <td height="10" align="center" valign="bottom" class="Verdana11B">Days</td>
                </tr>
                <?php 
	   if ($row_SELECTEDITEM['TSERUM']=='1'){
	   echo '<tr>
			<td colspan="3" class="Verdana11B">
			&nbsp;Serum, Duration, Name
			</td>
			</tr>';
		$vacc = explode(",",$row_SELECTEDITEM['TVACCS']);
		foreach ($vacc as $value ){
		
		$query_VACCINES = "SELECT * FROM VACCINES WHERE NAME='$value'";
		$VACCINES = mysqli_query($tryconnection, $query_VACCINES) or die(mysqli_error($mysqli_link));
		$row_VACCINES = mysqli_fetch_assoc($VACCINES);

			echo '	<tr>
					<td colspan="3" class="Verdana11">';
			echo '&nbsp;<input type="text" name="sauce[]" id="'.$row_VACCINES['SEQ'].'" value="'.$row_VACCINES['SERIAL'].'" class="Input" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/><input type="hidden" name="meat[]" value="'.$value.'" /><label><input id="x'.$row_VACCINES['SEQ'].'" name="potatoes[]" type="text" size="1" class="Input" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" />'.$value.'</label><br />';
			echo '</td>
				</tr>';
		 }
		}
		?>
                <tr>
                  <td height="10" align="left" valign="middle" class="Labels"><span style="background-color:#00CC66; font-size:35px; ">
                    <input name="ok" type="submit" value="OK" style="width:40px;" class="button"/>
                    </span>
                      <input name="lookupitem" id="lookupitem" type="button" value="LOOK UP" style="display:none" onclick="window.open('INVENTORY_POPUP_SCREEN.php','blank','width=732,height=500')" /></td>
                  <td height="10"  colspan="2" class="Labels" align="right">
                    <label><input name="feeupdte" type="checkbox" id="feeupdte" value="1" checked="checked"  />
                    &nbsp;Update price with changes</label>
                    </td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td width="133" rowspan="2" align="center" valign="top"><!--LIST CATEGORIES-->
                <?php categ($tryconnection,$spec); ?>            </td>
            <td width="200" height="500" rowspan="2" align="center" valign="top"><!--LIST PRODUCT SERVICE-->
                <?php subcateg($tryconnection,$cat, $spec); ?>            </td>
          </tr>
          <tr>
            <td rowspan="2" align="left" valign="top">
            <table width="344" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td width="344" height="25" colspan="6" align="center" valign="middle" bgcolor="#FFFFFF" class="Verdana13B">  <span style="background-color:"><?php if ($_SESSION['species']=='1') {echo "Canine";} else if ($_SESSION['species']=='2') {echo "Feline";} ?>&nbsp;-&nbsp;<?php echo $_SESSION['category'];  ?>&nbsp;-&nbsp;<?php echo $procname;  ?></span></td>
                </tr>
                <tr class="Verdana9">
                  <td width="31" align="right">Qty&nbsp;</td>
                  <td height="20" width="176" >Product/Service</td>
                  <td align="right" width="30">Uprice&nbsp;</td>
                  <td align="right" width="31">Price&nbsp;</td>
                  <td width="45" align="right">DispF</td>
                  <td align="right">&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="6"><div id="invpreview" style="width:384px;max-height:350px;overflow:auto;">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="31"></td>
                          <td width="176" height="0"></td>
                          <td width="30"></td>
                          <td width="50"></td>
                          <td width="45"></td>
                          <td></td>
                        </tr>
                        <input type="hidden" name="quantity" value=""  />
                        <?php 
				
	
				
				//IF NO ITEM SELECTED YET, DISPLAY NOTHING		  
				if (!isset($_SESSION['procline'])) {
					print "";
				}

		//DISPLAY INVOICE ITEMS
                else if (isset($_SESSION['procline'])) {
						
						foreach ($_SESSION['procline'] as $key => $value) {
						
if ($value['INVPET']==$_SESSION['patient']){						
						//if the value is an procline note
						if ($value['MEMO']=='1' || $value['INVSERUM']=='2'){
						echo ' <tr class="Verdana9">
								<td width="31" height="15"></td>
								<td width="176" height="2" colspan="4">*'.$value['INVDESCR'].'</td>
								<td ></td>
							  </tr>';
						}
						
						else if ($value['INVDECLINE']=='1'){
						echo "";
						}				
							
						
						else {
						
				 		echo '<tr><td height="15" class="';
						if ($value['INVEST']=='1' && $_SESSION['minvno']!='0'){echo 'Verdana11Blue'; } else {echo 'Verdana11';}
						echo '" align="right">';
						
							if (number_format($value['INVUNITS'],0)==$value['INVUNITS']){
							echo  number_format($value['INVUNITS'],0);
							}
							else {
							echo $value['INVUNITS'];
							}
						
						echo '&nbsp;</td><td height="15" class="';
						if ($value['INVEST']=='1' && $_SESSION['minvno']!='0'){echo 'Verdana11Blue'; } else {echo 'Verdana11';}
						echo '">'.substr($value['INVDESCR'],0,24).'</td><td align="right" class="Verdana11 style2">'.number_format($value['INVPRICE'],2,'.','').'</td><td height="15" align="right" class="Verdana11">'.number_format($value['INVTOT'],2,'.','').'</td><td align="right" class="Verdana11Blue style3">';
						if ($value['INVDISP']=='0.00'){echo " ";} else {echo $value['INVDISP'];}
						echo '</td><td id="B'.$key.'" align="center" class="Verdana12BRed" onclick="deletion('.$key.')" onmouseover="CursorToPointer(this.id)" title="Remove this item">&nbsp;&nbsp;X';
							
						echo '</td></tr>';
						
						}
				   
				   }

}				    
                }
                
				
                ?>
                      </table>
                  </div></td>
                </tr>
                <tr>
                  <td width="31" height="2"></td>
                  <td width="176" bgcolor="#CCCCCC"></td>
                  <td width="30" align="right" bgcolor="#CCCCCC"></td>
                  <td width="31" bgcolor="#CCCCCC"></td>
                  <td width="35" ></td>
                  <td ></td>
                </tr>
                <tr class="hidden">
                  <td height="15" align="right" class="Verdana11">&nbsp;</td>
                  <td height="15" class="Verdana12">&nbsp;</td>
                  <td width="30" align="right" class="Verdana11"></td>
                  <td height="15" align="right" class="Verdana12">&nbsp;</td>
                  <td width="35" height="15"></td>
                  <td height="15"></td>
                </tr>
                <tr valign="bottom">
                  <td height="25" align="right" class="Verdana11"></td>
                  <td class="Verdana12"><strong>TOTAL</strong></td>
                  <td width="30" align="right" class="Verdana11"></td>
                  <td align="right" class="Verdana12"><input type="hidden" name="xtotal" value=""  />
                      <strong>
                      <script type="application/javascript">
					//CALCULATE THE TOTAL PRICE INCLUDING GST's
					var price =<?php $INVtotal = array();
									   $INVdiscount=array();
						//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                		          foreach ($_SESSION['procline'] as $invtot){
									$INVtotal[]=round($invtot['INVTOT'],2);
									$INVdiscount[]=round($invtot['INVDISC'],2);
                                  }
								  //SUM UP THE INDIVIDUAL PRICES
                                echo (array_sum($INVtotal));
                                 ?>;
					//CONVERT THE PRICE INTO TWO DECIMAL POINTS
			 		var priceconv = price.toFixed(2);
					//DISPLAY THE RESULT
					document.write(priceconv);
					document.forms[0].xtotal.value=priceconv;
                
                </script>
                      </strong>
                      <!-- INVOICE TOTAL -->                  </td>
                  <td width="35"></td>
                  <td></td>
                </tr>
                <tr>
                  <td height="3" align="right"></td>
                  <td height="3" bgcolor="#666666" class="Verdana12"></td>
                  <td width="30" align="right" bgcolor="#666666"></td>
                  <td height="3" align="right" bgcolor="#666666" class="Verdana12"></td>
                  <td width="35"></td>
                  <td></td>
                </tr>
            </table></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td height="26" colspan="3" align="center" valign="middle" bgcolor="#B1B4FF" class="ButtonsTable">
      <input name="save" class="button" type="submit" value="SAVE" />
      <input name="input" class="button" type="button" value="EDIT" onclick="window.open('EDIT_PROCEDURE.php?reference=0','_blank','width=733,height=442')" />
      <input name="cancel" class="button" type="button" value="CANCEL" onclick="window.open('PROCEDURES_DIRECTORY.php?species=<?php echo $_SESSION['species']; ?>','_self');" />      </td>
    </tr>
  </table>
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>