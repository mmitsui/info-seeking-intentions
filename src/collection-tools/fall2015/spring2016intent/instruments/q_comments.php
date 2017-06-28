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

	if (isset($_POST['q_comments']))
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
			if ($k != "q_comments"){
				$questionnaire->addAnswer($k,$v);
			}
		}
		$questionnaire->commitAnswersToDatabase(array("$userID","$projectID","$stageID"),array('userID','projectID','stageID'),'questionnaire_postcomments');

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
		$questionnaire->populateQuestionsFromDatabase("fall2015intent-postcomments","questionID ASC");
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
		$rules = "
				difficult_find_task1: {
					required: true
				},
				understand_extent_task1: {
					required: true
				},
				difficult_find_task2: {
					required: true
				},
				understand_extent_task2: {
					required: true
				},
				diff_info_explain:{
					required:{
						depends: function(element) {
								return ($('input[name=\"diff_info\"]:checked').val() == 'Yes');
						}
					}
				},
				";

		$messages = "
				difficult_find_task1: {
					required:\"<span style='color:red'>Please enter your response.</span>\"
				},
				understand_extent_task1: {
					required:\"<span style='color:red'>Please enter your response.</span>\"
				},
				difficult_find_task2: {
					required:\"<span style='color:red'>Please enter your response.</span>\"
				},
				understand_extent_task2: {
					required:\"<span style='color:red'>Please enter your response.</span>\"
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
	<center><h2>Comments</h2></center>

	<p>Below is a questionnaire regarding the task you just completed.  Please fill out the requested information to the best of your ability.</p>



		<!-- <div class="grayrect">
			<span> -->
				<?php

				$base = Base::getInstance();
				$userID = $base->getUserID();
				$connection = Connection::getInstance();

				$query = "SELECT userID,topicAreaID1,topicAreaID2 FROM users WHERE userID='$userID'";
				$results = $connection->commit($query);
				$line = mysql_fetch_array($results,MYSQL_ASSOC);
				$topicAreaID1 = $line['topicAreaID1'];
				$topicAreaID2 = $line['topicAreaID2'];







				$query = "SELECT Q.title as title FROM questions_study Q WHERE Q.questionID=$topicAreaID1";
				$results = $connection->commit($query);
				$question1 = '';
				$line = mysql_fetch_array($results,MYSQL_ASSOC);
				$topicArea1title=$line['title'];

				$query = "SELECT Q.title as title FROM questions_study Q WHERE Q.questionID=$topicAreaID2+4";
				$results = $connection->commit($query);
				$question1 = '';
				$line = mysql_fetch_array($results,MYSQL_ASSOC);
				$topicArea2title=$line['title'];


				?>
			<!-- </span>
		</div> -->


<br/>

<form id="sum2015_qform" class="pure-form" method="post" action="q_comments.php">
	<div class="pure-form-stacked">
		<fieldset>
<?php
// Likert
$questionnaire->printQuestions(0,1);


?>


<!--  First likert -->
<div >


<label>How difficult was it to find the information for the two assignments?</label>
<div id="difficult_find_task1_div" class="container">
	<strong>Task 1: <?php echo $topicArea1title; ?></strong>
<div class="pure-g">
<div class="pure-u-1-8"><label for="difficult_find_task1_1" class="pure-radio"><input id="difficult_find_task1_1" type="radio" name="difficult_find_task1" value="1">1 (Not at all)</label></div>
<div  style="background-color:#F2F2F2" class="pure-u-1-8"><label for="difficult_find_task1_2" class="pure-radio"><input id="difficult_find_task1_2" type="radio" name="difficult_find_task1" value="2">2</label></div>
<div  class="pure-u-1-8"><label for="difficult_find_task1_3" class="pure-radio"><input id="difficult_find_task1_3" type="radio" name="difficult_find_task1" value="3">3</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="difficult_find_task1_4" class="pure-radio"><input id="difficult_find_task1_4" type="radio" name="difficult_find_task1" value="4">4 (Somewhat)</label></div>
<div  class="pure-u-1-8"><label for="difficult_find_task1_5" class="pure-radio"><input id="difficult_find_task1_5" type="radio" name="difficult_find_task1" value="5">5</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="difficult_find_task1_6" class="pure-radio"><input id="difficult_find_task1_6" type="radio" name="difficult_find_task1" value="6">6</label></div>
<div  class="pure-u-1-8"><label for="difficult_find_task1_7" class="pure-radio"><input id="difficult_find_task1_7" type="radio" name="difficult_find_task1" value="7">7 (Extremely)</label></div>
</div>
</div>
<br/>
<div id="difficult_find_task2_div" class="container">
	<strong>Task 2: <?php echo $topicArea2title; ?></strong>
<div class="pure-g">
<div  class="pure-u-1-8"><label for="difficult_find_task2_1" class="pure-radio"><input id="difficult_find_task2_1" type="radio" name="difficult_find_task2" value="1">1 (Not at all)</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="difficult_find_task2_2" class="pure-radio"><input id="difficult_find_task2_2" type="radio" name="difficult_find_task2" value="2">2</label></div>
<div  class="pure-u-1-8"><label for="difficult_find_task2_3" class="pure-radio"><input id="difficult_find_task2_3" type="radio" name="difficult_find_task2" value="3">3</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="difficult_find_task2_4" class="pure-radio"><input id="difficult_find_task2_4" type="radio" name="difficult_find_task2" value="4">4 (Somewhat)</label></div>
<div  class="pure-u-1-8"><label for="difficult_find_task2_5" class="pure-radio"><input id="difficult_find_task2_5" type="radio" name="difficult_find_task2" value="5">5</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="difficult_find_task2_6" class="pure-radio"><input id="difficult_find_task2_6" type="radio" name="difficult_find_task2" value="6">6</label></div>
<div class="pure-u-1-8"><label for="difficult_find_task2_7" class="pure-radio"><input id="difficult_find_task2_7" type="radio" name="difficult_find_task2" value="7">7 (Extremely)</label></div>
</div>
</div>



</div>
<br/><br/>


<?php
// Likert
$questionnaire->printQuestions(2,2);


?>


<!--  Second likert-->
<div >


<label>To what extent were you able to understand the intention annotation procedure?</label>
<div id="understand_extent_task1_div" class="container">
	<strong>Task 1: <?php echo $topicArea1title; ?></strong>
<div class="pure-g">
<div class="pure-u-1-8"><label for="understand_extent_task1_1" class="pure-radio"><input id="understand_extent_task1_1" type="radio" name="understand_extent_task1" value="1">1 (Not at all)</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="understand_extent_task1_2" class="pure-radio"><input id="understand_extent_task1_2" type="radio" name="understand_extent_task1" value="2">2</label></div>
<div  class="pure-u-1-8"><label for="understand_extent_task1_3" class="pure-radio"><input id="understand_extent_task1_3" type="radio" name="understand_extent_task1" value="3">3</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="understand_extent_task1_4" class="pure-radio"><input id="understand_extent_task1_4" type="radio" name="understand_extent_task1" value="4">4 (Somewhat)</label></div>
<div  class="pure-u-1-8"><label for="understand_extent_task1_5" class="pure-radio"><input id="understand_extent_task1_5" type="radio" name="understand_extent_task1" value="5">5</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="understand_extent_task1_6" class="pure-radio"><input id="understand_extent_task1_6" type="radio" name="understand_extent_task1" value="6">6</label></div>
<div class="pure-u-1-8"><label for="understand_extent_task1_7" class="pure-radio"><input id="understand_extent_task1_7" type="radio" name="understand_extent_task1" value="7">7 (Extremely)</label></div>
</div>
</div>
<br/>
<div id="understand_extent_task2_div" class="container">
	<strong>Task 2: <?php echo $topicArea2title; ?></strong>
<div class="pure-g">

<div  class="pure-u-1-8"><label for="understand_extent_task2_1" class="pure-radio"><input id="understand_extent_task2_1" type="radio" name="understand_extent_task2" value="1">1 (Not at all)</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="understand_extent_task2_2" class="pure-radio"><input id="understand_extent_task2_2" type="radio" name="understand_extent_task2" value="2">2</label></div>
<div  class="pure-u-1-8"><label for="understand_extent_task2_3" class="pure-radio"><input id="understand_extent_task2_3" type="radio" name="understand_extent_task2" value="3">3</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="understand_extent_task2_4" class="pure-radio"><input id="understand_extent_task2_4" type="radio" name="understand_extent_task2" value="4">4 (Somewhat)</label></div>
<div  class="pure-u-1-8"><label for="understand_extent_task2_5" class="pure-radio"><input id="understand_extent_task2_5" type="radio" name="understand_extent_task2" value="5">5</label></div>
<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="understand_extent_task2_6" class="pure-radio"><input id="understand_extent_task2_6" type="radio" name="understand_extent_task2" value="6">6</label></div>
<div class="pure-u-1-8"><label for="understand_extent_task2_7" class="pure-radio"><input id="understand_extent_task2_7" type="radio" name="difficult_find" value="7">7 (Extremely)</label></div>
</div>
</div>



</div>
<br/><br/>

<?php
// Likert
$questionnaire->printQuestions(3);


?>


</fieldset>
</div>

<hr>

<input type="hidden" name="q_comments" value="true"/>
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
