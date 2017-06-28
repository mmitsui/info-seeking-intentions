<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');

	$base = Base::getInstance();

	if ($base->isSessionActive())
	{
        if (isset($_GET['value']))
        {
        	$action = $_GET['action'];
        	$value = $_GET['value'];
					$userID = $base->getUserID();
			    $projectID = $_SESSION['CSpace_projectID'];
			    $timestamp = $base->getTimestamp();
			    $date = $base->getDate();
			    $time = $base->getTime();
			    $ip=$_SERVER['REMOTE_ADDR'];

        	Util::getInstance()->saveAction($action,$value,$base);
        }
    }
?>
