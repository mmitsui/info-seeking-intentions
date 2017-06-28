<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
    require_once('../core/Stage.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');

	Util::getInstance()->checkSession();
	$base = new Base();


	/*

     ---Find the  the user's topic area---

     SELECT u.userID, u.topicAreaID, u.sessionID
     FROM user_session_topic u
     WHERE u.userID='$userID' AND u.sessionID='$sessionID'
     --Then in where clause of retrieving the question form questions study have---
     WHERE topicAreaID checked

     */
	// echo "Hello World";
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{

		//$base = new Base();
		$projectID = $base->getProjectID();
		$userID = $base->getUserID();
		$time = $base->getTime();
		$date = $base->getDate();
		$timestamp = $base->getTimestamp();
		$stageID = $base->getStageID();
		$questionID = $base->getQuestionID();
		$collaborativeStudy = $base->getStudyID();


		$sessionID=1;

        $qQuery = "SELECT numUsers from users WHERE userID='$userID'";
        $connection = Connection::getInstance();
        $results = $connection->commit($qQuery);
        $line = mysql_fetch_array($results,MYSQL_ASSOC);
        $num_users = $line['numUsers'];


        $qQuery = "SELECT userID, topicAreaID, sessionID
        FROM user_session_topic
        WHERE userID='$userID' AND sessionID='$sessionID'";




        $connection = Connection::getInstance();
        $results = $connection->commit($qQuery);
        $numRows = mysql_num_rows($results);

        $topicAreaID = -1;
        if($numRows>0)
        {
            $line = mysql_fetch_array($results, MYSQL_ASSOC);
            $topicAreaID = $line['topicAreaID'];
        }

		if (isset($_POST['pretask']))
		{

            $localDate = $_POST['localDate'];
            $localTime = $_POST['localTime'];
            $localTimestamp = $_POST['localTimestamp'];
            Util::getInstance()->saveActionWithLocalTime("Question Progress: Clicked Start",$qProgressID,$base,$localTime,$localDate,$localTimestamp);

			$base->setAllowBrowsing(0);
			$base->setAllowBrowsing(1);
			$_SESSION['refreshQuestionSidebar'] = 1; //Experimental variable
            header("Location: about:blank");

		}
        //		else if (isset($_POST['posttask']))
        else if (isset($_GET['answer']))
		{

            $query = 'SELECT value FROM actions WHERE projectID='.$projectID.' AND action="Question Progress: Start" ORDER BY timestamp DESC LIMIT 1';
            $connection = Connection::getInstance();
            $results = $connection->commit($query);
            $line = mysql_fetch_array($results, MYSQL_ASSOC);

            $qProgressID = $line['value'];
            $answer_txt='';




            //			if (!isset($_POST['answer_hid']))
            //			{
            $query = "UPDATE questions_progress
            SET endDate='$date',
            endTimestamp='$timestamp',
            endTime='$time',
            answer='$answer_txt',
            responses = responses+1
            WHERE qProgressID='$qProgressID'";

            $connection = Connection::getInstance();
            $results = $connection->commit($query);

            //Save action
            Util::getInstance()->saveAction("Question Progress: Answer",$qProgressID,$base);
            //				Util::getInstance()->saveActionWithLocalTime("Question Progress: Answer",$qProgressID,$base,$localTime,$localDate,$localTimestamp);
            //			}

			//Next stage
			Util::getInstance()->moveToNextStage();
		}
		else
		{
            //Retrieve from DB task and set starting time
            $query = "SELECT min(timestamp) min_timestamp
            FROM session_progress
            WHERE stageID = '".$base->getStageID()."' and projectID = '".$base->getProjectID()."'";


            //Util::getInstance()->saveAction('min time stamp query',0,$base);



            $connection = Connection::getInstance();
            $results = $connection->commit($query);
            $line = mysql_fetch_array($results, MYSQL_ASSOC);
            $limit = $base->getMaxTime();

            if ($line['min_timestamp']<>'')
            {
                //Util::getInstance()->saveAction('min time stamp query inside IF',0,$base);

                $base->setTaskStartTimestamp($line['min_timestamp']);
            }

            if ($base->isTaskActive())
            {
                $question = "";
                $questionID = "";
                $answer = "";
                $altAnswer = "";

                //SELECT QUESTION IF THERE IS ONE OPEN
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

//                    if($topicAreaID==0){
//                        $query = "SELECT warmupItemID1,warmupItemID2 FROM users WHERE userID='$userID'";
//                        $results = $connection->commit($query);
//                        $line = mysql_fetch_array($results, MYSQL_ASSOC);
//                        $id1 = $line['warmupItemID1'];
//                        $id2 = $line['warmupItemID2'];
//                        $xstring='';
//                        $ystring='';
//
//                        $query = "SELECT * FROM warmup_countries WHERE groupID='1' AND groupItemID='$id1'";
//                        $results = $connection->commit($query);
//                        $line = mysql_fetch_array($results, MYSQL_ASSOC);
//                        $xstring=$line['countryName'];
//
//                        $query = "SELECT * FROM warmup_countries WHERE groupID='2' AND groupItemID='$id2'";
//                        $results = $connection->commit($query);
//                        $line = mysql_fetch_array($results, MYSQL_ASSOC);
//                        $ystring=$line['countryName'];
//
//                        $question = str_replace("[XXX]",$xstring,$question);
//                        $question = str_replace("[YYY]",$ystring,$question);
//                    }

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
//
//
//                        if($topicAreaID==0){
//                            $query = "SELECT warmupItemID1,warmupItemID2 FROM users WHERE userID='$userID'";
//                            $results = $connection->commit($query);
//                            $line = mysql_fetch_array($results, MYSQL_ASSOC);
//                            $id1 = $line['warmupItemID1'];
//                            $id2 = $line['warmupItemID2'];
//                            $xstring='';
//                            $ystring='';
//
//                            $query = "SELECT * FROM warmup_countries WHERE groupID='1' AND groupItemID='$id1'";
//                            $results = $connection->commit($query);
//                            $line = mysql_fetch_array($results, MYSQL_ASSOC);
//                            $xstring=$line['countryName'];
//
//                            $query = "SELECT * FROM warmup_countries WHERE groupID='2' AND groupItemID='$id2'";
//                            $results = $connection->commit($query);
//                            $line = mysql_fetch_array($results, MYSQL_ASSOC);
//                            $ystring=$line['countryName'];
//
//                            $question = str_replace("[XXX]",$xstring,$question);
//                            $question = str_replace("[YYY]",$ystring,$question);
//                        }



                        $base->setQuestionID($questionID);
                        $base->setQuestion($question);
                        $base->setQuestionStartTimestamp($base->getTimestamp());

                        Util::getInstance()->saveAction('Question Progress: Start',$qProgressID,$base);
                    }
                    else
                    {
                        Util::getInstance()->moveToNextStage();
                    }
                }



                $base->setAllowBrowsing(1);
                //Experimental code: else statement
    ?>

		<html>
		<head>
		<title>Search Task
		</title>
		<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
		<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
		<link rel="stylesheet" href="../study_styles/custom/text.css">
		<link rel="stylesheet" href="../study_styles/custom/background.css">

		</head>
		<script type="text/javascript" src="js/util.js"></script>
		<script type="text/javascript"></script>
		<body class="body">
		<center>
		<br/>
		<table width="90%">
		<tr><th><h2>Task</h2></th></tr>
		<tr align="center"><td>
		</center>
		<p>
		<?php
				//echo $isQuestionInTime."-".$isTaskInTime."-".$isPretaskQuestionnaireComplete."<br />";

				if (1)
				{

						echo "The research topic you entered is:";

				}

				$base = Base::getInstance();
						$connection = Connection::getInstance();
						$userID = $base->getUserID();
						$userID = $base->getProjectID();
						$query = "SELECT Q.question as question FROM recruits R,questions_study Q WHERE R.projectID='$projectID' AND R.userID='$userID' AND R.instructorID+1=Q.questionID ORDER BY recruitsID ASC";
						$results = $connection->commit($query);
						$question1 = '';

						$line = mysql_fetch_array($results,MYSQL_ASSOC);
						$question1 = $line['question'];

				?>


		</p>
		</td></tr>
		<tr><td><hr/></td></tr>
		<tr>
		<td>

		<br />
		<div class="grayrect">
		<span>
			<?php
			//    if($num_users == 2){
			//        echo "<strong><u>Topic 1</u></strong>";
			//        echo "<br><br>";
			//    }
					echo $question1;
			//    if($num_users == 2){
			//        echo "<br><br>";
			//        echo "<strong><u>Topic 2</u></strong>";
			//        echo "<br><br>";
			//        echo $question2;
			//    }
					?>

</span>
</div>
<br />

<hr>

<ul>
<?php
if($num_users>1){
echo "<li>With your partner, search for sources on this topic to use for your research paper.</li>";
}else{
echo "<li>Search for sources on this topic to use for your research paper.</li>";
}
?>
	<li> When conducting this task, use snippets to collect information from web pages, and take notes to help you write your paper. </li>
	<li> You have <strong> 40 minutes </strong> to complete the task.</li>
	<li> Clicking the “Search” button below will open a new tab.</li>
</ul>

</td>
</tr>
<tr><td><hr/></td></tr>
</table>
<br/>
<input type="hidden" name="pretask" value="true"/>
<input type="hidden" name="localTime" value=""/>
<input type="hidden" name="localDate" value=""/>
<input type="hidden" name="localTimestamp" value=""/>
<button class="pure-button pure-button-primary" onClick="window.open('http://libguides.rutgers.edu/content.php?pid=263022&sid=2171678','_blank')">Search</button>
</center>
</body>
</html>
<?php
	}

	}
    }
    ?>
