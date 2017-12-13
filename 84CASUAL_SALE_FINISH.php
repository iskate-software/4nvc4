<?php 
session_start();
require_once('../../tryconnection.php');
//include("../../ASSETS/tax.php");

//EDIT PAYMENT ROUTINE
if (isset($_POST['clear'])){
unset($_SESSION['splitpayment']);
unset($_SESSION['rcvdpayment']);
unset($_SESSION['paymdiscount']);
unset($_SESSION['credit']);
unset($_SESSION['onaccount']);
header("Location:CASUAL_SALE_FINISH.php");
}
//GO BACK
else if (isset($_POST['goback'])){
unset($_SESSION['splitpayment']);
unset($_SESSION['rcvdpayment']);
unset($_SESSION['paymdiscount']);
unset($_SESSION['credit']);
unset($_SESSION['onaccount']);
$wingoback="window.open('CASUAL_SALE.php','_self');";
}


$INVtotal = array();
$GSTtotal = array();
$PSTtotal = array();
//$INVdiscount=array();
//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
foreach ($_SESSION['casual'] as $invtot)
{
	$INVtotal[]=round($invtot['INVTOT'],2);
	$GSTtotal[]=round($invtot['INVGST'],2);
	$PSTtotal[]=round($invtot['INVTAX'],2);
}
//SUM UP THE INDIVIDUAL PRICES
$INVtotal=array_sum($INVtotal);
$GSTtotal=array_sum($GSTtotal);
$PSTtotal=array_sum($PSTtotal);
$INVdiscount=array_sum($INVdiscount);
$TOTAL=$INVtotal+$GSTtotal+$PSTtotal;//-$INVdiscount;
$GrandTOTAL=$TOTAL;//+$row_PATIENT_CLIENT['BALANCE'];

//FILLS OUT THE XPAYMENT & PAYMENT INPUT FIELDS
$payment=$GrandTOTAL;
$xpayment=$GrandTOTAL;

if (isset($_POST['ok2'])){

	
		if ($_POST['payment']=='0'){
			if (isset($_SESSION['splitpayment'])){
			$_SESSION['splitpayment'][]=array('AMOUNT' => $_POST['payment'], 'METHOD' => $_POST['paymethod']);
				foreach ($_SESSION['splitpayment'] as $xvalue){
				$sumxvalue[]=$xvalue['AMOUNT'];
				}
			$_SESSION['rcvdpayment']=array(array('AMOUNT' => array_sum($sumxvalue), 'METHOD' => $_POST['paymethod']));
			}
			else {
			$_SESSION['rcvdpayment']=array(array('AMOUNT' => $_POST['payment'], 'METHOD' => $_POST['paymethod']));
			}
		$_SESSION['onaccount']=array('AMOUNT' => $_POST['xpayment'], 'METHOD' => $_POST['paymethod']);	
		$openreason="window.open('REASON.php','_blank','width=300,height=200');";
		}
		else if ($_POST['payment']==$_POST['xpayment']){
			if (isset($_SESSION['splitpayment'])){
			$_SESSION['splitpayment'][]=array('AMOUNT' => $_POST['payment'], 'METHOD' => $_POST['paymethod']);
				foreach ($_SESSION['splitpayment'] as $xvalue){
				$sumxvalue[]=$xvalue['AMOUNT'];
				}
			$_SESSION['rcvdpayment']=array(array('AMOUNT' => array_sum($sumxvalue), 'METHOD' => $_POST['paymethod']));
			}
			else {
			$_SESSION['rcvdpayment']=array(array('AMOUNT' => $_POST['payment'], 'METHOD' => $_POST['paymethod']));
			}
		}
		else if ($_POST['payment']<$_POST['xpayment']){
			if (!isset($_SESSION['splitpayment'])){
			$_SESSION['splitpayment']=array();
			}
		$_SESSION['splitpayment'][]=array('AMOUNT' => $_POST['payment'], 'METHOD' => $_POST['paymethod']);
		$payment=$_POST['xpayment']-$_POST['payment'];
		$xpayment=$payment;	
		}
		else if ($_POST['payment']>$_POST['xpayment']){
		$_SESSION['rcvdpayment']=array(array('AMOUNT' => $_POST['payment'], 'METHOD' => $_POST['paymethod']));
		$_SESSION['credit']=$_POST['payment']-$_POST['xpayment'];
		}
	
}

