<?php
    require_once('../../core/Connection.class.php');
    require_once('../../core/Base.class.php');
    require_once('../../core/Tags.class.php');
    require_once('../../core/User.class.php');
    require_once('functions.php');


    // echo "<a alt=\"Refresh\" class=\"cursorType\" onclick=\"javascript:refreshBookmarks()\" style=\"font-size:12px; font-weight: bold; color:orange\">Reload</a>\n";
    echo "<div id=\"floatBookmarkLayer\" style=\"position:absolute;  width:150px;  padding:16px;background:#FFFFFF;  border:2px solid #2266AA;  z-index:100; display:none \"></div>";
    echo "<div id=\"floatBookmarkLayerDelete\" style=\"position:absolute;  width:150px;  padding:16px;background:#FFFFFF;  border:2px solid #2266AA;  z-index:100; display:none \"></div>";
    echo "<table width=100% cellspacing=0>\n";
    // echo "<tr>";
    // echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('bookmarks','userName asc','bookmarksBox','bookmarks.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('bookmarks','userName desc','bookmarksBox','bookmarks.php')\"></td>";
    // echo "<td align=\"left\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('bookmarks','title asc','bookmarksBox','bookmarks.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('bookmarks','title desc','bookmarksBox','bookmarks.php')\"></td>";
    // // echo "<td align=\"left\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('bookmarks','rating asc','bookmarksBox','bookmarks.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('bookmarks','rating desc','bookmarksBox','bookmarks.php')\"></td>";//echo "<td></td>";
    // //  echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Bookmarks','finalRating asc','bookmarksBox','bookmarks.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('Bookmarks','finalRating desc','bookmarksBox','bookmarks.php')\"></td>";
    //  echo "<td align=\"center\"><img src=\"images/asc.gif\" height=\"10\" width=\"10\" alt=\"Asc\" class=\"cursorType\" onclick=\"javascript:changeOrder('bookmarks','bookmarkID asc','bookmarksBox','bookmarks.php')\"><span style=\"font-size:10px; color:#FFFFFF\">-</span><img src=\"images/desc.gif\" height=\"10\" width=\"10\" alt=\"Desc\" class=\"cursorType\" onclick=\"javascript:changeOrder('bookmarks','bookmarkID desc','bookmarksBox','bookmarks.php')\"></td>";
    //  echo "<td></td>";
    // echo "</tr>";

    //    TODO: May not have been part of this code.  Delete?
    //    echo "Your session has expired. Please <a href=\"http://www.coagmento.org/loginOnSideBar.php\" target=_content><span style=\"color:blue;text-decoration:underline;cursor:pointer;\">login</span> again.\n";

    $base = Base::getInstance();
    $projectID = $base->getProjectID();
    $tags = Tags::retrieveFromProject($projectID);
    $userMap = $userMap = User::getIDMap($projectID);
    $userID = $base->getUserID();
    $connection = Connection::getInstance();
    $questionID = $base->getQuestionID();

    // $query = "SELECT instructorID from recruits WHERE userID='$userID'";
		// $cxn = Connection::getInstance();
		// $r = $cxn->commit($query);
		// $l = mysql_fetch_array($r,MYSQL_ASSOC);
		// $instructorID = $l['instructorID'];


    $filter = isset($_GET['filter']) ? intval($_GET['filter']) : -1; //tag id
    $only_mine = isset($_SESSION['only_mine']) ? $_SESSION['only_mine'] : false;
    $table = "bookmarks";
    $orderBy = "bookmarkID DESC";
    if (isset($_SESSION['orderBy'.$table])){
      $orderBy = $_SESSION['orderBy'.$table];
    }
    $only_mine_clause = sprintf(" AND b.userID=%d", $base->getUserID());
    if(!$only_mine){
      $only_mine_clause = "";
    }
    $query = "SELECT * FROM (SELECT * FROM bookmarks WHERE projectID='$projectID' AND questionID='$questionID' AND status=1 AND url NOT LIKE '%coagmento.org/spring2016intent%' AND url NOT LIKE '%coagmento.org/fall2015intent%' AND url NOT LIKE '%about:blank%' AND url NOT LIKE '%about:home%' AND url NOT LIKE '%about:newtab%' AND url NOT LIKE '%about:addons%') a INNER JOIN (SELECT userID,userName FROM users) b ON b.userID=a.userID " . $only_mine_clause . " ORDER BY ".$orderBy;
    if($filter != -1){
      $query = sprintf("SELECT * FROM (SELECT B.bookmarkID,B.userID,B.projectID,B.stageID,B.questionID,B.url,B.title,B.source,B.timestamp,B.date,B.time,B.localTimestamp,B.localDate,B.localTime,B.result,B.status,
         FROM bookmarks B,tag_assignments TA WHERE B.projectID='$projectID' AND B.status=1 AND TA.bookmarkID=B.bookmarkID AND TA.tagID=%d) a INNER JOIN (SELECT userID,userName FROM users) b ON b.userID=a.userID ". $only_mine_clause . " ORDER BY ".$orderBy, $filter);
    }

    $results = $connection->commit($query);
    $bgColor = '#E8E8E8';
    $numRows = mysql_num_rows($results);

    // echo "<a id='only_mine_select' style='cursor:pointer;text-decoration:underline' onclick=\"updateOnlyMine(" . ($only_mine ? "false" : "true")  . ",'bookmarks', refreshBookmarks)\">";
    // echo ($only_mine ? "Show everyone's data" : "Show only my data");
    // echo "</a>";

    // echo "<p>Filter by tag: ";
    // echo "<select id='tagfilter' onchange='filterBy(this.value,refreshBookmarks)' class='tags'><option value='-1'>Show all</option>";
    // foreach($tags as $t){
    //   $extra = $t["tagID"] == $filter ? "selected" : "";
    //   printf("<option %s value='%d'>%s</option>",$extra,$t["tagID"],$t["name"]);
    // }
    // echo "</select></p><br/>";

    if($numRows == 0){
      echo "No bookmarks saved";
      exit();
    }


  ?>
    <!-- <tr> -->
      <!-- <th>User</th> -->
      <!-- <th>Bookmark title</th> -->
      <!-- <th>Rating</th> -->
      <!-- <th>Date</th> -->
    <!-- </tr> -->

    <?php
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
        $bookmarkID = $line['bookmarkID'];
        //$userName = TODO : use a username.  Make map from userID to username, for each user in the project.
        $userIDItem = $line['userID'];
        $userName = isset($userMap[$userIDItem]) ? $userMap[$userIDItem] : "";
        $rating = 0;
        if (isset($line['rating'])){
          $rating = $line['rating'];
        }

        // $note = $line['note'];
        // $author_qualifications = $line['author_qualifications'];
        // $useful_info = $line['useful_info'];
        $url = $line['url'];
        $title = stripslashes($line['title']);
        $type = 'text';
        $time = $line['time'];
        $date = strtotime($line['date'] . ' ' . $line['time']);
        $display_date = strftime("%m/%d", $date);
        //if this is the same day, show the time instead
        $date_info = getdate($date);
        $today_info = getdate(time());
        if($date_info["year"] == $today_info["year"] && $date_info["yday"] == $today_info["yday"]){
          $display_date = strftime("%l:%M%p", $date);
        }
        // $noteAux = substr($note, 0, 20);

