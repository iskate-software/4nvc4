<?php
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);
$spec=$_GET['species'];
$POSTcategory=$_POST['category'];
$GETcategory=$_GET['category'];

$query_SPECIES = "SELECT DISTINCT TCATGRY, TTYPE FROM VETCAN WHERE TSPECIES='$spec' ORDER BY TCATGRY ASC";
$SPECIES = mysqli_query($tryconnection, $query_SPECIES) or die(mysqli_error($mysqli_link));
$row_SPECIES = mysqli_fetch_assoc($SPECIES);

$query_NAME = "SELECT TTYPE, THXCAT FROM VETCAN WHERE TSPECIES='$spec' AND TCATGRY='$GETcategory' ORDER BY TCATGRY ASC";
$NAME = mysqli_query($tryconnection, $query_NAME) or die(mysqli_error($mysqli_link));
$row_NAME = mysqli_fetch_assoc($NAME);

$query_HXFILTER = "SELECT * FROM HXFILTER WHERE HXCNAME!='Diagnostics'";
$HXFILTER = mysqli_query($tryconnection, $query_HXFILTER) or die(mysqli_error($mysqli_link));
$row_HXFILTER = mysqli_fetch_assoc($HXFILTER);


//UPDATE
if (isset($_POST['save']))
{
$query_UPDATEVETCAN = "UPDATE VETCAN SET TCATGRY='0', TTYPE='$_POST[ttype]', THXCAT='$_POST[thxfil]' WHERE TCATGRY='$GETcategory' AND TSPECIES='$spec'";
$UPDATEVETCAN = mysqli_query($tryconnection, $query_UPDATEVETCAN) or die(mysqli_error($mysqli_link));

	if ($POSTcategory<$GETcategory){
	$query_UPDATESEQ = "UPDATE VETCAN SET TCATGRY=TCATGRY+1 WHERE TCATGRY>='$POSTcategory' AND TCATGRY<'$GETcategory'  AND TSPECIES='$spec'";
	}
	else {
	$query_UPDATESEQ = "UPDATE VETCAN SET TCATGRY=TCATGRY-1 WHERE TCATGRY<='$POSTcategory' AND TCATGRY>'$GETcategory' AND TSPECIES='$spec'";
	}
$UPDATESEQ = mysqli_query($tryconnection, $query_UPDATESEQ) or die(mysqli_error($mysqli_link));

$query_UPDATEVETCAN = "UPDATE VETCAN SET TCATGRY='$POSTcategory' WHERE TCATGRY='0'";
$UPDATEVETCAN = mysqli_query($tryconnection, $query_UPDATEVETCAN) or die(mysqli_error($mysqli_link));
$refreshwindow="self.location.reload();";
}

//DELETE
elseif (isset($_POST['delete']))
{
$query_UPDATEVETCAN = "UPDATE VETCAN SET TCATGRY='0' WHERE TCATGRY='$GETcategory'  AND TSPECIES='$spec'";
$UPDATEVETCAN = mysqli_query($tryconnection, $query_UPDATEVETCAN) or die(mysqli_error($mysqli_link));

$query_UPDATESEQ = "UPDATE VETCAN SET TCATGRY=TCATGRY-1 WHERE TCATGRY>'$GETcategory' AND TSPECIES='$spec'";
$UPDATESEQ = mysqli_query($tryconnection, $query_UPDATESEQ) or die(mysqli_error($mysqli_link));
$query_DELETEVETCAN = "DELETE FROM VETCAN WHERE TCATGRY='0'";
$DELETEVETCAN = mysqli_query($tryconnection, $query_DELETEVETCAN) or die(mysqli_error($mysqli_link));
$refreshwindow="self.location.reload();";
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
self.location='RENUMBER_CATEGORY.php?species=<?php echo $_GET['species']; ?>&category=' + category;
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
    <td height="52" colspan="2" align="center" class="Verdana12B">Edit categories</td>
    </tr>
  <tr>
    <td width="156" align="center" valign="top">Sequence<br />
      <input type="text" class="Inputright" size="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php if ($_GET['category']=='i'){echo "";} else{echo $_GET['category'];} ?>" name="category" id="category" />
      <br />
      <br />
     Name
      <br />
      <input type="text" class="Input" size="18" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php if ($_GET['category']=='i'){echo "";} else{echo $row_NAME['TTYPE'];} ?>" name="ttype" id="ttype" /> 
      <br />
      <br />
     HX Filter
      <br />
      <select name="thxfil">
      <option value=""></option>
       	<?php do {
		echo '<option id="'.$row_HXFILTER['HXCAT'],'" value="'.$row_HXFILTER['HXCAT'],'">'.$row_HXFILTER['HXCNAME'].'</option>';
		} while ($row_HXFILTER = mysqli_fetch_assoc($HXFILTER));
		 ?>
      </select>
     </td>
    <td width="224">
	<select name="catlist" class="SelectList" multiple="multiple" id="catlist" onchange="callcategory();">
     <?php do { ?>
     <option value="<?php echo $row_SPECIES['TCATGRY']; ?>">&nbsp;<?php if ($row_SPECIES['TCATGRY']<10){echo "&nbsp;&nbsp;";} echo $row_SPECIES['TCATGRY']." ".$row_SPECIES['TTYPE']; ?></option>
   
    <?php } while ($row_SPECIES = mysqli_fetch_assoc($SPECIES)); ?>
    </select>    </td>
  </tr>
  <tr>
    <td height="42" colspan="2" align="center" valign="bottom" class="Verdana11Grey">To revert the changes you have to renumber again.</td>
    </tr>
  <tr>
    <td colspan="2" align="center" class="ButtonsTable">
    <input name="finished" class="button" type="button" value="FINISHED" onclick="self.close();"/>    
    <input name="save" class="button" type="submit" value="SAVE" />    
    <input name="delete" class="button" type="submit" value="DELETE" />    
    <input name="cancel" class="button" type="button" value="CLOSE" <?php if ($_GET['category']!='i'){echo "disabled";} ?> onclick="self.close();"/>    </td>
    </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
