<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/getSummaryData.php");


function getIntentionDistribution($studyOrUser,$metadata){

    $intentions = array(
        'id_start',
        'id_more',
        'learn_domain',
        'learn_database',
        'find_known',
        'find_specific',
        'find_common',
        'find_without',
        'keep_link',
        'access_item',
        'access_common',
        'access_area',
        'evaluate_correctness',
        'evaluate_specificity',
        'evaluate_usefulness',
        'evaluate_best',
        'evaluate_duplication',
        'obtain_specific',
        'obtain_part',
        'obtain_whole',
        'other'
    );


    $labels = array(
        'id_start',
        'id_more',
        'learn_domain',
        'learn_database',
        'find_known',
        'find_specific',
        'find_common',
        'find_without',
        'keep_link',
        'access_item',
        'access_common',
        'access_area',
        'evaluate_correctness',
        'evaluate_specificity',
        'evaluate_usefulness',
        'evaluate_best',
        'evaluate_duplication',
        'obtain_specific',
        'obtain_part',
        'obtain_whole',
        'other'
    );

    $data = array(
        'satisfied'=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
        'absent'=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
        'notsatisfied'=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
    );



    if($studyOrUser == 'study'){
        $query = "SELECT * FROM intent_assignments WHERE userID<500 AND userID >=112";
    }else if($studyOrUser=='user'){
        $query = "SELECT * FROM intent_assignments WHERE userID=".$metadata['userID'];
    }
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);


    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        foreach($intentions as $index=>$i){

            if($line[$i] ==0){
                $data['absent'][$index] += 1;
            }else if($line[$i]==1 and $line[$i."_success"]==1){
                $data['satisfied'][$index] += 1;
            }else if($line[$i]==1 and $line[$i."_success"]==0){
                $data['notsatisfied'][$index] += 1;
            }
        }

    }

    $output = array();
    $output['barchartdata_intentions'] = array();
    $output['barchartdata_intentions']['labels'] = $labels;
    $output['barchartdata_intentions']['datasets'] = array();
    $output['barchartdata_intentions']['datasets'][] = array(
        'label'=>'Absent',
        'backgroundColor'=>'#CCCCCC',
        'data'=>$data['absent'],
    );

    $output['barchartdata_intentions']['datasets'][] = array(
        'label'=>'Satisfied',
        'backgroundColor'=>'#0000FF',
        'data'=>$data['satisfied'],
    );


    $output['barchartdata_intentions']['datasets'][] = array(
        'label'=>'Not Satisfied',
        'backgroundColor'=>'#FF0000',
        'data'=>$data['notsatisfied'],
    );

    return $output;

}

