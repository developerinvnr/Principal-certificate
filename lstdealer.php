<?php

session_start();

require_once('config/config.php');

include('function.php');

if(check_user()==false) {header("Location: login.php");}

/*----------------------------*/

if(isset($_REQUEST['xn']) && $_REQUEST['xn']=='D'){

	$delete_confirm = "yes";

	$sql = mysql_query("SELECT * FROM pcissue WHERE dealer_id =".$_REQUEST['did'],$link) or die(mysql_error());

	if(mysql_num_rows($sql)>0) {$delete_confirm = "no";}

	if($delete_confirm == "yes"){

		$sql = mysql_query("SELECT * FROM field_assistant WHERE dist_id =".$_REQUEST['did'],$link) or die(mysql_error());

		if(mysql_num_rows($sql)>0) {$delete_confirm = "no";}

	}

	if($delete_confirm == "yes"){

		$sql = mysql_query("SELECT * FROM fa_provision WHERE dist_id =".$_REQUEST['did'],$link) or die(mysql_error());

		if(mysql_num_rows($sql)>0) {$delete_confirm = "no";}

	}

	/*----------------------------*/

	if($delete_confirm == "no"){

		echo '<script>alert("To many entries found for the selected item.\n Sorry! it can\'t delete from the records.");</script>';

	} elseif($delete_confirm == "yes"){

		$res = mysql_query("DELETE FROM dealer WHERE dealer_id =".$_REQUEST['did'],$link) or die(mysql_error());

	}

}

/*----------------------------*/

$sid = 0;

$cgr = "B";

$dds = 0;

if(isset($_REQUEST['sid'])){$sid = $_REQUEST['sid'];}

if(isset($_REQUEST['cgr'])){$cgr = $_REQUEST['cgr'];}

if(isset($_REQUEST['dds'])){$dds = $_REQUEST['dds'];}

$page_ref = "Location:lstdealer.php?sid=".$_POST['stateName']."&cgr=".$_POST['category']."&dds=".$_POST['ddsName'];

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

	} else {

		return true;

	}

}



function paginglist()

{

	window.location="lstdealer.php?sid="+document.getElementById("stateName").value+"&cgr="+document.getElementById("category").value+"&dds="+document.getElementById("ddsName").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;

}



function firstpage_list()

{

	document.getElementById("page").value = 1;

	paginglist();

}



function previouspage_list()

{

	var cpage = parseInt(document.getElementById("page").value);

	if(cpage>1){

		cpage = cpage - 1;

		document.getElementById("page").value = cpage;

	}

	paginglist();

}



function nextpage_list()

{

	var cpage = parseInt(document.getElementById("page").value);

	if(cpage<parseInt(document.getElementById("totalPage").value)){

		cpage = cpage + 1;

		document.getElementById("page").value = cpage;

	}

	paginglist();

}



function lastpage_list()

