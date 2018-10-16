<?php


session_start();
require_once('core/Connection.class.php');
require_once('core/Base.class.php');
require_once('core/Util.class.php');
require_once('core/Stage.class.php');



function getGoodBadAnnotationCounts($type){
	$col1="";
	$col2="";
	if($type=="eye_tracking"){
		$col1="bad_eye_tracking_1";
		$col2="bad_eye_tracking_2";
		$col1reason="bad_eye_tracking_reason_1";
		$col2reason="bad_eye_tracking_reason_2";
	}else if($type=='intention'){
		$col1="bad_intention_annotation_1";
		$col2="bad_intention_annotation_2";
		$col1reason="bad_intention_annotation_reason_1";
		$col2reason="bad_intention_annotation_reason_2";
	}
	$reasons = array();
	$bad_reasons = array();
	$other_reasons = array();
	$summary = array();
	$summary['Copy Editing'] = array();
	$summary['Story Pitch'] = array();
	$summary['Relationships'] = array();
	$summary['Interview Preparation'] = array();
	$summary['total'] = array();

	foreach($summary as $key=>$value){

		$data_array = array();

		//		0=>good, 1=>bad, 2=>remaining
		$value = 0;
		$label = '';

		$color = '#33cc33';
		$highlight = '#66FF66';
		$data_point = array('value'=>$value,'label'=>$label,'color'=>$color,'highlight'=>$highlight);
		array_push($summary[$key],$data_point);

		$color = '#F7464A';
		$highlight = '#FF5A5E';
		$data_point = array('value'=>$value,'label'=>$label,'color'=>$color,'highlight'=>$highlight);
		array_push($summary[$key],$data_point);


		$color = '#AAAAAA';
		$highlight = '#DCDCDC';
		$data_point = array('value'=>$value,'label'=>$label,'color'=>$color,'highlight'=>$highlight);
		array_push($summary[$key],$data_point);

	}





	$query = "SELECT * FROM participant_id_to_task INNER JOIN (SELECT * FROM (SELECT userID,projectID,arrived,participantID as pID,$col1 as bad_data_1,$col2 as bad_data_2,$col1reason as bad_data_1_reason,$col2reason as bad_data_2_reason FROM recruits INNER JOIN (SELECT userID as uID,participantID,arrived FROM users WHERE userID < 500) a on a.uID=userID) c WHERE pID IS NOT NULL) d on d.pID=participantID";

	$cxn = Connection::getInstance();
	$results = $cxn->commit($query);

	// Generate total counts
	while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
		if($line['arrived']==1){
			$reasons_subarray = array();
			$reasons_subarray['participantID'] = $line['participantID'];
			$reasons_subarray['userID'] = $line['userID'];
			$reasons_subarray['session'] = 1;
			$reasons_subarray['task'] = $line['taskName1'];
			$reasons_subarray['topic'] = $line['topicName1'];
			$reasons_subarray['reason'] = $line['bad_data_1_reason'];
			if(!empty($line['bad_data_1_reason'])){
			    if(intval($line["bad_data_1"]==1)){
                   array_push($bad_reasons,$reasons_subarray);
                }else{
                    array_push($other_reasons,$reasons_subarray);
                }
			}


			$reasons_subarray = array();
			$reasons_subarray['participantID'] = $line['participantID'];
			$reasons_subarray['userID'] = $line['userID'];
			$reasons_subarray['session'] = 2;
			$reasons_subarray['task'] = $line['taskName2'];
			$reasons_subarray['topic'] = $line['topicName2'];
			$reasons_subarray['reason'] = $line['bad_data_2_reason'];
            if(!empty($line['bad_data_2_reason'])){
                if(intval($line["bad_data_2"]==1)){
                    array_push($bad_reasons,$reasons_subarray);
            }else{
                    array_push($other_reasons,$reasons_subarray);
                }
            }

		}


		$cols = array(1=>$col1, 2=>$col2);
		foreach(array(1,2) as $sessionnum){
			$task = $line["taskName$sessionnum"];
            if($line["bad_data_$sessionnum"]===NULL){
                continue;
            }
			$bad_data_bool = intval($line["bad_data_$sessionnum"]);
			$summary[$task][$bad_data_bool]['value'] += 1;
			$summary['total'][$bad_data_bool]['value'] += 1;
		}




	}


	$totals = array();

	//	Generate remainder count
	foreach($summary as $key=>$value) {

		if($key!='total'){
            $query = "SELECT * FROM (SELECT * FROM users WHERE participantID is NOT NULL AND userID< 500) a  INNER JOIN (SELECT * from participant_id_to_task WHERE taskName1='$key' OR taskName2='$key') b on b.participantID=a.participantID";
            $results = $cxn->commit($query);
            $total = mysql_num_rows($results);
			$summary[$key][2]['value'] = $total - ($summary[$key][0]['value'] + $summary[$key][1]['value']);
			$totals[$key]=$total;
		}
	}

    $total = 0;
    foreach($summary as $key=>$value) {
        $total += $totals[$key];
    }

    $summary['total'][2]['value'] = $total - ($summary['total'][0]['value'] + $summary['total'][1]['value']);
    $totals['total']=$total;

	//Generate labels
	foreach($summary as $key=>$value) {
		foreach(array(0=>'Good',1=>'Bad',2=>'Pending') as $index=>$labelprefix){
            $perc = round(((double)$summary[$key][$index]['value'])/((double)$totals[$key])*100);
			$summary[$key][$index]['label'] = $labelprefix." ($perc%)";
		}


	}

	return array('summary'=>$summary,'bad_reasons'=>$bad_reasons,'other_reasons'=>$other_reasons);
}
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



