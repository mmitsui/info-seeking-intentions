<?php
	session_start();
	require_once('core/Connection.class.php');
	require_once('core/Base.class.php');
	require_once('core/Action.class.php');
	require_once('core/Util.class.php');
    //Variable for determining if the study is closed
    header("Location: instruments/getHome.php")
?>
