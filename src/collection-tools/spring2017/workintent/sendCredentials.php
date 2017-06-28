<?php
/*
Simpler login page for workspace. Allows redirect.
Maybe we can use this as a replacement for the sidebar login as well.
*/
	session_start();

    require_once('core/Connection.class.php');
	require_once("core/Base.class.php");
	require_once("core/Util.class.php");
    require_once('../services/utils/loginUtils.php');


	$cxn = Connection::getInstance();

	$success = array();

	$blankusername = isset($_POST['username']) && (trim($_POST['username']) =='');



    //    Check if <username/email,password> exists
	if(isset($_POST['username']) && !$blankusername && filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)){

        $username = trim($_POST['username']);


        $query = "SELECT email1,firstName,lastName FROM recruits WHERE `email1`='$username'";
        $results = $cxn->commit($query);

        if(mysql_num_rows($results) == 0){
            $success['success'] = false;
            $success['errortext'] = "Your e-mail was not found in our records.  If you believe this is in error, please contact us.";
        }else{
            $success['success'] = true;
            $success['errortext'] = "E-mail sent!";

            $subject = "Test Subject";
            $message = "Test Message";
            $headers = "Test Headers";
            mail ($username, $subject, $message, $headers);
            Util::getInstance()->saveAction('send_credentials',0,$base);
        }





    }else if($blankusername){
        $success['success'] = false;
        $success['errortext'] = "E-mail not given";
    }else{
        //Full credentials weren't given
        $success['success'] = false;
        $success['errortext'] = "Proper e-mail not given";
    }

    echo json_encode($success);



    ?>
