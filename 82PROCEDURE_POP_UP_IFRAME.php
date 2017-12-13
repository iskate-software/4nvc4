<?php 
session_start();
require_once('../../tryconnection.php'); 
include("../../ASSETS/tax.php");

$procode=$_GET['procode'];
$pettype=$_GET['pettype'];

mysqli_select_db($tryconnection, $database_tryconnection);
$query_PROCEDURE = "SELECT * FROM PROCEDUR WHERE PROCODE LIKE '$procode%' AND FEEFILE='$pettype' ORDER BY PROCODE,ISORTCODE ASC";
$PROCEDURE = mysqli_query($tryconnection, $query_PROCEDURE) or die(mysqli_error($mysqli_link));
$row_PROCEDURE = mysqli_fetch_assoc($PROCEDURE);
$totalRows_PROCEDURE = mysqli_num_rows($PROCEDURE);

$_SESSION['procode']=$_GET['procode'];

if (isset($_POST['selectedcode'])){

$query_PATIENT_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO='$_SESSION[client]' LIMIT 1";
$PATIENT_CLIENT = mysqli_query($tryconnection, $query_PATIENT_CLIENT) or die(mysqli_error($mysqli_link));
$row_PATIENT_CLIENT = mysqli_fetch_assoc($PATIENT_CLIENT);
$discpcnt = round($row_PATIENT_CLIENT['DISC'] * .01,2) ;

$query_PROCEDURE = "SELECT * FROM PROCEDUR WHERE PROCODE='$_POST[selectedcode]' AND FEEFILE='$pettype'";
$PROCEDURE = mysqli_query($tryconnection, $query_PROCEDURE) or die(mysqli_error($mysqli_link));
$row_PROCEDURE = mysqli_fetch_assoc($PROCEDURE);
$taxvalue=($_POST['taxvalue']/100);

			do {
				$autcomm=$row_PROCEDURE['AUTOCOMM'];
				$query_TAUTOCOMM = "SELECT * FROM ARSYSCOMM WHERE COMMCODE='$autcomm'";
				$TAUTOCOMM = mysqli_query($tryconnection, $query_TAUTOCOMM) or die(mysqli_error($mysqli_link));
				$row_TAUTOCOMM = mysqli_fetch_assoc($TAUTOCOMM);
				$invoicecomment=str_replace('$PETNAME', $_SESSION['petname'], $row_TAUTOCOMM['COMMENT']);
						   
						   //CREATE AN ARRAY FROM ENTIRE RECORD FROM VETCAN FOR SELECTED ITEM 			
						   $item = array('INVNO' => $_SESSION['minvno'],
						   				 'INVCUST' => $_SESSION['client'],
										 'INVPET' => $_SESSION['patient'],
										 'INVDATETIME' => $_SESSION['minvdte'].' '.date('H:s:i'),
										 'INVMAJ' => $row_PROCEDURE['INVMAJ'],
										 'INVMIN' => $row_PROCEDURE['INVTNO'],
										 'INVDOC' => $_SESSION['doctor'],
										 'INVSTAFF' => $_SESSION['staff'],
										 'INVUNITS' => $row_PROCEDURE['INVUNITS'],
										 'INVDESCR' => $row_PROCEDURE['INVDESC'],
										 'INVPRICE' => $row_PROCEDURE['INVPRICE'],
										 'INVTOT' => round($row_PROCEDURE['INVTOT'],2),
										 'INVINCM' => $row_PROCEDURE['INVINCM'],
										 'INVDISC' => round($row_PROCEDURE['INVTOT']*$discpcnt,2),
										 'INVLGSM' => $_SESSION['pettype'],
										 'INVREVCAT' => $row_PROCEDURE['INVREVCAT'],
										 'INVGST' => round((($row_PROCEDURE['INVTOT']-round($row_PROCEDURE['INVTOT']*$discpcnt,2))*$taxvalue),2),
										 'INVTAX' => round((($row_PROCEDURE['INVTOT']-round($row_PROCEDURE['INVTOT']*$discpcnt,2))*($row_PROCEDURE['INVTAX']/100)),2), 
										 'REFCLIN' => $row_PATIENT_CLIENT['REFCLIN'],
										 'REFVET' => $row_PATIENT_CLIENT['REFVET'],
										 'INVUPDTE' => $row_PROCEDURE['INVUPDTE'],										
										 'INVFLAGS' => $row_PROCEDURE['INVFLAGS'],
										 'INVDISP' => $row_PROCEDURE['INVDISP'],
										 'INVGET' => $row_PROCEDURE['INVGET'],
										 'INVPERCNT' => $row_PROCEDURE['INVPERCNT'],
										 'INVHYPE' => $row_PROCEDURE['INVHYPE'],
										 'AUTOCOMM' => $row_TAUTOCOMM['COMMCODE'],
										 'INVCOMM' => $row_PROCEDURE['INVCOMM'],
										 'HISTCOMM' => $row_PROCEDURE['HISTCOMM'],
										 'MODICODE' => $row_PROCEDURE['MODICODE'],
										 'INVNARC' => $row_PROCEDURE['INVNARC'],
										 'INVVPC' => $row_PROCEDURE['INVVPC'],
										 'INVUPRICE' => $row_PROCEDURE['INVUPRICE'],
										 'INVPKGQTY' => $row_PROCEDURE['INVPKGQTY'],
										 'MEMO' => "",
										 'IRADLOG' => $row_PROCEDURE['IRADLOG'],
										 'ISURGLOG' => $row_PROCEDURE['ISURGLOG'],
										 'INARCLOG' => $row_PROCEDURE['INARCLOG'],
										 'IUAC' => $row_PROCEDURE['IUAC'],
										 'INVSERUM' => $row_PROCEDURE['INVSERUM'],
										 'INVEST' => ($_SESSION['refID']=='EST') ? "1" : "0",
										 'INVDECLINE' => "0",
										 'PETNAME' => $_SESSION['petname'],
										 'INVOICECOMMENT' => $invoicecomment,
										 'INVPRU' => $row_PROCEDURE['INVPRU'],
										 'XDISC' => "0.00",
										 'MTAXRATE' => $row_PROCEDURE['TTAX'],
										 'TUNITS' => "1",
										 'TFLOAT' => "1",
										 'TENTER' => "1",
										 'INVPAYDISC' => "0",
										 'INVHXCAT' => $row_PROCEDURE['INVHXCAT']
										 );
						$_SESSION['invline'][] = $item;
						
				} while ($row_PROCEDURE = mysqli_fetch_assoc($PROCEDURE));
$closewindow="parent.window.close();";
}?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

