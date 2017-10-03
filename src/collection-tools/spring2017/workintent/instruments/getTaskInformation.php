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
$userID = $_GET['userID'];

$selectedStartTimeSeconds = null;
if(isset($_GET['startTime'])){
    $selectedStartTimeSeconds = $_GET['startTime'];
}else{
    $selectedStartTimeSeconds  = strtotime('today midnight');
}
$selectedEndTimeSeconds = getStartEndTimestamp($selectedStartTimeSeconds);
$selectedEndTimeSeconds  =$selectedEndTimeSeconds['endTime'];

$startEndTimestampList = getStartEndTimestampsList($userID,strtotime('today midnight'),30);

$taskIDNameMap = getTaskIDNameMap($userID);

$markTasksPanels = getMarkTasksPanels($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);

$tasksPanel = getTasksPanel($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
$taskInformationPanel = getTaskInformationPanels($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
//print_r(getTasksForDay($userID,$selectedStartTimeSeconds));
?>


    <html>
    <head>
        <title>
            Get Task Information
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

            .alert{
                position:fixed;
                top:0;
                align:center;
                width:100%;
                display:none;
                margin: 0 auto;
            }
        </style>


        <script>
            var mark_task_form_id= '#mark_task_form';
            var tasks_panel_id= '#mark_tasks_panel';
            var task_button_panel_id = '#task_buttons';
            var add_task_form_id = '#add_task_form';




            $(document).ready(function(){

                var annotation_function = function(ev) {
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
//                        alert(response);
                        response = JSON.parse(response);
                        if(response.hasOwnProperty('error')){
                            $('#addtask_confirmation').html(response.message);
                            $('#addtask_confirmation').removeClass('alert-success');
                            $('#addtask_confirmation').addClass('alert-danger');
                            $('#addtask_confirmation').show();
                            $('#addtask_confirmation').fadeOut(3000);


                        }else{
                            $(tasks_panel_id).html(response.taskpanels_html);
                            $(add_task_form_id+" button").click(add_task_function);
                            $(task_button_panel_id+" button").click(annotation_function);
                            $('#addtask_confirmation').removeClass('alert-danger');
                            $('#addtask_confirmation').addClass('alert-success');
                            $('#addtask_confirmation').html("Task annotated!");
                            $('#addtask_confirmation').show();
                            $('#addtask_confirmation').fadeOut(3000);
                        }

                    }).fail(function(data) {
                        alert("Communication to the server was temporarily lost. Your task was not added. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                    });
                };

                $(task_button_panel_id+" button").click(annotation_function);

                var add_task_function = function(ev){
                    ev.preventDefault()// cancel form submission
                    var formData = $(add_task_form_id).serialize() + "&"+$(mark_task_form_id).serialize();
//                    alert(formData);
                    if($(this).attr("value")=="addtask_button"){
                        $.ajax({
                            type: 'POST',
                            url: $(add_task_form_id).attr('action'),
                            data: formData
                        }).done(function(response) {
//                            alert(response);
                            response = JSON.parse(response);
                            if(response.hasOwnProperty('error')){
                                $('#addtask_confirmation').html(response.message);
                                $('#addtask_confirmation').removeClass('alert-success');
                                $('#addtask_confirmation').addClass('alert-danger');
                                $('#addtask_confirmation').show();
                                $('#addtask_confirmation').fadeOut(3000);
                            }else{
                                $(tasks_panel_id).html(response.taskpanels_html);
                                $('#addtask_panel').html(response.taskshtml);
//                            $(tasks_panel_id).html(response.taskpanels_html);
                                $(add_task_form_id+" button").click(add_task_function);
                                $(task_button_panel_id+" button").click(annotation_function);
                                $('#addtask_confirmation').removeClass('alert-danger');
                                $('#addtask_confirmation').addClass('alert-success');
                                $('#addtask_confirmation').html(response.message);
                                $('#addtask_confirmation').show();
                                $('#addtask_confirmation').fadeOut(3000);
                            }

                        }).fail(function(data) {
                            alert("Communication to the server was temporarily lost. Your task was not added. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                        });
                    }
                };

                $(add_task_form_id+" button").click(add_task_function);
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
                                    $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/getTaskInformation.php', $selectedStartTimeSeconds,$userID);
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




        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-8">


                        <?php
                        echo $taskInformationPanel;
//                        echo $markTasksPanels['taskpanels_html'];
                        ?>


            </div>



        </div>


    </div>

    </body>
    </html>
<?php
?>