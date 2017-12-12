<?php 
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

if (isset($_POST['cancel']))
{
$insertSQL="INSERT INTO REJECTIN (REJINV, REJDATE, DATETIME, CUSTNO, PETID, ITOTAL, csstaff, COMPANY) VALUES ($_SESSION[minvno], NOW(), NOW(),'0','0','$_POST[itotal]','$_SESSION[csstaff]','$_POST[company]')";
mysql_query($insertSQL, $tryconnection);

header("Location:../../INDEX.php");
}



//DELETE ITEM
if (isset($_GET['ref'])){
$keyunset=$_GET['ref'];
unset($_SESSION['casual'][$keyunset]);
$_SESSION['casual']=array_merge($_SESSION['casual']);
header("Location:CASUAL_SALE.php");
}



if (isset($_GET['reference'])){
$keyupdate=$_GET['reference'];
}


$_SESSION['GTAX']=0;
$_SESSION['PTAX']=0;

if (isset($_POST['ok']) && isset($_GET['reference'])){

	if(!empty($_SESSION['casual'][0]['INVDESCR'])){
		$keyupdate=$_GET['reference'];
		$_SESSION['casual'][$keyupdate]['INVDATETIME'] = $_SESSION['minvdte'].' '.date('H:s:i');
		$_SESSION['casual'][$keyupdate]['INVDOC'] = $_POST['invdoc'];
		$_SESSION['casual'][$keyupdate]['INVUNITS'] = $_POST['invunits'];
		$_SESSION['casual'][$keyupdate]['INVDESCR'] = $_POST['invdescr'];
		$_SESSION['casual'][$keyupdate]['INVPRICE'] = $_POST['invprice'];
		$_SESSION['casual'][$keyupdate]['INVTOT'] = round($_POST['invtot'],2);
		$_SESSION['casual'][$keyupdate]['INVDISC'] = $_POST['invdisc'];
		$_SESSION['casual'][$keyupdate]['INVGST'] = round($_POST['invgst'],2);
		$_SESSION['casual'][$keyupdate]['INVTAX'] = round($_POST['invtax'],2);
		$_SESSION['casual'][$keyupdate]['INVDISP'] = number_format($_POST['invdisp'],2,'.','');
		$_SESSION['casual'][$keyupdate]['AUTOCOMM'] = $_POST['tautocomm'];
		$_SESSION['casual'][$keyupdate]['INVCOMM'] = !empty($_POST['invcomm']) ? "1" : "0";
		$_SESSION['casual'][$keyupdate]['HISTCOMM'] = !empty($_POST['histcomm']) ? "1" : "0";
		
	}

		
}//if (isset($_POST['ok']) && isset($_GET['reference']))



$ref='Lookup Food';

$query_SELECTEDITEM = "SELECT * FROM VETCAN WHERE TDESCR = '$ref' LIMIT 1";
$SELECTEDITEM = mysql_query($query_SELECTEDITEM, $tryconnection) or die(mysql_error());
$row_SELECTEDITEM = mysql_fetch_assoc($SELECTEDITEM);


