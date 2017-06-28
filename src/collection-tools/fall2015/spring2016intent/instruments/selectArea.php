<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{ 
		$collaborativeStudy = Base::getInstance()->getStudyID();
	
		if (isset($_POST['area_interest'])) 
		{
			$base = new Base();
			
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
			
			$query = "INSERT INTO user_session_topic (userID, projectID,sessionID, topicAreaID)
									VALUES('$userID','$projectID','$sessionID','$topic_area')";
			
			$connection = Connection::getInstance();			
			$results = $connection->commit($query);
			
			Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
			Util::getInstance()->moveToNextStage();
		}
		else
		{
		
			$base = new Base();
			$userID = $base->getUserID();
			$stageID = $base->getStageID();
			
			$query = "SELECT areaID,areaName FROM topic_area";
			
			$connection = Connection::getInstance();			
			$results = $connection->commit($query);
			
			$topic_areas_left = array();
			
			$line = mysql_fetch_array($results, MYSQL_ASSOC);
			while($line){
				$topic_areas_left[$line['areaID']] = $line['areaName'];
				$line = mysql_fetch_array($results, MYSQL_ASSOC);
			}
			
			
			$query = "SELECT topicAreaID FROM user_session_topic WHERE userID='$userID' AND sessionID=1";
			$connection = Connection::getInstance();			
			$results = $connection->commit($query);
			if(mysql_num_rows($results)>0){
				$line = mysql_fetch_array($results, MYSQL_ASSOC);
				while($line){
					if(array_key_exists($line['topicAreaID'],$topic_areas_left)){
						unset($topic_areas_left[$line['topicAreaID']]);
					}
					$line = mysql_fetch_array($results, MYSQL_ASSOC);
				}
			}
			
			
			

?>
<html>
<head>
<title>Area of Interest
</title>

</head>
<script type="text/javascript">
	
	function validate(form)
	{
		var result = isItemSelected(form.topic_area);
								
		if (!result)
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
	<form action="selectArea.php" method="post" onsubmit="return validate(this)">
		
		<table class="body" width=90% >
		<tr><th colspan=3>Exploratory Search User Study: Task (Area of Interest)</tr>
		<tr><td><br/></td></tr>	
		<tr><td>
		<ul>
				<li>Your actual search session is about to start. </li>
				<li>In this session, you will be assigned a task to work on based on your area of interest.</li>					
				<li>Select an area of interest from this page and click 'Continue'. You can only select ONE area to work on.</li>
				<li>Then you will have to answer a couple of questions to understand the level of your domain knowledge in the area you selected.</li>
				<li>Then you will be shown a topic and a task based on the area you selected that you will have to work on using the system for 1-2 hours.</li>
				<li>You MUST work for AT LEAST 1 hour on the task and you have up to 2 hours to complete the task. If you wish to finish earlier than 2 hours after 1 hour has elapsed, you can click on the 'Finish' button on the sidebar.</li>
		</ul>
		<tr><td colspan=3><div style="display: none; background: Red; text-align:center;" id="alert"><strong>Select area of interest.</strong></div></td></tr>		
		<tr bgcolor="#F2F2F2"><td colspan=1>Select area of interest: </td></tr>
		</table>
		
		<table align="center">
		<tr><td><br/></td></tr>
		<?php
		foreach($topic_areas_left as $ID=>$name){
			echo "<tr><td align='left'><input type='radio' name='topic_area' value='$ID'/>".$name."</td></tr>";
		}
		?>	
		<tr><td><br/></td></tr>
		<tr><td align="center"><input type="hidden" name="area_interest" value="true"/><input type="submit" value="Continue" /></td></tr>
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
