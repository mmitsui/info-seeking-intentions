<?php


session_start();
require_once('../core/Base.class.php');
require_once('../core/Util.class.php');
require_once('../core/Connection.class.php');
require_once('../core/Questionnaires.class.php');


Util::getInstance()->checkSession();

if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
{
	$collaborativeStudy = Base::getInstance()->getStudyID();

	if (isset($_POST['presearch_q']))
	{
		$base = new Base();
		$stageID = $base->getStageID();

		$userID=$base->getUserID();
		$projectID=$base->getProjectID();


		/*

		SUBMIT ANSWER!


		*/

		$questionnaire = Questionnaires::getInstance();
		foreach($_POST as $k=>$v){
			if ($k != "pretask_q"){
				$questionnaire->addAnswer($k,$v);
			}
		}
		$questionnaire->commitAnswersToDatabase(array("$userID","$projectID","$stageID"),array('userID','projectID','stageID'),'questionnaire_pretask');

		Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
		Util::getInstance()->moveToNextStage();
	}
	else
	{
		$base = new Base();
		$userID = $base->getUserID();
		$stageID = $base->getStageID();
		$projectID = $base->getProjectID();

		$part_text = "";
		if($stageID > 35){
			$part_text = "Part 2 - ";
		}

		$questionnaire = Questionnaires::getInstance();
		$questionnaire->clearCache();
		$questionnaire->populateQuestionsFromDatabase("fall2015intent-pretask","questionID ASC");
		$questionnaire->setBaseDirectory("../");




?>

<html>
<head>
	<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
	<link rel="stylesheet" href="../study_styles/custom/text.css">
	<link rel="stylesheet" href="../styles.css">
	<script type="text/javascript" src="../sidebar/js/utilities.js"></script>
	<title>
		Research Study
    </title>


    <style>
    select {
      font-size:13px;
    }
    </style>
    <?php echo $questionnaire->printPreamble();?>

    <script type="text/javascript">
    <?php
    echo $questionnaire->printValidation("sum2015_qform");


    ?>


    </script>


<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
</style>
</head>
<body class="style1">
	<div class="panel panel-default" style="width:95%;  margin:auto">
		<div class="panel-body">
<div style="width:90%; margin: 0 auto">
	<center><h2><?php echo $part_text;?>Topic Questionnaire</h2></center>

	<p>You will be asked to conduct searching around the following topic.  Please read the topic description and answer the questions below.</p>



		 <div class="well" style="background-color:rgb(210,210,210);">
			<span>
				<?php

				$base = Base::getInstance();
				$userID = $base->getUserID();
				$connection = Connection::getInstance();

				$topicAreaID = $base->getTopicAreaID();

				$base->populateQuestionID();
				$questionID = $base->getQuestionID();
				$question1 = $base->getQuestion();

				echo $question1;


				?>
			 </span>
		</div>

<br/>

<form id="sum2015_qform" class="pure-form" method="post" action="presearch_q.php" onsubmit="addAction('presearch-formsubmit',0)">
	<div class="pure-form-stacked">
		<fieldset>
<?php
// Likert
$questionnaire->printQuestions();
?>
</fieldset>
</div>

<hr>

<input type="hidden" name="presearch_q" value="true"/>
  <button class="btn btn-primary" type="submit" onmousedown="addAction('presearch-mousedownsubmit',0);return false;" onclick="addAction('presearch-submit',0);return true;">Submit</button>
</form>
</div>
</div>
</div>
</body>
<?php $questionnaire->printPostamble();?>
</html>


<?php
	}
}
else {
	echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
}

	?>
