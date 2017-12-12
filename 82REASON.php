<?php 
session_start();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>REASON</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+237,toppos+180);
document.formreason.reason.focus();
}

function bodyonunload()
{
opener.document.routine.ponum.value=document.formreason.reason.value;
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="formreason" id="formreason" class="FormDisplay" style="position:absolute; top:0px; left:0px;">
<table width="300" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td height="47">&nbsp;</td>
  </tr>
  <tr>
    <td height="119" align="center" valign="top" class="Verdana11"><label>Reason
        <input name="reason" type="text" class="Input" id="reason" size="20" maxlength="20" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $_SESSION['petname']; ?>" />
    </label></td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input name="button" type="button" class="button" id="button" value="SAVE" onclick="self.close();" />
    <input name="button2" type="reset" class="hidden" id="button2" value="CLOSE" onclick="self.close();" /></td>
  </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
