<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');


	$base = Base::getInstance();

	if ($base->isSessionActive())
	{
		$base = new Base();
		$stageID = $base->getStageID();

		$userID = $base->getUserID();
		$projectID=$base->getProjectID();
		$localTime = $_GET['localTime'];
		$localDate = $_GET['localDate'];
		$localTimestamp = $_GET['localTimestamp'];

		Util::getInstance()->saveActionWithLocalTime("Review Video",0,$base,$localTime,$localDate,$localTimestamp);

?>
<html>
<head>

	<?php
	if($stageID<25 || ($stageID>35 && $stageID<51))
	{
	?>
	<title>System Annotation Tutorial
	</title>
	<?php
	}
	else
	{
	?>
	<title>Intention Annotation Tutorial
	</title>
	<?php
	}
	?>



</head>
<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">
<link rel="stylesheet" href="../styles.css">


<body class="body">
	<div class="panel panel-default" style="width:95%;  margin:auto">
		<div class="panel-body">

	<br/>

		<center>
			<?php
			if($stageID<25 || ($stageID>35 && $stageID<51))
			{
			?>
			<center><h2>System Tutorial</h2></center>
			<?php
			}
			else
			{
			?>
			<tr><th><h2>Intention Annotation Tutorial</h2></th></tr>
			<?php
			}
			?>
			</center>

	<hr/>

	<div class="panel panel-default">
		<div class="panel-heading">
			Below is the video that was just shown to you.  You may review this video again if you wish.  Otherwise, please click the checkbox below and press 'Continue'.
		</div>
		<div class="panel-body">
			<?php
			if($stageID<25 || ($stageID>35 && $stageID<51))
			{
				?>
				<center>
					<video id='session_video' width='90%' controls>
						<source id='mp4source' type='video/mp4' src='../tutorial/system_tutorial.mp4' >
					</video>
				</center>
				<?php
			}
			else
			{
				?>
				<center>
					<video id='session_video' width='90%' controls>
						<source id='mp4source' type='video/mp4' src='../tutorial/intent_tutorial.mp4' >
					</video>
				</center>
				<?php
			}
			?>

		</div>
	</div>


</div>
</div>
</body>
</html>
<?php

	}
	else {
		echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
	}

?>
