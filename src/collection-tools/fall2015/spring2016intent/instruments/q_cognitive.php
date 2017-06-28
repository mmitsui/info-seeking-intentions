<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{ 
		if (isset($_POST['cognitive'])) 
		   {
			
			$base = new Base();

			$q1_mentally = $_POST['q1_mentally'];
			$q2_physically = $_POST['q2_physically'];
			$q3_rushed = $_POST['q3_rushed'];
			$q4_success = $_POST['q4_success'];
			$q5_hard = $_POST['q5_hard'];
			$q6_negativeAffect = $_POST['q6_negativeAffect'];
			$q7_learning = $_POST['q7_learning'];
			$q8_interest = $_POST['q8_interest'];

			$localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp =  $_POST['localTimestamp'];
									
			$projectID = $base->getProjectID();
			$userID = $base->getUserID();
			$time = $base->getTime();
			$date = $base->getDate();
			$timestamp = $base->getTimestamp();
			$stageID = $base->getStageID();
			
			$query = "INSERT INTO questionnaire_cognitive (projectID, userID, stageID,q1_mentally, q2_physically, q3_rushed, q4_success, q5_hard, q6_negativeAffect,q7_learning, q8_interest,date, time, timestamp)
									VALUES('$projectID','$userID','$stageID','$q1_mentally','$q2_physically','$q3_rushed','$q4_success','$q5_hard','$q6_negativeAffect','$q7_learning','$q8_interest','$date','$time','$timestamp')";
			
			$connection = Connection::getInstance();			
			$results = $connection->commit($query);
			$lastID = $connection->getLastID();
						
			//Save action
			//Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
			Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$lastID,$base,$localTime,$localDate,$localTimestamp);			
			
			//Next stage
			Util::getInstance()->moveToNextStage();
		}
		else {
?>
<html>
<head>
<title>Questionnaire: Cognitive
</title>
<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript">
	function validate(form)
	{
		var result = isItemSelected(form.q1_mentally);
		result = result && isItemSelected(form.q2_physically);
		result = result && isItemSelected(form.q3_rushed);
		result = result && isItemSelected(form.q4_success);
		result = result && isItemSelected(form.q5_hard);
		result = result && isItemSelected(form.q6_negativeAffect);
		result = result && isItemSelected(form.q7_learning);
		result = result && isItemSelected(form.q8_interest);
						
		if (!result)
		{	
			document.getElementById("alert").style.display = "block";
			return false;
		}
		else
		{
			setLocalTime(form);
			return true;
		}
	}		 
</script>

</head>
<body class="body">
<center>
	<br/>
	<form action="q_cognitive.php" method="post" onsubmit="return validate(this)">
      <table class="body" width=85%>
		<tr><th colspan=3>Please answer the following questions on the scale of 1 to 5, 1 being lowest and 5 being highest.</th></tr>
		<tr><td><br/></td></tr>	
		<tr><td colspan=3><div style="display: none; background: Red; text-align:center;" id="alert"><strong>You MUST answer ALL questions!</strong></div></td></tr>		
		<tr bgcolor="#F2F2F2"><td colspan=3>1. How mentally demanding was this task?</td></tr>
					<tr><td align=center><input type="radio" name="q1_mentally" value="1" />1 
									 <input type="radio" name="q1_mentally" value="2" />2 
									 <input type="radio" name="q1_mentally" value="3" />3 
									 <input type="radio" name="q1_mentally" value="4" />4 
									 <input type="radio" name="q1_mentally" value="5" />5
					</td></tr>
		<tr><td colspan=3><br/></td></tr>

		<tr bgcolor="#F2F2F2"><td colspan=3>2. How physically demanding was this task?</td></tr>
					<tr><td align=center><input type="radio" name="q2_physically" value="1" />1 
									<input type="radio" name="q2_physically" value="2" />2 
									<input type="radio" name="q2_physically" value="3" />3 
									<input type="radio" name="q2_physically" value="4" />4 
									<input type="radio" name="q2_physically" value="5" />5
					</td></tr> 
		<tr><td colspan=3><br/></td></tr>
		
		<tr bgcolor="#F2F2F2" ><td colspan=3>3. How hurried or rushed was the pace of the task?</td></tr>
					<tr><td align=center><input type="radio" name="q3_rushed" value="1" />1 
									<input type="radio" name="q3_rushed" value="2" />2 
									<input type="radio" name="q3_rushed" value="3" />3 
									<input type="radio" name="q3_rushed" value="4" />4 
									<input type="radio" name="q3_rushed" value="5" />5
					</td></tr>
		<tr><td colspan=3><br/></td></tr>

		<tr bgcolor="#F2F2F2" ><td colspan=3>4. How successful were you in accomplishing what you were asked to do?</td></tr>
					<tr><td align=center><input type="radio" name="q4_success" value="1" />1 
									<input type="radio" name="q4_success" value="2" />2 
									<input type="radio" name="q4_success" value="3" />3 
									<input type="radio" name="q4_success" value="4" />4 
									<input type="radio" name="q4_success" value="5" />5
					</td></tr>			
		<tr><td colspan=3><br/></td></tr>
		
		<tr bgcolor="#F2F2F2" ><td colspan=3>5. How hard did you have to work to accomplish your level of performance?</td></tr>
					<tr><td align=center><input type="radio" name="q5_hard" value="1" />1 
									<input type="radio" name="q5_hard" value="2" />2 
									<input type="radio" name="q5_hard" value="3" />3 
									<input type="radio" name="q5_hard" value="4" />4 
									<input type="radio" name="q5_hard" value="5" />5
					</td></tr>			
		<tr><td colspan=3><br/></td></tr>

		<tr bgcolor="#F2F2F2"><td colspan=3>6. How insecure, discouraged, irritated, stressed, and annoyed were you?</td></tr>
		
						<tr><td align=center><input type="radio" name="q6_negativeAffect" value="1" />1 
									<input type="radio" name="q6_negativeAffect" value="2" />2 
									<input type="radio" name="q6_negativeAffect" value="3" />3 
									<input type="radio" name="q6_negativeAffect" value="4" />4 
									<input type="radio" name="q6_negativeAffect" value="5" />5
						</td></tr>			
		<tr><td colspan=3><br/></td></tr>	
		
		<tr bgcolor="#F2F2F2"><td colspan=3>7. How would you rate your level of learning about the topic by doing this task?</td></tr>
							<tr><td align=center><input type="radio" name="q7_learning" value="1" />1 
									<input type="radio" name="q7_learning" value="2" />2 
									<input type="radio" name="q7_learning" value="3" />3 
									<input type="radio" name="q7_learning" value="4" />4 
									<input type="radio" name="q7_learning" value="5" />5
							</td></tr>		 
		<tr><td colspan=3><br/></td></tr>
		
		<tr bgcolor="#F2F2F2"><td colspan=3>8. How would you rate your level of interest in the topic after completing the task?</td></tr>
							<tr><td align=center><input type="radio" name="q8_interest" value="1" />1 
									<input type="radio" name="q8_interest" value="2" />2 
									<input type="radio" name="q8_interest" value="3" />3 
									<input type="radio" name="q8_interest" value="4" />4 
									<input type="radio" name="q8_interest" value="5" />5 
							</td></tr>		
		<tr><td colspan=3><br/></td></tr>
			
		<tr><td colspan=3 align=center><br/><input type="hidden" name="cognitive" value="true"/>
									 		<input type="hidden" name="localTime" value=""/>
							 				<input type="hidden" name="localDate" value=""/>
							 				<input type="hidden" name="localTimestamp" value=""/>
											<input type="submit" value="Submit" />
		</td></tr>
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
		echo "<tr><td>Something went wrong. Please <a href=\"../index.php\">try again</a>.</td></tr>\n";
	}
	
?>