function getDataTasks($studyOrUser,$data){

    $intentions = array(
        'id_start',
        'id_more',
        'learn_domain',
        'learn_database',
        'find_known',
        'find_specific',
        'find_common',
        'find_without',
        'keep_link',
        'access_item',
        'access_common',
        'access_area',
        'evaluate_correctness',
        'evaluate_specificity',
        'evaluate_usefulness',
        'evaluate_best',
        'evaluate_duplication',
        'obtain_specific',
        'obtain_part',
        'obtain_whole',
        'other'
    );


    $taskIDToName = array();
    $task_data = array();
    if($studyOrUser=='user'){
        $cxn = Connection::getInstance();
        $query = "SELECT * FROM task_labels_user WHERE userID=".$data['userID'];
        $result = $cxn->commit($query);
        $task_data['count'] = mysql_num_rows($result);

        $task_data['data'] = array();
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $task_data['data'][$line['taskID']] = array();
            $task_data['data'][$line['taskID']]['name'] = $line['taskName'];
            $task_data['data'][$line['taskID']]['sessions_count'] = 0;
            $task_data['data'][$line['taskID']]['searchsegments_count'] = 0;
            $task_data['data'][$line['taskID']]['task_stage'] = '-';
            $task_data['data'][$line['taskID']]['goal'] = '-';
            $task_data['data'][$line['taskID']]['importance'] = '-';
            $task_data['data'][$line['taskID']]['urgency'] = '-';
            $task_data['data'][$line['taskID']]['difficulty'] = '-';
            $task_data['data'][$line['taskID']]['complexity'] = '-';
            $task_data['data'][$line['taskID']]['knowledge_topic'] = '-';
            $task_data['data'][$line['taskID']]['knowledge_procedures'] = '-';
            $task_data['data'][$line['taskID']]['intentions_total'] = 0;
            $task_data['data'][$line['taskID']]['intentions_successful'] = 0;
            $task_data['data'][$line['taskID']]['intentions_failed'] = 0;
        }

        $query = "SELECT COUNT(DISTINCT(sessionID)) as ct,taskID FROM pages WHERE userID=".$data['userID']." AND taskID IS NOT NULL GROUP BY taskID;";
        $result = $cxn->commit($query);
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $task_data['data'][$line['taskID']]['sessions_count'] = $line['ct'];
        }


        $query = "SELECT COUNT(DISTINCT(querySegmentID)) as ct,taskID FROM pages WHERE userID=".$data['userID']." AND taskID IS NOT NULL GROUP BY taskID;";
        $result = $cxn->commit($query);
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $task_data['data'][$line['taskID']]['searchsegments_count'] = $line['ct'];
        }


        $query = "SELECT * FROM questionnaire_exit_tasks WHERE userID=".$data['userID']." AND taskID IS NOT NULL;";
        $result = $cxn->commit($query);
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $task_data['data'][$line['taskID']]['task_stage'] = $line['task_stage'];
            $task_data['data'][$line['taskID']]['goal'] = $line['goal'];
            $task_data['data'][$line['taskID']]['importance'] = $line['importance'];
            $task_data['data'][$line['taskID']]['urgency'] = $line['urgency'];
            $task_data['data'][$line['taskID']]['difficulty'] = $line['difficulty'];
            $task_data['data'][$line['taskID']]['complexity'] = $line['complexity'];
            $task_data['data'][$line['taskID']]['knowledge_topic'] = $line['knowledge_topic'];
            $task_data['data'][$line['taskID']]['knowledge_procedures'] = $line['knowledge_procedures'];
        }

        $querySegmentIDToTaskID = array();
        $query = "SELECT taskID,querySegmentID FROM pages WHERE userID=".$data['userID']." GROUP BY taskID,querySegmentID";
        $result = $cxn->commit($query);
        while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
            $querySegmentIDToTaskID[$line['querySegmentID']] = $line['taskID'];
        }

        $query = "SELECT * FROM intent_assignments WHERE userID=".$data['userID']." AND querySegmentID IS NOT NULL";
        $result = $cxn->commit($query);
        while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
            $querySegmentID = $line['querySegmentID'];
            $taskID = $querySegmentIDToTaskID[$querySegmentID];
            if(is_null($taskID)){
                continue;
            }
            foreach($intentions as $i){
                if($line[$i]==1){
                    $task_data['data'][$taskID]['intentions_total'] += 1;
                    if($line[$i."_success"]==1){
                        $task_data['data'][$taskID]['intentions_successful'] += 1;
                    }else{
                        $task_data['data'][$taskID]['intentions_failed'] += 1;
                    }
                }

            }
        }




        return $task_data;
    }

}

function getDataSessions($studyOrUser,$data){

    $intentions = array(
        'id_start',
        'id_more',
        'learn_domain',
        'learn_database',
        'find_known',
        'find_specific',
        'find_common',
        'find_without',
        'keep_link',
        'access_item',
        'access_common',
        'access_area',
        'evaluate_correctness',
        'evaluate_specificity',
        'evaluate_usefulness',
        'evaluate_best',
        'evaluate_duplication',
        'obtain_specific',
        'obtain_part',
        'obtain_whole',
        'other'
    );


    $taskIDToName = array();

    $session_data = array();
    if($studyOrUser=='user'){
        $cxn = Connection::getInstance();
        $query = "SELECT * FROM task_labels_user WHERE userID=".$data['userID'];
        $result = $cxn->commit($query);
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $taskIDToName[$line['taskID']] = $line['taskName'];
        }


        $query = "SELECT * FROM session_labels_user WHERE userID=".$data['userID'];
        $result = $cxn->commit($query);
        $session_data['count'] = mysql_num_rows($result);
        $session_data['data'] = array();
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $session_data['data'][$line['id']] = array();
            $session_data['data'][$line['id']]['successful'] = '-';
            $session_data['data'][$line['id']]['useful'] = '-';
            $session_data['data'][$line['id']]['intentions_total'] = 0;
            $session_data['data'][$line['id']]['intentions_successful'] = 0;
            $session_data['data'][$line['id']]['intentions_failed'] = 0;

//            $session_data['data'][$line['id']]['taskID'] = 'hi';
        }

        $query = "SELECT sessionID,taskID,COUNT(DISTINCT(querySegmentID)) as ct FROM pages WHERE userID=".$data['userID']." AND sessionID IS NOT NULL GROUP BY sessionID;";
        $result = $cxn->commit($query);
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $session_data['data'][$line['sessionID']]['taskID'] = $line['taskID'];
            $session_data['data'][$line['sessionID']]['task_name'] = $taskIDToName[$line['taskID']];
            $session_data['data'][$line['sessionID']]['count_searchsegments'] = $line['ct'];
