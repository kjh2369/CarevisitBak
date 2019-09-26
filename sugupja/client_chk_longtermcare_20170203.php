<?
	include_once('../inc/_login.php');
?>
<!--span id="lblMsg1">Loading...</span-->
<script type="text/javascript">
	$(document).ready(function(){
		//setTimeout('lfSvcLTC()',10);
	});

	function lfSvcLTCReg(){
		var loginYn = __longcareLoginYn();

		if (!loginYn){
			alert('건보로그인을 하여 주십시오.');
			return false;
		}

		var obj = lfChkLTCReg();

		if (obj){
			$('#name').val(obj['name']);

			obj['level'] = obj['level'].split(' ').join('');

			var fromDt = obj['from'].split('.').join('-');
			var toDt   = obj['to'].split('.').join('-');
			var lvlCd  = '';

			if (obj['level'] == '1등급'){
				lvlCd = '1';
			}else if (obj['level'] == '2등급'){
				lvlCd = '2';
			}else if (obj['level'] == '3등급'){
				lvlCd = '3';
			}else{
				lvlCd = '9';
			}

			var maxpay = getHttpRequest('../find/_find_maxpay.php?lvl='+lvlCd);

			lfSvcShow('0');

			$('#txtStat_11').text('이용').attr('value','1');
			$('#reasonTr_11').hide();
			$('#txtFrom_11').text(obj['from']).attr('value',fromDt);
			$('#txtTo_11').text(obj['to']).attr('value',toDt);

			$('#11_sugupStatus').val('1');
			$('#11_gaeYakFm').val(fromDt);
			$('#11_gaeYakTo').val(toDt);

			//장기요양보험
			$('#mgmtNo').attr('value',obj['no']).text(obj['no']);
			$('#mgmtFrom').attr('value',fromDt).text(obj['from']);
			$('#mgmtTo').attr('value',toDt).text(obj['to']);
			$('#mgmtLvl').attr('value',lvlCd).text(obj['level']);
			$('#mgmtPay').attr('value',maxpay).text(__num2str(maxpay));
			$('#mgmtSeq').attr('value','1');
		}
	}

	function lfSvcLTC(){
		if ($('#writeMode').val() == '1'){
			$('#lblMsg1').hide();
			return;
		}

		if ($('#client_request').css('display') != 'none'){
			$('#lblMsg1').hide();
			return false;
		}

		var loginYn = __longcareLoginYn();

		if (!loginYn){
			$('#lblMsg1').hide();
			$('#lcRequest').hide();
			$('#lcLayer').show();
			return false;
		}

		var obj = lfChkLTCReg();

		if (obj){
			$('#lblLCName').text(obj['name']);
			$('#lblLCLvl').text(obj['level']);
			$('#lblLCNo').text(obj['no']);
			$('#lblLCFrom').text(obj['from']);
			$('#lblLCTo').text(obj['to']);
		}

		$('#lcRequest').show();
		$('#lcLayer').hide();
	}

	function lfChkLTCReg(){
		var loginYn = __longcareLoginYn();

		if (!loginYn){
			alert('건보로그인을 하여 주십시오.');
			return false;
		}

		if ($('#writeMode').val() == '1'){
			var jumin = $('#jumin1').val()+$('#jumin2').val();
		}else{
			var jumin = getHttpRequest('../inc/_ed_code.php?type=2&value='+$('#jumin').val());
		}

		if (jumin.length != 13){
			return '';
		}

		var rst;

		$.ajax({
			type : 'POST'
		,	async: false
		,	url  : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=TR'
		,	data : {
				'longTermAdminSym'	: '<?=$_SESSION["userCenterGiho"];?>'
			,	'longTermAdminNm'	: ''
			,	'adminKindCd'		: 'C'
			,	'searchPayYyyy'		: '20%'
			,	'searchGbn'			: 'searchJuminNo'
			,	'searchValue'		: jumin
			,	'searchDt'			: 'searchCtrDt'
			,	'fnc'				: 'select'
			}
		,	beforeSend: function(){
			}
		,	success: function (data){
				/*
				if ($('input[type=\'checkbox\'][name=\'selectCheck\'][value3=\''+jumin+'\']', data).val()){
					var obj = $('input[type=\'checkbox\'][name=\'selectCheck\'][value3=\''+jumin+'\']', data).parent().parent();

					rst = {	'name'	:$('td',obj).eq(3).text()
						,	'level'	:$('td',obj).eq(2).text()
						,	'from'	:$('td',obj).eq(5).text()
						,	'to'	:$('td',obj).eq(6).text()
						,	'no'	:$('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value2')
						,	'seq'	:$('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value4')};
				}else{
					//alert('건보공단에서 수급자를 찾을 수 없습니다.\n\n\n- 건보공단에 수급자가 등록되어 있는지 확인하여 주십시오.');
				}
				*/

				var addFlag = false;
				var obj = $('input[type="checkbox"][name="selectCheck"]',data).parent().parent();
				var selectCheck = $('input[type="checkbox"][name="selectCheck"]',data).val();
				var scIdx = selectCheck.indexOf('|'+jumin);

				if (scIdx >= 0) addFlag = true;

				if (addFlag){
					var addInfo = selectCheck.split('|');

					rst = {	'name'	:$('td',obj).eq(3).text()
						,	'level'	:$('td',obj).eq(2).text()
						,	'from'	:$('td',obj).eq(5).text()
						,	'to'	:$('td',obj).eq(6).text()
						,	'no'	:addInfo[1].split(' ').join('')
						,	'seq'	:addInfo[3].split(' ').join('')};
				}

			}
		,	error: function (request, status, error){
				/*
				if (g_ErrorCnt == 0){
					g_ErrorCnt ++;
					_iljungGetLongTermMgmtNo(svcKind, uploadYN, paraNo, mgmtYn, juminNo, abAdmin);
				}else{
					alert('[ERROR No.03]'
						 +'\nCODE : ' + request.status
						 +'\nSTAT : ' + status
						 +'\nMESSAGE : ' + request.responseText);
				}
				*/

				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});

		return rst;
	}
</script>
<div id="lcRequest" class="my_border_blue" style="position:absolute; display:none; left:684px; top:154px; width:341px; height:55px; z-index:10; background-color:#ffffff;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="70px">
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">성명</th>
				<td class="left"><span id="lblLCName"></span></td>
				<th class="center">인정번호</th>
				<td class="left last">
					<span id="lblLCNo"></span>&nbsp;/
					<span id="lblLCLvl"></span>
				</td>
			</tr>
			<tr>
				<th class="center bottom">기간</th>
				<td class="left bottom last" colspan="3">
					<div style="float:left; width:auto;">
						<span id="lblLCFrom"></span>&nbsp;~
						<span id="lblLCTo"></span>
					</div>
					<div id="client_request" style="float:right; width:auto; margin-top:1px; margin-right:3px; display:none;">&nbsp;</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="lcLayer" class="my_border_blue" style="position:absolute; display:none; left:684px; top:154px; width:341px; height:55px; z-index:10; text-align:center; background-color:#ffffff;">
	<!--건보공단 로그인 후 새로고침을 클릭하여 주십시오.<br>
	<a href="#" onclick="lfSvcLTC(); return false;">새로고침</a>-->
</div>