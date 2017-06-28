<?php


session_start();
require_once('core/Connection.class.php');
require_once('core/Base.class.php');
require_once('core/Util.class.php');
require_once('core/Stage.class.php');

date_default_timezone_set('America/New_York');

function printPage($line){
    echo "<tr>";
    echo "<td>".date('H:i:s', $line['localTimestamp']/1000.0)."</td>";
    echo "<td class='info'>"."Page"."</td>";
    echo "<td>".$line['info']."</td>";

    $shorturl = $line['url'];
    if(strlen($shorturl)>11){
        $shorturl + substr($shorturl, 0, 11)."...";
    }
    echo "<td><a href='".$line['url']."' title='".$line['url']."'>".$shorturl."</a></td>";

    echo "</tr>";
}

function printQuery($line){
    echo "<tr>";
    echo "<td>".date('H:i:s', $line['localTimestamp']/1000.0)."</td>";
    echo "<td class='warning'>"."Query"."</td>";
    echo "<td>".$line['info']."</td>";

    $shorturl = $line['url'];
    if(strlen($shorturl)>11){
        $shorturl + substr($shorturl, 0, 11)."...";
    }
    echo "<td><a href='".$line['url']."' title='".$line['url']."'>".$shorturl."</a></td>";

    echo "</tr>";
}

function renderTable($startTime,$endTime){
//    Info: query string or

    echo "
    <table class=\"table table-striped table-bordered\">
    <thead>
      <tr>
        <th>Time</th>
        <th>Type</th>
        <th>Title/Query</th>
        <th>URL</th>
      </tr>
    </thead>
    
    <tbody>
    ";

    $cnx = Connection::getInstance();
    $query_results = $cnx->commit("SELECT `url`,`localTimestamp`,`query` as `info` FROM queries WHERE userID=1000 AND `localTimestamp` >= $startTime AND `localTimestamp` <= $endTime GROUP BY `url` ORDER BY `localTimestamp` ASC");
    $page_results = $cnx->commit("SELECT `url`,`localTimestamp`,`title` as `info` FROM pages WHERE userID=1000 AND `localTimestamp` >= $startTime AND `localTimestamp` <= $endTime GROUP BY `url` ORDER BY `localTimestamp` ASC");

    $n_page_rows = mysql_num_rows($page_results);
    $n_query_rows = mysql_num_rows($query_results);

    $n_pages_processed = 0;
    $n_queries_processed = 0;

    $query_array = array();
    $page_array = array();
    while($row = mysql_fetch_assoc($query_results)){
        $query_array[] = $row;
    }

    while($row = mysql_fetch_assoc($page_results)){
        $page_array[] = $row;
    }
    while(!($n_pages_processed >= $n_page_rows and $n_queries_processed >= $n_query_rows)){
        if($n_pages_processed >= $n_page_rows){
            printQuery($query_array[$n_queries_processed]);
            $n_queries_processed += 1;
        }else if($n_queries_processed >= $n_query_rows){
            printPage($page_array[$n_pages_processed]);
            $n_pages_processed += 1;
        }else{
            $current_page = $page_array[$n_pages_processed];
            $current_query = $query_array[$n_queries_processed];

            if($current_page['localTimestamp']==$current_query['localTimestamp']){
                printQuery($query_array[$n_queries_processed]);
                $n_queries_processed += 1;
                $n_pages_processed += 1;
            }else if($current_page['localTimestamp']<$current_query['localTimestamp']){
                printPage($page_array[$n_pages_processed]);
                $n_pages_processed += 1;
            }else{
                printQuery($query_array[$n_queries_processed]);
                $n_queries_processed += 1;
            }
        }

    }

      
    echo "</tbody>
  </table>
    ";

}
function getProgress($userID,$stageID){

}




?>
<html>
<head>
<title>Study Progress</title>
<link rel="stylesheet" href="study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="study_styles/custom/text.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">

<!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">


    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<script type="text/javascript" src="study_styles/chart.js-1.0.2/Chart.min.js"></script>
<!--	<script type="text/javascript" src="lib/jquery-2.1.3.min.js"></script>-->

	<style>


		table, tbody,thead {
			border: 2px solid black;
			overflow: hidden;
			width: 35px;
			height: 35px;
		}

		td,th,tr {
			border: 2px solid black;
		}

		th.header:not([data-sortable='false']) {
			background-image: url(img/tablesorter/bg.gif);
			cursor: pointer;
			font-weight: bold;
			background-repeat: no-repeat;
			background-position: center right;
			border-right: 1px solid #dad9c7;
			margin-left: -1px;
			padding-right: 20px;
		}

	</style>

</head>


<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.css">

<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.js"></script>


<style>


	.sidenav {
		height: 100%;
		width: 0;
		position: fixed;
		z-index: 1;
		top: 0;
		left: 0;
		background-color: #111;
		overflow-x: hidden;
		transition: 0.0s;
		padding-top: 60px;
	}

	.sidenav a {
		padding: 8px 8px 8px 32px;
		text-decoration: none;
		font-size: 18px;
		color: #818181;
		display: block;
		transition: 0.0s
	}

	.sidenav a:hover, .offcanvas a:focus{
		color: #f1f1f1;
	}

	.sidenav .closebtn {
		position: absolute;
		top: 0;
		right: 25px;
		font-size: 36px;
		margin-left: 50px;
	}

	#main {
		transition: margin-left .0s;
		padding: 16px;
	}

	@media screen and (max-height: 450px) {
		.sidenav {padding-top: 15px;}
		.sidenav a {font-size: 18px;}
	}
</style>



<noscript>
<style type="text/css">
.pagecontainer {display:none;}
</style>
<div class="noscriptmsg">
You don't have Javascript enabled.  You must enable it in your browser to proceed with the task.
</div>
</noscript>

<body class="style1">



<div id="main">
	<center><h2>Demo</h2>
    </center>

    <br/><br/>

    <div class="container">
        <h2>Browser Log</h2>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#menu1">Friday</a></li>
            <li><a href="#menu2">Saturday</a></li>
            <li><a href="#menu3">Sunday</a></li>
            <li><a href="#menu4">Monday</a></li>
        </ul>

        <div class="tab-content">

            <div id="menu1" class="tab-pane fade in active">
                <h3>Friday</h3>
                <?php
                renderTable(1497043000000,1497067200000);
                ?>
            </div>
            <div id="menu2" class="tab-pane fade">
                <h3>Saturday</h3>
                <?php
                renderTable(1497067200000,1497153600000);
                ?>
            </div>
            <div id="menu3" class="tab-pane fade">
                <h3>Sunday</h3>
                <?php
                renderTable(1497153600000,1497240000000);

                ?>
            </div>
            <div id="menu4" class="tab-pane fade">
                <h3>Monday</h3>
                <?php
                renderTable(1497240000000,1497326400000);
                ?>
            </div>
        </div>
    </div>






</div>

<script>
    $(document).ready(function(){
        $(".nav-tabs a").click(function(){
            $(this).tab('show');
        });
    });
</script>


</body></html>
