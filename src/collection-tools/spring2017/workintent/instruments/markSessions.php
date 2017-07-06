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

$startEndTimestampList = getStartEndTimestampsList($userID,strtotime('today midnight'),20);

$taskIDNameMap = getTaskIDNameMap($userID);


?>


    <html>
    <head>
        <title>
            Research Study Registration: Introduction
        </title>

        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <style>
            .tab-pane{
                height:300px;
                overflow-y:scroll;
                width:100%;
            }
            /*table {*/
            /*width: 100%;*/
            /*}*/

            /*thead, tbody, tr, td, th { display,: block; }*/

            /*tr:after {*/
            /*content: ' ';*/
            /*display: block;*/
            /*visibility: hidden;*/
            /*clear: both;*/
            /*}*/

            /*thead th {*/
            /*height: 30px;*/

            /*!*text-align: left;*!*/
            /*}*/

            /*tbody {*/
            /*height: 120px;*/
            /*overflow-y: auto;*/
            /*}*/

            /*thead {*/
            /*!* fallback *!*/
            /*}*/


            /*tbody td, thead th {*/
            /*width: 19.2%;*/
            /*float: left;*/
            /*}*/


            /*.table-fixed thead {*/
            /*width: 97%;*/
            /*}*/
            /*.table-fixed tbody {*/
            /*height: 230px;*/
            /*overflow-y: auto;*/
            /*width: 100%;*/
            /*}*/
            /*.table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {*/
            /*display: block;*/
            /*}*/
            /*.table-fixed tbody td, .table-fixed thead > tr> th {*/
            /*float: left;*/
            /*border-bottom-width: 0;*/
            /*}*/
        </style>

        <script>
            var session_form_id= '#session_form';

            $(document).ready(function(){
                    $(session_form_id+" button").click(function(ev){
                        ev.preventDefault()// cancel form submission
                        var formData = $(session_form_id).serialize();
                        if($(this).attr("value")=="restore_button"){
                            $.ajax({
                                type: 'POST',
                                url: $(session_form_id).attr('action'),
                                data: formData
                            }).done(function(response) { alert("Sessions have been marked.")});
                        }
                    });

                }
            );


        </script>

<!--        <style>-->
<!--            .table-fixed thead {-->
<!--                width: 97%;-->
<!--            }-->
<!--            .table-fixed tbody {-->
<!--                height: 230px;-->
<!--                overflow-y: auto;-->
<!--                width: 100%;-->
<!--            }-->
<!--            .table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {-->
<!--                display: block;-->
<!--            }-->
<!--            .table-fixed tbody td, .table-fixed thead > tr> th {-->
<!--                float: left;-->
<!--                border-bottom-width: 0;-->
<!--            }-->
<!--        </style>-->
    </head>





    <body style="background-color:lightgrey">
    <div class="container">
        <!--   Dates Tab and Review     -->
        <div class="row">
            <div class="col-md-8">

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

            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center>
                            <?php
                            $actionUrls = actionUrls($selectedStartTimeSeconds);
                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['home']."'>&laquo; Back (Home)</a>";
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['tasks']."'>Next (Tasks) &raquo;</a>";
                            ?>
                        </center>
                    </div>

                </div>
            </div>



        </div>

        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Log</h4></center>

                    </div>
                    <form id="session_form" action="../services/utils/runPageQueryUtils.php?action=markSession">
                        <div class="panel-body tab-pane">
                            <table class="table table-striped table-fixed">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>
                                    <th >Session</th>
                                    <th >Task</th>
                                    <th >Session</th>
                                    <th >Title/Query</th>
                                    <th >URL</th>




                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $pagesQueries = getInterleavedPagesQueries($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds,0);
                                //                            $pagesQueries = getPagesQueries($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
                                //                            $pages =$pagesQueries['pages'];
                                $pages =$pagesQueries;
                                foreach($pages as $page){
                                    ?>
                                    <tr >
                                        <td ><?php echo isset($page['time'])?$page['time']:"";?></td>
                                        <td ><?php echo isset($page['type'])?$page['type']:"";?></td>
                                        <td ><?php
                                            $name = '';
                                            if($page['type']=='page'){
                                                $name='pages[]';
                                            }else{
                                                $name='queries[]';
                                            }
                                            $value = $page['id'];
                                            echo "<input type=\"checkbox\" name='$name' value='$value'>";
                                            ?></td>
                                        <td ><?php echo isset($page['taskID'])? $taskIDNameMap[$page['taskID']] :"";?></td>
                                        <td ><?php echo isset($page['sessionID']) ?$page['sessionID'] : "";?></td>
                                        <td ><?php echo isset($page['title']) ?$page['title'] : "";?></td>
                                        <td ><?php echo isset($page['url']) ?substr($page['url'],0,15)."..." : "";?></td>
                                        <!--                                        <td class="col-xs-1">--><?php //echo $page['localTime'];?><!--</td>-->
                                        <!--                                        <td class="col-xs-1">Page</td>-->
                                        <!--                                        <td class="col-xs-1">Checkbox</td>-->
                                        <!--                                        <td class="col-xs-1">Checkbox</td>-->
                                        <!--                                        <td class="col-xs-1">ID</td>-->
                                        <!--                                        <td class="col-xs-2">ID</td>-->
                                        <!--                                        <td class="col-xs-2">Title</td>-->
                                        <!--                                        <td class="col-xs-2">URL</td>-->
                                    </tr>
                                    <?php

                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <center>
                            <input type="hidden" name="userID" <?php echo "value='$userID'"?>/>
                            <button type="button" value="restore_button" class="btn btn-success">Mark Session Test</button>
                        </center>
                    </form>


                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel panel-heading">
                        <center><h4>Assign to:</h4></center>
                    </div>
                    <div>
                        <center>
                            <div>
                                <button type="button" class="btn btn-primary">1</button>
                            </div>

                            <div>
                                <button type="button" class="btn btn-primary">2</button>
                            </div>

                            <div>
                                <button type="button" class="btn btn-primary">3</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-success">+ Add Session</button>
                            </div>
                        </center>
                    </div>
                </div>
            </div>
        </div>


    </div>
    </body>
    </html>
<?php
?>