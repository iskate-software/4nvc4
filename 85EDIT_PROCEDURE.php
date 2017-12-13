<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

//unset($_SESSION['procline']);

//DELETE ITEM
if (isset($_GET['ref']) && (!isset($_POST['ok']) || !isset($_POST['save']))){
$keyunset=$_GET['ref'];
unset($_SESSION['procline'][$keyunset]);
$_SESSION['procline']=array_merge($_SESSION['procline']);
header("Location:EDIT_PROCEDURE.php?reference=0");
}


if (isset($_GET['reference'])){
$keyupdate=$_GET['reference'];
}


if (isset($_POST['ok']) || isset($_POST['save'])){

	if(!empty($_SESSION['procline'][0]['INVDESCR'])){
		$keyupdate=$_GET['reference'];
		$_SESSION['procline'][$keyupdate]['INVDATETIME'] = $_SESSION['minvdte'].' '.date('H:s:i');
		$_SESSION['procline'][$keyupdate]['INVDOC'] = $_POST['invdoc'];
		$_SESSION['procline'][$keyupdate]['INVUNITS'] = $_POST['invunits'];
		$_SESSION['procline'][$keyupdate]['INVDESCR'] = $_POST['invdescr'];
		$_SESSION['procline'][$keyupdate]['INVPRICE'] = $_POST['invprice'];
		$_SESSION['procline'][$keyupdate]['INVTOT'] = round($_POST['invtot'],2);
		$_SESSION['procline'][$keyupdate]['INVDISC'] = $_POST['invdisc'];
		$_SESSION['procline'][$keyupdate]['INVGST'] = round($_POST['invgst'],2);
		$_SESSION['procline'][$keyupdate]['INVTAX'] = round($_POST['invtax'],2);
		$_SESSION['procline'][$keyupdate]['INVDISP'] = number_format($_POST['invdisp'],2);
		
		if (!empty($_POST['commtext'])){
		$_SESSION['procline'][$keyupdate]['AUTOCOMM'] = $_POST['tautocomm'];
		$_SESSION['procline'][$keyupdate]['INVCOMM'] = !empty($_POST['invcomm']) ? "1" : "0";
		$_SESSION['procline'][$keyupdate]['HISTCOMM'] = !empty($_POST['histcomm']) ? "1" : "0";
		$_SESSION['procline'][$keyupdate]['INVOICECOMMENT'] = $_POST['commtext'];
		}
		
	if (!empty($_SESSION['procline'][$keyupdate]['INVOICECOMMENT']) && $_SESSION['procline'][$keyupdate]['INVPRU']!='1'){
	$_SESSION['procline'][$keyupdate]['MEMO']='3';
	}
				
	if ($keyupdate > $_POST['seq']){
		$myarray = $_SESSION['procline'];
		$myarray1= array_slice($myarray, ($_POST['seq']-1));
		$myarray2 = array_slice($myarray, 0, ($_POST['seq']-1));
		$myarray2[] = $myarray[$keyupdate];
		
		$myarray = array_merge($myarray2,$myarray1);
		unset($myarray[$keyupdate+1]);
		$myarray=array_merge($myarray);
		$_SESSION['procline']=$myarray;
		}

	elseif ($keyupdate < $_POST['seq']){
		$myarray = $_SESSION['procline'];
		$myarray1= array_slice($myarray, ($_POST['seq']));
		$myarray2 = array_slice($myarray, 0, ($_POST['seq']));
		$myarray2[] = $myarray[$keyupdate];
		
		$myarray = array_merge($myarray2,$myarray1);
		unset($myarray[$keyupdate]);
		$myarray=array_merge($myarray);
		$_SESSION['procline']=$myarray;
		}


		
//		$_SESSION['procline'] = array_splice($_SESSION['procline'],0,0,$_SESSION['procline'][$keyupdate]);
//		$_SESSION['procline'][]=;
//		$_SESSION['procline']=array_merge($_SESSION['procline'],$_SESSION['procline2']);
		}
		if (isset($_POST['save']) && $_SESSION['minvno']!='0'){
				foreach ($_SESSION['procline'] as $key => $value) {
				$_SESSION['procline'][$key]['INVEST']='0';
				$_SESSION['procline'][$key]['INVDECLINE']='0';
				}
				foreach ($_POST['est'] as $est){
				$_SESSION['procline'][$est]['INVEST']="1";
				}
				foreach ($_POST['dec'] as $dec){
				$_SESSION['procline'][$dec]['INVDECLINE']="1";
				}
		$closewin="self.close();";	
		}
		
		else if (isset($_POST['save']) && $_SESSION['minvno']=='0'){
				foreach ($_SESSION['procline'] as $key => $value) {
				$_SESSION['procline'][$key]['INVDECLINE']='0';
				}
				foreach ($_POST['dec'] as $dec){
				$_SESSION['procline'][$dec]['INVDECLINE']="1";
				}
		$closewin="self.close();";	
		}
		
		else if (isset($_POST['save'])){
		$closewin="self.close();";	
		}

		
}
mysqli_select_db($tryconnection, $database_tryconnection);
$query_DOCTOR = sprintf("SELECT DOCTOR FROM DOCTOR ORDER BY DOCTOR ASC");
$DOCTOR = mysqli_query($tryconnection, $query_DOCTOR) or die(mysqli_error($mysqli_link));
$row_DOCTOR = mysqli_fetch_assoc($DOCTOR);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title id="title">EDIT PROCEDURE ITEMS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" src="../../ASSETS/calculation.js"></script>

