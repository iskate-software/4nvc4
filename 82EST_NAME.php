<?php 
session_start();
require_once('../../tryconnection.php'); 
include("../../ASSETS/tax.php");

mysql_select_db($database_tryconnection, $tryconnection);
$query_CRITDATA = "SELECT ESTEXP FROM CRITDATA LIMIT 1";
$CRITDATA = mysql_query($query_CRITDATA, $tryconnection) or die(mysql_error());
$row_CRITDATA = mysqli_fetch_assoc($CRITDATA);


$intervquery1="SELECT CURRENT_DATE() + INTERVAL $row_CRITDATA[ESTEXP] DAY";
$interval1= mysql_unbuffered_query($intervquery1, $tryconnection) or die(mysql_error());
$interval=mysqli_fetch_array($interval1);
$intervquery2="SELECT DATE_FORMAT('$interval[0]','%m/%d/%Y');";
$interval2= mysql_unbuffered_query($intervquery2, $tryconnection) or die(mysql_error());
$interval=mysqli_fetch_array($interval2);


if (isset($_POST['save'])) {

$expquery="SELECT STR_TO_DATE('$_POST[estexp]','%m/%d/%Y');";
$expdate1= mysql_unbuffered_query($expquery, $tryconnection) or die(mysql_error());
$expdate=mysqli_fetch_array($expdate1);
//format the date into the mysql format
$query_invdatetime="SELECT STR_TO_DATE('$_SESSION[minvdte]','%m/%d/%Y %H:%i:%s')";
$invdatetime= mysql_unbuffered_query($query_invdatetime, $tryconnection) or die(mysql_error());
$row_invdatetime=mysqli_fetch_array($invdatetime);


$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysql_query($query_PREFER, $tryconnection) or die(mysql_error());
$row_PREFER = mysqli_fetch_assoc($PREFER);

$treatmxx=$_SESSION['client']/$row_PREFER['TRTMCOUNT'];
$treatmxx="TREATM".floor($treatmxx);

	$query_CHECKTABLE="SELECT * FROM $treatmxx LIMIT 1";
	$CHECKTABLE= mysql_query($query_CHECKTABLE, $tryconnection) or $none=1;
	
	if (isset($none)){
	$create_TREATMXX="CREATE TABLE $treatmxx LIKE TREATM0";
	$result=mysql_query($create_TREATMXX, $tryconnection) or die(mysql_error());
	}


//insert the heading with the estimate name
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$_SESSION[client]','$_SESSION[patient]','ESTIMATE ".mysql_real_escape_string($_POST['invhype'])."', 32,'71', '".mysql_real_escape_string($_SESSION['invline'][0]['INVDOC'])."', '$row_invdatetime[0]')";
mysql_query($insertSQL, $tryconnection) or die(mysql_error());


$insertSQL = sprintf("INSERT INTO ESTHOLD (INVNO, INVCUST, INVPET, INVDESCR, INVHYPE, DATETIME, PETNAME, ESTEXP) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s')",
 							  "0",
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  "0",
							 mysql_real_escape_string($_POST['invhype']),
							  $row_xnow[0],
							  mysql_real_escape_string($_SESSION['invline'][0]['PETNAME']),
							  $expdate[0]
							  );
mysql_query($insertSQL, $tryconnection);

foreach ($_SESSION['invline'] as $item){

//for those that are estimates
if ($item['INVEST']=='1'){

//makeup the TREATDESC out of the invline values for each invoice item
	if (number_format($item['INVUNITS'],0)==$item['INVUNITS']){
	$invunits = number_format($item['INVUNITS'],0);
	}
	else {
	$invunits = $item['INVUNITS'];
	}

$treatdesc=$invunits.";".$item['INVDESCR'].";".number_format($item['INVTOT'],2);

	if ($item['INVDECLINE']=='1'){
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, HTMAJ, WHO, TREATDATE) VALUES ('$item[INVCUST]','$item[INVPET]','$treatdesc', 32,'77','$item[INVMAJ]', '".mysql_real_escape_string($item['INVDOC'])."', '$row_invdatetime[0]')";
	mysql_query($insertSQL, $tryconnection);
	}

	else {
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, HTMAJ, WHO, TREATDATE) VALUES ('$item[INVCUST]','$item[INVPET]','$treatdesc', 32 ,'72','$item[INVMAJ]', '".mysql_real_escape_string($item['INVSTAFF'])."', '$row_invdatetime[0]')";
	mysql_query($insertSQL, $tryconnection);
	}
	
$insertSQL = sprintf("INSERT INTO ESTHOLD (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVMIN, INVDOC, INVSTAFF, INVUNITS, INVDESCR, INVPRICE, INVTOT, INVINCM, INVDISC, INVLGSM, INVREVCAT, INVGST, INVTAX, REFCLIN, REFVET, INVUPDTE, INVFLAGS, INVDISP, INVGET, INVPERCNT, INVHYPE, AUTOCOMM, INVCOMM, HISTCOMM, MODICODE, INVNARC, INVVPC, INVUPRICE, INVPKGQTY, MEMO, IRADLOG, ISURGLOG, INARCLOG, IUAC, INVSERUM, INVEST, INVDECLINE, PETNAME, INVOICECOMMENT, INVPRU, XDISC, MTAXRATE, TUNITS, TFLOAT, TENTER, LCODE, LCOMMENT, ESTEXP) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
 							  '0',
							  $item['INVCUST'],
							  $item['INVPET'],
							  $row_invdatetime[0],
							  $item['INVMAJ'],
							  $item['INVMIN'],
							  mysql_real_escape_string($item['INVDOC']),
							  mysql_real_escape_string($item['INVSTAFF']),
							  $item['INVUNITS'],
							  mysql_real_escape_string($item['INVDESCR']),
							  $item['INVPRICE'],
							  $item['INVTOT'],
							  $item['INVINCM'],
							  $item['INVDISC'],
							  $item['INVLGSM'],
							  $item['INVREVCAT'],
							  $item['INVGST'],
							  $item['INVTAX'],
							  mysql_real_escape_string($item['REFCLIN']),
							  mysql_real_escape_string($item['REFVET']),
							  $item['INVUPDTE'],
							  $item['INVFLAGS'],
							  $item['INVDISP'],
							  $item['INVGET'],
							  $item['INVPERCNT'],
							  mysql_real_escape_string($_POST['invhype']),
							  mysql_real_escape_string($item['AUTOCOMM']),
							  $item['INVCOMM'],
							  $item['HISTCOMM'],
							  $item['MODICODE'],
							  $item['INVNARC'],
							  $item['INVVPC'],
							  $item['INVUPRICE'],
							  $item['INVPKGQTY'],
							  $item['MEMO'],
							  $item['IRADLOG'],
							  $item['ISURGLOG'],
							  $item['INARCLOG'],
							  $item['IUAC'],
							  $item['INVSERUM'],
							  '1',
							  $item['INVDECLINE'],
							  mysql_real_escape_string($item['PETNAME']),
							  mysql_real_escape_string($item['INVOICECOMMENT']),
							  $item['INVPRU'],
							  $item['XDISC'],
							  $item['MTAXRATE'],
							  $item['TUNITS'],
							  $item['TFLOAT'],
							  $item['TENTER'],
							  mysql_real_escape_string($item['LCODE']),
							  mysql_real_escape_string($item['LCOMMENT']),
							  $expdate[0]
							  );
mysql_query($insertSQL, $tryconnection);
}
}

