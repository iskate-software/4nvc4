<?php 
session_start();
require_once('../../tryconnection.php'); 

mysql_select_db($database_tryconnection, $tryconnection);
$query_Staff = "SELECT * FROM STAFF WHERE SIGNEDIN=1 ORDER BY PERSONID";
$Staff = mysql_query($query_Staff, $tryconnection) or die(mysql_error());
$row_Staff = mysqli_fetch_assoc($Staff);

$query_Doctor = "SELECT * FROM DOCTOR WHERE SIGNEDIN=1 AND SUBSTR(DOCTOR,1,3) = 'Dr.'";
$Doctor = mysql_query($query_Doctor, $tryconnection) or die(mysql_error());
$row_Doctor = mysqli_fetch_assoc($Doctor);


if (isset($_POST['check']) && !isset($_POST['cancel'])) {
	$query_INVNO = "SELECT LASTINV FROM CRITDATA ";
	$INVNO = mysql_query($query_INVNO, $tryconnection) or die(mysql_error());
	$row_INVNO = mysqli_fetch_assoc($INVNO);
	$_SESSION['csminvno'] = $row_INVNO['LASTINV'] + 1 ;
	$query_INVNO = "UPDATE CRITDATA SET LASTINV = '$_SESSION[csminvno]'" ;
	$INVNO = mysql_query($query_INVNO,$tryconnection) or die(mysql_error()) ;

   $_SESSION['csstaff'] = $_POST['invstaff'];
   $_SESSION['csminvdte'] = $_POST['minvdte'];
$closewin="opener.document.location.reload(); self.close();";
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>SELECT STAFF FOR CASUAL SALE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
<?php echo $closewin; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+140,toppos+60);
document.forms[0].invstaff.options[0].selected=true;
}

function Staffselect(){
document.Staff.submit();
}


</script>


<style type="text/css">
<!--
.SelectList {	
	width: 100%;
	height: 100%;
	font-family: "Verdana";
	font-size: 13px;
	border-width: 1px;
	padding: 5 px;
	outline-width: 0px;
}
#table2 {	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
-->
</style>
<!-- InstanceEndEditable -->
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;">
<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="center" valign="top">
    
    <table width="100%" class="table" border="1" cellpadding="0" cellspacing="0" >
      <tr>
        <td height="300"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="30" colspan="3" align="center" valign="middle" class="Verdana12Blue"><strong>Please select Staff:</strong> <br  />
                    <span class="Verdana11Grey">Doubleclick or click &amp; save.</span> </td>
          </tr>
          <tr>
            <td width="136" height="188">&nbsp;</td>
            <td width="260"><select name="invstaff" size="10" id="select" class='SelectList' ondblclick="Staffselect();">
              <?php
do {  
      echo '<option value="'.$row_Doctor['DOCTOR'].'">'.$row_Doctor['DOCTOR'].'</option>';
} while ($row_Doctor = mysqli_fetch_assoc($Doctor));
  
	  
do {  
?>
              <option value="<?php echo $row_Staff['STAFF']?>" ><?php echo $row_Staff['STAFF']?></option>
              <?php
} while ($row_Staff = mysqli_fetch_assoc($Staff));
?>
            </select></td>
            <td width="100">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td height="40" align="center"><label>Date
              <input name="minvdte" type="text" class="Input" id="minvdte" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onclick="ds_sh(this,'<?php echo date('m/d/Y') ?>')" value="<?php echo date("m/d/Y"); ?>" />
            </label></td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="35" colspan="3" align="center" valign="middle" bgcolor="#B1B4FF"><input name="save" class="button" type="submit" value="SAVE"/>
        <input name="cancel" class="button" type="button" value="CANCEL" onclick="self.close();"/>
        <input type="hidden" name="check" value="1"/>
      </td>
  </tr>
</table>
</form>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
