<?php
/*
Simpler login page for workspace. Allows redirect.
Maybe we can use this as a replacement for the sidebar login as well.
*/
	session_start();

	require_once("../core/Base.class.php");
	require_once("../core/Util.class.php");

	$base = Base::getInstance();
  $feedback = "";

?>
<html>
  <head>
    <style type="text/css">
			*{
				margin: 0;
				padding: 0;
			}
      .feedback{
        background:#EEE;
        padding: 5px 10px;
      }
      #container{
        width: 325px;
        margin: 10px auto;
      }
      .row label{
        display: inline-block;
        width: 100px;
      }
      .row, .feedback{
        margin-bottom: 10px;
			}
			h3{
				font-family: "Arial";
				font-size: 16px;
				font-weight: normal;
			}
			h3 span{
				position: relative;
				top: -12px;
				left: 10px;
			}
			#header_container{
			  background: #7eb3dd;
			  border-bottom: 2px #1B77E0 solid;
			}
			.page_header{
			  width: 325px;
			  margin: 0px auto;
				color: #FFF;
			  position: relative;
			}
    </style>
		<link type="text/css" rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css" />
  </head>
  <body>
    <div id="container">
			This will show your workspace when you begin the main task.
    </div>
  </body>
<html>
