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

    $homePageTables = getHomePageTables($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
//    print_r($homePageTables);
?>


    <html>
    <head>
        <title>
            Home
        </title>

        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="../lib/bootstrap_notify/bootstrap-notify.min.js"></script>


        <style>

            body{
                background: #DFE2DB !important;
            }
            /*.btn-circle {*/
            /*}*/

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

            /*.alert{*/
                /*position:fixed;*/
                /*top:0;*/
                /*align:center;*/
                /*width:100%;*/
                /*display:none;*/
                /*margin: 0 auto;*/
            /*}*/
            /**/

        </style>

        <script>
            var trash_form_id = '#to_trash_form';
            var delete_form_id = '#permanently_delete_form';

            var highlight_rows = function(){

                if($(this).data('table-row-index')==0){
//                    alert("hi");

                    var tbody_name = ''
                    var table_name = $(this).data('table');
                    if(table_name=='trash_table'){
                        tbody_name='trash_table';
                    }else{
                        tbody_name='history_table';
                    }
                    var is_checked = $(this).prop('checked');
                    var the_rows = $('#'+tbody_name+' tr').filter(function() {
                        return $(this).css('display') !== 'none';
                    });
                    the_rows.find(':checkbox').prop('checked',is_checked);

                    if(this.checked){
                        the_rows.addClass('active');
                    }else{
                        the_rows.removeClass('active')
                    }
                    return;
                }
                var table_row_index = $(this).data('table-row-index');
                var table = $(this).data('table');
                if(this.checked){
                    $("table").find("[data-table='"+table+"'][data-table-row-index='"+table_row_index+"']").addClass('active');
                }else{
                    $("table").find("[data-table='"+table+"'][data-table-row-index='"+table_row_index+"']").removeClass('active');
                }
            }


            var restore_delete_function = function(ev){
                ev.preventDefault()// cancel form submission

                var formData = $(delete_form_id).serialize();
                if($(this).attr("value")=="restore_button"){
                    $.ajax({
                        type: 'POST',
                        url: $(delete_form_id).attr('action')+"?action=restore",
                        data: formData
                    }).done(function(response) {
//                            alert(response);
                        response = JSON.parse(response);
                        if(response.hasOwnProperty('error')){
//                            $('#trash_confirmation').removeClass('alert-success');
//                            $('#trash_confirmation').addClass('alert-danger');
//                            $('#trash_confirmation').html(response.message);

                            $.notify({
                                // options
                                message: response.message
                            },{
                                // settings
                                type: 'danger'
                            });
//                            $('#trash_confirmation').show();
//                            $('#trash_confirmation').fadeOut(3000);
                        }else {
                            $('#log_panel').html(response.loghtml);
                            $('#trash_panel').html(response.trashhtml);
                            $('input:checkbox').change(highlight_rows);
                            $('#history_search').keyup(search_history);
                            $('#trash_search').keyup(search_trash);
                            $(delete_form_id + " button").unbind("click").click(restore_delete_function);
//                            $('#trash_confirmation').removeClass('alert-danger');
//                            $('#trash_confirmation').addClass('alert-success');
//                            $('#trash_confirmation').html("Pages were restored!");

                            $.notify({
                                // options
                                message: "Pages were restored!"
                            },{
                                // settings
                                type: 'success'
                            });
                            $('#trash_confirmation').show();
                            $('#trash_confirmation').fadeOut(3000);
                        }
                    }).fail(function(data) {
                        alert("Communication to the server was temporarily lost. Pages were not restored. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                    });
                }else if($(this).attr("value")=="permanently_delete_button"){
                    $.ajax({
                        type: 'POST',
                        url: $(delete_form_id).attr('action')+"?action=permanentlyDelete",
                        data: formData
                    }).done(function(response) {

                        response = JSON.parse(response);
                        if(response.hasOwnProperty('error')){
//                            $('#trash_confirmation').removeClass('alert-success');
//                            $('#trash_confirmation').addClass('alert-danger');

                            $.notify({
                                // options
                                message: response.message
                            },{
                                // settings
                                type: 'danger'
                            });

//                            $('#trash_confirmation').html(response.message);
//                            $('#trash_confirmation').show();
//                            $('#trash_confirmation').fadeOut(3000);
                        }else{
                            $('#log_panel').html(response.loghtml);
                            $('#trash_panel').html(response.trashhtml);
                            $('input:checkbox').change(highlight_rows);
                            $('#history_search').keyup(search_history);
                            $('#trash_search').keyup(search_trash);
//                            $('#trash_confirmation').addClass('alert-success');
//                            $('#trash_confirmation').removeClass('alert-danger');
                            $(delete_form_id+" button").unbind("click").click(restore_delete_function);
//                            $('#trash_confirmation').html("Pages were permanently deleted!");

                            $.notify({
                                // options
                                message: "Pages were permanently deleted!"
                            },{
                                // settings
                                type: 'success'
                            });
//                            $('#trash_confirmation').show();
//                            $('#trash_confirmation').fadeOut(3000);
                        }


                    }).fail(function(data) {
                        alert("Communication to the server was temporarily lost. Pages were not permanently removed. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                    });
                }
            };

            var search_trash = function(ev){
                var value = $(this).val().toLowerCase();
                $("#trash_table tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            }

            var search_history = function(ev){
                var value = $(this).val().toLowerCase();
                $("#history_table tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            }


            $(document).ready(function(){

                    $('#history_search').keyup(search_history);
                    $('#trash_search').keyup(search_trash);
                    $('input:checkbox').change(highlight_rows);
                    $('#history_search').keyup(search_history);
                    $('#trash_search').keyup(search_trash);
                    $(trash_form_id).submit(function(event) {
                        // Stop the browser from submitting the form.
                        event.preventDefault();

                        var formData = $(trash_form_id).serialize();


                        $.ajax({
                            type: 'POST',
                            url: $(trash_form_id).attr('action'),
                            data: formData
                        }).done(function(response) {
                            response = JSON.parse(response);
                            if(response.hasOwnProperty('error')){
//                                $('#log_confirmation').removeClass('alert-success');
//                                $('#log_confirmation').addClass('alert-danger');
//                                $('#log_confirmation').html(response.message);

                                $.notify({
                                    // options
                                    message: response.message
                                },{
                                    // settings
                                    type: 'danger'
                                });
//                                $('#log_confirmation').show();
//                                $('#log_confirmation').fadeOut(3000);
                            }else{
                                $('#log_panel').html(response.loghtml);
                                $('#trash_panel').html(response.trashhtml);
                                $('input:checkbox').change(highlight_rows);
                                $('#history_search').keyup(search_history);
                                $('#trash_search').keyup(search_trash);
                                $(delete_form_id+" button").unbind("click").click(restore_delete_function);
//                                $('#log_confirmation').removeClass('alert-danger');
//                                $('#log_confirmation').addClass('alert-success');
//                                $('#log_confirmation').html("Sent to trash!");

                                $.notify({
                                    // options
                                    message: "Sent to trash!"
                                },{
                                    // settings
                                    type: 'success'
                                });
//                                $('#log_confirmation').show();
//                                $('#log_confirmation').fadeOut(3000);
                            }




                        }).fail(function(data) {
                            alert("Communication to the server was temporarily lost. Pages were not moved to trash. Please try again later or contact mmitsui@scarletmail.rutgers.edu if you experience further issues.");
                        });
                    });





                $(delete_form_id+" button").unbind("click").click(restore_delete_function);

                }
            );


        </script>
    </head>




    <body >
<!--    <body style="background-color:gainsboro">-->


    <div class="container-fluid">


        <!--   Dates Tab and Review     -->

        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="">
                        <h1>Welcome,
                            <?php echo $base->getFirstName()." ".$base->getLastName();?>!
                        </h1>
                        <h1>
                            (Annotation Part 1/5)
                        </h1>
                        <h1>
                            Mark Private Items
                        </h1>
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
                                    <?php
                                        $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/getHome.php', $selectedStartTimeSeconds);
                                        foreach($dayButtonStrings as $button){
                                            echo "$button\n";
                                        }

                                    ?>

                                </div>
                            </center>

                        </div>
                </div>
            </div>


            <div class="col-md-4" >
                <div class="panel panel-primary" >
                    <div class="panel-heading">
                        <center><h4>Help</h4></center>
                    </div>
                    <div class="panel-body">
                        <center><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tutorial_modal">Press for Help</button></center>
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
                        <center><h4>Send Private Items to Trash</h4></center>

                    </div>

                    <form id="to_trash_form" action="../services/utils/runPageQueryUtils.php?action=sendToTrash">
                        <!--                        <div class="container" id="log_panel">-->
                        <center>


                            <div class="panel-body" id="log_panel">



                                <center>
                                    <?php
                                    echo $homePageTables['loghtml'];
                                    ?>
                                </center>
                            </div>
                            <!--                        </center>-->

                            <!--                        </div>-->

                    </form>
                </div>






<!--                </div>-->
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h4>Trash Bin</h4></center>
                    </div>

                    <form id="permanently_delete_form" action="../services/utils/runPageQueryUtils.php">
<!--                        <div class="container" id="trash_panel">-->

                            <div class="panel-body" id="trash_panel">

                            <?php
                            echo $homePageTables['trashhtml'];
                            ?>
                            </div>

<!--                        </div>-->

                    </form>

                </div>



                </div>
            </div>

<!--        <div class="row">-->
<!--            <div class="col-md-12">-->
<!--                <div class="panel panel-primary">-->
<!--                    <div class="panel-heading">-->
<!--                        <center>-->
<!--                            --><?php
//                            $actionUrls = actionUrls($selectedStartTimeSeconds);
//                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['sessions']."'>Identify Your Day's Sessions &raquo;</a>";
//                            ?>
<!--                        </center>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--        </div>-->


    </div>

<?php
    printTutorialModal('trash');
?>

<!--    <center><h3 id="log_confirmation" class="alert alert-success"></h3></center>-->
<!--    <center><h3 id="trash_confirmation" class="alert alert-success"></h3></center>-->


    </div>

<!--<div style="position: fixed; bottom: 0px; right:20px; z-index: 90;">-->
<!--    <center>-->
<!---->
<!--        <div class="panel panel-default">-->
<!--            <div class="panel-body">-->
<!--                <center>-->

<div class="btn-group" style="position: fixed; bottom: 20px; right:20px; z-index: 90;">

    <?php
                    $actionUrls = actionUrls($selectedStartTimeSeconds);
                    echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['sessions']."'>Identify Your Day's Sessions <i class=\"fa fa-arrow-circle-right\" aria-hidden=\"true\"></i></a>";
                    ?>
</div>
<!--                </center>-->
<!--            </div>-->
<!--        </div>-->
<!--    </center>-->
<!--</div>-->

    </body>
    </html>
<?php
?>