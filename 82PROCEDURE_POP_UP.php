<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PROCEDURE INVOICING FILE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script langauge="javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+90,toppos+80);
document.search_procode.procode.focus();
document.search_procode.submit();
}

function bodyonunload()
{
opener.document.location.reload();
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form  action="PROCEDURE_POP_UP_IFRAME.php" method="get" target="proclist" name="search_procode" id="" style="position:absolute; top:0px; left:0px;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr>
      <td colspan="5" align="left" valign="top">
        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows">
          <tr>
            <td colspan="2" bgcolor="#000000" class="Verdana11Bwhite" id="item" onclick="setsorting('ITEM',this.id);" onmouseover="document.getElementById(this.id).style.cursor='pointer';">&nbsp;Procedure Code</td>
          </tr>
          <tr>
            <td height="25">
            <input name="procode" type="text" class="Input" id="procode" size="35"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['procode']; ?>"/>
            </td>
            <td align="right">&nbsp;</td>
          </tr>
    <tr>
      <td colspan="5" align="left" valign="top">
      <iframe name="proclist" id="proclist" scrolling="no" height="470" width="600" frameborder="0"></iframe>
      </td>
    </tr>
      </table>
      
      </td>
    </tr>
    
    
    
    
    <tr>
      <td colspan="5" align="center" valign="middle"class="ButtonsTable">
        <input class="button" type="reset" name="cancel" value="CANCEL" onclick="self.close();" />  </td>
    </tr>	
  </table>
<input type="hidden" value="<?php echo $_GET['pettype']; ?>" name="pettype"  />
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>