//            $session_data['data'][$line['id']]['taskID'] = 'hi';
        }


        $query = "SELECT * FROM questionnaire_exit_sessions WHERE userID=".$data['userID']." AND sessionID IS NOT NULL;";
        $result = $cxn->commit($query);
        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $session_data['data'][$line['sessionID']]['successful'] = $line['successful'];
            $session_data['data'][$line['sessionID']]['useful'] = $line['useful'];
        }


        $querySegmentIDToSessionID = array();
        $query = "SELECT sessionID,querySegmentID FROM pages WHERE userID=".$data['userID']." GROUP BY sessionID,querySegmentID";
        $result = $cxn->commit($query);
        while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
            $querySegmentIDToSessionID[$line['querySegmentID']] = $line['sessionID'];
        }

        $query = "SELECT * FROM intent_assignments WHERE userID=".$data['userID']." AND querySegmentID IS NOT NULL";
        $result = $cxn->commit($query);
        while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
            $querySegmentID = $line['querySegmentID'];
            $sessionID = $querySegmentIDToSessionID[$querySegmentID];
            if(is_null($sessionID)){
                continue;
            }
            foreach($intentions as $i){
                if($line[$i]==1){
                    $session_data['data'][$sessionID]['intentions_total'] += 1;
                    if($line[$i."_success"]==1){
                        $session_data['data'][$sessionID]['intentions_successful'] += 1;
                    }else{
                        $session_data['data'][$sessionID]['intentions_failed'] += 1;
                    }
                }

            }
        }

        return $session_data;
    }

}


function getDataExitSessionsInterview($studyOrUser,$metadata){
    $columns = array(
        'successful',
        'useful',
    );

    $labels = array(
        'successful'=>array('1 - Not at all','2','3','4','5','6','7 - Completely'),
        'useful'=>array('1 - Not at all','2','3','4','5','6','7 - Completely'),
    );

    $data = array(
        'successful'=>array(0,0,0,0,0,0,0),
        'useful'=>array(0,0,0,0,0,0,0),
    );

    $selection_clause = array();
    foreach($columns as $c){
        array_push($selection_clause,"AVG(`$c`) as `mean_$c`");
        array_push($selection_clause,"STD(`$c`) as `std_$c`");
    }
    $selection_clause = implode(',',$selection_clause);

    if($studyOrUser == 'study'){
        $query = "SELECT $selection_clause FROM questionnaire_exit_sessions WHERE userID<500 AND userID >=112";
    }else if($studyOrUser=='user'){
        $query = "SELECT $selection_clause FROM questionnaire_exit_sessions WHERE userID=".$metadata['userID'];
    }
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);

    $output = array();
    foreach($columns as $c){
        $output["mean_$c"] = $line["mean_$c"];
        $output["std_$c"] = $line["std_$c"];
    }

    foreach($columns as $c){
        if($studyOrUser == 'study'){
            $query = "SELECT $c,COUNT(*) as ct FROM questionnaire_exit_sessions WHERE userID<500 AND userID >=112 GROUP BY $c";
        }else if($studyOrUser=='user'){
            $query = "SELECT $c,COUNT(*) as ct FROM questionnaire_exit_sessions WHERE userID=".$metadata['userID']." GROUP BY $c";
        }

        $result = $cxn->commit($query);

        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $data[$c][$line[$c]-1] = intval($line['ct']);
        }

        $output["barchartdata_$c"] = array(
            'labels'=>$labels[$c],
            'datasets'=>array(
                array(
                    'label'=>'Distribution',
                    'backgroundColor'=>'#0000FF',
                    'borderColor'=>'#0000FF',
                    'borderWidth'=>1,
                    'data'=>$data[$c]
                )
            )
        );
    }


    return $output;
}



