<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");
include("../../ASSETS/tax.php");

////////// One-timer for the tracker for BBAH
    $query_invdatetime="SELECT STR_TO_DATE('$_SESSION[minvdte]','%m/%d/%Y')";
	$invdatetime= mysqli_query($tryconnection, $query_invdatetime) or die(mysqli_error($mysqli_link));
	$row_invdatetime=mysqli_fetch_array($invdatetime);
/////////////////////CLIENT + PATIENT INFO/////////////////////////
if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
elseif (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}
$_SESSION['ponum'] = $patient ;
if (isset($_GET['client'])){
$client=$_GET['client'];
$_SESSION['client']=$_GET['client'];
}
elseif (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}


$cat=$_GET['subcat'];
$ps = $_GET['product'];


if(isset($_POST['ok'])){
$ps = "j";
}


/////////////////////CLIENT + PATIENT INFO/////////////////////////
mysqli_select_db($tryconnection, $database_tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient' LIMIT 1";
$PATIENT_CLIENT = mysqli_query($tryconnection, $query_PATIENT_CLIENT) or die(mysqli_error($mysqli_link));
$row_PATIENT_CLIENT = mysqli_fetch_assoc($PATIENT_CLIENT);

$pettype=$row_PATIENT_CLIENT['PETTYPE'];
$_SESSION['pettype'] = $pettype ;
$psex=$row_PATIENT_CLIENT['PSEX'];
$pdob=$row_PATIENT_CLIENT['PDOB'];


if(!isset($_SESSION['GTAX']) && !isset($_SESSION['PTAX'])){
$_SESSION['GTAX']=$row_PATIENT_CLIENT['GTAX'];
$_SESSION['PTAX']=$row_PATIENT_CLIENT['PTAX'];
}

//        


function categ($tryconnection,$pettype)
{
$query_CATEGORY ="SELECT DISTINCT TCATGRY, TTYPE FROM VETCAN WHERE TSPECIES='$pettype' ORDER BY TCATGRY ASC";
$CATEGORY = mysqli_query($tryconnection, $query_CATEGORY) or die(mysqli_error($mysqli_link));
$row_CATEGORY = mysqli_fetch_assoc($CATEGORY);
$totalRows_CATEGORY = mysqli_num_rows($CATEGORY);

echo"<select name='category1' class='SelectList' id='category1' multiple='multiple' onchange='category();' >";
do {
echo"<option value='".$row_CATEGORY['TCATGRY']."'>";
echo $row_CATEGORY['TTYPE'];
echo"</option>\n";
} while ($row_CATEGORY = mysqli_fetch_assoc($CATEGORY));
echo"</select>";		 

}

//////////////////////////LIST PRODUCT SERVICE FROM TREATMENT FEE FILE////////////////////
function subcateg($tryconnection,$cat, $pettype)
{
$query_PRODUCTSERVICE = sprintf("SELECT TFFID, TNO, TDESCR, TTYPE, TFEE, TCATGRY, TDISCOUNT, TSTAT, TNOHST FROM VETCAN WHERE TCATGRY = '%s' AND  TSPECIES='$pettype' ORDER BY TNO ASC",mysqli_real_escape_string($mysqli_link, $cat));
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

$query_SELECTEDITEM = sprintf("SELECT * FROM VETCAN WHERE TFFID = '%s' LIMIT 1",$ps);
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
						   
						   if ($_POST['xlabel']=='1'){
						   	$invunits=round($_POST['quantity'],2);
							$memo='2';
							
							switch ($_POST['dosage']){
								case 1:
								$dosage='SID';
								break;
								case 2:
								$dosage='BID';
								break;
								case 3:
								$dosage='TID';
								break;
								case 4:
								$dosage='QID';
								break;
								case 5:
								$dosage='EOD';
								break;
								}
							
							$lcode=$dosage.$_POST['xtype'];
$query_TAUTOCOMM = "SELECT * FROM ARSYSCOMM WHERE COMMCODE='$lcode'";
$TAUTOCOMM = mysqli_query($tryconnection, $query_TAUTOCOMM) or die(mysqli_error($mysqli_link));
$row_TAUTOCOMM = mysqli_fetch_assoc($TAUTOCOMM);
							$lcomment=$row_TAUTOCOMM['COMMENT'];
							$lcomment=str_replace('XXX', $invunits/$_POST['dosage']/$_POST['days'], $lcomment);
							$lcomment=str_replace('YYY', $_POST['days'], $lcomment);
							$autcomm=$_POST['autocomm'];
$query_TAUTOCOMM = "SELECT * FROM ARSYSCOMM WHERE COMMCODE='$autcomm'";
$TAUTOCOMM = mysqli_query($tryconnection, $query_TAUTOCOMM) or die(mysqli_error($mysqli_link));
$row_TAUTOCOMM = mysqli_fetch_assoc($TAUTOCOMM);
							$invoicecomment=str_replace('$PETNAME', $_SESSION['petname'], $row_TAUTOCOMM['COMMENT']);
							if (!empty($invoicecomment)){$memo='3';}
						   }
						   //if there is a comment and no label, set the memo to 3 (this this is redundant)
						   else if (!empty($_POST['autocomm']) && $_POST['xlabel']=='0'){
						    $invunits=$_POST['invunits'];
							$memo='3';					   						   
						   }
						   else {
						    $invunits=$_POST['invunits'];
							$memo='';					   
						   }
						   
						   //CREATE AN ARRAY FROM ENTIRE RECORD FROM VETCAN FOR SELECTED ITEM 			
						   $item = array('INVNO' => $_SESSION['minvno'],
						   				 'INVCUST' => $_SESSION['client'],
										 'INVPET' => $_SESSION['patient'],
										 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
										 'INVMAJ' => $_POST['invmaj'],
										 'INVMIN' => $_POST['invmin'],
										 'INVDOC' => $_SESSION['doctor'],
										 'INVSTAFF' => $_SESSION['staff'],
										 'INVUNITS' => $invunits,
										 'INVDESCR' => $_POST['invdescr'],
										 'INVPRICE' => $_POST['invprice'],
										 'INVTOT' => round($_POST['invtot'],2),
										 'INVINCM' => $_POST['invincm'],
										 'INVDISC' => $_POST['invdisc'],
										 'INVLGSM' => $pettype,
										 'INVREVCAT' => $_POST['invrevcat'],
										 'INVGST' => round($_POST['invgst'],2),
										 'INVTAX' => round($_POST['invtax'],2), 
										 'REFCLIN' => $_POST['refclin'],
										 'REFVET' => $_POST['refvet'],
										 'INVUPDTE' => $_POST['invupdte'],										
										 'INVFLAGS' => $_POST['invflags'],
										 'INVDISP' => $_POST['invdisp'],
										 'INVGET' => $_POST['invget'],
										 'INVPERCNT' => $_POST['invpercnt'],
										 'INVHYPE' => $_POST['invhype'],
										 'AUTOCOMM' => $_POST['autocomm'],
										 'INVCOMM' => $_POST['invcomm'],
										 'HISTCOMM' => $_POST['histcomm'],
										 'MODICODE' => $_POST['modicode'],
										 'INVNARC' => $_POST['invnarc'],
										 'INVVPC' => $_POST['invvpc'],
										 'INVUPRICE' => $_POST['invuprice'],
										 'INVPKGQTY' => $_POST['invpkgqty'],
										 'MEMO' => $memo,
										 'INARCLOG' => $_POST['inarclog'],
										 'IRADLOG' => $_POST['iradlog'],
										 'ISURGLOG' => $_POST['isurlog'],
										 'IUAC' => $_POST['iuac'],
										 'INVSERUM' => $_POST['invserum'],
										 'INVEST' => $_POST['invest'],
										 'INVDECLINE' => $_POST['invdecline'],
										 'PETNAME' => $_POST['petname'],
										 'INVOICECOMMENT' => $invoicecomment,
										 'INVPRU' => $_POST['xlabel'],
										 'XDISC' => $_POST['xdisc'],
										 'MTAXRATE' => $_POST['mtaxrate'],
										 'TUNITS' => $_POST['tunits'],
										 'TFLOAT' => $_POST['tfloat'],
										 'INVSTAT' => $_POST['invstat'],
										 'TENTER' => $_POST['tenter'],
										 'LCODE' => $lcode,
										 'LCOMMENT' => $lcomment,
										 'INVNOHST' => $_POST['invnohst'],
										 'INVPAYDISC' => $_POST['invpaydisc'],
										 'INVHXCAT' => $_POST['invhxcat']
										 );
						$_SESSION['invline'][] = $item;
						
						//inline note
						   if (!empty($_POST['inlinenote'])){
						   $item1 = array(
						   				 'INVNO' => $_SESSION['minvno'],
						   				 'INVCUST' => $_SESSION['client'],
										 'INVPET' => $_SESSION['patient'],
										 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
										 'INVMAJ' => $_POST['invmaj'],
										 'INVMIN' => $_POST['invmin'],
										 'INVDOC' => $_SESSION['doctor'],
										 'INVSTAFF' => $_SESSION['staff'],
										 'INVDESCR' => $_POST['inlinenote'],
										 'INVINCM' => $_POST['invincm'],
										 'INVLGSM' => $pettype,
										 'INVHYPE' => $_POST['invhype'],
										 'MEMO' => '1',
										 'INVEST' => $_POST['invest'],
										 'INVSTAT' => $_POST['invstat'],
										 'INVDECLINE' => $_POST['invdecline'],
										 'PETNAME' => $_POST['petname'],
										 'INVHXCAT' => $_POST['invhxcat']
			 						 );
						$_SESSION['invline'][] = $item1;
							}
							
						//serums	
							if ($_POST['invserum']=='1'){
							$meatnsauce=array();
							$spice=array() ;
							$meal=array() ;
								for ($i=0; $i < count($_POST['meat']); $i++){
									if ($_POST['potatoes'][$i]<4 && $_POST['potatoes'][$i]>0){
									$potatoes=" Dur ".$_POST['potatoes'][$i]." yrs";				
									}
									else if($_POST['potatoes'][$i]==6){
									$potatoes=" Dur ".$_POST['potatoes'][$i]." mths";
									}
									else if ($_POST['potatoes'][$i]==4 || $_POST['potatoes'][$i]==8){
									$potatoes=" Dur ".$_POST['potatoes'][$i]." wks";
									}
									else {$potatoes="";}
								$meatnsauce[]=$_POST['sauce'][$i]." (".$_POST['meat'][$i].") ".$potatoes;
								$spice[]= $_POST['spice'][$i];
								$meal[$i][0] = $meatnsauce[$i];
								$meal[$i][1] = $spice[$i] ;
								}
							
							foreach ($meal as $mns){
							   $item2 = array(
							   				 'INVNO' => $_SESSION['minvno'],
											 'INVCUST' => $_SESSION['client'],
											 'INVPET' => $_SESSION['patient'],
											 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
											 'INVMAJ' => $_POST['invmaj'],
											 'INVMIN' => $_POST['invmin'],
											 'INVDOC' => $_SESSION['doctor'],
											 'INVSTAFF' => $_SESSION['staff'],
											 'INVDESCR' => $mns[0],
											 'INVINCM' => $_POST['invincm'],
											 'INVLGSM' => $pettype,
											 'REFCLIN' => $_POST['refclin'],
											 'REFVET' => $_POST['refvet'],
											 'INVFLAGS' => $mns[1],
											 'INVHYPE' => $_POST['invhype'],
											 'MEMO' => $memo,
											 'INVSERUM' => '2',
											 'INVEST' => $_POST['invest'],
										     'INVSTAT' => $_POST['invstat'],
											 'INVDECLINE' => $_POST['invdecline'],
											 'PETNAME' => $_POST['petname'],
										     'INVNOHST' => $_POST['tnohst'],
											 'INVHXCAT' => $_POST['invhxcat']
											 );
							$_SESSION['invline'][] = $item2;
							}//foreach ($meatnsauce as $mns)
							
							//rabtag
							if (!empty($_POST['xrabtag']) || !empty($_POST['xrabtaga'])){
								if (!empty($_POST['xrabtag'])){
								$rabtag=$_POST['xrabtag'];
								}
								else if (!empty($_POST['xrabtaga'])){
								$rabtag=$_POST['xrabtaga']."-".$_POST['xrabtagb'];
								}
							$item2 = array(
							   				 'INVNO' => $_SESSION['minvno'],
											 'INVCUST' => $_SESSION['client'],
											 'INVPET' => $_SESSION['patient'],
											 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
											 'INVMAJ' => $_POST['invmaj'],
											 'INVMIN' => $_POST['invmin'],
											 'INVDOC' => $_SESSION['doctor'],
											 'INVSTAFF' => $_SESSION['staff'],
											 'INVDESCR' => "Rabies Tag ".$rabtag,
											 'INVINCM' => $_POST['invincm'],
											 'INVLGSM' => $pettype,
											 'REFCLIN' => $_POST['refclin'],
											 'REFVET' => $_POST['refvet'],
											 'INVFLAGS' => $_POST['invflags'],
											 'INVHYPE' => $_POST['invhype'],
											 'MEMO' => $memo,
											 'INVSERUM' => '2',
											 'INVEST' => $_POST['invest'],
										     'INVSTAT' => $_POST['invstat'],
											 'INVDECLINE' => $_POST['invdecline'],
											 'PETNAME' => $_POST['petname'],
										     'INVNOHST' => $_POST['tnohst'],
											 'INVHXCAT' => $_POST['invhxcat']
											 );
							$_SESSION['invline'][] = $item2;
							}//if (!empty($_POST['xrabtag']))
						}//if ($_POST['invserum']=='1')
///////////  insert the trace logic here.
if ($_POST['invdescr'] != '') {
 foreach ($_SESSION['invline'] as $value2){
  $insertSQL2 = sprintf("INSERT INTO TRACER (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVMIN, INVDOC, INVSTAFF, INVUNITS, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVTAX, 
                      INVVPC) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s', '%s', '%s', '%s', '%s', '%s')",
 							  $value2['INVNO'],
							  $value2['INVCUST'],
							  $value2['INVPET'],
							  $row_invdatetime[0],
							  $value2['INVMAJ'],
							  $value2['INVMIN'],
							  mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
							  mysqli_real_escape_string($mysqli_link, $value2['staff']),
							  $value2['INVUNITS'],
							  mysqli_real_escape_string($mysqli_link, $value2['INVDESCR']),
							  $value2['INVPRICE'],
							  $value2['INVTOT'],
							  $value2['INVREVCAT'],
							  $value2['INVTAX'],
							  $value2['INVVPC']
							  );
				}
  mysqli_query($tryconnection, $insertSQL2);
}
///////////							
			}//if (isset($_POST['ok']))


