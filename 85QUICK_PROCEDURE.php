<?php
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$spec=$_GET['species'];

$query_HXFILTER = "SELECT * FROM HXFILTER WHERE HXCNAME!='Diagnostics'";
$HXFILTER = mysql_query($query_HXFILTER, $tryconnection) or die(mysql_error());
$row_HXFILTER = mysql_fetch_assoc($HXFILTER);

if (isset($_POST['save']))
{
$query_INSERTPROCEDUR = "INSERT INTO PROCEDUR (PROCODE, `PROCEDURE`, FEEFILE) VALUES ('$_POST[procode]','$_POST[procedure]', $spec)";
$INSERTPROCEDUR = mysql_query($query_INSERTPROCEDUR, $tryconnection) or die(mysql_error());
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CATEGORIES MAINTENANCE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
document.add_procedure.procode.focus();
}

function bodyonunload(){
opener.document.location.reload();
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
<form method="post" action="" name="add_procedure" id="add_procedure" class="FormDisplay" style="position:absolute; top:0px; left:0px;">

<table width="380" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td height="52" align="center" class="Verdana13B">Add New Procedure: 
      <?php if ($spec=='1') {echo "Canine";} else if ($spec=='2') {echo "Feline";} ?></td>
    </tr>
  <tr>
    <td align="center" valign="top">Procedure Code<br />
      <input type="text" class="Input" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="" name="procode" id="procode" />
      <br />
      <br />
     Name
      <br />
      <input type="text" class="Input" size="18" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="" name="procedure" id="procedure" />
      <br />
      <br />
     HX Filter
      <br />
      <select name="thxfil">
      <option value=""></option>
       	<?php do {
		echo '<option id="'.$row_HXFILTER['HXCAT'],'" value="'.$row_HXFILTER['HXCAT'],'">'.$row_HXFILTER['HXCNAME'].'</option>';
		} while ($row_HXFILTER = mysql_fetch_assoc($HXFILTER));
		 ?>
      </select>	</td>
    </tr>
  <tr>
    <td height="42" align="center" valign="bottom" class="Verdana11Grey">&nbsp;</td>
    </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input name="save" class="button" type="submit" value="SAVE" />
    <input name="cancel" class="button" type="button" value="CLOSE" onclick="self.close();"/>    </td>
    </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
