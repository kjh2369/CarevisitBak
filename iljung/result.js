var pageIndex = 1;

function lfSearch(){
	/*
	$.ajax({
		type:'POST',
		url:'http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_autoDmdList.do?method=nypkRfidmodify',
		data:{
			'pageIndex':'1'
		,	'serviceKind':subCd
		,	'searchFrDt':today
		,	'searchToDt':today
		,   'searchGbn':'5'
		,	'searchValue':appNo
		,	'delYn':'N'
		},
		beforeSend: function (){
			$(objRemark).html('<span style="color:#4374D9;">Loading...</span>');
		},
		success: function (html){
			var time = lfGetHtml(html, seq);

			$(objFrom).text(time['fromTime']);
			$(objTo).text(time['toTime']);
			$(objTime).text(time['procTime']);
			$(objRemark).html('<span style="color:blue; font-weight:bold;">OK</span>');
		},
		error: function (){
			alert('error');
		}
	}).responseXML;
	*/
}

function lfGetData(fromDt, toDt){
	var date = getToday();
	
	if (fromDt){
		date = fromDt;
	}
	
	try{
		$.ajax({
			type:'POST'
		,	url:'http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_autoDmdList.do?method=nypkRfidmodify'
		,	data:{
				'pageIndex':pageIndex
			,	'serviceKind':''
			,	'searchFrDt':date
			,	'searchToDt':date
			,	'searchGbn':''
			,	'searchValue':''
			,	'delYn':'N'
			}
		,	beforeSend:function (){
				if (pageIndex == 1){
					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
				}
			}
		,	success:function(data){
				if ($('.npaging', data).html()){
					lfSetData(data,date);
				}else{
					if (data.indexOf('로그인해주십시오.') > -1){
						$('#tempLodingBar').remove();
						alert('건보공단 로그인을 먼저 실행하여 주십시오.');
						return;
					}
					//종료
				}
			}
		,	error:function(){
				alert('error');
			}
		}).responseXML;
	}catch(e){
		alert(e);
	}
}

