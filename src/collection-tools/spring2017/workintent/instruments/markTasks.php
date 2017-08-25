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
                        alert("There was a server error.  Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
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
                            alert("There was a server error.  Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
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
                        <center><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tutorial_modal">Review Tutorial</button></center>
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


        <?php
        echo $markTasksPanels['nullpanel_html'];
        ?>

        <div class="row">

            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center>
                            <?php
                            $actionUrls = actionUrls($selectedStartTimeSeconds);
                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['sessions']."'>&laquo; Back (Sessions)</a>";
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['query segments']."'>Next (Mark Query Segments + Intentions) &raquo;</a>";
                            ?>
                        </center>
                    </div>

                </div>
            </div>
        </div>


    </div>

<div class="modal fade" id="tutorial_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Tutorial</h4>
            </div>





            <div class="modal-body" id="select_intentions_panel">

                <p>Once you have downloaded and installed the browser plugin for this study, it can be used to automatically record your daily browsing and searching activities in your Chrome browser.  By default, it is turned off, but you may click the plugin to activate it and start recording.  You may also click “Log Out” to stop recording. At any time, you may also click the Chrome extension to annotate the day’s activities.  You are asked to annotate your activity for every day of the study.</p>

                <p>The annotation is divided into 4 main phases.  Each phase is outlined below:</p>


                <p><u><strong>Mark As Private</strong></u></p>
                <ul>
                    <li>Select any pages that you wish to permanently delete from the log.  To do so, check their respective boxes in “Send Private Items to Trash” and click “Send to Trash”.</li>
                    <li>To confirm the deletion of these pages, select them in the “Trash Bin” and click “Permanently Delete”.  To undo deletion, select them in the “Trash Bin” and click "Undo Delete".
                    </li>
                </ul>


                <p><u><strong>Mark Sessions</strong></u></p>
                <ul>
                    <li>Here, you are asked to mark the beginning and end of a search session.</li>
                    <li>A search session is defined as a contiguous sequences of related searches - i.e., contiguous searches related to the same task.</li>
                    <li>To mark the beginning of a search session, click the “Begin” button for the page/query that indicates the beginning of the session.</li>
                    <li>To mark the end of a search session, click the “End” button for the page/query that indicates the end of the session.</li>
                    <li>To confirm this selection, click “Mark Session” at the bottom of the page.</li>
                    <li>To undo your selection(s), click the “Begin” or “End” button again.</li>
                </ul>







                <p><u><strong>Mark Tasks</strong></u></p>
                <ul>
                    <li>Next, you must assign sessions to tasks.</li>
                    <li>Some of the listed tasks are ones we asked you about in the pre-study interview.  You may also create new ones in the right-hand panel “2) Click to Assign a Task”.</li>
                    <li>Multiple sessions may belong to the same task.  This is fine.</li>
                    <li>To assign a session to a task, click the checkbox next to it.  You may then assign the task in one of two ways:</li>
                    <li>Click an existing task from the provided options</li>
                    <li>Create a new task in the bottom of the panel “2) Click to Assign a Task”.  After naming a new task, click “+ Add Task”</li>
                </ul>







                <p><u><strong>Mark Query Segments And Intentions</strong></u></p>
                <ul>
                    <li>Next you must assign intentions to each query segment.</li>
                    <li>You may first need to mark query segments within sessions.  Recall that each session is composed of one or more query segments pertaining to the same task.</li>
                    <li>A query segment is begun by a query and continues with all of the browsing and clicking that follows from that query.  It ends before the start of the next query.</li>
                    <li>Some of the annotation may be automatically done.  Other query segments may need to be assigned manually.</li>
                    <li>Assignment of the beginning and end of query segments works similarly to the “Begin” and “End” annotation for marking sessions.</li>
                    <li>After marking a query segment, you will be prompted to mark the intentions for that query segment.</li>

                    <li>You must choose one or more search intention; the elicitation question is:
                        <ul>
                            <li>What were you trying to accomplish (what was your intention) during this part of the search? Please choose one or more of the "search intentions" on the right; if none fits your goal at this point in the search, please choose "Other", and give a brief explanation.</li>
                        </ul>
                    </li>

                    <li>For each identified search intention, you are asked:
                        <ul>
                            <li>"Were you successful?" You must answer either "Yes" or "No".</li>
                            <li>If "No", you must respond, in a text entry box, to the question: "Why not?"</li>

                        </ul>
                    </li>
                </ul>

                <p>For more information about this study, please send e-mail to Matthew Mitsui at mmitsui@scarletmail.rutgers.edu. You can also contact Matthew Mitsui to ask questions or get more information about the project.</p>













            </div>


            <div class="modal-footer" id="select_intentions_footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Exit</button>

            </div>
        </div>
    </div>


</div>
    <center><h3 id="addtask_confirmation" class="alert alert-success"></h3></center>
    </body>
    </html>
<?php
?>