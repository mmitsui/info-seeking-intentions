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
        isset($_POST['taskID'])
    ){

        $userID = $_POST['userID'];
        $taskID = $_POST['taskID'];
        $query = "SELECT * FROM questionnaire_exit_tasks WHERE userID=$userID AND taskID=$taskID";
        $results = $cxn->commit($query);
        $success['taskcomplete'] = mysql_num_rows($results) > 0;

        $query = "SELECT * FROM questionnaire_exit_sessions WHERE userID=$userID AND taskID=$taskID GROUP BY sessionID";
        $results = $cxn->commit($query);
        $n_done_sessions = mysql_num_rows($results);
        $query = "SELECT * FROM pages WHERE userID=$userID AND taskID=$taskID AND sessionID IS NOT NULL GROUP BY sessionID";
        $results = $cxn->commit($query);
        $n_total_sessions = mysql_num_rows($results);
        $success['sessionscomplete'] = $n_done_sessions>=$n_total_sessions;
        $success['n_done_sessions'] = $n_done_sessions;
        $success['n_total_sessions'] = $n_total_sessions;

        $success['success'] = true;
    }else{
        $success['success'] = false;
        $success['message'] = "Your input was not complete.  Please try again.";
    }

    echo json_encode($success);



    ?>
