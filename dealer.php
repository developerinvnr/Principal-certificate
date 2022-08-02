<?php
session_start();
require_once('config/config.php');
include('function.php');
if(check_user()==false) {header("Location: login.php");}
/*----------------------------*/
if(isset($_POST['submit'])){
	$sql = mysql_query("SELECT dealer_id FROM dealer WHERE ai_id = ".$_POST['aiName']." AND dealer_name='".$_POST['dealerName']."' AND location_id=".$_POST['city'],$link) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*----------------------------*/
	if($count>0)
		echo '<script>alert("Duplication Error! can\'t insert dealer / subdealer.");</script>';
	else {
		$sql = mysql_query("SELECT Max(dealer_id) as maxid FROM dealer",$link);
		$row = mysql_fetch_assoc($sql);
		$did = ($row["maxid"]==null ? 1 : $row["maxid"]+1);
		$res = mysql_query("INSERT INTO dealer (dealer_id, ai_id, parent_id, dealer_name, category, owner_name, phone_no, location_id, licence_for) VALUES (".$did.", ".$_POST['aiName'].", ".$_POST['parentName'].", '".$_POST['dealerName']."', '".$_POST['category']."', '".$_POST['ownerName']."', '".$_POST['phone']."', ".$_POST['city'].", ".$_POST['licenceFor'].")",$link) or die(mysql_error());
	}
}
/*----------------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Princicate Ver 1.0</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/common.js" type="text/javascript"></script>
<script language="javascript" src="js/prototype.js" type="text/javascript"></script>
<script language="javascript" src="js/ajax.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
function check_value(value1)
{
	var Amount = value1.charAt(value1.length-1);
	var Numfilter=/^[0-9.]+$/;
	var test_num = Numfilter.test(Amount);
	if(!test_num){
		alert("Please Enter Only numeric data!");
		if(value1.length>0){
			document.getElementById("phone").value=document.getElementById("phone").value.substring(0,value1.length-1);
		}
		return false;
	} else {
		return true;
	}
}

function validate_dealer()
{
	if(document.getElementById("aiName").value==0){
		alert("* Please select area incharge name !!");
		document.getElementById("aiName").focus();
		return false;
	}
	if(document.getElementById("dealerName").value==""){
		alert("* Please input party name !!");
		document.getElementById("dealerName").focus();
		return false;
	}
	if(document.getElementById("ownerName").value==""){
		alert("* Please input owner name !!");
		document.getElementById("ownerName").focus();
		return false;
	}
	if(document.getElementById("phone").value==""){
		alert("* Please input mobile / phone no. !!");
		document.getElementById("phone").focus();
		return false;
	}
	if(document.getElementById("phone").value!="" && ! IsNumeric(document.getElementById("phone").value)){
		alert("* Please input valid (numeric only) mobile / phone no. !!");
		document.getElementById("phone").focus();
		return false;
	}
	if(document.getElementById("category").value!="B"){
		if(document.getElementById("parentName").value==0){
			if(document.getElementById("category").value=="D"){
				alert("* Please select distributor name !!");
				show_hide_parent("D");
			} else if(document.getElementById("category").value=="S"){
				alert("* Please select dealer name !!");
				show_hide_parent("S");
			}
			document.getElementById("parentName").focus();
			return false;
		}
	}
	if(document.getElementById("city").value==0){
		alert("* Please select city !!");
		document.getElementById("city").focus();
		return false;
	}
	document.getElementById("submit").style.display='none';
}

function show_hide_parent(value1)
{
	if(value1=="B"){
		document.getElementById("pid").style.display='none';
		document.getElementById("parentName").value=0;
		document.getElementById("spanParent").innerHTML='Distributor Name :';
	} else if(value1=="D"){
		document.getElementById("pid").style.display='';
		document.getElementById("spanParent").innerHTML='Distributor Name :';
		get_parent_list(document.getElementById("aiName").value, "B");
	} else if(value1=="S"){
		document.getElementById("pid").style.display='';
		document.getElementById("spanParent").innerHTML='Dealer Name :';
		get_parent_list(document.getElementById("aiName").value, "D");
	}
}
</script>
</head>

<body>
<div id="container">
	<div id="header">
		<h1>Princicate</h1>
		<h2>Ver 1.0 (Principal Certificate)</h2>
		<h4 align="right">Welcome : <?php echo $_SESSION['princicate_userid'];?>&nbsp;&nbsp;</h4>
	</div>
	<div id="content">
	<form name="dealer" method="post" onsubmit="return validate_dealer()">
	<fieldset><legend><b>Dealer / Subdealer Entry Page</b></legend>
	<table border="0" width="100%">
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td id="fDTxt">Area Incharge Name :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>
		<td>&nbsp;</td>
		<?php if($_SESSION['princicate_utype']=="S" || $_SESSION['princicate_utype']=="A"){?>
		<td id="fDInp"><select name="aiName" id="aiName" style="width:265px;" onchange="get_state_on_ai(this.value)"><option value="0">-- Select --</option><?php 
		$sql_ai = mysql_query("SELECT * FROM area_incharge INNER JOIN users ON area_incharge.uid = users.uid WHERE user_type='U' AND user_status='A' ORDER BY ai_name",$link) or die(mysql_error());
		while($row_ai = mysql_fetch_array($sql_ai)){
			echo '<option value="'.$row_ai['ai_id'].'">'.$row_ai['ai_name'].'</option>';
		} ?>
		</select>&nbsp;&nbsp;<a onclick="window.open('areaincharge.php','areaincharge','width=1100,height=500,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0"/></a></td>
		<?php } elseif($_SESSION['princicate_utype']=="U"){
		$sql_ai = mysql_query("SELECT area_incharge.*, state_name FROM area_incharge INNER JOIN state ON area_incharge.state_id = state.state_id WHERE uid=".$_SESSION['princicate_uid'],$link) or die(mysql_error());
		$row_ai = mysql_fetch_assoc($sql_ai);?>
		<td id="fDInp"><input name="areaIncharge" id="areaIncharge" readonly="true" size="40" value="<?php echo $row_ai['ai_name'];?>" style="background-color:#C2CEF5;"><input type="hidden" name="aiName" id="aiName" value="<?php echo $row_ai['ai_id'];?>" /></td>
		<?php } ?>
	</tr>
	<tr>
		<td id="fDTxt">Party name :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>
		<td>&nbsp;</td>
		<td id="fDInp"><input name="dealerName" id="dealerName" maxlength="50" size="40" value="" /></td>
	</tr>
	<tr>
		<td id="fDTxt">Category :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>
		<td>&nbsp;</td>
		<td id="fDInp"><select name="category" id="category" style="width:100px;" onchange="show_hide_parent(this.value)"><option value="D">Dealer</option><option value="S">Sub Dealer</option><option value="B">Distributor</option></select></td>
	</tr>
	<tr>
		<td id="fDTxt">Licence For :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>
		<td>&nbsp;</td>
		<td id="fDInp"><select name="licenceFor" id="licenceFor" style="width:100px;" ><option value="2">Vegetable</option><option value="1">Paddy</option><option value="3">Both</option></select></td>
	</tr>
	<tr>
		<td id="fDTxt">Owner name :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>
		<td>&nbsp;</td>
		<td id="fDInp"><input name="ownerName" id="ownerName" maxlength="50" size="40" value="" /></td>
	</tr>
	<tr>
		<td id="fDTxt">Phone no. :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>
		<td>&nbsp;</td>
		<td id="fDInp"><input name="phone" id="phone" maxlength="15" size="40" value="" onkeyup="return check_value(this.value);"></td>
	</tr>
	<tr id="pid">
		<td id="fDTxt"><span id="spanParent">Distributor Name :</span>&nbsp;<span style="color:red; font-weight:bold;">*</span></td>
		<td>&nbsp;</td>
		<td id="fDInp"><span id="spanPList"><select name="parentName" id="parentName" style="width:265px"><option value="0">-- Select --</option></select></span></td>
	</tr>
	<tr>
		<td id="fDTxt">City :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>
		<td>&nbsp;</td>
		<td id="fDInp"><span id="divLoc"><select name="city" id="city" style="width:265px"><option value="0">-- Select --</option>
		<?php if($_SESSION['princicate_utype']=="U"){
			$sql_city = mysql_query("SELECT * FROM city WHERE state_id=".$row_ai['state_id']." ORDER BY city_name",$link);
			while($row_city = mysql_fetch_array($sql_city)){
				echo '<option value="'.$row_city['city_id'].'">'.$row_city['city_name'].'</option>';
			}
		}?>
		</select></span>&nbsp;&nbsp;<a onclick="window.open('city.php','city','width=1100,height=500,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0"/></a></td>
	</tr>
	<tr>
		<td id="fDTxt">State : </td>
		<td>&nbsp;<input type="hidden" name="stateID" id="stateID" value="0" /></td>
		<?php if($_SESSION['princicate_utype']=="S" || $_SESSION['princicate_utype']=="A"){?>
			<td id="fDInp"><input name="state" id="state" readonly="true" size="40" style="background-color:#C2CEF5;"></td>
		<?php } elseif($_SESSION['princicate_utype']=="U"){?>
			<td id="fDInp"><input name="state" id="state" readonly="true" size="40" value="<?php echo $row_ai['state_name'];?>" style="background-color:#C2CEF5;"></td>
		<?php } ?>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit" name="submit" id="submit" value="Submit" />&nbsp;&nbsp;<input type="reset" name="reset" id="reset" value="Reset" /> &nbsp;&nbsp;<input type="button" name="exit" id="exit" value="Exit" onclick="window.location='menu.php'" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table>
	</fieldset>
	</form>
	<p><br /></p>
	</div>
	<div id="navigation"> 
		<h3><a href="menu.php"><img src="images/home.png" style="display:inline;cursor:hand;" title="Home" height="30" width="35" border="0"/></a><a href="uprofile.php"><img src="images/user.png" style="display:inline;cursor:hand;" title="User Profile" height="30" width="48" border="0"/></a><a href="logout.php"><img src="images/logout.png" style="display:inline;cursor:hand;" title="LogOut" height="30" width="50" border="0"/></a></h3>
		<ul>
			<li><a href="dealer.php">Dealer / Subdealer Entry</a></li>
			<?php if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S"){?>
			<li><a href="plicence.php">Principal Licence Input</a></li>
			<li><a href="lstplicence.php">List of Principal Licence</a></li>
			<li><a href="pcissue.php">Issue of PC</a></li>
			<li><a href="pcapproval.php">Approval of PC</a></li>
			<li><a href="lstpcrenewal.php">Renewal of PC</a></li>
			<?php } ?>
			<li><a href="pcprint.php">Print of PC</a></li>
			<li><a href="lstdealer.php">Dealer / Subdealer List</a></li>
			<li><a href="lstpcissue.php">Issue of PC List</a></li>
			<li><a href="dlrpcdetails.php">Dealer PC Details</a></li>
		</ul>
	</div>
	<div id="footer">VNR Seeds Pvt. Ltd.<br /></div>
	<div id="loading_screen"><img src="images/loading.gif" alt="Loading Image" align="middle" border="0" /></div>
</div>
</body>
</html>