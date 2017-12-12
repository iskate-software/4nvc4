<?php 
session_start();
require_once('../../tryconnection.php'); 

$sessionname=$_SESSION['petname'];
mysql_select_db($database_tryconnection, $tryconnection);

if (isset($_POST['save'])){
$invoicecomment=str_replace('$PETNAME', $_SESSION['petname'], $_POST['commtext']);
	$update_PETHOLD="UPDATE PETHOLD SET SUBTCOM='".mysql_real_escape_string($invoicecomment)."', PHINVNO='$_SESSION[minvno]', COMINV='$_POST[cominv]', MEDINV='$_POST[medinv]'  WHERE PHPETID='$_SESSION[patient]'";
	$PETHOLD=mysql_unbuffered_query($update_PETHOLD, $tryconnection) or die (mysql_error());
$closewindow='self.close() ;';
}

$query_PETHOLD = "SELECT * FROM PETHOLD WHERE PHPETID=$_SESSION[patient]";
$PETHOLD = mysql_query($query_PETHOLD, $tryconnection) or die(mysql_error());
$row_PETHOLD = mysql_fetch_assoc($PETHOLD);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>WRITE COMMENT</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload()
{
//<?php echo $closewindow; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+140,toppos+220);
document.forms[0].commtext.focus();
document.forms[0].petname.value=sessionStorage.petname;
}

function OnClose()
{
self.close();
}

function bodyonunload(){
<?php echo $closewindow; ?>
//opener.document.location.reload(); 
}


function countchar(){
var chars=document.forms[0].commtext.value.length;
document.getElementById('maxnum').innerText=chars;
	if (chars>1000){
	alert('I am sorry, but your comment is too long. It\'s not my fault.');
	document.forms[0].commtext.value=document.forms[0].commtext.value.substr(0,999);	
	}
document.getElementById('linstructions').innerText=document.forms[0].commtext.value;
}

</script>

<style type="text/css">
<!--
.Labels2{
font-family:Arial, Helvetica, sans-serif;
}
-->
</style>



<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="tff" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;" onsubmit="replacexy();">

<table id="linstructionsuser" width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td height="40" align="center" valign="bottom" class="Verdana11B">Write comment for : <script type="text/javascript">document.write(sessionStorage.petname);</script><?php //echo $_SESSION['petname']; ?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="view" id="view" value="VIEW" onclick="window.open('../COMMENTS/COMMENTS_LIST.php?path=LABEL','_blank')" /></td>
    </tr>
  <tr>
    <td align="center">
    <input type="hidden" name="petname" id="petname" value="<?php //echo $_SESSION['petname']; ?>" />
    <input type="hidden" name="tautocomm" id="tautocomm" size="6" value="" />
    <textarea name="commtext" id="commtext" cols="48" rows="5" wrap="virtual" class="commentarea" onkeyup="countchar()"><?php echo $row_PETHOLD['SUBTCOM']; ?></textarea>    </td>
  </tr>
  <tr>
    <td height="15" align="center" class="Verdana11Grey"># of characters: <span id="maxnum"></span> (max 1000)&nbsp;&nbsp;<input type="button" value="CLEAR" onclick="document.tff.commtext.value='';"  />
    </td>
    </tr>
  <tr>
    <td height="40" align="center"><label>
      <input type="checkbox" name="cominv" id="cominv" value="1" checked="checked"/>
      On Invoice
      <input type="checkbox" name="medinv" id="medinv" value="1" checked="checked"/>
      In History</label></td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input type="submit" class="button" name="save" id="save" value="SAVE" />
    <input type="button" class="button" name="button2" id="button2" value="CLOSE" onclick="self.close();" /></td>
  </tr>
</table>


<input type="hidden" name="check" value="1"  />


</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
