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
        Demographic Questionnaire
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./lib/bootstrap_notify/bootstrap-notify.min.js"></script>


    <script>

        function submitForm(ev){
            ev.preventDefault();
            var formData = $('#demographic_form').serialize();
            $.ajax({
                type: 'POST',
                url: 'http://coagmento.org/workintent/submitDemographic.php',
                data: formData
            }).done(function(response) {
                response = JSON.parse(response);
                if(response.hasOwnProperty('success') && response['success']){


                    $.notify(
                        {message:"You have successfully submitted the demographic survey!"},
                        {type: 'success'}
                    );

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
                $("#submit_demographic_button").on('click',submitForm);
            }
        );
    </script>


</head>




<body>

<div class="container">
    <a type="button" class="btn btn-info btn-lg" href="http://coagmento.org/workintent/userDataEntry.php?userID=<?php echo $userID;?>"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back</a>
</div>

<div class="container">
    <h1>User <?php echo $userID;?> Demographic Survey</h1>
</div>

<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Entry Questionnaire/Interview
        </div>
        <div class="panel-body">
            <form id="demographic_form">
                <?php
                echo "<input type='hidden' name='userID' value='$userID'/>";
                ?>

                <div class="form-group">
                    <label>1. What is your gender/age?</label>

                    <div>
                    <textarea   rows="1" cols="5" name="age" placeholder="Age"></textarea>
                    </div>


                    <div class="radio">
                        <label><input type="radio" name="gender" value="Male">Male</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="gender" value="Female">Female</label>
                    </div>

                </div>


                <div class="form-group">
                    <label>2. How many years have you been doing online searching?</label>

                    <textarea class="form-control" rows="5" name="search_years" placeholder="Search Years"></textarea>

                </div>

                <div class="form-group">
                    <label>3. Please indicate your level of expertise with searching for information using computing devices, including smartphones.</label>


                    <div class="radio">
                        <label><input type="radio" name="device_expertise" value="1">1 (Novice)</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="device_expertise" value="2">2</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="device_expertise" value="3">3</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="device_expertise" value="4">4</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="device_expertise" value="5">5</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="device_expertise" value="6">6</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="device_expertise" value="7">7 (Expert)</label>
                    </div>

                </div>

                <div class="form-group">
                    <label>4. How long have you been at your present work organization? If you are self-employed, please indicate that (SE), and for how long.</label>

                    <textarea class="form-control" rows="1" name="work_years" placeholder="Work Years"></textarea>

                </div>

                <div class="form-group">
                    <label>5. What is your work role?</label>

                    <textarea class="form-control" rows="5" name="work_role" placeholder="Work Role"></textarea>
                </div>


                <button id="submit_demographic_button" class="btn btn-primary">Submit</button>
            </form>


        </div>
    </div>
</div>





</body>
</html>