$cxn = Connection::getInstance();
$results = $cxn->commit("SELECT COUNT(*) as ct FROM users WHERE userID<500 AND participantID NOT IN ('S001','S007','S013') AND participantID IS NOT NULL");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$c = $line['ct'];

$results = $cxn->commit("SELECT COUNT(*) as total FROM participant_id_to_task");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$t = $line['total'];


$perc = round(((double)$c)/((double)$t)*100);

//$topic_counts = array(
//	'Coelacanths'=>array(),
//		'Methane'=>array()
//);
//
//
//$results = $cxn->commit("SELECT COUNT(*) as ct FROM users WHERE userID<500 AND participantID IS NOT NULL AND (topicAreaID1 IN (1,5) OR topicAreaID2 IN (1,5))");
//$line = mysql_fetch_array($results,MYSQL_ASSOC);
//$topic_counts['Coelacanths']['count'] = $line['ct'];
//
//$results = $cxn->commit("SELECT COUNT(*) as total FROM participant_id_to_task");
//$line = mysql_fetch_array($results,MYSQL_ASSOC);
//$topic_counts['Coelacanths']['total'] = $line['total'];







$task_counts = array(
	'Copy Editing'=>array(),
	'Story Pitch'=>array(),
	'Relationships'=>array(),
	'Interview Preparation'=>array()
);

$results = $cxn->commit("SELECT COUNT(*) as ct FROM users WHERE userID<500 AND participantID IS NOT NULL AND participantID NOT IN ('S001','S007','S013') AND (topicAreaID1 IN (1,5) OR topicAreaID2 IN (1,5))");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$task_counts['Copy Editing']['count'] = $line['ct'];

$results = $cxn->commit("SELECT COUNT(*) as total FROM participant_id_to_task WHERE (questionID1 IN (1,5) OR questionID2 IN (1,5))");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$task_counts['Copy Editing']['total'] = $line['total'];




$results = $cxn->commit("SELECT COUNT(*) as ct FROM users WHERE userID<500 AND participantID IS NOT NULL AND participantID NOT IN ('S001','S007','S013') AND (topicAreaID1 IN (2,6) OR topicAreaID2 IN (2,6))");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$task_counts['Story Pitch']['count'] = $line['ct'];

$results = $cxn->commit("SELECT COUNT(*) as total FROM participant_id_to_task WHERE (questionID1 IN (2,6) OR questionID2 IN (2,6))");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$task_counts['Story Pitch']['total'] = $line['total'];



