<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);

if (!empty($_POST['startdate'])){
$startdate=$_POST['startdate'];
}
else {
$startdate='00/00/0000';
}

mysql_select_db($database_tryconnection, $tryconnection);
$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysql_query($startdate, $tryconnection) or die(mysql_error());
$startdate=mysql_fetch_array($startdate);

if (!empty($_POST['enddate'])){
$enddate=$_POST['enddate'];
}
else {
$enddate=date('m/d/Y');
}

$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysql_fetch_array($enddate);

// This code examines the detailed invoicing files to determine which clients were
// sold a particular inventory item. It uses the variable minvvpc as a key.
// Starting and ending dates are provided. These are checked against PRACTICE to
// determine which of the DVMINV, DVMILAST, ARYDVMI files are involved, then the
// data is extracted, starting from the current file first.
//
// Extraction from the first and second generation tables is so fast that it happens
// every time, with the starting and ending dates acting as the filter if the given 
// date range did not include the timespan of those tables. ARYDVMI is only 
// interrogated if the date span covers it.
//

$Setup_1 = "DROP TEMPORARY TABLE IF EXISTS SALESEARCH " ;
$Setup_1 = mysql_query($Setup_1, $tryconnection ) or die(mysql_error()) ;

$Setup_2 = "CREATE TEMPORARY TABLE SALESEARCH (INVCUST CHAR(7), INVPET INT(6), INVDATETIME DATETIME, INVUNITS FLOAT(8,2), INVPRICE FLOAT(8,2), INVTOT FLOAT(8,2))";
$Setup_2 = mysql_query($Setup_2, $tryconnection ) or die(mysql_error()) ;

$Setup_3 = "INSERT INTO SALESEARCH (INVCUST, INVPET, INVDATETIME, INVUNITS, INVPRICE, INVTOT) SELECT INVCUST, INVPET, INVDATETIME, INVUNITS, INVPRICE, INVTOT FROM DVMINV WHERE INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]' AND INVVPC = $_POST[invvpc]" ;
$Setup_3 = mysql_query($Setup_3, $tryconnection ) or die(mysql_error()) ;

$Setup_4 = "INSERT INTO SALESEARCH (INVCUST, INVPET, INVDATETIME, INVUNITS, INVPRICE, INVTOT) SELECT INVCUST, INVPET, INVDATETIME, INVUNITS, INVPRICE, INVTOT FROM DVMILAST WHERE INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]' AND INVVPC = $_POST[invvpc]" ;
$Setup_4 = mysql_query($Setup_4, $tryconnection ) or die(mysql_error()) ;

$Get_History = "SELECT LASTCLOSE FROM PRACTICE WHERE LASTCLOSE > $startdate[0]" ;
$Is_History = mysql_query($Get_History, $tryconnection ) or die(mysql_error()) ;
$row_Is_History = mysql_fetch_array($Is_History);

$i=1;
$Use_Hist = 0 ;

while($i<=3 && $row_Is_History = mysql_fetch_array($Is_History)){

  if ( $row_Is_History['LASTCLOSE'] > $startdate[0] ) {
    $Use_Hist = 1 ;
  }
  
$i++;
}
  if ($i > 2 && $Use_Hist = 1) {
   $Setup_5 = "INSERT INTO SALESEARCH (INVCUST, INVPET, INVDATETIME, INVUNITS, INVPRICE, INVTOT) SELECT INVCUST, INVPET, INVDATETIME, INVUNITS, INVPRICE, INVTOT FROM ARYDVMI WHERE INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]' AND INVVPC = $_POST[invvpc]" ;
   $Setup_5 = mysql_query($Setup_5, $tryconnection ) or die(mysql_error()) ;
  }


$select_SALESEARCH = "SELECT *, DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME  FROM SALESEARCH JOIN ARCUSTO ON (ARCUSTO.CUSTNO=SALESEARCH.INVCUST)";
$SALESEARCH = mysql_query($select_SALESEARCH) or die(mysql_error());
$row_SALESEARCH = mysql_fetch_assoc($SALESEARCH);

