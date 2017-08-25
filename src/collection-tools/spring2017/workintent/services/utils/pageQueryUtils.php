<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
function getItems($userID,$startTimestamp,$endTimestamp,$type,$trash=0,$sessionID=-1,$querySegmentID=-1){
    $table = null;
    if($type=='pages'){
        $table ='pages';
    }else if($type=='queries'){
        $table='queries';
    }

    $sessionID_querysegment = '';

    if($sessionID != -1){
        if(is_null($sessionID)){
            $sessionID_querysegment = "and sessionID IS NULL";
        }else{
            $sessionID_querysegment = "and sessionID=$sessionID";
        }
    }



    $querySegmentID_querysegment = '';

    if($querySegmentID != -1){
        if(is_null($querySegmentID)){
            $querySegmentID_querysegment = "and querySegmentID IS NULL";
        }else{
            $querySegmentID_querysegment = "and querySegmentID=$querySegmentID";
        }
    }
    $startTimestampMillis = $startTimestamp * 1000.0;
    $endTimestampMillis = $endTimestamp * 1000.0;

    $query = "SELECT * FROM $table WHERE userID=$userID AND `is_coagmento`=0 AND `localTimestamp` >= $startTimestampMillis AND `localTimestamp` <= $endTimestampMillis AND `trash`='$trash' AND `permanently_delete`=0 $sessionID_querysegment $querySegmentID_querysegment ORDER BY `localTimestamp` ASC";
    $cxn = Connection::getInstance();
    $results = $cxn->commit($query);
    $rows = array();
    while($row = mysql_fetch_array($results,MYSQL_ASSOC))
    {
        $rows[] = $row;
    }
    return $rows;

}
function getPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0,$sessionID=-1,$querySegmentID=-1){
    $pages = getItems($userID,$startTimestamp,$endTimestamp,'pages',$trash,$sessionID,$querySegmentID);
    $queries = getItems($userID,$startTimestamp,$endTimestamp,'queries',$trash,$sessionID,$querySegmentID);
    return array('pages'=>$pages,'queries'=>$queries);
}

function getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0,$sessionID=-1,$querySegmentID=-1){
    $pages_queries = getPagesQueries($userID,$startTimestamp,$endTimestamp,$trash,$sessionID,$querySegmentID);
    $pages = $pages_queries['pages'];
    $queries = $pages_queries['queries'];

    $index_pages = 0;
    $index_queries = 0;
    $interleaved_objects = array();
    $lastpage = null;
    $lastquery = null;

    while($index_pages < count($pages) and $index_queries < count($queries)){
        $lastpage = $pages[$index_pages];
        $lastquery = $queries[$index_queries];
        if($lastpage['localTimestamp'] < $lastquery['localTimestamp']){
            $interleaved_objects[] = $lastpage;
            $interleaved_objects[count($interleaved_objects)-1]['type'] = 'page';
            $interleaved_objects[count($interleaved_objects)-1]['id'] = $lastpage['pageID'];
            $index_pages += 1;
        }else{
            $interleaved_objects[] = $lastquery;
            $interleaved_objects[count($interleaved_objects)-1]['type'] = 'query';
            $interleaved_objects[count($interleaved_objects)-1]['id'] = $lastquery['queryID'];
            $index_queries += 1;
        }
    }

    if($index_pages < count($pages) or $index_queries < count($queries)){
        if($index_pages < count($pages)){
            for($i=$index_pages;$i<count($pages);$i++){
                $interleaved_objects[] = $pages[$i];
                $interleaved_objects[count($interleaved_objects)-1]['type'] = 'page';
                $interleaved_objects[count($interleaved_objects)-1]['id'] = $pages[$i]['pageID'];
            }
        }else{
            for($i=$index_queries;$i<count($queries);$i++){
                $interleaved_objects[] = $queries[$i];
                $interleaved_objects[count($interleaved_objects)-1]['type'] = 'query';
                $interleaved_objects[count($interleaved_objects)-1]['id'] = $queries[$i]['queryID'];

            }
        }

    }

    return $interleaved_objects;
}