$results = $cxn->commit("SELECT COUNT(*) as ct FROM users WHERE userID<500 AND participantID IS NOT NULL AND (topicAreaID1 IN (3,7) OR topicAreaID2 IN (3,7)) AND participantID NOT IN ('S001','S007','S013')");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$task_counts['Relationships']['count'] = $line['ct'];

$results = $cxn->commit("SELECT COUNT(*) as total FROM participant_id_to_task WHERE (questionID1 IN (3,7) OR questionID2 IN (3,7))");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$task_counts['Relationships']['total'] = $line['total'];



$results = $cxn->commit("SELECT COUNT(*) as ct FROM users WHERE userID<500 AND participantID IS NOT NULL AND (topicAreaID1 IN (4,8) OR topicAreaID2 IN (4,8)) AND participantID NOT IN ('S001','S007','S013')");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$task_counts['Interview Preparation']['count'] = $line['ct'];

$results = $cxn->commit("SELECT COUNT(*) as total FROM participant_id_to_task WHERE (questionID1 IN (4,8) OR questionID2 IN (4,8))");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$task_counts['Interview Preparation']['total'] = $line['total'];


$results = $cxn->commit("SELECT SUM(arrived) as num_arrived FROM users WHERE userID<500");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$num_arrived = $line['num_arrived'];
$results = $cxn->commit("SELECT COUNT(*) as expected_users FROM users WHERE userID<500 AND arrived IS NULL");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$expected_user_total = $line['expected_users'];
$results = $cxn->commit("SELECT COUNT(*) AS arrived_or_not FROM users WHERE userID<500 and arrived IS NOT NULL");
$line = mysql_fetch_array($results,MYSQL_ASSOC);
$arrived_or_not_count = $line['arrived_or_not'];
$num_not_arrived = $arrived_or_not_count - $num_arrived;
$expected_users_remainder = $expected_user_total;




?>
<html>
<head>
<title>Study Progress</title>
<link rel="stylesheet" href="study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="study_styles/custom/text.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

	<script type="text/javascript" src="study_styles/chart.js-1.0.2/Chart.min.js"></script>
<!--	<script type="text/javascript" src="lib/jquery-2.1.3.min.js"></script>-->

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

		th.header:not([data-sortable='false']) {
			background-image: url(img/tablesorter/bg.gif);
			cursor: pointer;
			font-weight: bold;
			background-repeat: no-repeat;
			background-position: center right;
			border-right: 1px solid #dad9c7;
			margin-left: -1px;
			padding-right: 20px;
		}

	</style>

</head>


<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.css">

<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.js"></script>


<style>


	.sidenav {
		height: 100%;
		width: 0;
		position: fixed;
		z-index: 1;
		top: 0;
		left: 0;
		background-color: #111;
		overflow-x: hidden;
		transition: 0.0s;
		padding-top: 60px;
	}

	.sidenav a {
		padding: 8px 8px 8px 32px;
		text-decoration: none;
		font-size: 18px;
		color: #818181;
		display: block;
		transition: 0.0s
	}

	.sidenav a:hover, .offcanvas a:focus{
		color: #f1f1f1;
	}

	.sidenav .closebtn {
		position: absolute;
		top: 0;
		right: 25px;
		font-size: 36px;
		margin-left: 50px;
	}

	#main {
		transition: margin-left .0s;
		padding: 16px;
	}

	@media screen and (max-height: 450px) {
		.sidenav {padding-top: 15px;}
		.sidenav a {font-size: 18px;}
	}
</style>

