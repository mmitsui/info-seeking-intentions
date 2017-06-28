<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');
	require_once('../core/Util.class.php');

	$base = Base::getInstance();

	if ($base->isSessionActive())
	{
		$userID = $base->getUserID();
		$projectID=$base->getProjectID();
		$localTime = $_GET['localTime'];
		$localDate = $_GET['localDate'];
		$localTimestamp = $_GET['localTimestamp'];


    $connection = Connection::getInstance();

		$bookmark_results = $connection->commit("SELECT * FROM bookmarks WHERE userID='$userID'");
		$old_bookmark_results = $connection->commit("SELECT * FROM bookmarks_group2 WHERE projectID='2'");
		$snippet_results = $connection->commit("SELECT * FROM snippets WHERE userID='$userID'");


		$query = "SELECT Q.question as question,Q.questionID as questionID FROM questions_study Q WHERE Q.questionID=$questionID";
		$results = $connection->commit($query);
		$question1 = '';
		$line = mysql_fetch_array($results,MYSQL_ASSOC);
		$questionID = $line['questionID'];
		Util::getInstance()->saveActionWithLocalTime("View My Task",$questionID,$base,$localTime,$localDate,$localTimestamp);


?>

<html>
    <head>
		<title>View History</title>
<link rel="stylesheet" href="../study_styles/custom/background.css">
    </head>
<body>
			<h2>History</h2>
			<hr>

			<h3 style="background-color:green;color:white">Snippets</h3>
			<?php

			$line = '';

			while($line = mysql_fetch_array($snippet_results,MYSQL_ASSOC)){

				$ct += 1;
				$title = $line['title'];
				$url = $line['url'];
				$snippet = $line['snippet'];

				echo "<h5>Snippet $ct</h5>";
				echo "<p>Page Title: $title</p>";
				echo "<p>URL: $url</p>";
				echo "<p>Snippet: $snippet</p>";

			}
			?>


			<hr>
			<h3 style="background-color:green;color:white">Bookmarks</h3>

			<?php
			$line = '';
			$ct = 0;

			while($line = mysql_fetch_array($bookmark_results,MYSQL_ASSOC)){
				$ct += 1;


				$title = $line['title'];
				$url = $line['url'];
				$snippet = $line['snippet'];

				echo "<h5>Bookmark $ct</h5>";
				echo "<p>Page Title: $title</p>";
				echo "<p>URL: $url</p>";

			}


			?>

			<hr>
			<h3 style="background-color:orange">Previous Bookmarks</h3>

			<?php
			$line = '';
			$ct = 0;

			while($line = mysql_fetch_array($old_bookmark_results,MYSQL_ASSOC)){
				$ct += 1;


				$title = $line['title'];
				$url = $line['url'];
				$snippet = $line['snippet'];

				echo "<h5>Bookmark $ct</h5>";
				echo "<p>Page Title: $title</p>";
				echo "<p>URL: $url</p>";

			}
			?>

</body>
</html>

<?php

			// }


	}
?>
