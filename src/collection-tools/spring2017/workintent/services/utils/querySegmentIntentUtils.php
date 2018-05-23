<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once("pageQueryUtils.php");
require_once("sessionTaskUtils.php");

function getQuerySegmentTables($userID,$startTimestamp,$endTimestamp){


    $sessionIDs = getSessionIDs($userID,$startTimestamp,$endTimestamp);

    $sessionIDToLabel = array();
    $query = "SELECT * FROM session_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $sessionIDToLabel[$line['id']] = $line['sessionLabel'];
    }

    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,-1);
    $pages =$pagesQueries;
    $table_index = 0;
    $query_segment_table = '';
    $query_segment_panel_html = '';
    $query_segment_tablemap = array();
    $querySegmentIDs_done = array();
    $pageIDs_done = array();
    $querySegmentIDs_total = array();
    $querySegmentToIntent = array();
    $query = "SELECT querySegmentID,assignmentID FROM intent_assignments WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $querySegmentToIntent[$line['querySegmentID']] = $line['assignmentID'];
    }

    $querySegmentIDToLabel = array();

    $query = "SELECT * FROM querysegment_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $querySegmentIDToLabel[$line['id']] = $line['querySegmentLabel'];
    }

    $n_marked_querysegments = 0;
    $n_total_querysegments = 0;

    $n_marked_intents = 0;
    $n_total_intents = 0;

    $progress_bar_segments = '';
    $progress_bar_intents = '';


    $querySegmentIDsCount = array();
    $querySegmentIDsCount_total = array();
    $querySegmentIDsCount_start = array();
    $cxn = Connection::getInstance();

    $result = $cxn->commit("SELECT querySegmentID,COUNT(*) as ct FROM pages WHERE querySegmentID IS NOT NULL and userID=$userID GROUP BY querySegmentID");
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        $querySegmentIDsCount[$line['querySegmentID']] = $line['ct'];
        $querySegmentIDsCount_total[$line['querySegmentID']] = $line['ct'];
        $querySegmentIDsCount_start[$line['querySegmentID']] = True;
    }

    $result = $cxn->commit("SELECT querySegmentID,COUNT(*) as ct FROM queries WHERE querySegmentID IS NOT NULL and userID=$userID GROUP BY querySegmentID");
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        if(isset($querySegmentIDsCount[$line['querySegmentID']])){
            $querySegmentIDsCount[$line['querySegmentID']] += $line['ct'];
            $querySegmentIDsCount_total[$line['querySegmentID']] += $line['ct'];
        }else{
            $querySegmentIDsCount[$line['querySegmentID']] = $line['ct'];
            $querySegmentIDsCount_total[$line['querySegmentID']] = $line['ct'];
        }
    }

    if(count($pages)<=0){
        $query_segment_table = '<center><h3 class=\'bg-danger\'>You logged no activity. Please search and browse.</h3></center>';
        $query_segment_panel_html = '<center><h3 class=\'bg-danger\'>You logged no activity. Please search and browse.</h3></center>';
    }else{




        $even_odd_styles = array(0=>'info',1=>'success');
        $even_odd_index = array();
        $last_index = 0;

        $data_table_index = 0;
        $data_total_row_index = 0;


        foreach($sessionIDs as $sessionID) {
            $data_table_index += 1;
            if(!is_null($sessionID)){
                if(!isset($even_odd_index[$sessionID])){
                    $even_odd_index[$sessionID] = array();
                }
                $sessionLabel = $sessionIDToLabel[$sessionID];
                $pq_session = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,$sessionID);

                $query_segment_tablemap[$sessionID] = "<div class=\"panel panel-primary\">\n";
                $query_segment_tablemap[$sessionID] .= "<div class=\"panel-heading\">\n";
                $query_segment_tablemap[$sessionID] .= "<center>\n";
                $query_segment_tablemap[$sessionID] .= "Session $sessionLabel";
                $query_segment_tablemap[$sessionID] .= "</center>\n";
                $query_segment_tablemap[$sessionID] .= "</div>\n";
//
//                $session_panels[$sessionID] .= "<form id=\"task_form_$sessionID\" action=\"../services/utils/runPageQueryUtils.php?action=markTask\">\n";
                $query_segment_tablemap[$sessionID] .= "<div class=\"panel-body\" id=\"session_panel_$sessionID\">\n";
                $query_segment_tablemap[$sessionID] .= "<div class=\"tab-pane\">\n";
                $query_segment_tablemap[$sessionID] .= "<table class=\"table table-bordered table-fixed\">
                <thead>
                                <tr>
                                    <th >Mark Search Segments</th>
                                    <th >Mark Intentions</th>
                                    <th >Time</th>
                                    <th >Type</th>
                                    
                                    
                                    <th >Search Segment</th>
                                    <th >Title/Query</th>
                                    <th >Domain</th>




                                </tr>
                                </thead>
                                <tbody>";

                $row_index = 0;
                $querySegmentVisibleLabel = 0;
                $querySegmentIDs_visited = array();
                foreach($pq_session as $page){
                    $n_total_querysegments += 1;
                    $data_total_row_index += 1;
                    $row_index += 1;
                    $querySegmentID = $page['querySegmentID'];
                    $querySegmentLabel = '';
                    if(!is_null($querySegmentID)){
                        $querySegmentLabel = $querySegmentIDToLabel[$querySegmentID];
                        if(!in_array($querySegmentID,$querySegmentIDs_visited)){
                            array_push($querySegmentIDs_visited,$querySegmentID);
                        }
                        $querySegmentLabel = strval(array_search($querySegmentID,$querySegmentIDs_visited))+1;
                    }





                    if(isset($querySegmentToIntent[$querySegmentID])){

                        $class='';
                        if(!isset( $even_odd_index[$sessionID][$querySegmentID]  )){
                            $last_index += 1;
                            $last_index = $last_index % 2;
                            $even_odd_index[$sessionID][$querySegmentID] = $last_index;
                        }
                        $class=$even_odd_styles[$even_odd_index[$sessionID][$querySegmentID]];

//                        if(!isset($even_odd_index[$sessionID])){
//                            $even_odd_index[$sessionID] = array();
//                        }

                        $query_segment_tablemap[$sessionID] .= "<tr data-table-index=\"$data_table_index\"  data-session-id='$sessionID' data-session-label='$sessionLabel' data-row-index=\"$row_index\" data-total-row-index='$data_total_row_index' class='$class' data-query-segment-id='$querySegmentID'>";
                    }else{
                        $query_segment_tablemap[$sessionID] .= "<tr data-table-index=\"$data_table_index\"  data-session-id='$sessionID' data-session-label='$sessionLabel' data-row-index=\"$row_index\" data-total-row-index='$data_total_row_index' data-query-segment-id='$querySegmentID'>";
                    }


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

                    $intentions_button = "";

                    if(!is_null($querySegmentID)){
                        $n_marked_querysegments += 1;
                    }


                    if(isset($querySegmentToIntent[$querySegmentID]) and !in_array($querySegmentID,$querySegmentIDs_done)){
                        $n_marked_intents += 1;
                    }

                    if(!in_array($querySegmentID,$querySegmentIDs_done) and isset($page['querySegmentID']) and ($page['type']=='query')){
//                        $intentions_button = "<button name=\"initiate_mark_intentions_button\" data-toggle=\"modal\" data-target=\"#intent_modal\" data-table-index=\"$data_table_index\" data-row-index=\"$row_index\" data-query-segment-id=\"$querySegmentID\" type=\"button\" class=\"btn btn-success\">Mark Intentions</button>";
                        array_push($querySegmentIDs_done,$querySegmentID);
                        if(!isset($querySegmentToIntent[$querySegmentID])){
                            $intentions_button = "<button name=\"initiate_mark_intentions_button\" data-table-index=\"$data_table_index\" data-row-index=\"$row_index\" data-query-segment-id=\"$querySegmentID\" type=\"button\" class=\"btn btn-success\" onclick='show_intent_modal($querySegmentID)'>Mark Intentions</button>";

                        }

//                        $intentions_button = "<button name=\"initiate_mark_intentions_button\" data-toggle=\"modal\" data-target=\"#intent_modal\" data-table-index=\"$data_table_index\" data-row-index=\"$row_index\" data-query-segment-id=\"$querySegmentID\" type=\"button\" class=\"btn btn-success\" onclick='show_intent_modal($querySegmentID)'>Mark Intentions</button>";

                    }

                    if(!in_array($querySegmentID,$querySegmentIDs_done) and isset($page['querySegmentID']) and ($page['type']=='page')){
//                        $intentions_button = "<button name=\"initiate_mark_intentions_button\" data-toggle=\"modal\" data-target=\"#intent_modal\" data-table-index=\"$data_table_index\" data-row-index=\"$row_index\" data-query-segment-id=\"$querySegmentID\" type=\"button\" class=\"btn btn-success\">Mark Intentions</button>";
                        array_push($querySegmentIDs_done,$querySegmentID);
                        if(!isset($querySegmentToIntent[$querySegmentID])) {
                            $intentions_button = "<button name=\"initiate_mark_intentions_button\" data-table-index=\"$data_table_index\" data-row-index=\"$row_index\" data-query-segment-id=\"$querySegmentID\" type=\"button\" class=\"btn btn-success\" onclick='show_intent_modal($querySegmentID)'>Mark Intentions</button>";
                        }
//                        $intentions_button = "<button name=\"initiate_mark_intentions_button\" data-toggle=\"modal\" data-target=\"#intent_modal\" data-table-index=\"$data_table_index\" data-row-index=\"$row_index\" data-query-segment-id=\"$querySegmentID\" type=\"button\" class=\"btn btn-success\" onclick='show_intent_modal($querySegmentID)'>Mark Intentions</button>";

                    }

                    if(!is_null($querySegmentID) and !in_array($querySegmentID,$querySegmentIDs_total)){
                        array_push($querySegmentIDs_total,$querySegmentID);
                    }


                    $begin_button = '';
                    $end_button = '';
                    $filler = "<span name='filler' data-row-index='$row_index' data-session-id='$sessionID' data-table-index='$data_table_index'></span>";
                    if(true){
//                    if(is_null($querySegmentID) and $intentions_button==''){
                        $begin_button = "<button name=\"begin_button\" data-session-id='$sessionID' data-session-label='$sessionLabel' data-table-index=\"$data_table_index\" data-total-row-index='$data_total_row_index' data-row-index=\"$row_index\" type=\"button\" class=\"btn btn-success\">Begin</button>";
                        $end_button = "<button name=\"end_button\" data-session-id='$sessionID' data-session-label='$sessionLabel' data-table-index=\"$data_table_index\" data-total-row-index='$data_total_row_index' data-row-index=\"$row_index\" type=\"button\" class=\"btn btn-danger\">End</button>";
                    }

                    $badge = "";
                    if(isset($page['querySegmentID'])){
                        if($querySegmentIDsCount[$page['querySegmentID']] > 0){
                            $querySegmentIDsCount[$page['querySegmentID']] -= 1;

                            if($querySegmentIDsCount_start[$page['querySegmentID']]){
                                $badge .= "<h4 class='label label-primary'>Search Segment Begin</h4>";
                                $querySegmentIDsCount_start[$page['querySegmentID']] = False;
                            }

                            if($querySegmentIDsCount[$page['querySegmentID']] == 0){
                                $badge .= "<h4 class='label label-warning'>Search Segment End</h4>";
                            }

                        }

                    }


                    $query_segment_tablemap[$sessionID] .= "<td><input data-session-id=\"$sessionID\" data-table-index=\"$data_table_index\" data-row-index=\"$row_index\"  type=\"checkbox\" name='$name' value='$value' style='display:none'> $begin_button $end_button $badge $filler</td>";
                    $query_segment_tablemap[$sessionID] .= "<td><input  data-table-index=\"$table_index\" data-query-segment-id='$querySegmentID' type=\"checkbox\" name='$name' value='$value' style='display:none'> $intentions_button </td>";
                    $query_segment_tablemap[$sessionID] .="<td name=\"time_$table_index\">".(isset($page['time'])?$page['time']:"")."</td>";
                    $query_segment_tablemap[$sessionID] .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
                    $query_segment_tablemap[$sessionID] .="<td name='search-segment-id' data-session-id='$sessionID'>".(isset($page['querySegmentID']) ?$querySegmentLabel : "")."</td>";
//                    $query_segment_tablemap[$sessionID] .="<td>".(isset($page['querySegmentID']) ?$page['querySegmentID'] : "")."</td>";

//                    $query_segment_tablemap[$sessionID] .="<td>".(isset($page['querySegmentID']) ?$querySegmentVisibleLabel : "")."</td>";

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



                    $query_segment_tablemap[$sessionID] .= "<td name=\"title_$table_index\"><span title='$title'>$title_short</span></td>";
                    $query_segment_tablemap[$sessionID] .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
                    $table_index += 1;




                    $query_segment_tablemap[$sessionID] .= "</tr >";


                }
                $query_segment_tablemap[$sessionID] .= "</tbody>\n";
                $query_segment_tablemap[$sessionID] .= "</table>\n";
                $query_segment_tablemap[$sessionID] .= "</div>\n";
                $query_segment_tablemap[$sessionID] .= "</div>\n";
                $query_segment_tablemap[$sessionID] .= "</div>\n";

            }
        }


        $query_segment_table = '';
        foreach($sessionIDs as $sessionID){
            if(!is_null($sessionID)) {
                $query_segment_table .= $query_segment_tablemap[$sessionID];
            }
        }





        $n_total_intents = count($querySegmentIDs_total);
        if($n_marked_querysegments==$n_total_querysegments){
//            $progress_bar_segments = "<div class=\"panel panel-success\">";
//            $progress_bar_segments .= "<div class=\"panel-heading\">";
            $progress_bar_segments .= "<h2><div class='label label-success'>Progress: All Search Segments Marked!</div></h2>";
//            $progress_bar_segments .= "</div>";
//            $progress_bar_segments .= "</div>";

        }else{
//            $progress_bar_segments = "<div class=\"panel panel-warning\">";
//            $progress_bar_segments .= "<div class=\"panel-heading\">";
            $progress_bar_segments .= "<h2><div class='label label-warning'>Progress: ".intval($n_marked_querysegments/floatval($n_total_querysegments)*100)."% Search Segments Marked</div></h2>";

//            $progress_bar_segments .= "</div>";
//            $progress_bar_segments .= "</div>";

        }


        if($n_marked_intents==$n_total_intents){
//            $progress_bar_intents .= "<div class=\"panel panel-success\">";
//            $progress_bar_intents .= "<div class=\"panel-heading\">";
            $progress_bar_intents .= "<h2><div class='label label-success'>Progress: All Intents Marked!</h2></div>";
//            $progress_bar_intents .= "</div>";
//            $progress_bar_intents .= "</div>";

        }else{
//            $progress_bar_intents .= "<div class=\"panel panel-warning\">";
//            $progress_bar_intents .= "<div class=\"panel-heading\">";
            $progress_bar_intents .= "<h2><div class='label label-warning'>Progress: ".intval($n_marked_intents/floatval($n_total_intents)*100)."% Intents Marked</div></h2>";

//            $progress_bar_intents .= "<center><h4>Progress: $n_marked_intents $n_total_intents ".intval($n_marked_intents/floatval($n_total_intents)*100)."% Intents Marked</h4></center>";
//            $progress_bar_intents .= "</div>";
//            $progress_bar_intents .= "</div>";

        }



