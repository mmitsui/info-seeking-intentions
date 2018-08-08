<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");




//$taskIDNameMap = getTaskIDNameMap($userID);

?>



<html>
<head>
    <title>
        User Data Entry
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./lib/bootstrap_notify/bootstrap-notify.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="./study_styles/jquery-tablesorter/jquery.tablesorter.min.js"></script>



    <style>
        .tooltip-inner {
            /*max-width: 350px;*/
            /* If max-width does not work, try using width instead */
            width: 500px;
            min-width: 500px;
        }





        #main {
            transition: margin-left .0s;
            padding: 16px;
        }

        th.header:not([data-sortable='false']) {
            background-image: url(img/tablesorter/bg.gif);
            cursor: pointer;
            font-weight: bold;
            background-repeat: no-repeat;
            background-position: center right;
            border-right: 1px solid #dad9c7;
            margin-left: -1px;
            padding-right: 20px;
        }


    </style>


    <script>

        //        Note: use DataTable for sorting, not the sortable library.
        $(document).ready( function () {
            $('#myTable').DataTable({
                paging:false,
                searching:false,
                info:false,
                order: [[ 0, "desc" ]]
            });

            $('[data-toggle="tooltip"]').tooltip({
                trigger : 'hover'
            })
        } );
    </script>


</head>




<body>

<?php

$cxn = Connection::getInstance();

$allTasksData = array();
$taskIDToLabel = array();
$taskIDToName = array();
$taskIDToUserID = array();
$user_clause = "userID < 500";
$query = "SELECT * FROM task_labels_user WHERE $user_clause ORDER BY userID DESC";
$userID = -1;
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $taskIDToLabel[$line['id']] = $line['taskID'];
    $taskIDToName[$line['id']] = $line['taskName'];
    $taskIDToUserID[$line['id']] = $line['userID'];
    if($userID != $line['userID']){
        $userID = $line['userID'];
        $allTasksData[$userID] = array();
    }
    $allTasksData[$line['userID']][$line['taskID']] = array();
    $allTasksData[$line['userID']][$line['taskID']]['ct_pages'] = 0;
    $allTasksData[$line['userID']][$line['taskID']]['ct_queries'] = 0;
    $allTasksData[$line['userID']][$line['taskID']]['ct_searchsegments'] = 0;
    $allTasksData[$line['userID']][$line['taskID']]['ct_sessions'] = 0;
    $allTasksData[$line['userID']][$line['taskID']]['task_name'] = $line['taskName'];
    $allTasksData[$line['userID']][$line['taskID']]['description'] = "-";
    $allTasksData[$line['userID']][$line['taskID']]['task_accomplishment'] = "-";
}


$query = "SELECT userID,taskID,COUNT(*) as ct FROM pages WHERE $user_clause AND taskID IS NOT NULL GROUP BY userID,taskID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $allTasksData[$line['userID']][$line['taskID']]['ct_pages'] = $line['ct'];
}


$query = "SELECT userID,taskID,COUNT(*) as ct FROM queries WHERE $user_clause AND taskID IS NOT NULL GROUP BY userID,taskID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $allTasksData[$line['userID']][$line['taskID']]['ct_queries'] = $line['ct'];
}


$query = "SELECT userID,taskID,COUNT(DISTINCT(querySegmentID)) as ct FROM pages WHERE $user_clause  AND taskID IS NOT NULL AND querySegmentID IS NOT NULL GROUP BY userID,taskID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $allTasksData[$line['userID']][$line['taskID']]['ct_searchsegments'] = $line['ct'];
}



$query = "SELECT userID,taskID,COUNT(DISTINCT(sessionID)) as ct FROM pages WHERE $user_clause AND taskID IS NOT NULL AND sessionID IS NOT NULL GROUP BY userID,taskID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $allTasksData[$line['userID']][$line['taskID']]['ct_sessions'] = $line['ct'];
}



$query = "SELECT * FROM questionnaire_entry_tasks WHERE $user_clause";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $taskID = $taskIDToLabel[$line['task_idcolumn']];
    $name = "";
    if(!is_null($line['name'])){
        $name = $line['name'];
    }
    $allTasksData[$line['userID']][$taskID]['task_name'] = $name;
    $allTasksData[$line['userID']][$taskID]['frequency'] = "";
    $allTasksData[$line['userID']][$taskID]['familiarity'] = "";
    $allTasksData[$line['userID']][$taskID]['completiontime'] = "";
    $allTasksData[$line['userID']][$taskID]['individual_complete'] = "";
    $allTasksData[$line['userID']][$taskID]['num_collaborators'] = "";


    if(!is_null($line['frequency'])){
        $allTasksData[$line['userID']][$taskID]['frequency'] = $line['frequency'];
    }

    if(!is_null($line['familiarity'])){
        $allTasksData[$line['userID']][$taskID]['familiarity'] = $line['familiarity'];
    }

    if(!is_null($line['completiontime'])){
        $allTasksData[$line['userID']][$taskID]['completiontime'] = $line['completiontime'];
    }

    if(!is_null($line['individual_complete'])){
        $allTasksData[$line['userID']][$taskID]['individual_complete'] = $line['individual_complete'];
    }

    if(!is_null($line['num_collaborators'])){
        $allTasksData[$line['userID']][$taskID]['num_collaborators'] = $line['num_collaborators'];
    }

    if(!is_null($line['description'])){
        $allTasksData[$line['userID']][$taskID]['description'] = $line['description'];
    }
}



