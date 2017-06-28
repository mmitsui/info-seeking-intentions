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


	if (isset($_POST['posttask_q']))
	{
		$base = new Base();
		$stageID = $base->getStageID();

		$userID=$base->getUserID();
		$projectID=$base->getProjectID();
		$connection = Connection::getInstance();


		$time = $base->getTime();
		$date = $base->getDate();
		$timestamp = $base->getTimestamp();

		$q_familiar = addslashes($_POST['q_familiar']);
		$q_keywords = addslashes($_POST['q_keywords']);
		$q_lookup = addslashes($_POST['q_lookup']);
		$q_nextsteps = addslashes($_POST['q_nextsteps']);
		$q_valuable = addslashes($_POST['q_valuable']);

		$connection->commit("INSERT INTO questionnaire_repeated_final (userID,projectID,stageID,`date`,`time`,`timestamp`,q_familiar,q_keywords,q_lookup,q_nextsteps,q_valuable) VALUES ('$userID','$projectID','$stageID','$date','$time','$timestamp','$q_familiar','$q_keywords','$q_lookup','$q_nextsteps','$q_valuable')");

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
		$questionnaire->populateQuestionsFromDatabase("summer2015-repeated-final","questionID ASC");
		$questionnaire->setBaseDirectory("../");


		$oldanswers = array();
		$connection = Connection::getInstance();
		$res = $connection->commit("SELECT `group` FROM users WHERE userID='$userID'");
		$line = mysql_fetch_array($res,MYSQL_ASSOC);
		$group = $line['group'];
		$stageID="";

		if($group=='treatment'){
			$stageID="40";
		}else if($group=='control'){
			$stageID="40";
		}else{
			echo "ERROR!";
		}

		$res = $connection->commit("SELECT * FROM questionnaire_repeated_mid WHERE userID='$userID' AND stageID='$stageID'");
		$line = mysql_fetch_array($res,MYSQL_ASSOC);

		$oldanswers['q_familiar'] = $line['q_familiar'];
		$oldanswers['q_lookup'] = $line['q_lookup'];
		$oldanswers['q_keywords'] = $line['q_keywords'];

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
	<center><h2>Questionnaire 1 of 2</h2></center>
	<p>Now that you have searched for sources on this topic, please review the task description and your previous answers
		below.</p>

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

<form id="sum2015_qform" class="pure-form" method="post" action="posttask_q.php">
	<div class="pure-form-stacked">
		<fieldset>


<?php

	$familiar = $oldanswers['q_familiar'];
	$question = "How familiar are you with the topic of this task? <br/><span style=\"background-color:#F2F2F2\"><strong>Previous: $familiar</strong></span><br/>
	How familiar are you now? (1=Not Familiar At All, 5=Very Familiar)";
	printLikertTwo($question,"q_familiar",array(
    "1" => "1",
    "2" => "2",
		"3" => "3",
		"4" => "4",
		"5" => "5",
	));
?>


<br/>
<div class="pure-control-group">
<div id="q_lookup_div">
<label name="q_lookup">How would you look for information for this task? Where or how would you look up this information? <br/><span style="background-color:#F2F2F2"><strong>Previous answer: <?php echo $oldanswers['q_lookup'];?></strong></span><br/>
Did this approach work? Did you do anything differently? Why?
</label>
<textarea name="q_lookup" id="q_lookup" rows="5" cols="80" required></textarea>
<br>
</div>
</div>


<div class="pure-control-group">
<div id="q_keywords_div">
<label name="q_keywords">What keywords or terms would you search? Please list 3-4 keywords/terms? <br/><span style="background-color:#F2F2F2"><strong>Previous answer: <?php echo $oldanswers['q_keywords'];?></strong></span><br/>
Did these keywords work? Did you use any different ones? Why?
</label>
<textarea name="q_keywords" id="q_keywords" rows="5" cols="80" required></textarea>
<br>
</div>
</div>

<div class="pure-control-group">
<div id="q_nextsteps_div">
<label name="q_nextsteps">If you had to continue working on this group project, what steps would you take next?
</label>
<textarea name="q_nextsteps" id="q_nextsteps" rows="5" cols="80" required></textarea>
<br>
</div>
</div>




<div class="pure-control-group">
<div id="q_valuable_div">
<?php

$userID = $base->getUserID();
$connection = Connection::getInstance();
$res = $connection->commit("SELECT `group` FROM users WHERE userID='$userID'");
$line = mysql_fetch_array($res,MYSQL_ASSOC);
$group = $line['group'];

if($group == 'control'){
	echo "<label name=\"q_valuable\">How valuable was it to review the work done by other team members before you proceeded with your own searching?<br/>How did it affect your search task?</label>";
}else if ($group =='treatment'){
	echo "<label name=\"q_valuable\">How valuable was it to review and evaluate the work done by other team members before you proceeded with your own searching?<br/>How did it affect your search task?</label>";
}

?>

<textarea name="q_valuable" id="q_valuable" rows="5" cols="80" required></textarea>
<br>
</div>
</div>




</fieldset>
</div>

<hr>

<input type="hidden" name="posttask_q" value="true"/>
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