//        $query_segment_table = "<table class=\"table table-bordered table-striped table-fixed\">
//                                <thead>
//                                <tr>
//                                    <th >Time</th>
//                                    <th >Type</th>
//                                    <th >Mark</th>
//                                    <th >Session</th>
//                                    <th >Title/Query</th>
//                                    <th >Domain</th>
//
//
//
//
//                                </tr>
//                                </thead>
//                                <tbody>";
//
//        foreach($pages as $page){
//            $query_segment_table .= "<tr >";
//            $query_segment_table .="<td name=\"time_$table_index\">".(isset($page['time'])?$page['time']:"")."</td>";
//
//            $name = '';
//            $color = '';
//            if($page['type']=='page'){
//                $name='pages[]';
//                $color = 'class="warning"';
//            }else{
//                $name='queries[]';
//                $color = 'class="info"';
//            }
//            $value = $page['id'];
//
//            $query_segment_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
//            $begin_button = "<button name=\"begin_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-success\">Begin</button>";
//            $end_button = "<button name=\"end_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-danger\">End</button>";
//            $query_segment_table .= "<td><input data-table-index=\"$table_index\" type=\"checkbox\" name='$name' value='$value'> $begin_button $end_button </td>";
////        $query_segment_table .="<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>";
//            $query_segment_table .="<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
//            $query_segment_table .="<td>".(isset($page['querySegmentID']) ?$page['querySegmentID'] : "")."</td>";
//            $query_segment_table .= "<td name=\"title_$table_index\"><span title='".(isset($page['title'])?$page['title']:"")."'>".(isset($page['title'])?substr($page['title'],0,50)."...":"")."</span></td>";
//            $query_segment_table .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
//            $table_index += 1;
//
//
//
//
//            $query_segment_table .= "</tr >";
//
//        }
//        $query_segment_table .= "</tbody>
//                    </table>";


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

    return array('progressbar_intents_html'=>$progress_bar_intents,'progressbar_segments_html'=>$progress_bar_segments,'querysegmenthtml'=>$query_segment_panel_html);
}


