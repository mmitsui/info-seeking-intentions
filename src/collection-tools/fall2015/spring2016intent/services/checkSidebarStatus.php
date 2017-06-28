<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');

	$base = Base::getInstance();
	$stageID = $base->getStageID();
	$cxn = Connection::getInstance();

	$query = "SELECT * FROM session_stages WHERE stageID='$stageID'";
	$results = $cxn->commit($query);

	if (mysql_num_rows($results))
	{
				$line = mysql_fetch_array($results,MYSQL_ASSOC);
        if($line['hideSidebar']==1){
					echo "1";
				}else if($line['hideSidebar']==-1){
					echo "-1";
				}else{
					echo "0";
				}
	}
	else
		echo "0";
?>
