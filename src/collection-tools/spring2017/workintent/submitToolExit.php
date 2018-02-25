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
        isset($_POST['reviewannotation_clear']) &&
        isset($_POST['intentions_understandable']) &&
        isset($_POST['intentions_adequate'])
    ){

        $userID = $_POST['userID'];
        $reviewannotation_clear = $_POST['reviewannotation_clear'];
        $intentions_understandable = $_POST['intentions_understandable'];
        $intentions_adequate = $_POST['intentions_adequate'];


        $query = "INSERT INTO questionnaire_exit_tool (`userID`,`reviewannotation_clear`,`intentions_understandable`,`intentions_adequate`) VALUES ('$userID','$reviewannotation_clear','$intentions_understandable','$intentions_adequate')";
        $results = $cxn->commit($query);
        $success['success'] = true;
    }else{
        $success['success'] = false;
        $success['message'] = "Your input was not complete.  Please try again.";


    }

    echo json_encode($success);



    ?>
