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
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

        <style>
            .tab-pane{
                height:300px;
                overflow-y:scroll;
                width:90%;
            }



            .tooltip
            {
                position:absolute;
                background-color:#eeeefe;
                border: 1px solid #aaaaca;
                font-size: smaller;
                padding:4px;
                width: 160px;
                box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
                -moz-box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
                -webkit-box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
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
                /*position:fixed;*/
                /*top:0;*/
                align:center;
                /*width:100%;*/
                display:none;
                /*margin: 0 auto;*/
            }
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


            var mouseover_tooltip_function = function() {

                // first remove all existing abbreviation tooltips
                $('abbr').next('.tooltip').remove();

                // create the tooltip
                $(this).after('<span class="tooltip">' + $(this).data('title') + '</span>');

                // position the tooltip 4 pixels above and 4 pixels to the left of the abbreviation
                var left = $(this).position().left + $(this).width() + 4;
                var top = $(this).position().top - 4;
                $(this).next().css('left',left);
                $(this).next().css('top',top);

            }

            var click_tooltip_function = function(){

                $(this).mouseover();

                // after a slight 2 second fade, fade out the tooltip for 1 second
                $(this).next().animate({opacity: 0.9},{duration: 200, complete: function(){
//                    $(this).fadeOut(1000);
                }});

            }

            var mouseout_tooltip_function = function(){

                $(this).next('.tooltip').remove();

            }


//            $('abbr').mouseover();

            /**
             * when abbreviations are clicked trigger their mouseover event then fade the tooltip
             * (this is friendly to touch interfaces)
             */
//            $('abbr').click();

            /**
             * Remove the tooltip on abbreviation mouseout
             */
