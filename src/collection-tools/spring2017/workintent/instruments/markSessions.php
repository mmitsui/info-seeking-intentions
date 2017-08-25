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
$sessionTables = getSessionTables($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);

?>


    <html>
    <head>
        <title>
            Mark Sessions
        </title>

        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
<!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/css/bootstrap-slider.min.css">-->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/bootstrap-slider.min.js"></script>-->

        <style>
            /*.slider {*/
                /*height: 100% !important;*/
            /*}*/

            .tab-pane{
                height:300px;
                overflow-y:scroll;
                width:90%;
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
            var session_form_id= '#session_form';
            var begin_index = -1;
            var end_index = -1;
//            var slider_id = '#session_slider';
//            var slider_params = {
//                reversed : false,
//                formatter : function(value){
//                    var minValue = value[0];
//                    var maxValue = value[1];
//                    var minTime = $("td[name=time_"+minValue+"]").html();
//                    var minTitle = $("td[name=title_"+minValue+"]").html();
//                    var maxTime = $("td[name=time_"+maxValue+"]").html();
//                    var maxTitle = $("td[name=title_"+maxValue+"]").html();
//                    return minTime + " (" + minTitle + ") - " + maxTime + " (" + maxTitle + ")";
//                }
//            };
//            var slider_init_function = function(){
//                return $(slider_id).slider(slider_params);
//            };

//            var slider_slidestop_function = function(f){
//                var values = $(slider_id).val().split(",");
//                values = $.map(values,function(elem,i){
//                    return parseInt(elem);
//                });
//                var minValue = values[0];
//                var maxValue = values[1];
////                        alert("min " + minValue + "max " + maxValue + values);
//                $(session_form_id+" input[type='checkbox']").filter(function() {
//                    return ($(this).data('table-index') >= minValue && $(this).data('table-index') <= maxValue);
//                }).prop( "checked", true );
//
//                $(session_form_id+" input[type='checkbox']").filter(function() {
//                    return ($(this).data('table-index') < minValue || $(this).data('table-index') > maxValue);
//                }).prop( "checked", false );
//            };
            var denote_beginend_function = function(ev){
                if($(this).attr("name")=='begin_button'){

                    if($(this).hasClass('active')){
                        $("button[name='begin_button']").removeClass('active');
                        $("button[name='begin_button']").show();
                    }else{
                        $("button[name='begin_button']").removeClass('active');
                        $("button[name='begin_button']").hide();
                        $(this).addClass('active');
                        $(this).show();

                    }
//                    if(end_index != -1){
//                        $(this).addClass('active');
//                    }else{
//                        $(this).toggleClass('active');
//                    }

                    begin_index = $(this).data('table-index');
                }else if($(this).attr("name")=='end_button'){



                    if($(this).hasClass('active')){
                        $("button[name='end_button']").removeClass('active');
                        $("button[name='end_button']").show();
                    }else{
                        $("button[name='end_button']").removeClass('active');
                        $("button[name='end_button']").hide();
                        $(this).addClass('active');
                        $(this).show();

                    }
//                    if(begin_index != -1){
//                        $(this).addClass('active');
//                    }else{
//                        $(this).toggleClass('active');
//                    }
                    end_index = $(this).data('table-index');
                }

                if(begin_index != -1 && end_index != -1){
                    $(session_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
                    }).prop( "checked", true );

                    $(session_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('table-index') < begin_index || $(this).data('table-index') > end_index);
                    }).prop( "checked", false );

                }else{
                    if(begin_index != -1){
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') == begin_index);
                        }).prop( "checked", true );

                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') != begin_index);
                        }).prop( "checked", false );

                    }else if(end_index != -1){
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') == end_index);
                        }).prop( "checked", true );

                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') != end_index);
                        }).prop( "checked", false );

                    }else{
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return true;
                        }).prop( "checked", false );

                    }
                }
            }
            var mark_session_button_function = function(ev){
                ev.preventDefault()// cancel form submission
                var formData = $(session_form_id).serialize();
                if($(this).attr("value")=="mark_session_button"){
                    $.ajax({
                        type: 'POST',
                        url: $(session_form_id).attr('action'),
                        data: formData
                    }).done(function(response) {
//                                alert(response);
                        response = JSON.parse(response);
                        if(response.hasOwnProperty('error')){
                            $('#mark_session_confirmation').removeClass('alert-success');
                            $('#mark_session_confirmation').addClass('alert-danger');
                            $('#mark_session_confirmation').html(response.message);
                            $('#mark_session_confirmation').show();
                            $('#mark_session_confirmation').fadeOut(3000);
                        }else{
                            $('#session_panel').html(response.sessionhtml);
                            $(session_form_id+" button[name='mark_session_button']").click(mark_session_button_function);
                            $(session_form_id+" button[name='begin_button']").click(denote_beginend_function);
                            $(session_form_id+" button[name='end_button']").click(denote_beginend_function);
                            begin_index = -1;
                            end_index = -1;
                            $('#mark_session_confirmation').removeClass('alert-danger');
                            $('#mark_session_confirmation').addClass('alert-success');
                            $('#mark_session_confirmation').html("Session marked!");
                            $('#mark_session_confirmation').show();
                            $('#mark_session_confirmation').fadeOut(3000);
//                                mySlider = slider_init_function();
//                                mySlider.on("slideStop",slider_slidestop_function);
                        }

                    }).fail(function(data) {
                        alert("There was a server error.  Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                    });
                }
            };

            $(document).ready(function(){
//                    var mySlider = slider_init_function();
//                    mySlider.css('width','100% !important');
//                    mySlider.on("slideStop",slider_slidestop_function);


//                $(session_form_id+" input[type='checkbox']").filter(function() {
//                    return ($(this).data('table-index') > 3 && $(this).data('table-index') < 10);
//                }).prop( "checked", true );

                    $(session_form_id+" button[name='mark_session_button']").click(mark_session_button_function);
                    $(session_form_id+" button[name='begin_button']").click(denote_beginend_function);
                    $(session_form_id+" button[name='end_button']").click(denote_beginend_function);

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
                                    $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/markSessions.php', $selectedStartTimeSeconds);
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


        <!--   Query Log and Progress     -->


        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Mark Your Day's Sessions</h4></center>

                    </div>
                </div>
                    <form id="session_form" action="../services/utils/runPageQueryUtils.php?action=markSession">
<!--                        <div class="panel-body tab-pane" id="session_panel">-->
<!--                        </div>-->
                        <div class="container" id="session_panel">
                            <?php
                            echo $sessionTables['sessionhtml'];
                            ?>
<!--                            <div class="row">-->
<!--                                <div class="col-md-1 border">-->
<!--                                    <input id="session_slider" type="text" height="100%" data-slider-min="0" data-slider-max="30" data-slider-step="1" data-slider-value="[0,10]" data-slider-orientation="vertical"/>-->
<!--                                </div>-->
<!--                                <div class="col-md-11 border tab-pane">-->
<!--                                    -->
<!--                                </div>-->
<!--                            </div>-->


                        </div>

                    </form>



            </div>


        </div>

        <div class="row">

            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center>
                            <?php
                            $actionUrls = actionUrls($selectedStartTimeSeconds);
                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['home']."'>&laquo; Back (Home)</a>";
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['tasks']."'>Next (Assign Tasks to Sessions) &raquo;</a>";
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
    <center><h3 id="mark_session_confirmation" class="alert alert-success"></h3></center>

<!--    <div class="container">-->
<!--        <div class="row">-->
<!--            <div class="col-md-12">-->
<!--                <div class="panel panel-primary">-->
<!--                    <div class="panel panel-heading">-->
<!--                        <center><h4>Assign to:</h4></center>-->
<!--                    </div>-->
<!--                    <div>-->
<!--                        <center>-->
<!--                            <div>-->
<!--                                <button type="button" class="btn btn-primary">1</button>-->
<!--                            </div>-->
<!---->
<!--                            <div>-->
<!--                                <button type="button" class="btn btn-primary">2</button>-->
<!--                            </div>-->
<!---->
<!--                            <div>-->
<!--                                <button type="button" class="btn btn-primary">3</button>-->
<!--                            </div>-->
<!--                            <div>-->
<!--                                <button type="button" class="btn btn-success">+ Add Session</button>-->
<!--                            </div>-->
<!--                        </center>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

    </body>
    </html>
<?php
?>