//CANCEL INVOICE - INSERT INTO REJECTIN
if (isset($_POST['cancel']))
{

$lock_it = "LOCK TABLES INVHOLD WRITE, RECEP WRITE, REJECTIN WRITE, ARCUSTO WRITE" ;  
$Qlock = mysqli_query($tryconnection, $lock_it) or die(mysqli_error($mysqli_link)) ;

$insertSQL="INSERT INTO REJECTIN (REJINV, REJDATE, DATETIME, CUSTNO, PETID, ITOTAL, STAFF, COMPANY) VALUES ($_SESSION[minvno], NOW(), NOW(),'$_SESSION[client]','$_SESSION[patient]','$_POST[itotal]','$_SESSION[staff]','".mysqli_real_escape_string($mysqli_link, $_POST['company'])."')";
mysqli_query($tryconnection, $insertSQL);

//delete from INVHOLD
$deleteSQL = "DELETE FROM INVHOLD WHERE INVCUST='$_SESSION[client]'";
mysqli_query($tryconnection, $deleteSQL);
$optimize = "OPTIMIZE TABLE INVHOLD";
mysqli_query($tryconnection, $optimize);

//DELETE FROM RECEP FILE
$query_discharge="DELETE FROM RECEP WHERE RFPETID='$_SESSION[patient]' LIMIT 1";
$discharge=mysqli_query($tryconnection, $query_discharge) or die(mysqli_error($mysqli_link));
$query_optimize="OPTIMIZE TABLE RECEP ";
$optimize=mysqli_query($tryconnection, $query_optimize) or die(mysqli_error($mysqli_link));

$query_LOCK = "UPDATE ARCUSTO SET LOCKED='0' WHERE CUSTNO = '$client' LIMIT 1";
$LOCK = mysqli_query($tryconnection, $query_LOCK) or die(mysqli_error($mysqli_link));

$unlock_it = "UNLOCK TABLES" ;
$Qunlock = mysqli_query($tryconnection, $unlock_it) or die(mysqli_error($mysqli_link)) ;

//$gobackwin="history.go(-4);";
header("Location:../../CLIENT/CLIENT_PATIENT_FILE.php");
}

