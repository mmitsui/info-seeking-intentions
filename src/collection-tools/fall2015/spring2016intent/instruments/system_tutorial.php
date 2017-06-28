<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');

	Util::getInstance()->checkSession();

	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();

		if (isset($_POST['system_tutorial']))
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
<title>System Tutorial
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

	window.onload = function(){
		document.getElementById('session_video').playbackRate = 0.5;
	}

</script>
<body class="body">
	<div class="panel panel-default" style="width:95%;  margin:auto">
		<div class="panel-body">
<center>
	<br/>
	<form class="pure-form" action="system_tutorial.php" method="post" onsubmit="return validate(this)">
		<center><h2>System Tutorial</h2></center>
		<hr/>
		<div class="panel panel-default">
			<div class="panel-heading">

				<?php
				if(Base::getInstance()->getStageID()<30)
				{
					?>
					You are about to conduct our search task.  Please watch the video below and listen to the instructions carefully.
					<?php
				}
				else
				{
					?>
					Below is the video that you saw before for using our system.  You may review this video again if you wish.  Otherwise, please click the checkbox below and press 'Continue'.
					<?php
				}
				?>
			</div>
			<div class="panel-body">
				<center>
					<video id='session_video' width='90%' controls>
						<source id='mp4source' type='video/mp4' src='../tutorial/system_tutorial.mp4' >
					</video>
				</center>
			</div>
		</div>

		<center>
			<p>
				<strong>After you have watched this video, please check the option below and press "Continue." Have fun!</strong>
			</p>
		</center>
		<hr/>
		<div class="alert alert-danger" style="display:none" id="alert"><strong>Before you continue, you must watch the above video. Once you have watched and understood the video, click on the box below and then continue.</strong></div>
		<div class="alert alert-success" style="display:none" id="complete"><strong>Good! Click on Continue</strong></div>
		<p><input type="checkbox" name="confirmReadInstructions" value="true" onclick="complete(this)"/>I have watched and understood the above video</p>
		<input type="hidden" id="localTimestamp" name="localTimestamp" value=""/>
		<input type="hidden" id="localTime" name="localTime" value=""/>
		<input type="hidden" id="localDate" name="localDate" value=""/>
		<input type="hidden" name="system_tutorial" value="true"/>
		<br/>
		<center><button type="submit" class="btn btn-primary" >Continue</button></center>

	</form>
<br/>
</center>
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
