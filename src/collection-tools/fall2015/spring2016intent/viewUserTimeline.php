<?php

    session_start();
    require_once('core/Connection.class.php');
    require_once('core/Base.class.php');
    require_once('core/Util.class.php');


    date_default_timezone_set('America/New_York');


    $participantID = $_GET['participantID'];
    $questionID = $_GET['questionID'];
    $taskNum = $_GET['taskNum'];
    $cxn = Connection::getInstance();
    $results = $cxn->commit("SELECT * FROM users WHERE participantID='$participantID'");
    $userID = -1;
    while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
        $userID = $line['userID'];
        break;
    }
    $results = $cxn->commit("SELECT * FROM recruits WHERE userID=$userID");
    $line = mysql_fetch_array($results,MYSQL_ASSOC);
    $firstname = $line['firstName'];
    $lastname = $line['lastName'];


    ?>


    <html>
    <head>
    <title>IIR: Timeline</title>
        <link rel="stylesheet" type="text/css"
              href="https://fonts.googleapis.com/css?family=Work+Sans|Signika|Futura">
    <link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">
    <link rel="stylesheet" href="study_styles/custom/text.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="lib/vis/dist/vis.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    </head>
    <noscript>
    <style type="text/css">
        .pagecontainer {display:none;}
    </style>
    <div class="noscriptmsg">
        You don't have Javascript enabled.  You must enable it in your browser to proceed with the task.
    </div>
    </noscript>

    <script src="lib/jquery-2.1.3.min.js"></script>
    <script src="lib/vis/dist/vis.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/handlebars.js/2.0.0-alpha.4/handlebars.min.js"></script>

    <script id="unsave_template" type="text/x-handlebars-template">
        <div class="item-header">Unsave</div>
        <div class='toggle-attributes' onclick="toggleAttributes(this)">Show</div><br/>
        <div name="attributes-div" style="display:none;">
            <p>Title: {{title}}</p>
            <p>URL: {{url}}</p>
            <p>Unsave Reason: {{unsave_reason}}</p>
        </div>

    </script>

    <script id="save_template" type="text/x-handlebars-template">
        <div class="item-header">Save</div>
        <div class='toggle-attributes' onclick="toggleAttributes(this)">Show</div><br/>
        <div name="attributes-div" style="display:none;">
            <p>Title: {{title}}</p>
            <p>URL: {{url}}</p>
            <p>Usefulness: {{usefulness}}</p>
            <p>Confidence: {{confidence}}</p>
        </div>
    </script>

    <script id="page_template" type="text/x-handlebars-template">
        <div class="item-header">Page</div>
        <div class='toggle-attributes' onclick="toggleAttributes(this)">Show</div><br/>
        <div name="attributes-div" style="display:none;">
            <p>Title: {{title}}</p>
            <p>URL: {{url}}</p>
            <p>Source: {{source}}</p>
        </div>

    </script>

    <script id="query_template" type="text/x-handlebars-template">
        <div class="item-header">Query</div>
        <div class='toggle-attributes' onclick="toggleAttributes(this)">Show</div><br/>
        <div name="attributes-div" style="display:none;">
            <p>Query: {{query}}</p>
            <p>Query Length: {{length}}</p>
            <p>Reformulation Type: {{reformulation}}</p>
            <div name="hidden-content-serps"></div>
            <p class='toggle-attributes' onclick="toggleAttributes(this)">SERPs + Intents</p><br/>
            <div name="attributes-div" style="display:none;">
                <p>SERPS:</p>
                <p>Intents: {{intents}}</p>
            </div>
        </div>
        </div>
        <div name="hidden-content">
    </script>

    <script id="click_template" type="text/x-handlebars-template">
        <div class="item-header">Click</div>
        <div class='toggle-attributes' onclick="toggleAttributes(this)">Show</div>
        <div name="attributes-div" style="display:none;">
            <p>Client X/Y:{{clientX}}/{{clientY}}</p>
            <p>Screen X/Y:{{screenX}}/{{screenY}}</p>
            <p>Page X/Y:{{pageX}}/{{pageY}}</p>
        </div>
    </script>


    <script id="scroll_template" type="text/x-handlebars-template">
        <div class="item-header">Scroll</div>
        <div class='toggle-attributes' onclick="toggleAttributes(this)">Show</div>
        <div name="attributes-div" style="display:none;">
            <p>Client X/Y:{{clientX}}/{{clientY}}</p>
            <p>Screen X/Y:{{screenX}}/{{screenY}}</p>
            <p>Page X/Y:{{pageX}}/{{pageY}}</p>
            <p>Scroll X/Y:{{scrollX}}/{{scrollY}}</p>
        </div>
    </script>



    <style type="text/css">
        body, input {
            font: 12pt verdana;
        }

        .toggle-attributes{
            cursor:pointer;
            color:blue;
            text-decoration:underline;
            float:left;
            font-family: "Work Sans", Times, serif;
        }

        .item-header{
            text-decoration:underline;
            padding-bottom:10px;
            padding-left:10px;
            padding-right:10px;
            font-size:22px;
            font-family: 'Futura', sans-serif;
            /*font-family:'Enriqueta', arial, serif;*/

        }
        /* custom styles for individual items, load this after vis.css */



        .vis-item{
            box-shadow: 5px 5px 20px rgba(128,128,128, 0.5);
        }
        .vis-item.green {
            background-color: #e3ffb8;
            border-color: green;
            border-radius:5px;
        }

        /* create a custom sized dot at the bottom of the red item */
        .vis-item.red {
            background-color: red;
            border-color: darkred;
            border-radius:5px;
            color: black;

        }

        .vis-item.vis-box.red {
            border-radius:5px;
        }

        .vis-item.orange {
            background-color: #ffed79;
            border-color: orange;
            border-radius:5px;
        }
        .vis-item.vis-selected.orange {
            /* custom colors for selected orange items */
            background-color: orange;
            border-color: orangered;
            border-radius:5px;
        }

        .vis-item.magenta {
            background-color: #cfb9d9;
            border-color: purple;
            color: black;
            border-radius:5px;
        }

        /* our custom classes overrule the styles for selected events,
           so lets define a new style for the selected events */
        .vis-item.vis-selected {
            background-color: white;
            border-color: black;
            color: black;
            box-shadow: 5px 5px 20px rgba(128,128,128, 0.9);
        }
    </style>




    <script type="text/javascript">

        function toggleAttributes(object){
            var hidden_div = $(object).siblings("div[name='attributes-div']").first();
            if($(hidden_div).is(":visible")){
                $(object).text('Show');
            }else{
                $(object).text('Hide');
            }

            $(hidden_div).toggle();

        }

        $(document).ready(function(){
            var save_source = document.getElementById('save_template').innerHTML;
            var unsave_source = document.getElementById('unsave_template').innerHTML;
            var page_source = document.getElementById('page_template').innerHTML;
            var query_source = document.getElementById('query_template').innerHTML;
            var click_source = document.getElementById('click_template').innerHTML;
            var scroll_source = document.getElementById('scroll_template').innerHTML;

            var templates = {
                save_template: Handlebars.compile(save_source),
                unsave_template: Handlebars.compile(unsave_source),
                page_template: Handlebars.compile(page_source),
                query_template: Handlebars.compile(query_source),
                click_template: Handlebars.compile(click_source),
                scroll_template: Handlebars.compile(scroll_source)
            };

            // DOM element where the Timeline will be attached
            var container = document.getElementById('visualization');

            // Create a DataSet (allows two way data-binding)
            var items = new vis.DataSet(
                <?php
                    $checked_string = "";
                    $checked_array = array();

                    foreach($_GET as $key=>$value){
                        if(in_array($key,array('saved','unsaved','clicks','scrolling','pages','queries'))){
                            array_push($checked_array,"'$key'");
                        }

                    }


                    if (count($checked_array)>0){
                        $checked_string = implode(",",$checked_array);
                        $results = $cxn->commit("SELECT * FROM timeline_display_data WHERE userID=$userID AND questionID=$questionID AND data_type IN ($checked_string)");
                        $results_array = array();
                        while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
                            $timelineID = $line['timelineID'];
                            $data_json = $line['data_json'];
                            $data_type = $line['data_type'];
                            $date = $line['date'];
                            $time = $line['time'];
                            $timestamp = "$date $time";

                            $row_array = array('id'=>$timelineID,'content'=>$data_json,'start'=>$timestamp);
                            $data_json = json_decode($data_json);
//                            print_r($data_json);

                            if($data_type=='saved'){
                                $usefulness = $data_json->usefulness;
                                $confidence = $data_json->confidence;
                                $row_array['usefulness'] = $usefulness;
                                $row_array['confidence'] = $confidence;
                                $row_array['url'] = $data_json->url;
                                $row_array['title'] = $data_json->title;
                                $row_array['template']='save_template';
                            }else if ($data_type=='unsaved'){
                                $unsave_reason = $data_json->unsave_reason;
                                $row_array['unsave_reason'] = $unsave_reason;
                                $row_array['title'] = $data_json->title;
                                $row_array['url'] = $data_json->url;
                                $row_array['className'] = 'orange';
                                $row_array['template']='unsave_template';
                            }else if ($data_type=='pages'){
                                $url = $data_json->url;
                                $title = $data_json->title;
                                if(strlen($title)>10){
//                                    $title = substr($title,0,10)."...";
                                }
                                $row_array['title'] = $title;
                                $row_array['url'] = $data_json->url;
                                $row_array['source'] = $data_json->source;
                                $row_array['className'] = 'magenta';
                                $row_array['template']='page_template';
                            }else if ($data_type=='queries'){
                                $query = $data_json->query;
                                $row_array['query'] = $query;
                                $row_array['length'] = $data_json->length;
                                $row_array['intents'] = $data_json->intents;
                                $row_array['reformulation'] = $data_json->reformulation;
                                $row_array['className'] = 'green';
                                $row_array['template']='query_template';
                            }else if ($data_type=='clicks'){
                                $query = $data_json->query;
                                $row_array['className'] = 'red';
                                $row_array['template']='click_template';
                                $row_array['clientX'] = $data_json->clientX;
                                $row_array['clientY'] = $data_json->clientY;
                                $row_array['pageX'] = $data_json->pageX;
                                $row_array['pageY'] = $data_json->pageY;
                                $row_array['screenX'] = $data_json->screenX;
                                $row_array['screenY'] = $data_json->screenY;
                            }else if ($data_type=='scrolling'){
//                                $row_array['className'] = 'red';
                                $row_array['template']='scroll_template';
                                $row_array['clientX'] = $data_json->clientX;
                                $row_array['clientY'] = $data_json->clientY;
                                $row_array['pageX'] = $data_json->pageX;
                                $row_array['pageY'] = $data_json->pageY;
                                $row_array['screenX'] = $data_json->screenX;
                                $row_array['screenY'] = $data_json->screenY;
                                $row_array['scrollX'] = $data_json->scrollX;
                                $row_array['scrollY'] = $data_json->scrollY;

                            }


                            array_push($results_array,$row_array);
                        }
                        echo json_encode($results_array,128);
                    }


                    ?>

            );

            // Configuration for the Timeline
            var options = {
                template: function (item) {
                    var template = templates[item.template];  // choose the right template
                    return template(item);                    // execute the template
                },
                clickToUse: true,
            };

            // Create a Timeline
            var timeline = new vis.Timeline(container, items, options);

//            function onSelect (properties) {
//                alert('selected items: ' + properties.nodes);
//            }

            // add event listener
//            timeline.on('select', onSelect);
        });


    </script>



    <body class="body">
    <div class="panel panel-default" style="width:95%;  margin:auto">
    <div class="panel-body">


    <div id="login_div" style="display:block;">


