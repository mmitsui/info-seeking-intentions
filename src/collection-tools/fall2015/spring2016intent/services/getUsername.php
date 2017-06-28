<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');
	require_once('../core/Util.class.php');

    $base = Base::getInstance();

	if (!($base->isSessionActive())) // If not logged in
	{
		echo "";
	}
    // if not logged in end

	else //Currently logged in
	{
		echo $base->getUserID();

		// Save action in Database
	    //    If you want to save the local timestamp.  Just remember to change the GET parameters in the getHome.php call of coagmento.js
	}
?>
