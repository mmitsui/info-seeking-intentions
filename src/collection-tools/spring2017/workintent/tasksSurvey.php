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



?>



<html>
<head>
    <title>
        Tasks Questionnaire
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./lib/bootstrap_notify/bootstrap-notify.min.js"></script>


    <script>

        function submitForm(ev){
            ev.preventDefault();
            var formData = $('#task_form').serialize();
            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/submitInterviewTask.php',
                data: formData
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){


                    $.notify(
                        {message:"You have successfully submitted the task survey!"},
                        {type: 'success'}
                    );

                    $('#task_form').trigger('reset');

                }else if (response.hasOwnProperty('success') && !response['success']){

                    $.notify(
                        {message:response['message']},
                        {type: 'danger'}
                    );

                }else{
                    $.notify(
                        {message:"Something went wrong.  Please check your submission and try again"},
                        {type: 'danger'}
                    );
                }

            }).fail(function(data) {
                $.notify(
                    {message:"Something went wrong.  Please check your submission and try again."},
                    {type: 'danger'}
                );
            });

        }

        $(document).ready(function(){
                $("#submit_task_button").on('click',submitForm);
            }
        );
    </script>


</head>




<body>

<div class="container">
    <a type="button" class="btn btn-info btn-lg" href="http://coagmento.org/workintent/userDataEntry.php?userID=<?php echo $userID;?>"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back</a>
</div>

<div class="container">
    <h1>User <?php echo $userID;?> Task Survey</h1>
</div>



<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Entry Questionnaire/Interview
        </div>
        <div class="panel-body">
            <form id="task_form">
                <?php
                echo "<input type='hidden' name='userID' value='$userID'/>";
                ?>
                <h3>Please think of the tasks that you normally perform, with respect to your work role, and answer the following questions for each of the tasks.</h3>



                <div class="form-group">
                    <label>(Not to be asked to participant) Enter a name for the task:</label>

                    <textarea class="form-control" rows="1" name="task_name" placeholder="Name"></textarea>
                </div>


                <div class="form-group">
                    <label>Please briefly name and describe the task.</label>

                    <textarea class="form-control" rows="5" name="description" placeholder="Description"></textarea>
                </div>


                <div class="form-group">
                    <label>How often does this task occur?</label>


                    <div class="radio">
                        <label><input type="radio" name="frequency" value="1">Rarely</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="frequency" value="2">Monthly</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="frequency" value="3">Several times a month</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="frequency" value="4">Weekly</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="frequency" value="5">Several times a week</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="frequency" value="6">Daily</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="frequency" value="7">More than once a day</label>
                    </div>

                </div>




                <div class="form-group">
                    <label>How familiar are you with this task?</label>


                    <div class="radio">
                        <label><input type="radio" name="familiarity" value="1">1 (Not at all)</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="familiarity" value="2">2</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="familiarity" value="3">3</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="familiarity" value="4">4</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="familiarity" value="5">5</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="familiarity" value="6">6</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="familiarity" value="7">7 (Completely)</label>
                    </div>

                </div>



                <div class="form-group">
                    <label>How long does it take to complete this task?</label>


                    <div class="radio">
                        <label><input type="radio" name="completiontime" value="1">A few minutes</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="completiontime" value="2">An hour or so</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="completiontime" value="3">Several hours</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="completiontime" value="4">A day</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="completiontime" value="5">Several days</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="completiontime" value="6">A week or two</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="completiontime" value="7">Longer</label>
                    </div>

                </div>





                <div class="form-group">

                    <label>Is this a task that you normally complete on your own?</label>

                    <div class="radio">
                        <label><input type="radio" name="individual_complete" value="Yes">Yes</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="individual_complete" value="No">No</label>
                    </div>

                </div>

                <div class="form-group">

                    <label>If you do it with others, about how many?</label>

                    <div>
                        <textarea   rows="1" cols="20" name="num_collaborators" placeholder="# Collaborators"></textarea>
                    </div>




                </div>













                <button id="submit_task_button" class="btn btn-success">+ Add Task</button>
            </form>


        </div>
    </div>
</div>





</body>
</html>