function getDataExitTasksInterview($studyOrUser,$metadata){
    $columns = array('task_stage',
        'goal',
        'importance',
        'urgency',
        'difficulty',
        'complexity',
        'knowledge_topic',
        'knowledge_procedures',
        );

    $labels = array(
        'task_stage'=>array('1 - Starting','2','3','4','5','6','7 - Finished'),
        'goal'=>array('1 - Abstract','2','3','4','5','6','7 - Specific'),
        'importance'=>array('1 - Unimportant','2','3','4','5','6','7 - Extremely'),
        'urgency'=>array('1 - No urgency','2','3','4','5','6','7 - Extremely'),
        'difficulty'=>array('1 - Not difficult','2','3','4','5','6','7 - Extremely'),
        'complexity'=>array('1 - Not complex','2','3','4','5','6','7 - Extremely'),
        'knowledge_topic'=>array('1 - No knowledge','2','3','4','5','6','7 - Highly knowledgeable'),
        'knowledge_procedures'=>array('1 - No knowledge','2','3','4','5','6','7 - Highly knowledgeable'),
    );

    $data = array(
        'task_stage'=>array(0,0,0,0,0,0,0),
        'goal'=>array(0,0,0,0,0,0,0),
        'importance'=>array(0,0,0,0,0,0,0),
        'urgency'=>array(0,0,0,0,0,0,0),
        'difficulty'=>array(0,0,0,0,0,0,0),
        'complexity'=>array(0,0,0,0,0,0,0),
        'knowledge_topic'=>array(0,0,0,0,0,0,0),
        'knowledge_procedures'=>array(0,0,0,0,0,0,0),
    );

    $selection_clause = array();
    foreach($columns as $c){
        array_push($selection_clause,"AVG(`$c`) as `mean_$c`");
        array_push($selection_clause,"STD(`$c`) as `std_$c`");
    }
    $selection_clause = implode(',',$selection_clause);

    if($studyOrUser == 'study'){
        $query = "SELECT $selection_clause FROM questionnaire_exit_tasks WHERE userID<500 AND userID >=112";
    }else if($studyOrUser=='user'){

        $query = "SELECT $selection_clause FROM questionnaire_exit_tasks WHERE userID=".$metadata['userID'];
    }
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);

    $output = array();
    foreach($columns as $c){
        $output["mean_$c"] = $line["mean_$c"];
        $output["std_$c"] = $line["std_$c"];
    }


    foreach($columns as $c){
        if($studyOrUser == 'study'){
            $query = "SELECT $c,COUNT(*) as ct FROM questionnaire_exit_tasks WHERE userID<500 AND userID >=112 GROUP BY $c";
        }else if($studyOrUser=='user'){
            $query = "SELECT $c,COUNT(*) as ct FROM questionnaire_exit_tasks WHERE userID=".$metadata['userID']." GROUP BY $c";
        }

        $result = $cxn->commit($query);

        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $data[$c][$line[$c]-1] = intval($line['ct']);
        }

        $output["barchartdata_$c"] = array(
            'labels'=>$labels[$c],
            'datasets'=>array(
                array(
                    'label'=>'Distribution',
                    'backgroundColor'=>'#0000FF',
                    'borderColor'=>'#0000FF',
                    'borderWidth'=>1,
                    'data'=>$data[$c]
                )
            )
        );
    }


    return $output;
}



