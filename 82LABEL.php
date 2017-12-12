<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$query_CRITDATA = "SELECT * FROM CRITDATA";
$CRITDATA = mysql_query($query_CRITDATA, $tryconnection) or die(mysql_error());
$row_CRITDATA = mysql_fetch_assoc($CRITDATA);

$lastitem=(count($_SESSION['invline'])-1);

if (isset($_POST['check']) && !empty($_POST['commtext'])){
$_SESSION['invline'][$lastitem]['LCOMMENT']=$_POST['commtext']/*." (exp.".$_GET['expdate'].")"*/;
$_SESSION['invline'][$lastitem]['LCODE']=$_POST['tautocomm'];
$closewin="document.getElementById('linstructionsprint').style.display=''; document.getElementById('tff').style.display='none';";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>LABEL INSTRUCTIONS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload()
{
<?php echo $closewin; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+120,toppos+180);
window.resizeTo(500,300) ;
document.tff.commtext.focus();
document.getElementById('maxnum').innerText='0';
document.getElementById('linstructions').innerText="<?php echo $_SESSION['invline'][$lastitem]['LCOMMENT']; ?>";
}

function OnClose()
{
self.close();
}

function bodyonunload(){
opener.document.location="REGULAR_INVOICING.php?subcat=i&product=j"; 
}

function replacexy(){
var instructions=document.tff.commtext.value;
var xxx=document.tff.units.value;
var yyy=document.tff.days.value;
instructions=instructions.replace("XXX",xxx);
instructions=instructions.replace("YYY",yyy);
document.tff.commtext.value=instructions;
document.getElementById('linstructions').innerText=document.forms[0].commtext.value;
}


function countchar(){
var chars=document.forms[0].commtext.value.length;
document.getElementById('maxnum').innerText=chars;
	if (chars>255){
	alert('I am sorry, but your label instructions are too long. It\'s not my fault.');
	document.forms[0].commtext.value=document.forms[0].commtext.value.substr(0,254);	
	}
document.getElementById('linstructions').innerText=document.forms[0].commtext.value;
}

function savelabel(){
//opener.document.reg_invoicing.autocomm.value=document.tff.tautocomm.value;
//opener.document.reg_invoicing.commtext.value=document.tff.commtext.value;
//document.getElementById('linstructionsprint').style.display='';
//document.getElementById('tff').style.display='none';
//window.print();
//document.getElementById('linstructionsprint').style.display='none';
tff.submit();
}

</script>

<style type="text/css">
<!--
.Labels2{
font-family:Arial, Helvetica, sans-serif;
}
.commentarea{
font-family:Arial, Helvetica, sans-serif;
}
#apDiv1 {
	position:absolute;
	width:306px;
	height:38px;
	z-index:1;
	left: 94px;
	top: 137px;
	border:solid black thin;
}
-->
</style>
<!-- InstanceEndEditable -->
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="tff" id="tff" class="FormDisplay" style="position:absolute; top:0px; left:0px;" onsubmit="replacexy();">
<table id="linstructionsuser" width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <th width="175" height="46" align="right" valign="bottom">Label instructions: </th>
    <th colspan="2" align="left" valign="bottom">&nbsp;</th>
    <th width="155" align="center" valign="bottom">
    <input type="button" name="view" id="view" value="VIEW" onclick="window.open('../COMMENTS/COMMENTS_LIST.php?path=LABEL&display=PRESCRIPTION','_blank')" /></th>
  </tr>
  <tr>
    <td colspan="4" align="center">
    <input type="hidden" name="petname" id="petname" />
    <input type="hidden" name="tautocomm" id="tautocomm" size="6" value="<?php echo $_SESSION['invline'][$lastitem]['LCODE']; ?>" />
    <textarea name="commtext" id="commtext" cols="48" rows="5" wrap="virtual" class="commentarea" onkeyup="countchar()"><?php echo $_SESSION['invline'][$lastitem]['LCOMMENT']; ?></textarea>    </td>
  </tr>
  <tr>
    <td height="15" align="right">&nbsp;</td>
    <td height="15" align="center">&nbsp;</td>
    <td height="15" colspan="2" align="center" class="Verdana11Grey"># of characters: <span id="maxnum"></span> (max 255)</td>
    </tr>
  <tr>
    <td height="40" align="right"><label>Units
        <input name="units" type="text" class="Inputright" id="units" size="5" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value=""/>
    </label></td>
    <td width="36" height="40" align="center">&nbsp;</td>
    <td width="134" height="40" align="center"><label>Days
        <input name="days" type="text" class="Inputright" id="days" size="5" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value=""/>
    </label></td>
    <td height="40" align="left"><input type="button" name="button" id="button" value="OK" onclick="replacexy();" /></td>
  </tr>
  <tr>
    <td height="35" colspan="4" align="center"></td>
  </tr>
  <tr>
    <td colspan="4" align="center" class="ButtonsTable">
    <input type="button" class="button" name="button" id="button" value="SAVE" onclick="savelabel();" />
    <input type="button" class="button" name="button2" id="button2" value="CLOSE" onclick="self.close();" /></td>
  </tr>
</table>
<input type="hidden" name="check" value="1"  />
</form>

<!--<embed id="DYMOLabelPlugin" width="300" height="200" type="application/x-dymolabel">HELLO</embed>-->
<table id="linstructionsprint" style="" width="305" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" onclick="">
  <tr>
    <td height="5" colspan="2"></td>
    </tr>
  <tr>
    <td width="5" rowspan="6"></td>
    <td width="300" align="left" class="Labels2">
    <?php echo $row_CRITDATA['HOSPNAME']." (".$row_CRITDATA['HPACD'].")".$row_CRITDATA['HPPHONE']; ?>    </td>
    </tr>
  <tr>
    <td align="left" class="Labels2">
    <?php echo $_SESSION['doctor'].'&nbsp;&nbsp;' .
    $row_CRITDATA['HSTREET'].','.$row_CRITDATA['HCITY'] ;?>    </td>
    </tr>
  <tr>
    <td align="left" class="Labels2">
    Patient: <?php echo $_GET['pet']." ".date("m/d/Y")."<br />"; ?>
    </td>
  </tr>
  <tr>
    <td align="left" class="Labels2"><?php echo $_GET['labelunits']." ".$_GET['drug']; ?></td>
    </tr>
  <tr>
    <td align="left" class="Labels2">
    <span id="linstructions" style="width:10px;"></span>
    </td>
    </tr>
  <tr>
    <td align="left" class="Labels2">KEEP OUT OF REACH OF CHILDREN.&nbsp;&nbsp;&nbsp;&nbsp;<?php if ($_GET['expdate'] <> '00/00/0000') {echo 'Exp.'. $_GET['expdate']; }?></td>
    </tr>
</table>
<!--<input type="button" name="button3" id="button3" value="print" onclick="DYMOLabelPlugin.Paste();"/>-->

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
