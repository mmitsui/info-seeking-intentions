<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ ))) 
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();

		if (isset($_POST['taskIntro'])) 
		   {
			
			$base = new Base();
			
			$stageID = $base->getStageID();
               
           $localTime = $_POST['localTime'];
           $localDate = $_POST['localDate'];
           $localTimestamp =  $_POST['localTimestamp'];
           
           $userID = $base->getUserID();
           $projectID = $base->getProjectID();
           
           
           $sessionID=1;
           $topicAreaID = -1;
           
           $query = "SELECT topicAreaID FROM users WHERE userID=$userID";
           $connection = Connection::getInstance();
           $results = $connection->commit($query);
           $line = mysql_fetch_array($results, MYSQL_ASSOC);
           $topicAreaID = $line['topicAreaID'];
           
           
           $query = "INSERT INTO user_session_topic (userID, projectID,sessionID, topicAreaID)
           VALUES('$userID','$projectID','$sessionID','$topicAreaID')";
           $connection = Connection::getInstance();
           $results = $connection->commit($query);
        
           //Save action + move to next stage
           Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$stageID,$base,$localTime,$localDate,$localTimestamp);
           Util::getInstance()->moveToNextStage();
		}
		else {
			
			
?>
<html>
<head>
<title>Study Instructions
</title>
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">
</head>
<script type="text/javascript" src="js/util.js"></script>

<script type="text/javascript">
	function validate(form)
	{
		if (!form.confirmReadInstructions.checked)
		{	
			document.getElementById("alert").style.display = "block";
			return false;
		}
		else{
			setLocalTime(form);
			return true;
		}
	}

	function complete(check)
	{
		if (check.checked)
		{
			document.getElementById("complete").style.display = "block";
			document.getElementById("alert").style.display = "none";
		}
		else
		{
			document.getElementById("complete").style.display = "none";
			document.getElementById("alert").style.display = "block";
		}
	} 	 
</script>
<body class="body">

<?php
    $session=1;
?>
<center>
	<br/>
	<form action="<?php echo basename( __FILE__ );?>" method="post" onsubmit="return validate(this)">
		<table class="body" width="90%">
		<tr><th><h2>Study Instructions</h2></th></tr>
		<tr><td><hr/></td></tr>
		<tr>
			<td>
				<?php
					$teammateStr = "teammate";
					if ($collaborativeStudy==3)
						$teammateStr = $teammateStr."s";
				?>
				<ul>
				<li>During the following <strong>40 minutes</strong> you will search for sources on your research topic.</li>
				<li>Use the Coagmento toolbar to <strong>Snip</strong> portions of text from sources you find, <strong>Bookmark</strong> URLs, and write notes in the <strong>Task Pad</strong>.</li>
				<li>Use the Coagmento sidebar to review your <strong>History</strong> (bookmarks and snippets)</li>  
				<?php if ($collaborativeStudy>1) echo "<li>You can communicate with your partner using the <strong>Chat box</strong> in the sidebar</li>"; ?>
				<li>For more information on using Coagmento, click the <strong>Help</strong> button. </li>
				<li>Your <strong>40 minutes</strong> starts after you read the following instructions and click the <strong>Start Task</strong> button.</li>
				<li>You will see the timer in the sidebar flashing when you have <strong>5 minutes remaining</strong> to complete the task.</li>			
				<li>After time is up, you will automatically be redirected to a brief post-task questionnaire.</li>
                <li>You are eligible for a cash <strong>$20 first prize</strong> and <strong>$10 second prize</strong> for best performers.</li>
                <li>To win the cash prize, save the most good quality sources, make good notes on the sources you save, rate the sources using stars, and write a summary of your sources in the Task Pad.</li>

				</ul>
				<center>
					<p>
						<strong>If you have read these instructions and ready to start the task, please click the checkbox below and press "Start Task." Have fun!</strong>
					</p>
				</center>
			</td>
		</tr>	
		<tr><td><hr/></td></tr>
		<tr><td><div style="display: none; background: Red; text-align:center;" id="alert"><strong>Before you continue, you must read all the above instructions. Once you have read them, click on the box below and then continue.</strong></div></td></tr>
		<tr><td><div style="display: none; background: LightGreen; text-align:center;" id="complete"><strong>Good! Click on "Start Task" and proceed with working on the first task.</strong></div></td></tr>	        	
		<tr><td align=center><input type="checkbox" name="confirmReadInstructions" value="true" onclick="complete(this)"/>I have read all the above instructions</td></tr>
		<tr><td><br/></td></tr>
<tr><td align=center>
<input type="hidden" name="pretask" value="true"/><input type="hidden" name="taskIntro" value="true"/>
<input type="hidden" name="localTime" value=""/>
							 				<input type="hidden" name="localDate" value=""/>
							 				<input type="hidden" name="localTimestamp" value=""/>
							 				<button type="submit" class="pure-button pure-button-primary" >Start Task</button></td></tr>
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