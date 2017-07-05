<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	require_once('../core/Action.class.php');

	$base = Base::getInstance();

	if ($base->isSessionActive())
	{
		if (isset($_POST['value']))
		{
			$action = $_POST['action'];
			$value = $_POST['value'];
			$localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp = $_POST['localTimestamp'];

			$acc = new Action($action, $value);
			$acc->setLocalDate($localDate);
			$acc->setLocalTime($localTime);
			$acc->setLocalTimestamp($localTimestamp);
	        if(isset($_POST['actionJSON'])){
	        	$acc->setActionJSON($_POST['actionJSON']);
	    	}
			echo json_encode(array('code'=>$acc->save()));
		}
  }
?>
