<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");


if(!isset($_GET['userID'])){
    echo "You must specify a user ID!";
    exit();
}
$userID = $_GET['userID'];

$startTimestamp = 0;
$endTimestamp = strtotime('today midnight')+86400;

$taskIDNameMap = getTaskIDNameMap($userID);

$taskName = "<Not Assigned to Task>";
$taskData = array();


?>



<html>
<head>
    <title>
        User Data Entry
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./lib/bootstrap_notify/bootstrap-notify.min.js"></script>


    <script>

        function submitToolExit(ev){
            ev.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/submitToolExit.php',
                data: $('#tool_interview_form').serialize()
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){
                    $.notify(
                        {message:"Tool exit interview has been conducted successfully!"},
                        {type: 'success'}
                    );

                    $('#tool_interview_modal').modal('toggle');
                    $('#tool_interview_panel').removeClass('panel-primary').addClass('panel-success');
                    $('#tool_interview_panel_heading').html("<h4>Tool Exit Interview Complete</h4>");
                }else{
                    $.notify(
                        {message:response['message']},
                        {type: 'danger'}
                    );
                }
            }).fail(function(data) {
                $.notify(
                    {message:"Something went wrong.  Please try again."},
                    {type: 'danger'}
                );
            });

        }


        function viewExitProgress(ev){
            ev.preventDefault();
            var taskID = $(this).data('task-id');
            var userID = $('input[name="userID"]').val();
            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/getTaskExitProgress.php',
                data: 'userID='+userID+"&taskID="+taskID
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){


                    if(response['taskcomplete'] && response['sessionscomplete']){
                        $.notify(
                            {message:'Tasks and session interviews are both complete.'},
                            {type: 'success'}
                        );
                    }else{
                        var s = "";
                        if(response['taskcomplete']){
                            s += "Task complete, ";
                        }else{
                            s += "Task not complete, ";
                        }

                        if(response['sessionscomplete']){
                            s += "Sessions complete.";
                        }else{
                            s += "("+response['n_done_sessions']+"/"+response['n_total_sessions']+") sessions complete.";
                        }

                        $.notify(
                            {message:s},
                            {type: 'warning'}
                        );



                    }


                }else{

                    $.notify(
                        {message:response['message']},
                        {type: 'danger'}
                    );

                }

            }).fail(function(data) {
                $.notify(
                    {message:"Something went wrong.  Please try again."},
                    {type: 'danger'}
                );
            });
        }
        function ajaxEmail(email_url,formData,success_message,fail_message){
            $.ajax({
                type: 'POST',
                url: email_url,
                data: formData
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){


                    $.notify(
                        {message:success_message},
                        {type: 'success'}
                    );

                }else{

                    $.notify(
                        {message:fail_message},
                        {type: 'danger'}
                    );

                }

            }).fail(function(data) {
                $.notify(
                    {message:fail_message},
                    {type: 'danger'}
                );
            });
        }

        function selectTasksForInterview(ev){
            ev.preventDefault();

            var formData = $('#tasks_form').serialize();
            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/selectTasksForInterview.php',
                data: formData
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){
                    $.notify(
                        {message:'Tasks selected!'},
                        {type: 'success'}
                    );



                    var taskIDs = [];

//                    $.grep(begin_labels, function(el) {
//                        if ($.inArray(el, end_labels) == -1 && $.inArray(el, unfinishedLabels) == -1){
//                            unfinishedLabels.push(el);
//                        }
//                    });

                    $("#history_table input:checkbox:checked").map(function(){
                        taskIDs.push(parseInt($(this).val()));
                    }).get();

                    $("#history_table span[name='taskIDs_span']").html("");

                    $("#history_table tr").removeClass('active');
                    $("#history_table tr").filter(function(){
                        return $.inArray(parseInt($(this).data('task-id')),taskIDs)!=-1;
                        }
                    ).addClass('active');

                    $("#history_table button").filter(function(){
                            return $.inArray(parseInt($(this).data('task-id')),taskIDs)!=-1;
                        }
                    ).show();
                    $("#history_table button").filter(function(){
                            return $.inArray(parseInt($(this).data('task-id')),taskIDs)==-1;
                        }
                    ).hide();


                    $("#history_table span[name='taskIDs_span']").filter(function(){
                            return $.inArray(parseInt($(this).data('task-id')),taskIDs)!=-1;
                        }
                    ).html('Selected');

                }else if(response.hasOwnProperty('success') && !response['success']){
                    $.notify(
                        {message:response['message']},
                        {type: 'danger'}
                    );
                }else{

                    $.notify(
                        {message:"Something went wrong.  Please check your input."},
                        {type: 'danger'}
                    );

                }

            }).fail(function(data) {
                $.notify(
                    {message:"Something went wrong.  Please check your input."},
                    {type: 'danger'}
                );
            });
        }

        function emailExtension(ev){
            ev.preventDefault();
            $('input[name="emailType"]').val('extension');
            var formData = $('#email_form').serialize();
            ajaxEmail("http://coagmento.org/workintent/sendEmails.php",formData,'Extension instructions sent!','Extension instructions not sent.');
        }

        function emailEntry(ev){
            ev.preventDefault();
            $('input[name="emailType"]').val('entryinterview');
            var formData = $('#email_form').serialize();
            ajaxEmail("http://coagmento.org/workintent/sendEmails.php",formData,'Entry interview reminder sent!','Entry interview reminder not sent.');

        }

        function emailExit(ev){
            ev.preventDefault();
            $('input[name="emailType"]').val('exitinterview');
            var formData = $('#email_form').serialize();
            ajaxEmail("http://coagmento.org/workintent/sendEmails.php",formData,'Exit interview reminder sent!','Exit interview reminder not sent.');

        }

        function assignResearcher(ev){
            ev.preventDefault();


            var formData = $('#assign_researcher_form').serialize();
            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/assignResearcher.php',
                data: formData
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){
                    $.notify(
                        {message:"New researcher assigned!"},
                        {type: 'success'}
                    );
                    $("#researcher_name").html(response['researcher']);
                    $('input[name="researcher"]').val(response['researcher']);
                }else{
                    $.notify(
                        {message:"New researcher not assigned."},
                        {type: 'danger'}
                    );
                }
            }).fail(function(data) {
                $.notify(
                    {message:"New researcher not assigned."},
                    {type: 'danger'}
                );
            });

        }


        $(document).ready(function(){
                $("#tool_exit_submit_button").on('click',submitToolExit);
            $("#tool_interview_modal").on('hidden.bs.modal', function () {
                $(this).find('form').trigger('reset');
            });

                $("#extension_email_button").on('click',emailExtension);
                $("#entry_email_button").on('click',emailEntry);
                $("#exit_email_button").on('click',emailExit);
                $("#assign_researcher_button").on('click',assignResearcher);
                $("#select_tasks_button").on('click',selectTasksForInterview);
                $("button[name='progress_button']").on('click',viewExitProgress);
            }
        );
    </script>


