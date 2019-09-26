function changeInfoGuide(){
	
	cLayer.style.width  = document.body.offsetWidth;
		
	if (document.body.scrollHeight > document.body.offsetHeight){
		cLayer.style.height = document.body.scrollHeight;
	}else{
		cLayer.style.height = document.body.offsetHeight;
	}

	var tableLeft = (parseInt(__replace(cLayer.style.width, 'px', '')) - parseInt(__replace(guideTable.style.width, 'px', ''))) / 2+'px';
	var tableTop  = (parseInt(document.body.offsetHeight) - parseInt(__replace(guideTable.style.height, 'px', ''))) / 2+'px';
	guideLayer.style.top = tableTop;
	guideLayer.style.left = tableLeft;
	guideLayer.style.width = guideTable.style.width;
	guideLayer.style.height = guideTable.style.height;
	guideTable.style.display = '';
}

function changeGuideCancel(){
	cLayer.style.width = 0;
	cLayer.style.height = 0;
	guideLayer.style.width = 0;
	guideLayer.style.height = 0;
	guideTable.style.display = 'none';
}