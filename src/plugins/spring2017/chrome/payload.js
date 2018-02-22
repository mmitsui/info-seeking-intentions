var saveKeystrokeUrl = 'http://coagmento.org/workintent/services/saveKeystrokeData.php';
var saveClickUrl = 'http://coagmento.org/workintent/services/saveClickData.php';
var saveScrollUrl = 'http://coagmento.org/workintent/services/saveScrollData.php';
var saveCopyUrl = 'http://coagmento.org/workintent/services/saveCopyData.php';
var savePasteUrl = 'http://coagmento.org/workintent/services/savePasteData.php';

var keystroke_buffer = {};
var modifier_buffer = {};
var click_buffer = {};
var scroll_buffer = {};
var copy_buffer = {};
var paste_buffer = {};
var maxId = 0;

function clearTimers(){
    maxId = setTimeout(function(){}, 0);
    for(var i=0; i < maxId; i+=1) { 
        clearTimeout(i);
    }
    maxId = 0;
}

clearTimers();

// function getLocalDate(timestamp){
//     var currentTime = new Date(timestamp);
//     var month = currentTime.getMonth() + 1;
//     var day = currentTime.getDate();
//     var year = currentTime.getFullYear();
//     var localDate = year + "%2F" + month + "%2F" + day;
//     var hours = currentTime.getHours();
//     var minutes = currentTime.getMinutes();
//     var seconds = currentTime.getSeconds();
//     var localTime = hours + "%3A" + minutes + "%3A" + seconds;
//     var localTimestamp = currentTime.getTime();
//     return {'localDate':localDate,'localTime':localTime,'':localTimestamp}
// }

function defaultCallback(responseText){
    console.log(responseText);
}

function saveKeys(keystroke_buffer,modifier_buffer){

    chrome.runtime.sendMessage({
        method: 'POST',
        action: 'xhttp',
        url: saveKeystrokeUrl,
        data: {'keys':keystroke_buffer,'modifiers':modifier_buffer}
    }, defaultCallback); 

    
}


function saveClicks(click_buffer){
    chrome.runtime.sendMessage({
        method: 'POST',
        action: 'xhttp',
        url: saveClickUrl,
        data: {'clicks':click_buffer}
    }, defaultCallback); 
}


function saveScrolls(scroll_buffer){
    // alert('save scrolls!');
    chrome.runtime.sendMessage({
        method: 'POST',
        action: 'xhttp',
        url: saveScrollUrl,
        data: {'scrolls':scroll_buffer}
    }, defaultCallback); 
}


function saveCopy(copy_buffer){
    // alert('save scrolls!');
    chrome.runtime.sendMessage({
        method: 'POST',
        action: 'xhttp',
        url: saveCopyUrl,
        data: {'copies':copy_buffer}
    }, defaultCallback); 
}


function savePaste(paste_buffer){
    // alert('save scrolls!');
    chrome.runtime.sendMessage({
        method: 'POST',
        action: 'xhttp',
        url: savePasteUrl,
        data: {'pastes':paste_buffer}
    }, defaultCallback); 
}



function bufferClear(){
    if(Object.keys(keystroke_buffer).length > 0){
        saveKeys(keystroke_buffer,modifier_buffer);
        keystroke_buffer = {};
        modifier_buffer = {};
        clearTimers();
    }

    if(Object.keys(click_buffer).length > 0){
        saveClicks(click_buffer);
        click_buffer = {};
        clearTimers();
    }


    if(Object.keys(scroll_buffer).length > 0){
        saveScrolls(scroll_buffer);
        scroll_buffer = {};
        clearTimers();
    }


    if(Object.keys(copy_buffer).length > 0){
        saveCopy(copy_buffer);
        copy_buffer = {};
        clearTimers();
    }


    if(Object.keys(paste_buffer).length > 0){
        savePaste(paste_buffer);
        paste_buffer = {};
        clearTimers();
    }

}

