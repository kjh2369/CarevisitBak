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
<script type="text/javascript" src="../longcare/longcare.js"></script>
<script type="text/javascript" src="./iljung.longcare.js"></script>
<script type="text/javascript" src="./iljung.longcare.result.js"></script>
<script type="text/javascript">
	var uploadWin = {};
	var uploadKey = 0;
	var sendMaxCnt = 0; resultCnt = 0;

	$(document).ready(function(){
		setTimeout('lfSearch()',200);
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./today_modify_upload_search.php'
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
				//lfUpload();
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
		var tr = $('tr',$('#tbodyList'));
		var cnt = 1;

		$(tr).each(function(){
			var id = $(this).attr('id');
			var jumin = $(this).attr('jumin');
			var appNo = $(this).attr('appNo');
			var level = $(this).attr('level');
			var subCd = $(this).attr('subCd');

			setTimeout('lfLongtermLoadSub("'+id+'","'+jumin+'","'+appNo+'","'+level+'","'+subCd+'")',cnt*3000);
			cnt++;
		});

		//업로드 타이머
		setTimeout('lfUpload()',5000);
	}

	function lfLongtermLoadSub(id,encodeJumin,appNo,level,subCd){
		var today = new Date();
		var YYMM = today.getFullYear()+(today.getMonth()+1 < 10 ? '0' : '')+(today.getMonth()+1);
		var longTermMgmtNo, longTermMgmtSeq, payCtrNo, svcKind, tgtDemoChasu;
		var parent = $('#'+id);
		var note = $('#note',parent);
		var jumin = '';

		//에러 플래그
		var IsError = false;
		var errorMsg = '';

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

				var addFlag = false;

				var selectCheck = $('input[type="checkbox"][name="selectCheck"]',data).val();
				var scIdx = selectCheck.indexOf('|'+$('#clientAppNo').text());

				if (scIdx >= 0) addFlag = true;

				//if ($('input[type=\'checkbox\'][name=\'selectCheck\']['+selCheck+'=\''+appNo+'\']', data).val()){
				if (addFlag){
					//longTermMgmtNo  = $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value2');
					//longTermMgmtSeq = $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value4');
					//jumin			= getHttpRequest('../inc/_ed_code.php?type=2&value='+encodeJumin);
					//payCtrNo		= $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value');

					var addInfo = selectCheck.split('|');

					longTermMgmtNo	= addInfo[1].split(' ').join('');
					longTermMgmtSeq	= addInfo[3].split(' ').join('');
					jumin			= addInfo[2].split(' ').join('');
					payCtrNo		= addInfo[0].split(' ').join('');
					tgtDemoChasu	= addInfo[4].split(' ').join('');

					//$(note).text(longTermMgmtNo+'/'+longTermMgmtSeq+'/'+payCtrNo);
				}else{
					IsError = true;
					errorMsg = '건보 로그인오류';
					return false;
				}
			}
		,	error:function(request, status, error){
				lfLongtermLoadSub(id,encodeJumin,appNo,level,subCd);
			}
		});

		if (IsError){
			$(note).text(errorMsg);
			return;
		}

		//수급자 계약번호
		var data = {};

		//첫일과 말일
		var firstDate	= YYMM+'01';
		var lastDate	= YYMM+getLastDay(__getDate(firstDate));

		//기존
		data['payCtrNo'] = payCtrNo;
		data['tgtJuminNo'] = jumin;
		data['longTermMgmtNo'] = longTermMgmtNo;
		data['longTermMgmtSeq'] = longTermMgmtSeq;
		data['tgtDemoChasu'] = tgtDemoChasu;
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
						errorMsg = '업로드 불가능1';
						return false;
					}

					if (tmpYm_s<=YYMM && tmpYm_e>=YYMM){
						//svcKind		= $('input[type=\'checkbox\'][name=\'selectCheck\']', $(this).parent()).attr('value4');
						//payCtrNo	= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value3');
						//tgtDemoChasu= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value5');

						//$(note).text('/'+longTermMgmtNo+'/'+longTermMgmtSeq+'/'+payCtrNo+'/'+svcKind+'/'+tgtDemoChasu);

						var selectCheck = $('input[type="checkbox"][name="selectCheck"]',$(this).parent()).val();
						var addInfo = selectCheck.split('|');

						svcKind		= addInfo[1].split(' ').join('');
						payCtrNo	= addInfo[2].split(' ').join('');
						tgtDemoChasu= addInfo[7].split(' ').join('');

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
			$(note).text(errorMsg);
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

		$(tr).each(function(){
			var tmpChk = $('#careJuminNo'+i,this).text(); //

			$('td[id^="lblPlan_"]',$('#tblLongterm')).attr('loadYn','N');

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
				var planCnt = 0;
				var day = 1;
				var objP = $('div[id="plan"]',parent);

				$(payDt).each(function(){
					var obj = $('#lblPlan_'+day, objP); //결과를 담을 곳
					var cnt = __str2num($(obj).attr('cnt')); //일자별 일정수
					var htm = $(obj).attr('htm');

					if (!htm) htm = '';
					if ($(this).attr('checked')){
						cnt ++;

						htm += ('?memNm1='+(careNm1 ? careNm1 : '')
							+	'&memCd1='+(careJuminNo1 ? careJuminNo1 : '')
							+	'&familyYn1='+(familyYn1 ? familyYn1 : '')
							+	'&familyRel1='+(familyRel1 ? familyRel1 : '')
							+	'&memNm2='+(careNm2 ? careNm2 : '')
							+	'&memCd2='+(careJuminNo2 ? careJuminNo2 : '')
							+	'&familyYn2='+(familyYn2 ? familyYn2 : '')
							+	'&familyRel2='+(familyRel2 ? familyRel2 : '')
							+	'&from='+(serviceTmFr ? serviceTmFr : '')
							+	'&to='+(serviceTmTo ? serviceTmTo : '')
							+	'&subCd='+(subCd ? subCd : '')
							+	'&sugaCd='+(sugaCd ? sugaCd : '')
							);
					}

					var link = '/'+cnt;
					var dayLoadYn = $(obj).attr('loadYn');

					if (cnt > 0){
						dayLoadYn = 'Y';
					}

					$(obj).attr('htm',htm).attr('cnt',cnt).attr('loadYn',dayLoadYn).html(link);

					day ++;
				});

				i ++;
			}
		});

		//일정비교
		//년월
		var lastday = getLastDay(__getDate(YYMM+'01'));
		var longtermRegShow = false;
		var today = getToday().split('-').join('');
		var objP = $('div[id="iljung"]',parent);

		//일정비교
		for(var i=1; i<=lastday; i++){
			var dt = YYMM+(i < 10 ? '0' : '')+i;
			var plan = $('div[id^="loCal_'+i+'_"]',objP); //기관계획
			var longterm = $('#lblPlan_'+i).attr('htm'); //공단계획

			if ($(plan).length == 0 && !longterm){
				continue;
			}

			var row = longterm.split('?');
			var err = true;
			var errPlan = false;
			var tmpKind = '';

			for(var j=0; j<row.length; j++){
				if (row[j]){
					var col = __parseVal(row[j]);

					$(plan).each(function(){
						var planNm1 = $(this).attr('memNm1'); //주요양보호사
						var planNm2 = $(this).attr('memNm2'); //부요양보호사
						var planFrom = $(this).attr('from').replace(':',''); //시작시간
						var planTo = $(this).attr('to').replace(':',''); //종료시간
						var planFamily = $(this).attr('ynFamily'); //가족여부
						var planSub = $(this).attr('svcKind');
						var planErr = true;

						tmpKind = planSub;
						/*
						var planStr11 =  planNm1 + planNm2;
						var planStr12 =  planNm2 + planNm1;
						var planStr21 =  col['memNm1'] + col['memNm2'];
						var planStr22 =  col['memNm2'] + col['memNm1'];
						 */
						var planStr11 =  $(this).attr('memCd1') + '_' + $(this).attr('memCd2');
						var planStr12 =  $(this).attr('memCd2') + '_' +  $(this).attr('memCd1');
						var planStr21 =  col['memCd1'] + '_' +  col['memCd2'];
						var planStr22 =  col['memCd2'] + '_' +  col['memCd1'];

						//alert(i+'\n'+planStr11+'\n'+planStr21+'\n'+planStr22+'\n'+planFrom+'/'+planTo+'\n'+col['from']+'/'+col['to']+'\n'+planSub+'/'+col['subCd']);

						if ((planStr11 == planStr21 || planStr11 == planStr22) &&
							planFrom == col['from'] &&
							planTo == col['to'] &&
							planSub == col['subCd']){
							err = false;
							return false;
						}
					});

					if (today > dt){
						$('#lblPlan_'+i).attr(tmpKind+'Yn',err ? 'Y' : 'N');
					}

					if (!err) break;
				}
			}

			if (err){
				var cnt = __str2num($('#lblPlan_'+i).attr('cnt'));

				if (cnt > 0){
					var link = '<span style="color:#FF0000; font-weight:'+(err && today > dt ? 'bold' : 'normal')+';">/'+$('#lblPlan_'+i).attr('cnt')+'</span>';
					$('#lblPlan_'+i).html(link);
				}
			}

			//계획기준의 오류 검사
			$(plan).each(function(){
				var planNm1 = $(this).attr('memNm1'); //주요양보호사
				var planNm2 = $(this).attr('memNm2'); //부요양보호사
				var planFrom = $(this).attr('from').replace(':',''); //시작시간
				var planTo = $(this).attr('to').replace(':',''); //종료시간
				var planFamily = $(this).attr('ynFamily'); //가족여부
				var planSub = $(this).attr('svcKind');
				var planSeq = $(this).attr('svcSeq');
				var err = true;

				tmpKind = planSub;

				for(var j=0; j<row.length; j++){
					var col = __parseVal(row[j]);
					/*
					var planStr11 =  planNm1 + planNm2;
					var planStr12 =  planNm2 + planNm1;
					var planStr21 =  col['memNm1'] + col['memNm2'];
					var planStr22 =  col['memNm2'] + col['memNm1'];
					*/
					var planStr11 = $(this).attr('memCd1') + '_' + $(this).attr('memCd2');
					var planStr12 = $(this).attr('memCd2') + '_' + $(this).attr('memCd1');
					var planStr21 = col['memCd1'] + '_' + col['memCd2'];
					var planStr22 = col['memCd2'] + '_' + col['memCd1'];

					//alert(i+'\n'+planStr11+'\n'+planStr21+'\n'+planStr22+'\n'+planFrom+'/'+planTo+'\n'+col['from']+'/'+col['to']+'\n'+planSub+'/'+col['subCd']);

					if ((planStr11 == planStr21 || planStr11 == planStr22) &&
						planFrom == col['from'] &&
						planTo == col['to'] &&
						planSub == col['subCd']){
						err = false;
						break;
					}
				}

				if (err){
					if ($(this).attr('ynBipay') != 'Y'){
						if ('<?=$dpDelYn;?>' == 'Y'){
							//계획삭제
							if (today > dt){
								$.ajax({
									type:'POST'
								,	async:false
								,	url:'./today_modify_plan_delete.php'
								,	data:{
										'jumin':encodeJumin
									,	'date':dt
									,	'time':planFrom
									,	'seq':planSeq
									}
								,	beforeSend:function(){
									}
								,	success:function(result){
										if (result == 1){
										}else if (result == 9){
											IsError = true;
											errorMsg = '업로드 불가능4';
											return false;
										}else{
											IsError = true;
											errorMsg = '업로드 불가능5';
											alert(result);
											return false;
										}
									}
								,	error:function(){
										IsError = true;
										errorMsg = '업로드 불가능r';
										return false;
									}
								}).responseXML;
							}
						}else{
							//업로드막기
							if (today > dt){
								$('#lblPlan_'+i).attr(tmpKind+'Yn',err ? 'Y' : 'N');
							}

							var link = '<span style="color:#FF0000; font-weight:'+(err && today > dt ? 'bold' : 'normal')+';">/'+$('#lblPlan_'+i).attr('cnt')+'</span>';
							$('#lblPlan_'+i).html(link);
						}
					}
				}
			});
		}

		if (IsError){
			$(note).text(errorMsg);
			return;
		}

		//업로드 가능여부를 판단한다.
		var objP = $('div[id="plan"]',parent);
		var plan = $('span[id^="lblPlan_"]',objP);

		$(plan).each(function(){
			var day = $(this).attr('id').split('lblPlan_').join('');
			if ($(this).attr(subCd+'Yn') == 'Y'){
				$(note).html('<span style="color:RED; font-weight:bold;">'+day+'일</span> 공단계획이 상이하여 업로드가 <span style="color:RED; font-weight:bold;">불가능함</span>');
				IsError = true;
				errorMsg = day+'일 공단계획이 상이하여 업로드가 불가능함';
				return false;
			}
		});

		if (!IsError){
			$(note).attr('upload','Y').html('<span style="color:BLUE;">업로드 가능합</span>');
		}else{
			$.ajax({
				type:'POST'
			,	async:false
			,	url:'./today_modify_error.php'
			,	data:{
					'jumin':encodeJumin
				,	'date':today
				,	'error':errorMsg
				}
			,	beforeSend:function(){
				}
			,	success:function(result){
					if (result == 1){
					}else if (result == 9){
					}else{
						alert(result);
					}
				}
			,	error:function(){
				}
			}).responseXML;
		}
	}

	function lfUpload(){
		var tr = $('tr',$('#tbodyList'));
		var max = 0;
		var cnt = 1;

		if (sendMaxCnt == 0){
			sendMaxCnt = $(tr).length;
		}

		$(tr).each(function(){
			var id = $(this).attr('id');
			var jumin = $(this).attr('jumin');
			var subCd = $(this).attr('subCd');
			var gbn = ''; //$(this).attr('reasonGbn');
			var str = ''; //$(this).attr('reasonStr');
			var IsUpload = $(this).attr('IsUpload');
			var IsError = ($('#note',this).attr('upload') == 'Y' ? 'N' : 'Y');

			if (IsUpload != 'Y'){
				//if (gbn != '04'){
				//	str = '';
				//}

				if (IsError != 'Y'){
					$(this).attr('IsUpload','Y');
					setTimeout('lfUploadPop(\''+id+'\',\''+jumin+'\',\''+subCd+'\',\''+gbn+'\',\''+str+'\')',cnt*3000);
					cnt ++;
					max ++;
				}
			}else{
				max ++;
			}
		});

		if (sendMaxCnt == max){
			setTimeout('lfClose()',10000);
		}else{
			setTimeout('lfUpload()',5000);
		}
	}

	function lfUploadPop(id,jumin,subCd,reasonGbn,reasonStr){
		var date = new Date();
		var year = date.getFullYear();
		var month = date.getMonth()+1;
			month = (month < 10 ? '0' : '')+month;
		var h = screen.availHeight;
		var w = screen.availWidth;
		var t = 0;

		if(w >= 800) w = 800;

		h = 200;

		uploadKey ++;

		var key = 'ROW_'+uploadKey;
		var option = 'left=0, top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
		var url = './iljung_planlist.php';
			uploadWin[key] = window.open('', 'ROW_'+id, option);
			uploadWin[key].opener = self;
			uploadWin[key].focus();

		reasonGbn = '';
		reasonStr = '';

		var parm = new Array();
			parm = {
				'jumin':jumin
			,	'yymm':year+month
			,	'svcKind':subCd
			,	'uploadYN':'Y'
			,	'chgSayu':reasonGbn
			,	'chgSayuEtc':reasonStr
			,	'id':id
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', 'ROW_'+id);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();

		//작업 진행 저장
		$.ajax({
			type:'POST'
		,	async:false
		,	url:'./today_modify_send.php'
		,	data:{
				'jumin':jumin
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

		resultCnt ++;
	}

	function lfClose(){
		if (sendMaxCnt > 0 && resultCnt > 0 && sendMaxCnt == resultCnt){
			self.close();
		}else{
			setTimeout('lfClose()',10000);
		}
	}

	window.onunload = function(){
		for(var i in uploadWin){
			//uploadWin[i].close();
		}

		opener.lfSearch();
	}
</script>
<div class="title title_border">변경내역 공단업로드 리스트</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="90px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자</th>
			<th class="head">서비스</th>
			<th class="head">횟수</th>
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