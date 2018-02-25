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
            Assign Tasks
        </title>

        <!--        <link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">-->
        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../study_styles/font-awesome-4.7.0/css/font-awesome.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="../lib/bootstrap_notify/bootstrap-notify.min.js"></script>


        <style>

            body{
                background: #DFE2DB !important;
            }
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

            /*.alert{*/
                /*position:fixed;*/
                /*top:0;*/
                /*align:center;*/
                /*width:100%;*/
                /*display:none;*/
                /*margin: 0 auto;*/
            /*}*/

            /*input[type='checkbox']{*/
                /*width:15px;*/
                /*height:15px;*/
            /*}*/
        </style>


        <script>
            var mark_task_form_id= '#mark_task_form';
            var tasks_panel_id= '#mark_tasks_panel';
            var task_button_panel_id = '#task_buttons';
            var add_task_form_id = '#add_task_form';
            var progressbar_container_id = '#progressbar_container';


            var highlight_panels = function(){
                var panel_index = $(this).data('panel-index');
                if(this.checked){
                    $("div").find("[data-panel-index='"+panel_index+"']").css('background-color','lightgray');
                }else{
                    $("div").find("[data-panel-index='"+panel_index+"']").css('background-color','');
                }
            }




            $(document).ready(function(){
                    $('input:checkbox').change(highlight_panels);

                var annotation_function = function(ev) {
                    ev.preventDefault();
                    var taskID = $(this).data('task-id');
                    var formData = $(mark_task_form_id).serialize();
                    formData = formData + "&taskID="+taskID;
                    $.ajax({
                        type: 'POST',
                        url: $(mark_task_form_id).attr('action'),
                        data: formData
                    }).done(function(response) {
                        response = JSON.parse(response);
                        if(response.hasOwnProperty('error')){
                            $.notify({
                                // options
                                message: response.message
                            },{
                                // settings
                                type: 'danger'
                            });
//                            $('#addtask_confirmation').html(response.message);
//                            $('#addtask_confirmation').removeClass('alert-success');
//                            $('#addtask_confirmation').addClass('alert-danger');
//                            $('#addtask_confirmation').show();
//                            $('#addtask_confirmation').fadeOut(3000);


                        }else{
                            $.notify({
                                // options
                                message: "Task assigned!"
                            },{
                                // settings
                                type: 'success'
                            });
                            $(progressbar_container_id).html(response.progressbar_html);
                            $(tasks_panel_id).html(response.taskpanels_html);
                            $(add_task_form_id+" button").unbind("click").click(add_task_function);
                            $(task_button_panel_id+" button").unbind("click").click(annotation_function);
                            $('input:checkbox').change(highlight_panels);
//                            $('#addtask_confirmation').removeClass('alert-danger');
//                            $('#addtask_confirmation').addClass('alert-success');
//                            $('#addtask_confirmation').html("Task assigned!");
//                            $('#addtask_confirmation').show();
//                            $('#addtask_confirmation').fadeOut(3000);
                        }

                    }).fail(function(data) {
                        alert("Communication to the server was temporarily lost. Your task was not added. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                    });
                };

                $(task_button_panel_id+" button").unbind("click").click(annotation_function);

                var add_task_function = function(ev){
                    ev.preventDefault()// cancel form submission
                    var formData = $(add_task_form_id).serialize() + "&"+$(mark_task_form_id).serialize();
                    if($(this).attr("value")=="addtask_button"){
                        $.ajax({
                            type: 'POST',
                            url: $(add_task_form_id).attr('action'),
                            data: formData
                        }).done(function(response) {
                            response = JSON.parse(response);
                            if(response.hasOwnProperty('error')){
                                $.notify({
                                    // options
                                    message: response.message
                                },{
                                    // settings
                                    type: 'danger'
                                });
//                                $('#addtask_confirmation').html(response.message);
//                                $('#addtask_confirmation').removeClass('alert-success');
//                                $('#addtask_confirmation').addClass('alert-danger');
//                                $('#addtask_confirmation').show();
//                                $('#addtask_confirmation').fadeOut(3000);
                            }else{
                                $(progressbar_container_id).html(response.progressbar_html);
                                $(tasks_panel_id).html(response.taskpanels_html);
                                $('#addtask_panel').html(response.taskshtml);
                                $('input:checkbox').change(highlight_panels);
//                            $(tasks_panel_id).html(response.taskpanels_html);
                                $(add_task_form_id+" button").unbind("click").click(add_task_function);
                                $(task_button_panel_id+" button").unbind("click").click(annotation_function);
                                $.notify({
                                    // options
                                    message: response.message
                                },{
                                    // settings
                                    type: 'success'
                                });
//                                $('#addtask_confirmation').removeClass('alert-danger');
//                                $('#addtask_confirmation').addClass('alert-success');
//                                $('#addtask_confirmation').html(response.message);
//                                $('#addtask_confirmation').show();
//                                $('#addtask_confirmation').fadeOut(3000);
                            }

                        }).fail(function(data) {
                            alert("Communication to the server was temporarily lost. Your task was not added. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                        });
                    }
                };

                $(add_task_form_id+" button").unbind("click").click(add_task_function);
//                $("form input[type=submit]").unbind("click").click(function() {
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
            <div class="col-md-12">
                <div class="page-header">
                    <div class="">
                        <h1>
                            (Annotation Part 3/5)
                        </h1>
                        <h1>Assign Tasks to Sessions
                        </h1>
                        <div id="progressbar_container">
                            <?php
                            echo $markTasksPanels['progressbar_html'];
                            ?>
                        </div>


                    </div>
                </div>

            </div>
        </div>

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
                        <center><h4>Help</h4></center>
                    </div>
                    <div class="panel-body">
                        <center><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tutorial_modal">Press for Help</button></center>
                    </div>


                </div>
            </div>

        </div>



<!--        <div class="row">-->
<!--            <div class="col-md-12" id="progressbar_container">-->
<!--        --><?php
//            echo $markTasksPanels['progressbar_html'];
//        ?>
<!--            </div>-->
<!--        </div>-->


        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Step 1) Choose Sessions to Assign a Task</h4></center>
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


        <?php
        echo $markTasksPanels['nullpanel_html'];
        ?>



    </div>

<?php
    printTutorialModal('task');
?>
<!--    <center><h3 id="addtask_confirmation" class="alert alert-success"></h3></center>-->

<!--<div class='container' style="position: fixed; bottom: 20px; right:20px; z-index: 90;">-->
<!--    <center>-->

<!--        <div class="panel panel-default">-->
<!--            <div class="panel-heading">-->
<!--                <center>-->


                    <div class="btn-group" style="position: fixed; bottom: 20px; right:20px; z-index: 90;">
                        <?php
                        $actionUrls = actionUrls($selectedStartTimeSeconds);
                        echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['sessions']."'><i class=\"fa fa-arrow-circle-left\" aria-hidden=\"true\"></i> Back (Sessions)</a>";
//                        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                        echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['query segments']."'>Next (Annotate Search Segments + Intentions) <i class=\"fa fa-arrow-circle-right\" aria-hidden=\"true\"></i></a>";
                        ?>

                    </div>
<!--                </center>-->
<!--            </div>-->
<!---->
<!--        </div>-->
<!--    </center>-->
<!--</div>-->

    </body>
    </html>
<?php
?>