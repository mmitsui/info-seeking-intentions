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
        	$queryID = $_GET['value'];

        	Util::getInstance()->saveAction("View Search",$queryID, $base);

//       	 	$query = "SELECT queryID, userID, SUBSTRING((SELECT username from users b where a.userID = b.userID),1,5) username, url, query, source, time
//					 FROM queries a
//			 		 WHERE queryID = $queryID";

            $query = "SELECT queryID, userID, (SELECT username from users b where a.userID = b.userID) username, url, query, source, time, timestamp
            FROM queries a
            WHERE queryID = $queryID";

 			$connection = Connection::getInstance();
			$results = $connection->commit($query);
			$numRows = mysql_num_rows($results);

			if ($numRows>0)
			{
        		$line = mysql_fetch_array($results, MYSQL_ASSOC);
				$url = $line['url'];
        $source = $line['source'];
				$query = stripslashes($line['query']);
				$username = $line['username'];
				$time = $line['time'];
				$timestamp = $line["timestamp"];
				$pretty_time = strftime("%l:%M%P on %b %e, %Y", $timestamp);
				$userID = $line['userID'];

				$user = "";
				if ($base->getUserID()==$userID)
					$user = "You";
				else
					$user = $username;
?>

<html>
    <head>
		<title>Search View</title>
    </head>
	<script type="text/javascript" src="js/utilities.js"></script>
<body>
	<center>
			<br />
			<div><strong><?php echo $user;?></strong> searched for <strong>"<?php echo $query;?>"</strong> from <strong><?php echo $source;?></strong> at <?php echo $pretty_time;?>.
			<?php
				if ($base->getAllowBrowsing())
				{
			?>
			 		<br/>You may review it <strong><a onclick="javascript:ajaxpage('insertAction.php?action=Revisit Page From Search&value=<?php echo $queryID;?>',null)" href="<?php echo $url; ?>" target="_new">here</a></strong>
			<?php
				}
			?>
			</div>
			<br />
			<hr />
	</center>
</body>
</html>

<?php

			}

        }
	}
?>
