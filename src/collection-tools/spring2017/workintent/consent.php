<?php
	require_once('core/Connection.class.php');
    
    $closed=false;
    if(!$closed){
?>
<html>
<head>
	<title>
        CONSENT FORM TO TAKE PART IN A
        MOBILE/ELECTRONIC DEVICE OR TECHNOLOGY STUDY
    </title>
    <link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">
    <link rel="stylesheet" href="study_styles/custom/text.css">
    <link rel="stylesheet" href="styles.css">

<script type="text/javascript">
	
	var alertColor = "Red";
	var okColor = "White";
	

	function validateForm(form)
	{
		var isValid = 1;
		form.action = "consent_2.php";
	}
	
	
	function enableAccept(check)
	{
		if (check.checked)
			document.getElementById("acceptReg").disabled = false;
		else
			document.getElementById("acceptReg").disabled = true;
	}
	
	

</script>
<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
</style>
</head>
<body class="style1">
<br/>
<div class="panel panel-default" style="width:95%;  margin:auto">
    <div class="panel-body">
<div id="signupConsent">
	<h3>CONSENT FORM TO TAKE PART IN A
        MOBILE/ELECTRONIC DEVICE OR TECHNOLOGY STUDY</h3>
	<form method="post" onsubmit="return validateForm(this)">

        <div class="panel panel-primary">
            <div class="panel-heading">
                CONSENT
                FOR   CONFIDENTIAL   DATA   COLLECTION
            </div>
            <div class="panel-body">
                <p>You are invited to participate in a research study that is being conducted by Dr. Nicholas Belkin and
                    Dr. Chirag Shah, who are professors in the School of Communication and Information (SC&I) at Rutgers
                    University. The specific purpose of the study is to identify what people intend to accomplish during
                    the course of an information seeking episode, and to relate these intentions to observable search
                    behaviors and the different work-related tasks which lead people to engage in information seeking.</p>

                <p>This research is confidential. Confidential means that the research records will include some
                    information about you and this information will be stored in such a manner that some linkage between
                    your identity and the response in the research exists, during some period of the research. However,
                    there will be no linkage between your identity and your responses (i.e., web search logs, search
                    task and intention annotations) at the conclusion of your participation in the research. If you
                    agree to take part in the study, you will be assigned a code number that will be used on each search
                    log and annotation record. Your name will appear <u>only</u> on a list of subjects, which will be linked
                    to the code number that is assigned to you only for the duration of your participation in the study.
                    <strong>Please note that this study is completely confidential. At the end of your participation, the file
                    that links your name and contact information to your code number will be destroyed and will not be
                        stored in any dataset or any form.</strong> There will thus be no way to link your responses back to you.</p>

                <p>The research team and the Institutional Review Board at Rutgers University are the only parties that
                    will be allowed to see the data, except as may be required by law, or if you have agreed to release
                    the anonymized data for research purposes. If a report of this study is published, or the results
                    are presented at a professional conference, only group results will be stated.</p>

                <p>This study aims to recruit 30 participants. The total amount of time needed for participating is
                    about seven hours, spread over a week. There are no foreseeable risks to participation in this
                    study. Although you may receive no direct benefit from taking part in this study, other than payment
                    for participation, your participation will help in assisting the researchers to understand people’s
                    information seeking behavior, and therefore to improve information retrieval systems. If you are
                    interested in receiving the published results of our study you may contact the researchers listed below.</p>

                <p>Participation in this study is voluntary. You may choose not to participate, and you may withdraw at
                    any time during the study procedures without any penalty to you. For participating in the study, you
                    will receive $95.00 in cash. If you decide to end participation before completing the study, you will
                    receive payment pro-rated for the amount of time that you have devoted to the study. In addition, you
                    may choose not to answer any questions with which you are not comfortable. You should be aware that
                    the researchers may continue to use and disclose data, including your information that was provided
                    before you withdrew your authorization, if necessary to maintain integrity of the research or if the
                    data had already been stripped of all identifiers. If you wish to withdraw your permission for the
                    research use or disclosure of your data and/or health information in this study, you may do so in
                    writing by contacting Dr. Nicholas J Belkin or Dr. Chirag Shah.</p>

                <p>If you have any questions about the study or study procedures, you may contact us at Dr. Nicholas J
                    Belkin, 4 Huntington Street, New Brunswick, NJ 08901, 848-932-7608, belkin@comminfo.rutgers.edu; Dr.
                    Chirag Shah, 4 Huntington Street, New Brunswick, NJ 08901, 848-932-8807, chirags@rutgers.edu.</p>

                <p>If you have any questions about your rights as a research subject, please contact an IRB Administrator
                    at the Rutgers University, Arts and Sciences IRB:</p>

                <p>Institutional   Review   Board<br/>
                Rutgers   University,   the   State   University   of   New   Jersey Liberty   Plaza   /   Suite   3200<br/>
                335  George  Street,  3rd   Floor<br/>
                New   Brunswick,   NJ   08901<br/>
                Phone:   732-235-2866<br/>
                    Email:   humansubjects@orsp.rutgers.edu</p>

            </div>

        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                CONSENT
                FOR   AUDIO RECORDING
            </div>
            <div class="panel-body">
                <p>You are invited to participate in the exit interview which is a part of the research study that is
                    being conducted by Dr. Nicholas Belkin and Dr. Chirag Shah, who are professors in the School of
                    Communication and Information (SC&I) at Rutgers University. The purpose of this interview is to
                    understand people’s intentions and tasks when they engage in Web search.</p>


                <p>During this study, you will be asked to answer some questions regarding the changes of intentions and
                    behaviors in web search, knowledge about the assigned search tasks, and self-evaluation of task
                    performance. This interview was designed to be less than one hour in length. However, please feel
                    free to expand on the topic or talk about related ideas. Also, if there are any questions you would
                    rather not answer or that you do not feel comfortable answering, please say so and we will stop the interview or move on to the next question, whichever you prefer.</p>


                <p>This interview is confidential. Confidential means that your personal identity cannot be identified based on the information collected in the interview. Some of the information collected about you includes gender, age, education background, search behaviors before the interview, annotated search tasks and intentions. Please note that we will keep this information confidential by using pseudonyms for participants, limiting access to the research data and keeping it in a secure location. However, when your participation in the study is completed, we will remove all links between your personal information and the data we have collected, so that there will be no way to associate you with those data.</p>

                <p>The research team and the Institutional Review Board at Rutgers University are the only parties that will be allowed to see the data, except as may be required by law, or if you have agreed to release the anonymized data for research purposes. If a report of this study is published, or the results are presented at a professional conference, only group results will be stated.</p>

                <p>You are aware that your participation in this interview is voluntary, and that the purpose of this interview is to understand people’s intentions in Web search. If, for any reason, at any time, you wish to stop the interview, you may do so without having to give an explanation.</p>

                <p>There are no foreseeable risks to participation in this study. In addition, you have been told that the benefits of taking part in this study may be receiving a $95.00 incentive payment. You will receive the full payment for completing the entire study; if you decide to discontinue participation, you will receive payment pro-rated for the amount of time that you have devoted to the study.</p>

                <p>
                    The audio recording(s) will be used for: qualitative analysis by the research team; possible use as a teaching tool to those who are not members of the research staff (i.e. for educational purposes only).</p>

                <p>The audio recording(s) will include your responses to the interview questions regarding your understanding about the annotated search tasks, search intentions, and search performances. If you say anything that you believe at a later point may be hurtful and/or damage your reputation, then you can ask the interviewer to rewind the recording and record over such information OR you can ask that certain text be removed from the dataset/transcripts.</p>

                <p>The audio recording(s) will be stored in the private file server owned by the principal investigators. If you have any questions about the study or study procedures, you may contact us at Dr. Nicholas J Belkin, 4 Huntington Street, New Brunswick, NJ 08901, 848-932-7608, belkin@rutgers.edu; Dr. Chirag Shah, 4 Huntington Street, New Brunswick, NJ 08901, 848-932-8807, chirags@rutgers.edu.</p>


                <p>If you have any questions about your rights as a research participant, you can contact the Institutional Review Board at Rutgers (which is a committee that reviews research studies in order to protect research participants).</p>

                    <p>Institutional   Review   Board<br/>
                Rutgers   University,   the   State   University   of   New   Jersey Liberty   Plaza   /   Suite   3200<br/>
                335  George  Street,  3rd   Floor<br/>
                New   Brunswick,   NJ   08901<br/>
                Phone:   732-235-2866<br/>
                Email:   humansubjects@orsp.rutgers.edu</p>

                <p>You will be offered a copy of this consent form that you may keep for your own reference.</p>
                <p>Once you have read the above form and, with the understanding that you can withdraw at any time and for whatever reason, you need to let me know your decision to participate in today's interview.</p>
            </div>
        </div>


        <p>
            Please retain a copy of this form for your records. If you are 18 years of age or older, understand the statements above, and will consent to participate in the study, click on the "Agree" button. If not, please close this tab.
        </p>

		<table class="style1" width=90%>

	
			<tr>
				<td>
					<p><input type="checkbox" name="readConsent" id="readConsent" onclick="enableAccept(this)" /><strong> I HAVE READ AND UNDERSTOOD THE TERMS AND CONDITIONS AND LIKE TO PROVIDE MY CONSENT TO PARTICIPATE IN THIS STUDY. </strong></p>
				</td
			</tr>	
			<tr>
					<td align="center" colspan=2>
						<input type="submit" id="acceptReg" value="Accept" disabled=true style="width:100px; height:40px;"/>
						<input type="hidden" id="consentRead" name="consentRead"/>
					</td>
					
			</tr>
		</table>
    </form>
</div>
    </div></div>
</body>
</html>

<?php
    }else{
        echo "<html>\n";
        echo "<head>\n";
        echo "<title>Exploratory Search Study: Currently Closed</title>\n";
        echo "</head>\n";
        echo "<body class=\"body\">\n<center>\n<br/><br/>\n";
        echo "<table class=body align=center>\n";
        echo "<tr><td align=center>Our study is currently closed at this time, and we are currently not accepting new recruits.  We apologize for any inconvenience.</td></tr>\n";
        echo "</table></body>\n";
        echo "</html>";
    }
?>