<script type="text/javascript">

	function openNav() {
		document.getElementById("mySidenav").style.width = "250px";
		document.getElementById("main").style.marginLeft = "250px";
	}

	$(document).ready( function () {
//		var ctx = document.getElementById("intent_summary_chart").getContext("2d");
//		var data = <?php //echo json_encode($summary_data);?>//;
//		var options= <?php //echo json_encode($options);?>//;
//		var myBarChart = new Chart(ctx).Bar(data,options);

		$('#bad_intention_reasons_table').DataTable({
			paging:false,
			searching:false,
			info:false,
			"order": [[ 0, "asc" ]]
		});

		$('#bad_eyetracking_reasons_table').DataTable({
			paging:false,
			searching:false,
			info:false,
			"order": [[ 0, "asc" ]]
		});

		var dummy_donut_data = [
			{
				value: 300,
				color: "#33cc33",
				highlight: "#66FF66",
				label: "Good Data"
			},
			{
				value: 100,
				color:"#F7464A",
				highlight: "#FF5A5E",
				label: "Bad Data"
			},
			{
				value: 50,
				color: "#AAAAAA",
				highlight: "#DCDCDC",
				label: "Pending Annotation"
			},

		];

		<?php
		$arr = getGoodBadAnnotationCounts("eye_tracking");
		$eye_tracking_bad_reasons = $arr['bad_reasons'];
        $eye_tracking_other_reasons = $arr['other_reasons'];
        $summary = $arr['summary'];
		?>
		var eyetracking_total_data = <?php echo json_encode($summary['total'])?>;
		var eyetracking_copyediting_data = <?php echo json_encode($summary['Copy Editing'])?>;
		var eyetracking_storypitch_data = <?php echo json_encode($summary['Story Pitch'])?>;
		var eyetracking_relationships_data = <?php echo json_encode($summary['Relationships'])?>;
		var eyetracking_interviewpreparation_data = <?php echo json_encode($summary['Interview Preparation'])?>;



		var ctx = document.getElementById("eyetracking_progress_total").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(eyetracking_total_data);
		var ctx = document.getElementById("eyetracking_progress_copyediting").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(eyetracking_copyediting_data);
		var ctx = document.getElementById("eyetracking_progress_storypitch").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(eyetracking_storypitch_data);
		var ctx = document.getElementById("eyetracking_progress_relationships").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(eyetracking_relationships_data);
		var ctx = document.getElementById("eyetracking_progress_interviewpreparation").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(eyetracking_interviewpreparation_data);




		<?php
		$arr = getGoodBadAnnotationCounts("intention");
		$intention_bad_reasons = $arr['bad_reasons'];
		$summary = $arr['summary'];
		?>

		var intent_total_data = <?php echo json_encode($summary['total'])?>;
		var intent_copyediting_data = <?php echo json_encode($summary['Copy Editing'])?>;
		var intent_storypitch_data = <?php echo json_encode($summary['Story Pitch'])?>;
		var intent_relationships_data = <?php echo json_encode($summary['Relationships'])?>;
		var intent_interviewpreparation_data = <?php echo json_encode($summary['Interview Preparation'])?>;

		var ctx = document.getElementById("intent_progress_total").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(intent_total_data);
		var ctx = document.getElementById("intent_progress_copyediting").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(intent_copyediting_data);
		var ctx = document.getElementById("intent_progress_storypitch").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(intent_storypitch_data);
		var ctx = document.getElementById("intent_progress_relationships").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(intent_relationships_data);
		var ctx = document.getElementById("intent_progress_interviewpreparation").getContext("2d");
		var myBarChart = new Chart(ctx).Doughnut(intent_interviewpreparation_data);




		var user_progress_barchart_data = {
			labels: [],
			datasets: [
				{
					label: "Users Arrived + Completed",
					fillColor: "rgba(51, 153, 51,0.5)",
					strokeColor: "rgba(51, 153, 51,0.8)",
					highlightFill: "rgba(51, 153, 51,0.75)",
					highlightStroke: "rgba(51, 153, 51,1)",
					data: [<?php echo $num_arrived; ?>]
				},
				{
					label: "Not Arrived",
					fillColor: "rgba(255, 80, 80,0.5)",
					strokeColor: "rgba(255, 80, 80,0.8)",
					highlightFill: "rgba(255, 80, 80,0.75)",
					highlightStroke: "rgba(255, 80, 80,1)",
					data: [<?php echo $num_not_arrived;?>]
				},
				{
					label: "Pending Users",
					fillColor: "rgba(170, 170, 170,0.5)",
					strokeColor: "rgba(170, 170, 170,0.8)",
					highlightFill: "rgba(170, 170, 170,0.75)",
					highlightStroke: "rgba(170, 170, 170,1)",
					data: [<?php echo $expected_users_remainder;?>]
				}
			]
		};



		var ctx = document.getElementById("study_retention_canvas").getContext("2d");
		var myBarChart = new Chart(ctx).Bar(user_progress_barchart_data);

		openNav();

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

<div id="mySidenav" class="sidenav">
	<a href="#overall_progress">Overall Study Progress</a>
	<a href="#task_progress">Task Progress</a>
	<a href="#study_retention">Study Retention</a>
	<a href="#eye_tracking_progress">Eye Tracking Progress</a>
	<a href="#bad_eye_tracking_notes">Unusable Eye Tracking Notes</a>
	<a href="#other_eye_tracking_notes">Other Eye Tracking Notes</a>
	<a href="#intention_annotation_progress">Intention Annotation Progress</a>
	<a href="#bad_intention_annotation_notes">Bad Intention Annotation Notes</a>
</div>

<div id="main">
	<center><h2>Study Progress</h2><br/><br/><button class='btn' onclick="location.href='getUsers.php';return false;">Return To Users Page</button></center><br/>




	<div id="login_div" style="display:block;">


		<center><div class="panel panel-default" style="width:60%">
				<div class="panel-heading" id="overall_progress"><strong><?php echo "Overall Study Progress: $c/$t";?></strong></div>
				<div class="panel-body">

					<?php
					echo "<div class='progress'><div class='progress-bar progress-bar-success' style='width:$perc%'>$c/$t ($perc%)</div></div>";
					?>
				</div>
			</div>
		</center>

		<center><div class="panel panel-default" id="task_progress" style="width:60%">
				<div class="panel-heading"><strong>Task Progress</strong></div>
				<div class="panel-body">

					<?php

					foreach($task_counts as $task_name=>$counts){
						$c = $counts['count'];
						$t = $counts['total'];
						$perc = round(((double)$c)/((double)$t)*100);
						echo "<p>$task_name</p>";
						echo "<div class='progress'><div class='progress-bar progress-bar-success' style='width:$perc%'>$c/$t ($perc%)</div></div>";
					}

					?>
				</div>
			</div>
		</center>





		<center><div class="panel panel-default" id="study_retention" style="width:60%">
				<div class="panel-heading">
					<strong>Study Retention </strong>
				</div>
				<div class="panel-body">

					<canvas id="study_retention_canvas" width="400" height="400"></canvas>

				</div>
			</div>
		</center>


		<center><div class="panel panel-default" id="eye_tracking_progress" style="width:60%">
				<div class="panel-heading"><strong>Eye Tracking Progress</strong></div>
				<div class="panel-body">

					<div class="row">
						<div class="col-md-12"><strong>Total</strong></div>
					</div>
					<div class="row">
						<div class="col-md-12"><canvas id="eyetracking_progress_total" width="200" height="200"></canvas></div>
					</div>
					<div class="row">
						<div class="col-md-6"><strong>Copy Editing</strong></div>
						<div class="col-md-6"><strong>Story Pitch</strong></div>
					</div>
					<div class="row">
						<div class="col-md-6"><canvas id="eyetracking_progress_copyediting" width="200" height="200"></canvas></div>
						<div class="col-md-6"><canvas id="eyetracking_progress_storypitch" width="200" height="200"></canvas></div>
					</div>
					<div class="row">
						<div class="col-md-6"><strong>Relationships</strong></div>
						<div class="col-md-6"><strong>Interview Preparation</strong></div>
					</div>
					<div class="row">
						<div class="col-md-6"><canvas id="eyetracking_progress_relationships" width="200" height="200"></canvas></div>
						<div class="col-md-6"><canvas id="eyetracking_progress_interviewpreparation" width="200" height="200"></canvas></div>
					</div>



















				</div>
			</div>
		</center>

		<center>
			<div class="panel panel-default" id="bad_eye_tracking_notes" style="width:60%">
			<div class="panel-heading"><strong>Unusable Eye Tracking Notes</strong></div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<table id='bad_eyetracking_reasons_table'  class="table  table-striped table-bordered">
							<thead><tr><th>Subject ID</th><th>User ID</th><th>Session #</th><th>Task</th><th>Topic</th><th data-sortable="false">Reason</th></tr></thead>
							<tbody>
							<?php
							foreach($eye_tracking_bad_reasons as $row){
								$participantID = $row['participantID'];
								$session = $row['session'];
								$userID = $row['userID'];
								$task = $row['task'];
								$topic = $row['topic'];
								$reason = $row['reason'];
								echo "<tr><td>$participantID</td><td>$userID</td><td>$session</td><td>$task</td><td>$topic</td><td>$reason</td></tr>";
							}
							?>
							</tbody>
						</table>

					</div>
				</div>
			</div>
			</div>


		</center>


		<center>
			<div class="panel panel-default" id="other_eye_tracking_notes" style="width:60%">
				<div class="panel-heading"><strong>Other Eye Tracking Notes</strong></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<table id='other_eyetracking_reasons_table'  class="table  table-striped table-bordered">
								<thead><tr><th>Subject ID</th><th>User ID</th><th>Session #</th><th>Task</th><th>Topic</th><th data-sortable="false">Reason</th></tr></thead>
                                <tbody>
                                <?php
                                foreach($eye_tracking_other_reasons as $row){
                                    $participantID = $row['participantID'];
                                    $userID = $row['userID'];
                                    $session = $row['session'];
                                    $task = $row['task'];
                                    $topic = $row['topic'];
                                    $reason = $row['reason'];
                                    echo "<tr><td>$participantID</td><td>$userID</td><td>$session</td><td>$task</td><td>$topic</td><td>$reason</td></tr>";
                                }
                                ?>
                                </tbody>
							</table>

						</div>
					</div>
				</div>
			</div>


		</center>





		<center><div class="panel panel-default" id="intention_annotation_progress" style="width:60%">
				<div class="panel-heading"><strong>Intention Annotation Progress</strong></div>
				<div class="panel-body">

					<div class="row">
						<div class="col-md-12"><strong>Total</strong></div>
					</div>
					<div class="row">
						<div class="col-md-12"><canvas id="intent_progress_total" width="200" height="200"></canvas></div>
					</div>

					<div class="row">
						<div class="col-md-6"><strong>Copy Editing</strong></div>
						<div class="col-md-6"><strong>Story Pitch</strong></div>
					</div>
					<div class="row">
						<div class="col-md-6"><canvas id="intent_progress_copyediting" width="200" height="200"></canvas></div>
						<div class="col-md-6"><canvas id="intent_progress_storypitch" width="200" height="200"></canvas></div>
					</div>

					<div class="row">
						<div class="col-md-6"><strong>Relationships</strong></div>
						<div class="col-md-6"><strong>Interview Preparation</strong></div>
					</div>
					<div class="row">
						<div class="col-md-6"><canvas id="intent_progress_relationships" width="200" height="200"></canvas></div>
						<div class="col-md-6"><canvas id="intent_progress_interviewpreparation" width="200" height="200"></canvas></div>
					</div>

				</div>
			</div>
		</center>


		<center>
			<div class="panel panel-default" id="bad_intention_annotation_notes" style="width:60%">
				<div class="panel-heading"><strong>Bad Intention Annotation Notes</strong></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<table id='bad_intention_reasons_table' class="table  table-striped table-bordered">
								<thead><tr><th>Subject ID</th><th>User ID</th><th>Session #</th><th>Task</th><th>Topic</th><th data-sortable="false">Reason</th></tr></thead>
								<tbody>
								<?php
								foreach($intention_bad_reasons as $row){
									$participantID = $row['participantID'];
									$userID = $row['userID'];
									$session = $row['session'];
									$task = $row['task'];
									$topic = $row['topic'];
									$reason = $row['reason'];
									echo "<tr><td>$participantID</td><td>$userID</td><td>$session</td><td>$task</td><td>$topic</td><td>$reason</td></tr>";
								}
								?>
								</tbody>
							</table>

						</div>
					</div>
				</div>
			</div>


		</center>

</div>



</body></html>
