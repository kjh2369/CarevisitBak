<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$todayDt = date('Ymd');

?>
<script type="text/javascript">
var opener = null;

$(document).ready(function(){
	opener = window.dialogArguments;
	opener.result = 1;
	opener.changeFlag = 0;
	setTitle();
	setBody();
});

function setTitle(){
	var title = '';

	switch(opener.type){
		case 1:
			title = '고객 계약이력';
			break;

		case 2:
			title = '장기요양보험 이력';
			break;

		case 3:
			title = '수급자구분 이력';
			break;

		case 4:
			title = '청구한도변경 이력';
			break;

		case 5:
			title = '가사간병 이력';
			break;

		case 6:
			title = '소득등급 이력';
			break;

		case 7:
			title = '노인들봄 이력';
			break;

		case 8:
			title = '산모신생아 이력';
			break;

		case 9:
			title = '장애인활동지원 이력';
			break;
		
		case 10:
			title = '장애인활동지원 이력';
			break;
		
		case 11:
			title = '소득등급 이력';
			break;

		default:
			title = 'noname';
	}

	$('#title').text(title);
}

function setBody(){
	$.ajax({
		type: 'POST'
	,	url : './client_pop_'+opener.type+'.php'
	,	data: {
			'svcCd':opener.svcCd
		}
	,	beforeSend: function (){
		}
	,	success: function (html){
			$('#body').html(html);

			if (opener.type == 1){
				setPeriod();
			}else if (opener.type == 2){
				setMgmt();
			}else if (opener.type == 3){
				setExpense();
			}else if (opener.type == 4){
				if ('<?=$lbLimitSet;?>' == '1'){
				}else{
					setLimit();
				}
			}

			search();
			self.focus();
		}
	,	error: function (){
		}
	}).responseXML;
}

function setPeriod(){
	//서비스명
	var svcNm = '';

	switch(opener.svcId){
		case '11': svcNm = '재가요양'; break;
		case '21': svcNm = '가사간병'; break;
		case '22': svcNm = '노인돌봄'; break;
		case '23': svcNm = '산모신생아'; break;
		case '24': svcNm = '장애인활동지원'; break;
		case '26': svcNm = '재가관리'; break;
		case '31': svcNm = '산모유료'; break;
		case '32': svcNm = '병원간병'; break;
		case '33': svcNm = '기타비급여'; break;
		case 'S': svcNm = '재가지원'; break;
		case 'R': svcNm = '자원연계'; break;
	}

	$('#svcNm').text(svcNm);


	//서비스이용여부
	$('input:radio[name="useYn"]:input[value="'+opener.stat+'"]').attr('checked',true);
	$('input:radio[name="useYn"]').attr('value1',opener.stat);

	/*
	if (opener.mode != '1'){
		if (opener.stat != '1')
			$('.reCont').show();
		else
			$('.reCont').hide();
	}else{
		$('.reCont').hide();
	}
	*/
	$('.reCont').show();

	//중지사유
	var reason = '';

	if (opener.svcId == '11'){
		reason = '<div style=\'float:left; width:33%;\'><input id=\'reason01\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'01\' '+(opener.reason == '01' ? 'checked' : '')+'><label for=\'reason01\'>계약해지</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason02\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'02\' '+(opener.reason == '02' ? 'checked' : '')+'><label for=\'reason02\'>보류</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason03\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'03\' '+(opener.reason == '03' ? 'checked' : '')+'><label for=\'reason03\'>사망</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason04\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'04\' '+(opener.reason == '04' ? 'checked' : '')+'><label for=\'reason04\'>타업체이동</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason05\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'05\' '+(opener.reason == '05' ? 'checked' : '')+'><label for=\'reason05\'>등외판정</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason06\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'06\' '+(opener.reason == '06' ? 'checked' : '')+'><label for=\'reason06\'>입원</label></div>';

		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason07\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'07\' '+(opener.reason == '07' ? 'checked' : '')+'><label for=\'reason07\'>무리한요구</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason08\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'08\' '+(opener.reason == '08' ? 'checked' : '')+'><label for=\'reason08\'>단순서비스종료</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason09\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'09\' '+(opener.reason == '09' ? 'checked' : '')+'><label for=\'reason09\'>근무자미투입</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason10\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'10\' '+(opener.reason == '10' ? 'checked' : '')+'><label for=\'reason10\'>거주지이전</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason11\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'11\' '+(opener.reason == '11' ? 'checked' : '')+'><label for=\'reason11\'>건강호전</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason12\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'12\' '+(opener.reason == '12' ? 'checked' : '')+'><label for=\'reason12\'>부담금미납</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason13\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'13\' '+(opener.reason == '13' ? 'checked' : '')+'><label for=\'reason13\'>지점이동</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason14\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'14\' '+(opener.reason == '14' ? 'checked' : '')+'><label for=\'reason14\'>요양입소</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason15\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'15\' '+(opener.reason == '15' ? 'checked' : '')+'><label for=\'reason15\'>주야간보호이용</label></div>';
		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason16\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'16\' '+(opener.reason == '16' ? 'checked' : '')+'><label for=\'reason16\'>서비스거부</label></div>';

		reason += '<div style=\'float:left; width:33%;\'><input id=\'reason99\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'99\' '+(opener.reason == '99' ? 'checked' : '')+'><label for=\'reason99\'>기타</label></div>';

	}else if (opener.svcId >= '21' && opener.svcId <= '24'){
		reason = '<div style=\'float:left; width:33%;\'><input id=\'reason01\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'01\' '+(opener.reason == '01' ? 'checked' : '')+'><label for=\'reason01\'>본인포기</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason02\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'02\' '+(opener.reason == '02' ? 'checked' : '')+'><label for=\'reason02\'>사망</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason03\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'03\' '+(opener.reason == '03' ? 'checked' : '')+'><label for=\'reason03\'>말소</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason04\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'04\' '+(opener.reason == '04' ? 'checked' : '')+'><label for=\'reason04\'>전출</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason05\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'05\' '+(opener.reason == '05' ? 'checked' : '')+'><label for=\'reason05\'>미사용</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason06\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'06\' '+(opener.reason == '06' ? 'checked' : '')+'><label for=\'reason06\'>본인부담금미납</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason07\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'07\' '+(opener.reason == '07' ? 'checked' : '')+'><label for=\'reason07\'>사업종료</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason08\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'08\' '+(opener.reason == '08' ? 'checked' : '')+'><label for=\'reason08\'>자격종료</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason09\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'09\' '+(opener.reason == '09' ? 'checked' : '')+'><label for=\'reason09\'>판정결과반영</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason10\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'10\' '+(opener.reason == '10' ? 'checked' : '')+'><label for=\'reason10\'>자격정지</label></div>'
			   + '<div style=\'float:left; width:33%;\'><input id=\'reason99\' name=\'reason\' type=\'radio\' class=\'radio\' value=\'99\' '+(opener.reason == '99' ? 'checked' : '')+'><label for=\'reason99\'>기타</label></div>';
	}

	if (reason != '')
		$('#reason').html(reason);
	else
		$('#reasonTr').hide();


	//적용기간
	$('#fromDt').attr('value1', opener.fromDt);
	$('#toDt').attr('value1', opener.toDt);
	

	//구분설정
	setReasonGbn();
	initForm();
}

