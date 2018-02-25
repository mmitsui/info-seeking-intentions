<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");

$userID = $_GET['userID'];
$sessionID = $_GET['sessionID'];
$taskID = $_GET['taskID'];

$startTimestamp = 0;
$endTimestamp = strtotime('today midnight');
$day_log = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,$sessionID);
$taskIDNameMap = getTaskIDNameMap($userID);



$cxn = Connection::getInstance();
$query = "SELECT * FROM pages WHERE userID=$userID AND sessionID=$sessionID AND querySegmentID IS NOT NULL GROUP BY querySegmentID";
$result = $cxn->commit($query);
$querySegmentIDs = array();
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    array_push($querySegmentIDs,$line['querySegmentID']);
}
$query = "SELECT * FROM queries WHERE userID=$userID AND sessionID=$sessionID AND querySegmentID IS NOT NULL GROUP BY querySegmentID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    array_push($querySegmentIDs,$line['querySegmentID']);
}

$querySegmentIDs = array_unique($querySegmentIDs);


$intentions_data =array();
if(sizeof($querySegmentIDs)==0){
    echo "<h3>No intentions data for this session!</h3>";
    exit();
}

$query = "SELECT * FROM intent_assignments WHERE userID=$userID AND querySegmentID IN (".implode(',',$querySegmentIDs).") ORDER BY querySegmentID ASC";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $intentions_data[$line['querySegmentID']] = $line;
}



$intentions_description = array(
    'id_start'=>'Identify something to get started',
    'id_more'=>'Identify more to search',
//        'learn_feature'=>'Learn system feature',
//        'learn_structure'=>'Learn system structure',
    'learn_domain'=>'Learn domain knowledge',
    'learn_database'=>'Learn database content',
    'find_known'=>'Find a known item',
    'find_specific'=>'Find specific information',
    'find_common'=>'Find items sharing a named characteristic',
    'find_without'=>'Find items without predefined criteria',
//        'locate_specific'=>'Locate a specific item',
//        'locate_common'=>'Locate items with common characteristics',
//        'locate_area'=>'Locate an area/location',
//        'keep_bibliographical'=>'Keep record of bibliographical information',
    'keep_link'=>'Keep record of link',
//        'keep_item'=>'Note item for return',
    'access_item'=>'Access a specific item',
    'access_common'=>'Access items with common characteristics',
    'access_area'=>'Access a web site/home page or similar',
    'evaluate_correctness'=>'Evaluate correctness of an item',
    'evaluate_specificity'=>'Evaluate specificity of an item',
    'evaluate_usefulness'=>'Evaluate usefulness of an item',
    'evaluate_best'=>'Pick best item(s) from all the useful ones',
    'evaluate_duplication'=>'Evaluate duplication of an item',
    'obtain_specific'=>'Obtain specific information',
    'obtain_part'=>'Obtain part of the item',
    'obtain_whole'=>'Obtain a whole item(s)',
    'other'=>'Other'

);

$intentions_name = array(
    'id_start'=>'Identify start',
    'id_more'=>'Identify more',
//        'learn_feature'=>'Learn system feature',
//        'learn_structure'=>'Learn system structure',
    'learn_domain'=>'Learn domain knowledge',
    'learn_database'=>'Learn database content',
    'find_known'=>'Find known',
    'find_specific'=>'Find specific',
    'find_common'=>'Find common',
    'find_without'=>'Find without',
//        'locate_specific'=>'Locate a specific item',
//        'locate_common'=>'Locate items with common characteristics',
//        'locate_area'=>'Locate an area/location',
//        'keep_bibliographical'=>'Keep record of bibliographical information',
    'keep_link'=>'Keep link',
//        'keep_item'=>'Note item for return',
    'access_item'=>'Access item',
    'access_common'=>'Access common',
    'access_area'=>'Access site',
    'evaluate_correctness'=>'Evaluate correctness',
    'evaluate_specificity'=>'Evaluate specificity',
    'evaluate_usefulness'=>'Evaluate usefulness',
    'evaluate_best'=>'Evaluate best',
    'evaluate_duplication'=>'Evaluate duplication',
    'obtain_specific'=>'Obtain specific',
    'obtain_part'=>'Obtain part',
    'obtain_whole'=>'Obtain whole',
    'other'=>'Other'

);

?>

<html>
<head>
    <title>
        Session
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="../lib/bootstrap_notify/bootstrap-notify.min.js"></script>
    <script>

        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

</head>




<body>

<div class="container">
    <a type="button" class="btn btn-info btn-lg" href="http://www.coagmento.org/workintent/taskAndSessionExitInterview.php?userID=<?php echo $userID;?>&taskID=<?php echo $taskID;?>"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back</a>
</div>


<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Intentions for Session
        </div>
        <div class="panel-body">
            <table  class="table table-bordered table-fixed">
                <thead>
                <tr>
                    <th>Search Segment ID</th>
                    <?php
                     foreach($intentions_name as $id=>$name){
                         echo "<th>$name</th>";

                     }
                    ?>
                </tr>
                </thead>
                <tbody id='history_table'>
                <?php
                    foreach($intentions_data as $querySegmentID=>$datum){
                        echo "<tr>";
                        echo "<td>$querySegmentID</td>";
                        foreach($intentions_name as $id=>$name){
                            $td_style = "";
                            $icon = "";
                            $tooltip = '';
                            if($datum[$id]==1){
                                if($datum[$id."_success"]){
                                    $td_style = "class='success'";
                                    if($id=='other'){
                                        $tooltip = "Description: ".$datum[$id."_description"]."\n".$tooltip;
                                        $icon = "<center><i class=\"fa fa-question-circle fa-2x\" data-toggle='tooltip' data-title='$tooltip' aria-hidden=\"true\"></i></center>";
                                    }
                                }else{
                                    $td_style = "class='danger'";
                                    $tooltip = "Failure Reason:".htmlspecialchars($datum[$id."_failure_reason"],ENT_QUOTES);
                                    if($id=='other'){
                                        $tooltip = "Description: ".$datum[$id."_description"]."\n".$tooltip;
                                    }

                                    $icon = "<center><i class=\"fa fa-question-circle fa-2x\" data-toggle='tooltip' data-title='$tooltip' aria-hidden=\"true\"></i></center>";

                                }
                            }

                            echo "<td $td_style>$icon</td>";
                        }
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>


        </div>
    </div>

</div>
<?php

if(count($day_log)<=0){
    $day_table = '<center><h3 class=\'bg-danger\'>You have not done anything today.  Please log some activity.</h3></center>';
}else{
    $day_table = "
        <table  class=\"table table-bordered table-fixed\">
                                <thead>
                                <tr>
                                    <!--<th >Time</th>-->
                                    <th >Type</th>
                                    <th>Search Segment ID</th>
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

        if(!is_null($page['taskID'])){
            $taskName = $taskIDNameMap[$page['taskID']];
        }


//        $day_table .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";
        $day_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";




        $day_table .= "<td >".(isset($page['querySegmentID'])?$page['querySegmentID']:"")."</td>";





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





}

?>


<!--<div class="container">-->
<!--    <h3>Session --><?php //echo $sessionID;?><!-- for User --><?php //echo $userID;?><!--</h3>-->
<!--</div>-->
<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php
            echo "Session for Task: ".$taskName;
            ?>
        </div>

        <div class="panel-body">
            <?php
            echo $day_table;
            ?>
        </div>
    </div>
</div>


</body>
</html>