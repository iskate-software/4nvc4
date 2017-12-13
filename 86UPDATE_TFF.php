<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

$tffid=$_GET['tffid'];
$category=$_GET['category'];
$species=$_GET['species'] ;
$save = $_GET['save'] ;

$_SESSION['category'] = $category ;
$_SESSION['species'] = $species ;


mysqli_select_db($tryconnection, $database_tryconnection);
$query_TFF = "SELECT * FROM VETCAN WHERE TFFID='$tffid' LIMIT 1";
$TFF = mysqli_query($tryconnection, $query_TFF) or die(mysqli_error($mysqli_link));
$row_TFF = mysqli_fetch_assoc($TFF);

$tno = $row_TFF['TNO'] ;

 ///*

/////////////////////////////PAGING WITHIN A CATEGORY OF THE VETCAN FILE FOR A SPECIFIC SPECIES /////////////////////////
$query_VIEW="CREATE OR REPLACE VIEW INVOICE2 AS SELECT TFFID,TNO,TTYPE FROM VETCAN WHERE TSPECIES = $species AND TCATGRY = $category ORDER BY TNO ASC";
$VIEW= mysqli_query($tryconnection, $query_VIEW) or die(mysqli_error($mysqli_link));

$query_INVOICE2="SELECT * FROM INVOICE2";
$INVOICE2= mysqli_query($tryconnection, $query_INVOICE2) or die(mysqli_error($mysqli_link));
$row_INVOICE2 = mysqli_fetch_assoc($INVOICE2);

$ids= array();
$maxtno = 0 ;
$type = $row_INVOICE2['TTYPE'];
do {
 $ids[]=$row_INVOICE2['TFFID'];
 $maxtno = $row_INVOICE2['TNO'];
}
while ($row_INVOICE2 = mysqli_fetch_assoc($INVOICE2));

$key=array_search($_GET['tffid'],$ids);
$next = $key + 1 ;
$prev = $key - 1 ;

$_SESSION['key'] = $key ;
/////////////////////////////PAGING WITHIN A CATEGORY OF THE VETCAN FILE FOR A SPECIFIC SPECIES /////////////////////////

// */

$query_TFFNO = "SELECT TCATGRY, TTYPE, MAX(TNO) AS TNO FROM VETCAN WHERE TCATGRY='$category' AND TSPECIES = '$species' ";
$TFFNO = mysqli_query($tryconnection, $query_TFFNO) or die(mysqli_error($mysqli_link));
$row_TFFNO = mysqli_fetch_assoc($TFFNO);

$query_TCATGRY = "SELECT MAX(TCATGRY) AS TCATGRY FROM VETCAN WHERE TSPECIES='$_GET[species]'";
$TCATGRY = mysqli_query($tryconnection, $query_TCATGRY) or die(mysqli_error($mysqli_link));
$row_TCATGRY = mysqli_fetch_assoc($TCATGRY);

$commcode=$row_TFF['TAUTOCOMM'];
$query_COMMENTS = "SELECT * FROM ARSYSCOMM WHERE COMMCODE='$commcode' LIMIT 1";
$COMMENTS = mysqli_query($tryconnection, $query_COMMENTS) or die(mysqli_error($mysqli_link));
$row_COMMENTS = mysqli_fetch_assoc($COMMENTS);

$query_VACCINES = "SELECT * FROM VACCINES";
$VACCINES = mysqli_query($tryconnection, $query_VACCINES) or die(mysqli_error($mysqli_link));
$row_VACCINES = mysqli_fetch_assoc($VACCINES);


