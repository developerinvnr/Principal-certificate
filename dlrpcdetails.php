<?php

session_start();

require_once('config/config.php');

include('function.php');

if(check_user()==false) {header("Location: login.php");}

/*----------------------------*/

if(isset($_REQUEST['xn']) && $_REQUEST['xn']=='U'){

	if($_SESSION['princicate_utype']=="S"){

		$res = mysql_query("UPDATE pcissue SET approval_id=0 WHERE issue_id =".$_REQUEST['iid'],$link) or die(mysql_error());

	}

}

/*----------------------------*/

if(isset($_REQUEST['xn']) && $_REQUEST['xn']=='PE'){

	if($_SESSION['princicate_utype']=="S"){

		$res = mysql_query("UPDATE pcissue SET print_pc='Y' WHERE issue_id =".$_REQUEST['iid'],$link) or die(mysql_error());

	}

}

/*----------------------------*/

if(isset($_REQUEST['xn']) && $_REQUEST['xn']=='PD'){

	if($_SESSION['princicate_utype']=="S"){

		$res = mysql_query("UPDATE pcissue SET print_pc='N' WHERE issue_id =".$_REQUEST['iid'],$link) or die(mysql_error());

	}

}

/*----------------------------*/

if(isset($_REQUEST['xn']) && $_REQUEST['xn']=='D'){

	if($_SESSION['princicate_utype']=="S"){

		$sql = mysql_query("SELECT pcissue.*, licence_for FROM pcissue INNER JOIN licence ON pcissue.licence_id = licence.licence_id WHERE issue_id =".$_REQUEST['iid'],$link) or die(mysql_error());

		$row = mysql_fetch_assoc($sql);

		$licencefor = $row['licence_for'];

		/*----------------------------*/

		$res = mysql_query("DELETE FROM pcissue WHERE issue_id =".$_REQUEST['iid'],$link) or die(mysql_error());

		/*----------------------------*/

		$sql = mysql_query("SELECT * FROM pcissue INNER JOIN licence ON pcissue.licence_id = licence.licence_id WHERE dealer_id =".$_REQUEST['dds']." AND licence_for=".$licencefor." ORDER BY pcissue.issue_date DESC",$link) or die(mysql_error());

		if(mysql_num_rows($sql)>0){

			$row=mysql_fetch_assoc($sql);

			$issueId = $row['issue_id'];

			$pcvalidupto = $row['valid_upto'];

			$res = mysql_query("UPDATE pcissue SET is_editable='Y' WHERE issue_id =".$issueId,$link) or die(mysql_error());

			/*----------------------------*/

			if($licencefor==1)

				$res = mysql_query("UPDATE dealer SET paddy_valid='".$pcvalidupto."' WHERE dealer_id=".$_REQUEST['dds'],$link) or die(mysql_error());

			elseif($licencefor==2)

				$res = mysql_query("UPDATE dealer SET veg_valid='".$pcvalidupto."' WHERE dealer_id=".$_REQUEST['dds'],$link) or die(mysql_error());

		} else {

			if($licencefor==1)

				$res = mysql_query("UPDATE dealer SET paddy_valid=null WHERE dealer_id=".$_REQUEST['dds'],$link) or die(mysql_error());

			elseif($licencefor==2)

				$res = mysql_query("UPDATE dealer SET veg_valid=null WHERE dealer_id=".$_REQUEST['dds'],$link) or die(mysql_error());

		}

	}

}

/*----------------------------*/

$sid = 0;

$cgr = "B";

$dds = 0;

if(isset($_REQUEST['sid'])){$sid = $_REQUEST['sid'];}

if(isset($_REQUEST['cgr'])){$cgr = $_REQUEST['cgr'];}

if(isset($_REQUEST['dds'])){$dds = $_REQUEST['dds'];}

$page_ref = "Location:dlrpcdetails.php?sid=".$_POST['stateName']."&cgr=".$_POST['category']."&dds=".$_POST['ddsName'];