function setBufferClear(){
    // alert("maxId"+maxId);
    if(maxId >0){
        return;
    }else{
        maxId = setTimeout(bufferClear, 5000);
        // alert("timeout set");
    }
}
/* Keylib */
// Alphanumeric
document.addEventListener('keypress', function (e) {
    setBufferClear();
    var time = new Date().getTime();
    e = e || window.event;



    var key = e.which;   
    var modifier = "";
        
        if(event.altKey){
            if(modifier.length > 0){
                modifier = modifier + "-"
            }
            modifier = modifier + "alt"
        }
        if(event.shiftKey){
            if(modifier.length > 0){
                modifier = modifier + "-"
            }
            modifier = modifier + "shift"
        }
        if(event.ctrlKey){
            if(modifier.length > 0){
                modifier = modifier + "-"
            }
            modifier = modifier + "ctrl"
        }
        if(event.metaKey){
            if(modifier.length > 0){
                modifier = modifier + "-"
            }
            modifier = modifier + "meta"
        }

    if(time in keystroke_buffer){
        keystroke_buffer[time].push(key);
    }else{
        keystroke_buffer[time] = [key];
    } 


    if(time in modifier_buffer){
        modifier_buffer[time].push(modifier);
    }else{
        modifier_buffer[time] = [modifier];
    } 
});


var lastsnippet = '';

document.addEventListener('copy', function (e) {
    setBufferClear();

    // chrome.extension.getBackgroundPage().console.log('copy!');

    var snippet = window.getSelection().toString();
    lastsnippet = {'snippet':snippet,'title':document.title,'url':window.location.href};
    // alert(JSON.stringify(lastsnippet));

    var time = new Date().getTime();
    copy_buffer[time] = lastsnippet;
});



document.addEventListener('paste', function (e) {
    // alert('paste!');
    // alert(lastsnippet);
    var time = new Date().getTime();
    paste_buffer[time] = lastsnippet;
});








// gBrowser.addEventListener("click", function(e){saveClick(e,'click');}, false);

//             gBrowser.addEventListener("dblclick", function(e){saveClick(e,'dblclick');}, false);




function saveClick(event,type)
{

    setBufferClear();
    var time = new Date().getTime();
    click_buffer[time] = {'type':type,
        'clientX':event.clientX,
        'clientY':event.clientY,
        'pageX':event.pageX,
        'pageY':event.pageY,
        'screenX':window.screenX,
        'screenY':window.screenY,
        'scrollX':window.scrollX,
        'scrollY':window.scrollY,
    }
}




function scrollStart(event){
    


    setBufferClear();
    var time = new Date().getTime();
    scroll_buffer[time] = {
        'screenX':window.screenX,
        'screenY':window.screenY,
        'scrollX':window.scrollX,
        'scrollY':window.scrollY,
    }
}





document.addEventListener('click', function (e){saveClick(e,'click');}, false);
document.addEventListener('dblclick', function (e){saveClick(e,'dblclick');}, false);
document.addEventListener('scroll', function (e){scrollStart(e);}, false);



// gBrowser.addEventListener("keypress", keystrokeSave, false);

            
        
//             setInterval(function(){keyFlush();},10000);
            
//             gBrowser.addEventListener("scroll", function(e){ scrollStart(e);}, false);



//Added 08/2014
             // gBrowser.addEventListener("copy", copyData, false);

             //Added 1/2015
             // gBrowser.addEventListener("paste", pasteData, false);





// function scrollStart(event){
    
//     checkConnectivity();
//     if (loggedIn && allowBrowsingFlag)
//     {
        
//         var scrollX = event.scrollX;
//         var scrollY = event.scrollY;
//         var clientX = event.clientX;
//         var clientY = event.clientY;
//         var pageX = event.pageX;
//         var pageY = event.pageY;
//         var screenX = event.screenX;
//         var screenY = event.screenY;
        
        
//         var url = gBrowser.selectedBrowser.currentURI.spec;
//         url = encodeURIComponent(url);
        

//         var xmlHttpTimeoutScrollData;
//         var xmlHttpConnectionScrollData = new XMLHttpRequest();
        
        
//         //Capturing local time
//         var currentTime = new Date();
//         var month = currentTime.getMonth() + 1;
//         var day = currentTime.getDate();
//         var year = currentTime.getFullYear();
//         var localDate = year + "%2F" + month + "%2F" + day;
//         var hours = currentTime.getHours();
//         var minutes = currentTime.getMinutes();
//         var seconds = currentTime.getSeconds();
//         var localTime = hours + "%3A" + minutes + "%3A" + seconds;
//         var localTimestamp = currentTime.getTime();
        
        
        