<script type="text/javascript">
function OnClose()
{
self.close();
}

function bodyonload()
{
<?php echo $closewin; ?>
document.getElementById('title').innerText="EDIT "+sessionStorage.refID;
document.getElementById('A<?php echo $_GET['reference']; ?>').bgColor="#DCF6DD";
document.tff.invprice.focus();
}

function Cursor(x) 
{
document.getElementById(x).style.cursor="pointer";
}


function deletion(x)
{
//self.location="EDIT_PROCEDURE.php?ref=" + x + "&reference=0";
self.location="EDIT_PROCEDURE.php?ref=" + x;
}


function saving()
{
//self.close();
opener.document.location='UPDATE_PROCEDURE.php?product=j&record=k&subcat=i';
}

function bodyonunload(){
opener.document.location='UPDATE_PROCEDURE.php?product=j&record=k&subcat=i';
}


function modifyinvoice(x)
{
self.location="EDIT_PROCEDURE.php?reference=" + x;
}

function calculateprice(){
//takes quantity
var quantity = document.forms[0].invunits.value;
var invdisp=document.forms[0].invdisp.value;
var unitprice = document.forms[0].invprice.value;

var result = quantity * unitprice;
var result = result+parseFloat(invdisp);			

var resultrounded = Math.round(result*Math.pow(10,2))/Math.pow(10,2);
//converts into two decimal points
var resultfull = resultrounded.toFixed(2);
//inserts it into 'invtot' input field
document.forms[0].invtot.value = resultfull;
}

</script>

<style type="text/css">
<!--
.Verdana11 {	color: #000000; 
	font-family: Verdana;
	font-size:11px; 
}

.Verdana11B {	color: #000000; 
	font-family: Verdana;
	font-size:11px; 
	font-weight:bold;
}

