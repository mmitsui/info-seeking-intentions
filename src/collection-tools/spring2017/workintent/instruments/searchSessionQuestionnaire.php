<?php
session_start();
require_once('../core/Base.class.php');
require_once('../core/Util.class.php');
require_once('../services/utils/loginUtils.php');
require_once('../services/utils/dayTimeUtils.php');
require_once('../services/utils/pageQueryUtils.php');
require_once('../services/utils/sessionTaskUtils.php');
require_once('../services/utils/querySegmentIntentUtils.php');

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
$querySegmentTables = getSessionQuestionnaireTables($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
$intentionsPanel = getSearchQuestionnairePanel($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
$sessionIDs = getSessionIDs($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);



$sessionIDToLabel = array();
$query = "SELECT * FROM session_labels_user WHERE userID=$userID";
$cxn = Connection::getInstance();
$result = $cxn->commit($query);
while($line = mysql_fetch_array($result,MYSQL_ASSOC)){
    $sessionIDToLabel[$line['id']] = $line['sessionLabel'];
}


?>


    <html>
    <head>
        <title>
            Search Session Questionnaire
        </title>

        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../study_styles/font-awesome-4.7.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/css/bootstrap-slider.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/bootstrap-slider.min.js"></script>
        <script src="../lib/bootstrap_notify/bootstrap-notify.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <style>


            /*div#popup-container {*/
                /*display: none;*/
                /*position: absolute;*/
            /*}*/

            /*div.searchsegmentpanel {*/
                /*display: none;*/
                /*position: absolute;*/
            /*}*/

            body{
                background: #DFE2DB !important;
            }

            .tab-pane{
                height:300px;
                overflow-y:scroll;
                width:90%;
            }






            .alert{
                z-index: 999999 !important;
            }




            .right {
                margin-right: 25%;
            }
            .left {
                position:fixed; // keep fixed to window
            padding: 10px;

                margin-left: 75%;

                top: 0; left: 0; bottom: 0; // position to top left of window
            position: fixed;

                overflow-y: scroll;


                height:100%; //set dimensions
                /*width:20%;*/
            transition: width ease .5s; // fluid transition when resizing

                                               /* Sass/Scss only:
                                                 Using a selector (.open-nav) with an "&" afterward is actually selecting
                                               any parent selector. For instance, this outputs "body.open-nav .left { ... }"
                                               More info: http://thesassway.com/intermediate/referencing-parent-selectors-using-ampersand
                                               */
                                           body.open-nav & {
                width:200px;
            }

            ul {
                list-style:none;
                margin:0; padding:0;

            li {
                margin-bottom:25px;
            }
            }

            a {
                color:shade(darkslategray, 50%);
                text-decoration:none;
                border-bottom:1px solid transparent;
                transition:
                        color ease .35s,
                        border-bottom-color ease .35s;

            &:hover {
                 color:white;
                 border-bottom-color:white;
             }

            &.open {
                 font-size:1.75rem;
                 font-weight:700;
                 border:0;
             }
            }
            }
        </style>

        <script>
            var querysegment_form_id= '#querysegment_form';
            var intents_form_id= '#intentions_form';

            var sessionID_to_label = <?php echo json_encode($sessionIDToLabel);?>;
            var questionnaire_session_id = -1;





















            var clear_intentions_function = function() {
                $("tr").filter(function() {
                    return true;
                }).removeClass( "active");


                $('input:radio').prop('checked',false);
                $('textarea').val('');
                $(intents_form_id+" input[type='checkbox']").prop( "checked", false );
                $('#intent_modal').removeClass('left');
                $('#intent_modal').hide();
                $('#main_div').removeClass('right');
                $('#forwardback_buttons').css('right','20px');
            }



            var begin_mark_intentions_function = function(ev){

                if($(this).attr("name")=='initiate_mark_intentions_button'){
                    questionnaire_session_id = $(this).data('session-id');
                }
                $("input[type='checkbox']").filter(function() {
                    return ($(this).data('session-id') == questionnaire_session_id);
                }).prop( "checked", true );

                $("input[type='checkbox']").filter(function() {
                    return ($(this).data('session-id') != questionnaire_session_id );
                }).prop( "checked", false );
            }




            var toggle_text_function = function(ev){
                var intent_key = $(this).data('intent-key');
                if($(this).attr('value')=='5' ||$(this).attr('value')=='6' ||$(this).attr('value')=='7'){
                    $("#failurereason_div_"+intent_key+"").hide();
                    $("textarea[name='"+intent_key+"_description']").prop('disabled',true);
                }else{
                    $("#failurereason_div_"+intent_key+"").show();
                    $("textarea[name='"+intent_key+"_description']").prop('disabled',false);
                }
            }


            var mark_intentions_button_function = function(ev){
                ev.preventDefault()// cancel form submission
                var formData = $(querysegment_form_id).serialize()+"&"+$(intents_form_id).serialize()+"&sessionID="+questionnaire_session_id;


                if($(this).attr("value")=="mark_intentions_button"){
                    var intentions = ['successful','useful'];
                    var input_valid = true;
                    var arrayLength = intentions.length;
                    for (var i = 0; i < arrayLength; i++) {
                        var intention = intentions[i];
                        if($("input[type='radio'][data-intent-key='"+intention+"']:checked").length == 0){
                            input_valid = false;
                            break;
                        }else{
                            if($("input[type='radio'][data-intent-key='"+intention+"']:checked").val() <=4){
                                if($.trim($("textarea[name='"+intention+"_description']").val()) == ''){
                                    input_valid = false;
                                    break;
                                }
                            }
                        }
                    }




                    if(input_valid){
                        $.ajax({
                            type: 'POST',
                            url: $(intents_form_id).attr('action'),
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
                            }else{
                                $.notify({
                                    // options
                                    message: "Session questionnaire answered!"
                                },{
                                    // settings
                                    type: 'success'
                                });

                                $('#intent_modal').modal("hide");
                                $('#querysegment_panel').html(response.querysegmenthtml);
                                $('#select_intentions_panel').html(response.questionnairehtml);
                                $('#progressbar_segments_container').html(response.progressbar_segments_html);
                                $("button[name='mark_intentions_button']").unbind("click").click(mark_intentions_button_function);
                                $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                                $("button[name='cancel_intentions_button']").unbind("click").click(clear_intentions_function);
                                $('[data-toggle="tooltip"]').tooltip();
                                $('input:radio').unbind("click").click(toggle_text_function);
                                $("button[name='intent_modal_button']").fadeOut("slow");
                                clear_intentions_function();
                            }
                        }).fail(function(data) {
                            alert("Communication to the server was temporarily lost. Intentions were not marked. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                        });
                    }else{
                        $.notify({
                            // options
                            message: "Something is wrong with your input input.  Please check for the following:" +
                            "<ol>" +
                            "<li>You did not answer both questions regarding success and usefulness.</li>" +
                            "<li>If you did answer these questions but were asked for your reasons, you did not give a reason.</li>" +
                            "</ol>"
                        },{
                            // settings
                            type: 'danger',
                            delay: 20000
                        });
                    }
                }
            };



            function show_questionnaire_modal(sessionID){
                $("tr").filter(function() {
                    return ($(this).data('session-id') == sessionID);
                }).addClass( "active");
                $("tr").filter(function() {
                    return ($(this).data('session-id') != sessionID);
                }).removeClass( "active");

                $('#intent_modal').addClass('left');
                $('#intent_modal').show();
                $('#main_div').addClass('right');
                $('#forwardback_buttons').css('right','25%');
            }



            $(document).ready(function(){
                Object.keys(sessionID_to_label).forEach(function(key) {
                    if(typeof(key)=='string'){
                        sessionID_to_label[parseInt(key)] = parseInt(sessionID_to_label[key]);
                    }
                });




                $("button[name='mark_intentions_button']").unbind("click").click(mark_intentions_button_function);
                $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                $("button[name='cancel_intentions_button']").unbind("click").click(clear_intentions_function);
                $('input:radio').unbind("click").click(toggle_text_function);
                $('div[name="whichsearchsegmentpanel"]').draggable({cursor:'move'});
                $('[data-toggle="tooltip"]').tooltip();

                }
            );


        </script>

    </head>





    <body>

<!--    <center><h3 id="mark_querysegment_confirmation" class="alert alert-success"></h3></center>-->
<!--    <body style="background-color:gainsboro">-->
    <div id='main_div' class="container-fluid">
        <!--   Dates Tab and Review     -->


        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="">
                        <h1>
                            (Annotation Part 5/5)
                        </h1>
                        <h1>Search Session Questionnaire
                        </h1>

                        <div id="progressbar_segments_container">
                            <?php
                            echo $querySegmentTables['progressbar_segments_html'];
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
                                    $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/searchSessionQuestionnaire.php', $selectedStartTimeSeconds);
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
                        <center>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tutorial_modal">Press for Help</button>
                        </center>
                    </div>


                </div>
            </div>
        </div>


        <!--   Query Log and Progress     -->
<!--        <div class="row">-->
<!--            <div class="col-md-12" id="progressbar_container">-->
<!--                --><?php
//                echo $querySegmentTables['progressbar_html'];
//                ?>
<!--            </div>-->
<!--        </div>-->

        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Answer Questions For Each Session</h4></center>

                    </div>
                    <form id="querysegment_form" action="../services/utils/runPageQueryUtils.php?action=markQuerySegment">
                        <!--                        <div class="panel-body" id="querysegment_panel">-->

<!--                        <div class="container" id="querysegment_panel">-->
                        <div class="panel-body" id="querysegment_panel">
                            <?php
                            echo $querySegmentTables['querysegmenthtml'];
                            ?>
                        </div>
<!--                        </div>-->
                        <div class="container">
                            <center>
                                <input type="hidden" name="userID" <?php echo "value='$userID'"?>/>
                                <input type="hidden" name="startTimestamp" <?php echo "value='$selectedStartTimeSeconds'"?>/>
                                <input type="hidden" name="endTimestamp" <?php echo "value='$selectedEndTimeSeconds'"?>/>
                                <div>
                                    <button type="button" name="mark_querysegments_button" value="mark_querysegments_button" class="btn btn-lg btn-warning" style="position: fixed; bottom: 20px; left: 20px; display:none; z-index:100;">Annotate Search Segments</button>
                                </div>
                            </center>

                        </div>

                    </form>
                </div>



                </div>
            </div>





        <div id='forwardback_buttons' class="btn-group" style="position: fixed; bottom: 20px; right:20px; z-index: 90;">

            <?php
            $actionUrls = actionUrls($selectedStartTimeSeconds);
            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['query segments']."'><i class=\"fa fa-arrow-circle-left\" aria-hidden=\"true\"></i> Back (Annotate Search Segments + Intentions)</a>";
            //                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
            //                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['intentions']."'>Next (Intentions) &raquo;</a>";
            ?>
        </div>

        </div>














    <div class="panel panel-default" id="intent_modal" style="display:none">
            <div class="panel-heading">
                <h4 class="modal-title" id="myModalLabel">Search Session Questionnaire</h4>
            </div>
            <form id="intentions_form" action="../services/utils/runPageQueryUtils.php?action=submitSessionQuestionnairePart1">
                <div class="panel-body" >
                        <div class="well">
                            <div><p><h5>Now, we'd like to ask you some questions about <br>the search session that you engaged in <br>with respect to this task. <br><br>Please evaluate the search session by <br>answering following questions:</h5></p></div>
                        </div>

                        <div id="select_intentions_panel">
                            <?php
                            echo $intentionsPanel['questionnairehtml'];
                            ?>
                        </div>
                </div>
            </form>
    </div>

    <?php
    printTutorialModal('sessionquestionnaire');
    ?>

    </body>
    </html>
<?php
?>