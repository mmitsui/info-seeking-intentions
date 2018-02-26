<?php
session_start();
require_once('core/Connection.class.php');
require_once('core/Questionnaires.class.php');


date_default_timezone_set('America/New_York');

$num_recruits = 0;
$recruit_limit =44; // Current Recruitment Limit as of 07/15/2014
$section_closed = false;
$closed = false;


$cxn = Connection::getInstance();
$query = "SELECT COUNT(*) as ct from recruits WHERE userID <500";
$results = $cxn->commit($query);
$line = mysql_fetch_array($results, MYSQL_ASSOC);
$num_recruits = $line['ct'];




function availableDates(){
    $cxn = Connection::getInstance();
    $query = "SELECT * FROM questionnaire_questions WHERE `key`='date_firstchoice' AND questionID=1038 AND question_cat='fall2015intent'";
    $results = $cxn->commit($query);
    $line = mysql_fetch_array($results, MYSQL_ASSOC);
    $js = json_decode($line['question_data']);
    $dates_available = array();
    foreach($js->{'options'} as $key=>$val){
        $thesplit = explode(",",$val);
        $monthday = $thesplit[0];

//        array_push($dates_available,$val);
        if(strtotime($monthday)-strtotime("today midnight")>=86400){
            array_push($dates_available,$val);
        }
    }




    $query = "SELECT * FROM recruits WHERE date_firstchoice != ''";
    $results = $cxn->commit($query);
    $dates_taken = array();
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
        array_push($dates_taken,$line['date_firstchoice']);
    }


    return array_diff($dates_available,$dates_taken);

}





function availableDates2(){
    $cxn = Connection::getInstance();
    $query = "SELECT * FROM questionnaire_questions WHERE `key`='date_secondchoice' AND questionID=1091 AND question_cat='fall2015intent'";
    $results = $cxn->commit($query);
    $line = mysql_fetch_array($results, MYSQL_ASSOC);
    $js = json_decode($line['question_data']);
    $dates_available = array();
    foreach($js->{'options'} as $key=>$val){
        $thesplit = explode(",",$val);
        $monthday = $thesplit[0];

        array_push($dates_available,$val);
//        if(strtotime($monthday)-strtotime("today midnight")>=86400){
//            array_push($dates_available,$val);
//        }
    }

    $query = "SELECT * FROM recruits WHERE date_firstchoice != ''";
    $results = $cxn->commit($query);
    $dates_taken = array();
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
        array_push($dates_taken,$line['date_secondchoice']);
    }


    return array_diff($dates_available,$dates_taken);
}


function allSlotsTaken(){
    return count(availableDates()) <= 0;
}




if(!isset($_POST['consentRead'])){

    ?>

    <html>
    <head>
    </head>
    <body>
    <h3>Search Intentions Study: Read Consent Form!!!!</h3>
    <hr>
    <p>You have to first read the consent page and agree to the conditions before registering for participating in the study.</p>
    <p>Please visit <a href="http://coagmento.org/workintent/consent.php">consent form</a> first, read and accept the study conditions.</p>
    </body>
    </html>

    <?php
    exit();

}

