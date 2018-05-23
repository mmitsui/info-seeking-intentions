<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");

$userID = $_GET['userID'];
$taskID = $_GET['taskID'];

$startTimestamp = 0;
$endTimestamp = strtotime('today 11:59 PM');
$day_log = getInterleavedPagesQueries($userID,$startTimestamp,$endTimestamp,0,-1,-1,$taskID);
$taskIDNameMap = getTaskIDNameMap($userID);


$cxn = Connection::getInstance();
$query = "(SELECT sessionID,querySegmentID FROM pages WHERE userID=$userID AND taskID=$taskID AND querySegmentID IS NOT NULL GROUP BY sessionID,querySegmentID) UNION (SELECT sessionID,querySegmentID FROM queries WHERE userID=$userID AND taskID=$taskID AND querySegmentID IS NOT NULL GROUP BY sessionID,querySegmentID)";
$result = $cxn->commit($query);

while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
    if(array_key_exists($line['sessionID'],$sessionData)){
        $sessionData[$line['sessionID']]['ct_searchsegments'] += 1;
    }else{
        $sessionData[$line['sessionID']]['ct_searchsegments'] = 1;
    }

}



?>

<html>
<head>
    <title>
        Task + Session Exit Interview
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./lib/bootstrap_notify/bootstrap-notify.min.js"></script>
    <script>



        function hideTaskPanel(ev){
            ev.preventDefault();
            $("#task_panel_body").slideUp('slow');
        }
        function showTaskPanel(ev){
            ev.preventDefault();
            $("#task_panel_body").slideDown('slow');
        }
        function copyTaskURL(ev){
            ev.preventDefault();

            var aux = document.createElement("input");
            aux.setAttribute("value", $("#copy_task_button").data('task-url'));
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);

            $.notify(
                {message:"URL copied to clipboard!"},
                {type: 'success'}
            );


        }

        function copySessionURL(ev){
            ev.preventDefault();

            var aux = document.createElement("input");
            aux.setAttribute("value", $(this).data('session-url'));
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);

            $.notify(
                {message:"URL copied to clipboard!"},
                {type: 'success'}
            );
        }


        function submitTaskExit(ev){
            ev.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/submitTaskExit.php',
                data: $('#task_interview_form').serialize()
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){
                    $.notify(
                        {message:"Task exit interview has been conducted successfully!"},
                        {type: 'success'}
                    );

                    $('#task_interview_modal').modal('toggle');
                    $('#task_interview_panel').removeClass('panel-primary').addClass('panel-success');
                    $('#task_interview_panel_heading').html("Task Interview Complete");
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






        function submitSessionExit(ev){
            ev.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/submitSessionExit.php',
                data: $('#session_interview_form').serialize()
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){

                    var sessionID = $('input[name="sessionID"]').val();

                    $('tr[data-table="session_table"][data-session-id="'+sessionID+'"]').addClass('success');

                    $.notify(
                        {message:"Session exit interview has been conducted successfully!"},
                        {type: 'success'}
                    );

                    $('#session_interview_modal').modal('toggle');
//                    $('#session_interview_panel').removeClass('panel-primary').addClass('panel-success');
//                    $('#session_interview_panel_heading').html("Session Interview Complete");
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




        $(document).ready(function(){
                $("#task_exit_submit_button").on('click',submitTaskExit);

                $("#session_exit_submit_button").on('click',submitSessionExit);
                $("#show_task_button").on('click',showTaskPanel);
                $("#hide_task_button").on('click',hideTaskPanel);
                $("#copy_task_button").on('click',copyTaskURL);

                $("button[name='session_interview_show_button']").on('click',function(){
                    $('input[name="sessionID"]').val($(this).data('session-id'));
//                    alert($('input[name="sessionID"]').val());
//                    $('#session_interview_modal').modal('show');
                });

                $("#task_panel_body").slideUp();
                $("#task_interview_modal").on('hidden.bs.modal', function () {
                    $(this).find('form').trigger('reset');
                });



                $("#session_interview_modal").on('hidden.bs.modal', function () {
                    $(this).find('form').trigger('reset');
                });
                $("button[name='session_url_copy_button']").on('click',copySessionURL);
//                $("button[name='session_url_copy_button']").on('click',function(ev){
//                    ev.preventDefault();
//                    copySessionURL($(this).data(''))
//                });
            }
        );

    </script>

</head>




<body>

<?php

if(count($day_log)<=0){
    $day_table = '<center><h3 class=\'bg-danger\'>You have not done anything today.  Please log some activity.</h3></center>';
}else{
    $day_table = "
        <table  class=\"table table-bordered table-fixed\">
                                <thead>
                                <tr>
                                    <!--<th >Time</th>-->
                                    <th >Type</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th >Title/Query</th>
                                    <th> Session ID</th>
                                    <th >Domain</th>
                                </tr>
                                </thead>
                                <tbody id='history_table'>";

    $table_row_index = 0;


    foreach($day_log as $page){
        $table_row_index += 1;

        $day_table .= "<tr data-table='day_table' data-table-row-index='$table_row_index'>";


        $name = '';
        $color = '';
        if($page['type']=='page'){
            $name='pages[]';
            $color = 'class="warning"';
        }else{
            $name='queries[]';
            $color = 'class="info"';
        }
        $value = $page['id'];



        $day_table .= "<td $color>".(isset($page['type'])?$page['type']:"")."</td>";
        $day_table .= "<td>".(isset($page['date'])?$page['date']:"")."</td>";
        $day_table .= "<td>".(isset($page['time'])?$page['time']:"")."</td>";





//        $day_table .= "<td>".(isset($page['taskID'])? $page['taskID'] :"")."</td>"; //TODO: FIX
//        $day_table .= "<td>".(isset($page['sessionID']) ?$page['sessionID'] : "")."</td>";
        $title = "";
        if($page['type']=='page'){
            if(isset($page['title'])){
                $title = $page['title'];
            }
        }else{
            if(isset($page['query'])){
                $title = $page['query'];
            }
        }
        $title_short = $title;
        if(strlen($title)>60 or strlen(trim($title))==0){
            $title_short = substr($page['title'],0,60)."...";
        }

        $day_table .= "<td><span title='$title'>$title_short</span></td>";
        $day_table .= "<td><span title='$title'>".$page['sessionID']."</span></td>";
        $day_table .= "<td><span title='".(isset($page['host'])?htmlentities($page['host']):"")."'>".(isset($page['host'])?htmlentities($page['host']):"")."</span></td>";

        $day_table .= "</tr>";

    }
    $day_table .= "</tbody>
                    </table>";





}

?>

<div class="container">
    <h1>Task + Session Interview for User <?php echo $userID;?>, Task <?php echo $taskID;?></h1>
    <h1><?php
        $button = "<a class='btn btn-info btn-lg' href='http://coagmento.org/workintent/userDataEntry.php?userID=$userID'>Back to User Overview</a>";
        echo $button;
        ?></h1>
</div>

<div class="container">
    <div class="well">
        <h3>We've identified a few information-seeking episodes that we'd like you to tell us a bit more about.</h3>
        <h3>(Show participant the task log.)</h3>
        <h3>You said that this search was done in order to accomplish the task "<?php echo $taskIDNameMap[$taskID];?>". Can you tell us a bit more about the task itself?</h3>
    </div>
</div>




<div class="container">
    <?php
        $cxn = Connection::getInstance();
        $result = $cxn->commit($query = "SELECT * FROM questionnaire_exit_tasks WHERE userID=$userID AND taskID=$taskID");
        if(mysql_num_rows($result)>=1){

            echo "<div id='task_interview_panel' data-task-id='$taskID' class=\"panel panel-success\">
        <div id='task_interview_panel_heading' class=\"panel-heading\">
            Task Interview Complete
        </div>";
        }else{
            echo "<div id='task_interview_panel' data-task-id='$taskID' class=\"panel panel-primary\">
        <div id='task_interview_panel_heading' class=\"panel-heading\">
            Task Interview
        </div>";
        }

    ?>

        <div class="panel-body">
            <center>
                <div class="btn-group-vertical">
                <?php
                echo "<button id='copy_task_button' class='btn btn-success' data-task-url='http://coagmento.org/workintent/getTask.php?userID=$userID&taskID=$taskID'>Copy Task URL to Clipboard</button>";
                ?>
                <button class='btn btn-primary' data-toggle="modal" data-target="#task_interview_modal">Conduct Task Interview</button>
                </div>
            </center>

        </div>
    </div>
</div>







<div class="container">
    <div class="panel panel-primary" >
        <div class="panel-heading">
            <?php
            echo "Task: ".$taskIDNameMap[$taskID];
            echo "&nbsp&nbsp<button class='btn btn-default' id='show_task_button'>Show Task Log</button>";
            echo "<button class='btn btn-default' id='hide_task_button'>Hide Task Log</button>";
            ?>

        </div>

        <div class="panel-body" id="task_panel_body">
            <?php
            echo $day_table;
            ?>
        </div>
    </div>
</div>




<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php
            echo "Sessions for Task";
            ?>
        </div>

        <div class="panel-body">
            <table  class="table table-bordered table-fixed">
                <thead>
                <tr>
                    <th >Session ID</th>
                    <th># Search Segments</th>
                    <th >Options</th>
                </tr>
                </thead>
                <tbody id='session_table'>

            <?php

            $sessions_intentionexit = array();
            $query = "SELECT sessionID FROM questionnaire_exit_sessions WHERE userID=$userID AND ((intention_clarifications IS NOT NULL) OR (intention_transitions IS NOT NULL))";
            $result = $cxn->commit($query);
            while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
                array_push($sessions_intentionexit,$line['sessionID']);
            }


                $query = "SELECT * FROM pages WHERE userID=$userID AND taskID=$taskID AND sessionID IS NOT NULL GROUP BY sessionID ORDER BY sessionID ASC";
                $result = $cxn->commit($query);
                while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
                    $sessionID = $line['sessionID'];
                    if(in_array($sessionID,$sessions_intentionexit)){
                        echo "<tr class='success' data-session-id='$sessionID' data-table='session_table' >";
                    }else{
                        echo "<tr data-session-id='$sessionID' data-table='session_table' >";
                    }
                    echo "<td>".$sessionID."</td>";
                    echo "<td>".$sessionData[$sessionID]['ct_searchsegments']."</td>";
                    $button1 = "<button name='session_url_copy_button' data-session-url='http://coagmento.org/workintent/getSession.php?userID=$userID&sessionID=$sessionID' class='btn btn-success'>Copy Session URL to Clipboard</button>";
                    $button2 = "<button data-session-id='$sessionID' name='session_interview_show_button' class='btn btn-primary' data-toggle=\"modal\" data-target=\"#session_interview_modal\">Conduct Session Interview</button>";
                    $button3 = "<button data-session-id='$sessionID' class='btn btn-default' onclick='window.open(\"http://coagmento.org/workintent/showIntentionsForSession.php?userID=$userID&sessionID=$sessionID&taskID=$taskID\",\"_blank\");'>View Intentions For Session</button>";
                    echo "<td><div class='btn-group-vertical'>$button3 $button1 $button2</div></td>";
                    echo "</tr>";
                }
            ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<div class="modal fade" id="task_interview_modal" tabindex="-1" role="dialog" aria-labelledby="task_interview_modal_label" style="width=100%">

    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<!--                <h4 class="modal-title" id="myModalLabel">What were your intentions for this search segment? Were they successful?</h4>-->
            </div>

            <form id="task_interview_form">
                <div class="modal-body">


                    <div class="form-group">
                        <label>What did you obtain, create, disseminate, or otherwise accomplish as a result of task
                            completion? If the task was not completed as a result of this information-seeking episode,
                            please say so, and also describe what was accomplished.</label>

                        <textarea class="form-control" rows="5" name="task_accomplishment" placeholder="Enter answer here."></textarea>
                    </div>

                    <div class="form-group">
                        <label>What stage are you in with regard to completing this task?</label>


                        <div class="radio">
                            <label><input type="radio" name="task_stage" value="1">1 (Starting)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="task_stage" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="task_stage" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="task_stage" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="task_stage" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="task_stage" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="task_stage" value="7">7 (Finished)</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>How would you describe the goal of the task?</label>


                        <div class="radio">
                            <label><input type="radio" name="goal" value="1">1 (Abstract)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="goal" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="goal" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="goal" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="goal" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="goal" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="goal" value="7">7 (Specific)</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>How would you rate the importance of task?</label>


                        <div class="radio">
                            <label><input type="radio" name="importance" value="1">1 (Unimportant)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="importance" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="importance" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="importance" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="importance" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="importance" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="importance" value="7">7 (Extremely)</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>How would you rate the urgency of task?</label>


                        <div class="radio">
                            <label><input type="radio" name="urgency" value="1">1 (No urgency)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="urgency" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="urgency" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="urgency" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="urgency" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="urgency" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="urgency" value="7">7 (Extremely)</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>How would you rate the difficulty of task?</label>


                        <div class="radio">
                            <label><input type="radio" name="difficulty" value="1">1 (Not difficult)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="difficulty" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="difficulty" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="difficulty" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="difficulty" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="difficulty" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="difficulty" value="7">7 (Extremely)</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>How would you rate the complexity of task?</label>


                        <div class="radio">
                            <label><input type="radio" name="complexity" value="1">1 (Not complex)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="complexity" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="complexity" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="complexity" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="complexity" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="complexity" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="complexity" value="7">7 (Extremely)</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>How would you rate your knowledge of the topic of this task?</label>


                        <div class="radio">
                            <label><input type="radio" name="knowledge_topic" value="1">1 (No knowledge)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_topic" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_topic" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_topic" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_topic" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_topic" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_topic" value="7">7 (Highly knowledgeable)</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>How would you rate your knowledge of procedures or methods for completing the task?</label>


                        <div class="radio">
                            <label><input type="radio" name="knowledge_procedures" value="1">1 (No knowledge)</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_procedures" value="2">2</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_procedures" value="3">3</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_procedures" value="4">4</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_procedures" value="5">5</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_procedures" value="6">6</label>
                        </div>

                        <div class="radio">
                            <label><input type="radio" name="knowledge_procedures" value="7">7 (Highly knowledgeable)</label>
                        </div>

                    </div>

                </div>
                <div>
                    <?php
                        echo "<input type='hidden' name='taskID' value='$taskID'/>";
                        echo "<input type='hidden' name='userID' value='$userID'/>";
                    ?>
                    <center>
                        <button class="btn btn-primary" id="task_exit_submit_button">Submit</button>
                        <button class="btn btn-default" data-dismiss="modal">Cancel</button>

                    </center>
                </div>
            </form>
        </div>
    </div>


