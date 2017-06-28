<?php
	session_start();
		require_once('../core/Connection.class.php');

	require_once('../core/Settings.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');


    if (Base::getInstance()->isSessionActive())
    {

			// if(isset($_GET['fromtoolbar']) && $_GET['fromtoolbar']){
			// 	$localTime = $_GET['localTime'];
			// 	$localDate = $_GET['localDate'];
			// 	$localTimestamp = $_GET['localTimestamp'];
			// 	Util::getInstance()->saveActionWithLocalTime("Clicked Instructions Button", 0, Base::getInstance(), $localTime, $localDate, $localTimestamp);
			// 	header("Location: getInstructions.php");
			// 	exit();
			// }

    $base = Base::getInstance();
    $userID = $base->getUserID();
    $query = "SELECT numUsers from users WHERE userID='$userID'";
    $connection = Connection::getInstance();
    $results = $connection->commit($query);
    $line = mysql_fetch_array($results,MYSQL_ASSOC);
    $num_users = $line['numUsers'];
        ?>
<html>
<head>
<title>Help
</title>

</head>
<!-- <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="../study_styles/custom/text.css">
<script type="text/javascript" src="../instruments/js/util.js"></script>
<script type="text/javascript">


var to_show_array = ["System_Overview","Toolbar","Sidebar","Requirements","Tips","Caution"];
var current_index = 0;

// "Home","System_Guide","Snip","Task_Pad","Active_Task"----"Chat","History","Submit","Working_with_a_Partner","General"
//
function goto(to_show,to_zoom){
    var shown = 0;
    for(i=0;i<to_show_array.length;i++){
        if(to_show_array[i] === to_show)
        {
            shown = i;
            //            document.getElementById(to_show_array[i]+"_div").style.display="block";
        }else{
            //hide
            //            document.getElementById(to_show_array[i]+"_div").style.display="none";
        }
    }

    if(to_zoom !== ""){
        document.getElementById(to_zoom).scrollIntoView();
    }
    else{
        document.getElementById(to_show_array[shown]+"_div").scrollIntoView();
    }
    current_index = shown;
    //    if(current_index == to_show_array.length-1){
    //        document.getElementById("back_button").style.display = "block";
    //        document.getElementById("next_button").style.display = "none";
    //        document.getElementById("continue_button").style.display = "block";
    //    }else if(current_index == 0){
    //        document.getElementById("back_button").style.display = "none";
    //        document.getElementById("next_button").style.display = "block";
    //        document.getElementById("continue_button").style.display = "none";
    //    }else{
    //        document.getElementById("back_button").style.display = "block";
    //        document.getElementById("next_button").style.display = "block";
    //        document.getElementById("continue_button").style.display = "none";
    //    }
}

function next(){
    current_index += 1;
    if(current_index >= to_show_array.length){
        current_index = to_show_array.length-1;
    }
    if(current_index == to_show_array.length-1){
        document.getElementById("back_button").style.display = "block";
        document.getElementById("next_button").style.display = "none";
        document.getElementById("continue_button").style.display = "block";
    }else{
        document.getElementById("back_button").style.display = "block";
        document.getElementById("next_button").style.display = "block";
        document.getElementById("continue_button").style.display = "none";
    }
    goto(to_show_array[current_index],"");
}

function back(){
    current_index -=1;
    if(current_index < 0){
        current_index = 0;
    }

    if(current_index == 0){
        document.getElementById("back_button").style.display = "none";
        document.getElementById("next_button").style.display = "block";
        document.getElementById("continue_button").style.display = "none";
    }else{
        document.getElementById("back_button").style.display = "block";
        document.getElementById("next_button").style.display = "block";
        document.getElementById("continue_button").style.display = "none";
    }
    goto(to_show_array[current_index],"");
}

function validate(form)
{
    var result = form.confirmReadInstructions.checked;


    if (!result)
    {
        document.getElementById("alert").style.display = "block";
        return false;
    }
    else
    {
        setLocalTime(form);
        return true;
    }
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
<form action="system_instructions.php" method="post" onsubmit="return validate(this)">
<center><h2>How to use Coagmento</h2></center>

<!-- <p>The browser-based Coagmento tool is made of two components:
the Toolbar and the Sidebar. The Toolbar contains the main functions you will be using
while you research. -->
<?php
    // if($num_users > 1){
    //     echo "The Sidebar shows the results of your research and lets you chat with your partner.";
    // }else{
    //     echo "The Sidebar shows the results of your research.";
    // }
    ?>
<!-- </p> -->

<!-- <p>To open the Sidebar press <strong>Ctrl+Shift+S (Windows)</strong> or <strong>&#8984;+Shift+S (Mac)</strong>.</p> -->
<!---<center><span style="font-weight:bold; font-size:20px"><strong>Edusearch Study</strong>: System Instructions</span></center><br/>

<p>One of the transferable skills universities help you learn, is how to work together to find information, and convey that information to others. The tasks here are about trying to help a government minster understand the best supported evidence around a scientific issue - you'll need to work with your partner to find the best supported claims, and then write a summary document for the minister.</p>

<p>Don't worry if you don't understand everything you find; focus on the best supported of the claims.</p>-->



<!--- <center><center><strong><u><h3>Table of Contents</h3></u></strong></center></center>
<ul>
<!--<li><a href="javascript:goto('System_Overview','')">System Overview</li>
<li><a href="javascript:goto('Toolbar','')">Toolbar</li>
<ul>
<li><a href="javascript:goto('Toolbar','Home')">Home</li>
<li><a href="javascript:goto('Toolbar','Help')">Help</li>
<li><a href="javascript:goto('Toolbar','Snip')">Snip</li>
<li><a href="javascript:goto('Toolbar','Bookmark')">Bookmark</li>
<li><a href="javascript:goto('Toolbar','Task_Pad')">Task Pad</li>
<li><a href="javascript:goto('Toolbar','Active_Task')">Active Task</li>
</ul>
<li><a href="javascript:goto('Sidebar','')">Sidebar</li>
<ul>
<li><a href="javascript:goto('Sidebar','Chat')">Chat</li>
<li><a href="javascript:goto('Sidebar','History')">History</li>
<li><a href="javascript:goto('Sidebar','Submit')">Submit (consensus)</li>
</ul>
<li><a href="javascript:goto('Requirements','')">Requirements</li>
<li><a href="javascript:goto('Tips','')">Tips</li>
<ul>
<li><a href="javascript:goto('Tips','Working_with_a_Partner')">Working with a Partner</li>
<li><a href="javascript:goto('Tips','General')">General</li>
</ul>
</ul>

<hr/>
<!--<li><a href="javascript:goto('Caution','')">Caution!</li>-->






<!--<div id="System_Overview_div" style="display:block;">
<br/>
<ul>
<li>The browser-based Coagmento tool is made of two components: the <strong>Toolbar</strong> and the <strong>Sidebar.</strong> <br/><br/></li>
<li>The Toolbar contains the main functions you will be using while you research. <br/><br/></li>
<li>The Sidebar shows the results of your research and lets you chat with your partner. [comment out for individuals].<br/><br/></li>
<li>To open the Sidebar press <strong> Ctrl+Shift+S </strong>(Windows) or <strong> &#8984;+Shift+S </strong>(Mac). <br/><br/></li>
<br>
</ul>
<hr />-->
</div>

<br>

<!-- <div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
        Collapsible Group 1</a>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse in">
      <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
      sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
      minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
      commodo consequat.</div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
        Collapsible Group 2</a>
      </h4>
    </div>
    <div id="collapse2" class="panel-collapse collapse">
      <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
      sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
      minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
      commodo consequat.</div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
        Collapsible Group 3</a>
      </h4>
    </div>
    <div id="collapse3" class="panel-collapse collapse">
      <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
      sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
      minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
      commodo consequat.</div>
    </div>
  </div>
</div> -->


<ul style="list-style-type: none;">
	<li>Coagmento is a collaborative search system that helps you to share bookmarks,
	text snippets, and sources, <br>as well as write and chat with members of your group.
	It makes collaborative research easy and accessible<br> right in your browser.
	</li>
	<li>
		To watch the tutorial video again, click <a href="https://www.youtube.com/watch?v=YRDrMfROxf4" target="_new">here</a>.
	</li>
</ul>



<div id="Toolbar_div" style="display:block;">
<span id="Toolbar">
	<!-- <center> -->
	<hr>
		<strong><h3>Toolbar</h3>
		</strong>
		<!-- </center> -->
	</span><center><img src="images/toolbar_demo.png" width="80%" height="auto"/></center>
<!-- The Toolbar consists of five buttons:
<ol>
<li><span id="Home"><strong>Connect/Disconnect</strong></span> - Logs in and out of Coagmento</li>
<li><span id="Help"><strong>Help</strong></span> - Displays these instructions.</li>
<li><span id="Snip"><strong>Snip</strong></span> - Saves portions of text from a webpage. To save text, select the desired text by dragging the mouse, and then click the Snip button. The snipped text and the URL are automatically saved and appear in the Sidebar.</li>
<li><span id="Bookmark"><strong>Bookmark</strong></span> - Saves the URL of the website you are viewing. Make a note of why you saved this web page and what it will be useful for when you write your paper. Rate the quality of the page by clicking on a star rating. Add tags that describe your bookmark by typing or selecting from the pulldown list. Your saved URLs will appear in the History section of the Sidebar, along with your notes and star ratings.</li>
<li><span id="Task_Pad"><strong>Write</strong></span> - Opens a built-in text editor to take notes about your sources.  Anything you write here is automatically saved.</li>
<li><span id="Active_Task"><strong>Assignment</strong></span> - Reminds you of your research topic you entered when you registered for the study.</li>
<li><span><strong>Contact Us</strong> - Use this contact form to ask questions about Coagmento or get help</span></li>
</ol> -->
</div>

<br/>
<div id="Sidebar_div" style="display:block;">

<span id="Sidebar">
	<!-- <center> -->
	<hr>
		<strong>
			<h3>Sidebar</h3>
			</strong>
			<!-- </center> -->
			</span>
			<center><img src="images/sidebar_demo.png" width="80%" height="auto"/></center>
<!-- The Sidebar consists of:<br/> -->
<!-- <ol> -->
<?php
    if($num_users > 1){
        // echo "";
        // echo "<li><span id=\"Chat\"><strong>Chat</strong></span> - Contains a chat program for interacting and coordinating with your partner.";
        // echo "</li>";
    }
    ?>

<!-- <li><span id="History"><strong>History</strong></span> - Contains your bookmarks, snippets, and saved searches. Click on the links to open the original web page or re-run the search.You can also revise the notes and tags for a saved web page. Change the ratings by clicking on the stars. -->
<!-- </li> -->
<!-- <br/> -->
<!-- </li> -->
<!-- </ol> -->

</div>

<span id="Workspace">
	<!-- <center> -->
	<hr>
		<strong>
			<h3>Workspace</h3>
			</strong>
			<!-- </center> -->
			<ul style="list-style-type: none;">
				<li>
					All of your group's activity can be viewed in the Workspace. Stats show everyone's contributions.

					Explore by searching, filtering, or sorting.
				</li>
			</ul>
			</span>
			<center><img src="images/workspace_demo.png" width="80%" height="auto"/></center>
<!-- The Sidebar consists of:<br/> -->
<!-- <ol> -->
<?php
    if($num_users > 1){
        // echo "";
        // echo "<li><span id=\"Chat\"><strong>Chat</strong></span> - Contains a chat program for interacting and coordinating with your partner.";
        // echo "</li>";
    }
    ?>

<!-- <li><span id="History"><strong>History</strong></span> - Contains your bookmarks, snippets, and saved searches. Click on the links to open the original web page or re-run the search.You can also revise the notes and tags for a saved web page. Change the ratings by clicking on the stars. -->
<!-- </li> -->
<!-- <br/> -->
<!-- </li> -->
<!-- </ol> -->

</div>




<?php
    if($num_users>1){

        ?>

<div id="Tips_div" style="display:block;">
	<hr>
<span id="Working_with_a_Partner"><center><strong><h3>Working Collaboratively</h3></strong></center></span>
<ul>
<li>Work together with your group to search for information on your topic, evaluate its quality, and synthesize what you've all learned into your written report.</li>
<li>Focus your time on finding and selecting quality information and building a consensus with your group about which information is best for your topic.</li>
<li>While searching, open new tabs in Firefox. Do not open new browser windows.</li>
<li>Remember to use the Chat window to explain to your group what you're doing, and explain your reasoning.</li>
<li>Use the text editor to save notes, work on your outline, and organize your report.</li>
<li>Export or copy and paste your text into a word processer when you are ready to format it.</li>
<li><strong>Be sure to LOGOUT of Coagmento when you are done working.</strong> Your activity will not be recorded after you log out.</li>
</ul>
<?php
    }
    ?>



<!--<div id="Caution_div" style="display:block;">

<span id="Caution"><center><strong><u><h3>Caution!</h3></u></strong></center></span>

<ul>
<li>DO NOT CLOSE OR QUIT THE FIREFOX BROWSER AT ANY TIME!</li>
<li>You may open or close tabs in Firefox, but PLEASE DO NOT OPEN NEW WINDOWS!</li>
<li>Please do not close the sidebar.  If you do, press Ctrl+Shift+S (Windows) or &#8984;+Shift+S (Mac) to reopen it.</li>
</ul>


<hr/>
</div>-->




<center>
<!--After you proceed through the tutorial, a 'Continue' button will appear.  Click it to proceed to the tutorial stage.</P>-->
<table>
<tr><td><div style="display: none; background: Red; text-align:center;" id="alert"><strong>Before you proceed, you must read the tutorial. Once you have read it, click on the box below.</strong></div></td></tr>
<tr><td><div style="display: none; background: LightGreen; text-align:center;" id="complete"><strong>Good! You now proceed by clicking 'Continue'.</strong></div></td></tr>
<tr><td><br/></td></tr>
<!--<tr><td align=center><input type="checkbox" name="confirmReadInstructions" value="true" onclick="complete(this)"/>I have read the tutorial and understood basic usage of the system.</td></tr>-->
<tr><td align=center>
<input type="hidden" name="system_instructions" value="true"/>
<input type="hidden" name="localTime" value=""/>
<input type="hidden" name="localDate" value=""/>
<input type="hidden" name="localTimestamp" value=""/>



<!--<input type="button" style="display:none;" id="back_button" value="<< Back" onClick="back()" style="width:100px; height:40px;"/>
<input type="button" style="display:block;" id="next_button" value="Next >>"  onClick="next()" style="width:100px; height:40px;"/>
<input type="submit" style="display:block;" id="continue_button" value="Continue" style="width:100px; height:40px;"/></td></tr>-->
</table>
</center>
</form>
<br/>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> -->
</body>
</html>

<?php
    }
    ?>
