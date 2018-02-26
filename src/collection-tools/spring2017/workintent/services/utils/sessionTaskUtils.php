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
    return $taskID;
}

function addTask_returnabs($userID,$taskName){
    $query = "SELECT IFNULL(MAX(taskID),0) as maxTaskID FROM task_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    $taskID = $line['maxTaskID']+1;
    $query = "INSERT INTO task_labels_user (`userID`,`projectID`,`taskID`,`taskName`,`deleted`) VALUES ('$userID','$userID','$taskID','$taskName',0)";
    $cxn->commit($query);
    return $cxn->getLastID();
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

function getSessionIDByTaskID($userID,$taskID){

    $query = "";
    if(is_null($taskID)){
        $query = "SELECT * FROM pages WHERE userID=$userID AND taskID IS NULL AND sessionID IS NOT NULL GROUP BY sessionID";
    }else{
        $query = "SELECT * FROM pages WHERE userID=$userID AND taskID=$taskID AND sessionID IS NOT NULL GROUP BY sessionID";
    }

    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $sessionIDs = array();
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
    {
        $sessionIDs[] = $row['sessionID'];
    }

    return $sessionIDs;
}

function getTasksForDay($userID,$startTimestamp){
    $endTimestamp = $startTimestamp+86400;
    $startTimestamp_millis = $startTimestamp * 1000;
    $endTimestamp_millis = $endTimestamp * 1000;
    $query = "SELECT taskID FROM pages WHERE taskID IS NOT NULL AND `localTimestamp` >= $startTimestamp_millis AND `localTimestamp` <= $endTimestamp_millis";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $taskIDs = array();
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
    {
        $taskIDs[] = $row['taskID'];
    }


    if(count($taskIDs) > 0){
        $query = "SELECT * FROM task_labels_user WHERE userID=$userID AND taskID IN (".implode(",",$taskIDs).")";
        $cxn = Connection::getInstance();
        $result = $cxn->commit($query);
        $rows = array();
        while($row = mysql_fetch_array($result,MYSQL_ASSOC))
        {
            $rows[] = $row;
        }
        return $rows;

    }else{
        return null;
    }

}

function getTaskByName($userID,$taskName){
    $query = "SELECT * FROM task_labels_user WHERE userID=$userID AND deleted != 1 AND taskName='$taskName' ORDER BY taskID ASC";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);

    if(mysql_num_rows($result) > 0){
        return mysql_fetch_array($result,MYSQL_ASSOC);
    }else{
        return null;
    }

}

function getQuerySegmentsForSession($userID,$sessionID){
    $query = "SELECT querySegmentID FROM pages WHERE userID=$userID AND sessionID=$sessionID AND querySegmentID IS NOT NULL GROUP BY querySegmentID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $rows = array();
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $rows[] = $line['querySegmentID'];
    }
    return $rows;
}

function getIntentsForQuerySegmentID($userID,$querySegmentID){

    $intentions = array(
        'id_start'=>'Identify something to get started',
        'id_more'=>'Identify more to search',
//        'learn_feature'=>'Learn system feature',
//        'learn_structure'=>'Learn system structure',
        'learn_domain'=>'Learn domain knowledge',
        'learn_database'=>'Learn database content',
        'find_known'=>'Find a known item',
        'find_specific'=>'Find specific information',
        'find_common'=>'Find items sharing a named characteristic',
        'find_without'=>'Find items without predefined criteria',
//        'locate_specific'=>'Locate a specific item',
//        'locate_common'=>'Locate items with common characteristics',
//        'locate_area'=>'Locate an area/location',
//        'keep_bibliographical'=>'Keep record of bibliographical information',
        'keep_link'=>'Keep record of link',
//        'keep_item'=>'Note item for return',
        'access_item'=>'Access a specific item',
        'access_common'=>'Access items with common characteristics',
        'access_area'=>'Access a web site/home page or similar',
        'evaluate_correctness'=>'Evaluate correctness of an item',
        'evaluate_specificity'=>'Evaluate specificity of an item',
        'evaluate_usefulness'=>'Evaluate usefulness of an item',
        'evaluate_best'=>'Pick best item(s) from all the useful ones',
        'evaluate_duplication'=>'Evaluate duplication of an item',
        'obtain_specific'=>'Obtain specific information',
        'obtain_part'=>'Obtain part of the item',
        'obtain_whole'=>'Obtain a whole item(s)',
        'other'=>'Other'

    );



    $query = "SELECT * FROM intent_assignments WHERE userID=$userID AND querySegmentID=$querySegmentID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    $yesintents = array();
    foreach($intentions as $key=>$value){
        if($line[$key]==1){
            $yesintents[] = $value;
        }
    }

    return $yesintents;

}



