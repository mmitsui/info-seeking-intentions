<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once("pageQueryUtils.php");

function getTaskIDNameMap($userID){
    $query = "SELECT * FROM task_labels_user WHERE userID=$userID AND deleted != 1 ORDER BY taskID ASC";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $taskArray = array();
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $taskArray[$line['taskID']] = $line['taskName'];
    }
    return $taskArray;
}

function addTask($userID,$taskName){
    $query = "SELECT IFNULL(MAX(taskID),0) as maxTaskID FROM task_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    $taskID = $line['maxTaskID']+1;
    $query = "INSERT INTO task_labels_user (`userID`,`projectID`,`taskID`,`taskName`,`deleted`) VALUES ('$userID','$userID','$taskID','$taskName',0)";
    $cxn->commit($query);
}

function getTasks($userID){
    $query = "SELECT * FROM task_labels_user WHERE userID=$userID AND deleted != 1 ORDER BY taskID ASC";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $rows = array();
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
    {
        $rows[] = $row;
    }
    return $rows;
}


function getTasksPanel($userID,$startTimestamp,$endTimestamp){

    $tasks_html =  "<center>";
    $tasks_html .= "<div id='task_buttons'>";
    $tasks = getTasks($userID);
    foreach($tasks as $task){
        $taskID = $task['taskID'];
        $taskName = $task['taskName'];
        $tasks_html .=  "<p><button type=\"button\" id=\"task-button-$taskID\" data-task-id=\"$taskID\" class=\"btn btn-primary btn-block\">$taskName</button></p>";
    }


    $tasks_html .=  "</div>";
    $tasks_html .= "<hr/>";
    $tasks_html .= "<form id=\"add_task_form\" action=\"../services/utils/runPageQueryUtils.php?action=addTask\">";
    $tasks_html .= "<div class=\"form-group\">";


    $tasks_html .= "<div>";
    $tasks_html .= "<p><button type=\"button\" value=\"addtask_button\" class=\"btn btn-success btn-block\">+ Add Task</button></p>";
    $tasks_html .= "</div>";
    $tasks_html .= "<textarea class=\"form-control\" rows=\"1\" id=\"task_name_textfield\" name=\"taskName\"></textarea>";
    $tasks_html .= "<input type=\"hidden\" name=\"userID\" value=\"$userID\"/>";
    $tasks_html .= "<input type=\"hidden\" name=\"startTimestamp\" value=\"$startTimestamp\"/>";
    $tasks_html .= "<input type=\"hidden\" name=\"endTimestamp\" value=\"$endTimestamp\"/>";
    $tasks_html .= "</div>";
    $tasks_html .= "<center><h3 id=\"addtask_confirmation\" class=\"bg-success\"></h3></center>";
    $tasks_html .= "</form>";
    $tasks_html .= "</center>";

    return array('taskshtml'=>$tasks_html);

}

function makeNextSessionID($userID){
    $query = "SELECT IFNULL(MAX(sessionID),0) as maxSessionID FROM session_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    $sessionID = $line['maxSessionID']+1;
    $query = "INSERT INTO session_labels_user (`userID`,`projectID`,`sessionID`,`deleted`) VALUES ('$userID','$userID','$sessionID',0)";
    $cxn->commit($query);
    return $sessionID;
}

function markSessionID($userID,$sessionID,$pageIDs,$queryIDs){
    $cxn = Connection::getInstance();
    if(count($queryIDs) > 0){
        $queryID_list = implode(",",$queryIDs);
        $query = "UPDATE queries SET `sessionID`='$sessionID' WHERE `userID`='$userID' AND `queryID` IN ($queryID_list)";
        $cxn->commit($query);
    }

    if(count($pageIDs) > 0){
        $pageID_list = implode(",",$pageIDs);
        $query = "UPDATE pages SET `sessionID`='$sessionID' WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
        $cxn->commit($query);
    }
}

function getSessionIDs($userID){
    $query = "SELECT * FROM (SELECT sessionID FROM pages WHERE userID=$userID UNION SELECT sessionID FROM queries WHERE userID=$userID) a GROUP BY sessionID";
    $cxn = Connection::getInstance();
    $results = $cxn->commit($query);
    $sessionIDs = array();
    while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
        $sessionIDs[] = $line['sessionID'];
    }



}

function getPagesQueriesForSession($userID,$sessionID){
//    getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0,$sessionID=-1)
}


function markTaskID($userID,$sessionIDs,$taskID){
    $cxn = Connection::getInstance();
    if(count($sessionIDs) > 0){
        $sessionID_list = implode(",",$sessionIDs);
        $query = "UPDATE pages SET `taskID`='$taskID' WHERE `userID`='$userID' AND `sessionID` IN ($sessionID_list)";
        $cxn->commit($query);
        $query = "UPDATE queries SET `taskID`='$taskID' WHERE `userID`='$userID' AND `sessionID` IN ($sessionID_list)";
        $cxn->commit($query);
    }
}
?>
