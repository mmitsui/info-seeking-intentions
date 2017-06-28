<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');

	$base = Base::getInstance();

	if ($base->isSessionActive())
	{
        if (isset($_GET['url']))
        {
        	$value = addslashes($_GET['url']);

					$userID = $base->getUserID();
			    $projectID = $_SESSION['CSpace_projectID'];
			    $timestamp = $base->getTimestamp();
			    $date = $base->getDate();
			    $time = $base->getTime();
			    $ip=$_SERVER['REMOTE_ADDR'];

        	Util::getInstance()->saveAction("PHPFreeChat Redirect",$value,$base);
					header("location: $value");
        }
    }
?>
