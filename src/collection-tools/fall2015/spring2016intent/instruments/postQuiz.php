<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{ 
		$collaborativeStudy = Base::getInstance()->getStudyID();
	
		if (isset($_POST['postQuiz'])) 
		{
			$base = new Base();
			$stageID = $base->getStageID();
			
			$userID=$base->getUserID();
			$topicAreaID=$_POST['topicAreaID'];
			
			//For each question
			//- Get the response from $_POST
			//- save response
			foreach($_POST as $key=>$value){
				if (strpos($key,'group')!==FALSE && strcmp($key,'groupcheckbox')!=0){
					$groupnum = substr($key,5);
					$groupostsponse = $_POST['group'.$groupnum];
					
					$query = "INSERT INTO user_quiz_postresponses (userID, quizID,topicAreaID, postResponseID)
									VALUES('$userID','$groupnum','$topicAreaID','$groupostsponse')";
			
					$connection = Connection::getInstance();			
					$results = $connection->commit($query);
					
					
				}
			}
			
			/*
			$topic_area = $_POST['topic_area'];
			$projectID = $base->getProjectID();
			$userID = $base->getUserID();
			$stageID = $base->getStageID();
			
			
			if($stageID<120)
			{
				$sessionID=1;
			}
			else
			{
				$sessionID=2;
			}
			
			$query = "INSERT INTO user_quiz_postresponses (userID, quizID,topicAreaID, postResponseID)
									VALUES('$userID','$groupnum','$topicAreaID','$groupostsponse')";
			
			$connection = Connection::getInstance();			
			$results = $connection->commit($query);
			
			
			*/
			Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
			Util::getInstance()->moveToNextStage();
		}
		else
		{
			$base = new Base();
			$userID = $base->getUserID();
			$stageID = $base->getStageID();
			
			if($stageID<120)
			{
				$sessionID=1;
			}
			else
			{
				$sessionID=2;
			}
			
			$qQuery = "SELECT q.quizID AS 'quizID', q.quizQuestion AS 'quizQuestion', q.numPossibleAnswers AS 'numPossibleAnswers', q.answerOption1 AS 'option1', q.answerOption2 AS 'option2', q.answerOption3 AS 'option3', q.answerOption4 AS 'option4', q.answerOption5 AS 'option5', q.topicAreaID AS 'topicAreaID' 
						FROM 
					(SELECT u.userID, u.topicAreaID, u.sessionID
						FROM user_session_topic u
						WHERE u.userID='$userID' AND u.sessionID='$sessionID') a
					INNER JOIN topic_quiz q
					ON q.topicAreaID = a.topicAreaID";
		
					
			$connection = Connection::getInstance();
			$results = $connection->commit($qQuery);
			$numRows = mysql_num_rows($results);
			
		
?>
<html>
<head>
<title>Questionnaire: Post Quiz
</title>
</head>
<script type="text/javascript">
		
	function checkBox(id){
		var box = document.getElementById(id);
		box.checked = true;
	}
	
	
	function validate(form)
	{
	
		var results = document.getElementsByName('groupcheckbox');
		
		var filled = true;
		
		for (var i = 0; i < results.length;i++)
		{
		
			filled = filled && results[i].checked;
		
		}
		
						
		if (!filled)
		{	
			document.getElementById("alert").style.display = "block";
			return false;
		}
		else
		{
			return true;
		}
	}		  
</script>

<body class="body">
<center>
	<br/>
	<form action="postQuiz.php" method="post" onsubmit="return validate(this)">
		<table class="body" width="90%">
		<tr><th><span style="font-weight:bold; font-size:20px">Exploratory Search User Study: TASK (Post Quiz)</span><br/><br/></th></tr>
		<tr><td><hr/></td></tr>
		
		<tr>
			<td>
				<ul>
				<li>Please select answers for ALL of the following questions related to the topic you worked on.</li>					
				<li>Please refrain from searching the answers to these questions on the internet or any other media now and answer based on your current knowledge and based on what you learnt by doing the task.</li>
				<li>Your performance evaluation would not depend on the number of correct answers you give to this set of questions, so please attempt to answer honestly based on your current acquired domain knowledge. </li>
				<li>Once you have selected answers to ALL questions to the best of your knowledge, please click 'Continue' to proceed. </li>
				<li>Then you would have to answer few more questionnaires about your experience while performing this task after which you would be able to logout of the system.</li>	
				</ul>
			</td>
		</tr>
		<tr><td><hr/></td></tr>
		<tr><td colspan=3><div style="display: none; background: Red; text-align:center;" id="alert"><strong>You MUST answer ALL questions!</strong></div></td></tr>
		<?php
		for($numQuizQuestions=1; $numQuizQuestions<=$numRows; $numQuizQuestions++)
			{
		
				$line = mysql_fetch_array($results, MYSQL_ASSOC);
				$numPossibleAnswers = $line['numPossibleAnswers'];
				$thequestion = $line['quizQuestion'];
				$topicAreaID = $line['topicAreaID'];
				$thequestionID = $line['quizID'];
				echo "<tr><td><br/><br/></td></tr>\n";
				echo "<tr>\n";
				echo "<td>Question $numQuizQuestions: $thequestion </td>\n";
				//echo "<tr>";
				for($i=1;$i<=$numPossibleAnswers;$i++)
				{
				$thetext = $line['option'."$i"];
				echo "<tr><td><input type='radio' name='group"."$thequestionID"."' value="."$i"." onclick=\"checkBox('checkbox"."$thequestionID"."')\"> ".$thetext."</td></tr>\n";
					
				}
				echo "<input type='checkbox' name='groupcheckbox' id='checkbox"."$thequestionID"."' style='display:none;'>\n";
				echo "</tr>\n";
			
			}
		?>
		<tr>
			<td align=center>
			<input type="hidden" name="topicAreaID" value="<?php echo $topicAreaID?>"/>
			<input type="hidden" name="postQuiz" value="true"/>
			<input type="submit" value="Continue" />
			</td>
		</tr>
	  </table>
	</form>
<br/>
</center>
</body>
</html>
<?php
		}
	}
	else {
		echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
	}
	
?>
