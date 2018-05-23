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
function getTimezone($userID){
    $query = "SELECT * FROM recruits WHERE userID=$userID";
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);
    return $line['timezone'];
}

$base->setUserTimezone(getTimezone($userID));

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
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
            var table_index = -1;
            var row_index = -1;
            var query_segment_id = -1;
            var session_form_id = '';


            var history = {};
            var history_begin = {};
            var history_end = {};
            var min_row_indices = {};
            var max_row_indices = {};
            var sortedRowIndices = {};
            var max_new_labels = {};
            var unfinishedLabels = {};
            var all_begin_labels = {};
            var all_end_labels = {};


//            TODO
            var reset_data = function(sessionID){
//                If sessionID=-1, apply to all
//                Else, apply to particular sessionID
                if(sessionID==-1){
                    history = {};
                    history_begin = {};
                    history_end = {};
                    min_row_indices = {};
                    max_row_indices = {};
                    sortedRowIndices = {};
                    max_new_labels = {};
                    unfinishedLabels = {};
                    all_begin_labels = {};
                    all_end_labels = {};

                    Object.keys(sessionID_to_label).forEach(function(key) {
                        history[parseInt(key)] = {};
                        history_begin[parseInt(key)] = {};
                        history_end[parseInt(key)] = {};
                        min_row_indices[parseInt(key)] = 0;
                        max_row_indices[parseInt(key)] = 0;
                        sortedRowIndices[parseInt(key)] = [];
                        max_new_labels[parseInt(key)] = 0;
                        var max_fixed_labels = $.map($("td[name='search-segment-id'][data-session-id='"+parseInt(key)+"']"),function(o,i){
                            return parseInt($(o).html()) || 0;
                        });
                        max_new_labels[parseInt(key)] = Math.max.apply(Math,max_fixed_labels);
                        unfinishedLabels[parseInt(key)] = [];
                        all_begin_labels[parseInt(key)] = [];
                        all_end_labels[parseInt(key)] = [];
                    });




                }
                else{
                    history[parseInt(sessionID)] = {};
                    history_begin[parseInt(sessionID)] = {};
                    history_end[parseInt(sessionID)] = {};
                    min_row_indices[parseInt(sessionID)] = 0;
                    max_row_indices[parseInt(sessionID)] = 0;
                    sortedRowIndices[parseInt(sessionID)] = [];
                    max_new_labels[parseInt(sessionID)] = 0;
                    var max_fixed_labels = $.map($("td[name='search-segment-id'][data-session-id='"+parseInt(sessionID)+"']"),function(o,i){
                        return parseInt($(o).html()) || 0;
                    });
                    max_new_labels[parseInt(sessionID)] = Math.max.apply(Math,max_fixed_labels);
                    unfinishedLabels[parseInt(sessionID)] = [];
                    all_begin_labels[parseInt(sessionID)] = [];
                    all_end_labels[parseInt(sessionID)] = [];


                }
            }


            var edit_history_begin = function(action,index,sessionID,searchSegmentLabel){
                if(action=='add'){
                    history_begin[sessionID][index] = {'type':'begin','searchSegmentLabel':parseInt(searchSegmentLabel)};
                }else{
                    delete history_begin[sessionID][index];
                }
            }

            var edit_history_end = function(action,index,sessionID,searchSegmentLabel){
                if(action=='add'){
//                    alert(JSON.stringify(history_end));
//                    alert(searchSegmentLabel);
//                    alert(JSON.stringify(history_end[sessionID]));
                    history_end[sessionID][index] = {'type':'end','searchSegmentLabel':parseInt(searchSegmentLabel)};
                }else{
                    delete history_end[sessionID][index];
                }
            }


//            Hide/show "which seargh segment?" panel
            var hide_show_searchsegment_selectionpanel = function(sessionID,show_or_hide){
                if(sessionID==-1){
                    if(show_or_hide=='show'){
                        $('div[name="whichsearchsegmentpanel"]').show();
                    }else{
                        $('div[name="whichsearchsegmentpanel"]').hide();
                    }
                }else{
                    if(show_or_hide=='show'){
                        $('div[name="whichsearchsegmentpanel"][data-session-id="'+sessionID+'"]').show();
                    }else{
                        $('div[name="whichsearchsegmentpanel"][data-session-id="'+sessionID+'"]').hide();
                    }
                }
            }




//            Hide/show prompt for identifying search segments
            var hide_show_identify_session_panel = function(sessionID,show_or_hide){
                if(sessionID==-1){
                    if(show_or_hide=='show'){
                        $('div[name="identify_session_panel"]').show();
                    }else{
                        $('div[name="identify_session_panel"]').hide();
                    }
                }else{
                    if(show_or_hide=='show'){
                        $('div[name="identify_session_panel"][data-session-id="'+sessionID+'"]').show();
                    }else{
                        $('div[name="identify_session_panel"][data-session-id="'+sessionID+'"]').hide();
                    }
                }
            }

