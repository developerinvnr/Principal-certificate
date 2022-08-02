<?php

session_start();

require_once('config/config.php');

include('function.php');

if(check_user()==false){header("Location: login.php");}

/*----------------------------*/

$sql = mysql_query("SELECT dealer.*, ai_name, area_incharge.state_id, state_name FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id INNER JOIN state ON area_incharge.state_id = state.state_id WHERE dealer_id =".$_REQUEST['did'],$link) or die(mysql_error());

$row = mysql_fetch_assoc($sql);

/*----------------------------*/

$x="lstdealer.php?sid=".$_REQUEST['sid']."&cgr=".$_REQUEST['cgr']."&dds=".$_REQUEST['dds'];

if($_REQUEST['pg']>0){$x .= "&pg=".$_REQUEST['pg']."&tr=".$_REQUEST['tr'];}

/*----------------------------*/

if(isset($_POST['update'])){

	$sql_edit = mysql_query("SELECT dealer_id FROM dealer WHERE ai_id=".$row['ai_id']." AND dealer_name='".$_POST['dealerName']."' AND location_id=".$_POST['city'],$link) or die(mysql_error());

	$row_edit = mysql_fetch_assoc($sql_edit);

	$count = mysql_num_rows($sql_edit);

	/*-------------------------------------*/

	if($count>0){

		if($row_edit['dealer_id']!=$_REQUEST['did'])

			$msg = "Duplication Error! can&prime;t to update dealer / subdealer.";

		elseif($row_edit['dealer_id']==$_REQUEST['did']){

			$res = mysql_query("UPDATE dealer SET parent_id=".$_POST['parentName'].", dealer_name='".$_POST['dealerName']."', category='".$_POST['category']."', owner_name='".$_POST['ownerName']."', phone_no='".$_POST['phone']."', location_id=".$_POST['city'].", licence_for=".$_POST['licenceFor']." WHERE dealer_id=".$_REQUEST['did'],$link) or die(mysql_error());

			header("Location:".$x);

		}

	} else {

		$res = mysql_query("UPDATE dealer SET parent_id=".$_POST['parentName'].", dealer_name='".$_POST['dealerName']."', category='".$_POST['category']."', owner_name='".$_POST['ownerName']."', phone_no='".$_POST['phone']."', location_id=".$_POST['city'].", licence_for=".$_POST['licenceFor']." WHERE dealer_id=".$_REQUEST['did'],$link) or die(mysql_error());

		header("Location:".$x);

	}

}

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

	document.getElementById("update").style.display='none';

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

		<td colspan="3"><?php if($msg != "") echo '<font color="blue" size="">'.$msg.'</font>'; ?></td>

	</tr>

	<tr>

		<td id="fDTxt">Area Incharge Name :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input type="text" name="areainchargeName" id="areainchargeName" size="40" readonly="true" value="<?php echo $row['ai_name'];?>" style="background-color:#C2CEF5;"/><input type="hidden" name="aiName" id="aiName" value="<?php echo $row['ai_id'];?>" /></td>

	</tr>

	<tr>

		<td id="fDTxt">Party name :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><input type="text" name="dealerName" id="dealerName" maxlength="50" size="40" value="<?php echo $row['dealer_name'];?>"/></td>

	</tr>

	<tr>

		<td id="fDTxt">Category :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><select name="category" id="category" style="width:100px;" onchange="show_hide_parent(this.value)">

		<?php if($row['category'] == "D")

			echo '<option selected value="D">Dealer</option><option value="S">Sub Dealer</option><option value="B">Distributor</option>';

		elseif($row['category'] == "S")

			echo '<option value="D">Dealer</option><option selected value="S">Sub Dealer</option><option value="B">Distributor</option>';

		elseif($row['category'] == "B")

			echo '<option value="D">Dealer</option><option value="S">Sub Dealer</option><option selected value="B">Distributor</option>';

		?></select></td>

	</tr>

	<tr>

		<td id="fDTxt">Licence For :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><select name="licenceFor" id="licenceFor" style="width:100px;" >

		<?php if($row['licence_for'] == 1)

			echo '<option selected value="1">Paddy</option><option value="2">Vegetable</option><option value="3">Both</option>';

		elseif($row['licence_for'] == 2)

			echo '<option value="1">Paddy</option><option selected value="2">Vegetable</option><option value="3">Both</option>';

		elseif($row['licence_for'] == 3)

			echo '<option value="1">Paddy</option><option value="2">Vegetable</option><option selected value="3">Both</option>';

		?></select></td>

	</tr>

	<tr>

		<td id="fDTxt">Owner name :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><input type="text" name="ownerName" id="ownerName" maxlength="50" size="40" value="<?php echo $row['owner_name'];?>"/></td>

	</tr>

	<tr>

		<td id="fDTxt">Phone no. :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><input type="text" name="phone" id="phone" maxlength="15" value="<?php echo $row['phone_no'];?>" onkeyup="return check_value(this.value);"></td>

	</tr>

	<tr id="pid" <?php if($row['category']=="B"){echo 'style="display:none;"';}?> >

		<td id="fDTxt"><span id="spanParent"><?php if($row['category']=="B" || $row['category']=="D"){echo 'Distributor Name :';} elseif($row['category']=="S"){echo 'Dealer Name :';}?></span>&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><span id="spanPList"><select name="parentName" id="parentName" style="width:265px"><option value="0">-- Select --</option><?php 

		if($row['category']=="B" || $row['category']=="D")

			$sql_parent = mysql_query("SELECT dealer_id, dealer_name FROM dealer WHERE category='B' AND ai_id=".$row['ai_id']." ORDER BY dealer_name",$link);

		elseif($row['category']=="S")

			$sql_parent = mysql_query("SELECT dealer_id, dealer_name FROM dealer WHERE category='D' AND ai_id=".$row['ai_id']." ORDER BY dealer_name",$link);

		while($row_parent = mysql_fetch_array($sql_parent)){

			if($row_parent['dealer_id']==$row['parent_id'])

				echo '<option selected value="'.$row_parent['dealer_id'].'">'.$row_parent['dealer_name'].'</option>';

			else

				echo '<option value="'.$row_parent['dealer_id'].'">'.$row_parent['dealer_name'].'</option>';

		}?>

		</select></span></td>

	</tr>

	<tr>

		<td id="fDTxt">City :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><select name="city" id="city" style="width:265px">

		<option value="0">-- Select --</option>

		<?php

		$sql_city = mysql_query("SELECT * FROM city WHERE state_id=".$row['state_id']." ORDER BY city_name",$link);

		while($row_city = mysql_fetch_array($sql_city)){

			if($row_city['city_id'] == $row['location_id'])

				echo '<option selected value="'.$row_city['city_id'].'">'.$row_city['city_name'].'</option>';

			else

				echo '<option value="'.$row_city['city_id'].'">'.$row_city['city_name'].'</option>';

		} ?>

		</select></td>

	</tr>

	<tr>

		<td id="fDTxt">State : </td>

		<td>&nbsp;</td>

		<td id="fDInp"><input type="text" name="state" id="state" readonly="true" size="40" value="<?php echo $row['state_name'];?>" style="background-color:#C2CEF5;"></td>

	</tr>

	<tr>

		<td>&nbsp;</td>

		<td>&nbsp;</td>

		<td>&nbsp;</td>

	</tr>

	<tr>

		<td colspan="3"><input type="submit" name="update" id="update" value="Update" />&nbsp;&nbsp;<input type="reset" name="reset" id="reset" value="Reset" /> &nbsp;&nbsp;<input type="button" name="exit" id="exit" value="Exit" onclick="window.location='<?php echo $x;?>'" /></td>

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