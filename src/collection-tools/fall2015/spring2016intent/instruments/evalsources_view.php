<?php


session_start();
require_once('../core/Base.class.php');
require_once('../core/Util.class.php');
require_once('../core/Connection.class.php');
require_once('../core/Questionnaires.class.php');

Util::getInstance()->checkSession();

$base = Base::getInstance();

if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
{
	$collaborativeStudy = Base::getInstance()->getStudyID();

	$userID = $base->getUserID();
	$connection = Connection::getInstance();
	$res = $connection->commit("SELECT `group` FROM users WHERE userID='$userID'");
	$line = mysql_fetch_array($res,MYSQL_ASSOC);
	$group = $line['group'];

	if($group=='treatment'){
		Util::getInstance()->moveToNextStage();
	}
	else if (isset($_POST['evalsources']))
	{

		$base = Base::getInstance();
		$stageID = $base->getStageID();

		Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
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
	<title>
		Research Study
	</title>
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/grids-min.css">
<script type='text/javascript' src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type='text/javascript' src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
<link href="https://s3.amazonaws.com/mturk-public/bs30/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="../study_styles/custom/text.css" />
<link rel="stylesheet" href="../study_styles/custom/background.css" />

</head>
<body>
	<div style="width:90%; margin: 0 auto">
		<center><h2>Evaluate Sources</h2></center>
<p>
	Below are some online information sources that members of your group have already
	bookmarked for your IT Market Sector Analysis Project on Gaming.
	Click on the title of the source (in blue) to see it online.
	It will open in a new tab.
</p>
<p>
	You can review these sources before you start searching.
</p>
<hr>
<form id="sum2015_qform" class="pure-form" method="post" action="evalsources_view.php">
<div id="main">




<ul>
<?php

// Print task
$bookmarks_res = $connection->commit("SELECT * FROM bookmarks_group2 WHERE projectID='2' ORDER BY RAND()");


while($line = mysql_fetch_array($bookmarks_res,MYSQL_ASSOC)){
	$bookmarkID = $line['bookmarkID'];
	$url = $line['url'];
	$title = $line['title'];
	echo "<li><a href=\"$url\" target=\"_blank\">$title</a></li>";
}
?>
</ul>


</div>

<hr />
<style type="text/css">fieldset { padding: 10px; background:#fbfbfb; border-radius:5px; margin-bottom:5px; }
</style>
<br/><br/>
<input type="hidden" name="evalsources" value="true"/>
  <button id="submitButton" class="pure-button pure-button-primary" type="submit">Next</button>
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
