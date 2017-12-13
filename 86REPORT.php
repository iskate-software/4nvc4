<?php
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$spec=$_GET['species'];
$GETcategory=implode(",",$_POST['report']);

$query_SPECIES = "SELECT DISTINCT TCATGRY, TTYPE FROM VETCAN WHERE TSPECIES='$spec' ORDER BY TCATGRY ASC";
$SPECIES = mysql_query($query_SPECIES, $tryconnection) or die(mysql_error());
$row_SPECIES = mysqli_fetch_assoc($SPECIES);


if (isset($_POST['ok'])){
$openwindow="window.open('PRINT_REPORT.php?species=$spec&categories=$GETcategory','_blank')";
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>COMPILE TREATMENT FEE FILE REPORT</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload()
{
<?php echo $openwindow; ?>
//window.moveBy(0,140);
}

function OnClose()
{
self.close();
}

function bodyonunload()
{

}

function selectall(){
var i;
if (document.forms[0].selall.value!="Select All"){
	for (i=0; i<document.forms[0].checkbox.length; i++){
	document.forms[0].checkbox[i].checked=false;
	document.forms[0].selall.value="Select All";	
	}
}
else if (document.forms[0].selall.value="Select All"){
	for (i=0; i<document.forms[0].checkbox.length; i++){
	document.forms[0].checkbox[i].checked=true;
	document.forms[0].selall.value="Unselect All";	
	}
}
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="xreport" id="xreport" style="position:absolute; top:0px; left:0px;">

<table width="380" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td height="48" colspan="3" align="center" class="Verdana12B">
    Report compilation for: <?php if ($_GET['species']=='1'){echo "Canine";} else if ($_GET['species']=='2'){echo "Feline";} else if ($_GET['species']=='3'){echo "Equine";}else if ($_GET['species']=='4'){echo "Bovine";}else if ($_GET['species']=='5'){echo "Caprine";}else if ($_GET['species']=='6'){echo "Porcine";}else if ($_GET['species']=='7'){echo "Avian";}else if ($_GET['species']=='8'){echo "Other";}; ?>    </td>
    </tr>
  <tr>
    <td colspan="3" align="center">
      <input type="button" name="selall" id="selall" value="Select All" onclick="selectall();" />    
    </tr>
      <?php do { ?>
  <tr>
    <td height="20" width="69" align="center" valign="bottom"></td>
    <td width="242" align="left" valign="top" class="Verdana11">
      <label><input type="checkbox" name="report[]" id="checkbox" value="<?php echo $row_SPECIES['TCATGRY'] ?>" /><?php echo $row_SPECIES['TTYPE'] ?></label>
    </td>
    <td width="69">&nbsp;</td>
  </tr>
      <?php } while ($row_SPECIES = mysqli_fetch_assoc($SPECIES)); ?>
  <tr>
    <td height="26" colspan="3" align="center">    </tr>
  <tr>
    <td colspan="3" align="center" class="ButtonsTable">
    <input name="ok" class="button" type="submit" value="OK"/>    
    <input name="cancel" class="button" type="button" value="CLOSE" onclick="self.close();"/>    </td>
    </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
