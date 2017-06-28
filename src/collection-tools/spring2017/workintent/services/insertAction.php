<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	require_once('../core/Action.class.php');

	$base = Base::getInstance();
	
	if ($base->isSessionActive())
	{
    if (isset($_GET['value']))
    {
    	$action = $_GET['action'];
    	$value = $_GET['value'];

  		$localTime = $_GET['localTime'];
			$localDate = $_GET['localDate'];
			$localTimestamp = $_GET['localTimestamp'];

 		 	$acc = new Action($action, $value);
			$acc->setLocalDate($localDate);
  		$acc->setLocalTime($localTime);
  		$acc->setLocalTimestamp($localTimestamp);
 			$acc->save();
    }
  }
?>
