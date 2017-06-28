<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ ))) 
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();

		if (isset($_POST['practiceIntro'])) 
		   {
			
			$base = new Base();
			
			$stageID = $base->getStageID();
			
			//Save action
			Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
			
			//Next stage
			Util::getInstance()->moveToNextStage();	
		}
		else {
			
			
?>
<html>
<head>
<title>Practice Task: Introduction
</title>

</head>
<script type="text/javascript">
	function validate(form)
	{
		if (!form.confirmReadInstructions.checked)
		{	
			document.getElementById("alert").style.display = "block";
			return false;
		}
		else
			return true;
	}

	function complete(check)
	{
		if (check.checked)
		{
			document.getElementById("complete").style.display = "block";
			document.getElementById("alert").style.display = "none";
		}
		else
		{
			document.getElementById("complete").style.display = "none";
			document.getElementById("alert").style.display = "block";
		}
	} 	 
</script>
<body class="body">
<center>
	<br/>
	<form action="practiceIntro1.php" method="post" onsubmit="return validate(this)">
		<table class="body" width="90%">
		<tr><th><span style="font-weight:bold; font-size:20px">Exploratory Search User Study: Practice Task</span><br/><br/></th></tr>
		<tr><td><hr/></td></tr>
		<tr>
			<td>
				<ul>
				<li>Next you will have <strong>5 minutes (maximum time)</strong> to practice with the system while completing <strong>ONE</strong> simple practice task using <strong>Web search and a text editor</strong>.</li>
				<li>Your <strong>time will start</strong> ticking the moment you have read the following instructions and click the <strong>Start Practice Task</strong> button.</li>
				<li>You will see the <strong>timer flashing</strong> when you have <strong>30 seconds remaining</strong> to complete the practice task.</li>
				<li>You MUST work for AT LEAST 2 minutes on this practice task and you have up to 5 minutes to complete the task. If you wish to finish earlier than 5 minutes after the 2 minutes have elapsed, you can click on the 'Finish' button on the sidebar.</li>
				<li>Whether you provide the right or wrong answer for <strong>THIS</strong> task, it <strong>will not</strong> affect your performance evaluation.</li>
				<li>Please note that <strong>Home</strong> button will <strong>always be enabled</strong> on the toolbar but the <strong>other buttons (Snip, Editor, My Task)</strong> would <strong>only be enabled when your actual Web search task starts</strong> and will eventually be disabled when the task is complete and time runs out. </li>
				<!--<li>Try the <strong>Search</strong> and <strong>Snip buttons</strong>.</li>-->
				<li>Try the <strong>Home</strong> and <strong>Snip</strong> buttons.</li>
				<li>Also use the <strong>Editor</strong> button and type your answer to the task there.</li>
				<?php if ($collaborativeStudy>1) echo "<li>Try the <strong>chat system, snippet area, and collaboratively editing the text in the editor</strong> to share information or to access shared resources.</li>"; ?>				
				<li>On the sidebar, click on the snippets to view their content.</li>
				<!--<li>You can access Google search by clicking the <strong>Search button</strong> when it is enabled in the tool bar. You can search using any search engine you like (eg: Google, Yahoo, Bing, etc) by accessing those search engines by typing on the address bar when search is enabled.</li>-->
				<li>If you need to find what stage you are in this study or when in doubt or when you encounter an issue, you can click the <strong>Home</strong> button. It will show your current status and provide useful tips and guide you along this system.</li>
				<li>If you click on the <strong>My Task </strong>button you would be able to see the task you were assigned if you want to have a look and re-read while you are searching for information.</li>
				<li>It is up to you how you approach the task.</li>
				<li>In order to <strong>search on the Web</strong>, you can type in your queries directly on the address bar of the browser or go to your favorite search engine like Google, Yahoo, Bing, etc and issue the queries there.</li> 								
				<li>You can visit any page and find information required to complete the task.</li>
				<li>Remember to collect snippets from Web pages you find relevant for the task.</li>
				<li>To search within a page press <strong>Ctrl+F (if Windows user) or Cmd+F (if Mac user)</strong> and a search box will be enabled on the bottom part of the screen.</li>
				<li>When writing the task report, you can access the snippets on the right panel, <strong>select text</strong>, and then use <strong>Ctrl+C (if Windows user) or Cmd+C (if Mac user) to copy</strong> the selection and <strong>Ctrl+V (if Windows user) or Cmd+V (if Mac user) to paste</strong> it on the editor.</strong></li>					
				<li>After reading the task description, you will be asked to indicate your level of <strong>familiarity</strong> with the topic of the task and how <strong>challenging</strong> you think it will be to find the answer.</li> 			    		
				<!--<li>While in <strong>google.com</strong>, <strong>DO NOT change any of its settings</strong> (e.g. time range, or perform advanced search).</li>-->
				<li>After time is up or after you click finish, you will be automatically redirected to post-task questionnaire in order to indicate your level of <strong>confidence</strong> with regard to the answer and the level of <strong>difficulty</strong> of the task.</li>
				<li><strong>Remember</strong>, when asked about topic familiarity, complexity, confidence, and difficulty; please respond with <strong>honesty</strong>. <strong>This will not affect your final evaluation</strong>.</li>																								
				</ul>
				<center>
					<p>
						<strong>If you have read these instructions and ready to start the practice task, please click the checkbox below and press "Start Practice Task". Good Luck!</strong>
					</p>
				</center>
			</td>
		</tr>	
		<tr><td><hr/></td></tr>
		<tr><td><div style="display: none; background: Red; text-align:center;" id="alert"><strong>Before you continue, you must read all the above instructions. Once you have read them, click on the box below and then continue.</strong></div></td></tr>
		<tr><td><div style="display: none; background: LightGreen; text-align:center;" id="complete"><strong>Good! Click on "Start Practice Task" and proceed with the practice session to get accustomed to the system.</strong></div></td></tr>	        	
		<tr><td align=center><input type="checkbox" name="confirmReadInstructions" value="true" onclick="complete(this)"/>I have read all the above instructions</td></tr>
		<tr><td><br/></td></tr>
		<tr><td align=center><input type="hidden" name="practiceIntro" value="true"/><input type="submit" value="Start Practice Task" /></td></tr>
	  </table>
	</form>
<br/>
</center>
</body>
</html>
<?php
		}
	}
	else {
		echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
	}
	
?>