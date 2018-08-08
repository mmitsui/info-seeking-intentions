<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/getSummaryData.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/updateSummaryData.php");




date_default_timezone_set('America/New_York');

//updateSummaryData('study');
$summary = getSummaryData('study');

function start_date_to_int($start_date){
    if($start_date == '' or is_null($start_date)){
        return "";
    }
    $date_parts = explode("-",$start_date);
    $date_pref = $date_parts[0];
    $date_suff = $date_parts[1];
    if(strpos($date_suff,"AM")!==false){
        $date_suff="AM";
    }else if(strpos($date_suff,"PM")!==false){
        $date_suff="PM";
    }

    if(strpos($date_pref,"AM")!==false){
        $date_pref=substr($date_pref,0,-2);
        $date_suff = "AM";
    }else if(strpos($date_pref,"PM")!==false){
        $date_pref=substr($date_pref,0,-2);
        $date_suff = "PM";
    }

    $date_full = $date_pref." ".$date_suff;
    $date_parts = preg_split("/[,]+/", $date_full);
    $year_string = "2018";
    $date_full = $date_parts[0]." $year_string".$date_parts[1];



    return $date_full;
}

?>



<html>
<head>
    <title>
        SINS Participants Selection
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./lib/bootstrap_notify/bootstrap-notify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>


    <style>

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


    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="study_styles/jquery-tablesorter/jquery.tablesorter.min.js"></script>

    <script>
        $(document).ready( function () {
            $('#participantsTable').DataTable({
                paging:false,
                searching:false,
                info:false,
            });
        } );
    </script>


</head>




<body>



<div id="main">
    <?php





    $cxn = Connection::getInstance();

    $userIDs_entrydone = array();
    $userIDs_exitdone = array();
    $userIDs_entrypassed = array();
    $userIDs_exitpassed = array();

    $abandoned_registration_userIDs = array();
    $absent_registration_userIDs = array();
    $pending_userIDs = array();

    $query = 'SELECT * FROM questionnaire_exit_tool WHERE userID<500 AND userID >=112 GROUP BY userID';
    $result = $cxn->commit($query);
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        array_push($userIDs_exitdone,$line['userID']);
    }

    $query = 'SELECT * FROM questionnaire_entry_demographic WHERE userID<500 AND userID >=112 GROUP BY userID';
    $result = $cxn->commit($query);
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        array_push($userIDs_entrydone,$line['userID']);
    }


    $pages_done_userIDs = array();
    $query = 'SELECT * FROM pages WHERE userID<500 AND userID >=112 GROUP BY userID';
    $result = $cxn->commit($query);
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        array_push($pages_done_userIDs,$line['userID']);
    }

    $query = 'SELECT * FROM recruits WHERE userID<500 AND userID >=112 GROUP BY userID';

    $result = $cxn->commit($query);
    while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
        $userID = $line['userID'];
        $d = explode("-",$line['date_firstchoice']);
        $timestampentry = strtotime($d[0]);
        $d = explode("-",$line['date_secondchoice']);
        $timestampexit = strtotime($d[0]);
        $currenttime = time();


        if(in_array($userID,$userIDs_exitdone)){
//            Everything is done, including exit
            continue;
        }else if($currenttime-$timestampexit>0 and !in_array($userID,$userIDs_exitdone) and (in_array($userID,$pages_done_userIDs) or in_array($userID,$userIDs_entrydone))){
//            Abandoned.  Exit interview passed and not done but they still did work
            array_push($abandoned_registration_userIDs,$userID);
        } else if($currenttime-$timestampentry>0 and !in_array($userID,$userIDs_entrydone)){
//            Did not do entry
            array_push($userIDs_entrypassed,$userID);
        }else if($currenttime-$timestampexit>0 and !in_array($userID,$userIDs_exitdone)){
//            Did not do exit
            array_push($userIDs_exitpassed,$userID);
        }else if (!in_array($userID,$userIDs_exitdone) and !in_array($userID,$userIDs_entrydone)){
//            Did neither entry nor exit but pending
            array_push($pending_userIDs,$userID);
        }
    }



    $abandoned_registration_userIDs = array_unique($abandoned_registration_userIDs);
    $absent_registration_userIDs = array_unique(array_merge($userIDs_entrypassed,$userIDs_exitpassed));
    $pending_userIDs = array_unique($pending_userIDs);


    ?>
    <div class="container">
        <center><h1>Study Summary</h1></center>
    </div>

    <div class="container">
        <table  id='participantsTable' class="table table-bordered table-fixed table-striped sortable-theme-bootstrap" data-sortable>
            <thead>
            <tr>
                <th >Participant ID</th>
                <th data-sortable-type='date'>Entry Interview Date</th>
                <th data-sortable-type='date'>Exit Interview Date</th>
                <th>Status</th>
                <th >Assigned Researcher</th>
                <th data-sortable='false'>Select For Annotation</th>
            </tr>
            </thead>
            <tbody>
            <?php

                $query = "SELECT * FROM (SELECT userID FROM users WHERE userID<500 AND userID >=112) u INNER JOIN (SELECT * from recruits) a ON a.userID=u.userID";
                $result = $cxn->commit($query);
                while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
                    $userID = $line['userID'];
                    echo "<tr>";
                    echo "<td>".$line['userID']."</td>";
                    echo "<td>".start_date_to_int($line['date_firstchoice'])."</td>";
                    echo "<td>".start_date_to_int($line['date_secondchoice'])."</td>";
                    if(in_array($userID,$absent_registration_userIDs)){
                        echo "<td class='danger'><span >Absent</span></td>";
                    }
                    else if(in_array($userID,$abandoned_registration_userIDs)){
                        echo "<td class='warning'><span >Abandoned</span></td>";
                    }else if(in_array($userID,$pending_userIDs)){
                        echo "<td><span>Pending</span></td>";
                    }else if(in_array($userID,$userIDs_exitdone)){
                        echo "<td class='success'><span>Done</span></td>";
                    }else{
                        echo "<td class='info'><span>Active</span></td>";
                    }
                    echo "<td>".$line['experimenter']."</td>";
                    echo "<td><a class='btn btn-success' href='http://www.coagmento.org/workintent/userDataEntry.php?userID=$userID'>Select</a></td>";
                    echo "</tr>";
                }
            ?>


            </tbody>

        </table>


    </div>
</div>


</body>
</html>