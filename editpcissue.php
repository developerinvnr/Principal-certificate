<?php 

session_start();

require_once('config/config.php');

include('function.php');

if(check_user() == false) {header("Location: login.php");}

/*----------------------------*/

$sqlPC = mysql_query("SELECT * FROM pcissue WHERE issue_id=".$_REQUEST['iid'],$link) or die(mysql_error());

$rowPC = mysql_fetch_assoc($sqlPC);

/*----------------------------*/

$sqlDLR = mysql_query("SELECT dealer.*, city_name, state_name, ai_name FROM dealer INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE dealer_id=".$rowPC['dealer_id'],$link) or die(mysql_error());

$rowDLR = mysql_fetch_assoc($sqlDLR);

/*----------------------------*/

$sqlLIC = mysql_query("SELECT * FROM licence WHERE licence_id=".$rowPC['licence_id'],$link) or die(mysql_error());

$rowLIC = mysql_fetch_assoc($sqlLIC);

/*----------------------------*/

$issue_number = ($rowPC['certificate_no']>999 ? $rowPC['certificate_no'] : ($rowPC['certificate_no']>99 && $rowPC['certificate_no']<1000 ? "0".$rowPC['certificate_no'] : ($rowPC['certificate_no']>9 && $rowPC['certificate_no']<100 ? "00".$rowPC['certificate_no'] : "000".$rowPC['certificate_no'])));

/*----------------------------*/