//INSERT THE SELECTED AND MODIFIED ITEM INTO ITEM LIST
                if (isset($_POST['ok'])) {
						   $invoicecomment=$_POST['commtext'];
							$autcomm=$_POST['autocomm'];
$query_TAUTOCOMM = "SELECT * FROM ARSYSCOMM WHERE COMMCODE='$autcomm'";
$TAUTOCOMM = mysql_query($query_TAUTOCOMM, $tryconnection) or die(mysql_error());
$row_TAUTOCOMM = mysql_fetch_assoc($TAUTOCOMM);
							$invoicecomment=str_replace('$PETNAME', $_SESSION['petname'], $row_TAUTOCOMM['COMMENT']);

						    $invunits=$_POST['invunits'];
						   
						   //CREATE AN ARRAY FROM ENTIRE RECORD FROM VETCAN FOR SELECTED ITEM 			
						   $item = array('INVNO' => $_SESSION['csminvno'],
						   				 'INVCUST' => $_SESSION['client'],
										 'INVPET' => $_SESSION['patient'],
										 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
										 'INVMAJ' => $_POST['invmaj'],
										 'INVMIN' => $_POST['invmin'],
										 'INVDOC' => $_SESSION['csdoctor'],
										 'INVSTAFF' => $_SESSION['csstaff'],
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
										 'IRADLOG' => $_POST['iradlog'],
										 'ISURGLOG' => $_POST['isurlog'],
										 'INARCLOG' => $_POST['inarclog'],
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
										 'TENTER' => $_POST['tenter'],
										 'LCODE' => $lcode,
										 'LCOMMENT' => $lcomment,
										 'INVPAYDISC' => $_POST['invpaydisc'],
										 'INVHXCAT' => $_POST['invhxcat']
										 );
						$_SESSION['casual'][] = $item;
						
						//inline note
						   if (!empty($_POST['inlinenote'])){
						   $item1 = array(
						   				 'INVNO' => $_SESSION['csminvno'],
						   				 'INVCUST' => $_SESSION['client'],
										 'INVPET' => $_SESSION['patient'],
										 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
										 'INVMAJ' => $_POST['invmaj'],
										 'INVMIN' => $_POST['invmin'],
										 'INVDOC' => $_SESSION['csdoctor'],
										 'INVSTAFF' => $_SESSION['csstaff'],
										 'INVDESCR' => $_POST['inlinenote'],
										 'INVINCM' => $_POST['invincm'],
										 'INVLGSM' => $pettype,
										 'INVHYPE' => $_POST['invhype'],
										 'MEMO' => '1',
										 'INVEST' => $_POST['invest'],
										 'INVDECLINE' => $_POST['invdecline'],
										 'PETNAME' => $_POST['petname'],
										 'INVHXCAT' => $_POST['invhxcat']
			 						 );
						$_SESSION['casual'][] = $item1;
							}
							
						//serums	
							if ($_POST['invserum']=='1'){
							$meatnsauce=array();
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
								}
							
							foreach ($meatnsauce as $mns){
							   $item2 = array(
							   				 'INVNO' => $_SESSION['csminvno'],
											 'INVCUST' => $_SESSION['client'],
											 'INVPET' => $_SESSION['patient'],
											 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
											 'INVMAJ' => $_POST['invmaj'],
											 'INVMIN' => $_POST['invmin'],
											 'INVDOC' => $_SESSION['csdoctor'],
											 'INVSTAFF' => $_SESSION['csstaff'],
											 'INVDESCR' => $mns,
											 'INVINCM' => $_POST['invincm'],
											 'INVLGSM' => $pettype,
											 'REFCLIN' => $_POST['refclin'],
											 'REFVET' => $_POST['refvet'],
											 'INVFLAGS' => $_POST['invflags'],
											 'INVHYPE' => $_POST['invhype'],
											 'MEMO' => $memo,
											 'INVSERUM' => '2',
											 'INVEST' => $_POST['invest'],
											 'INVDECLINE' => $_POST['invdecline'],
											 'PETNAME' => $_POST['petname'],
											 'INVHXCAT' => $_POST['invhxcat']
											 );
							$_SESSION['casual'][] = $item2;
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
							   				 'INVNO' => $_SESSION['csminvno'],
											 'INVCUST' => $_SESSION['client'],
											 'INVPET' => $_SESSION['patient'],
											 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
											 'INVMAJ' => $_POST['invmaj'],
											 'INVMIN' => $_POST['invmin'],
											 'INVDOC' => $_SESSION['csdoctor'],
											 'INVSTAFF' => $_SESSION['csstaff'],
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
											 'INVDECLINE' => $_POST['invdecline'],
											 'PETNAME' => $_POST['petname'],
											 'INVHXCAT' => $_POST['invhxcat']
											 );
							$_SESSION['casual'][] = $item2;
							}//if (!empty($_POST['xrabtag']))
						}//if ($_POST['invserum']=='1')
							
							
			}//if (isset($_POST['ok']))


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CASUAL SALE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" src="../../ASSETS/calculation.js"></script>

