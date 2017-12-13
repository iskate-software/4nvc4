<?php
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$commcode=$_GET['commcode'];

if ($_GET['display']=="INVOICE"){
$filter="AND COMMTYPE='1'";
}
elseif ($_GET['display']=="PRESCRIPTION"){
$filter="AND COMMTYPE='2'";
}
else {$filter="";}

$query_COMMENTS = "SELECT * FROM ARSYSCOMM WHERE COMMCODE LIKE '$commcode%' ".$filter." ORDER BY COMMCODE ASC";
$COMMENTS = mysql_query($query_COMMENTS, $tryconnection) or die(mysql_error());
$row_COMMENTS = mysqli_fetch_assoc($COMMENTS);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/IFRAME.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>DV MANAGER MAC</title>

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<style type="text/css">
<!--
#WindowBody {
	position:absolute;
	top:0px;
	width:733px;
	height:553px;
	z-index:1;
	font-family: "Verdana";
	outline-style: ridge;
	outline-color: #FFFFFF;
	outline-width: medium;
	background-color: #FFFFFF;
	left: 0px;
	color: #000000;
	text-align: left;
}
-->
</style>

</head>
<!-- InstanceBeginEditable name="EditRegion2" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>

<script type="text/javascript">
function PerformComment(code,commid,comtext)
{
var path="<?php echo $_GET['path']; ?>";
if (path=="TFF" || path=="LABEL"){
parent.window.commlist.comcode.value=code;
parent.window.commlist.commtext.value=comtext;
parent.window.self.close();
	}
else if (path=="DIRECTORY"){
window.open("UPDATE_COMMENT.php?commid="+commid+"&path=<?php echo $_GET['path']; ?>","_parent");
	}
}

</script>

<style type="text/css">
#xcomlist {
display:none;
}
</style>

<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->
<form action="" method="post" name="comm_list" style="position:absolute; top:0px; left:0px;">
<table id="xcomlist" width="717" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows" bgcolor="#FFFFFF">
	 <tr height="10" >
      <td width="67" align="left" bgcolor="#000000" class="Verdana11Bwhite">Code</td>
        <td width="553" align="left" bgcolor="#000000" class="Verdana11Bwhite">Comment</td>
        <td align="right" bgcolor="#000000" class="Verdana11Bwhite"></td>
    </tr>
</table>

<table width="717" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows" bgcolor="#FFFFFF">
<?php do { ?>
  <tr onclick="PerformComment('<?php echo $row_COMMENTS['COMMCODE']; ?>','<?php echo $row_COMMENTS['COMMID']; ?>','<?php echo mysql_real_escape_string($row_COMMENTS['COMMENT']); ?>');" id="<?php echo $row_COMMENTS['COMMID']; ?>" onmouseover="CursorToPointer(this.id)">
    <td width="150" align="left" valign="top" class="Labels">&nbsp;<?php echo $row_COMMENTS['COMMCODE']; ?></td>
    <td colspan="2" align="left" valign="top" class="Labels"><?php echo $row_COMMENTS['COMMENT']; ?></td>
    <td width="30" align="center" valign="top" class="Andale13B"><?php if ($row_COMMENTS['COMMTYPE']=='2'){echo '*';} else {echo '';}?>    </td>
  </tr>
  <?php } while ($row_COMMENTS = mysqli_fetch_assoc($COMMENTS)); ?>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($COMMENTS);
?>
