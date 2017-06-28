<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{ 
		if (isset($_POST['taskProcess'])) 
		   {
			
			$base = new Base();

			$q1_plan = $_POST['q1_plan'];
			$q2_organize = $_POST['q2_organize'];
			$q3_report = $_POST['q3_report'];
			$q4_otherTools = $_POST['q4_otherTools'];
			$q5_tabs = $_POST['q5_tabs'];
			$q6_time = $_POST['q6_time'];
			$q7_otherTasks = $_POST['q7_otherTasks'];
			$q8_entertainment = $_POST['q8_entertainment'];
			$q9_comments = addslashes($_POST['q9_comments']);

			$localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp =  $_POST['localTimestamp'];
									
			$projectID = $base->getProjectID();
			$userID = $base->getUserID();
			$time = $base->getTime();
			$date = $base->getDate();
			$timestamp = $base->getTimestamp();
			$stageID = $base->getStageID();
			
			$query = "INSERT INTO questionnaire_taskprocess (projectID, userID,stageID, q1_plan, q2_organize, q3_report, q4_otherTools, q5_tabs, q6_time,q7_otherTasks, q8_entertainment,q9_comments,date, time, timestamp)
									VALUES('$projectID','$userID','$stageID','$q1_plan','$q2_organize','$q3_report','$q4_otherTools','$q5_tabs','$q6_time','$q7_otherTasks','$q8_entertainment','$q9_comments','$date','$time','$timestamp')";
			
			$connection = Connection::getInstance();			
			$results = $connection->commit($query);
			$lastID = $connection->getLastID();
						
			//Save action
			Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$lastID,$base,$localTime,$localDate,$localTimestamp);			
			
			//Next stage
			Util::getInstance()->moveToNextStage();
		}
		else {
?>
<html>
<head>
<title>Questionnaire: Strategy
</title>
<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript">
	function validate(form)
	{
		var result = isItemSelected(form.q1_plan);
		result = result && isItemSelected(form.q2_organize);
		result = result && isItemSelected(form.q3_report);
		result = result && isItemSelected(form.q4_otherTools);
		result = result && isItemSelected(form.q5_tabs);
		result = result && isItemSelected(form.q6_time);
		result = result && isItemSelected(form.q7_otherTasks);
		result = result && isItemSelected(form.q8_entertainment);
						
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
	<form action="q_taskProcess.php" method="post" onsubmit="return validate(this)">
      <table class="body" width=85%>
		<tr><th colspan=3>Please answer the following questions related to how you approached the task and on the activities you did while performing the task.</th></tr>
		<tr><th colspan=3>Please answer honestly. Your performance evaluation would not depend on any comments or answers you provide here.</th></tr>
		<tr><td><br/></td></tr>	
		<tr><td colspan=3><div style="display: none; background: Red; text-align:center;" id="alert"><strong>You MUST select either 'Yes' or 'No' to all questions from 1-8.</strong></div></td></tr>		
		<tr bgcolor="#F2F2F2" ><td colspan=3>1. Did you plan ahead as to how to approach the task after seeing the task description? </td></tr>
										<tr><td align=center><input type="radio" name="q1_plan" value="1" />Yes 
															<input type="radio" name="q1_plan" value="0" />No
										</td></tr>
		<tr><td colspan=3><br/></td></tr>
					
		<tr bgcolor="#F2F2F2"><td colspan=3>2. Did you organize what to search online based on different sub areas to cover that were given in the task description?</td></tr>
		<tr><td align=center><input type="radio" name="q2_organize" value="1" />Yes 
						<input type="radio" name="q2_organize" value="0" />No
		</td></tr>
		<tr><td colspan=3><br/></td></tr>
		
		<tr bgcolor="#F2F2F2"><td colspan=3>3. Did you structure the report by typing in potential headings first?</td></tr>
		<tr><td align=center><input type="radio" name="q3_report" value="1" />Yes 
						<input type="radio" name="q3_report" value="0" />No
		</td></tr>
		<tr><td colspan=3><br/></td></tr>
				
		<tr bgcolor="#F2F2F2"><td colspan=3>4. Did you use any other software tools (other than tools provided in the toolbar, side bar and browser search) such as other text editors, or other physical media such as actual pen and paper to write any notes?</td></tr>
		<tr><td align=center><input type="radio" name="q4_otherTools" value="1" />Yes 
						<input type="radio" name="q4_otherTools" value="0" />No
		</td></tr>
		
		<tr><td colspan=3><br/></td></tr>	
		<tr bgcolor="#F2F2F2"><td colspan=3>5. Did you use multiple tabs in the browser to search and organize information on this task?</td></tr>
		<tr><td align=center><input type="radio" name="q5_tabs" value="1" />Yes 
						<input type="radio" name="q5_tabs" value="0" />No
		</td></tr>
		<tr><td colspan=3><br/></td></tr>

		<tr bgcolor="#F2F2F2"><td colspan=3>6. Did you find the time allocated sufficient to complete the task to your satisfaction?</td></tr>
					<tr><td align=center><input type="radio" name="q6_time" value="1" />Yes 
									<input type="radio" name="q6_time" value="0" />No
					</td></tr>
		<tr><td colspan=3><br/></td></tr>
		
		<tr bgcolor="#F2F2F2" ><td colspan=3>7. Did you engage in other personal activities while performing this task (eg: take phone calls, online chat, visit social media sites, Web browsing for other tasks, check email, etc) ?</td></tr>
					<tr><td align=center><input type="radio" name="q7_otherTasks" value="1" />Yes 
									<input type="radio" name="q7_otherTasks" value="0" />No						
					</td></tr>
		<tr><td colspan=3><br/></td></tr>
		
		<tr bgcolor="#F2F2F2" ><td colspan=3>8. Did you listen to music or use any other entertainment media (such as watching tv, watching a videos, etc) while performing this task?</td></tr>
					<tr><td align=center><input type="radio" name="q8_entertainment" value="1" />Yes 
									<input type="radio" name="q8_entertainment" value="0" />No						
					</td></tr>
		<tr><td colspan=3><br/></td></tr>

		<tr bgcolor="#F2F2F2" ><td colspan=3>9. If there are any other strategie(s) or alternative way(s) you approached the task please mention here. </td></tr>
					<tr><td align=center><textarea name="q9_comments" cols=55 rows=3></textarea>						
					</td></tr>
		<tr><td colspan=3><br/></td></tr>
		
		<tr><td colspan=3 align=center><br/><input type="hidden" name="taskProcess" value="true"/>
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
