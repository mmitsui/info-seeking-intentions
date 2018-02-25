$(document).ready(function(){

    
    var signedinYesID = '#signedin_yes';
    var signedinNoID = '#signedin_no';
    var firstNameID = '#first_name';
    var lastNameID = '#last_name';
    var loginErrorTextID = '#login_error_text';
    var usernameInputID = '#username';
    var passwordInputID = '#password';
    var homeDir = "http://coagmento.org/workintent";
    
    // URLs
    var registerUrl = homeDir+"/signup_intro.php";
    var checkLoggedInUrl = homeDir + "/getLoggedIn.php";
    var loginUrl = homeDir + "/login.php";
    var logoutUrl = homeDir + "/logout.php";
    var sendCredentialsUrl = homeDir + "/sendCredentials.php";
    var homeUrl = homeDir + "/instruments/getHome.php";
    var tutorialUrl = homeDir + "/getTutorial.php";
    var contactUrl = "mailto:mmitsui@scarletmail.rutgers.edu?Subject=Intent%20Study%20Inquiry";


    function goHome(){
        chrome.tabs.create({url:homeUrl}, function(tab){
        },
        );
    }

    function gotoTutorial(){
        chrome.tabs.create({url:tutorialUrl}, function(tab){
        },
        );
    }

    function sendContactEmail(){
        chrome.tabs.create({url:contactUrl}, function(tab){
            setTimeout(function(){
                chrome.tabs.remove(tab.id);
            },500);
        },
        );
    }

    function goToRegistration(){
        chrome.tabs.create({url:registerUrl}, function(tab){
        },
        );
    }


    function toggleLoggedIn(logged){
        chrome.extension.getBackgroundPage().loggedIn = logged;
    }

    function renderLoggedIn(loggedIn){
    	var red = [255,0,0,255];
    	var green = [34,139,34,255];
    	if(loggedIn){
    		chrome.browserAction.setBadgeText({text:' '});
			chrome.browserAction.setBadgeBackgroundColor({color:green});
    	}else{
    		chrome.browserAction.setBadgeText({text:' '});
			chrome.browserAction.setBadgeBackgroundColor({color:red});
    	}
    }

    function handleLoggedIn(msg){
        msg = JSON.parse(msg);
        if(msg.loggedin){
            toggleLoggedIn(msg.loggedin)
            renderLoggedIn(msg.loggedin);
            $(signedinYesID).show();
            $(signedinNoID).hide();
            $('html').height($(signedinYesID).height());

            $(firstNameID).text(msg.firstName);
            $(lastNameID).text(msg.lastName);
        }else{
            $(signedinYesID).show();
            $(signedinNoID).hide();
            $('html').height($(signedinYesID).height());

            $.ajax({
            type: "POST",
            url: loginUrl,
            data:{
                username_sha1:CryptoJS.SHA1($(usernameInputID).val()).toString(),
                password_sha1:CryptoJS.SHA1($(passwordInputID).val()).toString(),
                browser:"chrome",
                extensionID:chrome.runtime.id
            },
            success: function(msg){
                msg = JSON.parse(msg);
                if(msg.success){
                    $(firstNameID).text(msg.firstName);
                    $(lastNameID).text(msg.lastName);
                    $(signedinNoID).hide();
                    $(signedinYesID).show();
                    $('html').height($(signedinYesID).height());

                    $(loginErrorTextID).text('');
                    toggleLoggedIn(true);
                    renderLoggedIn(true);
                }else{
                    $(signedinNoID).show();
                    $(signedinYesID).hide();
                    $('html').height($(signedinNoID).height());

                    $(loginErrorTextID).text(msg.errortext);
                    toggleLoggedIn(false);
                    renderLoggedIn(false);
                }

            },
            error: function(msg){
                toggleLoggedIn(false);
                renderLoggedIn(false);
            },
            });
            
        }
    }

    $.ajax({
        type: "POST",
        url: checkLoggedInUrl,
        data : {
            extensionID:chrome.runtime.id
        },
        dataType: "text",
        success: handleLoggedIn,
        error: function(msg){
            
            $(signedinNoID).show();
            $(signedinYesID).hide();
            $('html').height($(signedinNoID).height());
            
            toggleLoggedIn(false);
            renderLoggedIn(false);
        }
    });


    $( "#login_button" ).click(function() {
        $.ajax({
            type: "POST",
            url: loginUrl,
            data:{
                username_sha1:CryptoJS.SHA1($(usernameInputID).val()).toString(),
                password_sha1:CryptoJS.SHA1($(passwordInputID).val()).toString(),
                browser:"chrome",
                extensionID:chrome.runtime.id
            },
            success: function(msg){
                msg = JSON.parse(msg);
                if(msg.success){
                    $(firstNameID).text(msg.firstName);
                    $(lastNameID).text(msg.lastName);
                    
                    $(signedinNoID).hide();
                    $(signedinYesID).show();
                    $('html').height($(signedinYesID).height());
                    $(loginErrorTextID).text('');
                    toggleLoggedIn(true);
                    renderLoggedIn(true);
                }else{
                	$(signedinNoID).show();
                    $(signedinYesID).hide();
                    $('html').height($(signedinNoID).height());

                	$(loginErrorTextID).text(msg.errortext);
                    toggleLoggedIn(false);
                	renderLoggedIn(false);
                }

            },
            error: function(msg){

                toggleLoggedIn(false);
                renderLoggedIn(false);
            },
        });


    });


    // TODO: set logged in background variable
    $( "#logout_button" ).click(function() {
        $.ajax({
            type: "POST",
            url: logoutUrl,
            data:{browser:"chrome"},
            success: function(msg){
                msg = JSON.parse(msg);
                if(msg.success){
                    $(usernameInputID).val('');
                    $(passwordInputID).val('');
                    $(signedinNoID).show();
                    $(signedinYesID).hide();
                    $('html').height($(signedinNoID).height());

                    toggleLoggedIn(false);
                    renderLoggedIn(false);
                }

            },
        });
    });


    $( "#credentials_button" ).click(function() {
        $.ajax({
            type: "POST",
            url: sendCredentialsUrl,
            data:{
                // username:$(usernameInputID).val()
                username_sha1:CryptoJS.SHA1($(usernameInputID).val()).toString()
            },
            success: function(msg){

                msg = JSON.parse(msg);
                if(msg.success){
                    $(loginErrorTextID).text('E-mail sent!  Please check your inbox.');
                }else{
                	$(loginErrorTextID).text(msg.errortext);
                }
            },
        });
    });


    $( "#contact_us_signedin_link,#contact_us_signedout_link" ).click(function() {
        sendContactEmail();
    });


    $( "#register_link" ).click(function() {
        goToRegistration();
    });


    $( "#homepage_button" ).click(function() {
        goHome();
    });

    $( "#tutorial_button" ).click(function() {
        gotoTutorial();
    });

});
