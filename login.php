<?php

session_start();

require_once('config/config.php');

$_SESSION['princicate_login'] = false;

if(isset($_REQUEST['m']) && $_REQUEST['m'] != "") {$msg = "You have successfully logged out....";}

/*-----------------------------*/

if(isset($_POST['login'])){

	$userId = addslashes($_POST['userId']);

	$preSalt = "AKA";

	$postSalt = "AAA";

	$pwd_hash = md5($preSalt . md5(addslashes($_POST['pwd']) . $postSalt));

	/*-----------------------------*/

	$sql = mysql_query("SELECT * FROM users WHERE user_id='".$userId."' and user_pwd='".$pwd_hash."'",$link);

	$row = mysql_fetch_assoc($sql);

	if(mysql_num_rows($sql)==1){

		if($row['user_status'] == "A"){

			$sqlyear = mysql_query("SELECT * FROM year WHERE year_id=".$_POST['period'],$link);

			$rowyear = mysql_fetch_assoc($sqlyear);

			$syear = date("d-m-Y",strtotime($rowyear['start_year']));

			$eyear = date("d-m-Y",strtotime("31-03".substr(date("Y",strtotime($rowyear['start_year']))+1,0,4)));

			/*-----------------------------*/

			$_SESSION['princicate_login'] = true;

			$_SESSION['princicate_uid'] = $row['uid'];

			$_SESSION['princicate_userid'] = $row['user_id'];

			$_SESSION['princicate_utype'] = $row['user_type'];

			$_SESSION['princicate_ustatus'] = $row['user_status'];

			$_SESSION['princicate_yid'] = $_POST['period'];

			$_SESSION['princicate_syr'] = $syear;

			$_SESSION['princicate_eyr'] = $eyear;

			header('Location: menu.php');

		} else {

			$msg = "You are not authorize for this site...";

		}

	} else {

		$msg = "Invalid username or password....";

	}

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

<head>

<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

<title>Princicate Ver 1.0</title>

<link href="css/style.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript">

function validate_login()

{

	if(document.getElementById("userId").value==""){

		alert("* Please input userid !!");

		document.getElementById("userId").focus();

		return false;

	}

	if(document.getElementById("pwd").value==""){

		alert("* Please input Password !!");

		document.getElementById("pwd").focus();

		return false;

	}

	if(document.getElementById("period").value==0){

		alert("* please select period !!");

		document.getElementById("period").focus();

		return false;

	}

}

</script>

</head>



<body onload="document.getElementById('userId').focus()">

<div id="container">

	<div id="header">

		<h1>Princicate</h1>

		<h2>Ver 1.0 (Principal Certificate)</h2>

	</div>

	<div id="content" align="center" style="width:600px;">

	<form name="login" method="post" onsubmit="return validate_login()">

	<fieldset><legend><b>User Login</b></legend>

	<table border="0" width="100%">

	<tr>

		<td>&nbsp;</td>

		<td>&nbsp;</td>

		<td>&nbsp;</td>

	</tr>

	<tr>

		<td id="fDTxt">User Id :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><input type="text" name="userId" id="userId" maxlength="50" size="15" value="" /></td>

	</tr>

	<tr>

		<td id="fDTxt">Password :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;</td>

		<td id="fDInp"><input type="password" name="pwd" id="pwd" maxlength="50" size="15" value="" /></td>

	</tr>

	<tr>

		<td id="fDTxt">Period :&nbsp;<span style="color:red; font-weight:bold;">*</span></td>

		<td>&nbsp;

               </td>

		<td id="fDInp"><select name="period" id="period" style="width:115px;">

		<?php

		$sql_year = mysql_query("SELECT * FROM year ORDER BY start_year",$link);

		$count = mysql_num_rows($sql_year);

		if($count == 0){

			echo '<option value="0">-- Select --</option>';

		}

		$ctr = 0;

		while($row_year = mysql_fetch_array($sql_year)){

			$ctr++;

			if($ctr == $count)

				echo "<option selected value='".$row_year['year_id']."'>".$row_year['period']."</option>";

			else

				echo "<option value='".$row_year['year_id']."'>".$row_year['period']."</option>";

		} ?>

		</select></td>

	</tr>

	<tr>

		<td>&nbsp;</td>

		<td>&nbsp;</td>

		<td>&nbsp;</td>

	</tr>

	<tr>

		<td colspan="3" align="center"><input type="submit" name="login" id="login" value=" Login " />&nbsp;&nbsp;<input type="reset" name="reset" id="reset" value=" Reset " onclick="window.location='login.php'" /></td>

	</tr>

	<tr>

		<td colspan="3" align="center"><?php if($msg != "") {echo '<font color="blue" size="">'.$msg.'</font>';}?></td>

	</tr>

	</table>

	</fieldset>

	</form>

	</div>

	<div id="footer">VNR Seeds Pvt. Ltd.<br /></div>

</div>

</body>

</html>