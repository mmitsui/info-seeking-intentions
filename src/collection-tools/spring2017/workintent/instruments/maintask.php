<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	require_once('../core/Connection.class.php');

	Util::getInstance()->checkSession();

	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();

		if (isset($_POST['maintask']))
		{
      $localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp =  $_POST['localTimestamp'];



			$base = new Base();
			$stageID = $base->getStageID();

			$cxn = Connection::getInstance();
			$userID = $base->getUserID();
			$projectID = $base->getProjectID();
			$date = $base->getDate();
			$time = $base->getTime();
			$timestamp = $base->getTimestamp();
			$query = "UPDATE questions_progress SET `endDate`='$date', `endTime`='$time', `endTimestamp`='$timestamp' WHERE userID='$userID' AND projectID='$projectID' AND stageID='$stageID'";
			$cxn->commit($query);

			Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$stageID,$base,$localTime,$localDate,$localTimestamp);
			Util::getInstance()->moveToNextStage();
		}
		else if(isset($_GET['answer'])){
			$base = new Base();
			$stageID = $base->getStageID();


			$cxn = Connection::getInstance();
			$userID = $base->getUserID();
			$projectID = $base->getProjectID();
			$date = $base->getDate();
			$time = $base->getTime();
			$timestamp = $base->getTimestamp();
			$query = "UPDATE questions_progress SET `endDate`='$date', `endTime`='$time', `endTimestamp`='$timestamp' WHERE userID='$userID' AND projectID='$projectID' AND stageID='$stageID'";
			$cxn->commit($query);


			Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$stageID,$base);
			Util::getInstance()->moveToNextStage();

		}
		else
		{

			$_SESSION['refreshQuestionSidebar'] = 1;
			$base = new Base();
			$userID = $base->getUserID();
			$stageID = $base->getStageID();
			$projectID = $base->getProjectID();







			$part_text = "";
			if($stageID > 35){
				$part_text = "Part 2 - ";
			}
            ?>
<html>
<head>
<title>Research Study</title>

</head>

<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">
<link rel="stylesheet" href="../styles.css">
<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript">


function validate(form)
{
    return confirm("Are you sure you want to proceed?  If you do, you'll be unable to complete the task.");
}


</script>
<body class="body">
	<div class="panel panel-default" style="width:95%;  margin:auto">
		<div class="panel-body">
	<div style="width:90%; margin: 0 auto">
		<center><h2><?php echo $part_text;?>Search Task</h2></center>
<form action="maintask.php" method="post" onsubmit="return validate(this)">


<p>Below is the same task that you were shown before.  If you have not done so, please read it carefully.  You can also review this by clicking the "Assignment" button in your toolbar.</p>

<strong><p>You have approximately 20 minutes to search.  You may open new tabs when searching, but please do not open new browser windows.</p></strong>




	<div class="well" style="background-color:rgb(210,210,210);">
		<span>
			<?php

			$base = Base::getInstance();
			$userID = $base->getUserID();
			$projectID = $base->getProjectID();
			$stageID = $base->getStageID();
			$date = $base->getDate();
			$time = $base->getTime();
			$timestamp = $base->getTimestamp();
			$connection = Connection::getInstance();

			$topicAreaID = $base->getTopicAreaID();

			$base->populateQuestionID();
			$questionID = $base->getQuestionID();
			$question1 = $base->getQuestion();

			$query = "SELECT * FROM questions_progress WHERE userID='$userID' AND projectID='$projectID' AND stageID='$stageID'";
			$cxn = Connection::getInstance();
			$results = $cxn->commit($query);

			if(mysql_num_rows($results)<1){
				$query = "INSERT INTO questions_progress (userID,projectID,stageID,questionID,startDate,startTime,startTimestamp) VALUES ('$userID','$projectID','$stageID','$questionID','$date','$time','$timestamp')";
				$cxn->commit($query);
			}



			$qQuery = "SELECT question, answer, altAnswer
			FROM questions_study
			WHERE questionID = '$questionID'"; //Added topic area ID

			$connection = Connection::getInstance();
			$results = $connection->commit($qQuery);
			$line = mysql_fetch_array($results, MYSQL_ASSOC);
			$question = $line['question'];
			$answer = $line['answer'];
			$altAnswer = $line['altAnswer'];




			echo $question1;


			?>
		</span>
	</div>


<!-- <p>
	 <strong>DO NOT CLICK 'FINISH' UNTIL INSTRUCTED</strong>.  If you delete this tab, you may find revisit it by clicking the 'Home' button.
</p> -->


<center>
<table>
<tr><td align=center>
<input type="hidden" name="maintask" value="true"/>
<input type="hidden" name="localTime" value=""/>
<input type="hidden" name="localDate" value=""/>
<input type="hidden" name="localTimestamp" value=""/>
<?php

	$base = Base::getInstance();
	$stageID = $base->getStageID();
	$taskNum = $base->getTaskNum();
	$userID = $base->getUserID();

	$cxn = Connection::getInstance();
//	$query = "SELECT * FROM users WHERE userID='$userID'";
//	$results = $cxn->commit($query);
//	$line = mysql_fetch_array($results,MYSQL_ASSOC);
//	$finishTopic = $line["finishTopic$taskNum"];
//	if($finishTopic == 1){
//		echo "<button type=\"submit\" id=\"continue_button\" class=\"btn btn-danger\">Finish</button></td></tr>";
//	}
 ?>

		<input type='button' value='Search in a New Tab' id='newtab_button' class='btn btn-success' onclick="window.open('https://www.google.com/'); return false;"/></td></tr>
</table>
</center>
</form>
</div>
</div>
</div>
</body>
</html>
<?php
    }
	}
	else {
		echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
	}

    ?>
