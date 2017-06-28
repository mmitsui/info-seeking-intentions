<?php
session_start();
require_once('../core/Connection.class.php');
require_once('../core/Questionnaires.class.php');
require_once('../core/Base.class.php');


$base = Base::getInstance();
$questionnaire = Questionnaires::getInstance();
$questionnaire->setBaseDirectory('../');
if(!$base->isUserActive()){
  header("Location: ../login.php?redirect=workspace/index.php");
}

$userID = $base->getUserID();
$projectID = $base->getProjectID();

// 1.  Did date pass?
// 2.  Was first questionnaire complete?
// $second_session_start = strtotime("1 Nov 2014 00:00");
// $second_session_end = strtotime("1 Dec 2014 23:59");

$override = 0;
if($userID >= 3000 && $userID <=3010){
  $override = 1;
}



$cxn = Connection::getInstance();
$r = $cxn->commit("SELECT I.questionnaire2start as questionnaire2start,I.questionnaire2end as questionnaire2end FROM recruits R,instructors I WHERE R.userID='$userID' AND R.instructorID=I.instructorID");
$line = mysql_fetch_array($r,MYSQL_ASSOC);
if($questionnaire->isQuestionnaireComplete('spring2015-midtask-second',array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_second')){
  ?>
  <html>
	<head>
	  <link rel="stylesheet" href="../study_styles/custom/text.css">
		<title>
	    	Collaborative Search Study: Second Questionnaire
	    </title>
	    <link rel="stylesheet" type="text/css" href="../styles.css" />
	<style type="text/css">
			.cursorType{
			cursor:pointer;
			cursor:hand;
			}
	</style>
	</head>
	<body class="style1">
		<center>
		Thank you for completing this questionnaire!  You may now close this window.
		</center>
	</body>
	</html>
  <?php
  exit();
}else if(!$override && strtotime($line['questionnaire2start']) - time() >=0){
?>
  <html>
  <head>
    <link rel="stylesheet" href="../study_styles/custom/text.css">
  	<title>
      	Collaborative Search Study: Questionnaire #2
      </title>
      <link rel="stylesheet" type="text/css" href="../styles.css" />

      <style>
      select {
        font-size:13px;
      }
      </style>
  <style type="text/css">
  		.cursorType{
  		cursor:pointer;
  		cursor:hand;
  		}
  </style>
  </head>
  <body>
  <p>You are not able to complete this questionnaire yet.  The start date for this questionnaire is <?php echo $line['questionnaire2start'];?>.</p>
  <p>To go back to your workspace, please click <a href="../workspace/index.php">here</a>.</p>
  </body>
  </html>
  <?php
  exit();
}else if(!$override && time() - strtotime($line['questionnaire2end']) >= 0){
  ?>
  <html>
  <head>
    <link rel="stylesheet" href="../study_styles/custom/text.css">
  	<title>
      	Collaborative Search Study: Questionnaire #2
      </title>
      <link rel="stylesheet" type="text/css" href="../styles.css" />

      <style>
      select {
        font-size:13px;
      }
      </style>
  <style type="text/css">
  		.cursorType{
  		cursor:pointer;
  		cursor:hand;
  		}
  </style>
  </head>
  <body>
    <p>We apologize but the time limit to complete this questionnaire has passed.</p>
    <p>To go back to your workspace, please click <a href="../workspace/index.php">here</a>.</p>
  </body>
  </html>
  <?php
  exit();
}else if(!$override && !$questionnaire->isQuestionnaireComplete('spring2015-midtask-first',array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_first')){
  ?>
  <html>
  <head>
    <link rel="stylesheet" href="../study_styles/custom/text.css">
  	<title>
      	Collaborative Search Study: Questionnaire #2
      </title>
      <link rel="stylesheet" type="text/css" href="../styles.css" />

      <style>
      select {
        font-size:13px;
      }
      </style>
  <style type="text/css">
  		.cursorType{
  		cursor:pointer;
  		cursor:hand;
  		}
  </style>
  </head>
  <body>
    <p>To complete this questionnaire, you must complete the first questionnaire.</p>
    <p>Click <a href="../instruments/questionnaire_first.php">here</a> to complete the first questionnaire.</p>
  </body>
  </html>
  <?php
  exit();
}


if (!isset($_POST['questionnaire_second'])){
	// Not complete, no results submitted
	// print questionnaire
	$questionnaire->clearCache();
	$questionnaire->populateQuestionsFromDatabase("spring2015-midtask-second","questionID ASC");

?>

<html>
<head>
  <link rel="stylesheet" href="../study_styles/custom/text.css">
	<title>
    	Collaborative Search Study: Questionnaire #1
    </title>
    <link rel="stylesheet" type="text/css" href="../styles.css" />

    <style>
    select {
      font-size:13px;
    }
    </style>
    <?php echo $questionnaire->printPreamble();?>
    <script>
    <?php
    	echo $questionnaire->printValidation("spr2015_q_second");
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

	<h3>Collaborative Search Study: Questionnaire #2</h3>


<form id="spr2015_q_second" class="pure-form" method="post" action="questionnaire_second.php">
  <center>
    <table class="style1" width=90%>
    <tr><td>
  <?php

  echo "<div class=\"pure-form-stacked\">";

  echo "<hr>";
  $questionnaire->printQuestions();
  echo "<hr>";
  echo "</div>";



  ?>
				<input type="hidden" id="questionnaire_second" name="questionnaire_second"/>
        	<button class="pure-button pure-button-primary" type="submit">Submit</button>
        </td></tr></table></center>
    </form>


</body>
<?php $questionnaire->printPostamble();?>
</html>



<?php



}else if (isset($_POST['questionnaire_second'])){
	// Results submitted; commit and reload page
	foreach($_POST as $k=>$v){
		if($k != 'questionnaire_second'){
			$questionnaire->addAnswer($k,$v);
		}
	}
	$questionnaire->commitAnswersToDatabase(array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_second');
	$questionnaire->clearCache();
	header("Location: questionnaire_second.php");
}