</head>




<body>

<?php

$cxn = Connection::getInstance();
$query = "SELECT taskID,COUNT(*) as ct FROM pages WHERE userID=$userID GROUP BY taskID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $taskData[$line['taskID']] = array();
    $taskData[$line['taskID']]['ct_pages'] = $line['ct'];
}


$query = "SELECT taskID,COUNT(*) as ct FROM queries WHERE userID=$userID GROUP BY taskID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $taskData[$line['taskID']]['ct_queries'] = $line['ct'];
}


$query = "SELECT taskID,COUNT(DISTINCT(querySegmentID)) as ct FROM pages WHERE userID=$userID AND querySegmentID IS NOT NULL GROUP BY taskID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $taskData[$line['taskID']]['ct_searchsegments'] = $line['ct'];
}



$query = "SELECT taskID,COUNT(DISTINCT(sessionID)) as ct FROM pages WHERE userID=$userID AND sessionID IS NOT NULL GROUP BY taskID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    $taskData[$line['taskID']]['ct_sessions'] = $line['ct'];
}

$query = "SELECT * from recruits where userID=$userID";
$result = $cxn->commit($query);
$line = mysql_fetch_array($result,MYSQL_ASSOC);
$researcher = $line['experimenter'];
$tasks_selected = array();
$query = "SELECT * FROM task_labels_user WHERE userID=$userID";
$result = $cxn->commit($query);
while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    if($line['exitinterview']==1){
        array_push($tasks_selected,$line['taskID']);
    }
}

?>
<div class="container">
    <h1>User <?php echo $userID;?></h1>
</div>

