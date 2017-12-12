<?php 
session_start();
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);

$reqsupplier = $_SESSION['supplier'] ;

$query_INVENTORY = "SELECT * FROM ARINVT WHERE ITEMID='$_GET[itemid]' LIMIT 1";
$INVENTORY = mysql_query($query_INVENTORY, $tryconnection) or die(mysql_error());
$row_INVENTORY = mysql_fetch_assoc($INVENTORY);

if (isset($_POST['save'])){
$insert_INVENTOR = "INSERT INTO INVENTOR (`UNITS`, `CODE`, `DESCRIP`, SUPPLIER, VPCCODE, DRUGCOST, PKGQTY) VALUES ('$_POST[units]', '$row_INVENTORY[ITEM]', '$row_INVENTORY[DESCRIP]', '$row_INVENTORY[SUPPLIER]', '$row_INVENTORY[VPARTNO]', '$row_INVENTORY[COST]', '$row_INVENTORY[PKGQTY]')";
$insert_INVENTOR = mysql_query($insert_INVENTOR) or die(mysql_error());

if ($row_INVENTORY['MONITOR'] == 1) {
  $upd_inv = "UPDATE ARINVT SET ORDERED = ORDERED + '$_POST[units]' WHERE ITEMID='$_GET[itemid]' LIMIT 1" ;
  $do_upd = mysql_query($upd_inv, $tryconnection) or die(mysql_error()) ;
}

$closewin = "opener.document.location.reload(); self.close();";
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ADD ITEM TO THE ORDER LIST</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
<?php echo $closewin; ?>
document.addol.units.focus();
}
</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="addol" id="addol" style="position:absolute; top:0px; left:0px;">
    <table width="732" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td height="122" align="center" class="Verdana12B">
        <span style="background-color:#FFFF00"><?php echo $row_INVENTORY['DESCRIP']; ?></span>
        </td>
      </tr>
      <tr>
        <td height="343" align="center" valign="top" class="Verdana12">
        
        <table width="366" border="1" cellspacing="0" cellpadding="0" frame="box" rules="none">
          <tr>
            <td height="10" colspan="4"></td>
          </tr>
          <tr>
            <td width="60" height="30">&nbsp;</td>
            <td width="120" class="Verdana12B">Inventory Code</td>
            <td width="100" align="right"><?php echo $row_INVENTORY['ITEM']; ?></td>
            <td width="">&nbsp;</td>
          </tr>
          <tr>
            <td height="30">&nbsp;</td>
            <td class="Verdana12B">Vendor Code</td>
            <td align="right"><?php echo $row_INVENTORY['VPARTNO']; ?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30">&nbsp;</td>
            <td class="Verdana12B">Shelf Location</td>
            <td align="right"><?php echo $row_INVENTORY['SEQ']; ?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30">&nbsp;</td>
            <td class="Verdana12B">Cost of Drug</td>
            <td align="right"><?php echo $row_INVENTORY['COST']; ?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30">&nbsp;</td>
            <td class="Verdana12B">Packaging</td>
            <td align="right"><?php echo $row_INVENTORY['PKGQTY']; ?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="10" colspan="4"></td>
          </tr>
          <tr>
            <td colspan="4" align="center"><hr size="1" style="margin:0px;"/></td>
          </tr>
          <tr>
            <td height="40" colspan="2" align="center">Enter # of units to order</td>
            <td align="right"><input type="text" name="units" id="units" class="Inputright" value="1" size="6" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
            <td>&nbsp;</td>
          </tr>
        </table>
        <label></label></td>
      </tr>
      <tr>
        <td align="center" class="ButtonsTable">
          <input name="save" type="submit" class="button" id="save" value="SAVE"/>
        <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close();" /></td>
      </tr>
    </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
