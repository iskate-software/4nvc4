<?php 
session_start();
require_once('../../tryconnection.php');
mysqli_select_db($tryconnection, $database_tryconnection);
$select_INVENTOR = "SELECT *, SUM(`UNITS`) AS `UNITS` FROM INVENTOR WHERE VPCCODE=$_GET[soldid]";
$INVENTOR = mysqli_query($mysqli_link, $select_INVENTOR) or die(mysqli_error($mysqli_link));
$row_INVENTOR = mysqli_fetch_assoc($INVENTOR);

if (isset($_POST['save'])){

$delete_INVENTOR = "DELETE FROM INVENTOR WHERE VPCCODE=$_GET[soldid]";
$delete_INVENTOR = mysqli_query($mysqli_link, $delete_INVENTOR) or die(mysqli_error($mysqli_link));

$backorder = !empty($_POST['backorder']) ? 1 : 0;

$insert_INVENTOR = "INSERT INTO INVENTOR (`UNITS`, `CODE`, `DESCRIP`, VPCCODE, BACKORDER, SUPPLIER, DRUGCOST, PACKAGE) VALUES ('$_POST[units]',  '$row_INVENTOR[CODE]', '$row_INVENTOR[DESCRIP]', '$_GET[soldid]', '$backorder', '$_SESSION[supplier]','$_POST[drugcost]', '$_POST[package]')";
$insert_INVENTOR = mysqli_query($mysqli_link, $insert_INVENTOR) or die(mysqli_error($mysqli_link));

$closewin = "opener.document.location.reload(); self.close();";
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>EDIT ORDER LIST ITEM</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
<?php echo $closewin; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+190,toppos+160);
document.editsl.units.focus();
}
</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="editsl" id="editsl" style="position:absolute; top:0px; left:0px;">
    <table width="400" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td height="57" colspan="3" align="center" class="Verdana12B">
        <span style="background-color:#FFFF00"><?php echo $row_INVENTOR['DESCRIP']; ?></span>        </td>
      </tr>
      <tr>
        <td width="48" align="center" valign="middle" class="Verdana12">&nbsp;</td>
        <td width="154" height="30" align="left" valign="middle" class="Verdana12"><label>Number of Units
            
        </label></td>
        <td width="198" align="left" valign="middle" class="Verdana12"><input type="text" name="units" id="units" class="Inputright" value="<?php echo $row_INVENTOR['UNITS']; ?>" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
      </tr>
      <tr>
        <td align="center" valign="middle" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" valign="middle" class="Verdana12">Packaging</td>
        <td align="left" valign="middle" class="Verdana12"><input type="text" name="package" id="package" class="Inputright" value="<?php echo $row_INVENTOR['PACKAGE']; ?>" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
      </tr>
      <tr>
        <td align="center" valign="middle" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" valign="middle" class="Verdana12">Package Cost</td>
        <td align="left" valign="middle" class="Verdana12"><input type="text" name="drugcost" id="drugcost" class="Inputright" value="<?php echo $row_INVENTOR['DRUGCOST']; ?>" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
      </tr>
      <tr>
        <td align="center" valign="middle" class="Verdana12">&nbsp;</td>
        <td height="30" align="right" valign="middle" class="Verdana12"><label><input type="checkbox" name="backorder" value="1" <?php if ($row_INVENTOR['BACKORDER']=='1') {echo "checked";} ?>/> Back ordered</label></td>
        <td align="left" valign="middle" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" valign="middle" class="Verdana12">&nbsp;</td>
        <td height="45" align="left" valign="middle" class="Verdana12">&nbsp;</td>
        <td align="left" valign="middle" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" align="center" class="ButtonsTable">
          <input name="save" type="submit" class="button" id="save" value="SAVE"/>
        <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close();" /></td>
      </tr>
    </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
