<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');
	require_once('../core/Settings.class.php');
	require_once('../core/Stage.class.php');

	$base = Base::getInstance();
	$settings = Settings::getInstance();
	$homeURL = $settings->getHomeURL();
	$page = "";
	//echo "Allow Comm: ".$stage->getAllowCommunication();
    $projectID = $base->getProjectID();

	/*---COMMENTED OUT ON 05/23/2014-----*/
	if ($base->getAllowCommunication()==1)
	{
    			/*
                 *
                 * SIMPLIFIED CHAT
                 *
                 *

                require_once "chat/src/phpfreechat.class.php"; // adjust to your own path
			    $params["serverid"] = md5(__FILE__);
    			$params["dyn_params"] = array("nick"); //,"frozen_channels");
    			$params["max_channels"] = 1;
    			//$params["channels"] = array("chat".$base->getProjectID()."-".$base->getQuestionID());
    			$params["nick"] = $base->getUserName();
    			$params["short_url"] = false;
    			$params["display_ping"] = false;
    			$params["displaytabimage"]= false;
    			$params["displaytabclosebutton"] = false;
    			$params["showwhosonline"] = false;
    			$params["showsmileys"] = false;
    			$params["time_format"] = "H:i";
    			$params["timeout"] = 1000000;
    			$params["max_msg"] = 0;
    			//$params["shownotice"] = 2;
    			$params["max_text_len"] = 5000;
    			$params['skip_proxies'] = array('censor','noflood');
    			$params["height"]= "180px";
    			$params["title"] = "Coagmento";
    			$params['admins'] = array('admin'  => 'soportechat');
    			//$params["connect_at_startup"] = true;
  				$params["refresh_delay"] = 2000; // 2000ms = 2s
  				$chat = new phpFreeChat($params);
                 *
                 *
                 *
                 */

        /*
         *
         * COMPLICATED CHAT
         *
         */
        require_once "phpfreechat-1.7/src/phpfreechat.class.php"; // adjust to your own path
        //echo $projectID." - ".$projectTitle;
        $projectTitle = "lib-p-";
        $params["serverid"] = md5(__FILE__);
        /*$params["container_type"] = "Mysql";
         $params["container_cfg_mysql_host"] = "localhost";
         $params["container_cfg_mysql_database"] = "shahonli_coagmento";
         $params["container_cfg_mysql_username"] = "shahonli_super";
         $params["container_cfg_mysql_password"] = "superman-2010!";*/
        $params["nick"] = $base->getUserName(); //$_POST['nickname'];
        $params["title"] = "Coagmento";
        $params["display_ping"] = FALSE;
        $params["displaytabclosebutton"] = FALSE;
        $params["display_pfc_logo"] = FALSE;
        $params["showwhosonline"] = FALSE;
        $params["btn_sh_whosonline"] = TRUE;
        $params["displaytabimage"]= FALSE;
        $params["height"]= "180px";
        //$params["startwithsound"] = TRUE;
        $params["max_text_len"] = 5000;
        $params["timeout"] = 3000000;
        //$params["date_format"] = "m/d/Y";
        //$params["time_format"] = "H:i";
        //$params["short_url_width"] = 20;
        $params["showsmileys"] = FALSE;
        //$params["connect_at_startup"] = FALSE;
        //$params["frozen_channels"] = array();
        //$params["frozen_channels"] = array($projectTitle.$projectID);
        $params["channels"] = array($projectTitle.$projectID);
        //$params["dyn_params"] = array("nick","frozen_channels");
        $params["dyn_params"] = array("nick"); //,"frozen_channels");
        $params["max_channels"] = 1;
        //$params['frozen_nick'] = true;
        $params["max_msg"] = 0;
        //$params["max_nick_len"]   = 20;
        //$params['admins'] = array('admin'  => 'soportechatSummer2011');
        $params['admins'] = array('admin'  => 'soportechat');
        $params['skip_proxies'] = array('censor','noflood');
        $params["refresh_delay"] = 500;
//        $params["isadmin"] = TRUE;

        $chat = new phpFreeChat($params);
	}

/***************************************************************************************************************************************/
/***************************************************************************************************************************************/
/**************************************************** TIMER ****************************************************************************/
/***************************************************************************************************************************************/
/***************************************************************************************************************************************/

	$page = $base->getPage();
