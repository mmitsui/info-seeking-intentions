<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
function getItems($userID,$startTimestamp,$endTimestamp,$type,$trash=0){
    $table = null;
    if($type=='pages'){
        $table ='pages';
    }else if($type=='queries'){
        $table='queries';
    }
    $startTimestampMillis = $startTimestamp * 1000.0;
    $endTimestampMillis = $endTimestamp * 1000.0;

    $query = "SELECT * FROM $table WHERE userID=$userID AND `is_coagmento`=0 AND `localTimestamp` >= $startTimestampMillis AND `localTimestamp` <= $endTimestampMillis AND `trash`='$trash' AND `permanently_delete`=0 ORDER BY `localTimestamp` ASC";

    $cxn = Connection::getInstance();
    $results = $cxn->commit($query);
    $rows = array();
    while($row = mysql_fetch_array($results,MYSQL_ASSOC))
    {
        $rows[] = $row;
    }
    return $rows;

}
function getPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0){
    $pages = getItems($userID,$startTimestamp,$endTimestamp,'pages',$trash);
    $queries = getItems($userID,$startTimestamp,$endTimestamp,'queries',$trash);
    return array('pages'=>$pages,'queries'=>$queries);
}

function getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,$trash=0){
    $pages_queries = getPagesQueries($userID,$startTimestamp,$endTimestamp,$trash);
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

    if($index_pages < count($pages) or $index_pages < count($pages)){
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
    $day_log = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0);
    $trash = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,1);

    $day_table = "<table class=\"table table-striped table-fixed\">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Delete</th>
                                    <th >Task</th>
                                    <th >Session</th>
                                    <th >Title/Query</th>
                                    <th >URL</th>
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

        $day_table .= "<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>"; //TODO: FIX
        $day_table .= "<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
        $day_table .= "<td>".(isset($page['title'])?substr($page['title'],0,15)."...":"")."</td>";
        $day_table .= "<td><span title='".$page['url']."'>".(isset($page['url'])?substr($page['url'],0,15)."...":"")."</span></td>";

        $day_table .= "</tr>";

    }
    $day_table .= "</tbody>
                    </table>";



    $trash_table = "<table class=\"table table-striped table-fixed\">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Select</th>
                                    <th >Task</th>
                                    <th >Session</th>
                                    <th >Title/Query</th>
                                    <th >URL</th>
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

        $trash_table .= "<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>"; //TODO: FIX
        $trash_table .= "<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
        $trash_table .= "<td>".(isset($page['title'])?substr($page['title'],0,15)."...":"")."</td>";
        $trash_table .= "<td><span title='".$page['url']."'>".(isset($page['url'])?substr($page['url'],0,15)."...":"")."</span></td>";
        $trash_table .= "</tr>";

    }

    $trash_table .= "</tbody>
                       </table>";



    $tables = array('loghtml'=> $day_table,'trashhtml'=>$trash_table);
    return $tables;

}


function getSessionTables($userID,$startTimestamp,$endTimestamp){


    $session_table = "<table class=\"table table-striped table-fixed\">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Mark</th>
                                    <th >Task</th>
                                    <th >Session</th>
                                    <th >Title/Query</th>
                                    <th >URL</th>




                                </tr>
                                </thead>
                                <tbody>";

    $pagesQueries = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0);
    $pages =$pagesQueries;
    foreach($pages as $page){
        $session_table .= "<tr >";
        $session_table .="<td>".(isset($page['time'])?$page['time']:"")."</td>";

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
        $session_table .= "<td><input type=\"checkbox\" name='$name' value='$value'></td>";
        $session_table .="<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>";
        $session_table .="<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
        $session_table .= "<td>".(isset($page['title'])?substr($page['title'],0,15)."...":"")."</td>";
        $session_table .= "<td><span title='".$page['url']."'>".(isset($page['url'])?substr($page['url'],0,15)."...":"")."</span></td>";




        $session_table .= "</tr >";

    }
    $session_table .= "</tbody>
                    </table>";

    return array('sessionhtml'=>$session_table);
}
?>
