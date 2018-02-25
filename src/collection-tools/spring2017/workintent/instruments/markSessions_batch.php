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

$startEndTimestampList = getStartEndTimestampsList($userID,strtotime('today midnight'),20);

$taskIDNameMap = getTaskIDNameMap($userID);
$sessionTables = getSessionTables($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);

?>


    <html>
    <head>
        <title>
            Identify Sessions
        </title>

        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../study_styles/font-awesome-4.7.0/css/font-awesome.min.css">

        <!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/css/bootstrap-slider.min.css">-->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="../lib/bootstrap_notify/bootstrap-notify.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/bootstrap-slider.min.js"></script>-->

        <style>

            div#pop-up {
                display: none;
                position: absolute;
            }


            body{
                background: #DFE2DB !important;
            }
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
            /*.alert{*/
                /*position:fixed;*/
                /*top:0;*/
                /*align:center;*/
                /*width:100%;*/
                /*display:none;*/
                /*margin: 0 auto;*/
            /*}*/
        </style>

        <script>
            var session_form_id= '#session_form';

            var begin_index = -1;
            var end_index = -1;


//            var max_session_label = 0;
//            var n_elements = 0;
            var history = {};
            var history_begin = {};
            var history_end = {};
            var min_row_index = 0;
            var max_row_index = 0;
//            var sessionLabels = [];
            var sortedRowIndices = [];
            var max_new_labels = 0;
            var unfinishedLabels = [];

            var reset_data = function(){
//                max_session_label =  parseInt($('#max_session_label').val());
//                n_elements = 0;
                history = {};
                history_begin = {};
                history_end = {};
                min_row_index = 0;
                max_row_index = 0;
//                sessionLabels = [];
                sortedRowIndices = [];
                max_new_labels = 0;
                unfinishedLabels = [];
            }

            var edit_history_begin = function(action,index,sessionLabel){
                if(action=='add'){
                    history_begin[index] = {'type':'begin','sessionLabel':parseInt(sessionLabel)};
                }else{
                    delete history_begin[index];
                }
            }

            var edit_history_end = function(action,index,sessionLabel){
                if(action=='add'){
                    history_end[index] = {'type':'end','sessionLabel':parseInt(sessionLabel)};
                }else{
                    delete history_end[index];
                }
            }


            var clear_selection_function = function(){
                $("tr").removeClass('active');
                cached_session_markers = [];
                cached_session_numbers = [];

                $("button[name='end_button']").removeClass('active');
                $("button[name='begin_button']").removeClass('active');
                $("button[name='end_button']").show();
                $("button[name='begin_button']").show();
                $("button[name='end_button']").html('End');
                $("button[name='begin_button']").html('Begin');

                $(session_form_id+" tr").filter(function() {
                    return ($(this).data('marked'));
                }).addClass('success');

                $('span[name="filler"]').html("");

                $('div#pop-up').hide();
                reset_data();
            }



            var populate_session_popup = function(row_index,begin_or_end,begin_end_func) {
                $('#session_list').html("");

                if(begin_or_end=='begin'){
                    $('#session_list').append("<p>This is the beginning of which session?</p>");
                }else{
                    $('#session_list').append("<p>This is the end of which session?</p>");
                }

                $('#session_list').append("<ul id='newList'></ul>");

                $.each(unfinishedLabels, function(index, label) {
                    $("#newList").append("<div class='radio'><label><input type='radio' name='whichsession' value='"+label+"'/> Session "+label+"</label></div>");
                });

                if(begin_or_end=='begin'){
                    $("#newList").append("<div class='radio'><label><input type='radio' name='whichsession' value='0'/> New Session</label></div>");
                }

                $('#session_list').append("<input type='hidden' id='row_index' value='"+row_index+"'/>");
                $('#label_session_button').unbind("click").click(begin_end_func);
            }



            var popup_show = function(ev,button,row_index,begin_or_end,begin_end_func){

                populate_session_popup(row_index,begin_or_end,begin_end_func);
                var position = $(button).offset();
                $('div#pop-up').show()
                    .css('top', position.top)
                    .css('left', position.left+$(button).width()+30)
                    .css('position','absolute')
                    .appendTo('body');

                $('div#pop-up').mousedown(function() {
                    $(this).css('cursor','move');
                });

            }

            var popup_clear = function(ev){
                $('div#pop-up').hide();
            }