if($num_recruits<=$recruit_limit && !$closed && !$section_closed && !allSlotsTaken())
{
	if(1)
	{
        $NUM_USERS = 1;
        $questionnaire = Questionnaires::getInstance();
				$questionnaire->clearCache();
        $questionnaire->populateQuestionsFromDatabase("fall2015intent","questionID ASC");


	?>
<html>
<head>

  <link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">
  <link rel="stylesheet" href="study_styles/custom/text.css">
  <link rel="stylesheet" href="styles.css">
	<title>
			Research Study Registration
    </title>

    <style>
    select {
      font-size:13px;
    }
    </style>
    <?php echo $questionnaire->printPreamble();?>


		<script type="text/javascript">


            var validateDates = function(){
                var date1 = parseInt($('#date_firstchoice_1 option:selected').attr('val'));
                var date2 = parseInt($('#date_secondchoice_1 option:selected').attr('val'));


                if(date2-date1!=864000 && date2-date1!=860400){

//                if(date2-date1 != 259200){
                    if($(this).is($('#date_firstchoice_1'))){
                        $("#date_secondchoice_1").val($("#date_secondchoice_1 option:first").val());

                        $("#date_secondchoice_span").html("Please choose the Monday 10 days after your pre-task interview.");
//                        $("#date_secondchoice_span").html("Please choose the Monday after your pre-task interview.");
                        $("#date_firstchoice_span").html("");

                    }else{
                        $("#date_firstchoice_1").val($("#date_firstchoice_1 option:first").val());
                        $("#date_firstchoice_span").html("Please choose the Friday 10 days before your pre-task interview.");
//                        $("#date_firstchoice_span").html("Please choose the Friday before your pre-task interview.");
                        $("#date_secondchoice_span").html("");

                    }
                }else{
                    $("#date_firstchoice_span").html("");

                    $("#date_secondchoice_span").html("");

                }

            }



		$().ready(function(){

            $("#timezone_1").timezones();
			$.validator.addMethod("notEqualTo", function (value, element, param)
			{
			    var target = $(param);
			    if (value) return value != target.val();
			    else return this.optional(element);
			}, "Does not match");

			$('#date_firstchoice_1').change(validateDates);
            $('#date_secondchoice_1').change(validateDates);
		});
		</script>
    <script src="lib/easy-timezone-picker/dist/timezones.full.min.js"></script>

    <script>

        jQuery.validator.addMethod("rankedorder", function(value, element) {
            return isRankedOrderValid(value);
            }, "<span style='color:red'>Please specify a ranked order according to the description above.</span>");
        $().ready(function(){$("#spr2015_regform").validate({ignore:"",
            rules: {firstName_1: "required",
            lastName_1: "required",
            age_1: {
                required: true,
                number: true
            },
            email1_1: {
                required: true,
                email: true
            },
                study_source_1: {
                    required: true,
                },
                interview_medium_1:{
                required:true
                },
                medium_credentials_1:
                    {
                        required: true
                    },


                date_firstchoice_1: {
                    required:true
                 },

                timezone_1:{
                    required:true
                },

                date_secondchoice_1: {
                    required:true
                },

            reEmail_1: {
                required: true,
                email: true,
                equalTo: "#email1_1"
            }
            },
            messages: {
            firstName_1: {required:"<span style='color:red'>Please enter your first name.</span>"},
            lastName_1: {required:"<span style='color:red'>Please enter your last name.</span>"},
            age_1: {
                required:"<span style='color:red'>Please enter your age.</span>",
                    number:"<span style='color:red'>Please enter a number.</span>"
            },
            email1_1: {
                required: "<span style='color:red'>Please enter your e-mail address.</span>",
                    email: "<span style='color:red'>Please enter a valid e-mail address.</span>"
            },
                study_source_1: {
                    required: "<span style='color:red'>Please how you discovered this study.</span>",
                },
                 date_firstchoice_1: {
                 	required: "<span style='color:red'>Please enter a date.</span>",
                 },

                timezone_1:{
                    required: "<span style='color:red'>Please enter a time zone.</span>",
                },

                date_secondchoice_1: {
                    required: "<span style='color:red'>Please enter a date.</span>",
                },
            reEmail_1: {
                required: "<span style='color:red'>Please enter your e-mail address.</span>",
                    email: "<span style='color:red'>Please enter a valid e-mail address.</span>",
                    equalTo: "<span style='color:red'>Please enter the same e-mail address again.</span>",
            },
                interview_medium_1:{
                    required: "<span style='color:red'>Please enter your preferred method of contact.</span>",
                },
                medium_credentials_1:
                    {
                        required: "<span style='color:red'>Please enter your username.</span>",
                    },

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
	<h3>Research Study Registration</h3>
		<table class="body" width=90%>
			<tr>
			  <td colspan=2>
				<ul>
				<li>Use this form to register for the paid research study on <em>Search Intentions in Natural Settings</em>.</li>
				<li>Please fill out the information below then click Submit.</li>
                    <li>You will then receive a confirmation e-mail about completing an entry questionnaire.</li>
				<li>Afterward the entry questionnaire, you will receive a confirmation email within 24-48 hours with details about downloading and installing the software for the study.</li>

				<li><a href="mailto:mmitsui@scarletmail.rutgers.edu?subject=Study inquiry">Contact us</a> if you have any questions.</li>
				</ul>
				</td>
			</tr>
<!--
Registration
-->
			<tr>
				<td>
					<p><strong>Check here to if you would like to read recruitment details again. </strong><input type="checkbox" name="viewInstructionsCheckSingle" id="viewInstructionsCheckSingle" onclick="viewDetails(this)" /></p>
					<br />
					<div style="display: none; background: #F2F2F2; text-align:center; border-style:solid; width:70%; border-color:blue; padding:25px;" id="singleStudyDetails">
	 					<table class="body" width="100%">
							<tr>
								<td>


                                    <div>
                                        <p>Welcome! This is the sign-up form to register for the paid research study.</p>
                                    </div>
                                    <p>

                                        The research project, <i>Search Intentions in Natural Settings</i>, funded by the National Science Foundation, seeks
                                        participants in a study of information seeking. Participants will conduct searches for their work in a naturalistic
                                        setting - i.e., their work environment - for information relating to different kinds of information search tasks
                                        related to their employment. Participants will first conduct an initial interview in which they are asked for
                                        demographic information, introduced to the study’s software, and asked about regular search tasks, lasting
                                        <strong>about one hour</strong>. This will be followed by an experimental session.
                                        Participants will be asked to <strong>record their searching activity over the course of five days</strong> and to
                                        annotate the tasks that they conduct which will take <strong>about one hour each day</strong>, as well as to explain
                                        their search intentions at self-selected points during their tasks. Various aspects of their searching
                                        behavior will be recorded for subsequent analysis. The study will conclude with an exit interview,
                                        in which participants will be asked to analyze their search experiences and to give characterizations of the tasks they performed during the five days; this last session will last about one hour.

                                    </p>

                                    <p>
                                        All volunteers for this study will receive <strong>$95.00</strong> cash for their participation. Taking part in this study will
                                        help to advance the understanding of the search process and contribute towards development of search systems
                                        that can automatically adapt to a user's specific search goals.

                                    </p>








                                    <p>Requirements:
                                    <ul>
                                        <li>You must be at least 18 years old to participate.</li>
                                        <li>You must be a non-student and employed.</li>
                                        <li>Your work must at least occasionally entail information seeking.</li>
                                        <li>Proficiency in English is required.</li>
                                        <li>Intermediate typing and online search skills are required.</li>
                                        <li>You must use Google Chrome throughout the duration of the study.</li>
                                    </ul>
                                    </p>



                                    <p>
                                        You will not be offered or receive any special consideration if you take part in this research; it is purely
                                        voluntary. This study has been approved by the Rutgers Institutional Review Board (IRB Study #18-057), and will be
                                        supervised by Dr. Nicholas Belkin (belkin@rutgers.edu) and Dr. Chirag Shah (chirags@rutgers.edu) at the School
                                        of Communication and Information.
                                    </p>

                                    <p>For more information about this study, please send e-mail to Matthew Mitsui at
                                        <a href="mailto:mmitsui@scarletmail.rutgers.edu?Subject=Study%20Inquiry" target="_top">mmitsui@scarletmail.rutgers.edu</a>.
                                        You can also contact Matthew Mitsui to ask questions or get more information about the project. </p>


								</td>
							</tr>
	  					</table>
					</div>


<form id="spr2015_regform" class="pure-form" method="post" action="signupGroup.php">
<?php
echo "<input type=\"hidden\" name=\"num_users\" value=\"$NUM_USERS\">";

for($x=1;$x<=$NUM_USERS;$x++){

  echo "<h3>Participant Form</h3>";

  echo "<div class=\"pure-form-aligned\">";
  echo "<fieldset>";
  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"firstName_$x\">First Name</label>";
  echo "<input id=\"firstName_$x\" name=\"firstName_$x\" type=\"text\" placeholder=\"First Name\" required>";
  echo "</div>";

  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"lastName_$x\">Last Name</label>";
  echo "<input id=\"lastName_$x\" name=\"lastName_$x\" type=\"text\" placeholder=\"Last Name\" required>";
  echo "</div>";

	echo "<div class=\"pure-control-group\">";
  echo "<label for=\"age_$x\">Age</label>";
  echo "<input id=\"age_$x\" name=\"age_$x\" type=\"text\" placeholder=\"Age\" required>";
  echo "</div>";

  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"email1_$x\">Email Address</label>";
  echo "<input id=\"email1_$x\" name=\"email1_$x\" type=\"text\" placeholder=\"Primary Email\" required>";
  echo "</div>";

  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"reEmail_$x\">Confirm Email</label>";
  echo "<input id=\"reEmail_$x\" name=\"reEmail_$x\" type=\"text\" placeholder=\"Confirm Email\" required>";
  echo "</div>";


    echo "<div class=\"pure-control-group\">";
    echo "<label for=\"study_source_$x\">How did you find out about this study? (Facebook, Twitter, Mailing List, etc.)</label>";
    echo "<input id=\"study_source_$x\" name=\"study_source_$x\" type=\"text\" size='50' placeholder=\"Source\" required>";
    echo "</div>";

    echo "<br>";


    echo "<div class=\"pure-control-group\">";
    echo "<label for=\"interview_medium_$x\">For this study, we would like to conduct an entry interview and an exit interview.  For these, we would like to use Skype, Google Hangouts, or any medium with equivalent video and audio capabilities.  Which works best for you?</label>";
    echo "<select id=\"interview_medium_$x\" name=\"interview_medium_$x\">";
    echo "<option disabled selected value> -- select an option -- </option>";
    echo "<option value='Skype'>Skype</option>";
    echo "<option value='Google Hangouts'>Google Hangouts</option>";
    echo "<option value='Other'>Other</option>";
    echo "</select>";

    echo "</div>";

    echo "<div class=\"pure-control-group\">";
    echo "<label for=\"medium_credentials_$x\">For the above, provide the username we can use to contact you (e.g., Skype Name). If you selected 'Other', please also provide the name of the application.</label>";
    echo "<input id=\"medium_credentials_$x\" name=\"medium_credentials_$x\" type=\"text\" placeholder=\"Username (& Application)\" required>";
    echo "</div>";



  echo "</fieldset>";
  echo "</div>";
  if($NUM_USERS >1){
    echo "<hr>";
  }


  if(isset($_POST['readConsent']) && $_POST['readConsent']){
      echo "<input type='hidden' name='consent_furtheruse' value='1'>";

  }else{
      echo "<input type='hidden' name='consent_furtheruse' value='0'>";
  }
    ?>


    <div class="pure-control-group">
        <div id="timezone_1_div">
            <label name="timezone_1">Please choose the time zone that most closely matches where you'll conduct your work:</label>
            <span style="color:red" id="timezone_span"></span>
            <select id="timezone_1" name="timezone_1" required>

<!--                <option disabled selected>--Select one--</option><option value="Africa/Abidjan">Africa/Abidjan</option><option value="Africa/Accra">Africa/Accra</option><option value="Africa/Addis_Ababa">Africa/Addis Ababa</option><option value="Africa/Algiers">Africa/Algiers</option><option value="Africa/Asmara">Africa/Asmara</option><option value="Africa/Bamako">Africa/Bamako</option><option value="Africa/Bangui">Africa/Bangui</option><option value="Africa/Banjul">Africa/Banjul</option><option value="Africa/Bissau">Africa/Bissau</option><option value="Africa/Blantyre">Africa/Blantyre</option><option value="Africa/Brazzaville">Africa/Brazzaville</option><option value="Africa/Bujumbura">Africa/Bujumbura</option><option value="Africa/Cairo">Africa/Cairo</option><option value="Africa/Casablanca">Africa/Casablanca</option><option value="Africa/Ceuta">Africa/Ceuta</option><option value="Africa/Conakry">Africa/Conakry</option><option value="Africa/Dakar">Africa/Dakar</option><option value="Africa/Dar_es_Salaam">Africa/Dar es Salaam</option><option value="Africa/Djibouti">Africa/Djibouti</option><option value="Africa/Douala">Africa/Douala</option><option value="Africa/El_Aaiun">Africa/El Aaiun</option><option value="Africa/Freetown">Africa/Freetown</option><option value="Africa/Gaborone">Africa/Gaborone</option><option value="Africa/Harare">Africa/Harare</option><option value="Africa/Johannesburg">Africa/Johannesburg</option><option value="Africa/Kampala">Africa/Kampala</option><option value="Africa/Khartoum">Africa/Khartoum</option><option value="Africa/Kigali">Africa/Kigali</option><option value="Africa/Kinshasa">Africa/Kinshasa</option><option value="Africa/Lagos">Africa/Lagos</option><option value="Africa/Libreville">Africa/Libreville</option><option value="Africa/Lome">Africa/Lome</option><option value="Africa/Luanda">Africa/Luanda</option><option value="Africa/Lubumbashi">Africa/Lubumbashi</option><option value="Africa/Lusaka">Africa/Lusaka</option><option value="Africa/Malabo">Africa/Malabo</option><option value="Africa/Maputo">Africa/Maputo</option><option value="Africa/Maseru">Africa/Maseru</option><option value="Africa/Mbabane">Africa/Mbabane</option><option value="Africa/Mogadishu">Africa/Mogadishu</option><option value="Africa/Monrovia">Africa/Monrovia</option><option value="Africa/Nairobi">Africa/Nairobi</option><option value="Africa/Ndjamena">Africa/Ndjamena</option><option value="Africa/Niamey">Africa/Niamey</option><option value="Africa/Nouakchott">Africa/Nouakchott</option><option value="Africa/Ouagadougou">Africa/Ouagadougou</option><option value="Africa/Porto-Novo">Africa/Porto-Novo</option><option value="Africa/Sao_Tome">Africa/Sao Tome</option><option value="Africa/Tripoli">Africa/Tripoli</option><option value="Africa/Tunis">Africa/Tunis</option><option value="Africa/Windhoek">Africa/Windhoek</option><option value="America/Adak">America/Adak</option><option value="America/Anchorage">America/Anchorage</option><option value="America/Anguilla">America/Anguilla</option><option value="America/Antigua">America/Antigua</option><option value="America/Araguaina">America/Araguaina</option><option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos Aires</option><option value="America/Argentina/Catamarca">America/Argentina/Catamarca</option><option value="America/Argentina/Cordoba">America/Argentina/Cordoba</option><option value="America/Argentina/Jujuy">America/Argentina/Jujuy</option><option value="America/Argentina/La_Rioja">America/Argentina/La Rioja</option><option value="America/Argentina/Mendoza">America/Argentina/Mendoza</option><option value="America/Argentina/Rio_Gallegos">America/Argentina/Rio Gallegos</option><option value="America/Argentina/Salta">America/Argentina/Salta</option><option value="America/Argentina/San_Juan">America/Argentina/San Juan</option><option value="America/Argentina/San_Luis">America/Argentina/San Luis</option><option value="America/Argentina/Tucuman">America/Argentina/Tucuman</option><option value="America/Argentina/Ushuaia">America/Argentina/Ushuaia</option><option value="America/Aruba">America/Aruba</option><option value="America/Asuncion">America/Asuncion</option><option value="America/Atikokan">America/Atikokan</option><option value="America/Bahia_Banderas">America/Bahia Banderas</option><option value="America/Bahia">America/Bahia</option><option value="America/Barbados">America/Barbados</option><option value="America/Belem">America/Belem</option><option value="America/Belize">America/Belize</option><option value="America/Blanc-Sablon">America/Blanc-Sablon</option><option value="America/Boa_Vista">America/Boa Vista</option><option value="America/Bogota">America/Bogota</option><option value="America/Boise">America/Boise</option><option value="America/Cambridge_Bay">America/Cambridge Bay</option><option value="America/Campo_Grande">America/Campo Grande</option><option value="America/Cancun">America/Cancun</option><option value="America/Caracas">America/Caracas</option><option value="America/Cayenne">America/Cayenne</option><option value="America/Cayman">America/Cayman</option><option value="America/Chicago">America/Chicago</option><option value="America/Chihuahua">America/Chihuahua</option><option value="America/Costa_Rica">America/Costa Rica</option><option value="America/Cuiaba">America/Cuiaba</option><option value="America/Curacao">America/Curacao</option><option value="America/Danmarkshavn">America/Danmarkshavn</option><option value="America/Dawson_Creek">America/Dawson Creek</option><option value="America/Dawson">America/Dawson</option><option value="America/Denver">America/Denver</option><option value="America/Detroit">America/Detroit</option><option value="America/Dominica">America/Dominica</option><option value="America/Edmonton">America/Edmonton</option><option value="America/Eirunepe">America/Eirunepe</option><option value="America/El_Salvador">America/El Salvador</option><option value="America/Fortaleza">America/Fortaleza</option><option value="America/Glace_Bay">America/Glace Bay</option><option value="America/Godthab">America/Godthab</option><option value="America/Goose_Bay">America/Goose Bay</option><option value="America/Grand_Turk">America/Grand Turk</option><option value="America/Grenada">America/Grenada</option><option value="America/Guadeloupe">America/Guadeloupe</option><option value="America/Guatemala">America/Guatemala</option><option value="America/Guayaquil">America/Guayaquil</option><option value="America/Guyana">America/Guyana</option><option value="America/Halifax">America/Halifax</option><option value="America/Havana">America/Havana</option><option value="America/Hermosillo">America/Hermosillo</option><option value="America/Indiana/Indianapolis">America/Indiana/Indianapolis</option><option value="America/Indiana/Knox">America/Indiana/Knox</option><option value="America/Indiana/Marengo">America/Indiana/Marengo</option><option value="America/Indiana/Petersburg">America/Indiana/Petersburg</option><option value="America/Indiana/Tell_City">America/Indiana/Tell City</option><option value="America/Indiana/Vevay">America/Indiana/Vevay</option><option value="America/Indiana/Vincennes">America/Indiana/Vincennes</option><option value="America/Indiana/Winamac">America/Indiana/Winamac</option><option value="America/Inuvik">America/Inuvik</option><option value="America/Iqaluit">America/Iqaluit</option><option value="America/Jamaica">America/Jamaica</option><option value="America/Juneau">America/Juneau</option><option value="America/Kentucky/Louisville">America/Kentucky/Louisville</option><option value="America/Kentucky/Monticello">America/Kentucky/Monticello</option><option value="America/La_Paz">America/La Paz</option><option value="America/Lima">America/Lima</option><option value="America/Los_Angeles">America/Los Angeles</option><option value="America/Maceio">America/Maceio</option><option value="America/Managua">America/Managua</option><option value="America/Manaus">America/Manaus</option><option value="America/Marigot">America/Marigot</option><option value="America/Martinique">America/Martinique</option><option value="America/Matamoros">America/Matamoros</option><option value="America/Mazatlan">America/Mazatlan</option><option value="America/Menominee">America/Menominee</option><option value="America/Merida">America/Merida</option><option value="America/Metlakatla">America/Metlakatla</option><option value="America/Mexico_City">America/Mexico City</option><option value="America/Miquelon">America/Miquelon</option><option value="America/Moncton">America/Moncton</option><option value="America/Monterrey">America/Monterrey</option><option value="America/Montevideo">America/Montevideo</option><option value="America/Montreal">America/Montreal</option><option value="America/Montserrat">America/Montserrat</option><option value="America/Nassau">America/Nassau</option><option value="America/New_York">America/New York</option><option value="America/Nipigon">America/Nipigon</option><option value="America/Nome">America/Nome</option><option value="America/Noronha">America/Noronha</option><option value="America/North_Dakota/Beulah">America/North Dakota/Beulah</option><option value="America/North_Dakota/Center">America/North Dakota/Center</option><option value="America/North_Dakota/New_Salem">America/North Dakota/New Salem</option><option value="America/Ojinaga">America/Ojinaga</option><option value="America/Panama">America/Panama</option><option value="America/Pangnirtung">America/Pangnirtung</option><option value="America/Paramaribo">America/Paramaribo</option><option value="America/Phoenix">America/Phoenix</option><option value="America/Port_of_Spain">America/Port of Spain</option><option value="America/Port-au-Prince">America/Port-au-Prince</option><option value="America/Porto_Velho">America/Porto Velho</option><option value="America/Puerto_Rico">America/Puerto Rico</option><option value="America/Rainy_River">America/Rainy River</option><option value="America/Rankin_Inlet">America/Rankin Inlet</option><option value="America/Recife">America/Recife</option><option value="America/Regina">America/Regina</option><option value="America/Resolute">America/Resolute</option><option value="America/Rio_Branco">America/Rio Branco</option><option value="America/Santa_Isabel">America/Santa Isabel</option><option value="America/Santarem">America/Santarem</option><option value="America/Santiago">America/Santiago</option><option value="America/Santo_Domingo">America/Santo Domingo</option><option value="America/Sao_Paulo">America/Sao Paulo</option><option value="America/Scoresbysund">America/Scoresbysund</option><option value="America/Shiprock">America/Shiprock</option><option value="America/Sitka">America/Sitka</option><option value="America/St_Barthelemy">America/St Barthelemy</option><option value="America/St_Johns">America/St Johns</option><option value="America/St_Kitts">America/St Kitts</option><option value="America/St_Lucia">America/St Lucia</option><option value="America/St_Thomas">America/St Thomas</option><option value="America/St_Vincent">America/St Vincent</option><option value="America/Swift_Current">America/Swift Current</option><option value="America/Tegucigalpa">America/Tegucigalpa</option><option value="America/Thule">America/Thule</option><option value="America/Thunder_Bay">America/Thunder Bay</option><option value="America/Tijuana">America/Tijuana</option><option value="America/Toronto">America/Toronto</option><option value="America/Tortola">America/Tortola</option><option value="America/Vancouver">America/Vancouver</option><option value="America/Whitehorse">America/Whitehorse</option><option value="America/Winnipeg">America/Winnipeg</option><option value="America/Yakutat">America/Yakutat</option><option value="America/Yellowknife">America/Yellowknife</option><option value="Antarctica/Casey">Antarctica/Casey</option><option value="Antarctica/Davis">Antarctica/Davis</option><option value="Antarctica/DumontDUrville">Antarctica/DumontDUrville</option><option value="Antarctica/Macquarie">Antarctica/Macquarie</option><option value="Antarctica/Mawson">Antarctica/Mawson</option><option value="Antarctica/McMurdo">Antarctica/McMurdo</option><option value="Antarctica/Palmer">Antarctica/Palmer</option><option value="Antarctica/Rothera">Antarctica/Rothera</option><option value="Antarctica/South_Pole">Antarctica/South Pole</option><option value="Antarctica/Syowa">Antarctica/Syowa</option><option value="Antarctica/Vostok">Antarctica/Vostok</option><option value="Arctic/Longyearbyen">Arctic/Longyearbyen</option><option value="Asia/Aden">Asia/Aden</option><option value="Asia/Almaty">Asia/Almaty</option><option value="Asia/Amman">Asia/Amman</option><option value="Asia/Anadyr">Asia/Anadyr</option><option value="Asia/Aqtau">Asia/Aqtau</option><option value="Asia/Aqtobe">Asia/Aqtobe</option><option value="Asia/Ashgabat">Asia/Ashgabat</option><option value="Asia/Baghdad">Asia/Baghdad</option><option value="Asia/Bahrain">Asia/Bahrain</option><option value="Asia/Baku">Asia/Baku</option><option value="Asia/Bangkok">Asia/Bangkok</option><option value="Asia/Beirut">Asia/Beirut</option><option value="Asia/Bishkek">Asia/Bishkek</option><option value="Asia/Brunei">Asia/Brunei</option><option value="Asia/Choibalsan">Asia/Choibalsan</option><option value="Asia/Chongqing">Asia/Chongqing</option><option value="Asia/Colombo">Asia/Colombo</option><option value="Asia/Damascus">Asia/Damascus</option><option value="Asia/Dhaka">Asia/Dhaka</option><option value="Asia/Dili">Asia/Dili</option><option value="Asia/Dubai">Asia/Dubai</option><option value="Asia/Dushanbe">Asia/Dushanbe</option><option value="Asia/Gaza">Asia/Gaza</option><option value="Asia/Harbin">Asia/Harbin</option><option value="Asia/Ho_Chi_Minh">Asia/Ho Chi Minh</option><option value="Asia/Hong_Kong">Asia/Hong Kong</option><option value="Asia/Hovd">Asia/Hovd</option><option value="Asia/Irkutsk">Asia/Irkutsk</option><option value="Asia/Jakarta">Asia/Jakarta</option><option value="Asia/Jayapura">Asia/Jayapura</option><option value="Asia/Jerusalem">Asia/Jerusalem</option><option value="Asia/Kabul">Asia/Kabul</option><option value="Asia/Kamchatka">Asia/Kamchatka</option><option value="Asia/Karachi">Asia/Karachi</option><option value="Asia/Kashgar">Asia/Kashgar</option><option value="Asia/Kathmandu">Asia/Kathmandu</option><option value="Asia/Kolkata">Asia/Kolkata</option><option value="Asia/Krasnoyarsk">Asia/Krasnoyarsk</option><option value="Asia/Kuala_Lumpur">Asia/Kuala Lumpur</option><option value="Asia/Kuching">Asia/Kuching</option><option value="Asia/Kuwait">Asia/Kuwait</option><option value="Asia/Macau">Asia/Macau</option><option value="Asia/Magadan">Asia/Magadan</option><option value="Asia/Makassar">Asia/Makassar</option><option value="Asia/Manila">Asia/Manila</option><option value="Asia/Muscat">Asia/Muscat</option><option value="Asia/Nicosia">Asia/Nicosia</option><option value="Asia/Novokuznetsk">Asia/Novokuznetsk</option><option value="Asia/Novosibirsk">Asia/Novosibirsk</option><option value="Asia/Omsk">Asia/Omsk</option><option value="Asia/Oral">Asia/Oral</option><option value="Asia/Phnom_Penh">Asia/Phnom Penh</option><option value="Asia/Pontianak">Asia/Pontianak</option><option value="Asia/Pyongyang">Asia/Pyongyang</option><option value="Asia/Qatar">Asia/Qatar</option><option value="Asia/Qyzylorda">Asia/Qyzylorda</option><option value="Asia/Rangoon">Asia/Rangoon</option><option value="Asia/Riyadh">Asia/Riyadh</option><option value="Asia/Sakhalin">Asia/Sakhalin</option><option value="Asia/Samarkand">Asia/Samarkand</option><option value="Asia/Seoul">Asia/Seoul</option><option value="Asia/Shanghai">Asia/Shanghai</option><option value="Asia/Singapore">Asia/Singapore</option><option value="Asia/Taipei">Asia/Taipei</option><option value="Asia/Tashkent">Asia/Tashkent</option><option value="Asia/Tbilisi">Asia/Tbilisi</option><option value="Asia/Tehran">Asia/Tehran</option><option value="Asia/Thimphu">Asia/Thimphu</option><option value="Asia/Tokyo">Asia/Tokyo</option><option value="Asia/Ulaanbaatar">Asia/Ulaanbaatar</option><option value="Asia/Urumqi">Asia/Urumqi</option><option value="Asia/Vientiane">Asia/Vientiane</option><option value="Asia/Vladivostok">Asia/Vladivostok</option><option value="Asia/Yakutsk">Asia/Yakutsk</option><option value="Asia/Yekaterinburg">Asia/Yekaterinburg</option><option value="Asia/Yerevan">Asia/Yerevan</option><option value="Atlantic/Azores">Atlantic/Azores</option><option value="Atlantic/Bermuda">Atlantic/Bermuda</option><option value="Atlantic/Canary">Atlantic/Canary</option><option value="Atlantic/Cape_Verde">Atlantic/Cape Verde</option><option value="Atlantic/Faroe">Atlantic/Faroe</option><option value="Atlantic/Madeira">Atlantic/Madeira</option><option value="Atlantic/Reykjavik">Atlantic/Reykjavik</option><option value="Atlantic/South_Georgia">Atlantic/South Georgia</option><option value="Atlantic/St_Helena">Atlantic/St Helena</option><option value="Atlantic/Stanley">Atlantic/Stanley</option><option value="Australia/Adelaide">Australia/Adelaide</option><option value="Australia/Brisbane">Australia/Brisbane</option><option value="Australia/Broken_Hill">Australia/Broken Hill</option><option value="Australia/Currie">Australia/Currie</option><option value="Australia/Darwin">Australia/Darwin</option><option value="Australia/Eucla">Australia/Eucla</option><option value="Australia/Hobart">Australia/Hobart</option><option value="Australia/Lindeman">Australia/Lindeman</option><option value="Australia/Lord_Howe">Australia/Lord Howe</option><option value="Australia/Melbourne">Australia/Melbourne</option><option value="Australia/Perth">Australia/Perth</option><option value="Australia/Sydney">Australia/Sydney</option><option value="Europe/Amsterdam">Europe/Amsterdam</option><option value="Europe/Andorra">Europe/Andorra</option><option value="Europe/Athens">Europe/Athens</option><option value="Europe/Belgrade">Europe/Belgrade</option><option value="Europe/Berlin">Europe/Berlin</option><option value="Europe/Bratislava">Europe/Bratislava</option><option value="Europe/Brussels">Europe/Brussels</option><option value="Europe/Bucharest">Europe/Bucharest</option><option value="Europe/Budapest">Europe/Budapest</option><option value="Europe/Chisinau">Europe/Chisinau</option><option value="Europe/Copenhagen">Europe/Copenhagen</option><option value="Europe/Dublin">Europe/Dublin</option><option value="Europe/Gibraltar">Europe/Gibraltar</option><option value="Europe/Guernsey">Europe/Guernsey</option><option value="Europe/Helsinki">Europe/Helsinki</option><option value="Europe/Isle_of_Man">Europe/Isle of Man</option><option value="Europe/Istanbul">Europe/Istanbul</option><option value="Europe/Jersey">Europe/Jersey</option><option value="Europe/Kaliningrad">Europe/Kaliningrad</option><option value="Europe/Kiev">Europe/Kiev</option><option value="Europe/Lisbon">Europe/Lisbon</option><option value="Europe/Ljubljana">Europe/Ljubljana</option><option value="Europe/London">Europe/London</option><option value="Europe/Luxembourg">Europe/Luxembourg</option><option value="Europe/Madrid">Europe/Madrid</option><option value="Europe/Malta">Europe/Malta</option><option value="Europe/Mariehamn">Europe/Mariehamn</option><option value="Europe/Minsk">Europe/Minsk</option><option value="Europe/Monaco">Europe/Monaco</option><option value="Europe/Moscow">Europe/Moscow</option><option value="Europe/Oslo">Europe/Oslo</option><option value="Europe/Paris">Europe/Paris</option><option value="Europe/Podgorica">Europe/Podgorica</option><option value="Europe/Prague">Europe/Prague</option><option value="Europe/Riga">Europe/Riga</option><option value="Europe/Rome">Europe/Rome</option><option value="Europe/Samara">Europe/Samara</option><option value="Europe/San_Marino">Europe/San Marino</option><option value="Europe/Sarajevo">Europe/Sarajevo</option><option value="Europe/Simferopol">Europe/Simferopol</option><option value="Europe/Skopje">Europe/Skopje</option><option value="Europe/Sofia">Europe/Sofia</option><option value="Europe/Stockholm">Europe/Stockholm</option><option value="Europe/Tallinn">Europe/Tallinn</option><option value="Europe/Tirane">Europe/Tirane</option><option value="Europe/Uzhgorod">Europe/Uzhgorod</option><option value="Europe/Vaduz">Europe/Vaduz</option><option value="Europe/Vatican">Europe/Vatican</option><option value="Europe/Vienna">Europe/Vienna</option><option value="Europe/Vilnius">Europe/Vilnius</option><option value="Europe/Volgograd">Europe/Volgograd</option><option value="Europe/Warsaw">Europe/Warsaw</option><option value="Europe/Zagreb">Europe/Zagreb</option><option value="Europe/Zaporozhye">Europe/Zaporozhye</option><option value="Europe/Zurich">Europe/Zurich</option><option value="Indian/Antananarivo">Indian/Antananarivo</option><option value="Indian/Chagos">Indian/Chagos</option><option value="Indian/Christmas">Indian/Christmas</option><option value="Indian/Cocos">Indian/Cocos</option><option value="Indian/Comoro">Indian/Comoro</option><option value="Indian/Kerguelen">Indian/Kerguelen</option><option value="Indian/Mahe">Indian/Mahe</option><option value="Indian/Maldives">Indian/Maldives</option><option value="Indian/Mauritius">Indian/Mauritius</option><option value="Indian/Mayotte">Indian/Mayotte</option><option value="Indian/Reunion">Indian/Reunion</option><option value="Pacific/Apia">Pacific/Apia</option><option value="Pacific/Auckland">Pacific/Auckland</option><option value="Pacific/Chatham">Pacific/Chatham</option><option value="Pacific/Chuuk">Pacific/Chuuk</option><option value="Pacific/Easter">Pacific/Easter</option><option value="Pacific/Efate">Pacific/Efate</option><option value="Pacific/Enderbury">Pacific/Enderbury</option><option value="Pacific/Fakaofo">Pacific/Fakaofo</option><option value="Pacific/Fiji">Pacific/Fiji</option><option value="Pacific/Funafuti">Pacific/Funafuti</option><option value="Pacific/Galapagos">Pacific/Galapagos</option><option value="Pacific/Gambier">Pacific/Gambier</option><option value="Pacific/Guadalcanal">Pacific/Guadalcanal</option><option value="Pacific/Guam">Pacific/Guam</option><option value="Pacific/Honolulu">Pacific/Honolulu</option><option value="Pacific/Johnston">Pacific/Johnston</option><option value="Pacific/Kiritimati">Pacific/Kiritimati</option><option value="Pacific/Kosrae">Pacific/Kosrae</option><option value="Pacific/Kwajalein">Pacific/Kwajalein</option><option value="Pacific/Majuro">Pacific/Majuro</option><option value="Pacific/Marquesas">Pacific/Marquesas</option><option value="Pacific/Midway">Pacific/Midway</option><option value="Pacific/Nauru">Pacific/Nauru</option><option value="Pacific/Niue">Pacific/Niue</option><option value="Pacific/Norfolk">Pacific/Norfolk</option><option value="Pacific/Noumea">Pacific/Noumea</option><option value="Pacific/Pago_Pago">Pacific/Pago Pago</option><option value="Pacific/Palau">Pacific/Palau</option><option value="Pacific/Pitcairn">Pacific/Pitcairn</option><option value="Pacific/Pohnpei">Pacific/Pohnpei</option><option value="Pacific/Port_Moresby">Pacific/Port Moresby</option><option value="Pacific/Rarotonga">Pacific/Rarotonga</option><option value="Pacific/Saipan">Pacific/Saipan</option><option value="Pacific/Tahiti">Pacific/Tahiti</option><option value="Pacific/Tarawa">Pacific/Tarawa</option><option value="Pacific/Tongatapu">Pacific/Tongatapu</option><option value="Pacific/Wake">Pacific/Wake</option><option value="Pacific/Wallis">Pacific/Wallis</option><option value="UTC">UTC</option>-->
            </select>
        </div>
    </div>

    <div class="pure-control-group">
        <div id="date_firstchoice_1_div">
            <label name="date_firstchoice_1">Please choose a date for your pre-task interview (all listed times are in EST):</label>
            <span style="color:red" id="date_firstchoice_span"></span>
            <select name="date_firstchoice_1" id="date_firstchoice_1" required>
                <option disabled selected>--Select one--</option>
                <?php

                foreach(availableDates() as $d){
                    $thesplit = explode(",",$d);
                    $monthday = $thesplit[0];
                    $t = strtotime($monthday);

                    echo "<option val='$t'>";
                    echo "$d";
                    echo "</option>";
                }
                ?>
            </select>
            <br>
        </div>
    </div>



    <div class="pure-control-group">
        <div id="date_secondchoice_1_div"><label name="date_secondchoice_1">Please choose a date for your post-task interview (this must be on the Monday after your pre-task interview; all listed times are in EST):</label>
            <span style="color:red" id="date_secondchoice_span"></span>
            <select name="date_secondchoice_1" id="date_secondchoice_1" required>
                <option disabled selected>--Select one--</option>
                <?php

                foreach(availableDates2() as $d){

                    $thesplit = explode(",",$d);
                    $monthday = $thesplit[0];
                    $t = strtotime($monthday);




                    echo "<option val='$t'>";
                    echo "$d";
                    echo "</option>";
                }
                ?>
            </select>
            <br>
        </div>
    </div>



    <?php




echo "</fieldset>";
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
	else
	{
?>
	<html>
	<head>
	</head>
	<body>
	<h3>Interactive Search Study: Complete Previous Page!!!!</h3>
	<hr>
	<p>You have to first submit the number of registrants for participating in the study.</p>
	<p>Please visit the <a href="http://coagmento.org/workintent/signup_intro.php">signup introduction</a> first.</p>
	</body>
	</html>
<?php
	}
}

else if ($closed)
{
	echo "<html>\n";
  echo "<head>\n";
  echo "<title>Interactive Search Study: Currently Closed</title>\n";
  echo "</head>\n";
  echo "<body class=\"body\">\n<center>\n<br/><br/>\n";
  echo "<table class=body align=center>\n";
  echo "<tr><td align=center>Our study is currently closed at this time, and we are currently not accepting new recruits.  We apologize for any inconvenience.</td></tr>\n";
  echo "</table></body>\n";
  echo "</html>";



}else if($section_closed){
  echo "<html>\n";
  echo "<head>\n";
  echo "<title>Interactive Search Study: Currently Closed</title>\n";
  echo "</head>\n";
  echo "<body>\n";
  echo "<br/><br/>\n";
  echo "<hr/>\n";
  echo "<p>The number of required for this type of grouping has been reached at this point.</p>\n";
  echo "<p>If you wanted to register as a pair but would still like to participate, please register as individual users.</p>\n";
  echo "<hr/>\n";
  echo "</body>";
  echo "</html>";
}
else{


  echo "<html>\n";
  echo "<head>\n";
  echo "<title>Interactive Search Study: Currently Closed</title>\n";
  echo "</head>\n";
  echo "<body>\n";
  echo "<p style='background-color:red;'>Sorry! The user study registration is currently closed.</p>\n";
  echo "<br/><br/>\n";
  echo "<hr/>\n";
  echo "<p>The number of participants required has been reached at this point.</p>\n";
  echo "<p>If more user participation is required, we will reopen the study registration and send another round of recruitment emails.</p>\n";
  echo "<hr/>\n";
  echo "</body>";
  echo "</html>";
}
?>
