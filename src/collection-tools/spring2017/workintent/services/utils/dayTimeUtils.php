<?php

function getStartEndTimestampsList($startTimeSeconds = null,$numiters = 10){
    $SECONDSPERDAY = 86400;
    if(is_null($startTimeSeconds)){
        $startTimeSeconds = strtotime('today midnight');
    }

    $startTimeIter = $startTimeSeconds;
    $timesArray = array();
    for( $i = 0; $i<$numiters; $i++ ) {
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

function actionButtons($selectedTime = null){

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


    $buttonsArray=array();




    $buttonsArray['home'] = "<a type=\"button\" class=\"btn btn-success\" href='$homeUrl'>Go to Home</a>";

    $buttonsArray['sessions'] = "<a type=\"button\" class=\"btn btn-success\" href='$markSessionsUrl'>Go to Mark Sessions</a>";

    $buttonsArray['tasks'] = "<a type=\"button\" class=\"btn btn-success\" href='$markTasksUrl'>Go to Mark Tasks</a>";

    $buttonsArray['intentions'] = "<a type=\"button\" class=\"btn btn-success\" href='$chooseIntentionsUrl'>Go to Mark Intentions</a>";

    return $buttonsArray;

}


?>
