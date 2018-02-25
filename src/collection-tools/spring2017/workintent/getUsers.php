<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Base.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/pageQueryUtils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/services/utils/sessionTaskUtils.php");


?>



<html>
<head>
    <title>
        User Summary
    </title>

    <link rel="stylesheet" href="./study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./study_styles/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./lib/bootstrap_notify/bootstrap-notify.min.js"></script>




</head>




<body>

<?php

$cxn = Connection::getInstance();
$query = "SELECT * FROM recruits";
$result = $cxn->commit($query);

?>
<div class="container">
    <h1>All Users</h1>
</div>

<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4>Select a User</h4>
        </div>


        <div class="panel-body">
            <table  class="table table-bordered table-fixed">
                <thead>
                <tr>
                    <th >User ID</th>
                    <th >Experimenter</th>
                    <th >User Summary</th>
                </tr>
                </thead>
                <tbody id='history_table'>
                <?php

                while($line=mysql_fetch_array($result,MYSQL_ASSOC)){
                    $tcontent = "<td>".$line['userID']."</td>";
                    $tcontent .= "<td>".$line['experimenter']."</td>";
                    $tcontent .= "<td>"."<button class='btn btn-primary' onclick=\"window.location.href='./userDataEntry.php?userID=".$line['userID']."'\">Show User Summary</button>"."</td>";
                    echo "<tr>$tcontent</tr>";
                }
                ?>

                </tbody>
            </table>


        </div>
    </div>
</div>




</body>
</html>