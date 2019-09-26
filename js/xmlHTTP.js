function getHttpRequest(URL) {
	var xmlhttp = null;
	
	if(window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.open('GET', URL, false);
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState==4 && xmlhttp.status == 200 && xmlhttp.statusText=='OK') {
			responseText = xmlhttp.responseText;
		}
	}
	
	xmlhttp.send(null);
	
	return responseText = xmlhttp.responseText;
 }