//	$qProgressID = $_SESSION['qProgressID'];

	/* ---COMMENTED OUT ON 05/23/2014-----
	if ($base->getStageID()==170)
	{
		$page = "question3.php";
	}
	----------------------------------*/


	//echo "Page ".$page;
	/*
	if ($base->getStageID()==100)
		$page = "task.php";
	else
		if ($base->getStageID()==80)
			$page = "stimuli.php";
		else
			if ($base->getStageID()==78)
				$page = "pretask.php";
			else
				if ($base->getStageID()==91)
					$page = "posttask.php";
				else
					if ($base->getStageID()==43)
						$page = "practice.php";		*/


?>


<?php
	$base = Base::getInstance();
	// Temporary fix for disabling toolbar buttons when clicked Finish
	if(isset($_GET['disallowbrowsing'])){
		$base->setAllowBrowsing(0);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

<link rel="Coagmento icon" type="image/x-icon" href="../img/favicon.ico">
<link rel="stylesheet" type="text/css" href="ajaxtabs/ajaxtabs.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/tabview/assets/skins/sam/tabview.css" />
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/connection/connection-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/element/element-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/tabview/tabview-min.js"></script>

<script type="text/javascript" src="js/utilities-old.js"></script>
<script type="text/javascript" src="ajaxtabs/ajaxtabs.js"></script>



<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript"> jQuery.noConflict(); </script>
<script type="text/javascript">





/***********************************************
 * Ajax Tabs Content script v2.2- � Dynamic Drive DHTML code library (www.dynamicdrive.com)
 * This notice MUST stay intact for legal use
 * Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
 ***********************************************/

	var homeURL = "<?php echo $homeURL;?>"
	var uri = homeURL+"services/checkStageSidebar.php";

	var InfiniteAjaxRequest = function () {
		 jQuery.ajax({
    	        url: uri,
    	        success: function(data) {
    	            // do something with "data"
    	             //alert("hi1: "+data);
    	            if (data!="0")
        	        {

						if (data=="5")
						{
							//alert(data);
							<?php
									echo "document.location = '".$homeURL."sidebar/sidebar.php?show=true';\n";
							?>
						}
						if (data=="4")
						{
							//alert(data);
							<?php
									echo "document.location = '".$homeURL."sidebar/sidebar.php?clean=true';\n";
							?>
						}
						if (data=="2")
						{
							//alert(data);
							<?php
									if ((!(isset($_GET['answer'])))&& (!(isset($_GET['clean']))))
									{
										//echo "alert(data);";
										//echo "content.wrappedJSObject.location = '".$homeURL."instruments/".$page."?answer=true';\n";
										echo "content.location = '".$homeURL."instruments/".$page."?answer=true';\n";
										echo "document.location = '".$homeURL."sidebar/sidebar.php?answer=true&snippets=true';\n";
										//echo "document.location = '".$homeURL."sidebar/sidebar.php?show=true';\n";
										//echo "return;";
									}
									else
										//echo "InfiniteAjaxRequest(uri);";
										echo "setTimeout(\"InfiniteAjaxRequest()\",1500);";
							?>
						}
						 else if (data=="1")
						 {
						<?php
								//echo "alert(\"hi\");";
								if (!(isset($_GET['show'])))
								{
									echo "document.location = '".$homeURL."sidebar/sidebar.php?show=true';\n";
									//echo "return;";
								}
								else
									//echo "InfiniteAjaxRequest(uri);";
									echo "setTimeout(\"InfiniteAjaxRequest()\",1500);";
						?>

						 }
						 else if (data=="3")
								<?php
										if (!(isset($_GET['clean'])))
										{
											echo "document.location = '".$homeURL."sidebar/sidebar.php?clean=true';\n";
											//echo "document.location = '".$homeURL."sidebar/sidebar.php';\n";
											//echo "return;";
										}
										else
											//echo "InfiniteAjaxRequest(uri);";
											echo "setTimeout(\"InfiniteAjaxRequest()\",1500);";
								?>
    	            }
    	            else
    	            	setTimeout("InfiniteAjaxRequest()",1500);
    	        },
    	        error: function(xhr, ajaxOptions, thrownError) {
    	        }
    	    });
    	};

    	//InfiniteAjaxRequest(homeURL+"services/checkStageSidebar.php");
    	InfiniteAjaxRequest ();
    	//setTimeout("InfiniteAjaxRequest()",3000);
</script>

<title>
Sidebar
</title>


<link rel="stylesheet" href="css/stylesSidebarFusion.css" type="text/css" />
<style type="text/css">
.cursorType{
cursor:pointer;
cursor:hand;
}

</style>

</head>
<?php

	$base = Base::getInstance();



	if ($base->isSessionActive())
	{

	  if (isset($_GET['show'])) //&&(!(isset($_GET['answer']))))
	  {
	  	echo "<body onload=\"startTimeTask()\">";
//        echo "<body>";

		//first region
		//if (($base->getStageID()==80)||($base->getStageID()==100))
		if ($base->getAllowCommunication()==1)
		{
			//Show question and timer
			//Retrieve question
			$qQuery = "SELECT question
  					     FROM questions_study
					    WHERE questionID = '".$base->getQuestionID()."'";

			$connection = Connection::getInstance();
			$results = $connection->commit($qQuery);
			$line = mysql_fetch_array($results, MYSQL_ASSOC);
			$question = $line['question'];

			$extra = 0; //Adding extra seconds to compensate questionnaires, reading question, and syncrhonization
			$taskRemainingTime = $base->getTaskRemainingTime()+$extra;

			//Find question min time and max time

			$minTimeQuery = "SELECT minTimeQuestion, maxTime
  					     	FROM session_stages
					    	WHERE stageID = '".$base->getStageID()."'";

			$minTimeconnection = Connection::getInstance();
			$minTimeresults = $minTimeconnection->commit($minTimeQuery);
			$minTimeline = mysql_fetch_array($minTimeresults, MYSQL_ASSOC);
			$minTime = $minTimeline['minTimeQuestion'];
			$maxTime = $minTimeline['maxTime'];


			/*
			* Fix for showing timer at start of task display; hiding snippets and finish button until search is enabled.
			*/
			$questionID = $base->getQuestionID();
//			$queryQuest = "SELECT distinct(1) res
//							 	   FROM questionnaire_pretask
//								   WHERE stageID = '".$base->getStageID()."' and userID = '".$base->getUserID()."' and projectID = '".$base->getProjectID()."' and questionID = '$questionID'";
//
//			$connection = Connection::getInstance();
//			$queryQuestresults = $connection->commit($queryQuest);
//			$numRows = mysql_num_rows($queryQuestresults);
//			$isPretaskQuestionnaireComplete = false;
//
//			if ($numRows>0)
//			{
//					$isPretaskQuestionnaireComplete = true;
//			}

			// Blinker threshold for practice and real tasks
			$blinkThreshold = 300; //real task

//			if($base->getStageID()==50) //practice task
//			{
//				$blinkThreshold = 30;
//
//			}



?>






<script type="text/javascript">
var currentTimeTask = <?php echo $taskRemainingTime;?>;
var overallTimeTask = currentTimeTask;
var minTime = <?php echo $minTime;?>;
var maxTime = <?php echo $maxTime;?>;
var blinkThreshold=<?php echo $blinkThreshold;?>;


function startTimeTask()
{
    if (currentTimeTask>=0)
    {
        var h = Math.floor(overallTimeTask/3600);
        var m = Math.floor((overallTimeTask%3600)/60);
        var s = overallTimeTask%60;
        h=checkTime(h);
        m=checkTime(m);
        s=checkTime(s);

        <?php

        if($base->isTaskInTime()){

//        if($base->isTaskInTime() && $isPretaskQuestionnaireComplete){
            ?>
//            if(maxTime-currentTimeTask>=minTime)
//            {
//                document.getElementById('finishbutton').style.display = 'block';
//            }
//            else
//            {
//                document.getElementById('finishbutton').style.display = 'none';
//            }
            <?php
        }
        ?>


        if (overallTimeTask<blinkThreshold)
        {
            document.getElementById('taskClock').style.color = "Red";
            document.getElementById('taskClock').innerHTML=h+":"+m+":"+s; //+" - - "+currentTimeTask;
            setTimeout('blinkTaskTime()',500);
        }

        else
            document.getElementById('taskClock').innerHTML=h+":"+m+":"+s; //+" - - "+currentTimeTask;
        currentTimeTask--;
        overallTimeTask--;

        t=setTimeout('startTimeTask()',1000);
    }
    else
    {
        //load post-task questionnaire
        document.getElementById('taskClock').innerHTML="00:00";
        document.getElementById("taskInfo").style.display = "none";
        <?php
        if($base->isTaskInTime()){
//        if($base->isTaskInTime() && $isPretaskQuestionnaireComplete){
            ?>
//            document.getElementById('finishbutton').style.display = 'none';
            <?php
        }
        echo "content.location = '".$homeURL."instruments/".$page."?answer=true';\n";
        echo "document.location = '".$homeURL."sidebar/sidebar.php?answer=true&snippets=true&disallowbrowsing=true';\n";
        ?>

//        content.wrappedJSObject.location = homeURL+'index.php';
//        document.location = homeURL+'sidebar/sidebar.php';
    }
}

function blinkTaskTime()
{
    document.getElementById('taskClock').style.color = "Red";
}

function checkTime(i)
{
    if (i<10)
    {
        i="0" + i;
    }
    return i;
}

function answer()
{
    content.location = homeURL+'instruments/<?php echo $page?>?answer=true';
}

function skip()
{
    //                    TEMP FIX: NOT REQUIRED FOR THIS STUDY??? PRODUCES PHP ERRORS
    //					content.location = homeURL+'instruments/
    <?php
    //                    echo $page;
    ?>
    //                    ?skip=true&qProgressID=
    <?php
    //                    echo $qProgressID;
    ?>
    //                    ';
    <?php
    echo "document.location = '".$homeURL."sidebar/sidebar.php?clean=true';\n";
    ?>
}

function finish()
{
    document.getElementById('taskClock').innerHTML="00:00";
    document.getElementById("taskInfo").style.display = "none";
//    document.getElementById('finishbutton').style.display = 'none';
    <?php
    echo "content.location = '".$homeURL."instruments/".$page."?answer=true';\n";
    echo "document.location = '".$homeURL."sidebar/sidebar.php?answer=true&snippets=true&disallowbrowsing=true';\n";
    ?>

}


<?php

     $flagSession2 = false;
     $height = "100px";

     if ($base->getStageID()>=120)
     {
     $flagSession2 = true;
     $height = "300px";
     }
    ?>
</script>



<table class="body">
<tr>
<td>
<!-- 			<div id="statusMessage">&nbsp;&nbsp;<span style="font-size:10px;color:red;">Warning: Coagmento is turned off.</span><br/>&nbsp;&nbsp;<span style="color:blue;text-decoration:underline;cursor:pointer;font-size:10px;" onClick="tabsReload(0,'source');">Activate it</span>. <span style="color:blue;text-decoration:underline;cursor:pointer;font-size:10px;" onClick="tabsReload(0,'source');">Learn more.</span></div> -->
<span style="font-size:10px;"><div id="currentProj"></div></span>
<!-- 				<div id="reload"><span style="font-size:11px;color:blue;text-decoration:underline;cursor:pointer;" onClick="location.reload(true);">Reload the Sidebar</div>-->

<center>
<div id="taskInfo" style="border-color:black; height:<?php echo $height;?>">
<table>
<tr align="center">
<th colspan="3">Your remaining time</th>
</tr>
<tr align="center">
<td><div id="taskClock" style="font-weight:bold; color:Blue; font-size:30px"></div> </td>
</tr>
</table>
</div>
</center>


<ul class="acc2" id="acc2">
<?php
    $userID = Base::getInstance()->getUserID();
    $query = "SELECT numUsers from users WHERE userID='$userID'";
    $connection = Connection::getInstance();
    $results = $connection->commit($query);
    $line = mysql_fetch_array($results,MYSQL_ASSOC);
    $num_users = $line['numUsers'];

    if($num_users>1){
        ?>


<li>
<h4><img src="../img/chat.jpg" width=36 style="vertical-align:middle;border:0" /> Chat<br/><span style="color:gray;font-size:10px;">Chat with collaborators of the active project.</span></h4>
<div class="acc-section2">


<div id="chat" class="acc-content2">
<?php
    if (($projectID > 0)&&($projectTitle != ""))
    {
        $chat->printChat();

    }
    else
    {
        echo "In order to use the chat you must select a project first.ID".$projectID."title".$projectTitle;
    }
    //require_once("sidebarChat.php");
    ?>
</div>



</div>
</li>
<?php
    }
    ?>



<li>
<h4><img src="../img/history.jpg" width=32 style="vertical-align:middle;border:0" />&nbsp; History<br/><span style="color:gray;font-size:10px;">See personal/shared history and objects.</span></h4>
<div class="acc-section2">
<div id="history" class="acc-content2">
<ul id="tabs" class="shadetabs">
<?php


    $userID = Base::getInstance()->getUserID();
    $stageID = Base::getInstance()->getStageID();
    $sessionID = -1;
    $sessionID = 1;
//    if($stageID<Stage::SESSION_TWO_START)
//    {
//        $sessionID=1;
//    }
//    else
//    {
//        $sessionID=2;
//    }

//    $qQuery = "SELECT userID, topicAreaID, sessionID
//    FROM user_session_topic
//    WHERE userID='$userID' AND sessionID='$sessionID'";

    $qQuery = "SELECT userID, topicAreaID
    FROM users
    WHERE userID='$userID'";

    $connection = Connection::getInstance();
    $results = $connection->commit($qQuery);
    $numRows = mysql_num_rows($results);

    $topicAreaID = -1;
    if($numRows>0)
    {
        $line = mysql_fetch_array($results, MYSQL_ASSOC);
        $topicAreaID = $line['topicAreaID'];
    }



    ?>
<li><a href="sidebarComponents/snippets.php" rel="tabscontainer" class="selected">Snippets</a></li>
<?php
//    if($topicAreaID == Base::TASK_CIS){ //CIS
//
//        echo "<li><a href=\"sidebarComponents/searches.php\" rel=\"tabsycontainer\">Searches</a></li>";
//
//    }else if($topicAreaID == Base::TASK_MDP && $stageID>Stage::SESSION_ONE_WARMUP){
//        echo "<li><a href=\"sidebarComponents/mdp_documents.php\" rel=\"tabsycontainer\">Documents</a></li>";
//    }
  ?>
<li><a href="sidebarComponents/bookmarks.php" rel="tabscontainer" class="selected">Bookmarks</a></li>
</ul>
<div id="" style="border:1px solid gray; width:285px; margin-bottom: 1em; padding: 10px">  </div>
<script type="text/javascript">
var tabs=new ddajaxtabs("tabs", "tabsdivcontainer");
tabs.setpersist(true);
tabs.setselectedClassTarget("link"); //"link" or "linkparent"
tabs.init();
</script>
</div>
</div>
</li>








</ul>
</td>
</tr>
</table>


<script type="text/javascript" src="script.js"></script>


<script type="text/javascript">

var parentAccordion=new TINY.accordion.slider("parentAccordion");
parentAccordion.init("acc2","h4",0,-1);

var nestedAccordion=new TINY.accordion.slider("nestedAccordion");
nestedAccordion.init("nested","h4",1,-1,"acc-selected");

</script>







			<script type="text/javascript">

				function answer()
				{
                    alert('finished!');
                    <?php
                        echo "content.location = '".$homeURL."instruments/".$page."?answer=true';\n";
                        echo "document.location = '".$homeURL."sidebar/sidebar.php?answer=true&snippets=true&disallowbrowsing=true';\n";
                    ?>
                }
				function skipwarmup()
				{
//					content.location = homeURL+'instruments/<?php echo $page?>?skip=true&qProgressID=
                    <?php
//                    echo $qProgressID;
                    ?>
//                    ';
					<?php
                        echo "content.location = '".$homeURL."instruments/".$page."?answer=true';\n";
                        echo "document.location = '".$homeURL."sidebar/sidebar.php?answer=true&snippets=true&disallowbrowsing=true';\n";
                    ?>
				}

				function finish()
				{
					document.getElementById('taskClock').innerHTML="00:00";
					document.getElementById("taskInfo").style.display = "none";
//					document.getElementById('finishbutton').style.display = 'none';
					<?php
					echo "content.location = '".$homeURL."instruments/".$page."?answer=true';\n";
					echo "document.location = '".$homeURL."sidebar/sidebar.php?answer=true&snippets=true&disallowbrowsing=true';\n";
					?>

				}
			</script>

<?php
//    if($base->getStageID() == Stage::SESSION_ONE_WARMUP)
//    {
    ?>
<!--			<center>
			<div id="taskInfo" style="border-color:black">
<table>
<tr><td>Press this button to skip the warmup phase.</td></tr>
<tr><td><button onclick="skipwarmup();">Skip</button></td></tr>
</table>
				</div>
		    </center>-->
<?php
//    }

?>


<?php

		}
	}
  }
?>



</body>
</html>
