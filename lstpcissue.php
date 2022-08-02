<?php

session_start();

require_once('config/config.php');

include('function.php');

if(check_user()==false) {header("Location: login.php");}

/*----------------------------*/

$t = 0;

$today = date("d-m-Y");

$dt1 = strtotime($today);

$dt2 = strtotime($today);

$sid = 0;

if(isset($_REQUEST['t'])){$t = $_REQUEST['t'];}

if(isset($_REQUEST['dt1'])){$dt1 = $_REQUEST['dt1'];}

if(isset($_REQUEST['dt2'])){$dt2 = $_REQUEST['dt2'];}

if(isset($_REQUEST['sid'])){$sid = $_REQUEST['sid'];}

if(isset($_POST['submit'])){header("Location:lstpcissue.php?t=".$_POST['dealerType']."&dt1=".strtotime($_POST['date1'])."&dt2=".strtotime($_POST['date2'])."&sid=".$_POST['stateName']);}

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

<script language="javascript" src="js/calendar_eu.js"></script>

<script language="javascript" type="text/javascript">

function validate_data()

{

	if(document.getElementById("date1").value!=""){

		if(!checkdate(document.lstissuepc.date1)){

			return false;

		}

	} else if(document.getElementById("date1").value==""){

		alert("Please input/select starting From date !");

		return false;

	}

	if(document.getElementById("date2").value!=""){

		if(!checkdate(document.lstissuepc.date2)){

			return false;

		}

	} else if(document.getElementById("date2").value==""){

		alert("Please input/select ending To date !");

		return false;

	}

	var no_of_days = getDaysbetween2Dates(document.lstissuepc.date1,document.lstissuepc.date2);

	if(no_of_days < 0){

		alert("* Sorry! reporting date range wrongly selected. Please correct and submit again.\n");

		return false;

	}

	if(document.getElementById("stateName").value==0){

		alert("* Please select state name. !!");

		document.getElementById("stateName").focus();

		return false;

	}

	return true;

}



function paging_list()

{

	window.location="lstpcissue.php?t="+document.getElementById("dealerType").value+"&dt1="+document.getElementById("sdate").value+"&dt2="+document.getElementById("edate").value+"&sid="+document.getElementById("stateName").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;

}



function firstpage_list()

{

	document.getElementById("page").value = 1;

	paging_list();

}



function previouspage_list()

{

	var cpage = parseInt(document.getElementById("page").value);

	if(cpage>1){

		cpage = cpage - 1;

		document.getElementById("page").value = cpage;

	}

	paging_list();

}



function nextpage_list()

{

	var cpage = parseInt(document.getElementById("page").value);

	if(cpage<parseInt(document.getElementById("totalPage").value)){

		cpage = cpage + 1;

		document.getElementById("page").value = cpage;

	}

	paging_list();

}



function lastpage_list()

