<?php 
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>IN-LINE NOTE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+100,toppos+175);
document.in_line_note.inlinenote.focus();
document.in_line_note.inlinenote.value=opener.document.reg_invoicing.inlinenote.value;
}

function OnClose()
{
self.close();
}

function bodyonunload(){
//opener.document.location.reload(); 
}


function countchar(){
var chars=document.forms[0].inlinenote.value.length;
document.getElementById('maxnum').innerText=chars;
	if (chars>512){
	alert('I am sorry, but your note is too long. It\'s not my fault.');
	document.forms[0].inlinenote.value=document.forms[0].inlinenote.value.substr(0,254);	
	}
document.getElementById('linstructions').innerText=document.forms[0].inlinenote.value;
}

function savenote(){
opener.document.reg_invoicing.inlinenote.value=document.in_line_note.inlinenote.value;
self.close();
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
<form method="post" action="" name="in_line_note" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;" onsubmit="replacexy();">

<table id="linstructionsuser" width="700" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td height="40" align="center" valign="bottom" class="Verdana12B">Write In-Line comment: </td>
    </tr>
  <tr>
    <td align="center">
    <textarea name="inlinenote" id="inlinenote" cols="80" rows="5" wrap="virtual" class="commentarea" onkeyup="countchar()"></textarea>    </td>
  </tr>
  <tr>
    <td height="15" align="center" class="Verdana11Grey"># of characters: <span id="maxnum"></span> (max 512)</td>
    </tr>
  <tr>
    <td height="35" align="center"></td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input type="button" class="button" name="button" id="button" value="SAVE" onclick="savenote();" />
    <input type="reset" class="button" name="cancel" id="cancel" value="CANCEL" onclick="self.close();" /></td>
  </tr>
</table>


<input type="hidden" name="check" value="1"  />


</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