function getDataExitToolInterview($studyOrUser,$metadata){
    $columns = array('reviewannotation_clear',
        'intentions_understandable',
        'intentions_adequate');


    $labels = array(
        'reviewannotation_clear'=>array('1 - Not at all','2','3','4','5','6','7 - Completely'),
        'intentions_understandable'=>array('1 - Not at all','2','3','4','5','6','7 - Completely'),
        'intentions_adequate'=>array('1 - Not at all','2','3','4','5','6','7 - Completely'),
    );

    $data = array(
        'reviewannotation_clear'=>array(0,0,0,0,0,0,0),
        'intentions_understandable'=>array(0,0,0,0,0,0,0),
        'intentions_adequate'=>array(0,0,0,0,0,0,0),
    );


    $selection_clause = array();
    foreach($columns as $c){
        array_push($selection_clause,"AVG(`$c`) as `mean_$c`");
        array_push($selection_clause,"STD(`$c`) as `std_$c`");
    }
    $selection_clause = implode(',',$selection_clause);

    if($studyOrUser == 'study'){
        $query = "SELECT $selection_clause FROM questionnaire_exit_tool WHERE userID<500 AND userID >=112";
    }else if($studyOrUser=='user'){
        $query = "SELECT $selection_clause FROM questionnaire_exit_tool WHERE userID=".$metadata['userID'];
    }
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);

    $output = array();
    foreach($columns as $c){
        $output["mean_$c"] = $line["mean_$c"];
        $output["std_$c"] = $line["std_$c"];
    }


    foreach($columns as $c){
        if($studyOrUser == 'study'){
            $query = "SELECT $c,COUNT(*) as ct FROM questionnaire_exit_tool WHERE userID<500 AND userID >=112 GROUP BY $c";
        }else if($studyOrUser=='user'){
            $query = "SELECT $c,COUNT(*) as ct FROM questionnaire_exit_tool WHERE userID=".$metadata['userID']." GROUP BY $c";
        }

        $result = $cxn->commit($query);

        while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
            $data[$c][$line[$c]-1] = intval($line['ct']);
        }

        $output["barchartdata_$c"] = array(
            'labels'=>$labels[$c],
            'datasets'=>array(
                array(
                    'label'=>'Distribution',
                    'backgroundColor'=>'#0000FF',
                    'borderColor'=>'#0000FF',
                    'borderWidth'=>1,
                    'data'=>$data[$c]
                )
            )
        );
    }


    return $output;
}

function getIntentionCounts($studyOrUser,$data){

    $intentions = array(
        'id_start',
        'id_more',
        'learn_domain',
        'learn_database',
        'find_known',
        'find_specific',
        'find_common',
        'find_without',
        'keep_link',
        'access_item',
        'access_common',
        'access_area',
        'evaluate_correctness',
        'evaluate_specificity',
        'evaluate_usefulness',
        'evaluate_best',
        'evaluate_duplication',
        'obtain_specific',
        'obtain_part',
        'obtain_whole',
        'other'
    );
    $selection_clause = array();
    foreach($intentions as $i){
        array_push($selection_clause,"SUM(`$i`) as `$i`");
    }
    $selection_clause = implode(',',$selection_clause);


    $return_array = array();
    $cxn = Connection::getInstance();

    if($studyOrUser == 'study'){
        $query = "SELECT $selection_clause FROM intent_assignments WHERE userID<500 AND userID >=112";
        $result = $cxn->commit($query);
        $line = mysql_fetch_array($result,MYSQL_ASSOC);
        $ct = 0;
        foreach($intentions as $i){
            $ct += $line[$i];
        }
        $return_array['count'] = $ct;

    }else if($studyOrUser=='user'){
        $query = "SELECT $selection_clause FROM intent_assignments WHERE userID=".$data['userID'];
        $result = $cxn->commit($query);
        $line = mysql_fetch_array($result,MYSQL_ASSOC);
        $ct = 0;
        foreach($intentions as $i){
            $ct += $line[$i];
        }
        $return_array['count'] = $ct;

    }


    if($studyOrUser=='user'){

        $selection_clause = array();
        foreach($intentions as $i){
            array_push($selection_clause,"SUM(`$i"."_success` IS NOT NULL AND `$i"."_success`=1) as `$i`");
        }
        $selection_clause = implode(',',$selection_clause);

        $query = "SELECT $selection_clause FROM intent_assignments WHERE userID=".$data['userID'];
        $result = $cxn->commit($query);
        $line = mysql_fetch_array($result,MYSQL_ASSOC);
        $ct = 0;
        foreach($intentions as $i){
            $ct += $line[$i];
        }
        $return_array['count_successful'] = $ct;
        $return_array['count_failed'] = $return_array['count']-$return_array['count_successful'];



        $selection_clause = array();
        foreach($intentions as $i){
            array_push($selection_clause,"`$i`");
        }
        $selection_clause = implode('+',$selection_clause);
        $selection_clause = "MIN($selection_clause) as mn,MAX($selection_clause) as mx";
        $query = "SELECT $selection_clause FROM intent_assignments WHERE userID=".$data['userID'];
        $result = $cxn->commit($query);
        $line = mysql_fetch_array($result,MYSQL_ASSOC);
        $return_array['count_min'] = $line['mn'];
        $return_array['count_max'] = $line['mx'];
    }

    return $return_array;

}


