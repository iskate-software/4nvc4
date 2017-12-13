<?php 
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);
$select_INVSOLD = "SELECT * FROM INVSOLD WHERE INVVPC=$_GET[soldid]";
$INVSOLD = mysqli_query($mysqli_link, $select_INVSOLD) or die(mysqli_error($mysqli_link));
$row_INVSOLD = mysqli_fetch_assoc($INVSOLD);

$select_ARINVT = "SELECT * FROM ARINVT WHERE VPARTNO=$row_INVSOLD[INVVPC]";
$ARINVT = mysqli_query($mysqli_link, $select_ARINVT) or die(mysqli_error($mysqli_link));
$row_ARINVT = mysqli_fetch_assoc($ARINVT);

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
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+190,toppos+160);
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
<form method="post" action="RETRIEVE_SOLD_LIST.php" name="editsl" id="editsl" style="position:absolute; top:0px; left:0px;">
<input type="hidden" name="invvpc" value="<?php echo $row_INVSOLD['INVVPC']; ?>"  />
<input type="hidden" name="invdesc" value="<?php echo $row_INVSOLD['INVDESC']; ?>"  />
    <table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td width="130" height="20" align="left" class="Verdana12B">&nbsp;</td>
        <td width="70" align="left" class="Verdana12">&nbsp;</td>
        <td width="130" align="center" class="Verdana12B">&nbsp;</td>
        <td width="70" align="center" class="Verdana12B">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" align="left" class="Verdana12B">&nbsp;&nbsp;Vendor Code:</td>
        <td colspan="3" align="left" class="Verdana12"><?php echo $row_INVSOLD['INVVPC']; ?></td>
      </tr>
      <tr>
        <td height="30" align="left" class="Verdana12B">&nbsp;&nbsp;Product:</td>
        <td colspan="3" align="left" class="Verdana12"><span style="background-color:#FFFF00"><?php echo $row_INVSOLD['INVDESC']; ?></span></td>
      </tr>
      <tr>
        <td height="30" align="left" class="Verdana12B">&nbsp;&nbsp;Current stock:</td>
        <td align="left" class="Verdana12"><?php //echo $row_ARINVT['ONHAND']; ?></td>
        <td align="center" class="Verdana12B">Package Qty:</td>
        <td align="left" class="Verdana12"><?php echo $row_ARINVT['PKGQTY']; ?></td>
      </tr>
      <tr>
        <td height="30" align="left" valign="bottom" class="Verdana12B">&nbsp;&nbsp;Starting date:</td>
        <td align="left" valign="bottom" class="Verdana12B">&nbsp;</td>
        <td align="center" valign="bottom" class="Verdana12B">Ending Date:</td>
        <td align="center" valign="bottom" class="Verdana12B">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="center" valign="top" class="Verdana12B"><span class="Labels">
        <input name="startdate" type="text" class="Input" id="startdate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php $date = date_create(date('m/d/Y')); date_sub($date, date_interval_create_from_date_string('1 days')); echo date_format($date, 'm/d/Y'); ?>" size="10" onclick="ds_sh(this);"/>
        </span></td>
        <td colspan="2" align="center" valign="top" class="Verdana12B"><span class="Labels">
          <input name="enddate" type="text" class="Input" id="enddate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo date('m/d/Y'); ?>" size="10" onclick="ds_sh(this);"/>
        </span></td>
      </tr>
      <tr>
        <td height="30" align="left" class="Verdana12">&nbsp;</td>
        <td colspan="2" align="left" valign="top" class="Verdana12"><label class="hidden">
          <input type="checkbox" name="checkbox" id="checkbox" />
        Use current month only</label></td>
        <td align="center" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" align="left" class="Verdana12">&nbsp;</td>
        <td colspan="2" align="left" valign="top" class="Verdana12"><label class="hidden"><input type="checkbox" name="checkbox2" id="checkbox2" /> 
          Single client</label></td>
        <td align="left" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="35" align="center" valign="top" class="Verdana12">&nbsp;</td>
    <td colspan="2" align="left" valign="top" class="Verdana12"><label class="hidden"><input type="checkbox" name="checkbox3" id="checkbox3" /> 
          Include clients names</label></td>
        <td align="center" valign="top" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="4" align="center" class="ButtonsTable">
          <input name="retrieve" type="submit" class="button" id="retrieve" value="RETRIEVE"/>
          <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close();" />
        </td>
      </tr>
    </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
