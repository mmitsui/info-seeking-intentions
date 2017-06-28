<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');
	require_once('../core/Util.class.php');

	$base = Base::getInstance();

	if ($base->isSessionActive())
	{
		$userID = $base->getUserID();
		$projectID=$base->getProjectID();
		$localTime = $_GET['localTime'];
		$localDate = $_GET['localDate'];
		$localTimestamp = $_GET['localTimestamp'];

    $connection = Connection::getInstance();
		$topicAreaID = $base->getTopicAreaID();


		$query = "SELECT Q.question as question,Q.questionID as questionID FROM questions_study Q WHERE Q.questionID=$topicAreaID";
		$results = $connection->commit($query);
		$question1 = '';
		$line = mysql_fetch_array($results,MYSQL_ASSOC);
		$question1 = $line['question'];
		$questionID = $line['questionID'];
		Util::getInstance()->saveActionWithLocalTime("View My Task",$questionID,$base,$localTime,$localDate,$localTimestamp);


?>

<html>
    <head>
		<title>View My Task</title>
		<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
		<link rel="stylesheet" href="../study_styles/custom/text.css">

    </head>
<body style="padding-top:10px; padding-bottom:10px; padding-left:10px;padding-right:10px;">
	<div class="well" style="background-color:rgb(210,210,210);">

<p><span>
<?php
    echo $question1;
    ?>
</span></p>
</div>
</body>
</html>

<?php

			// }


	}
?>
