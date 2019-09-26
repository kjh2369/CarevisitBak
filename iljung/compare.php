<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$year = Date('Y');
	$month = Date('m');
	$lastday = $myF->lastday($year,$month);
?>
<script type='text/javascript' src='./plan.js'></script>
<script type="text/javascript">
	var ltLoadYn = {};
	var ltDataYn = {};

	function lfMoveYear(pos){
		var year = __str2num($('#yymm').attr('year'));

		year += pos;

		$('#yymm').attr('year',year).text(year);
	}

	function lfSearch(month){
		$('div[id^="btnMonth_"]').removeClass('my_month_y').addClass('my_month_1');
		$('#btnMonth_'+month).removeClass('my_month_1').addClass('my_month_y');

		month = (__str2num(month) < 10 ? '0' : '')+__str2num(month);

		$('#yymm').attr('month',month);

		$.ajax({
			type:'POST'
		,	url:'./compare_search.php'
		,	data:{
				'year':$('#yymm').attr('year')
			,	'month':$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#btnLoadLT').attr('disabled',true);
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);

				var cnt = $('tr[id^="ROW_"][jumin!=""]').length;

				$('#lblTotal').text(cnt);
				$('#lblLongterm').text('0');
				$('#lblYetProc').text('0');
				$('#tempLodingBar').remove();
				$('#btnLoadLT').attr('disabled',false);
			}
		,	complete:function(){
				//공단조회
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfEvt(obj,gbn){
		var clr = '';
		var id = $(obj).attr('id');

		if (gbn == 'OVER'){
			clr = 'D9E5FF';
		}else{
			clr = $(obj).attr('backClr');
		}

		$('tr[id="'+id+'"]').css('background-color',clr);
	}

	function lfLongTerm(){
		var fn = new lfTimer();
		var no = 1;

		ltLoadYn = {};
		ltDataYn = {};

		$('#lblLongterm').text('0');
		$('#lblYetProc').text('0');

		$('#btnLoadLT').attr('disabled',true);

		$('tr[id^="ROW_"][jumin!=""]').each(function(){
			var rowId = $(this).attr('id');
			var jumin = $(this).attr('jumin');
			var appNo = $(this).attr('appNo');

			fn.setTimeout(this,rowId,jumin,appNo,no,no*100);
			no ++;
		});

		if (no > 1){
			setTimeout('lfChkTimer()',200);
		}else{
			$('#btnLoadLT').attr('disabled',false);
		}
	}

	function lfTimer(){
		this.setTimeout = function(obj,rowId,jumin,appNo,no,speed,val){
			 setTimeout(function(speed){lfLongTermMgmt(rowId,jumin,appNo,no)},speed);
		}
	}

	function lfChkTimer(){
		if (!ltLoadYn || !ltDataYn) return;

		var cnt = 0;

		for(var i in ltLoadYn){
			if (ltLoadYn[i] && ltDataYn[i]){
				if (!ltLoadYn[i]['001']) ltLoadYn[i]['001'] = 'N';
				if (!ltLoadYn[i]['002']) ltLoadYn[i]['002'] = 'N';
				if (!ltLoadYn[i]['003']) ltLoadYn[i]['003'] = 'N';

				if (ltLoadYn[i]['001'] != 'Y' && ltLoadYn[i]['002'] != 'Y' && ltLoadYn[i]['003'] != 'Y'){
					var obj = $('tr[id^="ROW_"][appNo="'+i+'"]');
					var id = $(obj).attr('id');
					var empty = true;

					$('tr[id="'+id+'"]').each(function(){
						$('td',this).each(function(){
							if ($(this).attr('para')){
								empty = false;
								return false;
							}
						});

						if (!empty) return false;
					});

					if (empty){
						ltLoadYn[i]['001'] = 'Y';
						ltLoadYn[i]['002'] = 'Y';
						ltLoadYn[i]['003'] = 'Y';

						ltDataYn[i]['001'] = 'Y';
						ltDataYn[i]['002'] = 'Y';
						ltDataYn[i]['003'] = 'Y';

						$('tr[id="'+id+'"][appNo="'+i+'"]').attr('compareYn','Y');
					}
					cnt ++;
				}else{
					var obj = $('tr[id^="ROW_"][appNo="'+i+'"]');

					if (ltLoadYn[i]['001'] == ltDataYn[i]['001'] &&
						ltLoadYn[i]['002'] == ltDataYn[i]['002'] &&
						ltLoadYn[i]['003'] == ltDataYn[i]['003'] &&
						$(obj).attr('compareYn') != 'Y'){

						var id = $(obj).attr('id');

						$(obj).attr('compareYn','Y');

						//$('#tmp',obj).text($('#tmp',obj).text()+'/'+$(obj).attr('compareYn'));

						lfCompare(id);

						cnt ++;
					}
				}
			}
		}

		$('#lblLongterm').text($('tr[id^="ROW_"][compareYn="Y"]').length);
		$('#lblYetProc').text($('tr[id^="ROW_"][compareYn="N"]').length);

		if ($('tr[id^="ROW_"][compareYn="Y"]').length > 0 && cnt == 0){
			if (!confirm('일정 및 공단비교가 완료되었습니다.\n오류일정만 보시겠습니까?')) return;
			lfRowHide();
		}else{
			setTimeout('lfChkTimer()',200);
		}
	}

	function lfRowHide(){
		$('tr[id^="ROW_"][appNo!=""]').each(function(){
			var id = $(this).attr('id');
			var empty = true;
			var error = false;

			$('tr[id="'+id+'"]').each(function(){
				$('td',this).each(function(){
					if ($(this).css('font-weight') == '700'){
						error = true;
					}
				});

				if (error) return false;
			});

			if (!error){
				$('tr[id="'+id+'"]').remove();
				$('tr[id="'+id.replace('ROW','LIN')+'"]').remove();
			}
		});

		var no = 1;

		$('tr[id^="ROW_"][appNo!=""]').each(function(){
			var id = $(this).attr('id');
			var color = 'FFFFFF';

			if (no % 2 == 0){
				color = 'EFEFEF';
			}

			$('td',this).eq(0).text(no);
			$('tr[id="'+id+'"]').attr('backClr',color).css('background-color','#'+color);

			no ++;
		});

		var id = $('tr:first',$('#tbodyList')).attr('id');

		if (id.substring(0,3) == 'LIN'){
			$('tr:first',$('#tbodyList')).remove();
		}
	}

	/*********************************************************

		수급자 인정번호

	*********************************************************/
	function lfLongTermMgmt(rowId,jumin,appNo,procNo){
		try{
			$.ajax({
				type : 'POST'
			,	url  : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=TR' //TR->PR
			,	data : {
					'longTermAdminSym'	: '<?=$_SESSION["userCenterGiho"];?>'
				,	'longTermAdminNm'	: '<?=$_SESSION["userCenterName"];?>'
				,	'adminKindCd'		: 'C'
				,	'searchPayYyyy'		: '20%'
				,	'searchGbn'			: 'searchMgmtNo'
				,	'searchValue'		: appNo
				,	'searchDt'			: 'searchCtrDt'
				,	'fnc'				: 'select'
				}
			,	beforeSend: function(){
				}
			,	success: function (data){
					var selCheck = 'value2';
					var addFlag = false;

					var selectCheck = $('input[type="checkbox"][name="selectCheck"]',data).val();
					var scIdx = selectCheck.indexOf('|'+$('#clientAppNo').text());

					if (scIdx >= 0) addFlag = true;

					//if ($('input[type=\'checkbox\'][name=\'selectCheck\'][value2=\''+appNo+'\']', data).val()){
					if (addFlag){
						//longTermMgmtNo  = $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value2');
						//longTermMgmtSeq = $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value4');
						//jumin			= getHttpRequest('../inc/_ed_code.php?type=2&value='+jumin);
						//payCtrNo		= $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value');

						var addInfo = selectCheck.split('|');

						longTermMgmtNo	= addInfo[1].split(' ').join('');
						longTermMgmtSeq	= addInfo[3].split(' ').join('');
						jumin			= addInfo[2].split(' ').join('');
						payCtrNo		= addInfo[0].split(' ').join('');

						if (!ltLoadYn[longTermMgmtNo]){
							 ltLoadYn[longTermMgmtNo] = {};
							 ltDataYn[longTermMgmtNo] = {'001':'N','002':'N','003':'N'};
						}

						lfPayCtrNo(rowId, jumin, longTermMgmtNo, longTermMgmtSeq, payCtrNo, procNo);
					}else{
						return false;
					}
				}
			,	error: function (request, status, error){

				}
			});
		}catch(e){
			alert('ERROR 01\n'+e);
		}
	}

	/*********************************************************

		수급자 계약번호 가져오기

	*********************************************************/
	function lfPayCtrNo(rowId, jumin, longTermMgmtNo, longTermMgmtSeq, payCtr, procNo){
		try{
			var YYMM		= $('#yymm').attr('year')+$('#yymm').attr('month')
			var payCtrNo	= '';
			var tmpCtrNo	= '';
			var arrCtrNo	= {'001':'','002':'','003':''};
			var data = {};

			data['payCtrNo'] = payCtr;
			data['tgtJuminNo'] = jumin;
			data['longTermMgmtNo'] = longTermMgmtNo;
			data['longTermMgmtSeq'] = longTermMgmtSeq;
			data['fnc'] = 'select';

			$.ajax({
				type : 'POST',
				url : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=PR',  //TR->PR
				data: data,
				success: function (data){
					$('#npayTable td:nth-child(1)', data).each(function(){
						var tmpYYMM = $('td',$(this).parent()).eq(1).text().split('.').join('');
						var tmpTerm = $(this).text().replace(/[^0-9]/g, '');
						var tmpYm_s = tmpTerm.substr(0,6);
						var tmpYm_e = tmpTerm.substr(8,6);

						//if (!tmpYYMM) tmpYYMM = YYMM;
						if (tmpYm_s<=YYMM && tmpYm_e>=YYMM /*&& tmpYYMM == YYMM*/){
							//svcKind		= $('input[type=\'checkbox\'][name=\'selectCheck\']', $(this).parent()).attr('value4');
							//payCtrNo	= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value3');
							//tgtDemoChasu= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value5');

							var selectCheck = $('input[type="checkbox"][name="selectCheck"]',$(this).parent()).val();
							var addInfo = selectCheck.split('|');

							svcKind		= addInfo[3].split(' ').join('');
							payCtrNo	= addInfo[2].split(' ').join('');
							tgtDemoChasu= addInfo[6].split(' ').join('');

							ltLoadYn[longTermMgmtNo][svcKind] = 'N';
							ltDataYn[longTermMgmtNo][svcKind] = 'Y';

							//if (!arrCtrNo[svcKind]){
								if (payCtrNo != arrCtrNo[svcKind]){
									arrCtrNo[svcKind]	= payCtrNo;
									timer1	= setTimeout('lfLoadData("'+rowId+'","'+YYMM+'","'+jumin+'","'+arrCtrNo[svcKind]+'","'+longTermMgmtNo+'","'+longTermMgmtSeq+'","'+svcKind+'","'+tgtDemoChasu+'","'+procNo+'")',10);
								}
							//}
						}
					});
				},
				error: function (request, status, error){
					if ('<?=$debug;?>' == '1'){
						alert('[ERROR No.02]'
							 +'\n' + longTermMgmtNo
							 +'\nCODE : ' + request.status
							 +'\nSTAT : ' + status
							 +'\nMESSAGE : ' + request.responseText);
					}
				}
			});
		}catch(e){
			alert('ERROR 02\n'+e);
		}
	}

	function lfLoadData(rowId, YYMM, jumin, payCtrNo, longTermMgmtNo, longTermMgmtSeq, svcKind, tgtDemoChasu, procNo){
		try{
			$.ajax({
				type: 'POST'
			,	url : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU'
			,	data: {
					'payCtrNo'			: payCtrNo
				,	'payMm'				: YYMM
				,	'longTermMgmtNo'	: longTermMgmtNo
				,	'longTermMgmtSeq'	: longTermMgmtSeq
				,	'tgtJuminNo'		: jumin
				,	'serviceKind'		: svcKind
				,	'adminDemoChasu'	: '01'
				,	'tgtDemoChasu'		: tgtDemoChasu
				,	'fnc'				: 'select'
				}
			,	beforeSend: function(){
				}
			,	success: function(result){
					//데이타처리
					lfChkPlan(rowId, result, svcKind, procNo);
				}
			,	complete:function(){
					ltLoadYn[longTermMgmtNo][svcKind] = 'Y';
				}
			,	error: function (){
				}
			}).responseXML;
		}catch(e){
			alert('ERROR 03\n'+e);
		}
	}

	function lfChkPlan(rowId, result, svcKind, procNo){
		var data = result.toString().split('<b>').join('').split('</b>').join('');
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
			//var qlfNo = $('#qlfNo'+i,this).text(); //
			var tmpObj = $('th[id^="careJuminNo"]:first',this);
			var tmpChk = $(tmpObj).text();

			if (tmpChk){
				i = $(tmpObj).attr('id').split('careJuminNo').join('');

				var sugaCd = $('#sugaCd'+i,this).text(); //수가코드
				var serviceTmFr = $('#serviceTmFr'+i,this).text(); //시작시간
				var serviceTmTo = $('#serviceTmTo'+i,this).text(); //종료시간
				//var careJuminNo1 = $('#careJuminNo'+i,this).text(); //직원
				var careJuminNo1 = getHttpRequest('../inc/_ed_code.php?type=1&value='+$('#careJuminNo'+i,this).text()); //직원
				var careNm1 = $('#careNm'+i,this).text(); //직원명
				var familyYn1 = $('#familyYn1'+i,this).text(); //가족관계
				var familyRel1 = $('#_familyRel1'+i,this).val(); //가족관계 코드
				//var careJuminNo2 = $('#careJuminNo2'+i,this).text(); //직원
				var careJuminNo2 = getHttpRequest('../inc/_ed_code.php?type=1&value='+$('#careJuminNo2'+i,this).text()); //직원
				var careNm2 = $('#careNm2'+i,this).text(); //직원명
				var familyYn2 = $('#familyYn2'+i,this).text(); //가족관계
				var familyRel2 = $('#_familyRel2'+i,this).val(); //가족관계 코드
				var payDt = $('input:checkbox[name="payDt'+i+'"]',this);
				var planCnt = 0;
				var day = 1;

				$(payDt).each(function(){
					var parent = $('#'+rowId);
					var obj = $('#'+rowId+'_LT_'+day); //결과를 담을 곳
					var cnt = __str2num($(obj).attr('cnt')); //일자별 일정수
					var htm = $(obj).attr('para');
					var str = '';

					if (!htm) htm = '';
					if ($(this).attr('checked')){
						str = ('?memNm1='+(careNm1 ? careNm1 : '')
							+	'&memCd1='+(careJuminNo1 ? careJuminNo1 : '')
							+	'&familyYn1='+(familyYn1 ? familyYn1 : '')
							+	'&familyRel1='+(familyRel1 ? familyRel1 : '')
							+	'&memNm2='+(careNm2 ? careNm2 : '')
							+	'&memCd2='+(careJuminNo2 ? careJuminNo2 : '')
							+	'&familyYn2='+(familyYn2 ? familyYn2 : '')
							+	'&familyRel2='+(familyRel2 ? familyRel2 : '')
							+	'&from='+(serviceTmFr ? serviceTmFr : '')
							+	'&to='+(serviceTmTo ? serviceTmTo : '')
							+	'&subCd='+(subCd ? subCd : ''));

						if (htm.indexOf(str) < 0){
							cnt ++;
							htm += str;
						}
					}

					var link = cnt;

					$(obj).attr('para',htm).attr('cnt',cnt).html(cnt);

					day ++;
				});

				i ++;
			}
		});

		//$('#lblProc').text(procNo);
	}

	function lfCompare(rowId){
		var year = $('#yymm').attr('year');
		var month = $('#yymm').attr('month');
		var lastday = getLastDay(year+'-'+month+'-01');
		var error = false;

		for(var i=1; i<=lastday; i++){
			var CV = $('#'+rowId+'_CV_'+i);
			var LT = $('#'+rowId+'_LT_'+i);

			if (!CV || !LT) continue;

			var errorYn1 = lfCompareSub(CV,LT);
			var errorYn2 = lfCompareSub(LT,CV);

			if (!error){
				if (errorYn1 || errorYn2){
					error = true;
				}
			}
		}

		if (error){
			$('tr[id^="'+rowId+'"]').attr('errorYn','Y');
		}
	}
	function lfCompareSub(obj1,obj2){
		var row1 = $(obj1).attr('para').split('?');
		var row2 = $(obj2).attr('para').split('?');
		var planErr = false;

		for(var j1=0; j1<row1.length; j1++){
			if (row1[j1]){
				var col1 = __parseVal(row1[j1]);
				var err = true;

				for(var j2=0; j2<row2.length; j2++){
					if (row2[j2]){
						var col2 = __parseVal(row2[j2]);
						var nmStr11 = col1['memCd1'] + '_' + col1['memCd2'];
						var nmStr12 = col1['memCd2'] + '_' + col1['memCd1'];
						var nmStr21 = col2['memCd1'] + '_' + col2['memCd2'];
						var nmStr22 = col2['memCd2'] + '_' + col2['memCd1'];

						if ((nmStr11 == nmStr21 || nmStr11 == nmStr22) &&
							col1['from'] == col2['from'] &&
							col1['to'] == col2['to'] &&
							col1['subCd'] == col2['subCd']){
							err = false;
							break;
						}
					}
				}

				if (err){
					$(obj1).css('color','#FF0000').css('font-weight','bold');
				}

				if (!planErr) planErr = err;
			}
		}

		return planErr;
	}
</script>
<div class="title title_border">일정비교(공단)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="left last">
				- 년월을 선택하시면 케어비지트일정을 조회 후 공단조회를 클릭하면 케어비지트와 공단의 일정을 비교할 수 있습니다.<br>
				- 수급자 월 일정 위는 케어비지트이고 아래는 공단일정입니다.<br>
				- 일정의 숫자는 일정수 이며 일정이 서로 상이하면 발간색으로 표시됩니다.<br>
				- 완료되었다는 메세지가 나오기 전까지는 작업중이므로 기다려 주십시오.<br>
			</td>
		</tr>
		<tr>
			<td class="last">
				<div style="float:left; width:auto;">
					<div class="left" style="width:auto; margin-top:1px;">
						<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
						<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="yymm" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
						<div style="float:left; width:auto; padding-top:2px; margin-right:5px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
					</div>
					<div style="width:auto;"><?=$myF->_btn_month($month,'lfSearch(');?></div>
				</div>
				<div style="float:left; width:auto; margin-left:5px; padding-top:2px;">
					<span class="btn_pack m"><button id="btnLoadLT" onclick="lfLongTerm();">공단조회</button></span>
				</div>
				<div style="float:right; width:auto;">
					<span style="font-weight:bold;">전체 : </span>
					<span id="lblTotal" style="font-weight:bold;">0</span> /
					<span style="font-weight:bold;">공단 : </span>
					<span id="lblLongterm" style="font-weight:bold; color:#0000FF;">0</span> /
					<span style="font-weight:bold;">미처리 : </span>
					<span id="lblYetProc" style="font-weight:bold; color:#FF0000;">0</span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table id="tblCompare" class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="80px">
		<col width="40px">
		<col width="20px">
		<col width="21px" span="<?=$lastday;?>">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자</th>
			<th class="head">등급</th>
			<th class="head"><span style="font-size:9px; line-height:1em;">구<br>분</span></th><?
			for($i=1; $i<=31; $i++){
				if ($i == 31){
					$cls = 'last';
				}else{
					$cls = '';
				}?>
				<th class="head <?=$cls;?>"><?=$i;?></th><?
			}?>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="50">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>