<?php
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$procid=$_GET['procid'];

$query_PROCEDUR = "SELECT * FROM PROCEDUR WHERE PROCID=$procid";
$PROCEDUR = mysql_query($query_PROCEDUR, $tryconnection) or die(mysql_error());
$row_PROCEDUR = mysqli_fetch_assoc($PROCEDUR);

if (isset($_POST['save'])){
$query_QUICKUPDATE = "UPDATE PROCEDUR SET INVPRICE='$_POST[invprice]', INVUNITS='$_POST[invunits]', INVTOT='$_POST[invtot]', INVDISP='$_POST[invdisp]' WHERE PROCID=$procid";
$QUICKUPDATE = mysql_query($query_QUICKUPDATE, $tryconnection) or die(mysql_error());
}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>QUICK FEE UPDATE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
document.quick_fee.invprice.focus();
}

function bodyonunload(){
opener.document.location.reload();
}


function calculateprice(){
//takes quantity
var quantity = document.forms[0].invunits.value;
var invdisp=document.forms[0].invdisp.value;
var unitprice = document.forms[0].invprice.value;

var result = parseFloat(quantity) * parseFloat(unitprice);
//var result = result+parseFloat(invdisp);			
//var result = parseFloat(result);			

var resultrounded = Math.round(result*Math.pow(10,2))/Math.pow(10,2);
//converts into two decimal points
var resultfull = resultrounded.toFixed(2);
//inserts it into 'invtot' input field
document.forms[0].invtot.value = resultfull;
}

</script>

<style type="text/css">

.SelectList {
	width: 200px;
	height: 300px;
	font-family: "Verdana";
	font-size: 11px;
	border-width: 1px;
	padding: 5 px;
	outline-width: 0px;
}

</style>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="quick_fee" id="quick_fee" class="FormDisplay" style="position:absolute; top:0px; left:0px;">

<table width="450" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td height="83" colspan="5" align="center" class="Verdana13"><span class="Verdana14B">Quick Item Fee Update</span><br  />Procedure:&nbsp;<?php echo $row_PROCEDUR['PROCODE']; ?>&nbsp;-&nbsp;<?php echo $row_PROCEDUR['PROCEDURE']; ?></td>
    </tr>
  <tr>
    <td width="240" height="37" align="center" valign="bottom" class="Verdana11B">      </td>
    <td width="94" align="center" valign="bottom" class="Verdana11B">UPrice</td>
    <td width="94" align="center" valign="bottom" class="Verdana11B">Units</td>
    <td width="94" align="center" valign="bottom" class="Verdana11B">Disp. Fee</td>
    <td width="94" align="center" valign="bottom" class="Verdana11B">Price</td>
  </tr>
  <tr>
    <td height="37" align="center" valign="middle" class="Verdana12">
    <?php echo $row_PROCEDUR['INVDESC']; ?></td>
    <td height="37" align="center" valign="middle"><input type="text" class="Inputright" size="6" maxlength="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PROCEDUR['INVPRICE']; ?>" name="invprice" id="invprice" onkeyup="calculateprice()"/></td>
    <td height="37" align="center" valign="middle"><input type="text" class="Inputright" size="4" maxlength="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PROCEDUR['INVUNITS']; ?>" name="invunits" id="invunits" style="width:35px;"  onkeyup="calculateprice()"/></td>
    <td align="center" valign="middle"><input type="text" name="invdisp" class="Inputright" size="4" maxlength="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PROCEDUR['INVDISP']; ?>" id="invunits" style="width:35px;"  onkeyup="calculateprice()"/>
</td>
    <td align="center" valign="middle"><input type="text" class="Inputright" size="6" maxlength="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PROCEDUR['INVTOT']; ?>" name="invtot" id="invtot" readonly="readonly" /></td>
  </tr>
  <tr>
    <td height="67" colspan="5" align="center" valign="bottom" class="Verdana11Grey">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="5" align="center" class="ButtonsTable">
    <input name="save" class="button" type="submit" value="SAVE" />
    <input name="cancel" class="button" type="button" value="CLOSE" onclick="self.close();"/>    </td>
    </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
