<?php
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);
$query_CRITDATA = "SELECT * FROM CRITDATA LIMIT 1";
$CRITDATA = mysqli_query($tryconnection, $query_CRITDATA) or die(mysqli_error($mysqli_link));
$row_CRITDATA = mysqli_fetch_assoc($CRITDATA);

$spec=$_GET['species'];

$query_CAN1=sprintf("SELECT DISTINCT TCATGRY FROM VETCAN WHERE TSPECIES='%s' ORDER BY TCATGRY,TNO ASC", $spec);
$CAN1 = mysqli_query($tryconnection, $query_CAN1) or die(mysqli_error($mysqli_link));
$row_CAN1 = mysqli_fetch_assoc($CAN1);

$categories = array();

do {
$categories[]=$row_CAN1['TCATGRY'];
} while ($row_CAN1 = mysqli_fetch_assoc($CAN1));


///$categories=explode(",",$_GET['categories']);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PRINT TREATMENT FEE FILE REPORT</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload()
{
}

function OnClose()
{
self.close();
}

function bodyonunload()
{

}

</script>

<style type="text/css">
body {
background-color:#FFFFFF;
overflow:auto;
}
#apDiv1 {
	position:absolute;
	left:86px;
	top:12px;
	width:76px;
	height:24px;
	z-index:1;
}
#apDiv2 {
	position:absolute;
	left:520px;
	top:12px;
	width:76px;
	height:24px;
	z-index:1;
}
#apDiv4 {
	position:absolute;
	left:612px;
	top:6px;
	width:88px;
	height:21px;
	z-index:2;
}
</style>
<!-- InstanceEndEditable -->
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<div id="apDiv4">
<span class="Verdana12"><?php echo date("m/d/Y"); ?></span></div>
<div id="apDiv1">
    <input name="print" type="button" class="button" id="print" value="PRINT" onclick="window.print();" />
</div>
<form method="post" action="" name="" id="" style="position:absolute; top:0px; left:0px;">
  <table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
    <tr class="Verdana14B">
      <td height="50">&nbsp;</td>
      <td align="center" colspan="9">
		<?php echo $row_CRITDATA['HOSPNAME']; ?><br  />
        <?php if ($_GET['species']=='1'){echo "Canine";} else if ($_GET['species']=='2'){echo "Feline";} else if ($_GET['species']=='3'){echo "Equine";}else if ($_GET['species']=='4'){echo "Bovine";}else if ($_GET['species']=='5'){echo "Caprine";}else if ($_GET['species']=='6'){echo "Porcine";}else if ($_GET['species']=='7'){echo "Avian";}else if ($_GET['species']=='8'){echo "Other";}; ?> 
      Treatment Fee File Report	  
      </td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr class="Verdana11" valign="bottom">
      <td height="25">&nbsp;</td>
      <td align="left" colspan="3">Subtreatment</td>
      <td width="50" align="right">Fee</td>
      <td width="12" align="right">&nbsp;RA</td>
      <td width="50" align="right">Disp. Fee</td>
      <td width="40" align="center">Disc.</td>
      <td width="40" align="center">Units</td>
      <td width="40" align="center">Upd.</td>
      <td width="40" align="left">Prof %</td>
      <td align="center">Comment</td>
      <td align="right">&nbsp;</td>
    </tr>

  <?php
  
foreach ($categories as $value){

$query_CAN1=sprintf("SELECT * FROM VETCAN WHERE TCATGRY='$value' AND TSPECIES='%s' ORDER BY TCATGRY,TNO ASC", $spec);
$CAN1 = mysqli_query($tryconnection, $query_CAN1) or die(mysqli_error($mysqli_link));
$row_CAN1 = mysqli_fetch_assoc($CAN1);
$totalRows_CAN1 = mysqli_num_rows($CAN1);
  
echo '<tr class="Verdana13B">
      <td>&nbsp;</td>
      <td height="30" align="right" >'.$row_CAN1['TCATGRY'].'.</td>
      <td>&nbsp;</td>
      <td colspan="8">'.$row_CAN1['TTYPE'].'</td>
    </tr>';
  
do {
?>
    <tr class="Verdana12">
      <td height="18">&nbsp;</td>
      <td width="20" align="right"><?php echo $row_CAN1['TNO']; ?>.</td>
      <td>&nbsp;</td>
      <td width="200" align="left"><?php echo $row_CAN1['TDESCR']; ?></td>
      <td width="50" align="right"><?php echo $row_CAN1['TFEE']; ?></td>
      <td width="10" align="right"><?php echo $row_CAN1['TREVCAT'];?></td>
      <td width="50" align="right"><?php echo $row_CAN1['TDISP'];?></td>
      <td width="40" align="center"><?php if ($row_CAN1['TDISCOUNT']=='1'){echo "Yes";} else {echo "No";}?></td>
      <td width="40" align="center"><?php if ($row_CAN1['TUNITS']=='1'){echo "Yes";} else {echo "No";}?></td>
      <td width="40" align="center"><?php if ($row_CAN1['TUPDATE']=='1'){echo "Yes";} else {echo "No";}?></td>
      <td width="40" align="center"><?php echo $row_CAN1['TSTAT']; ?></td>
      <td align="left"><?php echo $row_CAN1['TAUTOCOMM']; ?></td>
      <td align="right">&nbsp;</td>
    </tr>
  <?php }
while ($row_CAN1 = mysqli_fetch_assoc($CAN1));
echo  '<tr>
      <td height="10" colspan="9"></td>
    </tr>';

}


?>   
</table>
  <div id="apDiv2">
    <input name="close" type="button" class="button" id="close" value="CLOSE" onclick="self.close();" />
  </div>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
