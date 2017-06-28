<?php


session_start();
require_once('core/Connection.class.php');
require_once('core/Base.class.php');
require_once('core/Util.class.php');
require_once('core/Stage.class.php');


function getProgress($userID,$stageID){
	$ret = array();
	// Outputs:
	// 'intention'
	// 'reformulation'
	// 'save'
	// 'unsave'
	// 'none'



	$start_stage = 15;
	$end_stage = 45;

	if($stageID == 15){
		$start_stage = 15;
		$end_stage = 35;
	}else if($stageID==35){
		$start_stage = 45;
		$end_stage = 55;
	}
	$total = 0;
	$ct = 0;
	$retarr = array();
	$cxn = Connection::getInstance();

	$results = $cxn->commit("SELECT * FROM video_segments WHERE userID='$userID' AND stageID='$start_stage'");
	$total = mysql_num_rows($results);
	$results = $cxn->commit("SELECT * FROM video_segments WHERE userID='$userID' AND stageID='$start_stage' AND Details LIKE '%Q%'");
	$total += mysql_num_rows($results)-1;

	$results = $cxn->commit("SELECT * FROM video_intent_assignments WHERE userID='$userID' AND stageID='$end_stage'");
	$ct += mysql_num_rows($results);

	$results = $cxn->commit("SELECT * FROM video_reformulation_history WHERE userID='$userID' AND stageID='$end_stage'");
	$ct += mysql_num_rows($results);

	$results = $cxn->commit("SELECT * FROM video_save_history WHERE userID='$userID' AND stageID='$end_stage'");
	$ct += mysql_num_rows($results);

	$results = $cxn->commit("SELECT * FROM video_unsave_history WHERE userID='$userID' AND stageID='$end_stage'");
	$ct += mysql_num_rows($results);



	if ($total == -1){
		$total = 0;
	}
	$retarr['count'] = $ct;
	$retarr['total'] = $total;
	return $retarr;
}


$userID=$_GET['userID'];

$participantID = '-1';
$cxn = Connection::getInstance();
$res = $cxn->commit("SELECT * FROM users WHERE userID=$userID");
while($line = mysql_fetch_array($res,MYSQL_ASSOC)){
	$participantID = $line['participantID'];
	break;
}

$taskNum=$_GET['taskNum'];
$progress = 0;
$stageID = -1;
$start_stage = -1;
$end_stage = -1;

if($taskNum==1){
	$progress = getProgress($userID,15);
	$stageID = 15;
	$start_stage = 15;
	$end_stage=35;
}else{

	$stageID = 35;
	$start_stage = 45;
	$end_stage=55;
}
$progress = getProgress($userID,$stageID);

$c = $progress['count'];
$t = $progress['total'];
$perc = round(((double)$c)/((double)$t)*100);

$colID_to_name = array(
		'id_start' => "ID Start",
		'id_more' => "ID More",
		'learn_domain' => "Learn Domain",
		'learn_database' => "Learn Database",
		'find_known' => "Find Known",
		'find_specific' => "Find Specific",
		'find_common' => "Find Common",
		'find_without' => "Find Without",
		'keep_link' => "Keep Link",
		'access_item' => "Access Item",
		'access_common' => "Access Common",
		'access_area' => "Access Area",
		'evaluate_correctness' => "Evaluate Correctness",
		'evaluate_specificity' => "Evaluate Specificity",
		'evaluate_usefulness' => "Evaluate Usefulness",
		'evaluate_best' => "Evaluate Best",
		'evaluate_duplication' => "Evaluate Duplication",
		'obtain_specific' => "Obtain Specific",
		'obtain_part' => "Obtain Part",
		'obtain_whole' => "Obtain Whole",
		'other' => "Other"
);




$colID_to_acronym = array(
		'id_start' => "IS",
		'id_more' => "IM",
		'learn_domain' => "LK",
		'learn_database' => "LD",
		'find_known' => "FK",
		'find_specific' => "FS",
		'find_common' => "FC",
		'find_without' => "FP",
		'keep_link' => "KL",
		'access_item' => "AS",
		'access_common' => "AC",
		'access_area' => "AP",
		'evaluate_correctness' => "EC",
		'evaluate_specificity' => "ES",
		'evaluate_usefulness' => "EU",
		'evaluate_best' => "EB",
		'evaluate_duplication' => "ED",
		'obtain_specific' => "OS",
		'obtain_part' => "OP",
		'obtain_whole' => "OW",
		'other' => "Other"
);

$cxn = Connection::getInstance();
$count_string = "";
foreach($colID_to_name as $col_name=>$col_alias){
	$count_string .= "SUM($col_name) as $col_name,";
}
$count_string .= "userID";
$result_counts = $cxn->commit("SELECT $count_string FROM video_intent_assignments WHERE userID='$userID' and stageID='$end_stage'");
$result_counts_line = -1;
$result_counts_line = mysql_fetch_array($result_counts,MYSQL_ASSOC);

