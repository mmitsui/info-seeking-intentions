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


?>
