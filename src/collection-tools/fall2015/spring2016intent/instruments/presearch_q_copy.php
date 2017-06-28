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
			echo "K:$k\n";
			echo "V:$v\n";
			if ($k != "presearch_q"){
				echo "ADD: $v";
				$questionnaire->addAnswer($k,$v);
			}
		}
		$questionnaire->commitAnswersToDatabase(array("$userID","$projectID","$stageID"),array('userID','projectID','stageID'),'questionnaire_checkbox_test');

		Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
		Util::getInstance()->moveToNextStage();
	}
	else
	{
		$base = new Base();
		$userID = $base->getUserID();
		$stageID = $base->getStageID();
		$projectID = $base->getProjectID();

		$questionnaire = Questionnaires::getInstance();
		$questionnaire->clearCache();
		$questionnaire->populateQuestionsFromDatabase("fall2015-checkbox-test","questionID ASC");
		$questionnaire->setBaseDirectory("../");




?>

<html>
<head>
	<link rel="stylesheet" href="../study_styles/custom/text.css">
	<link rel="stylesheet" href="../study_styles/custom/background.css">
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
<br/>
<div style="width:90%; margin: 0 auto">
	<center><h2>Pre-Task Questionnaire</h2></center>

	<p>Complete the survey!</p>



		<!-- <div class="grayrect">
			<span> -->
				<?php

				// $base = Base::getInstance();
				// $userID = $base->getUserID();
				// $connection = Connection::getInstance();
				// $query = "SELECT userID, topicAreaID
				// 			FROM users
				// 			WHERE userID='$userID'";
				// $results = $connection->commit($query);
				// $line = mysql_fetch_array($results,MYSQL_ASSOC);
				// $topicAreaID = $line['topicAreaID'];
				//
				//
				// $query = "SELECT Q.question as question FROM questions_study Q WHERE Q.questionID=$topicAreaID+1";
				// $results = $connection->commit($query);
				// $question1 = '';
				// $line = mysql_fetch_array($results,MYSQL_ASSOC);
				// $question1 = $line['question'];
				//
				//
				// echo $question1;


				?>
			<!-- </span>
		</div> -->


<br/>

<form id="sum2015_qform" class="pure-form" method="post" action="presearch_q.php">
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
  <button class="pure-button pure-button-primary" type="submit">Submit</button>
</form>
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
