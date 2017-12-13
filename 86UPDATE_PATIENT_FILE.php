<?php 
require_once('../../tryconnection.php'); 

mysqli_select_db($tryconnection, $database_tryconnection);
$query_TFF = "SELECT * FROM VETCAN WHERE TFFID='$_GET[tffid]'";
$TFF = mysqli_query($tryconnection, $query_TFF) or die(mysqli_error($mysqli_link));
$row_TFF = mysqli_fetch_assoc($TFF);

$tflags=$row_TFF['TFLAGS'];
$tvaccs=$row_TFF['TVACCS'];

$query_SPECIES = "SELECT * FROM ANIMTYPE WHERE ANIMALID='$_GET[species]'";
$SPECIES = mysqli_query($tryconnection, $query_SPECIES) or die(mysqli_error($mysqli_link));
$row_SPECIES = mysqli_fetch_assoc($SPECIES);
$species=$row_SPECIES['ANIMAL'];

$query_VACCINES = "SELECT * FROM VACCINES WHERE NAME LIKE '%$species%' ORDER BY SEQ ASC";
$VACCINES = mysqli_query($tryconnection, $query_VACCINES) or die(mysqli_error($mysqli_link));
$row_VACCINES = mysqli_fetch_assoc($VACCINES);
//$totalRows_VACCINES = mysql_num_rows($VACCINES);
$vaccines=array();
do {
$vaccines[]=$row_VACCINES['VACID'];
} while ($row_VACCINES = mysqli_fetch_assoc($VACCINES));

