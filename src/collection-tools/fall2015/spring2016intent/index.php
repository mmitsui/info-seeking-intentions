<?php
	session_start();
	require_once('core/Connection.class.php');
	require_once('core/Base.class.php');
	require_once('core/Action.class.php');
	require_once('core/Util.class.php');
    //Variable for determining if the study is closed
    $closed = false;


	//If you want to set session variables use Base::getInstance(). Do not create a new Base object for that
	// If login information was sent

	if (isset($_POST['userName'])&&(!Base::getInstance()->isSessionActive()))
	{

		$userName = $_POST['userName'];
		$password = sha1($_POST['password']);


		//BEGIN TEMP: restart test_1
		$a = array('test_1','test_2','test_3','test_10','test_t','test_c');
        if(in_array($userName,$a)){
            foreach($a as $t){
                $q = "SELECT userID, projectID from users WHERE userName='$userName'";
                $c = Connection::getInstance();
                $r = $c->commit($q);
                if(mysql_num_rows($r)>0){
                    $l = mysql_fetch_array($r,MYSQL_ASSOC);

                    $i = $l['userID'];
                    $q = "DELETE FROM session_progress WHERE userID='$i'";

                    $c = Connection::getInstance();
                    $r = $c->commit($q);

										$q = "DELETE FROM questions_progress WHERE userID='$i'";
										$r = $c->commit($q);

										$q = "DELETE FROM video_intent_assignments WHERE userID='$i'";
										$r = $c->commit($q);

										$q = "DELETE FROM video_reformulation_history WHERE userID='$i'";
										$r = $c->commit($q);

										$q = "DELETE FROM video_save_history WHERE userID='$i'";
										$r = $c->commit($q);

										$q = "DELETE FROM video_unsave_history WHERE userID='$i'";
										$r = $c->commit($q);

                    // $q = "INSERT INTO session_progress (projectID,userID,stageID,`date`,`time`,`timestamp`) VALUES ('$i','$i','32','2014-11-06','17:03:20','1415311400'),('$i','$i','1','2014-11-06','17:03:20','1415311400'),('$i','$i','33','2014-11-06','17:03:20','1415311400')";
                    // $c = Connection::getInstance();
                    // $r = $c->commit($q);

                    $q = "DELETE FROM questions_progress WHERE userID='$i'";
                    $c = Connection::getInstance();
                    $r = $c->commit($q);
                }
            }
        }
		//END TEMP: restart test_1
		// Check if second session login has passed 48 hours since completing first session
		$qGetUserID = "SELECT userID from users WHERE userName='$userName' AND password_sha1='$password'";
		$cGetUserID = Connection::getInstance();
		$rGetUserID = $cGetUserID->commit($qGetUserID);
		if (mysql_num_rows($rGetUserID)==1) //matching user exists
		{

			$lGetUserID = mysql_fetch_array($rGetUserID, MYSQL_ASSOC);
			$userID = $lGetUserID['userID'];

			$qEndFirstSession = "SELECT userID, projectID, stageID, timestamp FROM session_progress WHERE userID = '$userID' AND stageID='".strval(Stage::TASK_END)."'";
			$cEndFirstSession = Connection::getInstance();
			$rEndFirstSession = $cEndFirstSession->commit($qEndFirstSession);

			$base = Base::getInstance();
			if (mysql_num_rows($rEndFirstSession)>=1) //Task is already complete
			{

                $qStartSecSession = "UPDATE users SET status = 0 WHERE userID = '$userID'";
                $cStartSecSession = Connection::getInstance();
                $rStartSecSession = $cStartSecSession->commit($qStartSecSession);
			}else{
                $qStartSecSession = "UPDATE users SET status = 1 WHERE userID = '$userID'";
                $cStartSecSession = Connection::getInstance();
                $rStartSecSession = $cStartSecSession->commit($qStartSecSession);

            }

		}






        $query = "SELECT userID, projectID, username, study FROM users WHERE userName='$userName' AND password_sha1='$password' AND status = 1";

        $connection = Connection::getInstance();
        $results = $connection->commit($query);

        $localTime = $_POST['localTime'];
        $localDate = $_POST['localDate'];
        $localTimestamp = $_POST['localTimestamp'];
        if (mysql_num_rows($results)==1) {
            $line = mysql_fetch_array($results, MYSQL_ASSOC);
            $userID = $line['userID'];



            $ip = $_SERVER['REMOTE_ADDR'];

            $q = "UPDATE users SET ip='$ip' WHERE userID='$userID'";
            $c = Connection::getInstance();
            $r = $c->commit($q);





						// Set participant ID
			$query = "SELECT * FROM users WHERE userID='$userID' AND participantID IS NOT NULL";
			$connection = Connection::getInstance();
			$results = $connection->commit($query);

			if(mysql_num_rows($results) == 0){
				$query = "SELECT COUNT(*) as ct FROM users WHERE userID!='$userID' AND participantID IS NOT NULL AND arrived=1;";
				$results = $connection->commit($query);
				$line = mysql_fetch_array($results,MYSQL_ASSOC);
				$next_participantID = $line['ct']+1;
				$next_participantID = "S".str_pad("$next_participantID", 3, '0', STR_PAD_LEFT);
				$query = "UPDATE users SET participantID='$next_participantID' WHERE userID='$userID'";
				$results = $connection->commit($query);

				$query = "SELECT questionID1,questionID2 FROM participant_id_to_task WHERE participantID='$next_participantID'";
				$results = $connection->commit($query);
				$line = mysql_fetch_array($results,MYSQL_ASSOC);
				$questionID1 = $line['questionID1'];
				$questionID2 = $line['questionID2'];

				$query = "UPDATE users SET topicAreaID1='$questionID1',topicAreaID2='$questionID2' WHERE userID='$userID' AND participantID='$next_participantID'";
				$results = $connection->commit($query);
				$line = mysql_fetch_array($results,MYSQL_ASSOC);
			}




			$query = "SELECT * FROM users WHERE userID='$userID' AND participantID IS NOT NULL";
			$connection = Connection::getInstance();
			$results = $connection->commit($query);
			$line = mysql_fetch_array($results,MYSQL_ASSOC);
            //$userName = $line['userName'];
            $projectID = $line['projectID'];
            $studyID = $line['study'];

            $base = Base::getInstance();
            $base->setUserName($userName);
            $base->setUserID($userID);
            $base->setProjectID($projectID);
            $base->setStageID(-1);
            $base->setStudyID($studyID);
            $base->setLocalTimestamp($localTimestamp);
            $base->setLocalTime($localTime);
            $base->setLocalDate($localDate);
            //$base->setQuestionID(-1);
            //Save action
            Util::getInstance()->saveAction('login',0,$base); //Try later to insert machine name; otherwise work with IP
            //Next stage




            $stage = new Stage(); //causing storage error (1/28/2015)
            if ($stage->getCurrentStage()<0){
                $stage->moveToNextStage();
						}
            else
            {
                $base->setStageID($stage->getCurrentStage());
                $base->setMaxTime($stage->getMaxTime());
                $base->setMaxTimeQuestion($stage->getMaxTimeQuestion());

            }

            $page = $stage->getCurrentPage();

            if($page == "index.php"){
                header("Location: $page");
            }else{
                header("Location: instruments/$page");
            }
        }else {
            echo "<body class=\"body\">\n<center>\n<br/><br/>\n";
						echo "<div class=\"panel panel-default\" style=\"width:95%;  margin:auto\">";
					  echo "<div class=\"panel-body\">";
            echo "<table class=body align=center>\n";
            echo "<tr><td align=center>Username/password didn't match or you are not authorized to access this.</td></tr>\n";
            echo "</table>\n";
        }

	}
	else
	{
		if (!Base::getInstance()->isSessionActive())
		{
            if(!$closed){
?>
	<html>
		<head>
			<title>Login</title>
			<link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">
			<link rel="stylesheet" href="study_styles/custom/text.css">
			<link rel="stylesheet" href="styles.css">


		</head>
        <script type="text/javascript" src="instruments/js/checkExtension.js"></script>
        <script type="text/javascript" src="instruments/js/get_browser.js"></script>
		<script type="text/javascript">

            var is_ff;


            function loadFailureText(){
                var e_div = document.getElementById("error_div");
                e_div.style.display = "block";
                var login_div = document.getElementById("login_div");
                login_div.style.display= "none";
                if(!is_ff){
                    document.getElementById("ff_div").style.display = "block";
                }else{
                    document.getElementById("ff_div").style.display = "none";
                }
            }

            function preLoginValidation(){
//                is_ff = isFirefox();
//                var f = function() {};
//                checkExtension(f,loadFailureText);

            }


			function validate(form)
			{
		          //Capturing local time
		          var currentTime = new Date();
		          var month = currentTime.getMonth() + 1;
		          var day = currentTime.getDate();
		          var year = currentTime.getFullYear();
		          var localDate = year + "/" + month + "/" + day;
		          var hours = currentTime.getHours();
		          var minutes = currentTime.getMinutes();
		          var seconds = currentTime.getSeconds();
		          var localTime = hours + ":" + minutes + ":" + seconds;
		          var localTimestamp = currentTime.getTime();

		          document.getElementById("localTimestamp").value = localTimestamp;
		          document.getElementById("localDate").value = localDate;
		          document.getElementById("localTime").value = localTime;

		          return true;
			}
		</script>

	<!--</html>-->
	<body class="body" onload="preLoginValidation()">
		<div class="panel panel-default" style="width:95%;  margin:auto">
	    <div class="panel-body">
    <div id="error_div" style="display:none;">
    <p>We have detected the following error(s) when you attempted to access our system.</p>
    <ul>
        <div id="ff_div" style="display:none;">
        <li>Firefox is not your current browser.</li>
        </div>
        <li >You have not installed our plugin.</li>
    </ul>
    <p>You must correct the error(s) before logging into our system.  Please refer to the registration e-mail you received for instructions.</p>
    </div>
    <div id="login_div" style="display:block;">

	<table class="body" width="90%">
<tr><th><h2>Research Study: Log In</h2></th></tr>
		<tr><td><hr/></td></tr>
		<tr>
			<td>
				<p>
					Thank you for your participation in this study!
				</p>
				<p>
					Please <strong>log in</strong> to the system using the username and password you were given.
				</p>
			</td>
		</tr>
		<tr><td><hr/></td></tr>
	</table>
<?php


			//echo "<body>\n<center>\n<br/><br/>\n";
			echo "<center>\n\n";
			echo "<form class=\"pure-form\" id=\"login_form\" action=\"index.php\" method=\"post\" onsubmit=\"return validate(this)\">\n";
			echo "<br/><br/>\n<table class=body>\n";
            echo "<tr><td>Username:</td><td>&nbsp;&nbsp; <input type=\"text\" name=\"userName\" placeholder=\"Username\" size=20 /></td></tr>\n";
            echo "<tr><td>Password:</td><td>&nbsp;&nbsp; <input type=\"password\" name=\"password\" placeholder=\"Password\" size=20 /></td></tr>\n";

//			echo "<tr><td>Username</td><td>&nbsp;&nbsp; <input type=\"text\" name=\"userName\" size=20 /></td></tr>\n";
//			echo "<tr><td>Password</td><td>&nbsp;&nbsp; <input type=\"password\" name=\"password\" size=20 /></td></tr>\n";
			echo "<tr><td colspan=\"2\"><br/></td></tr>\n";
			echo "<tr><td colspan=\"2\" align=center><input type=\"hidden\" id=\"localTimestamp\" name=\"localTimestamp\" value=\"\"/><input type=\"hidden\" id=\"localTime\" name=\"localTime\" value=\"\"/><input type=\"hidden\" id=\"localDate\" name=\"localDate\" value=\"\"/><button type=\"submit\" class=\"btn btn-primary\" >Submit</button></td></tr>\n";
			echo "</table>\n";
			echo "</form>\n";
            echo "</div>\n";

			echo "</div></div></body></html>";

            }else{
                echo "<body class=\"body\">\n<center>\n<br/><br/>\n";
								echo "<div class=\"panel panel-default\" style=\"width:95%;  margin:auto\">";
							  echo "<div class=\"panel-body\">";
				echo "<table class=body align=center>\n";
				echo "<tr><td align=center>Our study is currently closed at this time, and we are currently not accepting new recruits. We apologize for any inconvenience.</td></tr>\n";
				echo "</table></div></div></body>\n";
            }
		}
		else
		{
			$stage = new Stage();
			$page = $stage->getCurrentPage();
            if($page == "index.php"){
                header("Location: $page");
            }else{
                header("Location: instruments/$page");
            }

		}
	}
?>
