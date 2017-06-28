<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');

	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ ))) 
	{ 
		
			
		if (isset($_POST['demographic'])) 
		   {
			
			$base = new Base();

			$age = addslashes($_POST['age']);
			$sex = $_POST['sex'];
			$hand = $_POST['handed'];
			$program = $_POST['program'];
			$major = addslashes($_POST['major']);
			$os = $_POST['os'];
			$browser = $_POST['browser'];
			$searchExperience = $_POST['searchExperience'];
			$oftenSearch = $_POST['oftenSearch'];
			$mostUsedSearchEngine = $_POST['mostUsedSearchEngine'];
			
			/* ---Collaboation related ------
			$oftenTextMsg = $_POST['oftenTextMsg'];
			$oftenProjectWithOthers = $_POST['oftenProjectWithOthers'];
			$numCollaborationPastYear = addslashes($_POST['numCollaborationPastYear']);
			$enjoyCollaboration = $_POST['enjoyCollaboration'];
			$successCollaboartion = $_POST['successCollaboartion'];
			$sinceknowyear = addslashes($_POST['since_know_year']);
			$times_collab = addslashes($_POST['times_collab']);
			$past_collab_teammate = addslashes($_POST['past_collab_teammate']);
			$past_collab_other = addslashes($_POST['past_collab_other']);
			
			$relationship =  implode(",", $_POST['relationship']);
        	if ($relationship!="")
        		$relationship = $relationship . ",";
        	$relationship = $relationship . $_POST['relationshiptxt'];
        	$relationship = addslashes($relationship);
			*/
			
			$localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp =  $_POST['localTimestamp'];
			
			$projectID = $base->getProjectID();
			$userID = $base->getUserID();
			$time = $base->getTime();
			$date = $base->getDate();
			$timestamp = $base->getTimestamp();
			$stageID = $base->getStageID();
			
			/*$query = "INSERT INTO questionnaire_demographic (projectID, userID, age, sex, hand, program, major, os, browser, searchExperience, oftenSearch, oftenTextMsg, oftenProjectWithOthers, numCollaborationPastYear, enjoyCollaboration, successCollaboartion, mostUsedSearchEngine,date, time, timestamp, since_know_year, times_collab_general_teammate,past_collab_search_teammate,past_collab_search_others, relationship)
									                  VALUES('$projectID','$userID','$age', '$sex'  , '$hand', '$program', '$major', '$os', '$browser', '$searchExperience', '$oftenSearch', '$oftenTextMsg', '$oftenProjectWithOthers', '$numCollaborationPastYear', '$enjoyCollaboration', '$successCollaboartion', '$mostUsedSearchEngine', '$date','$time','$timestamp','$sinceknowyear','$times_collab','$past_collab_teammate','$past_collab_other','$relationship')";
			*/
			$query = "INSERT INTO questionnaire_demographic (projectID, userID, age, sex, hand, program, major, os, browser, searchExperience, oftenSearch, mostUsedSearchEngine,date, time, timestamp)
									                  VALUES('$projectID','$userID','$age', '$sex'  , '$hand', '$program', '$major', '$os', '$browser', '$searchExperience', '$oftenSearch', '$mostUsedSearchEngine', '$date','$time','$timestamp')";
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
		$base = new Base();
		$collab = $base->getStudyID();
		
?>
<html>
<head>
<title>Questionnaire: Demographics
</title>
<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript">

	function validate(form)
	{

		var result = !isNaN(form.age.value);
		
		result = result && (form.age.value != "");
		result = result && isItemSelected(form.sex);
		result = result && (form.program.value != "");
		result = result && (form.major.value != "");
		result = result && isItemSelected(form.os);
		result = result && isItemSelected(form.browser);
		//result = result && isItemSelected(form.oftenTextMsg);
		result = result && isItemSelected(form.searchExperience);
		result = result && isItemSelected(form.oftenSearch);
		result = result && isItemSelected(form.handed);
		result = result && isItemSelected(form.mostUsedSearchEngine);
		/*
		result = result && isItemSelected(form.oftenProjectWithOthers);
		result = result && !isNaN(form.numCollaborationPastYear.value);
		result = result && (form.numCollaborationPastYear.value != "");
		result = result && isItemSelected(form.enjoyCollaboration);
		result = result && isItemSelected(form.successCollaboartion);
		*/
		
	
	           
		if (!result)
		{	
			document.getElementById("alert").style.display = "block";
			return false;
		}
		else
		{
			document.getElementById("alert").style.display = "none";
			setLocalTime(form);
			return true;
		}
		
	}		 
</script>

</head>
<body class="body">
<center>
	<br/>
	<form id="form1" action="q_demographic.php" method="post" onsubmit="return validate(this)">
	<table class="body" width=60%>
	<?php
	$index=0;
	$color="\"White\"";
	function getColor($value)
	{
		if (($value % 2) == 0)
			$color="\"F2F2F2\"";
		else
			$color="\"White\"";

		return $color;
	}
	?>
		<tr><th colspan=3><span style="font-weight:bold; font-size:20px;">Exploratory Search User Study: Demographic Questionnaire</span><br/><br/></th></tr>
		<tr><td colspan=3><span style="font-weight:bold">Please fill in all the fields</span><br/></td></tr>
		<tr><td colspan=3><div style="display: none; background: Red; text-align:center;" id="alert"><strong>You must fill in all fields</strong></div></td></tr>			
		<tr bgcolor=<?php echo getColor($index); ?>>
		<td align=right><?php echo(++$index);?>.</td><td>Age</td><td><input type="text" size=4 name="age"/></td></tr>
		<tr bgcolor=<?php echo getColor($index); ?>>
		<td align=right><?php echo(++$index);?>.</td><td>Sex</td><td><input type="radio" name="sex" value="male"/> Male <input type="radio" name="sex" value="female"/> Female</td>
		</tr>
		<tr bgcolor=<?php echo getColor($index); ?>><td align=right><?php echo(++$index);?>.</td><td>Dominant hand</td><td><input type="radio" name="handed" value="right"/> Right-handed <br /><input type="radio" name="handed" value="left"/> Left-handed <br /><input type="radio" name="handed" value="ambi"/> Ambidextrous</td>
		</tr>
		
		<tr bgcolor=<?php echo getColor($index); ?>>
		<td align=right><?php echo(++$index);?>.</td><td>Your program of study</td><td><input type="radio" name="program" value="undergrad"/> Undergraduate <input type="radio" name="program" value="grad"/> Graduate <input type="radio" name="program" value="professional"/> Professional </td></tr>
		<tr bgcolor=<?php echo getColor($index); ?>><td align=right><?php echo(++$index);?>.</td><td>What is your major course of study?</td><td><input type="text" name="major" size=30 /></td></tr>

		<tr bgcolor=<?php echo getColor($index); ?>>
		<td align=right><?php echo(++$index);?>.</td><td>Which operating system do you<br/> use most frequently?</td><td><input type="radio" name="os" value="mac" /> Mac<br/><input type="radio" name="os" value="windows"/> Windows<br/><input type="radio" name="os" value="linux"/> Linux<br/><input type="radio" name="os" value="other"/> Other</td></tr>
		<tr bgcolor=<?php echo getColor($index); ?>><td align=right><?php echo(++$index);?>.</td><td>Which browser do you<br/> use most frequently?</td><td><input type="radio" name="browser" value="chrome" /> Chrome<br/><input type="radio" name="browser" value="firefox" /> Firefox<br/><input type="radio" name="browser" value="ie"/> Internet Explorer<br/><input type="radio" name="browser" value="safari"/> Safari<br/><input type="radio" name="browser" value="other"/> Other</td></tr>
		<tr bgcolor=<?php echo getColor($index); ?>><td align=right><?php echo(++$index);?>.</td><td>Which search engine do you use most frequently?</td><td><input type="radio" name="mostUsedSearchEngine" value="google" /> Google<br/><input type="radio" name="mostUsedSearchEngine" value="bing" /> Bing<br/><input type="radio" name="mostUsedSearchEngine" value="yahoo"/> Yahoo<br/><input type="radio" name="mostUsedSearchEngine" value="ask"/> Ask<br/><input type="radio" name="mostUsedSearchEngine" value="other"/> Other</td></tr>
		
		<tr bgcolor=<?php echo getColor($index); ?>>
		<td align=right><?php echo(++$index);?>.</td><td>How would you describe<br/>your search experience?</td><td> (Very inexperienced) <input type="radio" name="searchExperience" value="1"/>1 <input type="radio" name="searchExperience" value="2"/>2 <input type="radio" name="searchExperience" value="3"/>3 <input type="radio" name="searchExperience" value="4"/>4 <input type="radio" name="searchExperience" value="5"/>5 (Very experienced)</td></tr>
		<tr bgcolor=<?php echo getColor($index); ?>><td align=right><?php echo(++$index);?>.</td><td>How often do you<br/>search the Web?</td><td><input type="radio" name="oftenSearch" value="occassionally" /> Occasionally<br/><input type="radio" name="oftenSearch" value="1-3"/> 1-3 searches per day<br/><input type="radio" name="oftenSearch" value="4-6"/> 4-6 searches per day<br/><input type="radio" name="oftenSearch" value="7-10"/> 7-10 searches per day<br/><input type="radio" name="oftenSearch" value="10+"/> 10+ searches per day</td></tr>
		<tr bgcolor=<?php echo getColor($index); ?>>
		</tr>
				
		
		<tr><td colspan=3><br/></td></tr>	
		<tr><td colspan="3" align=center><input type="hidden" name="demographic" value="true"/>
									 	<input type="hidden" name="localTime" value=""/>
							 			<input type="hidden" name="localDate" value=""/>
							 			<input type="hidden" name="localTimestamp" value=""/>	
							 			<input type="submit" value="Submit"/></td></tr>	
										<!-- <input type="button" value="Submit" onClick="validate(document.getElementById('form1'))"/></td></tr>-->
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
		echo "Something went wrong. Please <a href=\"../index.php\">try again</a>.\n";
	}
	
?>
