<?php
session_start();
require_once('../core/Base.class.php');
require_once('../core/Util.class.php');
require_once('../services/utils/loginUtils.php');
require_once('../services/utils/dayTimeUtils.php');
require_once('../services/utils/pageQueryUtils.php');
require_once('../services/utils/sessionTaskUtils.php');

isSessionOrDie();

$base = Base::getInstance();
$userID = $base->getUserID();

$selectedStartTimeSeconds = null;
if(isset($_GET['startTime'])){
    $selectedStartTimeSeconds = $_GET['startTime'];
}else{
    $selectedStartTimeSeconds  = strtotime('today midnight');
}
$selectedEndTimeSeconds = getStartEndTimestamp($selectedStartTimeSeconds);
$selectedEndTimeSeconds  =$selectedEndTimeSeconds['endTime'];

$startEndTimestampList = getStartEndTimestampsList($userID,strtotime('today midnight'),10);

$taskIDNameMap = getTaskIDNameMap($userID);

?>


    <html>
    <head>
        <title>
            Research Study Registration: Introduction
        </title>

        <!--        <link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">-->
        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <style>
            .table-fixed thead {
                width: 97%;
            }
            .table-fixed tbody {
                height: 230px;
                overflow-y: auto;
                width: 100%;
            }
            .table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {
                display: block;
            }
            .table-fixed tbody td, .table-fixed thead > tr> th {
                float: left;
                border-bottom-width: 0;
            }
        </style>
    </head>





    <body style="background-color:gainsboro">
    <div class="container-fluid">
        <!--   Dates Tab and Review     -->
        <div class="row">
            <div class="col-md-8">

                <div class="panel panel-primary">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <center><h4>Choose a Day</h4></center>
                        </div>
                        <div class="panel-body">
                            <center>
                                <div class="btn-group btn-group-lg" role="group" aria-label="...">

                                    <div class="btn-group btn-group-lg" role="group" aria-label="...">
                                        <?php
                                        $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/getHome.php', $selectedStartTimeSeconds);
                                        foreach($dayButtonStrings as $button){
                                            echo "$button\n";
                                        }

                                        ?>

                                    </div>
                                </div>
                            </center>

                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Tutorial</h4></center>
                    </div>
                    <div class="panel-body">
                        <center><button type="button" class="btn btn-primary">Review Tutorial</button></center>
                    </div>


                </div>
            </div>

        </div>


        <!--   Query Log and Progress     -->
        <div class="row">

            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Actions</h4></center>
                    </div>
                    <div class="panel-body">
                        <center>
                            <?php
                            $actionButtons = actionUrls($selectedStartTimeSeconds);
                            echo $actionButtons['home']."\n";
                            echo $actionButtons['sessions']."\n";
                            echo $actionButtons['tasks']."\n";
                            ?>
                        </center>
                    </div>
                </div>
            </div>

            <div class="col-md-4">

            </div>

        </div>

        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Log</h4></center>
                    </div>
                    <div class="panel-body">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <center><button type="button" class="btn btn-info" data-toggle="collapse" data-target="#session1">Query Segment 1</button></center>
                            </div>
                            <div class="panel-body">
                                <div id="session1" class="collapse">
                                    <center><button type="button" class="btn btn-success">Mark Intentions</button></center>

                                    <table class="table table-striped table-fixed">
                                        <thead>
                                        <tr>
                                            <th class="col-xs-2">#</th><th class="col-xs-8">Name</th><th class="col-xs-2">Points</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="col-xs-2">1</td><td class="col-xs-8">Mike Adams</td><td class="col-xs-2">23</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">2</td><td class="col-xs-8">Holly Galivan</td><td class="col-xs-2">44</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">3</td><td class="col-xs-8">Mary Shea</td><td class="col-xs-2">86</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">4</td><td class="col-xs-8">Jim Adams</td><td>23</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">5</td><td class="col-xs-8">Henry Galivan</td><td class="col-xs-2">44</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">6</td><td class="col-xs-8">Bob Shea</td><td class="col-xs-2">26</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">7</td><td class="col-xs-8">Andy Parks</td><td class="col-xs-2">56</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">8</td><td class="col-xs-8">Bob Skelly</td><td class="col-xs-2">96</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">9</td><td class="col-xs-8">William Defoe</td><td class="col-xs-2">13</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">10</td><td class="col-xs-8">Will Tripp</td><td class="col-xs-2">16</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">11</td><td class="col-xs-8">Bill Champion</td><td class="col-xs-2">44</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">12</td><td class="col-xs-8">Lastly Jane</td><td class="col-xs-2">6</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <center><button type="button" class="btn btn-info" data-toggle="collapse" data-target="#session2">Query Segment 2</button></center>
                            </div>
                            <div class="panel-body">
                                <div id="session2" class="collapse">
                                    <center><button type="button" class="btn btn-success">Mark Intentions</button></center>
                                    <table class="table table-striped table-fixed">
                                        <thead>
                                        <tr>
                                            <th class="col-xs-2">#</th><th class="col-xs-8">Name</th><th class="col-xs-2">Points</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="col-xs-2">1</td><td class="col-xs-8">Mike Adams</td><td class="col-xs-2">23</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">2</td><td class="col-xs-8">Holly Galivan</td><td class="col-xs-2">44</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">3</td><td class="col-xs-8">Mary Shea</td><td class="col-xs-2">86</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">4</td><td class="col-xs-8">Jim Adams</td><td>23</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">5</td><td class="col-xs-8">Henry Galivan</td><td class="col-xs-2">44</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">6</td><td class="col-xs-8">Bob Shea</td><td class="col-xs-2">26</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">7</td><td class="col-xs-8">Andy Parks</td><td class="col-xs-2">56</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">8</td><td class="col-xs-8">Bob Skelly</td><td class="col-xs-2">96</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">9</td><td class="col-xs-8">William Defoe</td><td class="col-xs-2">13</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">10</td><td class="col-xs-8">Will Tripp</td><td class="col-xs-2">16</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">11</td><td class="col-xs-8">Bill Champion</td><td class="col-xs-2">44</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-2">12</td><td class="col-xs-8">Lastly Jane</td><td class="col-xs-2">6</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>




                        </div>


                    </div>
                    <center>




                    </center>
                </div>
            </div>


            <div class="col-md-4">

            </div>


        </div>


    </div>
    </body>
    </html>
<?php
?>