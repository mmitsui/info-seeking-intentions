<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/getSummaryData.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/updateSummaryData.php");






//updateSummaryData('study');
$summary = getSummaryData('study');

?>



<html>
<head>
    <title>
        Study Summary
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./lib/bootstrap_notify/bootstrap-notify.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>


    <style>


        .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            transition: 0.0s;
            padding-top: 60px;
        }

        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 18px;
            color: #818181;
            display: block;
            transition: 0.0s
        }

        .sidenav a:hover, .offcanvas a:focus{
            color: #f1f1f1;
        }

        .sidenav .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        #main {
            transition: margin-left .0s;
            padding: 16px;
        }

        @media screen and (max-height: 450px) {
            .sidenav {padding-top: 15px;}
            .sidenav a {font-size: 18px;}
        }
    </style>



    <script>


        var barchartdata_reviewannotation_clear = <?php echo json_encode($summary['interviewdata_exittool']['barchartdata_reviewannotation_clear']); ?>;
        var barchartdata_intentions_understandable = <?php echo json_encode($summary['interviewdata_exittool']['barchartdata_intentions_understandable']); ?>;
        var barchartdata_intentions_adequate = <?php echo json_encode($summary['interviewdata_exittool']['barchartdata_intentions_adequate']); ?>;

        var barchartdata_task_stage = <?php echo json_encode($summary['interviewdata_exittasks']['barchartdata_task_stage']); ?>;
        var barchartdata_goal = <?php echo json_encode($summary['interviewdata_exittasks']['barchartdata_goal']); ?>;
        var barchartdata_importance = <?php echo json_encode($summary['interviewdata_exittasks']['barchartdata_importance']); ?>;
        var barchartdata_urgency = <?php echo json_encode($summary['interviewdata_exittasks']['barchartdata_urgency']); ?>;
        var barchartdata_difficulty = <?php echo json_encode($summary['interviewdata_exittasks']['barchartdata_difficulty']); ?>;
        var barchartdata_complexity = <?php echo json_encode($summary['interviewdata_exittasks']['barchartdata_complexity']); ?>;
        var barchartdata_knowledge_topic = <?php echo json_encode($summary['interviewdata_exittasks']['barchartdata_knowledge_topic']); ?>;
        var barchartdata_knowledge_procedures = <?php echo json_encode($summary['interviewdata_exittasks']['barchartdata_knowledge_procedures']); ?>;


        var barchartdata_successful= <?php echo json_encode($summary['interviewdata_exitsessions']['barchartdata_successful']); ?>;
        var barchartdata_useful= <?php echo json_encode($summary['interviewdata_exitsessions']['barchartdata_useful']); ?>;


        var barchartdata_intention_distribution= <?php echo json_encode($summary['intention_distribution']['barchartdata_intentions']); ?>;









        var chart_reviewannotation_clear;
        var chart_intentions_understandable;
        var chart_intentions_adequate;


        var chart_task_stage;
        var chart_goal;
        var chart_importance;
        var chart_urgency;
        var chart_difficulty;
        var chart_complexity;
        var chart_knowledge_topic;
        var chart_knowledge_procedures;


        var chart_successful;
        var chart_useful;

        var chart_intention_distribution;




        var assign_chart = function(element_id,chartdata,text){
            var c;
            if(element_id=='canvas_intention_distribution'){
                c = new Chart(document.getElementById(element_id).getContext("2d"),
                    {
                        type: 'bar',
                        data: chartdata,
                        options: {
                            responsive: true,
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: text
                            },
                            scales : {
                                xAxes: [{
                                    ticks: {
                                        autoSkip: false,
                                    }
                                }]
                            }
                        }
                    }
                );


            }else{

                c = new Chart(document.getElementById(element_id).getContext("2d"),
                    {
                        type: 'bar',
                        data: chartdata,
                        options: {
                            responsive: true,
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: text
                            }
                        }
                    }
                );

            }


            return c;


        }

        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            document.getElementById("main").style.marginLeft = "250px";
        }



        function addData_id(chart, new_dataset) {
//        function addData(chart, label, data) {
//            chart.data.labels.push(label);

//            chart.data.datasets.push(data);


            var i = 0;
            chart.data.datasets.forEach((dataset) => {


                for(j=0; j < new_dataset[i].data.length;j++){
                dataset.data.push(new_dataset[i].data[j]);
                }

            i += 1;

        });
            chart.update();
        }

        function removeData_id(chart) {
//            chart.data.labels.pop();
//            chart.data.datasets.pop();


            chart.data.datasets.forEach((dataset) => {
                while(dataset.data.length > 0){
                dataset.data.pop();
            }

//                dataset.data.pop();
        });
        }


        function addData(chart, data) {
//        function addData(chart, label, data) {
//            chart.data.labels.push(label);

//            chart.data.datasets.push(data);

            chart.data.datasets.forEach((dataset) => {

                for(i=0; i < data.length;i++){
                    dataset.data.push(data[i]);
                }
            });
            chart.update();
        }

        function removeData(chart) {
//            chart.data.labels.pop();
//            chart.data.datasets.pop();


            chart.data.datasets.forEach((dataset) => {
                while(dataset.data.length > 0){
                    dataset.data.pop();
                }

//                dataset.data.pop();
            });
        }

        function updateStatistics(ev){
            $("#update_loading_spinner").show();

            ev.preventDefault();

            function addZero(i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }

            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/updateSummaryData.php',
                data: 'summaryType=study&ajax_call=update'
            }).done(function(response) {

                response = JSON.parse(response);
                var d = new Date(response.lastupdate_timestamp*1000);
                $("span[name='lastupdate_timestamp']").text(addZero(d.getMonth()+1)+"-"+d.getDate()+"-"+d.getFullYear()+" "+addZero(d.getHours())+":"+addZero(d.getMinutes())+":"+addZero(d.getSeconds()));

                $("span[name='study_completion_registered']").text(response.study_completion.registered).css('background-color','aqua');
                $("span[name='study_completion_completed']").text(response.study_completion.completed).css('background-color','aqua');
                $("span[name='study_completion_running']").text(response.study_completion.running).css('background-color','aqua');
                $("span[name='study_completion_open_registrations']").text(response.study_completion.open_registrations).css('background-color','aqua');


                $("span[name='count_tasks_count']").text(response.count_tasks.count).css('background-color','aqua');
                $("span[name='count_taskquestionnaires_count']").text(response.count_taskquestionnaires.count).css('background-color','aqua');
                $("span[name='rate_peruser_taskquestionnaires']").text(response.rate_peruser_taskquestionnaires).css('background-color','aqua');
                $("span[name='count_sessions_count']").text(response.count_sessions_count).css('background-color','aqua');
                $("span[name='count_sessionquestionnaires_count']").text(response.count_sessionquestionnaires.count).css('background-color','aqua');
                $("span[name='rate_peruser_sessionquestionnaires']").text(response.rate_peruser_sessionquestionnaires).css('background-color','aqua');
                $("span[name='count_intentions_count']").text(response.count_intentions.count).css('background-color','aqua');
                $("span[name='count_intentionquestionnaires_count']").text(response.count_intentionquestionnaires.count).css('background-color','aqua');
                $("span[name='rate_peruser_intentionquestionnaires']").text(response.rate_peruser_intentionquestionnaires).css('background-color','aqua');


                $("span[name='interviewdata_exittool_mean_reviewannotation_clear']").text(response.interviewdata_exittool.mean_reviewannotation_clear).css('background-color','aqua');
                $("span[name='interviewdata_exittool_std_reviewannotation_clear']").text(response.interviewdata_exittool.std_reviewannotation_clear).css('background-color','aqua');
                $("span[name='interviewdata_exittool_mean_intentions_understandable']").text(response.interviewdata_exittool.mean_intentions_understandable).css('background-color','aqua');
                $("span[name='interviewdata_exittool_std_intentions_understandable']").text(response.interviewdata_exittool.std_intentions_understandable).css('background-color','aqua');
                $("span[name='interviewdata_exittool_mean_intentions_adequate']").text(response.interviewdata_exittool.mean_intentions_adequate).css('background-color','aqua');
                $("span[name='interviewdata_exittool_std_intentions_adequate']").text(response.interviewdata_exittool.std_intentions_adequate).css('background-color','aqua');

                $("span[name='interviewdata_exittasks_mean_task_accomplishment']").text(response.interviewdata_exittasks.mean_task_accomplishment).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_task_accomplishment']").text(response.interviewdata_exittasks.std_task_accomplishment).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_mean_task_stage']").text(response.interviewdata_exittasks.mean_task_stage).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_task_stage']").text(response.interviewdata_exittasks.std_task_stage).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_mean_goal']").text(response.interviewdata_exittasks.mean_goal).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_goal']").text(response.interviewdata_exittasks.std_goal).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_mean_importance']").text(response.interviewdata_exittasks.mean_importance).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_importance']").text(response.interviewdata_exittasks.std_importance).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_mean_urgency']").text(response.interviewdata_exittasks.mean_urgency).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_urgency']").text(response.interviewdata_exittasks.std_urgency).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_mean_difficulty']").text(response.interviewdata_exittasks.mean_difficulty).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_difficulty']").text(response.interviewdata_exittasks.std_difficulty).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_mean_complexity']").text(response.interviewdata_exittasks.mean_complexity).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_complexity']").text(response.interviewdata_exittasks.std_complexity).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_mean_knowledge_topic']").text(response.interviewdata_exittasks.mean_knowledge_topic).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_knowledge_topic']").text(response.interviewdata_exittasks.std_knowledge_topic).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_mean_knowledge_procedures']").text(response.interviewdata_exittasks.mean_knowledge_procedures).css('background-color','aqua');
                $("span[name='interviewdata_exittasks_std_knowledge_procedures']").text(response.interviewdata_exittasks.std_knowledge_procedures).css('background-color','aqua');

                $("span[name='interviewdata_exitsessions_mean_successful']").text(response.interviewdata_exitsessions.mean_successful).css('background-color','aqua');
                $("span[name='interviewdata_exitsessions_std_successful']").text(response.interviewdata_exitsessions.std_successful).css('background-color','aqua');
                $("span[name='interviewdata_exitsessions_mean_useful']").text(response.interviewdata_exitsessions.mean_useful).css('background-color','aqua');
                $("span[name='interviewdata_exitsessions_std_useful']").text(response.interviewdata_exitsessions.std_useful).css('background-color','aqua');