function getHomePageTables($userID,$startTimestamp,$endTimestamp){
    //    TODO: "No data" message
    $day_log = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,-1);
    $trash = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,1,-1);

    $day_table = '';
    $trash_table = '';
    if(count($day_log)<=0){
        $day_table = '<center><h3 class=\'bg-danger\'>You not done anything today.  Please log some activity.</h3></center>';
    }else{
        $day_table = "<table class=\"table table-bordered table-striped table-fixed\">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Delete</th>
                                    <th >Title/Query</th>
                                    <th >Domain</th>
                                </tr>
                                </thead>
                                <tbody>";

        foreach($day_log as $page){
            $day_table .= "<tr>";
            $day_table .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";

            $name = '';
            $color = '';
            if($page['type']=='page'){
                $name='pages[]';
                $color = 'class="warning"';
            }else{
                $name='queries[]';
                $color = 'class="info"';
            }

            $day_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";



            $value = $page['id'];

            $day_table .= "<td>"."<input type=\"checkbox\" name='$name' value='$value'>"."</td>";

//        $day_table .= "<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>"; //TODO: FIX
//        $day_table .= "<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
            $day_table .= "<td><span title='".(isset($page['title'])?htmlentities($page['title']):"")."'>".(isset($page['title'])?htmlentities(substr($page['title'],0,60))."...":"")."</span></td>";
            $day_table .= "<td><span title='".(isset($page['host'])?htmlentities($page['host']):"")."'>".(isset($page['host'])?htmlentities($page['host']):"")."</span></td>";

            $day_table .= "</tr>";

        }
        $day_table .= "</tbody>
                    </table>";


        $day_table .= "<div class=\"container\">";
        $day_table .= "<center>";
        $day_table .= "<input type=\"hidden\" name=\"userID\" value='$userID'/>";
        $day_table .= "<input type=\"hidden\" name=\"startTimestamp\" value='$startTimestamp'/>";
        $day_table .= "<input type=\"hidden\" name=\"endTimestamp\" value='$endTimestamp'/>";
        $day_table .= "<input type=\"submit\" id='send_trash_button' class=\"btn btn-warning\" value=\"Send to Trash\">";
        $day_table .= "</center>";

        $day_table .= "</div>";
    }


    if(count($trash)<=0){
        $trash_table = '<center><h3 class=\'bg-danger\'>Your trash is empty.</h3></center>';
    }else{
        $trash_table = "<table class=\"table table-bordered table-striped table-fixed\">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Select</th>
                                    
                                    <th >Title/Query</th>
                                    <th >Domain</th>
                                </tr>
                                </thead>
                                <tbody>";

        foreach($trash as $page){
            $trash_table .= "<tr>";
            $trash_table .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";

            $name = '';
            $color = '';
            if($page['type']=='page'){
                $name='pages[]';
                $color = 'class="warning"';
            }else{
                $name='queries[]';
                $color = 'class="info"';
            }
            $trash_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";

            $value = $page['id'];


            $trash_table .= "<td>"."<input type=\"checkbox\" name='$name' value='$value'>"."</td>";

//        $trash_table .= "<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>"; //TODO: FIX
//        $trash_table .= "<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
            $trash_table .= "<td><span title='".(isset($page['title'])?htmlentities($page['title']):"")."'>".(isset($page['title'])?htmlentities(substr($page['title'],0,60))."...":"")."</span></td>";
            $trash_table .= "<td><span title='".(isset($page['host'])?htmlentities($page['host']):"")."'>".(isset($page['host'])?htmlentities($page['host']):"")."</span></td>";
            $trash_table .= "</tr>";

        }

        $trash_table .= "</tbody>
                       </table>";

        $trash_table .= "<div class=\"container\">";
        $trash_table .= "<center>";
        $trash_table .= "<input type=\"hidden\" name=\"userID\" value='$userID'/>";
        $trash_table .= "<input type=\"hidden\" name=\"startTimestamp\" value='$startTimestamp'/>";
        $trash_table .= "<input type=\"hidden\" name=\"endTimestamp\" value='$endTimestamp'/>";
        $trash_table .= "<button type=\"button\" value=\"restore_button\" class=\"btn btn-success\">Undo Delete</button>";
        $trash_table .= "<button type=\"button\" value=\"permanently_delete_button\" class=\"btn btn-danger\">Permanently Delete</button>";
        $trash_table .= "</center>";

        $trash_table .= "</div>";

    }




//    echo $day_table;
    $tables = array('loghtml'=> utf8_encode($day_table),'trashhtml'=>utf8_encode($trash_table));
    return $tables;

}


function getSessionTables($userID,$startTimestamp,$endTimestamp){


    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,-1);
    $pages =$pagesQueries;
    $session_table = '';
    $session_panel_html = '';

    if(count($pages)<=0){
        $session_table = '<center><h3 class=\'bg-danger\'>You not done anything today.  Please log some activity.</h3></center>';
        $session_panel_html = '<center><h3 class=\'bg-danger\'>You not done anything today.  Please log some activity.</h3></center>';
    }else{
        $session_table = "<table class=\"table table-striped table-fixed\">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Mark</th>
                                    <th >Session</th>
                                    <th >Title/Query</th>
                                    <th >Domain</th>




                                </tr>
                                </thead>
                                <tbody>";


        $table_index = 0;




        foreach($pages as $page){
            $session_table .= "<tr >";
            $session_table .="<td name=\"time_$table_index\">".(isset($page['time'])?$page['time']:"")."</td>";

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

            $session_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
            $begin_button = "<button name=\"begin_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-success\">Begin</button>";
            $end_button = "<button name=\"end_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-danger\">End</button>";
            $session_table .= "<td><input data-table-index=\"$table_index\" type=\"checkbox\" name='$name' value='$value'> $begin_button $end_button </td>";
//        $session_table .="<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>";

            $session_table .="<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";

            $session_table .= "<td name=\"title_$table_index\"><span title='".(isset($page['title'])?htmlentities($page['title']):"")."'>".(isset($page['title'])?substr($page['title'],0,60)."...":"")."</span></td>";
            $session_table .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
            $session_table .= "</tr >";

            $table_index += 1;

        }
        $session_table .= "</tbody>
                    </table>";
        $session_table .= "<div class=\"container\">";
        $session_table .= "<center>";
        $session_table .= "<input type=\"hidden\" name=\"userID\" value='$userID'/>";
        $session_table .= "<input type=\"hidden\" name=\"startTimestamp\" value='$startTimestamp'/>";
        $session_table .= "<input type=\"hidden\" name=\"endTimestamp\" <?php echo value='$endTimestamp'/>";
        $session_table .= "<button type=\"button\" name=\"mark_session_button\" value=\"mark_session_button\" class=\"btn btn-success\">Mark Session</button>";
        $session_table .= "</center>";
        $session_table .= "</div>";














        $slider_html = "";
        $session_panel_html = "
    <div class=\"row\">
        $slider_html
        <div class=\"col-md-12 border\">
        $session_table
        </div>
        
    </div>
        ";

    }



    return array('sessionhtml'=>utf8_encode($session_panel_html));
}

