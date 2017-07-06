<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/workintent/core/Connection.class.php");
function getFirstActivityStartTimestamp($userID){
    $cxn = Connection::getInstance();

    $results = $cxn->commit("SELECT * FROM pages WHERE `userID`=$userID ORDER BY `localTimestamp` ASC LIMIT 10");
    if(mysql_num_rows($results)==0){
        return -1;
    }else{
        $line = mysql_fetch_array($results,MYSQL_ASSOC);
        $firstTime = $line['localTimestamp'];
        $firstDayMidnight = $firstTime -($firstTime %86400000);
        return $firstDayMidnight/1000;
    }
}

function getStartEndTimestampsList($userID,$startTimeSeconds = null,$numiters = 5){
    $firstDayMidnightTimestamp = getFirstActivityStartTimestamp($userID);
    $SECONDSPERDAY = 86400;
    if(is_null($startTimeSeconds)){
        $startTimeSeconds = strtotime('today midnight');
    }

    $startTimeIter = $startTimeSeconds;
    $timesArray = array();
    for( $i = 0; $i<$numiters; $i++ ) {
        if($startTimeIter < $firstDayMidnightTimestamp and $i>0){
            break;
        }
        $timesArray[] = array('startTime'=>$startTimeIter,'endTime'=>$startTimeIter+$SECONDSPERDAY);
        $startTimeIter -=$SECONDSPERDAY;
    }

    return $timesArray;

}

function getStartEndTimestamp($startTimeSeconds = null){
    $SECONDSPERDAY = 86400;
    if(is_null($startTimeSeconds)){
        $startTimeSeconds = strtotime('today midnight');
    }

    return array('startTime'=>$startTimeSeconds,'endTime'=>$startTimeSeconds+$SECONDSPERDAY);
}

function dayButtonStrings($startEndTimeArray, $url, $selectedTime = null){
    $todayMignightSeconds = strtotime('today midnight');
    $buttonsArray = array();
    foreach($startEndTimeArray as $startEndElement){
        $startTimeSeconds = $startEndElement['startTime'];
        $endTimeSeconds = $startEndElement['endTime'];
        $href = $url."?startTime=$startTimeSeconds&endTime=$endTimeSeconds";
        $date = getdate($startTimeSeconds);
        $weekday = substr($date['weekday'],0,3);
        $month = $date['month'];
        $mday = $date['mday'];
        $dateString = "$weekday, $month $mday";
        $activatedString = "";
        if($startTimeSeconds==$selectedTime){
            $activatedString=" active";
        }

        if($startTimeSeconds == $todayMignightSeconds){
            $dateString = "Today";
        }

        $buttonString = "<a type=\"button\" class=\"btn btn-default$activatedString\" href=\"$href\">$dateString</a>";
        array_unshift($buttonsArray,$buttonString);
    }

    return $buttonsArray;
}

function actionUrls($selectedTime = null){

    $SECONDSPERDAY = 86400;
    $todayMignightSeconds = strtotime('today midnight');
    if(is_null($selectedTime)){
        $selectedTime = $todayMignightSeconds;
    }

    $endSelectedTime = $selectedTime+$SECONDSPERDAY;

    $homeUrl = 'http://coagmento.org/workintent/instruments/getHome.php'."?startTime=$selectedTime&endTime=$endSelectedTime";
    $markSessionsUrl = 'http://coagmento.org/workintent/instruments/markSessions.php'."?startTime=$selectedTime&endTime=$endSelectedTime";
    $markTasksUrl = 'http://coagmento.org/workintent/instruments/markTasks.php'."?startTime=$selectedTime&endTime=$endSelectedTime";
    $chooseIntentionsUrl = 'http://coagmento.org/workintent/instruments/chooseIntentions.php'."?startTime=$selectedTime&endTime=$endSelectedTime";
//    $homeUrl = 'http://coagmento.org/workintent/instruments/getHome.php'."?startTime=$selectedTime&endTime=$endSelectedTime";


    $urlsArray =array();




//    $buttonsArray['home'] = "<a type=\"button\" class=\"btn btn-success\" href='$homeUrl'>Go to Home</a>";
//
//    $buttonsArray['sessions'] = "<a type=\"button\" class=\"btn btn-success\" href='$markSessionsUrl'>Go to Mark Sessions</a>";
//
//    $buttonsArray['tasks'] = "<a type=\"button\" class=\"btn btn-success\" href='$markTasksUrl'>Go to Mark Tasks</a>";
//
//    $buttonsArray['intentions'] = "<a type=\"button\" class=\"btn btn-success\" href='$chooseIntentionsUrl'>Go to Mark Intentions</a>";

    $urlsArray['home'] = $homeUrl;

    $urlsArray['sessions'] = $markSessionsUrl;

    $urlsArray['tasks'] = $markTasksUrl;

    $urlsArray['intentions'] = $chooseIntentionsUrl;

    return $urlsArray;

}


?>
