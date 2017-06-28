<?php


session_start();
require_once('../core/Base.class.php');
require_once('../core/Util.class.php');
require_once('../core/Connection.class.php');
require_once('../core/Questionnaires.class.php');


function printLikertTwo($question,$key,$data){
	$suffix = "";
	$pref = $key;
	echo "<div style=\"border:1px solid gray; border-right-width:0px;border-left-width:0px\">\n";
	echo "<label>$question</label>\n";
	echo "<div id=\"".$pref."_div$suffix\" class=\"container\">\n";
	echo "<div class=\"pure-g\">\n";
	$count = 1;
	foreach($data as $k=>$v){
		$style = "";
		if(($count)%2){
			$style = "style=\"background-color:#F2F2F2\"";
		}
		$countstr = "_$count";
		echo "<div $style class=\"pure-u-1-8\">";
		echo "<label for=\"".$pref."$suffix$countstr\" class=\"pure-radio\">";
		echo "<input id=\"".$pref."$suffix$countstr\" type=\"radio\" name=\"".$pref."$suffix\" value=\"$v\">$k";
		echo "</label>";
		echo "</div>\n";
		$count += 1;
	}
	echo "</div>\n";
	echo "</div>\n";
	echo "</div>\n\n";
}


Util::getInstance()->checkSession();

if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
{
	$collaborativeStudy = Base::getInstance()->getStudyID();

	if (isset($_POST['q_task']))
	{
		$base = new Base();
		$stageID = $base->getStageID();

		$userID=$base->getUserID();
		$projectID = $base->getProjectID();
		$stageID = $base->getStageID();


		$questionnaire = Questionnaires::getInstance();
		foreach($_POST as $k=>$v){
			if ($k != "q_task"){
				$questionnaire->addAnswer($k,$v);
			}
		}
		$questionnaire->commitAnswersToDatabase(array("$userID","$projectID","$stageID"),array('userID','projectID','stageID'),'questionnaire_tlx_short');

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
		$questionnaire->populateQuestionsFromDatabase("summer2015-cogshort","questionID ASC");
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

	<?php
	$base = Base::getInstance();
	$stageID = $base->getStageID();
	if($stageID<45){
	?>
	<center><h2>Review Questionnaire (2 of 2)</h2></center>

	<?php
	}else{
	?>
	<center><h2>Questionnaire 2 of 2</h2></center>
	<?php
	}
	?>

	<?php
	$base = Base::getInstance();
	$stageID = $base->getStageID();
	if($stageID<45){
		echo "<p>You are done with the first portion of your work, which involved reviewing/evaluating sources that were already collected for the project assigned to you. Please answer the following questions about this task that you just finished.</p>";
	}else{
		echo "<p>You just finished the second portion of your work, which involved searching for and collecting relevant information. Please answer the following questions about this task that you just finished.</p>";
	}
	?>


<br/>

<form id="sum2015_qform" class="pure-form" method="post" action="q_task.php">
	<div class="pure-form-stacked">
		<fieldset>

			<?php


			$base = Base::getInstance();
			$stageID = $base->getStageID();
			$question = "";
			if($stageID<45){
				$question = "How much mental effort did you invest in this evaluation task?";
			}else{
				$question = "How much mental effort did you invest in this search task?";
			}


				printLikertTwo($question,"q_mentaleffort",array(
					"Very low mental effort" => "Very low mental effort",
					"Low mental effort" => "Low mental effort",
					"Somewhat low mental effort" => "Somewhat low mental effort",
					"Neither low nor high mental effort" => "Neither low nor high mental effort",
					"Somewhat high mental effort" => "Somewhat high mental effort",
					"High mental effort" => "High mental effort",
					"Very high mental effort" => "Very high mental effort"
				));
			?>


			<?php

			$base = Base::getInstance();
			$stageID = $base->getStageID();
			$question = "";
			if($stageID<45){
				$question = "How easy or difficult was this evaluation task?";
			}else{
				$question = "How easy or difficult was this evaluation task?";
			}
				$question = "How easy or difficult was this evaluation task?";
				printLikertTwo($question,"q_difficulty",array(
					"Very easy" => "Very easy",
					"Easy" => "Easy",
					"Fairly easy" => "Fairly easy",
					"Neither easy nor difficult" => "Neither easy nor difficult",
					"Fairly difficult" => "Fairly difficult",
					"Difficult" => "Difficult",
					"Very difficult" => "Very difficult"
				));
			?>
</fieldset>
</div>

<hr>

<input type="hidden" name="q_task" value="true"/>
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