$insertSQL = sprintf("INSERT INTO ESTHOLD (INVNO, INVCUST, INVPET, INVDESCR, INVTOT, INVHYPE, PETNAME, ESTEXP) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s')",
 							  "0",
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  $nametax,
							  $_POST['xgst'],
							  mysql_real_escape_string($_POST['invhype']),
							  mysql_real_escape_string($_SESSION['invline'][0]['PETNAME']),
							  $expdate[0]
							  );
mysql_query($insertSQL, $tryconnection);

$insertSQL = sprintf("INSERT INTO ESTHOLD (INVNO, INVCUST, INVPET, INVDESCR, INVTOT, INVHYPE, PETNAME, ESTEXP) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s')",
 							  "0",
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  "PST",
							  $_POST['xpst'],
							  mysql_real_escape_string($_POST['invhype']),
							  mysql_real_escape_string($_SESSION['invline'][0]['PETNAME']),
							  $expdate[0]
							  );
mysql_query($insertSQL, $tryconnection);

$insertSQL = sprintf("INSERT INTO ESTHOLD (INVNO, INVCUST, INVPET, INVDESCR, INVTOT, INVHYPE, PETNAME, ESTEXP) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s')",
 							  "0",
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  "TOTAL",
							  $_POST['xtotal'],
							  mysql_real_escape_string($_POST['invhype']),
							  mysql_real_escape_string($_SESSION['invline'][0]['PETNAME']),
							  $expdate[0]
							  );
mysql_query($insertSQL, $tryconnection);


$xtotal=" ;TOTAL;".number_format($_POST['xtotal'],2);
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, HTMAJ, WHO, TREATDATE) VALUES ('".$_SESSION['invline'][0]['INVCUST']."','".$_SESSION['invline'][0]['INVPET']."','$xtotal', 32 ,'72','', '".mysql_real_escape_string($_SESSION['invline'][0]['INVSTAFF'])."', '$row_invdatetime[0]')";
mysql_query($insertSQL, $tryconnection);


$client=$_SESSION['invline'][0]['INVCUST'];
	if ($_SESSION['refID']=='EST'){
	    unset($_SESSION['invline']) ;
//		session_destroy();
//		session_start();
		$_SESSION['client']=$client;
		$closewindow="self.close();  opener.document.location='../../CLIENT/CLIENT_PATIENT_FILE.php';";
	}
	else {
		if ($_GET['refID']=='reserve'){
		$closewindow="self.close(); opener.document.location='PRINT_INVOICE.php';";
		}
		else{
		$closewindow="self.close(); opener.document.location='PAYMENT_ROUTINE.php';";
		} 
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>FINISH BUILDING ESTIMATE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload()
{
<?php echo $closewindow; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+185,toppos+200);
document.est_invoice.invhype.focus();
}