function getTaskInformationPanels($userID,$startTimestamp,$endTimestamp){
    $taskIDNameMap = getTaskIDNameMap($userID);
    $sessionIDs = getSessionIDs($userID,$startTimestamp,$endTimestamp);

    $sessionIDToLabel = array();
    $query = "SELECT * FROM session_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $sessionIDToLabel[$line['id']] = $line['sessionLabel'];
    }
    $tasksForDay = getTasksForDay($userID,$startTimestamp);
    array_push($tasksForDay,array('taskName'=>'UNASSIGNED TO TASK','taskID'=>null));


    $display = "";
    if(count($tasksForDay)==0){
        $session_panels_html = "<center><h3 class='bg-danger'>You no tasks for the day.  Please go back and mark some.</h3></center>";
    }else{
        foreach($tasksForDay as $taskInformation){




            $sessionIDs = getSessionIDByTaskID($userID,$taskInformation['taskID']);

            if(count($sessionIDs)==0){
                continue;
            }

            $display .= "<div class=\"panel panel-success\">\n";
            $display .= "<div class=\"panel-heading\">\n";
            $test = "<ul><li>one</li><li>two</li></ul>";
            $display .= "<center><h3>Task: ".$taskInformation['taskName']."</h3></center>";
            $display .= "</div>";

            $display .= "<div class=\"panel-body\">\n";

            foreach($sessionIDs as $sessionID) {
                if (!is_null($sessionID)) {

                    $sessionLabel = $sessionIDToLabel[$sessionID];
                    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,$sessionID);

                    $pages =$pagesQueries;
                    if(count($pages)==0){
                        continue;
                    }


                    $display .= "<div class=\"panel panel-primary\">\n";
                    $display .= "<div class=\"panel-heading\">\n";
                    $display .= "<center>\n";
                    $display .= "Session $sessionLabel\n";
//                $display .= "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#session_panel_$sessionID\">Session $sessionID (Show/Hide)</button>\n";

                    $querySegmentIDs = getQuerySegmentsForSession($userID,$sessionID);

                    $display .= "<br/><br/># Search Segments:\n".count($querySegmentIDs);

                    $display .= "<br/><br/>";

                    foreach($querySegmentIDs as $querySegmentID){
                        $display .= "<ul> Search Segment $querySegmentID";

                        $intents = getIntentsForQuerySegmentID($userID,$querySegmentID);
                        if(count($intents)>0){
                            foreach($intents as $intent){
                                $display .= "<li>";
                                $display .= "$intent";
                                $display .= "</li>";
                            }

                        }else{
                            $display .= "<br/>No Intentions";
                        }

                        $display .= "</ul>";
                    }


                    $display .= "</center>\n";
                    $display .= "</div>\n";


                    $display .= "<form id=\"task_form_$sessionID\" action=\"../services/utils/runPageQueryUtils.php?action=markTask\">\n";
                    $display .= "<div class=\"panel-body\" id=\"session_panel_$sessionID\">\n";

//                $display .= "<div class=\"panel-body collapse\" id=\"session_panel_$sessionID\">\n";
                    $display .= "<div class=\"tab-pane\">\n";
                    $display .= "<table class=\"table table-bordered table-fixed \">\n";
                    $display .= "<thead>\n";
                    $display .= "<tr>\n";
                    $display .= "<th >Time</th>\n";
                    $display .= "<th >Type</th>\n";
                    $display .= "<th >Task</th>\n";
                    $display .= "<th >Title/Query</th>\n";
                    $display .= "<th >URL</th>\n";
                    $display .= "</tr>\n";
                    $display .= "</thead>\n";
                    $display .= "<tbody>\n";


//            echo "SESSIONID".$sessionID."COUNT".count($pages);
                    foreach($pages as $page) {
                        $display .= "<tr >\n";
                        $display .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";

                        $color = '';
                        if($page['type']=='page'){
                            $color = 'class="warning"';
                        }else{
                            $color = 'class="info"';
                        }

                        $display .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
                        $display .= "<td >".(isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"")."</td>\n";
                        $title = '';
                        if($page['type']=='page'){
                            if(isset($page['title'])){
                                $title = $page['title'];
                            }
                        }else{
                            if(isset($page['query'])){
                                $title = $page['query'];
                            }
                        }
                        $title_short = $title;
                        if(strlen($title)>60 or strlen(trim($title))==0){
                            $title_short = substr($page['title'],0,60)."...";
                        }

                        $display .= "<td><span title='$title'>$title_short</span></td>";

                        $display .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
                        $display .= "</tr>\n";
                    }
                    $display .= "</tbody>\n";
                    $display .= "</table>\n";
                    $display .= "</div>\n";
                    $display .= "</div>\n";
                    $display .= "</div>\n";

                }
            }
            $display .= "</div>";
            $display .= "</div>\n";
        }
    }

    return $display;

}
function getMarkTasksPanels($userID,$startTimestamp,$endTimestamp){
    $taskIDNameMap = getTaskIDNameMap($userID);
    $sessionIDs = getSessionIDs($userID,$startTimestamp,$endTimestamp);
    $session_panels = array();

    $sessionIDToLabel = array();
    $query = "SELECT * FROM session_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $sessionIDToLabel[$line['id']] = $line['sessionLabel'];
    }
    $null_panel = "";


    $session_panels_html = "";
    if(count($sessionIDs)==0 or (count($sessionIDs)==1 and in_array(null,$sessionIDs))){
        $session_panels_html = "<center><h3 class='bg-danger'>You have marked no sessions. Please go back and mark some.</h3></center>";
    }
    else{
        $panel_index = 0;
        $n_marked = 0;
        $n_total = 0;
        foreach($sessionIDs as $sessionID){

            if(!is_null($sessionID)){
                $panel_index += 1;
                $sessionLabel = $sessionIDToLabel[$sessionID];
                $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,$sessionID);

                $pages =$pagesQueries;




//                $session_panels[$sessionID] .= "<center>\n";
//                <input type=\"checkbox\" name=\"sessionIDs[]\" id=\"sessionID_checkbox_$sessionID\" value=\"$sessionID\">
//                $session_panels[$sessionID] .= "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#session_panel_$sessionID\">Session $sessionID (Show/Hide)</button>\n";

                $marked='';
                $panel_marked = '';
                $panel_heading_marked = '';
                if(count($pages) > 0){
                    $all_marked = true;
                    foreach($pages as $page) {
                        if(!isset($page['taskID'])){
                            $all_marked = false;
                            break;
                        }
                    }

                    if($all_marked){
                        $n_marked += 1;
//                        $marked='<span class="label label-success">Marked</span>';
                        $marked= "<span >Marked! (Session $sessionLabel)</span>";
                        $panel_marked = "<div class=\"panel panel-success container-fluid\">\n";
                        $panel_heading_marked = "<div class=\"panel-heading row\" data-panel-index='$panel_index'>\n";;
//                        $session_panels[$sessionID] .= "<span class=\"label label-success glyphicon glyphicon-ok\"> </span>";
                    }else{
//                        $marked='<span class="label label-danger">Not Marked</span>';
                        $marked="<span>Not Marked (Session $sessionLabel: Select for Annotation)</span>";
                        $panel_marked = "<div class=\"panel panel-warning container-fluid\">\n";
                        $panel_heading_marked = "<div class=\"panel-heading row\" data-panel-index='$panel_index'>\n";;
//                        $session_panels[$sessionID] .= "<span class=\"label label-lg label-danger glyphicon glyphicon-remove\"> </span>";
                    }
                    $n_total += 1;
                }

                $session_panels[$sessionID] = $panel_marked;
                $session_panels[$sessionID] .= $panel_heading_marked;

                $session_panels[$sessionID] .= "<div class='col-xs-1'>
                        <label style='cursor:pointer;display:inline-block;width:100%'>
                                  <input style='cursor:pointer;zoom:1.6;' data-panel-index='$panel_index'type=\"checkbox\" name=\"sessionIDs[]\" id=\"sessionID_checkbox_$sessionID\" value=\"$sessionID\">
                                  </label>
                                  </div>
                                  <div class='col-xs-11'>
                                  <h5>$marked</h5>
                                </div>";




//                $session_panels[$sessionID] .= "</center>\n";
                $session_panels[$sessionID] .= "</div>\n";
                $session_panels[$sessionID] .= "<form id=\"task_form_$sessionID\" action=\"../services/utils/runPageQueryUtils.php?action=markTask\">\n";
                $session_panels[$sessionID] .= "<div class=\"panel-body\" id=\"session_panel_$sessionID\">\n";

//                $session_panels[$sessionID] .= "<div class=\"panel-body collapse\" id=\"session_panel_$sessionID\">\n";
                $session_panels[$sessionID] .= "<div class=\"tab-pane\">\n";
                $session_panels[$sessionID] .= "<table class=\"table table-bordered table-fixed \">\n";
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


                    $title = '';
                    if($page['type']=='page'){
                        if(isset($page['title'])){
                            $title = $page['title'];
                        }
                    }else{
                        if(isset($page['query'])){
                            $title = $page['query'];
                        }
                    }
                    $title_short = $title;
                    if(strlen($title)>60 or strlen(trim($title))==0){
                        $title_short = substr($page['title'],0,60)."...";
                    }

                    $session_panels[$sessionID] .= "<td><span title='$title'>$title_short</span></td>";
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



    $progress_bar = "";
    if($n_marked==$n_total){
//        $progress_bar = "<div class=\"panel panel-success\">";
//        $progress_bar .= "<div class=\"panel-heading\">";
        $progress_bar .= "<h2><div class='label label-success'>Progress: All Tasks Marked!</div></h2>";
//        $progress_bar .= "</div>";
//        $progress_bar .= "</div>";

    }else{
//        $progress_bar = "<div class=\"panel panel-warning\">";
//        $progress_bar .= "<div class=\"panel-heading\">";
        $progress_bar .= "<h2><div class='label label-warning'>Progress: ".intval($n_marked/floatval($n_total)*100)."% Tasks Marked</div></h2>";
//        $progress_bar .= "</div>";
//        $progress_bar .= "</div>";

    }


//    $progress_bar .= "<div class=\"panel-body\">";

//    $progress_bar .= "<div class='progress'><div class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"".$n_marked."\"
//  aria-valuemin=\"0\" aria-valuemax=\"".$n_total."\" style=\"width:".intval($n_marked/floatval($n_total)*100)."%\">
//    <span style='color:black' >70% Complete</span>
//  </div>
//  </div>";




//    $progress_bar .= "</div>";
    $progress_bar .= "</div>";

    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,NULL);
    $pages =$pagesQueries;


    if(count($pages) > 0) {
        $null_panel .= "<div class=\"row\">";
        $null_panel .= "<div class=\"col-md-8\">";
        $null_panel .= "<div class=\"panel panel-primary\">";

        $null_panel .= "<div class=\"panel-heading\">";
        $null_panel .= "<center><h4>Unassigned to Session:</h4></center>";

        $null_panel .= "<center><button type=\"button\" class=\"btn btn-default\" data-toggle=\"collapse\" data-target=\"#session_panel_null\">No Session (Show/Hide)</button></center>\n";
        $null_panel .= "</div>";

        $null_panel .= "<div class=\"panel-body collapse\" id=\"session_panel_null\">\n";
        $null_panel .= "<center><h3 class=\"bg-danger\">Please review these items</h3></center>";

//        $null_panel .= "<center><h3 class=\"bg-danger\">Please assign these to a session</h3></center>";
        $null_panel .= "<div class=\"tab-pane\">\n";

        $null_panel .= "<table class=\"table table-bordered table-fixed \">\n";
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

            $title = '';
            if($page['type']=='page'){
                if(isset($page['title'])){
                    $title = $page['title'];
                }
            }else{
                if(isset($page['query'])){
                    $title = $page['query'];
                }
            }
            $title_short = $title;
            if(strlen($title)>60 or strlen(trim($title))==0){
                $title_short = substr($page['title'],0,60)."...";
            }

            $null_panel .= "<td><span title='$title'>$title_short</span></td>";

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

    return array('progressbar_html'=>utf8_encode($progress_bar),'taskpanels_html'=>utf8_encode($session_panels_html),'nullpanel_html'=>utf8_encode($null_panel));
}

function getTasksPanel($userID,$startTimestamp,$endTimestamp){

//    $tasks_html =  "<center>";
    $tasks_html = "<div id='task_buttons'>";
    $tasks = getTasks($userID);
    foreach($tasks as $task){
        $taskID = $task['taskID'];
        $taskName = $task['taskName'];
        $tasks_html .=  "<p><button type=\"button\" id=\"task-button-$taskID\" data-task-id=\"$taskID\" class=\"btn btn-default btn-block\">$taskName</button></p>";
    }


    $tasks_html .=  "</div>";
//    $tasks_html .= "<hr/>";
    $tasks_html .= "<form id=\"add_task_form\" action=\"../services/utils/runPageQueryUtils.php?action=addTask\">";
    $tasks_html .= "<div class=\"form-group\">";


    $tasks_html .= "<div class='well well-sm'>";


    $tasks_html .= "<h5><strong>Enter a new Task here:</strong></h5>";
    $tasks_html .= "<div class='form-group'><textarea class=\"form-control\" rows=\"1\" id=\"task_name_textfield\" name=\"taskName\" placeholder='Task Name'></textarea></div>";
    $tasks_html .= "<div>";
    $tasks_html .= "<p><button type=\"button\" value=\"addtask_button\" class=\"btn btn-success btn-block\">+ Add Task</button></p>";
    $tasks_html .= "</div>";
    $tasks_html .= "<input type=\"hidden\" name=\"userID\" value=\"$userID\"/>";
    $tasks_html .= "<input type=\"hidden\" name=\"startTimestamp\" value=\"$startTimestamp\"/>";
    $tasks_html .= "<input type=\"hidden\" name=\"endTimestamp\" value=\"$endTimestamp\"/>";
    $tasks_html .= "</div>";
    $tasks_html .= "</div>";

    $tasks_html .= "</form>";
//    $tasks_html .= "</center>";

    return array('taskshtml'=>utf8_encode($tasks_html));

}

function makeNextSessionLabel($userID,$startTimestamp){
    $base = Base::getInstance();
    date_default_timezone_set($base->getUserTimezone());
//    date_default_timezone_set('America/New_York');
    $date = date('Y-m-d', $startTimestamp);
    $cxn = Connection::getInstance();



//    $query = "SELECT IFNULL(MAX(sessionLabel),0) as maxSessionID FROM session_labels_user WHERE userID=$userID AND `date`='$date'";
//    $result = $cxn->commit($query);
//    $line = mysql_fetch_array($result,MYSQL_ASSOC);
//    $sessionID = $line['maxSessionID']+1;

    $query = "INSERT INTO session_labels_user (`userID`,`projectID`,`sessionLabel`,`deleted`,`date`) VALUES ('$userID','$userID','0',0,'$date')";
    $cxn->commit($query);
    $lastID = $cxn->getLastID($query);


    $query = "SELECT IFNULL(MIN(`id`),0) as minSessionID FROM session_labels_user WHERE userID=$userID AND `date`='$date'";
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    $minSessionID = $line['minSessionID'];


    $sessionLabel = $lastID-$minSessionID+1;
    $query = "UPDATE session_labels_user SET `sessionLabel`='$sessionLabel' WHERE userID=$userID AND `date`='$date' AND `id`=$lastID";
    $result = $cxn->commit($query);




    return $lastID;
//    return $sessionID;
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
        if(!is_null($line['sessionID'])){
            $sessionIDs[] = $line['sessionID'];
        }

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
        $cxn->commit($query);
        $query = "UPDATE queries SET `taskID`='$taskID' WHERE `userID`='$userID' AND `sessionID` IN ($sessionID_list)";
        $cxn->commit($query);
    }
}
?>