function lfSetData(data,date){
	var html = '';
	var nextPage = $('.npaging b + a', data).text();
	var nextTag = $('.npaging', data).find('a:last').text();
	var tagTimeTable = $('table[@background="/autodmd/ny/img/common/table_nemo_bg.gif"]', data);
	var claimYN = null,
		svcKind = null,
		client = null, cCd = null, cNm = null, cNo = null,
		member = null, mCd = null, mNm = null, mNo = null,
		autoYN = null,
		fromDT = null, fromDate = null, fromTime = null,
		toDT = null, toDate = null, toTime = null,
		procTime = null,
		min90YN = null,
		bathGbn = null,
		svcInfo = null, svcStr = null,
		addRow = false;
	var idx = 0;
	var first = true;

	$('tr', tagTimeTable).each(function(){
		if (!isNaN($('td:nth-child(1)', $(this)).text())){
			//claimYN = ($('td:nth-child(13)', $(this)).text().indexOf('청구') >= 0 ? 'Y' : 'N');
			claimYN = ($('td',this).eq(13).text().indexOf('청구') >= 0 ? 'Y' : 'N');
		}else{
			claimYN = 'error';
		}
		//svcKind = '방문'+$('td:nth-child(7)', $(this)).text();
		svcKind = '방문'+$('td',this).eq(7).text();
		//cNm = $('td:nth-child(3)', $(this)).text().split(' ').join('');
		cNm = $('td',this).eq(3).text().split(' ').join('').replace('(5등급)','');
		//cNo = $('td:nth-child(4)', $(this)).text().split(' ').join('');
		cNo = $('td',this).eq(4).text().split(' ').join('');
		//mNm = $('td:nth-child(5)', $(this)).text().split(' ').join('');
		mNm = $('td',this).eq(5).text().split(' ').join('');
		//mCd = $('td:nth-child(5)', $(this)).attr('title').split(' ').join('');
		mCd = $('td',this).eq(5).attr('title').split(' ').join('');
		//autoYN = ($('td:nth-child(2)', $(this)).text() == '[자동전송]' ? 'Y' : 'N');
		autoYN = ($('td',this).eq(2).text() == '[자동전송]' ? 'Y' : 'N');
		//fromDT = _getSplitDateTime($('td:nth-child(9)', $(this)).text());
		fromDT = _getSplitDateTime($('td',this).eq(9).text());
		fromDate = fromDT[0];
		fromTime = fromDT[1];
		//toDT = _getSplitDateTime($('td:nth-child(10)', $(this)).text());
		toDT = _getSplitDateTime($('td',this).eq(10).text());

		if (toDT){
			toDate = toDT[0];
			toTime = toDT[1];
		}else{
			claimYN = '-'
			toDate  = fromDate;
			toTime  = '';
		}

		//procTime = $('td:nth-child(8)', $(this)).text().split('분').join('');
		procTime = $('td',this).eq(8).text().split('분').join('');
		min90YN = (procTime == 90 ? 'Y' : 'N');


		if (svcKind == '방문목욕'){
			try{
				svcInfo = $('div', $(this)).html();
				svcStr  = svcInfo.split('<BR>');
				svcStr  = svcStr[1];
				svcStr  = svcStr.split(' ').join('');

				if (svcStr == '차량미이용이동식욕조사용')
					bathGbn = '1';
				else if (svcStr == '차량이용차량내목욕')
					bathGbn = '2';
				else if (svcStr == '차량이용가정내목욕')
					bathGbn = '3';
				else
					bathGbn = '4';
			}catch(e){
			}
		}else{
			bathGbn	= null;
			svcStr  = null;
		}

		addRow = false;

		if ($(':radio[name="claimYN"]:checked').attr('value') == 'Y'){
			if (claimYN == 'Y') addRow = true;
		}else{
			if (claimYN.length <= 1) addRow = true;
		}

		if (addRow){
			//데이타
			var para = 'claimYN='+claimYN
					 + '&svcKind='+svcKind
					 + '&cNm='+cNm
					 + '&cCd='+cCd
					 + '&cNo='+cNo
					 + '&mNm='+mNm
					 + '&mCd='+mCd
					 + '&autoYN='+autoYN
					 + '&fromDate='+fromDate
					 + '&fromTime='+fromTime
					 + '&toDate='+toDate
					 + '&toTime='+toTime
					 + '&procTime='+procTime
					 + '&min90YN='+min90YN
					 + '&bathGbn='+bathGbn
					 + '&first='+(first ? 'Y' : 'N');

			first = false;

			//if ($('#orgNo').val() == '34715000136'){
			//	alert(para);
			//	return;
			//}
			
			$.ajax({
				type:'POST'
			,	async:false
			,	url:'./result_save.php'
			,	data:{
					'para':para
				}
			,	beforeSend:function (){
				}
			,	success:function(result){
					if (result) alert(result);
				}
			,	error:function(){
				}
			}).responseXML;
		}

		idx ++;
	});

	if (pageIndex == 1 && !nextPage && !nextTag){
		var moveNext = false;
	}else if (!nextTag){
		var moveNext = true;
	}else{
		var moveNext = false;
	}

	//alert(pageIndex+'/'+nextPage+'/'+nextTag+'/'+moveNext);

	if (moveNext){
		//다음
		pageIndex ++;
		lfGetData(date, date);
	}else{
		//종료
		$('#tempLodingBar').remove();
		lfSearch();
	}
}

function _getSplitDateTime(html){
	var DateTime = html.split(' ');

	if (html){
		try{
		//	DateTime[0] = DateTime[0].split('.').join('');
			DateTime[1] = DateTime[1].substring(0,5);
		}catch(e){
		}

		return DateTime;
	}else{
		return '';
	}
}