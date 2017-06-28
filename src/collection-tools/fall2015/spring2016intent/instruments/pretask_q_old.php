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

	if (isset($_POST['pretask_q']))
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
		$age = $_POST['age'];
		$search_years = $_POST['search_years'];
		$questionnaire->commitAnswersToDatabase(array("$userID","$projectID","$stageID","$age","$search_years"),array('userID','projectID','stageID',"age","search_years"),'questionnaire_demographic');

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
		$questionnaire->populateQuestionsFromDatabase("fall2015intent-demog","questionID ASC");
		$questionnaire->setBaseDirectory("../");




?>

<html>
<head>
	<link rel="stylesheet" href="../study_styles/custom/text.css">
	<link rel="stylesheet" href="../study_styles/custom/background.css">
	<title>
		Research Study
    </title>


    <style  type="text/css">
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
<body class="style1">
<br/>
<div style="width:90%; margin: 0 auto">
	<center><h2>Background Questionnaire</h2></center>

	<p>Below are some questions regarding how you typically conduct searching on the internet.  Please complete them to the best of your ability.</p>
	<hr/>

<br/>

<form id="sum2015_qform" class="pure-form" method="post" action="pretask_q.php">
	<div class="pure-form-stacked">
		<fieldset>
<?php




$questionnaire->printQuestions(0,0);


echo "<div class=\"pure-control-group\">";
echo "<label for=\"age\">Age (Years)</label>";
echo "<input id=\"age\" name=\"age\" type=\"text\" placeholder=\"Age\" required>";
echo "</div><br/>";


echo "<div class=\"pure-control-group\">";
echo "<label for=\"search_years\">How many years have you been doing online searching?</label>";
echo "<input id=\"search_years\" name=\"search_years\" type=\"text\" placeholder=\"Years\" required>";
echo "</div><br/>";

$questionnaire->printQuestions(1);

?>
</fieldset>
</div>

<hr/>

<input type="hidden" name="pretask_q" value="true"/>
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