if(isset($_REQUEST['pg'])){$page_ref .= "&pg=".$_REQUEST['pg']."&tr=".$_REQUEST['tr'];}

if(isset($_POST['submit'])){header($page_ref);}

/*----------------------------*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

<head>

<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

<title>Princicate Ver 1.0</title>

<link href="css/style.css" rel="stylesheet" type="text/css" />

<script language="javascript" src="js/prototype.js" type="text/javascript"></script>

<script language="javascript" src="js/ajax.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript">

function ConfirmDelete(value)

{

	var del = confirm('Do you really want to delete this record?');

	if(del) {window.location=value;}

}



function validate_data()

{

	if(document.getElementById("stateName").value==0){

		alert("* Please select state name. !!");

		document.getElementById("stateName").focus();

		return false;

	}

	if(document.getElementById("ddsName").value==0){

		alert("* Please select party name. !!");

		document.getElementById("ddsName").focus();

		return false;

	}

	return true

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

	<div id="content" align="center" style="width:950px;">

  	<form name="dlrissuepc" method="post" onsubmit="return validate_data()">

	<fieldset><legend><b>Dealer PC Details</b></legend>

	<table width="100%" border="0" cellpadding="2" cellspacing="1">

	<tr bgcolor="#CCCCCC">

		<td>State : <?php 

		if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S"){

			echo '<select name="stateName" id="stateName" style="width:240px;" onchange="get_dds_from_pcdetails_onstate(this.value)"><option value="0">-- Select --</option>';

			$sql_state = mysql_query("SELECT * FROM state ORDER BY state_name",$link) or die(mysql_error());

			while($row_state = mysql_fetch_array($sql_state)){

				if($row_state['state_id']==$sid)

					echo '<option selected value="'.$row_state['state_id'].'">'.$row_state['state_name'].'</option>';

				else

					echo '<option value="'.$row_state['state_id'].'">'.$row_state['state_name'].'</option>';

			}

			echo '</select>';

		} elseif($_SESSION['princicate_utype']=="U"){

			$sql_state = mysql_query("SELECT state.* FROM area_incharge INNER JOIN state ON area_incharge.state_id = state.state_id WHERE uid=".$_SESSION['princicate_uid']." ORDER BY state_name",$link) or die(mysql_error());

			$row_state = mysql_fetch_array($sql_state);

			$sid = $row_state['state_id'];

			echo '<input name="sName" id="sName" disabled value="'.$row_state['state_name'].'"/><input type="hidden" name="stateName" id="stateName" value="'.$sid.'"/>';

		}

		echo '&nbsp;&nbsp;&nbsp;&nbsp;Category : ';

		echo '<select name="category" id="category" style="width:100px;" onchange="get_dds_from_pcdetails_oncategory(this.value)">';

		if($cgr=="D")

			echo '<option selected value="D">Dealer</option><option value="S">Sub Dealer</option><option value="B">Distributor</option>';

		elseif($cgr=="S")

			echo '<option value="D">Dealer</option><option selected value="S">Sub Dealer</option><option value="B">Distributor</option>';

		elseif($cgr=="B")

			echo '<option value="D">Dealer</option><option value="S">Sub Dealer</option><option selected value="B">Distributor</option>';

		echo '</select>';

		echo '&nbsp;&nbsp;&nbsp;&nbsp;Party : <span id="spanDDSList">';

		echo '<select name="ddsName" id="ddsName" style="width:240px;"><option value="0">-- Select --</option>';

		if($_SESSION['princicate_utype']=="S" || $_SESSION['princicate_utype']=="A"){

			if($cgr=="D"){

				$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id WHERE category='D' AND state_id=".$sid." ORDER BY dds_name",$link) or die(mysql_error());

			} elseif($cgr=="S"){

				$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id WHERE category='S' AND state_id=".$sid." ORDER BY dds_name",$link) or die(mysql_error());

			} elseif($cgr=="B"){

				$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id WHERE category='B' AND state_id=".$sid." ORDER BY dds_name",$link) or die(mysql_error());

			}

		} elseif($_SESSION['princicate_utype']=="U"){

			if($cgr=="D"){

				$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE category='D' AND uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

			} elseif($cgr=="S"){

				$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE category='S' AND uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

			} elseif($cgr=="B"){

				$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE category='B' AND uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

			}

		}

		while($row_dds = mysql_fetch_array($sql_dds)){

			if($row_dds['dds_id']==$dds)

				echo '<option selected value="'.$row_dds['dds_id'].'">'.$row_dds['dds_name'].'</option>';

			else

				echo '<option value="'.$row_dds['dds_id'].'">'.$row_dds['dds_name'].'</option>';

		}

		echo '</select></span>';

		echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value=" Display "/>';

		?>

		</td>

	</tr>

	</table>

	<?php 

	$count = 0;

	$ctr = 0;

	$sql = mysql_query("SELECT pcissue.*, dealer.*, city_name, state_name, ai_name, licence.licence_for AS plicencefor FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id INNER JOIN licence ON pcissue.licence_id = licence.licence_id WHERE pcissue.dealer_id=".$dds." ORDER BY issue_date",$link) or die(mysql_error());

	while($row = mysql_fetch_array($sql)){

		if($ctr==0){

			if($row['licence_for']==1){$pc_for = "Paddy";} elseif($row['licence_for']==2){$pc_for = "Vegetable";} elseif($row['licence_for']==3){$pc_for = "Both";}

			echo '<table width="100%" border="0" cellpadding="2" cellspacing="0">';

			echo '<tr>';

			echo '<td width="15%" align="right">Party Name</td><td width="5%">:</td><td width="30%" align="left">'.$row['dealer_name'].'</td>';

			echo '<td width="15%" align="right">Contact Person</td><td width="5%">:</td><td align="left">'.$row['owner_name'].'</td>';

			echo '</tr>';

			echo '<tr>';

			echo '<td align="right">Location</td><td>:</td><td align="left">'.$row['city_name'].'</td>';

			echo '<td align="right">Area Incharge</td><td>:</td><td align="left">'.$row['ai_name'].'</td>';

			echo '</tr>';

			echo '<tr>';

			echo '<td align="right">State</td><td>:</td><td align="left">'.$row['state_name'].'</td>';

			echo '<td align="right">Licence For </td><td>:</td><td align="left">'.$pc_for.'</td>';

			echo '</tr>';

			echo '<tr>';

			echo '<td align="right">Phone No.</td><td>:</td><td align="left">'.$row['phone_no'].'</td>';

			echo '<td align="right">&nbsp;</td><td>&nbsp;</td><td align="left">&nbsp;</td>';

			echo '</tr>';

			echo '</table>';

			echo '<table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#9681FC">';

			echo '<tr class="tableHead" bgcolor="#CCCCCC">';

			echo '<td width="5%">Sl. No.</td>';

			echo '<td width="10%">Type</td>';

			echo '<td width="40%">Issue / Renewal No.</td>';

			echo '<td width="10%">Issue Date</td>';

			echo '<td width="10%">Valid Upto</td>';

			echo '<td width="10%">PC For</td>';

			echo '<td width="15%">Action</td>';

			echo '</tr>';

		}

		if($count%2)

			$RowColor='bgcolor="#E4E4ED"';

		else

			$RowColor='bgcolor="#FFFBEA"';

		$ctr++;

		$count++;

		if($row['issue_type']=="I"){$issue_type = "Issue";} elseif($row['issue_type']=="R"){$issue_type = "Renewal";}

		$issue_number = ($row['certificate_no']>999 ? $row['certificate_no'] : ($row['certificate_no']>99 && $row['certificate_no']<1000 ? "0".$row['certificate_no'] : ($row['certificate_no']>9 && $row['certificate_no']<100 ? "00".$row['certificate_no'] : "000".$row['certificate_no'])));

		if($row['certificate_prefix']!=null){$issue_number = $row['certificate_prefix']."/".$issue_number;}

		if($row['plicencefor']==1){$issue_for = "Paddy";} elseif($row['plicencefor']==2){$issue_for = "Vegetable";}

		echo '<tr class="tableData" '.$RowColor.'>';

		echo '<td>'.$ctr.'</td>';

		echo '<td>'.$issue_type.'</td>';

		echo '<td>'.$issue_number.'</td>';

		echo '<td>'.date("d-m-Y",strtotime($row['issue_date'])).'</td>';

		echo '<td>'.date("d-m-Y",strtotime($row['valid_upto'])).'</td>';

		echo '<td>'.$issue_for.'</td>';

		if($_SESSION['princicate_utype']=="S"){

			if($row['print_pc']=="Y"){

				$print_ref = "dlrpcdetails.php?xn=PD&iid=".$row['issue_id']."&sid=".$sid."&cgr=".$cgr."&dds=".$dds;

				$print_strg = '<a href="'.$print_ref.'"><img src="images/print.gif" title="click to disable print of this record" border="0"></a>';

			} elseif($row['print_pc']=="N"){

				$print_ref = "dlrpcdetails.php?xn=PE&iid=".$row['issue_id']."&sid=".$sid."&cgr=".$cgr."&dds=".$dds;

				$print_strg = '<a href="'.$print_ref.'"><img src="images/print_dis.gif" title="click to enable print of this record" border="0"></a>';

			}

			if($row['is_editable']=="Y"){

				$delete_ref = "dlrpcdetails.php?xn=D&iid=".$row['issue_id']."&sid=".$sid."&cgr=".$cgr."&dds=".$dds;

				if($row['issue_type']=="I"){

					$edit_ref = "editpcissue.php?iid=".$row['issue_id']."&sid=".$sid."&cgr=".$cgr."&dds=".$dds;

				} elseif($row['issue_type']=="R"){

					$edit_ref = "editpcrenew.php?iid=".$row['issue_id']."&sid=".$sid."&cgr=".$cgr."&dds=".$dds;

				}

				if($row['approval_id']!=0){

					$undo_ref = "dlrpcdetails.php?xn=U&iid=".$row['issue_id']."&sid=".$sid."&cgr=".$cgr."&dds=".$dds;

					echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.png" title="click to Change data" border="0"></a>&nbsp;&nbsp;<a onclick=ConfirmDelete("'.$delete_ref.'")><img src="images/cancel.gif" title="click to Delete this record" border="0" style="display:inline; cursor:hand;"></a>&nbsp;&nbsp;<a href="'.$undo_ref.'"><img src="images/undo.gif" title="click to Unapprove this record" border="0"></a>&nbsp;&nbsp;'.$print_strg.'</td>';

				} else {

					echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.png" title="click to Change data" border="0"></a>&nbsp;&nbsp;<a onclick=ConfirmDelete("'.$delete_ref.'")><img src="images/cancel.gif" title="click to Delete this record" border="0" style="display:inline; cursor:hand;"></a>&nbsp;&nbsp;<img src="images/check.gif" title="Unapproved" border="0">&nbsp;&nbsp;'.$print_strg.'</td>';

				}

			} elseif($row['is_editable']=="N"){

				echo '<td align="center">&nbsp;</td>';

			}

		} else {

			echo '<td align="center">&nbsp;</td>';

		}

		echo '</tr>';

	}

	echo '</table>';

	echo '&nbsp;&nbsp;<input type="button" name="refresh" id="refresh" value=" Refresh " onclick="window.location=\'dlrpcdetails.php?sid='.$sid.'&cgr='.$cgr.'&dds='.$dds.'\'" />&nbsp;&nbsp;<input type="button" name="back" id="back" value=" Back " onclick="window.location=\'menu.php\'" />';

	?>

	</fieldset>

	</form>

	</div>

	<div id="footer">VNR Seeds Pvt. Ltd.<br /></div>

</div>

</body>

</html>