if (isset($_POST["save"])  && $_GET["tffid"] == "0") {

//INSERT
	$tno=$_POST['tno'];
		if ($row_TFFNO['TNO'] >= (int)$tno){
		$query_UPDATESEQ = "UPDATE VETCAN SET TNO=TNO+1 WHERE TNO>='$tno' AND TSPECIES='$_GET[species]' AND TCATGRY='$_GET[category]'";
		$UPDATESEQ = mysqli_query($tryconnection, $query_UPDATESEQ) or die(mysqli_error($mysqli_link));
		}
		
		// make sure the trevcats are all right adjusted.
		
$rev = trim($_POST['trevcat']) ;
while (strlen($rev) < 3 ) {
 $rev = ' ' . $rev ;
}
 
$insertSQL = sprintf("INSERT INTO VETCAN (TSPECIES, TCATGRY, TTYPE, TNO, TDESCR, TFEE, TDISP, TPROF, TCOST, TENTER, TUNITS, TINCMAST, TVENDOR, TDISCOUNT, TREVCAT, 
         TFLOAT, TGET, TUPDATE, TFLAGS, TNOPRINT, TWDESCR, TINV, TAUTOCOMM, TINVCOMM, THISTCOMM, TMODICODE, TTAX, TNOHST, TRADLOG, TSURGLOG, TNARCLOG, TOUTPATIEN, 
         TINHOSP, TSERUM, TVACCS, TUAC, `DATETIME`, TPAYDISC) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' , '%s', '%s', '%s', '%s', '%s','%s','%s', '%s')",
					   $_GET['species'],
                       $_POST['tcatgry'],
                       $_POST['ttype'],
                       $_POST['tno'],
                       mysqli_real_escape_string($mysqli_link, $_POST['tdescr']),
                       $_POST['tfee'],
                       $_POST['tdisp'],
                       $_POST['tprof'],
                       $_POST['tcost'],
                       !empty($_POST['tenter']) ? "1" : "0",
                       !empty($_POST['tunits']) ? "1" : "0",
                       !empty($_POST['tincmast']) ? "1" : "0",
                       $_POST['tvendor'],
                       !empty($_POST['tdiscount']) ? "1" : "0",
                       $rev,
                       !empty($_POST['tfloat']) ? "1" : "0",
                       !empty($_POST['tget']) ? "1" : "0",
                       !empty($_POST['tupdate']) ? "1" : "0",
                       $_POST['tflags'],
                       !empty($_POST['tnoprint']) ? "1" : "0",
                       mysqli_real_escape_string($mysqli_link, $_POST['twdescr']),
                       $_POST['tinv'],
                       $_POST['tautocomm'],
                       !empty($_POST['tinvcomm']) ? "1" : "0",
                       !empty($_POST['thistcomm']) ? "1" : "0",
                       !empty($_POST['tmodicode']) ? "1" : "0",
                       $_POST['ttax'],
                       !empty($_POST['tnohst']) ? "1" : "0",
                       !empty($_POST['tradlog']) ? "1" : "0",
                       !empty($_POST['tsurglog']) ? "1" : "0",
                       !empty($_POST['tnarclog']) ? "1" : "0",
                       !empty($_POST['toutpatien']) ? "1" : "0",
                       !empty($_POST['tinhosp']) ? "1" : "0",
                       !empty($_POST['tserum']) ? "1" : "0",
                       $_POST['tvaccs'],
                       $_POST['tuac'],
                       date("Y-m-d H:s:i"),
					   $_POST['tpaydisc']
					   );

  mysqli_select_db($tryconnection, $database_tryconnection);
  $Result1 = mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
  if (isset($_POST['save'])){
    header("Location: TFF_DIRECTORY.php?species=$_GET[species]&check=");
  }
  else {
    header("Location: UPDATE_TFF.php?tffid=$_POST[invpointer]&check=&save=0");
    }
  
}  // End of insert.



