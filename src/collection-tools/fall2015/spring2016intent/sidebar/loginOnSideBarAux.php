<?php
	session_start();

	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	require_once('../sidebar/pubnub-lib/autoloader.php');
	use Pubnub\Pubnub;

	$base = Base::getInstance();
	$base->registerActivity();
	$time = $base->getTime();
	$date = $base->getDate();
	$timestamp = $base->getTimestamp();
	$connection = Connection::getInstance();

	if (isset($_GET['logout']) && $_GET['logout'] =='true') {
		Util::getInstance()->saveAction('logout',0,$base);
		session_destroy();
		// setcookie("CSpace_userID");
?>

<?php
		echo "<table class=\"body\">\n";
		echo "<tr><td colspan=2><font color=\"green\">You have been successfully logged out of your CSpace.</font><br/>Thank you for using Coagmento. See you back soon!</font><br/><br/></td></tr>\n";
		// echo "<tr><td colspan=2>Continue to <a href=\"http://www.coagmento.org\">Coagmento homepage</a>.</td></tr>\n";
	}
	else {
		// If the user tried to login
		if (isset($_POST['userName'])) {
			$userName = $_POST['userName'];
			$password = sha1($_POST['password']);
			$query = "SELECT * FROM users WHERE username='$userName' AND password_sha1='$password' AND status=1";
			$results = $connection->commit($query);




			// If the login information was incorrect
			if (mysql_num_rows($results)==0) {
	?>
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<link rel="Coagmento icon" type="image/x-icon" href="http://www.coagmento.org/spring2016intent/img/favicon.ico">
			<title>Coagmento</title>
			<link rel="stylesheet" href="css/styles.css" type="text/css" />

			</head>
			<body>
			<center>
			<br/>
			<br/>
			<table class="body">
			<tr><td>The login information you entered does not match our records. Please <a href="http://www.coagmento.org/spring2016intent/sidebar/loginOnSideBar.php" style="text-decoration:underline">try again</a>.</td></tr>
      </table>
      </center>
      </body>
      </html>
	<?php
	exit();
}
			else {
				$line = mysql_fetch_array($results, MYSQL_ASSOC);


				$userID = $line['userID'];
				$projectID = $line['projectID'];
				$studyID = $line['study'];

				if(is_null($projectID) || $projectID <= 0){
					?>
																<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
							<link rel="Coagmento icon" type="image/x-icon" href="http://www.coagmento.org/spring2016intent/img/favicon.ico">
							<title>Coagmento</title>
							<link rel="stylesheet" href="css/styles.css" type="text/css" />

							</head>
							<body>
							<center>
							<br/>
							<br/>
							<table class="body">
							<tr><td>It seems you have not yet been assigned to a group in our system.  Please wait until you have been assigned to log in.</td></tr>
																</table>
																</center>
																</body>
																</html>
					<?php
					exit();
				}else{

				}

				$base->setUserName($userName);
				$base->setUserID($userID);
				$base->setProjectID($projectID);
				$base->setStageID(-1);
				$base->setStudyID($studyID);

        $ip=$_SERVER['REMOTE_ADDR'];
				Util::getInstance()->saveAction('login',0,$base);

				//Retrieve from DB task and set starting time
				$query = "SELECT min(timestamp) min_timestamp
				FROM actions
				WHERE stageID = '".$base->getStageID()."' and projectID = '".$base->getProjectID()."' AND action='login'";





				$connection = Connection::getInstance();
				$results = $connection->commit($query);
				$line = mysql_fetch_array($results, MYSQL_ASSOC);
				$limit = $base->getMaxTime();


				if ($line['min_timestamp']<>'')
				{

						$base->setTaskStartTimestamp($line['min_timestamp']);
				}

				if ($base->isTaskActive())
				{

						$topicAreaID=1;

						$q = "SELECT I.instructorID as instructorID FROM recruits R,instructors I WHERE R.userID='$userID' AND R.instructorID=I.instructorID";
						$r = $connection->commit($q);
						$l = mysql_fetch_array($r, MYSQL_ASSOC);
						$topicAreaID=$l['instructorID'];


						$question = "";
						$questionID = "";
						$answer = "";
						$altAnswer = "";

						//SELECT QUESTION IF THERE IS ONE OPEN
						$collaborativeStudy = 1;
						$query = "SELECT qProgressID, questionID, startTimestamp
						FROM questions_progress
						WHERE stageID = '".$base->getStageID()."' and projectID = '".$base->getProjectID()."' and (endTimestamp IS NULL OR responses<2) and skip<>$collaborativeStudy";

						$connection = Connection::getInstance();
						$results = $connection->commit($query);
						$numRows = mysql_num_rows($results);
						$qProgressID = 0;

						//IF QUESTION OPEN EXIST
						if ($numRows>0)
						{
								$line = mysql_fetch_array($results, MYSQL_ASSOC);
								$qProgressID = $line['qProgressID'];
								$questionID = $line['questionID'];
								$questionStartingTimestamp = $line['startTimestamp'];

								//Retrieve question
								$qQuery = "SELECT question, answer, altAnswer
								FROM questions_study
								WHERE questionID = '$questionID'
								AND topicAreaID = $topicAreaID"; //Added topic area ID

								$connection = Connection::getInstance();
								$results = $connection->commit($qQuery);

								$line = mysql_fetch_array($results, MYSQL_ASSOC);
								$question = $line['question'];
								$answer = $line['answer'];
								$altAnswer = $line['altAnswer'];

								$base->setQuestionID($questionID);
								$base->setQuestion($question);
								$base->setQuestionStartTimestamp($questionStartingTimestamp);

								Util::getInstance()->saveAction('Question Progress: Revisit',$qProgressID,$base);
						}
						else //IF PREVIOUS QUESTION WAS RESPONDED OR NO QUESTIONS HAS BEEN RESPONDED
						{
								//echo "NEW QUESTION<br />";
								//Retrieve new question
								//Added topic area ID

								$qQuery = "SELECT questionID, question, answer, altAnswer
								FROM questions_study
								WHERE stageID='".$base->getStageID()."'
								AND topicAreaID = $topicAreaID
								AND NOT questionID in (SELECT questionID FROM questions_progress WHERE stageID = '".$base->getStageID()."' and projectID = '".$base->getProjectID()."')
								AND NOT `order` is NULL
								ORDER BY `order` ASC
								LIMIT 1";

								$connection = Connection::getInstance();
								$results = $connection->commit($qQuery);
								$line = mysql_fetch_array($results, MYSQL_ASSOC);
								$numRows = mysql_num_rows($results);

								if ($numRows>0)
								{
										$questionID = $line['questionID'];
										$question = $line['question'];
										$answer = $line['answer'];
										$altAnswer = $line['altAnswer'];

										$qQuery = "INSERT INTO questions_progress (userID, projectID, stageID, questionID, startDate, startTime, startTimestamp)
										VALUES ('".$base->getUserID()."','".$base->getProjectID()."','".$base->getStageID()."','$questionID','".$base->getDate()."','".$base->getTime()."','".$base->getTimestamp()."')";

										$connection = Connection::getInstance();
										$results = $connection->commit($qQuery);
										$qProgressID = $connection->getLastID();




										$base->setQuestionID($questionID);
										$base->setQuestion($question);
										$base->setQuestionStartTimestamp($base->getTimestamp());

										Util::getInstance()->saveAction('Question Progress: Start',$qProgressID,$base);
								}
						}
				}


				// setcookie("CSpace_userID", $userID);
				$base->setAllowCommunication(1);
				$base->setAllowBrowsing(1);


				// $_SESSION['refreshQuestionSidebar'] = 1;
				$base->setUserName($userName);
				$base->setUserID($userID);
				$base->setProjectID($projectID);
				$base->setStageID(-1);
				$base->setStudyID($studyID);


				$pubnub = new Pubnub(array('publish_key'=>'pub-c-0ee3d3d2-e144-4fab-bb9c-82d9be5c13f1','subscribe_key'=>'sub-c-ac9b4e84-b567-11e4-bdc7-02ee2ddab7fe'));
				$query = "SELECT userID from users WHERE projectID='$userID'";
				$results = $connection->commit($query);
				$lineBroadcast = mysql_fetch_array($results,MYSQL_ASSOC);
				//$userIDBroadcast = $lineBroadcast['userID'];
				$userIDBroadcast = $userID;
				$message = array('message'=>'1');
				$res=$pubnub->publish("spr15-".$base->getStageID()."-".$base->getProjectID()."-checkStage".$userIDBroadcast,$message);
				// $_SESSION['login_first'] = true;
				header("Location: sidebar.php");
				// exit();
			}
		}
	}
	?>
