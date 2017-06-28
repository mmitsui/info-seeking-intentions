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

	$blankusername = isset($_POST['username']) && (trim($_POST['username']) =='');
    $blankpassword = isset($_POST['password']) && ($_POST['password'] =='');

    //Check if <username/email,password> exists
	if(isset($_POST['username']) && isset($_POST['password']) && !($blankusername || $blankpassword)){
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $password_sha1 = sha1($password);
        $query = "";
	    if(filter_var($username, FILTER_VALIDATE_EMAIL)){
            $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits WHERE `email1`='$username') a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE `password`='$password') b on a.userID=b.userID";
        }else{
	        $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits) a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE `username`='$username' AND `password`='$password') b on a.userID=b.userID";
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
            $base->setFirstName($firstName);
            $base->setLastName($lastName);
            $success['firstName'] = $firstName;
            $success['lastName'] = $lastName;

            Util::getInstance()->saveAction('login',0,$base);
        }


    }else{
        //Full credentials weren't given
        $success['success'] = false;
        $success['errortext'] = "Username and/or password not given";

    }

    echo json_encode($success);



    ?>
