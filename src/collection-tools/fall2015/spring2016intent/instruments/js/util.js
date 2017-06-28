function isItemSelected(items) 
{
	for (i=0;i<items.length;i++) 
	{
		if (items[i].checked) 
			return true;
	}
	return false;
}

/*
function wait() 
{
	var date = new Date();
	var curDate = null;

	do { curDate = new Date(); } 
		while(curDate-date < 4000);
} */

function setLocalTime(form)
{
	 var currentTime = new Date();
     var month = currentTime.getMonth() + 1;
     var day = currentTime.getDate();
     var year = currentTime.getFullYear();
     var localDateVal = year + "/" + month + "/" + day;
     var hours = currentTime.getHours();
     var minutes = currentTime.getMinutes();
     var seconds = currentTime.getSeconds();
     var localTimeVal = hours + ":" + minutes + ":" + seconds;
     var localTimestampVal = currentTime.getTime();
     
     form.localTime.value = localTimeVal;
     form.localDate.value = localDateVal;
     form.localTimestamp.value = localTimestampVal;

}