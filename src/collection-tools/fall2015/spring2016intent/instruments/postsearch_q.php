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

	if (isset($_POST['postsearch_q']))
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
			if ($k != "postsearch_q"){
				$questionnaire->addAnswer($k,$v);
			}
		}
		$questionnaire->commitAnswersToDatabase(array("$userID","$projectID","$stageID"),array('userID','projectID','stageID'),'questionnaire_postsearch');

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
		$questionnaire->populateQuestionsFromDatabase("fall2015intent-search","questionID ASC");
		$questionnaire->setBaseDirectory("../");

		$part_text = "";
		if($stageID > 35){
			$part_text = "Part 2 - ";
		}



?>

<html>
<head>
	<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
	<link rel="stylesheet" href="../study_styles/custom/text.css">
	<link rel="stylesheet" href="../styles.css">
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

		$rules = "
				age: {
					required: true,
					number: true
				},
				search_years: {
					required: true,
					number: true
				},
				";
				// date_firstchoice_1: {
				//
				// 	notEqualTo: \"#date_secondchoice_1\"
				// },
				// date_secondchoice_1: {
				//
				// 	notEqualTo: \"#date_firstchoice_1\"
				// },

				$messages = "
						age: {
							required:\"<span style='color:red'>Please enter your age.</span>\",
							number:\"<span style='color:red'>Please enter a number.</span>\"
						},
						search_years: {
							required:\"<span style='color:red'>Please enter the years.</span>\",
							number:\"<span style='color:red'>Please enter a number.</span>\"
						},
						";

    echo $questionnaire->printValidation("sum2015_qform",$rules,$messages);
    ?>


    </script>


<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
</style>
</head>
<body class="body">
	<div class="panel panel-default" style="width:95%;  margin:auto">
		<div class="panel-body">
<div style="width:90%; margin: 0 auto">
	<center><h2><?php echo $part_text;?>Post-Search Questionnaire</h2></center>

	<p>Below is a questionnaire regarding the task you just completed.  Please fill out the requested information to the best of your ability.</p>



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

<form id="sum2015_qform" class="pure-form" method="post" action="postsearch_q.php">
	<div class="pure-form-stacked">
		<fieldset>
<?php
// Likert
$questionnaire->printQuestions();
?>
</fieldset>
</div>

<hr>

<input type="hidden" name="postsearch_q" value="true"/>
  <button class="btn btn-primary" type="submit">Submit</button>
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