function printTutorialModal(){
    ?>
    <div class="modal fade" id="tutorial_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelTutorial" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabelTutorial">Tutorial</h4>
                </div>





                <div class="modal-body" id="tutorial_modal_panel">

                    <p>Once you have downloaded and installed the browser plugin for this study, it can be used to automatically record your daily browsing and searching activities in your Chrome browser.  By default, it is turned off, but you may click the plugin to activate it and start recording.  You may also click “Log Out” to stop recording. At any time, you may also click the Chrome extension to annotate the day’s activities.  You are asked to annotate your activity for every day of the study.</p>

                    <p>The annotation is divided into 4 main phases.  Each phase is outlined below:</p>


                    <p><u><strong>Mark As Private</strong></u></p>
                    <ul>
                        <li>Select any pages that you wish to permanently delete from the log.  To do so, check their respective boxes in “Send Private Items to Trash” and click “Send to Trash”.</li>
                        <li>To confirm the deletion of these pages, select them in the “Trash Bin” and click “Permanently Delete”.  To undo deletion, select them in the “Trash Bin” and click "Undo Delete".
                        </li>
                    </ul>


                    <p><u><strong>Mark Sessions</strong></u></p>
                    <ul>
                        <li>Here, you are asked to mark the beginning and end of a search session.</li>
                        <li>A search session is defined as a contiguous sequences of related searches - i.e., contiguous searches related to the same task.</li>
                        <li>To mark the beginning of a search session, click the “Begin” button for the page/query that indicates the beginning of the session.</li>
                        <li>To mark the end of a search session, click the “End” button for the page/query that indicates the end of the session.</li>
                        <li>To confirm this selection, click “Mark Session” at the bottom of the page.</li>
                        <li>To undo your selection(s), click the “Begin” or “End” button again.</li>
                    </ul>







                    <p><u><strong>Mark Tasks</strong></u></p>
                    <ul>
                        <li>Next, you must assign sessions to tasks.</li>
                        <li>Some of the listed tasks are ones we asked you about in the pre-study interview.  You may also create new ones in the right-hand panel “2) Click to Assign a Task”.</li>
                        <li>Multiple sessions may belong to the same task.  This is fine.</li>
                        <li>To assign a session to a task, click the checkbox next to it.  You may then assign the task in one of two ways:</li>
                        <li>Click an existing task from the provided options</li>
                        <li>Create a new task in the bottom of the panel “2) Click to Assign a Task”.  After naming a new task, click “+ Add Task”</li>
                    </ul>







                    <p><u><strong>Mark Query Segments And Intentions</strong></u></p>
                    <ul>
                        <li>Next you must assign intentions to each query segment.</li>
                        <li>You may first need to mark query segments within sessions.  Recall that each session is composed of one or more query segments pertaining to the same task.</li>
                        <li>A query segment is begun by a query and continues with all of the browsing and clicking that follows from that query.  It ends before the start of the next query.</li>
                        <li>Some of the annotation may be automatically done.  Other query segments may need to be assigned manually.</li>
                        <li>Assignment of the beginning and end of query segments works similarly to the “Begin” and “End” annotation for marking sessions.</li>
                        <li>After marking a query segment, you will be prompted to mark the intentions for that query segment.</li>

                        <li>You must choose one or more search intention; the elicitation question is:
                            <ul>
                                <li>What were you trying to accomplish (what was your intention) during this part of the search? Please choose one or more of the "search intentions" on the right; if none fits your goal at this point in the search, please choose "Other", and give a brief explanation.</li>
                            </ul>
                        </li>

                        <li>For each identified search intention, you are asked:
                            <ul>
                                <li>"Were you successful?" You must answer either "Yes" or "No".</li>
                                <li>If "No", you must respond, in a text entry box, to the question: "Why not?"</li>

                            </ul>
                        </li>
                    </ul>

                    <p>For more information about this study, please send e-mail to Matthew Mitsui at mmitsui@scarletmail.rutgers.edu. You can also contact Matthew Mitsui to ask questions or get more information about the project.</p>













                </div>


                <div class="modal-footer" id="tutorial_modal_footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Exit</button>

                </div>
            </div>
        </div>


    </div>
<?php
}
?>
