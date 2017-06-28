<?php

	if (session_id() == "")
				session_start();

	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');
	require_once('../core/Util.class.php');

	if(isset($_GET['clicktab'])){
	  $base = Base::getInstance();
	  Util::getInstance()->saveAction("Clicked Sidebar Tab: snippets",0, $base);
	}


	$base = Base::getInstance();

	if ($base->isSessionActive())
	{
	$query = "SELECT snippetID, userID, SUBSTRING((SELECT username from users b where a.userID = b.userID),1,5) username, url, title, snippet, time
			  FROM snippets a
			  WHERE projectID='".$base->getProjectID()."'
			  AND stageID='".$base->getStageID()."'
			  AND questionID='".$base->getQuestionID()."'
			  AND status=1
			  order by timestamp DESC";

	$connection = Connection::getInstance();
	$results = $connection->commit($query);
	$numRows = mysql_num_rows($results);

	if ($numRows>0)
	{
		$bgColor = '#F2F2F2';


?>
	<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
	</style>

<table width="100%" cellspacing="1">
<?php
		while ($line = mysql_fetch_array($results, MYSQL_ASSOC)) {
			$snippetID = $line['snippetID'];
			$userID = $line['userID'];
			$username = $line['username'];
			$url = $line['url'];
			$title = stripslashes($line['title']);
			$snippet = stripslashes($line['snippet']);
			$time = $line['time'];

			$snippet = substr($snippet, 0, 30);
			$snippet = $snippet . '..';

			$user == "";
			$style = "";
			if ($base->getUserID()==$userID)
			{
				$user = "You";
				$style = "style=\"font-weight:bold\"";
			}
			else
				$user = $username;

?>
	<tr style="background:<?php echo $bgColor;?>;">
		<td <?php echo $style;?>> <?php echo $user; ?> </td>
<?php
         $viewSnipetOnWindow = "window.open('viewSnippet.php?value=$snippetID','Snippet View','directories=no, toolbar=no, location=no, status=no, menubar=no, resizable=no,scrollbars=yes,width=400,height=300,left=600')";
?>
		<td style="color:Blue"><a class="cursorType" onclick="javascript:<?php echo $viewSnipetOnWindow;?>"><?php echo $snippet; ?></a></td>
		<td><?php echo $time; ?></td>
	</tr>
<?php
	if ($bgColor == '#F2F2F2')
		$bgColor = 'White';
	else
		$bgColor = '#F2F2F2';
	}
	}
	}
?>
</table>
