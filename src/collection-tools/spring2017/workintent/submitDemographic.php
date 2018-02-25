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


	if(
	    isset($_POST['userID']) &&
        isset($_POST['age']) &&
        isset($_POST['gender']) &&
        isset($_POST['work_years']) &&
        isset($_POST['work_role']) &&
        isset($_POST['search_years']) &&
        isset($_POST['device_expertise'])
    ){

        $userID = $_POST['userID'];
        $age = mysql_escape_string($_POST['age']);
        $gender = mysql_escape_string($_POST['gender']);
        $work_years = mysql_escape_string($_POST['work_years']);
        $work_role = mysql_escape_string($_POST['work_role']);
        $search_years = mysql_escape_string($_POST['search_years']);
        $device_expertise = mysql_escape_string($_POST['device_expertise']);
        $query = "INSERT INTO questionnaire_entry_demographic (`userID`,`age`,`gender`,`work_years`,`work_role`,`search_years`,`device_expertise`) VALUES ('$userID','$age','$gender','$work_years','$work_role','$search_years','$device_expertise')";
        $results = $cxn->commit($query);
        $success['success'] = true;
    }else{
        $success['success'] = false;
        $success['message'] = "Your input was not complete.  Please try again.";


    }

    echo json_encode($success);



    ?>