//                alert(JSON.stringify(response.interviewdata_exittool.barchartdata_intentions_adequate.datasets));
//                alert(JSON.stringify(response.interviewdata_exittool.barchartdata_reviewannotation_clear.datasets));

                removeData(chart_reviewannotation_clear);
                addData(chart_reviewannotation_clear,response.interviewdata_exittool.barchartdata_reviewannotation_clear.datasets[0].data);
                removeData(chart_intentions_understandable);
                addData(chart_intentions_understandable,response.interviewdata_exittool.barchartdata_intentions_understandable.datasets[0].data);
                removeData(chart_intentions_adequate);
                addData(chart_intentions_adequate,response.interviewdata_exittool.barchartdata_intentions_adequate.datasets[0].data);




                removeData(chart_task_stage);
                addData(chart_task_stage,response.interviewdata_exittasks.barchartdata_task_stage.datasets[0].data);
                removeData(chart_goal);
                addData(chart_goal,response.interviewdata_exittasks.barchartdata_goal.datasets[0].data);
                removeData(chart_importance);
                addData(chart_importance,response.interviewdata_exittasks.barchartdata_importance.datasets[0].data);
                removeData(chart_urgency);
                addData(chart_urgency,response.interviewdata_exittasks.barchartdata_urgency.datasets[0].data);
                removeData(chart_difficulty);
                addData(chart_difficulty,response.interviewdata_exittasks.barchartdata_difficulty.datasets[0].data);
                removeData(chart_complexity);
                addData(chart_complexity,response.interviewdata_exittasks.barchartdata_complexity.datasets[0].data);
                removeData(chart_knowledge_topic);
                addData(chart_knowledge_topic,response.interviewdata_exittasks.barchartdata_knowledge_topic.datasets[0].data);
                removeData(chart_knowledge_procedures);
                addData(chart_knowledge_procedures,response.interviewdata_exittasks.barchartdata_knowledge_procedures.datasets[0].data);


                removeData(chart_successful);
                addData(chart_successful,response.interviewdata_exitsessions.barchartdata_successful.datasets[0].data);
                removeData(chart_useful);
                addData(chart_useful,response.interviewdata_exitsessions.barchartdata_useful.datasets[0].data);

                removeData_id(chart_intention_distribution);
                addData_id(chart_intention_distribution,response.intention_distribution.barchartdata_intentions.datasets);





                $.notify(
                    {message:"Stats and charts have been udpated!"},
                    {type: 'success'}
                );

                $("#update_loading_spinner").hide();

            }).fail(function(data) {

                $.notify(
                    {message:"Something went wrong.  Please try again."},
                    {type: 'danger'}
                );
                $("#update_loading_spinner").hide();
            });


        }

        $(document).ready(function(){


            chart_reviewannotation_clear = assign_chart('canvas_reviewannotation_clear',barchartdata_reviewannotation_clear,'Review Annotation Clear');
            chart_intentions_understandable = assign_chart('canvas_intentions_understandable',barchartdata_intentions_understandable,'Intentions Understandable');
            chart_intentions_adequate = assign_chart('canvas_intentions_adequate',barchartdata_intentions_adequate,'Intentions Adequate');

            chart_task_stage = assign_chart('canvas_task_stage',barchartdata_task_stage,'Task Stage');
            chart_goal = assign_chart('canvas_goal',barchartdata_goal,'Goal');
            chart_importance = assign_chart('canvas_importance',barchartdata_importance,'Importance');
            chart_urgency = assign_chart('canvas_urgency',barchartdata_urgency,'Urgency');
            chart_difficulty = assign_chart('canvas_difficulty',barchartdata_difficulty,'Difficulty');
            chart_complexity = assign_chart('canvas_complexity',barchartdata_complexity,'Complexity');
            chart_knowledge_topic = assign_chart('canvas_knowledge_topic',barchartdata_knowledge_topic,'Knowledge Topic');
            chart_knowledge_procedures = assign_chart('canvas_knowledge_procedures',barchartdata_knowledge_procedures,'Knowledge Procedures');

            chart_successful = assign_chart('canvas_successful',barchartdata_successful,'Successful');
            chart_useful = assign_chart('canvas_useful',barchartdata_useful,'Useful');


            chart_intention_distribution = assign_chart('canvas_intention_distribution',barchartdata_intention_distribution,'Intention Distribution');



            $( "select[name=usersummary_navigation]" )
                .change(function () {
                    var userID = "";
                    $( "select[name=usersummary_navigation] option:selected" ).each(function() {
                        if($( this ).text() != 'Navigate to User:'){
                            userID += $( this ).text();
                        }

                    });
                    if(userID!=''){
                        window.open("http://coagmento.org/workintent/showUserSummary.php?userID="+userID,"_blank");
                    }
                });

            $("#update_button").on('click',updateStatistics);







                openNav();
            }
        );
    </script>


