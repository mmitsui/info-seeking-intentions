<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	require_once('../core/Connection.class.php');

	Util::getInstance()->checkSession();

	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();

		if (isset($_POST['intro']))
		{
      $localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp =  $_POST['localTimestamp'];

			$base = new Base();
			$stageID = $base->getStageID();
			Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$stageID,$base,$localTime,$localDate,$localTimestamp);
			Util::getInstance()->moveToNextStage();
		}
		else
		{
            ?>
<html>
<head>
<title>Research Study</title>

</head>

<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">
<link rel="stylesheet" href="../study_styles/custom/background.css">
<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript">


function validate(form)
{
    return true;
}


</script>
<body class="body" >
	<div style="width:90%; margin: 0 auto">
	<center><h2>Collaborative Research Study</h2></center>
<form action="intro.php" method="post" onsubmit="return validate(this)">


<p>You have been an assigned to a group project in an undergraduate course on
	Information Technology (IT).
	The description of the assignment you receive from the instructor is shown below:</p>



	<div class="grayrect">
		<span>
			<?php

			$base = Base::getInstance();
			$userID = $base->getUserID();
			$connection = Connection::getInstance();
			$query = "SELECT userID, topicAreaID
						FROM users
						WHERE userID='$userID'";
			$results = $connection->commit($query);
			$line = mysql_fetch_array($results,MYSQL_ASSOC);
			$topicAreaID = $line['topicAreaID'];


			$query = "SELECT Q.question as question FROM questions_study Q WHERE Q.questionID=$topicAreaID";
			$results = $connection->commit($query);
			$question1 = '';
			$line = mysql_fetch_array($results,MYSQL_ASSOC);
			$question1 = $line['question'];


			echo $question1;


			?>
		</span>
	</div>

<p>
	Your group must search online for information sources to use in writing this report.
	Some of the members of your group have already started searching and found some sources.
</p>


<center>
<table>
<tr><td align=center>
<input type="hidden" name="intro" value="true"/>
<input type="hidden" name="localTime" value=""/>
<input type="hidden" name="localDate" value=""/>
<input type="hidden" name="localTimestamp" value=""/>
<button type="submit" id="continue_button" class="pure-button pure-button-primary">Start</button></td></tr>
</table>
</center>
</form>
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