$summary_data=array();
$summary_data['labels']=array();
$summary_data['datasets']=array();
$data_set = array();
$data_set['label'] = 'Task';
$data_set['fillColor'] = "rgba(151,187,205,0.5)";
$data_set['strokeColor'] = "rgba(151,187,205,0.8)";
$data_set['highlightFill'] = "rgba(151,187,205,0.75)";
$data_set['highlightStroke'] = "rgba(151,187,205,1)";
$data_set['data'] = array();
$options = array();
$options['scaleOverride'] = true;
$options['scaleSteps'] = 5;
$options['scaleStartValue'] = 0;
$options['scaleStepWidth'] = intval($progress['total']/5);

foreach($colID_to_name as $col_name=>$col_alias){
	array_push($summary_data['labels'],$col_alias);
	array_push($data_set['data'],$result_counts_line[$col_name]);
}

array_push($summary_data['datasets'],$data_set);


?>
<html>
<head>
<title>View Course Writeups</title>
<link rel="stylesheet" href="study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="study_styles/custom/text.css">
	<link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">

	<script type="text/javascript" src="study_styles/chart.js-1.0.2/Chart.min.js"></script>
	<script type="text/javascript" src="lib/jquery-2.1.3.min.js"></script>

	<style>

		table, tbody,thead {
			border: 2px solid black;
			overflow: hidden;
			width: 35px;
			height: 35px;
		}

		td,th,tr {
			border: 2px solid black;
		}

	</style>

</head>
<script type="text/javascript">

	$(document).ready( function () {
		var ctx = document.getElementById("intent_summary_chart").getContext("2d");
		var data = <?php echo json_encode($summary_data);?>;
		var options= <?php echo json_encode($options);?>;
		var myBarChart = new Chart(ctx).Bar(data,options);
	} );




</script>
<noscript>
<style type="text/css">
.pagecontainer {display:none;}
</style>
<div class="noscriptmsg">
You don't have Javascript enabled.  You must enable it in your browser to proceed with the task.
</div>
</noscript>

<body class="style1">
<?php
	echo "<center><h2>Intent: Subject $participantID - Task $taskNum</h2><br/><br/><button class='btn' onclick=\"location.href='editUser.php?userID=$userID';return false;\">Return To Edit Page</button></center><br/>";

?>




<div id="login_div" style="display:block;">


	<center><div class="panel panel-default" style="width:60%">
		<div class="panel-heading"><?php echo "Progress: $c/$t";?></div>
		<div class="panel-body">

			<?php
			echo "<div class='progress'><div class='progress-bar progress-bar-success' style='width:$perc%'>$perc%</div></div>";
			?>
		</div>
	</div>
	</center>

	<center><div class="panel panel-default" style="width:60%">
			<div class="panel-heading">Intent Counts</div>
			<div class="panel-body">
				<canvas id="intent_summary_chart" width="475" height="475"></canvas>


			</div>
		</div></center>



	<center>
	<div class="panel panel-default" style="width:60%">
		<div class="panel-heading">Full Intent Annotations</div>
		<div class="panel-body">
			<table class="table table-striped table-bordered" style="width:30%">
				<thead>
				<tr><th colspan="2">Legend</th></tr>
				</thead>
				<tbody>
				<tr><td>Label </td><td>Color</td></tr>
				<tr><td>Yes </td><td style="background-color:green"></td></tr>
				<tr><td>No </td><td style="background-color:red"></td></tr>
				</tbody>
			</table>
<?php

echo "<table class='table table-striped table-hover table-bordered' style='width:100%'>";
//		Render column names

echo "<thead><tr><th>Intent #</th>";
foreach($colID_to_name as $key=>$value){
	echo "<th>".$colID_to_acronym[$key]."</th>";
}
echo "</tr></thead>";

echo "<tbody>";
//		Render each row
$cxn = Connection::getInstance();
$results = $cxn->commit("SELECT * FROM video_intent_assignments WHERE userID=$userID AND stageID=$end_stage ORDER BY assignmentID ASC");
$intent_count = 0;
while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
	$intent_count += 1;
	echo "\n<tr>";
	echo "<td>$intent_count</td>";
	foreach($colID_to_name as $key=>$value){

		if($line[$key] == 0){
			echo "<td></td>";
		}else{
			if($line[$key."_radio"]=="Yes"){
				echo "<td style=\"background-color:green\"></td>";
			}else{
				echo "<td style=\"background-color:red\"></td>";
			}
		}
	}
	echo "</tr>";
}

echo "</tbody>";
echo "</table>";

    ?>
		</div>
	</div>
	</center>

</body></html>
