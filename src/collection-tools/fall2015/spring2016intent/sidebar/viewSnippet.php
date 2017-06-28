<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');
	require_once('../core/Util.class.php');

    $base = new Base();

	if ($base->isSessionActive())
	{
        if (isset($_GET['value']))
        {
        	$snippetID = $_GET['value'];

        	Util::getInstance()->saveAction("View Snippet",$snippetID, $base);

//       	 	$query = "SELECT snippetID, userID, SUBSTRING((SELECT username from users b where a.userID = b.userID),1,5) username, url, snippet, time
//					 FROM snippets a
//			 		 WHERE status=1
//			  		 AND snippetID = $snippetID";
            $query = "SELECT snippetID, userID, (SELECT username from users b where a.userID = b.userID) username, url, snippet,title, time, timestamp
            FROM snippets a
            WHERE status=1
            AND snippetID = $snippetID";

 			$connection = Connection::getInstance();
			$results = $connection->commit($query);
			$numRows = mysql_num_rows($results);

			if ($numRows>0)
			{
        		$line = mysql_fetch_array($results, MYSQL_ASSOC);
				$url = $line['url'];
				$title = stripslashes($line['title']);
				$snippet = stripslashes($line['snippet']);
				$username = $line['username'];
				$time = $line['time'];
				$userID = $line['userID'];
				$pretty_time = strftime("%l:%M%P on %b %e, %Y", $timestamp);

				$user = "";
				if ($base->getUserID()==$userID)
					$user = "You";
				else
					$user = $username;
?>

<html>
    <head>
		<title>Snippet View</title>
    </head>
	<script type="text/javascript" src="js/utilities.js"></script>
<body>

			<!--  <div>The following snippet was collected by <strong><?php echo $user;?></strong> at <strong><?php echo $time;?></strong> from this <strong><a onclick="javascript:ajaxpage('insertAction.php?action=Revisit Page From Snippet&value=<?php echo $snippetID;?>',null)" href="<?php echo $url; ?>" target="_new">link</a></strong></div> -->
			<div>

				<!-- The following snippet was collected by <strong><?php echo $user;?></strong> at <strong><?php echo $time;?></strong> -->
			<?php
				if ($base->getAllowBrowsing())
				{
			?>
			<h3><a href="<?php echo $url; ?>" target="_new"><?php echo $title; ?></a></h3>
			 		<!-- from this <strong><a onclick="javascript:addAction('Revisit Page From Snippet','<?php echo $snippetID;?>')" href="<?php echo $url; ?>" target="_new">link</a></strong></div> -->
			<?php
				}
			?>
			<h4>Saved by <strong><?php echo $user;?></strong> at <strong><?php echo $time;?></strong></h4>
			</div>
			<hr />
			<div><p><strong>Snippet:</strong> <?php echo $snippet;?></p></div>
</body>
</html>

<?php

			}

        }
	}
?>