</div>









<div class="modal fade" id="session_interview_modal" tabindex="-1" role="dialog" aria-labelledby="session_interview_modal_label" style="width=100%">

    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>

            <form id="session_interview_form">
                <div class="modal-body">
                    <h3>Now, we'd like to ask you some questions about the search session that you engaged in with respect
                        to this task. Please evaluate the search session by answering following questions:</h3>


                    <div class="form-group">
                        <label>[Ask specific questions about any intentions that need clarification]</label>

                        <textarea class="form-control" rows="5" name="intention_clarifications" placeholder="Clarifications"></textarea>
                    </div>

                    <div class="form-group">
                        <label>[If necessary, ask] Why did you go from this intention to the following intention?</label>

                        <textarea class="form-control" rows="5" name="intention_transitions" placeholder="Explanation"></textarea>
                    </div>



<!--                    <div class="form-group">-->
<!--                        <label>Was the search session successful?</label>-->
<!---->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="successful" value="1">1 (Not at all)</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="successful" value="2">2</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="successful" value="3">3</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="successful" value="4">4</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="successful" value="5">5</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="successful" value="6">6</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="successful" value="7">7 (Completely)</label>-->
<!--                        </div>-->
<!---->
<!--                    </div>-->
<!---->
<!--                    <div class="form-group">-->
<!--                        <label>[FOR ANY ANSWER 4 OR LESS] Why do you say this?</label>-->
<!---->
<!--                        <textarea class="form-control" rows="5" name="successful_description" placeholder="Explanation"></textarea>-->
<!--                    </div>-->
<!---->
<!--                    <div class="form-group">-->
<!--                        <label>Was the search session problematic?</label>-->
<!---->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="problematic" value="1">1 (Not at all)</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="problematic" value="2">2</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="problematic" value="3">3</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="problematic" value="4">4</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="problematic" value="5">5</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="problematic" value="6">6</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="problematic" value="7">7 (Completely)</label>-->
<!--                        </div>-->
<!---->
<!--                    </div>-->
<!---->
<!--                    <div class="form-group">-->
<!--                        <label>[FOR ANY ANSWER 4 OR GREATER] Why do you say this?</label>-->
<!---->
<!--                        <textarea class="form-control" rows="5" name="problematic_description" placeholder="Explanation"></textarea>-->
<!--                    </div>-->
<!---->
<!--                    <div class="form-group">-->
<!--                        <label>Was the search session useful to accomplish the task?</label>-->
<!---->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="useful" value="1">1 (Unimportant)</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="useful" value="2">2</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="useful" value="3">3</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="useful" value="4">4</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="useful" value="5">5</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="useful" value="6">6</label>-->
<!--                        </div>-->
<!---->
<!--                        <div class="radio">-->
<!--                            <label><input type="radio" name="useful" value="7">7 (Extremely)</label>-->
<!--                        </div>-->
<!---->
<!--                    </div>-->
<!---->
<!--                    <div class="form-group">-->
<!--                        <label>[FOR ANY ANSWER 4 OR LESS] Why do you say this?</label>-->
<!---->
<!--                        <textarea class="form-control" rows="5" name="useful_description" placeholder="Explanation"></textarea>-->
<!--                    </div>-->



                </div>
                <div>
                    <?php
                    echo "<input type='hidden' name='sessionID' value=''/>";
                    echo "<input type='hidden' name='taskID' value='$taskID'/>";
                    echo "<input type='hidden' name='userID' value='$userID'/>";
                    ?>
                    <center>
                        <button class="btn btn-primary" id="session_exit_submit_button">Submit</button>
                        <button class="btn btn-default" id='' data-dismiss="modal">Cancel</button>

                    </center>
                </div>
            </form>
        </div>
    </div>


</div>




</body>
</html>