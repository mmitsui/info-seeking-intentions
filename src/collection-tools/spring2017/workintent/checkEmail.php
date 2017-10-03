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

$blankusername = isset($_POST['email']) && (trim($_POST['email']) =='');

//Check if <username/email,password> exists
if(isset($_POST['email'])  && !$blankusername){
    $email = trim($_POST['email']);

    //    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
//    $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits WHERE `email1`='$email') a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE `password`='$password') b on a.userID=b.userID";

    $query = "SELECT userID,email1,firstName,lastName FROM recruits WHERE `email1`='$email'";
//    }else{
//        $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits) a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE `username`='$username' AND `password`='$password') b on a.userID=b.userID";
//    }

    $results = $cxn->commit($query);

    if(mysql_num_rows($results)==0){
        $success['success'] = "false";
    }else{
        //Create login action for userID
        $success['success'] = "true";
    }

}else{
    //Full credentials weren't given
    $success['success'] = "false";


}

echo $success['success'];



?>
