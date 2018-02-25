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
            Annotate Search Segments
        </title>

        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../study_styles/font-awesome-4.7.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/css/bootstrap-slider.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/bootstrap-slider.min.js"></script>
        <script src="../lib/bootstrap_notify/bootstrap-notify.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

        <style>

            body{
                background: #DFE2DB !important;
            }

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
                z-index: 999999 !important;
            }
            /*.alert{*/
                /*!*position:fixed;*!*/
                /*!*top:0;*!*/
                /*align:center;*/
                /*!*width:100%;*!*/
                /*display:none;*/
                /*!*margin: 0 auto;*!*/
            /*}*/
        </style>

        <script>
            var querysegment_form_id= '#querysegment_form';
            var intents_form_id= '#intentions_form';
//            var begin_end_indices = [];
//            var session_labels = [];
            var begin_index = -1;
            var end_index = -1;
            var table_index = -1;
            var row_index = -1;
            var query_segment_id = -1;
            var session_form_id = '';
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


            var mark_querysegments_function = function(ev){
                var row_numbers = [];

                var pageIDs = [];
                var queryIDs = [];
                $("tr.active input[type='checkbox'][name='pages[]']").each(function() {
                    pageIDs.push($(this).val());
                });

                $("tr.active input[type='checkbox'][name='queries[]']").each(function() {
                    queryIDs.push($(this).val());
                });


                pageIDs = $.unique(pageIDs);
                queryIDs = $.unique(queryIDs);



                var formData = $(querysegment_form_id).serialize()+"&"+$.param({pages:pageIDs,queries:queryIDs});
                $.ajax({
                    type: 'POST',
                    url: '../services/utils/runPageQueryUtils.php?action=markQuerySegment',
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
//                            $('#mark_querysegment_confirmation').html(response.message);
//                            $('#mark_querysegment_confirmation').removeClass('alert-success');
//                            $('#mark_querysegment_confirmation').addClass('alert-danger');
//                            $('#mark_querysegment_confirmation').show();
//                            $('#mark_querysegment_confirmation').fadeOut(3000);
                    }else{
                        $.notify({
                            // options
                            message: "Search segment marked!"
                        },{
                            // settings
                            type: 'success'
                        });
//                            $('#mark_querysegment_confirmation').removeClass('alert-danger');
//                            $('#mark_querysegment_confirmation').addClass('alert-success');
//                            $('#mark_querysegment_confirmation').html("Query segment and intentions marked!");
//                            $('#mark_querysegment_confirmation').show();
//                            $('#mark_querysegment_confirmation').fadeOut(3000);

                        $('#intent_modal').modal("hide");
                        $("button[name='mark_querysegments_button']").hide();
                        $('#querysegment_panel').html(response.querysegmenthtml);
                        $('#select_intentions_panel').html(response.intentionshtml);
                        $('#progressbar_segments_container').html(response.progressbar_segments_html);
                        $('#progressbar_intents_container').html(response.progressbar_intents_html);
//                        $('#select_intentions_footer').html(response.intentionsfooterhtml);
                        $("button[name='mark_querysegments_button']").unbind("click").click(mark_querysegments_function);
                        $("button[name='mark_intentions_button']").unbind("click").click(mark_intentions_button_function);
                        $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                        $(querysegment_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
                        $(querysegment_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);
                        $('input[name="intentions[]"]').unbind("click").click(toggle_radio_function);
                        $('.fa-info-circle').mouseover(mouseover_tooltip_function);
                        $('.fa-info-circle').unbind("click").click(click_tooltip_function);
                        $('.fa-info-circle').mouseout(mouseout_tooltip_function);
                        $('input:radio').unbind("click").click(toggle_text_function);
                        $("button[name='intent_modal_button']").fadeOut("slow");
                        begin_index = -1;
                        end_index = -1;
                    }




//                        mySlider = slider_init_function();
//                        mySlider.on("slideStop",slider_slidestop_function);
                }).fail(function(data) {
                    alert("Communication to the server was temporarily lost. Intentions were not marked. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                });



//                $("tr.active").find().each(function(){
//                    row_numbers.push()
//                })
            }


//            $('abbr').mouseover();

            /**
             * when abbreviations are clicked trigger their mouseover event then fade the tooltip
             * (this is friendly to touch interfaces)
             */
//            $('abbr').unbind("click").click();

            /**
             * Remove the tooltip on abbreviation mouseout
             */
//            $('abbr').mouseout();

            var denote_beginend_function = function(ev){

                var hide_same = false;
//                $($this)
                if($(this).attr("name")=='begin_button'){

                    if($(this).hasClass('active')){
                        $("button[name='begin_button']").removeClass('active');
                        $("button[name='begin_button']").show();
                        $(this).html("Begin");
                        begin_index = -1;

                    }else{
                        $("button[name='begin_button']").removeClass('active');
                        $("button[name='begin_button']").hide();
                        $(this).html("Undo Begin");
                        $(this).addClass('active');
                        $(this).show();
                        begin_index = $(this).data('total-row-index');
                    }
//                    if(end_index != -1){
//                        $(this).addClass('active');
//                    }else{
//                        $(this).toggleClass('active');
//                    }



                }else if($(this).attr("name")=='end_button'){



                    if($(this).hasClass('active')){
                        $("button[name='end_button']").removeClass('active');
                        $("button[name='end_button']").show();
                        $(this).html("End");
                        end_index = -1;
                    }else{
                        $("button[name='end_button']").removeClass('active');
                        $("button[name='end_button']").hide();
                        $(this).html("Undo End");
                        $(this).addClass('active');
                        $(this).show();
                        end_index = $(this).data('total-row-index');

                    }
//                    if(begin_index != -1){
//                        $(this).addClass('active');
//                    }else{
//                        $(this).toggleClass('active');
//                    }

                }

                $(session_form_id+" tr").filter(function() {
                    return ($(this).data('marked'));
                }).addClass('success');

                if(begin_index != -1 && end_index != -1){
                    $(session_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('total-row-index') >= begin_index && $(this).data('total-row-index') <= end_index);
                    }).prop( "checked", true );

                    $(session_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('total-row-index') < begin_index || $(this).data('total-row-index') > end_index);
                    }).prop( "checked", false );


                    $("tr").filter(function() {
                        return ($(this).data('total-row-index') >= begin_index && $(this).data('total-row-index') <= end_index);
                    }).removeClass('success').addClass( "active");

                    $("tr").filter(function() {
                        return ($(this).data('total-row-index') < begin_index || $(this).data('total-row-index') > end_index);
                    }).removeClass( "active");

                    if(hide_same){
                        $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
                            return ($(this).data('total-row-index') == begin_index);
                        }).hide();

                        $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
                            return ($(this).data('total-row-index') == end_index);
                        }).hide();
                    }


                    if(begin_index > end_index){
                        $.notify({
                            // options
                            message: "Begin should not come after end."
                        },{
                            // settings
                            type: 'danger'
                        });

                    }else{
                        $("button[name='mark_querysegments_button']").fadeIn("slow");
                    }


                }else{
                    $("button[name='mark_querysegments_button']").fadeOut("slow");

                    if(begin_index != -1){
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('total-row-index') == begin_index);
                        }).prop( "checked", true );



                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('total-row-index') != begin_index);
                        }).prop( "checked", false );

                        $("tr").filter(function() {
                            return ($(this).data('total-row-index') == begin_index);

                        }).removeClass('success').addClass('active');

                        $("tr").filter(function() {
                            return ($(this).data('total-row-index') != begin_index);
                        }).removeClass('active');


                        if(hide_same){
                            $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
                                return ($(this).data('total-row-index') == begin_index);
                            }).hide();

                        }




                    }else if(end_index != -1){
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('total-row-index') != end_index);
                        }).prop( "checked", true );

                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('total-row-index') != end_index);
                        }).prop( "checked", false );

                        $("tr").filter(function() {
                            return ($(this).data('total-row-index') == begin_index);

                        }).removeClass('success').addClass('active');

                        $("tr").filter(function() {
                            return ($(this).data('total-row-index') != end_index);
                        }).removeClass('active');


                        if(hide_same){
                            $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
                                return ($(this).data('total-row-index') == end_index);
                            }).hide();
                        }





                    }else{
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return true;
                        }).prop( "checked", false );



                        $("tr").filter(function() {
                            return true;
                        }).removeClass('active');



                        $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
                            return ($(this).data('total-row-index') != begin_index);
                        }).show();


                        $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
                            return ($(this).data('total-row-index') != end_index);
                        }).show();
                    }
                }
            }


            var begin_mark_intentions_function = function(ev){
                var rows = "";
                if($(this).attr("name")=='initiate_mark_intentions_button'){
                    query_segment_id = $(this).data('query-segment-id');
                }

                $(querysegment_form_id+" input[type='checkbox']").filter(function() {
                    return ($(this).data('query-segment-id') == query_segment_id );
                }).prop( "checked", true );


            }


