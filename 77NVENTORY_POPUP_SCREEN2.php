<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

if (isset($_GET['arinvtype'])){
$arinvtype = $_GET['arinvtype'];
}
else {$arinvtype='';}

if (isset($_SESSION['minvdte'])){
$minvdte=$_SESSION['minvdte'];
}
else {
$minvdte=date('m/d/Y');
}

if (isset($_POST['clearcheck'])){
unset($_SESSION['lookup']);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>LOOKUP THE ITEM</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<style type="text/css">
</style>

<script type="text/javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+23,toppos+90);
document.getElementById('lookup').focus();
document.searchinventory.submit();
document.getElementById('<?php echo $_GET['id']; ?>').bgColor="#FF0099";
}


function setsorting(x,y)
{
self.location='INVENTORY_SEARCH_SCREEN2.php?sorting=' + x + '&refID=<?php echo $_GET['refID']; ?>&id=' + y;
}

function bodyonunload(){
}

</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->

  <form action="INVENTORY_POPUP_IFRAME2.php" method="get" target="list" name="searchinventory" style="position:absolute; top:0px; left:0px;">
  
  <input type="hidden" name="sorting" id="sorting" value="<?php echo $_GET['sorting']; ?>" />
  <input type="hidden" name="arinvtype" id="arinvtype" value="<?php echo $arinvtype; ?>" />
  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td colspan="5" align="left" valign="top">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
          <tr>
            <td width="90" bgcolor="#000000" id="item" class="Verdana11Bwhite" onclick="setsorting('ITEM',this.id);" onmouseover="document.getElementById(this.id).style.cursor='pointer';">Item</td>
      <td width="310" align="left" bgcolor="#000000" id="description" class="Verdana11Bwhite" onclick="setsorting('DESCRIP',this.id);" onmouseover="document.getElementById(this.id).style.cursor='pointer';">Description</td>
      <td width="135" align="left" bgcolor="#000000" class="Verdana11Bwhite" id="vpartno" onclick="setsorting('VPARTNO',this.id);"onmouseover="document.getElementById(this.id).style.cursor='pointer';">Vpartno</td>
      <td bgcolor="#000000" class="Verdana11Bwhite" id="barcode">Barcode</td>
      </tr>
          <tr>
            <td height="10" colspan="3">
            <input name="lookup" type="text" class="Input" id="lookup" size="35"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['lookup']; ?>"/>            </td>
            <td height="10" align="right"><span class="Andale12noDecor">
              <input type="button" name="clear" value="Clear search" onclick="document.clearsessions.submit();" />
              </span></td>
          </tr>
          <tr>
            <td colspan="4" height="430" valign="top">
            <iframe name="list" id="list" scrolling="auto" height="430" width="732" frameborder="0"></iframe></td>
    </tr>
      </table></td>
    </tr>
    
    
    
    
    <tr>
      <td colspan="5" align="center" valign="middle"class="ButtonsTable"><input name="button" type="button" class="button" id="button" onclick="self.close();" value="CLOSE" /></td>
    </tr>	
  </table>

<input type="hidden" name="check" value="1"  />

 </form>

<form action="" method="post" name="clearsessions">
<input type="hidden" name="clearcheck" value="1"  />
</form>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