function setMgmt(){
	//인정번호
	$('#mgmtNo').val(opener.mgmtNo);

	//유효기간
	$('#fromDt').val(opener.mgmtFrom).attr('value1',opener.mgmtFrom);
	$('#toDt').val(opener.mgmtTo).attr('value1',opener.mgmtTo);

	//등급
	$('input:radio[name="lvl"]:input[value="'+opener.mgmtLvl+'"]').attr('checked','checked');
	$('input:radio[name="lvl"]').attr('value1',opener.mgmtLvl);

	$('#seq').val(opener.seq);

	setLimitPay();
}


function setDtEnabled(obj,abRe){
	//적용기간 수정금지를 하지 않는다.
	//if ($(obj).attr('checked')){
	//	$('#fromDt').removeAttr('readonly');
	//	$('#toDt').removeAttr('readonly');
	//}else{
	//	$('#fromDt').val($('#fromDt').attr('value1')).attr('readonly',true);
	//	$('#toDt').val($('#toDt').attr('value1')).attr('readonly',true);
	//}
	//initForm();

	if (abRe){
		if ($(obj).attr('checked')){
			var lsDt = __addDate('d', 1, $('#toDt').val());

			$('#fromDt').val(lsDt);
			$('#toDt').val('').focus();
		}else{
			$('#fromDt').val($('#fromDt').attr('value1'));
			$('#toDt').val($('#toDt').attr('value1'));
		}

		if (opener.svcCd == '2' && opener.type == '6'){
			setExpense($('input:radio[name=\'lvl\']:checked'));
		}
	}
}

function setLimitPay(){
	var lvl = $('input:radio[name="lvl"]:checked').val();
	var dt  = getDt();
	var pay = '';

	if (opener.type == '2'){
		if (dt >= '2018-01-01'){
			$('#lvlA').parent().show();
		}else{
			$('#lvlA').parent().hide();
		}

		if (dt >= '2014-07-01'){
			$('#lvl4').parent().show();
			$('#lvl5').parent().show();
		}else{
			$('#lvl4').parent().hide();
			$('#lvl5').parent().hide();
		}
	}

	if (!dt){
		pay = '0';
	}else{
		pay = __num2str(getHttpRequest('../inc/_check.php?gubun=getMaxPay&code='+lvl+'&date='+dt));
	}

	$('#limitPay').text(pay);
}

function setReasonGbn(reContYn){
	/*
	if('<?=$todayDt;?>' > $('#toDt').val().replace('-','')){
		$("#use9").attr('checked', true);  
	}
	*/
	
	//구분설정
	var enabled = true;

	if (!reContYn) reContYn = false;

	if ($('input:radio[name="useYn"]:checked').val() != '1'){
		enabled = false;
	}

	$('input:radio[name="reason"]').attr('checked','checked').attr('disabled', enabled);
	$('input:radio[name="reason"]:input[value="'+opener.reason+'"]').attr('checked','checked');

	//중지시 계약기간 수정금지를 해제한다.
	//if (!enabled){
	//	$('input:text[name="dt[]"]').attr('readonly',true);
	//}else{
	//	$('input:text[name="dt[]"]').removeAttr('readonly');
	//}

	if (!reContYn || !enabled){
		$('#fromDt').val($('#fromDt').attr('value1'));
		$('#toDt').val($('#toDt').attr('value1'));
		$('#reCont').attr('checked','');
		
		//$('.reCont').show();
	}else{
		//$('#reCont').attr('checked', '');
		//$('.reCont').hide();
	}
	
	//중지 선택 시
	if (reContYn == 9){
		$('#toDt').val('');
		$('#toDt').focus();
		return;
	}

	/*
	if (!reContYn){
		if ($('input:radio[name="useYn"]:checked').val() == '1'){
			$('#reCont').attr('disabled',true);
		}else{
			$('#reCont').attr('disabled',false);
		}
	}
	*/

	__init_form(document.f);
}


/*********************************************************

	수급자구분 설정

*********************************************************/
function setExpense(){
	//수급자구분
	if(opener.kind == 4){
		if(opener.rate == '6.0'){
			$('input:radio[name="expenseKind"]:input[tags="1"]').attr('checked',true);
		}else if(opener.rate == '9.0'){
			$('input:radio[name="expenseKind"]:input[tags="2"]').attr('checked',true);
		}else {
			$('input:radio[name="expenseKind"]:input[tags="3"]').attr('checked',true);
		}
	}else if(opener.kind == 2){
		if(opener.rate == '6.0'){
			$('input:radio[name="expenseKind"]:input[tags="4"]').attr('checked',true);
		}else if(opener.rate == '9.0'){
			$('input:radio[name="expenseKind"]:input[tags="5"]').attr('checked',true);
		}else {
			$('input:radio[name="expenseKind"]:input[tags="6"]').attr('checked',true);
		}
	}else {
		$('input:radio[name="expenseKind"]:input[value="'+opener.kind+'"]').attr('checked',true);
	}
	$('input:radio[name="expenseKind"]').attr('value1',opener.kind);

	//본인부담율
	$('#expenseRate').val(opener.rate);

	//적용기간
	$('#fromDt').val(opener.from).attr('value1',opener.from);
	$('#toDt').val(opener.to).attr('value1',opener.to);

	$('#seq').val(opener.seq);

	/*
	if ($('#tblList tr').length > 0){
		$('.expenseModify').show();
		setDtEnabled($('#expenseModify'));
	}else{
		$('.expenseModify').hide();
	}
	*/

	/*********************************************************
		현재 수급자 등급 및 한도금액
	*********************************************************/
	setLimitAmt();
}