</head>




<body>


<div id="mySidenav" class="sidenav">
    <a href="#update_navigation_panel">Update + User Navigation</a>
    <a href="#progress_panel_heading">Study Progress</a>
    <a href="#exit_interview_panel_heading">Exit Interview Summary</a>
    <a href="#annotation_panel_heading">Annotation Interview Summary</a>
    <a href="#tasks_interview_panel_heading">Tasks Interview Summary</a>
    <a href="#sessions_interview_panel_heading">Sessions Interview Summary</a>
    <a href="#intentions_panel_heading">Intentions Summary</a>
</div>



<div id="main">
    <?php







    ?>
    <div class="container">
        <center><h1>Study Summary</h1></center>
    </div>





    <div class="container">


        <div class="panel panel-primary">
            <div class="panel-heading" id="update_navigation_panel">
                <center><h4>Update + User Navigation</h4></center>
            </div>
            <div class="panel-body">
                <div class="container">
                    <p><center>

                        Last Update: <span name="lastupdate_timestamp"><?php echo date('m-d-Y H:i:s', $summary['lastupdate_timestamp']); ?></span>
                    </center>

                    </p>
                </div>

                <div class="container">
                    <center><button class="btn btn-lg btn-success" id="update_button">Update!
                            <i id='update_loading_spinner' class="fa fa-refresh fa-spin" style="font-size:24px;display:none;"></i>
                        </button></center>
                </div>


                <div class="container">
                    <p><center>Navigate to:</center></p>
                </div>

                <div class="container">
                    <p><center>
                        <select name="usersummary_navigation">
                            <option selected="true" disabled>Navigate to User:</option>
                            <?php
                                $cxn = Connection::getInstance();
                                $query = "SELECT userID FROM users WHERE userID<500 AND userID >=112";
                                $result = $cxn->commit($query);
                                while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
                                    echo "<option>".$line['userID']."</option>";
                                }
                            ?>

                        </select>
                    </center></p>
                </div>

            </div>
        </div>



        <div class="panel panel-primary">
            <div class="panel-heading" id="progress_panel_heading">
                <center><h4>Study Progress</h4></center>
            </div>
            <div class="panel-body">
                    <p><h4># Users Registered: <span name="study_completion_registered"><?php echo $summary['study_completion']['registered'];?></span></h4></p>
                    <p><h4># Users Completed: <span name="study_completion_completed"><?php echo $summary['study_completion']['completed'];?></span></h4></p>
                    <p><h4># Running (Not Finished): <span name="study_completion_running"><?php echo $summary['study_completion']['running'];?></span></h4></p>
                    <p><h4># Open Registrations: <span name="study_completion_open_registrations"><?php echo $summary['study_completion']['open_registrations'];?></span></h4></p>
            </div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading" id="exit_interview_panel_heading">
                <center><h4>Exit Interview Summary</h4></center>
            </div>
            <div class="panel-body">
                <table  class="table table-bordered table-fixed">
                    <thead>
                    <tr>
                        <th ></th>
                        <th ># Total Recorded</th>
                        <th ># Questionnaires Completed</th>
                        <th ># Questionnaires Completed Per User</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tasks</td>
                            <td><span name="count_tasks_count"><?php echo $summary['count_tasks']['count'];?></span></td>
                            <td><span name="count_taskquestionnaires_count"><?php echo $summary['count_taskquestionnaires']['count'];?></span></td>
                            <td><span name="rate_peruser_taskquestionnaires"><?php echo $summary['rate_peruser_taskquestionnaires'];?></span></td>
                        </tr>

                        <tr>
                            <td>Sessions</td>
                            <td><span name="count_sessions_count"><?php echo $summary['count_sessions']['count'];?></span></td>
                            <td><span name="count_sessionquestionnaires_count"><?php echo $summary['count_sessionquestionnaires']['count'];?></span></td>
                            <td><span name="rate_peruser_sessionquestionnaires"><?php echo $summary['rate_peruser_sessionquestionnaires'];?></span></td>
                        </tr>

                        <tr>
                            <td>Intentions</td>
                            <td><span name="count_intentions_count"><?php echo $summary['count_intentions']['count'];?></span></td>
                            <td><span name="count_intentionquestionnaires_count"><?php echo $summary['count_intentionquestionnaires']['count'];?></span></td>
                            <td><span name="rate_peruser_intentionquestionnaires"><?php echo $summary['rate_peruser_intentionquestionnaires'];?></span></td>
                        </tr>
                    </tbody>

                </table>


            </div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading" id="annotation_panel_heading">
                <center><h4>Annotation Interview Summary</h4></center>
            </div>
            <div class="panel-body">


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <center><h5><p>Was the process of log review and annotation clear? (1=Not at all, 7=Completely)</p>
                            <p>(&mu;=<span name="interviewdata_exittool_mean_reviewannotation_clear"><?php echo $summary['interviewdata_exittool']['mean_reviewannotation_clear'];?></span>,
                                &sigma;=<span name="interviewdata_exittool_std_reviewannotation_clear"><?php echo $summary['interviewdata_exittool']['std_reviewannotation_clear'];?></span>)</p></h5></center>
                        </div>
                        <div class="panel-body">
                            <canvas id="canvas_reviewannotation_clear" >

                            </canvas>
                        </div>

                    </div>


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <center><h5><p>Was the set of intentions that you could choose from understandable? (1=Not at all, 7=Completely)</p>
                            <p>(&mu;=<span name="interviewdata_exittool_mean_intentions_understandable"><?php echo $summary['interviewdata_exittool']['mean_intentions_understandable'];?></span>,
                                &sigma;=<span name="interviewdata_exittool_std_intentions_understandable"><?php echo $summary['interviewdata_exittool']['std_intentions_understandable'];?></span>)</p></h5></center>
                        </div>
                        <div class="panel-body">
                            <canvas id="canvas_intentions_understandable" >

                            </canvas>

                        </div>

                    </div>


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <center><h5><p>Was the set of intentions that you could choose from adequate? (1=Not at all, 7=Completely)</p>
                            <p>(&mu;=<span name="interviewdata_exittool_mean_intentions_adequate"><?php echo $summary['interviewdata_exittool']['mean_intentions_adequate'];?></span>,
                                &sigma;=<span name="interviewdata_exittool_std_intentions_adequate"><?php echo $summary['interviewdata_exittool']['std_intentions_adequate'];?></span>)</p></h5></center>
                        </div>
                        <div class="panel-body">
                            <canvas id="canvas_intentions_adequate" >

                            </canvas>

                        </div>

                    </div>



            </div>
        </div>


        <div class="panel panel-primary">
            <div class="panel-heading" id="tasks_interview_panel_heading">
                <center><h4>Tasks Interview Summary</h4></center>
            </div>
            <div class="panel-body">


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>What stage are you in with regard to completing this task? (1=Starting, 7=Finished)</p>
                        <p>(&mu;=<span name="interviewdata_exittasks_mean_task_stage"><?php echo $summary['interviewdata_exittasks']['mean_task_stage'];?></span>,
                            &sigma;=<span name="interviewdata_exittasks_std_task_stage"><?php echo $summary['interviewdata_exittasks']['std_task_stage'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">
                        <canvas id="canvas_task_stage" >

                        </canvas>
                    </div>

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>How would you describe the goal of the task? (1=Abstract, 7=Specific)</p>
                        <p>(&mu;=<span name="interviewdata_exittasks_mean_goal"><?php echo $summary['interviewdata_exittasks']['mean_goal'];?></span>,
                            &sigma;=<span name="interviewdata_exittasks_std_goal"><?php echo $summary['interviewdata_exittasks']['std_goal'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">
                        <canvas id="canvas_goal" >

                        </canvas>
                    </div>

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>How would you rate the importance of task? (1=Unimportant, 7=Extremely)</p>
                        <p>(&mu;=<span name="interviewdata_exittasks_mean_importance"><?php echo $summary['interviewdata_exittasks']['mean_importance'];?></span>,
                            &sigma;=<span name="interviewdata_exittasks_std_importance"><?php echo $summary['interviewdata_exittasks']['std_importance'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">

                        <canvas id="canvas_importance" >

                        </canvas>
                    </div>

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>How would you rate the urgency of task? (1=No urgency, 7=Extremely)</p>
                        <p>(&mu;=<span name="interviewdata_exittasks_mean_urgency"><?php echo $summary['interviewdata_exittasks']['mean_urgency'];?></span>,
                            &sigma;=<span name="interviewdata_exittasks_std_urgency"><?php echo $summary['interviewdata_exittasks']['std_urgency'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">
                        <canvas id="canvas_urgency" >

                        </canvas>
                    </div>

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>How would you rate the difficulty of task? (1=Not difficult, 7=Extremely)</p>
                        <p>(&mu;=<span name="interviewdata_exittasks_mean_difficulty"><?php echo $summary['interviewdata_exittasks']['mean_difficulty'];?></span>,
                            &sigma;=<span name="interviewdata_exittasks_std_difficulty"><?php echo $summary['interviewdata_exittasks']['std_difficulty'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">
                        <canvas id="canvas_difficulty" >

                        </canvas>
                    </div>

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>How would you rate the complexity of task? (1=Not complex, 7=Extremely)</p>
                        <p>(&mu;=<span name="interviewdata_exittasks_mean_complexity"><?php echo $summary['interviewdata_exittasks']['mean_complexity'];?></span>,
                            &sigma;=<span name="interviewdata_exittasks_std_complexity"><?php echo $summary['interviewdata_exittasks']['std_complexity'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">

                        <canvas id="canvas_complexity" >

                        </canvas>
                    </div>

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>How would you rate your knowledge of the topic of this task? (1=No knowledge, 7=Highly knowledgeable)</p>
                        <p>(&mu;=<span name="interviewdata_exittasks_mean_knowledge_topic"><?php echo $summary['interviewdata_exittasks']['mean_knowledge_topic'];?></span>,
                            &sigma;=<span name="interviewdata_exittasks_std_knowledge_topic"><?php echo $summary['interviewdata_exittasks']['std_knowledge_topic'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">
                        <canvas id="canvas_knowledge_topic" >

                        </canvas>
                    </div>

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>How would you rate your knowledge of procedures or methods for completing the task? (1=No knowledge, 7=Highly knowledgeable)</p>
                        <p>(&mu;=<span name="interviewdata_exittasks_mean_knowledge_procedures"><?php echo $summary['interviewdata_exittasks']['mean_knowledge_procedures'];?></span>,
                            &sigma;=<span name="interviewdata_exittasks_std_knowledge_procedures"><?php echo $summary['interviewdata_exittasks']['std_knowledge_procedures'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">

                        <canvas id="canvas_knowledge_procedures" >

                        </canvas>
                    </div>

                </div>



            </div>
        </div>


        <div class="panel panel-primary">
            <div class="panel-heading" id="sessions_interview_panel_heading">
                <center><h4>Sessions Interview Summary</h4></center>
            </div>
            <div class="panel-body">



                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>Was the search session successful? (1=Not at all, 7=Completely)</p>
                        <p>(&mu;=<span name="interviewdata_exitsessions_mean_successful"><?php echo $summary['interviewdata_exitsessions']['mean_successful'];?></span>,
                            &sigma;=<span name="interviewdata_exitsessions_std_successful"><?php echo $summary['interviewdata_exitsessions']['std_successful'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">
                        <canvas id="canvas_successful" >

                        </canvas>
                    </div>

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <center><h5><p>Was the search session useful to accomplish the task? (1=Not at all, 7=Completely)</p>
                        <p>(&mu;=<span name="interviewdata_exitsessions_mean_useful"><?php echo $summary['interviewdata_exitsessions']['mean_useful'];?></span>,
                            &sigma;=<span name="interviewdata_exitsessions_std_useful"><?php echo $summary['interviewdata_exitsessions']['std_useful'];?></span>)</p></h5></center>
                    </div>
                    <div class="panel-body">

                        <canvas id="canvas_useful" >

                        </canvas>
                    </div>

                </div>


            </div>
        </div>



        <div class="panel panel-primary">
            <div class="panel-heading" id="intentions_panel_heading">
                <center><h4>Intentions Summary</h4></center>
            </div>
            <div class="panel-body">

                <canvas id="canvas_intention_distribution" >

                </canvas>

            </div>
        </div>




    </div>
</div>


</body>
</html>