function OnClose()
{
self.close();
}

function bodyonunload()
{

}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>

<form method="post" action="" name="est_invoice" id="est_invoice" style="position:absolute; top:0px; left:0px;">

<span class="Verdana9"><?php ?></span>



				<input type="hidden" name="xgst" value=""  />
				<script type="application/javascript">
					//CALCULATE THE GST TOTAL OF INVOICE ITEMS
					var GST = <?php $GSTtotal = array();
							//TAKE CALCULATED GST's FROM EACH INVOICE ITEM AND INSERT THEM INTO ARRAY 
									foreach ($_SESSION['invline'] as $GSTitem)
									{
										if(($GSTitem['INVEST']=='1' || $_SESSION['minvno']=='0') && $GSTitem['INVDECLINE']!='1'){
										$GSTtotal[]=round($GSTitem['INVGST'],2);
										}
									}
							//SUM UP THE VALUES IN ARRAY & DISPLAY
									echo array_sum($GSTtotal);
								   ?>;
												
					//CONVERT THE DISPLAYED VALUE INTO TWO DECIMAL POINT NUMBER
					var GSTconv = GST.toFixed(2);
					//DISPLAY GST VALUE IN INVOICE PREVIEW
					document.forms[0].xgst.value=GSTconv;
                
                </script>                				
                <input type="hidden" name="xpst" value=""  />
				<script type="application/javascript">
					//CALCULATE THE GST TOTAL OF INVOICE ITEMS
					var PST =<?php $PSTtotal = array();
							//TAKE CALCULATED GST's FROM EACH INVOICE ITEM AND INSERT THEM INTO ARRAY 
									foreach ($_SESSION['invline'] as $PSTitem)
									{
										if(($PSTitem['INVEST']=='1' || $_SESSION['minvno']=='0') && $PSTitem['INVDECLINE']!='1'){
										$PSTtotal[]=round($PSTitem['INVTAX'],2);
										}
									}
							//SUM UP THE VALUES IN ARRAY & DISPLAY
									echo array_sum($PSTtotal);
								   ?>;
												
					//CONVERT THE DISPLAYED VALUE INTO TWO DECIMAL POINT NUMBER
					var PSTconv = PST.toFixed(2);
					//DISPLAY GST VALUE IN INVOICE PREVIEW
					document.forms[0].xpst.value=PSTconv;
                </script>                </td>

                <input type="hidden" name="xtotal" value=""  />				
				<script type="application/javascript">
					//CALCULATE THE TOTAL PRICE INCLUDING GST's
					var price =<?php $INVtotal = array();
						//TAKE THE CALCULATED INDIVIDUAL PRICE OF INVOICE ITEMS AND INSERT THEM INTO ARRAY
                		          foreach ($_SESSION['invline'] as $invtot)
								  {
									if(($invtot['INVEST']=='1' || $_SESSION['minvno']=='0') && $invtot['INVDECLINE']!='1'){
									$INVtotal[]=round($invtot['INVTOT'],2);
									}
                                  }
								  //SUM UP THE INDIVIDUAL PRICES
                                  echo array_sum($INVtotal);
                                 ?> 
								 
								 +  //ADD THE GST
								 
								 <?php 
                                   echo array_sum($GSTtotal);
                                 ?>
								 
								 +
								 
								<?php 
                                   echo array_sum($PSTtotal);
                                 ?>	;
					//CONVERT THE PRICE INTO TWO DECIMAL POINTS
			 		var priceconv = price.toFixed(2);
					//DISPLAY THE RESULT
					document.forms[0].xtotal.value=priceconv;
                
                </script>
                
                






<table width="400" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td width="76" height="65" class="Verdana11">&nbsp;</th>
    <td width="324" height="65" align="left" valign="bottom" class="Verdana11">Name of the estimate</th>  
  </tr>
  <tr>
    <td height="30" class="Verdana11">&nbsp;</td>
    <td height="30" class="Verdana11"><input name="invhype" type="text" class="Input" id="textfield" size="30" maxlength="30" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $_SESSION['invhype']; ?>"/></td>
  </tr>
  <tr>
    <td height="30" class="Verdana11">&nbsp;</td>
    <td height="30" valign="bottom" class="Verdana11">Expiry date</td>
  </tr>
  <tr>
    <td height="30" class="Verdana11">&nbsp;</td>
    <td height="30" class="Verdana11">
            <input name="estexp" type="text" class="Input" id="estexp" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onclick="ds_sh(this,'<?php echo $interval[0]; ?>')" value="<?php echo $interval[0]; ?>" />

    </td>
  </tr>
  <tr>
    <td height="81" colspan="2" class="Verdana11">
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="ButtonsTable">
    <input type="submit" name="save" id="save" value="SAVE" class="button" />
	<input type="button" name="cancel" id="cancel" value="CLOSE" class="button" onclick="self.close();" />    </td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