//CANCEL INVOICE - INSERT INTO REJECTIN
if (isset($_POST['cancel']))
{
$insertSQL="INSERT INTO REJECTIN (REJINV, REJDATE, DATETIME, CUSTNO, PETID, ITOTAL, csstaff, COMPANY) VALUES ($_SESSION[minvno], NOW(), NOW(),'$_SESSION[client]','$_SESSION[patient]','$_POST[itotal]','".mysqli_real_escape_string($mysqli_link, $_SESSION['csstaff'])."','".mysqli_real_escape_string($mysqli_link, $_POST['company'])."')";
mysqli_query($tryconnection, $insertSQL);

header("Location:../../INDEX.php");
}


include("../../ASSETS/saveinvoice.php");


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
function bodyonload(){
<?php //echo $balalert;
	   echo $wingoback; 
	   //echo $openreason; ?>
document.getElementById('inuse').innerText=localStorage.xdatabase;
document.forms[0].payment.focus();
document.forms[0].payment.select();
}

function onaccount(){
if (document.forms[0].payment.value=='0'){
//document.getElementById('onac1').selected=true;
document.getElementById('onac2').selected=true;
}
}

function setnewtotal(x,y){
newtotal=x;
document.routine.payment.value=parseFloat(newtotal); 
document.routine.discpaymethod.value=y; 
}

