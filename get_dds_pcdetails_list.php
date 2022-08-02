<?php

session_start();

require_once('config/config.php');

if(isset($_POST['cgr']) && $_POST['cgr']!=""){

	echo '<select name="ddsName" id="ddsName" style="width:240px;"><option value="0">-- Select --</option>';

	if($_SESSION['princicate_utype']=="S" || $_SESSION['princicate_utype']=="A"){

		if($_POST['cgr']=="D"){

			$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id WHERE category='D' AND state_id=".$_POST['sid']." ORDER BY dds_name",$link) or die(mysql_error());

		} elseif($_POST['cgr']=="S"){

			$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id WHERE category='S' AND state_id=".$_POST['sid']." ORDER BY dds_name",$link) or die(mysql_error());

		} elseif($_POST['cgr']=="B"){

			$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id WHERE category='B' AND state_id=".$_POST['sid']." ORDER BY dds_name",$link) or die(mysql_error());

		}

	} elseif($_SESSION['princicate_utype']=="U"){

		if($_POST['cgr']=="D"){

			$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE category='D' AND uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

		} elseif($_POST['cgr']=="S"){

			$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE category='S' AND uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

		} elseif($_POST['cgr']=="B"){

			$sql_dds = mysql_query("SELECT Distinct pcissue.dealer_id AS dds_id, dealer_name AS dds_name FROM pcissue INNER JOIN dealer ON pcissue.dealer_id = dealer.dealer_id INNER JOIN area_incharge ON pcissue.sm_id = area_incharge.ai_id WHERE category='B' AND uid=".$_SESSION['princicate_uid']." ORDER BY dds_name",$link) or die(mysql_error());

		}

	}

	while($row_dds = mysql_fetch_array($sql_dds)){

		echo '<option value="'.$row_dds['dds_id'].'">'.$row_dds['dds_name'].'</option>';

	}

	echo '</select>';

}?>