$query = "SELECT * FROM questionnaire_exit_tasks WHERE $user_clause";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $taskID = $line['taskID'];
    $allTasksData[$line['userID']][$taskID]['task_stage'] = "";
    $allTasksData[$line['userID']][$taskID]['goal'] = "";
    $allTasksData[$line['userID']][$taskID]['importance'] = "";
    $allTasksData[$line['userID']][$taskID]['urgency'] = "";
    $allTasksData[$line['userID']][$taskID]['difficulty'] = "";
    $allTasksData[$line['userID']][$taskID]['complexity'] = "";
    $allTasksData[$line['userID']][$taskID]['knowledge_topic'] = "";
    $allTasksData[$line['userID']][$taskID]['knowledge_procedures'] = "";


    if(!is_null($line['task_stage'])){
        $allTasksData[$line['userID']][$taskID]['task_stage'] = $line['task_stage'];
    }

    if(!is_null($line['goal'])){
        $allTasksData[$line['userID']][$taskID]['goal'] = $line['goal'];
    }

    if(!is_null($line['importance'])){
        $allTasksData[$line['userID']][$taskID]['importance'] = $line['importance'];
    }

    if(!is_null($line['urgency'])){
        $allTasksData[$line['userID']][$taskID]['urgency'] = $line['urgency'];
    }

    if(!is_null($line['difficulty'])){
        $allTasksData[$line['userID']][$taskID]['difficulty'] = $line['difficulty'];
    }


    if(!is_null($line['complexity'])){
        $allTasksData[$line['userID']][$taskID]['complexity'] = $line['complexity'];
    }


    if(!is_null($line['knowledge_topic'])){
        $allTasksData[$line['userID']][$taskID]['knowledge_topic'] = $line['knowledge_topic'];
    }

    if(!is_null($line['knowledge_procedures'])){
        $allTasksData[$line['userID']][$taskID]['knowledge_procedures'] = $line['knowledge_procedures'];
    }

    if(!is_null($line['task_accomplishment'])){
        $allTasksData[$line['userID']][$taskID]['task_accomplishment'] = $line['task_accomplishment'];
    }
}

//$query = "SELECT * from recruits where userID=$userID";
//$result = $cxn->commit($query);
//$line = mysql_fetch_array($result,MYSQL_ASSOC);
//$researcher = $line['experimenter'];
//$tasks_selected = array();
//$query = "SELECT * FROM task_labels_user WHERE userID=$userID";
//$result = $cxn->commit($query);
//while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
//    $taskData[$line['taskID']]['by_researcher'] = $line['by_researcher'];
//    if($line['exitinterview']==1){
//        array_push($tasks_selected,$line['taskID']);
//    }
//}

?>

<div id="main">
<div class="container">
    <table  id="myTable" class="table table-bordered table-fixed table-striped sortable-theme-bootstrap" data-sortable>
        <thead>
        <tr>
            <th>User ID</th>
            <th>Task ID</th>
            <th>Name</th>
            <th ># Pages</th>
            <th ># Search Segments</th>
            <th># Sessions</th>
            <th data-sortable='false'>Pretask Details</th>
            <th data-sortable='false'>Posttask Accomplishment</th>
            <th>Frequency</th>
            <th>Familiarity</th>
            <th>Completion Time</th>
            <th>Completed by Self?</th>
            <th>Stage</th>
            <th>Goal</th>
            <th>Importance</th>
            <th>Urgency</th>
            <th>Difficulty</th>
            <th>Complexity</th>
            <th>Topic Knowledge</th>
            <th>Procedure Knowledge</th>
        </tr>
        </thead>
        <tbody id='history_table'>
        <?php
                foreach($allTasksData as $userID=>$taskIDs){
                    if($userID >=500 or $userID <112){
                        continue;
                    }

                    foreach($taskIDs as $taskID=>$taskInfo){

                        echo "<tr data-task-id='$taskID'>";


                        echo "<td>".$userID."</td>";
                        echo "<td>".$taskID."</td>";
                        if(!isset($taskInfo['task_name'])){
                            echo "TASK".$taskID;
                            echo "user".$userID;
                            print_r($taskInfo);
                        }



                        foreach(array('task_name','ct_pages','ct_searchsegments','ct_sessions','description','task_accomplishment','frequency','familiarity','completiontime','individual_complete','task_stage','goal','importance','urgency',
                                    'difficulty','complexity','knowledge_topic','knowledge_procedures') as $index){
                            if(isset($taskInfo[$index]) and $taskInfo[$index]!="-"){
                                if($index == 'description' or $index =='task_accomplishment'){
                                    $tooltip = $taskInfo[$index];
                                    echo "<td><i class=\"fa fa-info-circle fa-2x\" data-toggle='tooltip' data-placement='right' data-html=\"true\" data-title=\"$tooltip\" title=\"$tooltip\" aria-hidden=\"true\"  style='color:dodgerblue; cursor:pointer'></i></td>";
                                }else{
                                    echo "<td>".$taskInfo[$index]."</td>";
                                }

                            }else{

                                echo "<td>"."-"."</td>";
                            }
                        }
                        echo "</tr>";
                    }
                }
                ?>

                </tbody>
            </table>


<!--        </div>-->
<!--    </div>-->
</div>

</div>
</body>
</html>