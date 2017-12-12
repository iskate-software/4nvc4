<?php
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$spec=$_GET['species'];
$POSTcategory=$_POST['category'];
$GETcategory=$_GET['category'];

$query_SPECIES = "SELECT DISTINCT `PROCEDURE`, PROCODE FROM PROCEDUR WHERE FEEFILE='$spec'";
$SPECIES = mysql_query($query_SPECIES, $tryconnection) or die(mysql_error());
$row_SPECIES = mysql_fetch_assoc($SPECIES);

$query_NAME = "SELECT `PROCEDURE`, PROCODE, INVHXCAT FROM PROCEDUR WHERE FEEFILE='$spec' AND PROCODE='$GETcategory'";
$NAME = mysql_query($query_NAME, $tryconnection) or die(mysql_error());
$row_NAME = mysql_fetch_assoc($NAME);

$query_HXFILTER = "SELECT * FROM HXFILTER WHERE HXCNAME!='Diagnostics'";
$HXFILTER = mysql_query($query_HXFILTER, $tryconnection) or die(mysql_error());
$row_HXFILTER = mysql_fetch_assoc($HXFILTER);


//UPDATE
if (isset($_POST['save']))
{
$query_UPDATEPROCEDUR = "UPDATE PROCEDUR SET `PROCEDURE`='$_POST[procedure]', PROCODE='$_POST[procode]', THXCAT='$_POST[thxfil]' WHERE PROCODE='$GETcategory' AND FEEFILE='$spec'";
$UPDATEPROCEDUR = mysql_query($query_UPDATEPROCEDUR, $tryconnection) or die(mysql_error());

$refreshwindow="self.location.reload();";
}

//DELETE
elseif (isset($_POST['delete']))
{
$query_DELETEPROCEDUR = "DELETE FROM PROCEDUR WHERE PROCODE='$GETcategory'";
$DELETEPROCEDUR = mysql_query($query_DELETEPROCEDUR, $tryconnection) or die(mysql_error());
$refreshwindow="self.location.reload();";
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PROCEDURES MAINTENANCE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload()
{
<?php echo $refreshwindow; ?>
//window.moveBy(0,140);
document.forms[0].category.focus();
var loc=<?php echo $_GET['category']; ?>;
var i=loc-1;
var hxcat='<?php echo $row_NAME['THXCAT']; ?>';
	{
	document.forms[0].catlist.options[i].selected="selected";
	document.getElementById(hxcat).selected="selected";
	}
}

function OnClose()
{
self.close();
}

function bodyonunload()
{

}

function callcategory()
{
var category=document.forms[0].catlist.value;
self.location='RENUMBER_PROCEDURE.php?species=<?php echo $_GET['species']; ?>&category=' + category;
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
<form method="post" action="" name="" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;">

<table width="380" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td height="52" colspan="2" align="center" class="Verdana13B">Edit procedures: <?php if ($spec=='1') {echo "Canine";} else if ($spec=='2') {echo "Feline";} ?></td>
    </tr>
  <tr>
    <td width="156" align="center" valign="top">Procedure Code<br />
      <input type="text" class="Input" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php if ($_GET['category']=='i'){echo "";} else{echo $_GET['category'];} ?>" name="procode" id="procode" />
      <br />
      <br />
     Name
      <br />
      <input type="text" class="Input" size="18" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php if ($_GET['category']=='i'){echo "";} else{echo $row_NAME['PROCEDURE'];} ?>" name="procedure" id="procedure" /> 
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
      </select>
     </td>
    <td width="224">
	<select name="catlist" class="SelectList" multiple="multiple" id="catlist" onchange="callcategory();">
     <?php do { ?>
     <option value="<?php echo $row_SPECIES['PROCODE']; ?>">&nbsp;<?php echo $row_SPECIES['PROCODE']." ".$row_SPECIES['PROCEDURE']; ?></option>
   
    <?php } while ($row_SPECIES = mysql_fetch_assoc($SPECIES)); ?>
    </select>    </td>
  </tr>
  <tr>
    <td height="42" colspan="2" align="center" valign="bottom" class="Verdana11Grey"></td>
    </tr>
  <tr>
    <td colspan="2" align="center" class="ButtonsTable">
    <input name="save" class="button" type="submit" value="SAVE" />    
    <input name="delete" class="button" type="submit" value="DELETE" />    
    <input name="cancel" class="button" type="button" value="CLOSE" onclick="self.close();"/>    </td>
    </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
