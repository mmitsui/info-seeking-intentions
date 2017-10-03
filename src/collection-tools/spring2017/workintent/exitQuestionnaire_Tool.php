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
            Exit Questionnaire - Tool
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
                        },


                        logannotation_clear: {
                            required:true
                        },
                        intentions_understandable: {
                            required: true
                        },
                        intentions_adequate: {
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
                <h3>Exit Questionnaire - Tool</h3>

            </div>


            <form id="spr2015_regform" method="post" action="exitQuestionnaire_Tool.php">
                <?php

                for($x=1;$x<=$NUM_USERS;$x++){

                    echo "<h3>Participant</h3>";




                    echo "<div class=\"form-group\">";
                    echo "<label for=\"email\">Enter the e-mail you used for registration</label>";

                    echo "<textarea id=\"email\" class=\"form-control\" name=\"email\" style=\"width:30%;\" rows=\"1\" cols=\"30\" placeholder=\"Primary Email\" required></textarea>";
                    echo "</div>";

                    ?>


                    <h3>Procedure Review</h3>




                    <div class="form-group">
                        <label>Was the process of log review and annotation clear?</label>
                        <div id="logannotation_clear" class="container">
                            <div class="row">
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="logannotation_clear_1" ><input id="logannotation_clear_1" type="radio" name="logannotation_clear" value="1">1 (Not at all)</label></div>
                                <div  class="col-md-1"><label for="logannotation_clear_2" ><input id="logannotation_clear_2" type="radio" name="logannotation_clear" value="2">2</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="logannotation_clear_3" ><input id="logannotation_clear_3" type="radio" name="logannotation_clear" value="3">3</label></div>
                                <div  class="col-md-1"><label for="logannotation_clear_4" ><input id="logannotation_clear_4" type="radio" name="logannotation_clear" value="4">4</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="logannotation_clear_5" ><input id="logannotation_clear_5" type="radio" name="logannotation_clear" value="5">5</label></div>
                                <div  class="col-md-1"><label for="logannotation_clear_6" ><input id="logannotation_clear_6" type="radio" name="logannotation_clear" value="6">6</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="logannotation_clear_7" ><input id="logannotation_clear_7" type="radio" name="logannotation_clear" value="7">7 (Completely)</label></div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    Please evaluate the intentions provided in the annotation part

                    <div class="form-group">
                        <label>Was the set of intentions that you could choose from understandable?</label>
                        <div id="intentions_understandable_div" class="container">
                            <div class="row">
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="intentions_understandable_1" ><input id="intentions_understandable_1" type="radio" name="intentions_understandable" value="1">1 (Not at all)</label></div>
                                <div  class="col-md-1"><label for="intentions_understandable_2" ><input id="intentions_understandable_2" type="radio" name="intentions_understandable" value="2">2</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="intentions_understandable_3" ><input id="intentions_understandable_3" type="radio" name="intentions_understandable" value="3">3</label></div>
                                <div  class="col-md-1"><label for="intentions_understandable_4" ><input id="intentions_understandable_4" type="radio" name="intentions_understandable" value="4">4</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="intentions_understandable_5" ><input id="intentions_understandable_5" type="radio" name="intentions_understandable" value="5">5</label></div>
                                <div  class="col-md-1"><label for="intentions_understandable_6" ><input id="intentions_understandable_6" type="radio" name="intentions_understandable" value="6">6</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="intentions_understandable_7" ><input id="intentions_understandable_7" type="radio" name="intentions_understandable" value="7">7 (Completely)</label></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Was the set of intentions that you could choose from adequate?</label>
                        <div id="intentions_adequate_div" class="container">
                            <div class="row">
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="intentions_adequate_1" ><input id="intentions_adequate_1" type="radio" name="intentions_adequate" value="1">1 (Not at all)</label></div>
                                <div  class="col-md-1"><label for="intentions_adequate_2" ><input id="intentions_adequate_2" type="radio" name="intentions_adequate" value="2">2</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="intentions_adequate_3" ><input id="intentions_adequate_3" type="radio" name="intentions_adequate" value="3">3</label></div>
                                <div  class="col-md-1"><label for="intentions_adequate_4" ><input id="intentions_adequate_4" type="radio" name="intentions_adequate" value="4">4</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="intentions_adequate_5" ><input id="intentions_adequate_5" type="radio" name="intentions_adequate" value="5">5</label></div>
                                <div  class="col-md-1"><label for="intentions_adequate_6" ><input id="intentions_adequate_6" type="radio" name="intentions_adequate" value="6">6</label></div>
                                <div style="background-color:#F2F2F2" class="col-md-1"><label for="intentions_adequate_7" ><input id="intentions_adequate_7" type="radio" name="intentions_adequate" value="7">7 (Completely)</label></div>
                            </div>
                        </div>
                    </div>














                    <hr/>
                    <?php







//Demographic Survey








                }
                ?>

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

    if($email=='mmitsui@scarletmail.rutgers.edu'){
        $query = "SELECT userID,email1,firstName,lastName FROM recruits WHERE `userID`='1000'";
    }else{
        $query = "SELECT userID,email1,firstName,lastName FROM recruits WHERE `email1`='$email'";
    }



//    }else{
//        $query = "SELECT * FROM (SELECT userID,email1,firstName,lastName FROM recruits) a INNER JOIN (SELECT `userID`,`username`,`password` from users WHERE `username`='$username' AND `password`='$password') b on a.userID=b.userID";
//    }

    $results = $cxn->commit($query);
    $line = mysql_fetch_array($results,MYSQL_ASSOC);

    $email = mysql_escape_string($email);
    $userID = $line['userID'];
    $intentions_understandable= $_POST['intentions_understandable'];
    $logannotation_clear= $_POST['logannotation_clear'];
    $intentions_adequate= $_POST['intentions_adequate'];





    $base = Base::getInstance();
    $time = $base->getTime();
    $date = $base->getDate();
    $timestamp = $base->getTimestamp();

    $query = "INSERT INTO questionnaire_exit_tool (`userID`,`email`,`intentions_understandable`,`logannotation_clear`,`intentions_adequate`,`date`,`time`,`timestamp`) VALUES ('$userID','$email','$intentions_understandable','$logannotation_clear','$intentions_adequate','$date','$time','$timestamp')";
    $cxn->commit($query);

    ?>

    <html>
    <head>

        <link rel="stylesheet" href="study_styles/bootstrap-3.3.7-dist/css/bootstrap.css">
        <link rel="stylesheet" href="study_styles/custom/text.css">
        <link rel="stylesheet" href="styles.css">
        <title>
            Exit Questionnaire - Tool
        </title>
    </head>

    <body>

    <form id="myForm" action="exitQuestionnaire_Tasks.php" method="post">
        <?php
        echo "<input type='hidden' name='email' value='$email'>";
        ?>
    </form>
    <script type="text/javascript">
        document.getElementById('myForm').submit();
    </script>

    </body>
    </html>
    <?php
}
?>