//
            var click_new_session_begin = function(){
                var row_index = parseInt($('#row_index').val());
                var sessionLabel = -1;
                if(typeof $('input[name="whichsession"]:checked').val() !== typeof undefined ){
                    sessionLabel = $('input[name="whichsession"]:checked').val()
                }else{
                    $.notify({
                        // options
                        message: "Please make a selection."
                    },{
                        // settings
                        type: 'danger'
                    });
                    return;
                }

                if(sessionLabel==0){
                    edit_history_begin('add',row_index,max_new_labels+1);
                }else{
                    edit_history_begin('add',row_index,sessionLabel);
                }


                $.notify({
                    // options
                    message: "New beginning marked!."
                },{
                    // settings
                    type: 'success'
                });


                update_session_labels();
                popup_clear();
            }

            var click_new_session_end = function(){

                var row_index = parseInt($('#row_index').val());
                var sessionLabel = -1
                if(typeof $('input[name="whichsession"]:checked').val() !== typeof undefined ){
                    sessionLabel = $('input[name="whichsession"]:checked').val()
                }else{
                    $.notify({
                        // options
                        message: "Please make a selection."
                    },{
                        // settings
                        type: 'danger'
                    });
                    return;
                }

                if(sessionLabel==0){
                    edit_history_end('add',row_index,max_new_labels+1);
                }else{
                    edit_history_end('add',row_index,sessionLabel);
                }


                $.notify({
                    // options
                    message: "New ending marked!."
                },{
                    // settings
                    type: 'success'
                });

                update_session_labels();
                popup_clear();


            }








            function sortNumber(a,b) {
                return a - b;
            }


            var update_session_labels = function(){
                sortedRowIndices = [];
                $.each(history_begin, function(row_index, value) {
                    sortedRowIndices.push(parseInt(row_index));
                });
                $.each(history_end, function(row_index, value) {
                    sortedRowIndices.push(parseInt(row_index));
                });
                sortedRowIndices.sort(sortNumber);
                min_row_index = sortedRowIndices[0];
                max_row_index = sortedRowIndices[sortedRowIndices.length-1];


                //                Getting unfinished labels
                var begin_labels = [];
                var end_labels = [];
                var all_labels = [];
                $.each(history_begin, function(row_index, history_item) {
                    begin_labels.push(history_item['sessionLabel']);
                    all_labels.push(history_item['sessionLabel']);

                });
                $.each(history_end, function(row_index, history_item) {
                    end_labels.push(history_item['sessionLabel']);
                    all_labels.push(history_item['sessionLabel']);

                });
                unfinishedLabels = [];
                $.grep(begin_labels, function(el) {
                    if ($.inArray(el, end_labels) == -1 && $.inArray(el, unfinishedLabels) == -1){
                        unfinishedLabels.push(el);
                    }
                });


                max_new_labels = Math.max.apply(Math,all_labels);
                if(all_labels.length ==0){
                    max_new_labels = 1;

                }

//                Renaming rows
                $('span[name="filler"]').html("");

                $(session_form_id+" input[type='checkbox']").filter(function () {
                    return true;
                }).prop( "checked", false ).removeData('session-label');


//                alert(JSON.stringify(history_begin));
//                alert(JSON.stringify(history_end));
//                alert(max_row_index);
//                alert(max_new_labels);

                for(i=0; i < sortedRowIndices.length; i++){
                    var current_row_index = sortedRowIndices[i];
                    var sessionLabel = '';
                    if(current_row_index in history_end){
                        sessionLabel = history_end[current_row_index]['sessionLabel'];
                    }else{
                        sessionLabel = history_begin[current_row_index]['sessionLabel'];
                    }

//                    alert("ROW "+current_row_index);
//                    alert("Session Label"+sessionLabel);
                    $('span[name="filler"]').filter(function () {
                        return $(this).data('table-index') >= current_row_index;
                    }).html("Session "+sessionLabel);

                    $(session_form_id+" input[type='checkbox']").filter(function () {
                        return $(this).data('table-index') >= current_row_index;
                    }).prop( "checked", true).data('session-label',sessionLabel);
                }

                $('span[name="filler"]').filter(function () {
                    return $(this).data('table-index') > sortedRowIndices[sortedRowIndices.length-1];
                }).html("");


                $(session_form_id+" input[type='checkbox']").filter(function () {
                    return $(this).data('table-index') > sortedRowIndices[sortedRowIndices.length-1];
                }).prop( "checked", false ).removeData('session-label');







                //                    $(session_form_id+" input[type='checkbox']").filter(function() {
//                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
//                    }).prop( "checked", true );

                if(begin_labels.length+end_labels.length >=2){
                    $('#identify_session_group').show();
                }else{
                    $('#identify_session_group').hide();
                }

            }




            var denote_beginend_function = function(ev){
                var hide_same = false;
                var label = $(this).html();
                var curr_row_index = $(this).data('table-index');

                if($(this).attr("name")=='begin_button'){

                    if(curr_row_index < min_row_index){
                        if(label=="Begin"){
                            edit_history_begin('add',curr_row_index,max_new_labels+1);
                            $(this).addClass('active');
                            $(this).html("Undo Begin");
                            update_session_labels();
                        }
                        else if(label=="Undo Begin"){

                            if((history_begin.length+history_end.length)>=2 && (sortedRowIndices[1] in history_end) ){
                                $.notify({
                                    // options
                                    message: "Your annotations should not begin with 'End'."
                                },{
                                    // settings
                                    type: 'danger'
                                });
                                return;
                            }


                            edit_history_begin('remove',curr_row_index,-1);
                            $(this).removeClass('active');
                            $(this).html("Begin");
                            update_session_labels();
                        }

                    }
                    else if(curr_row_index >= max_row_index)
                    {
                        last_type = ''
                        if(max_row_index in history_end){
                            last_type = history_end[max_row_index]['type'];
                        }else if(max_row_index in history_begin){
                            last_type = history_begin[max_row_index]['type'];
                        }

                        if(label=="Begin"){
                            if(max_row_index - curr_row_index > 1 && last_type=='end'){
                                $.notify({
                                    // options
                                    message: "Begin should come immediately after End."
                                },{
                                    // settings
                                    type: 'danger'
                                });
                                return;
                            }

                            if(unfinishedLabels.length >=2){
                                popup_show(ev,$(this),curr_row_index,'begin',click_new_session_begin);
                            }else{
                                edit_history_begin('add',curr_row_index,max_new_labels+1);
                                $(this).addClass('active');
                                $(this).html("Undo Begin");
                                update_session_labels();
                            }


                        }

                        if(label=="Undo Begin"){
                            edit_history_begin('remove',curr_row_index,-1);
                            $(this).removeClass('active');
                            $(this).html("Begin");
                            update_session_labels();
                        }

                    }
                    else{

                        if(label=="Begin"){

                            popup_show(ev,$(this),curr_row_index,'begin',click_new_session_begin);
//                            if(unfinishedLabels.length >= 2){
//                                populate_session_popup();
//                            }else{
//                                history[curr_row_index] = {'type':'begin','sessionLabel':max_new_labels+1};
//                                max_new_labels += 1;
//                                $(this).addClass('active');
//                                $(this).html("Undo Begin");
//                                update_session_labels();
//                            }
                        }
                        else{
                            if(unfinishedLabels.length >= 2){
                                var prev_index = sortedRowIndices.indexOf(curr_row_index)-1;
                                if(prev_index >=0){
                                    if(curr_row_index in history_end){
                                        $.notify({
                                            // options
                                            message: "A Begin should come immediately after an End."
                                        },{
                                            // settings
                                            type: 'danger'
                                        });
                                        return;
                                    }
                                }
                            }

                            edit_history_begin('remove',curr_row_index,-1);
                            $(this).removeClass('active');
                            $(this).html("Begin");
                            update_session_labels();

                        }
                    }
                }else if($(this).attr("name")=='end_button'){


                    if(unfinishedLabels.length ==0){
                        $(this).removeClass('active');
                        $(this).html("End");
                        $.notify({
                            // options
                            message: "End should not come first."
                        },{
                            // settings
                            type: 'danger'
                        });
                        return;
                    }

                    if(curr_row_index < min_row_index){
                        $(this).removeClass('active');
                        $(this).html("End");
                        $.notify({
                            // options
                            message: "End should not come before anything else."
                        },{
                            // settings
                            type: 'danger'
                        });
                        return;
                    }

                    else if(curr_row_index >= max_row_index){

                        if(label=="Undo End"){
                            $(this).removeClass('active');
                            $(this).html("End");
                            edit_history_end('remove',curr_row_index,-1);
                            update_session_labels();
                        }else{
                            if(unfinishedLabels.length >=2){
                                popup_show(ev,$(this),curr_row_index,'end',click_new_session_end);
                            }else{
                                edit_history_end('add',curr_row_index,max_new_labels);
                                $(this).addClass('active');
                                $(this).html("Undo End");
                                update_session_labels();
                            }
                        }

                    }
                    else{
                        if(label=="End") {
                            popup_show(ev,$(this),curr_row_index,'end',click_new_session_end);
                        }
                        else{
                            edit_history_end('remove',curr_row_index,-1);
                            $(this).removeClass('active');
                            $(this).html("End");
                            update_session_labels();
                        }


                    }
                }


//                Post: 1) highlight all rows between max and min 2) update:

//                $(session_form_id+" tr").filter(function() {
//                    return ($(this).data('marked'));
//                }).addClass('success');
//
//                if(begin_index != -1 && end_index != -1){
//
//                    $(session_form_id+" input[type='checkbox']").filter(function() {
//                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
//                    }).prop( "checked", true );
//
//                    $(session_form_id+" input[type='checkbox']").filter(function() {
//                        return ($(this).data('table-index') < begin_index || $(this).data('table-index') > end_index);
//                    }).prop( "checked", false );
//
//
//                    $("tr").filter(function() {
//                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
//                    }).removeClass('success').addClass( "active");
//
//                    $("tr").filter(function() {
//                        return ($(this).data('table-index') < begin_index || $(this).data('table-index') > end_index);
//                    }).removeClass( "active");
//
//                    if(hide_same){
////                        $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
////                            return ($(this).data('table-index') == begin_index);
////                        }).hide();
////
////                        $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
////                            return ($(this).data('table-index') == end_index);
////                        }).hide();
//                    }
//
//
//
//                    if(begin_index > end_index){
//                        $.notify({
//                            // options
//                            message: "Begin should not come after end."
//                        },{
//                            // settings
//                            type: 'danger'
//                        });
//
//                    }else{
//
//                        popup_show(ev,this);
//
////                        $("div[name='session_button_group']").fadeIn("slow");
//
////                        $("button[name='mark_session_button']").fadeIn("slow");
//                    }
//
//
//                }else{
//                    popup_clear(ev);
//
////                    $("div[name='session_button_group']").fadeOut("slow");
////                    $("button[name='mark_session_button']").fadeOut("slow");
//
//                    if(begin_index != -1){
//                        $(session_form_id+" input[type='checkbox']").filter(function() {
//                            return ($(this).data('table-index') == begin_index);
//                        }).prop( "checked", true );
//
//
//
//                        $(session_form_id+" input[type='checkbox']").filter(function() {
//                            return ($(this).data('table-index') != begin_index);
//                        }).prop( "checked", false );
//
//                        $("tr").filter(function() {
//                            return ($(this).data('table-index') == begin_index);
//
//                        }).removeClass('success').addClass('active');
//
//                        $("tr").filter(function() {
//                            return ($(this).data('table-index') != begin_index);
//                        }).removeClass('active');
//
//
//
//                        if(hide_same){
////                            $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
////                                return ($(this).data('table-index') == begin_index);
////                            }).hide();
//                        }
//
//
//
//                    }else if(end_index != -1){
//                        $(session_form_id+" input[type='checkbox']").filter(function() {
//                            return ($(this).data('table-index') != end_index);
//                        }).prop( "checked", true );
//
//                        $(session_form_id+" input[type='checkbox']").filter(function() {
//                            return ($(this).data('table-index') != end_index);
//                        }).prop( "checked", false );
//
//                        $("tr").filter(function() {
//                            return ($(this).data('table-index') == begin_index);
//
//                        }).removeClass('success').addClass('active');
//
//                        $("tr").filter(function() {
//                            return ($(this).data('table-index') != end_index);
//                        }).removeClass('active');
//
//
//
//                        if(hide_same){
////                            $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
////                                return ($(this).data('table-index') == end_index);
////                            }).hide();
//                        }
//
//
//
//
//                    }else{
//                        $(session_form_id+" input[type='checkbox']").filter(function() {
//                            return true;
//                        }).prop( "checked", false );
//
//
//
//                        $("tr").filter(function() {
//                            return true;
//                        }).css( "background-color", "");
//
//
//
//                        $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
//                            return ($(this).data('table-index') != begin_index);
//                        }).show();
//
//
//                        $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
//                            return ($(this).data('table-index') != end_index);
//                        }).show();
//                    }
//                }


            }


            var remove_idsession_popup = function(cleardata){
                $('#identify_session_group').hide();
                if(cleardata){
                    reset_data();
                    clear_selection_function();
                }
            }


            var mark_session_button_function = function(ev){
                ev.preventDefault()// cancel form submission


//                Get userID, startTimestamp, endTimestamp
                var userID = $('input[name="userID"]').val();
                var startTimestamp = $('input[name="startTimestamp"]').val();
                var endTimestamp = $('input[name="endTimestamp"]').val();

                var begin_labels = [];


                $.each(history_begin, function(key, history_item) {
                    if(history_item['type']=='begin'){
                       begin_labels.push(history_item['sessionLabel']);
                    }
                });




//                alert("STUFF");
                var f1 = function(){
                    var return_data = '';
                    for(i=0; i < begin_labels.length; i++){
                        var label = begin_labels[i];
//                        alert("LABEL"+label);
                        var queryIDs = [];
                        var pageIDs = [];



                        var queryIDs_filter = $('input[type="checkbox"]').filter(function(){
                            return $(this).data('session-label')==label && $(this).attr('name')=='queries[]';
                        });

                        var pageIDs_filter = $('input[type="checkbox"]').filter(function(){
                            return $(this).data('session-label')==label && $(this).attr('name')=='pages[]';
                        });

                        $.each(queryIDs_filter, function(row_index, value) {
                            queryIDs.push($(this).attr('value'));
                        });

                        $.each(pageIDs_filter, function(row_index, value) {
                            pageIDs.push($(this).attr('value'));
                        });

//                        alert("QIDS"+JSON.stringify(queryIDs));
//                        alert("PIDS"+JSON.stringify(pageIDs));
                        formData = {
                            'userID':userID,
                            'startTimestamp':startTimestamp,
                            'endTimestamp':endTimestamp,
                        }

                        if(queryIDs.length>0){
                            formData['queries[]']=queryIDs;
                        }

                        if(pageIDs.length>0){
                            formData['pages[]']=pageIDs;
                        }
//                        alert(JSON.stringify(formData));


                        $.ajax({
                            type: 'POST',
                            url: $(session_form_id).attr('action'),
                            data: formData
                        }).done(function(response) {
                            response = JSON.parse(response);
                            return_data = response;

                            if(response.hasOwnProperty('error')){

                                $.notify({
                                    // options
                                    message: response.message
                                },{
                                    // settings
                                    type: 'danger'
                                });
                            }else{


                                $('#session_panel').html(response.sessionhtml);
                                $('#progress_container').html(response.progressbar_html);
                                $(session_form_id+" button[name='clear_selection_button']").unbind("click").click(clear_selection_function);
                                $(session_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
                                $(session_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);
                                $('#identify_session_button').unbind('click').click(mark_session_button_function);


                            }

                        }).fail(function(data) {
//                        alert("Communication to the server was temporarily lost. The session was not marked. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                        });

                        $.notify({
                            // options
                            message: "Sessions identified!"
                        },{
                            // settings
                            type: 'success'
                        });
                    }

                    return return_data;

//                    var formData = $(session_form_id).serialize();
//                    var sessionID = -1;
//                    if(typeof $('input[name="whichsession"]:checked').val() !== typeof undefined ){
//                        sessionID = $('input[name="whichsession"]:checked').val()
//                    }
                }

                $.when(f1()).done(function(response){
                    reset_data();
                    clear_selection_function();
                    popup_clear();
                    remove_idsession_popup(false);



                });





            };

            $(document).ready(function(){

//                    $(session_form_id+" button[name='mark_session_button']").unbind("click").click(mark_session_button_function);
                    $(session_form_id+" button[name='clear_selection_button']").unbind("click").click(clear_selection_function);
                    $(session_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
                    $(session_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);
                    $("#identify_session_button").unbind("click").click(mark_session_button_function);
                    $('div#pop-up').draggable({cursor:'move'});
                    reset_data();

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
            <div class="col-md-12">
                <div class="page-header">
                    <div class="">
                        <h1>Identify Your Day's Sessions


                        </h1>
                        <div id="progress_container">
                            <?php
                            echo $sessionTables['progressbar_html'];
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
                        <center><h4>Help</h4></center>
                    </div>
                    <div class="panel-body">


                        <div>
                            <center><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tutorial_modal">Press for Help</button></center>
                        </div>

                    </div>


                </div>
            </div>
        </div>


        <!--   Query Log and Progress     -->

<!--        <div class="row">-->
<!--            <div class="col-md-12" id="progress_container">-->
<!--                --><?php
//                echo $sessionTables['progressbar_html'];
//                ?>
<!--            </div>-->
<!--        </div>-->

        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Identify Your Day's Sessions</h4></center>
                    </div>

                    <form id="session_form" action="../services/utils/runPageQueryUtils.php?action=markSessionBatch">


                            <div class="panel-body" id="session_panel">
                            <?php
                            echo $sessionTables['sessionhtml'];
                            ?>
                            </div>



                    </form>

                </div>




            </div>


        </div>
    </div>

<?php
    printTutorialModal('session');
?>


<div class="btn-group" style="position: fixed; bottom: 20px; right:20px; z-index: 90;">

                    <?php
                    $actionUrls = actionUrls($selectedStartTimeSeconds);
                    echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['home']."'><i class=\"fa fa-arrow-circle-left\" aria-hidden=\"true\"></i> Back (Home)</a>";
                    echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['tasks']."'>Next (Assign Tasks to Sessions) <i class=\"fa fa-arrow-circle-right\" aria-hidden=\"true\"></i></a>";
                    ?>
</div>

    <div id="pop-up" class="panel panel-default">
        <div class="panel-heading clearfix">
            <h3 class="panel-title">Please Select a Session</h3>


        </div>
        <div class="panel-body">
            <div id="session_list">

            </div>

            <center>
                <button type="button" id='label_session_button' class="btn btn-warning" >Add Label</button>
                <button type="button" class="btn btn-default" onclick="popup_clear(this);">Cancel</button>
            </center>
        </div>



    </div>


<div id='identify_session_group' class="btn-group" style="position: fixed; bottom: 20px; left:20px; z-index: 90;display:none">
    <button type="button" id='identify_session_button' class="btn btn-warning" >Identify Session</button>
    <button type="button" class="btn btn-default" onclick="remove_idsession_popup(true);">Cancel</button>
</div>









    </body>
    </html>