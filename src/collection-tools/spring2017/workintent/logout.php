<?php
/*
Simpler login page for workspace. Allows redirect.
Maybe we can use this as a replacement for the sidebar login as well.
*/
session_start();

require_once('core/Connection.class.php');
require_once("core/Base.class.php");
require_once("core/Util.class.php");

//If logged in, 1) create logout action for userID, destroy session, destroy Base
//Return true in both cases.
$base = Base::getInstance();
Util::getInstance()->saveAction('logout',"browser-".$_POST['browser'],$base);
session_destroy();

echo json_encode(array('success'=>true));



?>
