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
	if(isset($_POST['userID']) && isset($_POST['researcher_radio'])){
	    $userID = $_POST['userID'];
	    $researcher = $_POST['researcher_radio'];

	    $query = "UPDATE recruits SET `experimenter`='$researcher' WHERE userID=$userID";
        $results = $cxn->commit($query);

        $success['success'] = true;
        $success['researcher'] = $researcher;
        echo json_encode($success);
    }



    ?>
