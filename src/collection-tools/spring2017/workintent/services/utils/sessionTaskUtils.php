<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");

function getTaskIDNameMap($userID){
    $query = "SELECT * FROM task_labels_user WHERE userID=$userID ORDER BY taskID ASC";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $taskArray = array();
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $taskArray[$line['taskID']] = $line['taskName'];
    }
    return $taskArray;
}


?>
