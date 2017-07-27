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

$markTasksPanels = getMarkTasksPanels($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);

$tasksPanel = getTasksPanel($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
?>


    <html>
    <head>
        <title>
            Mark Tasks
        </title>

        <!--        <link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">-->
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
            var mark_task_form_id= '#mark_task_form';
            var tasks_panel_id= '#mark_tasks_panel';
            var task_button_panel_id = '#task_buttons';
            var add_task_form_id = '#add_task_form';




            $(document).ready(function(){
                $(task_button_panel_id+" button").click(function(ev) {
                    ev.preventDefault();
                    var taskID = $(this).data('task-id');
//                    alert(taskID);
                    var formData = $(mark_task_form_id).serialize();
                    formData = formData + "&taskID="+taskID;
//                    alert(formData);
//                    alert($(mark_task_form_id).attr('action'));
                    $.ajax({
                        type: 'POST',
                        url: $(mark_task_form_id).attr('action'),
                        data: formData
                    }).done(function(response) {
                        alert(response);
                        response = JSON.parse(response);
                        $(tasks_panel_id).html(response.taskpanels_html);
                        $('#addtask_confirmation').html("Task annotated!");
                        $('#addtask_confirmation').show();
                        $('#addtask_confirmation').fadeOut(2000);
                    });
                });

                $(add_task_form_id+" button").click(function(ev){
                    ev.preventDefault()// cancel form submission
                    var formData = $(add_task_form_id).serialize();
                    alert(formData);
                    if($(this).attr("value")=="addtask_button"){
                        $.ajax({
                            type: 'POST',
                            url: $(add_task_form_id).attr('action'),
                            data: formData
                        }).done(function(response) {
//                            alert(response);
                            response = JSON.parse(response);
                            $('#addtask_panel').html(response.taskshtml);
                            $('#addtask_confirmation').html("Task added!");
                            $('#addtask_confirmation').show();
                            $('#addtask_confirmation').fadeOut(2000);
                        });
                    }
                });
//                $("form input[type=submit]").click(function() {
//                    $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
//                    $(this).attr("clicked", "true");
//                });


                }
            );


        </script>
    </head>





    <body >
<!--    <body style="background-color:gainsboro">-->
    <div class="container-fluid">
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
                                    $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/markTasks.php', $selectedStartTimeSeconds);
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




        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Step 1) Choose Sessions to Assign to a Task</h4></center>
                    </div>
                    <form id="mark_task_form" action="../services/utils/runPageQueryUtils.php?action=markTasks">
                    <div class="panel-body" id="mark_tasks_panel">


                        <?php
                        echo $markTasksPanels['taskpanels_html'];
                        ?>


                    </div>
                        <center>
                            <input type="hidden" name="userID" <?php echo "value='$userID'";?>/>
                            <input type="hidden" name="startTimestamp" <?php echo "value='$selectedStartTimeSeconds'";?>/>
                            <input type="hidden" name="endTimestamp" <?php echo "value='$selectedEndTimeSeconds'";?>/>
                        </center>
                    </form>

                </div>
            </div>


            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Step 2) Click to Assign a Task</h4></center>
                    </div>
                    <div class="panel-body" id="addtask_panel">

                        <?php
                            echo $tasksPanel['taskshtml'];
                        ?>


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
                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['sessions']."'>&laquo; Back (Sessions)</a>";
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['query segments']."'>Next (Query Segments + Intentions) &raquo;</a>";
                            ?>
                        </center>
                    </div>

                </div>
            </div>
        </div>

        <?php
        echo $markTasksPanels['nullpanel_html'];
        ?>


    </div>
    </body>
    </html>
<?php
?>