elseif ((isset($_POST["save"]) ||  isset($_POST["check"]) && !isset($_POST['delete'])) && $_GET["tffid"] != "0") {

//UPDATE
	if ($row_TFF['TNO']!=$_POST['tno'])
	{
	$tno=$_POST['tno'];
		if ($tno<$row_TFF['TNO']){
		$query_UPDATESEQ = "UPDATE VETCAN SET TNO=TNO+1 WHERE TNO>='$tno' AND TNO<'$row_TFF[TNO]' AND TSPECIES='$_GET[species]' AND TCATGRY='$_GET[category]'";
		}
		else {
		$query_UPDATESEQ = "UPDATE VETCAN SET TNO=TNO-1 WHERE TNO<='$tno' AND TNO>'$row_TFF[TNO]' AND TSPECIES='$_GET[species]' AND TCATGRY='$_GET[category]'";
		}
	$UPDATESEQ = mysqli_query($tryconnection, $query_UPDATESEQ) or die(mysqli_error($mysqli_link));
	}
		
		// make sure the trevcats are all right adjusted.
		
$rev = trim($_POST['trevcat']) ;
while (strlen($rev) < 3 ) {
$rev = ' ' . $rev ;
 }
 

$updateSQL = sprintf("UPDATE VETCAN SET TSPECIES='%s', TCATGRY='%s', TTYPE='%s', TNO='%s', TDESCR='%s', TFEE='%s', TDISP='%s', TPROF='%s', TCOST='%s', 
                     TENTER='%s', TUNITS='%s', TINCMAST='%s', TVENDOR='%s', TDISCOUNT='%s', TREVCAT='%s', TFLOAT='%s', TGET='%s', TUPDATE='%s', TFLAGS='%s', 
                     TNOPRINT='%s', TWDESCR='%s', TINV='%s', TAUTOCOMM='%s', TINVCOMM='%s', THISTCOMM='%s', TMODICODE='%s', TTAX='%s',  TNOHST='%s', TRADLOG='%s', 
                     TSURGLOG='%s', TNARCLOG='%s', TOUTPATIEN='%s', TINHOSP='%s', TSERUM='%s', TVACCS='%s', TUAC='%s', `DATETIME`='%s',  
                     TPAYDISC='%s' WHERE TFFID='%s' LIMIT 1",
					   $_GET['species'],
                       $_POST['tcatgry'],
                       $_POST['ttype'],
                       $_POST['tno'],
                       mysqli_real_escape_string($mysqli_link, $_POST['tdescr']),
                       $_POST['tfee'],
                       $_POST['tdisp'],
                       $_POST['tprof'],
                       $_POST['tcost'],
                       !empty($_POST['tenter']) ? "1" : "0",
                       !empty($_POST['tunits']) ? "1" : "0",
                       !empty($_POST['tincmast']) ? "1" : "0",
                       $_POST['tvendor'],
                       !empty($_POST['tdiscount']) ? "1" : "0",
                       $rev,
                       !empty($_POST['tfloat']) ? "1" : "0",
                       !empty($_POST['tget']) ? "1" : "0",
                       !empty($_POST['tupdate']) ? "1" : "0",
                       $_POST['tflags'],
                       !empty($_POST['tnoprint']) ? "1" : "0",
                       mysqli_real_escape_string($mysqli_link, $_POST['twdescr']),
                       $_POST['tinv'],
                       $_POST['tautocomm'],
                       !empty($_POST['tinvcomm']) ? "1" : "0",
                       !empty($_POST['thistcomm']) ? "1" : "0",
                       !empty($_POST['tmodicode']) ? "1" : "0",
                       $_POST['ttax'],
                       !empty($_POST['tnohst']) ? "1" : "0",
                       !empty($_POST['tradlog']) ? "1" : "0",
                       !empty($_POST['tsurglog']) ? "1" : "0",
                       !empty($_POST['tnarclog']) ? "1" : "0",
                       !empty($_POST['toutpatien']) ? "1" : "0",
                       !empty($_POST['tinhosp']) ? "1" : "0",
                       !empty($_POST['tserum']) ? "1" : "0",
                       $_POST['tvaccs'],
                       $_POST['tuac'],
                       date("Y-m-d H:s:i"),
					   $_POST['tpaydisc'],
					   $_GET['tffid']
					   );

mysqli_select_db($tryconnection, $database_tryconnection);
$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));

// update any associated procedure
$invprice = $_POST['tfee'] ;
$feefile = $_GET['species'] ;
$invmaj = $_POST['tcatgry'] ;
$invmin = $_POST['tno'] ;

$QUERY_Proc = "UPDATE PROCEDUR SET INVPRICE = '$invprice', INVTOT = INVPRICE*INVUNITS WHERE 
FEEFILE='$feefile' AND INVMAJ = '$invmaj' AND INVMIN = '$invmin' AND FEEUPDTE = '1' ";
$UPDATE_Proc = mysqli_query($tryconnection, $QUERY_Proc) or die(mysqli_error($mysqli_link)) ; 


  if (isset($_POST['save'])){
    header("Location: TFF_DIRECTORY.php?species=$_GET[species]&check=");
    }
  else {
    header("Location: UPDATE_TFF.php?tffid=$_POST[invpointer]&species=$_GET[species]&category=$_POST[tcatgry]&check=");
    }
  
 
// header("Location: TFF_DIRECTORY.php?species=$_GET[species]&category=$row_TFF[TCATGRY]&check=");
} // End of update

