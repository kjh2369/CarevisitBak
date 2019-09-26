<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->fetch_type = 'assoc';

	$code    = $_POST['code'];
	$jumin   = $_POST['jumin'];
	$svcCd   = $_POST['svcCd'];
	$svcKind = $_POST['svcKind'];
	$year    = $_POST['year'];
	$month   = $_POST['month'];
	$day     = $_POST['day'];
	$from    = $_POST['from'];
	$to      = $_POST['to'];
	$seq     = $_POST['seq'];
	$cNm     = $_POST['cNm'];
	$memCd1  = trim($_POST['memCd1']);
	$memNm1  = trim($_POST['memNm1']);
	$memCd2  = trim($_POST['memCd2']);
	$memNm2  = trim($_POST['memNm2']);
	$sugaNm  = $_POST['sugaNm'];
	$suga    = $_POST['suga'];

	$tmpSvcCd = $_POST['tmpSvcCd'];

	$sudangPay  = $_POST['sudangPay'];
	$sudangKind = $_POST['sudangKind'];
	$sudangVal1 = $_POST['sudangVal1'];
	$sudangVal2 = $_POST['sudangVal2'];

	$svcVal  = $_POST['svcVal'];
	$svcLvl  = $_POST['svcLvl'];
	$svcCost = $_POST['svcCost'];

	$ynHoliday = $_POST['ynHoliday'];
	$ynBipay   = $_POST['ynBipay'];
	$ynSave    = $_POST['ynSave'];
	$stat      = $_POST['stat'];
	$id        = $_POST['id'];

	$request = $_POST['request'];

	$ynClose  = $_POST['ynClose'];
	$ynSalary = $_POST['ynSalary'];

	$type = $_POST['type'];

	$modifyPos = $_POST['modifyPos'];

	//if ($type == 'CONF'){
	//	$ynClose = 'Y';
	//}

	$today = date('Ymd');

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	if (!is_numeric($memCd1)) $memCd1 = $ed->de($memCd1);
	if (!is_numeric($memCd2)) $memCd2 = $ed->de($memCd2);

	if ($type == 'PLAN'){
		//과거 및 오늘일정까지는 계획 수정을 막는다.
		if ($svcCd == '0'){
			if ($today >= $year.$month.$day){
				$type = 'CONF';
			}
		}
	}

	if ($svcCd == '0'){
		//가족 요양보호사
		$sql = 'select cf_mem_cd
				  from client_family
				 where org_no   = \''.$code.'\'
				   and cf_jumin = \''.$jumin.'\'
				 limit 1';
		$lsMemCd = $conn->get_data($sql);

		//동거 조건
		$sql = 'select m03_yoyangsa1 as mem_cd
				,      m03_partner as partner_yn
				,      m03_stat_nogood as stat_yn
				  from m03sugupja
				 where m03_ccode = \''.$code.'\'
				   and m03_mkind = \''.$svcCd.'\'
				   and m03_jumin = \''.$jumin.'\'';
		$row = $conn->get_array($sql);

		//$lsMemCd  = $row['mem_cd'];
		$liMemAge = $myF->issToAge($lsMemCd1);

		$ynPartner = $row['partner_yn']; //주요양보호사 배우자 여부
		$ynStatNot = $row['stat_yn'];    //상태이상여부

		//if ($ynPartner != 'Y'){
		//	$lsMemCd = '';
		//}
	}else{
		$ynPartner = 'N';
		$ynStatNot = 'N';
	}

	//직원 수당정보
	if ($svcKind == '500' || $svcKind == '800'){
		$sql = 'SELECT extra500_1 as B3
				,      extra500_2 as B1
				,      extra500_3 as B2
				,      extra800_1 as N1
				,      extra800_2 as N2
				,      extra800_3 as N3
				  FROM mem_extra
				 WHERE org_no = \''.$code.'\'';

		$sl = $sql.' AND jumin = \''.$memCd1.'\'';
		$laExtraMem1 = $conn->get_array($sl);

		if ($svcKind == '500'){
			$sl = $sql.' AND jumin = \''.$memCd2.'\'';
			$laExtraMem2 = $conn->get_array($sl);
		}

		//기관수당
		$sql = 'SELECT m21_mcode2 as cd
				,      m21_svalue as val
				  FROM m21sudang
				 WHERE m21_mcode = \''.$code.'\'';
		$laExtraCenter = $conn->_fetch_array($sql,'cd');
	}
