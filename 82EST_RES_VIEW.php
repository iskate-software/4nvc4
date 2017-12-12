<?php
session_start();
require_once('../../tryconnection.php'); 
include("../../ASSETS/tax.php");

if (isset($_POST['ok']) || isset($_POST['cancel'])){
//unset($_SESSION['view']);
$closewindow='opener.document.location.reload(); self.close();';
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ESTIMATES &amp; RESERVED INVOICES VIEW</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script langauge="javascript">

function bodyonload()
{
<?php echo $closewindow; ?>
resizeTo(700,600);
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(70,30);
}

function bodyonunload()
{

}

</script>

<style type="text/css">
.style3 {color: #CC6600}
</style>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;">
<table width="700" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr class="Verdana9White" bgcolor="#000000">
  <td width="6">&nbsp;</td>
  <td width="90">Patient </td>
  <td width="54" align="center">Units</td>
  <td width="174">Item description</td>
  <td width="66" align="right">U.Price</td>
  <td width="42" align="right">Price</td>
  <td width="40" align="right">D. Fee</td>
  <td width="6" align="right">&nbsp;</td>
  <td width="142" align="left">Doctor</td>
</tr>
 <tr>
   <td colspan='9' bgcolor="#FFFFFF">
   
   <div style="height:530px; overflow:auto;">
 	<table width="700" border="0" cellpadding="0" cellspacing="0">
         <tr>
          <td height="0" width="6"></td>
          <td width="90"> </td>
          <td width="54" align="center"></td>
          <td width="174"></td>
          <td width="66" align="right"></td>
          <td width="42" align="right"></td>
          <td width="40" align="right"></td>
          <td width="6" align="right"></td>
          <td width="142" align="left"></td>
         </tr>
         
<?php foreach ($_SESSION['view'] as $key => $value) {

if ($value['INVDESCR']=="0"){
	echo '<tr bgcolor="#DBEBF0">
      	<td align="center" colspan="9"><span class="Verdana12BBlue">'.$value['INVHYPE'].'</span></td>
    	</tr>';
}
else if($value['INVDESCR']=="1"){
	echo '<tr bgcolor="#F9DEE9">
      	<td align="center" colspan="9"><span class="Verdana12BRed">Reserved invoice #'.$value['INVNO'].'</span></td>
    	</tr>';
}
else if($value['MEMO']=="1"){
	echo '<tr><td width="7" class="Verdana11">&nbsp;</td>
          <td height="15" class="Verdana11">'.$value['PETNAME'].'</td>
          <td align="right" class="Verdana11">&nbsp;&nbsp;</td>
          <td class="Verdana10" colspan="5">*'.$value['INVDESCR'].'</td>
          <td align="left"class="Verdana11">'.$value['INVDOC'].'</td>
        </tr>';
}
else if($value['INVDESCR']==$row_TAX['HTAXNAME'] || $value['INVDESCR']==$row_TAX['HOTAXNAME']){
	echo '<tr><td height="2"></td>
          <td></td>
          <td></td>
          <td bgcolor="#CCCCCC"></td>
          <td bgcolor="#CCCCCC"></td>
          <td bgcolor="#CCCCCC"></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>';
	echo '<tr><td width="7" class="Verdana11">&nbsp;</td>
          <td height="15" class="Verdana11"></td>
          <td align="center" class="Verdana11"></td>
          <td class="Verdana11">'.$value['INVDESCR'].'</td>
          <td align="right" class="Verdana11"></td>
          <td align="right" class="Verdana11">'.$value['INVTOT'].'</td>
          <td align="right" class="Verdana11"></td>
          <td align="right" class="Verdana11">&nbsp;</td>
          <td align="left"class="Verdana11"></td>
        </tr>';
}
else if($value['INVDESCR']=="PST" && $value['INVTOT']!="0.00"){
	echo '<tr><td width="7" class="Verdana11">&nbsp;</td>
          <td height="15" class="Verdana11"></td>
          <td align="center" class="Verdana11"></td>
          <td class="Verdana11">'.$value['INVDESCR'].'</td>
          <td align="right" class="Verdana11"></td>
          <td align="right" class="Verdana11">'.$value['INVTOT'].'</td>
          <td align="right" class="Verdana11"></td>
          <td align="right" class="Verdana11">&nbsp;</td>
          <td align="left"class="Verdana11"></td>
        </tr>';
}
else if($value['INVDESCR']=="TOTAL"){
	echo '<tr><td>&nbsp;</td>
          <td height="15"></td>
          <td></td>
          <td class="Verdana11B">'.$value['INVDESCR'].'</td>
          <td></td>
          <td align="right" class="Verdana11B">'.$value['INVTOT'].'</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>';
	echo '<tr><td height="2"></td>
          <td></td>
          <td></td>
          <td bgcolor="#666666"></td>
          <td bgcolor="#666666"></td>
          <td bgcolor="#666666"></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>';
	echo '<tr>
      	<td colspan="9" height="5"></td>
    	</tr>';

}
else if ($value['INVDESCR']!="PST" && $value['INVDESCR']!="TOTAL" && $value['INVDESCR']!=$row_TAX['HTAXNAME'] && $value['INVDESCR']!=$row_TAX['HOTAXNAME']){
	echo '<tr><td width="7" class="Verdana11">&nbsp;</td>
          <td height="15" class="Verdana11">'.$value['PETNAME'].'</td>
          <td align="right" class="Verdana11">';
	
		if (number_format($value['INVUNITS'],0)==$value['INVUNITS']){
		echo  number_format($value['INVUNITS'],0);
		}
		else {
		echo $value['INVUNITS'];
		}
	
	echo  '&nbsp;&nbsp;</td>
          <td class="Verdana11">'.$value['INVDESCR'].'</td>
          <td align="right" class="Verdana11">'.$value['INVPRICE'].'</td>
          <td align="right" class="Verdana11">'.$value['INVTOT'].'</td>
          <td align="right" class="Verdana11 style3">';
		  if ($value['INVDISP']!="0.00"){
	echo  $value['INVDISP'];	  
		  }
	echo '</td>
          <td align="right" class="Verdana11">&nbsp;</td>
          <td align="left"class="Verdana11">'.$value['INVDOC'].'</td>
        </tr>';
}									
}
		?>
		</table>
    </div>
</td>
</tr>
    
    <tr>
    
    <td colspan="13" align="center" valign="middle" class="ButtonsTable">
      <input name="ok" type="submit" class="button" id="ok" value="OK" />
      <input name="cancel" type="submit" class="button" id="cancel" value="CLOSE" />
     </td>
  	</tr>  
  </table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
