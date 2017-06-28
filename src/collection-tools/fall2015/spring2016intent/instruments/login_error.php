<?php

$timeLeft = $_GET['timeLeft'];
$days = floor($timeLeft / 86400);
$hours = floor(($timeLeft % 86400)/3600);
$minutes = floor(($timeLeft % 3600)/60); 

$timeMaxLeft = $_GET['timeMaxLeft'];
$maxdays = floor($timeMaxLeft / 86400);
$maxhours = floor(($timeMaxLeft % 86400)/3600);
$maxminutes = floor(($timeMaxLeft % 3600)/60); 

echo "<html><head><title>Login Error</title></head><body class='body'>\n"; 
echo "<p>\n";
echo "Time remaining to <strong>start second session</strong>: \n";
echo "</p>\n";
echo "<hr></hr>\n";
echo "<p>\n";
echo "You must wait until 2 days (48 hours) after completing the first session to log back again to do the second session.\n";
echo "</p>\n";
echo "<p>\n";
echo "You have ".$days. " days ". $hours. " hours ". $minutes ." minutes left until you can log in again.\n";
echo "</p>\n";
echo "<br><br>\n";
echo "<p>\n";
echo "Time remaining to <strong>complete second session</strong>: \n";
echo "</p>\n";
echo "<hr></hr>\n";
echo "<p>\n";
echo "Remember that you must complete the second session with in 5 days after completing the first session to be entitled for compensation.\n";
echo "</p>\n";
echo "<p>\n";
echo "You must finish the second task within ".$maxdays. " days ". $maxhours. " hours ". $maxminutes ." minutes .\n";
echo "</p>\n";
echo "</body></html>\n";
?>