<script type="text/javascript">

function bodyonload()
{
<?php echo $closewindow; ?>
}

function bodyonunload()
{

}

function highliteline(x){
document.getElementById(x).style.cursor="pointer";
document.getElementById(x).style.backgroundColor="#DCF6DD";
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
}

function selectproc(x){
document.select_procode.selectedcode.value=x;
document.select_procode.submit();
}

</script>


<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->
<form method="post" action="" name="select_procode" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;">
<input type="hidden" name="selectedcode" value="" />
<input type="hidden" name="taxvalue" value="<?php taxvalue($database_tryconnection, $tryconnection, $_SESSION['minvdte']); ?>"  />
<table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
          <tr>
            <td colspan="2" height="400" valign="top" class="Verdana12">
            
            <div style="height:470px; overflow:auto;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            
            <?php do {
			if ($xrepeat!=$row_PROCEDURE['PROCODE']){echo "<tr><td colspan='5' height='5'></td></tr>";}
			if ($xrepeat!=$row_PROCEDURE['PROCODE']){echo "<tr  bgcolor='#CCCCCC'><td colspan='5' height='1'></td></tr>";}
            
		echo "<tr ";
		
		if ($xrepeat!=$row_PROCEDURE['PROCODE']){echo "id='".$row_PROCEDURE['PROCODE']."'";}
		
		echo ' onmouseover="highliteline(this.id);" onmouseout="whiteoutline(this.id);" onclick="selectproc(this.id);">';
			echo "<td>&nbsp;";
			echo "</td>";
			echo "<td>";
			
			if ($xrepeat!=$row_PROCEDURE['PROCODE']){echo $row_PROCEDURE['PROCODE'];}
			
			echo "</td>";
			echo "<td class='Verdana12B' width='170' title='Click to select'>";
			
			if ($xrepeat!=$row_PROCEDURE['PROCODE']){echo $row_PROCEDURE['PROCEDURE'];}
			
			echo "</td>";
			echo "<td>";
			
			echo $row_PROCEDURE['INVDESC'];
			
			echo "</td>";
			echo "<td width='50' align='right'>";
			
			echo $row_PROCEDURE['INVPRICE'];
			
			echo "&nbsp;&nbsp;&nbsp;</td>";			
		echo "</tr>";
			
			$xrepeat=$row_PROCEDURE['PROCODE'];
			
			 } while ($row_PROCEDURE = mysqli_fetch_assoc($PROCEDURE)); ?>
            </table></div>            </td>
    </tr>
      </table></td>
    </tr>
    
  </table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>