function getSessionQuestionnaireTables($userID,$startTimestamp,$endTimestamp){


    $sessionIDs = getSessionIDs($userID,$startTimestamp,$endTimestamp);

    $sessionIDToLabel = array();
    $query = "SELECT * FROM session_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $sessionIDToLabel[$line['id']] = $line['sessionLabel'];
    }



    $sessionIDToQuestionnaire_complete = array();
    $query = "SELECT * FROM questionnaire_exit_sessions WHERE userID=$userID AND `successful` IS NOT NULL GROUP BY sessionID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $sessionIDToQuestionnaire_complete[$line['sessionID']] = $line['successful'];
    }




    $taskIDNameMap = getTaskIDNameMap($userID);


    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,-1);
    $pages =$pagesQueries;
    $table_index = 0;
    $query_segment_table = '';
    $query_segment_panel_html = '';
    $query_segment_tablemap = array();
    $querySegmentIDs_done = array();
    $pageIDs_done = array();
    $querySegmentIDs_total = array();
    $querySegmentToIntent = array();
    $query = "SELECT querySegmentID,assignmentID FROM intent_assignments WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $querySegmentToIntent[$line['querySegmentID']] = $line['assignmentID'];
    }

    $querySegmentIDToLabel = array();



    $query = "SELECT * FROM querysegment_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
        $querySegmentIDToLabel[$line['id']] = $line['querySegmentLabel'];
    }

    $n_marked_querysegments = 0;
    $n_total_querysegments = 0;

    $n_marked_intents = 0;
    $n_total_intents = 0;

    $n_marked_sessions = 0;
    $n_total_sessions = 0;

    $progress_bar_segments = '';
    $progress_bar_intents = '';


    $querySegmentIDsCount = array();
    $querySegmentIDsCount_total = array();
    $querySegmentIDsCount_start = array();
    $cxn = Connection::getInstance();

    $result = $cxn->commit("SELECT querySegmentID,COUNT(*) as ct FROM pages WHERE querySegmentID IS NOT NULL and userID=$userID GROUP BY querySegmentID");
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        $querySegmentIDsCount[$line['querySegmentID']] = $line['ct'];
        $querySegmentIDsCount_total[$line['querySegmentID']] = $line['ct'];
        $querySegmentIDsCount_start[$line['querySegmentID']] = True;
    }

    $result = $cxn->commit("SELECT querySegmentID,COUNT(*) as ct FROM queries WHERE querySegmentID IS NOT NULL and userID=$userID GROUP BY querySegmentID");
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        if(isset($querySegmentIDsCount[$line['querySegmentID']])){
            $querySegmentIDsCount[$line['querySegmentID']] += $line['ct'];
            $querySegmentIDsCount_total[$line['querySegmentID']] += $line['ct'];
        }else{
            $querySegmentIDsCount[$line['querySegmentID']] = $line['ct'];
            $querySegmentIDsCount_total[$line['querySegmentID']] = $line['ct'];
        }
    }

    if(count($pages)<=0){
        $query_segment_table = '<center><h3 class=\'bg-danger\'>You logged no activity. Please search and browse.</h3></center>';
        $query_segment_panel_html = '<center><h3 class=\'bg-danger\'>You logged no activity. Please search and browse.</h3></center>';
    }else{




        $even_odd_styles = array(0=>'info',1=>'success');
        $even_odd_index = array();
        $last_index = 0;

        $data_table_index = 0;
        $data_total_row_index = 0;


        $panel_index = 0;
        foreach($sessionIDs as $sessionID) {
            $data_table_index += 1;
            $panel_index += 1;
            if(!is_null($sessionID)){
                if(!isset($even_odd_index[$sessionID])){
                    $even_odd_index[$sessionID] = array();
                }
                $sessionLabel = $sessionIDToLabel[$sessionID];
                $pq_session = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,$sessionID);

                $pages =$pq_session;




                $marked='';
                $panel_marked = '';
                $panel_heading_marked = '';
                if(count($pages) > 0){
                    $all_marked = isset($sessionIDToQuestionnaire_complete[$sessionID]);


                    if($all_marked){
                        $n_marked_sessions += 1;

                        $marked= "<span >Marked! (Session $sessionLabel)</span>";
//                        $panel_marked = "<div class=\"panel panel-success\">\n";
                        $panel_marked = "<div class=\"panel panel-success container-fluid\">\n";
                        $panel_heading_marked = "<div class=\"panel-heading row\" data-panel-index='$panel_index'>\n";;
                    }else{

                        $questionnaire_button = "<button type=\"button\" data-session-id=\"$sessionID\" name=\"initiate_mark_intentions_button\" onclick='show_questionnaire_modal($sessionID)' class=\"btn btn-success\">Complete Questionnaire</button>";
                        $marked="<span>Not Marked (Session $sessionLabel: Select for Annotation)<div>$questionnaire_button</div></span>";

//                        $panel_marked = "<div class=\"panel panel-warning\">\n";
                        $panel_marked = "<div class=\"panel panel-warning container-fluid\">\n";
                        $panel_heading_marked = "<div class=\"panel-heading row\" data-panel-index='$panel_index'>\n";;
                    }
                    $n_total_sessions += 1;
                }



//                $query_segment_tablemap[$sessionID] = "<div class=\"panel panel-primary\">\n";
                $query_segment_tablemap[$sessionID] = $panel_marked;
                $query_segment_tablemap[$sessionID] .= $panel_heading_marked;
                $query_segment_tablemap[$sessionID] .= "<center>\n";
                $query_segment_tablemap[$sessionID] .= "Session $marked";
//                $query_segment_tablemap[$sessionID] .= "Session $sessionLabel";
                $query_segment_tablemap[$sessionID] .= "</center>\n";

                $query_segment_tablemap[$sessionID] .= "</div>\n";
//
//                $session_panels[$sessionID] .= "<form id=\"task_form_$sessionID\" action=\"../services/utils/runPageQueryUtils.php?action=markTask\">\n";
                $query_segment_tablemap[$sessionID] .= "<div class=\"panel-body\" id=\"session_panel_$sessionID\">\n";
                $query_segment_tablemap[$sessionID] .= "<div class=\"tab-pane\">\n";
                $query_segment_tablemap[$sessionID] .= "<table class=\"table table-bordered table-fixed\">
                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Task</th>
                                    <th >Title/Query</th>
                                    <th >Domain</th>




                                </tr>
                                </thead>
                                <tbody>";

                $row_index = 0;
                $querySegmentVisibleLabel = 0;
                $querySegmentIDs_visited = array();
                foreach($pq_session as $page){
                    $n_total_querysegments += 1;
                    $data_total_row_index += 1;
                    $row_index += 1;
                    $querySegmentID = $page['querySegmentID'];
                    $querySegmentLabel = '';
                    if(!is_null($querySegmentID)){
                        $querySegmentLabel = $querySegmentIDToLabel[$querySegmentID];
                        if(!in_array($querySegmentID,$querySegmentIDs_visited)){
                            array_push($querySegmentIDs_visited,$querySegmentID);
                        }
                        $querySegmentLabel = strval(array_search($querySegmentID,$querySegmentIDs_visited))+1;
                    }





                    if(isset($querySegmentToIntent[$querySegmentID])){

                        $class='';
                        if(!isset( $even_odd_index[$sessionID][$querySegmentID]  )){
                            $last_index += 1;
                            $last_index = $last_index % 2;
                            $even_odd_index[$sessionID][$querySegmentID] = $last_index;
                        }
//                        $class=$even_odd_styles[$even_odd_index[$sessionID][$querySegmentID]];

//                        if(!isset($even_odd_index[$sessionID])){
//                            $even_odd_index[$sessionID] = array();
//                        }

                        $query_segment_tablemap[$sessionID] .= "<tr data-table-index=\"$data_table_index\"  data-session-id='$sessionID' data-session-label='$sessionLabel' data-row-index=\"$row_index\" data-total-row-index='$data_total_row_index' data-query-segment-id='$querySegmentID'>";
//                        $query_segment_tablemap[$sessionID] .= "<tr data-table-index=\"$data_table_index\"  data-session-id='$sessionID' data-session-label='$sessionLabel' data-row-index=\"$row_index\" data-total-row-index='$data_total_row_index' class='$class' data-query-segment-id='$querySegmentID'>";
                    }else{
                        $query_segment_tablemap[$sessionID] .= "<tr data-table-index=\"$data_table_index\"  data-session-id='$sessionID' data-session-label='$sessionLabel' data-row-index=\"$row_index\" data-total-row-index='$data_total_row_index' data-query-segment-id='$querySegmentID'>";
                    }


                    $color = '';
                    if($page['type']=='page'){
                        $color = 'class="warning"';
                    }else{
                        $color = 'class="info"';
                    }





                    $query_segment_tablemap[$sessionID] .="<td name=\"time_$table_index\">".(isset($page['time'])?$page['time']:"")."</td>";
                    $query_segment_tablemap[$sessionID] .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";

                    $query_segment_tablemap[$sessionID].= "<td >".(isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"")."</td>\n";
//                    $query_segment_tablemap[$sessionID] .="<td name='search-segment-id' data-session-id='$sessionID'>".(isset($page['querySegmentID']) ?$querySegmentLabel : "")."</td>";
//                    $query_segment_tablemap[$sessionID] .="<td>".(isset($page['querySegmentID']) ?$page['querySegmentID'] : "")."</td>";

//                    $query_segment_tablemap[$sessionID] .="<td>".(isset($page['querySegmentID']) ?$querySegmentVisibleLabel : "")."</td>";

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



                    $query_segment_tablemap[$sessionID] .= "<td name=\"title_$table_index\"><span title='$title'>$title_short</span></td>";
                    $query_segment_tablemap[$sessionID] .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
                    $table_index += 1;




                    $query_segment_tablemap[$sessionID] .= "</tr >";


                }
                $query_segment_tablemap[$sessionID] .= "</tbody>\n";
                $query_segment_tablemap[$sessionID] .= "</table>\n";
                $query_segment_tablemap[$sessionID] .= "</div>\n";
                $query_segment_tablemap[$sessionID] .= "</div>\n";
                $query_segment_tablemap[$sessionID] .= "</div>\n";

            }
        }


        $query_segment_table = '';
        foreach($sessionIDs as $sessionID){
            if(!is_null($sessionID)) {
                $query_segment_table .= $query_segment_tablemap[$sessionID];
            }
        }





        if($n_marked_sessions==$n_total_sessions){
            $progress_bar_segments .= "<h2><div class='label label-success'>Progress: All Search Segments Marked!</div></h2>";
        }else{
            $progress_bar_segments .= "<h2><div class='label label-warning'>Progress: ".intval($n_marked_sessions/floatval($n_total_sessions)*100)."% Search Segments Marked</div></h2>";

        }



