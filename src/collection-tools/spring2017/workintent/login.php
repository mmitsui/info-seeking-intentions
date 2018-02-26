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


	function getTimezone($userID){
	    $query = "SELECT * FROM recruits WHERE userID=$userID";
        $cxn = Connection::getInstance();
        $result = $cxn->commit($query);
        $line = mysql_fetch_array($result,MYSQL_ASSOC);
        return $line['timezone'];
    }

	$blankusername = isset($_POST['username_sha1']) && (trim($_POST['username_sha1']) =='' || trim($_POST['username_sha1']) =='da39a3ee5e6b4b0d3255bfef95601890afd80709');
    $blankpassword = isset($_POST['password_sha1']) && (trim($_POST['password_sha1']) =='' || trim($_POST['password_sha1']) =='da39a3ee5e6b4b0d3255bfef95601890afd80709');

    //Check if <username/email,password> exists
	if(isset($_POST['username_sha1']) && isset($_POST['password_sha1']) && !($blankusername || $blankpassword)){
        $username = trim($_POST['username_sha1']);
        $password = $_POST['password_sha1'];
        $extensionID = $_POST['extensionID'];
        $password_sha1 = sha1($password);
        $query = "";
	    if(filter_var($username, FILTER_VALIDATE_EMAIL)){
            $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits WHERE `email1`='$username') a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE `password_sha1`='$password') b on a.userID=b.userID";
        }else{
	        $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits) a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE `username_sha1`='$username' AND `password_sha1`='$password') b on a.userID=b.userID";
        }


        $results = $cxn->commit($query);

	    if(mysql_num_rows($results)==0){
            //Does not exist
            $success['success'] = false;
            $success['errortext'] = "Given username and/or password are invalid";
        }else{
            //Create login action for userID
            $success['success'] = true;
            $row = mysql_fetch_array($results,MYSQL_ASSOC);

            $userID = $row['userID'];
            $firstName = $row['firstName'];
            $lastName = $row['lastName'];



            $base = Base::getInstance();
            $base->setUserID($userID);
            $base->setUserTimezone(getTimezone($userID));
            $base->setFirstName($firstName);
            $base->setLastName($lastName);

            $extensionID = $_POST['extensionID'];
            $query = "UPDATE users SET extensionID=$extensionID WHERE userID=$userID";
            $success['firstName'] = $firstName;
            $success['lastName'] = $lastName;

            Util::getInstance()->saveAction('login',"browser-".$_POST['browser'],$base);
        }


    }else if(isset($_POST['extensionID']) && (trim($_POST['extensionID']) !='')){
	    $extensionID = $_POST['extensionID'];
        $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits) a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE extensionID='$extensionID') b on a.userID=b.userID";
        $results = $cxn->commit($query);


        if(mysql_num_rows($results)==0){
            //Does not exist
            $success['success'] = false;
            $success['errortext'] = "Automatic login did not work.  Try logging in with your username/password.";
        }else{
            //Create login action for userID
            $success['success'] = true;
            $row = mysql_fetch_array($results,MYSQL_ASSOC);

            $userID = $row['userID'];
            $firstName = $row['firstName'];
            $lastName = $row['lastName'];

            $base = Base::getInstance();
            $base->setUserID($userID);
            $base->setUserTimezone(getTimezone($userID));
            $base->setFirstName($firstName);
            $base->setLastName($lastName);
            $success['firstName'] = $firstName;
            $success['lastName'] = $lastName;

            Util::getInstance()->saveAction('login',"browser-".$_POST['browser'],$base);
        }


    }else{
        //Full credentials weren't given
        $success['success'] = false;
        $success['errortext'] = "Username and/or password not given";

    }

    echo json_encode($success);



    ?>
