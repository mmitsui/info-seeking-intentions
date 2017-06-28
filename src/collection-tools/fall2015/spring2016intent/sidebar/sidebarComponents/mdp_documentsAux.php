<?php
    
    require_once('../../core/Connection.class.php');
    require_once('../../core/Base.class.php');
	//if ((isset($_SESSION['CSpace_userID']))) {
		//require_once("functions.php");
		//require_once("../connect.php"); USE MYSQL CONNECTION
        /*$userID = $_SESSION['CSpace_userID'];
		if (isset($_SESSION['CSpace_projectID']))
			$projectID = $_SESSION['CSpace_projectID'];
		$orderBy = $_SESSION['orderByQueries'];*/
                echo "<a alt=\"Refresh\" class=\"cursorType\" onclick=\"javascript:reload('sidebarComponents/searches.php','queriesBox')\" style=\"font-size:12px; font-weight: bold; color:orange\">Reload</a>\n";
                echo "<div id=\"floatQueryLayer\" style=\"position:absolute;  width:150px;  padding:16px;background:#FFFFFF;  border:2px solid #2266AA;  z-index:100; display:none \"></div>";
                echo "<div id=\"floatQueryLayerDelete\" style=\"position:absolute;  width:150px;  padding:16px;background:#FFFFFF;  border:2px solid #2266AA;  z-index:100; display:none \"></div>";
		echo "<table width=100% cellspacing=0>\n";
		echo "<tr>";
		/*echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Queries','userName asc','queriesBox','searches.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Queries','userName desc','queriesBox','searches.php')\"></td>";
		echo "<td align=\"left\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Queries','query asc','queriesBox','searches.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Queries','query desc','queriesBox','searches.php')\"></td>";
		echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Queries','finalRating asc','queriesBox','searches.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Queries','finalRating desc','queriesBox','searches.php')\"></td>";
		echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Queries','queryID asc','queriesBox','searches.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Queries','queryID desc','queriesBox','searches.php')\"></td>";
		//echo "<td></td>";*/
		echo "</tr>";
        //retrieve username, query, and query ID
        $base = Base::getInstance();
        $projectID = $base->getProjectID();
        $userID = $base->getUserID();
        $connection = Connection::getInstance();
    
        $query = "SELECT * FROM mdp_documents ORDER BY documentID ASC";
        $connection = Connection::getInstance();
        $results = $connection->commit($query);
        $bgColor = '#E8E8E8';
    

        while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
            $documentID = $line['documentID'];
            $text = $line['documentName'];
            $userName = $userID;
            //$userName = TODO : use a username.  Make map from userID to username, for each user in the project.
//            $time = $line['time'];
//            $queryVal = stripslashes($line['query']);
//            $date = strtotime($line['date']);
//            $date = strftime("%m/%d", $date);
			$url = "http://edusearch.coagmento.org/instruments/mdp/".$line['fileName'];
//            $queryAux = substr($queryVal, 0, 11)."-".substr($source, 0, 4);
//            echo "<tr style=\"background:$bgColor;\"><td><span style=\"font-size:10px\">$userName</span> </td><td><span style=\"font-size:10px\">";
            echo "<tr style=\"background:$bgColor;\"><td><span style=\"font-size:10px\">$documentID</span> </td><td><span style=\"font-size:10px\">";
			
            if ($url)
				echo "<font color=blue><a onclick=\"javascript:ajaxpage('sidebarComponents/insertAction.php?action=sidebar-document&value='+$documentID,null)\" href=\"$url\" class=\"tt\" target=_content style=\"font-size:10px\">$text</a></span></td>\n";
			else
				echo "$text</span></td>\n";
            
//			echo "<input type=\"hidden\" id=\"queryValue$documentID\" value=\"$queryVal\">";
//            echo "<input type=\"hidden\" id=\"time$documentID\" value=\"$time\">";

			echo "<td align=\"center\"></td>";
//			echo "<td align=\"right\" onmouseover=\"javascript:showTime('floatQueryLayer',null,'$documentID')\" onmouseout=\"javascript:hideLayer('floatQueryLayer')\"><span style=\"font-size:10px\">$date</span></td>";
            
            echo "<td align=\"right\" onmouseover=\"javascript:showTime('floatQueryLayer',null,'$documentID')\" onmouseout=\"javascript:hideLayer('floatQueryLayer')\"><span style=\"font-size:10px\"></span></td>";
            
            

                echo "<td></td>";
            echo "</tr>";

            if ($bgColor == '#E8E8E8')
				$bgColor = '#FFFFFF';
			else
				$bgColor = '#E8E8E8';
        }
        echo "</table>\n";
    
				
			

			

			
            



			
		//}
		//echo "</table>\n";
		//}
	//else {
	//	echo "Your session has expired. Please <a href=\"http://www.coagmento.org/loginOnSideBar.php\" target=_content><span style=\"color:blue;text-decoration:underline;cursor:pointer;\">login</span> again.\n";
	//}
?>