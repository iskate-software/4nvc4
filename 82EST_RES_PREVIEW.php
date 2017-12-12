<?php
session_start();
unset($_SESSION['view']);
require_once('../../tryconnection.php'); 

$psex=$_GET['psex'];
$pdob=$_GET['pdob'];
$llocalid=$_GET['llocalid'];
$pettype=$_GET['pettype'];

mysql_select_db($database_tryconnection, $tryconnection);

$query_ESTHOLD = "SELECT DISTINCT INVHYPE, DATE_FORMAT(ESTEXP, '%m/%d/%Y') AS ESTEXP FROM ESTHOLD WHERE INVCUST=$_SESSION[client]";
$ESTHOLD = mysql_query($query_ESTHOLD, $tryconnection) or die(mysql_error());
$row_ESTHOLD = mysql_fetch_assoc($ESTHOLD);
$totalRows_ESTHOLD = mysql_num_rows($ESTHOLD);

$query_INVHOLD = "SELECT INVNO, INVHYPE, DATE_FORMAT(DATETIME, '%m/%d/%Y') AS DATETIME FROM INVHOLD WHERE INVCUST=$_SESSION[client]";
$INVHOLD = mysql_query($query_INVHOLD, $tryconnection) or die(mysql_error());
$row_INVHOLD = mysql_fetch_assoc($INVHOLD);
$totalRows_INVHOLD = mysql_num_rows($INVHOLD);

if (isset($_POST['view'])){
$_SESSION['view'] = array();
	foreach ($_POST['estres'] as $value){
	if ($value!='0'){
	$query_ESTHOLD = "SELECT *, DATE_FORMAT(ESTEXP, '%m/%d/%Y') AS ESTEXP FROM ESTHOLD WHERE INVCUST=$_SESSION[client] AND INVHYPE='".mysql_real_escape_string($value)."'  ORDER BY PETNAME, ISORTCODE";
	$ESTHOLD = mysql_query($query_ESTHOLD, $tryconnection) or die(mysql_error());
	$row_ESTHOLD = mysql_fetch_assoc($ESTHOLD);
		do {
		$_SESSION['view'][]=array('INVNO' => $row_ESTHOLD['INVNO'],
								'INVCUST' => $row_ESTHOLD['INVCUST'],
								'INVPET' => $row_ESTHOLD['INVPET'],
								'INVDATETIME' => $row_ESTHOLD['INVDATETIME'],
								'INVDOC' => $row_ESTHOLD['INVDOC'],
								'INVUNITS' => $row_ESTHOLD['INVUNITS'],
								'INVDESCR' => $row_ESTHOLD['INVDESCR'],
								'INVPRICE' => $row_ESTHOLD['INVPRICE'],
								'INVTOT' => $row_ESTHOLD['INVTOT'],
								'INVDISP' => $row_ESTHOLD['INVDISP'],
								'INVHYPE' => $row_ESTHOLD['INVHYPE'],
								'DATETIME' => $row_ESTHOLD['DATETIME'],
								'MEMO' => $row_ESTHOLD['MEMO'],
								'INVEST' => $row_ESTHOLD['INVEST'],
								'INVDECLINE' => $row_ESTHOLD['INVDECLINE'],
								'PETNAME' => $row_ESTHOLD['PETNAME'],
								'INVSTAT' => $row_ESTHOLD['INVSTAT'],
								'ESTEXP' => $row_ESTHOLD['ESTEXP']
								);
			}
		while ($row_ESTHOLD = mysql_fetch_assoc($ESTHOLD));
		}
	
		else if ($value=='0'){
		$query_INVHOLD = "SELECT *, DATE_FORMAT(DATETIME, '%m/%d/%Y') AS DATETIME FROM INVHOLD WHERE INVCUST=$_SESSION[client] ORDER BY INVNO,PETNAME, ISORTCODE";
		$INVHOLD = mysql_query($query_INVHOLD, $tryconnection) or die(mysql_error());
		$row_INVHOLD = mysql_fetch_assoc($INVHOLD);
		do {
		$_SESSION['view'][]=array('INVNO' => $row_INVHOLD['INVNO'],
								'INVCUST' => $row_INVHOLD['INVCUST'],
								'INVPET' => $row_INVHOLD['INVPET'],
								'INVDATETIME' => $row_INVHOLD['INVDATETIME'],
								'INVDOC' => $row_INVHOLD['INVDOC'],
								'INVUNITS' => $row_INVHOLD['INVUNITS'],
								'INVDESCR' => $row_INVHOLD['INVDESCR'],
								'INVPRICE' => $row_INVHOLD['INVPRICE'],
								'INVTOT' => $row_INVHOLD['INVTOT'],
								'INVDISP' => $row_INVHOLD['INVDISP'],
								'INVHYPE' => $row_INVHOLD['INVHYPE'],
								'DATETIME' => $row_INVHOLD['DATETIME'],
								'MEMO' => $row_INVHOLD['MEMO'],
								'INVEST' => $row_INVHOLD['INVEST'],
								'INVDECLINE' => $row_INVHOLD['INVDECLINE'],
								'PETNAME' => $row_INVHOLD['PETNAME'],
								'INVSTAT' => $row_INVHOLD['INVSTAT'],
								'ESTEXP' => $row_INVHOLD['ESTEXP']
								);
			}
		while ($row_INVHOLD = mysql_fetch_assoc($INVHOLD));
		}
		}
$openview="window.open('EST_RES_VIEW.php','_blank','width=600,height=600');";
}

