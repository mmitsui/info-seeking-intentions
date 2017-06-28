<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');

	$base = Base::getInstance();


// 2/14/15: 'refreshsidebar' never activated, so "4" never echoed
//  Same for "5"
	/*----- Chat communication is not required since this is for single participant------*/

	$allowCommunication = $base->getAllowCommunication();
	if ($allowCommunication==1)
	{
	  //Experimental: VERY RISKY ECHO!  Don't know if this echo "5" will work...
	  if(isset($_SESSION['refreshQuestionSidebar']) && ($_SESSION['refreshQuestionSidebar']) == 1){
	  	$_SESSION['refreshQuestionSidebar'] = 0;
	  	echo "5";
	  }
	  else if (isset($_SESSION['refreshSidebar']) && ($_SESSION['refreshSidebar']) == 1)
	  {
			$_SESSION['refreshSidebar'] = 0;
			echo "4";
	  }
//	  else if (!$base->isTaskInTime())
//	  {
//		echo "2";
//	  }
	  else
	  {
			if ($base->getAllowBrowsing()==1)
				echo "1";
			else
				echo "0";
	  //   $query = "select 1 from questions_progress
		// 		  where stageID = '".$base->getStageID()."'
  	// 			    and projectID = '".$base->getProjectID()."'
  	// 				and responses > 0
  	// 				and questionID = '".$base->getQuestionID()."'";
		//
		// $connection = Connection::getInstance();
		// $results = $connection->commit($query);
		// $numRows = mysql_num_rows($results);


		// if ($numRows>0) //Someone already responded
		// {
		// 	echo "2";
		// }
		// else
		// if ($base->getStudyID()==1)
		// {
		// 	if ($base->getAllowBrowsing()==1)
		// 		echo "1";
		// 	else
		// 		echo "0";
		// }
		// else
		// {
		// 	if ($base->getStageID()==170)
		// 	{
		// 		if ($_SESSION['syncQuestion']==0)
		// 			echo "0";
		// 		else
		// 			if ($base->getAllowBrowsing()==1)
		// 				echo "1";
		// 			else
		// 				echo "2";
		// 	}
		// 	else
		// 		if ($base->getAllowBrowsing()==1)
		// 			echo "1";
		// 		else
		// 			echo "2";
		// }
		//}
		//else
		//	echo "0";
		}
	}
	else
		echo "3";
	//echo "2";

	// if ($base->getStudyID()==1)
	// {
	// 	$query = "select 1 from questions_progress
	// 			  where stageID = '".$base->getStageID()."'
  // 				    and projectID = '".$base->getProjectID()."'
  // 					and responses = 1
  // 					and questionID = '".$base->getQuestionID()."'";
	//
	// 	$connection = Connection::getInstance();
	// 	$results = $connection->commit($query);
	// 	$numRows = mysql_num_rows($results);
	//
	//
	//
	// 	if($numRows==1)
	// 	{
	// 		//refresh side bar in sidebar.php
	// 	}
	// 	else if($numRows==0)
	// 	{
	// 		// show side bar (echo "1")
	// 	}
	// 	/*
	// 	if ((($base->getStageID()==50)||($base->getStageID()==70)||($base->getStageID()==150)||($base->getStageID()==170))&&($base->getStudyID()>1))
	// 		echo "3";
	// 	else
	// 	if (($numRows>0)&&($base->getStageID()==100))
	// 	{
	// 		echo "2";
	// 	}
	// 	else
	// 		if ($base->getAllowBrowsing()==1)
	// 			echo "1";
	// 		else
	// 			echo "0";
	// 	*/
	// }


	/*
	else
	{
		if ((($base->getStageID()==50)||($base->getStageID()==70)||($base->getStageID()==150)||($base->getStageID()==170))&&($base->getStudyID()>1))
			echo "3";
		else
			if ($base->getAllowBrowsing()==1)
				echo "1";
			else
				echo "0";
	}
	*/
?>
