<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/querySegmentIntentUtils.php");

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
            $query = "UPDATE queries SET `trash`=1 WHERE `userID`='$userID' AND `queryID` IN ($queryID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) > 0){
            $pageID_list = implode(",",$pageIDs);
            $query = "UPDATE pages SET `trash`=1 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));

        }
        else{
            echo json_encode(getHomePageTables($userID,$startTimestamp,$endTimestamp));
        }

        exit();

    }
    else if($action == 'permanentlyDelete'){
        $pageIDs = postInputAsArray($pages_post);
        $queryIDs = postInputAsArray($queries_post);

        if(count($queryIDs) > 0){
            $queryID_list = implode(",",$queryIDs);
            $query = "UPDATE queries SET `permanently_delete`=1 WHERE `userID`='$userID' AND `queryID` IN ($queryID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) > 0){
            $pageID_list = implode(",",$pageIDs);
            $query = "UPDATE pages SET `permanently_delete`=1 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
            $cxn->commit($query);
        }


        if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));
        }else{
            echo json_encode(getHomePageTables($userID,$startTimestamp,$endTimestamp));
        }
        exit();

    }
    else if($action == 'restore'){
        $pageIDs = postInputAsArray($pages_post);
        $queryIDs = postInputAsArray($queries_post);

        if(count($queryIDs) > 0){
            $queryID_list = implode(",",$queryIDs);
            $query = "UPDATE queries SET `trash`=0 WHERE `userID`='$userID' AND `queryID` IN ($queryID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) > 0){
            $pageID_list = implode(",",$pageIDs);
            $query = "UPDATE pages SET `trash`=0 WHERE `userID`='$userID' AND `pageID` IN ($pageID_list)";
            $cxn->commit($query);
        }

        if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));
        }else{
            echo json_encode(getHomePageTables($userID,$startTimestamp,$endTimestamp));
        }


        exit();

    }
    else if($action=='markSession'){
        $pageIDs = postInputAsArray($_POST['pages']);
        $queryIDs = postInputAsArray($_POST['queries']);

        if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));
        }else{
            $sessionID = makeNextSessionID($userID,$startTimestamp);
            markSessionID($userID,$sessionID,$pageIDs,$queryIDs);
            echo json_encode(getSessionTables($userID,$startTimestamp,$endTimestamp));

        }

        exit();
    }
    else if($action=='markTasks'){
        $sessionIDs = postInputAsArray($_POST['sessionIDs']);
        $taskID = $_POST['taskID'];

        if(count($sessionIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No sessions marked for assignment.'));
        }else{
            markTaskID($userID,$sessionIDs,$taskID);
            echo json_encode(getMarkTasksPanels($userID,$startTimestamp,$endTimestamp));
        }

        exit();
    }else if($action=='addTask'){
        $taskName = $_POST['taskName'];
        if($taskName==''){
            echo json_encode(array('error'=>true,'message'=>'No task name given.'));
        }else{
            $taskID = addTask($userID,$taskName);
            $sessionIDs = postInputAsArray($_POST['sessionIDs']);
            $message = '';
            if(count($sessionIDs) > 0){
                $message = 'Task has been added and assigned!';
                markTaskID($userID,$sessionIDs,$taskID);
            }else{
                $message = 'Task has been added!';
            }

            echo json_encode(array_merge(getMarkTasksPanels($userID,$startTimestamp,$endTimestamp),getTasksPanel($userID),array('message'=>$message)));
        }
//        if(count($sessionIDs) >= 1){
//            markTaskID($userID,$sessionIDs,$taskID);
//        }

//        echo json_encode(array_merge(getTasksPanel($userID),getMarkTasksPanels($userID,$startTimestamp,$endTimestamp)));
        exit();
    }else if($action=='markQuerySegment'){
        $pageIDs = postInputAsArray($_POST['pages']);
        $queryIDs = postInputAsArray($_POST['queries']);

        if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));
        }else{
            $querySegmentID = makeNextQuerySegmentID($userID,$startTimestamp);
            markQuerySegmentID($userID,$querySegmentID,$pageIDs,$queryIDs);
            echo json_encode(getQuerySegmentTables($userID,$startTimestamp,$endTimestamp));
        }

        exit();
    }else if($action=='markIntentions'){
        $querySegmentID = $_POST['querySegmentID'];
        $taskID = $_POST['taskID'];
        $checkedIntentions = $_POST['intentions'];
        markIntentions($userID,$querySegmentID,$checkedIntentions);
        echo json_encode(getMarkIntentionsPanels($userID,$startTimestamp,$endTimestamp));
        exit();
    }else if($action=='markQuerySegmentsAndIntentions'){

        $querySegmentID = $_POST['querySegmentID'];
        $pageIDs = postInputAsArray($_POST['pages']);
        $queryIDs = postInputAsArray($_POST['queries']);

        if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));
        }else{
//            $querySegmentID = makeNextQuerySegmentID($userID,$startTimestamp);
//            markQuerySegmentID($userID,$querySegmentID,$pageIDs,$queryIDs);
            $checkedIntentions = $_POST['intentions'];

            markIntentions($userID,$querySegmentID,$checkedIntentions,$_POST);
            echo json_encode(getQuerySegmentAndMarkIntentionsPanels($userID,$startTimestamp,$endTimestamp));
        }

        exit();
    }

}

?>