//         //Saving page
//         xmlHttpConnectionScrollData.open('GET', globalUrl+'services/saveScrollData.php?'+'URL='+url+'&type=start'+'&clientX='+clientX+'&clientY='+clientY+'&pageX='+pageX+'&pageY='+pageY+'&screenX='+screenX+'&screenY='+screenY+'&scrollX='+scrollX+'&scrollY='+scrollY+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate, true);
//         action = "";
        
        
//         xmlHttpConnectionScrollData.onreadystatechange=function(){
//             if (xmlHttpConnectionScrollData.readyState == 4 && xmlHttpConnectionScrollData.status == 200) {
//                 clearTimeout(xmlHttpTimeoutScrollData);
//             }
//         };
        
//         xmlHttpConnectionScrollData.send(null);
//         xmlHttpTimeoutScrollData = setTimeout(function(){
//                                              xmlHttpConnectionScrollData.abort();
//                                              clearTimeout(xmlHttpTimeoutScrollData);
//                                              }
//                                              ,3000);
        
// //        document.getElementById('msgs').textContent = " Scroll Start Saved!";
// //        setTimeout('cleanAlert()', 3000);
        
        
        
//         if(scrollTimer !== null) {
//             clearTimeout(scrollTimer);
//         }
//         scrollTimer = setTimeout(function() {
//                                  scrollStop();
//                            }, 150);
//     }
    
// }

// function scrollStop(event){
//     checkConnectivity();
//     if (loggedIn)
//     {
        
//         var scrollX = window.scrollX;
//         var scrollY = window.scrollY;
//         var clientX = window.clientX;
//         var clientY = window.clientY;
//         var pageX = window.pageXOffset;
//         var pageY = window.pageYOffset;
//         var screenX = window.screenX;
//         var screenY = window.screenY;
        
//         var url = gBrowser.selectedBrowser.currentURI.spec;
//         url = encodeURIComponent(url);
        
        
//         var xmlHttpTimeoutScrollData;
//         var xmlHttpConnectionScrollData = new XMLHttpRequest();
        
        
//         //Capturing local time
//         var currentTime = new Date();
//         var month = currentTime.getMonth() + 1;
//         var day = currentTime.getDate();
//         var year = currentTime.getFullYear();
//         var localDate = year + "%2F" + month + "%2F" + day;
//         var hours = currentTime.getHours();
//         var minutes = currentTime.getMinutes();
//         var seconds = currentTime.getSeconds();
//         var localTime = hours + "%3A" + minutes + "%3A" + seconds;
//         var localTimestamp = currentTime.getTime();
        
        
        
//         //Saving page
//         xmlHttpConnectionScrollData.open('GET', globalUrl+'services/saveScrollData.php?'+'URL='+url+'&type=stop'+'&clientX='+clientX+'&clientY='+clientY+'&pageX='+pageX+'&pageY='+pageY+'&screenX='+screenX+'&screenY='+screenY+'&scrollX='+scrollX+'&scrollY='+scrollY+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate, true);
//             action = "";
        
        
//             xmlHttpConnectionScrollData.onreadystatechange=function(){
//                 if (xmlHttpConnectionScrollData.readyState == 4 && xmlHttpConnectionScrollData.status == 200) {
//                     clearTimeout(xmlHttpTimeoutScrollData);
//                 }
//             };
        
//             xmlHttpConnectionScrollData.send(null);
//             xmlHttpTimeoutScrollData = setTimeout(function(){
//                                                  xmlHttpConnectionScrollData.abort();
//                                                  clearTimeout(xmlHttpTimeoutScrollData);
//                                                  }
//                                                  ,3000);
        
// //        document.getElementById('msgs').textContent = " Scroll Stop Saved!";
// //        setTimeout('cleanAlert()', 3000);
//     }
    
// }