/*********************************************************

	가사간병 서비스 구분설정

*********************************************************/
/*
function setNurseTime(){
	//단가
	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	svcCd : opener.svcCd
		,	suga  : 'VH001'
		,	mode  : 9
		}
	,	beforeSend: function (){
		}
	,	success: function (cost){
			var time = $('input:radio[name="nurseVal"]:checked').attr('value1');
			var amt  = cost * time;

			//$('#nurseCost').attr('value', cost).text(__num2str(cost)+'원');
			//$('#nurseTime').attr('value', time).text(__num2str(time)+'시간');
			$('#nurseAmt').attr('value', amt).text(__num2str(amt));
		}
	,	error: function (){
		}
	}).responseXML;
}
*/

function search(){

	$.ajax({
		type: 'POST',
		url : './client_pop_list_'+opener.type+'.php',
		data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		},
		beforeSend: function (){
		},
		success: function (html){
			$('#tblList').html(html);
			
			eval('setDetail'+opener.type+'();');
		},
		error: function (){
		}
	}).responseXML;
}

function setDetail1(){
	if ($('#tblList tr').length == 0){
		//$('.reCont').hide();
		initForm();
		return;
	}

	var tr = $('#tblList tr:first');
	var td = $('td', tr);
	var id = '';

	$('label').each(function(){
		if (id == '' && $(this).text() == $(td).eq(3).text()){
			id = $(this).attr('for');
		}
	});

	id = id.split('reason').join('');

	opener.fromDt = $(td).eq(0).text().split('.').join('-');
	opener.toDt   = $(td).eq(1).text().split('.').join('-');
	opener.stat   = $(td).eq(2).text() == '이용' ? '1' : '9';
	opener.reason = id;
	opener.seq    = __str2num($('#seq_0').text());
	var mp = $(tr).attr('mp') ? $(tr).attr('mp') : 'N';

	$('#fromDt').attr('value', opener.fromDt).attr('value1', opener.fromDt);
	$('#toDt').attr('value', opener.toDt).attr('value1', opener.toDt);

	if (opener.stat == '1'){
	//if ($('#tblList tr').length == 0){
		$('input:radio[name="useYn"]').attr('value1','1');
		$('#use1').attr('checked',true);
		//$('.reCont').hide();
	}else{
		$('input:radio[name="useYn"]').attr('value1','9');
		$('#use9').attr('checked',true);
		$('input:radio[name="reason"]:input[value="'+opener.reason+'"]').attr('checked','checked');
		//$('.reCont').show();

		//if (opener.mode != '1'){
		//	if (opener.stat != '1'){
		//		$('.reCont').attr('checked', '').show();
		//	}
		//}
	}

	if (opener.svcCd == 'S' || opener.svcCd == 'R'){
		$('#optMPGbn'+mp).attr('checked',true);
	}

	setReasonGbn();
}

