<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
function getItems($userID,$startTimestamp,$endTimestamp,$type,$trash=0,$sessionID=-1,$querySegmentID=-1,$taskID=-1){
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

    $taskID_querysegment='';
    if($taskID != -1){
        if(is_null($taskID)){
            $taskID_querysegment = "and taskID IS NULL";
        }else{
            $taskID_querysegment = "and taskID=$taskID";
        }
    }

    $noisquerypages = '';
    if($type=='pages'){
        $noisquerypages ="and is_query=0";
    }

    $startTimestampMillis = $startTimestamp * 1000.0;
    $endTimestampMillis = $endTimestamp * 1000.0;


    $query = "SELECT * FROM $table WHERE userID=$userID AND `is_coagmento`=0 AND `localTimestamp` >= $startTimestampMillis AND `localTimestamp` <= $endTimestampMillis AND `trash`='$trash' AND `permanently_delete`=0 $sessionID_querysegment $querySegmentID_querysegment $taskID_querysegment $noisquerypages ORDER BY `localTimestamp` ASC";
    $cxn = Connection::getInstance();
    $results = $cxn->commit($query);
    $rows = array();
    while($row = mysql_fetch_array($results,MYSQL_ASSOC))
    {
        $rows[] = $row;
    }
    return $rows;

}


function getPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0,$sessionID=-1,$querySegmentID=-1,$taskID=-1){
    $pages = getItems($userID,$startTimestamp,$endTimestamp,'pages',$trash,$sessionID,$querySegmentID,$taskID);
    $queries = getItems($userID,$startTimestamp,$endTimestamp,'queries',$trash,$sessionID,$querySegmentID,$taskID);
    return array('pages'=>$pages,'queries'=>$queries);
}

function getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0,$sessionID=-1,$querySegmentID=-1,$taskID=-1){
    $pages_queries = getPagesQueries($userID,$startTimestamp,$endTimestamp,$trash,$sessionID,$querySegmentID,$taskID);
    $pages = $pages_queries['pages'];
    $queries = $pages_queries['queries'];

    $index_pages = 0;
    $index_queries = 0;
    $interleaved_objects = array();
    $lastpage = null;
    $lastquery = null;

    while($index_pages < count($pages) and $index_queries < count($queries)) {
        $lastpage = $pages[$index_pages];
        $lastquery = $queries[$index_queries];
        if ($lastpage['localTimestamp'] < $lastquery['localTimestamp']) {
            $interleaved_objects[] = $lastpage;
            $interleaved_objects[count($interleaved_objects) - 1]['type'] = 'page';
            $interleaved_objects[count($interleaved_objects) - 1]['id'] = $lastpage['pageID'];
            $index_pages += 1;
        }else if(($lastpage['localTimestamp'] == $lastquery['localTimestamp'])and ($lastpage['url'] == $lastquery['url'])){
            $interleaved_objects[] = $lastquery;
            $interleaved_objects[count($interleaved_objects)-1]['type'] = 'query';
            $interleaved_objects[count($interleaved_objects)-1]['id'] = $lastquery['queryID'];
            $index_queries += 1;
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
        $day_table = '<center><h3 class=\'bg-danger\'>You have not done anything today.  Please log some activity.</h3></center>';
    }else{
        $day_table = "
         <div class='well'>
             <input class=\"form-control\" id=\"history_search\" type=\"text\" placeholder=\"Search your history\">
             </div>
        <table  class=\"table table-bordered table-fixed\">
                                <thead>
                                <tr>
                                <th ><span id='history_table_select'>Select All</span> <input style='cursor:pointer;zoom:1.6;' type='checkbox' data-table-row-index='0' data-table='day_table' >
                                </th>
                                    <th >Time</th>
                                    <th >Type</th>
                                    
                                    <th >Title/Query</th>
                                    <th >Domain</th>
                                </tr>
                                </thead>
                                <tbody id='history_table'>";

        $table_row_index = 0;
        foreach($day_log as $page){
            $table_row_index += 1;
            $day_table .= "<tr data-table='day_table' data-table-row-index='$table_row_index'>";


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


            $day_table .= "<td>"."<center><label style='cursor:pointer; display: inline-block;width:100%;height:100%'><input style='cursor:pointer;zoom:1.6;' type=\"checkbox\" name='$name' value='$value' data-table-row-index='$table_row_index' data-table='day_table' ></center>"."</label></td>";
            $day_table .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";
            $day_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";





//        $day_table .= "<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>"; //TODO: FIX
//        $day_table .= "<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
            $title = "";
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

            $day_table .= "<td><span title='$title'>$title_short</span></td>";
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
        $trash_table = "
    <div class='well'><input class=\"form-control\" id=\"trash_search\" type=\"text\" placeholder=\"Search your trash\"></div>
        <table  class=\"table table-bordered table-fixed\">
                                <thead>
                                <tr>
                                                                    <th ><span id='trash_table_select'>Select All</span> <input style='cursor:pointer;zoom:1.6;' type='checkbox' data-table-row-index='0' data-table='trash_table' >

                                    <th >Time</th>
                                    <th >Type</th>
                                    
                                    <th >Title/Query</th>
                                    <th >Domain</th>
                                </tr>
                                </thead>
                                <tbody id='trash_table'>";

        $table_row_index = 0;
        foreach($trash as $page){
            $table_row_index += 1;

            $trash_table .= "<tr data-table='trash_table' data-table-row-index='$table_row_index'>";

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


            $trash_table .= "<td>"."<center><label style='cursor:pointer; display: inline-block;width:100%;height:100%'><input style='cursor:pointer;zoom:1.6;'type=\"checkbox\" name='$name' value='$value' data-table-row-index='$table_row_index' data-table='trash_table' ></center>"."</label></td>";

            $trash_table .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";

            $trash_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";




//        $trash_table .= "<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>"; //TODO: FIX
//        $trash_table .= "<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";

            $title = "";
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
            $trash_table .= "<td><span title='$title'>$title_short</span></td>";

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

    $progress_bar = '';

    $even_odd_styles = array(0=>'info',1=>'success');
    $n_marked = 0;
    $n_total = 0;
    $even_odd_index = array();
    $last_index = 0;
    $max_session_label = 0;

    $sessionIDsCount = array();
    $sessionIDsCount_total = array();
    $sessionIDsCount_start = array();

    $cxn = Connection::getInstance();

    $result = $cxn->commit("SELECT sessionID,COUNT(*) as ct FROM pages WHERE sessionID IS NOT NULL and userID=$userID GROUP BY sessionID");
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        $sessionIDsCount[$line['sessionID']] = $line['ct'];
        $sessionIDsCount_total[$line['sessionID']] = $line['ct'];
        $sessionIDsCount_start[$line['sessionID']] = True;
    }

    $result = $cxn->commit("SELECT sessionID,COUNT(*) as ct FROM queries WHERE sessionID IS NOT NULL and userID=$userID GROUP BY sessionID");
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        if(isset($sessionIDsCount[$line['sessionID']])){
            $sessionIDsCount[$line['sessionID']] += $line['ct'];
            $sessionIDsCount_total[$line['sessionID']] += $line['ct'];
        }else{
            $sessionIDsCount[$line['sessionID']] = $line['ct'];
            $sessionIDsCount_total[$line['sessionID']] = $line['ct'];
        }
    }

    if(count($pages)<=0){
        $session_table = '<center><h3 class=\'bg-danger\'>You have not done anything today.  Please log some activity.</h3></center>';
        $session_panel_html = '<center><h3 class=\'bg-danger\'>You have not done anything today.  Please log some activity.</h3></center>';
    }else{
        $session_table = "<table class=\"table table-fixed\">
                                <thead>
                                <tr>
                                    <th >Annotate</th>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Session</th>
                                    <th >Title/Query</th>
                                    <th >Domain</th>




                                </tr>
                                </thead>
                                <tbody>";


        $table_index = 0;




        $sessionIDToLabel = array();
        $query = "SELECT * FROM session_labels_user WHERE userID=$userID";
        $cxn = Connection::getInstance();
        $result = $cxn->commit($query);
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $sessionIDToLabel[$line['id']] = $line['sessionLabel'];
        }


        $sessionIDAndLabel = array();

        foreach($pages as $page){
            $n_total += 1;
            $table_index += 1;

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

            if(isset($page['sessionID'])){

                $class='';
                if(!isset($even_odd_index[$page['sessionID']])){
                    $last_index += 1;
                    $last_index = $last_index % 2;
                    $even_odd_index[$page['sessionID']] = $last_index;
                }
                $class=$even_odd_styles[$even_odd_index[$page['sessionID']]];

                $n_marked += 1;
                $session_table .= "<tr data-marked='true' data-sid='".$page['sessionID']."' class='$class' data-table-index='$table_index' data-original-class='$class'>";
            }else{
                $session_table .= "<tr data-table-index='$table_index'>";
            }


            $begin_button = "";
            $end_button = "";
            if(!isset($page['sessionID'])){
                $begin_button = "<button name=\"begin_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-success\">Begin</button>";
                $end_button = "<button name=\"end_button\" data-table-index=\"$table_index\" type=\"button\" class=\"btn btn-danger\">End</button>";
            }

            $badge = "";
            if(isset($page['sessionID']) and isset($sessionIDsCount[$page['sessionID']]) and isset($sessionIDsCount_start[$page['sessionID']])){
                if($sessionIDsCount[$page['sessionID']] > 0){
                    $sessionIDsCount[$page['sessionID']] -= 1;

                    if($sessionIDsCount_start[$page['sessionID']]){
                        $badge .= "<h4 class='label label-primary'>Session Begin</h4>";
                        $sessionIDsCount_start[$page['sessionID']] = False;
                    }

                    if($sessionIDsCount[$page['sessionID']] == 0){
                        $badge .= "<h4 class='label label-warning'>Session End</h4>";
                    }

                }

            }

            $session_table .= "<td><input data-table-index=\"$table_index\" type=\"checkbox\" name='$name' value='$value' style='display:none'>$badge $begin_button $end_button <span name='filler' data-table-index='$table_index'></span></td>";

            $session_table .="<td name=\"time_$table_index\">".(isset($page['time'])?$page['time']:"")."</td>";


            $session_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";

//        $session_table .="<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>";



            if(isset($page['sessionID'])){
                $max_session_label = max($sessionIDToLabel[$page['sessionID']],$max_session_label);

                array_push($sessionIDAndLabel,array('ID'=>$page['sessionID'],'label'=>$sessionIDToLabel[$page['sessionID']]));
            }
            $session_table .="<td name='session-id'>".(isset($page['sessionID']) ?$sessionIDToLabel[$page['sessionID']] : "")."</td>";


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
            $session_table .= "<td name=\"title_$table_index\"><span title='$title'>$title_short</span></td>";


            $session_table .= "<td><span title='".$page['host']."'>".(isset($page['host'])?$page['host']:"")."</span></td>";
            $session_table .= "</tr >";


        }
        $session_table .= "</tbody>
                    </table>";
        $session_table .= "<div class=\"container\">";
        $session_table .= "<center>";
        $session_table .= "<input type=\"hidden\" name=\"userID\" value='$userID'/>";
        $session_table .= "<input type=\"hidden\" name=\"startTimestamp\" value='$startTimestamp'/>";
        $session_table .= "<input type=\"hidden\" name=\"endTimestamp\" value='$endTimestamp'/>";
        $session_table .= "<input type=\"hidden\" id='max_session_label' name=\"max_session_label\" value='$max_session_label'/>";
        foreach(array_unique($sessionIDAndLabel,SORT_REGULAR) as $IDLabel){
            $ID = $IDLabel['ID'];
            $label = $IDLabel['label'];
            $session_table .= "<input type=\"hidden\" name=\"sessionidlabel\" data-session-id=\"$ID\" data-session-label='$label'/>\n";
        }

//            <button type="button" name="intent_modal_button" value="intent_modal_button" class="btn btn-lg btn-success" data-toggle="modal" data-target="#intent_modal" style="position: fixed; bottom: 20px; right: 20px; display:none; z-index:100;">Mark Intentions</button>

//        $session_table .= "<div class=\"btn-group\" name='session_button_group' style=\"position: fixed; bottom: 20px; left:20px; display:none; z-index:100;\"> ";
//        $session_table .= "<button type=\"button\" name=\"clear_selection_button\" value=\"clear_selection_button\" class=\"btn btn-lg btn-default\" >Clear Selection</button>";
//
//        $session_table .= "<button type=\"button\" name=\"mark_session_button\" value=\"mark_session_button\" class=\"btn btn-lg btn-warning\" >Identify Session</button>";

        $session_table .= "</div>";

        $session_table .= "</center>";
        $session_table .= "</div>";








        if($n_marked==$n_total){
//            $progress_bar .= "<div class=\"panel panel-success\">";

//            $progress_bar .= "<div class=\"panel panel-success\">";
//            $progress_bar .= "<div class=\"panel-heading\">";
            $progress_bar .= "<h3><div class='label label-success'>Progress: All Sessions Identified!</div></h3>";
//            $progress_bar .= "</div>";
//            $progress_bar .= "</div>";
//            $progress_bar .= "</div>";

        }else{
//            $progress_bar .= "<div class=\"panel panel-warning\">";
//            $progress_bar .= "<div class=\"panel-heading\">";
            $progress_bar .= "<h3><div class='label label-warning'>Progress: ".intval($n_marked/floatval($n_total)*100)."% Sessions Identified</div></h3>";
//            $progress_bar .= "</div>";
//            $progress_bar .= "</div>";

        }







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



    return array('progressbar_html'=>utf8_encode($progress_bar),'sessionhtml'=>utf8_encode($session_panel_html));
}

function printTutorialModal($mode){
    ?>
    <div class="modal fade" id="tutorial_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelTutorial" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabelTutorial">Help</h4>
                </div>

                <div class="modal-body" id="tutorial_modal_panel">




                <?php
    if($mode=='trash'){
     ?>

        <p>Welcome to the Search Intentions User Study!</p>

        <p>You have been asked to record and comment on your search activity for a period of 5 work days.
            Once you have downloaded and installed the browser plugin for this study, it can be used to automatically record your daily browsing and searching activities in your Chrome browser. By default, it is turned off, but you may click the plugin to activate it and start recording. You may also click “Log Out” to stop recording. At any time, you may also click the Chrome extension to annotate the day’s activities. You are asked to annotate your activity for every day of the study. To do this, click the Chrome extension to annotate the day&rsquo;s activities.</p>

            <p>The annotation is divided into 4 main phases. Each phase is outlined below:</p>

        <ul>
            <li>Delete Private Items</li>
            <li>Identify Sessions</li>
            <li>Assign Tasks to Sessions</li>
            <li>Annotate Search Segments and Intentions</li>
        </ul>

        <p>Good luck! To ask questions or for more information about this study, please send e-mail to Matthew Mitsui at mmitsui@scarletmail.rutgers.edu.</p>



        <p><u><strong>Delete Private Items</strong></u></p>

        <ul>
            <li>Select any pages that you wish to permanently delete from the log. To do so, check their respective boxes in &ldquo;Send Private Items to Trash&rdquo; and click &ldquo;Send to Trash&rdquo;.</li>
            <li>To undo deletion, select them in the &ldquo;Trash Bin&rdquo; and click &ldquo;Undo Delete&rdquo;.</li>
            <li>To confirm the deletion of these pages, select them in the &ldquo;Trash Bin&rdquo; and click &ldquo;Permanently Delete&rdquo;.</li>
        </ul>


        <!--        <ul>-->
<!--            <li><u><strong>Send Private Items to Trash</strong></u> - Select any pages that you wish to permanently delete from the log. To do so, check their respective boxes in &ldquo;Send Private Items to Trash&rdquo; and click &ldquo;Send to Trash&rdquo;.</li>-->
<!--            <li><u><strong>Undo Delete</strong></u> - To undo deletion, select them in the &ldquo;Trash Bin&rdquo; and click &ldquo;Undo Delete&rdquo;.</li>-->
<!--            <li><u><strong>Permanently Delete</strong></u> - To confirm the deletion of these pages, select them in the &ldquo;Trash Bin&rdquo; and click &ldquo;Permanently Delete&rdquo;.</li>-->
<!--        </ul>-->



        <center><p><h4><u>Example</u></h4></p></center>

        <p><u><strong>Send Private Items to Trash</strong></u> Perhaps you do some searching.  You issue some queries,
            get some results, and look at some pages. You look at Buzzfeed in the meantime and wish to mark this as private.
            You can select them like so.
        </p>

        <center><img class='img-responsive' src="images/tutorial/trash_1.png" style="width=50%"></center><br/><br/>
        <p>
            And then you can delete them by pressing the "Send to Trash" button
        </p>



        <p><u><strong>Undo Delete</strong></u> Oops! You accidentally deleted too many activities! To undo a delete,
            select the activites you wish to restore from the Trash Bin and click "Undo Delete".
        </p>

        <center><img class='img-responsive' src="images/tutorial/trash_2.png" style="width=50%"></center><br/><br/>


        <p><u><strong>Permanently Delete</strong></u> To permanently delete the rest, click "Permanently Delete".  Recall that this action is final and cannot be undone!
        </p>

        <center><img class='img-responsive' src="images/tutorial/trash_3.png" style="width=50%"></center><br/><br/>


        <p><u><strong>Next Task - Identify Sessions</strong></u>
            A button for going to the 'Annotate Session' task is in the lower-right corner.  Please click it to proceed to the next part of the study
        </p>
        <center><img class='img-responsive' src="images/tutorial/trash_4.png" style="width=50%"></center><br/><br/>



        <?php
    }else if($mode=='session'){
        ?>

        <p><u><strong>Identify Sessions</strong></u></p>
        <ul>
            <li>Here, you are asked to identify the beginning and end of a search session.</li>
            <li><u><strong>What is a search session?</strong></u> A search session is defined as a sequence of related activities - i.e., activities related to finding information for a specific task.
            </li>
            <li>Most search sessions begin with a query to some search system and end when you've completed the activities related to that task.</li>
            <li>Some search sessions don't begin with queries - for instance, when you begin by going to a specific URL. In such cases, the session begins when you arrive at that URL.</li>
<!--            <li><u><strong>What is a search session?</strong></u> A search session is defined as a contiguous sequences of related activities - i.e., contiguous activities related to finding information for a specific task.-->

            <li>To identify the beginning of a search session, click the &ldquo;Begin&rdquo; button for the page/query that indicates the beginning of the session.</li>
            <li>To identify the end of a search session, click the &ldquo;End&rdquo; button for the page/query that indicates the end of the session.</li>
            <li><u><strong>Multi-tasking</strong></u>
                You may sometimes find you have multiple unrelated search sessions interleaved with each other, for instance when searching in multiple tabs. That's okay.
                Here is how you can mark those sessions:
                <ul>
                    <li>Click "Begin" at the beginning of the first session.</li>
                    <li>Click "Begin" for the beginning of the next session.</li>
                    <li>You will see text next to the "Begin" and "End" buttons indicating which pages and queries are designated to which sessions.</li>
                    <li>
                        Click "Begin" any time a new session begins.  You will eventually be asked whether this is the beginning of a previous session (e.g. Session 1 or 2), or whether this is
                        the beginning of a new session.  Please select accordingly.
                    </li>
                    <li>
                        When a session has completed entirely, click "End".  You may be prompted which session is ending (e.g. Session 1 or 2).  Please make a selection.
                    </li>
                </ul>
                You may sometimes find you have an unrelated search session embedded within a search session you have already identified, for instance when searching in two tabs.
                That's okay.  Mark the beginning and end of that second session and then go back to the first.
            </li>
            <li>To confirm your selection, click &ldquo;Identify Session&rdquo; at the bottom-left of the page.</li>
            <li>To partially undo your selection(s), click "Undo Begin" and "Undo End" where appropriate.</li>
            <li>To undo your selection(s) entirely, click "Cancel" at the bottom-left of the page.</li>
        </ul>


        <!--        <p>Here are the actions you can do:</p>-->
<!--        <ul>-->
<!--            <li><u><strong>Begin/Undo Begin</strong></u> - Indicate where a session starts.  Once you click a "Begin" button, it will become a "Undo Begin" button.  If you clicked the wrong activity, you may undo your action and click another one.</li>-->
<!--            <li><u><strong>End/Undo End</strong></u> - Indicate where a session ends.  Once you click a "Begin" button, it will become a "Undo End" button.  If you clicked the wrong activity, you may undo your action and click another one.</li>-->
<!--            <li><u><strong>Identify Session</strong></u> - Once you have marked the beginning and end of a session, a "Annotate Session" button will appear.  You may click that to finalize your annotation.</li>-->
<!--            <li><u><strong>Undo Identifications</strong></u> - Annotations cannot be undone, strictly speaking.  But if you made a mistake, you may overwrite by marking a new session.</li>-->
<!--        </ul>-->

        <center><p><h4><u>Example</u></h4></p></center>

        <p><u><strong>Identifying a Session</strong></u>
            First, you must mark the beginning of a session press "Begin" to do so.
        </p>

        <center><img class='img-responsive' src="images/tutorial/session_1.png" style="width=50%"></center><br/><br/>

        <p>
            You will notice that the buttons will change, and the same button now reads "Undo Begin".
            If you wish to undo your selection, you may select "Undo Begin".
        </p>

        <center><img class='img-responsive' src="images/tutorial/session_2.png" style="width=50%"></center><br/><br/>


        <p>
            You must then select the last action in the section by clicking "End".  After you've finished your selection,
            click "Annotate Session" in the bottom-left corner
        </p>
        <center><img class='img-responsive' src="images/tutorial/session_3.png" style="width=50%"></center><br/><br/>




        <p><u><strong>Identifying a Session Without a Query</strong></u>
            Sessions don't necessarily need to include a query!  Consider the one below, which went directly to a news source
            to search for information.  Please mark sessions for these as well.
        </p>

        <center><img class='img-responsive' src="images/tutorial/session_4.png" style="width=50%"></center><br/><br/>


        <p><u><strong>Identifying a Session When Multitasking</strong></u>
            Sessions may be interleaved with each other, for instance when switching between tabs.  Each session must be associated with only one task!
            To handle these cases, click "Begin" each time the session changes.
        </p>

        <center><img class='img-responsive' src="images/tutorial/session_5.png" style="width=50%"></center><br/><br/>

        <p>You may eventually be asked whether you are identifying a new session or a previous session.  Click one of the options in the popup when this happens.</p>

        <center><img class='img-responsive' src="images/tutorial/session_6.png" style="width=50%"></center><br/><br/>

        <p>Mark the end of a session only when a session has completely finished.  Please identify which session this is as well in the popup</p>

        <center><img class='img-responsive' src="images/tutorial/session_7.png" style="width=50%"></center><br/><br/>


        <?php
    }else if($mode=='task'){
        ?>


        <p><u><strong>Assign Tasks to Sessions</strong></u></p>
        <ul>
            <li>Next, you must assign tasks to sessions.</li>
            <li><u><strong>What is a task?</strong></u> - Tasks are the things you are trying to accomplish in your work role. You identified some of your tasks in the pre-study interview, and you will see them listed.  You may also create new ones.</li>
            <li>Multiple sessions may belong to the same task. This is fine.</li>
            <li>To assign a task to a session, click the checkbox next to it.</li>
            <li>For the marked session, choose a task by clicking the name of the respective task on the right-hand side.</li>
            <li>If you need to add a task, navigate to the panel that says "Click to Assign a Task". At the bottom, you may specify some new task in the text box and click "+ Add Task"</li>
        </ul>

<!--        <p>Here are the actions you can do:</p>-->
<!---->
<!--        <ul>-->
<!--            <li><u><strong>Choose a Session</strong></u> - You will see the sessions you've marked on the left hand side. In order to assign a task to a session, you must first identify the session by clicking the button next to it.</li>-->
<!--            <li><u><strong>Assign Task to Session</strong></u> - For the marked session, choose a task by clicking the name of the respective task on the right-hand side.</li>-->
<!--            <li><u><strong>Add Task</strong></u> - If you need to add a task, navigate to the panel that says "Click to Assign a Task". At the bottom, you may specify some new task in the text box and click "+ Add Task"</li>-->
<!--        </ul>-->

        <center><p><h4><u>Example</u></h4></p></center>


        <p><u><strong>Assigning a Task</strong></u>
            In your initial interview before the study, you told the interviewer about some common tasks
            you work on, listed on the right hand side like so.
        </p>

        <center><img class='img-responsive' src="images/tutorial/task_1.png" style="width=50%"></center><br/><br/>


        <p>To assign a task to a session, click the checkbox associated with the session.
            Then click the task you wish to assign to it.
        </p>

        <center><img class='img-responsive' src="images/tutorial/task_2.png" style="width=50%"></center><br/><br/>

        <p>
            The updates will be reflected in the session you just marked!
        </p>

        <center><img class='img-responsive' src="images/tutorial/task_3.png" style="width=50%"></center><br/><br/>



        <p><u><strong>Creating a New Task</strong></u>
            If you worked on a task different from the ones already listed, you'll need to create a new one.
            You may do this by entering the name of the task in the textbox on the right hand side and clicking "+ Add Task".
        </p>

        <center><img class='img-responsive' src="images/tutorial/task_4.png" style="width=50%"></center><br/><br/>










        <?php
    }else if($mode=='intention'){
        ?>




        <p><u><strong>Annotate Search Segments and Intentions</strong></u></p>
        <ul>
            <li>Next you must assign intentions to each search segment.</li>
            <li>
                <u><strong>What is a search segment?</strong></u> - A search segment a set of related activities
                associated with one goal within a search session.
                A typical search segment begins with a query to a search engine and ends with the next query.
                Such search segments are automatically marked by our system.
            </li>
            <!--        <li>You may find that <strong>some search sessions don't have queries</strong>, for instance when you go directly to a new URL. In such cases, a search segment is defined as ending when you change what you're trying to do within the search session.-->
            <!--            Often, search sessions have only one search segment.</li>-->
            <!--        <li>You may find that some search segments are automatically marked by our system.</li>-->

            <li>You may find that in some sessions without queries, there are nevertheless search segments.  These you will need to mark manually using the begin and end buttons as in marking search sessions.</li>
            <li>Many search sessions have only one segment, but many others will have several.</li>
            <!--        <li>The system automatically marks some search segments, but you may need to mark other search segments within sessions. Recall that each session is composed of one or more search segments pertaining to the same task.-->
            <!--            <ul>-->
            <!---->
            <!--                <li>Some of the annotation may be automatically done. Other search segments may need to be assigned manually.</li>-->
            <!--                <li>Assignment of the beginning and end of search segments works similarly to the &ldquo;Begin&rdquo; and &ldquo;End&rdquo; annotation for marking sessions.</li>-->
            <!--            </ul>-->
            <!--        </li>-->
            <!--        <li>Assignment of the beginning and end of search segments works similarly to the &ldquo;Begin&rdquo; and &ldquo;End&rdquo; annotation for marking sessions.</li>-->


            <li>After marking a search segment, you will be prompted to mark the intentions for that search segment.
                <ul>
                    <li>You <strong>must</strong> choose one or more search intentions, which indicate what you were trying to accomplish during this search segment.</li>
                    <li>The elicitation question is: <strong>What were you trying to accomplish (what was your intention) during this part of the search? Please choose one or more of the "search intentions" below; if none fits your goal at this point in the search, please choose "Other", and give a brief explanation.</strong></li>

                    <li>For each identified search intention, you are asked:
                        <ul>
                            <li>"<strong>Were you successful?</strong>" You <strong>must</strong> answer either "Yes" or "No".</li>
                            <li>If "No", you <strong>must</strong> respond, in a text entry box, to the question: "<strong>Why not?</strong>"</li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>

        <center><p><h4><u>Example</u></h4></p></center>


        <p><u><strong>Annotating Intentions on Search Segments</strong></u>
            The logger automatically extracted as many search segments as possible.  You may see some entries with a "Mark Intentions"
            button.  Click this button to begin marking intentions for that search segment.
        </p>

        <center><img class='img-responsive' src="images/tutorial/intention_1.png" style="width=50%"></center><br/><br/>

        <p>
            A popup will emerge.  Each intention will have a short description. If you want to see
            a longer description, click the blue information icon below it.
        </p>

        <center><img class='img-responsive' src="images/tutorial/intention_1-5.png" style="width=50%"></center><br/><br/>


        <p>
            You may select as many intentions as you'd like, but please only select the ones that apply.  In addition, you must specify which ones were satisfied
            and which were not.  For those that were not satisfied, please list a reason why.
        </p>

        <center><img class='img-responsive' src="images/tutorial/intention_2.png" style="width=50%"></center><br/><br/>


        <p>
            If there was an additional intention that applied, please select "other".
        </p>

        <center><img class='img-responsive' src="images/tutorial/intention_3.png" style="width=50%"></center><br/><br/>


        <p>
            After selecting all intentions that apply, confirm your selection
        </p>

        <center><img class='img-responsive' src="images/tutorial/intention_3-5.png" style="width=50%"></center><br/><br/>




        <p>
            <u><strong>Creating a Search Segment</strong></u>
            Search sessions include a query!  We use the example from before.  You must first mark these search segments like so,
            marking beginning and end like before. Please mark intentions for these segments as well.
        </p>

        <center><img class='img-responsive' src="images/tutorial/intention_4.png" style="width=50%"></center><br/><br/>





        <?php
    }else if($mode=='sessionquestionnaire'){
        ?>




        <p><u><strong>Search Session Questionnaire</strong></u></p>
        <ul>
            <li>Next you must determine the success and usefulness of each search session with respect to accomplishing your task goal.</li>

            <li> For each session:
                <ul>
                    <li>
                        You will be asked 2 questions: "Was the search session successful?" and "How useful was this search session in accomplishing its goal?"
                    </li>
                    <li>
                        Please rate each session on a scale of 1 to 7, where 1="Not at all", 4="Moderately", and 7="Completely".
                    </li>
                    <li>
                        If you answer either of these questions with a score of 4 or below, you will be asked to explain your reasoning.
                        Please answer as accurately as possible.
                    </li>
                </ul>
            </li>

        </ul>


        <center><p><h4><u>Example</u></h4></p></center>


        <p>
            You will be shown a list of the sessions that you conducted during the day, which you annotated previously.
        </p>

        <center><img class='img-responsive' src="images/tutorial/sessionquestionnaire_1.png" style="width=50%"></center><br/><br/>



        <p>
            For each session, please click the "Complete Questionnaire" button.  A popup will appear on the right-hand side with the questionnaire.
        </p>

        <center><img class='img-responsive' src="images/tutorial/sessionquestionnaire_2.png" style="width=50%"></center><br/><br/>



        <p>
            Please complete this as accurately as possible.
        </p>

        <center><img class='img-responsive' src="images/tutorial/sessionquestionnaire_3.png" style="width=50%"></center><br/><br/>



        <p>
            Once you complete the questionnaire, you will be given a confirmation message.  Completed sessions will be highlighted with green instead of orange.
        </p>

        <center><img class='img-responsive' src="images/tutorial/sessionquestionnaire_4.png" style="width=50%"></center><br/><br/>

        <?php
    }
?>




































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
