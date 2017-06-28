<?php

    require_once('../../core/Connection.class.php');
    require_once('../../core/Base.class.php');
    require_once('../../core/User.class.php');

	//if ((isset($_SESSION['CSpace_userID']))) {
		//require_once("functions.php");
		//require_once("../connect.php"); USE MYSQL CONNECTION
        /*$userID = $_SESSION['CSpace_userID'];
		if (isset($_SESSION['CSpace_projectID']))
			$projectID = $_SESSION['CSpace_projectID'];
		$orderBy = $_SESSION['orderByQueries'];*/
        // echo "<a alt=\"Refresh\" class=\"cursorType\" onclick=\"javascript:reload('sidebarComponents/sources.php','sourcesBox')\" style=\"font-size:12px; font-weight: bold; color:orange\">Reload</a>\n";
        echo "<div id=\"floatSourceLayer\" style=\"position:absolute;  width:150px;  padding:16px;background:#FFFFFF;  border:2px solid #2266AA;  z-index:100; display:none \"></div>";
        echo "<div id=\"floatSourceLayerDelete\" style=\"position:absolute;  width:150px;  padding:16px;background:#FFFFFF;  border:2px solid #2266AA;  z-index:100; display:none \"></div>";
		echo "<table width=100% cellspacing=0>\n";
		echo "<tr>";
		// echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('sources','source asc','sourcesBox','sources.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('sources','userName desc','sourcesBox','sources.php')\"></td>";
		echo "<td align=\"left\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('sources','host asc','sourcesBox','sources.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('sources','host desc','sourcesBox','sources.php')\"></td>";
		// echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('sources','finalRating asc','sourcesBox','sources.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('sources','finalRating desc','sourcesBox','sources.php')\"></td>";
		echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('sources','timestamp asc','sourcesBox','sources.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('sources','time desc','sourcesBox','sources.php')\"></td>";
		// echo "<td></td>";
		echo "</tr>";
?>
<tr>
  <!-- <th>User</th> -->
  <th align="left">Source</th>
  <th align="center">Date</th>
</tr>
<?php
        //retrieve username, query, and query ID
        $base = Base::getInstance();
        $projectID = $base->getProjectID();
        $userID = $base->getUserID();
        $connection = Connection::getInstance();
        $questionID = $base->getQuestionID();
        $table = "sources";
        $orderBy = "host DESC";
        if (isset($_SESSION['orderBy'.$table])){
          $orderBy = $_SESSION['orderBy'.$table];
        }
        $only_mine = isset($_SESSION['only_mine']) ? $_SESSION['only_mine'] : false;
        $only_mine_clause = sprintf(" WHERE a.userID=%d", $base->getUserID());
        if(!$only_mine){
          $only_mine_clause = "";
        }
        // $query = "SELECT * FROM (SELECT * FROM (SELECT * FROM queries WHERE projectID='$projectID' AND questionID='$questionID' AND status=1) a INNER JOIN (SELECT userID as secondUserID,userName FROM users) b ON b.secondUserID=a.userID " . $only_mine_clause . " GROUP BY a.userID,a.`query`) c ORDER BY ".$orderBy;
        $query = "SELECT * FROM (SELECT userID,`host`,projectID,MIN(`date`) as `date`,MIN(`time`) as `time`,MIN(`timestamp`) as `timestamp` FROM (SELECT userID,projectID,`date`,`time`,`timestamp`,host,url,status FROM bookmarks UNION ALL SELECT userID,projectID,`date`,`time`,`timestamp`,host,url,status from snippets) a WHERE projectID='$projectID' AND status=1 AND host IS NOT NULL AND url NOT LIKE '%coagmento.org/spring2016intent%' AND url NOT LIKE '%coagmento.org/fall2015intent%' AND url NOT LIKE '%about:blank%' AND url NOT LIKE '%about:home%' AND url NOT LIKE '%about:newtab%' AND url NOT LIKE '%about:addons%' GROUP BY host) d ORDER BY ".$orderBy;
        $results = $connection->commit($query);
        $bgColor = '#E8E8E8';

        $numRows = mysql_num_rows($results);

        $userMap = User::getIDMap($projectID);

        // echo "<a id='only_mine_select' style='cursor:pointer;text-decoration:underline' onclick='updateOnlyMine(" . ($only_mine ? "false" : "true")  . ", 'sources', refreshSources)'>";
        // echo ($only_mine ? "Show everyone's data" : "Show only my data");
        // echo "</a>";

        while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
            $sourceID = urlencode($line['host']);
            //$userName = TODO : use a username.  Make map from userID to username, for each user in the project.
            // $userIDItem = $line['userID'];
            // $userName = isset($userMap[$userIDItem]) ? $userMap[$userIDItem] : "";

            $source= $line['host'];
            $time = $line['time'];
            // $source = stripslashes($line['query']);
            $date = strtotime($line['date'] . ' ' . $line['time']);
            $display_date = strftime("%m/%d", $date);
            //if this is the same day, show the time instead
            $date_info = getdate($date);
            $today_info = getdate(time());
            if($date_info["year"] == $today_info["year"] && $date_info["yday"] == $today_info["yday"]){
              $display_date = strftime("%l:%M%p", $date);
            }
			      // $url = $line['url'];
            // $sourceAux = substr($source, 0, 200)." (" . $source . ")";
            $sourceAux = $source;
            $sourceShown = $sourceAux;
            if (strlen($sourceShown)>25) {
                $sourceShown = substr($sourceShown, 0, 20);
                $sourceShown = $sourceShown . '..';
            }

            echo "<tr style=\"background:$bgColor;\">";
            // echo "<td><span style=\"font-size:10px\">$userName</span>&nbsp;</td>";
            echo "<td><span style=\"font-size:10px\">";
            if (1){
                $viewSearchOnWindow = "window.open('viewSource.php?value=$sourceID','Source View','directories=no, toolbar=no, location=no, status=no, menubar=no, resizable=no,scrollbars=yes,width=400,height=400,left=600')";
                echo "<a alt=\"View\" class=\"cursorType\" onmouseover=\"javascript:showSource('floatSourceLayer',null,'$sourceID','text')\" onmouseout=\"javascript:hideLayer('floatSourceLayer')\" onclick=\"javascript:$viewSearchOnWindow\" style=\"font-size:10px; color:blue\">$sourceShown</a></span></td>\n";
//                echo "<font color=blue><a onclick=\"javascript:ajaxpage('sidebarComponents/insertAction.php?action=sidebar-query&value='+$sourceID,null)\" href=\"$url\" class=\"tt\" target=\"_new\" style=\"font-size:10px\">$sourceAux</a></span></td>\n";
//				echo "<font color=blue><a onclick=\"javascript:ajaxpage('sidebarComponents/insertAction.php?action=sidebar-query&value='+$sourceID,null)\" href=\"$url\" class=\"tt\" target=_content style=\"font-size:10px\">$sourceAux</a></span></td>\n";
			}else{
				echo "$sourceShown</span></td>\n";
            }
            echo "<input type=\"hidden\" id=\"queryurl$sourceID\" value=\"$source\">";

            echo "<input type=\"hidden\" id=\"querysource$sourceID\" value=\"$source\">";
			echo "<input type=\"hidden\" id=\"queryValue$sourceID\" value=\"$source\">";
            echo "<input type=\"hidden\" id=\"time$sourceID\" value=\"$time\">";
			//$ratingRepresentation = getRatingRepresentation($finalRating,$sourceID,'queries','floatSourceLayer','sourcesBox','sources.php');
			// echo "<td align=\"center\"></td>";
			echo "<td align=\"right\" onmouseover=\"javascript:showTime('floatSourceLayer',null,'$sourceID')\" onmouseout=\"javascript:hideLayer('floatSourceLayer')\"><span style=\"font-size:10px\">$display_date</span></td>";
			//echo "<td align=\"right\"><img src=\"images/copy.gif\" height=\"18\" width=\"18\" alt=\"Copy\" class=\"cursorType\" onclick=\"javascript:copyToClipboard('queryValue$sourceID')\"></td>";


            //TEMP: REMOVED THIS FOR EDUSEARCH -> Matt
            // if ($userID==$userIDItem)
            //     echo "<td align=\"right\" class=\"cursorType\" onclick=\"javascript:deleteItem('floatSourceLayerDelete',null,'$sourceID','queries','sourcesBox','sources.php')\"><span style=\"font-size:10px; color:red; font-weight: bold \"> <a style=\"font-size:10px; color:$bgColor\"> - </a>X</span></td>";
            // else
            //     echo "<td></td>";



            echo "</tr>";

            if ($bgColor == '#E8E8E8')
				$bgColor = '#FFFFFF';
			else
				$bgColor = '#E8E8E8';
        }
        echo "</table>\n";













?>
