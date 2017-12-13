 <?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);


if (isset($_POST['save'])){

if (!empty($_POST['filedate'])){
$filedate=$_POST['filedate'];
}
else {
$filedate='00/00/0000';
}

$filedate="SELECT STR_TO_DATE('$filedate','%m/%d/%Y')";
$filedate=mysql_query($filedate, $tryconnection) or die(mysql_error());
$filedate=mysqli_fetch_array($filedate);

$query_INVENTOR = "INSERT INTO INVTHIST (`UNITS`, `CODE`, `DESCRIP`, SUPPLIER, VPCCODE, DRUGCOST, PACKAGE, LOCN, BACKORDER, ORDERED, RECEIVED) SELECT `UNITS`, `CODE`, `DESCRIP`, SUPPLIER, VPCCODE, DRUGCOST, PKGQTY, LOCN, BACKORDER, '$filedate[0]', RECEIVED FROM INVENTOR WHERE BACKORDER!='1' AND UNITS <> 0";
$INVENTOR = mysql_query($query_INVENTOR) or die(mysql_error());

$query_INVENTOR = "DELETE FROM INVENTOR WHERE BACKORDER !='1'";
$INVENTOR = mysql_query($query_INVENTOR) or die(mysql_error());

$winclose = "self.close();";
}


$select_INVENTOR = "SELECT * FROM INVENTOR GROUP BY `VPCCODE` ORDER BY `DESCRIP` ASC";
$INVENTOR = mysql_query($select_INVENTOR) or die(mysql_error());
$row_INVENTOR = mysqli_fetch_array($INVENTOR);
$totalRows_INVENTOR = mysqli_num_rows($INVENTOR);

do {
$cogs = $cogs + ($row_INVENTOR['DRUGCOST']*$row_INVENTOR['UNITS']);
$vpartno = $row_INVENTOR['VPCCODE'] ;
$UPDinvt = "UPDATE ARINVT SET LDATE = DATE(NOW()) WHERE VPARTNO = '$vpartno' " ;
$UPLDATE = mysql_query($UPDinvt, $tryconnection) or die(mysql_error()) ;
$backorder = $backorder + $row_INVENTOR['BACKORDER'];
} while ($row_INVENTOR = mysqli_fetch_assoc($INVENTOR));

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>FILE ORDER LIST</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
<?php echo $winclose; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+190,toppos+100);
}

function bodyonunload(){
opener.document.location.reload();
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

<form method="post" action="" name="fileol" id="fileol" style="position:absolute; top:0px; left:0px;">
<input type="hidden" name="vpccode" value="<?php echo $row_INVENTOR['VPCCODE']; ?>"  />
<input type="hidden" name="desc" value="<?php echo $row_INVENTOR['DESCRIP']; ?>"  />
    <table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td width="129" height="20" align="left" class="Verdana12B"></td>
        <td width="161" align="left" class="Verdana12"></td>
        <td width="100" align="center" class="Verdana12B"></td>
        <td width="35" align="center" class="Verdana12B"></td>
      </tr>
      <tr>
        <td height="55" colspan="4" align="center" class="Verdana12Blue">
        ADDING CURRENT ORDER LIST TO HISTORY AND <br />
		DELETING NON-BACK ORDERED ITEMS        </td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left" class="Verdana12B">&nbsp;&nbsp;Number of inventory items ordered:</td>
        <td height="30" align="right" class="Verdana12B"><?php echo $totalRows_INVENTOR; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left" class="Verdana12B">&nbsp;&nbsp;Cost of inventory ordered:</td>
        <td align="right" class="Verdana12B"><?php echo $cogs; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left" class="Verdana12B">&nbsp;&nbsp;Number of Back-orders:</td>
        <td align="right" class="Verdana12B"><?php echo $backorder; ?></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left" valign="bottom" class="Verdana12B">&nbsp;&nbsp;Date order was placed:</td>
        <td align="right" valign="bottom" class="Verdana12B"><span class="Labels">
          <input name="filedate" type="text" class="Input" id="filedate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php $date = date_create(date('m/d/Y')); date_sub($date, date_interval_create_from_date_string('0 days')); echo date_format($date, 'm/d/Y'); ?>" size="10" onclick="ds_sh(this);"/>
        </span></td>
        <td align="center" valign="bottom" class="Verdana12B">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="center" valign="top" class="Verdana12B">&nbsp;</td>
        <td align="center" valign="top" class="Verdana12B">&nbsp;</td>
        <td align="center" valign="top" class="Verdana12B">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="4" align="center" class="Verdana12Red"><label class="hidden">
          <input type="checkbox" name="checkbox" id="checkbox" />
        Use current month only</label>
          Back-ordered items must be deleted manually<br /> 
          from the current list when they ship.</td>
      </tr>
      <tr>
        <td height="30" align="left" class="Verdana12">&nbsp;</td>
        <td colspan="2" align="left" valign="top" class="Verdana12"><label class="hidden"><input type="checkbox" name="checkbox2" id="checkbox2" /> 
          Single client</label></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="4" align="center" class="ButtonsTable">
          <input name="save" type="submit" class="button" id="save" value="SAVE"/>
          <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close();" />        </td>
      </tr>
    </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
