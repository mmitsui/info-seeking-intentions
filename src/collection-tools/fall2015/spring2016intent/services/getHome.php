<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');
	require_once('../core/Util.class.php');

    $base = Base::getInstance();

	if (!($base->isSessionActive())) // If not logged in
	{
		header("Location: ../index.php");
	}
    // if not logged in end

	else //Currently logged in
	{
		$stageID = $base->getStageID();
		// Save action in Database
	    //    If you want to save the local timestamp.  Just remember to change the GET parameters in the getHome.php call of coagmento.js
		Util::getInstance()->saveAction("Clicked Get Home",0, $base);

		header("Location: ../index.php");
	}
?>
