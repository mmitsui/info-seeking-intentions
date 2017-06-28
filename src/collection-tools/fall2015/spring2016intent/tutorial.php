<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ ))) 
	{ 
		if (isset($_POST['tutorial'])) 
		   {
			
			$base = new Base();
			
			$stageID = $base->getStageID();
			
			$q_clear =  $_POST['q_clear'];
			$localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp =  $_POST['localTimestamp'];

			$projectID = $base->getProjectID();
			$userID = $base->getUserID();
			$time = $base->getTime();
			$date = $base->getDate();
			$timestamp = $base->getTimestamp();
			$stageID = $base->getStageID();
						
			/*$action = new Action('tutorial',"", $base);
			$action->setBase($base);
			$action->save();*/

			$query = "INSERT INTO questionnaire_tutorial (projectID, userID, stageID, clear, date, time, timestamp)
													VALUES('$projectID','$userID','$stageID','$q_clear','$date','$time','$timestamp')";
			
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
			
				$base = Base::getInstance();
				/*$path = "";
				$file = "";
				if ($base->getStudyID()==1)
				{
					$path = "tutorial/tutorialCollab/";
					$file = "TutorialCollab_controller.swf";
				}
				else
				{ 
					$path = "tutorial/tutorialSingle/";
					$file = "Tutorial Single 3_controller.swf";
				}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- saved from url=(0014)about:internet -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <!-- saved from url=(0025)http://www.techsmith.com/ -->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="DC.title" content="TutorialFinal" />

        <title>Video Tutorial</title>
		<script type="text/javascript" src="js/util.js"></script>        
        <script type="text/javascript">
        /*function validate(form)
    	{
    		return confirm("If you want to stay in the tutorial page press CANCEL. If you want to leave this page press OK");
    	}	*/
    	function validate(form)
    	{
    		var result = form.tutorialComplete.checked;

    		if (document.getElementById("q_tutorial").style.display == "block")
				result = result && isItemSelected(form.q_clear);
			else
				result = result && 0;
    		
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

    	function complete(check)
    	{
    		if (check.checked)
    		{
    			document.getElementById("complete").style.display = "block";
    			document.getElementById("q_tutorial").style.display = "block";
    			document.getElementById("alert").style.display = "none";
    		}
    		else
    		{
    			document.getElementById("complete").style.display = "none";
    			document.getElementById("q_tutorial").style.display = "none";
    			document.getElementById("alert").style.display = "block";
    		}
    	} 
        </script>
        <style type="text/css">
            body 
            {
                font: .8em/1.3em verdana,arial,helvetica,sans-serif;
                text-align: center;
            }
            #media
            {
                margin-top: 40px;
            }
            #noUpdate
            {
                margin: 0 auto;
                font-family:Arial, Helvetica, sans-serif;
                font-size: x-small;
                color: #cccccc;
                text-align: left;
                width: 210px; 
                height: 200px;	
                padding: 40px;
            }
		</style>
    </head>
    <body class="body">
	    <center>
	    <table>

	    <tr align="center"><th><span style="font-weight:bold; font-size:18px">Exploratory Search User Study: Tutorial</span></th></tr>
	    <tr><td><br/></td></tr>
	    <?php
	    if(Base::getInstance()->getStageID()>120)
	    {
	    ?>
	    <tr align="center"><td><span style="font-size:18px; background: Yellow;">This is the same video tutorial you watched in session one of this study. If you would like to remind yourself of the system usage, please watch it again.</span></td></tr>
	    <?php
	    }
	    ?>
	    
	    <tr align="center"><td><span style="font-size:18px; color:blue">This stage requires headphones or speakers to listen to the instructions. Click play on the black box below when you are ready and watch the tutorial.</span></td></tr>
	    
	    <tr align="center"><td>
	   <div id="media">

        <video width="900" height="675" id="csSWF" controls>
        <source src="tutorial/summer2014tutorial.mp4" type="video/mp4">
        <source src="tutorial/summer2014tutorial.ogg" type="video/ogg">

        <object data="tutorial/summer2014tutorial.mp4" width="900" height="675">
            <embed width="900" height="675" src="tutorial/summer2014tutorial.mp4">
        </object>
        </video>
        </div>
        </td></tr>
        <tr align="center"><td>
        <form action="tutorial.php" method="post" onsubmit="return validate(this)">
        	<table>
	        	<tr><td><div style="display: none; background: Red; text-align:center;" id="alert"><strong>Before you continue, you must watch the tutorial. Once you have watched it, click on the box below, complete the questionnaire, and then continue.</strong></div></td></tr>
	        	<tr><td><div style="display: none; background: LightGreen; text-align:center;" id="complete"><strong>Good! You can take off the headphones or turn off the speakers now. Respond the following question and then click on Continue.</strong></div></td></tr>	        	
	        	<tr><td><br/></td></tr>
    		    <tr><td align=center><input type="checkbox" name="tutorialComplete" value="true" onclick="complete(this)"/>I have watched the tutorial and understood basic usage of the system.</td></tr>
	        	<tr><td><div style="display: none; text-align:center;" id="q_tutorial">
	        		<strong><br />
	        				<p>Was this tutorial clear?<p>
							<p>Not clear at all&nbsp;&nbsp;<input type="radio" name="q_clear" value="1"/>1 <input type="radio" name="q_clear" value="2"/>2 <input type="radio" name="q_clear" value="3"/>3 <input type="radio" name="q_clear" value="4"/>4 <input type="radio" name="q_clear" value="5"/>5 &nbsp;&nbsp;&nbsp;&nbsp;Very Clear</p>
							<br />	        					        	
	        		</strong></div>
	        		</td>
	        	</tr>    		    
				<tr><td align=center><input type="hidden" name="tutorial" value="true"/>
									 <input type="hidden" name="localTime" value=""/>
							 		<input type="hidden" name="localDate" value=""/>
							 		<input type="hidden" name="localTimestamp" value=""/>						
									<input type="submit" value="Continue" /></td></tr>
			</table>
		</form>
		</td></tr>
		</table>
		</center>
    </body>
</html>

<?php
		}
	}
	else {
		echo "<tr><td>Something went wrong. Please <a href=\"../index.php\">try again </a>.</td></tr>\n";
	}
	
?>
