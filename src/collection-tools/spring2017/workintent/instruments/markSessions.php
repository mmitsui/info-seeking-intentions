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
                        $('#session_panel').html(response.sessionhtml);
                        $(session_form_id+" button[name='begin_button']").click(denote_beginend_function);
                        $(session_form_id+" button[name='end_button']").click(denote_beginend_function);
                        begin_index = -1;
                        end_index = -1;
                        $('#mark_session_confirmation').html("Session marked!");
                        $('#mark_session_confirmation').show();
                        $('#mark_session_confirmation').fadeOut(2000);
//                                mySlider = slider_init_function();
//                                mySlider.on("slideStop",slider_slidestop_function);
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
                        <center><button type="button" class="btn btn-primary">Review Tutorial</button></center>
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
                        <div class="container">
                        <center>
                            <input type="hidden" name="userID" <?php echo "value='$userID'"?>/>
                            <input type="hidden" name="startTimestamp" <?php echo "value='$selectedStartTimeSeconds'"?>/>
                            <input type="hidden" name="endTimestamp" <?php echo "value='$selectedEndTimeSeconds'"?>/>
                            <button type="button" name="mark_session_button" value="mark_session_button" class="btn btn-success">Mark Session</button>
                        </center>
                        <center><h3 id="mark_session_confirmation" class="bg-success"></h3></center>
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
                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['tasks']."'>Next (Tasks) &raquo;</a>";
                            ?>
                        </center>
                    </div>

                </div>
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