$select_CASUAL = "SELECT *, DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME  FROM SALESEARCH WHERE INVCUST = '0'";
$CASUAL = mysql_query($select_CASUAL) or die(mysql_error());
$row_CASUAL = mysql_fetch_assoc($CASUAL);


//$select_INVSOLD = "SELECT * FROM INVSOLD WHERE SOLDID=$_GET[soldid]";
//$INVSOLD = mysql_query($select_INVSOLD) or die(mysql_error());
//$row_INVSOLD = mysql_fetch_assoc($INVSOLD);
//
//$select_ARINVT = "SELECT * FROM ARINVT WHERE VPARTNO=$row_INVSOLD[INVVPC]";
//$ARINVT = mysql_query($select_ARINVT) or die(mysql_error());
//$row_ARINVT = mysql_fetch_assoc($ARINVT);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>INVENTORY DETAIL SALES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
resizeTo(650,500);
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+60,toppos+10);
document.editsl.invunits.focus();
}
</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>
<form method="post" action="" name="editsl" id="editsl" style="position:absolute; top:0px; left:0px;">
    <table width="650" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td height="20" colspan="6" align="center" class="Verdana12B"><span style="background-color:#FFFF00">&nbsp;<?php echo $_POST['invdesc']; ?>&nbsp;</span></td>
      </tr>
      <tr class="Verdana11Bwhite" bgcolor="#000000">
        <td width="195" align="center">&nbsp;Date</td>
        <td width="273" align="left">Client</td>
        <td width="145" align="right">Qty</td>
        <td width="105" align="right">U.Price</td>
        <td width="121" align="right">Ext.Price</td>
        <td width="101" align="right">Disp. Fee</td>
      </tr>
      <tr>
       <td colspan="6">
       <div style="height:407px; overflow:auto;">
     <table width="100%" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">
     <?php if (!empty($row_CASUAL)){ do { ?>   
      <tr>
        <td height="18" align="center" class="Verdana12">&nbsp;<?php echo $row_CASUAL['INVDATETIME']; ?></td>
        <td align="left" class="Verdana12">CASUAL SALE</td>
        <td align="right" class="Verdana12"><?php echo $row_CASUAL['INVUNITS']; ?></td>
        <td align="right" class="Verdana12"><?php echo $row_CASUAL['INVPRICE']; ?></td>
        <td align="right" class="Verdana12"><?php echo $row_CASUAL['INVTOT']; ?></td>
        <td align="right" class="Verdana12"><?php echo number_format($row_CASUAL['INVTOT']-($row_CASUAL['INVPRICE']*$row_CASUAL['INVUNITS']), 2); ?>&nbsp;</td>
      </tr>
  <?php } while ($row_CASUAL = mysql_fetch_assoc($CASUAL)); } ?> 
     <?php do { ?>   
      <tr>
        <td height="18" align="center" class="Verdana12">&nbsp;<?php echo $row_SALESEARCH['INVDATETIME']; ?></td>
        <td align="left" class="Verdana12"><?php 
		echo $row_SALESEARCH['TITLE']." ".$row_SALESEARCH['CONTACT']." ".$row_SALESEARCH['COMPANY']; 
		?></td>
        <td align="right" class="Verdana12"><?php echo $row_SALESEARCH['INVUNITS']; ?></td>
        <td align="right" class="Verdana12"><?php echo $row_SALESEARCH['INVPRICE']; ?></td>
        <td align="right" class="Verdana12"><?php echo $row_SALESEARCH['INVTOT']; ?></td>
        <td align="right" class="Verdana12"><?php echo number_format($row_SALESEARCH['INVTOT']-($row_SALESEARCH['INVPRICE']*$row_SALESEARCH['INVUNITS']), 2); ?>&nbsp;</td>
      </tr>
  <?php } while ($row_SALESEARCH = mysql_fetch_assoc($SALESEARCH)); ?> 
        </table> 
        </div> 
       </td>
      </tr>
      <tr>
        <td colspan="6" align="center" class="ButtonsTable"><input name="cancel" type="reset" class="button" id="cancel" value="OK" onclick="self.close();" />        </td>
      </tr>
    </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
