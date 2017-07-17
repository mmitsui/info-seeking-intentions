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


function getMarkTasksPanels($userID,$startTimestamp,$endTimestamp){
    $taskIDNameMap = getTaskIDNameMap($userID);
    $sessionIDs = getSessionIDs($userID,$startTimestamp,$endTimestamp);
    $session_panels = array();
    $session_panels_html = "";
    if(count($sessionIDs)==0 or (count($sessionIDs)==1 and in_array(null,$sessionIDs))){
        $session_panels_html = "<center><h3 class='bg-danger'>You have marked no sessions. Please go back and mark some</h3></center>";
    }
    else{
        foreach($sessionIDs as $sessionID){
            if(!is_null($sessionID)){
                $session_panels[$sessionID] = "<div class=\"panel panel-primary\">\n";
                $session_panels[$sessionID] .= "<div class=\"panel-heading\">\n";
                $session_panels[$sessionID] .= "<center>\n";
                $session_panels[$sessionID] .= "<input type=\"checkbox\" name=\"sessionIDs[]\" id=\"sessionID_checkbox_$sessionID\" value=\"$sessionID\">\n";
                $session_panels[$sessionID] .= "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#session_panel_$sessionID\">Session $sessionID (Show/Hide)</button>\n";
                $session_panels[$sessionID] .= "</center>\n";
                $session_panels[$sessionID] .= "</div>\n";
                $session_panels[$sessionID] .= "<form id=\"task_form_$sessionID\" action=\"../services/utils/runPageQueryUtils.php?action=markTask\">\n";
                $session_panels[$sessionID] .= "<div class=\"panel-body collapse\" id=\"session_panel_$sessionID\">\n";
                $session_panels[$sessionID] .= "<div class=\"tab-pane\">\n";
                $session_panels[$sessionID] .= "<table class=\"table table-striped table-fixed \">\n";
                $session_panels[$sessionID] .= "<thead>\n";
                $session_panels[$sessionID] .= "<tr>\n";
                $session_panels[$sessionID] .= "<th >Time</th>\n";
                $session_panels[$sessionID] .= "<th >Type</th>\n";
                $session_panels[$sessionID] .= "<th >Task</th>\n";
                $session_panels[$sessionID] .= "<th >Title/Query</th>\n";
                $session_panels[$sessionID] .= "<th >URL</th>\n";
                $session_panels[$sessionID] .= "</tr>\n";
                $session_panels[$sessionID] .= "</thead>\n";
                $session_panels[$sessionID] .= "<tbody>\n";

                $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,$sessionID);

                $pages =$pagesQueries;
//            echo "SESSIONID".$sessionID."COUNT".count($pages);
                foreach($pages as $page) {
                    $session_panels[$sessionID] .= "<tr >\n";
                    $session_panels[$sessionID] .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";

                    $color = '';
                    if($page['type']=='page'){
                        $color = 'class="warning"';
                    }else{
                        $color = 'class="info"';
                    }

                    $session_panels[$sessionID] .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
                    $session_panels[$sessionID] .= "<td >".(isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"")."</td>\n";
                    $session_panels[$sessionID] .= "<td>".(isset($page['title'])?substr($page['title'],0,60)."...":"")."</td>";
                    $session_panels[$sessionID] .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
                    $session_panels[$sessionID] .= "</tr>\n";
                }
                $session_panels[$sessionID] .= "</tbody>\n";
                $session_panels[$sessionID] .= "</table>\n";
                $session_panels[$sessionID] .= "</div>\n";
                $session_panels[$sessionID] .= "</div>\n";
                $session_panels[$sessionID] .= "</div>\n";
            }

        }


        foreach($sessionIDs as $sessionID){
            if(!is_null($sessionID)) {
                $session_panels_html .= $session_panels[$sessionID];
            }
        }

    }




    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,NULL);
    $pages =$pagesQueries;
    if(count($pages) > 0) {
        $null_panel = "<div class=\"row\">";
        $null_panel .= "<div class=\"col-md-8\">";
        $null_panel .= "<div class=\"panel panel-primary\">";

        $null_panel .= "<div class=\"panel-heading\">";
        $null_panel .= "<center><h4>Unassigned to Session:</h4></center>";

        $null_panel .= "<center><button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#session_panel_null\">No Session (Show/Hide)</button></center>\n";
        $null_panel .= "</div>";

        $null_panel .= "<div class=\"panel-body collapse\" id=\"session_panel_null\">\n";
        $null_panel .= "<center><h3 class=\"bg-danger\">Please assign these to a session</h3></center>";
        $null_panel .= "<div class=\"tab-pane\">\n";

        $null_panel .= "<table class=\"table table-striped table-fixed \">\n";
        $null_panel .= "<thead>\n";
        $null_panel .= "<tr>\n";
        $null_panel .= "<th >Time</th>\n";
        $null_panel .= "<th >Type</th>\n";
        $null_panel .= "<th >Task</th>\n";
        $null_panel .= "<th >Title/Query</th>\n";
        $null_panel .= "<th >URL</th>\n";
        $null_panel .= "</tr>\n";
        $null_panel .= "</thead>\n";
        $null_panel .= "<tbody>\n";

        foreach($pages as $page) {
            $null_panel .= "<tr >\n";
            $null_panel .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";

            $color = '';
            if($page['type']=='page'){
                $color = 'class="warning"';
            }else{
                $color = 'class="info"';
            }

            $null_panel .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
            $null_panel .= "<td >".(isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"")."</td>\n";
            $null_panel .= "<td>".(isset($page['title'])?substr($page['title'],0,60)."...":"")."</td>";
            $null_panel .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
            $null_panel .= "</tr>\n";
            }
        $null_panel .= "</tbody>\n";
        $null_panel .= "</table>\n";
        $null_panel .= "</div>\n";
        $null_panel .= "</div>\n";
        $null_panel .= "</div>\n";
        $null_panel .= "</div>";
        $null_panel .= "</div>";
        $null_panel .= "</div>";
//        $null_html .= "</div>";
    }

    return array('taskpanels_html'=>$session_panels_html,'nullpanel_html'=>$null_panel);
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

function getSessionIDs($userID,$startTimestamp,$endTimestamp){
    $startTimestampMillis = $startTimestamp*1000;
    $endTimestampMillis= $endTimestamp*1000;
    $query = "SELECT * FROM (SELECT sessionID FROM pages WHERE userID=$userID AND `localTimestamp` >=$startTimestampMillis AND `localTimestamp` <= $endTimestampMillis  UNION SELECT sessionID FROM queries WHERE userID=$userID  AND `localTimestamp` >=$startTimestampMillis AND `localTimestamp` <= $endTimestampMillis) a GROUP BY sessionID";
    $cxn = Connection::getInstance();
    $results = $cxn->commit($query);
    $sessionIDs = array();
    while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
        $sessionIDs[] = $line['sessionID'];
    }


    return $sessionIDs;
}

function getPagesQueriesForSession($userID,$sessionID){
//    getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0,$sessionID=-1)
}


function markTaskID($userID,$sessionIDs,$taskID){
    $cxn = Connection::getInstance();
    if(count($sessionIDs) > 0){
        $sessionID_list = implode(",",$sessionIDs);
        $query = "UPDATE pages SET `taskID`='$taskID' WHERE `userID`='$userID' AND `sessionID` IN ($sessionID_list)";
        echo $query;
        $cxn->commit($query);
        $query = "UPDATE queries SET `taskID`='$taskID' WHERE `userID`='$userID' AND `sessionID` IN ($sessionID_list)";
//        echo $query;
        $cxn->commit($query);
    }
}
?>
