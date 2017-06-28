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

// $override = 0;
// if($userID >= 3000 && $userID <=3010){
//   $override = 1;
// }
$override = 1;


$cxn = Connection::getInstance();
$r = $cxn->commit("SELECT I.questionnaire3start as questionnaire3start,I.questionnaire3end as questionnaire3end FROM recruits R,instructors I WHERE R.userID='$userID' AND R.instructorID=I.instructorID");
$line = mysql_fetch_array($r,MYSQL_ASSOC);
if($questionnaire->isQuestionnaireComplete('spring2015-midtask-third',array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_third') &&
$questionnaire->isQuestionnaireComplete('spring2015-midtask-third-parttwo',array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_third_parttwo') &&
!isset($_POST['questionnaire_third']) &&
!isset($_POST['questionnaire_third_parttwo']) ){
// if($questionnaire-> (!isset($_POST['questionnaire_third']) && !isset($_POST['questionnaire_third_parttwo']) ){

?>

  <html>
	<head>
	  <link rel="stylesheet" href="../study_styles/custom/text.css">
		<title>
	    	Collaborative Search Study: Third Questionnaire
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
		<p>Thank you for completing this questionnaire!</p>
    <p>To remove Coagmento from the Firefox browser, click on Tools and Select Add-ons from the menu. Next to "Coagmento Spring 2015" click Remove.</p>
    <p>You may now close this window.</p>
		</center>
	</body>
	</html>
  <?php
  exit();
// }else if(!$override && strtotime($line['questionnaire3start']) - time() >=0){
}else if(!$override && strtotime($line['questionnaire3start']) - time() >=0){
?>
<html>
  <head>
    <link rel="stylesheet" href="../study_styles/custom/text.css">
  	<title>
      	Collaborative Search Study: Questionnaire #3
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
  <p>You are not able to complete this questionnaire yet.  The start data for this questionnaire is <?php echo $line['questionnaire3start'];?>.</p>
  <p>To go back to your workspace, please click <a href="../workspace/index.php">here</a>.</p>
  </body>
  </html>

 <?php
 exit();
}else if(!$override && time() - strtotime($line['questionnaire3end']) >= 0){
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
}else if(!$override){

  $notcompletestr = "";
  $notcompleteurls = "Click ";
  if(!$questionnaire->isQuestionnaireComplete('spring2015-midtask-first',array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_first')){
    $notcompletestr = "first";
    $notcompleteurls .="<a href=\"../instruments/questionnaire_first.php\">here</a> to complete the first questionnaire";
  }
  if(!$questionnaire->isQuestionnaireComplete('spring2015-midtask-second',array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_second')){
    if($notcompletestr!=""){
      $notcompletestr.=" and";
      $notcompleteurls.=" and";
    }
    $notcompletestr.=" second";
    $notcompleteurls .=" <a href=\"../instruments/questionnaire_second.php\">here</a> to complete the second questionnaire";
  }
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
    <p>To complete this questionnaire, you must complete the <?php echo $notcompletestr; ?> questionnaire.</p>
    <p><?php echo $notcompleteurls; ?>.</p>
  </body>
  </html>
  <?php
  exit();
}



if(!$questionnaire->isQuestionnaireComplete('spring2015-midtask-third',array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_third')
&& !isset($_POST['questionnaire_third'])){
  // Render first questionnaire
// if(!$questionnaire->!isset($_POST['questionnaire_third'])){
  $questionnaire->clearCache();
	$questionnaire->populateQuestionsFromDatabase("spring2015-midtask-third","questionID ASC");
  ?>

  <html>
  <head>
    <link rel="stylesheet" href="../study_styles/custom/text.css">
  	<title>
      	Collaborative Search Study: Questionnaire #3
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
      	echo $questionnaire->printValidation("spr2015_q_third");
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

  	<h3>Collaborative Search Study: Questionnaire #3 (1/2)</h3>


  <form id="spr2015_q_third" class="pure-form" method="post" action="questionnaire_third.php">
    <center><table class="style1" width=90%>
      <tr><td>
    <?php

    echo "<div class=\"pure-form-stacked\">";

    echo "<hr>";
    $questionnaire->printQuestions(0,2);
    echo "<hr>";
    echo "</div>";
    $questionnaire->printQuestions(3,4);
    echo "<div class=\"pure-form-stacked\">";
    echo "<hr>";
    $questionnaire->printQuestions(5);
    echo "<hr>";
    echo "</div>";


    ?>
  				<input type="hidden" id="questionnaire_third" name="questionnaire_third"/>
          	<button class="pure-button pure-button-primary" type="submit">Submit</button>
          </td></tr></table></center>
      </form>


  </body>
  <?php $questionnaire->printPostamble();?>
  </html>



  <?php

}else if(isset($_POST['questionnaire_third'])){
  //Push first results
  // header  to second questionnaire
  // Results submitted; commit and reload page
	foreach($_POST as $k=>$v){
		if($k != 'questionnaire_third'){
			$questionnaire->addAnswer($k,$v);
		}
	}
	$questionnaire->commitAnswersToDatabase(array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_third');
	$questionnaire->clearCache();
	header("Location: questionnaire_third.php");

}else if(!$questionnaire->isQuestionnaireComplete('spring2015-midtask-third-parttwo',array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_third_parttwo') && !isset($_POST['questionnaire_third_parttwo'])){
  // Part two questionnaire

  $questionnaire->clearCache();
	$questionnaire->populateQuestionsFromDatabase("spring2015-midtask-third-parttwo","questionID ASC");


  ?>

  <html>
  <head>
    <link rel="stylesheet" href="../study_styles/custom/text.css">
  	<title>
      	Collaborative Search Study: Questionnaire #3
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
      	echo $questionnaire->printValidation("spr2015_q_third_parttwo");
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

  	<h3>Collaborative Search Study: Questionnaire #3 (2/2)</h3>


  <form id="spr2015_q_third_parttwo" class="pure-form" method="post" action="questionnaire_third.php">
    <center><table class="style1" width=90%>
      <tr><td>
    <?php

    echo "<div class=\"pure-form-stacked\">";
    echo "<span>At the beginning of this study, you answered the same questions. Here are your initial responses and the responses you just entered:</span>";
    echo "<table border=1><center>";

    $cxn = Connection::getInstance();

    $resultsdescription = $cxn->commit("SELECT * FROM questionnaire_questions WHERE question_cat='spring2015-midtask-third' AND question_type='likert' ORDER BY questionID ASC");
    $descriptions = array();
    $count = 1;
    while($line = mysql_fetch_array($resultsdescription,MYSQL_ASSOC)){
      array_push($descriptions,array($line['key'],substr($line['question'],strlen((string)$count)+2)));
      $count += 1;
    }

    $resultsbefore = $cxn->commit("SELECT * FROM questionnaire_recruitment WHERE userID='$userID'");
    $beforeanswers = array();
    while($line = mysql_fetch_array($resultsbefore,MYSQL_ASSOC)){
      foreach($line as $k=>$v){
        $beforeanswers[$k]=$v;
      }
    }

    $resultsafter = $cxn->commit("SELECT * FROM questionnaire_midtask_third WHERE userID='$userID' AND projectID='$projectID'");
    $afteranswers = array();
    while($line = mysql_fetch_array($resultsafter,MYSQL_ASSOC)){
      foreach($line as $k=>$v){
        $afteranswers[$k]=$v;
      }
    }


    echo "<tr><td style=\"width:450px\"><strong>Question</strong></td><td style=\"background-color:#F2F2F2;width:180px\"><strong>Before Study</strong></td><td style=\"width:180px\"><strong>Now</strong></td></tr>";
    foreach($descriptions as $val){
      $key = $val[0];
      $question = $val[1];
      $bef = $beforeanswers[$key];
      $aft = $afteranswers[$key];
      $styletext = "";
      if(strcmp($bef,$aft)!==0){
        $styletext = "style=\"background-color:#FFF000\"";
      }

      echo "<tr><td style=\"width:450px\">$question</td><td style=\"background-color:#F2F2F2;width:180px\">$bef</td><td style=\"width:180px\"><span $styletext>$aft</span></td></tr>";
    }
    echo "</center></table>";
    echo "<hr>";
    $questionnaire->printQuestions();
    echo "<hr>";
    echo "</div>";


    ?>
  				<input type="hidden" id="questionnaire_third_parttwo" name="questionnaire_third_parttwo"/>
          	<button class="pure-button pure-button-primary" type="submit">Submit</button>
          </td></tr></table></center>
      </form>


  </body>
  <?php $questionnaire->printPostamble();?>
  </html>



  <?php


}else if(isset($_POST['questionnaire_third_parttwo'])){
  // Push second questionnaire results
  // Results submitted; commit and reload page
	foreach($_POST as $k=>$v){
		if($k != 'questionnaire_third_parttwo'){
			$questionnaire->addAnswer($k,$v);
		}
	}
	$questionnaire->commitAnswersToDatabase(array("$userID","$projectID"),array('userID','projectID'),'questionnaire_midtask_third_parttwo');
	$questionnaire->clearCache();
	header("Location: questionnaire_third.php");

}