function getStudyCompletionCounts(){
    $query = 'SELECT * FROM questionnaire_exit_tasks WHERE userID<500 AND userID >=112 GROUP BY userID';
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $completed_users = mysql_num_rows($result);

    $query = 'SELECT * FROM pages WHERE userID<500 AND userID >=112 GROUP BY userID';
    $result = $cxn->commit($query);
    $running_users = $completed_users - mysql_num_rows($result);

    $query = 'SELECT * FROM recruits WHERE userID<500 AND userID >=112  GROUP BY userID';
    $result = $cxn->commit($query);
    $registered_users = mysql_num_rows($result) - $completed_users - $running_users;


    $remaining_registrations = 36-$completed_users-$running_users-$registered_users;

    return array(
        'completed'=>$completed_users,
        'running'=>$running_users,
        'registered'=>$registered_users,
        'open_registrations'=>$remaining_registrations
    );
}

function getTotalIntentionQuestionnaireCompleted($studyOrUser,$data){
    $cxn = Connection::getInstance();
    if($studyOrUser == 'study'){
        $query = 'SELECT COUNT(*) as ct FROM questionnaire_exit_sessions WHERE (`intention_clarifications` IS NOT NULL OR `intention_transitions` IS NOT NULL) AND userID<500 AND userID >=112';
    }else if($studyOrUser=='user'){
        $query = "SELECT COUNT(*) as ct FROM questionnaire_exit_sessions WHERE (`intention_clarifications` IS NOT NULL OR `intention_transitions` IS NOT NULL) AND userID=".$data['userID'];
    }
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    return array('count'=>$line['ct']);
}

function getTotalSessionQuestionnaireCompleted($studyOrUser,$data){
    $cxn = Connection::getInstance();
    if($studyOrUser == 'study'){
        $query = 'SELECT COUNT(*) as ct FROM questionnaire_exit_sessions WHERE `successful` IS NOT NULL AND userID<500 AND userID >=112';
    }else if($studyOrUser=='user'){
        $query = "SELECT COUNT(*) as ct FROM questionnaire_exit_sessions WHERE `successful` IS NOT NULL AND userID=".$data['userID'];
    }
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    return array('count'=>$line['ct']);
}


function getTotalTaskQuestionnaireCompleted($studyOrUser,$data){
    $cxn = Connection::getInstance();
    if($studyOrUser == 'study'){
        $query = 'SELECT COUNT(*) as ct FROM questionnaire_exit_tasks WHERE userID<500 AND userID >=112';
    }else if($studyOrUser=='user'){
        $query = "SELECT COUNT(*) as ct FROM questionnaire_exit_tasks WHERE userID=".$data['userID'];
    }
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    return array('count'=>$line['ct']);
}


function getTotalSearchSegments($studyOrUser,$data){

    $cxn = Connection::getInstance();
    $return_array = array();
    if($studyOrUser == 'study'){

    }else if($studyOrUser=='user'){
        $query = "SELECT * FROM pages WHERE userID=".$data['userID']." AND querySegmentID IS NOT NULL GROUP BY querySegmentID";
        $result = $cxn->commit($query);
        $total = mysql_num_rows($result);
        $query = "SELECT * FROM (SELECT SUM(querySegmentID_automatic) as sm FROM pages WHERE userID=".$data['userID']." AND querySegmentID IS NOT NULL GROUP BY querySegmentID) a where a.sm > 0";
        $result = $cxn->commit($query);
        $total_automatic = mysql_num_rows($result);
        $total_manual = $total - $total_automatic;
        $return_array['count_total'] = $total;
        $return_array['count_automated'] = $total_automatic;
        $return_array['count_manual'] = $total_manual;
    }

    return $return_array;
}