<div class="container">


    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4>Designate an Experimenter</h4>
        </div>
        <div class="panel-body">
            <form id="assign_researcher_form">
                <center>
                    <strong><div>Current Researcher: <span id="researcher_name"><?php echo $researcher;?></span></div></strong>
                    <?php
                    echo "<input type='hidden' name='userID' value='$userID'/>";
                    ?>
                    <div class="radio">
                        <label><input type="radio" name="researcher_radio" value="Matt">Matt</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="researcher_radio" value="Eun">Eun</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="researcher_radio" value="Jiqun">Jiqun</label>
                    </div>
                    <button id="assign_researcher_button" class="btn btn-default">Assign Researcher</button>
                </center>
            </form>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4>Send Study E-mails</h4>
        </div>
        <div class="panel-body">

            <?php
            $button_extensionemail = "<button id='extension_email_button' class=\"btn btn-default\">Send Extension Credentials</button>";
            $button_entryinterview = "<button id='entry_email_button' class=\"btn btn-default\">Send Entry Interview Reminder</button>";
            $button_exitinterview = "<button id='exit_email_button'  class=\"btn btn-default\">Send Exit Interview Reminder</button>";
            ?>





                <form id="email_form">
                    <center>
                    <?php
                    echo "<input type='hidden' name='researcher' value='$researcher'/>";
                    echo "<input type='hidden' name='userID' value='$userID'/>";
                    echo "<input type='hidden' name='emailType' value=''/>";
                    echo "<div class='btn-group-vertical'>";
                    echo "$button_entryinterview";
                    echo "$button_extensionemail";
                    echo "$button_exitinterview";
                    echo "</div>";

                    ?>
                    </center>
                </form>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4>Entry Interviews</h4>
        </div>
        <div class="panel-body">
            <center>
                <div class="btn-group-vertical">
            <?php

            $badge1=$badge2="";
            $query = "SELECT * FROM questionnaire_entry_demographic WHERE userID=$userID";
            $result = $cxn->commit($query);
            if(mysql_num_rows($result)>0){
                $badge1 = "<span class=\"badge badge-default\"><i class='fa fa-check'></i> Done</span>";
            }

            $query = "SELECT * FROM task_labels_user WHERE userID=$userID";
            $result = $cxn->commit($query);
            $badge2 = "<span class=\"badge badge-default\">".mysql_num_rows($result)." Tasks</span>";
            echo "<button class=\"btn btn-success\" onclick='window.open(\"http://coagmento.org/workintent/demographicSurvey.php?userID=$userID\",\"_blank\");'>Go To Demographic Survey $badge1</button>";
            echo "<button class=\"btn btn-success\" onclick='window.open(\"http://coagmento.org/workintent/tasksSurvey.php?userID=$userID\",\"_blank\");'>Go To Tasks Survey $badge2</button>";
            ?>
                </div>
            </center>
        </div>
    </div>



</div>

<div class="container">
    <?php
    $cxn = Connection::getInstance();
    $result = $cxn->commit($query = "SELECT * FROM questionnaire_exit_tool WHERE userID=$userID");
    if(mysql_num_rows($result)>=1){

        echo "<div id='tool_interview_panel' data-task-id='$taskID' class=\"panel panel-success\">
        <div id='tool_interview_panel_heading' class=\"panel-heading\">
            <h4>Tool Exit Interview Complete</h4>
        </div>";
    }else{
        echo "<div id='tool_interview_panel' data-task-id='$taskID' class=\"panel panel-primary\">
        <div id='tool_interview_panel_heading' class=\"panel-heading\">
            <h4>Tool Exit Interview</h4>
        </div>";
    }

    ?>

    <div class="panel-body">
        <center>
            <div><button class='btn btn-success' data-toggle="modal" data-target="#tool_interview_modal">Conduct Tool Exit Interview</button></div>
        </center>

    </div>
</div>
</div>