<?php

    echo "<center><h2>Timeline: $firstname $lastname</h2><br/><br/><button class='btn' onclick=\"location.href='selectUserTimeline.php'; return false;\">Return To Users Page</button></center><hr/><br/>\n";

//    if($submitted != 0){
//        echo "<div class=\"alert alert-success\">\n";
//        echo "<strong>Success!</strong> $message\n";
//        echo "</div>\n";
//
//    }

?>

        <div class='panel panel-default'>
            <div class='panel-body'>
                <center><h2>Options</h2></center><hr/><br/>
<!--
Options:
1) Dwell Time
2) Page (Queries + pages)
3) Bookmark action (usefulness, confidence)
4) Click
5) Scroll
Misc Notes:
Queries get intentions, query length, reformulation type
Pages generally get page type

-->

                <form action="viewUserTimeline.php" method="get">
                    <input type="hidden" name="participantID" value='<?php echo $participantID;?>'>
                    <input type="hidden" name="userID" value=<?php echo $userID;?>>
                    <input type="hidden" name="questionID" value=<?php echo $questionID;?>>
                    <input type="hidden" name="taskNum" value=<?php echo $taskNum;?>>

                    <div class='row'>
                        <div class="col-md-4">
                            <div class="checkbox-inline">
                                <?php
                                    if(isset($_GET['pages'])){
                                        echo "<label><input type=\"checkbox\" id=\"pages\" name=\"pages\" value=\"\" checked>Pages</label>";
                                    }else{
                                        echo "<label><input type=\"checkbox\" id=\"pages\" name=\"pages\" value=\"\">Pages</label>";
                                    }
                                ?>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox-inline">
                                <?php
                                    if(isset($_GET['queries'])){
                                        echo "<label><input type=\"checkbox\" id=\"queries\" name=\"queries\" value=\"\" checked>Queries</label>";
                                    }else{
                                        echo "<label><input type=\"checkbox\" id=\"queries\" name=\"queries\" value=\"\">Queries</label>";
                                    }
                                ?>

                            </div>
                        </div>



                    </div>
                    <div class='row'>
                        <div class="col-md-4">
                            <div class="checkbox-inline">
                                <?php
                                if(isset($_GET['clicks'])){
                                    echo "<label><input type=\"checkbox\" id=\"clicks\" name=\"clicks\" value=\"\" checked>Clicks</label>";
                                }else{
                                    echo "<label><input type=\"checkbox\" id=\"clicks\" name=\"clicks\" value=\"\">Clicks</label>";
                                }
                                ?>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox-inline">
                                <?php
                                if(isset($_GET['scrolling'])){
                                    echo "<label><input type=\"checkbox\" id=\"scrolling\" name=\"scrolling\" value=\"\" checked>Scrolling</label>";
                                }else{
                                    echo "<label><input type=\"checkbox\" id=\"scrolling\" name=\"scrolling\" value=\"\">Scrolling</label>";
                                }
                                ?>

                            </div>
                        </div>




                    </div>
                    <div class='row'>
                        <div class="col-md-4">
                            <div class="checkbox-inline">
                                <?php
                                if(isset($_GET['saved'])){
                                    echo "<label><input type=\"checkbox\" id=\"saved\" name=\"saved\" value=\"\" checked>Saved Pages</label>";
                                }else{
                                    echo "<label><input type=\"checkbox\" id=\"saved\" name=\"saved\" value=\"\">Saved Pages</label>";
                                }
                                ?>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox-inline">
                                <?php
                                if(isset($_GET['unsaved'])){
                                    echo "<label><input type=\"checkbox\" id=\"unsaved\" name=\"unsaved\" value=\"\" checked>Unsaved Pages</label>";
                                }else{
                                    echo "<label><input type=\"checkbox\" id=\"unsaved\" name=\"unsaved\" value=\"\">Unsaved Pages</label>";
                                }
                                ?>

                            </div>
                        </div>


                    </div>
                    <input type="submit" value="Submit">
                </form>



            </div>
        </div>

        <center><h2>Timeline</h2></center><hr/><br/>

        <div id="visualization"></div>

    </div>
    </div>
    </div>
    </body>
    </html>