//        $query_segment_table = "<table class=\"table table-bordered table-striped table-fixed\">
//                                <thead>
//                                <tr>
//                                    <th >Time</th>
//                                    <th >Type</th>
//                                    <th >Mark</th>
//                                    <th >Session</th>
//                                    <th >Title/Query</th>
//                                    <th >Domain</th>
//
//
//
//
//                                </tr>
//                                </thead>
//                                <tbody>";
//
//        foreach($pages as $page){
//            $query_segment_table .= "<tr >";
//            $query_segment_table .="<td name=\"time_$table_index\">".(isset($page['time'])?$page['time']:"")."</td>";
//
//            $name = '';
//            $color = '';
//            if($page['type']=='page'){
//                $name='pages[]';
//                $color = 'class="warning"';
//            }else{
//                $name='queries[]';
//                $color = 'class="info"';
//            }
//            $value = $page['id'];
//
//            $query_segment_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
//            $begin_button = "<button name=\"begin_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-success\">Begin</button>";
//            $end_button = "<button name=\"end_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-danger\">End</button>";
//            $query_segment_table .= "<td><input data-table-index=\"$table_index\" type=\"checkbox\" name='$name' value='$value'> $begin_button $end_button </td>";
////        $query_segment_table .="<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>";
//            $query_segment_table .="<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
//            $query_segment_table .="<td>".(isset($page['querySegmentID']) ?$page['querySegmentID'] : "")."</td>";
//            $query_segment_table .= "<td name=\"title_$table_index\"><span title='".(isset($page['title'])?$page['title']:"")."'>".(isset($page['title'])?substr($page['title'],0,50)."...":"")."</span></td>";
//            $query_segment_table .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
//            $table_index += 1;
//
//
//
//
//            $query_segment_table .= "</tr >";
//
//        }
//        $query_segment_table .= "</tbody>
//                    </table>";


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

    return array('progressbar_segments_html'=>$progress_bar_segments,'querysegmenthtml'=>$query_segment_panel_html);
}

