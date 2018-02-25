<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");






function getSummaryData($summaryType,$data){
    $cxn = Connection::getInstance();
    if($summaryType=='study'){
        $query = "SELECT * FROM study_progress WHERE `slice_by`='study'";
        $results = $cxn->commit($query);
        $return_array = array();
        $lastUpdateTime = 0;
        while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
            $return_array[$line['var_name']]=json_decode($line['data'], true);
            $lastUpdateTime = max($lastUpdateTime,$line['lastupdate_timestamp']);
        }
        $return_array['lastupdate_timestamp'] = $lastUpdateTime;
        return $return_array;
    }else if($summaryType=='user'){
        $userID = $data['userID'];
        $query = "SELECT * FROM study_progress WHERE `slice_by`='user' AND `slice_id`='$userID'";
        $results = $cxn->commit($query);
        $return_array = array();
        $lastUpdateTime = 0;
        while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
            $return_array[$line['var_name']]=json_decode($line['data'], true);
            $lastUpdateTime = max($lastUpdateTime,$line['lastupdate_timestamp']);
        }
        $return_array['lastupdate_timestamp'] = $lastUpdateTime;
        return $return_array;
    }
}

if(isset($_POST['ajax_call'])&&$_POST['ajax_call']=='get'){
    echo json_encode(getSummaryData($_POST['summaryType'],$_POST));
}




?>