#table {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
.style1 {color: #FFFFFF}
.style2 {color: #666666}
.style3 {color: #CC6600}
.Verdana11B1 {color: #000000; 
	font-family: Verdana;
	font-size:11px; 
	font-weight:bold;
}
.Verdana111 {color: #000000; 
	font-family: Verdana;
	font-size:11px; 
}


-->
</style>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="tff" >

<!--HIDDEN FIELDS FOR CALCULATIONS-->
<!--ARCUSTO-->
<input type="hidden" name="invdisc" value="<?php echo $_SESSION['procline'][$keyupdate]['INVDISC']; ?>" />
<input type="hidden" name="invpercnt" value="<?php echo $_SESSION['procline'][$keyupdate]['INVPERCNT']; ?>" />
<input type="hidden" name="refclin" value="<?php echo $_SESSION['procline'][$keyupdate]['REFCLIN'];?>" />
<input type="hidden" name="refvet" value="<?php echo $_SESSION['procline'][$keyupdate]['REFVET'];?>" />
<input type="hidden" name="ptax" value="<?php echo $_SESSION['PTAX'];?>" />
<input type="hidden" name="gtax" value="<?php echo $_SESSION['GTAX'];?>" />
<input type="hidden" name="xdisc" value="<?php echo $_SESSION['procline'][$keyupdate]['XDISC'];?>" />
<!-- VETCAN (TREATMENTFEEFILE) -->
<input type="hidden" name="invmaj" value="<?php echo $_SESSION['procline'][$keyupdate]['INVMAJ']; ?>" />
<input type="hidden" name="invmin" value="<?php echo $_SESSION['procline'][$keyupdate]['INVMIN']; ?>" />
<input type="hidden" name="invincm" value="<?php echo $_SESSION['procline'][$keyupdate]['INVINCM']; ?>" />
<input type="hidden" name="invrevcat" value="<?php echo $_SESSION['procline'][$keyupdate]['INVREVCAT']; ?>" />
<input type="hidden" name="invflags" value="<?php echo $_SESSION['procline'][$keyupdate]['INVFLAGS']; ?>" />
<input type="hidden" name="invget" value="<?php echo $_SESSION['procline'][$keyupdate]['INVGET']; ?>" />
<input type="hidden" name="modicode" value="<?php echo $_SESSION['procline'][$keyupdate]['MODICODE']; ?>" />
<input type="hidden" name="iradlog" value="<?php echo $_SESSION['procline'][$keyupdate]['IRADLOG']; ?>" />
<input type="hidden" name="isurlog" value="<?php echo $_SESSION['procline'][$keyupdate]['ISURLOG']; ?>" />
<input type="hidden" name="inarclog" value="<?php echo $_SESSION['procline'][$keyupdate]['INARCLOG']; ?>" />
<input type="hidden" name="iuac" value="<?php echo $_SESSION['procline'][$keyupdate]['IUAC']; ?>" />
<input type="hidden" name="invserum" value="<?php echo $_SESSION['procline'][$keyupdate]['INVSERUM']; ?>" />
<input type="hidden" name="invupdte" value="<?php echo $_SESSION['procline'][$keyupdate]['INVUPDATE']; ?>" /><!--needed at the end of invoice to say if there is anything to update in the patient file-->
<input type="hidden" name="mtaxrate" value="<?php echo $_SESSION['procline'][$keyupdate]['MTAXRATE']; ?>" />
<input type="hidden" name="tunits" value="<?php echo $_SESSION['procline'][$keyupdate]['TUNITS']; ?>" /><!--if invunits is editable-->  
<input type="hidden" name="tfloat" value="<?php echo $_SESSION['procline'][$keyupdate]['TFLOAT']; ?>" /><!--if invunits is a float or integer-->   
<input type="hidden" name="tenter" value="<?php echo $_SESSION['procline'][$keyupdate]['TENTER']; ?>" /><!--if the description is editable-->
<!-- PETMAST	-->                
<input  type="hidden"name="petname" value="<?php echo $_SESSION['procline'][$keyupdate]['PETNAME'];?>" />			
<!-- PHP/JAVASCRIPT GENERATED -->
<input type="hidden" name="invgst" id="invgst" value="<?php echo $_SESSION['procline'][$keyupdate]['INVGST'];?>" /> <!-- GST TOTAL-->
<input type="hidden" name="invtax" value="<?php echo $_SESSION['procline'][$keyupdate]['INVTAX'];?>" /> <!-- PST TOTAL-->

<!-- OTHER FOR CALCULATIONS -->
<!--SET TO 0 TO SKIP THE CALCULATIONS OF PACKAGE PRICE-->
<input type="hidden" name="pkgprice" value="0" />
<input type="hidden" name="pkgs" id="pkgs" value="0<?php //echo $_SESSION['procline'][$keyupdate]['PKGS']; ?>" />
<input type="hidden" name="pkgqty" value="" />
<input type="hidden" name="markup" value="0" />
<input type="hidden" name="cost" value="" />
<input type="hidden" name="dfyes" value="" />
<input type="hidden" name="result" value="" />
<input type="hidden" name="bulk" value="" />
<input type="hidden" name="dispfee" value="" />
<input type="hidden" name="bdispfee" value="" />
<!--these are just dummy elements, they are not needed here, but they are needed in the calculations file-->
<input type="hidden" name="days" value="" />
<input type="hidden" id="spkgs"  />
<input type="hidden" id="qty"  />
<input type="hidden" id="fullpkg"  />
<input type="hidden" name="full" id="full"/>
<input type="hidden" id="pkgcount"  />
<!--INVPRU IS ALWAYS 0 FOR CALCULATIONS - IF THEY WANT TO MODIFY A LOOKUP ITEM, THEY HAVE TO DELETE THE ITEM AND ENTER AGAIN-->
<input type="hidden" name="xlabel" value="0" />

<input type="hidden" name="xseq" value="" />
<input type="hidden" name="abctotal" value="" />
<input type="hidden" name="abccost" value="" />

<input type="hidden" name="salmon" value="2" />
<table class="table" width="" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="position:absolute; top:0px; left:0px;">
  <tr>
    <td>
    <table width="726" height="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
      <tr id="nadpis">
        <td height="16" colspan="10" align="center" valign="top" class="Verdana11BPink">MODIFY PROCEDURE ITEM</td>
      </tr>
      <tr class="Verdana11B">
        <td width="11" valign="bottom">&nbsp;</td>
        <td width="66" align="right" valign="bottom">Seq&nbsp;</td>
        <td height="10" colspan="2">&nbsp;Product/Service</td>
        <td height="10" colspan="2" align="center">Uprice&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Qty&nbsp;</td>
        <td height="10" align="center">Price&nbsp;</td>
        <td width="98" align="center">Disp.Fee</td>
        <td width="135" colspan="2" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td width="11" height="30" valign="top" class="Labels2">&nbsp;</td>
        <td width="66" height="30" align="right" valign="top">
          
          <input name="seq" id="seq" type="text" class="Inputright" size="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo ($keyupdate+1); ?>" onkeyup="calculateprice();" />          </td>
        


       <td height="30" colspan="<?php if ($_SESSION['procline'][$keyupdate]['MEMO']=='1' || $_SESSION['procline'][$keyupdate]['INVSERUM']=='2'){echo "6";} else {echo '2';}?>" valign="top" class="Labels2">
        <input type="hidden" name="check1" value="1"/>
        <input name="invdescr" type="text"  id="invdescr" class="Input" size="<?php if ($_SESSION['procline'][$keyupdate]['MEMO']=='1' || $_SESSION['procline'][$keyupdate]['INVSERUM']=='2'){echo "65";} else {echo '25';}?>" value="<?php echo $_SESSION['procline'][$keyupdate]['INVDESCR']; ?>" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" />
       </td>
       <td height="30" colspan="2" align="right" valign="top" class="Labels2" <?php if ($_SESSION['procline'][$keyupdate]['MEMO']=='1' || $_SESSION['procline'][$keyupdate]['INVSERUM']=='2'){echo "style='display:none'";}?>>
       <input type="text" name="invprice" id="invprice" class="Inputright" value="<?php echo number_format($_SESSION['procline'][$keyupdate]['INVPRICE'], 2,'.','');?>" size="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice();" />                
       <input name="invunits" id="invunits" type="text" class="Inputright" value="<?php echo $_SESSION['procline'][$keyupdate]['INVUNITS']; ?>" size="5" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" title="number of units" onkeyup="calculateprice();" style="width:35px;"/>         
        </td>
        <td width="98" height="30" align="center" valign="top" class="Labels2" <?php if ($_SESSION['procline'][$keyupdate]['MEMO']=='1' || $_SESSION['procline'][$keyupdate]['INVSERUM']=='2'){echo "style='display:none'";}?>>
<input name="invtot" id="invtot" type="text" class="Inputright" size="8" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo number_format($_SESSION['procline'][$keyupdate]['INVTOT'], 2,'.','');?>" readonly="readonly" style="width:55px;"/>        
		</td>
        <td height="30" align="center" valign="top" class="Labels2" <?php if ($_SESSION['procline'][$keyupdate]['MEMO']=='1' || $_SESSION['procline'][$keyupdate]['INVSERUM']=='2'){echo "style='display:none'";}?>>
        <input name="invdisp" id="invdisp" type="text" class="Inputright" value="<?php echo number_format($_SESSION['procline'][$keyupdate]['INVDISP'],2); ?>" size="8" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice();" style="width:55px;"/>        
        </td>
        <td height="30" colspan="2" align="center" valign="top" class="Labels2">&nbsp;</td>
      </tr>
      <tr <?php //if ($_SESSION['procline'][$keyupdate]['MEMO']=='2'){echo "title='If you need to modify the label, please delete the drug from the invoice and enter again.'";} ?>>
        <td valign="middle" class="Labels2">&nbsp;</td>
        <td align="right" valign="middle"><span class="Verdana11B1">Comment</span></td>
        <td width="78" height="10" valign="middle" class="Labels2"><input name="tautocomm" id="tautocomm" type="text" class="Inputright" value="<?php echo $_SESSION['procline'][$keyupdate]['AUTOCOMM']; ?>" size="8" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" <?php //if ($_SESSION['procline'][$keyupdate]['MEMO']=='2'){echo "readonly";} ?>/></td>
        <td width="130" valign="middle" class="Labels2">
          <input type="button" name="view" id="view" value="VIEW" onclick="window.open('../COMMENTS/COMMENTS_LIST.php?path=LABEL&display=INVOICE','_blank')" <?php //if ($_SESSION['procline'][$keyupdate]['MEMO']=='2'){echo "disabled";} ?>/>
          <input type="button" name="clear" id="clear" value="CLEAR" onclick="document.tff.commtext.value='';" <?php //if ($_SESSION['procline'][$keyupdate]['MEMO']=='2'){echo "disabled";} ?>/>
        </td>
        <td height="10" colspan="2" align="right" valign="middle" class="Labels2">
          <label><input type="checkbox" name="invcomm" id="invcomm" <?php if ($_SESSION['procline'][$keyupdate]['INVCOMM']=='0'){echo "";} else {echo "checked='checked'";} ?> />On Invoice</label>
        </td>
        <td height="10" align="left" valign="middle" colspan="2" class="Labels2">
        <label><input type="checkbox" name="histcomm" id="histcomm" <?php if ($_SESSION['procline'][$keyupdate]['HISTCOMM']=='0'){echo "";} else {echo "checked='checked'";}?>/>In History</label>
        </td>
        <td height="10" colspan="2" align="center" valign="middle" class="Verdana9Red">
        </td>
        </tr>
      <tr>
        <td valign="top" class="Labels2">&nbsp;</td>
        <td align="right" valign="top">&nbsp;</td>
        <td height="10" colspan="6" valign="top" class="Labels2"><textarea name="commtext" cols="60" rows="3" class="commentarea" id="commtext" <?php //if ($_SESSION['procline'][$keyupdate]['MEMO']=='2'){echo "readonly";} ?>><?php echo $_SESSION['procline'][$keyupdate]['INVOICECOMMENT']; ?></textarea></td>
        <td colspan="2" align="center" valign="middle" class="Labels2"><span class="Labels">
          <input name="ok" type="submit" class="button" value="SAVE" />
        </span></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td height="4"></td>
        <td></td>
        <td width="33"></td>
        <td width="77"></td>
        <td>      
        <td></td>
        <td colspan="2"></td>
      </tr>        
    </table>
    </td>
  </tr>
  <tr>
    <td height="200" valign="top">
    <table width="730" border="0" cellspacing="0" cellpadding="0">
		<tr>
           <td colspan="14" height="24" align="center" valign="top" class="Verdana13B">Procedure:&nbsp;&nbsp;<?php echo $_SESSION['procname'];  ?></td>
        </tr>
        <tr bgcolor="#FFFFFF" class="Verdana9" height="15">
          <td width="150" align="right" >Seq</td>
          <td width="50" align="center" >Units</td>
          <td width="250">Item description</td>
          <td width="50" align="right" >U.Price</td>
          <td width="50" align="right" >Price</td>
          <td width="50" align="right" >D. Fee</td>
          <td width="130">&nbsp;</td>
        </tr>
        
     <tr>
    <td colspan="9">
    
     <div id="invpreview" style="height:220px; overflow:auto;">

     <table width="730" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="145" align="right" height="5"></td>
          <td width="55"></td>
          <td width="250"></td>
          <td width="55"></td>
          <td width="55"></td>
          <td width="55"></td>
          <td width=""></td>
          <td width=""></td>
        </tr>
            
            	<?php 
	
	foreach ($_SESSION['procline'] as $key => $value) {
	
	
			if ($value['MEMO']=='1' || $value['INVSERUM']=='2'){
		echo ' <tr class="Verdana9" id="A'.$key.'">
	            <td height="10" align="right" class="Verdana11">'.($key+1).'</td>
	            <td height="10"></td>
				<td colspan="7" id="B'.$key.'" onclick="modifyinvoice('.$key.')" onmouseover="Cursor(this.id)">'.$value['INVDESCR'].'</td>
          		<td id="'.$key.'" align="center" class="Verdana13BRed" onclick="deletion('.$key.')" onmouseover="Cursor(this.id)" title="Remove this item">X<input type="hidden" name="check2" value="'.$key.'"/></td>
			  </tr>';
		}
		
			else if ($value['MEMO']=='S'){
		echo '';
		}

		
		else {
	echo '<tr id="A'.$key.'">
          <td height="10" align="right" class="Verdana11">'.($key+1).'</td>
          <td height="10" align="right" class="Verdana11">';
		  
		if (number_format($value['INVUNITS'],0)==$value['INVUNITS']){
		echo  number_format($value['INVUNITS'],0);
		}
		else {
		echo $value['INVUNITS'];
		}
		  
	echo  '&nbsp;&nbsp;&nbsp;</td>
          <td height="10" class="Verdana11" id="B'.$key.'" onclick="modifyinvoice('.$key.')" onmouseover="Cursor(this.id)">'.$value['INVDESCR'].'</td>
          <td height="10" align="right" class="Verdana11 style2">'.$value['INVPRICE'].'</td>
          <td height="10" align="right" class="Verdana11">'.number_format($value['INVTOT'],2,'.','').'</td>
          <td height="10" align="right" class="Verdana11 style3">';
		  
		  if ($value['INVDISP']=='0.00' || empty($value['INVDISP'])){echo " ";} else {echo number_format($value['INVDISP'],2);}
		  
		  echo '</td>
          <td align="right" class="Verdana11">&nbsp;</td>
          <td id="'.$key.'" align="center" class="Verdana13BRed" onclick="deletion('.$key.')" onmouseover="Cursor(this.id)" title="Remove this item">X<input type="hidden" name="check2" value="'.$key.'"/></td>
        </tr>';
									
									
									
		}
	}
		?>
            </table>
            </div>
    </td>
  	</tr>  
    
    <tr>
    <td colspan="13" align="left" class="Verdana9">
	<?php if (!empty($_SESSION['writein'])){echo "*This invoice contains also Write-In comments.";} ?>
    </td>
  	</tr>  

    <tr>
    <td colspan="13" align="center" valign="middle" class="ButtonsTable">
    <input name="save" type="submit" class="button" id="save" value="OK" onclick="saving()" />
    <input name="button2" type="button" class="button" id="button2" value="CLOSE" onclick="self.close();" />
    </td>
  	</tr>  
        </table>
    </td>
  </tr>
</table>
</form>




<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
