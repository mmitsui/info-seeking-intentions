<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once("pageQueryUtils.php");

function getQuerySegmentTables($userID,$startTimestamp,$endTimestamp){


    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,-1);
    $pages =$pagesQueries;
    $table_index = 0;
    $query_segment_table = '';
    $query_segment_panel_html = '';

    if(count($pages)<=0){
        $query_segment_table = '<center><h3 class=\'bg-danger\'>You logged no activity. Please search and browse.</h3></center>';
        $query_segment_panel_html = '<center><h3 class=\'bg-danger\'>You logged no activity. Please search and browse.</h3></center>';
    }else{

        $query_segment_table = "<table class=\"table table-bordered table-striped table-fixed\">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Mark</th>
                                    <th >Session</th>
                                    <th >Query Segment</th>
                                    <th >Title/Query</th>
                                    <th >Domain</th>




                                </tr>
                                </thead>
                                <tbody>";

        foreach($pages as $page){
            $query_segment_table .= "<tr >";
            $query_segment_table .="<td name=\"time_$table_index\">".(isset($page['time'])?$page['time']:"")."</td>";

            $name = '';
            $color = '';
            if($page['type']=='page'){
                $name='pages[]';
                $color = 'class="warning"';
            }else{
                $name='queries[]';
                $color = 'class="info"';
            }
            $value = $page['id'];

            $query_segment_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
            $begin_button = "<button name=\"begin_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-success\">Begin</button>";
            $end_button = "<button name=\"end_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-danger\">End</button>";
            $query_segment_table .= "<td><input data-table-index=\"$table_index\" type=\"checkbox\" name='$name' value='$value'> $begin_button $end_button </td>";
//        $query_segment_table .="<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>";
            $query_segment_table .="<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
            $query_segment_table .="<td>".(isset($page['querySegmentID']) ?$page['querySegmentID'] : "")."</td>";
            $query_segment_table .= "<td name=\"title_$table_index\"><span title='".(isset($page['title'])?$page['title']:"")."'>".(isset($page['title'])?substr($page['title'],0,50)."...":"")."</span></td>";
            $query_segment_table .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
            $table_index += 1;




            $query_segment_table .= "</tr >";

        }
        $query_segment_table .= "</tbody>
                    </table>";


        $slider_html = "";

        $query_segment_panel_html = "
    <div class=\"row\">
        $slider_html
        <div class=\"col-md-12 border\">
        $query_segment_table
        </div>
        
    </div>
        ";
    }



//    $slider_html = "<div class=\"col-md-1 border\">
//<table>
//<thead>
//<th>Drag to Select</th>
//</thead>
//
//<tbody>
//<tr><td>
//
//                        <p><input id=\"querysegment_slider\" type=\"text\" height=\"100%\" data-slider-min=\"0\" data-slider-max=\"$table_index\" data-slider-step=\"1\" data-slider-value=\"[0,$table_index]\" data-slider-orientation=\"vertical\"/></p>
//
//                    </td>
//                    </tr>
//                    </tbody>
//                    </table>
//                    </div>";



//    <div class="col-md-12 border tab-pane">

    return array('querysegmenthtml'=>$query_segment_panel_html);
}

function makeNextQuerySegmentID($userID){
    $query = "SELECT IFNULL(MAX(querySegmentID),0) as maxQuerySegmentID FROM querysegment_labels_user";
//    $query = "SELECT IFNULL(MAX(querySegmentID),0) as maxQuerySegmentID FROM querysegment_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);

    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    $querySegmentID = $line['maxQuerySegmentID']+1;
    $query = "INSERT INTO querysegment_labels_user (`userID`,`projectID`,`querySegmentID`,`deleted`) VALUES ('$userID','$userID','$querySegmentID',0)";
    $cxn->commit($query);
    return $querySegmentID;
}

function markQuerySegmentID($userID,$querySegmentID,$pageIDs,$queryIDs){
    $cxn = Connection::getInstance();
    if(count($queryIDs) > 0){
        $queryID_list = implode(",",$queryIDs);
        $query = "UPDATE queries SET `querySegmentID`='$querySegmentID' WHERE `userID`='$userID' AND `queryID` IN ($queryID_list)";
        $cxn->commit($query);
    }

    if(count($pageIDs) > 0){
        $pageID_list = implode(",",$pageIDs);
        $query = "UPDATE pages SET `querySegmentID`='$querySegmentID' WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
        $cxn->commit($query);
    }
}

