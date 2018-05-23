<?php
/*
Simpler login page for workspace. Allows redirect.
Maybe we can use this as a replacement for the sidebar login as well.
*/
	session_start();

    require_once('core/Connection.class.php');
	require_once("core/Base.class.php");
	require_once("core/Util.class.php");

	$base = Base::getInstance();
	$cxn = Connection::getInstance();
	$success = array();




    //Check if <username/email,password> exists
	if(isset($_POST['userID']) && isset($_POST['interviewtype'])){
	    $userID = $_POST['userID'];
	    $interviewtype = $_POST['interviewtype'];

	    $folder = "";
        if($interviewtype=='entry'){
            $folder='Entry Interviews';
        }else if($interviewtype=='exit'){
            $folder='Exit Interviews';
        }

    }else{
        $success['success'] = false;
        $success['message'] = "";
        echo json_encode($success);
	}



    ?>
