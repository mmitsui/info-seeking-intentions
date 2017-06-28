<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
		$collaborativeStudy = Base::getInstance()->getStudyID();
        
		if (isset($_POST['system_instructions']))
		{
            $localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp =  $_POST['localTimestamp'];
            
			$base = new Base();
			$stageID = $base->getStageID();
			Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$stageID,$base,$localTime,$localDate,$localTimestamp);
			Util::getInstance()->moveToNextStage();
		}
		else
		{
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
<title>System Instructions
</title>

</head>

<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">
<script type="text/javascript" src="js/util.js"></script>
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
    return true;
//    var result = form.confirmReadInstructions.checked;
//    
//    
//    if (!result)
//    {
//        document.getElementById("alert").style.display = "block";
//        return false;
//    }
//    else
//    {
//        setLocalTime(form);
//        return true;
//    }
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

<p>The browser-based Coagmento tool is made of two components:
the Toolbar and the Sidebar. The Toolbar contains the main functions you will be using
while you research.
<?php
    if($num_users > 1){
        echo "The Sidebar shows the results of your research and lets you chat with your partner.";
    }else{
        echo "The Sidebar shows the results of your research.";
    }
    ?>
</p>

<p>To open the Sidebar press <strong>Ctrl+Shift+S (Windows)</strong> or <strong>&#8984;+Shift+S (Mac)</strong>.</p>
<!---<center><span style="font-weight:bold; font-size:20px"><strong>Edusearch Study</strong>: System Instructions</span></center><br/>

<p>One of the transferable skills universities help you learn, is how to work together to find information, and convey that information to others. The tasks here are about trying to help a government minster understand the best supported evidence around a scientific issue - you'll need to work with your partner to find the best supported claims, and then write a summary document for the minister.</p>

<p>Don't worry if you don't understand everything you find; focus on the best supported of the claims.</p>-->



<!--- <center><center><strong><u><h3>Table of Contents</h3></u></strong></center></center>
<ul>
<!--<li><a href="javascript:goto('System_Overview','')">System Overview</a></li>
<li><a href="javascript:goto('Toolbar','')">Toolbar</a></li>
<ul>
<li><a href="javascript:goto('Toolbar','Home')">Home</a></li>
<li><a href="javascript:goto('Toolbar','Help')">Help</a></li>
<li><a href="javascript:goto('Toolbar','Snip')">Snip</a></li>
<li><a href="javascript:goto('Toolbar','Bookmark')">Bookmark</a></li>
<li><a href="javascript:goto('Toolbar','Task_Pad')">Task Pad</a></li>
<li><a href="javascript:goto('Toolbar','Active_Task')">Active Task</a></li>
</ul>
<li><a href="javascript:goto('Sidebar','')">Sidebar</a></li>
<ul>
<li><a href="javascript:goto('Sidebar','Chat')">Chat</a></li>
<li><a href="javascript:goto('Sidebar','History')">History</a></li>
<li><a href="javascript:goto('Sidebar','Submit')">Submit (consensus)</a></li>
</ul>
<li><a href="javascript:goto('Requirements','')">Requirements</a></li>
<li><a href="javascript:goto('Tips','')">Tips</a></li>
<ul>
<li><a href="javascript:goto('Tips','Working_with_a_Partner')">Working with a Partner</a></li>
<li><a href="javascript:goto('Tips','General')">General</a></li>
</ul>
</ul>

<hr/>
<!--<li><a href="javascript:goto('Caution','')">Caution!</a></li>-->






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
<div id="Toolbar_div" style="display:block;">
<span id="Toolbar"><center><strong><h3>Toolbar</h3></strong></center></span><br/>
The Toolbar consists of five buttons:
<ol>
<li><span id="Home"><strong>Home</strong></span> - Displays your current stage and a link for returning where you left off.<br/><br/></li>
<li><span id="Help"><strong>Help</strong></span> - Displays these instructions.<br/><br/></li>
<li><span id="Snip"><strong>Snip</strong></span> - Saves portions of text from a webpage. To save text, select the desired text by dragging the mouse, and then click the Snip button. The snipped text and the URL are automatically saved and appear in the Sidebar.<br/><br/></li>
<li><span id="Bookmark"><strong>Bookmark</strong></span> - Saves the URL of the website you are viewing. Make a note of why you saved this web page and what it will be useful for when you write your paper. Rate the quality of the page by clicking on a star rating. Your saved URLs will appear in the History section of the Sidebar, along with your notes and star ratings. <br/><br/></li>
<li><span id="Task_Pad"><strong>Task Pad</strong></span> - Opens a built-in text editor to take notes about your sources.  Anything you write here is automatically saved.<br/><br/></li>
<li><span id="Active_Task"><strong>Research Topic</strong></span> - Reminds you of your research topic you entered when you registered for the study.<br/></li>
</ol>
</div>

<br>
<div id="Sidebar_div" style="display:block;">

<span id="Sidebar"><center><strong><h3>Sidebar</h3></strong></center></span>
The Sidebar consist of:<br/>
<ol>
<?php
    if($num_users > 1){
        echo "";
        echo "<li><span id=\"Chat\"><strong>Chat</strong></span> - Contains a chat program for interacting and coordinating with your partner.<br/><br/>";
        echo "</li>";
    }
    ?>

<li><span id="History"><strong>History</strong></span> - Contains your snippets, bookmarks, comments and ratings. Click on a link to open it. A popup window will appear, containing the snippet and a link to the originating web page. Click the URL to open the original page. You can also change the ratings for a saved web page.<br/><br/>
</li>
</ul>
<br/>
</li>
</ol>

</div>




<?php
    if($num_users>1){
        
    ?>

<div id="Tips_div" style="display:block;">

<span id="Working_with_a_Partner"><center><strong><h3>Working with a Partner</h3></strong></center></span><br/>
<ul>
<li>Share specific information or arguments from pages by using the <a href="javascript:goto('Sidebar','Chat')">Chat box</a>, the <a href="javascript:goto('Toolbar','Snip')">Snip tool</a>, or by cutting and pasting extracts from the pages into the <a href="javascript:goto('Toolbar','Task_Pad')">Task Pad</a>.</li>
<li>Focus your time on finding and selecting information and building a consensus with your partner about which bits of information are best supported. Use the <a href="javascript:goto('Toolbar','Task_Pad')">Task Pad</a> to collate this information.</li>
<li>Use the star ratings in the History/Bookmarks section of the side bar to rate the best sources.</li>
<li>Remember to explain to your partner what you're doing, and justify your reasoning.</li>
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
<input type="button" style="display:block;" id="next_button" value="Next >>"  onClick="next()" style="width:100px; height:40px;"/>-->
<button type="submit" id="continue_button" class="pure-button pure-button-primary">Continue</button></td></tr>
</table>
</center>
</form>
<br/>
</body>
</html>
<?php
    }
	}
	else {
		echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
	}
	
    ?>