//            var denote_beginend_function = function(ev){
//                if($(this).attr("name")=='begin_button'){
//                    $("button[name='begin_button']").removeClass('active');
//                    if($(this).hasClass('active')){
//                        $("button[name='begin_button']").removeClass('active');
////                        $("button[name='begin_button']").show();
//                    }else{
//                        $("button[name='begin_button']").removeClass('active');
////                        $("button[name='begin_button']").hide();
//                        $(this).addClass('active');
//                        $(this).show();
//
//                    }
//
//                    begin_index = $(this).data('table-index');
//                    query_segment_id = $(this).data('query-segment-id');
//                }else if($(this).attr("name")=='end_button'){
//                    $("button[name='end_button']").removeClass('active');
//
//                    if($(this).hasClass('active')){
//                        $("button[name='end_button']").removeClass('active');
//                        $("button[name='end_button']").show();
//                    }else{
//                        $("button[name='end_button']").removeClass('active');
//                        $("button[name='end_button']").hide();
//                        $(this).addClass('active');
//                        $(this).show();
//
//                    }
//
//                    end_index = $(this).data('table-index');
//
//                }
//
//                console.log(query_segment_id);
//                if(query_segment_id != -1){
//                    $("button[name='intent_modal_button']").fadeIn("slow");
//                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
//                        return ($(this).data('query-segment-id') == query_segment_id );
//                    }).prop( "checked", true );
//
//
//                    $(querysegment_form_id+" tr").removeClass('bg-success');
//                    $(querysegment_form_id+" tr[data-query-segment-id='"+query_segment_id+"']").addClass('bg-success');
//                    console.log($(querysegment_form_id+" tr"));
//                    console.log($(querysegment_form_id+" tr[data-query-segment-id='"+query_segment_id+"']"));
//
//                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
//                        return ($(this).data('query-segment-id') != query_segment_id );
//                    }).prop( "checked", false );
//
//                }else{
//                    $("button[name='intent_modal_button']").fadeOut("slow");
//                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
//                        return ($(this).data('query-segment-id') == query_segment_id );
//                    }).prop( "checked", true );
//
//                    $(querysegment_form_id+" tr").removeClass('bg-success');
//                    $(querysegment_form_id+" tr[data-query-segment-id='"+query_segment_id+"']").addClass('bg-success');
//
//                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
//                        return ($(this).data('query-segment-id') != query_segment_id );
//                    }).prop( "checked", false );
//                }
//
////                if(begin_index != -1 && end_index != -1){
////                    $("button[name='intent_modal_button']").fadeIn("slow");
////                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
////                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
////                    }).prop( "checked", true );
////
////                    $(querysegment_form_id+" input[type='checkbox']").filter(function() {
////                        return ($(this).data('table-index') < begin_index || $(this).data('table-index') > end_index);
////                    }).prop( "checked", false );
////
////                }else{
////                    $("button[name='intent_modal_button']").fadeOut("slow");
////                    if(begin_index != -1){
////                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
////                            return ($(this).data('table-index') == begin_index);
////                        }).prop( "checked", true );
////
////                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
////                            return ($(this).data('table-index') != begin_index);
////                        }).prop( "checked", false );
////
////                    }else if(end_index != -1){
////                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
////                            return ($(this).data('table-index') == end_index);
////                        }).prop( "checked", true );
////
////                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
////                            return ($(this).data('table-index') != end_index);
////                        }).prop( "checked", false );
////
////                    }else{
////                        $(querysegment_form_id+" input[type='checkbox']").filter(function() {
////                            return true;
////                        }).prop( "checked", false );
////
////                    }
////                }
//            }



            var toggle_radio_function = function(ev){
                var intent_key = $(this).data('intent-key');
                if($( this ).is( ":checked" )){
                    $("input[name='"+intent_key+"_success']").prop('disabled',false);
                }else{
                    $("input[name='"+intent_key+"_success']").prop('disabled',true);
                }

                if(intent_key=='other'){
                    if($( this ).is( ":checked" )){
                        $("textarea[name='"+intent_key+"_description']").prop('disabled',false);
                    }else{
                        $("textarea[name='"+intent_key+"_description']").prop('disabled',true);
                    }
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



                var formData = $(querysegment_form_id).serialize()+"&"+$(intents_form_id).serialize()+"&querySegmentID="+query_segment_id;

                if($(this).attr("value")=="mark_intentions_button"){
                    var intentions = ['id_start','id_more','learn_domain','learn_database','find_known','find_specific','find_common',"find_without",'keep_link','access_item','access_common','access_area','evaluate_correctness','evaluate_specificity','evaluate_usefulness','evaluate_best','evaluate_duplication','obtain_specific','obtain_part','obtain_whole','other'];
                    var input_valid = true;

                    var arrayLength = intentions.length;
                    for (var i = 0; i < arrayLength; i++) {
                        var intention = intentions[i];
                        if($("input[type='checkbox'][data-intent-key='"+intention+"']:checked").length > 0){

                            if(intention=='other' && $.trim($("textarea[name='other_description']").val()) == ''){
                                input_valid = false;
                                break;
                            }
                            if($("input[type='radio'][data-intent-key='"+intention+"']:checked").length == 0){
                                input_valid = false;
                                break;
                            }else{
                                if($("input[type='radio'][data-intent-key='"+intention+"']:checked").val() == 0){
                                    if($.trim($("textarea[name='"+intention+"_failure_reason']").val()) == ''){
                                        input_valid = false;
                                        break;
                                    }
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
//                            $('#mark_querysegment_confirmation').html(response.message);
//                            $('#mark_querysegment_confirmation').removeClass('alert-success');
//                            $('#mark_querysegment_confirmation').addClass('alert-danger');
//                            $('#mark_querysegment_confirmation').show();
//                            $('#mark_querysegment_confirmation').fadeOut(3000);
                            }else{
                                $.notify({
                                    // options
                                    message: "Search segment and intentions marked!"
                                },{
                                    // settings
                                    type: 'success'
                                });
//                            $('#mark_querysegment_confirmation').removeClass('alert-danger');
//                            $('#mark_querysegment_confirmation').addClass('alert-success');
//                            $('#mark_querysegment_confirmation').html("Query segment and intentions marked!");
//                            $('#mark_querysegment_confirmation').show();
//                            $('#mark_querysegment_confirmation').fadeOut(3000);

                                $('#intent_modal').modal("hide");
                                $('#querysegment_panel').html(response.querysegmenthtml);
                                $('#select_intentions_panel').html(response.intentionshtml);
                                $('#progressbar_segments_container').html(response.progressbar_segments_html);
                                $('#progressbar_intents_container').html(response.progressbar_intents_html);
//                        $('#select_intentions_footer').html(response.intentionsfooterhtml);
                                $("button[name='mark_querysegments_button']").unbind("click").click(mark_querysegments_function);
                                $("button[name='mark_intentions_button']").unbind("click").click(mark_intentions_button_function);
                                $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                                $(querysegment_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
                                $(querysegment_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);
                                $('input[name="intentions[]"]').unbind("click").click(toggle_radio_function);
                                $('.fa-info-circle').mouseover(mouseover_tooltip_function);
                                $('.fa-info-circle').unbind("click").click(click_tooltip_function);
                                $('.fa-info-circle').mouseout(mouseout_tooltip_function);
                                $('input:radio').unbind("click").click(toggle_text_function);
                                $("button[name='intent_modal_button']").fadeOut("slow");
                                begin_index = -1;
                                end_index = -1;
                            }




//                        mySlider = slider_init_function();
//                        mySlider.on("slideStop",slider_slidestop_function);
                        }).fail(function(data) {
                            alert("Communication to the server was temporarily lost. Intentions were not marked. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                        });

                    }else{
                        $.notify({
                            // options
                            message: "Something is wrong with your intentions input.  Please check for the following:" +
                            "<ol>" +
                            "<li>You did not specify whether an intention was satisfied.</li>" +
                            "<li>You did not explain why an intention was not satisfied.</li>" +
                            "<li>You specific 'Other' as an intention but did not describe the intention.</li>" +
                            "</ol>"
                        },{
                            // settings
                            type: 'danger',
                            delay: 20000
                        });
                    }


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
                        $('#progressbar_segments_container').html(response.progressbar_segments_html);
                        $('#progressbar_intents_container').html(response.progressbar_intents_html);
                        $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                        $(querysegment_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
                        $(querysegment_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);
                        $('input[name="intentions[]"]').unbind("click").click(toggle_radio_function);
                        $('.fa-info-circle').mouseover(mouseover_tooltip_function);
                        $('.fa-info-circle').unbind("click").click(click_tooltip_function);
                        $('.fa-info-circle').mouseout(mouseout_tooltip_function);
                        $('input:radio').unbind("click").click(toggle_text_function);
                        begin_index = -1;
                        end_index = -1;
                        $.notify({
                            // options
                            message: "Search segment marked!"
                        },{
                            // settings
                            type: 'success'
                        });
//                        $('#mark_querysegment_confirmation').html("Query segment marked!");
//                        $('#mark_querysegment_confirmation').show();
//                        $('#mark_querysegment_confirmation').fadeOut(3000);
//                        mySlider = slider_init_function();
//                        mySlider.on("slideStop",slider_slidestop_function);
                    }).fail(function(data) {
                        alert("Communication to the server was temporarily lost. The search segment was not marked. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                    });
                }
            };

            function show_intent_modal(querySegmentID){
                var rows_html = "";

                $('tr[data-query-segment-id='+querySegmentID+']').filter(function(){
                    return true;
                }).each(function(){
                    var row_html = $(this).html();
                    row_html = row_html.substring(row_html.indexOf("</td>")+5);
                    row_html = row_html.substring(row_html.indexOf("</td>")+5);

                    rows_html += "<tr>"+row_html+"</tr>";
                });




                $('#intent_modal_table').html(rows_html);




            }
            $(document).ready(function(){
//
//                    session_labels = [];
//                    var f = $("tr").filter(function() {
//                        $(this).data('session-label');
//                        session_labels += [$(this).data('session-label')]
//                        return true;
//                    }).data("session-label");
//                    session_labels = [...new Set(session_labels)];
//                    var mySlider = slider_init_function();
//                    mySlider.on("slideStop",slider_slidestop_function);


//                    var f = function(index,value){
//
////                        += [[index,value,-1,-1]]
//                        begin_end_indices[value] = [-1,-1]
//
//                    }
//                    $.each(session_labels,f);

                    $("button[name='mark_intentions_button']").unbind("click").click(mark_intentions_button_function);
                    $("button[name='mark_querysegments_button']").unbind("click").click(mark_querysegments_function);

//                    $(querysegment_form_id+" button[name='mark_querysegment_button']").unbind("click").click(mark_querysegment_button_function);
                    $(querysegment_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
                    $(querysegment_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);
                    $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                    $('input:radio').unbind("click").click(toggle_text_function);
                    $('input[name="intentions[]"]').unbind("click").click(toggle_radio_function);
                    $('.fa-info-circle').mouseover(mouseover_tooltip_function);
                    $('.fa-info-circle').unbind("click").click(click_tooltip_function);
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

<!--    <center><h3 id="mark_querysegment_confirmation" class="alert alert-success"></h3></center>-->
<!--    <body style="background-color:gainsboro">-->
    <div class="container-fluid">
        <!--   Dates Tab and Review     -->


        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="">
                        <h1>Annotate Query Segments and Intentions
                        </h1>

                        <div id="progressbar_segments_container">
                            <?php
                            echo $querySegmentTables['progressbar_segments_html'];
                            ?>
                        </div>
                        <div id="progressbar_intents_container">
                            <?php
                            echo $querySegmentTables['progressbar_intents_html'];
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
                                    $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/markSearchSegments.php', $selectedStartTimeSeconds);
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
                        <center><h4>Annotate Your Day's Search Segments</h4></center>

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

                                    <!--                                <button type="button" name="intent_modal_button" value="intent_modal_button" class="btn btn-lg btn-success" data-toggle="modal" data-target="#intent_modal" style="position: fixed; bottom: 20px; right: 20px; display:none; z-index:100;">Mark Query Segments</button>-->
                                </div>
                                <!--                            <button type="button" name="intent_modal_button" value="intent_modal_button" class="btn btn-success" data-toggle="modal" data-target="#intent_modal">Mark Intentions</button>-->
                                <!--                            <button type="button" name="mark_querysegment_button" value="mark_querysegment_button" class="btn btn-success">Mark Query Segment</button>-->
                            </center>

                        </div>

                    </form>
                </div>



                </div>
            </div>


        <div class="row">
<!---->
<!--            <div class="col-md-12">-->
<!--                <div class="panel panel-primary">-->
<!--                    <div class="panel-heading">-->
<!--                        <center>-->
<!--                            --><?php
//                            $actionUrls = actionUrls($selectedStartTimeSeconds);
//                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['tasks']."'>&laquo; Back (Assign Tasks to Sessions)</a>";
//                            //                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
//                            //                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['intentions']."'>Next (Intentions) &raquo;</a>";
//                            ?>
<!--                        </center>-->
<!--                    </div>-->
<!---->
<!--                </div>-->
<!--            </div>-->



        </div>

        </div>




    <!-- Button trigger modal -->
<!--    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#intent_modal">-->
<!--        Launch demo modal-->
<!--    </button>-->

    <!-- Modal -->
<!--    <div class="modal fade" id="intent_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="left:50%">-->
    <div class="modal fade" id="intent_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="width=100%">

        <div class="modal-dialog" role="document" style="width:70%">
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">What were your intentions for this search segment? Were they successful?</h4>
                </div>






                <form id="intentions_form" action="../services/utils/runPageQueryUtils.php?action=markQuerySegmentsAndIntentions">
                <div class="modal-body" >


                    <div class="row">
                        <div class="well">
                            <div><p><h4>What were you trying to accomplish (what was your intention) during this part of the search? Please choose one or more of the "search intentions" on the right; if none fits your goal at this point in the search, please choose "Other", and give a brief explanation.</h4></p></div>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-bordered table-fixed">
                                <thead>
                                <tr>
                                    <th >Time</th>
                                    <th >Type</th>


                                    <th >Search Segment</th>
                                    <th >Title/Query</th>
                                    <th >Domain</th>




                                </tr>
                                </thead>
                                <tbody id="intent_modal_table">
                                </tbody>
                            </table>

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12" id="select_intentions_panel">
                            <?php
                            echo $intentionsPanel['intentionshtml'];
                            ?>
                        </div>
                    </div>




                </div>
            </div>
        </div>


    </div>

    <?php
    printTutorialModal('intention');
    ?>







<!--<div style="position: fixed; bottom: 0px; right:20px; z-index: 90;">-->
<!--    <center>-->
<!---->
<!--        <div class="panel panel-primary">-->
<!--            <div class="panel-heading">-->
<!--                <center>-->
<div class="btn-group" style="position: fixed; bottom: 20px; right:20px; z-index: 90;">

                    <?php
                    $actionUrls = actionUrls($selectedStartTimeSeconds);
                    echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['tasks']."'><i class=\"fa fa-arrow-circle-left\" aria-hidden=\"true\"></i> Back (Assign Tasks to Sessions)</a>";
                    //                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                    //                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['intentions']."'>Next (Intentions) &raquo;</a>";
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