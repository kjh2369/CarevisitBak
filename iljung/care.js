/*********************************************************
 *	기관 및 직원정보
 *********************************************************/
function _careLoadInfo(jumin){
	$.ajax({
		type :'POST'
	,	url  :'./care_info.php'
	,	data :{
			'jumin':jumin
		}
	,	success: function(html){
			$('#divInfo').html(html);
		}
	});
}

/*********************************************************
 *	수가정보
 *********************************************************/
function _careLoadSuga(year,month){
	$.ajax({
		type :'POST'
	,	url  :'./care_suga.php'
	,	data :{
			'year':year
		,	'month':month
		}
	,	success: function(html){
			$('#divSuga').html(html);
		}
	});
}

/*********************************************************
 *	달력출력
 *********************************************************/
function _careLoadCaln(jumin,year,month){
	$.ajax({
		type :'POST'
	,	url  :'./care_calendar.php'
	,	data :{
			'jumin':jumin
		,	'year':year
		,	'month':month
		}
	,	success: function(html){
			$('#divCaln').html(html);
		}
	});
}

/*********************************************************
 *	버튼
 *********************************************************/
function _careLoadBtn(year,month,sr){
	$.ajax({
		type :'POST'
	,	url  :'./care_btn.php'
	,	data :{
			'year':year
		,	'month':month
		,	'sr':sr
		}
	,	success: function(html){
			$('#divBtn').html(html);
		}
	});
}

/*********************************************************
 *	일정출력
 *********************************************************/
function _careLoadIljung(jumin,year,month,sr){
	$.ajax({
		type :'POST'
	,	url  :'./care_iljung.php'
	,	data :{
			'jumin':jumin
		,	'year':year
		,	'month':month
		,	'sr':sr
		}
	,	success: function(html){
			$('#divIljung').html(html);
		}
	});
}