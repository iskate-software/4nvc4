<?php 
session_start();
require_once('../../tryconnection.php');

//$reqsupplier = $_GET['supplier'] ;
mysql_select_db($database_tryconnection, $tryconnection);

$select_INVSOLD = "SELECT INVSOLD.INVVPC,INVSOLD.INVDESC,SUPPLIER, SUM(INVUNITS) AS INVUNITS FROM INVSOLD LEFT JOIN ARINVT ON INVSOLD.INVVPC = ARINVT.VPARTNO  GROUP BY INVDESC ORDER BY SUPPLIER,INVDESC ASC";
$INVSOLD = mysql_query($select_INVSOLD) or die(mysql_error());
$row_INVSOLD = mysql_fetch_assoc($INVSOLD);


if (isset($_POST['zap'])){
$delete_INVSOLD = "TRUNCATE TABLE INVSOLD";
$delete_INVSOLD = mysql_query($delete_INVSOLD) or die(mysql_error());
$closewin="document.location.reload();";
}

else if (isset($_POST['finish'])){
	foreach ($_POST['deleted'] as $deleted){
	$delete_INVSOLD = "DELETE FROM INVSOLD WHERE INVVPC='$deleted' LIMIT 1";
	$delete_INVSOLD = mysql_query($delete_INVSOLD) or die(mysql_error());
	}
$closewin="window.open('../COMMON/INVENTORY_DIRECTORY.php','_self');";

}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>INVENTORY SOLD LIST</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/JavaScript">
function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
<?php echo $closewin; ?>
}

function highliteline(x){
document.getElementById(x).style.cursor="default";
document.getElementById(x).style.backgroundColor="#DCF6DD";
document.getElementById(document.cash_receipt.invno.value).style.backgroundColor="#00E684";
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
document.getElementById(document.cash_receipt.invno.value).style.backgroundColor="#00E684";
}

function checkzap()
{
valid = true;

  if (document.soldlist.whichbutton.value=='zap'){	
	if (confirm("This will delete the entire SOLD LIST. Are you sure you want to continue?")){
		valid = true;
	}
	else {
		valid = false;
	}
  }
  
return valid;
}

</script>

<style type="text/css">
</style>
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
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
<form method="post" action="" name="soldlist" id="soldlist" onsubmit="return checkzap();">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="35" colspan="3" align="left" class="Verdana13B">&nbsp;&nbsp;Inventory Sold List</td>
    <td height="35" align="center" class="Verdana13B"></td>
    <td height="35" align="right" class="Verdana12"><?php echo date('m/d/Y'); ?>&nbsp;&nbsp;</td>
  </tr>
  <tr>
    <td width="100" align="left" class="Verdana11Bwhite" bgcolor="#000000">&nbsp;Vendor#</td>
    <td width="80" align="left" class="Verdana11Bwhite" bgcolor="#000000">&nbsp;Supplier</td>
    <td width="55" align="left" class="Verdana11Bwhite" bgcolor="#000000">Units</td>
    <td width="115" align="left" class="Verdana11Bwhite" bgcolor="#000000">&nbsp;&nbsp;Description</td>
    <td width="190"class="Verdana11Bwhite" align="left" bgcolor="#000000">Delete</td>
    <td width="210" class="Verdana11Bwhite" bgcolor="#000000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5">
    <div id="soldlistdiv">
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">  
<?php do { ?>
  <tr class="Verdana11" id="<?php echo $row_INVSOLD['INVVPC']; ?>" onmouseover="highliteline(this.id)" onmouseout="whiteoutline(this.id)">
    <td height="15" width="130">&nbsp;<?php echo $row_INVSOLD['INVVPC']; ?></td>
    <td height="15" width="80">&nbsp;<?php echo $row_INVSOLD['SUPPLIER']; ?></td>
    <td width="60" align="right">
	<?php 
	if (number_format($row_INVSOLD['INVUNITS'],0)==$row_INVSOLD['INVUNITS']){
		echo  number_format($row_INVSOLD['INVUNITS'],0)."&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		else {
		echo $row_INVSOLD['INVUNITS'];
		}
	 ?>
    </td>
    <td width="40"></td>
    <td width="150"><?php echo $row_INVSOLD['INVDESC']; ?></td>
    <td align="left"><input type="checkbox" name="deleted[]" value="<?php echo $row_INVSOLD['INVVPC']; ?>"/></td>
    <td align="right" valign="middle">
    <img src="../../IMAGES/e3 copy.jpg" alt="e" id="e<?php echo $row_INVSOLD['INVVPC']; ?>" width="15" height="15" onclick="window.open('EDIT_SOLD_LIST.php?soldid=<?php echo $row_INVSOLD['INVVPC']; ?>','_blank','toolbar=no, status=no, width=400, height=300')" onmouseover="CursorToPointer(this.id)" title="EDIT"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <img src="../../IMAGES/v copy.jpg" alt="v" id="v<?php echo $row_INVSOLD['INVVPC']; ?>" width="15" height="15" onclick="window.open('VIEW_SOLD_LIST.php?soldid=<?php echo $row_INVSOLD['INVVPC']; ?>','_blank','toolbar=no, status=no, width=400, height=300')" onmouseover="CursorToPointer(this.id)" title="VIEW DETAIL"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  </tr>
<?php } while ($row_INVSOLD = mysql_fetch_assoc($INVSOLD)); ?>  
</table>
    </div>
    </td>
  </tr>
  <tr id="buttons">
    <td colspan="5" align="center" class="ButtonsTable">
    	<input name="finish" type="submit" class="button" id="finish" value="FINISH" onclick="document.soldlist.whichbutton.value='finish';" title="This will delete all checked items and take you back to the inventory directory screen."/>
    	<input name="button" type="button" class="button" id="button" value="PRINT" onclick="window.print();" />
    	<input name="zap" type="submit" class="button" id="zap" value="ZAP" onclick="document.soldlist.whichbutton.value='zap';" title="This will delete the entire sold list without an option to revert."/>
    	<input name="button" type="button" class="button" id="button" value="CANCEL" onclick="window.open('../COMMON/INVENTORY_DIRECTORY.php','_self')" title="This will ignore any ticked checkboxes and take you back to the inventory directory screen."/>
    </td>
  </tr>
</table>
<input type="hidden" name="whichbutton" value="" />
</form>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
