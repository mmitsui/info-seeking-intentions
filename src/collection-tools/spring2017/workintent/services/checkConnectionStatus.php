<?php
	session_start();
	require_once('../core/Base.class.php');

	$base = new Base();
	$stageID = $base->getStageID();

    $stageResponse=1;
//	if ($base->getStageID()<120)
//		$stageResponse = 1;
//	else
//		$stageResponse = 2;

    if (isset($_SESSION['CSpace_userID'])){
			$connected = $stageResponse;
		}else{
			$connected = -1;
		}

    echo "$connected";

?>