///////////////WRITE-IN COMMENT INTO PETHOLD
else if (isset($_POST['finish'])){
	if(substr($_POST['iwrite'],0,3)!='...'){
	$update_PETHOLD="UPDATE PETHOLD SET WRITEIN='$_POST[iwrite]', PHINVNO='$_SESSION[minvno]' WHERE PHPETID='$_POST[patient]'";
	$PETHOLD=mysqli_query($tryconnection, $update_PETHOLD) or die (mysqli_error($mysqli_link));
	}

header("Location:FINISH_INVOICE.php?psex=$_SESSION[psex]");
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title id="title"></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript" src="../../ASSETS/calculation.js"></script>

<script type="text/javascript">
setInterval("self.location='REGULAR_INVOICING.php?product=j&record=k&subcat=i'", 36000000);
document.getElementById('title').innerText=sessionStorage.refID;


function labelx(){
var drug=<?php if (isset($_POST['ok'])){echo "'".mysqli_real_escape_string($mysqli_link, $_POST['invdescr'])."'";} else {echo "document.forms[0].invdescr.value";}?>;
//var labelunits=<?php if (isset($_POST['ok'])){echo "'".$invunits."'";} else {echo "' '";}?>;
var labelunits=<?php echo "'".$invunits."'" ;?> ;
var expdate=<?php if (isset($_POST['ok'])){echo "'".$_POST['expdate']."'";} else {echo "document.forms[0].expdate.value";}?>;
window.open('LABEL.php?pet=<?php echo mysqli_real_escape_string($mysqli_link, $row_PATIENT_CLIENT['PETNAME']).". Client: ".mysqli_real_escape_string($mysqli_link, $row_PATIENT_CLIENT['CONTACT']).' ' .mysqli_real_escape_string($mysqli_link, $row_PATIENT_CLIENT['COMPANY']); ?>&labelunits='+labelunits+'&drug='+drug+'&expdate='+expdate,'_blank','width=500,height=252');
}


function bodyonload(){
var invpreview=document.getElementById('invpreview');
invpreview.scrollTop = invpreview.scrollHeight;

// was at 468
var lookupitem=document.forms[0].invdescr.value.substr(0,6);
if (lookupitem =='Lookup'){
document.getElementById('lookupitem').style.display='display';
}

if(sessionStorage.iwrite){
document.reg_invoicing.iwrite.value=sessionStorage.iwrite;
}
else {
document.reg_invoicing.iwrite.value="...doubleclick to type in an internal message...";
}


document.getElementById('inuse').innerText=localStorage.xdatabase;

<?php

if (substr($row_SELECTEDITEM['TDESCR'],0,6)=="Lookup" && (!isset($_POST['ok']))){
$arinvtype=substr($row_SELECTEDITEM['TDESCR'],7,1);
echo "window.open('INVENTORY_POPUP_SCREEN.php?arinvtype=$arinvtype','_blank','width=745,height=755');";
} 

if ($_POST['xlabel']=="1" && (isset($_POST['ok']))){
echo "labelx();";
} 
?>
//HIGHLIGHT SELECTED ITEMS IN SELECT LISTS
var loc=<?php echo $_GET['subcat']; ?>;
var i=loc-1;
	{
	document.reg_invoicing.category1.options[i].selected="selected";
	}
var loc2=<?php echo $ps; ?>;
	{
	document.getElementById(loc2).selected="selected";
	}




var commtext=document.reg_invoicing.commtext.value;
commtext=commtext.replace('$PETNAME',sessionStorage.petname);
document.reg_invoicing.commtext.value=commtext;


calculateprice(localStorage.ovma, localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>');


<?php if ($row_SELECTEDITEM['TSERUM']=='1') {
echo "document.getElementById('xrabtaga').focus();";
}
else if ($row_SELECTEDITEM['TFEE']=='0.00') {
echo "document.getElementById('invprice').focus();";
echo "document.getElementById('invprice').select();";
}
else if (substr($row_SELECTEDITEM['TDESCR'],0,6)=="Lookup"){
echo "document.getElementById('invunits').focus();";
echo "document.getElementById('invunits').select();";
}
else if ($row_SELECTEDITEM['TGET']=='1') {
echo "document.getElementById('invprice').focus();";
echo "document.getElementById('invprice').select();";
}
?>

}




//LIST PRODUCT SERVICE ON CATEGORY SELECTION
function category()
{
var cat=document.getElementById('category1').value;
self.location='REGULAR_INVOICING.php?subcat=' + cat + '&product=j';
}

//INSERT SELECTED ITEM INTO THE INPUT FIELDS FOR MODIFICATION
function modifyitem()
{
var cat=<?php echo $_GET['subcat']; ?>;
var ps=document.getElementById('prodser').value;
	if (ps==0){ps=document.reg_invoicing.prodser.options[0].value;}
self.location='REGULAR_INVOICING.php?product=' + ps + '&subcat=' + cat;
//&record=k
}
//function bodyonload() {
//}
function bodyonunload() {
}
function setiwrite(){
var iwrite=document.reg_invoicing.iwrite.value;
sessionStorage.setItem('iwrite',iwrite);
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
<div id="LogoHead" onmouseover="document.getElementById(this.id).style.cursor='default';">DVM</div>
<!-- InstanceEndEditable -->

<div id="MenuBar">

	<ul id="navlist">
                
<!--FILE-->                
                
		<li><a href="#" id="current">File</a> 
			<ul id="subnavlist">
                <li><a href="#"><span class="disabled">About DV Manager</span></a></li>
                <li><a onclick=""><span class="disabled">Utilities</span></a></li>
			</ul>
		</li>
                
<!--INVOICE-->                
                
		<li><a href="#" id="current">Invoice</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Casual Sale Invoicing</span></a></li>
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick=""><span class="disabled">Regular Invoicing</span></a><!-- InstanceEndEditable --></li>
                <li><a href="#" onclick=""><span class="disabled">Estimate</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Summary Invoices</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Cash Receipts</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Cancel Invoices</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Comments</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Treatment and Fee File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Worksheet File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Procedure Invoicing File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Invoicing Reports</span></a></li>
			</ul>
		</li>
                
<!--RECEPTION-->                
                
		<li><a href="#" id="current">Reception</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Appointment Scheduling</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Patient Registration</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Using Reception File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Examination Sheets</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Generic Examination Sheets</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Duty Log</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Staff Sign In &amp; Out</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">End of Day Accounting Reports</span></a></li>
                    </ul>
                </li>
                
<!--PATIENT-->                
                
                <li><a href="#" id="current">Patient</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Processing Menu</span></a> </li>
                <li><a href="#" onclick=""><span class="disabled">Review Patient Medical History</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter New Medical History</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter Patient Lab Results</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter Surgical Templates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Create New Client</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Move Patient to a New Client</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Rabies Tags</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Tattoo Numbers</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Certificates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Clinical Logs</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Patient Categorization</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Laboratory Templates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Quick Weight</span></a></li>
<!--                <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
-->			</ul>
		</li>
        
<!--ACCOUNTING-->        
		
        <li><a href="#" id="current">Accounting</a>
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Accounting Reports</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Inventory</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Business Status Report</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Hospital Statistics</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Month End Closing</span></a></li>
			</ul>
		</li>
        
<!--MAILING-->        
		
        <li><a href="#" id="current">Mailing</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Recalls and Searches</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Handouts</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Mailing Log</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Vaccine Efficiency Report</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Referring Clinics and Doctors</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Referral Adjustments</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Labels</span></a></li>
			</ul>
		</li>
	</ul>
</div>
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" -->
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->
<form action="" name="reg_invoicing" method="post">
<!--ARCUSTO-->
<input type="hidden" name="invdisc" value="" />
<input type="hidden" name="invpaydisc" value="<?php echo $row_SELECTEDITEM['INVPAYDISC']; ?>" />
<input type="hidden" name="invpercnt" value="<?php if ($row_PATIENT_CLIENT['DISC']!='0' && $row_SELECTEDITEM['TDISCOUNT']=='1') {echo '1';} else {echo '0';}?>" />
<input type="hidden" name="refclin" value="<?php echo $row_PATIENT_CLIENT['REFCLIN'];?>" />
<input type="hidden" name="refvet" value="<?php echo $row_PATIENT_CLIENT['REFVET'];?>" />
<input type="hidden" name="ptax" value="<?php echo $_SESSION['PTAX'];?>" />
<input type="hidden" name="gtax" value="<?php echo $_SESSION['GTAX'];?>" />
<input type="hidden" name="xdisc" value="<?php echo $row_PATIENT_CLIENT['DISC'];?>" />
<!-- VETCAN (TREATMENTFEEFILE) -->
<input type="hidden" name="invtitem" value="<?php IF (STRISTR($row_SELECTEDITEM['TDESCR'],'Lookup') === FALSE ){echo 0;} else {echo 1;} ?>" />
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
<input type="hidden" name="invnohst" value="<?php echo $row_SELECTEDITEM['TNOHST']; ?>" /> <!-- if 1, item is hst exempt -->
<input type="hidden" name="invstat" value="<?php echo $row_SELECTEDITEM['TSTAT']; ?>" /> <!-- the percentage of professional fee -->
        <!-- PETMAST	-->                
<input  type="hidden"name="petname" value="<?php echo $row_PATIENT_CLIENT['PETNAME'];?>" />			
<!-- PHP/JAVASCRIPT GENERATED -->
<input type="hidden" name="invgst" id="invgst" value="" /> <!-- GST TOTAL-->
<input type="hidden" name="invtax" value="" /> <!-- PST TOTAL-->
<!-- OTHER FOR SESSION[invline] -->
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
<input type="hidden" name="barcode" value="" />

<input type="hidden" name="xseq" value="" />
<input type="hidden" name="abctotal" value="" />
<input type="hidden" name="abccost" value="" /><input type="hidden" name="salmon" value="1" />

<table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
<tr>
<td height="60" colspan="3" valign="top">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
        </span><?php echo $_SESSION['round']; ?></td>
        <td width="22%" rowspan="2" valign="middle" align="center"><span class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>          
        </span>
        </td>
        <td width="19%" colspan="2" rowspan="4" align="center"><table width="100%" border="1" cellspacing="0" cellpadding="0" id="table2">
            <tr>
              <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="15" colspan="2" align="center"><span class="Verdana11B" style="background-color:#FFFF00"><?php echo date('m/d/Y'); ?></span></td>
                  </tr>
                  <tr>
                    <td width="59%" height="13" class="Labels2">&nbsp;Balance</td>
                    <td width="41%" height="13" align="right" class="Labels2">        
					<script type="text/javascript">document.write(sessionStorage.custprevbal);</script></td>
                  </tr>
                  <tr>
                    <td height="13" class="Labels2">&nbsp;Deposit</td>
                    <td height="13" align="right" class="Labels2">
                    <script type="text/javascript">document.write(sessionStorage.custcurbal);</script></td>
                  </tr>
                  <tr>
                    <td height="13" class="Labels2">&nbsp;Last</td>
                    <td height="13 align="right" class="Labels2">
                    <script type="text/javascript">document.write(sessionStorage.custlmonbal);</script></td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="15" align="left" class="Labels2">        
		<script type="text/javascript">document.write(sessionStorage.custphone);</script></td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
        <td height="15" colspan="2" align="left"  class="Labels2"><span class="Verdana12B" style="background-color:#FFFF00">&nbsp;<script type="text/javascript">document.write(sessionStorage.petname);</script>
</span>        <script type="text/javascript">document.write(sessionStorage.desco);</script>
         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">
        <script type="text/javascript">document.write(sessionStorage.desct);</script> (<?php agecalculation($tryconnection,$pdob); ?>)
		</td>
      </tr>
    </table>    
    
</td>
</tr>

<tr>
<td height="" colspan="3" align="center" valign="top">

    <table id="table" width="733" border="1" cellpadding="0" cellspacing="0" >

    <tr bgcolor="#000000">
    <td width="133" height="13" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite" title='Click to select a category'>Category</td>
    <td width="200" height="13" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite" title='Click to select a product/service'>Product/Service</td>
    <td rowspan="2" align="center" valign="middle" bgcolor="#FFFFFF">


        <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">

        <tr>
        <td height="15" colspan="3" align="center" valign="top" class="Verdana11BBlue">MODIFY INVOICE ITEM</td>
        </tr>
        
        <tr>
        <td width="50%" height="10" valign="bottom" class="Verdana11B">
        &nbsp;
    <!--PRODUCT SERVICE-->
        <span id="ps">Product/Service</span>
    <!--DRUG-->
        <span id="drug" style="display:none">Drug</span>        </td>
        <td width="35%" height="10" align="right" valign="bottom" class="Verdana11B">
    <!--QTY-->
    	UPrice&nbsp;&nbsp;
        <span id="spkgs" style="display:none">Pkgs&nbsp;</span>
&nbsp;                
        <span id="qty">Qty</span>
        <span id="dose" style="display:none">Dose</span>
        <!--UNITS-->
        <span id="units" style="display:none" >Units</span>		</td>
        <!--PRICE-->
        <td height="10" align="right" valign="bottom" class="Verdana11B">Price&nbsp;</td>
        </tr>
        
        <tr>
        <td height="10" valign="top" class="Labels2">
        <input name="invdescr" type="text"  id="item" class="Input" size="25" value="<?php echo $row_SELECTEDITEM['TDESCR'];?>" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" <?php if ($row_SELECTEDITEM['TENTER']!='1'){echo "readonly='readonly'";} ?> />
        </td>
        <td height="10" align="right" valign="top"><input type="text" name="invprice" id="invprice" value="<?php  echo number_format($row_SELECTEDITEM['TFEE'], 2,'.','');?>" class="Inputright" size="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')" <?php //if ($row_SELECTEDITEM['TGET']!='1'){echo "readonly='readonly'";} ?>/><input name="pkgs" id="pkgs" type="text" class="Inputright" value="0" size="4" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')" title="number of packages" style="display:none"/><input name="invunits" id="invunits" type="text" class="Inputright" value="1" size="4" onfocus="InputOnFocus(this.id)" onblur="nozeroes;InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')" title="number of units" <?php //if ($row_SELECTEDITEM['TUNITS']!='1'){echo "readonly='readonly'";} ?>/>
        </td>
        <td width="15%" height="10" align="right" valign="top" class="Labels2">
        <input name="invtot" id="invtot" type="text" class="Inputright" size="7" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo number_format($row_SELECTEDITEM['TFEE'], 2,'.','');?>" readonly="readonly"/>         </td>
         </tr>
         
         <tr id="pharmacy1" style="display:none">
         <td height="10" valign="bottom" class="Verdana11B">&nbsp;Dosage</td>
         <td height="10" valign="bottom" class="Labels">&nbsp;</td>
         <td height="10" align="center" valign="bottom" class="Verdana11B">Days</td>
         </tr>
         
         <tr id="pharmacy2" style="display:none">
         <td height="10" colspan="2" valign="middle" class="Labels2">
          <label><input type="radio" name="dosage" id="sid" value="1" onchange="document.reg_invoicing.days.focus(); document.reg_invoicing.days.select(); calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')"/>SID</label>
          <label><input type="radio" name="dosage" id="bid" value="2" onchange="document.reg_invoicing.days.focus(); document.reg_invoicing.days.select(); calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')"/>BID</label>
          <label><input type="radio" name="dosage" id="tid" value="3" onchange="document.reg_invoicing.days.focus();  document.reg_invoicing.days.select(); calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')"/>TID</label>
          <label><input type="radio" name="dosage" id="qid" value="4" onchange="document.reg_invoicing.days.focus();  document.reg_invoicing.days.select(); calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')"/>QID</label>
          <label><input type="radio" name="dosage" id="eod" value="5" onchange="document.reg_invoicing.days.focus();  document.reg_invoicing.days.select(); calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')"/>EOD</label>
          <label><input name="dosage" type="radio" id="other" onchange="document.reg_invoicing.days.focus();  document.reg_invoicing.days.select(); calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')" value="6" checked="checked"/>
          Other</label>         </td>
         <td height="10" align="center" valign="middle" class="Labels2">
         <input name="days" id="days" type="text" class="Inputright" value="1" size="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')"/>         </td>
         </tr>
         
                <?php 
	   if ($row_SELECTEDITEM['TSERUM']=='1'){
	   echo '<tr>
			<td colspan="3" class="Verdana11BRed">
			&nbsp;Serum&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Durn&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name
			</td>
			</tr>';
		$vacc = explode(",",$row_SELECTEDITEM['TVACCS']);
		foreach ($vacc as $value ){
		
		$query_VACCINES = "SELECT * FROM VACCINES WHERE NAME='$value'";
		$VACCINES = mysqli_query($tryconnection, $query_VACCINES) or die(mysqli_error($mysqli_link));
		$row_VACCINES = mysqli_fetch_assoc($VACCINES);
		$isitkitpup = strpos($row_SELECTEDITEM['TTYPE'],' WK ') ;
		$isitlast = strpos($row_SELECTEDITEM['TTYPE'],'16 ') ;
		$isit3yr = strpos($row_SELECTEDITEM['TDESCR'],'3 year') ;
        if ( $isitkitpup === false || $isitlast === 0) {
        $vdur = '1';
        }
        else {
         $vdur = '4' ;
        }
    	if ($isit3yr != 0) {
    	$vdur = '3' ;
    	}
			echo '	<tr>
					<td colspan="3" class="Verdana11">';
			echo '&nbsp;<input type="text" name="sauce[]" id="'.$row_VACCINES['SEQ'].'" value="'.$row_VACCINES['SERIAL'].'" class="Input" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>
			<input type="hidden" name="meat[]" value="'.$value.'" />
			<input type="hidden" name="spice[]"  value="'.$row_VACCINES['VFLAGS'].'" />
			<label><input id="x'.$row_VACCINES['SEQ'].'" name="potatoes[]" type="text" size="1" value="'.$vdur.'" class="Input" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" />'.$value.'</label><br />';
			echo '</td>
				</tr>';
		 }
		}
		?>
		  
         <tr>
         <td height="10" align="left" valign="middle" class="Labels">
         <span style="background-color:#00CC66; font-size:35px; "><input name="ok" type="submit" value="OK" style="width:40px;" class="button" onclick="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')"/></span>         
         <input name="lookupitem" id="lookupitem" type="button" value="LOOK UP" style="display:none" onclick="window.open('INVENTORY_POPUP_SCREEN.php','blank','width=732,height=700')" />
         <input name="inline" type="button" value="IN-LINE" onclick="window.open('IN_LINE.php','_blank','width=700, height=215');"/>
         </td>
         <td height="10"  colspan="2" class="Labels">
         
         <?php if ($row_SELECTEDITEM['TSERUM']=='1' && substr($row_SELECTEDITEM['TFLAGS'],0,1) == '1'){
		 	$q_critdata="SELECT HRABTAG FROM CRITDATA LIMIT 1";
			$critdata=mysqli_query($tryconnection, $q_critdata) or die (mysqli_error($mysqli_link));
			$row_critdata=mysqli_fetch_array($critdata);
		 		if ($row_critdata[0]=='0'){
				echo "<label>&nbsp;Rabies tag&nbsp;<input type='text' class='Input' name='xrabtag' id='xrabtag' size='14' onfocus='InputOnFocus(this.id)' onblur='InputOnBlur(this.id)' /></label>";
				}
				else {
				echo "<label>&nbsp;Rabies tag&nbsp;<input type='text' class='Inputright' name='xrabtaga' id='xrabtaga' size='14' onfocus='InputOnFocus(this.id)' onblur='InputOnBlur(this.id)' style='margin-right:0px;' /><input type='text' size='1' class='Input' value='-' disabled style='margin-left:0px; margin-right:0px;'/><input type='text' class='Input' name='xrabtagb' id='xrabtagb' size='2' onfocus='InputOnFocus(this.id)' onblur='InputOnBlur(this.id)' style='margin-left:0px;' value='".date('y')."'/></label>";
				}
		 	} 
			else {
			echo "<input type='hidden' name='xrabtaga' id='xrabtaga' />";
			}
		 ?>
         
         <label id="fullpkg" style="display:none"><input type="checkbox" name="full" id="full" onclick="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>')" />Full package </label><span id="pkgcount"></span><input id="labelbutton" name="label" type="button" value="LABEL"  style="display:none" onclick="labelx();"/>           
         
         
         
        
         </td>
         </tr>
         </table>
         
    </td>
    </tr>
    
    <tr>
    <td width="133" height="350" rowspan="2" align="center" valign="top">
	   <!--LIST CATEGORIES-->                
		<?php categ($tryconnection,$pettype); ?>    </td>
    <td width="200" height="350" rowspan="2" align="center" valign="top">
		<!--LIST PRODUCT SERVICE-->                
		 <?php subcateg($tryconnection,$cat, $pettype); ?>    </td>
    </tr>
    
    <tr>
    <td rowspan="2" align="left" valign="top">
        
         
         <table width="384" border="0" cellspacing="0" cellpadding="0" >
     
         <tr>
         <td colspan="6" height="15" align="center" valign="top" bgcolor="#FFFFFF">
         <?php 
		 if (isset($_SESSION['morethan1']) || isset($_SESSION['round'])){
		 echo "&nbsp;<span class='Verdana12BRed' title='This invoice contains more than 1 patient'>&bull;&nbsp;</span>";
		 }
		 
		 if ($_SESSION['minvno']=='0') {echo "<span class='Verdana11BBlue'>ESTIMATE</span>";} else {echo "<span class='Verdana11B'>INVOICE #$_SESSION[minvno]</span>";} 
		 ?>
         </td>
         </tr>
        <tr class="Verdana9">
            <td width="31" align="right">Qty&nbsp;</td>
            <td height="20" width="160" >Product/Service</td>
            <td align="right" width="55">Uprice&nbsp;</td>
            <td align="right" width="55">Price&nbsp;</td>
            <td width="40" align="right">DispF</td>
            <td align="center">&nbsp;Disc</td>
        </tr>
        
         <tr>
         <td colspan="6">
         
         <div id="invpreview" style="width:384px;max-height:<?php if ($row_SELECTEDITEM['TSERUM']=='1'){echo "180";} else {echo "270";} ?>px;overflow:auto;">
            
            <table width="384" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30"></td>
                <td width="165" height="0"></td>
                <td width="55"></td>
				<td width="55"></td>
                <td width="40"></td>
                <td></td>
              </tr>
  <input type="hidden" name="quantity" value=""  />
			  <?php 
				
	
				
				//IF NO ITEM SELECTED YET, DISPLAY NOTHING		  
				if (!isset($_SESSION['invline'])) {
					print "";
				}

		//DISPLAY INVOICE ITEMS
                else if (isset($_SESSION['invline'])) {
						
						foreach ($_SESSION['invline'] as $key => $value) {
						
if ($value['INVPET']==$_SESSION['patient']){						
						//if the value is an invline note
						if ($value['MEMO']=='1' || $value['INVSERUM']=='2'){
						echo ' <tr class="Verdana9">
								<td height="15"></td>
								<td colspan="4">*'.$value['INVDESCR'].'</td>
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
						
//							if ($value['INVUNITS']=='0.00' || $value['INVUNITS']=='0'){
//							echo  "&nbsp;";
//							}
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
						echo '</td><td align="right" class="Verdana11 style2">&nbsp;';
							
						if ($value['INVPERCNT']=='1'){
							if ($value['XDISC']=='0'){echo "&nbsp;";}
							else {echo $value['XDISC']/*."%"*/;}
							}
							
						echo '</td></tr>';
						
						}
				   
				   }

}				    
                }
                
				
                ?>
              
            </table>
            
        </div>
            
          </td>
          </tr>
              <tr>
                <td height="2"></td>
                <td bgcolor="#CCCCCC"></td>
                <td align="right" bgcolor="#CCCCCC"></td>
				<td bgcolor="#CCCCCC"></td>
                <td></td>
                <td ></td>
              </tr>
   
              <!-- here is where the discount should be shown. -->
              <tr<?php if (array_sum(round($_SESSION['invline']['INVDISC'],2)) == 0) {echo 'class="hidden">' ;} else {echo '>' ;}?>
                <td height="15" align="right" class="Verdana11">&nbsp;</td>
                <td height="15" align="left" class="Verdana11">Discount</td>
                <td height="15" align="right" class="Verdana11"></td>
                <td height="15" align="right" class="Verdana11">
                <script type="application/javascript">
					//CALCULATE THE TOTAL DISCOUNT
					var discount = -<?php $INVtotal = array();
						//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                		          foreach ($_SESSION['invline'] as $invtot)
								  {
										if(($invtot['INVEST']!='1' || $_SESSION['minvno']=='0') && $invtot['INVDECLINE']!='1' && $invtot['PETNAME']==$_SESSION['petname'] ){
										$INVtotal[]=round($invtot['INVDISC'],2);
										}
								  }
								  //SUM UP THE INDIVIDUAL DISCOUNTS
                                  echo array_sum($INVtotal);
                                 ?> ;
					//CONVERT THE PRICE INTO TWO DECIMAL POINTS
			 		var discconv = discount.toFixed(2);
					//DISPLAY THE RESULT
					document.write(discconv);
					document.forms[0].invdisc.value=discconv;
                
</script> 
                </td>
              </tr>           
              <tr class="hidden">
                <td height="15" align="right" class="Verdana11">&nbsp;</td>
                <td height="15" class="Verdana12">Subtotal</td>
                <td align="right" class="Verdana11"></td>
                <td height="15" align="right" class="Verdana12">
				<input type="hidden" name="subtotal" value=""  />
<script type="application/javascript">
					//CALCULATE THE TOTAL PRICE INCLUDING GST's
					var price =<?php $INVtotal = array();
						//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                		          foreach ($_SESSION['invline'] as $invtot)
								  {
										if(($invtot['INVEST']!='1' || $_SESSION['minvno']=='0') && $invtot['INVDECLINE']!='1' && $invtot['PETNAME']==$_SESSION['petname'] ){
										$INVtotal[]=round($invtot['INVTOT'],2);
										}
								  }
								  //SUM UP THE INDIVIDUAL PRICES
                                  echo array_sum($INVtotal);
                                 ?> ;
					//CONVERT THE PRICE INTO TWO DECIMAL POINTS
			 		var priceconv = price.toFixed(2);
					//DISPLAY THE RESULT
					document.write(priceconv);
					document.forms[0].subtotal.value=priceconv;
                
</script>                
				</td>
                <td height="15"></td>
                <td height="15"></td>
              </tr>
              <tr <?php if ($row_PATIENT_CLIENT['GTAX']=='1'){echo "style='display:none;'";} ?>>
                <td height="15" align="right" class="Verdana11">&nbsp;</td>
                <td height="15" class="Verdana12"><?php echo $nametax; ?> <span class="Verdana9">(<?php echo $taxnumber; ?>)</span></td>
                <td align="right" class="Verdana11"></td>
                <td height="15" align="right" class="Verdana12">
				<input type="hidden" name="xgst" value=""  />
				<script type="application/javascript">
					//CALCULATE THE GST TOTAL OF INVOICE ITEMS
					var GST = <?php $GSTtotal = array();
							//TAKE CALCULATED GST's FROM EACH INVOICE ITEM AND INSERT THEM INTO ARRAY 
									foreach ($_SESSION['invline'] as $GSTitem)
									{
										if(($GSTitem['INVEST']!='1' || $_SESSION['minvno']=='0') && $GSTitem['INVDECLINE']!='1'){
										$GSTtotal[]=round($GSTitem['INVGST'],2);
										}
									}
							//SUM UP THE VALUES IN ARRAY & DISPLAY
									echo array_sum($GSTtotal);
								   ?>;
												
					//CONVERT THE DISPLAYED VALUE INTO TWO DECIMAL POINT NUMBER
					var GSTconv = GST.toFixed(2);
					//DISPLAY GST VALUE IN INVOICE PREVIEW
					document.write(GSTconv);
					document.forms[0].xgst.value=GSTconv;
                
                </script>                </td>
                <td height="15"></td>
                <td height="15"></td>
              </tr>
              <tr <?php if ($row_PATIENT_CLIENT['PTAX']=='1'){echo "style='display:none;'";} ?>>
                <td height="15" align="right" class="Verdana11"></td>
                <td height="15" class="Verdana12">PST</td>
                <td align="right" class="Verdana11"></td>
                <td height="15" align="right" class="Verdana12">
				<input type="hidden" name="xpst" value=""  />
				<script type="application/javascript">
					//CALCULATE THE PST TOTAL OF INVOICE ITEMS
					var PST =<?php $PSTtotal = array();
							//TAKE CALCULATED PST's FROM EACH INVOICE ITEM AND INSERT THEM INTO ARRAY 
									foreach ($_SESSION['invline'] as $PSTitem)
									{
										if(($PSTitem['INVEST']!='1' || $_SESSION['minvno']=='0') && $PSTitem['INVDECLINE']!='1'){
										$PSTtotal[]=round($PSTitem['INVTAX'],2);
										}
									}
							//SUM UP THE VALUES IN ARRAY & DISPLAY
									echo array_sum($PSTtotal);
								   ?>;
												
					//CONVERT THE DISPLAYED VALUE INTO TWO DECIMAL POINT NUMBER
					var PSTconv = PST.toFixed(2);
					//DISPLAY GST VALUE IN INVOICE PREVIEW
					document.write(PSTconv);
					document.forms[0].xpst.value=PSTconv;
                </script>                </td>
                <td height="15"></td>
                <td height="15"></td>
              </tr>
              
              <tr title="Estimated items (in blue) excluded">
                <td height="15" align="right" class="Verdana11"></td>
                <td height="15" class="Verdana12"><strong>TOTAL</strong></td>
				<td colspan="2" height="15" align="right" class="Verdana12">
                <input type="hidden" name="xtotal" value=""  />
                <strong>
				
				<script type="application/javascript">
					//CALCULATE THE TOTAL PRICE INCLUDING GST's
					var price =<?php $INVtotal = array();
									   $INVdiscount=array();
						//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                		          foreach ($_SESSION['invline'] as $invtot)
								  {
									if(($invtot['INVEST']!='1' || $_SESSION['minvno']=='0') && $invtot['INVDECLINE']!='1'){
									$INVtotal[]=round($invtot['INVTOT'],2);
									$INVdiscount[]=round($invtot['INVDISC'],2);
									}
                                  }
								  //SUM UP THE INDIVIDUAL PRICES
                                echo (array_sum($INVtotal) + array_sum($GSTtotal) + array_sum($PSTtotal) - array_sum($INVdiscount));
                                 ?>;
					//CONVERT THE PRICE INTO TWO DECIMAL POINTS
			 		var priceconv = price.toFixed(2);
					//DISPLAY THE RESULT
					document.write(priceconv);
					document.forms[0].xtotal.value=priceconv;
                
                </script>
                </strong>                
                <!-- INVOICE TOTAL -->                </td>
                <td height="15"></td>
                <td height="15"></td>
              </tr>
              
              <tr>
                <td height="3" align="right"></td>
                <td height="3" bgcolor="#666666" class="Verdana12"></td>
                <td align="right" bgcolor="#666666"></td>
                <td height="3" align="right" bgcolor="#666666" class="Verdana12"></td>
                <td ></td>
                <td></td>
              </tr>
</table>
        
        
         
          
    </td>
    </tr>
    
    <tr>
      <td height="83" colspan="2" align="center" valign="middle">
      <textarea name="iwrite" cols="45" rows="4" class="commentarea" id="iwrite" ondblclick="document.reg_invoicing.iwrite.value='';" onkeyup="setiwrite();" ></textarea>
      </td>
      </tr>
          
   </table>

</td>
</tr>

<tr>
<td height="26" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF" class="ButtonsTable">
	<input name="finish" class="button" type="submit" value="FINISH" />  
    <input name="edit" class="button" type="button" value="EDIT" onclick="window.open('EDIT_INVOICE.php?reference=0','_blank','width=733,height=465')" />
    <input name="procedure" class="button" type="button" value="PROCED." onclick="window.open('PROCEDURE_POP_UP.php?pettype=<?php echo $pettype; ?>','_blank','width=600, height=545');" />
    <input type="button" class="button" name="history" value="HISTORY" onclick="window.open('../../PATIENT/HISTORY/REVIEW_HISTORY.php?path=2close','_blank','status=no, width=785, height=670')"/>
    <input name="preview" class="button" type="button" value="PREVIEW" onclick="window.open('../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW.php?preview=PREVIEW','_blank',' width=855, height=750')" />
    <input name="cancel2" class="hidden" type="button" value="CANCEL" onclick="confirmation()" />
    
    
</td>
</tr>

</table>

</form>

<form action="" name="cancelinvoice" method="post">
<input type="hidden" name="cancel" value="1" />
<input type="hidden" name="itotal" value="<?php $INVtotal = array();foreach ($_SESSION['invline'] as $invtot){$INVtotal[]=$invtot['INVTOT'];}echo array_sum($INVtotal);?>" /> 
<!-- OTHER FOR REJECTED INVOICE -->
<input type="hidden" name="rejdate" value="<?php echo date("Y/m/d"); ?>"/>
<input type="hidden" name="company" value="<?php echo $row_PATIENT_CLIENT['TITLE'].' '.$row_PATIENT_CLIENT['CONTACT'].' '.mysqli_real_escape_string($mysqli_link, $row_PATIENT_CLIENT['COMPANY']); ?>"/>
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>