function getQuerySegmentIDs($userID,$startTimestamp,$endTimestamp){
    $startTimestampMillis = $startTimestamp*1000;
    $endTimestampMillis= $endTimestamp*1000;
    $query = "SELECT * FROM (SELECT querySegmentID FROM pages WHERE userID=$userID AND `localTimestamp` >=$startTimestampMillis AND `localTimestamp` <= $endTimestampMillis  UNION SELECT querySegmentID FROM queries WHERE userID=$userID  AND `localTimestamp` >=$startTimestampMillis AND `localTimestamp` <= $endTimestampMillis) a GROUP BY querySegmentID";
    $cxn = Connection::getInstance();
    $results = $cxn->commit($query);
    $querySegmentIDs = array();
    while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
        $querySegmentIDs[] = $line['querySegmentID'];
    }

    return $querySegmentIDs;

}

function getMarkIntentionsPanels($userID,$startTimestamp,$endTimestamp){
    $taskIDNameMap = getTaskIDNameMap($userID);
    $querySegmentIDs = getQuerySegmentIDs($userID,$startTimestamp,$endTimestamp);
    $session_panels = array();
    $session_panels_html = "";
    if(count($querySegmentIDs)==0 or (count($querySegmentIDs)==1 and in_array(null,$querySegmentIDs))){
        $session_panels_html = "<center><h3 class='bg-danger'>You have marked no query segment. Please go back and mark some.</h3></center>";
    }
    else{
        foreach($querySegmentIDs as $querySegmentID){
            if(!is_null($querySegmentID)){
                $session_panels[$querySegmentID] = "<div class=\"panel panel-primary\">\n";
                $session_panels[$querySegmentID] .= "<div class=\"panel-heading\">\n";
                $session_panels[$querySegmentID] .= "<center>\n";
                $session_panels[$querySegmentID] .= "<input type=\"radio\" name=\"querySegmentID\" id=\"querySegmentID_radio_$querySegmentID\" value=\"$querySegmentID\">\n";
                $session_panels[$querySegmentID] .= "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#intentions_panel_$querySegmentID\">Query Segment $querySegmentID (Show/Hide)</button>\n";
                $session_panels[$querySegmentID] .= "</center>\n";
                $session_panels[$querySegmentID] .= "</div>\n";
                $session_panels[$querySegmentID] .= "<form id=\"intentions_form_$querySegmentID\" action=\"../services/utils/runPageQueryUtils.php?action=markIntentions\">\n";
                $session_panels[$querySegmentID] .= "<div class=\"panel-body collapse\" id=\"intentions_panel_$querySegmentID\">\n";
                $session_panels[$querySegmentID] .= "<div class=\"tab-pane\">\n";
                $session_panels[$querySegmentID] .= "<table class=\"table table-bordered table-striped table-fixed \">\n";
                $session_panels[$querySegmentID] .= "<thead>\n";
                $session_panels[$querySegmentID] .= "<tr>\n";
                $session_panels[$querySegmentID] .= "<th >Time</th>\n";
                $session_panels[$querySegmentID] .= "<th >Type</th>\n";
                $session_panels[$querySegmentID] .= "<th >Task</th>\n";
                $session_panels[$querySegmentID] .= "<th >Title/Query</th>\n";
                $session_panels[$querySegmentID] .= "<th >URL</th>\n";
                $session_panels[$querySegmentID] .= "</tr>\n";
                $session_panels[$querySegmentID] .= "</thead>\n";
                $session_panels[$querySegmentID] .= "<tbody>\n";

                $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,-1,$querySegmentID);

                $pages =$pagesQueries;
//            echo "SESSIONID".$querySegmentID."COUNT".count($pages);
                foreach($pages as $page) {
                    $session_panels[$querySegmentID] .= "<tr >\n";
                    $session_panels[$querySegmentID] .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";

                    $color = '';
                    if($page['type']=='page'){
                        $color = 'class="warning"';
                    }else{
                        $color = 'class="info"';
                    }

                    $session_panels[$querySegmentID] .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
                    $session_panels[$querySegmentID] .= "<td >".(isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"")."</td>\n";
                    $session_panels[$querySegmentID].= "<td><span title='".(isset($page['title'])?$page['title']:"")."'>".(isset($page['title'])?substr($page['title'],0,60)."...":"")."</span></td>";
                    $session_panels[$querySegmentID] .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
                    $session_panels[$querySegmentID] .= "</tr>\n";
                }
                $session_panels[$querySegmentID] .= "</tbody>\n";
                $session_panels[$querySegmentID] .= "</table>\n";
                $session_panels[$querySegmentID] .= "</div>\n";
                $session_panels[$querySegmentID] .= "</div>\n";
                $session_panels[$querySegmentID] .= "</div>\n";
            }

        }


        foreach($querySegmentIDs as $querySegmentID){
            if(!is_null($querySegmentID)) {
                $session_panels_html .= $session_panels[$querySegmentID];
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
        $null_panel .= "<center><h4>Unassigned to Query Segment:</h4></center>";

        $null_panel .= "<center><button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#session_panel_null\">No Session (Show/Hide)</button></center>\n";
        $null_panel .= "</div>";

        $null_panel .= "<div class=\"panel-body collapse\" id=\"session_panel_null\">\n";
        $null_panel .= "<center><h3 class=\"bg-danger\">Please assign these to a query segment</h3></center>";
        $null_panel .= "<div class=\"tab-pane\">\n";

        $null_panel .= "<table class=\"table table-bordered table-striped table-fixed \">\n";
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
            $null_panel.= "<td><span title='".(isset($page['title'])?$page['title']:"")."'>".(isset($page['title'])?substr($page['title'],0,60)."...":"")."</span></td>";
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

    return array('intentionspanels_html'=>utf8_encode($session_panels_html),'nullpanel_html'=>utf8_encode($null_panel));

}

function getIntentionsPanel($userID,$startTimestamp,$endTimestamp){


    $intentions = array(
        'id_start'=>'Identify something to get started',
        'id_more'=>'Identify more to search',
        'learn_feature'=>'Learn system feature',
        'learn_structure'=>'Learn system structure',
        'learn_domain'=>'Learn domain knowledge',
        'learn_database'=>'Learn database content',
        'find_known'=>'Find a known item',
        'find_specific'=>'Find specific information',
        'find_common'=>'Find items sharing a named characteristic',
        'find_without'=>'Find items without predefined criteria',
        'locate_specific'=>'Locate a specific item',
        'locate_common'=>'Locate items with common characteristics',
        'locate_area'=>'Locate an area/location',
        'keep_bibliographical'=>'Keep record of bibliographical information',
        'keep_link'=>'Keep record of link',
        'keep_item'=>'Note item for return',
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


    $intentions_html = "<div class=\"panel-body\">";




    $intentions_html .= "<form id=\"mark_intentions_form\" action=\"../services/utils/runPageQueryUtils.php?action=markIntentions\">";

    $intentions_html .="<center>";


    $intentions_html .= "<table class='table table-bordered table-striped table-fixed'>";
    $intentions_html .= "<tr>";
    $intentions_html .= "<th>Intention</th>";
    $intentions_html .= "<th>Satisfied?</th>";
    $intentions_html .= "<th>If not, why not?</th>";
    $intentions_html .= "</tr>";



    $intentions_html .="<tbody>";
    foreach($intentions as $key=>$value){

        $intentions_html .="<tr>";
        $intentions_html .="<td>";
        $intentions_html .="<div class=\"checkbox\">";
        $intentions_html .="<label>";
        $intentions_html .="<input type=\"checkbox\" name='intentions[]' value='$key'/> $value";
//        $intentions_html .="<input type=\"checkbox\" data-toggle=\"collapse\" data-target=\"#intention_submenu_$key\" name='intentions[]' value='$key'/> $value";
        $intentions_html .="</label>";
        $intentions_html .="</div>";
        $intentions_html .="</td>";

        $intentions_html .="<td>";
        $intentions_html .="<div id='intention_submenu_$key'>";
//        $intentions_html .="<div id='intention_submenu_$key' class='collapse'>";
        $intentions_html .= "<div class='radio'>";
        $intentions_html .= "<label><input type='radio' name='$key"."_success' value='1'> Yes</label>";
        $intentions_html .= "</div>";
        $intentions_html .= "<div class='radio'>";
        $intentions_html .= "<label><input type='radio' name='$key"."_success' value='0'> No</label>";
//        $intentions_html .= "<input type='radio' name='$key"."_success' value='0' data-toggle=\"collapse\" data-target=\"#failure_submenu_$key\"> No";
        $intentions_html .= "</div>";
        $intentions_html .= "</div>";
        $intentions_html .="</td>";


        $intentions_html .="<td>";
        $intentions_html .="<div id='failure_submenu_$key'>";
//        $intentions_html .="<div id='failure_submenu_$key' class='collapse'>";
//        $intentions_html .= "Why Not?";
        $intentions_html .= "<textarea class=\"form-control\" rows=\"3\" cols=\"40\" name=\"$key"."_failure_reason\"></textarea>";
        $intentions_html .="</div>";


        $intentions_html .="</div>";
        $intentions_html .="</td>";
        $intentions_html .="</tr>";
    }
    $intentions_html .="</tbody>";


    $intentions_html .= "<input type=\"hidden\" name=\"userID\" value=\"$userID\"/>";
    $intentions_html .= "<input type=\"hidden\" name=\"startTimestamp\" value=\"$startTimestamp\"/>";
    $intentions_html .= "<input type=\"hidden\" name=\"endTimestamp\" value=\"$endTimestamp\"/>";

//    $intentions_html .= "<p><button type=\"button\" value=\"markintentions_button\" class=\"btn btn-success btn-block\">+ Mark Intentions</button></p>";
    $intentions_html .="</center>";
    $intentions_html .= "<center><h3 id=\"addintentions_confirmation\" class=\"alert alert-success\"></h3></center>";

    $intentions_html .= "</div>";
    $intentions_html .=  "</div>";

    $intentions_html .= "</table>";
    $intentions_html .= "</center>";

    $intentions_html .= "</form>";


    return array('intentionshtml'=>utf8_encode($intentions_html));

}


function markIntentions($userID,$querySegmentID,$checkedIntentions){

    $intentions = array(
        'id_start'=>0,
        'id_more'=>0,
        'learn_feature'=>0,
        'learn_structure'=>0,
        'learn_domain'=>0,
        'learn_database'=>0,
        'find_known'=>0,
        'find_specific'=>0,
        'find_common'=>0,
        'find_without'=>0,
        'locate_specific'=>0,
        'locate_common'=>0,
        'locate_area'=>0,
        'keep_bibliographical'=>0,
        'keep_link'=>0,
        'keep_item'=>0,
        'access_item'=>0,
        'access_common'=>0,
        'access_area'=>0,
        'evaluate_correctness'=>0,
        'evaluate_specificity'=>0,
        'evaluate_usefulness'=>0,
        'evaluate_best'=>0,
        'evaluate_duplication'=>0,
        'obtain_specific'=>0,
        'obtain_part'=>0,
        'obtain_whole'=>0,
        'other'=>'Other'
    );
    foreach($checkedIntentions as $intention){
        $intentions[$intention] = 1;
    }

    $cxn = Connection::getInstance();
    $query="INSERT INTO intent_assignments (`userID`,`querySegmentID`,
        
`id_start`,
        `id_more`,
        `learn_feature`,
        `learn_structure`,
        `learn_domain`,
        `learn_database`,
        `find_known`,
        `find_specific`,
        `find_common`,
        `find_without`,
        `locate_specific`,
        `locate_common`,
        `locate_area`,
        `keep_bibliographical`,
        `keep_link`,
        `keep_item`,
        `access_item`,
        `access_common`,
        `access_area`,
        `evaluate_correctness`,
        `evaluate_specificity`,
        `evaluate_usefulness`,
        `evaluate_best`,
        `evaluate_duplication`,
        `obtain_specific`,
        `obtain_part`,
        `obtain_whole`,
        `other`
        
        
) VALUES ('$userID','$querySegmentID'
,".$intentions['id_start']."
,".$intentions['id_more']."
,".$intentions['learn_feature']."
,".$intentions['learn_structure']."
,".$intentions['learn_domain']."
,".$intentions['learn_database']."
,".$intentions['find_known']."
,".$intentions['find_specific']."
,".$intentions['find_common']."
,".$intentions['find_without']."
,".$intentions['locate_specific']."
,".$intentions['locate_common']."
,".$intentions['locate_area']."
,".$intentions['keep_bibliographical']."
,".$intentions['keep_link']."
,".$intentions['keep_item']."
,".$intentions['access_item']."
,".$intentions['access_common']."
,".$intentions['access_area']."
,".$intentions['evaluate_correctness']."
,".$intentions['evaluate_specificity']."
,".$intentions['evaluate_usefulness']."
,".$intentions['evaluate_duplication']."
,".$intentions['evaluate_best']."
,".$intentions['obtain_specific']."
,".$intentions['obtain_part']."
,".$intentions['obtain_whole']."
,".$intentions['other']."

          )";
        $result = $cxn->commit($query);


}

function getQuerySegmentAndMarkIntentionsPanels($userID,$startTimestamp,$endTimestamp){
    $intentionsPanel = getIntentionsPanel($userID,$startTimestamp,$endTimestamp);
    $querySegmentTables = getQuerySegmentTables($userID,$startTimestamp,$endTimestamp);
    return array_merge($intentionsPanel,$querySegmentTables);
}




//function getTaskIDNameMap($userID){
//    $query = "SELECT * FROM task_labels_user WHERE userID=$userID AND deleted != 1 ORDER BY taskID ASC";
//    $cxn = Connection::getInstance();
//    $result = $cxn->commit($query);
//    $taskArray = array();
//    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
//        $taskArray[$line['taskID']] = $line['taskName'];
//    }
//    return $taskArray;
//}
//
//function addTask($userID,$taskName){
//    $query = "SELECT IFNULL(MAX(taskID),0) as maxTaskID FROM task_labels_user WHERE userID=$userID";
//    $cxn = Connection::getInstance();
//    $result = $cxn->commit($query);
//    $line = mysql_fetch_array($result,MYSQL_ASSOC);
//    $taskID = $line['maxTaskID']+1;
//    $query = "INSERT INTO task_labels_user (`userID`,`projectID`,`taskID`,`taskName`,`deleted`) VALUES ('$userID','$userID','$taskID','$taskName',0)";
//    $cxn->commit($query);
//}
//
//function getTasks($userID){
//    $query = "SELECT * FROM task_labels_user WHERE userID=$userID AND deleted != 1 ORDER BY taskID ASC";
//    $cxn = Connection::getInstance();
//    $result = $cxn->commit($query);
//    $rows = array();
//    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
//    {
//        $rows[] = $row;
//    }
//    return $rows;
//}
//
//
//function getMarkTasksPanels($userID,$startTimestamp,$endTimestamp){
//    $taskIDNameMap = getTaskIDNameMap($userID);
//    $sessionIDs = getSessionIDs($userID,$startTimestamp,$endTimestamp);
//    $session_panels = array();
//    $session_panels_html = "";
//    if(count($sessionIDs)==0 or (count($sessionIDs)==1 and in_array(null,$sessionIDs))){
//        $session_panels_html = "<center><h3 class='bg-danger'>You have marked no sessions. Please go back and mark some</h3></center>";
//    }
//    else{
//        foreach($sessionIDs as $sessionID){
//            if(!is_null($sessionID)){
//                $session_panels[$sessionID] = "<div class=\"panel panel-primary\">\n";
//                $session_panels[$sessionID] .= "<div class=\"panel-heading\">\n";
//                $session_panels[$sessionID] .= "<center>\n";
//                $session_panels[$sessionID] .= "<input type=\"checkbox\" name=\"sessionIDs[]\" id=\"sessionID_checkbox_$sessionID\" value=\"$sessionID\">\n";
//                $session_panels[$sessionID] .= "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#session_panel_$sessionID\">Session $sessionID (Show/Hide)</button>\n";
//                $session_panels[$sessionID] .= "</center>\n";
//                $session_panels[$sessionID] .= "</div>\n";
//                $session_panels[$sessionID] .= "<form id=\"task_form_$sessionID\" action=\"../services/utils/runPageQueryUtils.php?action=markTask\">\n";
//                $session_panels[$sessionID] .= "<div class=\"panel-body collapse\" id=\"session_panel_$sessionID\">\n";
//                $session_panels[$sessionID] .= "<div class=\"tab-pane\">\n";
//                $session_panels[$sessionID] .= "<table class=\"table table-striped table-fixed \">\n";
//                $session_panels[$sessionID] .= "<thead>\n";
//                $session_panels[$sessionID] .= "<tr>\n";
//                $session_panels[$sessionID] .= "<th >Time</th>\n";
//                $session_panels[$sessionID] .= "<th >Type</th>\n";
//                $session_panels[$sessionID] .= "<th >Task</th>\n";
//                $session_panels[$sessionID] .= "<th >Title/Query</th>\n";
//                $session_panels[$sessionID] .= "<th >URL</th>\n";
//                $session_panels[$sessionID] .= "</tr>\n";
//                $session_panels[$sessionID] .= "</thead>\n";
//                $session_panels[$sessionID] .= "<tbody>\n";
//
//                $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,$sessionID);
//
//                $pages =$pagesQueries;
////            echo "SESSIONID".$sessionID."COUNT".count($pages);
//                foreach($pages as $page) {
//                    $session_panels[$sessionID] .= "<tr >\n";
//                    $session_panels[$sessionID] .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";
//
//                    $color = '';
//                    if($page['type']=='page'){
//                        $color = 'class="warning"';
//                    }else{
//                        $color = 'class="info"';
//                    }
//
//                    $session_panels[$sessionID] .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
//                    $session_panels[$sessionID] .= "<td >".(isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"")."</td>\n";
//                    $session_panels[$sessionID] .= "<td>".(isset($page['title'])?substr($page['title'],0,60)."...":"")."</td>";
//                    $session_panels[$sessionID] .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
//                    $session_panels[$sessionID] .= "</tr>\n";
//                }
//                $session_panels[$sessionID] .= "</tbody>\n";
//                $session_panels[$sessionID] .= "</table>\n";
//                $session_panels[$sessionID] .= "</div>\n";
//                $session_panels[$sessionID] .= "</div>\n";
//                $session_panels[$sessionID] .= "</div>\n";
//            }
//
//        }
//
//
//        foreach($sessionIDs as $sessionID){
//            if(!is_null($sessionID)) {
//                $session_panels_html .= $session_panels[$sessionID];
//            }
//        }
//
//    }
//
//
//
//
//    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,NULL);
//    $pages =$pagesQueries;
//    if(count($pages) > 0) {
//        $null_panel = "<div class=\"row\">";
//        $null_panel .= "<div class=\"col-md-8\">";
//        $null_panel .= "<div class=\"panel panel-primary\">";
//
//        $null_panel .= "<div class=\"panel-heading\">";
//        $null_panel .= "<center><h4>Unassigned to Session:</h4></center>";
//
//        $null_panel .= "<center><button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#session_panel_null\">No Session (Show/Hide)</button></center>\n";
//        $null_panel .= "</div>";
//
//        $null_panel .= "<div class=\"panel-body collapse\" id=\"session_panel_null\">\n";
//        $null_panel .= "<center><h3 class=\"bg-danger\">Please assign these to a session</h3></center>";
//        $null_panel .= "<div class=\"tab-pane\">\n";
//
//        $null_panel .= "<table class=\"table table-striped table-fixed \">\n";
//        $null_panel .= "<thead>\n";
//        $null_panel .= "<tr>\n";
//        $null_panel .= "<th >Time</th>\n";
//        $null_panel .= "<th >Type</th>\n";
//        $null_panel .= "<th >Task</th>\n";
//        $null_panel .= "<th >Title/Query</th>\n";
//        $null_panel .= "<th >URL</th>\n";
//        $null_panel .= "</tr>\n";
//        $null_panel .= "</thead>\n";
//        $null_panel .= "<tbody>\n";
//
//        foreach($pages as $page) {
//            $null_panel .= "<tr >\n";
//            $null_panel .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";
//
//            $color = '';
//            if($page['type']=='page'){
//                $color = 'class="warning"';
//            }else{
//                $color = 'class="info"';
//            }
//
//            $null_panel .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
//            $null_panel .= "<td >".(isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"")."</td>\n";
//            $null_panel .= "<td>".(isset($page['title'])?substr($page['title'],0,60)."...":"")."</td>";
//            $null_panel .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
//            $null_panel .= "</tr>\n";
//            }
//        $null_panel .= "</tbody>\n";
//        $null_panel .= "</table>\n";
//        $null_panel .= "</div>\n";
//        $null_panel .= "</div>\n";
//        $null_panel .= "</div>\n";
//        $null_panel .= "</div>";
//        $null_panel .= "</div>";
//        $null_panel .= "</div>";
////        $null_html .= "</div>";
//    }
//
//    return array('taskpanels_html'=>$session_panels_html,'nullpanel_html'=>$null_panel);
//}
//
//function getTasksPanel($userID,$startTimestamp,$endTimestamp){
//
//    $tasks_html =  "<center>";
//    $tasks_html .= "<div id='task_buttons'>";
//    $tasks = getTasks($userID);
//    foreach($tasks as $task){
//        $taskID = $task['taskID'];
//        $taskName = $task['taskName'];
//        $tasks_html .=  "<p><button type=\"button\" id=\"task-button-$taskID\" data-task-id=\"$taskID\" class=\"btn btn-primary btn-block\">$taskName</button></p>";
//    }
//
//
//    $tasks_html .=  "</div>";
//    $tasks_html .= "<hr/>";
//    $tasks_html .= "<form id=\"add_task_form\" action=\"../services/utils/runPageQueryUtils.php?action=addTask\">";
//    $tasks_html .= "<div class=\"form-group\">";
//
//
//    $tasks_html .= "<div>";
//    $tasks_html .= "<p><button type=\"button\" value=\"addtask_button\" class=\"btn btn-success btn-block\">+ Add Task</button></p>";
//    $tasks_html .= "</div>";
//    $tasks_html .= "<textarea class=\"form-control\" rows=\"1\" id=\"task_name_textfield\" name=\"taskName\"></textarea>";
//    $tasks_html .= "<input type=\"hidden\" name=\"userID\" value=\"$userID\"/>";
//    $tasks_html .= "<input type=\"hidden\" name=\"startTimestamp\" value=\"$startTimestamp\"/>";
//    $tasks_html .= "<input type=\"hidden\" name=\"endTimestamp\" value=\"$endTimestamp\"/>";
//    $tasks_html .= "</div>";
//    $tasks_html .= "<center><h3 id=\"addtask_confirmation\" class=\"bg-success\"></h3></center>";
//    $tasks_html .= "</form>";
//    $tasks_html .= "</center>";
//
//    return array('taskshtml'=>$tasks_html);
//
//}
//
//function makeNextSessionID($userID){
//    $query = "SELECT IFNULL(MAX(sessionID),0) as maxSessionID FROM session_labels_user WHERE userID=$userID";
//    $cxn = Connection::getInstance();
//    $result = $cxn->commit($query);
//    $line = mysql_fetch_array($result,MYSQL_ASSOC);
//    $sessionID = $line['maxSessionID']+1;
//    $query = "INSERT INTO session_labels_user (`userID`,`projectID`,`sessionID`,`deleted`) VALUES ('$userID','$userID','$sessionID',0)";
//    $cxn->commit($query);
//    return $sessionID;
//}
//
//function markSessionID($userID,$sessionID,$pageIDs,$queryIDs){
//    $cxn = Connection::getInstance();
//    if(count($queryIDs) > 0){
//        $queryID_list = implode(",",$queryIDs);
//        $query = "UPDATE queries SET `sessionID`='$sessionID' WHERE `userID`='$userID' AND `queryID` IN ($queryID_list)";
//        $cxn->commit($query);
//    }
//
//    if(count($pageIDs) > 0){
//        $pageID_list = implode(",",$pageIDs);
//        $query = "UPDATE pages SET `sessionID`='$sessionID' WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
//        $cxn->commit($query);
//    }
//}
//
//function getSessionIDs($userID,$startTimestamp,$endTimestamp){
//    $startTimestampMillis = $startTimestamp*1000;
//    $endTimestampMillis= $endTimestamp*1000;
//    $query = "SELECT * FROM (SELECT sessionID FROM pages WHERE userID=$userID AND `localTimestamp` >=$startTimestampMillis AND `localTimestamp` <= $endTimestampMillis  UNION SELECT sessionID FROM queries WHERE userID=$userID  AND `localTimestamp` >=$startTimestampMillis AND `localTimestamp` <= $endTimestampMillis) a GROUP BY sessionID";
//    $cxn = Connection::getInstance();
//    $results = $cxn->commit($query);
//    $sessionIDs = array();
//    while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
//        $sessionIDs[] = $line['sessionID'];
//    }
//
//
//    return $sessionIDs;
//}
//
//function getPagesQueriesForSession($userID,$sessionID){
////    getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0,$sessionID=-1)
//}
//
//
//function markTaskID($userID,$sessionIDs,$taskID){
//    $cxn = Connection::getInstance();
//    if(count($sessionIDs) > 0){
//        $sessionID_list = implode(",",$sessionIDs);
//        $query = "UPDATE pages SET `taskID`='$taskID' WHERE `userID`='$userID' AND `sessionID` IN ($sessionID_list)";
////        echo $query;
//        $cxn->commit($query);
//        $query = "UPDATE queries SET `taskID`='$taskID' WHERE `userID`='$userID' AND `sessionID` IN ($sessionID_list)";
////        echo $query;
//        $cxn->commit($query);
//    }
//}
?>
