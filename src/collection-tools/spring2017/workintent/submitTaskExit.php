<?php
/*
Simpler login page for workspace. Allows redirect.
Maybe we can use this as a replacement for the sidebar login as well.
*/
	session_start();

    header("Access-Control-Allow-Origin: " . "*");

  require_once('core/Connection.class.php');
	require_once("core/Base.class.php");
	require_once("core/Util.class.php");
	$base = Base::getInstance();
	$cxn = Connection::getInstance();

	$success = array();


	if(
	    isset($_POST['userID']) &&
        isset($_POST['taskID']) &&
        isset($_POST['task_accomplishment']) &&
        isset($_POST['task_stage']) &&
        isset($_POST['goal']) &&
        isset($_POST['importance']) &&
        isset($_POST['urgency']) &&
        isset($_POST['difficulty']) &&
        isset($_POST['complexity']) &&
        isset($_POST['knowledge_topic']) &&
        isset($_POST['knowledge_procedures'])
    ){

        $userID = $_POST['userID'];
        $taskID = $_POST['taskID'];
        $task_accomplishment = mysql_escape_string($_POST['task_accomplishment']);
        $task_stage = $_POST['task_stage'];
        $goal = $_POST['goal'];
        $importance = $_POST['importance'];
        $urgency = $_POST['urgency'];
        $difficulty = $_POST['difficulty'];
        $complexity = $_POST['complexity'];
        $knowledge_topic = $_POST['knowledge_topic'];
        $knowledge_procedures = $_POST['knowledge_procedures'];

        $query = "INSERT INTO questionnaire_exit_tasks (`userID`,`taskID`,`task_accomplishment`,`task_stage`,`goal`,`importance`,`urgency`,`difficulty`,`complexity`,`knowledge_topic`,`knowledge_procedures`) VALUES ('$userID','$taskID','$task_accomplishment','$task_stage','$goal','$importance','$urgency','$difficulty','$complexity','$knowledge_topic','$knowledge_procedures')";
        $results = $cxn->commit($query);
        $success['success'] = true;
    }else{
        $success['success'] = false;
        $success['message'] = "Your input was not complete.  Please try again.";


    }

    echo json_encode($success);



    ?>