//DELETE
elseif (isset($_POST['delete']) && $_GET['tffid'] != "0"){
$tno=$row_TFF['TNO'];
$query_UPDATESEQ = "UPDATE VETCAN SET TNO=TNO-1 WHERE TNO>'$tno' AND TSPECIES='$_GET[species]' AND TCATGRY='$_GET[category]'";
$UPDATESEQ = mysqli_query($tryconnection, $query_UPDATESEQ) or die(mysqli_error($mysqli_link));
$deleteSQL="DELETE FROM VETCAN WHERE TFFID='$tffid'";
mysqli_select_db($tryconnection, $database_tryconnection);
$Result1 = mysqli_query($tryconnection, $deleteSQL) or die(mysqli_error($mysqli_link));
//  if (isset($_POST['save'])){
    header("Location: TFF_DIRECTORY.php?species=$_GET[species]&check=");
//    }
//  else {
 //   header("Location: UPDATE_TFF.php?tffid=$_POST[invpointer]&species=$_GET[species]&check=");
//    }
  
//header("Location: TFF_DIRECTORY.php?species=$_GET[species]&check=");
} //End of Delete.


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<style type="text/css">
<!--
.style2 {color: #990033}
.CustomizedButton1 {
	font-family: Verdana;
	font-size: 12px;
	width: 80px;
	height: 27px;
	margin-left: 4px;
	margin-right: 4px;
}

.CustomizedButton2 {
	font-family: Verdana;
	font-size: 20px;
	width: ;
	height: 27px;
	margin-left: 4px;
	margin-right: 4px;
}

-->
</style>
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
/*document.getElementById('invpointer') = <?php echo $tffid; ?>*/

}

