<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/querySegmentIntentUtils.php");


function findNextQuerySegmentLabel($userID,$startTimestamp){
    $base = Base::getInstance();
    date_default_timezone_set($base->getUserTimezone());
//    date_default_timezone_set('America/New_York');
    $date = date('Y-m-d', $startTimestamp);
    $query = "SELECT IFNULL(MAX(querySegmentLabel),0) as maxQuerySegmentID FROM querysegment_labels_user WHERE userID='$userID' AND `date`='$date'";
//    $query = "SELECT IFNULL(MAX(querySegmentID),0) as maxQuerySegmentID FROM querysegment_labels_user WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);

    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    $querySegmentID = $line['maxQuerySegmentID']+1;
    return $querySegmentID;
}

function markQuerySegmentLabel($userID,$querySegmentID,$startTimestamp){
    $base = Base::getInstance();
    date_default_timezone_set($base->getUserTimezone());
//    date_default_timezone_set('America/New_York');
    $date = date('Y-m-d', $startTimestamp);
    $query = "INSERT INTO querysegment_labels_user (`userID`,`projectID`,`querySegmentLabel`,`deleted`,`date`) VALUES ('$userID','$userID','$querySegmentID',0,'$date')";
    $cxn = Connection::getInstance();
    $cxn->commit($query);
    return $cxn->getLastID();
}

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
        $pageIDs = array();
        $queryIDs  = array();

        if(isset($_POST['pages'])){
            $pageIDs = postInputAsArray($_POST['pages']);
        }
        if(isset($_POST['queries'])){
            $queryIDs = postInputAsArray($_POST['queries']);
        }

        if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));
        }else{
            $sessionID = makeNextSessionLabel($userID,$startTimestamp);
            markSessionID($userID,$sessionID,$pageIDs,$queryIDs);
            echo json_encode(getSessionTables($userID,$startTimestamp,$endTimestamp));

        }

        exit();
    }
    else if($action=='markSessionSeq'){
        $pageIDs = array();
        $queryIDs  = array();

        $sessionID = $_POST['sessionID'];

        if(isset($_POST['pages'])){
            $pageIDs = postInputAsArray($_POST['pages']);
        }
        if(isset($_POST['queries'])){
            $queryIDs = postInputAsArray($_POST['queries']);
        }

        if($sessionID < 0){
            echo json_encode(array('error'=>true,'message'=>'Please select a session.'));
        }
        else if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));
        }else{
            if($sessionID==0){
                $sessionID = makeNextSessionLabel($userID,$startTimestamp);
            }
            markSessionID($userID,$sessionID,$pageIDs,$queryIDs);
            echo json_encode(getSessionTables($userID,$startTimestamp,$endTimestamp));
        }

        exit();
    }

    else if($action=='markSessionBatch'){
        $pageIDs = array();
        $queryIDs  = array();

        if(isset($_POST['pages'])){
            $pageIDs = postInputAsArray($_POST['pages']);
        }
        if(isset($_POST['queries'])){
            $queryIDs = postInputAsArray($_POST['queries']);
        }

        if(count($pageIDs) == 0 && count($queryIDs) == 0){
            echo json_encode(array('error'=>true,'message'=>'No selection given.'));
        }else{
            $sessionID = makeNextSessionLabel($userID,$startTimestamp);
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
        $taskName = mysql_escape_string($_POST['taskName']);
        if($taskName==''){
            echo json_encode(array('error'=>true,'message'=>'No task name given.'));
        }else{

            if(!is_null(getTaskByName($userID,$taskName))){
                echo json_encode(array('error'=>true,'message'=>'This task already exists.'));
            }else{
                $taskID = addTask($userID,$taskName);
                $message = '';
                $sessionIDs = array();
                if(isset($_POST['sessionIDs'])){
                    $sessionIDs = postInputAsArray($_POST['sessionIDs']);

                }

                if(count($sessionIDs) > 0){
                    $message = 'Task has been added and assigned!';
                    markTaskID($userID,$sessionIDs,$taskID);
                }else{
                    $message = 'Task has been added!';
                }

                echo json_encode(array_merge(getMarkTasksPanels($userID,$startTimestamp,$endTimestamp),getTasksPanel($userID,$startTimestamp,$endTimestamp),array('message'=>$message)));
            }

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
            $querySegmentID = findNextQuerySegmentLabel($userID,$startTimestamp);
            $querySegmentID = markQuerySegmentLabel($userID,$querySegmentID,$startTimestamp);
//            $querySegmentID = findNextQuerySegmentLabel($userID,$localTimestamp/1000);
//            $querySegmentID = markQuerySegmentLabel($userID,$projectID,$querySegmentID,$localTimestamp/1000);
//            $querySegmentID = makeNextQuerySegmentID($userID,$startTimestamp);
            markQuerySegmentID($userID,$querySegmentID,$pageIDs,$queryIDs);
            echo json_encode(getQuerySegmentAndMarkIntentionsPanels($userID,$startTimestamp,$endTimestamp));
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
        $pageIDs = array();
        $queryIDs = array();
        if(isset($_POST['pages'])){
            $pageIDs = postInputAsArray($_POST['pages']);
        }
        if(isset($_POST['queries'])){
            $queryIDs = postInputAsArray($_POST['queries']);
        }

        if(!isset($_POST['intentions']) || count(postInputAsArray($_POST['intentions']))==0){
            echo json_encode(array('error'=>true,'message'=>'No intentions selected.'));
        }
        else if((count($pageIDs) == 0 && count($queryIDs) == 0)){
            echo json_encode(array('error'=>true,'message'=>'No intentions selected.'));
        }else{
//            $querySegmentID = makeNextQuerySegmentID($userID,$startTimestamp);
//            markQuerySegmentID($userID,$querySegmentID,$pageIDs,$queryIDs);
            $checkedIntentions = $_POST['intentions'];

            markIntentions($userID,$querySegmentID,$checkedIntentions,$_POST);
            echo json_encode(getQuerySegmentAndMarkIntentionsPanels($userID,$startTimestamp,$endTimestamp));
        }

        exit();
    }else if($action=='submitSessionQuestionnairePart1'){
        $sessionID = $_POST['sessionID'];
        $success = $_POST['successful'];
        $useful = $_POST['useful'];
        $success_description = $_POST['successful_description'];
        $useful_description = $_POST['useful_description'];

        markSessionQuestionnaire($userID,$sessionID,$success,$useful,$success_description,$useful_description);
        echo json_encode(array_merge(getSessionQuestionnaireTables($userID,$startTimestamp,$endTimestamp),getSearchQuestionnairePanel($userID,$startTimestamp,$endTimestamp)));
        exit();
    }

}

?>
