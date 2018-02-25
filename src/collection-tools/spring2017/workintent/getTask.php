<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");

$userID = $_GET['userID'];
$taskID = $_GET['taskID'];

$startTimestamp = 0;
$endTimestamp = strtotime('today midnight');
$day_log = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,-1,-1,$taskID);
$taskIDNameMap = getTaskIDNameMap($userID);


?>

<html>
<head>
    <title>
        Tutorial
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="../lib/bootstrap_notify/bootstrap-notify.min.js"></script>

</head>




<body>
<!--<h3>Task Log for User --><?php //echo $userID;?><!--</h3>-->

<?php

if(count($day_log)<=0){
    $day_table = '<center><h3 class=\'bg-danger\'>The user has not done anything for this task.</h3></center>';
}else{
    $day_table = "
        <table  class=\"table table-bordered table-fixed\">
                                <thead>
                                <tr>
                                    <!--<th >Time</th>-->
                                    <th >Type</th>
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


//        $day_table .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";
        $day_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";





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


<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php
            echo "Task: ".$taskIDNameMap[$taskID];
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