<?php
session_start();
require_once('../../tryconnection.php');
 
mysql_select_db($database_tryconnection, $tryconnection);

$query_COMMENTS = sprintf("SELECT * FROM ARSYSCOMM WHERE ARSYSCOMM.COMMID='%s'", $_GET['commid']);
$COMMENTS = mysql_query($query_COMMENTS, $tryconnection) or die(mysql_error());
$row_COMMENTS = mysql_fetch_assoc($COMMENTS);

$path=$_GET['path'];

if ((isset($_POST["save"])) && ($_GET["commid"]!=0)) {
$updateSQL = sprintf("UPDATE ARSYSCOMM SET COMMCODE='%s', `COMMENT`='%s', COMMTYPE='%s' WHERE COMMID='%s'",
                       $_POST['commcode'],
                       mysql_real_escape_string($_POST['comment']),
                       $_POST['commtype'],
                       $_GET['commid']);

$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
header("Location: COMMENTS_LIST.php?path=$path");
}

elseif ((isset($_POST["save"])) && ($_GET["commid"]==0)){
$insertSQL = sprintf("INSERT INTO ARSYSCOMM (COMMID, COMMCODE, `COMMENT`, COMMTYPE) VALUES ('%s', '%s', '%s', '%s')",
                       $_POST['commid'],
                       $_POST['commcode'],
                       mysql_real_escape_string($_POST['comment']),
                       $_POST['commtype']);
$Result1 = mysql_query($insertSQL, $tryconnection) or die(mysql_error());
header("Location: COMMENTS_LIST.php?path=$path");
}

elseif (isset($_POST["delete"])){
$insertSQL = sprintf("DELETE FROM ARSYSCOMM WHERE COMMID='%s'",
                       $_GET['commid']);
$Result1 = mysql_query($insertSQL, $tryconnection) or die(mysql_error());
header("Location: COMMENTS_LIST.php?path=$path");
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php if ($_GET['commid']=="0"){echo "ADD NEW";} else {echo "EDIT";} ?> SYSTEM COMMENT</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
function bodyonload()
{
}
</script>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->

  <form action="<?php echo $editFormAction; ?>" method="post" name="update_comm" style="position:absolute; top:0px; left:0px;">
  <input name="commid" type="hidden" value="<?php echo $row_COMMENTS['COMMID']; ?>" />
  <table width="733" height="553" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
    <tr>
      <td height="59" align="center" valign="bottom" class="Verdana14B"><?php if ($_GET['commid']=="0"){echo "ADD NEW";} else {echo "EDIT";} ?> SYSTEM COMMENT</td>
    </tr>
    <tr>
      <td height="157" align="center" valign="bottom">
        <table width="40%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="box" rules="none">
          <tr>
            <td height="10" colspan="2" align="right" class="Labels">&nbsp;</td>
          <td height="10" align="left" class="Labels">&nbsp;</td>
          <td height="10" align="left" class="Labels">&nbsp;</td>
        </tr>
          <tr>
            <td height="31" colspan="2" align="right" class="Labels">Comment Code</td>
          <td height="31" align="left" class="Labels">&nbsp;</td>
          <td height="31" align="left" class="Labels"><input name="commcode" type="text" class="Input" id="commcode" value="<?php echo $row_COMMENTS['COMMCODE']; ?>" size="17" maxlength="16" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        </tr>
          <tr>
            <td width="15%" height="31" align="right" valign="middle" class="Labels"><input type="radio" name="commtype" id="commtype" value="1" <?php if ($row_COMMENTS['COMMTYPE']=='1'){echo 'checked';}; ?>/></td>
          <td width="26%" height="31" align="left" valign="middle" class="Labels">Invoice </td>
          <td width="10%" height="31" align="right" valign="middle" class="Labels"><input type="radio" name="commtype" id="commtype2" value="2" <?php if ($row_COMMENTS['COMMTYPE']=='2'){echo 'checked';}; ?>/></td>
          <td width="49%" height="31" align="left" valign="middle" class="Labels">Prescription</td>
        </tr>
          <tr>
            <td height="10" align="right" valign="middle" class="Labels">&nbsp;</td>
          <td height="10" align="left" valign="middle" class="Labels">&nbsp;</td>
          <td height="10" align="right" valign="middle" class="Labels">&nbsp;</td>
          <td height="10" align="left" valign="middle" class="Labels">&nbsp;</td>
        </tr>
        </table></td>
    </tr>
    <tr>
      <td height="32" align="center" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td height="272" align="center" valign="top"><textarea name="comment" cols="65" rows="15" class="commentarea" id="comment"><?php echo $row_COMMENTS['COMMENT']; ?></textarea></td>
    </tr>
    <tr>
      <td height="35" align="center" valign="middle" class="ButtonsTable">
        <input name="save" type="submit" class="button" id="button" value="SAVE" />
        <input name="delete" type="submit" class="button" id="button2" value="DELETE" />
        <input name="button3" type="reset" class="button" id="button3" value="CANCEL" onclick="history.back()" /></td>
    </tr>
  </table>
  <input type="hidden" name="check" value="1" />
  </form>
</div>
    
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($COMMENTS);
?>
