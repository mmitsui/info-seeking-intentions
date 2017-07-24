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
$querySegmentTables = getQuerySegmentTables($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
$intentionsPanel = getIntentionsPanel($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
?>


    <html>
    <head>
        <title>
            Mark Query Segments
        </title>

        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/css/bootstrap-slider.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/bootstrap-slider.min.js"></script>

        <style>
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
        </style>

        <script>
            var querysegment_form_id= '#querysegment_form';
            var intents_form_id= '#intentions_form';
            var begin_index = -1;
            var end_index = -1;
//            var slider_id = '#querysegment_slider';
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
//
//            var slider_slidestop_function = function(f){
//                var values = $(slider_id).val().split(",");
//                values = $.map(values,function(elem,i){
//                    return parseInt(elem);
//                });
//                var minValue = values[0];
//                var maxValue = values[1];
////                        alert("min " + minValue + "max " + maxValue + values);
//                $(querysegment_form_id+" input[type='checkbox']").filter(function() {
//                    return ($(this).data('table-index') >= minValue && $(this).data('table-index') <= maxValue);
//                }).prop( "checked", true );
//
//                $(querysegment_form_id+" input[type='checkbox']").filter(function() {
//                    return ($(this).data('table-index') < minValue || $(this).data('table-index') > maxValue);
//                }).prop( "checked", false );
//            };


            var denote_beginend_function = function(ev){
                if($(this).attr("name")=='begin_button'){
                    $("button[name='begin_button']").removeClass('active');
                    if(end_index != -1){
                        $(this).addClass('active');
                    }else{
                        $(this).toggleClass('active');
                    }

                    begin_index = $(this).data('table-index');
                }else if($(this).attr("name")=='end_button'){
                    $("button[name='end_button']").removeClass('active');
                    if(begin_index != -1){
                        $(this).addClass('active');
                    }else{
                        $(this).toggleClass('active');
                    }
                    end_index = $(this).data('table-index');
                }

                if(begin_index != -1 && end_index != -1){
                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
                    }).prop( "checked", true );

                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('table-index') < begin_index || $(this).data('table-index') > end_index);
                    }).prop( "checked", false );

                }else{
                    if(begin_index != -1){
                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') == begin_index);
                        }).prop( "checked", true );

                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') != begin_index);
                        }).prop( "checked", false );

                    }else if(end_index != -1){
                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') == end_index);
                        }).prop( "checked", true );

                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') != end_index);
                        }).prop( "checked", false );

                    }else{
                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                            return true;
                        }).prop( "checked", false );

                    }
                }
            }


            var mark_intentions_button_function = function(ev){

                ev.preventDefault()// cancel form submission
                var formData = $(querysegment_form_id).serialize()+"&"+$(intents_form_id).serialize();

                if($(this).attr("value")=="mark_intentions_button"){

                    $.ajax({
                        type: 'POST',
                        url: $(intents_form_id).attr('action'),
                        data: formData
                    }).done(function(response) {
                        response = JSON.parse(response);
                        $('#querysegment_panel').html(response.querysegmenthtml);
                        $('#select_intentions_panel').html(response.intentionshtml);
                        $("button[name='mark_intentions_button']").click(mark_intentions_button_function);
                        $(querysegment_form_id+" button[name='begin_button']").click(denote_beginend_function);
                        $(querysegment_form_id+" button[name='end_button']").click(denote_beginend_function);
                        begin_index = -1;
                        end_index = -1;
                        $('#mark_querysegment_confirmation').html("Query segment and intentions marked!");
                        $('#mark_querysegment_confirmation').show();
                        $('#mark_querysegment_confirmation').fadeOut(2000);
//                        mySlider = slider_init_function();
//                        mySlider.on("slideStop",slider_slidestop_function);
                    });
                }
            };

            var mark_querysegment_button_function = function(ev){
                ev.preventDefault()// cancel form submission
                var formData = $(querysegment_form_id).serialize();

                if($(this).attr("value")=="mark_querysegment_button"){

                    $.ajax({
                        type: 'POST',
                        url: $(querysegment_form_id).attr('action'),
                        data: formData
                    }).done(function(response) {
                        response = JSON.parse(response);
                        $('#querysegment_panel').html(response.querysegmenthtml);
                        $(querysegment_form_id+" button[name='begin_button']").click(denote_beginend_function);
                        $(querysegment_form_id+" button[name='end_button']").click(denote_beginend_function);
                        begin_index = -1;
                        end_index = -1;
                        $('#mark_querysegment_confirmation').html("Query segment marked!");
                        $('#mark_querysegment_confirmation').show();
                        $('#mark_querysegment_confirmation').fadeOut(2000);
//                        mySlider = slider_init_function();
//                        mySlider.on("slideStop",slider_slidestop_function);
                    });
                }
            };

            $(document).ready(function(){
//                    var mySlider = slider_init_function();
//                    mySlider.on("slideStop",slider_slidestop_function);


                    $("button[name='mark_intentions_button']").click(mark_intentions_button_function);
//                    $(querysegment_form_id+" button[name='mark_querysegment_button']").click(mark_querysegment_button_function);
                    $(querysegment_form_id+" button[name='begin_button']").click(denote_beginend_function);
                    $(querysegment_form_id+" button[name='end_button']").click(denote_beginend_function);

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





    <body style="background-color:gainsboro">
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
                                    $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/markQuerySegments.php', $selectedStartTimeSeconds);
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
//                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
//                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['intentions']."'>Next (Intentions) &raquo;</a>";
                            ?>
                        </center>
                    </div>

                </div>
            </div>



        </div>

        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Mark Your Day's Query Segments</h4></center>

                    </div>
                </div>
                    <form id="querysegment_form" action="../services/utils/runPageQueryUtils.php?action=markQuerySegment">
<!--                        <div class="panel-body" id="querysegment_panel">-->

                        <div class="container" id="querysegment_panel">
                            <?php
                            echo $querySegmentTables['querysegmenthtml'];
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
                        <div class="container">
                        <center>
                            <input type="hidden" name="userID" <?php echo "value='$userID'"?>/>
                            <input type="hidden" name="startTimestamp" <?php echo "value='$selectedStartTimeSeconds'"?>/>
                            <input type="hidden" name="endTimestamp" <?php echo "value='$selectedEndTimeSeconds'"?>/>
                            <button type="button" name="intent_modal_button" value="intent_modal_button" class="btn btn-success" data-toggle="modal" data-target="#intent_modal">Mark Intentions</button>
<!--                            <button type="button" name="mark_querysegment_button" value="mark_querysegment_button" class="btn btn-success">Mark Query Segment</button>-->
                        </center>
                        <center><h3 id="mark_querysegment_confirmation" class="bg-success"></h3></center>
                        </div>
                    </form>


                </div>
            </div>


        </div>

    <!-- Button trigger modal -->
<!--    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#intent_modal">-->
<!--        Launch demo modal-->
<!--    </button>-->

    <!-- Modal -->
    <div class="modal fade" id="intent_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Intentions</h4>
                </div>





                <form id="intentions_form" action="../services/utils/runPageQueryUtils.php?action=markQuerySegmentsAndIntentions">
                <div class="modal-body" id="select_intentions_panel">
                    <?php
                    echo $intentionsPanel['intentionshtml'];
                    ?>
                </div>
                <div class="modal-footer">
                    <center>
                        <input type="hidden" name="userID" <?php echo "value='$userID'";?>/>
                        <input type="hidden" name="startTimestamp" <?php echo "value='$selectedStartTimeSeconds'";?>/>
                        <input type="hidden" name="endTimestamp" <?php echo "value='$selectedEndTimeSeconds'";?>/>
                        <button type="button" name='mark_intentions_button' value='mark_intentions_button' class="btn btn-primary">Mark Query Segment + Intentions</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

                    </center>
                </div>
                </form>
            </div>
        </div>
    </div>


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