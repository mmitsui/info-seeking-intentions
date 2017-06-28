<?php
	session_start();

    require_once('../core/Base.class.php');
		require_once('../core/Tags.class.php');
		require_once('../core/Util.class.php');
		require_once('../core/Connection.class.php');

    $base = Base::getInstance();
		$base->registerActivity();
    $userID = $base->getUserID();
    $projectID = $base->getProjectID();
		$query = "SELECT instructorID from recruits WHERE userID='$userID'";
		$cxn = Connection::getInstance();
		$r = $cxn->commit($query);
		$l = mysql_fetch_array($r,MYSQL_ASSOC);
		$instructorID = $l['instructorID'];

	$title = $_GET['title'];
	$originalURL = $_GET['page'];
	$url = $originalURL;
	$host = "";
	$p = parse_url($url);
	if ($p && isset($p['host'])){
		$host = $p['host'];
	}
	// Get the date, time, and timestamp

    $timestamp = $base->getTimestamp();
    $date = $base->getDate();
    $time = $base->getTime();
    $localDate = isset($_GET['localDate']) ? $_GET['localDate'] : "";
		$localTime = isset($_GET['localTime']) ? $_GET['localTime'] : "";
		$localTimestamp = isset($_GET['localTimestamp']) ? $_GET['localTimestamp'] : "";

		Util::getInstance()->saveActionWithLocalTime("Create Bookmark View",0,$base,$localTime,$localDate,$localTimestamp);


?>

<?php
			require_once("utilityFunctions.php");

			// Parse the URL to extract the source
			$new_url = str_replace("http://", "", $url); // Remove 'http://' from the reference
			$new_url = str_replace("https://", "", $new_url); // Remove 'https://' from the reference
			$new_url = str_replace("com/", "com.", $new_url);
			$new_url = str_replace("org/", "org.", $new_url);
			$new_url = str_replace("edu/", "edu.", $new_url);
			$new_url = str_replace("gov/", "gov.", $new_url);
			$new_url = str_replace("us/", "us.", $new_url);
			$new_url = str_replace("ca/", "ca.", $new_url);
			$new_url = str_replace("uk/", "uk.", $new_url);
			$new_url = str_replace("es/", "es.", $new_url);
			$new_url = str_replace("net/", "net.", $new_url);
			$entry = explode(".", $new_url);
			$i = 0;
			$isWebsite = 0;
					$site = NULL;

			$originalURL = $url;
			while (isset($entry[$i]) && ($isWebsite == 0))
			{
				$entry[$i] = strtolower($entry[$i]);
				if (($entry[$i] == "com") || ($entry[$i] == "edu") || ($entry[$i] == "org") || ($entry[$i] == "gov") || ($entry[$i] == "info") || ($entry[$i] == "us") || ($entry[$i] == "ca") || ($entry[$i] == "es") || ($entry[$i] == "uk") || ($entry[$i] == "net"))
				{
					$isWebsite = 1;
									if(($entry[$i] == "uk") && strpos($originalURL,'uk.yahoo.com') !== false){
											$domain = $entry[$i+2];
											$site = $entry[$i+1];
									}else if(($entry[$i] == "uk") && strpos($originalURL,'uk.search.yahoo.com') !== false){
											$domain = $entry[$i+3];
											$site = $entry[$i+2];
									}else if(($entry[$i] == "uk") && strpos($originalURL,'.co.uk') !== false){
											$domain = $entry[$i];
											$site = $entry[$i-2];
									}else{
											$domain = $entry[$i];
											$site = $entry[$i-1];
									}
				}
				$i++;
			}

			// Extract the query if there is any
			$queryString = extractQuery($originalURL);

			// Get user's tags
			$tags = new Tags();
			$available_tags = $tags->retrieveFromProject($projectID);
			require_once("templates/bookmark.php");
?>