{

	document.getElementById("page").value = document.getElementById("totalPage").value;

	paginglist();

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

	<div id="content" align="center" style="width:975px;">

  	<form name="dlrlist" method="post" onsubmit="return validate_data()">

	<fieldset><legend><b>Dealer / Subdealer List</b></legend>

	<table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#9681FC">

	<tr bgcolor="#CCCCCC">

		<td colspan="10">State : <?php 

		if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S"){

			echo '<select name="stateName" id="stateName" style="width:240px;" onchange="get_dds_from_dealer_list_onstate(this.value)"><option value="0">-- Select --</option>';

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

		echo '<select name="category" id="category" style="width:100px;" onchange="get_dds_from_dealer_list_oncategory(this.value)">';

		if($cgr=="D")

			echo '<option selected value="D">Dealer</option><option value="S">Sub Dealer</option><option value="B">Distributor</option>';

		elseif($cgr=="S")

			echo '<option value="D">Dealer</option><option selected value="S">Sub Dealer</option><option value="B">Distributor</option>';

		elseif($cgr=="B")

			echo '<option value="D">Dealer</option><option value="S">Sub Dealer</option><option selected value="B">Distributor</option>';

		echo '</select>';

		echo '&nbsp;&nbsp;&nbsp;&nbsp;Select : <span id="spanDDSList">';

		echo '<select name="ddsName" id="ddsName" style="width:240px;">';

		if($_SESSION['princicate_utype']=="S" || $_SESSION['princicate_utype']=="A"){

			if($cgr=="D"){

				echo '<option value="0">All Distributor</option>';

				$sql_dds = mysql_query("SELECT Distinct dealer_id AS dds_id, dealer_name AS dds_name FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE category='B' AND state_id=".$sid." ORDER BY dds_name",$link) or die(mysql_error());

			} elseif($cgr=="S"){

				echo '<option value="0">All Dealer</option>';

				$sql_dds = mysql_query("SELECT Distinct dealer_id AS dds_id, dealer_name AS dds_name FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE category='D' AND state_id=".$sid." ORDER BY dds_name",$link) or die(mysql_error());

			} elseif($cgr=="B"){

				echo '<option value="0">All Area Incharge</option>';

				$sql_dds = mysql_query("SELECT Distinct area_incharge.ai_id AS dds_id, ai_name AS dds_name FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE state_id=".$sid." ORDER BY dds_name",$link) or die(mysql_error());

			}

		} elseif($_SESSION['princicate_utype']=="U"){

			if($cgr=="D"){

				echo '<option value="0">All Distributor</option>';

				$sql_dds = mysql_query("SELECT Distinct dealer_id AS dds_id, dealer_name AS dds_name FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE category='B' AND uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

			} elseif($cgr=="S"){

				echo '<option value="0">All Dealer</option>';

				$sql_dds = mysql_query("SELECT Distinct dealer_id AS dds_id, dealer_name AS dds_name FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE category='D' AND uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

			} elseif($cgr=="B"){

				$sql_dds = mysql_query("SELECT Distinct area_incharge.ai_id AS dds_id, ai_name AS dds_name FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

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

	<tr class="tableHead" bgcolor="#CCCCCC">

		<td width="3%" style="border-bottom:none; border-left:none; border-top:none;">Sl. No.</td>

		<td width="20%" style="border-bottom:none; border-left:none; border-top:none;">Party Name</td>

		<td width="20%" style="border-bottom:none; border-left:none; border-top:none;">Owner Name</td>

		<td width="7%" style="border-bottom:none; border-left:none; border-top:none;">Category</td>

		<td width="7%" style="border-bottom:none; border-left:none; border-top:none;">Licence For</td>

		<td width="7%" style="border-bottom:none; border-left:none; border-top:none;">Phone No.</td>

		<td width="12%" style="border-bottom:none; border-left:none; border-top:none;">City</td>

		<td width="4%" style="border-bottom:none; border-left:none; border-top:none;">State</td>

		<?php if($cgr=="D"){?>

		<td width="15%" style="border-bottom:none; border-left:none; border-top:none;">Distributor Name</td>

		<?php } elseif($cgr=="S"){?>

		<td width="15%" style="border-bottom:none; border-left:none; border-top:none;">Dealer Name</td>

		<?php } elseif($cgr=="B"){?>

		<td width="15%" style="border-bottom:none; border-left:none; border-top:none;">Area Incharge</td>

		<?php }?>

		<td width="5%" style="border-bottom:none; border-left:none; border-top:none; border-right:none;">Action</td>

	</tr>

	<?php

	$start=0;

	$count = 0;

	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}

	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$start=($_REQUEST['pg']-1)*$end;}

	$ctr = $start;

	if($_SESSION['princicate_utype']=="S" || $_SESSION['princicate_utype']=="A"){

		if($dds==0){

			$sql = mysql_query("SELECT dealer.*, city_name, state_abbre, ai_name FROM dealer INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE state.state_id=".$sid." AND category='".$cgr."' ORDER BY state_abbre, city_name, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

		} elseif($dds!=0){

			if($cgr=="B")

				$sql = mysql_query("SELECT dealer.*, city_name, state_abbre, ai_name FROM dealer INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE state.state_id=".$sid." AND category='".$cgr."' AND dealer.ai_id=".$dds." ORDER BY state_abbre, city_name, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

			elseif($cgr=="D" || $cgr=="S")

				$sql = mysql_query("SELECT dealer.*, city_name, state_abbre, ai_name FROM dealer INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE state.state_id=".$sid." AND category='".$cgr."' AND dealer.parent_id=".$dds." ORDER BY state_abbre, city_name, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

		}

	} elseif($_SESSION['princicate_utype']=="U"){

		if($dds==0){

			$sql = mysql_query("SELECT dealer.*, city_name, state_abbre, ai_name FROM dealer INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE uid=".$_SESSION['princicate_uid']." AND category='".$cgr."' ORDER BY state_abbre, city_name, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

		} elseif($dds!=0){

			if($cgr=="B")

				$sql = mysql_query("SELECT dealer.*, city_name, state_abbre, ai_name FROM dealer INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE uid=".$_SESSION['princicate_uid']." AND category='".$cgr."' AND dealer.ai_id=".$dds." ORDER BY state_abbre, city_name, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

			elseif($cgr=="D" || $cgr=="S")

				$sql = mysql_query("SELECT dealer.*, city_name, state_abbre, ai_name FROM dealer INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE uid=".$_SESSION['princicate_uid']." AND category='".$cgr."' AND dealer.parent_id=".$dds." ORDER BY state_abbre, city_name, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

		}

	}

	while($row = mysql_fetch_array($sql)){

		$delete_ref = "lstdealer.php?xn=D&did=".$row['dealer_id']."&sid=".$sid."&cgr=".$cgr."&dds=".$dds;

		$edit_ref = "editdealer.php?did=".$row['dealer_id']."&sid=".$sid."&cgr=".$cgr."&dds=".$dds;

		if(isset($_REQUEST['pg'])){

			$delete_ref .= "&pg=".$_REQUEST['pg']."&tr=".$_REQUEST['tr'];

			$edit_ref .= "&pg=".$_REQUEST['pg']."&tr=".$_REQUEST['tr'];

		}

		if($count%2){$RowColor='bgcolor="#E4E4ED"';} else {$RowColor='bgcolor="#FFFBEA"';}

		if($row['category']=="D"){$category = "Dealer";} elseif($row['category']=="S"){$category = "Sub Dealer";} elseif($row['category']=="B"){$category = "Distributor";}

		if($row['licence_for']==1){$licenceFor = "Paddy";} elseif($row['licence_for']==2){$licenceFor = "Vegetable";} elseif($row['licence_for']==3){$licenceFor = "Both";}

		$under_whome_name = "&nbsp;";

		if($cgr=="B"){

			$under_whome_name = $row['ai_name'];

		} elseif($cgr=="D" || $cgr=="S"){

			$sqlP = mysql_query("SELECT * FROM dealer WHERE dealer_id=".$row['parent_id'],$link) or die(mysql_error());

			$rowP = mysql_fetch_assoc($sqlP);

			$under_whome_name = $rowP['dealer_name'];

		}

		$ctr++;

		$count++;

		echo '<tr class="tableData" '.$RowColor.'>';

		echo '<td style="border-bottom:none; border-left:none;">'.$ctr.'</td>';

		echo '<td style="border-bottom:none; border-left:none;">'.$row['dealer_name'].'</td>';

		echo '<td style="border-bottom:none; border-left:none;">'.$row['owner_name'].'</td>';

		echo '<td style="border-bottom:none; border-left:none;">'.$category.'</td>';

		echo '<td style="border-bottom:none; border-left:none;">'.$licenceFor.'</td>';

		echo '<td style="border-bottom:none; border-left:none;">'.$row['phone_no'].'</td>';

		echo '<td style="border-bottom:none; border-left:none;">'.$row['city_name'].'</td>';

		echo '<td style="border-bottom:none; border-left:none;" align="center">'.$row['state_abbre'].'</td>';

		echo '<td style="border-bottom:none; border-left:none;">'.$under_whome_name.'</td>';

		if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S")

			echo '<td style="border-bottom:none; border-left:none; border-right:none;"><a href="'.$edit_ref.'"><img src="images/edit.png" border="0" title="Edit"></a>&nbsp;&nbsp;<a onclick=ConfirmDelete("'.$delete_ref.'")><img src="images/cancel.gif" border="0" style="display:inline;cursor:hand;" title="Delete"></a></td>';

		else

			echo '<td style="border-bottom:none; border-left:none; border-right:none;">&nbsp;</td>';

		echo '</tr>';

	} ?>

	</table>

	<?php

	if($_SESSION['princicate_utype']=="S" || $_SESSION['princicate_utype']=="A"){

		if($dds==0){

			$sql = mysql_query("SELECT * FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE state_id=".$sid." AND category='".$cgr."'",$link) or die(mysql_error());

		} elseif($dds!=0){

			if($cgr=="B")

				$sql = mysql_query("SELECT * FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE state_id=".$sid." AND category='".$cgr."' AND dealer.ai_id=".$dds,$link) or die(mysql_error());

			elseif($cgr=="D" || $cgr=="S")

				$sql = mysql_query("SELECT * FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE state_id=".$sid." AND category='".$cgr."' AND dealer.parent_id=".$dds,$link) or die(mysql_error());

		}

	} elseif($_SESSION['princicate_utype']=="U"){

		if($dds==0){

			$sql = mysql_query("SELECT dealer.* FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE uid=".$_SESSION['princicate_uid']." AND category='".$cgr."'",$link) or die(mysql_error());

		} elseif($dds!=0){

			if($cgr=="B")

				$sql = mysql_query("SELECT dealer.* FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE uid=".$_SESSION['princicate_uid']." AND category='".$cgr."' AND dealer.ai_id=".$dds,$link) or die(mysql_error());

			elseif($cgr=="D" || $cgr=="S")

				$sql = mysql_query("SELECT dealer.* FROM dealer INNER JOIN area_incharge ON dealer.ai_id = area_incharge.ai_id WHERE uid=".$_SESSION['princicate_uid']." AND category='".$cgr."' AND dealer.parent_id=".$dds,$link) or die(mysql_error());

		}

	}

	$tot_row = mysql_num_rows($sql);

	echo '<p> Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	echo '<input type="button" name="show" id="show" value="Show:" onclick="paginglist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="1" tabindex="1" />&nbsp;rows&nbsp;&nbsp;&nbsp;&nbsp;';

	$total_page=0;

	if($tot_row>$end){

		echo "Page number: ";

		$total_page=ceil($tot_row/$end);

		echo '<select name="page" id="page" onchange="paginglist()" style="vertical-align:middle">';

		for($i=1;$i<=$total_page;$i++){

			if(isset($_REQUEST["pg"]) && $_REQUEST["pg"]==$i)

				echo '<option selected value="'.$i.'">'.$i.'</option>';

			else

				echo '<option value="'.$i.'">'.$i.'</option>';

		}

		echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	}else {

		echo '<input type="hidden" name="page" id="page" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	}

	echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" />';

	if($total_page>1 && $_REQUEST["pg"]>1)

		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_list()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_list()" />&nbsp;&nbsp;';

	if($total_page>1 && $_REQUEST["pg"]<$total_page)

		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_list()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_list()" />';

	echo '&nbsp;&nbsp;<input type="button" name="refresh" id="refresh" value=" Refresh " onclick="window.location=\'lstdealer.php?sid='.$sid.'&cgr='.$cgr.'&dds='.$dds.'\'" />&nbsp;&nbsp;<input type="button" name="back" id="back" value=" Back " onclick="window.location=\'menu.php\'" />';

	?>

	</fieldset>

	</form>

	</div>

	<div id="footer">VNR Seeds Pvt. Ltd.<br /></div>

</div>

</body>

</html>