<script type="text/javascript">
function bodyonload()
{
<?php 
if (!isset($_SESSION['csminvno'])){
echo "window.open('CASUAL_SALE_STAFF.php','_blank','width=500, height=340');";
}
?>
document.reg_invoicing.invprice.focus();
document.getElementById('inuse').innerText=localStorage.xdatabase;
//calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>');
calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>');

}


function modifyinvoice(x)
{
self.location="CASUAL_SALE?reference=" + x;
}

function deletion(x)
{
self.location="CASUAL_SALE?ref=" + x + "&reference=0";
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
</script>

<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<!-- InstanceEndEditable -->

<!-- InstanceBeginEditable name="HOME" -->
<div id="LogoHead" onclick="confirmation()" onmouseover="CursorToPointer(this.id)" title="Home">DVM</div>
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


<form action="" name="reg_invoicing" method="post">

<!--ARCUSTO-->
<input type="hidden" name="invdisc" value="" />
<input type="hidden" name="invpaydisc" value="<?php echo $row_SELECTEDITEM['INVPAYDISC']; ?>" />
<input type="hidden" name="invpercnt" value="0" />
<input type="hidden" name="refclin" value=" " />
<input type="hidden" name="refvet" value=" " />
<input type="hidden" name="ptax" value="0" />
<input type="hidden" name="gtax" value="0" />
<input type="hidden" name="xdisc" value="0" />
<!-- VETCAN (TREATMENTFEEFILE) -->
<input type="hidden" name="invmaj" value="<?php echo $row_SELECTEDITEM['TCATGRY']; ?>" />
<input type="hidden" name="invmin" value="<?php echo $row_SELECTEDITEM['TNO']; ?>" />
<input type="hidden" name="invincm" value="<?php echo $row_SELECTEDITEM['TINCMAST']; ?>" />
<input type="hidden" name="invrevcat" value="<?php echo $row_SELECTEDITEM['TREVCAT']; ?>" />
<input type="hidden" name="invflags" value="<?php echo $row_SELECTEDITEM['TFLAGS']; ?>" />
<input type="hidden" name="invdisp" value="0" />
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
        <!-- PETMAST	-->                
<input  type="hidden"name="petname" value="Casual Sale " />		
<!-- PHP/JAVASCRIPT GENERATED -->
<input type="hidden" name="invgst" id="invgst" value="" /> <!-- GST TOTAL-->
<input type="hidden" name="invtax" value="" /> <!-- PST TOTAL-->		
<!-- OTHER FOR SESSION[casual] -->
<input type="hidden" name="inlinenote" value=""  />
<input type="hidden" name="invhype" value="" />
<input type="hidden" name="invest" value="0" />
<input type="hidden" name="invdecline" value="0" />
<!-- OTHER FOR REJECTED INVOICE -->
<input type="hidden" name="rejdate" value="<?php echo date("Y/m/d"); ?>"/>
<input type="hidden" name="company" value="CASUAL SALE"/>

<!-- OTHER FOR CALCULATIONS -->
<input type="hidden" name="invuprice" value="0" />
<input type="hidden" name="pkgprice" value="0" />
<input type="hidden" name="pkgqty" value="" />
<input type="hidden" name="markup" value="" />
<input type="hidden" name="xlabel" value="0" />
<input type="hidden" name="dfyes" value="" />
<input type="hidden" name="result" value="" />
<input type="hidden" name="bulk" value="" />
<input type="hidden" name="dispfee" value="" />
<input type="hidden" name="bdispfee" value="" />
<input type="hidden" name="expdate" value="" />
<input type="hidden" name="xtype" value="" />
<input type="hidden" name="invtitem" value="1" />
                             
<input type="hidden" name="salmon" value="1" />
<input type="hidden" name="quantity" value=""  />


<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="57" align="center" valign="bottom" class="Verdana12B">
    CASUAL SALE
    </td>
  </tr>
  <tr>
    <td height="33" align="center" valign="bottom" class="Verdana12B"><input name="button2" type="button" id="button2" value="LOOK UP" onclick="window.open('../../INVOICE/INVOICING/INVENTORY_POPUP_SCREEN.php','blank','width=732,height=500')" style="width:80px;"/>&nbsp;&nbsp;
      <input name="button4" type="button" id="button4" value="CLEAR" onclick="document.location='CASUAL_SALE.php'" style="width:80px;"/>&nbsp;&nbsp;
      <input name="button4" type="button" id="button4" value="STAFF" onclick="document.location='CASUAL_SALE.php'" style="width:80px;<?php if (isset($_SESSION['csstaff'])){echo " display:none;'";} ?>" /></td>
  </tr>
  <tr>
    <td height="12" align="center" valign="middle">
    
    <table width="367" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" frame="box" rules="none" class="hidden">
      <tr>
        <td width="144" height="40" align="right" valign="middle" class="Verdana12">Inventory Code: </td>
        <td width="217" height="40" valign="middle" class="Verdana12">&nbsp;&nbsp;
          <input type="text" name="invnarc" value="" style="font-size:12px; border:none;" readonly="readonly" /></td>
      </tr>
      <tr>
        <td height="30" align="right" class="Verdana12">Vendor Code: </td>
        <td height="30" class="Verdana12">&nbsp;&nbsp;
          <input type="text" name="invvpc" value="" style="font-size:12px; border:none;" readonly="readonly" /></td>
      </tr>
      <tr>
        <td height="40" align="right" valign="middle" class="Verdana12">Shelf Location: </td>
        <td height="40" valign="middle" class="Verdana12">&nbsp;&nbsp;
          <input type="text" name="xseq" value="" style="font-size:12px; border:none;" readonly="readonly" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="98" align="center" valign="top">
    
    <table width="80%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" frame="box" rules="none">

<tr>
<td height="100" align="center" valign="top">

        <table width="70%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="20" colspan="3" valign="bottom" class="Verdana11B">&nbsp;</td>
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
        <span id="spkgs" style="display:">Pkgs&nbsp;</span>
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
        <input name="invdescr" type="text"  id="item" class="Input" size="25" value="<?php echo $_SESSION['casual'][$keyupdate]['INVDESCR']; ?>" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>        </td>
        <td height="10" align="right" valign="top"><input type="text" name="invprice" id="invprice" value="<?php  echo number_format($_SESSION['casual'][$keyupdate]['INVPRICE'], 2,'.','');?>" class="Inputright" size="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')" /><input name="pkgs" id="pkgs" type="text" class="Inputright" value="0" size="4" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')" title="number of packages" style="display:"/><input name="invunits" id="invunits" type="text" class="Inputright" value="1<?php //echo $_SESSION['casual'][$keyupdate]['INVUNITS']; ?>" size="4" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')" title="number of units" />        </td>
        <td width="15%" height="10" align="right" valign="top" class="Labels2">
        <input name="invtot" id="invtot" type="text" class="Inputright" size="7" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php $tfee=number_format($_SESSION['casual'][$keyupdate]['INVTOT'], 2,'.',''); echo $tfee;?>"/>         </td>
         </tr>
         
         <tr id="pharmacy1" style="display:none">
         <td height="10" valign="bottom" class="hidden">&nbsp;Dosage</td>
         <td height="10" valign="bottom" class="hidden">&nbsp;</td>
         <td height="10" align="center" valign="bottom" class="hidden">Days</td>
         </tr>
         
         <tr id="pharmacy2" style="display:none">
         <td height="10" colspan="2" valign="middle" class="hidden">
          <label><input type="radio" name="dosage" id="sid" value="1" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>SID</label>
          <label><input type="radio" name="dosage" id="bid" value="2" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>BID</label>
          <label><input type="radio" name="dosage" id="tid" value="3" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>TID</label>
          <label><input type="radio" name="dosage" id="qid" value="4" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>QID</label>
          <label><input type="radio" name="dosage" id="other" value="5" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>Other</label>         </td>
         <td height="10" align="center" valign="middle" class="hidden">
         <input name="days" id="days" type="text" class="Inputright" value="7" size="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>         </td>
         </tr>		  
         <tr>
         <td height="10" class="Verdana11">
         <label id="fullpkg" style="display:none"><input type="checkbox" name="full" id="full" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')" />Full package </label><span id="pkgcount"></span><input id="labelbutton" name="label" type="button" value="LABEL"  style="display:none" onclick="labelx();"/>         </td>
         <td height="10" colspan="2" align="right" class="Verdana11">
         <span style="display:none;">
         <input name="lookupitem" id="lookupitem" type="button" value="LOOK UP" style="display:none" onclick="window.open('INVENTORY_POPUP_SCREEN.php','blank','width=732,height=500')" />
         <input name="inline" type="button" value="IN-LINE" onclick="window.open('IN_LINE.php','_blank','width=500, height=215');"/></span>         
         <input name="ok" type="submit" value="OK" style="width:80px;"/>         </td>
         </tr>
         <tr>
           <td height="40" colspan="3" align="right" class="hidden">
           <?php taxname($database_tryconnection, $tryconnection, date("m/d/Y")); ?>: 
             <input name="invgst" type="text" id="invgst" style="font-size:12px; border:none; text-align:right;" value="" size="10" readonly="readonly" /><br  />
           PST: <input name="invtax" type="text" style="font-size:12px; border:none; text-align:right;" value="" size="10" readonly="readonly" /><br  /><hr  />
           Total invoice cost for items: <input name="abctotal" type="text" style="font-size:12px; border:none; text-align:right; font-weight:bold" value="" size="10" readonly="readonly" />           </td>
           </tr>
         <tr>
           <td height="10" colspan="3" class="hidden"></td>
         </tr>
         <tr>
           <td height="10" colspan="3" class="hidden">&nbsp;</td>
         </tr>
         </table>    </td>
