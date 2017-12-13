<?php 
require_once('../../tryconnection.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form")) {
  $insertSQL = sprintf("INSERT INTO ARSYSCOMM (COMMCODE, `COMMENT`, COMMTYPE, COMMULT, COMMEMO, COMSWITCH) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                      $_POST['commcode'],
                      mysql_real_escape_string($_POST['comment']),
                      $_POST['commtype'],
                      $_POST['commult'],
                      $_POST['commemo'],
                      $_POST['comswitch']);

  mysql_select_db($database_tryconnection, $tryconnection);
  $Result1 = mysql_query($insertSQL, $tryconnection) or die(mysql_error());
header("Location:COMMENTS_LIST2.php");
}

mysql_select_db($database_tryconnection, $tryconnection);
$query_Recordset1 = "SELECT * FROM ARSCOM WHERE ARSCOM.COMMCODE='$_GET[recordID]'";
$Recordset1 = mysql_query($query_Recordset1, $tryconnection) or die(mysql_error());
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>CREATE NEW SYSTEM COMMENT</title>


<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<script type="text/javascript">

function bodyonload()
{
document.getElementById('dropmsg2').style.display="none";
}

</script>


<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">


<div id="LogoHead" onclick="window.open('/'+localStorage.xdatabase+'/INDEX.php','_self');" onmouseover="CursorToPointer(this.id)" title="Home">DVM</div>

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
                <li><a href="#" onclick="nav0();">Regular Invoicing</a></li>
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
                <li><a href="#" onclick="gexamsheets();"><span class="hidden">nav2();</span>Examination Sheets</a></li>
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
                <li><a href="#">Move Patient to a New Client</a></li>
                <li><a href="#" onclick="searchpatient()">Rabies Tags</a></li>
                <li><a href="#" onclick="searchpatient()">Tattoo Numbers</a></li>
                <li><a href="#" onclick="nav8();"><span class="">Certificates</span></a></li>
                <li><a href="#" onclick="nav9();"><span class="">Clinical Logs</span></a></li>
                <li><a href="#" onclick="nav10();"><span class="">Patient Categorization</span></a></li>
                <li><a href="#" onclick="">Laboratory Templates</a></li>
                <li><a href="#" onclick="nav1();"><span class="">Quick Weight</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
			</ul>
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
<div id="inuse" title="File in memory"></div>



<div id="WindowBody">


  <form action="<?php echo $editFormAction; ?>" method="POST" name="form" >
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="left" valign="top" class="Labels"><label>CommID
        <input type="text" name="commid" id="commid" />
        </label></td>
    </tr>
    <tr>
      <td align="left" valign="top" class="Labels"><label>Code
        <input name="commcode" type="text" id="commcode" value="<?php echo $row_Recordset1['COMMCODE']; ?>" />
        </label></td>
    </tr>
    
    <tr>
      <td align="left" valign="top" class="Labels">
        <label>
          <input name="commtype" type="radio" id="commtype_0" value="1"  />
          invoice</label>
        <br />
        <label>
          <input type="radio" name="commtype" value="2" id="commtype_1" checked/>
          prescription</label>
        <br />     </td>
    </tr>
    <tr>
      <td align="left" valign="top" class="Labels"><label>COMMULT
        <input name="commult" type="text" id="commult" value="<?php echo $row_Recordset1['COMMULT']; ?>" />
        </label></td>
    </tr>
    <tr>
      <td align="left" valign="top" class="Labels"><label>COMMEMO
        <input name="commemo" type="text" id="commemo" value="<?php echo $row_Recordset1['COMMEMO']; ?>" />
        </label></td>
    </tr>
    <tr>
      <td align="left" valign="top" class="Labels"><label>COMSWITCH
        <input name="comswitch" type="text" id="comswitch" value="<?php echo $row_Recordset1['COMSWITCH']; ?>" />
        </label></td>
    </tr>
    <tr>
      <td align="left" valign="top" class="Labels"><label>Comment
        <textarea name="comment" cols="70" rows="8" class="commentarea" id="comment"><?php do {echo $row_Recordset1['COMMENT']."&nbsp;";} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)); ?></textarea>
        </label></td>
    </tr>
    <tr>
      <td align="left" valign="top" class="Labels"><input type="submit" name="button" id="button" value="Submit" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form" />
  </form>
</div>
</body>
</html>