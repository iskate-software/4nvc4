<?php 
session_start();
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);

// in case they have already selected the delete box in other items.

	foreach ($_POST['deleted'] as $deleted){
	$delete_INVSOLD = "DELETE FROM INVSOLD WHERE INVVPC=$deleted";
	$delete_INVSOLD = mysql_query($delete_INVSOLD) or die(mysql_error());
	}
	
$select_INVSOLD = "SELECT *, SUM(INVUNITS) AS INVUNITS FROM INVSOLD WHERE INVVPC='$_GET[soldid]'";
$INVSOLD = mysql_query($select_INVSOLD) or die(mysql_error());
$row_INVSOLD = mysql_fetch_assoc($INVSOLD);

if (isset($_POST['save'])){

$delete_INVSOLD = "DELETE FROM INVSOLD WHERE INVVPC='$_GET[soldid]'";
$delete_INVSOLD = mysql_query($delete_INVSOLD) or die(mysql_error());

$insert_INVSOLD = "INSERT INTO INVSOLD (INVVPC, INVDESC, INVUNITS) VALUES ('$_GET[soldid]', '$row_INVSOLD[INVDESC]', '$_POST[invunits]')";
$insert_INVSOLD = mysql_query($insert_INVSOLD) or die(mysql_error());

$closewin = "opener.document.location.reload(); self.close();";
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>EDIT SOLD LIST ITEM</title>
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
document.editsl.invunits.focus();
}
</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="editsl" id="editsl" style="position:absolute; top:0px; left:0px;">
    <table width="400" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td height="83" align="center" class="Verdana12B">
        <span style="background-color:#FFFF00"><?php echo $row_INVSOLD['INVDESC']; ?></span>
        </td>
      </tr>
      <tr>
        <td height="74" align="center" valign="top" class="Verdana12"><label>Number of Units
            <input type="text" name="invunits" id="invunits" class="Inputright" value="<?php echo $row_INVSOLD['INVUNITS']; ?>" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>
        </label></td>
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
