<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
function getItems($userID,$startTimestamp,$endTimestamp,$type){
    $table = null;
    if($type=='pages'){
        $table ='pages';
    }else if($type=='queries'){
        $table='queries';
    }
    $startTimestampMillis = $startTimestamp * 1000.0;
    $endTimestampMillis = $endTimestamp * 1000.0;

    $query = "SELECT * FROM $table WHERE userID=$userID AND `localTimestamp` >= $startTimestampMillis AND `localTimestamp` <= $endTimestampMillis ORDER BY `localTimestamp` ASC";
//    echo $query;
//    exit();
    $cxn = Connection::getInstance();
    $results = $cxn->commit($query);
    $rows = array();
    while($row = mysql_fetch_array($results,MYSQL_ASSOC))
    {
        $rows[] = $row;
    }
    return $rows;

}
function getPagesQueries($userID,$startTimestamp,$endTimestamp){
    $pages = getItems($userID,$startTimestamp,$endTimestamp,'pages');
    $queries = getItems($userID,$startTimestamp,$endTimestamp,'queries');
    return array('pages'=>$pages,'queries'=>$queries);
}

function getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp){
    $pages_queries = getPagesQueries($userID,$startTimestamp,$endTimestamp);
    $pages = $pages_queries['pages'];
    $queries = $pages_queries['queries'];

    $index_pages = 0;
    $index_queries = 0;
    $interleaved_objects = array();
    while($index_pages < count($pages) and $index_queries < count($index_queries)){
        $lastpage = $pages[$index_pages];
        $lastquery = $queries[$index_queries];
        if($lastpage['localTimestamp'] < $lastquery['localTimestamp']){
            $interleaved_objects[] = $lastpage;
            $interleaved_objects[count($interleaved_objects)-1]['type'] = 'page';
            $index_pages += 1;
        }else{
            $interleaved_objects[] = $lastquery;
            $interleaved_objects[count($interleaved_objects)-1]['type'] = 'query';
            $index_queries += 1;
        }
    }

    if($index_pages < count($pages) or $index_pages < count($pages)){
        if($index_pages < count($pages)){
            for($i=$index_pages;$i<count($pages);$i++){
                $interleaved_objects[] = $pages[$i];
                $interleaved_objects[count($interleaved_objects)-1]['type'] = 'page';
            }
        }else{
            for($i=$index_queries;$i<count($queries);$i++){
                $interleaved_objects[] = $queries[$i];
                $interleaved_objects[count($interleaved_objects)-1]['type'] = 'query';
            }
        }

    }

    return $interleaved_objects;
}


?>
