<?php
	session_start();
    require_once('../../core/Connection.class.php');
    require_once('../../core/Base.class.php');
    require_once('../../core/Util.class.php');

	if ((isset($_SESSION['CSpace_userID']))) {
		$type = $_GET['type'];
		$itemID = $_GET['itemID'];
		$base = Base::getInstance();
        $userID = $base->getUserID();
        $projectID = $base->getProjectID();
        $query1 = "";
        $connection = Connection::getInstance();


        //It can be reduced to one line using the variable type to refer to each table, but just to make clear this it is presented with a condition.
        if ($type=="snippets")
            $query1 = "UPDATE snippets SET `status`='0' WHERE `snippetID`='$itemID' AND `userID`='$userID' AND `projectID`='$projectID'";
        else if ($type=="pages")
            $query1 = "UPDATE pages SET `result`='0' WHERE `pageID`='$itemID' AND `userID`='$userID' AND `projectID`='$projectID'";
        else if ($type=="queries")
            $query1 = "UPDATE queries SET `status`='0' WHERE `queryID`='$itemID' AND `userID`='$userID' AND `projectID`='$projectID'";
        else if ($type=="files")
            $query1 = "UPDATE files SET `status`='0' WHERE `id`='$itemID' AND `userID`='$userID' AND `projectID`='$projectID'";
        else if ($type=="bookmarks")
            $query1 = "UPDATE bookmarks SET `status`='0' WHERE `bookmarkID`='$itemID' AND `userID`='$userID' AND `projectID`='$projectID'";

        if ($query1 != "")
        {
            $results = $connection->commit($query1);
            $timestamp = $base->getTimestamp();
            $date = $base->getDate();
            $time = $base->getTime();

            $webPage = $_GET['webPage'];
            $ip=$_SERVER['REMOTE_ADDR'];
						Util::getInstance()->saveAction("delete_$type", "$itemID",$base);
            // $aquery = "INSERT INTO actions (userID, projectID, timestamp, date, time, action, value, ip) VALUES ('$userID', '$projectID', '$timestamp', '$date', '$time', 'delete_$type', '$itemID','$ip')";
            // $results = $connection->commit($aquery);
            if ($webPage!="")
                require_once($webPage);
        }
	}
?>