//            Hide/show container of above
            var hide_show_identify_session_container = function(){
                var n_displayed = $('div[name="identify_session_panel"]').filter(function() {
                    return $(this).css('display') !== 'none';
                }).length;

                if(n_displayed >0){
                    $('div[name="identify_session_container"]').show();
                }else{
                    $('div[name="identify_session_container"]').hide();
                }

            }


//            Given sessionID, remove active classes and reset buttons for session panel
            var clear_selection_function = function(sessionID){
                $("tr[data-session-id='"+sessionID+"']").removeClass('active');
                $("button[name='end_button'][data-session-id='"+sessionID+"']").removeClass('active');
                $("button[name='begin_button'][data-session-id='"+sessionID+"']").removeClass('active');
                $("button[name='end_button'][data-session-id='"+sessionID+"']").show();
                $("button[name='begin_button'][data-session-id='"+sessionID+"']").show();
                $("button[name='end_button'][data-session-id='"+sessionID+"']").html('End');
                $("button[name='begin_button'][data-session-id='"+sessionID+"']").html('Begin');

//                TODO: Correct? Needs to be inserted in other parts of code?
                $("tr").filter(function() {
                    return ($(this).data('marked')) && ($(this).data('session-id')==sessionID);
                }).addClass('success');

                $('span[name="filler"][data-session-id="'+sessionID+'"]').html("");
                hide_show_searchsegment_selectionpanel(sessionID,'hide');
                hide_show_identify_session_panel(sessionID,'hide');

                reset_data(sessionID);
                update_session_labels();
            }



            var populate_whichsegment_popup = function(row_index,begin_or_end,begin_end_func,sessionID) {
                var query_segment_list = $('div[name="session_list"][data-session-id="'+sessionID+'"]');
                query_segment_list.html("");

                if(begin_or_end=='begin'){
                    query_segment_list.append("<p>This is the beginning of which search segment?</p>");
                }else{
                    query_segment_list.append("<p>This is the end of which search segment?</p>");
                }

                query_segment_list.append("<ul name='newList' data-session-id='"+sessionID+"'></ul>");
                var new_list = $("ul[name='newList'][data-session-id='"+sessionID+"']");

                $.each(unfinishedLabels[sessionID], function(index, label) {
                    new_list.append("<div class='radio'><label><input type='radio' data-session-id='"+sessionID+"' name='whichsearchsegment' value='"+label+"'/> Search Segment "+label+"</label></div>");
                });

                if(begin_or_end=='begin'){
                    new_list.append("<div class='radio'><label><input type='radio' data-session-id='"+sessionID+"' name='whichsearchsegment' value='0'/> New Search Segment</label></div>");
                }

                query_segment_list.append("<input type='hidden' name='row_index' data-session-id='"+sessionID+"'value='"+row_index+"'/>");
                var label_querysegment_button = $("button[name='label_session_button'][data-session-id='"+sessionID+"']");
                label_querysegment_button.unbind("click").click(begin_end_func);
            }




//          TODO
            var mark_identifications_panel_show = function(ev,button,row_index,begin_or_end,begin_end_func,sessionID){
                var position = $(button).offset();
                $('div[name="identify_session_panel"][data-session-id="'+sessionID+'"]').show()
                    .css('top', position.top)
                    .css('left', position.left+$(button).width()+30)
                    .css('position','absolute')
                    .appendTo('body').mousedown(function() {
                        $(this).css('cursor','move');
                    });
                hide_show_identify_session_container();
            }


