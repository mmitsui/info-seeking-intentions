function getBrowser(){
    //Output: browser as string
    //Modified from http://jsfiddle.net/9zxvE/383/
    
    var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
    // Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
    var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
    var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
    // At least Safari 3+: "[object HTMLElementConstructor]"
    var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
    var isIE = /*@cc_on!@*/false || !!document.documentMode;   // At least IE6
    
    
    
    var output = ''
    var ct = 0
    if(isOpera){
        ct += 1
        output += ((ct > 1) ? " " : "");
        output += 'Opera'
    }
    if(isFirefox){
        ct += 1
        output += ((ct > 1) ? " " : "");
        output += 'Firefox'
    }
    if(isSafari){
        ct += 1
        output += ((ct > 1) ? " " : "");
        output += 'Safari'
    }
    if(isChrome){
        ct += 1
        output += ((ct > 1) ? " " : "");
        output += 'Chrome'
    }
    if(isIE){
        ct += 1
        output += ((ct > 1) ? " " : "");
        output += 'IE'
    }
    
    if(!isOpera && !isFirefox && !isChrome && !isIE && !isSafari){
        output = 'Other'
    }
    return output
    /*
    var output = 'Detecting browsers by ducktyping:<hr>';
    output += 'isFirefox: ' + isFirefox + '<br>';
    output += 'isChrome: ' + isChrome + '<br>';
    output += 'isSafari: ' + isSafari + '<br>';
    output += 'isOpera: ' + isOpera + '<br>';
    output += 'isIE: ' + isIE + '<br>';
    document.body.innerHTML = output;*/
    
}

function isFirefox(){
    return getBrowser() == 'Firefox'
}