//        if ($noteAux!="")
//            $title = $noteAux . '..';
//        else
//        {
            if (!$title)
                $title = $url;

            if (strlen($title)>38) {
                $title = substr($title, 0, 35);
                $title = $title . '..';
            }
//        }


        echo "<tr style=\"background:$bgColor;\">";
        // echo "<td><span style=\"font-size:10px\">$userName</span>&nbsp;</td>";
        echo "<td><span style=\"font-size:12px\">";
        //echo "<a alt=\"View\" class=\"cursorType\" onclick=\"javascript:showSnippet('floatSnippetLayer',null,'$snippetID','$type')\" style=\"font-size:10px; color:blue\">$title</a></span></td>\n";
        $viewBookmarkOnWindow = "window.open('viewBookmark.php?value=$bookmarkID','Bookmark View','directories=no, toolbar=no, location=no, status=no, menubar=no, resizable=no,scrollbars=yes,width=400,height=400,left=600')";
        echo "<a alt=\"View\" class=\"cursorType\" onclick=\"javascript:ajaxpage('sidebarComponents/insertAction.php?action=bookmark-view&value='+$bookmarkID,null)\" href=\"$url\" target=_content onmouseover=\"javascript:showBookmark('floatBookmarkLayer',null,'$bookmarkID','$type')\" onmouseout=\"javascript:hideLayer('floatBookmarkLayer')\" style=\"font-size:13px; color:blue\">$title</a></span></td>\n";
        // if($instructorID==1){
        //   echo "<a alt=\"View\" class=\"cursorType\" onclick=\"javascript:$viewBookmarkOnWindow\" onmouseover=\"javascript:showBookmark('floatBookmarkLayer',null,'$bookmarkID','$type')\" onmouseout=\"javascript:hideLayer('floatBookmarkLayer')\" style=\"font-size:10px; color:blue\">$title</a></span></td>\n";
        // }else{
        //   echo "<a alt=\"View\" class=\"cursorType\" onclick=\"javascript:$viewBookmarkOnWindow\" onmouseover=\"javascript:showBookmark2('floatBookmarkLayer',null,'$bookmarkID','$type')\" onmouseout=\"javascript:hideLayer('floatBookmarkLayer')\" style=\"font-size:10px; color:blue\">$title</a></span></td>\n";
        // }
        //                echo "<a alt=\"View\" class=\"cursorType\" onclick=\"javascript:$viewSnipetOnWindow\" onmouseover=\"javascript:showSnippet('floatSnippetLayer',null,'$snippetID','$type')\" onmouseout=\"javascript:hideLayer('floatSnippetLayer')\" style=\"font-size:10px; color:blue\">$title</a></span></td>\n";
        //			if ($url)
        //				echo "<font color=blue><a alt=\"View\" class=\"cursorType\" onclick=\"javascript:showSnippet('floatSnippetLayer',null,'$snippetID','$type')\" style=\"font-size:10px\">$title</a></span></td>\n";
        //			else
        //				echo "<font color=blue><a alt=\"View\" class=\"cursorType\" onclick=\"javascript:showSnippet('floatSnippetLayer',null,'$snippetID','$type')\" style=\"font-size:10px\">$snippet</a></span></td>\n";

        //$fullSnippet = "[Source: " . $url . "] || ".$snippet;

        echo "<input type=\"hidden\" id=\"bookmarkValue$bookmarkID\" value=\"$bookmarkID\">";
        // echo "<input type=\"hidden\" id=\"useful_info$bookmarkID\" value=\"$useful_info\">";
        // echo "<input type=\"hidden\" id=\"note$bookmarkID\" value=\"$note\">";
        // echo "<input type=\"hidden\" id=\"author_qualifications$bookmarkID\" value=\"$author_qualifications\">";

        echo "<input type=\"hidden\" id=\"source$bookmarkID\" value=\"$title\">";
        echo "<input type=\"hidden\" id=\"url$bookmarkID\" value=\"$url\">";
        echo "<input type=\"hidden\" id=\"time$bookmarkID\" value=\"$time\">";
        // $ratingRepresentation = getBookmarkRatingRepresentation($rating, $bookmarkID,'Bookmarks','floatBookmarkLayer','bookmarksBox','bookmarks.php');
        // echo "<td align=\"center\">$ratingRepresentation</td>";

        // echo "<td align=\"right\" onmouseover=\"javascript:showTime('floatBookmarkLayer',null,'$bookmarkID')\" onmouseout=\"javascript:hideLayer('floatBookmarkLayer')\"><span style=\"font-size:10px\">$display_date</span></td>";

        //TEMP: REMOVED THIS FOR EDUSEARCH -> Matt

        if ($userID==$userIDItem){
          echo "<td align=\"right\"  ><button class=\"cursorType\" onclick=\"javascript:deleteItem('floatBookmarkLayerDelete',null,'$bookmarkID','bookmarks','bookmarksBox','bookmarks.php')\"> <i class=\"fa fa-trash\"></i> UNSAVE</button></td>";
            // echo "<td align=\"right\" class=\"cursorType\" onclick=\"javascript:deleteItem('floatBookmarkLayerDelete',null,'$bookmarkID','bookmarks','bookmarksBox','bookmarks.php')\"><span style=\"font-size:10px; color:red; font-weight: bold \"> <a style=\"font-size:10px; color:$bgColor\"> - </a>X</span></td>";
        }else{
            echo "<td></td>";
        }

        /*echo "<td align=\"right\">";
         if ($url)
         echo "<font color=blue><a href=\"$url\" class=\"tt\" target=_content style=\"font-size:10px\"><img src=\"images/link.gif\" height=\"18\" width=\"18\" alt=\"Go\" class=\"cursorType\" /></a>\n";
         else
         echo "<img src=\"images/blank.gif\" height=\"18\" width=\"18\">";

         echo "<span style=\"font-size:10px; color:$bgColor\">-</span><img src=\"images/copy.gif\" height=\"18\" width=\"18\" alt=\"Copy\" class=\"cursorType\" onclick=\"javascript:copyToClipboard('snippetValue$snippetID')\"></td>";
         */

        echo "</tr>";

        if ($bgColor == '#E8E8E8')
            $bgColor = '#FFFFFF';
        else
            $bgColor = '#E8E8E8';

    }
    echo "</table>\n";


	//else {
	//	echo "Your session has expired. Please <a href=\"http://www.coagmento.org/loginOnSideBar.php\" target=_content><span style=\"color:blue;text-decoration:underline;cursor:pointer;\">login</span> again.\n";
	//}
    ?>
