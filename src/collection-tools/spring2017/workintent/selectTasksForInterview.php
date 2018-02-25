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
        isset($_POST['taskIDs'])

    ){

        $userID = $_POST['userID'];
        $taskIDs_commaseparated = implode(",",$_POST['taskIDs']);

        $query = "UPDATE task_labels_user SET exitinterview=0 WHERE userID=$userID and taskID NOT IN ($taskIDs_commaseparated)";
        $cxn->commit($query);
        $query = "UPDATE task_labels_user SET exitinterview=1 WHERE userID=$userID and taskID IN ($taskIDs_commaseparated)";
        $cxn->commit($query);

        $success['success'] = true;
    }else if(isset($_POST['userID'])){
        $userID = $_POST['userID'];
        $query = "UPDATE task_labels_user SET exitinterview=0 WHERE userID=$userID";
        $cxn->commit($query);
        $success['success'] = true;
    }else{
        $success['success'] = false;
        $success['message'] = "Your input was not complete.  Please try again.";
    }

    echo json_encode($success);



    ?>
