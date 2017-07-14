<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");

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
    $query = "SELECT MAX(taskID) as maxTaskID FROM task_labels_user WHERE userID=$userID";
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
    $tasks_html .= "<div>";
    $tasks = getTasks($userID);
    foreach($tasks as $task){
        $taskID = $task['taskID'];
        $taskName = $task['taskName'];
        $tasks_html .=  "<p><button type=\"button\" data-task-id=\"$taskID\" class=\"btn btn-primary btn-block\">$taskName</button></p>";
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

?>
