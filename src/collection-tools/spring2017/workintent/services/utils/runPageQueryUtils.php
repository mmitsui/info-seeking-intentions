<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");

function postInputAsArray($inp){
    $arr = array();
    foreach($inp as $elem){
        $arr[] = $elem;
    }

    return $arr;
}
if(isset($_GET['action'])){
    $action = $_GET['action'];
    $userID = $_POST['userID'];
    $startTimestamp = $_POST['startTimestamp'];
    $endTimestamp = $_POST['endTimestamp'];
    $pages_post = isset($_POST['pages'])?$_POST['pages']:array();
    $queries_post = isset($_POST['queries'])?$_POST['queries']:array();
    $cxn = Connection::getInstance();
    if($action == 'sendToTrash'){
        $pageIDs = postInputAsArray($pages_post);
        $queryIDs = postInputAsArray($queries_post);


        if(count($queryIDs) > 0){
            $queryID_list = implode(",",$queryIDs);
            $query = "UPDATE queries SET `trash`=1 WHERE `userID`=$userID' AND `queryID` IN ($queryID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) > 0){
            $pageID_list = implode(",",$pageIDs);
            $query = "UPDATE pages SET `trash`=1 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
            $cxn->commit($query);
        }
        echo json_encode(getHomePageTables($userID,$startTimestamp,$endTimestamp));
        exit();
//        return json_encode(getHomePageTables($userID,$startTimestamp,$endTimestamp));

    }
    else if($action == 'permanentlyDelete'){
        $pageIDs = postInputAsArray($pages_post);
        $queryIDs = postInputAsArray($queries_post);

        if(count($queryIDs) > 0){
            $queryID_list = implode(",",$queryIDs);
            $query = "UPDATE queries SET `permanently_delete`=1 WHERE `userID`=$userID' AND `queryID` IN ($queryID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) > 0){
            $pageID_list = implode(",",$pageIDs);
            $query = "UPDATE pages SET `permanently_delete`=1 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
            $cxn->commit($query);
        }

        echo json_encode(getHomePageTables($userID,$startTimestamp,$endTimestamp));
        exit();

    }
    else if($action == 'restore'){
        $pageIDs = postInputAsArray($pages_post);
        $queryIDs = postInputAsArray($queries_post);

        if(count($queryIDs) > 0){
            $queryID_list = implode(",",$queryIDs);
            $query = "UPDATE queries SET `trash`=0 WHERE `userID`=$userID' AND `queryID` IN ($queryID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) > 0){
            $pageID_list = implode(",",$pageIDs);
            $query = "UPDATE pages SET `trash`=0 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
            $cxn->commit($query);
        }

        echo json_encode(getHomePageTables($userID,$startTimestamp,$endTimestamp));
        exit();

    }
    else if($action=='markSession'){
        $pageIDs = postInputAsArray($_POST['pages']);
        $queryIDs = postInputAsArray($_POST['queries']);

        if(count($queryIDs) > 0){
            $queryID_list = implode(",",$queryIDs);
            $query = "UPDATE queries SET `sessionID`=1 WHERE `userID`=$userID' AND `queryID` IN ($queryID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) > 0){
            $pageID_list = implode(",",$pageIDs);
            $query = "UPDATE pages SET `sessionID`=1 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
            $cxn->commit($query);
        }
    }
    else if($action=='markTask'){
        $pageIDs = postInputAsArray($_POST['pages']);
        $queryIDs = postInputAsArray($_POST['queries']);

        if(count($queryIDs) > 0){
            $queryID_list = implode(",",$queryIDs);
            $query = "UPDATE queries SET `taskID`=5 WHERE `userID`=$userID' AND `queryID` IN ($queryID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) > 0){
            $pageID_list = implode(",",$pageIDs);
            $query = "UPDATE pages SET `taskID`=5 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
            $cxn->commit($query);
        }
    }

}

?>
