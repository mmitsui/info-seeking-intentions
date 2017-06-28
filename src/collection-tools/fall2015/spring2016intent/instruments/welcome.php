<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');

	Util::getInstance()->checkSession();

	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();

		if (isset($_POST['welcome']))
		{
			$base = new Base();
			$stageID = $base->getStageID();
            $localTime = $_POST['localTime'];
            $localDate = $_POST['localDate'];
            $localTimestamp = $_POST['localTimestamp'];

            Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$stageID,$base,$localTime,$localDate,$localTimestamp);
			Util::getInstance()->moveToNextStage();
		}
		else
		{
?>
<html>
<head>
<title>Overview
</title>

</head>
<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">
<link rel="stylesheet" href="../styles.css">


<script type="text/javascript">
	function validate(form)
	{
		if (!form.confirmReadInstructions.checked)
		{
			document.getElementById("alert").style.display = "block";
			return false;
		}
		else{
            var currentTime = new Date();
            var month = currentTime.getMonth() + 1;
            var day = currentTime.getDate();
            var year = currentTime.getFullYear();
            var localDate = year + "/" + month + "/" + day;
            var hours = currentTime.getHours();
            var minutes = currentTime.getMinutes();
            var seconds = currentTime.getSeconds();
            var localTime = hours + ":" + minutes + ":" + seconds;
            var localTimestamp = currentTime.getTime();

            document.getElementById("localTimestamp").value = localTimestamp;
            document.getElementById("localDate").value = localDate;
            document.getElementById("localTime").value = localTime;
			return true;
        }
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
<center>
	<br/>
	<form class="pure-form" action="welcome.php" method="post" onsubmit="return validate(this)">
		<center><h2>Research Study: Overview</h2></center>
		<table class="body" width="90%">
		<tr><th></th></tr>
		<tr><td><hr/></td></tr>
		<tr>
			<td>
				<?php
				if(Base::getInstance()->getStageID()<120)
				{
				?>
				Thank you for signing up for this study. We appreciate your participation!
				<ul>
				<li>You will fill out a background questionnaire.</li>
				<li>Then, you will be asked to complete a search task that we give you.  You will receive instructions for this.</li>
				<li>Before each task, you will be asked to indicate your knowledge of the task topics.</li>
				<li>You have 20 minutes to complete the task.</li>
				<li>As part of the search task, you will be asked to rate the usefulness of the web pages you found and to indicate why they were useful.  Your evaluation will be recorded for later analysis.</li>
				<li>You will also observe recorded video of your previous session, labeling your intentions at each point of your search.</li>
				<li>You will do all of this for a total of 2 tasks.</li>
				<!-- <li>Do your best!  Compensation is $40.</li> -->
				<li>Do your best!  Compensation is $30.  We are also giving an additional $10 compensation to the best performers!</li>
				</ul>
				<?php
				}
				else
				{
				?>
				<ul>
				<li>Thank you for signing up for this study. We appreciate your participation!</li>
				<li>First, questionnaire.</li>
				<li>Second, another questionnaire</li>
				<li>Then you will have <strong>X minutes</strong> to SEARCH (DESCRIBE TASK MORE HERE).</li>
				<li>Questionnaire</li>
				<li>Annotation.</li>
				<li>Do your best!  Compensation is $40.</li>
				</ul>
				<?php
				}
				?>
				<center>
					<p>
						<strong>If you have read these instructions, please check the option below and press "Continue." Have fun!</strong>
					</p>
				</center>
			</td>
		</tr>
		<tr><td><hr/></td></tr>
		<tr><td><div class="alert alert-danger" style="display:none" id="alert"><strong>Before you continue, you must read all the above instructions. Once you have read them, click on the box below and then continue.</strong></div></td></tr>
		<tr><td><div class="alert alert-success" style="display:none" id="complete"><strong>Good! Click on Continue</strong></div></td></tr>
		<tr><td align=center><input type="checkbox" name="confirmReadInstructions" value="true" onclick="complete(this)"/>I have read all the above instructions</td></tr>
		<tr><td><br/><input type="hidden" id="localTimestamp" name="localTimestamp" value=""/><input type="hidden" id="localTime" name="localTime" value=""/><input type="hidden" id="localDate" name="localDate" value=""/></td></tr>
		<tr><td align=center><input type="hidden" name="welcome" value="true"/><button type="submit" class="btn btn-primary" >Continue</button></td></tr>
	  </table>
	</form>
<br/>
</center>
</div></div>
</body>
</html>
<?php
		}
	}
	else {
		echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
	}

?>