function setDetail2(){
	if ($('#tblList tr').length == 0){
		$('.mgmtModify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
		opener.seq = 0;
		initForm();
		return;
	}

	var td = $('td', $('#tblList tr:first'));

	opener.mgmtNo    = $(td).eq(0).text();
	opener.mgmtFrom  = $(td).eq(1).text().split('.').join('-');
	opener.mgmtTo    = $(td).eq(2).text().split('.').join('-');
	//opener.mgmtLvl   = $(td).eq(3).text() == '일반' ? '9' : $(td).eq(3).text().substring(0,1);
	opener.mgmtLvl = $('#mgmtLvl', td).attr('lvl');
	opener.mgmtLvlNm = $(td).eq(3).text();
	opener.mgmtPay   = __str2num($(td).eq(4).text());
	opener.reason    = opener.mgmtLvl;
	opener.seq       = $('#seq_0').text();

	var dt = getDt(opener.mgmtFrom, opener.mgmtTo);

	if (dt >= '2014-07-01'){
		$('#lvl4').parent().show();
		$('#lvl5').parent().show();
	}else{
		$('#lvl4').parent().hide();
		$('#lvl5').parent().hide();
	}

	if (!opener.mgmtLvl) opener.mgmtLvl = '9';

	$('#mgmtNo').attr('value', opener.mgmtNo);
	$('#fromDt').attr('value', opener.mgmtFrom).attr('value1', opener.mgmtFrom);
	$('#toDt').attr('value', opener.mgmtTo).attr('value1', opener.mgmtTo);
	$('input:radio[name="lvl"]:input[value="'+opener.mgmtLvl+'"]').attr('checked','checked');
	$('#limitPay').text(__num2str(opener.mgmtPay));
	$('#seq').val(opener.seq);

	if ($('#tblList tr').length > 0){
		$('.mgmtModify').show();
		setDtEnabled($('#mgmtModify'));
	}else{
		$('.mgmtModify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
	}

	initForm();
}

function setDetail3(){
	if ($('#tblList tr').length == 0){
		$('.expenseModify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
		opener.seq = 0;
		initForm();
		return;
	}

	var td    = $('td', $('#tblList tr:first'));
	var lvlNm = $(td).eq(2).text();
	var lvlCd = lvlNm == '일반' ? '9' : lvlNm.substring(0,1);
	var limit = __str2num($(td).eq(2).text());
	var kind  = $(td).eq(4).text();
	var seq   = $('#seq_0').text();

	switch(kind){
		case '기초': kind = '3'; break;
		case '의료': kind = '2'; break;
		case '경감': kind = '4'; break;
		default: kind = '1'; break;
	}

	opener.from   = $(td).eq(0).text().split('.').join('-');
	opener.to     = $(td).eq(1).text().split('.').join('-');
	opener.kind   = kind;
	opener.rate   = $(td).eq(5).text();
	opener.seq    = seq;
	opener.reason = opener.kind;

	if (opener.seq > 0){
		$('.expenseModify').show();
		setDtEnabled($('#expenseModify'));
	}else{
		$('.expenseModify').hide();
	}

	/*********************************************************
		현재 수급자 등급 및 한도금액
	*********************************************************/
	setExpenseRate();
	setExpense();
}

function setDetail4(){
	if ($('#tblList tr').length == 0){
		$('.limitModify').hide();
		initForm();
		return;
	}

	var td  = $('td', $('#tblList tr:first'));
	var seq = $('#seq_0').text();

	opener.from = __getDate($(td).eq(0).text());
	opener.to   = __getDate($(td).eq(1).text());
	opener.seq  = seq;

	if ('<?=$lbLimitSet;?>' == '1'){
		opener.amtCare  = __str2num($(td).eq(2).text());
		opener.amtBath  = __str2num($(td).eq(3).text());
		opener.amtNurse = __str2num($(td).eq(4).text());
		opener.amt      = __str2num($(td).eq(5).text());
	}else{
		opener.amt = __str2num($(td).eq(2).text());
	}

	if (opener.seq > 0){
		$('.limitModify').show();
		setDtEnabled($('#limitModify'));
	}else{
		$('.limitModify').hide();
	}

	$('#fromDt').val(opener.from).attr('value1',opener.from);
	$('#toDt').val(opener.to).attr('value1',opener.to);

	if ('<?=$lbLimitSet;?>' == '1'){
		$('#txtLimitCare').val(__num2str(opener.amtCare)).attr('value1',opener.amtCare);
		$('#txtLimitBath').val(__num2str(opener.amtBath)).attr('value1',opener.amtBath);
		$('#txtLimitNurse').val(__num2str(opener.amtNurse)).attr('value1',opener.amtNurse);
		$('#lblLimitTot').text(__num2str(opener.amt));
	}else{
		$('#limitAmt').val(__num2str(opener.amt)).attr('value1',opener.amt);
	}

	initForm();
}

function setDetail5(){
	var td  = $('td', $('#tblList tr:first'));
	var seq = $('#seq_0').text();

	opener.from = __getDate($(td).eq(0).text());
	opener.to   = __getDate($(td).eq(1).text());
	opener.amt  = __str2num($(td).eq(3).text());
	opener.seq  = seq;

	if (opener.seq > 0){
		$('.modify').show();
		setDtEnabled($('#modify'));
	}else{
		$('.modify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
	}

	$('#fromDt').val(opener.from).attr('value1',opener.from);
	$('#toDt').val(opener.to).attr('value1',opener.to);
	$('#nurseAmt').attr('value',opener.amt).text(__num2str(opener.amt));

	initForm();
}

function setDetail6(){
	var td  = $('td', $('#tblList tr:first'));
	var seq = $('#seq_0').text();

	opener.from = __getDate($(td).eq(0).text());
	opener.to   = __getDate($(td).eq(1).text());
	opener.lvl  = $('#lvl_0').val();
	opener.amt  = __str2num($(td).eq(3).text());
	opener.seq  = seq;

	//if (!opener.lvl) opener.lvl = '9';
	//if (opener.lvl == '9') opener.amt = $('#amt').text();

	if (opener.seq > 0){
		$('.modify').show();
		setDtEnabled($('#modify'));
	}else{
		$('.modify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
	}

	$('#fromDt').val(opener.from).attr('value1',opener.from);
	$('#toDt').val(opener.to).attr('value1',opener.to);
	//$('#expense').text(__num2str(opener.amt));
	$('input:radio[name="lvl"]:input[value="'+opener.lvl+'"]').attr('checked','checked');

	//getExpenseAmt();
	initForm();

	if (opener.svcCd == '2'){
		setExpense($('input:radio[name=\'lvl\']:checked'));
	}
}

function setDetail7(){
	var td  = $('td', $('#tblList tr:first'));

	opener.from = __getDate($(td).eq(0).text());
	opener.to   = __getDate($(td).eq(1).text());
	opener.seq  = $('#seq_0').text();

	if (opener.seq > 0){
		$('.modify').show();
		setDtEnabled($('#modify'));
	}else{
		$('.modify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
	}

	$('#fromDt').val(opener.from).attr('value1',opener.from);
	$('#toDt').val(opener.to).attr('value1',opener.to);

	initForm();
}

function setDetail8(){
	var td  = $('td', $('#tblList tr:first'));

	opener.from = __getDate($(td).eq(0).text());
	opener.to   = __getDate($(td).eq(1).text());
	opener.seq  = $('#seq_0').text();

	if (opener.seq > 0){
		$('.modify').show();
		setDtEnabled($('#modify'));
	}else{
		$('.modify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
	}

	$('#fromDt').val(opener.from).attr('value1',opener.from);
	$('#toDt').val(opener.to).attr('value1',opener.to);

	initForm();
}

function setDetail11(){
	var td  = $('td', $('#tblList tr:first'));
	var seq = $('#seq_0').text();

	opener.from = __getDate($(td).eq(0).text());
	opener.to   = __getDate($(td).eq(1).text());
	opener.lvl  = $('#lvl_0').val();
	opener.amt  = __str2num($(td).eq(3).text());
	opener.seq  = seq;
	
	if (opener.seq > 0){
		$('.modify').show();
		setDtEnabled($('#modify'));
	}else{
		$('.modify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
	}

	$('#fromDt').val(opener.from).attr('value1',opener.from);
	$('#toDt').val(opener.to).attr('value1',opener.to);
	$('input:radio[name="lvl"]:input[value="'+opener.lvl+'"]').attr('checked','checked');

	initForm();

}

function doDel(no){
	var execFun = false;

	//if (opener.type == 1) execFun = true;
	if (execFun){
		$.ajax({
			type: "POST",
			url : "./client_pop_fun.php",
			data: {
				code  : opener.code
			,	jumin : opener.jumin
			,	svcCd : opener.svcCd
			,	para  : 'from='+$('#from_'+no).text()+'&to='+$('#to_'+no).text()
			,	type  : opener.type
			,	mode  : 1
			},
			beforeSend: function (){
			},
			success: function (result){
				if (__str2num(result) > 0){
					alert('계약기간에 등록된 일정이 있어 계약을 삭제할 수 없습니다.\n확인하여 주십시오.');
					return;
				}

				if (!confirm('계약기간 삭제 후 복구가 불가능합니다.\n선택하신 계약기간을 정말로 삭제하시곘습니까?')) return;
				execDel(no);
			},
			error: function (){
			}
		}).responseXML;
	}else{
		if (!confirm('계약기간 삭제 후 복구가 불가능합니다.\n선택하신 계약기간을 정말로 삭제하시곘습니까?')) return;
		execDel(no);
	}
}

function execDel(no){
	$.ajax({
		type: "POST",
		url : "./client_pop_fun.php",
		data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : $('#seq_'+no).text()
		,	type  : opener.type
		,	mode  : 2
		},
		beforeSend: function (){
		},
		success: function (result){
			if (result == 9){
				alert('계약기간 삭제중 오류가 발생하였습니다.\n 잠시후 다시 시도하여 주십시오.');
				return;
			}

			alert('정상적으로 처리되었습니다.');
			opener.changeFlag = 1;
			search();
		},
		error: function (){
		}
	}).responseXML;
}

function execApply(){
	eval('execApply'+opener.type+'();');
}

function execApply1(){
	if (opener.svcCd >= 'A'){
	}else{
		if ($('input:radio[name="useYn"]:checked').attr('value') != '1'){
			var reason = $('input:radio[name="reason"]:checked').attr('value');

			if (!reason){
				alert('중지사유를 선택하여 주십시오.');
				return;
			}
		}
	}

	if (!$('#fromDt').val()){
		alert('계약시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('계약종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('계약시작일자가 계약종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	/*
	$.ajax({
		type: "POST",
		url : "./client_pop_fun.php",
		data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 3
		},
		beforeSend: function (){
		},
		success: function (result){
			if (result == 2){
				alert('입력하신 계약기간 이전에 등록된 일정이 있습니다.\n확인하여 주십시오.');
				$('#fromDt').focus();
				return;
			}else if (result == 3){
				alert('입력하신 계약기간 이후에 등록된 일정이 있습니다.\n확인하여 주십시오.');
				$('#toDt').focus();
				return;
			}else if (result == 4){
				alert('입력하신 계약시작일자 이후에 계약이력이 있습니다.\n확인하여 주십시오.');
				$('#fromDt').focus();
				return;
			}else if (result == 1){
				var reContYn = '';

				if (opener.mode != '1')
					reContYn = !$('#reCont').attr('checked') ? '1' : '2';

				if (reContYn == '1')
					execPeriod();
				else
					execReReriod();
			}else{
				alert(result);
			}
		},
		error: function (){
		}
	}).responseXML;
	*/

	var reContYn = '';

	if (opener.mode != '1')
		reContYn = !$('#reCont').attr('checked') ? '1' : '2';

	if (reContYn == '1')
		execPeriod();
	else
		execReReriod();
}

function execApply2(){
	if ($('#mgmtNo').val().length != $('#mgmtNo').attr('maxlength')){
		alert('인정번호를 "L"을 포함하여 11자리의 숫자를 입력하여 주십시오.');
		$('#mgmtNo').focus();
		return;
	}

	if (!$('#fromDt').val()){
		alert('유효기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('유효기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('유효기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#mgmtModify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 4
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execMgmtNo(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 유효기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	수급자구분 이력

*********************************************************/
function execApply3(){
	if (!$('#fromDt').val()){
		alert('적용기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('적용기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('적용기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#expenseModify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 6
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execKind(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 적용기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	청구한도이력

*********************************************************/
function execApply4(){
	if (!$('#fromDt').val()){
		alert('적용기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('적용기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('적용기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#limitModify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()+'&amt1='+__str2num($('#limitAmt').attr('value'))+'&amt2='+__str2num($('#limitAmt').attr('value1'))
		,	mode  : 8
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execLimit(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 적용기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			//}else if (result == 4){
			//	alert('입력하신 적용기간에 등록된 일정이 있으면 청구한도금액을 수정할 수 없습니다.');
			//	$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	가사간병 이력

*********************************************************/
function execApply5(){
	if (!$('#fromDt').val()){
		alert('적용기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('적용기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('적용기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#modify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 10
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execNurse(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 적용기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	소득등급 이력

*********************************************************/
function execApply6(){
	
	if (!$('input:radio[name="lvl"]:checked').val()){
		alert('소득등급을 선택하여 주십시오.');
		return;
	}

	if (!$('#fromDt').val()){
		alert('적용기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('적용기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('적용기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#modify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 12
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execLevel(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 적용기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	노인돌봄 이력

*********************************************************/
function execApply7(){
	if (!$('#fromDt').val()){
		alert('적용기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('적용기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('적용기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#modify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 13
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execOld(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 적용기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	산모신생아 이력

*********************************************************/
function execApply8(){
	if (!$('input:radio[name="val"]:checked').val()){
		alert('서비스구분을 선택하여 주십시오.');
		return;
	}

	if (!$('#fromDt').val()){
		alert('적용기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('적용기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('적용기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#modify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 14
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execBaby(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 적용기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	장애인(신) 소득등급 이력

*********************************************************/
function execApply11(){
	if (!$('input:radio[name="lvl"]:checked').val()){
		alert('소득등급을 선택하여 주십시오.');
		return;
	}

	if (!$('#fromDt').val()){
		alert('적용기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('적용기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	//var diffDt = diffDate('d',$('#fromDt').val(),$('#toDt').val());
	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('적용기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#modify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 12
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execLevel(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 적용기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}



function execPeriod(){
	if (!confirm('현재 설정을 적용시키시겠습니까?')) return;

	execApplyReriod(2);
}

function execReReriod(){
	if (!confirm('현재 설정으로 계약을 적용시키시겠습니까?')) return;

	execApplyReriod(1);
}

function execMgmtNo(seq){
	$.ajax({
		type: 'POST',
		url : './client_apply.php',
		data: {
			code     : opener.code
		,	jumin    : opener.jumin
		,	seq      : seq
		,	mgmtNo   : $('#mgmtNo').val()
		,	mgmtFrom : $('#fromDt').val()
		,	mgmtTo   : $('#toDt').val()
		,	mgmtLvl  : $('input:radio[name="lvl"]:checked').val()
		,	svcCd    : opener.svcCd
		,	mode     : 11
		,	type     : opener.type
		},
		beforeSend: function (){
		},
		success: function (result){
			execApplyResult(result);
		},
		error: function (){
		}
	}).responseXML;
}

/*********************************************************

	수급자구분 적용

*********************************************************/
function execKind(seq){
	$.ajax({
		type: 'POST',
		url : './client_apply.php',
		data: {
			code     : opener.code
		,	jumin    : opener.jumin
		,	seq      : seq
		,	mgmtSeq  : $('#mgmtSeq').val()
		,	kind	 : $('input:radio[name="expenseKind"]:checked').val()
		,	rate	 : $('#expenseRate').val()
		,	amt		 : __str2num($('#expenseAmt').text())
		,	from	 : $('#fromDt').val()
		,	to		 : $('#toDt').val()
		,	mode     : 12
		,	type     : opener.type
		},
		beforeSend: function (){
		},
		success: function (result){
			execApplyResult(result);
		},
		error: function (){
		}
	}).responseXML;
}

/*********************************************************

	청구한도이력 적용

*********************************************************/
function execLimit(seq){
	if ('<?=$lbLimitSet;?>' == '1'){
		$.ajax({
			type: 'POST',
			url : './client_apply.php',
			data: {
				code     : opener.code
			,	jumin    : opener.jumin
			,	seq      : seq
			,	from	 : $('#fromDt').val()
			,	to		 : $('#toDt').val()
			,	amtCare  : __str2num($('#txtLimitCare').val())
			,	amtBath  : __str2num($('#txtLimitBath').val())
			,	amtNurse : __str2num($('#txtLimitNurse').val())
			,	mode     : 13
			,	type     : opener.type
			},
			beforeSend: function (){
			},
			success: function (result){
				execApplyResult(result);
			},
			error: function (){
			}
		}).responseXML;
	}else{
		$.ajax({
			type: 'POST',
			url : './client_apply.php',
			data: {
				code     : opener.code
			,	jumin    : opener.jumin
			,	seq      : seq
			,	from	 : $('#fromDt').val()
			,	to		 : $('#toDt').val()
			,	amt		 : __str2num($('#limitAmt').val())
			,	mode     : 13
			,	type     : opener.type
			},
			beforeSend: function (){
			},
			success: function (result){
				execApplyResult(result);
			},
			error: function (){
			}
		}).responseXML;
	}
}

function execApplyReriod(mode){
	var stat   = $('input:radio[name="useYn"]:checked').attr('value');
	var reason = $('input:radio[name="reason"]:checked').attr('value');
	var mp = '';

	if (opener.svcCd == 'S' || opener.svcCd == 'R'){
		mp = $('input:radio[name="optMPGbn"]:checked').val();
		if (!mp) mp = 'N';
	}

	if (!stat) stat = '9';
	if (!reason) reason = '';
	
	$.ajax({
		type: 'POST',
		url : './client_apply.php',
		data: {
			code   : opener.code
		,	jumin  : opener.jumin
		,	svcCd  : opener.svcCd
		,	stat   : stat
		,	reason : reason
		,	fromDt : $('#fromDt').val()
		,	toDt   : $('#toDt').val()
		,	mp		: mp
		,	mode   : mode
		,	type   : opener.type
		},
		beforeSend: function (){
		},
		success: function (result){
			execApplyResult(result);
		},
		error: function (result){
		}
	}).responseXML;
}

/*********************************************************

	가사간병

*********************************************************/
function execNurse(seq){
	$.ajax({
		type: 'POST'
	,	url : './client_apply.php'
	,	data: {
			code     : opener.code
		,	jumin    : opener.jumin
		,	seq      : seq
		,	from	 : $('#fromDt').val()
		,	to		 : $('#toDt').val()
		,	val		 : $('input:radio[name="nurseVal"]:checked').val()
		,	mode     : 14
		,	type     : opener.type
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			execApplyResult(result);
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	노인돌봄 적용

*********************************************************/
function execOld(seq){
	$.ajax({
		type: 'POST'
	,	url : './client_apply.php'
	,	data: {
			code     : opener.code
		,	jumin    : opener.jumin
		,	seq      : seq
		,	from	 : $('#fromDt').val()
		,	to		 : $('#toDt').val()
		,	val		 : $('input:radio[name="oldVal"]:checked').val()
		,	time	 : $('input:radio[name="oldTm"]:checked').val()
		,	mode     : 16
		,	type     : opener.type
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			execApplyResult(result);
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	산모신생아 적용

*********************************************************/
function execBaby(seq){
	$.ajax({
		type: 'POST'
	,	url : './client_apply.php'
	,	data: {
			code     : opener.code
		,	jumin    : opener.jumin
		,	seq      : seq
		,	from	 : $('#fromDt').val()
		,	to		 : $('#toDt').val()
		,	val		 : $('input:radio[name="val"]:checked').val()
		,	mode     : 17
		,	type     : opener.type
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			execApplyResult(result);
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	소득등급

*********************************************************/
function execLevel(seq){
	$.ajax({
		type: 'POST',
		url : './client_apply.php',
		data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : seq
		,	from  : $('#fromDt').val()
		,	to	  : $('#toDt').val()
		,	lvl	  : $('input:radio[name="lvl"]:checked').val()
		,	mode  : 15
		,	type  : opener.type
		},
		beforeSend: function (){
		},
		success: function (result){
			execApplyResult(result);
		},
		error: function (){
		}
	}).responseXML;
}

function execApplyResult(result){
	if (!isNaN(result)){
		if (result == 101 || result == 102){
			alert('중복된 계약기간을 입력하셨습니다.\n확인 후 다시 입력하여 주십오.');
		}else if (result == 9){
			alert('데이타 저장중 오류가 발생하였습니다.\n확인 후 다시 입력하여 주십오.');
		}else if (result == 1){
			if (opener.svcId == 'S' || opener.svcId == 'R'){
				opener.fromDt	= $('#fromDt').val();
				opener.toDt		= $('#toDt').val();
				opener.mp		= $('input:radio[name="optMPGbn"]:checked').val();
			}

			alert('정상적으로 처리되었습니다.');
			search();
			//self.close();

			return;
		}else if (result == 0){

			if (opener.type == 1){
				opener.result   = result;
				opener.stat     = $('input:radio[name="useYn"]:checked').val();
				opener.reason   = $('input:radio[name="reason"]:checked').val();
				opener.statNm   = $('label[for="use'+opener.stat+'"]').text();
				opener.reasonNm = $('label[for="reason'+opener.stat+'"]').text();
				opener.fromDt   = $('#fromDt').val();
				opener.toDt     = $('#toDt').val();
				opener.mpGbn	= $('input:radio[name="optMPGbn"]:checked').val();
			}else if (opener.type == 2){
				opener.result    = result;
				opener.mgmtNo    = $('#mgmtNo').val()
				opener.mgmtFrom  = $('#fromDt').val()
				opener.mgmtTo    = $('#toDt').val()
				opener.mgmtLvl   = $('input:radio[name="lvl"]:checked').val()
				opener.mgmtLvlNm = $('label[for="lvl'+$('input:radio[name="lvl"]:checked').val()+'"]').text();
				opener.mgmtPay   = $('#limitPay').text();
				opener.seq       = '1';
			}else if (opener.type == 3){
				opener.result = result;
				opener.kind   = $('input:radio[name="expenseKind"]:checked').val();
				opener.kindNm = __kindNm(opener.kind);
				opener.rate   = $('#expenseRate').val();
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.amt    = __str2num($('#expenseAmt').text());
				opener.seq    = '1';
			}else if (opener.type == 4){
				opener.result = result;
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.seq    = '1';

				if ('<?=$lbLimitSet;?>' == '1'){
					opener.amtCare  = $('#txtLimitCare').val();
					opener.amtBath  = $('#txtLimitBath').val();
					opener.amtNurse = $('#txtLimitNurse').val();
					opener.amt      = $('#lblLimitTot').text();
				}else{
					opener.amt = $('#limitAmt').val();
				}
			}else if (opener.type == 5){
				opener.result = result;
				opener.val    = $('input:radio[name="nurseVal"]:checked').val();
				opener.time   = $('input:radio[name="nurseVal"]:checked').attr('value1');
				opener.amt    = $('#nurseAmt').attr('value');
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.seq    = '1';
			}else if (opener.type == 6){
				opener.result = result;
				opener.lvl    = $('input:radio[name="lvl"]:checked').val();
				opener.lvlNm  = $('label[for="lvl'+opener.lvl+'"]').text();
				opener.amt    = __str2num($('#expense').text());
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.seq    = '1';
			}else if (opener.type == 7){
				opener.result = result;
				opener.val    = $('input:radio[name="oldVal"]:checked').val();
				opener.valNm  = $('label[for="oldVal'+opener.val+'"]').text();
				opener.time   = $('input:radio[name="oldTm"]:checked').val();
				opener.timeNm = $('label[for="oldTm'+opener.time+'"]').text();
				opener.amt    = __str2num($('#oldAmt').text());
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.seq    = '1';
			}else if (opener.type == 8){
				opener.result = result;
				opener.val    = $('input:radio[name="val"]:checked').val();
				opener.valNm  = $('label[for="val'+opener.val+'"]').text();
				opener.amt    = __str2num($('#amt').text());
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.seq    = '1';
			}else if (opener.type == 9){
				opener.result = result;
				opener.val    = $('input:radio[name="val"]:checked').val();
				opener.valNm  = $('label[for="val'+opener.val+'"]').text();
				opener.lvl    = $('input:radio[name="lvl"]:checked').val();
				opener.lvlNm  = $('label[for="lvl'+opener.val+'"]').text();
				opener.amt    = $('#amt').attr('value');
				opener.time   = $('#time').attr('value');
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.seq    = '1';
			}else if (opener.type == 10){
				opener.result = result;
				opener.val    = $('input:radio[name="val"]:checked').val();
				opener.valNm  = $('label[for="val'+opener.val+'"]').text();
				opener.lvl    = $('input:radio[name="lvl"]:checked').val();
				opener.lvlNm  = $('label[for="lvl'+opener.val+'"]').text();
				opener.amt    = $('#amt').attr('value');
				opener.time   = $('#time').attr('value');
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.seq    = '1';
			}else if (opener.type == 11){
				opener.result = result;
				opener.lvl    = $('input:radio[name="lvl"]:checked').val();
				opener.lvlNm  = $('label[for="lvl'+opener.lvl+'"]').text();
				opener.amt    = __str2num($('#expense').text());
				opener.from   = $('#fromDt').val();
				opener.to     = $('#toDt').val();
				opener.seq    = '1';
			}

			self.close();

			return;
		}else{
			alert(result);
		}
	}else{
		/*
		if (mode == 9){
			var val = __parseStr(result);

			if (val['fromDt'] != ''){
				alert('계약시작일자가 기존의 계약기간과 중복됩니다.\n확인 후 다시 입력하여 주십시오.');
			}else if (val['toDt'] != ''){
				alert('계약종료일자가 기존의 계약기간과 중복됩니다.\n확인 후 다시 입력하여 주십시오.');
			}
		}else{
			alert(result);
		}
		*/
		alert(result);
	}

	search();
}

function setReCont(){
	/*
	if ($('#reCont').attr('checked')){
		var fromDt = addDate('d', 1, opener.toDt);
		var toDt   = addDate('d', -1, addDate('yyyy', 1, opener.toDt));

		$('#use1').attr('checked','checked');
		$('#fromDt').val(fromDt);
		$('#toDt').val(toDt);
	}else{
		$('input:radio[name="useYn"]:input[value="'+opener.stat+'"]').attr('checked','checked');
	}

	setReasonGbn(true);
	 */
	if ($('#reCont').attr('checked')){
		$.ajax({
			type: "POST",
			url : "./client_chk_next_dt.php",
			data: {
				code  : opener.code
			,	jumin : opener.jumin
			,	svcCd : opener.svcCd
			},
			success: function (date){
				var fromDt = date;
				var toDt   = __addDate('d', -1, __addDate('yyyy', 1, fromDt));

				$('#use1').attr('checked','checked');
				$('#fromDt').val(fromDt);
				$('#toDt').val(toDt);

				setReasonGbn(true);
			},
			error: function (){
			}
		}).responseXML;
	}else{
		$('input:radio[name="useYn"]:input[value="'+opener.stat+'"]').attr('checked','checked');
		$('#fromDt').val($('#fromDt').attr('value1'));
		$('#toDt').val($('#toDt').attr('value1'));
		setReasonGbn(true);
	}
}


/*********************************************************
	수급자 한도금액
*********************************************************/
function setLimitAmt(){
	$.ajax({
		type: "POST",
		url : "./client_pop_fun.php",
		data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 5
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);

			//등급순번
			$('#mgmtSeq').val(__str2num(val['seq']));

			if (!val['level']) val['level'] = opener.mgmtLvl;
			if (!val['limit']) val['limit'] = opener.mgmtPay;

			//수급자등급
			$('#mgmtLvl').val(val['level']);
			$('#lvlNm').text(__lvlNm(val['level']));

			if (val['level'] != '9'){
				$('#expenseKind2').attr('disabled', false);
				$('#expenseKind3').attr('disabled', false);
				$('#expenseKind4').attr('disabled', false);
				$('#expenseRate').removeAttr('readonly');
			}else{
				$('#expenseKind2').attr('disabled', true);
				$('#expenseKind3').attr('disabled', true);
				$('#expenseKind4').attr('disabled', true);
				$('#expenseRate').attr('readonly', true);
			}

			//한도금액
			$('#limitPay').text(__num2str(val['limit']));
			$('#mgmtPay').val(__str2num(val['limit']));

			//본인부담금
			setExpenseRate();

			initForm();
		},
		error: function (){
		}
	}).responseXML;
}

/*********************************************************
	수급자 본인부담금
*********************************************************/
function setExpenseRate(){
	var kind = $('input:radio[name="expenseKind"]:checked').val();
	var tmpK = $('input:radio[name="expenseKind"]:checked').attr('value1');
	var rate = $('#mgmtLvl').val() != '9' ? 15.0 : 100;
	var amt  = 0;
	var dt = $('#fromDt').val();

	if (!opener.rate) opener.rate = rate;
	
	if (kind == tmpK){
		rate = opener.rate;
		if (dt >= '2018-08-01'){
			$('#expenseKind4').parent().hide();
			$('#expenseKind5').parent().show();
			$('#expenseKind6').parent().show();
			$('#expenseKind2').parent().hide();
			$('#expenseKind7').parent().show();
			$('#expenseKind8').parent().show();
		
			if (kind == '4'){
				if($('input:radio[name="expenseKind"]:checked').attr('tags')=='1'){
					rate = 6;
				}else {
					rate = 9;
				}
			}

			if (kind == '2'){
				if($('input:radio[name="expenseKind"]:checked').attr('tags')=='4'){
					rate = 6;
				}else {
					rate = 9;
				}
			}
		}else {
			$('#expenseKind4').parent().show();
			$('#expenseKind5').parent().hide();
			$('#expenseKind6').parent().hide();
			$('#expenseKind2').parent().show();
			$('#expenseKind7').parent().hide();
			$('#expenseKind8').parent().hide();

			if (kind == '4'){
				rate = 7.5;
			}
		}
	}else{
		
		if (dt >= '2018-08-01'){
			$('#expenseKind4').parent().hide();
			$('#expenseKind5').parent().show();
			$('#expenseKind6').parent().show();
			$('#expenseKind2').parent().hide();
			$('#expenseKind7').parent().show();
			$('#expenseKind8').parent().show();
			
			if (kind == '3'){
				rate = 0;
			}else if (kind == '2'){
				if($('input:radio[name="expenseKind"]:checked').attr('tags')=='4'){
					rate = 6;
				}else {
					rate = 9;
				}
			}else if (kind == '4'){
				if($('input:radio[name="expenseKind"]:checked').attr('tags')=='1'){
					rate = 6;
				}else {
					rate = 9;
				}
			}
			
		}else{
			
			$('#expenseKind4').parent().show();
			$('#expenseKind5').parent().hide();
			$('#expenseKind6').parent().hide();
			$('#expenseKind2').parent().show();
			$('#expenseKind7').parent().hide();
			$('#expenseKind8').parent().hide();

			if (kind == '3')
				rate = 0;
			else if (kind == '2' || kind == '4')
				rate = 7.5;
		}
	}

	$('#expenseRate').val(rate);

	setExpenseAmt();
}
function setExpenseAmt(){
	var limit = $('#mgmtPay').val();
	var rate  = $('#expenseRate').val();

	$('#expenseAmt').text(__num2str(cutOff(limit * rate * 0.01)));
}

/*********************************************************

	청구한도이력

*********************************************************/
function setLimit(abVal){
	/*********************************************************
		현재 수급자 등급 및 한도금액
	*********************************************************/
	$.ajax({
		type: "POST",
		url : "./client_pop_fun.php",
		data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 5
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);

			if (val['seq'] != '') abVal = true;

			if (abVal){
				if (val['seq'] != ''){
					$('#mgmtLvl').text(__lvlNm(val['level']));
					$('#mgmtPay').text(__num2str(val['limit']));

					setKind();
				}else{
					$('#mgmtLvl').text(__lvlNm(opener.lvl));
					$('#mgmtPay').text(__num2str(opener.maxPay));
					$('#expenseKind').text(__kindNm(opener.kind));
					$('#expenseAmt').text(__num2str(cutOff(__str2num(opener.maxPay) * __str2num(opener.rate) * 0.01)));
					$('#expenseRate').text('['+opener.rate+'%]');
				}
			}else{
				$('#mgmtLvl').text(__lvlNm(opener.lvl));
				$('#mgmtPay').text(__num2str(opener.maxPay));
				$('#expenseKind').text(__kindNm(opener.kind));
				$('#expenseAmt').text(__num2str(cutOff(__str2num(opener.maxPay) * __str2num(opener.rate) * 0.01)));
				$('#expenseRate').text('['+opener.rate+'%]');

				$('#fromDt').val(opener.from).attr('value1',opener.from);
				$('#toDt').val(opener.to).attr('value1',opener.to);
				$('#limitAmt').val(__num2str(opener.amt)).attr('value1',opener.amt);
			}
		},
		error: function (){
		}
	}).responseXML;
}


/*********************************************************

	수급자구분

*********************************************************/
function setKind(){
	$.ajax({
		type: "POST",
		url : "./client_pop_fun.php",
		data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 7
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);

			$('#expenseKind').text(__kindNm(val['kind']));
			$('#expenseAmt').text(__num2str(cutOff(__str2num($('#mgmtPay').text()) * val['rate'] * 0.01)));
			$('#expenseRate').text('['+val['rate']+'%]');
		},
		error: function (){
		}
	}).responseXML;
}

function initForm(){
	//초기화
	__init_form(document.f);

	var height = $(document).height();
	var top    = __getObjectTop(tblList);

	$("#tblList").height(height - top - 32);
}

function getDt(asFrom, asTo){
	var date  = new Date();
	var year  = date.getFullYear();
	var month = date.getMonth()+1;
		month = (month < 10 ? '0' : '')+month;
	var day   = date.getDate();
		day   = (day < 10 ? '0' : '')+day;
	var today = year+'-'+month+'-'+day;

	var fromDt = $('#fromDt').val();
	var toDt   = $('#toDt').val();
	var rst    = '';

	if (asFrom) fromDt = asFrom;
	if (asTo) toDt = asTo;

	if (!fromDt && !toDt){
		rst = today;
	}else if (!fromDt || !toDt){
		if (fromDt) rst = fromDt;
		else if (toDt) rst = toDt;
	}else{
		if (today > fromDt && today < toDt){
			rst = today;
		}else if (today <= fromDt){
			rst = fromDt;
		}else if (today >= toDt){
			rst = toDt;
		}
	}

	return rst;
}

function lfClose(){
	//if ($('#tblList tr').length == 0){
	//	opener.result = 1;
	//}else{
	//	opener.result = -1;
	//}
	opener.result = -1;

	if (opener.changeFlag == 1){
		opener.result = 1;
	}

	self.close();
}
</script>

<form id="f" name="f" method="post">

<div id="title" class="title title_border"></div>
<div id="body"></div>

</form>

<?
	include_once('../inc/_footer.php');
?>