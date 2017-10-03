<?php
session_start();
require_once('core/Base.class.php');
require_once('core/Connection.class.php');
require_once('core/Questionnaires.class.php');


date_default_timezone_set('America/New_York');


$cxn = Connection::getInstance();





if(isset($_POST['email'])){

    $cxn = Connection::getInstance();
    $email = $_POST['email'];

    if($email=='mmitsui@scarletmail.rutgers.edu'){
        $query = "SELECT userID,email1,firstName,lastName FROM recruits WHERE `userID`='1000'";
    }else{
        $query = "SELECT userID,email1,firstName,lastName FROM recruits WHERE `email1`='$email'";
    }

    $results = $cxn->commit($query);
    $line = mysql_fetch_array($results,MYSQL_ASSOC);
    $userID = $line['userID'];





    if(isset($_POST['id'])){
        $id = $_POST['id'];
        $base = Base::getInstance();
        $time = $base->getTime();
        $date = $base->getDate();
        $timestamp = $base->getTimestamp();
        $task_accomplishment = mysql_escape_string($_POST['task_accomplishment']);
        $task_completionstage = $_POST['task_completionstage'];
        $task_goal = $_POST['task_goal'];
        $task_importance = $_POST['task_importance'];
        $task_urgency = $_POST['task_urgency'];
        $task_difficulty = $_POST['task_difficulty'];
        $task_complexity = $_POST['task_complexity'];
        $task_topicknowledge = $_POST['task_topicknowledge'];
        $task_procedureknowledge = $_POST['task_procedureknowledge'];
        $session_success = $_POST['session_success'];
        $session_success_description = mysql_escape_string($_POST['session_success_description']);
        $session_problematic = $_POST['session_problematic'];
        $session_problematic_description = mysql_escape_string($_POST['session_problematic_description']);
        $session_useful = $_POST['session_useful'];
        $session_useful_description = mysql_escape_string($_POST['session_useful_description']);
        $intention_question = mysql_escape_string($_POST['intention_question']);
        $intention_changequestion = mysql_escape_string($_POST['intention_changequestion']);

        $query = "INSERT INTO questionnaire_exit_task (`task_idcolumn`,`userID`,`email`,`date`,`time`,`timestamp`,
                `task_accomplishment`,
                `task_completionstage`,
                `task_goal`,
                `task_importance`,
                `task_urgency`,
                `task_difficulty`,
                `task_complexity`,
                `task_topicknowledge`,
                `task_procedureknowledge`,
                `session_success`,
                `session_success_description`,
                `session_problematic`,
                `session_problematic_description`,
                `session_useful`,
                `session_useful_description`,
                `intention_question`,
                `intention_changequestion`
                ) VALUES ('$id','$userID','$email','$date','$time','$timestamp',
                '$task_accomplishment',
                '$task_completionstage',
                '$task_goal',
                '$task_importance',
                '$task_urgency',
                '$task_difficulty',
                '$task_complexity',
                '$task_topicknowledge',
                '$task_procedureknowledge',
                '$session_success',
                '$session_success_description',
                '$session_problematic',
                '$session_problematic_description',
                '$session_useful',
                '$session_useful_description',
                '$intention_question',
                '$intention_changequestion'
                )";
        $cxn->commit($query);

    }


    $query = "SELECT * FROM questionnaire_exit_task WHERE userID=$userID GROUP BY task_idcolumn";
    $results = $cxn->commit($query);
    $n_annotated = mysql_num_rows($results);

    $query = "SELECT * FROM task_labels_user WHERE userID=$userID AND exitinterview=1";
    $results = $cxn->commit($query);
    $n_total = mysql_num_rows($results);

    $n_remaining = $n_total - $n_annotated;

    if($n_remaining==0){
        ?>
        <html>
        <head>

        <link rel="stylesheet" href="study_styles/bootstrap-3.3.7-dist/css/bootstrap.css">
        <link rel="stylesheet" href="study_styles/custom/text.css">
        <link rel="stylesheet" href="styles.css">
        <title>
            Exit Questionnaire - Tasks
        </title>

        <style>
            select {
                font-size:13px;
            }
            .my-error-class{
                color:#FF0000;
            }
        </style>
        </head>
        <body>
        <div class="panel panel-default">
            <div class="panel-body">
                Thank you for your participation!
            </div>

        </div>

        </body>
        </html>
        <?php
        exit();
    }else{

        $tasks_array = array();
        while($row = mysql_fetch_array($results,MYSQL_ASSOC)){
            $tasks_array[] = $row;
        }

        $current_task = $tasks_array[$n_annotated];

        ?>
        <html>
        <head>



        <link rel="stylesheet" href="study_styles/bootstrap-3.3.7-dist/css/bootstrap.css">
        <link rel="stylesheet" href="study_styles/custom/text.css">
        <link rel="stylesheet" href="styles.css">
            <script type="text/javascript" src="lib/jquery-2.1.3.min.js"></script>
            <script type="text/javascript" src="lib/validation/jquery-validation-1.13.1/dist/jquery.validate.js"></script>
            <script type="text/javascript" src="lib/validation/validation.js"></script>
        <title>
        Exit Questionnaire - Tasks
        </title>

        <style>
        select {
            font-size:13px;
            }
            .my-error-class{
            color:#FF0000;
        }
        </style>

            <script>

                var num_tasks = 1;
                jQuery.validator.addMethod("rankedorder", function(value, element) {
                    return isRankedOrderValid(value);
                }, "<span style='color:red'>Please specify a ranked order according to the description above.</span>");
                $().ready(function(){
                    $("#spr2015_regform").validate({
                        errorClass:"my-error-class",
                        ignore:"",
                        rules: {
                            task_accomplishment: {
                                required: true,
                            },
                            task_completionstage: {
                                required: true,
                            },
                            task_accomplishment: {
                                required: true,
                            },
                            task_goal: {
                                required: true,
                            },
                            task_importance: {
                                required: true,
                            },
                            task_urgency: {
                                required: true,
                            },
                            task_difficulty: {
                                required: true,
                            },
                            task_complexity: {
                                required: true,
                            },
                            task_topicknowledge: {
                                required: true,
                            },
                            task_procedureknowledge: {
                                required: true,
                            },
                            session_success: {
                                required: true,
                            },
                            session_success_description: {
                                required: function (element) {
                                    if($('input[name=session_success]:checked').val()){
                                        return parseInt($('input[name=session_success]:checked').val()) < 4;
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                },
                            },
                            session_problematic: {
                                required: true,
                            },
                            session_problematic_description: {
                                required: function (element) {
                                    if($('input[name=session_problematic]:checked').val()){
                                        return parseInt($('input[name=session_problematic]:checked').val()) < 4;
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                },
                            },
                            session_useful: {
                                required: true,
                            },
                            session_useful_description: {
                                required: function (element) {
                                    if($('input[name=session_useful]:checked').val()){
                                        return parseInt($('input[name=session_useful]:checked').val()) < 4;
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                },
                            },
                            intention_question: {
                                required: true,
                            },
                            intention_changequestion: {
                                required: true,
                            }


                        },
                        messages: {

//                            email: {
//                                required: "<span style='color:red'>This field is required.</span>",
//                                email: "<span style='color:red'>Please enter a valid e-mail address.</span>",
//                                remote: "<span style='color:red'>The given e-mail does not match our records.</span>"
//                            },

//                        age_1: {
//                            required:"<span style='color:red'>Please enter your age.</span>",
//                            number:"<span style='color:red'>Please enter a number.</span>"
//                        },


                        },
                        errorPlacement: function(error, element)
                        {
                            if ( element.is(":radio") )
                            {
                                error.appendTo( element.parents('.container') );
                            }
                            else
                            { // This is the default behavior
                                error.insertAfter( element );
                            }
                        }});

                });



            </script>
        </head>
        <body>
        <form id="spr2015_regform" method="post" action="exitQuestionnaire_Tasks.php">
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="progress">
                    <?php
                        echo "<div class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"".((floatval($n_annotated))/floatval($n_total)*100)."\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:".((floatval($n_annotated))/floatval($n_total)*100)."%\">";
                        echo "<span style='color:#000000'>($n_annotated/$n_total) Complete</span>";
                        echo "</div>";
                    ?>
                </div>



                We've identified a few information-seeking episodes that we'd like you to tell us a bit more about...

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <?php
                        echo "Task: ".$current_task['taskName'];
                        ?>
                    </div>
                    <div class="panel-body">



                        You said that this search was done in order to accomplish the task: <?php echo $current_task['taskName'];?>.

                        <h3 id="mark_session_confirmation" class="alert alert-danger">Log for entire task should go here</h3>


                        Can you tell us a bit more about the task itself?


                        <div class="form-group">
                            <label for="task_accomplishment">What did you obtain, create, disseminate, or otherwise accomplish as a result of task completion? If the task was not completed as a result of this information-seeking episode, please say so, and also describe what was accomplished.</label>
                            <textarea class="form-control" id="task_accomplishment" rows="5" name="task_accomplishment" placeholder="Please enter your answer here." required></textarea>
                            <br/>
                        </div>


                        <div class="form-group">
                            <label>What stage are you in with regard to completing this task?</label>
                            <div id="task_completionstage" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_completionstage_1" ><input id="task_completionstage_1" type="radio" name="task_completionstage" value="1">1 (Starting)</label></div>
                                    <div  class="col-md-1"><label for="task_completionstage_2" ><input id="task_completionstage_2" type="radio" name="task_completionstage" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_completionstage_3" ><input id="task_completionstage_3" type="radio" name="task_completionstage" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="task_completionstage_4" ><input id="task_completionstage_4" type="radio" name="task_completionstage" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_completionstage_5" ><input id="task_completionstage_5" type="radio" name="task_completionstage" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="task_completionstage_6" ><input id="task_completionstage_6" type="radio" name="task_completionstage" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_completionstage_7" ><input id="task_completionstage_7" type="radio" name="task_completionstage" value="7">7 (Finished)</label></div>
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label>How would you describe the goal of the task?</label>
                            <div id="task_goal" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_goal_1" ><input id="task_goal_1" type="radio" name="task_goal" value="1">1 (Abstract)</label></div>
                                    <div  class="col-md-1"><label for="task_goal_2" ><input id="task_goal_2" type="radio" name="task_goal" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_goal_3" ><input id="task_goal_3" type="radio" name="task_goal" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="task_goal_4" ><input id="task_goal_4" type="radio" name="task_goal" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_goal_5" ><input id="task_goal_5" type="radio" name="task_goal" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="task_goal_6" ><input id="task_goal_6" type="radio" name="task_goal" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_goal_7" ><input id="task_goal_7" type="radio" name="task_goal" value="7">7 (Specific)</label></div>
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label>How would you rate the importance of task?</label>
                            <div id="task_importance" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_importance_1" ><input id="task_importance_1" type="radio" name="task_importance" value="1">1 (Unimportant)</label></div>
                                    <div  class="col-md-1"><label for="task_importance_2" ><input id="task_importance_2" type="radio" name="task_importance" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_importance_3" ><input id="task_importance_3" type="radio" name="task_importance" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="task_importance_4" ><input id="task_importance_4" type="radio" name="task_importance" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_importance_5" ><input id="task_importance_5" type="radio" name="task_importance" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="task_importance_6" ><input id="task_importance_6" type="radio" name="task_importance" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_importance_7" ><input id="task_importance_7" type="radio" name="task_importance" value="7">7 (Extremely)</label></div>
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label>How would you rate the urgency of task?</label>
                            <div id="task_urgency" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_urgency_1" ><input id="task_urgency_1" type="radio" name="task_urgency" value="1">1 (Not urgent)</label></div>
                                    <div  class="col-md-1"><label for="task_urgency_2" ><input id="task_urgency_2" type="radio" name="task_urgency" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_urgency_3" ><input id="task_urgency_3" type="radio" name="task_urgency" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="task_urgency_4" ><input id="task_urgency_4" type="radio" name="task_urgency" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_urgency_5" ><input id="task_urgency_5" type="radio" name="task_urgency" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="task_urgency_6" ><input id="task_urgency_6" type="radio" name="task_urgency" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_urgency_7" ><input id="task_urgency_7" type="radio" name="task_urgency" value="7">7 (Extremely)</label></div>
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label>How would you rate the difficulty of task?</label>
                            <div id="task_difficulty" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_difficulty_1" ><input id="task_difficulty_1" type="radio" name="task_difficulty" value="1">1 (Not difficult)</label></div>
                                    <div  class="col-md-1"><label for="task_difficulty_2" ><input id="task_difficulty_2" type="radio" name="task_difficulty" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_difficulty_3" ><input id="task_difficulty_3" type="radio" name="task_difficulty" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="task_difficulty_4" ><input id="task_difficulty_4" type="radio" name="task_difficulty" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_difficulty_5" ><input id="task_difficulty_5" type="radio" name="task_difficulty" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="task_difficulty_6" ><input id="task_difficulty_6" type="radio" name="task_difficulty" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_difficulty_7" ><input id="task_difficulty_7" type="radio" name="task_difficulty" value="7">7 (Extremely)</label></div>
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label>How would you rate the complexity of task?</label>
                            <div id="task_complexity" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_complexity_1" ><input id="task_complexity_1" type="radio" name="task_complexity" value="1">1 (Not complex)</label></div>
                                    <div  class="col-md-1"><label for="task_complexity_2" ><input id="task_complexity_2" type="radio" name="task_complexity" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_complexity_3" ><input id="task_complexity_3" type="radio" name="task_complexity" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="task_complexity_4" ><input id="task_complexity_4" type="radio" name="task_complexity" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_complexity_5" ><input id="task_complexity_5" type="radio" name="task_complexity" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="task_complexity_6" ><input id="task_complexity_6" type="radio" name="task_complexity" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_complexity_7" ><input id="task_complexity_7" type="radio" name="task_complexity" value="7">7 (Extremely)</label></div>
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label>How would you rate your knowledge of the topic of this task?</label>
                            <div id="task_topicknowledge" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_topicknowledge_1" ><input id="task_topicknowledge_1" type="radio" name="task_topicknowledge" value="1">1 (No knowledge)</label></div>
                                    <div  class="col-md-1"><label for="task_topicknowledge_2" ><input id="task_topicknowledge_2" type="radio" name="task_topicknowledge" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_topicknowledge_3" ><input id="task_topicknowledge_3" type="radio" name="task_topicknowledge" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="task_topicknowledge_4" ><input id="task_topicknowledge_4" type="radio" name="task_topicknowledge" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_topicknowledge_5" ><input id="task_topicknowledge_5" type="radio" name="task_topicknowledge" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="task_topicknowledge_6" ><input id="task_topicknowledge_6" type="radio" name="task_topicknowledge" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_topicknowledge_7" ><input id="task_topicknowledge_7" type="radio" name="task_topicknowledge" value="7">7 (Highly knowledgeable)</label></div>
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label>How would you rate your knowledge of procedures or methods for completing the task?</label>
                            <div id="task_procedureknowledge" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_procedureknowledge_1" ><input id="task_procedureknowledge_1" type="radio" name="task_procedureknowledge" value="1">1 (No knowledge)</label></div>
                                    <div  class="col-md-1"><label for="task_procedureknowledge_2" ><input id="task_procedureknowledge_2" type="radio" name="task_procedureknowledge" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_procedureknowledge_3" ><input id="task_procedureknowledge_3" type="radio" name="task_procedureknowledge" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="task_procedureknowledge_4" ><input id="task_procedureknowledge_4" type="radio" name="task_procedureknowledge" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_procedureknowledge_5" ><input id="task_procedureknowledge_5" type="radio" name="task_procedureknowledge" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="task_procedureknowledge_6" ><input id="task_procedureknowledge_6" type="radio" name="task_procedureknowledge" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_procedureknowledge_7" ><input id="task_procedureknowledge_7" type="radio" name="task_procedureknowledge" value="7">7 (Highly knowledgeable)</label></div>
                                </div>
                            </div>
                        </div>

                        <hr/>

                        <p>Now, we'd like to ask you some questions about the search session that you engaged in with respect to this task.</p>

                        <p>Please evaluate the search session by answering following questions:</p>

                        <h3 id="mark_session_confirmation" class="alert alert-warning">Log for a session should go here?  For multiple sessions?</h3>

                        <div class="form-group">
                            <label for="intention_question">[Ask specific questions about any intentions that need clarification]</label>
                            <textarea class="form-control" id="intention_question" style="width:30%;" rows="1" name="intention_question" placeholder="Please enter your answer here." required></textarea>
                            <br/>
                        </div>


                        <div class="form-group">
                            <label for="intention_changequestion">[if necessary, ask] Why did you go from this intention to the following intention?</label>
                            <textarea class="form-control" id="intention_changequestion" style="width:30%;" rows="1" name="intention_changequestion" placeholder="Please enter your answer here." required></textarea>
                            <br/>
                        </div>



                        <div class="form-group">
                            <label>Was the search session successful?</label>
                            <div id="session_success" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_success_1" ><input id="session_success_1" type="radio" name="session_success" value="1">1 (No knowledge)</label></div>
                                    <div  class="col-md-1"><label for="session_success_2" ><input id="session_success_2" type="radio" name="session_success" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_success_3" ><input id="session_success_3" type="radio" name="session_success" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="session_success_4" ><input id="session_success_4" type="radio" name="session_success" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_success_5" ><input id="session_success_5" type="radio" name="session_success" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="session_success_6" ><input id="session_success_6" type="radio" name="session_success" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_success_7" ><input id="session_success_7" type="radio" name="session_success" value="7">7 (Highly knowledgeable)</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="session_success_description">If less than 4, why do you say this?</label>
                            <textarea class="form-control" id="session_success_description" style="width:30%;" rows="1" name="session_success_description" placeholder="Please enter your answer here." required></textarea>
                            <br/>
                        </div>





                        <div class="form-group">
                            <label>Was the search session problematic?</label>
                            <div id="session_problematic" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_problematic_1" ><input id="session_problematic_1" type="radio" name="session_problematic" value="1">1 (No knowledge)</label></div>
                                    <div  class="col-md-1"><label for="session_problematic_2" ><input id="session_problematic_2" type="radio" name="session_problematic" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_problematic_3" ><input id="session_problematic_3" type="radio" name="session_problematic" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="session_problematic_4" ><input id="session_problematic_4" type="radio" name="session_problematic" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_problematic_5" ><input id="session_problematic_5" type="radio" name="session_problematic" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="session_problematic_6" ><input id="session_problematic_6" type="radio" name="session_problematic" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_problematic_7" ><input id="session_problematic_7" type="radio" name="session_problematic" value="7">7 (Highly knowledgeable)</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="session_problematic_description">If less than 4, why do you say this?</label>
                            <textarea class="form-control" id="session_problematic_description" style="width:30%;" rows="1" name="session_problematic_description" placeholder="Please enter your answer here." required></textarea>
                            <br/>
                        </div>




                        <div class="form-group">
                            <label>Was the search session useful to accomplish the task?</label>
                            <div id="session_useful" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_useful_1" ><input id="session_useful_1" type="radio" name="session_useful" value="1">1 (No knowledge)</label></div>
                                    <div  class="col-md-1"><label for="session_useful_2" ><input id="session_useful_2" type="radio" name="session_useful" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_useful_3" ><input id="session_useful_3" type="radio" name="session_useful" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="session_useful_4" ><input id="session_useful_4" type="radio" name="session_useful" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_useful_5" ><input id="session_useful_5" type="radio" name="session_useful" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="session_useful_6" ><input id="session_useful_6" type="radio" name="session_useful" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="session_useful_7" ><input id="session_useful_7" type="radio" name="session_useful" value="7">7 (Highly knowledgeable)</label></div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="session_useful_description">If less than 4, why do you say this?</label>
                            <textarea class="form-control" id="session_useful_description" style="width:30%;" rows="1" name="session_useful_description" placeholder="Please enter your answer here." required></textarea>
                            <br/>
                        </div>



                        <input type="hidden" id="id" name="id" value=<?php echo $current_task['id'];?>>
                        <input type="hidden" id="email" name="email" value=<?php echo $email;?>>

                        <button class="btn btn-primary" type="submit">Submit</button>














                    </div>



                </div>
            </div>

        </div>
        </form>

        </body>
        </html>
        <?php
        exit();

    }

}else{



    $NUM_USERS = 1;
    $questionnaire = Questionnaires::getInstance();
    $questionnaire->clearCache();
    $questionnaire->populateQuestionsFromDatabase("fall2015intent","questionID ASC");


    ?>
    <html>
    <head>

        <link rel="stylesheet" href="study_styles/bootstrap-3.3.7-dist/css/bootstrap.css">
        <link rel="stylesheet" href="study_styles/custom/text.css">
        <link rel="stylesheet" href="styles.css">
        <title>
            Exit Questionnaire - Tasks
        </title>

        <style>
            select {
                font-size:13px;
            }
            .my-error-class{
                color:#FF0000;
            }
        </style>
        <?php echo $questionnaire->printPreamble();?>



        <script>

            var num_tasks = 1;
            jQuery.validator.addMethod("rankedorder", function(value, element) {
                return isRankedOrderValid(value);
            }, "<span style='color:red'>Please specify a ranked order according to the description above.</span>");
            $().ready(function(){
                $("#spr2015_regform").validate({
                    errorClass:"my-error-class",
                    ignore:"",
                    rules: {
                        email: {
                            required: true,
                            email: true,
                            remote: {
                                url:'checkEmail.php',
                                type:'post'
                            }
                        }
                    },
                    messages: {

                        email: {
                            required: "<span style='color:red'>This field is required.</span>",
                            email: "<span style='color:red'>Please enter a valid e-mail address.</span>",
                            remote: "<span style='color:red'>The given e-mail does not match our records.</span>"
                        },

//                        age_1: {
//                            required:"<span style='color:red'>Please enter your age.</span>",
//                            number:"<span style='color:red'>Please enter a number.</span>"
//                        },


                    },
                    errorPlacement: function(error, element)
                    {
                        if ( element.is(":radio") )
                        {
                            error.appendTo( element.parents('.container') );
                        }
                        else
                        { // This is the default behavior
                            error.insertAfter( element );
                        }
                    }});

            });



        </script>

        <style type="text/css">
            .cursorType{
                cursor:pointer;
                cursor:hand;
            }
        </style>
    </head>



    <body class="body" >

    <div class="panel panel-default" style="width:95%; margin:auto">
        <div class="panel-body">

            <div id="signupForm" align="center">
                <h3>Exit Questionnaire - Tasks</h3>
            </div>


            <form id="spr2015_regform" method="post" action="exitQuestionnaire_Tasks.php">
                <?php

                for($x=1;$x<=$NUM_USERS;$x++){

                    echo "<h3>Participant</h3>";




                    echo "<div class=\"form-group\">";
                    echo "<label for=\"email\">To view the questionnaire, please enter the e-mail you used for registration</label>";
                    echo "<textarea id=\"email\" class=\"form-control\" name=\"email\" style=\"width:30%;\" rows=\"1\" cols=\"30\" placeholder=\"Primary Email\" required></textarea>";
                    echo "</div>";

                }
                ?>
                <hr>
                <button class="btn btn-primary" type="submit">Submit</button>
            </form>
        </div>
    </div>
    </div>
    </body>
    <?php $questionnaire->printPostamble();?>
    </html>
    <?php

}
?>
