<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$orgNm = $_SESSION['userCenterName'];
	$lastDay = $myF->lastDay(Date('Y'),Date('m'));

	//공단과 상이한 계획 삭제여부
	$dpDelYn = $_POST['dpDelYn'];
?>
<script type="text/javascript" src="../iljung/plan.js"></script>
<script type="text/javascript">
	var sendMaxCnt = 0; resultCnt = 0;

	$(document).ready(function(){
		setTimeout('lfSearch()',200);
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./today_modify_confirm_search.php'
		,	data:{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	complete:function(){
				lfLongtermLoad();
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	//공단 데이타 로드
	function lfLongtermLoad(){
		var tr = $('tr[id^="rowId_"]',$('#tbodyList'));
		var cnt = 1;

		$(tr).each(function(){
			var id = $(this).attr('id');
			var jumin = $(this).attr('jumin');
			var appNo = $(this).attr('appNo');
			var level = $(this).attr('level');
			var obj = $('td[id^="'+id+'"]',this);

			sendMaxCnt += __str2num($(this).attr('cnt'));

			$(obj).each(function(){
				var subCd = $(this).attr('subCd');

				setTimeout('lfLongtermLoadSub("'+id+'","'+jumin+'","'+appNo+'","'+level+'","'+subCd+'")',cnt*3000);
				cnt++;
			});
		});

		setTimeout('lfClose()',5000);
	}

	function lfLongtermLoadSub(id,encodeJumin,appNo,level,subCd){
		var today = new Date();
		var YYMM = today.getFullYear()+(today.getMonth()+1 < 10 ? '0' : '')+(today.getMonth()+1);
		var date = today.getDate();
		var longTermMgmtNo, longTermMgmtSeq, payCtrNo, svcKind, tgtDemoChasu;
		var parent = $('#'+id);
		var note = $('div[id^="'+id+'"]:first',parent);
		var jumin = '';

		var subKind = subCd;

		//수급자 인정번호
		$.ajax({
			type:'POST'
		,	async:false
		,	url:'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=TR' //TR->PR
		,	data:{
				'longTermAdminSym'	:'<?=$orgNo;?>'
			,	'longTermAdminNm'	:'<?=$orgNm;?>'
			,	'adminKindCd'		:'C'
			,	'searchPayYyyy'		:'20%'
			,	'searchGbn'			:'searchMgmtNo'
			,	'searchValue'		:appNo
			,	'searchDt'			:'searchCtrDt'
			,	'fnc'				:'select'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var selCheck = 'value2';

				if ($('input[type=\'checkbox\'][name=\'selectCheck\']['+selCheck+'=\''+appNo+'\']', data).val()){
					longTermMgmtNo  = $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value2');
					longTermMgmtSeq = $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value4');
					jumin			= getHttpRequest('../inc/_ed_code.php?type=2&value='+encodeJumin);
					payCtrNo		= $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value');

					//$(note).text(longTermMgmtNo+'/'+longTermMgmtSeq+'/'+payCtrNo);
				}else{
					IsError = true;
					$(note).text('건보 로그인오류');
					return false;
				}
			}
		,	error:function(request, status, error){
				lfLongtermLoadSub(id,encodeJumin,appNo,level,subCd);
			}
		});

		if (IsError){
			return;
		}

		//수급자 계약번호
		var data = {};

		//첫일과 말일
		var firstDate	= YYMM+'01';
		var lastDate	= YYMM+getLastDay(__getDate(firstDate));

		//에러 플래그
		var IsError = false;

		//기존
		data['payCtrNo'] = payCtrNo;
		data['tgtJuminNo'] = jumin;
		data['longTermMgmtNo'] = longTermMgmtNo;
		data['longTermMgmtSeq'] = longTermMgmtSeq;
		data['fnc'] = 'select';

		$.ajax({
			type:'POST'
		,	async:false
		,	url:'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=PR' //TR->PR
		,	data:data
		,	success:function(data){
				$('#npayTable td:nth-child(1)', data).each(function(){
					var tmpYYMM = $('td',$(this).parent()).eq(1).text().split('.').join('');
					var tmpTerm = $(this).text().replace(/[^0-9]/g, '');
					var tmpYm_s = tmpTerm.substr(0,6);
					var tmpYm_e = tmpTerm.substr(8,6);

					//계약 종료일자
					var tmpYmd = tmpTerm.substr(8);

					//계약기간
					if (tmpYmd > firstDate && tmpYmd < lastDate){
						IsError = true;
						return false;
					}

					if (tmpYm_s<=YYMM && tmpYm_e>=YYMM){
						svcKind		= $('input[type=\'checkbox\'][name=\'selectCheck\']', $(this).parent()).attr('value4');
						payCtrNo	= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value3');
						tgtDemoChasu= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value5');

						//$(note).text('/'+longTermMgmtNo+'/'+longTermMgmtSeq+'/'+payCtrNo+'/'+svcKind+'/'+tgtDemoChasu);

						if (svcKind = subKind){
							return false;
						}
					}
				});
			}
		,	error: function (request, status, error){
				$(note).text('계약번호 조회 오류');
				return false;
			}
		});

		if (IsError){
			$(note).text('업로드 불가능1');
			return;
		}

		var longtermResult;

		//일정조회
		$.ajax({
			type:'POST'
		,	async:false
		,	url:'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU'
		,	data:{
				'payCtrNo'			:payCtrNo
			,	'payMm'				:YYMM
			,	'longTermMgmtNo'	:longTermMgmtNo
			,	'longTermMgmtSeq'	:longTermMgmtSeq
			,	'tgtJuminNo'		:jumin
			,	'serviceKind'		:svcKind
			,	'adminDemoChasu'	:'01'
			,	'tgtDemoChasu'		:tgtDemoChasu
			,	'fnc'				:'select'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				longtermResult = result;
			}
		,	error:function(){
				$(note).text('업로드 불가능2');
				return;
			}
		}).responseXML;

		//계획비교
		var data = longtermResult.toString().split('<b>').join('').split('</b>').join('');
		var div = $('div[id="TableData3"]',data);
		var tbl = $('table:first',div);
		var tr = $('tr',tbl);
		var subCd = '';
		var i = 0;

		if (svcKind == '001'){
			subCd = '200';
		}else if (svcKind == '002'){
			subCd = '500';
		}else{
			subCd = '800';
		}

		var rstMsg = '';

		var obj = $('div[id^="'+id+'"]',parent);

		$(obj).each(function(){
			var from = $(this).attr('from');
			var to = $(this).attr('to');
			var memCd1 = getHttpRequest('../inc/_ed_code.php?type=2&value='+$(this).attr('memCd1'));
			var memNm1 = $(this).attr('memNm1');
			var memCd2 = getHttpRequest('../inc/_ed_code.php?type=2&value='+$(this).attr('memCd2'));
			var memNm2 = $(this).attr('memNm2');

			$(tr).each(function(){
				var tmpChk = $('#careJuminNo'+i,this).text(); //

				if (tmpChk){
					//B2300000 차량 60분 - CBKD1
					//B2380000 차랑 40분~60분미만 - CBKD1
					//B2400000 가정내 60분 - CBKD2
					//B2480000 가정내 40분~60분미만 - CBKD2
					//B2500000 미차랑 60분 - CBFD1
					//B2580000 미차랑 40분~60분미만 - CBFD1
					var qlfNo = $('#qlfNo'+i,this).text(); //요양요원자격증번호
					var sugaCd = $('#sugaCd'+i,this).text(); //수가코드
					var serviceTmFr = $('#serviceTmFr'+i,this).text(); //시작시간
					var serviceTmTo = $('#serviceTmTo'+i,this).text(); //종료시간
					var careJuminNo1 = getHttpRequest('../inc/_ed_code.php?type=1&value='+$('#careJuminNo'+i,this).text()); //직원
					var careNm1 = $('#careNm'+i,this).text(); //직원명
					var familyYn1 = ($('#familyYn1'+i,this).attr('checked') ? 'Y' : 'N'); //가족관계
					var familyRel1 = $('#_familyRel1'+i,this).val(); //가족관계 코드
					var careJuminNo2 = getHttpRequest('../inc/_ed_code.php?type=1&value='+$('#careJuminNo2'+i,this).text()); //직원
					var careNm2 = $('#careNm2'+i,this).text(); //직원명
					var familyYn2 = ($('#familyYn2'+i,this).attr('checked') ? 'Y' : 'N'); //가족관계
					var familyRel2 = $('#_familyRel2'+i,this).val(); //가족관계 코드
					var payDt = $('input:checkbox[name="payDt'+i+'"]',this);
					var day = 1;

					$(payDt).each(function(){
						if (date == day && $(this).attr('checked')){
							if (from || to){
								if (from != serviceTmFr || to != serviceTmTo){
									IsError = true;
									rstMsg = '서비스시간 오류';
									return false;
								}
							}

							if (memCd1){
								if (careJuminNo1 && memCd1 == careJuminNo1){
									if (memNm1 != careNm1){
										IsError = true;
										rstMsg = '요양보호사 오류';
									}
								}else if (careJuminNo2 && memCd1 == careJuminNo2){
									if (memNm1 != careNm2){
										IsError = true;
										rstMsg = '요양보호사 오류';
									}
								}else{
									IsError = true;
									rstMsg = '요양보호사 오류';
								}
							}

							if (memCd2){
								if (careJuminNo1 && memCd2 == careJuminNo1){
									if (memNm2 != careNm1){
										IsError = true;
										rstMsg = '요양보호사 오류';
									}
								}else if (careJuminNo2 && memCd2 == careJuminNo2){
									if (memNm2 != careNm2){
										IsError = true;
										rstMsg = '요양보호사 오류';
									}
								}else{
									IsError = true;
									rstMsg = '요양보호사 오류';
								}
							}
						}
						day ++;
					});

					i ++;

					if (IsError){
						return false;
					}
				}
			});

			if (!rstMsg){
				 rstMsg = 'OK';
			}

			$(this).css('color',IsError ? 'RED' : 'BLUE').text(rstMsg);

			if (!IsError){
				$.ajax({
					type:'POST'
				,	async:false
				,	url:'./today_modify_confirm_result.php'
				,	data:{
						'jumin':encodeJumin
					,	'time':$(this).attr('time')
					,	'seq':$(this).attr('seq')
					,	'idx':$(this).attr('idx')
					}
				,	beforeSend:function(){
					}
				,	success:function(result){
						if (result == 1){
						}else if (result == 9){
						}else{
						}
					}
				,	error:function(request, status, error){
						alert('[ERROR No.02]'
							 +'\nCODE : ' + request.status
							 +'\nSTAT : ' + status
							 +'\nMESSAGE : ' + request.responseText);
					}
				});
			}

			resultCnt ++;
		});
	}

	function lfClose(){
		if (sendMaxCnt > 0 && resultCnt > 0 && sendMaxCnt == resultCnt){
			self.close();
		}else{
			setTimeout('lfClose()',5000);
		}
	}

	window.onunload = function(){
		opener.lfSearch();
	}
</script>
<div class="title title_border">변경내역 공단일정확인 리스트</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="50px" span="2">
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자</th>
			<th class="head">서비스</th>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">담당자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center bottom" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>