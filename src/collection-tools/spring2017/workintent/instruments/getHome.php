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

    $homePageTables = getHomePageTables($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
//    print_r($homePageTables);
?>


    <html>
    <head>
        <title>
            Home
        </title>

        <link rel="stylesheet" href="../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <style>
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
            var trash_form_id = '#to_trash_form';
            var delete_form_id = '#permanently_delete_form';

            $(document).ready(function(){
                    $(trash_form_id).submit(function(event) {
                        // Stop the browser from submitting the form.
                        event.preventDefault();

                        var formData = $(trash_form_id).serialize();


                        $.ajax({
                            type: 'POST',
                            url: $(trash_form_id).attr('action'),
                            data: formData
                        }).done(function(response) {
//                            alert(response);
                            response = JSON.parse(response);

                            $('#log_panel').html(response.loghtml);
                            $('#trash_panel').html(response.trashhtml);
                            $('#log_confirmation').html("Sent to trash!");
                            $('#log_confirmation').show();
                            $('#log_confirmation').fadeOut(2000);
//                // Make sure that the formMessages div has the 'success' class.
//                $(formMessages).removeClass('error');
//                $(formMessages).addClass('success');
//
//                // Set the message text.
//                $(formMessages).text(response);
//
//                // Clear the form.
//                $('#name').val('');
//                $('#email').val('');
//                $('#message').val('');
                        }).fail(function(data) {
//                // Make sure that the formMessages div has the 'error' class.
//                $(formMessages).removeClass('success');
//                $(formMessages).addClass('error');
//
//                // Set the message text.
//                if (data.responseText !== '') {
//                    $(formMessages).text(data.responseText);
//                } else {
//                    $(formMessages).text('Oops! An error occured and your message could not be sent.');
//                }
                        });
                        // TODO
                    });




                $(delete_form_id+" button").click(function(ev){
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
                            $('#log_panel').html(response.loghtml);
                            $('#trash_panel').html(response.trashhtml);
                            $('#trash_confirmation').html("Pages were restored!");
                            $('#trash_confirmation').show();
                            $('#trash_confirmation').fadeOut(2000);
                        });
                    }else if($(this).attr("value")=="permanently_delete_button"){
                        $.ajax({
                            type: 'POST',
                            url: $(delete_form_id).attr('action')+"?action=permanentlyDelete",
                            data: formData
                        }).done(function(response) {
//                            alert(response);
                            response = JSON.parse(response);
                            $('#log_panel').html(response.loghtml);
                            $('#trash_panel').html(response.trashhtml);
                            $('#trash_confirmation').html("Pages were permanently deleted!");
                            $('#trash_confirmation').show();
                            $('#trash_confirmation').fadeOut(2000);
                        });
                    }
                });

                }
            );


        </script>
    </head>




    <body >
<!--    <body style="background-color:gainsboro">-->


    <div class="container-fluid">
        <!--   Dates Tab and Review     -->

        <div class="row">
            <div class="col-md-8">
                <h1>Welcome,
                    <?php echo $base->getFirstName()." ".$base->getLastName();?>!
                </h1>
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
                        <center><h4>Send Private Items to Trash</h4></center>

                    </div>
                </div>

                    <form id="to_trash_form" action="../services/utils/runPageQueryUtils.php?action=sendToTrash">
                        <div class="container" id="log_panel">
                            <center>

<!--                    <div class="panel-body tab-pane" id="log_panel">-->
                        <?php
                            echo $homePageTables['loghtml'];
                        ?>
<!--                    </div>-->
                        </div>

                        </center>
                    </form>




<!--                </div>-->
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h4>Trash Bin</h4></center>
                    </div>
                </div>
                    <form id="permanently_delete_form" action="../services/utils/runPageQueryUtils.php">
                        <div class="container" id="trash_panel">
<!--                        <div class="panel-body tab-pane" id="trash_panel">-->
                            <?php
                            echo $homePageTables['trashhtml'];
                            ?>

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
                            echo "<a type=\"button\" class=\"btn btn-info btn-lg\" href='".$actionUrls['sessions']."'>Mark Your Day's Sessions &raquo;</a>";
                            ?>
                        </center>
                    </div>
                </div>
            </div>

        </div>


    </div>
    <center><h3 id="log_confirmation" class="alert alert-success"></h3></center>


    </div>
    </body>
    </html>
<?php
?>