//            TODO
            var click_new_searchsegment_begin = function(){
                var sessionID = $(this).data('session-id');
                var row_index = parseInt($('input[name="row_index"][data-session-id="'+sessionID+'"]').val());
                var searchSegmentLabel = -1;

                if(typeof $('input[data-session-id="'+sessionID+'"][name="whichsearchsegment"]:checked').val() !== typeof undefined ){
                    searchSegmentLabel = $('input[data-session-id="'+sessionID+'"][name="whichsearchsegment"]:checked').val()
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

                if(searchSegmentLabel==0){
                    edit_history_begin('add',row_index,sessionID,max_new_labels[sessionID]+1);
                }else{
                    edit_history_begin('add',row_index,sessionID,searchSegmentLabel);
                }

                $('button').filter(function () {
                    return $(this).data('session-id')==sessionID && $(this).data('row-index') ==row_index && $(this).html()=="Begin";
                }).addClass('active').html("Undo Begin");

//                $.notify({
//                    // options
//                    message: "New beginning marked!"
//                },{
//                    // settings
//                    type: 'success'
//                });

                update_session_labels();
                hide_show_searchsegment_selectionpanel(sessionID,'hide');
            }

            var click_new_searchsegment_end = function(){
                var sessionID = $(this).data('session-id');
                var row_index = parseInt($('input[name="row_index"][data-session-id="'+sessionID+'"]').val());
                var searchSegmentLabel = -1;
                if(typeof $('input[data-session-id="'+sessionID+'"][name="whichsearchsegment"]:checked').val() !== typeof undefined ){
                    searchSegmentLabel = $('input[data-session-id="'+sessionID+'"][name="whichsearchsegment"]:checked').val()
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


                if(searchSegmentLabel==0){
                    edit_history_end('add',row_index,sessionID,max_new_labels[sessionID]+1);
                }else{
                    edit_history_end('add',row_index,sessionID,searchSegmentLabel);
                }


                $('button').filter(function () {
                    return $(this).data('session-id')==sessionID && $(this).data('row-index') ==row_index && $(this).html()=="End";
                }).addClass('active').html("Undo End");

//                $.notify({
//                    // options
//                    message: "New ending marked!"
//                },{
//                    // settings
//                    type: 'success'
//                });

                update_session_labels();
                hide_show_searchsegment_selectionpanel(sessionID,'hide');
            }

            function sortNumber(a,b) {
                return a - b;
            }

            var update_session_labels = function(){

//                $.each(history_begin, function(sessionID, session_history) {
//                    sortedRowIndices[sessionID] = [];
//                    $.each(session_history, function(row_index, value) {
//                        sortedRowIndices[sessionID].push(parseInt(row_index));
//                    });
//                    sortedRowIndices[sessionID].sort(sortNumber);
//                    if(sortedRowIndices[sessionID].length>=1){
//                        min_row_indices[sessionID] = sortedRowIndices[sessionID][0];
//                        max_row_indices[sessionID] = sortedRowIndices[sessionID][sortedRowIndices[sessionID].length-1];
//                    }
//                });


                $.each(history_begin, function(sessionID, session_history) {
                    sortedRowIndices[sessionID] = [];
                    $.each(session_history, function(row_index, value) {
                        sortedRowIndices[sessionID].push(parseInt(row_index));
                    });
                });


                $.each(history_end, function(sessionID, session_history) {
                    $.each(session_history, function(row_index, value) {
                        sortedRowIndices[sessionID].push(parseInt(row_index));
                    });
                    sortedRowIndices[sessionID].sort(sortNumber);
                    if(sortedRowIndices[sessionID].length>=1){
                        min_row_indices[sessionID] = sortedRowIndices[sessionID][0];
                        max_row_indices[sessionID] = sortedRowIndices[sessionID][sortedRowIndices[sessionID].length-1];
                    }
                });








                var begin_labels = {};
                var end_labels = {};
                var all_labels = {};
                all_begin_labels = {};
                all_end_labels = {};
                $.each(history_begin, function(sessionID, session_history) {
                    begin_labels[sessionID] = [];
                    all_begin_labels[sessionID] = [];
                    all_labels[sessionID] = [];
                    $.each(session_history, function(row_index, history_item) {
                        begin_labels[sessionID].push(history_item['searchSegmentLabel']);
                        all_begin_labels[sessionID].push(history_item['searchSegmentLabel']);
                        all_labels[sessionID].push(history_item['searchSegmentLabel']);
                    });
                });

                $.each(history_end, function(sessionID, session_history) {
                    end_labels[sessionID] = [];
                    all_end_labels[sessionID] = [];
                    $.each(session_history, function(row_index, history_item) {
                        end_labels[sessionID].push(history_item['searchSegmentLabel']);
                        all_end_labels[sessionID].push(history_item['searchSegmentLabel']);
                        all_labels[sessionID].push(history_item['searchSegmentLabel']);
                    });
                });



                unfinishedLabels = {};
                $.each(begin_labels, function(sessionID, begin_labels_session) {
                    unfinishedLabels[sessionID] = [];
                    $.grep(begin_labels_session, function (el) {
                        if ($.inArray(el, end_labels[sessionID]) == -1 && $.inArray(el, unfinishedLabels[sessionID]) == -1) {
                            unfinishedLabels[sessionID].push(el);
                        }
                    });
                });





                $.each(all_labels, function(sessionID, all_labels_session) {

                    max_new_labels[sessionID] = Math.max.apply(Math,all_labels[sessionID]);
                    if(all_labels[sessionID].length ==0){
                        max_new_labels[sessionID] = 0;
                        var max_fixed_labels = $.map($("td[name='search-segment-id'][data-session-id='"+sessionID+"']"),function(o,i){
                            return parseInt($(o).html()) || 0;
                        });
                        max_new_labels[sessionID] = Math.max.apply(Math,max_fixed_labels);

                    }
                });

//                alert("sorted row indices"+JSON.stringify(sortedRowIndices));
//                alert("all_labels" + JSON.stringify(all_labels));


//                Renaming rows
                $('span[name="filler"]').html("");

                $("input[type='checkbox']").filter(function () {
                    return true;
                }).prop( "checked", false ).removeData('searchsegment-label');



                $.each(sortedRowIndices, function(sessionID, sortedRowIndices_session) {

                    for(i=0; i < sortedRowIndices_session.length; i++){
                        var current_row_index = sortedRowIndices_session[i];

//                        alert("sessionID "+sessionID);
//                        alert("begin "+JSON.stringify(history_begin[sessionID]));
//                        alert("end "+JSON.stringify(history_end[sessionID]));

                        if(current_row_index in history_end[sessionID]){
                            searchSegmentLabel = history_end[sessionID][current_row_index]['searchSegmentLabel'];
                        }else{
                            searchSegmentLabel = history_begin[sessionID][current_row_index]['searchSegmentLabel'];
                        }

                        $('span[name="filler"]').filter(function () {
                            return $(this).data('session-id')==sessionID && $(this).data('row-index') >= current_row_index;
                        }).html("Search Segment "+searchSegmentLabel);

                        $('tr').filter(function () {
                            return $(this).data('session-id')==sessionID  && $(this).data('row-index') >= current_row_index;
                        }).addClass('active');

                        $("input[type='checkbox']").filter(function () {
                            return $(this).data('session-id')==sessionID && $(this).data('row-index') >= current_row_index;
                        }).prop( "checked", true).data('searchsegment-label',searchSegmentLabel);
//                        alert("LABEL" + searchSegmentLabel);
//                        alert("sessionID" + sessionID);
//                        alert("current_row_index" + current_row_index);
//                        alert($("input[type='checkbox']").filter(function () {
//                            return $(this).data('session-id')==sessionID && $(this).data('row-index') >= current_row_index;
//                        }).length);
                    }


                    $('span[name="filler"]').filter(function () {
                        return $(this).data('session-id')==sessionID && $(this).data('row-index') > sortedRowIndices_session[sortedRowIndices_session.length-1];
                    }).html("");

                    $('tr').filter(function () {
                        return $(this).data('session-id')==sessionID && $(this).data('row-index') > sortedRowIndices_session[sortedRowIndices_session.length-1];
                    }).removeClass('active');

                    $("input[type='checkbox']").filter(function () {
                        return $(this).data('session-id')==sessionID && $(this).data('row-index') > sortedRowIndices_session[sortedRowIndices_session.length-1];
                    }).prop( "checked", false ).removeData('searchsegment-label');



                    if(begin_labels[sessionID].length+end_labels[sessionID].length >=2){
//                        TODO
                        hide_show_identify_session_panel(sessionID,'show');
                        hide_show_identify_session_container();
                    }else{
//                        TODO
                        hide_show_identify_session_panel(sessionID,'hide');
                        hide_show_searchsegment_selectionpanel(sessionID,'hide');
                        hide_show_identify_session_container();
                    }
                });
            }



            var denote_beginend_function = function(ev){
                var hide_same = false;
                var last_type = '';
                var label = $(this).html();
                var curr_row_index = $(this).data('row-index');
                var curr_session_id = $(this).data('session-id');
                var min_row_index_session = min_row_indices[curr_session_id];
                var max_row_index_session = max_row_indices[curr_session_id];
                var max_new_labels_session = max_new_labels[curr_session_id];
                var sortedRowIndices_session = sortedRowIndices[curr_session_id];
                var history_end_session = history_end[curr_session_id];
                var history_begin_session = history_begin[curr_session_id];
                var unfinishedLabels_session = unfinishedLabels[curr_session_id];
                var all_begin_labels_session = all_begin_labels[curr_session_id];



//                alert ("curr row index "+ curr_row_index);
//                alert ("min_row_index_session "+ min_row_index_session);
//                alert ("max_row_index_session "+ max_row_index_session);
//                alert ("unfinishedLabels_session "+ JSON.stringify(unfinishedLabels_session));
//                alert ("history_begin_session "+ JSON.stringify(history_begin_session));
//                alert ("history_end_session "+ JSON.stringify(history_end_session));
//                alert ("all_begin_labels_session "+ JSON.stringify(all_begin_labels_session));
//                alert ("max_new_labels_session "+ JSON.stringify(max_new_labels_session));


//                alert("curr row index" + curr_row_index);
//                alert("min_row_index_session" + min_row_index_session);
//                alert("max_row_index_session" + max_row_index_session);
//                alert("label" + label);

                if($(this).attr("name")=='begin_button'){

                    if(curr_row_index <= min_row_index_session){
//                        alert("begin one");
                        if(label=="Begin"){
                            edit_history_begin('add',curr_row_index,curr_session_id,max_new_labels_session+1);
                            $(this).addClass('active');
                            $(this).html("Undo Begin");
                            update_session_labels();
                        }
                        else if(label=="Undo Begin"){
                            if((Object.keys(history_begin_session).length+Object.keys(history_end_session).length)>=2 && (sortedRowIndices_session[1] in history_end_session) ){
                                $.notify({
                                    // options
                                    message: "Your annotations should not begin with 'End'."
                                },{
                                    // settings
                                    type: 'danger'
                                });
                                return;
                            }

                            edit_history_begin('remove',curr_row_index,curr_session_id,-1);
                            $(this).removeClass('active');
                            $(this).html("Begin");
                            update_session_labels();
                        }

                    }
                    else if(curr_row_index >= max_row_index_session)
                    {
//                        alert("begin two");
                        last_type = '';
                        if(max_row_index_session in history_end_session){
                            last_type = history_end_session[max_row_index_session]['type'];
                        }else if(max_row_index_session in history_begin_session){
                            last_type = history_begin_session[max_row_index_session]['type'];
                        }
//                        alert("last_type"+last_type);

                        if(label=="Begin"){

                            if(curr_row_index - max_row_index_session > 1 && last_type=='end'){
                                $.notify({
                                    // options
                                    message: "Begin should come immediately after End."
                                },{
                                    // settings
                                    type: 'danger'
                                });
                                return;
                            }

//                            alert(JSON.stringify(unfinishedLabels_session));

//                            TODO: last
                            if(unfinishedLabels_session.length >=2){
//                                TODO
                                populate_whichsegment_popup(curr_row_index,'begin',click_new_searchsegment_begin,curr_session_id);
                                hide_show_searchsegment_selectionpanel(curr_session_id,'show');
                                hide_show_identify_session_container();
                            }else if(all_begin_labels_session.length >=2){
//                                TODO
                                mark_identifications_panel_show(ev,$(this),curr_row_index,'begin',click_new_searchsegment_begin);
                            }
                            else{
                                edit_history_begin('add',curr_row_index,curr_session_id,max_new_labels_session+1);
                                $(this).addClass('active');
                                $(this).html("Undo Begin");
                                update_session_labels();
                            }


                        }

                        if(label=="Undo Begin"){
                            edit_history_begin('remove',curr_row_index,curr_session_id,-1);
                            $(this).removeClass('active');
                            $(this).html("Begin");
                            update_session_labels();
                        }

                    }
                    else{

//                        alert("begin three");
                        if(label=="Begin"){
//                            TODO
                            mark_identifications_panel_show(ev,$(this),curr_row_index,'begin',click_new_searchsegment_begin);
                        }
                        else{
                            if(unfinishedLabels_session.length >= 2){
                                var prev_index = sortedRowIndices_session.indexOf(curr_row_index)-1;
                                if(prev_index >=0){
                                    if(curr_row_index in history_end_session){
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

                            edit_history_begin('remove',curr_row_index,curr_session_id,-1);
                            $(this).removeClass('active');
                            $(this).html("Begin");
                            update_session_labels();

                        }
                    }
                }else if($(this).attr("name")=='end_button'){


                    if(unfinishedLabels_session.length ==0 && label=="End"){
//                        alert("end zero");
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

                    if(curr_row_index < min_row_index_session){
//                        alert("end one");
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

                    else if(curr_row_index >= max_row_index_session){
//                        alert("end two");

                        if(label=="Undo End"){
                            $(this).removeClass('active');
                            $(this).html("End");
                            edit_history_end('remove',curr_row_index,curr_session_id,-1);
                            update_session_labels();
                        }else{
                            if(unfinishedLabels_session.length >=2){
//                                TODO
//                                alert("here");

                                populate_whichsegment_popup(curr_row_index,'end',click_new_searchsegment_end,curr_session_id);
                                hide_show_searchsegment_selectionpanel(curr_session_id,'show');
                                hide_show_identify_session_container();

                            }else{
//                                alert("there");
//                                alert("add rowindex "+curr_row_index); //2
//                                alert("add sessid "+curr_session_id); //432
//                                alert("add newlabels "+JSON.stringify(max_new_labels_session)); //0
                                edit_history_end('add',curr_row_index,curr_session_id,max_new_labels_session);
                                $(this).addClass('active');
                                $(this).html("Undo End");
                                update_session_labels();
                            }
                        }

                    }
                    else{
//                        alert("end three");
                        if(label=="End") {
                            mark_identifications_panel_show(ev,$(this),curr_row_index,'end',click_new_searchsegment_end);
                        }
                        else{
                            edit_history_end('remove',curr_row_index,curr_session_id,-1);
                            $(this).removeClass('active');
                            $(this).html("End");
                            update_session_labels();
                        }


                    }
                }
            }


//            TODO
            var remove_idsession_popup = function(sessionID,cleardata){
                $('div[name="identify_session_panel"][data-session-id="'+sessionID+'"]').hide();
                hide_show_searchsegment_selectionpanel(sessionID,'hide');
                hide_show_identify_session_container();
                if(cleardata){
                    clear_selection_function(sessionID); //includes reset_data
                }
            }




//            TODO: mark query segments - see below
            var mark_searchsegment_button_function = function(ev){
                ev.preventDefault()// cancel form submission
//                Get userID, startTimestamp, endTimestamp
                var userID = $('input[name="userID"]').val();
                var startTimestamp = $('input[name="startTimestamp"]').val();
                var endTimestamp = $('input[name="endTimestamp"]').val();
                var sessionID = $(this).data('session-id');
                var history_begin_session = history_begin[sessionID];

//                $.each(history_begin, function(sessionID, history_begin_session) {

                    if(Object.keys(history_begin_session).length <=0){
                        return;
                    }

                    var begin_labels = [];
                    $.each(history_begin_session, function(key, history_item) {
                        if(history_item['type']=='begin'){
                            begin_labels.push(history_item['searchSegmentLabel']);
                        }
                    });

                    var iter_labels = []
                    $.grep(begin_labels, function(el) {
                        if ($.inArray(el, iter_labels) == -1){
                            iter_labels.push(el);
                        }
                    });


                    var f1 = function(){
                        var return_data = '';
                        for(i=0; i < iter_labels.length; i++){
                            var label = iter_labels[i];
                            var queryIDs = [];
                            var pageIDs = [];



                            var queryIDs_filter = $('input[type="checkbox"]').filter(function(){
                                return  $(this).data('session-id')==sessionID && $(this).data('searchsegment-label')==label && $(this).attr('name')=='queries[]';
                            });

                            var pageIDs_filter = $('input[type="checkbox"]').filter(function(){
                                return $(this).data('session-id')==sessionID && $(this).data('searchsegment-label')==label && $(this).attr('name')=='pages[]';
                            });

                            $.each(queryIDs_filter, function(row_index, value) {
                                queryIDs.push($(this).attr('value'));
                            });

                            $.each(pageIDs_filter, function(row_index, value) {
                                pageIDs.push($(this).attr('value'));
                            });

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

//                            alert("formData "+ JSON.stringify(formData));

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
                                }else{
                                    $.notify({
                                        // options
                                        message: "Search segment marked!"
                                    },{
                                        // settings
                                        type: 'success'
                                    });

//                                    TODO
                                    $('#intent_modal').modal("hide");
                                    hide_show_searchsegment_selectionpanel(sessionID,'hide');
//                                    $("button[name='mark_querysegments_button']").hide();
                                    $('#querysegment_panel').html(response.querysegmenthtml);
                                    $('#select_intentions_panel').html(response.intentionshtml);
                                    $('#progressbar_segments_container').html(response.progressbar_segments_html);
                                    $('#progressbar_intents_container').html(response.progressbar_intents_html);
//                                    $("button[name='mark_querysegments_button']").unbind("click").click(mark_querysegments_function);
                                    $("button[name='mark_intentions_button']").unbind("click").click(mark_intentions_button_function);
                                    $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                                    $("button[name='cancel_intentions_button']").unbind("click").click(clear_intentions_function);
                                    $("button[name='begin_button']").unbind("click").click(denote_beginend_function);
                                    $("button[name='end_button']").unbind("click").click(denote_beginend_function);
                                    $('input[name="intentions[]"]').unbind("click").click(toggle_radio_function);
                                    $('[data-toggle="tooltip"]').tooltip();
                                    $('input:radio').unbind("click").click(toggle_text_function);
                                    $("button[name='intent_modal_button']").fadeOut("slow");
                                    begin_index = -1;
                                    end_index = -1;

                                }
                            }).fail(function(data) {
                                alert("Communication to the server was temporarily lost. Intentions were not marked. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                            });

                        }

                        return return_data;

                    };

                    $.when(f1()).done(function(response){
                        clear_selection_function(sessionID); //includes reset_data call
                        hide_show_searchsegment_selectionpanel(sessionID,'hide');
                        remove_idsession_popup(sessionID,false);
                    });



//                });

            };



            var clear_intentions_function = function() {
                $("tr").filter(function() {
                    return true;
                }).removeClass( "active");


                $('input:radio').prop('checked',false);
                $('textarea').val('');
                $(intents_form_id+" input[type='checkbox']").prop( "checked", false );
                $(intents_form_id+" div[id^='success_div']").hide();
                $(intents_form_id+" div[id^='failurereason_div']").hide();
                $("#other_description_div").hide();
                $('#intent_modal').removeClass('left');
                $('#intent_modal').hide();
                $('#main_div').removeClass('right');
                $('#forwardback_buttons').css('right','20px');
            }



            var begin_mark_intentions_function = function(ev){
                var rows = "";
                if($(this).attr("name")=='initiate_mark_intentions_button'){
                    query_segment_id = $(this).data('query-segment-id');
                }
                $("input[type='checkbox']").filter(function() {
                    return ($(this).data('query-segment-id') == query_segment_id );
                }).prop( "checked", true );

                $("input[type='checkbox']").filter(function() {
                    return ($(this).data('query-segment-id') != query_segment_id );
                }).prop( "checked", false );
            }


            var toggle_radio_function = function(ev){
                var intent_key = $(this).data('intent-key');
                if($( this ).is( ":checked" )){

                    $("#success_div_"+intent_key+"").show();

                    $("input[name='"+intent_key+"_success']").prop('disabled',false);
                }else{
                    $("#success_div_"+intent_key+"").hide();

                    $("input[name='"+intent_key+"_success']").prop('disabled',true);
                }

                if(intent_key=='other'){
                    if($( this ).is( ":checked" )){
                        $("textarea[name='"+intent_key+"_description']").prop('disabled',false);
                        $("#"+intent_key+"_description_div").show();
                    }else{
                        $("textarea[name='"+intent_key+"_description']").prop('disabled',true);
                        $("#"+intent_key+"_description_div").hide();
                    }
                }
            }

            var toggle_text_function = function(ev){
                var intent_key = $(this).data('intent-key');
                if($(this).attr('value')=='1'){
                    $("#failurereason_div_"+intent_key+"").hide();
                    $("textarea[name='"+intent_key+"_failure_reason']").prop('disabled',true);
                }else{
                    $("#failurereason_div_"+intent_key+"").show();
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
                            }else{
                                $.notify({
                                    // options
                                    message: "Search segment and intentions marked!"
                                },{
                                    // settings
                                    type: 'success'
                                });

                                $('#intent_modal').modal("hide");
                                $('#querysegment_panel').html(response.querysegmenthtml);
                                $('#select_intentions_panel').html(response.intentionshtml);
                                $('#progressbar_segments_container').html(response.progressbar_segments_html);
                                $('#progressbar_intents_container').html(response.progressbar_intents_html);
                                $("button[name='identify_session_button']").unbind("click").click(mark_searchsegment_button_function);
                                $("button[name='mark_intentions_button']").unbind("click").click(mark_intentions_button_function);
                                $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                                $("button[name='cancel_intentions_button']").unbind("click").click(clear_intentions_function);
                                $("button[name='begin_button']").unbind("click").click(denote_beginend_function);
                                $("button[name='end_button']").unbind("click").click(denote_beginend_function);
                                $('input[name="intentions[]"]').unbind("click").click(toggle_radio_function);
                                $('[data-toggle="tooltip"]').tooltip();
                                $('input:radio').unbind("click").click(toggle_text_function);
                                $("button[name='intent_modal_button']").fadeOut("slow");
                                begin_index = -1;
                                end_index = -1;
                                clear_intentions_function();
                            }
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



            function show_intent_modal(querySegmentID){
                $("tr").filter(function() {
                    return ($(this).data('query-segment-id') == querySegmentID);
                }).addClass( "active");
                $("tr").filter(function() {
                    return ($(this).data('query-segment-id') != querySegmentID);
                }).removeClass( "active");

                $('#intent_modal').addClass('left');
                $('#intent_modal').show();
                $('#main_div').addClass('right');
                $('#forwardback_buttons').css('right','25%');
            }


//            TODO
//            $(document).ready(function(){
//
//                    $(session_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
//                    $(session_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);
//                    $("#identify_session_button").unbind("click").click(mark_session_button_function);
//
//                    reset_data();
//
//                }
//            );

            $(document).ready(function(){
                Object.keys(sessionID_to_label).forEach(function(key) {
                    if(typeof(key)=='string'){
                        sessionID_to_label[parseInt(key)] = parseInt(sessionID_to_label[key]);
                    }
                });

                reset_data(-1);



                $("button[name='mark_intentions_button']").unbind("click").click(mark_intentions_button_function);
                $("button[name='identify_session_button']").unbind("click").click(mark_searchsegment_button_function);
                $("button[name='begin_button']").unbind("click").click(denote_beginend_function);
                $("button[name='end_button']").unbind("click").click(denote_beginend_function);
                $("button[name='initiate_mark_intentions_button']").unbind("click").click(begin_mark_intentions_function);
                $("button[name='cancel_intentions_button']").unbind("click").click(clear_intentions_function);
                $('input:radio').unbind("click").click(toggle_text_function);
                $('input[name="intentions[]"]').unbind("click").click(toggle_radio_function);
                $('div[name="whichsearchsegmentpanel"]').draggable({cursor:'move'});
                hide_show_searchsegment_selectionpanel(-1,'hide');
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
                            (Annotation Part 4/5)
                        </h1>
                        <h1>Annotate Search Segments and Intentions
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
            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['tasks']."'><i class=\"fa fa-arrow-circle-left\" aria-hidden=\"true\"></i> Back (Assign Tasks to Sessions)</a>";
            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['session questionnaire']."'><i class=\"fa fa-arrow-circle-right\" aria-hidden=\"true\"></i> Next (Search Session Questionnaire)</a>";

            //                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
            //                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['intentions']."'>Next (Intentions) &raquo;</a>";
            ?>
        </div>

        </div>





<div name='identify_session_container' style="position: fixed; bottom: 20px; left:20px; z-index: 90;display:none">

<!--    <div id="popup-container" class="container">-->
        <div class="panel-group">

            <?php
            foreach($sessionIDs as $sessionID){
                echo "<div name='whichsearchsegmentpanel' class='searchsegmentpanel panel panel-primary' data-session-id='$sessionID'>";
                ?>
                <div class="panel-heading clearfix">
                    <h3 class="panel-title">
                        <?php
                        echo "Session ".$sessionIDToLabel[$sessionID]." - Please Select a Search Segment:";
                        ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <?php
                    echo "<div name=\"session_list\" data-session-id='$sessionID'>

                </div>";
                    ?>

                    <center>

                        <?php
                        echo "<button type=\"button\" name='label_session_button' data-session-id='$sessionID' class=\"btn btn-warning\" >Add Label</button>";
                        echo "<button type=\"button\" class=\"btn btn-default\" data-session-id='$sessionID' onclick=\"hide_show_searchsegment_selectionpanel($sessionID,'hide');\">Cancel</button>";
                        ?>

                    </center>
                </div>
                <?php
                echo "</div>";
            }
            ?>

        </div>

<!--    </div>-->
    
    <?php
    foreach($sessionIDs as $sessionID) {

        echo "<div name='identify_session_panel' class='panel panel-default' data-session-id='$sessionID' style='display:none'>";
        echo "<div class=\"panel-heading clearfix\">";
        echo "<h3 class=\"panel-title\">";
        echo "Session " . $sessionIDToLabel[$sessionID] . " - Identify Search Segments";
        echo "</h3>";
        echo "</div>";
        echo "<div class='panel-body'>";
        echo "<div name='identify_session_group' data-session-id='$sessionID' class=\"btn-group\">";
        echo "<button type=\"button\" data-session-id='$sessionID' name='identify_session_button' class=\"btn btn-warning\">Identify Segments</button>
        <button type=\"button\" class=\"btn btn-default\" onclick=\"remove_idsession_popup($sessionID,true);\">Cancel</button>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    ?>



</div>








    <div class="panel panel-default" id="intent_modal" style="display:none">
            <div class="panel-heading">
                <h4 class="modal-title" id="myModalLabel">What were your intentions for this search segment? Were they successful?</h4>
            </div>
            <form id="intentions_form" action="../services/utils/runPageQueryUtils.php?action=markQuerySegmentsAndIntentions">
                <div class="panel-body" >
                        <div class="well">
                            <div><p><h5>What were you trying to accomplish (what was your intention) during this part of the search? Please choose one or more of the "search intentions" on the right; if none fits your goal at this point in the search, please choose "Other", and give a brief explanation.</h5></p></div>
                        </div>

                        <div id="select_intentions_panel">
                            <?php
                            echo $intentionsPanel['intentionshtml'];
                            ?>
                        </div>
                </div>
            </form>
    </div>

    <?php
    printTutorialModal('intention');
    ?>

    </body>
    </html>
<?php
?>