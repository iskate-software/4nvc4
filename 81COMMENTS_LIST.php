<?php 
session_start();
require_once('../../tryconnection.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>SYSTEM COMMENTS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
function bodyonload(){
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+25,toppos+0);
document.commlist.commcode.focus();
}

function bodyonunload()
{
opener.document.tff.tautocomm.value=document.commlist.comcode.value;
var commtext=document.commlist.commtext.value;
var petname=opener.document.tff.petname.value;
commtext=commtext.replace('$PETNAME',petname);
commtext=commtext.replace('$PETNAME',petname);
commtext=commtext.replace('$PETNAME',petname);
commtext=commtext.replace('$PETNAME',petname);
commtext=commtext.replace('$PETNAME',petname);
opener.document.tff.commtext.value=opener.document.tff.commtext.value+'*'+commtext;
}

function bodyonblur()
{
}

</script>




<style type="text/css">
.Customizedbutton {
	font-family: Verdana;
	font-size: 20px;
	width: 110px;
	height: 27px;
	margin-left: 5px;
	margin-right: 5px;
}


</style>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
  <form action="COMMENT_LIST_IFRAME.php" target="list" method="get" name="commlist" style="position:absolute; top:0px; left:0px;">
  <input type="hidden" name="comcode" id="comcode" value="" />
  <input type="hidden" name="display" id="display" value="<?php echo $_GET['display']; ?>" />
  <input type="hidden" name="commtext" value="" />
  <input type="hidden" name="path" value="<?php echo $_GET['path']; ?>" />
  <table width="733" height="553" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr height="10">
      <td width="67" align="left" bgcolor="#000000" class="Verdana11Bwhite">Code</td>
        <td width="553" align="left" bgcolor="#000000" class="Verdana11Bwhite">Comment</td>
        <td align="right" bgcolor="#000000" class="Verdana11Bwhite"></td>
        <td width="88" align="right" bgcolor="#000000" class="Verdana11Bwhite">&nbsp;</td>
    </tr>
    <tr>
      <td width="67" align="left"><input name="commcode" type="text" class="Input" id="commcode" size="17" maxlength="16" onkeyup="this.form.submit()" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td width="553" align="left"></td>
        <td align="right"></td>
        <td width="88" align="right">&nbsp;</td>
      </tr>
    
    <tr>
      <td height="100%" align="left" valign="top" colspan="4">
        
        <iframe name="list" scrolling="auto" height="100%" width="100%" frameborder="0" src="COMMENT_LIST_IFRAME.php?path=<?php echo $_GET['path']; ?>&display=<?php echo $_GET['display']; ?>" ></iframe>  </td>
      </tr>
    <tr>
      <td height="35" align="center" valign="middle" class="ButtonsTable" colspan="4">
        <input name="button" type="button" class="button" id="button" value="ADD" onclick="window.open('UPDATE_COMMENT.php?commid=0&path=<?php echo $_GET['path']; ?>','_self')"/>
        <input name="button2" type="button" class="Customizedbutton" id="button2" value="ALL" onclick="self.location='COMMENTS_LIST.php?display=ALL&path=<?php echo $_GET['path']; ?>'" title="Display all comments" <?php if ( $_GET['display']=="ALL"){echo "disabled";} ?>/>
        
        <input name="button4" type="button" class="Customizedbutton" id="button4" value="INVOICE" onclick="window.open('COMMENTS_LIST.php?display=INVOICE&path=<?php echo $_GET['path']; ?>','_self')" title="Display invoice comments only" <?php if ( $_GET['display']=="INVOICE"){echo "disabled";} ?>/>
        <input name="button5" type="button" class="Customizedbutton" id="button5" value="*PRESCRIPTION" onclick="window.open('COMMENTS_LIST.php?display=PRESCRIPTION&path=<?php echo $_GET['path']; ?>','_self')" title="Display prescription comments only" <?php if ( $_GET['display']=="PRESCRIPTION"){echo "disabled";} ?>/>
        <input name="button6" type="button" class="button" id="button6" value="PRINT" onclick="window.list.print();"/>
      <input name="button3" type="reset" class="button" id="button3" value="CLOSE" onclick="self.close();" />     	</td>
      </tr>
    </table>
    </form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($commlist);
?>