<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
           <h4>Select Tasks for Interview</h4>
        </div>


        <form id="tasks_form">
        <div class="panel-body">
            <table  class="table table-bordered table-fixed">
                <thead>
                <tr>
                    <th >Select for Interview</th>
                    <th >Task ID</th>
                    <th ># Pages</th>
                    <th ># Search Segments</th>
                    <th># Sessions</th>
                    <th>Options</th>
                </tr>
                </thead>
                <tbody id='history_table'>
                <?php
                foreach($taskData as $taskID=>$taskInfo){
                    if(!is_null($taskID) && $taskID!='' && in_array($taskID,$tasks_selected)){
                        echo "<tr class='active' data-task-id='$taskID'>";
                    }else{
                        echo "<tr data-task-id='$taskID'>";

                    }

                    if(!is_null($taskID) and $taskID != ''){
                        if(!is_null($taskID) && $taskID!='' && in_array($taskID,$tasks_selected)){
                            echo "<td><center><input style='cursor:pointer;zoom:1.6;'type='checkbox' name='taskIDs[]' value='$taskID' ><span name='taskIDs_span' data-task-id='$taskID'>Selected</span></center></td>";
                        }else{
                            echo "<td><center><input style='cursor:pointer;zoom:1.6;'type='checkbox' name='taskIDs[]' value='$taskID' ><span name='taskIDs_span' data-task-id='$taskID'></span></center></td>";

                        }

                    }else{
                        echo "<td></td>";
                    }
                    if(is_null($taskID) or $taskID==''){
                        echo "<td>(No Task)</td>";
                    }else{
                        echo "<td>".$taskID."</td>";
                    }

                    echo "<td>".$taskInfo['ct_pages']."</td>";
                    echo "<td>".$taskInfo['ct_searchsegments']."</td>";
                    echo "<td>".$taskInfo['ct_sessions']."</td>";
                    $button1 = $button2 = $button3 = "";
                    if(!is_null($taskID) and $taskID != ''){
                        $url = "http://www.coagmento.org/workintent/taskAndSessionExitInterview.php?userID=$userID&taskID=$taskID";
                        if(!is_null($taskID) && $taskID!='' && in_array($taskID,$tasks_selected)){
                            $button1 = "<button name='progress_button' class=\"btn btn-primary\" data-task-id='$taskID' >View Exit Interview Progress</button>";
                            $button2 = "<button class=\"btn btn-success\" data-task-id='$taskID' onclick='window.open(\"$url\",\"_blank\");'>Go To Interview</button>";
                        }else{
                            $button1 = "<button name='progress_button' style='display:none' class=\"btn btn-primary\" data-task-id='$taskID'>View Exit Interview Progress</button>";
                            $button2 = "<button style='display:none' class=\"btn btn-success\" data-task-id='$taskID' onclick='window.open(\"$url\",\"_blank\");'>Go To Interview</button>";
                        }
                    }

                    echo "<td> <div class='btn-group-vertical'>$button1 $button2 $button3</div></td>";

                    echo "</tr>";
                }
                ?>

                </tbody>
            </table>


        </div>
        <div class="panel-footer">
            <center>
                <?php
                    echo "<input type='hidden' name='userID' value='$userID'/>";
                ?>
                <button id='select_tasks_button' class="btn btn-warning">Update Selection</button>
            </center>
        </div>
        </form>
    </div>
</div>


<div class="modal fade" id="tool_interview_modal" tabindex="-1" role="dialog" aria-labelledby="tool_interview_modal_label" style="width=100%">

    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >Tool Exit Interview</h4>
            </div>

            <form id="tool_interview_form">
                <div class="modal-body">




                    <div class="form-group">
                        <label>Was the process of log review and annotation clear?</label>


                        <div class="radio">
                            <label><input type="radio" name="reviewannotation_clear" value="1">1 (Not at all)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="reviewannotation_clear" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="reviewannotation_clear" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="reviewannotation_clear" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="reviewannotation_clear" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="reviewannotation_clear" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="reviewannotation_clear" value="7">7 (Completely)</label>
                        </div>

                    </div>

                    <p>Please evaluate the intentions provided in the annotation part:</p>

                    <div class="form-group">
                        <label>Was the set of intentions that you could choose from understandable?</label>


                        <div class="radio">
                            <label><input type="radio" name="intentions_understandable" value="1">1 (Not at all)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_understandable" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_understandable" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_understandable" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_understandable" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_understandable" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_understandable" value="7">7 (Completely)</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>Was the set of intentions that you could choose from adequate?</label>


                        <div class="radio">
                            <label><input type="radio" name="intentions_adequate" value="1">1 (Unimportant)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_adequate" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_adequate" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_adequate" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_adequate" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_adequate" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="intentions_adequate" value="7">7 (Extremely)</label>
                        </div>

                    </div>



                </div>
                <div>
                    <?php
                    echo "<input type='hidden' name='taskID' value='$taskID'/>";
                    echo "<input type='hidden' name='userID' value='$userID'/>";
                    ?>
                    <center>
                        <button class="btn btn-primary" id="tool_exit_submit_button">Submit</button>
                        <button class="btn btn-default" data-dismiss="modal">Cancel</button>

                    </center>
                </div>
            </form>
        </div>
    </div>


</div>


</body>
</html>