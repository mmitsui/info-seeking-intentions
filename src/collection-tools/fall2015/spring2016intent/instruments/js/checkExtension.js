var _img;


function _cleanup(){
	_img.onload = null;
	_img.onerror = null;
	_img.onabort = null;
	_img = null;
}

/*
*
* For implementers: this is the only function you'll need to call
* Supply your own success function and failure function as arguments
* (i.e. what should be done on success and failure)
*
*/
function checkExtension(succfun,failfun){
	//load image
	//onload, perform succfun
	//onerror or abort, perform failfun
	var img = new Image();
	_img = img;
	img.onerror = function() {failfun(); _cleanup();};
	img.onabort = function() {failfun(); _cleanup();};
	img.onload = function() {succfun(); _cleanup();};
	img.src = "chrome://coagmento/skin/gripper.png";
}