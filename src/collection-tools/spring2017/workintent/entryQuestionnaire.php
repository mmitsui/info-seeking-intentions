<?php
session_start();
require_once('core/Base.class.php');
require_once('core/Connection.class.php');
require_once('core/Questionnaires.class.php');


date_default_timezone_set('America/New_York');


$cxn = Connection::getInstance();





if(!isset($_POST['email'])){




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
                Entry Questionnaire
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
                        age_1: {
                            required: true,
                            number: true
                        },
                        email: {
                            required: true,
                            email: true,
                            remote: {
                                url:'checkEmail.php',
                                type:'post'
                            }
                        },
                        device_expertise: {
                            required: true
                        },
                        gender: {
                            required: true,
                        },
                        search_years: {
                            required:true,
                            number:true
                        },
                        work_years: {
                            required: true
                        },
                        work_role: {
                            required: true
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

                    var rules_function = function(task_num){
                        $("#task_name_"+task_num).rules("add",{required:true});
                        $("#task_description_"+task_num).rules("add",{required:true});
                        $("input[name='task_frequency_"+task_num+"']").rules("add",{required:true});
                        $("input[name='task_familiarity_"+task_num+"']").rules("add",{required:true});
                        $("input[name='task_completion_"+task_num+"']").rules("add",{required:true});
                        $("input[name='task_individual_"+task_num+"']").rules("add",{required:true});
                        $("#collaboration_number_"+task_num).rules("add",{
                            required:{
                                depends:function(){
                                    return $("#individual_"+task_num+"_No").is(':checked');
                                }
                            },
                            number:true
                        });

                    }

                    rules_function(1);


                    var add_task_function = function(ev){
                        ev.preventDefault();// cancel form submission
                        num_tasks += 1;
                        $("#num_tasks").val(num_tasks);
                        console.log($("#num_tasks").val());


                        var task_string = "<div data-task-num='_"+num_tasks+"' class=\"panel panel-default\">\n" +
                            "                                <div class=\"panel-heading\">\n" +
                            "                                    Task "+num_tasks+"\n" +
                            "                                </div><div class=\"panel-body\">\n" +
                            "\n" +
                            "\n" +
                            "                                    <div class=\"form-group\">\n" +
                            "                                            <label for=\"task_name\">Please briefly name the task.</label>\n" +
                            "                                            <textarea class=\"form-control\" style=\"width:30%;\" id=\"task_name_"+num_tasks+"\" name=\"task_name_"+num_tasks+"\" placeholder=\"Name\" required></textarea>\n" +
                            "                                        <br/>\n" +
                            "                                    </div>\n" +
                            "\n" +
                            "                                    <div class=\"form-group\">\n" +
                            "                                        <label for=\"task_description\">Please briefly describe the task.</label>\n" +
                            "                                        <textarea class=\"form-control\" style=\"width:30%;\" id=\"task_description_"+num_tasks+"\" name=\"task_description_"+num_tasks+"\" placeholder=\"Description\" required></textarea>\n" +
                            "                                        <br/>\n" +
                            "                                    </div>\n" +
                            "\n" +
                            "                                    <div class=\"form-group\">\n" +
                            "                                        <label>How often does this task occur?</label>\n" +
                            "                                        <div id=\"task_frequency_"+num_tasks+"-div\" class=\"container\">\n" +
                            "                                            <div class=\"row\">\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_frequency_"+num_tasks+"_1\" ><input id=\"task_frequency_"+num_tasks+"_1\" type=\"radio\" name=\"task_frequency_"+num_tasks+"\" value=\"1\">1 (Rarely)</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_frequency_"+num_tasks+"_2\" ><input id=\"task_frequency_"+num_tasks+"_2\" type=\"radio\" name=\"task_frequency_"+num_tasks+"\" value=\"2\">2 (Monthly)</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_frequency_"+num_tasks+"_3\" ><input id=\"task_frequency_"+num_tasks+"_3\" type=\"radio\" name=\"task_frequency_"+num_tasks+"\" value=\"3\">3 (Several times a month)</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_frequency_"+num_tasks+"_4\" ><input id=\"task_frequency_"+num_tasks+"_4\" type=\"radio\" name=\"task_frequency_"+num_tasks+"\" value=\"4\">4 (Weekly)</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_frequency_"+num_tasks+"_5\" ><input id=\"task_frequency_"+num_tasks+"_5\" type=\"radio\" name=\"task_frequency_"+num_tasks+"\" value=\"5\">5 (Several times a week)</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_frequency_"+num_tasks+"_6\" ><input id=\"task_frequency_"+num_tasks+"_6\" type=\"radio\" name=\"task_frequency_"+num_tasks+"\" value=\"6\">6 (Daily)</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_frequency_"+num_tasks+"_7\" ><input id=\"task_frequency_"+num_tasks+"_7\" type=\"radio\" name=\"task_frequency_"+num_tasks+"\" value=\"7\">7 (More than once a day)</label></div>\n" +
                            "                                            </div>\n" +
                            "                                        </div>\n" +
                            "                                    </div>\n" +
                            "\n" +
                            "\n" +
                            "\n" +
                            "                                    <div class=\"form-group\">\n" +
                            "                                        <label>How familiar are you with this task?</label>\n" +
                            "                                        <div id=\"task_familiarity_"+num_tasks+"-div\" class=\"container\">\n" +
                            "                                            <div class=\"row\">\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_familiarity_"+num_tasks+"_1\" ><input id=\"task_familiarity_"+num_tasks+"_1\" type=\"radio\" name=\"task_familiarity_"+num_tasks+"\" value=\"1\">1 (Not at all)</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_familiarity_"+num_tasks+"_2\" ><input id=\"task_familiarity_"+num_tasks+"_2\" type=\"radio\" name=\"task_familiarity_"+num_tasks+"\" value=\"2\">2</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_familiarity_"+num_tasks+"_3\" ><input id=\"task_familiarity_"+num_tasks+"_3\" type=\"radio\" name=\"task_familiarity_"+num_tasks+"\" value=\"3\">3</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_familiarity_"+num_tasks+"_4\" ><input id=\"task_familiarity_"+num_tasks+"_4\" type=\"radio\" name=\"task_familiarity_"+num_tasks+"\" value=\"4\">4</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_familiarity_"+num_tasks+"_5\" ><input id=\"task_familiarity_"+num_tasks+"_5\" type=\"radio\" name=\"task_familiarity_"+num_tasks+"\" value=\"5\">5</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_familiarity_"+num_tasks+"_6\" ><input id=\"task_familiarity_"+num_tasks+"_6\" type=\"radio\" name=\"task_familiarity_"+num_tasks+"\" value=\"6\">6</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_familiarity_"+num_tasks+"_7\" ><input id=\"task_familiarity_"+num_tasks+"_7\" type=\"radio\" name=\"task_familiarity_"+num_tasks+"\" value=\"7\">7 (Completely)</label></div>\n" +
                            "                                            </div>\n" +
                            "                                        </div>\n" +
                            "                                    </div>\n" +
                            "\n" +
                            "\n" +
                            "\n" +
                            "                                    <div class=\"form-group\">\n" +
                            "                                        <label>How long does it take to complete this task?</label>\n" +
                            "                                        <div id=\"task_completion_"+num_tasks+"_div\" class=\"container\">\n" +
                            "                                            <div class=\"row\">\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_completion_"+num_tasks+"_1\" ><input id=\"task_completion_"+num_tasks+"_1\" type=\"radio\" name=\"task_completion_"+num_tasks+"\" value=\"1\">1 (A few minutes)</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_completion_"+num_tasks+"_2\" ><input id=\"task_completion_"+num_tasks+"_2\" type=\"radio\" name=\"task_completion_"+num_tasks+"\" value=\"2\">2 (An hour or so)</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_completion_"+num_tasks+"_3\" ><input id=\"task_completion_"+num_tasks+"_3\" type=\"radio\" name=\"task_completion_"+num_tasks+"\" value=\"3\">3 (Several hours)</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_completion_"+num_tasks+"_4\" ><input id=\"task_completion_"+num_tasks+"_4\" type=\"radio\" name=\"task_completion_"+num_tasks+"\" value=\"4\">4 (A day)</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_completion_"+num_tasks+"_5\" ><input id=\"task_completion_"+num_tasks+"_5\" type=\"radio\" name=\"task_completion_"+num_tasks+"\" value=\"5\">5 (Several days)</label></div>\n" +
                            "                                                <div  class=\"col-md-1\"><label for=\"task_completion_"+num_tasks+"_6\" ><input id=\"task_completion_"+num_tasks+"_6\" type=\"radio\" name=\"task_completion_"+num_tasks+"\" value=\"6\">6 (A week or two)</label></div>\n" +
                            "                                                <div style=\"background-color:#F2F2F2\" class=\"col-md-1\"><label for=\"task_completion_"+num_tasks+"_7\" ><input id=\"task_completion_"+num_tasks+"_7\" type=\"radio\" name=\"task_completion_"+num_tasks+"\" value=\"7\">7 (Longer)</label></div>\n" +
                            "                                            </div>\n" +
                            "                                        </div>\n" +
                            "                                    </div>\n" +
                            "\n" +
                            "\n" +
                            "\n" +
                            "\n" +
                            "\n" +
                            "                                    <div class=\"form-group\">\n" +
                            "                                        <label name=\"taskindividual_radio_"+num_tasks+"\">Is this a task you normally complete on your own?</label>\n" +
                            "                                        <div id=\"individual_"+num_tasks+"_div\" class=\"container\">\n" +
                            "                                            <div class=\"radio\">\n" +
                            "                                                <label><input id=\"individual_"+num_tasks+"_Yes\" type=\"radio\" name=\"task_individual_"+num_tasks+"\" value=\"Yes\" required> Yes</label>\n" +
                            "                                            </div>\n" +
                            "                                            <div class=\"radio\">\n" +
                            "                                                <label><input id=\"individual_"+num_tasks+"_No\" type=\"radio\" name=\"task_individual_"+num_tasks+"\" value=\"No\" > No</label>\n" +
                            "                                            </div>\n" +
                            "                                        </div>\n" +
                            "                                    </div>\n" +
                            "\n" +
                            "                                    <div class=\"form-group\">\n" +
                            "                                            <label for=\"collaboration_number_"+num_tasks+"\">If you do it with others, about how many?</label>\n" +
                            "                                            <textarea class=\"form-control\" style=\"width:30%;\" id=\"collaboration_number_"+num_tasks+"\" name=\"collaboration_number_"+num_tasks+"\" placeholder=\"Number\" required></textarea>\n" +
                            "                                        <br/>\n" +
                            "                                    </div>\n" +
                            "                                </div>\n" +
                            "\n" +
                            "                            </div>";

                        $("#taskslist_panel").append(task_string);

                        rules_function(num_tasks);

                    };

                    $("#add_task_button").click(add_task_function);



                });



            </script>
            <script type="text/javascript">
                function viewDetails(check)
                {
                    if (check.checked)
                        document.getElementById("singleStudyDetails").style.display = "block";
                    else
                        document.getElementById("singleStudyDetails").style.display = "none";
                }

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
                    <h3>Entry Questionnaire</h3>

                </div>


                <form id="spr2015_regform" method="post" action="entryQuestionnaire.php">
                    <?php

                    for($x=1;$x<=$NUM_USERS;$x++){

                        echo "<h3>Participant</h3>";




                        echo "<div class=\"form-group\">";
                        echo "<label for=\"email\">Enter the e-mail you used for registration</label>";

                        echo "<textarea id=\"email\" class=\"form-control\" name=\"email\" style=\"width:30%;\" rows=\"1\" cols=\"30\" placeholder=\"Primary Email\" required></textarea>";
                        echo "</div>";

                        ?>


                    <h3>Background Information</h3>
                        <div class="form-group">
                            <label for="age_1">What is your age?</label>
                            <textarea class="form-control" id="age_1" style="width:30%;" rows="1" name="age_1" placeholder="Age" required></textarea>
                            <br/>
                        </div>
                    <div class="form-group">
                        <label name="gender_radio">What is your gender?</label>
                        <div id="gender_div" class="container">
                            <div class="radio">
                                <label><input id="gender-M" type="radio" name="gender" value="M" required> Male</label>
                            </div>
                            <div class="radio">
                                <label><input id="gender-F" type="radio" name="gender" value="F" > Female</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                            <label for="search_years">How many years have you been doing online searching?</label>
                            <textarea class="form-control" id="search_years" style="width:30%;" rows="1" name="search_years" placeholder="Years" required></textarea>
                        <br/>
                    </div>
                        <div class="form-group">
                            <label>Please indicate your level of expertise with searching for information using computing
                                devices, including smartphones.</label>
                            <div id="device_expertise_div" class="container">
                                <div class="row">
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="device_expertise_1" ><input id="device_expertise_1" type="radio" name="device_expertise" value="1">1 (Novice)</label></div>
                                    <div  class="col-md-1"><label for="device_expertise_2" ><input id="device_expertise_2" type="radio" name="device_expertise" value="2">2</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="device_expertise_3" ><input id="device_expertise_3" type="radio" name="device_expertise" value="3">3</label></div>
                                    <div  class="col-md-1"><label for="device_expertise_4" ><input id="device_expertise_4" type="radio" name="device_expertise" value="4">4</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="device_expertise_5" ><input id="device_expertise_5" type="radio" name="device_expertise" value="5">5</label></div>
                                    <div  class="col-md-1"><label for="device_expertise_6" ><input id="device_expertise_6" type="radio" name="device_expertise" value="6">6</label></div>
                                    <div style="background-color:#F2F2F2" class="col-md-1"><label for="device_expertise_7" ><input id="device_expertise_7" type="radio" name="device_expertise" value="7">7 (Expert)</label></div>
                                </div>
                            </div>
                        </div>



                        <hr/>
                        <h3>General Work Information</h3>
                        <div class="form-group">
                                <label for="work_years">How long have you been at your present work organization? If you are self-employed, please indicate that (SE), and for how long.</label>
                                <textarea class="form-control" style="width:30%;" id="work_years" name="work_years" placeholder="Length of time (and SE if applicable)" required></textarea>
                            <br/>
                        </div>

                        <div class="form-group">
                                <label for="work_role">What is your work role?</label>
                                <textarea class="form-control" style="width:30%;" id="work_role" name="work_role" placeholder="Work role" required></textarea>
                            <br/>
                        </div>

                        <hr/>

                        <h3>Work Task</h3>


                        Please think of the tasks that you normally perform, with respect to your work role, and answer the following questions for each of the tasks.

                        <div id="taskslist_panel">
                            <div data-task-num='1' class="panel panel-default">
                                <div class="panel-heading">
                                    Task 1
                                </div>
                                <div class="panel-body">


                                    <div class="form-group">
                                            <label for="task_name">Please briefly name the task.</label>
                                            <textarea class="form-control" style="width:30%;" id="task_name_1" name="task_name_1" placeholder="Name" required></textarea>
                                        <br/>
                                    </div>

                                    <div class="form-group">
                                        <label for="task_description">Please briefly describe the task.</label>
                                        <textarea class="form-control" style="width:30%;" id="task_description_1" name="task_description_1" placeholder="Description" required></textarea>
                                        <br/>
                                    </div>

                                    <div class="form-group">
                                        <label>How often does this task occur?</label>
                                        <div id="task_frequency_1-div" class="container">
                                            <div class="row">
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_frequency_1_1" ><input id="task_frequency_1_1" type="radio" name="task_frequency_1" value="1">1 (Rarely)</label></div>
                                                <div  class="col-md-1"><label for="task_frequency_1_2" ><input id="task_frequency_1_2" type="radio" name="task_frequency_1" value="2">2 (Monthly)</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_frequency_1_3" ><input id="task_frequency_1_3" type="radio" name="task_frequency_1" value="3">3 (Several times a month)</label></div>
                                                <div  class="col-md-1"><label for="task_frequency_1_4" ><input id="task_frequency_1_4" type="radio" name="task_frequency_1" value="4">4 (Weekly)</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_frequency_1_5" ><input id="task_frequency_1_5" type="radio" name="task_frequency_1" value="5">5 (Several times a week)</label></div>
                                                <div  class="col-md-1"><label for="task_frequency_1_6" ><input id="task_frequency_1_6" type="radio" name="task_frequency_1" value="6">6 (Daily)</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_frequency_1_7" ><input id="task_frequency_1_7" type="radio" name="task_frequency_1" value="7">7 (More than once a day)</label></div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <label>How familiar are you with this task?</label>
                                        <div id="task_familiarity_1-div" class="container">
                                            <div class="row">
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_familiarity_1_1" ><input id="task_familiarity_1_1" type="radio" name="task_familiarity_1" value="1">1 (Not at all)</label></div>
                                                <div  class="col-md-1"><label for="task_familiarity_1_2" ><input id="task_familiarity_1_2" type="radio" name="task_familiarity_1" value="2">2</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_familiarity_1_3" ><input id="task_familiarity_1_3" type="radio" name="task_familiarity_1" value="3">3</label></div>
                                                <div  class="col-md-1"><label for="task_familiarity_1_4" ><input id="task_familiarity_1_4" type="radio" name="task_familiarity_1" value="4">4</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_familiarity_1_5" ><input id="task_familiarity_1_5" type="radio" name="task_familiarity_1" value="5">5</label></div>
                                                <div  class="col-md-1"><label for="task_familiarity_1_6" ><input id="task_familiarity_1_6" type="radio" name="task_familiarity_1" value="6">6</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_familiarity_1_7" ><input id="task_familiarity_1_7" type="radio" name="task_familiarity_1" value="7">7 (Completely)</label></div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <label>How long does it take to complete this task?</label>
                                        <div id="task_completion_1_div" class="container">
                                            <div class="row">
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_completion_1_1" ><input id="task_completion_1_1" type="radio" name="task_completion_1" value="1">1 (A few minutes)</label></div>
                                                <div  class="col-md-1"><label for="task_completion_1_2" ><input id="task_completion_1_2" type="radio" name="task_completion_1" value="2">2 (An hour or so)</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_completion_1_3" ><input id="task_completion_1_3" type="radio" name="task_completion_1" value="3">3 (Several hours)</label></div>
                                                <div  class="col-md-1"><label for="task_completion_1_4" ><input id="task_completion_1_4" type="radio" name="task_completion_1" value="4">4 (A day)</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_completion_1_5" ><input id="task_completion_1_5" type="radio" name="task_completion_1" value="5">5 (Several days)</label></div>
                                                <div  class="col-md-1"><label for="task_completion_1_6" ><input id="task_completion_1_6" type="radio" name="task_completion_1" value="6">6 (A week or two)</label></div>
                                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="task_completion_1_7" ><input id="task_completion_1_7" type="radio" name="task_completion_1" value="7">7 (Longer)</label></div>
                                            </div>
                                        </div>
                                    </div>





                                    <div class="form-group">
                                        <label name="taskindividual_radio_1">Is this a task you normally complete on your own?</label>
                                        <div id="individual_1_div" class="container">
                                            <div class="radio">
                                                <label><input id="individual_1_Yes" type="radio" name="task_individual_1" value="Yes" required> Yes</label>
                                            </div>
                                            <div class="radio">
                                                <label><input id="individual_1_Yes" type="radio" name="task_individual_1" value="No" > No</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                            <label for="collaboration_number_1">If you do it with others, about how many?</label>
                                            <textarea class="form-control" style="width:30%;" id="collaboration_number_1" name="collaboration_number_1" placeholder="Number" required></textarea>
                                        <br/>
                                    </div>
                                </div>

                            </div>

                        </div>






                        <hr/>
                    <?php






//Demographic Survey








                    }
                    ?>

                    <input type="hidden" id="num_tasks" name="num_tasks" value="1">
                    <button id="add_task_button" class="btn btn-success">+ Add Another Task</button>
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

}else{

    $cxn = Connection::getInstance();
    $email = $_POST['email'];

    $query = "SELECT userID,email1,firstName,lastName FROM recruits WHERE `email1`='$email'";
//    }else{
//        $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits) a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE `username`='$username' AND `password`='$password') b on a.userID=b.userID";
//    }

    $results = $cxn->commit($query);
    $line = mysql_fetch_array($results,MYSQL_ASSOC);

    $email = mysql_escape_string($email);
    $userID = $line['userID'];
    $age = $_POST['age_1'];
    $gender = $_POST['gender'];


    $device_expertise = $_POST['device_expertise'];
    $search_years = $_POST['search_years'];
    $work_years = mysql_escape_string($_POST['work_years']);
    $work_role = mysql_escape_string($_POST['work_role']);
    $num_tasks = $_POST['num_tasks'];

    $task_data = array();
    for($i=1;$i<=$num_tasks;$i+=1){
        $task_datum = array(
            'task_name'=>$_POST["task_name_$i"],
            'task_description'=>$_POST["task_description_$i"],
            'task_frequency'=>$_POST["task_frequency_$i"],
            'task_familiarity'=>$_POST["task_familiarity_$i"],
            'task_completion'=>$_POST["task_completion_$i"],
            'task_individual'=>$_POST["task_individual_$i"],
            'task_collaborationnumber'=>$_POST["collaboration_number_$i"]
        );
        array_push($task_data,$task_datum);

    }

    $base = Base::getInstance();
    $time = $base->getTime();
    $date = $base->getDate();
    $timestamp = $base->getTimestamp();

    $task_data = mysql_escape_string(json_encode($task_data));
    $query = "INSERT INTO questionnaire_entry (`userID`,`email`,`age`,`gender`,`search_years`,`num_tasks`,`device_expertise`,`work_years`,`work_role`,`task_data`,`date`,`time`,`timestamp`) VALUES ('$userID','$email','$age','$gender','$search_years','$num_tasks','$device_expertise','$work_years','$work_role','$task_data','$date','$time','$timestamp')";
    $cxn->commit($query);


    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: Information Seeking Intentions <mmitsui@scarletmail.rutgers.edu>' . "\r\n";
    $headers .= 'Bcc: Matthew Mitsui <mmitsui@scarletmail.rutgers.edu>' . "\r\n";

    $subject = "Search intentions in natural settings study entry questionnaire confirmation";



    $message = "<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type content='text/html; charset=utf-8' />";
    $message .= "\r\n";
    $message .= "<title>Search intentions in natural settings entry questionnaire confirmation email</title></head>\n<body>\n";
    $message .= "\r\n";
    $message .= "Thank you for submitting the answer to your entrance questionnaire.<br/><br/>";

    $message .= "\r\n";
    $message .= "Now that you have completed the entry questionnaire, you will receive a confirmation e-mail in 24-48 hours on downloading and installing the plugin for this study. Feel free to <a href=\"mailto:mmitsui@scarletmail.rutgers.edu?subject=Study inquiry\">contact us</a> if you have any questions.";
    $message .= "\r\n";
    $message .= "</body></html>";

    mail ($email, $subject, $message, $headers); //Notificaiton to Participant's primary email
    mail ('mmitsui@scarletmail.rutgers.edu', $subject, $message, $headers); //Copy to researchers conducting the study
    mail ('mmitsui88@gmail.com', $subject, $message, $headers); //Copy to researchers conducting the study
//                mail ('belkin@rutgers.edu', $subject, $message, $headers); //Copy to researchers conducting the study
//                mail ('erha43@gmail.com', $subject, $message, $headers); //Copy to researchers conducting the study


?>

    <html>
        <head>

            <link rel="stylesheet" href="study_styles/bootstrap-3.3.7-dist/css/bootstrap.css">
            <link rel="stylesheet" href="study_styles/custom/text.css">
            <link rel="stylesheet" href="styles.css">
            <title>
    Entry Questionnaire
    </title>
    </head>

    <body>
    <div class="panel panel-default">
        <div class="panel-body">
            <p>Thanks for submitting your questionnaire!</p>
            <p>You will receive a confirmation e-mail.  Afterwards, an additional e-mail will be sent to you in the next 24-48 hours instructing you on how to download and install the plugin.</p>
        </div>

    </div>

    </body>
    </html>
    <?php
}
?>