function getTotalSessions($studyOrUser,$data){
    $cxn = Connection::getInstance();
    if($studyOrUser == 'study'){
        $query = 'SELECT COUNT(*) as ct FROM session_labels_user WHERE userID<500 AND userID >=112';
    }else if($studyOrUser=='user'){
        $query = "SELECT COUNT(*) as ct FROM session_labels_user WHERE userID=".$data['userID'];
    }
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    return array('count'=>$line['ct']);
}


function getTotalTasks($studyOrUser,$data){
    $cxn = Connection::getInstance();
    if($studyOrUser == 'study'){
        $query = 'SELECT COUNT(*) as ct FROM task_labels_user WHERE userID<500 AND userID >=112';
    }else if($studyOrUser=='user'){
        $query = "SELECT COUNT(*) as ct FROM task_labels_user WHERE userID=".$data['userID'];
    }
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    return array('count'=>$line['ct']);
}

function updateOrWrite($slice_by,$slice_id,$var_name,$data,$update_time){
    $cxn = Connection::getInstance();
    $slice_id_clause = "`slice_id`='$slice_id'";
    $slice_id_insert_clause = "$slice_id";
    if(is_null($slice_id)){
        $slice_id_clause = "`slice_id` IS NULL";
        $slice_id_insert_clause = "NULL";
    }
    $query = "SELECT * FROM study_progress WHERE `slice_by`='$slice_by' AND $slice_id_clause AND `var_name`='$var_name'";
    $result = $cxn->commit($query);



    $data = mysql_escape_string(json_encode($data));
    if(mysql_num_rows($result)==0){
        $query = "INSERT INTO study_progress (`slice_by`,`slice_id`,`var_name`,`data`,`lastupdate_timestamp`) VALUES ('$slice_by',$slice_id_insert_clause,'$var_name','$data','$update_time')";
    }else{
        $query = "UPDATE study_progress SET `lastupdate_timestamp`='$update_time',`data`='$data' WHERE `slice_by`='$slice_by' AND $slice_id_clause AND `var_name`='$var_name'";
    }
    $result = $cxn->commit($query);
}

