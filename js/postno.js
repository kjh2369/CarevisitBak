var ClsPostNo = function(){
	this.Init();
}

ClsPostNo.prototype.Init = function(){
	this.host = 'http://openapi.epost.go.kr';
	this.path = '/postal/retrieveNewAdressAreaCdSearchAllService/retrieveNewAdressAreaCdSearchAllService/getNewAddressListAreaCdSearchAll';
	this.ServiceKey = 'BZoHapiuJMXWTy4xVXM7q3x8Z9QiGSLJ6SMGGLzj8mdHQUT7c5KB1EHiiLYbDSwwANMQ5WJqNbeKUVnEYtbFCg%3D%3D';
	this.countPerPage = 20;
	this.currentPage = 1;
	this.srchwrd = '';
}

ClsPostNo.prototype.GetPostNo = function(){
	var xmlhttp = null;
	var url = this.host+this.path+'?ServiceKey='+this.ServiceKey+'&countPerPage='+this.countPerPage+'&currentPage='+this.currentPage+'&srchwrd='+this.srchwrd;

	if(window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		txmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.open('GET', url, false);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status == 200 && xmlhttp.statusText=='OK') {
			responseText = xmlhttp.responseText;
		}
	}
	xmlhttp.send(null);

	return responseText = xmlhttp.responseText;
}

ClsPostNo.prototype.Xml2Obj = function(data){
	if (window.DOMParser){
		tmp = new DOMParser();
		xml = tmp.parseFromString(data, "text/xml");
	}else{
		xml = new ActiveXObject("Microsoft.XMLDOM");
		xml.async = "false";
		xml.loadXML(data);
	}

	$cmmMsgHeader = $(xml).find('cmmMsgHeader');

	var totalCount = $cmmMsgHeader.find('totalCount').text(); //전체 검색수
	var countPerPage = $cmmMsgHeader.find('countPerPage').text(); //마지막페이지
	var totalPage = $cmmMsgHeader.find('totalPage').text(); //전체 페이지수
	var currentPage = $cmmMsgHeader.find('currentPage').text(); //현재페이지

	$row =  $(xml).find('newAddressListAreaCdSearchAll');
	
	var str = '';

	str += 'totCnt='+totalCount;
	str += '&totPag='+totalPage;
	str += '&curPag='+currentPage;

	$row.each(function(){
		str += '?zipNo='+$(this).find('zipNo').text();
		str += '&lnmAdres='+$(this).find('lnmAdres').text();
		str += '&rnAdres='+$(this).find('rnAdres').text();
	});
	
	return str;
}