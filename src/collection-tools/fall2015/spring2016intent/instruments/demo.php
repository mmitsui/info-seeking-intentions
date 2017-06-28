<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	require_once('../core/Connection.class.php');

	Util::getInstance()->checkSession();

	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();

		if (isset($_POST['demo']))
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
<title>Demo</title>

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
<body class="body">
	<div style="width:90%; margin: 0 auto">
		<center><h2>Toolbar Demo</h2></center>
<form action="demo.php" method="post" onsubmit="return validate(this)">


<p>Now, you will search for new sources to add to the group’s collection.
	You will use a custom toolbar to bookmark quality sources for the project.</p>

<p>You will now be shown a demo of the Coagmento system.
	Please click 'Next' after you have been shown the demo.</p>


<center>
<table>
<tr><td align=center>
<input type="hidden" name="demo" value="true"/>
<input type="hidden" name="localTime" value=""/>
<input type="hidden" name="localDate" value=""/>
<input type="hidden" name="localTimestamp" value=""/>
<button type="submit" id="continue_button" class="pure-button pure-button-primary">Next</button></td></tr>
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
