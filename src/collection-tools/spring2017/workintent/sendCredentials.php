<?php
/*
Simpler login page for workspace. Allows redirect.
Maybe we can use this as a replacement for the sidebar login as well.
*/
	session_start();

    require_once('core/Connection.class.php');
	require_once("core/Base.class.php");
	require_once("core/Util.class.php");
    require_once('./services/utils/loginUtils.php');


	$cxn = Connection::getInstance();
	$base = Base::getInstance();

	$success = array();

	$blankusername = isset($_POST['username_sha1']) && (trim($_POST['username_sha1']) =='' || trim($_POST['username_sha1']) =='da39a3ee5e6b4b0d3255bfef95601890afd80709');



    //    Check if <username/email,password> exists
	if(isset($_POST['username_sha1']) && !$blankusername){

        $email_sha1 = trim($_POST['username_sha1']);


        $query = "SELECT * FROM recruits WHERE `email_sha1`='$email_sha1'";
        $results = $cxn->commit($query);

        if(mysql_num_rows($results) == 0){
            $success['success'] = false;
            $success['errortext'] = "Your e-mail was not found in our records.  If you believe this is in error, please contact us.";
        }else{
            $line = mysql_fetch_array($results,MYSQL_ASSOC);
            $firstname = $line['firstName'];
            $lastname = $line['lastName'];
            $email = $line['email1'];
            $userID = $line['userID'];
            $query = "SELECT * FROM users WHERE userID=$userID";
            $results = $cxn->commit($query);
            $line = mysql_fetch_array($results,MYSQL_ASSOC);
            $username = $line['username'];
            $password = $line['password'];


            $success['success'] = true;
            $success['errortext'] = "E-mail sent!";

            $subject = "Password confirmation for Search intentions in natural settings";
            $message = "
            
            Hello $firstname $lastname,<br/><br/>
You are receiving this e-mail because you asked for a reminder of your username and password. They are contained below.  Please save these for your records.<br/><br/>

Username: $username<br/>
Password: $password

            ";
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: Information Seeking Intentions <mmitsui@scarletmail.rutgers.edu>' . "\r\n";
            $headers .= 'Bcc: Matthew Mitsui <mmitsui@scarletmail.rutgers.edu>' . "\r\n";
            mail ($email, $subject, $message, $headers);
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