if (isset($_POST['save']) || isset($_POST['check'])){

	foreach ($vaccines as $paris){
	$xxx="x".$paris;
	$yyy=$_POST[$xxx];
	$aaa=$aaa.$_POST[$xxx];
	
		$zzz=array();
		for ($i=0; $i<10; $i++){
		$zzz[]=$yyy[$i];
		}
		
				$PRABDAT="0";
				$POTHDAT="0";
				$PLEUKDAT="0";
				$POTHTWO="0";
				$POTHTHR="0";
				$POTHFOR="0";
				$POTHFIV="0";
				$POTHSIX="0";
				$PNEUTER="0";
				$PDEADATE="0";
				
			if (in_array('1',$zzz)){
			$PRABDAT="1";
			}
			if (in_array('2',$zzz)){
			$POTHDAT="1";
			}
			if (in_array('3',$zzz)){
			$PLEUKDAT="1";
			}
			if (in_array('4',$zzz)){
			$POTHTWO="1";
			}
			if (in_array('5',$zzz)){
			$POTHTHR="1";
			}
			if (in_array('6',$zzz)){
			$POTHFOR="1";
			}
			if (in_array('7',$zzz)){
			$POTHFIV="1";
			}
			if (in_array('8',$zzz)){
			$POTHSIX="1";
			}
			if (in_array('9',$zzz)){
			$PNEUTER="1";
			}
			if (in_array('10',$zzz)){
			$PDEADATE="1";
			}	
		
		$bbb=$PRABDAT.$POTHDAT.$PLEUKDAT.$POTHTWO.$POTHTHR.$POTHFOR.$POTHFIV.$POTHSIX.$PNEUTER.$PDEADATE;
	
	
//	$query_UPDATE="UPDATE VACCINES SET VFLAGS='$bbb', VDECODE='$_POST[$xxx]' WHERE VACID='$paris'";
//	$UPDATE = mysql_query($query_UPDATE, $tryconnection) or die(mysql_error());
	}


// 1) PRABDAT PRABYEARS Rabies
$PRABDAT=!empty($_POST['PRABDAT']) ? "1" : "0";
// 2) POTHDAT POTHYEARS Upper Respiratory Tract  (DA2P, FVRCP)
$POTHDAT=!empty($_POST['POTHDAT']) ? "1" : "0";
// 3) PLEUKDAT PKLEUKYEARS (Lepto, Feline Leukemia)
$PLEUKDAT=!empty($_POST['PLEUKDAT']) ? "1" : "0";
// 4) POTHTWO POTH02YEARS (Corona ,Chlamydia)
$POTHTWO=!empty($_POST['POTHTWO']) ? "1" : "0";
// 5) POTHTHR POTH03YEARS (Parvo, Feline Infectious Peritonitis)
$POTHTHR=!empty($_POST['POTHTHR']) ? "1" : "0";
// 6) POTHFOR POTH04YEARS (Heartworm, West Nile for Equine)
$POTHFOR=!empty($_POST['POTHFOR']) ? "1" : "0";
// 7) POTHFIV  (Fecal) 
$POTHFIV=!empty($_POST['POTHFIV']) ? "1" : "0";
// 8) POTHSIX POTH06YEARS (Bordetella)
$POTHSIX=!empty($_POST['POTHSIX']) ? "1" : "0";
// 9) PNEUTER
$PNEUTER=!empty($_POST['PNEUTER']) ? "1" : "0";
//10) PDEADATE   PDEAD
$PDEADATE=!empty($_POST['PDEADATE']) ? "1" : "0";
//11) PXRAYFILE (Number taken out of Critdata.
$PXRAYFILE=!empty($_POST['PXRAYFILE']) ? "1" : "0";
//12) PTATNO (taken from first part of INVDESC)
$PTATNO=!empty($_POST['PTATNO']) ? "1" : "0";
//13) POTHSEV  (Lyme) (Just PDECLAW if cat, PMAGNET if Bovine)
$POTHSEV=!empty($_POST['POTHSEV']) ? "1" : "0";
//14) PNEUTER
$PNEUTER=!empty($_POST['PNEUTER']) ? "1" : "0";
//15) PWEIGHT (Taken from first part of INVDESC)
$PWEIGHT=!empty($_POST['PWEIGHT']) ? "1" : "0";
//16) POTH8 Annual Exam
$POTH8=!empty($_POST['POTH8']) ? "1" : "0";
//17) POTH9 (Distemper only Canine,Equine Arteritis)
$POTH9=!empty($_POST['POTH9']) ? "1" : "0";
//18) POTH10 POTH0YEARS (Giardia)
$POTH10=!empty($_POST['POTH10']) ? "1" : "0";

$tflags=$PRABDAT.$POTHDAT.$PLEUKDAT.$POTHTWO.$POTHTHR.$POTHFOR.$POTHFIV.$POTHSIX.$PNEUTER.$PDEADATE.$PXRAYFILE.$PTATNO.$POTHSEV.$PNEUTER.$PWEIGHT.$POTH8.$POTH9.$POTH10;

$tvaccs=implode(",", $_POST['xtvaccs']);


$kkk=array();
for ($i=0; $i<36; $i++){
$kkk[]=$aaa[$i];
}

if ((in_array('1',$kkk) && $PRABDAT=='0') || (in_array('2',$kkk) && $POTHDAT=='0') || (in_array('3',$kkk) && $PLEUKDAT=='0') || (in_array('4',$kkk) && $POTHTWO=='0') || (in_array('5',$kkk) && $POTHTHR=='0') || (in_array('6',$kkk) && $POTHSIX=='0') || (in_array('7',$kkk) && $POTHSEV=='0') || (in_array('8',$kkk) && $POTH9=='0') || (in_array('9',$kkk) && $POTH10=='0') || (in_array('10',$kkk) && $PDEADATE=='0')){
$closewindow="alert('Please check all appropriate patient file updates.');";
}
else {$closewindow="self.close();";}

}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>UPDATE PATIENT FILE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
<?php echo $closewindow; ?>
}

function OnClose()
{
self.close();
}