{

	document.getElementById("page").value = document.getElementById("totalPage").value;

	paging_list();

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

	<div id="content" align="center" style="width:980px;">

  	<form name="lstissuepc" method="post" onsubmit="return validate_data()">

	<fieldset><legend><b>Principal Certificate List</b></legend>

	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="1">

	<tr align="left">

		<td width="20%">Type : <select name="dealerType" id="dealerType" style="width:140px;">

		<?php if($t==1)

				echo '<option value="0">Issued/Renewed</option><option selected value="1">To be renewed</option>';

			else

				echo '<option selected value="0">Issued/Renewed</option><option value="1">To be renewed</option>';

		?></select></td>

		<td width="35%">From : <input name="date1" id="date1" maxlength="10" size="10" value="<?php echo date("d-m-Y",$dt1); ?>"/>&nbsp;<script language="JavaScript">new tcal ({'formname': 'lstissuepc', 'controlname': 'date1'});</script>&nbsp;&nbsp;&nbsp;&nbsp;To : <input name="date2" id="date2" maxlength="10" size="10" value="<?php echo date("d-m-Y",$dt2); ?>"/>&nbsp;<script language="JavaScript">new tcal ({'formname': 'lstissuepc', 'controlname': 'date2'});</script></td>

		<td width="35%">State : <?php 

		if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S"){

			echo '<select name="stateName" id="stateName" style="width:240px;"><option value="0">-- Select --</option>';

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

		}?>

		</td>

		<td width="10%"><input type="submit" name="submit" value=" Generate "/>&nbsp;<input type="hidden" name="sdate" id="sdate" value="<?php echo $dt1; ?>"/><input type="hidden" name="edate" id="edate" value="<?php echo $dt2; ?>"/></td>

	</tr>

	</table>

	<table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#9681FC">

	<?php if($msg!="") {echo '<tr><td colspan="8" aling="center" style="color:#FF0000; font-weight:bold">'.$msg.'</font>'; } ?>

	<tr class="tableHead" bgcolor="#CCCCCC">

		<td width="4%">Sl.No.</td>

		<td width="25%">Dealer Name</td>

		<td width="15%">Location</td>

		<td width="5%">State</td>

		<td width="5%">Type</td>

		<td width="7%">Issue For</td>

		<td width="25%">Issue / Renewal No.</td>

		<td width="7%">Issue Date</td>

		<td width="7%">Valid Upto</td>

	</tr>

	<?php

	$start=0;

	$count = 0;

	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}

	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$start=($_REQUEST['pg']-1)*$end;}

	$ctr = $start;

	if($t==0){

		if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S"){

			$sql = mysql_query("SELECT pcissue.*, licence.licence_for, dealer_name, city_name, state_abbre FROM pcissue INNER JOIN licence ON pcissue.licence_id = licence.licence_id INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE pcissue.issue_date BETWEEN '".date("Y-m-d",$dt1)."' AND '".date("Y-m-d",$dt2)."' AND state.state_id=".$sid." ORDER BY state_abbre, issue_date, certificate_no, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

		} elseif($_SESSION['princicate_utype']=="U"){

			$sql = mysql_query("SELECT pcissue.*, licence.licence_for, dealer_name, city_name, state_abbre FROM pcissue INNER JOIN licence ON pcissue.licence_id = licence.licence_id INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE pcissue.issue_date BETWEEN '".date("Y-m-d",$dt1)."' AND '".date("Y-m-d",$dt2)."' AND uid=".$_SESSION['princicate_uid']." ORDER BY state_abbre, issue_date, certificate_no, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

		}

	} elseif($t==1){

		if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S"){

			$sql = mysql_query("SELECT pcissue.*, licence.licence_for, dealer_name, city_name, state_abbre FROM pcissue INNER JOIN licence ON pcissue.licence_id = licence.licence_id INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE is_editable='Y' AND pcissue.valid_upto BETWEEN '".date("Y-m-d",$dt1)."' AND '".date("Y-m-d",$dt2)."' AND state.state_id=".$sid." ORDER BY state_abbre, issue_date, certificate_no, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

		} elseif($_SESSION['princicate_utype']=="U"){

			$sql = mysql_query("SELECT pcissue.*, licence.licence_for, dealer_name, city_name, state_abbre FROM pcissue INNER JOIN licence ON pcissue.licence_id = licence.licence_id INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN city ON dealer.location_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE is_editable='Y' AND pcissue.valid_upto BETWEEN '".date("Y-m-d",$dt1)."' AND '".date("Y-m-d",$dt2)."' AND uid=".$_SESSION['princicate_uid']." ORDER BY state_abbre, issue_date, certificate_no, dealer_name LIMIT ".$start.",".$end,$link) or die(mysql_error());

		}

	}

	while($row = mysql_fetch_array($sql)){

		if($count%2){$RowColor='bgcolor="#E4E4ED"';} else {$RowColor='bgcolor="#FFFBEA"';}

		$ctr++;

		$count++;

		if($row['issue_type']=="I"){$issue_type = "Issue";} elseif($row['issue_type']=="R"){$issue_type = "Renewal";}

		$issue_number = ($row['certificate_no']>999 ? $row['certificate_no'] : ($row['certificate_no']>99 && $row['certificate_no']<1000 ? "0".$row['certificate_no'] : ($row['certificate_no']>9 && $row['certificate_no']<100 ? "00".$row['certificate_no'] : "000".$row['certificate_no'])));

		if($row['certificate_prefix']!=null){$issue_number = $row['certificate_prefix']."/".$issue_number;}

		if($row['licence_for']==1){$issue_for = "Paddy";} elseif($row['licence_for']==2){$issue_for = "Vegetable";}

		echo '<tr class="tableData" '.$RowColor.'>';

		echo '<td>'.$ctr.'</td>';

		echo '<td>'.$row['dealer_name'].'</td>';

		echo '<td>'.$row['city_name'].'</td>';

		echo '<td align="center">'.$row['state_abbre'].'</td>';

		echo '<td>'.$issue_type.'</td>';

		echo '<td>'.$issue_for.'</td>';

		echo '<td>'.$issue_number.'</td>';

		echo '<td>'.($row['issue_date']==null? "&nbsp;" : date("d-m-Y",strtotime($row['issue_date']))).'</td>';

		echo '<td>'.($row['valid_upto']==null? "&nbsp;" : date("d-m-Y",strtotime($row['valid_upto']))).'</td>';

		echo '</tr>';

	} ?>

	</table>

	<?php 

	if($t==0){

		if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S"){

			$sql = mysql_query("SELECT * FROM pcissue WHERE issue_date BETWEEN '".date("Y-m-d",$dt1)."' AND '".date("Y-m-d",$dt2)."' AND state_id=".$sid,$link) or die(mysql_error());

		} elseif($_SESSION['princicate_utype']=="U"){

			$sql = mysql_query("SELECT * FROM pcissue INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE issue_date BETWEEN '".date("Y-m-d",$dt1)."' AND '".date("Y-m-d",$dt2)."' AND uid=".$_SESSION['princicate_uid'],$link) or die(mysql_error());

		}

	} elseif($t==1){

		if($_SESSION['princicate_utype']=="A" || $_SESSION['princicate_utype']=="S"){

			$sql = mysql_query("SELECT * FROM pcissue WHERE is_editable='Y' AND valid_upto BETWEEN '".date("Y-m-d",$dt1)."' AND '".date("Y-m-d",$dt2)."' AND state_id=".$sid,$link) or die(mysql_error());

		} elseif($_SESSION['princicate_utype']=="U"){

			$sql = mysql_query("SELECT * FROM pcissue INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE is_editable='Y' AND valid_upto BETWEEN '".date("Y-m-d",$dt1)."' AND '".date("Y-m-d",$dt2)."' AND uid=".$_SESSION['princicate_uid'],$link) or die(mysql_error());

		}

	}

	$tot_row = mysql_num_rows($sql);

	echo '<p> Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	echo '<input type="button" name="show" id="show" value="Show :" onclick="paging_list()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="1" tabindex="1" />&nbsp;rows&nbsp;&nbsp;&nbsp;&nbsp;';

	$total_page=0;

	if($tot_row>$end){

		echo "Page number: ";

		$total_page=ceil($tot_row/$end);

		echo '<select name="page" id="page" onchange="paging_list()" style="vertical-align:middle">';

		for($i=1;$i<=$total_page;$i++){

			if(isset($_REQUEST["pg"]) && $_REQUEST["pg"]==$i)

				echo '<option selected value="'.$i.'">'.$i.'</option>';

			else

				echo '<option value="'.$i.'">'.$i.'</option>';

		}

		echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	} else {

		echo '<input type="hidden" name="page" id="page" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	}

	

	echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" />';

	if($total_page>1 && $_REQUEST["pg"]>1)

		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_list()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_list()" />&nbsp;&nbsp;';

	if($total_page>1 && $_REQUEST["pg"]<$total_page)

		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_list()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_list()" />';

	echo '&nbsp;&nbsp;<input type="button" name="refresh" id="refresh" value=" Refresh " onclick="window.location=\'lstpcissue.php\'" />&nbsp;&nbsp;<input type="button" name="back" id="back" value=" Back " onclick="window.location=\'menu.php\'" />';

	?>

	</fieldset>

	</form>

	</div>

	<div id="footer">VNR Seeds Pvt. Ltd.<br /></div>

</div>

</body>

</html>