elseif (isset($_POST['use']) || isset($_POST['ignore']) || isset($_POST['check'])){
unset($_SESSION['view']);
if (!isset($_SESSION['invline'])){
$_SESSION['invline'] = array();
}

if (!isset($_POST['ignore'])){
    $lock_it = "LOCK TABLES ESTHOLD WRITE" ;  
    $Qlock = mysql_query($lock_it, $tryconnection) or die(mysql_error()) ;
	foreach ($_POST['estres'] as $value){
	if ($value!='0'){
	$query_ESTHOLD = "SELECT *, DATE_FORMAT(ESTEXP, '%m/%d/%Y') AS ESTEXP FROM ESTHOLD WHERE INVCUST=$_SESSION[client] AND INVHYPE='".mysql_real_escape_string($value)."' AND (INVDESCR!='0' AND INVDESCR!='GST' AND INVDESCR!='HST' AND INVDESCR!='PST' AND INVDESCR!='TOTAL' AND INVDESCR!='Subtotal') ORDER BY PETNAME ASC";
	$ESTHOLD = mysql_query($query_ESTHOLD, $tryconnection) or die(mysql_error());
	$row_ESTHOLD = mysql_fetch_assoc($ESTHOLD);
	if ($_SESSION['refID']=='EST'){$invest='1';}else {$invest='0';}
	$_SESSION['invhype']=$row_ESTHOLD['INVHYPE'];
		do {
		
if ($row_ESTHOLD['INVDESC']!='0' && $row_ESTHOLD['INVDESC']!='GST' && $row_ESTHOLD['INVDESC']!='HST' && $row_ESTHOLD['INVDESC']!='PST' && $row_ESTHOLD['INVDESC']!='TOTAL' && $row_ESTHOLD['INVDESC']!='Subtotal'){		
		
		$_SESSION['invline'][]=array('INVNO' => $_SESSION['minvno'],
									 'INVCUST' => $row_ESTHOLD['INVCUST'],
									 'INVPET' => $row_ESTHOLD['INVPET'],
									 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
									 'INVMAJ' => $row_ESTHOLD['INVMAJ'],
									 'INVMIN' => $row_ESTHOLD['INVMIN'],
									 'INVDOC' => $_SESSION['doctor'],
									 'INVSTAFF' =>  $_SESSION['staff'],
									 'INVUNITS' => $row_ESTHOLD['INVUNITS'],
									 'INVDESCR' => $row_ESTHOLD['INVDESCR'],
									 'INVPRICE' => $row_ESTHOLD['INVPRICE'],
									 'INVTOT' => $row_ESTHOLD['INVTOT'],
									 'INVINCM' => $row_ESTHOLD['INVINCM'],
									 'INVDISC' => $row_ESTHOLD['INVDISC'],
									 'INVLGSM' => $row_ESTHOLD['INVLGSM'],
									 'INVREVCAT' => $row_ESTHOLD['INVREVCAT'],
									 'INVGST' => $row_ESTHOLD['INVGST'],
									 'INVTAX' => $row_ESTHOLD['INVTAX'], 
									 'REFCLIN' => $row_ESTHOLD['REFCLIN'],
									 'REFVET' => $row_ESTHOLD['REFVET'],
									 'INVUPDTE' => $row_ESTHOLD['INVUPDTE'],										
									 'INVFLAGS' => $row_ESTHOLD['INVFLAGS'],
									 'INVDISP' => $row_ESTHOLD['INVDISP'],
									 'INVGET' => $row_ESTHOLD['INVGET'],
									 'INVPERCNT' => $row_ESTHOLD['INVPERCNT'],
									 'INVHYPE' => $row_ESTHOLD['INVHYPE'],
									 'AUTOCOMM' => $row_ESTHOLD['AUTOCOMM'],
									 'INVCOMM' => $row_ESTHOLD['INVCOMM'],
									 'HISTCOMM' => $row_ESTHOLD['HISTCOMM'],
									 'MODICODE' => $row_ESTHOLD['MODICODE'],
									 'INVNARC' => $row_ESTHOLD['INVNARC'],
									 'INVVPC' => $row_ESTHOLD['INVVPC'],
									 'INVUPRICE' => $row_ESTHOLD['INVUPRICE'],
									 'INVPKGQTY' => $row_ESTHOLD['INVPKGQTY'],
									 'MEMO' => $row_ESTHOLD['MEMO'],
									 'IRADLOG' => $row_ESTHOLD['IRADLOG'],
									 'ISURGLOG' => $row_ESTHOLD['ISURGLOG'],
									 'INARCLOG' => $row_ESTHOLD['INARCLOG'],
									 'IUAC' => $row_ESTHOLD['IUAC'],
									 'INVSERUM' => $row_ESTHOLD['INVSERUM'],
									 'INVEST' => $invest,
									 'INVDECLINE' => $row_ESTHOLD['INVDECLINE'],
									 'PETNAME' => $row_ESTHOLD['PETNAME'],
									 'INVOICECOMMENT' => $row_ESTHOLD['INVOICECOMMENT'],
									 'INVPRU' => $row_ESTHOLD['INVPRU'],
									 'XDISC' => $row_ESTHOLD['XDISC'],
									 'MTAXRATE' => $row_ESTHOLD['MTAXRATE'],
									 'TUNITS' => $row_ESTHOLD['TUNITS'],
									 'TFLOAT' => $row_ESTHOLD['TFLOAT'],
									 'TENTER' => $row_ESTHOLD['TENTER'],
									 'INVSTAT' => $row_ESTHOLD['INVSTAT'],
									 'LCODE' => $row_ESTHOLD['LCODE'],
									 'LCOMMENT' => $row_ESTHOLD['LCOMMENT']
							);	
			   }
			
			}
		while ($row_ESTHOLD = mysql_fetch_assoc($ESTHOLD));
$deleteSQL = "DELETE  FROM ESTHOLD WHERE INVCUST='$_SESSION[client]' AND INVHYPE='".mysql_real_escape_string($value)."' ";;
mysql_query($deleteSQL, $tryconnection) or die(mysql_error());
$optimize = "OPTIMIZE TABLE ESTHOLD";
mysql_query($optimize, $tryconnection) or die(mysql_error());
		}
	}
     $unlock_it = "UNLOCK TABLES" ;
     $Qunlock = mysql_query($unlock_it, $tryconnection) or die(mysql_error()) ;
	}
	if ($_SESSION['refID']!='EST'){
        $lock_it = "LOCK TABLES INVHOLD WRITE" ;  
        $Qlock = mysql_query($lock_it, $tryconnection) or die(mysql_error()) ;
		$query_INVHOLD = "SELECT *, DATE_FORMAT(DATETIME, '%m/%d/%Y') AS DATETIME FROM INVHOLD WHERE INVCUST=$_SESSION[client] AND (INVDESCR!='1' AND INVDESCR!='GST' AND INVDESCR!='HST' AND INVDESCR!='PST' AND INVDESCR!='TOTAL' AND INVDESCR!='Subtotal') ORDER BY PETNAME,ISORTCODE ASC";
		$INVHOLD = mysql_query($query_INVHOLD, $tryconnection) or die(mysql_error());
		$row_INVHOLD = mysql_fetch_assoc($INVHOLD);
		
		if ($totalRows_INVHOLD!=0 && !isset($_SESSION['round'])){
			do {
		$_SESSION['invline'][]=array('INVNO' => $row_INVHOLD['INVNO'],
									 'INVCUST' => $row_INVHOLD['INVCUST'],
									 'INVPET' => $row_INVHOLD['INVPET'],
									 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
									 'INVMAJ' => $row_INVHOLD['INVMAJ'],
									 'INVMIN' => $row_INVHOLD['INVMIN'],
									 'INVDOC' => $row_INVHOLD['INVDOC'],
									 'INVSTAFF' => $row_INVHOLD['INVSTAFF'],
									 'INVUNITS' => $row_INVHOLD['INVUNITS'],
									 'INVDESCR' => $row_INVHOLD['INVDESCR'],
									 'INVPRICE' => $row_INVHOLD['INVPRICE'],
									 'INVTOT' => $row_INVHOLD['INVTOT'],
									 'INVINCM' => $row_INVHOLD['INVINCM'],
									 'INVDISC' => $row_INVHOLD['INVDISC'],
									 'INVLGSM' => $row_INVHOLD['INVLGSM'],
									 'INVREVCAT' => $row_INVHOLD['INVREVCAT'],
									 'INVGST' => $row_INVHOLD['INVGST'],
									 'INVTAX' => $row_INVHOLD['INVTAX'], 
									 'REFCLIN' => $row_INVHOLD['REFCLIN'],
									 'REFVET' => $row_INVHOLD['REFVET'],
									 'INVUPDTE' => $row_INVHOLD['INVUPDTE'],										
									 'INVFLAGS' => $row_INVHOLD['INVFLAGS'],
									 'INVDISP' => $row_INVHOLD['INVDISP'],
									 'INVGET' => $row_INVHOLD['INVGET'],
									 'INVPERCNT' => $row_INVHOLD['INVPERCNT'],
									 'INVHYPE' => $row_INVHOLD['INVHYPE'],
									 'AUTOCOMM' => $row_INVHOLD['AUTOCOMM'],
									 'INVCOMM' => $row_INVHOLD['INVCOMM'],
									 'HISTCOMM' => $row_INVHOLD['HISTCOMM'],
									 'MODICODE' => $row_INVHOLD['MODICODE'],
									 'INVNARC' => $row_INVHOLD['INVNARC'],
									 'INVVPC' => $row_INVHOLD['INVVPC'],
									 'INVUPRICE' => $row_INVHOLD['INVUPRICE'],
									 'INVPKGQTY' => $row_INVHOLD['INVPKGQTY'],
									 'MEMO' => $row_INVHOLD['MEMO'],
									 'INARCLOG' => $row_INVHOLD['INARCLOG'],
									 'IRADLOG' => $row_INVHOLD['IRADLOG'],
									 'ISURGLOG' => $row_INVHOLD['ISURGLOG'],
									 'IUAC' => $row_INVHOLD['IUAC'],
									 'INVSERUM' => $row_INVHOLD['INVSERUM'],
									 'INVEST' => $row_INVHOLD['INVEST'],
									 'INVDECLINE' => $row_INVHOLD['INVDECLINE'],
									 'PETNAME' => $row_INVHOLD['PETNAME'],
									 'INVOICECOMMENT' => $row_INVHOLD['INVOICECOMMENT'],
									 'INVPRU' => $row_INVHOLD['INVPRU'],
									 'XDISC' => $row_INVHOLD['XDISC'],
									 'MTAXRATE' => $row_INVHOLD['MTAXRATE'],
									 'TUNITS' => $row_INVHOLD['TUNITS'],
									 'TFLOAT' => $row_INVHOLD['TFLOAT'],
									 'INVSTAT' => $row_INVHOLD['INVSTAT'],
									 'TENTER' => $row_INVHOLD['TENTER'],
									 'LCODE' => $row_INVHOLD['LCODE'],
									 'LCOMMENT' => $row_INVHOLD['LCOMMENT'],
									 'TNOHST' => $row_INVHOLD['INVNOHST'],
									 'INVPAYDISC' =>$row_INVHOLD['INVPAYDISC'],
									 'INVHXCAT' =>$row_INVHOLD['INVHXCAT']
									);
			}
		while ($row_INVHOLD = mysql_fetch_assoc($INVHOLD));

  $deleteSQL = "DELETE FROM INVHOLD WHERE INVCUST='$_SESSION[client]'";
  mysql_query($deleteSQL, $tryconnection);
  $optimize = "OPTIMIZE TABLE INVHOLD";
  mysql_query($optimize, $tryconnection) or die(mysql_error());
} 
 $unlock_it = "UNLOCK TABLES" ;
 $Qunlock = mysql_query($unlock_it, $tryconnection) or die(mysql_error()) ;
}

$firstone=$_SESSION['invline'][0]['PETNAME'];
$invlinenumber=(count($_SESSION['invline'])-1);
$lastone=$_SESSION['invline'][$invlinenumber]['PETNAME'];
if($firstone==$lastone || $firstone==$_SESSION['petname']){
unset($_SESSION['morethan1']);
}
else {
$_SESSION['morethan1']="1";
}

$openview="opener.document.location='REGULAR_INVOICING.php?record=k&subcat=i&product=j&psex=$psex&pdob=$pdob&pettype=$pettype'; self.close();";

}
//$closewindow = " self.close();" ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ESTIMATES &amp; RESERVED INVOICES LIST</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">
function bodyonload()
{
<?php 
echo $openview; 
?>
<?php if($totalRows_ESTHOLD==0){echo "document.estrespreview.submit();";} ?>
<?php //echo $closewindow; 
?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+185,toppos+150);
}