function updateSummaryData($summaryType,$data){
    $currentTimestamp = time();
    if($summaryType=='user'){
        $userID = $data['userID'];

        $count_sessions = getTotalSessions('user',array('userID'=>$userID));
        $count_tasks = getTotalTasks('user',array('userID'=>$userID));
        $count_intentions = getIntentionCounts('user',array('userID'=>$userID));
        $count_searchsegments = getTotalSearchSegments('user',array('userID'=>$userID));
        updateOrWrite($summaryType,$userID,
            'count_tasks',
            $count_tasks,$currentTimestamp);
        updateOrWrite($summaryType,$userID,
            'count_sessions',
            $count_sessions,$currentTimestamp);
        updateOrWrite($summaryType,$userID,
            'count_intentions',
            $count_intentions,$currentTimestamp);

        updateOrWrite($summaryType,$userID,
            'count_sessionquestionnaires',
            getTotalSessionQuestionnaireCompleted('user',array('userID'=>$userID)),$currentTimestamp);

        updateOrWrite($summaryType,$userID,
            'count_taskquestionnaires',
            getTotalTaskQuestionnaireCompleted('user',array('userID'=>$userID)),$currentTimestamp);

        updateOrWrite($summaryType,$userID,
            'count_searchsegments',
            $count_searchsegments,$currentTimestamp);




        updateOrWrite($summaryType,$userID,
            'rate_pertask_sessions',
            $count_sessions['count']/$count_tasks['count'],$currentTimestamp);
        updateOrWrite($summaryType,$userID,
            'rate_pertask_searchsegments',
            $count_searchsegments['count_total']/$count_tasks['count'],$currentTimestamp);
        updateOrWrite($summaryType,$userID,
            'rate_pertask_intentions',
            $count_intentions['count']/$count_tasks['count'],$currentTimestamp);


        updateOrWrite($summaryType,$userID,
            'rate_persession_searchsegments',
            $count_searchsegments['count_total']/$count_sessions['count'],$currentTimestamp);


        updateOrWrite($summaryType,$userID,
            'rate_persession_intentions',
            $count_intentions['count']/$count_sessions['count'],$currentTimestamp);

//        updateOrWrite($summaryType,null,
//            'rate_peruser_sessionquestionnaires',
//            $count_sessionquestionnaires['count']/$studycompletioncounts['completed'],$currentTimestamp);
//        updateOrWrite($summaryType,null,
//            'rate_peruser_sessionquestionnaires',
//            $count_sessionquestionnaires['count']/$studycompletioncounts['completed'],$currentTimestamp);


        updateOrWrite($summaryType,$userID,
            'task_data',
            getDataTasks('user',array('userID'=>$userID)),$currentTimestamp);

        updateOrWrite($summaryType,$userID,
            'session_data',
            getDataSessions('user',array('userID'=>$userID)),$currentTimestamp);


        updateOrWrite($summaryType,$userID,
            'interviewdata_exittool',
            getDataExitToolInterview('user',array('userID'=>$userID)),$currentTimestamp);


        updateOrWrite($summaryType,$userID,
            'interviewdata_exittasks',
            getDataExitTasksInterview('user',array('userID'=>$userID)),$currentTimestamp);

        updateOrWrite($summaryType,$userID,
            'interviewdata_exitsessions',
            getDataExitSessionsInterview('user',array('userID'=>$userID)),$currentTimestamp);

        updateOrWrite($summaryType,$userID,
            'intention_distribution',
            getIntentionDistribution('user',array('userID'=>$userID)),$currentTimestamp);



    }else if($summaryType=='study'){
        updateOrWrite($summaryType,null,
            'count_tasks',
            getTotalTasks('study'),$currentTimestamp);
        updateOrWrite($summaryType,null,
            'count_sessions',
            getTotalSessions('study'),$currentTimestamp);

        updateOrWrite($summaryType,null,
            'count_intentions',
            getIntentionCounts('study'),$currentTimestamp);







        $count_sessionquestionnaires = getTotalSessionQuestionnaireCompleted('study');
        $count_taskquestionnaires = getTotalTaskQuestionnaireCompleted('study');
        $count_intentionquestionnaires = getTotalIntentionQuestionnaireCompleted('study');
        $studycompletioncounts = getStudyCompletionCounts();
        updateOrWrite($summaryType,null,
            'count_sessionquestionnaires',
            $count_sessionquestionnaires,$currentTimestamp);
        updateOrWrite($summaryType,null,
            'count_taskquestionnaires',
            $count_taskquestionnaires,$currentTimestamp);
        updateOrWrite($summaryType,null,
            'count_intentionquestionnaires',
            $count_intentionquestionnaires,$currentTimestamp);
        updateOrWrite($summaryType,null,
            'study_completion',
            $studycompletioncounts,$currentTimestamp);




        updateOrWrite($summaryType,null,
            'rate_peruser_sessionquestionnaires',
            $count_sessionquestionnaires['count']/$studycompletioncounts['completed'],$currentTimestamp);

        updateOrWrite($summaryType,null,
            'rate_peruser_taskquestionnaires',
            $count_taskquestionnaires['count']/$studycompletioncounts['completed'],$currentTimestamp);

        updateOrWrite($summaryType,null,
            'rate_peruser_intentionquestionnaires',
            $count_intentionquestionnaires['count']/$studycompletioncounts['completed'],$currentTimestamp);

        updateOrWrite($summaryType,null,
            'interviewdata_exittool',
            getDataExitToolInterview('study'),$currentTimestamp);

        updateOrWrite($summaryType,null,
            'interviewdata_exittasks',
            getDataExitTasksInterview('study'),$currentTimestamp);

        updateOrWrite($summaryType,null,
            'interviewdata_exitsessions',
            getDataExitSessionsInterview('study'),$currentTimestamp);



        updateOrWrite($summaryType,null,
            'intention_distribution',
            getIntentionDistribution('study'),$currentTimestamp);

    }
}


if(isset($_POST['ajax_call'])&&$_POST['ajax_call']=='update'){
    updateSummaryData($_POST['summaryType'],$_POST);
    echo json_encode(getSummaryData($_POST['summaryType'],$_POST));
}



?>