</tr>
</table></td>
  </tr>
  <tr>
    <td height="14" align="center" valign="top" class="Verdana11"><?php echo $_SESSION['csstaff']; ?></td>
  </tr>
  <tr>
    <td height="300" align="center" valign="top"><table width="344" border="0" cellspacing="0" cellpadding="0" >
      <tr>
        <td colspan="6" width="344" height="15" align="center" valign="top" bgcolor="#FFFFFF"><?php 
		 if (isset($_SESSION['morethan1']) || isset($_SESSION['round'])){
		 echo "&nbsp;<span class='Verdana12BRed' title='This invoice contains more than 1 patient'>&bull;&nbsp;</span>";
		 }
		 
		 if ($_SESSION['csminvno']=='0') {echo "<span class='Verdana11BBlue'>ESTIMATE</span>";} else {echo "<span class='Verdana11B'>INVOICE #$_SESSION[csminvno]</span>";} 
		 ?>        </td>
      </tr>
      <tr class="Verdana9">
        <td width="31" align="right">Qty&nbsp;</td>
        <td height="20" width="176" >Product/Service</td>
        <td align="right" width="30">Uprice&nbsp;</td>
        <td align="right" width="31">Price&nbsp;</td>
        <td width="45" align="right">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="6"><div id="invpreview2" style="width:384px;max-height:<?php if ($row_SELECTEDITEM['TSERUM']=='1'){echo "180";} else {echo "270";} ?>px;overflow:auto;">
            <table width="344" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="31"></td>
                <td width="176" height="0"></td>
                <td width="30"></td>
                <td width="50"></td>
                <td width="45"></td>
                <td></td>
              </tr>
              <input type="hidden" name="quantity2" value=""  />
              <?php 
				
	
				
				//IF NO ITEM SELECTED YET, DISPLAY NOTHING		  
				if (!isset($_SESSION['casual'])) {
					print "";
				}

		//DISPLAY INVOICE ITEMS
                else if (isset($_SESSION['casual'])) {
						
						foreach ($_SESSION['casual'] as $key => $value) {
						
if ($value['INVPET']==$_SESSION['patient']){						
						//if the value is an casual note
						if ($value['MEMO']=='1' || $value['INVSERUM']=='2'){
						echo ' <tr class="Verdana9" >
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
						if ($value['INVEST']=='1' && $_SESSION['csminvno']!='0'){echo 'Verdana11Blue'; } else {echo 'Verdana11';}
						echo '" align="right">';
						
//							if ($value['INVUNITS']=='0.00' || $value['INVUNITS']=='0'){
//							echo  "&nbsp;";
//							}
							if (intval($value['INVUNITS'])==floatval($value['INVUNITS'])){
							echo  number_format($value['INVUNITS'],0);
							}
							else {
							echo $value['INVUNITS'];
							}
						
						echo '&nbsp;</td><td height="15" class="';
						if ($value['INVEST']=='1' && $_SESSION['csminvno']!='0'){echo 'Verdana11Blue'; } else {echo 'Verdana11';}
						echo '">'.substr($value['INVDESCR'],0,24).'</td><td align="right" class="Verdana11 style2">'.number_format($value['INVPRICE'],2,'.','').'</td><td height="15" align="right" class="Verdana11">'.number_format($value['INVTOT'],2,'.','').'</td><td align="right" class="Verdana11Blue style3">';
						if ($value['INVDISP']=='0.00'){echo " ";} else {echo $value['INVDISP'];}
						echo '</td><td id="'.$key.'" align="right" class="Verdana12BRed" onclick="deletion('.$key.')" onmouseover="CursorToPointer(this.id)" title="Remove this item">&nbsp;&nbsp;X</td></tr>';
						
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
        <td height="15" class="Verdana12">Subtotal</td>
        <td width="30" align="right" class="Verdana11"></td>
        <td height="15" align="right" class="Verdana12"><input type="hidden" name="subtotal" value=""  />
            <script type="application/javascript">
					//CALCULATE THE TOTAL PRICE INCLUDING GST's
					var price =<?php $INVtotal = array();
						//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                		          foreach ($_SESSION['casual'] as $invtot)
								  {
										if(($invtot['INVEST']!='1' || $_SESSION['csminvno']=='0') && $invtot['INVDECLINE']!='1' && $invtot['PETNAME']==$_SESSION['petname'] ){
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
                
      </script>        </td>
        <td width="35" height="15"></td>
        <td height="15"></td>
      </tr>
      <tr >
        <td height="15" align="right" class="Verdana11">&nbsp;</td>
        <td height="15" class="Verdana12"><?php echo $nametax; ?> <span class="Verdana9">(<?php echo $taxnumber; ?>)</span></td>
        <td width="30" align="right" class="Verdana11"></td>
        <td height="15" align="right" class="Verdana12"><input type="hidden" name="xgst" value=""  />
            <script type="application/javascript">
					//CALCULATE THE GST TOTAL OF INVOICE ITEMS
					var GST = <?php $GSTtotal = array();
							//TAKE CALCULATED GST's FROM EACH INVOICE ITEM AND INSERT THEM INTO ARRAY 
									foreach ($_SESSION['casual'] as $GSTitem)
									{
										$GSTtotal[]=round($GSTitem['INVGST'],2);
									}
							//SUM UP THE VALUES IN ARRAY & DISPLAY
									echo array_sum($GSTtotal);
								   ?>;
												
					//CONVERT THE DISPLAYED VALUE INTO TWO DECIMAL POINT NUMBER
					var GSTconv = GST.toFixed(2);
					//DISPLAY GST VALUE IN INVOICE PREVIEW
					document.write(GSTconv);
					document.forms[0].xgst.value=GSTconv;
                
                </script>        </td>
        <td width="35" height="15"></td>
        <td height="15"></td>
      </tr>
      <tr>
        <td height="15" align="right" class="Verdana11"></td>
        <td height="15" class="Verdana12">PST</td>
        <td width="30" align="right" class="Verdana11"></td>
        <td height="15" align="right" class="Verdana12"><input type="hidden" name="xpst" value=""  />
            <script type="application/javascript">
					//CALCULATE THE PST TOTAL OF INVOICE ITEMS
					var PST =<?php $PSTtotal = array();
							//TAKE CALCULATED GST's FROM EACH INVOICE ITEM AND INSERT THEM INTO ARRAY 
									foreach ($_SESSION['casual'] as $PSTitem)
									{									
										$PSTtotal[]=round($PSTitem['INVTAX'],2);
									}
							//SUM UP THE VALUES IN ARRAY & DISPLAY
									echo array_sum($PSTtotal);
								   ?>;
												
					//CONVERT THE DISPLAYED VALUE INTO TWO DECIMAL POINT NUMBER
					var PSTconv = PST.toFixed(2);
					//DISPLAY GST VALUE IN INVOICE PREVIEW
					document.write(PSTconv);
					document.forms[0].xpst.value=PSTconv;
                </script>        </td>
        <td width="35" height="15"></td>
        <td height="15"></td>
      </tr>
      <tr title="Estimated items (in blue) excluded">
        <td height="15" align="right" class="Verdana11"></td>
        <td height="15" class="Verdana12"><strong>TOTAL</strong></td>
        <td width="30" align="right" class="Verdana11"></td>
        <td height="15" align="right" class="Verdana12"><input type="hidden" name="xtotal" value=""  />
            <strong>
            <script type="application/javascript">
					//CALCULATE THE TOTAL PRICE INCLUDING GST's
					var price =<?php $INVtotal = array();
									   $INVdiscount=array();
						//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                		          foreach ($_SESSION['casual'] as $invtot)
								  {
									if(($invtot['INVEST']!='1' || $_SESSION['csminvno']=='0') && $invtot['INVDECLINE']!='1'){
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
            <!-- INVOICE TOTAL -->        </td>
        <td width="35" height="15"></td>
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
    </table>    </td>
  </tr>
  <tr>
    <td height="30" align="right" valign="bottom" class="hidden">
    <input type="hidden" name="petia" id="petia" value=""  />
    <input type="text" name="cost" value="" style="font-size:12px; border:none; text-align:right;" readonly="readonly"  size="10"/>
   	<input type="text" name="abccost" value="" style="font-size:12px; border:none; text-align:right;" readonly="readonly"  size="10"/></td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input name="button" type="button" class="button" id="button" value="FINISHED" onclick="document.location='CASUAL_SALE_FINISH.php'" />
    <input name="preview" class="button" type="button" value="PREVIEW" onclick="window.open('../../IMAGES/CUSTOM_DOCUMENTS/CASUAL_INVOICE_PREVIEW.php','_blank',' width=855px, height=750px')" />
    <input name="cancel2" type="button" class="button" id="cancel2" value="CANCEL"  onclick="<?php if (isset($_SESSION['casual'])) {echo "confirmation();";} else {echo "history.back();";} ?>" />
    <input type="hidden" name="hapoo" value=""  />    </td>
  </tr>
</table>
<div id="invpreview"></div>
</form>


<form action="" name="cancelinvoice" method="post">
<input type="hidden" name="cancel" value="1" />
<input type="hidden" name="itotal" value="<?php $INVtotal = array();foreach ($_SESSION['casual'] as $invtot){$INVtotal[]=$invtot['INVTOT'];}echo array_sum($INVtotal);?>" /> 
<!-- OTHER FOR REJECTED INVOICE -->
<input type="hidden" name="rejdate" value="<?php echo date("Y/m/d"); ?>"/>
<input type="hidden" name="company" value="CASUAL SALE"/>
</form>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>