function OnClose()
{
self.close();
}

function bodyonunload()
{
}

function selectall(){
var i;
if (document.forms[0].selall.value!="Select All"){
	for (i=0; i<document.forms[0].checkbox.length; i++){
	document.forms[0].checkbox[i].checked=false;
	document.forms[0].selall.value="Select All";
	break;
	}
}
else if (document.forms[0].selall.value="Select All"){
	for (i=0; i<document.forms[0].checkbox.length; i++){
	document.forms[0].checkbox[i].checked=true;
	document.forms[0].selall.value="Unselect All";	
	break;
	}
}
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="estrespreview" id="estrespreview" class="FormDisplay" style="position:absolute; top:0px; left:0px;">


<table width="400" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td height="42" colspan="3" align="center" class="Verdana12B" > There is <?php if($totalRows_ESTHOLD==0){echo "no";} else {echo $totalRows_ESTHOLD;} ?> estimate(s)  <?php if ($totalRows_INVHOLD>0){echo "and 1 reserved invoice ";} ?>on file. <?php   echo $firstone;
  echo $lastone;
  echo $_SESSION['morethan1'];
  
?></td>
  </tr>
  <tr>
    <td height="30" colspan="3" align="center" class="Verdana11" >
<script type="text/javascript"></script>
      <input type="button" name="selall" id="selall" value="Select All" onclick="selectall();" />    
    </td>
  </tr>

  <tr>
    <td height="200" colspan="3" align="center" class="Verdana11" >
    
    <div id="invpreview" style="width:400px;height:200px;overflow:auto;">
<table width="400" border="0" cellspacing="0" cellpadding="0">
  <?php   
  do { 
  if (isset($_SESSION['view'])){
  foreach ($_SESSION['view'] as $value) 
  if ($value['INVHYPE'] == $row_ESTHOLD['INVHYPE'])
  {$checked='checked';}
  }
  ?>
  <tr class="Verdana12Blue" height="20" <?php if ($totalRows_ESTHOLD==0){echo "style='display:none'";}?>>
    <td width="78">&nbsp;</td>
    <td width="166">
    <label><input type="checkbox" name="estres[]" id="checkbox" value="<?php echo $row_ESTHOLD['INVHYPE']; ?>" <?php echo $checked; ?>/>&nbsp;<?php echo $row_ESTHOLD['INVHYPE']; ?></label>    </td>
    <td width="156" title="Estimate expiry date"><?php echo $row_ESTHOLD['ESTEXP']; ?></td>
  </tr>
  <?php } while ($row_ESTHOLD = mysql_fetch_assoc($ESTHOLD));?>
  <tr class="Verdana12Red" height="20" <?php if ($totalRows_INVHOLD==0){echo "style='display:none;'";} ?>>
    <td width="78">&nbsp;</td>
    <td width="166">
    <label><input type="checkbox" name="estres[]" id="checkbox" value="0" checked="checked" readonly="readonly" />&nbsp;<?php echo "Invoice #".$row_INVHOLD['INVNO']; ?></label>    </td>
    <td width="156" title="Invoice creation date"><?php echo $row_INVHOLD['DATETIME']; ?></td>
  </tr>
</table>    
    </div>
    </td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="ButtonsTable">
    <input type="submit" name="use" id="use" value="USE" class="button" />
    <input type="submit" name="view" id="view" value="VIEW" class="button" />
    <input type="submit" name="ignore" id="ignore" value="IGNORE" class="button" />    
    <input type="hidden" name="check" value="1"  />
    </td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