if(isset($_POST['submit'])){

	$sql_pc = mysql_query("SELECT issue_id FROM pcissue WHERE issue_date='".date("Y-m-d",strtotime($_POST['issueDate']))."' AND dealer_id=".$rowPC['dealer_id']." AND licence_id=".$_POST['licenceNo'],$link) or die(mysql_error());

	$row_pc=mysql_fetch_assoc($sql_pc);

	$count = mysql_num_rows($sql_pc);

	/*--------------------------------------------*/

	if($count>0){

		if($row_pc['issue_id']!=$_REQUEST['iid'])

			echo '<script>alert("Duplication Error! can\'t change data of issue of pc.");</script>';

		elseif($row_pc['issue_id']==$_REQUEST['iid']){

			$res = mysql_query("UPDATE pcissue SET issue_date='".date("Y-m-d",strtotime($_POST['issueDate']))."', certificate_prefix='".$_POST['preIssueNo']."', valid_upto='".date("Y-m-d",strtotime($_POST['pcValidUpTo']))."', licence_id=".$_POST['licenceNo'].", sm_id=".$_POST['recommById']." WHERE issue_id=".$_REQUEST['iid'],$link) or die(mysql_error());

			/*----------------------------*/

			$sql = mysql_query("SELECT * FROM licence WHERE licence_id=".$_POST['licenceNo'],$link);

			$row = mysql_fetch_assoc($sql);

			if($row['licence_for']==1)

				$res = mysql_query("UPDATE dealer SET paddy_valid='".date("Y-m-d",strtotime($_POST['pcValidUpTo']))."' WHERE dealer_id=".$rowPC['dealer_id'],$link) or die(mysql_error());

			elseif($row['licence_for']==2)

				$res = mysql_query("UPDATE dealer SET veg_valid='".date("Y-m-d",strtotime($_POST['pcValidUpTo']))."' WHERE dealer_id=".$rowPC['dealer_id'],$link) or die(mysql_error());

			/*----------------------------*/

			header("Location: dlrpcdetails.php?sid=".$_REQUEST['sid']."&cgr=".$_REQUEST['cgr']."&dds=".$_REQUEST['dds']);

		}

	} elseif($count==0){

		$res = mysql_query("UPDATE pcissue SET issue_date='".date("Y-m-d",strtotime($_POST['issueDate']))."', certificate_prefix='".$_POST['preIssueNo']."', valid_upto='".date("Y-m-d",strtotime($_POST['pcValidUpTo']))."', licence_id=".$_POST['licenceNo'].", sm_id=".$_POST['recommById']." WHERE issue_id=".$_REQUEST['iid'],$link) or die(mysql_error());

		/*----------------------------*/

		$sql = mysql_query("SELECT * FROM licence WHERE licence_id=".$_POST['licenceNo'],$link);

		$row = mysql_fetch_assoc($sql);

		if($row['licence_for']==1)

			$res = mysql_query("UPDATE dealer SET paddy_valid='".date("Y-m-d",strtotime($_POST['pcValidUpTo']))."' WHERE dealer_id=".$rowPC['dealer_id'],$link) or die(mysql_error());

		elseif($row['licence_for']==2)

			$res = mysql_query("UPDATE dealer SET veg_valid='".date("Y-m-d",strtotime($_POST['pcValidUpTo']))."' WHERE dealer_id=".$rowPC['dealer_id'],$link) or die(mysql_error());

		/*----------------------------*/

		header("Location: dlrpcdetails.php?sid=".$_REQUEST['sid']."&cgr=".$_REQUEST['cgr']."&dds=".$_REQUEST['dds']);

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

<link href="css/calendar.css" rel="stylesheet" type="text/css" />

<script language="javascript" src="js/common.js" type="text/javascript"></script>

<script language="javascript" src="js/prototype.js" type="text/javascript"></script>

<script language="javascript" src="js/ajax.js" type="text/javascript"></script>

<script language="javascript" src="js/calendar_eu.js"></script>

<script language="javascript" type="text/javascript">

function validate_pcissue()

{

	if(document.getElementById("licenceNo").value==0){

		alert("* Please select Licence No. !!");

		document.getElementById("licenceNo").focus();

		return false;

	}

	if(document.getElementById("preIssueNo").value==""){

		alert("* Please input prefix for certificate no. !!");

		document.getElementById("preIssueNo").focus();

		return false;

	}

/*	if(document.getElementById("pcSerialNo").value==""){

		alert("* Please input serial/certificate no. of PC !!");

		document.getElementById("pcSerialNo").focus();

		return false;

	}*/

	if(document.getElementById("issueDate").value==""){

		alert("* Please input certificate issue date. !!");

		document.getElementById("issueDate").focus();

		return false;

	}

	if(document.getElementById("issueDate").value!=""){

		if(!checkdate(document.issueofpc.issueDate)){

			document.getElementById("issueDate").focus();

			return false;

		}

	}

	var no_of_days1 = getDaysbetween2Dates(document.issueofpc.issueDate,document.issueofpc.licenceValidUpTo);

	if(no_of_days1 < 0){

		alert("* Sorry! Issue date wrongly selected.\nPlease correct and submit again.\n");

		document.getElementById("issueDate").focus();

		return false;

	}

	if(document.getElementById("pcValidUpTo").value==""){

		alert("* Please input certificate validity date. !!");

		document.getElementById("pcValidUpTo").focus();

		return false;

	}

	if(document.getElementById("pcValidUpTo").value!=""){

		if(!checkdate(document.issueofpc.pcValidUpTo)){

			document.getElementById("pcValidUpTo").focus();

			return false;

		}

		var no_of_days2 = getDaysbetween2Dates(document.issueofpc.issueDate, document.issueofpc.pcValidUpTo);

		if(no_of_days2 < 0){

			alert("* Sorry! PC Validity date wrongly selected.\nPlease correct and submit again.\n");

			document.getElementById("pcValidUpTo").focus();

			return false;

		}

	}

}



function check_value(me)

{

	var Amount = me.value.charAt(me.value.length-1);

	var Numfilter=/^[0-9.]+$/;

	var test_num = Numfilter.test(Amount);

	if(!test_num){

		alert("Please Enter Only numeric data!");

		if(me.value.length>0){

			document.getElementById("pcSerialNo").value=document.getElementById("pcSerialNo").value.substring(0,me.value.length-1);

		}

		return false;

	} else {

		return true;

	}

}



function get_pc_valid_date()

{

	if(!checkdate(document.issueofpc.issueDate)){

		document.getElementById("issueDate").focus();

		return false;

	} else {

		var dayfield=document.issueofpc.issueDate.value.split("-")[0];

		var monthfield=document.issueofpc.issueDate.value.split("-")[1];

		var yearfield=document.issueofpc.issueDate.value.split("-")[2];

		yearfield = parseInt(yearfield)+1;

		document.issueofpc.pcValidUpTo.value = dayfield+"-"+monthfield+"-"+yearfield;

		return true;

	}

}



/*function check_pc_valid_date()

{

	if(!checkdate(document.issueofpc.pcValidUpTo)){

		return false;

	} else {

		var dayfield=document.issueofpc.pcValidUpTo.value.split("-")[0];

		var monthfield=document.issueofpc.pcValidUpTo.value.split("-")[1];

		var yearfield=document.issueofpc.pcValidUpTo.value.split("-")[2];

		var dayobj = new Date(yearfield, monthfield-1, dayfield);

		if((dayobj.getMonth()+1!=3)||(dayobj.getDate()!=31)){

			alert("Error: \n PC Validity Date is Invalid.\nPlease correct and submit again.");

			return false;

		} else {

			return true;

		}

	}

}*/

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

  	<form name="issueofpc" method="post" onsubmit="return validate_pcissue()">

	<fieldset><legend><b>Issue of PC Entry Change Page</b></legend>

	<table border="0" width="100%">

	<tr>

		<td id="fDTxt">Dealer Name :</td>

		<td><span style="color:red; font-weight:bold;">*</span></td>

		<td id="fDInp"><input name="dealerName" id="dealerName" size="35" readonly="true" value="<?php echo $rowDLR['dealer_name']; ?>" style="background-color:#C2CEF5;"/></td>

	</tr>

	<tr>

		<td id="fDTxt">Type / Category :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="typeCategory" id="typeCategory" size="35" readonly="true" value="<?php if($rowDLR['category']=="D"){echo "Dealer";} elseif($rowDLR['category']=="S"){echo "Sub-Dealer";} elseif($rowDLR['category']=="B"){echo "Distributor";} ?>" style="background-color:#C2CEF5;"/></td>

	</tr>

	<tr>

		<td id="fDTxt">Owner Name :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="ownerName" id="ownerName" size="35" readonly="true" value="<?php echo $rowDLR['owner_name']; ?>" style="background-color:#C2CEF5;"/></td>

	</tr>

	<tr>

		<td id="fDTxt">City :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="cityName" id="cityName" size="35" readonly="true" value="<?php echo $rowDLR['city_name']; ?>" style="background-color:#C2CEF5;"/></td>

	</tr>

	<tr>

		<td id="fDTxt">State :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="stateName" id="stateName" size="35" readonly="true" value="<?php echo $rowDLR['state_name']; ?>" style="background-color:#C2CEF5;"/></td>

	</tr>

	<tr>

		<td id="fDTxt">PC Apply For :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="pcApplyFor" id="pcApplyFor" size="10" readonly="true" value="<?php if($rowDLR['licence_for']==1){echo "Paddy";} elseif($rowDLR['licence_for']==2){echo "Vegetable";} elseif($rowDLR['licence_for']==3){echo "Both";}?>" style="background-color:#C2CEF5;" /></td>

	</tr>

	<tr>

		<td id="fDTxt">Principal Licence No. :</td>

		<td><span style="color:red; font-weight:bold;">*</span></td>

		<td id="fDInp"><select name="licenceNo" id="licenceNo" style="width:235px;" onchange="get_licence_details(this.value)">

		<option value="0">-- Select --</option>

		<?php

		if($rowDLR['licence_for']==1)

			$sql_lic = mysql_query("SELECT * FROM licence WHERE state_id=".$rowPC['state_id']." AND licence_for=1 AND is_select='Y' ORDER BY licence_no",$link);

		elseif($rowDLR['licence_for']==2)

			$sql_lic = mysql_query("SELECT * FROM licence WHERE state_id=".$rowPC['state_id']." AND licence_for=2 AND is_select='Y' ORDER BY licence_no",$link);

		elseif($rowDLR['licence_for']==3)

			$sql_lic = mysql_query("SELECT * FROM licence WHERE state_id=".$rowPC['state_id']." AND (licence_for=1 OR licence_for=2) AND is_select='Y' ORDER BY licence_no",$link);

		while($row_lic = mysql_fetch_array($sql_lic)){

			if($row_lic['licence_id']==$rowPC['licence_id'])

				echo '<option selected value="'.$row_lic['licence_id'].'">'.$row_lic['licence_no'].'</option>';

			else

				echo '<option value="'.$row_lic['licence_id'].'">'.$row_lic['licence_no'].'</option>';

		} ?>

		</select></td>

	</tr>

	<tr>

		<td id="fDTxt">Principal Licence For :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="licenceFor" id="licenceFor" size="10" readonly="true" value="<?php if($rowLIC['licence_for']==1){echo "Paddy";} elseif($rowLIC['licence_for']==2){echo "Vegetable";} ?>" style="background-color:#C2CEF5;" /></td>

	</tr>

	<tr>

		<td id="fDTxt">Licence Valid upto :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="licenceValidUpTo" id="licenceValidUpTo" size="10" readonly="true" value="<?php echo date("d-m-Y",strtotime($rowLIC['valid_upto'])); ?>" style="background-color:#C2CEF5;" /></td>

	</tr>

	<tr>

		<td id="fDTxt">Licence Renewal No. :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="renewalNo" id="renewalNo" size="35" readonly="true" value="<?php echo $rowLIC['issue_no']; ?>" style="background-color:#C2CEF5;" /></td>

	</tr>

	<tr>

		<td id="fDTxt">Licence Renewal date :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="renewalDate" id="renewalDate" size="10" readonly="true" value="<?php echo date("d-m-Y",strtotime($rowLIC['issue_date'])); ?>" style="background-color:#C2CEF5;" /></td>

	</tr>

	<tr>

		<td id="fDTxt">Prefix for PC :</td>

		<td><span style="color:red; font-weight:bold;">*</span></td>

		<td id="fDInp"><input name="preIssueNo" id="preIssueNo" maxlength="50" size="35" value="<?php echo $rowPC['certificate_prefix']; ?>" /></td>

	</tr>

	<?php /*

	<tr>

		<td id="fDTxt">PC Serial No. :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="pcSerialNo" id="pcSerialNo" maxlength="5" size="10" readonly="true" value="<?php echo $issue_number; ?>" style="background-color:#C2CEF5;" /></td>

	</tr> */?>

	<tr>

		<td id="fDTxt">PC Issue date :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="issueDate" id="issueDate" size="10" maxlength="10" readonly="true" value="<?php echo date("d-m-Y",strtotime($rowPC['issue_date'])); ?>" style="background-color:#C2CEF5;" />&nbsp;<script language="JavaScript">new tcal ({'formname': 'issueofpc', 'controlname': 'issueDate'});</script>&nbsp;&nbsp;<span style="color:red;">(dd-mm-yyyy)</span></td>

	</tr>

	<tr>

		<td id="fDTxt">PC Valid upto :</td>

		<td><span style="color:red; font-weight:bold;">*</span></td>

		<td id="fDInp"><input name="pcValidUpTo" id="pcValidUpTo" size="10" maxlength="10" value="<?php echo date("d-m-Y",strtotime($rowPC['valid_upto'])); ?>" />&nbsp;<script language="JavaScript">new tcal ({'formname': 'issueofpc', 'controlname': 'pcValidUpTo'});</script>&nbsp;&nbsp;<span style="color:red;">(dd-mm-yyyy)</span></td>

	</tr>

	<tr>

		<td id="fDTxt">Recommended by :</td>

		<td>&nbsp;</td>

		<td id="fDInp"><input name="recommByName" id="recommByName" size="35" value="<?php echo $rowDLR['ai_name']; ?>" readonly="true" style="background-color:#C2CEF5;"/><input type="hidden" name="recommById" id="recommById" value="<?php echo $rowPC['sm_id']; ?>" /></td>

	</tr>

	<tr>

		<td colspan="3" align="center"><input type="submit" name="submit" id="submit" value=" Submit " />&nbsp;&nbsp;<input type="reset" name="reset" id="reset" value=" Reset " /> &nbsp;&nbsp;<input type="button" name="exit" id="exit" onclick="window.location='dlrpcdetails.php?sid=<?php echo $_REQUEST['sid'];?>&cgr=<?php echo $_REQUEST['cgr'];?>&dds=<?php echo $_REQUEST['dds'];?>'" value=" Exit " /></td>

	</tr>

	</table>

	</fieldset>

	</form>

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

</div>

</body>

</html>