function makeNextQuerySegmentID($userID,$startTimestamp){
    $base = Base::getInstance();
    date_default_timezone_set($base->getUserTimezone());
    $date = date('Y-m-d', $startTimestamp);
    $query = "SELECT IFNULL(MAX(querySegmentLabel),0) as maxQuerySegmentID FROM querysegment_labels_user WHERE userID='$userID' AND `date`='$date'";
//    $query = "SELECT IFNULL(MAX(querySegmentID),0) as maxQuerySegmentID FROM querysegment_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);

    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    $querySegmentID = $line['maxQuerySegmentID']+1;
    $query = "INSERT INTO querysegment_labels_user (`userID`,`projectID`,`querySegmentLabel`,`deleted`,`date`) VALUES ('$userID','$userID','$querySegmentID',0,'$date')";
    $cxn->commit($query);
    return $querySegmentID;
}

function markQuerySegmentID($userID,$querySegmentID,$pageIDs,$queryIDs){
    $cxn = Connection::getInstance();
    if(count($queryIDs) > 0){
        $queryID_list = implode(",",$queryIDs);
        $query = "UPDATE queries SET `querySegmentID`='$querySegmentID',`querySegmentID_automatic`=0 WHERE `userID`='$userID' AND `queryID` IN ($queryID_list)";
        $cxn->commit($query);
    }

    if(count($pageIDs) > 0){
        $pageID_list = implode(",",$pageIDs);
        $query = "UPDATE pages SET `querySegmentID`='$querySegmentID',`querySegmentID_automatic`=0 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
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
        $session_panels_html = "<center><h3 class='bg-danger'>You have marked no search segment. Please go back and mark some.</h3></center>";
    }
    else{
        foreach($querySegmentIDs as $querySegmentID){
            if(!is_null($querySegmentID)){
                $session_panels[$querySegmentID] = "<div class=\"panel panel-primary\">\n";
                $session_panels[$querySegmentID] .= "<div class=\"panel-heading\">\n";
                $session_panels[$querySegmentID] .= "<center>\n";
                $session_panels[$querySegmentID] .= "<input type=\"radio\" name=\"querySegmentID\" id=\"querySegmentID_radio_$querySegmentID\" value=\"$querySegmentID\">\n";
                $session_panels[$querySegmentID] .= "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#intentions_panel_$querySegmentID\">Search Segment $querySegmentID (Show/Hide)</button>\n";
                $session_panels[$querySegmentID] .= "</center>\n";
                $session_panels[$querySegmentID] .= "</div>\n";
                $session_panels[$querySegmentID] .= "<form id=\"intentions_form_$querySegmentID\" action=\"../services/utils/runPageQueryUtils.php?action=markIntentions\">\n";
                $session_panels[$querySegmentID] .= "<div class=\"panel-body collapse\" id=\"intentions_panel_$querySegmentID\">\n";
                $session_panels[$querySegmentID] .= "<div class=\"tab-pane\">\n";
                $session_panels[$querySegmentID] .= "<table class=\"table table-bordered table-fixed \">\n";
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

                    $session_panels[$querySegmentID] .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
                    $session_panels[$querySegmentID] .= "<td >".(isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"")."</td>\n";
                    $session_panels[$querySegmentID].= "<td><span title='$title'>$title_short</span></td>";
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
        $null_panel .= "<center><h4>Unassigned to Search Segment:</h4></center>";

        $null_panel .= "<center><button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#session_panel_null\">No Session (Show/Hide)</button></center>\n";
        $null_panel .= "</div>";

        $null_panel .= "<div class=\"panel-body collapse\" id=\"session_panel_null\">\n";
        $null_panel .= "<center><h3 class=\"bg-danger\">Please assign these to a search segment</h3></center>";
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

            $null_panel.= "<td><span title='$title'>$title_short</span></td>";
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

function getIntentionsPanel_copy($userID,$startTimestamp,$endTimestamp){


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


    $intention_explanations = array(
        'id_start'=>'For instance, find good query terms.',
        'id_more'=>'Explore a topic more broadly.',
//        'learn_feature'=>'Learn system feature',
//        'learn_structure'=>'Learn system structure',
        'learn_domain'=>'Learn about the topic of a search.',
        'learn_database'=>'Learn the type of information/resources available at a particular website – e.g., a government database.',
        'find_known'=>'Searching for an item that you were familiar with in advance.',
        'find_specific'=>'Finding a predetermined piece of information.',
        'find_common'=>'Finding items with something in common.',
        'find_without'=>'Finding items that will be useful for a task, but which haven\'t been specified in advance.',
//        'locate_specific'=>'Locate a specific item',
//        'locate_common'=>'Locate items with common characteristics',
//        'locate_area'=>'Locate an area/location',
//        'keep_bibliographical'=>'Keep record of bibliographical information',
        'keep_link'=>'Saving a good item or an item to look at later.',
//        'keep_item'=>'Note item for return',
        'access_item'=>'Go to some item that you already know about.',
        'access_common'=>'Go to some set of items with common characteristics.',
        'access_area'=>'Relocating or going to a website.',
        'evaluate_correctness'=>'Determine whether an item is factually correct.',
        'evaluate_specificity'=>'Determine whether an item is specific or general enough.',
        'evaluate_usefulness'=>'Determine whether an item is useful.',
        'evaluate_best'=>'Determine the best item among a set of items.',
        'evaluate_duplication'=>'Determine whether the information in one item is the same as in another or others.',
        'obtain_specific'=>'Finding specific information to bookmark, highlight, or copy.',
        'obtain_part'=>'Finding part of an item to bookmark, highlight, or copy.',
        'obtain_whole'=>'Finding a whole item to bookmark, highlight, or copy.',
        'other'=>'If you have another intention, please explain here.'

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
        $tooltip = $intention_explanations[$key];

        $intentions_html .="<tr>";
        $intentions_html .="<td>";
        $intentions_html .="<div class=\"checkbox\">";
        $intentions_html .="<label>";
        $intentions_html .="<input type=\"checkbox\" data-intent-key='$key' name='intentions[]' value='$key'/> $value ";

//        $intentions_html .="<input type=\"checkbox\" data-toggle=\"collapse\" data-target=\"#intention_submenu_$key\" name='intentions[]' value='$key'/> $value";
        $intentions_html .="</label>";
        $intentions_html .="</div>";
        $intentions_html .= "<i class=\"fa fa-info-circle fa-2x\" data-title=\"$tooltip\" aria-hidden=\"true\" style='color:dodgerblue; cursor:pointer'></i>";
        if($key=='other'){
            $intentions_html .= "<br/>Description: <textarea class=\"form-control\" rows=\"3\" cols=\"40\" name=\"$key"."_description\" disabled></textarea>";
        }
        $intentions_html .="</td>";

        $intentions_html .="<td>";
        $intentions_html .="<div id='intention_submenu_$key'>";
//        $intentions_html .="<div id='intention_submenu_$key' class='collapse'>";
        $intentions_html .= "<div class='radio'>";
        $intentions_html .= "<label><input type='radio' data-intent-key='$key' name='$key"."_success' value='1' disabled> Yes</label>";
        $intentions_html .= "</div>";
        $intentions_html .= "<div class='radio'>";
        $intentions_html .= "<label><input type='radio' data-intent-key='$key' name='$key"."_success' value='0' disabled> No</label>";
//        $intentions_html .= "<input type='radio' name='$key"."_success' value='0' data-toggle=\"collapse\" data-target=\"#failure_submenu_$key\"> No";
        $intentions_html .= "</div>";
        $intentions_html .= "</div>";


        $intentions_html .="</td>";


        $intentions_html .="<td>";
        $intentions_html .="<div id='failure_submenu_$key'>";
//        $intentions_html .="<div id='failure_submenu_$key' class='collapse'>";
//        $intentions_html .= "Why Not?";
        $intentions_html .= "<textarea class=\"form-control\" rows=\"3\" cols=\"40\" name=\"$key"."_failure_reason\" disabled></textarea>";
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
//    $intentions_html .= "<center>
//            <h3 id=\"addintentions_confirmation\" class=\"alert alert-success\"></h3>
//            </center>";

    $intentions_html .= "<center>
            </center>";

    $intentions_html .= "</div>";
    $intentions_html .=  "</div>";

    $intentions_html .= "</table>";
    $intentions_html .= "</center>";

    $intentions_html .= "</form>";
    $intentions_html .= "</div>";


    $intentionsfooter_html = "";
    $intentions_html .= "<center>";
    $intentions_html .= "<input type=\"hidden\" name=\"userID\" value='$userID'/>";
    $intentions_html .= "<input type=\"hidden\" name=\"startTimestamp\" value='$startTimestamp'/>";
    $intentions_html .= "<input type=\"hidden\" name=\"endTimestamp\" value='$endTimestamp'/>";
    $intentions_html .= "<button type=\"button\" name='mark_intentions_button' value='mark_intentions_button' class=\"btn btn-primary\">Mark Search Segment + Intentions</button>";
    $intentions_html .= "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cancel</button>";
    $intentions_html .= "</center>";







    return array('intentionshtml'=>utf8_encode($intentions_html),'intentionsfooterhtml'=>utf8_encode($intentionsfooter_html));

}




function existPermutations($userID){
    $cxn = Connection::getInstance();
    $results = $cxn->commit("SELECT * FROM intent_permutations WHERE userID=$userID");
    return mysql_num_rows($results) > 0;
}


function createPermutations($userID){

    $intentionGroups = array(
        0=>'Identify search information',
        1=>'Learning',
        2=>'Finding',
        3=>'Keep record',
        4=>'Access an item or set of items',
        5=>'Evaluate',
        6=>'Obtain',
        7=>'Other'
    );

    $intentionsByGroup = array(
        0=>array(0,1),
        1=>array(2,3),
        2=>array(4,5,6,7),
        3=>array(8),
        4=>array(9,10,11),
        5=>array(12,13,14,15,16),
        6=>array(17,18,19),
        7=>array(20)
    );

    $cxn = Connection::getInstance();
    $p = range(0,6);
    shuffle($p);
    foreach($p as $key){
        $intentGroupID = $key;
        foreach($intentionsByGroup[$intentGroupID] as $intentID){
            $query = "INSERT INTO intent_permutations (userID,projectID,intentGroupID,intentID) VALUES ($userID,$userID,$intentGroupID,$intentID)";
            $cxn->commit($query);

        }
    }

    $query = "INSERT INTO intent_permutations (userID,projectID,intentGroupID,intentID) VALUES ($userID,$userID,7,20)";
    $cxn->commit($query);

}

function getIntentionsPanel($userID,$startTimestamp,$endTimestamp){

    if(!existPermutations($userID)){
        createPermutations($userID);
    }


    $intentionGroups = array(
        0=>'Identify search information',
        1=>'Learning',
        2=>'Finding',
        3=>'Keep record',
        4=>'Access an item or set of items',
        5=>'Evaluate',
        6=>'Obtain',
        7=>'Other'
    );

    $intentionsByGroup = array(
        0=>array(0,1),
        1=>array(2,3),
        2=>array(4,5,6,7),
        3=>array(8),
        4=>array(9,10,11),
        5=>array(12,13,14,15,16),
        6=>array(17,18,19),
        7=>array(20)
    );

    $intentionsByIndex = array(
        0=>'id_start',
        1=>'id_more',
        2=>'learn_domain',
        3=>'learn_database',
        4=>'find_known',
        5=>'find_specific',
        6=>'find_common',
        7=>'find_without',
        8=>'keep_link',
        9=>'access_item',
        10=>'access_common',
        11=>'access_area',
        12=>'evaluate_correctness',
        13=>'evaluate_specificity',
        14=>'evaluate_usefulness',
        15=>'evaluate_best',
        16=>'evaluate_duplication',
        17=>'obtain_specific',
        18=>'obtain_part',
        19=>'obtain_whole',
        20=>'other'
    );

    $intentions = array(
        'id_start'=>'Identify something to get started',
        'id_more'=>'Identify more to search',
        'learn_domain'=>'Learn domain knowledge',
        'learn_database'=>'Learn database content',
        'find_known'=>'Find a known item',
        'find_specific'=>'Find specific information',
        'find_common'=>'Find items sharing a named characteristic',
        'find_without'=>'Find items without predefined criteria',
        'keep_link'=>'Keep record of link',
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


    $intention_explanations = array(
        'id_start'=>'For instance, find good query terms.',
        'id_more'=>'Explore a topic more broadly.',
        'learn_domain'=>'Learn about the topic of a search.',
        'learn_database'=>'Learn the type of information/resources available at a particular website – e.g., a government database.',
        'find_known'=>'Searching for an item that you were familiar with in advance.',
        'find_specific'=>'Finding a predetermined piece of information.',
        'find_common'=>'Finding items with something in common.',
        'find_without'=>'Finding items that will be useful for a task, but which haven\'t been specified in advance.',
        'keep_link'=>'Saving a good item or an item to look at later.',
        'access_item'=>'Go to some item that you already know about.',
        'access_common'=>'Go to some set of items with common characteristics.',
        'access_area'=>'Relocating or going to a website.',
        'evaluate_correctness'=>'Determine whether an item is factually correct.',
        'evaluate_specificity'=>'Determine whether an item is specific or general enough.',
        'evaluate_usefulness'=>'Determine whether an item is useful.',
        'evaluate_best'=>'Determine the best item among a set of items.',
        'evaluate_duplication'=>'Determine whether the information in one item is the same as in another or others.',
        'obtain_specific'=>'Finding specific information to bookmark, highlight, or copy.',
        'obtain_part'=>'Finding part of an item to bookmark, highlight, or copy.',
        'obtain_whole'=>'Finding a whole item to bookmark, highlight, or copy.',
        'other'=>'If you have another intention, please explain here.'

    );








    $intentions_html = "<form id=\"mark_intentions_form\" action=\"../services/utils/runPageQueryUtils.php?action=markIntentions\">";


    $intentions_html .= "<input type=\"hidden\" name=\"userID\" value='$userID'/>";
    $intentions_html .= "<input type=\"hidden\" name=\"startTimestamp\" value='$startTimestamp'/>";
    $intentions_html .= "<input type=\"hidden\" name=\"endTimestamp\" value='$endTimestamp'/>";
    $intentions_html .= "<button type=\"button\" name='mark_intentions_button' value='mark_intentions_button' class=\"btn btn-primary\">Mark Search Segment + Intentions</button>";
    $intentions_html .= "<button type=\"button\" name='cancel_intentions_button' id='cancel_intentions_button' class=\"btn btn-default\" >Cancel</button>";
    $intentions_html .= "<hr/>";







    $cxn = Connection::getInstance();
    $results = $cxn->commit("SELECT * FROM intent_permutations WHERE userID=$userID ORDER BY id ASC");
    $intentions_html .="<div class='form-group'>";

    $prevIntentGroupID = -1;
    while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
        $intentID = $line['intentID'];
        $intentGroupID = $line['intentGroupID'];
        $tooltip = $intention_explanations[$intentionsByIndex[$intentID]];

        if($prevIntentGroupID!=$intentGroupID){
            $intentions_html .="<legend>".$intentionGroups[$intentGroupID]."</legend>";

        }

        $key = $intentionsByIndex[$intentID];
        $value = $intentions[$key];

        $intentions_html .="<div class='checkbox'>";
        $intentions_html .="<label>";
        $intentions_html .="<input type=\"checkbox\" data-intent-key='$key' name='intentions[]' value='$key'/> $value ";
        $intentions_html .="</label>";
        $intentions_html .= "&nbsp<i class=\"fa fa-info-circle\" data-toggle='tooltip' title=\"$tooltip\" data-title=\"$tooltip\" aria-hidden=\"true\" style='color:dodgerblue; cursor:pointer'></i>";
        $intentions_html .="</div>";

        if($key=='other'){
            $intentions_html .= "<div id='other_description_div' style='display:none'>Description: <textarea class=\"form-control\" rows=\"3\" cols=\"40\" name=\"$key"."_description\" disabled></textarea></div>";
        }

        $intentions_html .="<div id='success_div_$key' style='display:none'>";
        $intentions_html .="<h5>Were you successful?</h5>";
        $intentions_html .="<label class='radio-inline'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key"."_success' value='1' disabled> Yes";
        $intentions_html .="</label>";
        $intentions_html .="<label class='radio-inline'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key"."_success' value='0' disabled> No";
        $intentions_html .="</label>";
        $intentions_html .="</div>";



        $intentions_html .="<div id='failurereason_div_$key' style='display:none'>";
        $intentions_html .="<h5>Why not?</h5>";
        $intentions_html .= "<textarea class='form-control' rows=\"3\" cols=\"40\" name=\"$key"."_failure_reason\" disabled></textarea>";
        $intentions_html .="</div>";

        $prevIntentGroupID = $intentGroupID;
    }

    $intentions_html .="</div>";


    $intentions_html .= "</div>";
    $intentions_html .=  "</div>";


    $intentions_html .= "</form>";



    $intentionsfooter_html = "";

    return array('intentionshtml'=>utf8_encode($intentions_html),'intentionsfooterhtml'=>utf8_encode($intentionsfooter_html));

}


function getSearchQuestionnairePanel($userID,$startTimestamp,$endTimestamp){


    $intentions_html = "<form id=\"mark_intentions_form\" action=\"../services/utils/runPageQueryUtils.php?action=submitSessionQuestionnairePart1\">";


    $intentions_html .= "<input type=\"hidden\" name=\"userID\" value='$userID'/>";
    $intentions_html .= "<input type=\"hidden\" name=\"startTimestamp\" value='$startTimestamp'/>";
    $intentions_html .= "<input type=\"hidden\" name=\"endTimestamp\" value='$endTimestamp'/>";
    $intentions_html .= "<button type=\"button\" name='mark_intentions_button' value='mark_intentions_button' class=\"btn btn-primary\">Submit Answers</button>";
    $intentions_html .= "<button type=\"button\" name='cancel_intentions_button' id='cancel_intentions_button' class=\"btn btn-default\" >Cancel</button>";
    $intentions_html .= "<hr/>";




    $questions = array(
        'successful'=>'Was the search session successful?',
        'useful'=>'How useful was this search session in <br>accomplishing its goal?',
    );



    $cxn = Connection::getInstance();
    $results = $cxn->commit("SELECT * FROM intent_permutations WHERE userID=$userID ORDER BY id ASC");
    $intentions_html .="<div class='form-group'>";



    foreach($questions as $key=>$question_description){


        $intentions_html .="<div id='success_div_$key' class='container'>";
        $intentions_html .="<h5>$question_description</h5>";
        $intentions_html .="<label class='radio'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key' value='1'> 1 (Not at all)";
        $intentions_html .="</label>";
        $intentions_html .="<label class='radio'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key' value='2'> 2";
        $intentions_html .="</label>";
        $intentions_html .="<label class='radio'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key' value='3'> 3";
        $intentions_html .="</label>";
        $intentions_html .="<label class='radio'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key' value='4'> 4 (Moderately)";
        $intentions_html .="</label>";
        $intentions_html .="<label class='radio'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key' value='5'> 5";
        $intentions_html .="</label>";
        $intentions_html .="<label class='radio'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key' value='6'> 6";
        $intentions_html .="</label>";
        $intentions_html .="<label class='radio'>";
        $intentions_html .="<input type='radio' data-intent-key='$key' name='$key' value='7'> 7 (Completely)";
        $intentions_html .="</label>";

        $intentions_html .="</div>";



        $intentions_html .="<div id='failurereason_div_$key' style='display:none'>";
        $intentions_html .="<h5>Why do you say this?</h5>";
        $intentions_html .= "<textarea rows=\"3\" cols=\"40\" name=\"$key"."_description\" disabled></textarea>";
        $intentions_html .="</div>";

    }


    $intentions_html .=  "</div>";


    $intentions_html .= "</form>";



    $intentionsfooter_html = "";

    return array('questionnairehtml'=>utf8_encode($intentions_html),'questionnairefooterhtml'=>utf8_encode($intentionsfooter_html));

}


function markIntentions($userID,$querySegmentID,$checkedIntentions,$formData){

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

    foreach($intentions as $intention=>$isChecked){
        if(isset($formData[$intention."_success"])){
            $intentions[$intention."_success"] = $formData[$intention."_success"];
        }
        if(isset($formData[$intention."_failure_reason"])){
            $intentions[$intention."_failure_reason"] = mysql_escape_string($formData[$intention."_failure_reason"]);
        }



    }

    if(isset($formData["other_description"])){
        $intentions["other_description"] = mysql_escape_string($formData["other_description"]);
    }

    $keys = '(';
    $values = "(";

    foreach($intentions as $key=>$value){
        $keys .= "`$key`,";
        $values .= "'$value',";
    }

    $keys .= '`userID`,`querySegmentID`)';
    $values .= "'$userID','$querySegmentID')";
    $cxn = Connection::getInstance();
    $query="INSERT INTO intent_assignments $keys VALUES $values";
        $result = $cxn->commit($query);


}

function markSessionQuestionnaire($userID,$sessionID,$success,$useful,$success_description,$useful_description){
    $success_description = mysql_escape_string($success_description);
    $useful_description = mysql_escape_string($useful_description );

    $cxn = Connection::getInstance();
    $query="SELECT * FROM questionnaire_exit_sessions WHERE userID=$userID AND sessionID=$sessionID";
    $result = $cxn->commit($query);
    if(mysql_num_rows($result)>0){
        $query="UPDATE questionnaire_exit_sessions SET `sessionID`='$sessionID',`userID`=$userID,`successful`='$success',`successful_description`='$success_description',`useful`='$useful',`useful_description`='$useful_description' WHERE userID=$userID AND sessionID=$sessionID";
    }else{
        $query="INSERT INTO questionnaire_exit_sessions (`userID`,`sessionID`,`successful`,`successful_description`,`useful`,`useful_description`) VALUES ('$userID','$sessionID','$success','$success_description','$useful','$useful_description')";
    }
    $result = $cxn->commit($query);

    return $result;

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
