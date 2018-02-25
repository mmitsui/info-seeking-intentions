<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/getSummaryData.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/updateSummaryData.php");


if(!isset($_GET['userID'])){
    echo "You must specify a user ID!";
    exit();
}
$userID = $_GET['userID'];
updateSummaryData('user',array('userID'=>$userID));
$summary = getSummaryData('user',array('userID'=>$userID));
//print_r($summary);


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


        var userID = <?php echo $_GET['userID'];?>;

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

            ev.preventDefault();
            $("#update_loading_spinner").show();



            function addZero(i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }

            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/updateSummaryData.php',
                data: 'summaryType=user&ajax_call=update&userID='+userID
            }).done(function(response) {

//                alert(response);
                response = JSON.parse(response);
                var d = new Date(response.lastupdate_timestamp*1000);
                $("span[name='lastupdate_timestamp']").text(addZero(d.getMonth()+1)+"-"+d.getDate()+"-"+d.getFullYear()+" "+addZero(d.getHours())+":"+addZero(d.getMinutes())+":"+addZero(d.getSeconds()));

//                $("span[name='study_completion_registered']").text(response.study_completion.registered).css('background-color','aqua');
//                $("span[name='study_completion_completed']").text(response.study_completion.completed).css('background-color','aqua');
//                $("span[name='study_completion_running']").text(response.study_completion.running).css('background-color','aqua');
//                $("span[name='study_completion_open_registrations']").text(response.study_completion.open_registrations).css('background-color','aqua');


                $("span[name='count_tasks_count']").text(response.count_tasks.count).css('background-color','aqua');
                $("span[name='count_sessions_count']").text(response.count_sessions.count).css('background-color','aqua');
                $("span[name='count_searchsegments_count_total']").text(response.count_searchsegments.count_total).css('background-color','aqua');
                $("span[name='count_searchsegments_count_automated']").text(response.count_searchsegments.count_automated).css('background-color','aqua');
                $("span[name='count_searchsegments_count_manual']").text(response.count_searchsegments.count_manual).css('background-color','aqua');

                $("span[name='count_intentions_count']").text(response.count_intentions.count).css('background-color','aqua');
                $("span[name='count_intentions_count_successful']").text(response.count_intentions.count_successful).css('background-color','aqua');
                $("span[name='count_intentions_count_failed']").text(response.count_intentions.count_failed).css('background-color','aqua');
                $("span[name='count_intentions_count_min']").text(response.count_intentions.count_min).css('background-color','aqua');
                $("span[name='count_intentions_count_max']").text(response.count_intentions.count_max).css('background-color','aqua');


                $("span[name='rate_pertask_sessions']").text(response.rate_pertask_sessions).css('background-color','aqua');
                $("span[name='rate_pertask_searchsegments']").text(response.rate_pertask_searchsegments).css('background-color','aqua');
                $("span[name='rate_pertask_intentions']").text(response.rate_pertask_intentions).css('background-color','aqua');

                $("span[name='rate_persession_searchsegments']").text(response.rate_persession_searchsegments).css('background-color','aqua');
                $("span[name='rate_persession_intentions']").text(response.rate_persession_intentions).css('background-color','aqua');


                $("span[name='interviewdata_exittool_mean_reviewannotation_clear']").text(response.interviewdata_exittool.mean_reviewannotation_clear).css('background-color','aqua');
                $("span[name='interviewdata_exittool_mean_intentions_understandable']").text(response.interviewdata_exittool.mean_intentions_understandable).css('background-color','aqua');
                $("span[name='interviewdata_exittool_mean_intentions_adequate']").text(response.interviewdata_exittool.mean_intentions_adequate).css('background-color','aqua');

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

                $("#tasks_tbody").html("");
                var tasks_tbody_htmlstring = "";



                Object.keys(response.task_data.data).forEach(function (taskID) {
                    var item = response.task_data.data[taskID]
                    tasks_tbody_htmlstring += "<tr>";
                    tasks_tbody_htmlstring += "<td><span name='task_data_data_id' data-task-id='"+taskID+"'>"+taskID+"</span></td>";
                    tasks_tbody_htmlstring += "<td><span name='task_data_data_name' data-task-id='"+taskID+"'>"+item.name+"</span></td>";
                    tasks_tbody_htmlstring += "<td><span name='task_data_data_sessions_count' data-task-id='"+taskID+"'>"+item.sessions_count+"</span></td>";
                    tasks_tbody_htmlstring += "<td><span name='task_data_data_searchsegments_count' data-task-id='"+taskID+"'>"+item.searchsegments_count+"</span></td>";

                    tasks_tbody_htmlstring += "<td>"+item.intentions_total+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.intentions_successful+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.intentions_failed+"</td>";




                    tasks_tbody_htmlstring += "<td><button name='task_goto_button' data-task-id='"+taskID+"' class='btn btn-success' onclick='window.open(\"http://coagmento.org/workintent/getTask.php?userID="+userID+"&taskID="+taskID+"\",\"_blank\")'>View Task</button></td>";
                    tasks_tbody_htmlstring += "<td>"+item.task_stage+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.goal+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.importance+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.urgency+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.difficulty+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.complexity+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.knowledge_topic+"</td>";
                    tasks_tbody_htmlstring += "<td>"+item.knowledge_procedures+"</td>";
                    tasks_tbody_htmlstring += "</tr>";
                });

                $("#tasks_tbody").html(tasks_tbody_htmlstring);




                $("#sessions_tbody").html("");
                var sessions_tbody_htmlstring = "";



                Object.keys(response.session_data.data).forEach(function (sessionID) {
                    var item = response.session_data.data[sessionID]
                    sessions_tbody_htmlstring += "<tr>";
                    sessions_tbody_htmlstring += "<td><span>"+sessionID+"</span></td>";
                    sessions_tbody_htmlstring += "<td><span>"+item.taskID+"</span></td>";
                    sessions_tbody_htmlstring += "<td><span>"+item.task_name+"<span></td>";
                    sessions_tbody_htmlstring += "<td><span>"+item.count_searchsegments+"<span></td>";
                    sessions_tbody_htmlstring += "<td>"+item.intentions_total+"</td>";
                    sessions_tbody_htmlstring += "<td>"+item.intentions_successful+"</td>";
                    sessions_tbody_htmlstring += "<td>"+item.intentions_failed+"</td>";
                    sessions_tbody_htmlstring += "<td><button class='btn btn-success' onclick='window.open(\"http://coagmento.org/workintent/getSession.php?userID="+userID+"&sessionID="+sessionID+"\",\"_blank\")'>View Session</button></td>";
                    sessions_tbody_htmlstring += "<td><button class='btn btn-success' onclick='window.open(\"http://coagmento.org/workintent/showIntentionsForSession.php?userID="+userID+"&sessionID="+sessionID+"\",\"_blank\")'>View Intentions</button></td>";
                    sessions_tbody_htmlstring += "<td>"+item.successful+"</td>";
                    sessions_tbody_htmlstring += "<td>"+item.useful+"</td>";



                    sessions_tbody_htmlstring += "</tr>";
                });

                $("#sessions_tbody").html(sessions_tbody_htmlstring);





//                alert(JSON.stringify(response.interviewdata_exittool.barchartdata_intentions_adequate.datasets));
//                alert(JSON.stringify(response.interviewdata_exittool.barchartdata_reviewannotation_clear.datasets));

//                removeData(chart_reviewannotation_clear);
//                addData(chart_reviewannotation_clear,response.interviewdata_exittool.barchartdata_reviewannotation_clear.datasets[0].data);
//                removeData(chart_intentions_understandable);
//                addData(chart_intentions_understandable,response.interviewdata_exittool.barchartdata_intentions_understandable.datasets[0].data);
//                removeData(chart_intentions_adequate);
//                addData(chart_intentions_adequate,response.interviewdata_exittool.barchartdata_intentions_adequate.datasets[0].data);




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
                    {message:"Updated!"},
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


        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            document.getElementById("main").style.marginLeft = "250px";
        }

        $(document).ready(function()
            {


//                chart_reviewannotation_clear = assign_chart('canvas_reviewannotation_clear',barchartdata_reviewannotation_clear,'Review Annotation Clear');
//                chart_intentions_understandable = assign_chart('canvas_intentions_understandable',barchartdata_intentions_understandable,'Intentions Understandable');
//                chart_intentions_adequate = assign_chart('canvas_intentions_adequate',barchartdata_intentions_adequate,'Intentions Adequate');

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



                $("#update_button").on('click',updateStatistics);
                openNav();


            }
        );
    </script>


</head>




<body>

<div id="mySidenav" class="sidenav">
    <a href="#update_panel">Update</a>
    <a href="#summary_panel_heading">User Summary</a>
    <a href="#annotation_panel_heading">Annotation Interview Summary</a>
    <a href="#tasks_summary_panel_heading">Tasks Summary</a>
    <a href="#tasks_interview_summary_panel_heading">Tasks Interview Summary</a>
    <a href="#sessions_summary_panel_heading">Sessions Summary</a>
    <a href="#sessions_interview_summary_panel_heading">Sessions Interview Summary</a>
    <a href="#intentions_panel_heading">Intentions Summary</a>

</div>







<div id="main">

<div class="container">
    <center>
        <button class="btn btn-lg btn-info" onclick="window.location.replace('http://coagmento.org/workintent/showStudySummary.php');">
            <i class="fa fa-arrow-left" style="font-size:24px;"></i>
            Back to Study Summary
        </button>
    </center>
</div>

<div class="container">
    <center><h1>User <?php echo $userID;?></h1></center>
</div>



<div class="container">

    <div class="panel panel-primary">
        <div class="panel-heading" id="update_panel">
            <center><h4>Update</h4></center>
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






        </div>
    </div>



    <div class="panel panel-primary">
        <div class="panel-heading" id="summary_panel_heading">
            <center><h4>User Summary</h4></center>
        </div>
        <div class="panel-body">
            <form id="assign_researcher_form">
                <p><h4># Tasks: <span name="count_tasks_count"><?php echo $summary['count_tasks']['count'];?></span></h4></p>
                <p><h4># Sessions: <span name="count_sessions_count"><?php echo $summary['count_sessions']['count'];?></span></h4></p>
                <br/>
                <p><h4># Search Segments (total): <span name="count_searchsegments_count_total"><?php echo $summary['count_searchsegments']['count_total'];?></span></h4></p>
                <p><h4># Search Segments (automated): <span name="count_searchsegments_count_automated"><?php echo $summary['count_searchsegments']['count_automated'];?></span></h4></p>
                <p><h4># Search Segments (manual): <span name="count_searchsegments_count_manual"><?php echo $summary['count_searchsegments']['count_manual'];?></span></h4></p>
                <br/>

                <p><h4># Intentions (total): <span name="count_intentions_count"><?php echo $summary['count_intentions']['count'];?></span></h4></p>
                <p><h4># Intentions (successful): <span name="count_intentions_count_successful"><?php echo $summary['count_intentions']['count_successful'];?></span></h4></p>
                <p><h4># Intentions (failed): <span name="count_intentions_count_failed"><?php echo $summary['count_intentions']['count_failed'];?></span></h4></p>
                <p><h4># Intentions (max in search segment): <span name="count_intentions_count_max"><?php echo $summary['count_intentions']['count_max'];?></span></h4></p>
                <p><h4># Intentions (min in search segment): <span name="count_intentions_count_min"><?php echo $summary['count_intentions']['count_min'];?></span></h4></p>
                <br/>

                <p><h4># Sessions/Task: <span name="rate_pertask_sessions"><?php echo $summary['rate_pertask_sessions'];?></span></h4></p>
                <p><h4># Search Segments/Task: <span name="rate_pertask_searchsegments"><?php echo $summary['rate_pertask_searchsegments'];?></span></h4></p>
                <p><h4># Intentions/Task: <span name="rate_pertask_intentions"><?php echo $summary['rate_pertask_intentions'];?></span></h4></p>
                <br/>

                <p><h4># Search Segments/Session: <span name="rate_persession_searchsegments"><?php echo $summary['rate_persession_searchsegments'];?></span></h4></p>
                <p><h4># Intentions/Session: <span name="rate_persession_intentions"><?php echo $summary['rate_persession_intentions'];?></span></h4></p>

            </form>
        </div>
    </div>


    <div class="panel panel-primary">
        <div class="panel-heading" id="annotation_panel_heading">
            <center><h4>Annotation Interview Summary</h4></center>
        </div>
        <div class="panel-body">
            <center><h5><p>Was the process of log review and annotation clear? (1=Not at all, 7=Completely)</p>
                    <p>Answer=<span name="interviewdata_exittool_mean_reviewannotation_clear"><?php echo $summary['interviewdata_exittool']['mean_reviewannotation_clear'];?></span></p></h5></center>

            <center><h5><p>Was the set of intentions that you could choose from understandable? (1=Not at all, 7=Completely)</p>
                    <p>Answer=<span name="interviewdata_exittool_mean_intentions_understandable"><?php echo $summary['interviewdata_exittool']['mean_intentions_understandable'];?></span></p></h5></center>


            <center><h5><p>Was the set of intentions that you could choose from adequate? (1=Not at all, 7=Completely)</p>
                    <p>Answer=<span name="interviewdata_exittool_mean_intentions_adequate"><?php echo $summary['interviewdata_exittool']['mean_intentions_adequate'];?></span></p></h5></center>

        </div>
    </div>




    <div class="panel panel-primary">
        <div class="panel-heading" id="tasks_summary_panel_heading">
            <center><h4>Tasks Summary</h4></center>
        </div>
        <div class="panel-body">
            <table  class="table table-bordered table-fixed">
                <thead>
                <tr>
                    <th >Task ID</th>
                    <th >Task Name</th>
                    <th ># Sessions</th>
                    <th ># Search Segments</th>
                    <th ># Total Intentions</th>
                    <th ># Successful Intentions</th>
                    <th ># Failed Intentions</th>
                    <th >View Task Activity</th>
                    <th>Stage</th>
                    <th>Goal</th>
                    <th>Importance</th>
                    <th>Urgency</th>
                    <th>Difficulty</th>
                    <th>Complexity</th>
                    <th>Topic Knowledge</th>
                    <th>Procedure Knowledge</th>
                </tr>
                </thead>
                <tbody id="tasks_tbody">

                <?php




                    foreach($summary['task_data']['data'] as $id=>$item){
                        echo "</tr>";
                        echo "<td><span name='task_data_data_id' data-task-id='$id'>$id</span></td>";
                        echo "<td><span name='task_data_data_name' data-task-id='$id'>".htmlentities($item['name'])."</span></td>";
                        echo "<td><span name='task_data_data_sessions_count' data-task-id='$id'>".$item['sessions_count']."</span></td>";
                        echo "<td><span name='task_data_data_searchsegments_count' data-task-id='$id'>".$item['searchsegments_count']."</span></td>";

                        echo "<td>".$item['intentions_total']."</td>";
                        echo "<td>".$item['intentions_successful']."</td>";
                        echo "<td>".$item['intentions_failed']."</td>";
                        echo "<td><button name='task_goto_button' data-task-id='$id' class='btn btn-success' onclick='window.open(\"http://coagmento.org/workintent/getTask.php?userID=".$userID."&taskID=".$id."\",\"_blank\")'>View Task</button></td>";
                        echo "<td>".$item['task_stage']."</td>";
                        echo "<td>".$item['goal']."</td>";
                        echo "<td>".$item['importance']."</td>";
                        echo "<td>".$item['urgency']."</td>";
                        echo "<td>".$item['difficulty']."</td>";
                        echo "<td>".$item['complexity']."</td>";
                        echo "<td>".$item['knowledge_topic']."</td>";
                        echo "<td>".$item['knowledge_procedures']."</td>";

                        echo "</tr>";
                    }
                ?>
                </tbody>

            </table>


        </div>
    </div>




    <div class="panel panel-primary">
        <div class="panel-heading" id="tasks_interview_summary_panel_heading">
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
        <div class="panel-heading" id="sessions_summary_panel_heading">
            <center><h4>Sessions Summary</h4></center>
        </div>
        <div class="panel-body">
            <table  class="table table-bordered table-fixed">
                <thead>
                <tr>
                    <th >Session ID</th>
                    <th >Task ID</th>
                    <th >Task Name</th>
                    <th ># Search Segments</th>
                    <th ># Total Intentions</th>
                    <th ># Successful Intentions</th>
                    <th ># Failed Intentions</th>
                    <th >View Session Activity</th>
                    <th >View Intentions</th>
                    <th >Successful</th>
                    <th >Useful</th>
                </tr>
                </thead>
                <tbody id="sessions_tbody">

                <?php


                foreach($summary['session_data']['data'] as $id=>$item){
                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>".$item['taskID']."</td>";
                    echo "<td>".$item['task_name']."</td>";
                    echo "<td>".$item['count_searchsegments']."</td>";
                    echo "<td>".$item['intentions_total']."</td>";
                    echo "<td>".$item['intentions_successful']."</td>";
                    echo "<td>".$item['intentions_failed']."</td>";
                    echo "<td><button class='btn btn-success' onclick='window.open(\"http://coagmento.org/workintent/getSession.php?userID=".$userID."&sessionID=".$id."\",\"_blank\")'>View Session</button></td>";
                    echo "<td><button class='btn btn-success' onclick='window.open(\"http://coagmento.org/workintent/showIntentionsForSession.php?userID=".$userID."&sessionID=".$id."\",\"_blank\")'>View Intentions</button></td>";
                    echo "<td>".$item['successful']."</td>";
                    echo "<td>".$item['useful']."</td>";
                    echo "</tr>";
                }
                ?>



                </tbody>

            </table>


        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading" id="sessions_interview_summary_panel_heading">
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