?>
<script type="text/javascript" src="./plan.js"></script>
<form id="f" name="f" method="post">
<div class="title title_border">
	<div style="float:left; width:auto;">계획정보</div>
	<div style="float:right; width:auto; margin-top:9px; margin-right:5px;"><?
		if ($type == 'PLAN'){?>
			<span class="btn_pack m"><button id="btnPlan" type="button" stat="1">변경</button></span>
			<span class="btn_pack m" style="display:none;"><button id="btnPlanCancel" type="button">취소</button></span><?
		}?>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="130px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left">고객명</th>
			<td class="left" id="lblCNm"></td>
			<th class="left">담당요양보호사</th>
			<td id="lblPlanMem">
				<div style="float:left; width:auto;"><span id="lblPlanMem1" code="" style="margin-left:5px; margin-right:3px;"></span></div>
				<div style="float:left; width:auto;"><img id="imgFindPlanMem1" src="../image/btn_find.png" class="clsMem2" style="margin-top:1px;" onclick="lfMemFind('Plan','1');"></div>
				<div style="float:left; width:auto;"><span id="lblPlanTmp1" class="clsMem2" style="margin-left:5px;">/</span></div>
				<div style="float:left; width:auto;"><span id="lblPlanMem2" code="" class="clsMem2" style="margin-right:5px;"></span></div>
				<div style="float:left; width:auto;"><img id="imgFindPlanMem2" src="../image/btn_find.png" class="clsMem2" style="margin-top:1px;" onclick="lfMemFind('Plan','2');"></div>
				<!--input id="txtPlanMemCd1" name="txtMemCd" type="text" value="" code="" style="width:55px; cursor:default;" alt="not" readonly><span class="clsMem2">/</span>
				<input id="txtPlanMemCd2" name="txtMemCd" type="text" value="" code="" class="clsMem2" style="width:55px; cursor:default;" alt="not" readonly-->
			</td>
		</tr>
		<tr>
			<th class="left">일자</th>
			<td class="left" id="lblPlanDt"></td>
			<th class="left">시간</th>
			<td id="lblPlanTime">
				<div id="grpPlanTimeLbl" style="width:auto;">
					<span id="lblPlanFrom" style="margin-left:5px;"></span>
					<span style="margin-left:5px;">~</span>
					<span id="lblPlanTo" style="margin-right:5px;"></span>
				</div>
				<div id="grpPlanTimeTxt" style="width:auto; display:none;">
					<input id="txtPlanFromH" name="txtPlanTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">:<input id="txtPlanFromM" name="txtPlanTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">~<input id="txtPlanToH" name="txtPlanTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">:<input id="txtPlanToM" name="txtPlanTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">
				</div>
			</td>
		</tr>
		<tr>
			<th class="left">수가명</th>
			<td class="left" id="lblPlanSuga" code=""></td>
			<th class="left">금액</th>
			<td class="left" id="lblPlanSugaCost" value="0"></td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	//변경, 저장버튼
	$('#btnPlan').unbind('click').bind('click',function(){
		if ($(this).attr('stat') == '1'){
			if ($('#btnConf').attr('stat') == '2'){
				$('#btnConfCancel').click();
			}

			$(this).attr('stat','2').text('저장');
			$('#btnPlanCancel').parent().show();
			$('#imgFindPlanMem1').show();
			$('#grpPlanTimeLbl').hide();
			$('#grpPlanTimeTxt').show();

			if (lfSubMemYn()){
				$('#lblPlanTmp1').show();
				$('#lblPlanMem2').show();
				$('#imgFindPlanMem2').show();
			}

			lfFindSuga('Plan');
		}else if ($(this).attr('stat') == '2'){
			lfPlanSave();
			$('#btnPlanCancel').click();
		}
	});

	//계획 변경 취소
	$('#btnPlanCancel').unbind('click').bind('click',function(){
		$('#btnPlan').attr('stat','1').text('변경');
		$('#btnPlanCancel').parent().hide();
		$('#imgFindPlanMem1').hide();
		$('#grpPlanTimeLbl').show();
		$('#grpPlanTimeTxt').hide();
		$('#imgFindPlanMem2').hide();

		if (lfSubMemYn()){
			$('#lblPlanTmp1').show();
			$('#lblPlanMem2').show();
		}else{
			$('#lblPlanTmp1').hide();
			$('#lblPlanMem2').hide();
		}

		lfFindSuga('Plan'
				,$('#lblPlanFrom').text()
				,$('#lblPlanTo').text());
	});

	//계획저장
	function lfPlanSave(){
		if ($('#txtMsg').attr('ynDuplicate') == 'Y'){
			alert('중복된 일정은 적용할 수 없습니다.');
			return;
		}

		if (__str2num($('#lblPlanSugaCost').text()) <= 0){
			alert('등록할 수 없는 수가입니다. 확인 후 다시 시도하여 주십시오.');
			return;
		}

		if (!confirm('계획내역이 변경됩니다.\n저장하시겠습니까?')) return;

		var lsSugaNm  = $('#loSuga').attr('name');
		var lsSvcCd   = $('#val').attr('svcCd');
		var lsSvcKind = $('#val').attr('svcKind');

		if ($('#loSuga').attr('ynHoliday') == 'Y'){
			if (lsSvcCd == '0'){
				if (lsSvcKind != '500'){
					lsSugaNm += '(<span style=\'color:#ff0000;\'>30%</span>)';
				}
			}
		}

		var liExtraPay = 0;
		var lsExtraKind = '';
		var liExtraVal1 = 0
		,	liExtraVal2 = 0;

		if (lsSvcKind == '500' || lsSvcKind == '800'){
			lsExtraKind = $('input:radio[name="opt"]:checked').val();
		}

		switch(lsSvcKind){
			case '500':
				switch(lsExtraKind){
					case 'PERSON':
						liExtraPay = 0;
						break;

					default:
						liExtraPay = __str2num($('#txtBathPay').val());

						if (lsExtraKind == 'AMT'){
							liExtraVal1 = __str2num($('#txtBathPay1').val());
							liExtraVal2 = __str2num($('#txtBathPay2').val());
						}else{
							liExtraVal1 = __str2num($('#txtBathRate1').val());
							liExtraVal2 = __str2num($('#txtBathRate2').val());
						}
				}
				break;

			case '800':
				switch(lsExtraKind){
					case 'PERSON':
						liExtraPay = 0;
						break;

					default:
						liExtraPay  = __str2num($('#txtNursePay').val());
						liExtraVal1 = 0;
						liExtraVal2 = 0;
				}
				break;

			default:
				lsExtraKind = '';
		}

		$('#'+$('#val').attr('objId'),opener.document)
			.attr('from',$('#txtPlanFromH').val()+$('#txtPlanFromM').val()/*$('#val').attr('from')*/)
			.attr('to',$('#txtPlanToH').val()+$('#txtPlanToM').val()/*$('#val').attr('to')*/)
			.attr('memCd1',$('#val').attr('memCd1'))
			.attr('memNm1',$('#val').attr('memNm1'))
			.attr('memCd2',$('#val').attr('memCd2'))
			.attr('memNm2',$('#val').attr('memNm2'))
			.attr('duplicate','N')
			.attr('sugaName',$('#loSuga').attr('name'))
			.attr('sugaCd',$('#loSuga').attr('code'))
			.attr('sugaNm',lsSugaNm)
			.attr('procTime',$('#loSuga').attr('procTime'))
			.attr('cost',$('#loSuga').attr('cost'))
			.attr('costEvening',$('#loSuga').attr('costEvening'))
			.attr('costNight',$('#loSuga').attr('costNight'))
			.attr('costTotal',$('#loSuga').attr('costTotal'))
			.attr('timeEvening',$('#loSuga').attr('timeEvening'))
			.attr('timeNight',$('#loSuga').attr('timeNight'))
			.attr('ynNight',$('#loSuga').attr('ynNight'))
			.attr('ynEvening',$('#loSuga').attr('ynEvening'))
			.attr('ynFamily',$('#val').attr('ynFamily'))
			.attr('sudangPay',liExtraPay)
			.attr('sudangKind',lsExtraKind)
			.attr('sudangVal1',liExtraVal1)
			.attr('sudangVal2',liExtraVal2)
			.attr('ynSave','N');

		var lsTime = $('#txtPlanFromH').val()+':'+$('#txtPlanFromM').val()+'~'+$('#txtPlanToH').val()+':'+$('#txtPlanToM').val();
		var lsMem  = $('#val').attr('memNm1')+($('#val').attr('memNm2') ? '/'+$('#val').attr('memNm2') : '');
		var lsSuagHtml = '<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;">'+lsSugaNm+'</div>';

		switch(lsSvcKind){
			case '500':
				lsSuagHtml += '<div style="float:left; width:auto;"><img src="../image/icon_bath.png" style="width:15px; height:14px;"></div>';
				break;

			case '800':
				lsSuagHtml += '<div style="float:left; width:auto;"><img src="../image/icon_nurs.png" style="width:15px; height:14px;"></div>';
				break;
		}

		$('#lblTimeStr', $('#'+$('#val').attr('objId'),opener.document)).text(lsTime);
		$('#lblMemStr',  $('#'+$('#val').attr('objId'),opener.document)).text(lsMem);
		$('#lblSugaStr', $('#'+$('#val').attr('objId'),opener.document)).html(lsSuagHtml);
		$('#divErrorMsg',$('#'+$('#val').attr('objId'),opener.document)).text('');

		$('#lblPlanFrom').text($('#txtPlanFromH').val()+':'+$('#txtPlanFromM').val());
		$('#lblPlanTo').text($('#txtPlanToH').val()+':'+$('#txtPlanToM').val());

		$.ajax({
			type : 'POST'
		,	url  : './plan_pop_save_plan.php'
		,	data : {
				'code'		: $('#val').attr('code')
			,	'jumin'		: $('#val').attr('jumin')
			,	'svcCd'		: $('#val').attr('svcCd')
			,	'date'		: $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
			,	'time'		: $('#val').attr('fTime').split(':').join('')
			,	'seq'		: $('#val').attr('seq')
			,	'type'		: 'PLAN'
			,   'svcKind'	: $('#val').attr('svcKind')
			,	'date'		: $('#lblConfDt').text().split('.').join('')
			,	'from'		: $('#txtPlanFromH').val()+$('#txtPlanFromM').val()
			,	'to'		: $('#txtPlanToH').val()+$('#txtPlanToM').val()
			,	'procTime'	: $('#loSuga').attr('procTime')
			,	'memCd1'	: $('#val').attr('memCd1')
			,	'memNm1'	: $('#val').attr('memNm1')
			,	'memCd2'	: $('#val').attr('memCd2')
			,	'memNm2'	: $('#val').attr('memNm2')
			,	'sugaCd'	: $('#loSuga').attr('code')
			,	'sugaCost'	: $('#loSuga').attr('cost')
			,	'sugaECost'	: $('#loSuga').attr('costEvening')
			,	'sugaNCost'	: $('#loSuga').attr('costNight')
			,	'sugaTCost'	: $('#loSuga').attr('costTotal')
			,	'sugaETime'	: $('#loSuga').attr('timeEvening')
			,	'sugaNTime'	: $('#loSuga').attr('timeNight')
			,	'sudangPay'	: liExtraPay
			,	'sudangKind': lsExtraKind
			,	'sudangVal1': liExtraVal1
			,	'sudangVal2': liExtraVal2
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');

					var obj = $('#'+$('#val').attr('objId'), opener.document);
					//$('div[id="btnRemove"]', $(obj)).html('<img src="../image/img_key.jpg" onclick="" style="margin-top:3px; width:15px; height:14px;" alt="실적이 등록되었습니다.">');

					if ($('#val').attr('svcKind') == '500'){
						$(obj).attr('sudangPay', liExtraPay)
							  .attr('sudangKind', $('input:radio[name="opt"]:checked').val())
							  .attr('sudangVal1', ($('input:radio[name="opt"]:checked').val() == 'RATE' ? __str2num($('#txtBathRate1').val()) : __str2num($('#txtBathPay1').val())))
							  .attr('sudangVal2', ($('input:radio[name="opt"]:checked').val() == 'RATE' ? __str2num($('#txtBathRate2').val()) : __str2num($('#txtBathPay2').val())));
					}else if ($('#val').attr('svcKind') == '800'){
						$(obj).attr('sudangPay', liExtraPay)
							  .attr('sudangKind', $('input:radio[name="opt"]:checked').val())
							  .attr('sudangVal1', __str2num($('#txtNursePay').val()))
							  .attr('sudangVal2', '0');
					}

					//$(obj).attr('stat','1').attr('ynSave','Y');
					opener.lfCalClean(3);
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}
</script>

<div class="title title_border">
	<div style="float:left; width:auto;">실적정보</div>
	<div style="float:right; width:auto; margin-top:9px; margin-right:5px;">
		<span class="btn_pack m" style="display:none;"><button id="btnConfDel" type="button">삭제</button></span>
		<span class="btn_pack m"><button id="btnConf" type="button" stat="1">변경</button></span>
		<span class="btn_pack m" style="display:none;"><button id="btnConfCancel" type="button">취소</button></span>
	</div>
</div>
<table id="tblResult" class="my_table clsResult" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="155px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left">일자</th>
			<td class="left" id="lblConfDt" value=""></td>
			<th class="left">요양보호사1</th>
			<td class="left">
				<div style="float:left; width:auto;"><span id="lblConfMem1" code="" style="margin-right:3px;"></span></div>
				<div style="float:left; width:auto;"><img id="imgFindConfMem1" src="../image/btn_find.png" class="clsMem2" onclick="lfMemFind('Conf','1');"></div>
				<!--input id="txtConfMemCd1" name="txtMemCd" type="text" value="" code="" class="clsConf" style="width:55px; cursor:default;" alt="not" readonly>
				<span id="lblConfMemStr1"></span-->
			</td>
		</tr>
		<tr>
			<th class="left">시간</th>
			<td class="left">
				<div id="grpConfTimeLbl" style="width:auto;">
					<span id="lblConfTime"></span>
				</div>
				<div id="grpConfTimeTxt" style="width:auto; display:none;">
					<input id="txtConfFromH" name="txtConfTime" type="text" value="" class="no_string clsConf" maxlength="2" style="width:25px; text-align:center; margin-left:0;"><span class="clsConf">:</span><input id="txtConfFromM" name="txtConfTime" type="text" value="" class="no_string clsConf" maxlength="2" style="width:25px; text-align:center;"><span class="clsConf">~</span><input id="txtConfToH" name="txtConfTime" type="text" value="" class="no_string clsConf" maxlength="2" style="width:25px; text-align:center;"><span class="clsConf">:</span><input id="txtConfToM" name="txtConfTime" type="text" value="" class="no_string clsConf" maxlength="2" style="width:25px; text-align:center;">
					<span id="lblConfTimeStr"></span>
				</div>
			</td>
			<th class="left">요양보호사2</th>
			<td class="left">
				<div style="float:left; width:auto;"><span id="lblConfMem2" code="" style="margin-right:3px;"></span></div>
				<div style="float:left; width:auto;"><img id="imgFindConfMem2" src="../image/btn_find.png" class="clsMem2" onclick="lfMemFind('Conf','2');"></div>
				<!--input id="txtConfMemCd2" name="txtMemCd" type="text" value="" code="" class="clsMem2 clsConf" style="width:55px; cursor:default;" alt="not" readonly>
				<span id="lblConfMemStr2"></span-->
			</td>
		</tr>
		<tr>
			<th class="left">수가명</th>
			<td class="left" id="lblConfSuga" code=""></td>
			<th class="left">금액</th>
			<td class="left" id="lblConfSugaCost" value="0"></td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	//실적변경, 저장버튼
	$('#btnConf').unbind('click').bind('click',function(){
		if ($(this).attr('stat') == '1'){
			if ($('#btnPlan').attr('stat') == '2'){
				$('#btnPlanCancel').click();
			}

			$(this).attr('stat','2').text('저장');
			$('#btnConfCancel').parent().show();
			$('#imgFindConfMem1').show();
			$('#lblConfTime').hide();
			$('#grpConfTimeLbl').hide();
			$('#grpConfTimeTxt').show();

			if (lfSubMemYn()){
				$('#imgFindConfMem2').show();
			}

			if (!$('#txtConfFromH').val() ||
				!$('#txtConfFromM').val() ||
				!$('#txtConfToH').val() ||
				!$('#txtConfToM').val()){
				$('#txtConfFromH').val($('#txtPlanFromH').val());
				$('#txtConfFromM').val($('#txtPlanFromM').val());
				$('#txtConfToH').val($('#txtPlanToH').val());
				$('#txtConfToM').val($('#txtPlanToM').val());
			}

			if (!$('#lblConfMem1').attr('code')){
				 $('#lblConfMem1').attr('code',$('#lblPlanMem1').attr('code')).text($('#lblPlanMem1').text());
			}
			if (!$('#lblConfMem2').attr('code')){
				 $('#lblConfMem2').attr('code',$('#lblPlanMem2').attr('code')).text($('#lblPlanMem2').text());
			}

			if ($('#val').attr('stat') == '1'){
				$('#btnConfDel').parent().show();
			}

			lfFindSuga('Conf');
		}else if ($(this).attr('stat') == '2'){
			lfConfSave();
			$('#btnConfCancel').click();
		}
	});

	//실적 변경 취소
	$('#btnConfCancel').unbind('click').bind('click',function(){
		$('#btnConf').attr('stat','1').text('변경');
		$('#btnConfCancel').parent().hide();
		$('#btnConfDel').parent().hide();
		$('#imgFindConfMem1').hide();
		$('#imgFindConfMem2').hide();
		$('#lblConfTime').show();
		$('#grpConfTimeLbl').show();
		$('#grpConfTimeTxt').hide();

		if ($('#val').attr('stat') != '1'){
			$('#lblConfMem1').attr('code','').text('');
			$('#lblConfMem2').attr('code','').text('');
		}

		var time = $('#lblConfTime').text().split('~');
			time[0] = time[0] ? time[0].split(' ').join('') : '0000';
			time[1] = time[1] ? time[1].split(' ').join('') : '0000';

		try{
			lfFindSuga('Conf',time[0],time[1]);
		}catch(e){
		}
	});

	//실적삭제
	$('#btnConfDel').unbind('click').bind('click',function(){
		lfConfDelete();
		$('#btnConfCancel').click();
	});

	function lfConfSave(){
		if (__str2num($('#lblConfSugaCost').text()) <= 0){
			alert('등록할 수 없는 수가입니다. 확인 후 다시 시도하여 주십시오.');
			return;
		}

		if (!confirm('실적내역이 변경됩니다.\n저장하시겠습니까?')) return;

		var lsSugaNm  = $('#loSuga').attr('name');
		var lsSvcCd   = $('#val').attr('svcCd');
		var lsSvcKind = $('#val').attr('svcKind');

		if ($('#loSuga').attr('ynHoliday') == 'Y'){
			if (lsSvcCd == '0'){
				if (lsSvcKind != '500'){
					lsSugaNm += '(<span style=\'color:#ff0000;\'>30%</span>)';
				}
			}
		}

		var liExtraPay = 0;
		var lsExtraKind = '';
		var liExtraVal1 = 0
		,	liExtraVal2 = 0;

		if (lsSvcKind == '500' || lsSvcKind == '800'){
			lsExtraKind = $('input:radio[name="opt"]:checked').val();
		}

		switch(lsSvcKind){
			case '500':
				switch(lsExtraKind){
					case 'PERSON':
						liExtraPay = 0;
						break;

					default:
						liExtraPay = __str2num($('#txtBathPay').val());

						if (lsExtraKind == 'AMT'){
							liExtraVal1 = __str2num($('#txtBathPay1').val());
							liExtraVal2 = __str2num($('#txtBathPay2').val());
						}else{
							liExtraVal1 = __str2num($('#txtBathRate1').val());
							liExtraVal2 = __str2num($('#txtBathRate2').val());
						}
				}
				break;

			case '800':
				switch(lsExtraKind){
					case 'PERSON':
						liExtraPay = 0;
						break;

					default:
						liExtraPay  = __str2num($('#txtNursePay').val());
						liExtraVal1 = 0;
						liExtraVal2 = 0;
				}
				break;

			default:
				lsExtraKind = '';
		}

		$('#lblConfTime').text($('#txtConfFromH').val()+':'+$('#txtConfFromM').val()+' ~ '+$('#txtConfToH').val()+':'+$('#txtConfToM').val());

		var lsTime = $('#lblConfTime').text().split(' ').join('');
		var lsMem  = $('#lblConfMem1').text()+($('#lblConfMem2').text() ? '/'+$('#lblConfMem2').text() : '');
		var lsSuagHtml = '<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;">'+lsSugaNm+'</div>';

		$('#lblTimeStr', $('#'+$('#val').attr('objId'),opener.document)).text(lsTime);
		$('#lblMemStr',  $('#'+$('#val').attr('objId'),opener.document)).text(lsMem);
		$('#lblSugaStr', $('#'+$('#val').attr('objId'),opener.document)).html(lsSuagHtml);
		$('#divErrorMsg',$('#'+$('#val').attr('objId'),opener.document)).text('');

		$.ajax({
			type : 'POST'
		,	async: false
		,	url  : './plan_pop_save_conf.php'
		,	data : {
				'code'		: $('#val').attr('code')
			,	'jumin'		: $('#val').attr('jumin')
			,	'svcCd'		: $('#val').attr('svcCd')
			,	'date'		: $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
			,	'time'		: $('#val').attr('fTime').split(':').join('')
			,	'seq'		: $('#val').attr('seq')
			,	'type'		: 'CONF'
			,   'svcKind'	: $('#val').attr('svcKind')
			,	'date'		: $('#lblConfDt').text().split('.').join('')
			,	'from'		: $('#txtConfFromH').val()+$('#txtConfFromM').val()
			,	'to'		: $('#txtConfToH').val()+$('#txtConfToM').val()
			,	'procTime'	: $('#loSuga').attr('procTime')
			,	'memCd1'	: $('#val').attr('memCd1')
			,	'memNm1'	: $('#val').attr('memNm1')
			,	'memCd2'	: $('#lblConfMem2').attr('code')//$('#val').attr('memCd2')
			,	'memNm2'	: $('#lblConfMem2').text()//$('#val').attr('memNm2')
			,	'sugaCd'	: $('#loSuga').attr('code')
			,	'sugaCost'	: $('#loSuga').attr('cost')
			,	'sugaECost'	: $('#loSuga').attr('costEvening')
			,	'sugaNCost'	: $('#loSuga').attr('costNight')
			,	'sugaTCost'	: $('#loSuga').attr('costTotal')
			,	'sugaETime'	: $('#loSuga').attr('timeEvening')
			,	'sugaNTime'	: $('#loSuga').attr('timeNight')
			,	'sudangPay'	: liExtraPay
			,	'sudangKind': lsExtraKind
			,	'sudangVal1': liExtraVal1
			,	'sudangVal2': liExtraVal2
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');

					var obj = $('#'+$('#val').attr('objId'), opener.document);
					var tmp = $('#val').attr('objId').split('_');

					if ('<?=$type;?>' == 'PLAN'){
						$('div[id="btnRemove"]', $(obj)).html('<img src="../image/img_key.jpg" onclick="" style="margin-top:3px; width:15px; height:14px;" alt="실적이 등록되었습니다.">'
															+' <img src="../image/btn_close.gif" onclick="lfRemoveWork(\''+tmp[1]+'\',\''+tmp[2]+'\');" style="margin-top:3px;">');
					}

					if ($('#val').attr('svcKind') == '500'){
						$(obj).attr('sudangPay', liExtraPay)
							  .attr('sudangKind', $('input:radio[name="opt"]:checked').val())
							  .attr('sudangVal1', ($('input:radio[name="opt"]:checked').val() == 'RATE' ? __str2num($('#txtBathRate1').val()) : __str2num($('#txtBathPay1').val())))
							  .attr('sudangVal2', ($('input:radio[name="opt"]:checked').val() == 'RATE' ? __str2num($('#txtBathRate2').val()) : __str2num($('#txtBathPay2').val())));
					}else if ($('#val').attr('svcKind') == '800'){
						$(obj).attr('sudangPay', liExtraPay)
							  .attr('sudangKind', $('input:radio[name="opt"]:checked').val())
							  .attr('sudangVal1', __str2num($('#txtNursePay').val()))
							  .attr('sudangVal2', '0');
					}

					$(obj).attr('stat','1').attr('ynSave','Y');
					$('#val').attr('stat','1');
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	function lfConfDelete(){
		if (!confirm('실적내역을 삭제하시면 복구할 수 없습니다.\n정말로 실적내역을 삭제하시겠습니까?')) return;

		$.ajax({
			type : 'POST'
		,	async: false
		,	url  : './plan_pop_del_conf.php'
		,	data : {
				'code'		: $('#val').attr('code')
			,	'jumin'		: $('#val').attr('jumin')
			,	'svcCd'		: $('#val').attr('svcCd')
			,	'date'		: $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
			,	'time'		: $('#val').attr('fTime').split(':').join('')
			,	'seq'		: $('#val').attr('seq')
			,	'type'		: 'CONF'
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');

					var obj = $('#'+$('#val').attr('objId'), opener.document);
					var tmp = $('#val').attr('objId').split('_');

					$('div[id="btnRemove"]', $(obj)).html('<img src="../image/btn_close.gif" onclick="lfRemoveWork(\''+tmp[1]+'\',\''+tmp[2]+'\');" style="margin-top:3px;">');
					$(obj).attr('stat','9').attr('ynSave','N');
					$('#val').attr('stat','9');
					$('#lblConfTime').text('');
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}
</script>

<div id="loExtrapay" style="display:none;">
	<div class="title title_border">수당정보</div>
	<div id="loExtraMem1" B3="<?=$laExtraMem1['B3'];?>" B2="<?=$laExtraMem1['B2'];?>" B1="<?=$laExtraMem1['B1'];?>" N1="<?=$laExtraMem1['N1'];?>" N2="<?=$laExtraMem1['N2'];?>" N3="<?=$laExtraMem1['N3'];?>" style="display:none;"></div>
	<div id="loExtraMem2" B3="<?=$laExtraMem2['B3'];?>" B2="<?=$laExtraMem2['B2'];?>" B1="<?=$laExtraMem2['B1'];?>" N1="<?=$laExtraMem2['N1'];?>" N2="<?=$laExtraMem2['N2'];?>" N3="<?=$laExtraMem2['N3'];?>" style="display:none;"></div>
	<div id="loExtraCenter" <?
	if (Is_Array($laExtraCenter)){
		foreach($laExtraCenter as $laVal){
			echo $laVal['cd'].'="'.$laVal['val'].'" ';
		}
	}?> style="display:none;"></div><?
	if ($svcKind == '500'){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="50px">
				<col width="30px">
				<col width="80px">
				<col width="50px">
				<col width="50px" span="2">
				<col width="135px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center" rowspan="2">개별</th>
					<th class="center">주</th>
					<td class="center"><div id="lblBathPay1" class="right" style="font-weight:bold;">0</div></td>
					<td class="center" rowspan="2" onclick="$('#optPerson').attr('checked','checked').click();"><input id="optPerson" name="opt" type="radio" value="PERSON" class="radio"></td>
					<th class="center" rowspan="3">기관</th>
					<th class="center">수당</th>
					<td colspan="2"><input id="txtBathPay" name="txtBathPay" type="text" value="0" class="number" style="width:50px;" onchange="_planExtraPayChk(this);"></td>
				</tr>
				<tr>
					<th class="center">부</th>
					<td class="center"><div id="lblBathPay2" class="right" style="font-weight:bold;">0</div></td>
					<th class="center">비율</th>
					<td>
						<input id="txtBathRate1" name="txtBathRate" type="text" value="0" class="number" style="width:30px;" onchange="_planExtraPayChk(this);">% /
						<input id="txtBathRate2" name="txtBathRate" type="text" value="0" class="number" style="width:30px;" onchange="_planExtraPayChk(this);">%
					</td>
					<td class="center" onclick="$('#optRate').attr('checked','checked').click();"><input id="optRate" name="opt" type="radio" value="RATE" class="radio"></td>
				</tr>
				<tr>
					<td class="bottom" colspan="4"></td>
					<th class="center">금액</th>
					<td>
						<input id="txtBathPay1" name="txtBathPay" type="text" value="0" class="number" style="width:50px;" onchange="_planExtraPayChk(this);"> /
						<input id="txtBathPay2" name="txtBathPay" type="text" value="0" class="number" style="width:50px;" onchange="_planExtraPayChk(this);">
					</td>
					<td class="center" onclick="$('#optAmt').attr('checked','checked').click();"><input id="optAmt" name="opt" type="radio" value="AMT" class="radio"></td>
				</tr>
			</tbody>
		</table><?
	}else if ($svcKind == '800'){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="50px">
				<col width="70px">
				<col width="30px">
				<col width="50px">
				<col width="70px">
				<col width="30px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center">개별</th>
					<td class="center"><div id="lblNursePay" class="right" style="font-weight:bold;">0</div></td>
					<td class="center" onclick="$('#optPerson').attr('checked','checked').click();"><input id="optPerson" name="opt" type="radio" value="PERSON" class="radio"></td>
					<th class="center">기관</th>
					<td class="center"><input id="txtNursePay" name="txtNursePay" type="text" value="0" class="number" style="width:100%;" onchange=""></td>
					<td class="center" onclick="$('#optAmt').attr('checked','checked').click();"><input id="optAmt" name="opt" type="radio" value="AMT" class="radio"></td>
					<td class="center"></td>
				</tr>
			</tbody>
		</table><?
	}?>
</div>

<div class="title title_border">기타</div>
<div id="txtMsg" ynDuplicate="N" style="padding:5px; overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
<div style="text-align:center; height:30px; padding-top:4px; border-top:1px solid #cccccc;"><?
	if ($debug){?>
		<span class="btn_pack m"><button type="button" onclick="location.reload();">새로고침</button></span><?
	}?>
	<!--span id="btnApply" class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span><span id="btnSave" class="btn_pack m"><button type="button" onclick="lfSave();">저장</button></span-->
	<span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span>
</div>
<div id="val" style="display:none;"
	code="<?=$code;?>"
	jumin="<?=$ed->en($jumin);?>"
	svcCd="<?=$svcCd;?>"
	svcKind="<?=$svcKind;?>"
	year="<?=$year;?>"
	month="<?=$month;?>"
	day="<?=$day;?>"
	from="<?=$from;?>"
	to="<?=$to;?>"
	fTime="<?=$from;?>"
	seq="<?=$seq;?>"
	cNm="<?=$cNm;?>"
	memCd1="<?=$ed->en($memCd1);?>"
	memNm1="<?=$memNm1;?>"
	memCd2="<?=$ed->en($memCd2);?>"
	memNm2="<?=$memNm2;?>"
	sugaNm="<?=$sugaNm;?>"
	suga="<?=$suga;?>"

	sudangPay="<?=$sudangPay;?>"
	sudangKind="<?=$sudangKind;?>"
	sudangVal1="<?=$sudangVal1;?>"
	sudangVal2="<?=$sudangVal2;?>"

	svcVal="<?=$svcVal;?>"
	svcLvl="<?=$svcLvl;?>"
	svcCost="<?=$svcCost;?>"

	familyMemCd="<?=$ed->en($lsMemCd);?>"
	ynHoliday="<?=$ynHoliday;?>"
	ynBipay="<?=$ynBipay;?>"
	ynPartner="<?=$ynPartner;?>"
	ynStatNot="<?=$ynStatNot;?>"
	ynFamily="N"
	ynSave="<?=$ynSave;?>"
	stat="<?=$stat;?>"
	ynClose="<?=$ynClose;?>"
	ynSalary="<?=$ynSalary;?>"
	modifyPos="<?=$modifyPos;?>"
	objId="<?=$id;?>"></div><?
include('./plan_suga_obj.php');?>
</form>
<script type="text/javascript">
	//담당요양보호사
	$('input:text[name="txtMemCd"]').unbind('click').click(function(){
		var lsType = $(this).attr('id').substring(3,7);
		var lsIdx  = $(this).attr('id').substring($(this).attr('id').length-1,$(this).attr('id').length);

		lfMemFind(lsType,lsIdx)
	});

	//계획시간
	$('input:text[name="txtPlanTime"]').unbind('keyup').keyup(function(){
		$('#btnResultSet').unbind('click').text('');

		if ($(this).val().length == $(this).attr('maxlength')){
			//시간 초과시 변경
			if ($(this).attr('id') == 'txtPlanFromH' || $(this).attr('id') == 'txtPlanToH'){
				var liVal = __str2num($(this).val());

				if (liVal >= 24){
					liVal = liVal % 24;
				}

				liVal = (liVal < 10 ? '0' : '')+liVal;

				$(this).val(liVal);
			}

			//분 초과시 변경
			if ($(this).attr('id') == 'txtPlanFromM' || $(this).attr('id') == 'txtPlanToM'){
				var liVal = __str2num($(this).val());

				if (liVal >= 60){
					liVal = 0;
				}

				liVal = (liVal < 10 ? '0' : '')+liVal;

				$(this).val(liVal);
			}

			if ($(this).attr('id') == 'txtPlanFromH'){
				$('#txtPlanFromM').focus();
				return;
			}else if ($(this).attr('id') == 'txtPlanFromM'){
				$('#txtPlanToH').val('').focus();
				return;
			}else if ($(this).attr('id') == 'txtPlanToH'){
				$('#txtPlanToM').val('').focus();
				return;
			}else if ($(this).attr('id') == 'txtPlanToM'){
				return;
			}
		}
	}).unbind('change').change(function(){
	}).unbind('focus').focus(function(){
	}).unbind('blur').blur(function(){
		var lsSvcCd   = $('#val').attr('svcCd');
		var lsSvcKind = $('#val').attr('svcKind');

		if ($(this).val() == ''){
			$(this).val('00');
		}else if ($(this).val().length == 1){
			$(this).val('0'+$(this).val());
		}

		if ($(this).attr('id') == 'txtPlanFromH' || $(this).attr('id') == 'txtPlanFromM'){
			$('#val').attr('from',$('#txtPlanFromH').val()+':'+$('#txtPlanFromM').val());
		}else if ($(this).attr('id') == 'txtPlanToH' || $(this).attr('id') == 'txtPlanToM'){
			if ($(this).attr('id') == 'txtPlanToM'){
				lfSetEndTime('Plan');
			}
			$('#val').attr('to',$('#txtPlanToH').val()+':'+$('#txtPlanToM').val());
		}

		if ($('#txtPlanFromH').val().length == $('#txtPlanFromH').attr('maxlength') &&
			$('#txtPlanFromM').val().length == $('#txtPlanFromM').attr('maxlength') &&
			$('#txtPlanToH').val().length == $('#txtPlanToH').attr('maxlength') &&
			$('#txtPlanToM').val().length == $('#txtPlanToM').attr('maxlength')){
			lfFindSuga('Plan');
			lfDuplicate('Plan');
		}
	});

	function lfSetSuga(){
		if ($('#txtPlanFromH').val().length == $('#txtPlanFromH').attr('maxlength') &&
			$('#txtPlanFromM').val().length == $('#txtPlanFromM').attr('maxlength') &&
			$('#txtPlanToH').val().length == $('#txtPlanToH').attr('maxlength') &&
			$('#txtPlanToM').val().length == $('#txtPlanToM').attr('maxlength')){

			lfFindSuga('Plan');
			lfDuplicate('Plan');
		}
	}

	//실적시간
	$('input:text[name="txtConfTime"]').unbind('keyup').keyup(function(){
		if ($(this).val().length == $(this).attr('maxlength')){
			//시간 초과시 변경
			if ($(this).attr('id') == 'txtConfFromH' || $(this).attr('id') == 'txtConfToH'){
				var liVal = __str2num($(this).val());

				if (liVal >= 24){
					liVal = liVal % 24;
				}

				liVal = (liVal < 10 ? '0' : '')+liVal;

				$(this).val(liVal);
			}

			//분 초과시 변경
			if ($(this).attr('id') == 'txtConfFromM' || $(this).attr('id') == 'txtConfToM'){
				var liVal = __str2num($(this).val());

				if (liVal >= 60){
					liVal = 0;
				}

				liVal = (liVal < 10 ? '0' : '')+liVal;

				$(this).val(liVal);
			}

			if ($(this).attr('id') == 'txtConfFromH'){
				$('#txtConfFromM').focus();
				return;
			}else if ($(this).attr('id') == 'txtConfFromM'){
				$('#txtConfToH').val('').focus();
				return;
			}else if ($(this).attr('id') == 'txtConfToH'){
				$('#txtConfToM').val('').focus();
				return;
			}else if ($(this).attr('id') == 'txtConfToM'){
				return;
			}
		}
	}).unbind('change').change(function(){
	}).unbind('focus').focus(function(){
	}).unbind('blur').blur(function(){
		var lsSvcCd   = $('#val').attr('svcCd');
		var lsSvcKind = $('#val').attr('svcKind');

		if ($(this).val() == ''){
			$(this).val('00');
		}else if ($(this).val().length == 1){
			$(this).val('0'+$(this).val());
		}

		if ($(this).attr('id') == 'txtConfFromH' || $(this).attr('id') == 'txtConfFromM'){
			$('#val').attr('from',$('#txtConfFromH').val()+':'+$('#txtConfFromM').val());
		}else if ($(this).attr('id') == 'txtConfToH' || $(this).attr('id') == 'txtConfToM'){
			$('#val').attr('to',$('#txtConfToH').val()+':'+$('#txtConfToM').val());
		}

		if ($('#txtConfFromH').val().length == $('#txtConfFromH').attr('maxlength') &&
			$('#txtConfFromM').val().length == $('#txtConfFromM').attr('maxlength') &&
			$('#txtConfToH').val().length == $('#txtConfToH').attr('maxlength') &&
			$('#txtConfToM').val().length == $('#txtConfToM').attr('maxlength')){
			lfFindSuga('Conf');
			lfDuplicate('Conf');
		}
	});

	$('#btnResultSet').unbind('click').click(function(){
		$('#btnResultSet').hide();
		$('#tblResult').show();

		$('#txtPlanMemCd1').hide();
		$('#txtPlanMemCd2').hide();
		$('#txtPlanFromH').hide();
		$('#txtPlanFromM').hide();
		$('#txtPlanToH').hide();
		$('#txtPlanToM').hide();

		$('#txtConfMemCd1').attr('code',$('#txtPlanMemCd1').attr('code')).val($('#txtPlanMemCd1').val());
		$('#txtConfMemCd2').attr('code',$('#txtPlanMemCd2').attr('code')).val($('#txtPlanMemCd2').val());
		$('#txtConfFromH').val($('#txtPlanFromH').val());
		$('#txtConfFromM').val($('#txtPlanFromM').val());
		$('#txtConfToH').val($('#txtPlanToH').val());
		$('#txtConfToM').val($('#txtPlanToM').val());

		$('#val').attr('from',$('#txtConfFromH').val()+':'+$('#txtConfFromM').val());
		$('#val').attr('to',$('#txtConfToH').val()+':'+$('#txtConfToM').val());

		$('#lblPlanMem').addClass('left').text($('#val').attr('memNm1')+($('#val').attr('memNm2') ? '/'+$('#val').attr('memNm2') : ''));
		$('#lblPlanTime').addClass('left').text($('#val').attr('from')+'~'+$('#val').attr('to'));

		$('#btnApply').hide();

		if ($('#val').attr('ynClose') != 'Y') $('#btnSave').show();

		lfFindSuga('Conf');
		lfSetOtherHeight();
	});

	//수당구분
	$('#txtExtraGbn').unbind('change').change(function(){
		if ($(this).val() == 'rate'){
			$('#txtExtraPay1').css('width','30px').val($('#txtExtraPay1').attr('rate'));
			$('#txtExtraPay2').css('width','30px').val($('#txtExtraPay2').attr('rate'));
			$('.clsRate').show();
		}else{
			$('#txtExtraPay1').css('width','50px').val($('#txtExtraPay1').attr('amt'));
			$('#txtExtraPay2').css('width','50px').val($('#txtExtraPay2').attr('amt'));
			$('.clsRate').hide();
		}
	});

	//수당분리
	$('input:text[name="txtExtraPR"]').unbind('change').change(function(){
		var lsExtraGbn = $('#txtExtraGbn').val();
		var liTxt = 0;
		var liVal = 0;

		liVal = $(this).val();

		if (lsExtraGbn == 'rate'){
			if (liVal > 100) liVal = 100;
			liTxt = 100 - liVal;
		}else{
			if (liVal > __str2num($('#txtExtraBath').val())) liVal = __str2num($('#txtExtraBath').val());
			liTxt = __str2num($('#txtExtraBath').val()) - liVal;
		}

		if ($(this).attr('id') == 'txtExtraPay1'){
			$('#txtExtraPay1').val(liVal);
			$('#txtExtraPay2').val(liTxt);
		}else{
			$('#txtExtraPay1').val(liTxt);
			$('#txtExtraPay2').val(liVal);
		}
	});

	//수당선택
	$('input:radio[name="opt"]').unbind('click').click(function(){
		//alert($(this).val());
	});

	$(document).ready(function(){
		$('#lblCNm').text($('#val').attr('cNm'));
		$('#lblPlanDt').text($('#val').attr('year')+'.'+$('#val').attr('month')+'.'+$('#val').attr('day'));

		$('#txtPlanMemCd1').attr('code',$('#val').attr('memCd1')).val($('#val').attr('memNm1'));
		$('#txtPlanMemCd2').attr('code',$('#val').attr('memCd2')).val($('#val').attr('memNm2'));
		$('#lblPlanMem1').attr('code',$('#val').attr('memCd1')).text($('#val').attr('memNm1'));
		$('#lblPlanMem2').attr('code',$('#val').attr('memCd2')).text($('#val').attr('memNm2'));

		var liTime = $('#val').attr('from').split(':').join('');

		$('#txtPlanFromH').val(liTime.substring(0,2));
		$('#txtPlanFromM').val(liTime.substring(2,4));
		$('#lblPlanFrom').text(liTime.substring(0,2)+':'+liTime.substring(2,4));

		var liTime = $('#val').attr('to').split(':').join('');

		$('#txtPlanToH').val(liTime.substring(0,2));
		$('#txtPlanToM').val(liTime.substring(2,4));
		$('#lblPlanTo').text(liTime.substring(0,2)+':'+liTime.substring(2,4));

		$('#lblPlanSuga').html($('#val').attr('sugaNm'));
		$('#lblPlanSugaCost').text(__num2str($('#val').attr('suga')));

		//가족여부
		lfYnFamily();

		//수가조회
		lfFindSuga('Plan');

		var lbMem2 = false;

		if ($('#val').attr('svcKind') == '500'){
			lbMem2 = true;
		}else if ($('#val').attr('svcCd') == '4' && $('#val').attr('svcKind') == '200'){
			lbMem2 = true;
		}

		if (!lbMem2){
			$('.clsMem2').hide();
		}else{
			if (lfSubMemYn()){
				$('#imgFindPlanMem1').hide();
				$('#imgFindPlanMem2').hide();
			}
		}

		var lsStat = $('#val').attr('stat');

		//if (lsStat == '1'){
		//	$('#txtPlanMemCd1').hide();
		//	$('#txtPlanMemCd2').hide();
		//	$('#txtPlanFromH').hide();
		//	$('#txtPlanFromM').hide();
		//	$('#txtPlanToH').hide();
		//	$('#txtPlanToM').hide();

		//	$('#lblPlanMem').addClass('left').text($('#val').attr('memNm1')+($('#val').attr('memNm2') ? '/'+$('#val').attr('memNm2') : ''));
		//	$('#lblPlanTime').addClass('left').text($('#val').attr('from')+'~'+$('#val').attr('to'));

		//	$('#btnApply').hide();
		//}else{
		//	$('#btnSave').hide();
		//}

		if ($('#val').attr('seq') != ''){
			lfGetCalendarConf();

		//	if (lsStat == '1'){
		//		lfFindSuga('Conf');
		//		$('#btnResultSet').hide();
		//	}else{
		//		lfFindSuga('Plan');
		//		$('#tblResult').hide();
		//	}
		}else{
			lfGetCalendarMem();
		//	lfFindSuga('Plan');
		//	$('.clsResult').hide();
		}

		//if ($('#val').attr('ynSave') == 'N'){
		//	$('#btnResultSet').unbind('click').text('');
		//}

		//수당입력
		if ($('#val').attr('ynSalary') != 'Y'){
			if ($('#val').attr('svcKind') == '500'){
				var liExtraPay = __str2num($('#val').attr('sudangPay'));

				$('#loExtrapay').show();
				$('#loExtraBath').show();
				$('#loExtraNurse').hide();

				if ($('#val').attr('sudangKind') == 'rate'){
					$('.clsRate').show();
				}else{
					$('.clsRate').hide();
				}
			}else if ($('#val').attr('svcKind') == '800'){
				$('#loExtrapay').show();
				$('#loExtraBath').hide();
				$('#loExtraNurse').show();
			}
		}else{
			$('#txtExtraBath').hide();
			$('#txtExtraGbn').hide();
			$('#txtExtraPay1').hide();
			$('#txtExtraPay2').hide();
			$('#txtExtraNurse').hide();

			if ($('#val').attr('svcKind') == '500'){
				$('#loExtrapay').show();
				$('#loExtraBath').show();
				$('#loExtraNurse').hide();

				var lsExtraKindMark = '';

				if ($('#val').attr('sudangKind') == 'rate'){
					lsExtraKindMark = '%';
				}

				$('#lblExtraBath').addClass('left').text(__num2str($('#val').attr('sudangPay')));
				$('#lblExtraGbn').addClass('left').text($('#val').attr('sudangKind') == 'rate' ? '비율' : '금액');
				$('#lblExtraPay').addClass('left').text(__num2str($('#val').attr('sudangVal1'))+lsExtraKindMark+' / '+__num2str($('#val').attr('sudangVal2'))+lsExtraKindMark);
			}else if ($('#val').attr('svcKind') == '800'){
				$('#loExtrapay').show();
				$('#loExtraBath').hide();
				$('#loExtraNurse').show();

				$('#lblExtraNurse').addClass('left').text(__num2str($('#val').attr('sudangPay')));
			}
		}

		lfSetOtherHeight();

		__init_form(document.f);

		self.focus();
	});

	function lfSetOtherHeight(){
		var h = $(this).height();
			h = h - $('#txtMsg').offset().top;
			h = h - 30;

		$('#txtMsg').height(h);
	}

	//가족여부
	function lfYnFamily(){
		$.ajax({
			type : 'POST'
		,	url  : './plan_family_yn.php'
		,	data : {
				'code'  : $('#val').attr('code')
			,	'jumin'	: $('#val').attr('jumin')
			,	'svcCd' : $('#val').attr('svcCd')
			,	'memCd' : $('#val').attr('memCd1')
			}
		,	success: function(result){
				$('#val').attr('ynFamily', result);
			}
		});
	}

	//요양보호사 찾기
	function lfMemFind(asType,aiIdx){
		var code  = $('#val').attr('code');
		var jumin = $('#val').attr('jumin');
		var svcCd = $('#val').attr('svcCd');
		var memCd = $('#txt'+asType+'MemCd1').attr('code')+','+$('#txt'+asType+'MemCd2').attr('code');

		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url    = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win    = window.open('about:blank', 'FIND_MEMBER', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type'  : 'member'
			,	'code'  : code
			,	'kind'  : svcCd
			,	'jumin' : jumin
			,	'yoy'   : memCd
			,	'idx'	: asType+'_'+aiIdx
			,	'return': 'lfMemFindResult'
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

		form.setAttribute('target', 'FIND_MEMBER');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	//요양보호사 찾기 결과
	function lfMemFindResult(asObj){
		var val = __parseStr(asObj);
		var laGbn = val['idx'].split('_');
		var lsSvcCd = $('#val').attr('svcCd');

		//$('#txt'+laGbn[0]+'MemCd'+laGbn[1]).attr('code',val['jumin']).val(val['name']);
		$('#lbl'+laGbn[0]+'Mem'+laGbn[1]).attr('code',val['jumin']).text(val['name']);

		if (laGbn[0] == 'Plan'){
			$('#val').attr('memCd'+laGbn[1],val['jumin']);
			$('#val').attr('memNm'+laGbn[1],val['name']);
		}

		if (lsSvcCd == '0' && laGbn[1] == '1'){
			lfYnFamily();
		}

		//수당조회
		setTimeout('lfFindExtraMemPay('+laGbn[1]+')',10);

		lfFindSuga(laGbn[0]);
		lfDuplicate(laGbn[0]);
	}

	//중복체크
	function lfDuplicate(asType){
		if (asType == 'Plan'){
			var liDuplicate = opener.lfPopDuplicate(
									$('#val').attr('objId')
								,	$('#val').attr('from').split(':').join('')
								,	$('#val').attr('to').split(':').join('')
								,	$('#val').attr('memCd1')+'|'+$('#val').attr('memCd2'));
		}else{
			var liDuplicate = 1;
		}

		if (liDuplicate == 1){
			$('#txtMsg').attr('ynDuplicate','N').html('');
		}else if (liDuplicate == 2 || liDuplicate == 3 || liDuplicate == 4){
			lfGetCalendarMem();
		}else{
			$('#txtMsg').attr('ynDuplicate','Y').html('<span style="color:#ff0000; font-weight:bold; cursor:default;">'+_planErrorMsg(liDuplicate)+'</span>');
		}
	}

	//수가
	function lfFindSuga(asType,asFrom, asTo){
		var lsSvcCd    = $('#val').attr('svcCd');
		var lsSvcKind  = $('#val').attr('svcKind');
		var ynFamily   = 'N';
		var ynFamily90 = 'N';
		var lsBathKind = '';
		var liMemCnt   = 1;

		if (asFrom && asTo){
			var from = asFrom.split(':').join('');
			var to   = asTo.split(':').join('');
		}else{
			var from = $('#txt'+asType+'FromH').val()+$('#txt'+asType+'FromM').val();
			var to   = $('#txt'+asType+'ToH').val()+$('#txt'+asType+'ToM').val();
		}

		//if (asType == 'Plan' && $('#val').attr('stat') == '1') return;
		if (lsSvcCd == '0'){
			//동거가족 여부
			//if ($('#val').attr('familyMemCd') == $('#txt'+asType+'MemCd1').attr('code')){
			if ($('#val').attr('familyMemCd') == $('#lbl'+asType+'Mem1').attr('code')){
				ynFamily = 'Y';

				if ($('#val').attr('ynPartner') == 'Y' || $('#val').attr('ynStatNot') == 'Y'){
					ynFamily90 = 'Y';
				}
			}

			if (ynFamily == 'Y'){
				var liDiffTime = diffDate('n', $('#val').attr('from'), $('#val').attr('to'));

				if (ynFamily90 == 'Y'){
					if (liDiffTime > 90) ynFamily = 'N';
				}else{
					if (liDiffTime > 60) ynFamily = 'N';
				}
			}

			if ($('#val').attr('sugaNm') == '목욕/차량(입욕)'){
				lsBathKind = '1';
			}else if ($('#val').attr('sugaNm') == '목욕/차량(가정내입욕)'){
				lsBathKind = '2';
			}else{
				lsBathKind = '3';
			}
		}else if (lsSvcCd == '4'){
			ynFamily   = 'N';

			if (lsSvcKind == '200'){
				liMemCnt = 0;

				//if ($('#txt'+asType+'MemCd1').attr('code') != '') liMemCnt ++;
				//if ($('#txt'+asType+'MemCd2').attr('code') != '') liMemCnt ++;
				if ($('#lbl'+asType+'Mem1').attr('code') != '') liMemCnt ++;
				if ($('#lbl'+asType+'Mem2').attr('code') != '') liMemCnt ++;
			}else if (lsSvcKind == '500'){
				if ($('#val').attr('sugaNm') == '방문목욕/차량내입욕'){
					lsBathKind = '1';
				}else if ($('#val').attr('sugaNm') == '방문목욕/가정내입욕'){
					lsBathKind = '2';
				}else{
					lsBathKind = '3';
				}
			}
		}else{
			if ($('#txtPayKind').attr('checked')){
				lsPayKind = 'Y';
			}else{
				lsPayKind = 'N';
			}
		}

		$('#val').attr('ynFamily',ynFamily);

		$.ajax({
			type : 'POST'
		,	async: false
		,	url  : '../find/find_suga.php'
		,	data : {
				'code'     : $('#val').attr('code')
			,	'svcCd'    : lsSvcCd
			,	'svcKind'  : lsSvcKind
			,	'date'     : $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
			,	'fromTime' : from //$('#txt'+asType+'FromH').val()+$('#txt'+asType+'FromM').val()//$('#val').attr('from').split(':').join('')
			,	'toTime'   : to //$('#txt'+asType+'ToH').val()+$('#txt'+asType+'ToM').val()//$('#val').attr('to').split(':').join('')
			,	'ynFamily' : ynFamily
			,	'bathKind' : lsBathKind
			,	'svcVal'   : $('#val').attr('svcVal')
			,	'svcLvl'   : $('#val').attr('svcLvl')
			,	'memCnt'   : liMemCnt
			}
		,	success: function(result){
				//if ($('#val').attr('code') == '31138000091'){
				//	$('#txtMsg').text($('#val').attr('sugaNm'));
				//}

				if (!result){
					$('#lbl'+asType+'Suga').attr('code','').text('');
					$('#lbl'+asType+'SugaCost').attr('value','0').text('0');
					return;
				}

				var val = __parseStr(result);

				if (lsSvcCd == 'A' || lsSvcCd == 'B' || lsSvcCd == 'C'){
					val['cost'] = __str2num($('#val').attr('svcCost'));
					val['costHoliday'] = val['cost'];
					val['costTotal']   = val['cost'] * val['procTime'];
				}

				/*
				if ('<?=$debug;?>' == '1'){
					if (lsSvcCd == '4' && lsSvcKind == '200'){
						var tmpObj = $('#val').attr('objId').split('_');
						var addTime = 0;

						$('div[id^="'+tmpObj[0]+'_'+tmpObj[1]+'"]',opener.document).each(function(){
							if ($(this).attr('day') != undefined && $(this).attr('id') != $('#val').attr('objId')){
								if ($(this).attr('day') != undefined){
									addTime += __str2num($(this).attr('timeNight'));
								}
							}
						});

						if (addTime > 0){
							val['timeNight'] = __str2num(val['timeNight']) - addTime;

							if (__str2num(val['timeNight']) < 0){
								val['timeNight'] = 0;
							}

							var tmpProcTime = __str2num(val['procTime']) / 60;

							tmpProcTime = tmpProcTime - __str2num(val['timeNight']);

							val['costTotal'] = tmpProcTime * __str2num(val['cost']) + __str2num(val['timeNight']) * __str2num(val['costNight']);
						}
					}
				}
				*/

				$('#loSuga')
					.attr('code',val['code']) //수가코드
					.attr('name',val['name']) //수가명
					.attr('cost',val['cost']) //수가
					.attr('costEvening',val['costEvening']) //연장할증금액
					.attr('costNight',val['costNight']) //야간할증금액
					.attr('costTotal',val['costTotal']) //총금액
					.attr('sudangPay',val['sudangPay']) //수당
					.attr('timeEvening',val['timeEvening']) //연장시간
					.attr('timeNight',val['timeNight']) //야간시간
					.attr('ynEvening',val['ynEvening']) //연장여부
					.attr('ynNight',val['ynNight']) //야간여부
					.attr('ynHoliday',val['ynHoliday']) //휴일여부
					.attr('costBipay',val['costBipay']) //비급여수가
					.attr('costHoliday',val['costHoliday']) //휴일할증수가
					.attr('procTime',val['procTime']) //제공시간
					.attr('hour',val['hour'] ? val['hour'] : 0) //기준시간
					.attr('hourNight',val['hourNight'] ? val['hourNight'] : 0) //연장시간
					.attr('holidayHour',val['holidayHour'] ? val['holidayHour'] : 0) //휴일기준시간
					.attr('holidayHourNight',val['holidayHourNight'] ? val['holidayHourNight'] : 0); //휴일연장시간

				$('#lbl'+asType+'Suga').attr('code',val['code']).text(val['name']);

				if (lsSvcCd == '0' || $('#val').attr('ynHoliday') != 'Y'){
					$('#lbl'+asType+'SugaCost').attr('value',val['costTotal']).text(__num2str(val['costTotal']));
				}else{
					$('#lbl'+asType+'SugaCost').attr('value',val['costHoliday']).text(__num2str(val['costHoliday']));
				}

				if (lsSvcKind == '500' || lsSvcKind == '800'){
					lfSetExtraMemPay();
					lfSetExtraCenterPay();
				}
			}
		});
	}

	//적용
	function lfApply(){
		if ($('#txtMsg').attr('ynDuplicate') == 'Y'){
			alert('중복된 일정은 적용할 수 없습니다.');
			return;
		}

		if (__str2num($('#lblPlanSugaCost').text()) <= 0){
			alert('등록할 수 없는 수가입니다. 확인 후 다시 시도하여 주십시오.');
			return;
		}

		var lsSugaNm  = $('#loSuga').attr('name');
		var lsSvcCd   = $('#val').attr('svcCd');
		var lsSvcKind = $('#val').attr('svcKind');

		if ($('#loSuga').attr('ynHoliday') == 'Y'){
			if (lsSvcCd == '0'){
				if (lsSvcKind != '500'){
					lsSugaNm += '(<span style=\'color:#ff0000;\'>30%</span>)';
				}
			}
		}

		var liExtraPay = 0;
		var lsExtraKind = '';
		var liExtraVal1 = 0
		,	liExtraVal2 = 0;

		lsExtraKind = $('input:radio[name="opt"]:checked').val();

		switch(lsSvcKind){
			case '500':
				switch(lsExtraKind){
					case 'PERSON':
						liExtraPay = 0;
						break;

					default:
						liExtraPay = __str2num($('#txtBathPay').val());

						if (lsExtraKind == 'AMT'){
							liExtraVal1 = __str2num($('#txtBathPay1').val());
							liExtraVal2 = __str2num($('#txtBathPay2').val());
						}else{
							liExtraVal1 = __str2num($('#txtBathRate1').val());
							liExtraVal2 = __str2num($('#txtBathRate2').val());
						}
				}
				break;

			case '800':
				switch(lsExtraKind){
					case 'PERSON':
						liExtraPay = 0;
						break;

					default:
						liExtraPay  = __str2num($('#txtNursePay').val());
						liExtraVal1 = 0;
						liExtraVal2 = 0;
				}
				break;

			default:
				lsExtraKind = '';
		}

		$('#'+$('#val').attr('objId'),opener.document)
			.attr('from',$('#val').attr('from'))
			.attr('to',$('#val').attr('to'))
			.attr('memCd1',$('#val').attr('memCd1'))
			.attr('memNm1',$('#val').attr('memNm1'))
			.attr('memCd2',$('#val').attr('memCd2'))
			.attr('memNm2',$('#val').attr('memNm2'))
			.attr('duplicate','N')
			.attr('sugaName',$('#loSuga').attr('name'))
			.attr('sugaCd',$('#loSuga').attr('code'))
			.attr('sugaNm',lsSugaNm)
			.attr('procTime',$('#loSuga').attr('procTime'))
			.attr('cost',$('#loSuga').attr('cost'))
			.attr('costEvening',$('#loSuga').attr('costEvening'))
			.attr('costNight',$('#loSuga').attr('costNight'))
			.attr('costTotal',$('#loSuga').attr('costTotal'))
			.attr('timeEvening',$('#loSuga').attr('timeEvening'))
			.attr('timeNight',$('#loSuga').attr('timeNight'))
			.attr('ynNight',$('#loSuga').attr('ynNight'))
			.attr('ynEvening',$('#loSuga').attr('ynEvening'))
			.attr('ynFamily',$('#val').attr('ynFamily'))
			.attr('sudangPay',liExtraPay)
			.attr('sudangKind',lsExtraKind)
			.attr('sudangVal1',liExtraVal1)
			.attr('sudangVal2',liExtraVal2)
			.attr('ynSave','N');

		var lsTime = $('#val').attr('from')+'~'+$('#val').attr('to');
		var lsMem  = $('#val').attr('memNm1')+($('#val').attr('memNm2') ? '/'+$('#val').attr('memNm2') : '');
		var lsSuagHtml = '<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;">'+lsSugaNm+'</div>';

		switch(lsSvcKind){
			case '500':
				lsSuagHtml += '<div style="float:left; width:auto;"><img src="../image/icon_bath.png" style="width:15px; height:14px;"></div>';
				break;

			case '800':
				lsSuagHtml += '<div style="float:left; width:auto;"><img src="../image/icon_nurs.png" style="width:15px; height:14px;"></div>';
				break;
		}

		$('#lblTimeStr', $('#'+$('#val').attr('objId'),opener.document)).text(lsTime);
		$('#lblMemStr',  $('#'+$('#val').attr('objId'),opener.document)).text(lsMem);
		$('#lblSugaStr', $('#'+$('#val').attr('objId'),opener.document)).html(lsSuagHtml);
		$('#divErrorMsg',$('#'+$('#val').attr('objId'),opener.document)).text('');

		opener.lfCalClean(3);
		lfClose();
	}

	function lfSave(){
		var lsSvcKind  = $('#val').attr('svcKind');
		var liExtraPay = 0;
		var lsExtraKind = '';
		var liExtraVal1 = 0
		,	liExtraVal2 = 0;

		if (__str2num($('#lblConfSugaCost').text()) <= 0){
			alert('등록할 수 없는 수가입니다. 확인 후 다시 시도하여 주십시오.');
			return;
		}

		/*
		if (lsSvcKind == '500'){
			liExtraPay = __str2num($('#txtExtraBath').val());
		}else if (lsSvcKind == '800'){
			liExtraPay = __str2num($('#txtExtraNurse').val());
		}
		*/

		lsExtraKind = $('input:radio[name="opt"]:checked').val();

		switch(lsSvcKind){
			case '500':
				switch(lsExtraKind){
					case 'PERSON':
						liExtraPay = 0;
						break;

					default:
						liExtraPay = __str2num($('#txtBathPay').val());

						if (lsExtraKind == 'AMT'){
							liExtraVal1 = __str2num($('#txtBathPay1').val());
							liExtraVal2 = __str2num($('#txtBathPay2').val());
						}else{
							liExtraVal1 = __str2num($('#txtBathRate1').val());
							liExtraVal2 = __str2num($('#txtBathRate2').val());
						}
				}
				break;

			case '800':
				switch(lsExtraKind){
					case 'PERSON':
						liExtraPay = 0;
						break;

					default:
						liExtraPay  = __str2num($('#txtNursePay').val());
						liExtraVal1 = 0;
						liExtraVal2 = 0;
				}
				break;

			default:
				lsExtraKind = '';
		}

		if ($('#val').attr('ynClose') != 'Y'){
			$.ajax({
				type : 'POST'
			,	url  : './plan_pop_save.php'
			,	data : {
					'code'  : $('#val').attr('code')
				,	'jumin'	: $('#val').attr('jumin')
				,	'svcCd' : $('#val').attr('svcCd')
				,	'date'  : $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
				,	'from'  : $('#val').attr('fTime').split(':').join('')
				,	'seq'   : $('#val').attr('seq')
				,   'svcKind'      : $('#val').attr('svcKind')
				,	'confDt'       : $('#lblConfDt').text().split('.').join('')
				,	'confFrom'     : $('#txtConfFromH').val()+$('#txtConfFromM').val()
				,	'confTo'       : $('#txtConfToH').val()+$('#txtConfToM').val()
				,	'confProctime' : $('#loSuga').attr('procTime')
				,	'confMemCd1'   : $('#txtConfMemCd1').attr('code')
				,	'confMemNm1'   : $('#txtConfMemCd1').val()
				,	'confMemCd2'   : $('#txtConfMemCd2').attr('code')
				,	'confMemNm2'   : $('#txtConfMemCd2').val()
				,	'confSugaCd'   : $('#lblConfSuga').attr('code')
				,	'confSugaCost' : $('#lblConfSugaCost').attr('value')
				,	'sudangPay'  : liExtraPay
				,	'sudangKind' : lsExtraKind /*$('#txtExtraGbn').val()*/
				,	'sudangVal1' : liExtraVal1 /*__str2num($('#txtExtraPay1').val())*/
				,	'sudangVal2' : liExtraVal2 /*__str2num($('#txtExtraPay2').val())*/
				}
			,	success: function(result){
					if (result == 1){
						alert('정상적으로 처리되었습니다.');

						var obj = $('#'+$('#val').attr('objId'), opener.document);
						$('div[id="btnRemove"]', $(obj)).html('<img src="../image/img_key.jpg" onclick="" style="margin-top:3px; width:15px; height:14px;" alt="실적이 등록되었습니다.">');

						if ($('#val').attr('svcKind') == '500'){
							$(obj).attr('sudangPay', liExtraPay)
								  .attr('sudangKind', $('input:radio[name="opt"]:checked').val() /*$('#txtExtraGbn').val()*/)
								  .attr('sudangVal1', ($('input:radio[name="opt"]:checked').val() == 'RATE' ? __str2num($('#txtBathRate1').val()) : __str2num($('#txtBathPay1').val())))
								  .attr('sudangVal2', ($('input:radio[name="opt"]:checked').val() == 'RATE' ? __str2num($('#txtBathRate2').val()) : __str2num($('#txtBathPay2').val())));
						}else if ($('#val').attr('svcKind') == '800'){
							$(obj).attr('sudangPay', liExtraPay)
								  .attr('sudangKind', $('input:radio[name="opt"]:checked').val())
								  .attr('sudangVal1', __str2num($('#txtNursePay').val()))
								  .attr('sudangVal2', '0');
						}

						$(obj).attr('stat','1').attr('ynSave','Y');
						lfClose();
					}else if (result == 9){
						alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
					}else{
						alert(result);
					}
				}
			});
		}else{
			$.ajax({
				type : 'POST'
			,	url  : './plan_pop_update.php'
			,	data : {
					'code'  : $('#val').attr('code')
				,	'jumin'	: $('#val').attr('jumin')
				,	'svcCd' : $('#val').attr('svcCd')
				,	'date'  : $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
				,	'from'  : $('#val').attr('fTime').split(':').join('')
				,	'seq'   : $('#val').attr('seq')
				,   'svcKind'    : $('#val').attr('svcKind')
				,	'sudangPay'  : liExtraPay
				,	'sudangKind' : lsExtraKind /*$('#txtExtraGbn').val()*/
				,	'sudangVal1' : liExtraVal1 /*__str2num($('#txtExtraPay1').val())*/
				,	'sudangVal2' : liExtraVal2 /*__str2num($('#txtExtraPay2').val())*/
				}
			,	success: function(result){
					if (result == 1){
						alert('정상적으로 처리되었습니다.');

						var obj = $('#'+$('#val').attr('objId'), opener.document);

						$(obj).attr('sudangPay', liExtraPay)
							  .attr('sudangKind', $('input:radio[name="opt"]:checked').val() /*$('#txtExtraGbn').val()*/)
							  .attr('sudangVal1', liExtraVal1 /*__str2num($('#txtExtraPay1').val())*/)
							  .attr('sudangVal2', liExtraVal2 /*__str2num($('#txtExtraPay2').val())*/);

						lfClose();
					}else if (result == 9){
						alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
					}else{
						alert(result);
					}
				}
			});
		}
	}

	function lfClose(){
		self.close();
	}

	function lfGetCalendarConf(){
		$.ajax({
			type : 'POST'
		,	async: false
		,	url  : './plan_pop_result.php'
		,	data : {
				'code'  : $('#val').attr('code')
			,	'jumin'	: $('#val').attr('jumin')
			,	'svcCd' : $('#val').attr('svcCd')
			,	'date'  : $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
			,	'from'  : $('#val').attr('from').split(':').join('')
			,	'seq'   : $('#val').attr('seq')
			}
		,	success: function(result){
				var val = __parseStr(result);

				$('#lblConfDt').text($('#val').attr('year')+'.'+$('#val').attr('month')+'.'+$('#val').attr('day'));
				$('#lblConfSuga').attr('code',val['sugaCd']).text(val['sugaNm']);
				$('#lblConfSugaCost').attr('value',val['sugaCost']).text(__num2str(val['sugaCost']));

				//$('#txtConfMemCd1').attr('code',val['memCd1']).val(val['memNm1']);
				//$('#txtConfMemCd2').attr('code',val['memCd2']).val(val['memNm2']);
				$('#lblConfMem1').attr('code',val['memCd1']).text(val['memNm1']);
				$('#lblConfMem2').attr('code',val['memCd2']).text(val['memNm2']);

				if (val['from'] && val['to']){
					var time = val['from'].substring(0,2)+':'+val['from'].substring(2,4)+' ~ '+val['to'].substring(0,2)+':'+val['to'].substring(2,4);
				}else{
					var time = '';
				}

				$('#lblConfTime').text(time);

				$('#txtConfFromH').val(val['from'].substring(0,2));
				$('#txtConfFromM').val(val['from'].substring(2,4));
				$('#txtConfToH').val(val['to'].substring(0,2));
				$('#txtConfToM').val(val['to'].substring(2,4));

				if ($('#val').attr('ynClose') == 'Y'){
					$('.clsConf').hide();

					$('#lblConfMemStr1').addClass('left').text(val['memNm1']);
					$('#lblConfMemStr2').addClass('left').text(val['memNm2']);
					$('#lblConfTimeStr').addClass('left').text(val['from']+'~'+val['to']);
				}

				/*
				if ($('#val').attr('ynClose') != 'Y'){
					$('#txtConfMemCd1').attr('code',val['memCd1']).val(val['memNm1']);
					$('#txtConfMemCd2').attr('code',val['memCd2']).val(val['memNm2']);
					$('#txtConfFromH').val(val['from'].substring(0,2));
					$('#txtConfFromM').val(val['from'].substring(2,4));
					$('#txtConfToH').val(val['to'].substring(0,2));
					$('#txtConfToM').val(val['to'].substring(2,4));
				}else{
					$('#txtConfMemCd1').hide();
					$('#txtConfMemCd2').hide();

					$('#txtConfFromH').hide();
					$('#txtConfFromM').hide();
					$('#txtConfToH').hide();
					$('#txtConfToM').hide();

					$('#lblConfMem1').addClass('left').text(val['memNm1']);
					$('#lblConfMem2').addClass('left').text(val['memNm2']);
					$('#lblConfTime').addClass('left').text(val['from']+'~'+val['to']);
				}
				*/
			}
		});
	}

	function lfGetCalendarMem(){
		$.ajax({
			type : 'POST'
		,	url  : './plan_pop_mem.php'
		,	data : {
				'code'  : $('#val').attr('code')
			,	'jumin'	: $('#val').attr('jumin')
			,	'svcCd'	: $('#val').attr('svcCd')
			,	'date'  : $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
			,	'memCd1': $('#val').attr('memCd1')
			,	'memNm1': $('#val').attr('memNm1')
			,	'memCd2': $('#val').attr('memCd2')
			,	'memNm2': $('#val').attr('memNm2')
			,	'from'	: $('#val').attr('from')
			,	'to'	: $('#val').attr('to')
			,	'tmpSvcCd':'<?=$tmpSvcCd;?>'
			}
		,	success: function(result){
				var html = result;
				var obj  = $('div[id^="loCal_'+parseInt($('#val').attr('day'),10)+'_"][id!="'+$('#val').attr('objId')+'"]',opener.document);

				liFrom = _planTime2Min($('#val').attr('from'));
				liTo   = _planTime2Min($('#val').attr('to'));

				if (liTo < liFrom)
					liTo = liTo + 24 * 60;

				$(obj).each(function(){
					var liChkF = _planTime2Min($(this).attr('from'));
					var liChkT = _planTime2Min($(this).attr('to'));

					//일정중복확인
					if (liChkF + liChkT > 0){
						if ((liFrom <= liChkF && liTo > liChkF) ||
							(liFrom < liChkT && liTo >= liChkT) ||
							(liFrom > liChkF && liTo < liChkT)){
							if ($(this).attr('svcKind') == '200' || $(this).attr('svcKind') == '500'){
								if ($('#val').attr('svcKind') == '200' || $('#val').attr('svcKind') == '500'){
									//수급자중복
									html += '<span style="line-height:20px; color:#ff0000; font-weight:bold; padding-right:5px;">수급자중복</span><br>'
										 +  '<span style="line-height:20px; padding-left:10px;">시간 : </span><span style="font-weight:bold;">'+$(this).attr('from')+'~'+$(this).attr('to')+'</span><br>';
								}
							}else{
								if ($(this).attr('svcKind') == $('#val').attr('svcKind')){
									//수급자중복
									html += '<span style="line-height:20px; color:#ff0000; font-weight:bold; padding-right:5px;">수급자중복</span><br>'
										 +  '<span style="line-height:20px; padding-left:10px;">시간 : </span><span style="font-weight:bold;">'+$(this).attr('from')+'~'+$(this).attr('to')+'</span><br>';
								}
							}
						}
					}

					//재가 일정간 2시간 간격확인
					if ($('#val').attr('svcCd') == '0'){
						if ($('#val').attr('svcKind') == '200' && $(this).attr('svcKind') == '200'){
							var liGabF = liFrom - 120;
							var liGabT = liTo + 120;

							if ((liGabF <= liChkF && liGabT > liChkF) ||
								(liGabF < liChkT && liGabT >= liChkT) ||
								(liGabF > liChkF && liGabT < liChkT)){
								//2시간미경과
								html += '<span style="line-height:20px; color:#ff0000; font-weight:bold; padding-right:5px;">전일정과 2시간간격오류</span><br>'
									 +  '<span style="line-height:20px; padding-left:10px;">시간 : </span><span style="font-weight:bold;">'+$(this).attr('from')+'~'+$(this).attr('to')+'</span><br>';
							}
						}
					}
				});

				if (html){
					$('#txtMsg').attr('ynDuplicate','Y').html(html);
				}else{
					$('#txtMsg').attr('ynDuplicate','N').html('');
				}
			}
		});
	}

	//시간 절사
	function lfSetEndTime(asType){
		var lsSvcCd   = $('#val').attr('svcCd');
		var lsSvcKind = $('#val').attr('svcKind');

		var liFromH = __str2num($('#txt'+asType+'FromH').val());
		var liFromM = __str2num($('#txt'+asType+'FromM').val());
		var liToH   = __str2num($('#txt'+asType+'ToH').val());
		var liToM   = __str2num($('#txt'+asType+'ToM').val());

		var liFrom = 0, liTo = 0, liTime = 0;

		if (lsSvcCd == '0' && lsSvcKind == '200'){
			liFrom = liFromH * 60 + liFromM;
			liTo   = liToH * 60 + liToM;

			if (liFrom > liTo){
				liTo = liTo + (24 * 60);
			}

			//30분단위의 계산을 분단위로 전환
			//liTime = cut(liTo - liFrom,30);
			liTime = liTo - liFrom;

			if (liTime > 510){
				liTime = 510;
			}

			liTo = liFrom + liTime;

			liToH = Math.floor(liTo / 60);
			liToM = liTo % 60;

			liToH = (liToH < 10 ? '0' : '')+liToH;
			liToM = (liToM < 10 ? '0' : '')+liToM;

			$('#txt'+asType+'ToH').val(liToH);
			$('#txt'+asType+'ToM').val(liToM);
		}
	}

	//기관수당입력
	function lfSetExtraCenterPay(){
		var lsSugaCd    = $('#loSuga').attr('code'); //수가코드
		var liCenterPay = __str2num($('#loExtraCenter').attr(lsSugaCd)); //기관수당
		var liExtraPay  = __str2num($('#val').attr('sudangPay'));  //입력수당
		var liExtraVal1 = __str2num($('#val').attr('sudangVal1')); //값1
		var liExtraVal2 = __str2num($('#val').attr('sudangVal2')); //값2
		var lsExtraKind = $('#val').attr('sudangKind');            //수당구분

		var liPay   = (liExtraPay > 0 ? liExtraPay : liCenterPay)
		,	liPay1  = liPay * 0.5
		,	liPay2  = liPay * 0.5
		,	liRate1 = 50
		,	liRate2 = 50;

		switch(lsExtraKind){
			case 'PERSON':
				break;

			case 'AMT':
				liPay1 = __str2num(liExtraVal1);
				liPay2 = __str2num(liExtraVal2);
				break;

			default:
				liRate1 = __str2num(liExtraVal1);
				liRate2 = __str2num(liExtraVal2);
		}

		if ($('#val').attr('svcKind') == '500'){
			$('#txtBathPay').val(__num2str(liPay));
			$('#txtBathPay1').val(__num2str(liPay1));
			$('#txtBathPay2').val(__num2str(liPay2));
			$('#txtBathRate1').val(liRate1);
			$('#txtBathRate2').val(liRate2);
		}else if ($('#val').attr('svcKind') == '800'){
			$('#txtNursePay').val(__num2str(liPay));
		}

		if ($('input:radio[name="opt"]:checked').length == 0){
			$('input:radio[name="opt"]input[value="'+lsExtraKind+'"]').attr('checked','checked');
		}
	}

	//개별수당입력
	function lfSetExtraMemPay(){
		try{
			var lsSugaCd  = $('#loSuga').attr('code');
			var lsSvcKind = $('#val').attr('svcKind');
			var lsSugaVal = lfGetPersonCd();

			if (!lsSugaVal){
				return;
			}

			//직원별수당
			var liPay1 = $('#loExtraMem1').attr(lsSugaVal)
			,	liPay2 = $('#loExtraMem2').attr(lsSugaVal);

			if (lsSvcKind == '500'){
				$('#lblBathPay1').text(__num2str(liPay1));
				$('#lblBathPay2').text(__num2str(liPay2));
			}else if (lsSvcKind == '800'){
				$('#lblNursePay').text(__num2str(liPay1));
			}
		}catch(e){
			alert(e);
		}
	}

	//요양보호사 수당조회
	function lfFindExtraMemPay(aiIdx){
		try{
			var lsSvcKind = $('#val').attr('svcKind');
			var lsSugaVal = lfGetPersonCd();

			if ($('#val').attr('memCd'+aiIdx) == ''){
				return;
			}

			if (lsSvcKind == '500' || lsSvcKind == '800'){
				//목욕 및 간호는 직원별 수당을 가져온다.
				$.ajax({
					type : 'POST'
				,	url  : '../common/find_extra_pay.php'
				,	data : {
						'code'  : $('#val').attr('code')
					,	'jumin'	: $('#val').attr('memCd'+aiIdx)
					}
				,	success: function(result){
						var val = result.split('/');

						$('#loExtraMem'+aiIdx)
							.attr('B3',val[0])
							.attr('B2',val[2])
							.attr('B1',val[1])
							.attr('N1',val[3])
							.attr('N2',val[4])
							.attr('N3',val[5]);

						//수당입력
						setTimeout('lfSetExtraMemPay()',10);
					}
				});
			}
		}catch(e){
		}
	}

	//수당금액구분코드
	function lfGetPersonCd(){
		var lsSubaCd   = $('#loSuga').attr('code');
		var lsSvcKind  = $('#val').attr('svcKind');
		var lsPersonCd = '';

		if (!lsSubaCd){
			return;
		}

		if (lsSvcKind == '500'){
			lsPersonCd = 'B';

			if (lsSubaCd == 'CBFD1'){
				lsPersonCd += '3';
			}else if (lsSubaCd == 'CBKD1' || lsSubaCd == 'VAB10'){
				lsPersonCd += '1';
			}else if (lsSubaCd == 'CBKD2' || lsSubaCd == 'VAB20'){
				lsPersonCd += '2';
			}
		}else if (lsSvcKind == '800'){
			lsPersonCd = 'N';

			if (lsSubaCd == 'CNWS1' || lsSubaCd == 'CNHS1' || lsSubaCd == 'VAN10'){
				lsPersonCd += '1';
			}else if (lsSubaCd == 'CNWS2' || lsSubaCd == 'CNHS2' || lsSubaCd == 'VAN20'){
				lsPersonCd += '2';
			}else if (lsSubaCd == 'CNWS3' || lsSubaCd == 'CNHS3' || lsSubaCd == 'VAN30'){
				lsPersonCd += '3';
			}
		}

		return lsPersonCd;
	}

	//부요야보호사 입력 가능여부
	function lfSubMemYn(){
		if ($('#val').attr('svcKind') == '500'){
			return true;
		}else if ($('#val').attr('svcCd') == '4' && $('#val').attr('svcKind') == '200'){
			return true;
		}

		return false;
	}
</script>
<?
	include_once('../inc/_footer.php');
?>