//            $('abbr').mouseout();

            var denote_beginend_function = function(ev){
                if($(this).attr("name")=='begin_button'){
                    $("button[name='begin_button']").removeClass('active');
                    if($(this).hasClass('active')){
                        $("button[name='begin_button']").removeClass('active');
                        $("button[name='begin_button']").show();
                    }else{
                        $("button[name='begin_button']").removeClass('active');
                        $("button[name='begin_button']").hide();
                        $(this).addClass('active');
                        $(this).show();

                    }

                    begin_index = $(this).data('table-index');
                }else if($(this).attr("name")=='end_button'){
                    $("button[name='end_button']").removeClass('active');

                    if($(this).hasClass('active')){
                        $("button[name='end_button']").removeClass('active');
                        $("button[name='end_button']").show();
                    }else{
                        $("button[name='end_button']").removeClass('active');
                        $("button[name='end_button']").hide();
                        $(this).addClass('active');
                        $(this).show();

                    }

                    end_index = $(this).data('table-index');
                }

                if(begin_index != -1 && end_index != -1){
                    $("button[name='intent_modal_button']").fadeIn("slow");
                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
                    }).prop( "checked", true );

                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('table-index') < begin_index || $(this).data('table-index') > end_index);
                    }).prop( "checked", false );

                }else{
                    $("button[name='intent_modal_button']").fadeOut("slow");
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

            var toggle_radio_function = function(ev){
                var intent_key = $(this).data('intent-key');
                if($( this ).is( ":checked" )){
                    $("input[name='"+intent_key+"_success']").prop('disabled',false);
                }else{
                    $("input[name='"+intent_key+"_success']").prop('disabled',true);
                }
            }

            var toggle_text_function = function(ev){
                var intent_key = $(this).data('intent-key');
                if($(this).attr('value')=='1'){
                    $("textarea[name='"+intent_key+"_failure_reason']").prop('disabled',true);
                }else{
                    $("textarea[name='"+intent_key+"_failure_reason']").prop('disabled',false);
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
                        $('#intent_modal').modal("hide");
                        $('#querysegment_panel').html(response.querysegmenthtml);
                        $('#select_intentions_panel').html(response.intentionshtml);
//                        alert(response.intentionshtml);
//                        $('#select_intentions_footer').html(response.intentionsfooterhtml);
//                        alert(response.intentionsfooterhtml);
                        $("button[name='mark_intentions_button']").click(mark_intentions_button_function);
                        $(querysegment_form_id+" button[name='begin_button']").click(denote_beginend_function);
                        $(querysegment_form_id+" button[name='end_button']").click(denote_beginend_function);
                        $('input[name="intentions[]"]').click(toggle_radio_function);
                        $('.fa-info-circle').mouseover(mouseover_tooltip_function);
                        $('.fa-info-circle').click(click_tooltip_function);
                        $('.fa-info-circle').mouseout(mouseout_tooltip_function);
                        $('input:radio').click(toggle_text_function);
                        $("button[name='intent_modal_button']").fadeOut("slow");
                        begin_index = -1;
                        end_index = -1;
                        $('#mark_querysegment_confirmation').html("Query segment and intentions marked!");
                        $('#mark_querysegment_confirmation').show();
                        $('#mark_querysegment_confirmation').fadeOut(3000);


//                        mySlider = slider_init_function();
//                        mySlider.on("slideStop",slider_slidestop_function);
                    }).fail(function(data) {
                        alert("There was a server error.  Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
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
                        $('input[name="intentions[]"]').click(toggle_radio_function);
                        $('.fa-info-circle').mouseover(mouseover_tooltip_function);
                        $('.fa-info-circle').click(click_tooltip_function);
                        $('.fa-info-circle').mouseout(mouseout_tooltip_function);
                        $('input:radio').click(toggle_text_function);
                        begin_index = -1;
                        end_index = -1;
                        $('#mark_querysegment_confirmation').html("Query segment marked!");
                        $('#mark_querysegment_confirmation').show();
                        $('#mark_querysegment_confirmation').fadeOut(3000);
//                        mySlider = slider_init_function();
//                        mySlider.on("slideStop",slider_slidestop_function);
                    }).fail(function(data) {
                        alert("There was a server error.  Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
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
                    $('input:radio').click(toggle_text_function);
                    $('input[name="intentions[]"]').click(toggle_radio_function);
                    $('.fa-info-circle').mouseover(mouseover_tooltip_function);
                    $('.fa-info-circle').click(click_tooltip_function);
                    $('.fa-info-circle').mouseout(mouseout_tooltip_function);

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





    <body>

    <center><h3 id="mark_querysegment_confirmation" class="alert alert-success"></h3></center>
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
                            <div>
                                <button type="button" name="intent_modal_button" value="intent_modal_button" class="btn btn-lg btn-success" data-toggle="modal" data-target="#intent_modal" style="position: fixed; bottom: 20px; right: 20px; display:none; z-index:100;">Mark Intentions</button>
                            </div>
<!--                            <button type="button" name="intent_modal_button" value="intent_modal_button" class="btn btn-success" data-toggle="modal" data-target="#intent_modal">Mark Intentions</button>-->
<!--                            <button type="button" name="mark_querysegment_button" value="mark_querysegment_button" class="btn btn-success">Mark Query Segment</button>-->
                        </center>

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
                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['tasks']."'>&laquo; Back (Assign Tasks to Sessions)</a>";
                            //                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                            //                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['intentions']."'>Next (Intentions) &raquo;</a>";
                            ?>
                        </center>
                    </div>

                </div>
            </div>



        </div>

        </div>




    <!-- Button trigger modal -->
<!--    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#intent_modal">-->
<!--        Launch demo modal-->
<!--    </button>-->

    <!-- Modal -->
    <div class="modal fade" id="intent_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="left:50%">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">What were your intentions for this query segment? Were they successful?</h4>
                </div>





                <form id="intentions_form" action="../services/utils/runPageQueryUtils.php?action=markQuerySegmentsAndIntentions">
                <div class="modal-body" id="select_intentions_panel">
                    <?php
                    echo $intentionsPanel['intentionshtml'];
                    ?>


                </div>
            </div>
        </div>


    </div>

    <?php
//    printTutorialModal();
    ?>

    <div class="modal fade" id="tutorial_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Tutorial</h4>
                </div>





                <div class="modal-body" id="intent_tutorial_body">


                    <p>You will be asked to indicate what you were attempting to accomplish (your search intention) at various points during that session. This will proceed as follows.</p>

                    <ul><strong><u>Search Segments</u></strong>
                        <li>You will be asked to review your day’s search segments.</li>
                        <li>A search segment is everything that happens from your issuing a query, to your issuing the next query, or completing your search session.</li>
                        <li>For each search segment, you will be shown a form on the right of the screen, which lists possible search intentions (including "other")</li>
                    </ul>

                    <ul><strong><u>What You Are Asked To Do</u></strong>
                        <li>When a search segment is finished, you will select, on the popup form, your underlying intention (what you were attempting to accomplish) for that search segment.  You will also indicate whether that intention was satisfied, and if not, you will explain why not.</li>
                        <li>It is possible that what you intended to do during that search segment is not listed in the form. In this case, you should select "Other", and then type a brief description of your intention in the box provided.</li>
                    </ul>

                    During each search segment, you may have had more than one intention (you may have wanted to accomplish more than one goal). In this case, you should check all, but only, the intentions

                    <ul><strong><u>Identify search information</u></strong>
                        <li><strong><u>Identify something to get started</u></strong> - For instance, find good query terms.</li>
                        <li><strong><u>Identify something more to search</u></strong> – Explore a topic more broadly.</li>
                    </ul>

                    <ul><strong><u>Learning</u></strong>
                        <li><strong><u>Learn domain knowledge</u></strong> - Learn about the topic of a search.</li>
                        <li><strong><u>Learn database content</u></strong> – Learn the type of information/resources available at a particular website – e.g., a government database.</li>
                    </ul>

                    <ul><strong><u>Finding</u></strong>
                        <li><strong><u>Find a known item</u></strong> – Searching for an item that you were familiar with in advance.</li>
                        <li><strong><u>Find specific information</u></strong> – Finding a predetermined piece of information.</li>
                        <li><strong><u>Find items sharing a named characteristic</u></strong> – Finding items with something in common.</li>
                        <li><strong><u>Find items without predefined criteria</u></strong> – Finding items that will be useful for a task, but which haven't been specified in advance.</li>
                    </ul>


                    <ul><strong><u>Keep record</u></strong>
                        <li><strong><u>Keep record of a link</u></strong> - Saving a good item or an item to look at later</li>
                    </ul>

                    <ul><strong><u>Access an item or set of items</u></strong>
                        <li><strong><u>Access a specific item</u></strong> – Go to some item that you already know about.</li>
                        <li><strong><u>Access items with common characteristics</u></strong> – Go to some set of items with common characteristics.</li>
                        <li><strong><u>Access a web site/home page or similar</u></strong> – Relocating or going to a website.</li>
                    </ul>


                    <ul><strong><u>Evaluate</u></strong>
                        <li><strong><u>Evaluate correctness of an item</u></strong> - Determine whether an item is factually correct.</li>
                        <li><strong><u>Evaluate usefulness of an item</u></strong> - Determine whether an item is useful.</li>
                        <li><strong><u>Pick best item(s) from all the useful ones</u></strong> - Determine the best item among a set of items.</li>
                        <li><strong><u>Evaluate specificity of an item</u></strong> – Determine whether an item is specific or general enough.</li>
                        <li><strong><u>Evaluate duplication of an item</u></strong> – Determine whether the information in one item is the same as in another or others.</li>
                    </ul>


                    <ul><strong><u>Obtain</u></strong>
                        <li><strong><u>Obtain specific information</u></strong> – Finding specific information to bookmark, highlight, or copy.</li>
                        <li><strong><u>Obtain part of the item</u></strong> – Finding part of an item to bookmark, highlight, or copy.</li>
                        <li><strong><u>Obtain a whole item(s)</u></strong> - Finding a whole item to bookmark, highlight, or copy.</li>
                    </ul>

                </div>


                <div class="modal-footer" id="intent_tutorial_footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Exit</button>

                </div>
            </div>
        </div>


    </div>








    </body>
    </html>
<?php
?>