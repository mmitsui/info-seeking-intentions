<?php
/*
Simpler login page for workspace. Allows redirect.
Maybe we can use this as a replacement for the sidebar login as well.
*/
	session_start();

  require_once('core/Connection.class.php');
	require_once("core/Base.class.php");
	require_once("core/Util.class.php");
	require_once('services/utils/sessionTaskUtils.php');
	$base = Base::getInstance();
	$cxn = Connection::getInstance();

	$success = array();


	if(
	    isset($_POST['userID']) &&
        isset($_POST['description']) &&
        isset($_POST['frequency']) &&
        isset($_POST['familiarity']) &&
        isset($_POST['completiontime']) &&
        isset($_POST['individual_complete']) &&
        isset($_POST['num_collaborators']) &&
        isset($_POST['task_name'])
    ){

        $userID = $_POST['userID'];
        $description = mysql_escape_string($_POST['description']);
        $frequency = mysql_escape_string($_POST['frequency']);
        $familiarity = mysql_escape_string($_POST['familiarity']);
        $completiontime = mysql_escape_string($_POST['completiontime']);
        $work_role = mysql_escape_string($_POST['work_role']);
        $individual_complete = mysql_escape_string($_POST['individual_complete']);
        $num_collaborators = mysql_escape_string($_POST['num_collaborators']);
        $task_name = mysql_escape_string($_POST['task_name']);
        $taskID = addTask_returnabs($userID,$task_name,1);

        $query = "INSERT INTO questionnaire_entry_tasks (`userID`,`task_idcolumn`,`name`,`description`,`frequency`,`familiarity`,`completiontime`,`individual_complete`,`num_collaborators`) VALUES ('$userID','$taskID','$task_name','$description','$frequency','$familiarity','$completiontime','$individual_complete','$num_collaborators')";
        $results = $cxn->commit($query);
        $success['success'] = true;
    }else{
        $success['success'] = false;
        $success['message'] = "Your input was not complete.  Please try again.";


    }

    echo json_encode($success);



    ?>