function bodyonunload()
{
//opener.document.tff.tflags.value=document.patient_update.tflags.value;
//opener.document.tff.tvaccs.value=document.patient_update.tvaccs.value;
opener.document.tff.tflags.value='<?php echo $tflags; ?>';
opener.document.tff.tvaccs.value='<?php echo $tvaccs; ?>';
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="patient_update" id="patient_update" class="FormDisplay" style="position:absolute; top:0px; left:0px;">
<input name="check" value="1" type="hidden"  />
<table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <th height="67" colspan="2" align="center" class="Verdana13B" scope="col"><?php echo $species; ?> File <br />
    </th>
    </tr>
  <tr>
    <td width="350" height="40" align="center" class="Verdana12Blue"><?php echo $_GET['cocktail']; ?></td>
    <td width="350" height="40" align="center" class="Verdana12">Associated Antigens</td>
  </tr>
  <tr>
    <td height="90" align="center">
    <div style="border:thin solid #0000FF; width:310px; height:300px;">
    <table width="300" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input name="POTH8" type="checkbox" id="POTH8" value="1" <?php if(substr($row_TFF['TFLAGS'],15,1)){echo "checked";} ?>/>
       . Annual</label></td>
    <td height="25" width="150" align="left" class="Verdana11"><label>
      <input type="checkbox" name="POTHFOR" id="POTHFOR"  value="1" <?php if(substr($row_TFF['TFLAGS'],5,1)){echo "checked";} ?>/>  <?php if($_GET['species']=='1'){echo "Heartworm";} elseif ($_GET['species']=='3'){echo "West Nile";} else {echo "N/A";} ?>
    </label></td>
    </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input name="PRABDAT" type="checkbox" id="1" value="1" <?php if(substr($row_TFF['TFLAGS'],0,1)){echo "checked";} ?>/>
       1 Rabies</label></td>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="POTHFIV" id="POTHFIV"  value="1"<?php if(substr($row_TFF['TFLAGS'],6,1)){echo "checked";} ?>/> <?php if($_GET['species']=='1'){echo "Fecal";} else {echo "N/A";} ?>
    </label></td>
  </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input name="POTHDAT" type="checkbox" id="2" value="1" <?php if(substr($row_TFF['TFLAGS'],1,1)){echo "checked";} ?>/> 2  <?php if($_GET['species']=='1'){echo "DA2P";} elseif ($_GET['species']=='2'){echo "FVRCP";} elseif ($_GET['species']=='8'){echo "Distemper";} else {echo "N/A";}?></label></td>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="PNEUTER" id="PNEUTER"  value="1" <?php if(substr($row_TFF['TFLAGS'],13,1)){echo "checked";} ?>/> Neuter/Spay</label></td>
  </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input name="PLEUKDAT" type="checkbox" id="3" value="1" <?php if(substr($row_TFF['TFLAGS'],2,1)){echo "checked";} ?>/> 3 <?php if($_GET['species']=='1'){echo "Lepto";} elseif ($_GET['species']=='2'){echo "Feline Leukemia";} else {echo "N/A";}?></label></td>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="PXRAYFILE" id="PXRAYFILE"  value="1" <?php if(substr($row_TFF['TFLAGS'],10,1)){echo "checked";} ?>/> X Ray</label></td>
  </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input name="POTHTWO" type="checkbox" id="4" value="1" <?php if(substr($row_TFF['TFLAGS'],3,1)){echo "checked";} ?>/> 4 <?php if($_GET['species']=='1'){echo "Corona";} elseif ($_GET['species']=='2'){echo "Chlamydia";} else {echo "N/A";} ?></label></td>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="PTATNO" id="PTATNO"  value="1" <?php if(substr($row_TFF['TFLAGS'],11,1)){echo "checked";} ?>/> ID. #</label></td>
  </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="POTHTHR" id="5"  value="1" <?php if(substr($row_TFF['TFLAGS'],4,1)){echo "checked";} ?>/> 5 <?php if($_GET['species']=='1'){echo "Parvo";} elseif ($_GET['species']=='2'){echo "Feline Infectious Peritonitis";} else {echo "N/A";} ?></label></td>
    <td height="25" align="left" class="Verdana11"><label>
    <input type="checkbox" name="PWEIGHT" id="PWEIGHT"  value="1" <?php if(substr($row_TFF['TFLAGS'],14,1)){echo "checked";} ?>/> Weight</label></td>
  </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="POTHSIX" id="6"  value="1" <?php if(substr($row_TFF['TFLAGS'],7,1)){echo "checked";} ?>/> 6 <?php if($_GET['species']=='1'){echo "Bordetella";} else {echo "N/A";} ?></label></td>
    <td height="25" align="left" class="Verdana11"><label>
    <input type="checkbox" name="PDEADATE" id="PDEADATE"  value="1" <?php if(substr($row_TFF['TFLAGS'],9,1)){echo "checked";} ?>/> Deceased</label></td>
  </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="POTHSEV" id="7"  value="1" <?php if(substr($row_TFF['TFLAGS'],12,1)){echo "checked";} ?>/> 7 <?php if($_GET['species']=='1'){echo "Lyme disease";} elseif ($_GET['species']=='2'){echo "Declawed";} elseif ($_GET['species']=='4'){echo "Magnet";} else {echo "N/A";} ?></label></td>
    <td height="25" align="left" class="Verdana11">&nbsp;</td>
  </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="POTH9" id="8" value="1" <?php if(substr($row_TFF['TFLAGS'],16,1)){echo "checked";} ?>/> 8 <?php if($_GET['species']=='1'){echo "Distemper";} elseif($_GET['species']=='3'){echo "Equine Arteritis";} else {echo "N/A";} ?></label></td>
    <td height="25" align="left" class="Verdana11">&nbsp;</td>
  </tr>
  <tr>
    <td height="25" align="left" class="Verdana11"><label>
      <input type="checkbox" name="POTH10" id="9"  value="1" <?php if(substr($row_TFF['TFLAGS'],17,1)){echo "checked";} ?>/> 9 <?php if($_GET['species']=='1'){echo "Giardia";} else {echo "N/A";} ?></label></td>
    <td height="25" align="left" class="Verdana11">&nbsp;</td>
  </tr>
</table>
    </div>    </td>
    <td align="center" valign="top">
    
    
    <div style="border:thin solid #000000; width:310px; height:300px; <?php if ($_GET['tserum']=='0'){echo "display:none;";} ?>">
    <table width="300" border="0" cellspacing="0" cellpadding="0">
  <?php 
  	$VACCINES = mysqli_query($tryconnection, $query_VACCINES) or die(mysqli_error($mysqli_link));
	$row_VACCINES = mysqli_fetch_assoc($VACCINES);
	do{ ?>
  <tr class="Verdana11">
    <td height="25" width="51"><input type="text" id="<?php echo $row_VACCINES['VACID']; ?>" name="x<?php echo $row_VACCINES['VACID']; ?>" size="6" class="Input" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_VACCINES['VDECODE']; ?>" /></td>
    <td width="249"><label><input type="checkbox" name="xtvaccs[]" value="<?php echo $row_VACCINES['NAME']; ?>" 
	<?php 
		$ytvaccs = explode(",",$row_TFF['TVACCS']);
		if (in_array($row_VACCINES['NAME'], $ytvaccs))
		{echo "CHECKED";} 
		?>/> <?php echo $row_VACCINES['NAME']; ?></label></td>
  </tr>
  <?php } while ($row_VACCINES = mysqli_fetch_assoc($VACCINES)); ?>
</table>
    </div>    </td>
  </tr>
  <tr>
    <td height="56" colspan="2" align="center" class="Verdana11">Please check the patient fileds and associated antigens to update</td>
    </tr>
  <tr>
    <td colspan="2" class="ButtonsTable" align="center">
    <input type="submit" name="save" id="save" value="SAVE" class="button" />
    <input type="button" name="cancel" id="cancel" value="CLOSE"  class="button" onclick="self.close();" />
    
    <input type="hidden" name="tflags" id="tflags" value="<?php echo $tflags; ?>" />
    <input type="hidden" name="tvaccs" id="tvaccs" size="60" value="<?php echo $tvaccs; ?>" />
    
    </td>
    </tr>
</table>



</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
