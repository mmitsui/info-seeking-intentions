<?php
	session_start();
	require_once('../core/Base.class.php');

	$base = Base::getInstance();
//	When time mattered
//	if ($base->getAllowBrowsing()==1)
//	{
//		if (!$base->isTaskInTime())
//			echo "0";
//		else
//			echo "1";
//	}
//	else
//		echo "0";

	if ($base->getAllowBrowsing()==1)
	{
        echo "1";
	}
	else
		echo "0";
?>
