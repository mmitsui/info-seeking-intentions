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

$markIntentionsPanels = getMarkIntentionsPanels($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);

$intentionsPanel = getIntentionsPanel($userID,$selectedStartTimeSeconds,$selectedEndTimeSeconds);
?>


    <html>
    <head>
        <title>
            Research Study Registration: Introduction
        </title>

        <!--        <link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">-->
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
        </style>


        <script>
            var mark_intentions_form_id= '#TODO';
            var intentions_panel_id= '#mark_intentions_panel';
            var intentions_button_panel_id = '#intentions_buttons';
            var add_intentions_form_id = '#mark_intentions_form';




            $(document).ready(function(){
//                $(intentions_button_panel_id+" button").click(function(ev) {
//                    ev.preventDefault();
//                    var taskID = $(this).data('task-id')
//                    alert(taskID);
//                    var formData = $(mark_intentions_form_id).serialize();
//                    formData = formData + "&taskID="+taskID;
//                    alert(formData);
//                    alert($(mark_intentions_form_id).attr('action'));
//                    $.ajax({
//                        type: 'POST',
//                        url: $(mark_intentions_form_id).attr('action'),
//                        data: formData
//                    }).done(function(response) {
//                        alert(response);
//                        response = JSON.parse(response);
//                        $(intentions_panel_id).html(response.intentionspanels_html);
//                        $('#addintentions_confirmation').html("Task annotated!");
//                        $('#addintentions_confirmation').show();
//                        $('#addintentions_confirmation').fadeOut(2000);
//                    });
//                });

                $(add_intentions_form_id+" button").click(function(ev){
                    ev.preventDefault()// cancel form submission
                    var querySegmentID = $("input[name='querySegmentID']:checked").val();
                    var formData = $(add_intentions_form_id).serialize()+"&querySegmentID="+querySegmentID;
//                    alert(formData);

                    if($(this).attr("value")=="markintentions_button"){
//                        alert("clicked!");
                        $.ajax({
                            type: 'POST',
                            url: $(add_intentions_form_id).attr('action'),
                            data: formData
                        }).done(function(response) {
//                            alert(response);
                            response = JSON.parse(response);
                            $('#select_querysegments_panel').html(response.intentionspanels_html);
                            $('#addintentions_confirmation').html("Task added!");
                            $('#addintentions_confirmation').show();
                            $('#addintentions_confirmation').fadeOut(3000);
                        });
                    }
                });
//                $("form input[type=submit]").click(function() {
//                    $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
//                    $(this).attr("clicked", "true");
//                });


                }
            );


        </script>
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
                                    $dayButtonStrings = dayButtonStrings($startEndTimestampList, 'http://coagmento.org/workintent/instruments/markTasks.php', $selectedStartTimeSeconds);
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
                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['sessions']."'>&laquo; Back (Sessions)</a>";
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                            echo "<a type=\"button\" class=\"btn btn-danger btn-lg\" href='".$actionUrls['query segments']."'>Next (Query Segments) &raquo;</a>";
                            ?>
                        </center>
                    </div>

                </div>
            </div>



        </div>

        <!--   Actions and Trash Bin    -->
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Choose a Query Segment to Mark for Intentions</h4></center>
                    </div>
                    <form id="select_querysegments_form" action="../services/utils/runPageQueryUtils.php?action=markTasks">
                    <div class="panel-body" id="select_querysegments_panel">


                        <?php
                        echo $markIntentionsPanels['intentionspanels_html'];
                        ?>


                    </div>
                        <center>
                            <input type="hidden" name="userID" <?php echo "value='$userID'";?>/>
                            <input type="hidden" name="startTimestamp" <?php echo "value='$selectedStartTimeSeconds'";?>/>
                            <input type="hidden" name="endTimestamp" <?php echo "value='$selectedEndTimeSeconds'";?>/>
                        </center>
                    </form>

                </div>
            </div>


            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <center><h4>Choose Your Intentions</h4></center>
                    </div>
                    <div class="panel-body" id="select_intentions_panel">

                        <?php
                            echo $intentionsPanel['intentionshtml'];
                        ?>


                    </div>

                </div>
            </div>
        </div>

        <?php
        echo $markIntentionsPanels['nullpanel_html'];
        ?>


    </div>
    </body>
    </html>
<?php
?>