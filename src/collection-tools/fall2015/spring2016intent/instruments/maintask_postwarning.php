<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	require_once('../core/Connection.class.php');

	Util::getInstance()->checkSession();

	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();

		if (isset($_POST['maintask_postwarning']))
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

			Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$stageID,$base,$localTime,$localDate,$localTimestamp);
			Util::getInstance()->moveToNextStage();
		}

		else
		{

			$base = new Base();
			$userID = $base->getUserID();
			$stageID = $base->getStageID();
			$projectID = $base->getProjectID();

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
	if (!form.confirmReadInstructions.checked)
	{
		document.getElementById("alert").style.display = "block";
		return false;
	}
	return true;
}

function complete(check)
{
	if (check.checked)
	{
		document.getElementById("complete").style.display = "block";
		document.getElementById("alert").style.display = "none";
	}
	else
	{
		document.getElementById("complete").style.display = "none";
		document.getElementById("alert").style.display = "block";
	}
}


</script>
<body class="body">
	<div class="panel panel-default" style="width:95%;  margin:auto">
		<div class="panel-body">
	<div style="width:90%; margin: 0 auto">
		<center><h2><?php echo $part_text;?>Search Task Complete!</h2></center>
<form action="maintask_postwarning.php" method="post" onsubmit="return validate(this)">



	<div class="alert alert-danger">
		<strong>WARNING! A popup window will soon appear on your screen.  It is saving some data.  DO NOT CANCEL THIS PROCESS OR CLOSE THE POPUP WINDOW!</strong>
	</div>



<center>
<table>
<tr><td align=center>
<input type="hidden" name="maintask_postwarning" value="true"/>
<input type="hidden" name="localTime" value=""/>
<input type="hidden" name="localDate" value=""/>
<input type="hidden" name="localTimestamp" value=""/>

		<p><input type="checkbox" name="confirmReadInstructions" value="true" onclick="complete(this)"/>I have understood the above request.</p>
		<button type='submit' id='continue_button' class='btn btn-primary'>Next</button>
		<p><div class="alert alert-danger" style="display:none" id="alert"><strong>Before you continue, you must confirm you have understood the above statement.  Once you have read and understood it, click on the box below and then continue.</strong></div>
		<div class="alert alert-success" style="display:none" id="complete"><strong>Good! Click on Next</strong></div></p>
	</td></tr>

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
