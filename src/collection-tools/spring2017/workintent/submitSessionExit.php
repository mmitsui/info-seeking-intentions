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
        isset($_POST['sessionID']) &&
        isset($_POST['intention_clarifications']) &&
        isset($_POST['intention_transitions'])
//        &&
//        isset($_POST['successful']) &&
//        isset($_POST['successful_description']) &&
//        isset($_POST['useful']) &&
//        isset($_POST['useful_description']) &&
//        isset($_POST['problematic']) &&
//        isset($_POST['problematic_description'])
    ){

        $userID = $_POST['userID'];
        $taskID = $_POST['taskID'];
        $sessionID = $_POST['sessionID'];
//        $successful = $_POST['successful'];
//        $useful = $_POST['useful'];
//        $problematic = $_POST['problematic'];

        $intention_clarifications = mysql_escape_string($_POST['intention_clarifications']);
        $intention_transitions = mysql_escape_string($_POST['intention_transitions']);
//        $successful_description = mysql_escape_string($_POST['successful_description']);
//        $useful_description = mysql_escape_string($_POST['useful_description']);
//        $problematic_description = mysql_escape_string($_POST['problematic_description']);


        $query = "SELECT * FROM questionnaire_exit_sessions WHERE userID='$userID' AND sessionID='$sessionID'";
        $results = $cxn->commit($query);
        if(mysql_num_rows($results) > 0){
            $query = "UPDATE questionnaire_exit_sessions SET `taskID`='$taskID',`intention_clarifications`='$intention_clarifications',`intention_transitions`='$intention_transitions' WHERE userID='$userID' AND sessionID='$sessionID'";
        }else{
            $query = "INSERT INTO questionnaire_exit_sessions (`userID`,`taskID`,`sessionID`,`intention_clarifications`,`intention_transitions`) VALUES ('$userID','$taskID','$sessionID','$intention_clarifications','$intention_transitions')";
        }

        $results = $cxn->commit($query);
        $success['success'] = true;
    }else{
        $success['success'] = false;
        $success['message'] = "Your input was not complete.  Please try again.";


    }

    echo json_encode($success);



    ?>
