
<html>
<head>
<title>View Course Writeups</title>
	<link rel="stylesheet" href="study_styles/bootstrap-3.3.5-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="study_styles/custom/text.css">
	<link rel="stylesheet" href="styles.css">



</head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="study_styles/jquery-tablesorter/jquery.tablesorter.min.js"></script>

<script type="text/javascript">


	$(document).ready(function()
			{
				$("#myTable").tablesorter();
			}
	);


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
<div id="login_div" style="display:block;">

<?php

	session_start();
	require_once('core/Connection.class.php');
	require_once('core/Base.class.php');
	require_once('core/Util.class.php');
    require_once('core/Stage.class.php');





//  Show for each (user, task+topic):
//  1) Pre-task, Post-task questionnaires
//  2) # of things
//  3) Time taken to complete each thing


	$partID_to_userID = array(
//			'S001'=>	2,
			'S002'=>	7,
			'S003'=>	3,
			'S004'=>	4,
			'S005'=>	6,
			'S006'=>	17,
//			'S007'=>	1,
			'S008'=>	14,
			'S009'=>	11,
			'S010'=>	13,
			'S011'=>	22,
			'S012'=>	21,
//			'S013'=>	9,
			'S014'=>	26,
			'S015'=>	24,
			'S016'=>	12
	);




	$colnames = array(
			'Subject ID',
			'Task Number',
			'Topic',
			'Task',
			'Time(Task)',
			'Time(Intention)',
			'N(Queries)',
			'N(Bookmarks)',
			'N(Pages)',
			'N(Sources)',
			'How familiar?',
			'How much experience?',
			'Anticipated difficulty?',
			'Actual difficulty?',
			'Actual success?',
			'Enough time?',
			'Did you understand?',
	);




	$print_results = array();
	$uID_str = array();

	foreach($partID_to_userID as $sID=>$uID){
		$print_results[$uID] = array();
		array_push($print_results[$uID],array('Subject ID'=>$sID));
		array_push($print_results[$uID],array('Subject ID'=>$sID));
		array_push($uID_str,$uID);
	}


	$uID_str = "(".implode(", ", $uID_str).")";


	// GET COUNTS
	$cxn = Connection::getInstance();
	$dist_to_table = array(
			array('N(Queries)','query','queries'),
			array('N(Pages)','url','pages'),
			array('N(Bookmarks)','url','bookmarks'),
			array('N(Sources)','source','pages')
	);
	foreach($dist_to_table as $ind=>$elem){
		$colname = $elem[0];
		$item = $elem[1];
		$tablename = $elem[2];
		$results = $cxn->commit("SELECT userID,stageID,IFNULL(COUNT(DISTINCT(".$item.")),0) as ct FROM ".$tablename." WHERE userID IN $uID_str AND stageID IN (15,45) GROUP BY userID, stageID");

		while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
			$userID = $line['userID'];
			$stageID = $line['stageID'];
			$count = $line['ct'];
			$taskNum = -1;
			if($stageID < 36){
				$taskNum = 1;
			}else{
				$taskNum = 2;
			}

			$print_results[$userID][$taskNum-1][$colname]=$count;
		}

	}


	$times_array = array(
			array('Time(Task)',1,15,20),
			array('Time(Intention)',1,35,37),
			array('Time(Task)',2,45,50),
			array('Time(Intention)',2,55,65)
	);

	// GET TIMES FOR EACH TASK
	// GET TIMES FOR INTENTION ANNOTATION
	foreach($partID_to_userID as $sID=>$uID) {
		foreach ($times_array as $item => $value) {
			$colname = $value[0];
			$taskNum = $value[1];
			$startStage = $value[2];
			$endStage = $value[3];
			$results = $cxn->commit("SELECT `timestamp` FROM session_progress WHERE userID=$uID AND projectID=$uID AND stageID=$startStage");
			$line = mysql_fetch_array($results,MYSQL_ASSOC);
			$startTime = $line['timestamp'];

			$results = $cxn->commit("SELECT `timestamp` FROM session_progress WHERE userID=$uID AND projectID=$uID AND stageID=$endStage");
			$line = mysql_fetch_array($results,MYSQL_ASSOC);
			$endTime= $line['timestamp'];

			$diff = $endTime-$startTime;
			$print_results[$uID][$taskNum-1][$colname]=gmdate('H:i:s',$diff);
		}
	}

	// GET PRETASK/POSTTASK QUESTIONS
	$results = $cxn->commit("SELECT userID,stageID,topic_familiarity,assignment_experience,perceived_difficulty FROM questionnaire_pretask WHERE userID IN $uID_str GROUP BY userID, stageID");
	while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
		$userID = $line['userID'];
		$stageID = $line['stageID'];
		$taskNum = -1;
		if($stageID < 36){
			$taskNum = 1;
		}else{
			$taskNum = 2;
		}


		$print_results[$userID][$taskNum-1]['How familiar?']=$line['topic_familiarity'];
		$print_results[$userID][$taskNum-1]['How much experience?']=$line['assignment_experience'];
		$print_results[$userID][$taskNum-1]['Anticipated difficulty?']=$line['perceived_difficulty'];

	}



	$results = $cxn->commit("SELECT userID,stageID,q1_difficult,q2_success,q3_time,q4_comp FROM questionnaire_postsearch WHERE userID IN $uID_str GROUP BY userID, stageID");
	while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
		$userID = $line['userID'];
		$stageID = $line['stageID'];
		$taskNum = -1;
		if($stageID < 36){
			$taskNum = 1;
		}else{
			$taskNum = 2;
		}


		$print_results[$userID][$taskNum-1]['Actual difficulty?']=$line['q1_difficult'];
		$print_results[$userID][$taskNum-1]['Actual success?']=$line['q2_success'];
		$print_results[$userID][$taskNum-1]['Enough time?']=$line['q3_time'];
		$print_results[$userID][$taskNum-1]['Did you understand?']=$line['q4_comp'];

	}




	// GET TOPIC/TASK NAMES

	$results = $cxn->commit("SELECT * FROM (SELECT userID,participantID as pID FROM users WHERE userID IN $uID_str) a INNER JOIN (SELECT * FROM participant_id_to_task) b on a.pID=b.participantID");
	while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
		$userID = $line['userID'];

		$print_results[$userID][0]['Topic'] = $line['topicName1'];
		$print_results[$userID][0]['Task'] = $line['taskName1'];
		$print_results[$userID][0]['Task Number'] = 1;
		$print_results[$userID][1]['Topic'] = $line['topicName2'];
		$print_results[$userID][1]['Task'] = $line['taskName2'];
		$print_results[$userID][1]['Task Number'] = 2;
	}


	// Print all column names

	?>

	<div class="panel" style="display:inline-block">
		<center><h2>Task Results</h2></center>
	<table id="myTable" class="table table-bordered tablesorter">
	<?php
	echo "<thead><tr>";
	foreach($colnames as $cname){
		echo "<th>$cname</th>";
	}
	echo "</tr></thead>";
	echo "<tbody>";
	foreach($partID_to_userID as $partID => $userID) {

		$user_results = $print_results[$userID];


		foreach($user_results as $res){
			echo "<tr>";
			foreach($colnames as $cname){
				$output = $res[$cname];
				echo "<td>$output</td>";
			}
			echo "</tr>";
		}

	}
	echo "</tbody>";
	?>
	</table>
	</div>
</body></html>