function checkvitals(){
 valid = true;
 
 if (document.getElementById('trevcat').value == false) {
  alert('Please provide a Revenue Summary Category.');
  valid = false;
 }
 if (!document.tff.tincmast.checked ){
 alert('Please check off the history box..');
 valid = false;
 }
 return valid;
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

function nextinv(x){
max = <?php echo $maxtno; ?> ;
// if (x > max) {x = max ;}
document.tff.invpointer.value=x;
document.tff.submit();
}

function previnv(x){
//if (x==0){x=1;}
document.tff.invpointer.value=x;
document.tff.submit();
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

</script>

<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload();MM_preloadImages('../../IMAGES/left_arrow_dark.JPG','../../IMAGES/right_arrow_dark.JPG')" onunload="bodyonunload()">
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
<form name="tff" action="" class="FormDisplay" id="tff" method="POST" onsubmit="return checkvitals();" >
<input type="hidden" id="invpointer" name="invpointer" value=""  />
<input type="hidden" id="xdelete" name="xdelete" value=""  />
<input type="hidden" name="petname" value="$PETNAME"  />
<table width="733" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="all">
  <tr>
    <td height="25" colspan="2" align="center" valign="middle" class="Verdana13B">RECORD # <?php if ($_GET['tffid']!=0){echo $row_TFF['TFFID'];} else {echo $row_TFFNO['TNO']+1;} ?> </td>
    </tr>
  <tr>
    <td width="367"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="35%" height="25" align="left" class="RequiredItems">Category Number</td>
        <td width="65%" height="25" align="left"><input name="tcatgry" type="text" class="Inputright" id="tcatgry" value="<?php if ($_GET['category']=='0'){echo $row_TCATGRY['TCATGRY']+1;} elseif ($_GET['tffid']=='0'){echo $_GET['category'];} else {echo $row_TFF['TCATGRY'];} ?>" size="3" maxlength="3" readonly="readonly" /></td>
      </tr>
      <tr>
        <td height="25" align="left" class="RequiredItems">Category Name</td>
        <td height="25" align="left"><input name="ttype" type="text" class="Input" id="ttype" value="<?php echo $type; ?>" size="15" maxlength="15" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
      </tr>
    </table></td>
    <td width="366"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="34%" height="25" align="left" class="RequiredItems">Sub-treatment</td>
        <td width="15%" height="25" align="left"><input name="tno" type="text" class="Inputright" id="tno" value="<?php if ($_GET['tffid']!=0){echo $row_TFF['TNO'];} else {echo $row_TFFNO['TNO']+1;} ?>" size="3" maxlength="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td width="25%" height="25" align="left" class="Labels">OVMA Code</td>
        <td width="26%" height="25" align="left"><input name="tuac" type="text" class="Input" id="tuac" value="<?php echo $row_TFF['TUAC']; ?>" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
      </tr>
      <tr>
        <td height="25" align="left" class="RequiredItems">Invoice Detail</td>
        <td height="25" colspan="3" align="left">
        <input name="tdescr" type="text" class="Input" id="tdescr" value="<?php if ($_GET['category']=='0'){echo "First Item";} else {echo $row_TFF['TDESCR'];} ?>" size="25" maxlength="25" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>         
        </td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="35%" height="25" align="left" class="Labels">Worksheet</td>
        <td width="33%" height="25" align="left"><input name="twdescr" type="text" class="Input" id="twdescr" value="<?php echo $row_TFF['TWDESCR']; ?>" size="15" maxlength="15" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td height="25" align="left"><label><input name="tnoprint" type="checkbox" id="tnoprint" <?php if ($row_TFF['TNOPRINT']=='1'){echo "CHECKED";}; ?> />&nbsp;No print</label></td>
        </tr>
    </table></td>
    <td></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="34%" height="25" align="left" class="RequiredItems">Fee</td>
        <td width="25%" height="25" align="left"><input name="tfee" type="text" class="Inputright" id="tfee" value="<?php echo $row_TFF['TFEE']; ?>" size="7" maxlength="13" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td width="13%" height="25" align="left" class="Labels">Cost</td>
        <td width="28%" height="25" align="left"><input name="tcost" type="text" class="Inputright" id="tcost" value="<?php echo $row_TFF['TCOST']; ?>" size="7" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
      </tr>
      <tr>
        <td height="25" align="left" class="Labels">Dispensing Fee</td>
        <td height="25" align="left"><input name="tdisp" type="text" class="Inputright" id="tdisp" value="<?php echo $row_TFF['TDISP']; ?>" size="7" maxlength="9" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td height="25" align="right" class="Labels">&nbsp;</td>
        <td height="25" align="left" class="Labels"><label><input name="tget" type="checkbox" id="tget" <?php if ($row_TFF['TGET']=='1'){echo "CHECKED";}; ?>/>&nbsp;Modify Fee</label></td>
      </tr>
      <tr>
        <td height="25" align="left" class="RequiredItems">Summary Category</td>
        <td height="25" align="left"><input name="trevcat" type="text" class="Inputright" id="trevcat" value="<?php echo $row_TFF['TREVCAT']; ?>" size="3" maxlength="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td height="25" align="left" class="RequiredItems">Prof. %</td>
        <td height="25" align="left"><input name="tstat" type="text" class="Inputright" id="tstat" value="<?php echo $row_TFF['TSTAT']; ?>" size="3" maxlength="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        </tr>
      <tr>
        <td height="25" align="left" class="Labels">Vendor Code</td>
        <td height="25" align="left"><input name="tvendor" type="text" class="Input" id="tvendor" value="<?php echo $row_TFF['TVENDOR']; ?>" size="7" maxlength="7" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td height="25" colspan="2" align="left" class="Labels">(Inventory only)</td>        
        </tr>
      <tr>
        <td height="25" align="left" class="Labels">Prov.Tax Rate</td>
        <td height="25" align="left"><input name="ttax" type="text" class="Inputright" id="ttax" value="<?php echo $row_TFF['TTAX']; ?>" size="7" maxlength="9" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td height="25" align="right" class="Labels">&nbsp;</td>
        <td height="25" align="left" class="Labels"><label><input name="tnohst" type="checkbox" id="tnohst" <?php if ($row_TFF['TNOHST']=='1'){echo "CHECKED";}; ?> />&nbsp;HST Exempt</label></td>
        </tr>
    </table></td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="2%" height="25" align="right" valign="middle">&nbsp;</td>
        <td width="51%" height="25" align="left" class="RequiredItems"><label><input name="tunits" type="checkbox" id="tunits" <?php if ($row_TFF['TUNITS']=='1'){echo "CHECKED";}; ?>/>&nbsp;Units in Invoices</label></td>
        <td height="25" colspan="2" align="left" class="RequiredItems"><label><input name="tenter" type="checkbox" id="tenter" <?php if ($row_TFF['TENTER']=='1'){echo "CHECKED";}; ?>/>&nbsp;Modify Item</label></td>
        </tr>
      <tr>
        <td height="25" align="right" valign="middle">&nbsp;</td>
        <td height="25" colspan="2" align="left" class="Labels"><label><input name="tfloat" type="checkbox" id="tfloat" <?php if ($row_TFF['TFLOAT']=='1'){echo "CHECKED";}; ?> />&nbsp;Decimal Point in Units</label></td>
        <td width="27%" height="25" align="left" class="Labels">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="right" valign="middle">&nbsp;</td>
        <td height="25" colspan="2" align="left" class="RequiredItems"><label><input name="tincmast" type="checkbox" id="tincmast" <?php if ($row_TFF['TINCMAST']=='1'){echo "CHECKED";}; ?> />&nbsp;Include in history Files</label></td>
        <td height="25" align="left" class="Labels">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="right" valign="middle">&nbsp;</td>
        <td height="25" align="left" class="Labels"><label onclick="showbutton();"><input name="tupdate" type="checkbox" id="tupdate" <?php if ($row_TFF['TUPDATE']=='1'){echo "CHECKED";}; ?>/>&nbsp;Update Patient Files</label></td>
        <td height="25" colspan="2" align="left" class="Labels"><label onclick="openpatientfile('1');"><input name="tserum" type="checkbox" id="tserum" <?php if ($row_TFF['TSERUM']=='1'){echo "CHECKED";}; ?>/>&nbsp;Serum&nbsp; #</label></td>
        </tr>
      <tr>
        <td height="25" align="right" valign="middle">&nbsp;</td>
        <td height="25" align="left" class="Labels"><label><input name="tdiscount" type="checkbox" id="tdiscount" <?php if ($row_TFF['TDISCOUNT']=='1'){echo "CHECKED";}; ?>/>&nbsp;Discount Item</label></td>
        <td height="25" colspan="2" align="left" class="Labels"><label>
          <input type="checkbox" name="tpaydisc" id="tpaydisc" <?php if ($row_TFF['TPAYDISC']=='1'){echo "CHECKED";}; ?>/>
          Payment Discount Item</label></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="18%" height="20" align="left" class="Labels">Automatic</td>
        <td width="4%" height="20" align="left" class="Labels"><input name="tradlog" type="checkbox" id="tradlog" <?php if ($row_TFF['TRADLOG']=='1'){echo "CHECKED";}; ?>/></td>
        <td width="23%" height="20" align="left" class="Labels">Radiology Log</td>
        <td width="4%" height="20" align="left" class="Labels"><input name="tsurglog" type="checkbox" id="tsurglog" <?php if ($row_TFF['TSURGLOG']=='1'){echo "CHECKED";}; ?>/></td>
        <td width="22%" height="20" align="left" class="Labels">Surgery Log</td>
        <td width="4%" height="20" align="left" class="Labels"><input name="tnarclog" type="checkbox" id="tnarclog" <?php if ($row_TFF['TNARCLOG']=='1'){echo "CHECKED";}; ?>/></td>
        <td width="25%" height="20" align="left" class="Labels">Anex./Narc. Log</td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="18%" height="20" align="left" class="Labels">Include In</td>
        <td width="4%" height="20" align="left" class="Labels"><input name="toutpatien" type="checkbox" id="toutpatien" <?php if ($row_TFF['TOUTPATIEN']=='1'){echo "CHECKED";}; ?>/></td>
        <td width="23%" height="20" align="left" class="RequiredItems">Out-Patient Billing</td>
        <td width="4%" height="20" align="left" class="Labels"><input name="tinhosp" type="checkbox" id="tinhosp" <?php if ($row_TFF['TINHOSP']=='1'){echo "CHECKED";}; ?>/></td>
        <td width="29%" height="20" align="left" class="Labels">In-Patient Billing</td>
        <td width="7%" height="20" class="Labels">&nbsp;</td>
        <td width="15%" height="20" class="Labels">&nbsp;</td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td height="168" colspan="2">
    <table height="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="124" height="25" align="left" class="Labels">Comment Code</td>
        <td width="133" height="25" align="left"><input name="tautocomm" type="text" class="Inputright" id="tautocomm" value="<?php echo $row_TFF['TAUTOCOMM']; ?>" size="8" maxlength="8" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td width="133" height="25" align="left"><input type="button" name="viewcodes" id="viewcodes" value="View Codes" onclick="window.open('../COMMENTS/COMMENTS_LIST.php??display=INVOICE&path=TFF','_blank','width=732,height=553')" /></td>
        <td height="25" colspan="2" align="center">&nbsp;</td>
      </tr>
      <tr align="left">
        <td colspan="3" rowspan="4" align="center" valign="top" class="Labels"><textarea name="commtext" cols="50" rows="9" class="commentarea" id="commtext"><?php echo $row_COMMENTS['COMMENT']; ?></textarea></td>
        <td width="30" height="30" align="right" valign="bottom" class="Labels"><input name="thistcomm" type="checkbox" id="thistcomm" <?php if ($row_TFF['THISTCOMM']=='1'){echo "CHECKED";}; ?>/></td>
        <td width="309" align="left" valign="bottom" class="Labels">Add comments to the medical history file</td>
      </tr>
      <tr align="left">
        <td height="31" align="right" valign="middle" class="Labels"><input name="tinvcomm" type="checkbox" id="tinvcomm" <?php if ($row_TFF['TINVCOMM']=='1'){echo "CHECKED";}; ?> /></td>
        <td height="31" align="left" valign="middle" class="Labels">Add comments on the invoice</td>
      </tr>
      <tr align="left">
        <td align="right" valign="top" class="Labels"><input name="tmodicode" type="checkbox" id="tmodicode" <?php if ($row_TFF['TMODICODE']=='1'){echo "CHECKED";}; ?>/></td>
        <td align="left" valign="top" class="Labels">Modify comment code at invoice time</td>
      </tr>
      <tr align="left">
        <td height="25" colspan="2" align="center" valign="middle" class="Labels"><input type="button" name="patientfile" id="patientfile" value="Edit updating patient file fields" <?php if($row_TFF['TUPDATE']=="1"){echo "style='display:'";} else {echo "style='display:none'";}?> onclick="openpatientfile('2');" /></td>
        </tr>
    </table></td>
    </tr>
  
  <tr>
    <td colspan="2">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ButtonsTable">
      <tr>
        <td width="11%" height="44" align="center" valign="middle">&nbsp;</td>
        <td width="38%" align="right" valign="middle" id="xxprev" <?php if ($row_TFF['TNO'] == 1) {echo "style='visibility:hidden' ";}?> onmouseover="CursorToPointer(this.id);"><img src="../../IMAGES/left_arrow_light.JPG" alt="lal" width="28" height="28" id="Image1" onmouseover="MM_swapImage('Image1','','../../IMAGES/left_arrow_dark.JPG',1)" onmouseout="MM_swapImgRestore()"  onclick="previnv('<?php echo $ids[$key-1]; ?>');" title="Save changes and go to Previous Item"/></td>
        <td width="2%" align="right" valign="middle">&nbsp;</td>
        <td width="38%" height="44" align="left" valign="middle" id="xxnext" <?php if ($row_TFF['TNO'] == $maxtno) {echo "style='visibility:hidden' ";}?> onmouseover="CursorToPointer(this.id);"><img src="../../IMAGES/right_arrow_light.JPG" alt="ral" width="28" height="28" id="Image2" onmouseover="MM_swapImage('Image2','','../../IMAGES/right_arrow_dark.JPG',1)" onmouseout="MM_swapImgRestore()"  onclick="nextinv('<?php echo $ids[$key+1]; ?>');" title="Save changes and go to Next Item"/></td>
        <td width="11%" align="center" valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td height="34" colspan="5" align="center" valign="middle">
    	<input type="submit" name="save" id="save" class="button" value="SAVE" />
    	<input type="button" name="add" id="add" class="button" value="ADD" onclick="window.open('UPDATE_TFF.php?species=<?php echo $row_TFF['TSPECIES']; ?>&category=<?php echo $_GET['category']; ?>&tffid=0','_self')" />
    	<input type="button" name="scan" id="scan" class="button" value="SCAN" onclick="window.open('TFF_DIRECTORY.php?species=<?php echo $row_TFF['TSPECIES']; ?>&category=<?php echo $row_TFF['TCATGRY']; ?>','_self')" />
    	<input type="submit" name="delete" id="delete" class="button" value="DELETE" />
    	<input type="reset" name="cancel" id="cancel" class="button" value="CANCEL" onclick="window.open('TFF_DIRECTORY.php?species=','_self')" /></td>
    </tr>
</table> 
<input name="check" id="check" type="hidden" value="1"/> 
<input name="tinv" id="tinv" type="hidden" value=""/>
<input name="tprof" id="tprof" type="hidden" value=""/>
<input name="tflags" id="tflags" type="hidden" value="<?php echo $row_TFF['TFLAGS']; ?>" />
<input name="tvaccs" id="tvaccs" type="hidden" value="<?php echo $row_TFF['TVACCS']; ?>" size="80" />
<!--<input name="tnohst" id="tnohst" type="hidden" value="<?php echo $row_TFF['TNOHST']; ?>" /> -->
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>