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

            var session_numbers;

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

                $('div#pop-up').hide();
            }


            var populate_session_popup = function() {
                $('#session_list').html("");
                $('#session_list').append("<ul id='newList'></ul>");

                $.each($('input[name="sessionidlabel"]'), function(n, elem) {
                    $("#newList").append("<div class='radio'><label><input type='radio' name='whichsession' value='"+$(elem).data('session-id')+"'/> Session "+$(elem).data('session-label')+"</label></div>");
                });

                $("#newList").append("<div class='radio'><label><input type='radio' name='whichsession' value='0'/> New Session</label></div>");

//                for (cnt = 0; cnt < someList.length; cnt++) {
//                    $("#newList").append("<li>"+someList[cnt].FirstName + ":" + someList[cnt].LastName+"</li>");
//                }
//                session_numbers = $('td[name="session_number"]').
            }


            var popup_show = function(ev,button){

                if($('button.active').length <= 1){
                    return;
                }

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

            var denote_beginend_function = function(ev){

                var hide_same = false;
                if($(this).attr("name")=='begin_button'){

                    if($(this).hasClass('active')){
                        $(this).removeClass('active');
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
                        begin_index = $(this).data('table-index');
                    }
                }else if($(this).attr("name")=='end_button'){
                    if($(this).hasClass('active')){
                        $(this).removeClass('active');
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
                        end_index = $(this).data('table-index');

                    }
                }

                $(session_form_id+" tr").filter(function() {
                    return ($(this).data('marked'));
                }).addClass('success');

                if(begin_index != -1 && end_index != -1){

                    $(session_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
                    }).prop( "checked", true );

                    $(session_form_id+" input[type='checkbox']").filter(function() {
                        return ($(this).data('table-index') < begin_index || $(this).data('table-index') > end_index);
                    }).prop( "checked", false );


                    $("tr").filter(function() {
                        return ($(this).data('table-index') >= begin_index && $(this).data('table-index') <= end_index);
                    }).removeClass('success').addClass( "active");

                    $("tr").filter(function() {
                        return ($(this).data('table-index') < begin_index || $(this).data('table-index') > end_index);
                    }).removeClass( "active");

                    if(hide_same){
//                        $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
//                            return ($(this).data('table-index') == begin_index);
//                        }).hide();
//
//                        $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
//                            return ($(this).data('table-index') == end_index);
//                        }).hide();
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

                        popup_show(ev,this);

//                        $("div[name='session_button_group']").fadeIn("slow");

//                        $("button[name='mark_session_button']").fadeIn("slow");
                    }


                }else{
                    popup_clear(ev);

//                    $("div[name='session_button_group']").fadeOut("slow");
//                    $("button[name='mark_session_button']").fadeOut("slow");

                    if(begin_index != -1){
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') == begin_index);
                        }).prop( "checked", true );



                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') != begin_index);
                        }).prop( "checked", false );

                        $("tr").filter(function() {
                            return ($(this).data('table-index') == begin_index);

                        }).removeClass('success').addClass('active');

                        $("tr").filter(function() {
                            return ($(this).data('table-index') != begin_index);
                        }).removeClass('active');



                        if(hide_same){
//                            $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
//                                return ($(this).data('table-index') == begin_index);
//                            }).hide();
                        }



                    }else if(end_index != -1){
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') != end_index);
                        }).prop( "checked", true );

                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return ($(this).data('table-index') != end_index);
                        }).prop( "checked", false );

                        $("tr").filter(function() {
                            return ($(this).data('table-index') == begin_index);

                        }).removeClass('success').addClass('active');

                        $("tr").filter(function() {
                            return ($(this).data('table-index') != end_index);
                        }).removeClass('active');



                        if(hide_same){
//                            $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
//                                return ($(this).data('table-index') == end_index);
//                            }).hide();
                        }




                    }else{
                        $(session_form_id+" input[type='checkbox']").filter(function() {
                            return true;
                        }).prop( "checked", false );



                        $("tr").filter(function() {
                            return true;
                        }).css( "background-color", "");



                        $(session_form_id+" button[type='button'][name='end_button']").filter(function() {
                            return ($(this).data('table-index') != begin_index);
                        }).show();


                        $(session_form_id+" button[type='button'][name='begin_button']").filter(function() {
                            return ($(this).data('table-index') != end_index);
                        }).show();
                    }
                }


            }



            var mark_session_button_function = function(ev){
                ev.preventDefault()// cancel form submission
                var formData = $(session_form_id).serialize();
                var sessionID = -1;
                if(typeof $('input[name="whichsession"]:checked').val() !== typeof undefined ){
                    sessionID = $('input[name="whichsession"]:checked').val()
                }

                if(true){
                    $.ajax({
                        type: 'POST',
                        url: $(session_form_id).attr('action'),
                        data: formData + "&sessionID="+sessionID
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
//                            $('#mark_session_confirmation').removeClass('alert-success');
//                            $('#mark_session_confirmation').addClass('alert-danger');
//                            $('#mark_session_confirmation').html(response.message);
//                            $('#mark_session_confirmation').show();
//                            $('#mark_session_confirmation').fadeOut(3000);
                        }else{
                            $('#session_panel').html(response.sessionhtml);
                            $('#progress_container').html(response.progressbar_html);
//                            $(session_form_id+" button[name='mark_session_button']").unbind("click").click(mark_session_button_function);
                            $(session_form_id+" button[name='clear_selection_button']").unbind("click").click(clear_selection_function);
                            $(session_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
                            $(session_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);

                            populate_session_popup();
                            begin_index = -1;
                            end_index = -1;
                            $.notify({
                                // options
                                message: "Session identified!"
                            },{
                                // settings
                                type: 'success'
                            });
                        }

                    }).fail(function(data) {
                        alert("Communication to the server was temporarily lost. The session was not marked. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                    });
                }
            };

            $(document).ready(function(){

//                    $(session_form_id+" button[name='mark_session_button']").unbind("click").click(mark_session_button_function);
                    $(session_form_id+" button[name='clear_selection_button']").unbind("click").click(clear_selection_function);
                    $(session_form_id+" button[name='begin_button']").unbind("click").click(denote_beginend_function);
                    $(session_form_id+" button[name='end_button']").unbind("click").click(denote_beginend_function);
                    $("#identify_session_button").unbind("click").click(mark_session_button_function);
                    $('div#pop-up').draggable({cursor:'move'});
                    populate_session_popup();

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

                    <form id="session_form" action="../services/utils/runPageQueryUtils.php?action=markSessionSeq">


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
            <p>Which session?</p>
            <div id="session_list">

            </div>

            <center>
                <button type="button" id='identify_session_button' class="btn btn-warning" >Identify Session</button>
                <button type="button" class="btn btn-default" onclick="popup_clear(this);">Cancel</button>
            </center>
        </div>



    </div>









    </body>
    </html>