function addbalance(){
	if (document.routine.addprevbal.checked){
	var oldbalance='<?php echo $row_PATIENT_CLIENT['BALANCE']; ?>';
	document.routine.payment.value=parseFloat(newtotal) + parseFloat(oldbalance); 
	}
	else {
	document.routine.payment.value=parseFloat(newtotal); 
	}
}
function bodyonunload(){
<?php
/*if (isset($_SESSION['printinvoice'])){
echo "window.open('../../IMAGES/CUSTOM_DOCUMENTS/CASUAL_INVOICE_PREVIEW.php','_blank');";
}
*/?>
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
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="57" align="center" valign="bottom" bgcolor="#B1B4FF" class="Verdana12B">&nbsp;</td>
  </tr>
  <tr>
    <td height="29" align="center" valign="top" bgcolor="#B1B4FF">&nbsp;</td>
  </tr>
  <tr>
    <td height="308" align="center" valign="top" bgcolor="#B1B4FF"><table width="70%" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="table">
        <tr>
          <td colspan="4"><table width="509" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="38" colspan="5" align="center" class="Verdana14B"><u>PAYMENT</u></td>
              </tr>
              <tr>
                <td width="27" height="20" valign="top">&nbsp;</td>
                <td width="116" height="20" valign="bottom" class="Verdana12">Subtotal</td>
                <td width="90" height="20" align="right" valign="bottom" class="Verdana12"><?php echo number_format($INVtotal,2,'.',''); ?> </td>
                <td width="44" height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
                <td width="232" rowspan="7" align="center" valign="top" class="Verdana11"><div style="position:absolute; width:232px;">
                    <!--<div id="2" style="position:absolute; top:0px; display:<?php if ($INVdiscount!=0 || isset($_SESSION['finalpayment']) || isset($_POST['ok']) || isset($_POST['ok2']) || ($row_PATIENT_CLIENT['BALANCE']!='0.00')){echo "none";} else {echo "";} ?>; ">
-->
                    <!--<div style="display:none">
<strong>Select Payment Method</strong>
<input type="hidden" name="discpaymethod" id="discpaymethod" value=""  />
<select name="discamount" size="7" class="SelectList" id="discamount">
<?php 
//do { 
//$dots=(10-strlen($row_DISCOUNT['METHOD'])); 
//$newtotal=number_format(round(($INVtotal+$GSTtotal+$PSTtotal)*((100-$row_DISCOUNT['PERCENTG'])*0.01),2),2);
//$newdiscount=number_format(round(($INVtotal+$GSTtotal+$PSTtotal)-$newtotal,2),2);
?>
<option value="<?php //echo $newdiscount; ?>" onclick="setnewtotal('<?php //echo $newtotal; ?>','<?php //echo $row_DISCOUNT['METHOD']; ?>')">&nbsp;&nbsp;<?php //echo $row_DISCOUNT['METHOD'];  for ($i=0; $i<$dots; $i++) {echo '&nbsp;';}  echo $newtotal; ?></option>
<?php //} while ($row_DISCOUNT = mysql_fetch_assoc($DISCOUNT));?>
<option value="ONAC" id="onac1" onclick="document.routine.payment.value='0';">&nbsp;&nbsp;On Account</option>
<option value="SP">&nbsp;&nbsp;Split/Partial</option>
    </select>
<br  />
<input type="submit" name="ok" id="ok" class="button" style="width:50px;" value="OK" />
</div>
-->
                    <div id="3" style="position:absolute; top:0px; display:<?php if (isset($_SESSION['rcvdpayment'])) {echo "none";} else {echo "";}?>; "> <strong>Select Payment Method</strong>
                        <select name="paymethod" size="11" class="SelectList" id="paymethod">
                          <option value="Cash">&nbsp;&nbsp;Cash</option>
                          <option value="Cheque">&nbsp;&nbsp;Cheque</option>
                          <option value="DCrd">&nbsp;&nbsp;DCrd</option>
                          <option value="Visa" selected="selected">&nbsp;&nbsp;Visa</option>
                          <option value="MC">&nbsp;&nbsp;M/C</option>
                          <option value="Amex">&nbsp;&nbsp;Amex</option>
                          <option value="Diners">&nbsp;&nbsp;Diners</option>
                          <option value="GE">&nbsp;&nbsp;GE</option>
                          <option value="PDC">&nbsp;&nbsp;Post Dated Cheque</option>
                          <option value="Pound">&nbsp;&nbsp;Pound</option>
                          <option value="Cell">&nbsp;&nbsp;Cell</option>
                        </select>
                        <br  />
                        <input type="submit" name="ok2" id="ok2" class="button" style="width:50px;" value="OK" />
                    </div>
                  <input type="hidden" name="ponum" id="ponum" value=""  />
                    <input type="hidden" name="company" id="company" value="<?php echo $row_PATIENT_CLIENT['TITLE']." ".$row_PATIENT_CLIENT['CONTACT']." ".$row_PATIENT_CLIENT['COMPANY']; ?>"  />
                    <input type="hidden" name="refvet" id="refvet" value="<?php echo $row_PATIENT_CLIENT['REFVET']; ?>"  />
                    <input type="hidden" name="refclin" id="refclin" value="<?php echo $row_PATIENT_CLIENT['REFCLIN']; ?>"  />
                    <div id="4" style="position:absolute; top:0px; z-index:1; display:<?php if (isset($_SESSION['rcvdpayment'])){echo "";} else {echo "none";} ?>"> <br  />
                        <br  />
                        <input class="button" type="submit" name="clear" id="clear" value="EDIT PAYMENT ROUTINE" style="width:180px;"/>
                        <br  />
                        <br  />
                        <input class="button" type="submit" name="save" id="save" value="SAVE INVOICE" style="width:180px;"/>
                        <br  />
                        <br  />
						<input class="button" type="submit" name="prtsave" id="prtsave" value="PRINT & SAVE INVOICE" onclick="window.open('PRINT_INVOICE.php','_parent')" style="width:180px;"/>
                        <br  />
                        <br  />
                    </div>
                </div></td>
              </tr>
              <tr>
                <td height="20" valign="top">&nbsp;</td>
                <td height="20" valign="bottom" class="Verdana12">HST<?php //taxname($database_tryconnection, $tryconnection, date("m/d/Y")); ?></td>
                <td height="20" align="right" valign="bottom" class="Verdana12"><?php echo number_format($GSTtotal,2,'.',''); ?> </td>
                <td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
              </tr>
              <tr>
                <td height="20" valign="top">&nbsp;</td>
                <td height="20" valign="bottom" class="Verdana12">PST</td>
                <td height="20" align="right" valign="bottom" class="Verdana12"><?php echo number_format($PSTtotal,2,'.',''); ?> </td>
                <td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
              </tr>
              <tr>
                <td height="20" valign="top">&nbsp;</td>
                <td height="20" valign="bottom" class="Verdana12">Total</td>
                <td height="20" align="right" valign="bottom" class="Verdana12"><hr noshade="noshade" size="1" color="#000000"  />
                    <?php echo number_format($TOTAL,2,'.',''); ?> </td>
                <td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
              </tr>
              <tr>
                <td height="20" valign="top" class="hidden">&nbsp;</td>
                <td height="20" valign="bottom" class="hidden"><span <?php if ($row_PATIENT_CLIENT['BALANCE']!='0.00'){echo "class='Verdana12BPink' style='background-color:#FFFF00'";} ?>>Previous Balance</span></td>
                <td height="20" align="right" valign="bottom" class="hidden"><span <?php if ($row_PATIENT_CLIENT['BALANCE']!='0.00'){echo "class='Verdana12BPink' style='background-color:#FFFF00'";} ?>><?php echo $row_PATIENT_CLIENT['BALANCE']; ?></span></td>
                <td height="20" align="right" valign="bottom" class="hidden">&nbsp;</td>
              </tr>
              <tr>
                <td height="20" valign="top"></td>
                <td height="20" valign="bottom" class="hidden"><span class="Verdana12"><strong>Grand Total</strong></span></td>
                <td height="20" align="right" valign="bottom" class="hidden"><span <?php if ($row_PATIENT_CLIENT['BALANCE']!='0.00'){echo "class='Verdana12BBlue' style='background-color:#FFFF00'";} ?>> <?php echo number_format($GrandTOTAL,2,'.',''); ?> </span> </td>
                <td height="20" align="right" valign="bottom" class="Verdana12B">&nbsp;</td>
              </tr>
              <tr>
                <td height="20" valign="top">&nbsp;</td>
                <td height="20" valign="bottom" class="Verdana12">Payment</td>
                <td height="20" align="right" valign="bottom" class="Verdana12"><hr noshade="noshade" size="1" color="#000000"  />
                    <input type="hidden" value="<?php echo number_format($xpayment,2,'.',''); ?>" name="xpayment" id="xpayment" size="6"/>
                    <input type="text" name="payment" id="payment" value="<?php echo number_format($payment,2,'.',''); ?>" size="8" class="Inputright" onfocus="InputOnFocus(this.id);" onblur="InputOnBlur(this.id);" onkeyup="onaccount();" style="display:<?php if (isset($_SESSION['rcvdpayment'])){echo "none";} else {echo "";} ?>"/>
                    <?php if (isset($_SESSION['rcvdpayment'])){echo $_POST['payment'];} else {echo "";} ?>                </td>
                <td height="20" align="right" valign="bottom" class="Verdana12">&nbsp;</td>
              </tr>
              <?php if (!empty($_SESSION['splitpayment'])){ 

		foreach ($_SESSION['splitpayment'] as $value) { 
		echo      '<tr class="Verdana11">
					<td height="15">&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right">'.number_format($value['AMOUNT'],2,'.','').'</td>
					<td align="left">&nbsp;'.$value['METHOD'].'</td>
					<td>&nbsp;</td>
				  </tr>';
		  }
} ?>
              <tr>
                <td height="20" valign="top">&nbsp;</td>
                <td height="20" valign="top" class="Verdana12"></td>
                <td height="20" valign="top" class="Verdana12">&nbsp;</td>
                <td height="20" valign="top" class="Verdana12">&nbsp;</td>
                <td height="20" align="center" valign="top">&nbsp;</td>
              </tr>
              <tr>
                <td height="20" valign="top">&nbsp;</td>
                <td height="20" valign="top" class="Verdana12">&nbsp;</td>
                <td height="20" valign="top" class="Verdana12">&nbsp;</td>
                <td height="20" valign="top" class="Verdana12"></td>
                <td height="20" align="center" valign="top"></td>
              </tr>
              <tr>
                <td height="20" valign="top" class="Verdana12" colspan="5"><?php //print_r($_SESSION['rcvdpayment']); ?>                </td>
              </tr>
          </table></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="top" bgcolor="#B1B4FF"><input class="button" type="submit" name="goback" id="goback" value="GO BACK" style="width:110px;" />
        <input name="cancel2" class="button" type="button" value="CANCEL" onclick="confirmation()"  style="width:110px;"/>    </td>
  </tr>
  <tr>
    <td height="140" align="center" valign="bottom" bgcolor="#B1B4FF" class="Verdana12B"></td>
  </tr>
</table>
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