<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

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

	$sudangPay  = $_POST['sudangPay'];
	$sudangKind = $_POST['sudangKind'];
	$sudangVal1 = $_POST['sudangVal1'];
	$sudangVal2 = $_POST['sudangVal2'];

	$svcVal  = $_POST['svcVal'];
	$svcLvl  = $_POST['svcLvl'];
	$svcCost = $_POST['svcCost'];

	$ynBipay = $_POST['ynBipay'];
	$ynSave  = $_POST['ynSave'];
	$stat    = $_POST['stat'];
	$id      = $_POST['id'];

	$request = $_POST['request'];

	$ynClose = $_POST['ynClose'];

	$type = $_POST['type'];

	if ($type == 'CONF'){
		$ynClose = 'Y';
	}

	$today = date('Ymd');

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	if (!is_numeric($memCd1)) $memCd1 = $ed->de($memCd1);
	if (!is_numeric($memCd2)) $memCd2 = $ed->de($memCd2);

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
?>
<script type="text/javascript" src="./plan.js"></script>
<form id="f" name="f" method="post"><?
if ($request != 'LOG'){?>
	<div class="title title_border">계획정보</div>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="80px">
			<col width="150px">
			<col width="100px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="left">고객명</th>
				<td class="left" id="lblCNm"></td>
				<th class="left">담당요양보호사</th>
				<td id="lblPlanMem"><input id="txtPlanMemCd1" name="txtMemCd" type="text" value="" code="" style="width:55px; cursor:default;" alt="not" readonly><span class="clsMem2">/</span><input id="txtPlanMemCd2" name="txtMemCd" type="text" value="" code="" class="clsMem2" style="width:55px; cursor:default;" alt="not" readonly></td>
			</tr>
			<tr>
				<th class="left">일자</th>
				<td class="left" id="lblPlanDt"></td>
				<th class="left">시간</th>
				<td id="lblPlanTime"><input id="txtPlanFromH" name="txtPlanTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">:<input id="txtPlanFromM" name="txtPlanTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">~<input id="txtPlanToH" name="txtPlanTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">:<input id="txtPlanToM" name="txtPlanTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"></td>
			</tr>
			<tr>
				<th class="left">수가명</th>
				<td class="left" id="lblPlanSuga" code=""></td>
				<th class="left">금액</th>
				<td class="left" id="lblPlanSugaCost" value="0"></td>
			</tr>
		</tbody>
	</table><?
}?>
<div class="title title_border clsResult"><?
	if ($today >= $year.$month.$day){?>
		<div id="btnResultSet" style="float:right; width:auto; font-weight:normal; cursor:default;"><img src="./img/btn_calen11.gif" style="margin-top:10px; margin-right:5px;"></div><?
	}else{?>
		<div style="float:right; width:auto; font-weight:normal; color:#ff0000; cursor:default;">※미래의 일정에 대해서는 실적을 입력할 수 없습니다.</div><?
	}?>
	<div style="float:left; width:auto;">실적정보</div>
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
			<td id="lblConfMem1"><input id="txtConfMemCd1" name="txtMemCd" type="text" value="" code="" style="width:55px; cursor:default;" alt="not" readonly></td>
		</tr>
		<tr>
			<th class="left">시간</th>
			<td id="lblConfTime"><input id="txtConfFromH" name="txtConfTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">:<input id="txtConfFromM" name="txtConfTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">~<input id="txtConfToH" name="txtConfTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">:<input id="txtConfToM" name="txtConfTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"></td>
			<th class="left">요양보호사2</th>
			<td id="lblConfMem2"><input id="txtConfMemCd2" name="txtMemCd" type="text" value="" code="" class="clsMem2" style="width:55px; cursor:default;" alt="not" readonly></td>
		</tr>
		<tr>
			<th class="left">수가명</th>
			<td class="left" id="lblConfSuga" code=""></td>
			<th class="left">금액</th>
			<td class="left" id="lblConfSugaCost" value="0"></td>
		</tr>
	</tbody>
</table>

<div id="loExtrapay" style="display:none;">
	<div class="title title_border">수당정보</div>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="78px">
			<col width="50px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody id="loExtraBath">
			<tr>
				<th class="left">목욕수당</th>
				<td id="lblExtraBath"><input id="txtExtraBath" name="txtExtraPR" type="text" value="<?=$liRate1;?>" rate="<?=$liRate1;?>" amt="0" class="number" style="width:50px;"></td>
				<th id="lblExtraGbn" class="center">
					<select id="txtExtraGbn" name="txtExtraGbn" style="width:auto;">
						<option value="rate">비율</option>
						<!--option value="amt">금액</option-->
					</select>
				</th>
				<td id="lblExtraPay">
					<input id="txtExtraPay1" name="txtExtraPR" type="text" value="0" rate="0" amt="0" class="number" style="width:30px;">% /
					<input id="txtExtraPay2" name="txtExtraPR" type="text" value="0" rate="0" amt="0" class="number" style="width:30px;">%
				</td>
			</tr>
		</tbody>
		<tbody id="loExtraNurse">
			<tr>
				<th class="left">간호수당</th>
				<td id="lblExtraNurse"><input id="txtExtraNurse" name="txtExtraNurse" type="text" value="<?=$liRate1;?>" rate="<?=$liRate1;?>" amt="0" class="number" style="width:50px;"></td>
				<td class="last">&nbsp;</td>
				<td class="last">&nbsp;</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="title title_border">기타</div>
<div id="txtMsg" ynDuplicate="N" style="padding:5px; overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
<div style="text-align:center; height:30px; padding-top:4px; border-top:1px solid #cccccc;"><?
	if ($debug){?>
		<span class="btn_pack m"><button type="button" onclick="location.reload();">새로고침</button></span><?
	}?>
	<span id="btnApply" class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span><span id="btnSave" class="btn_pack m"><button type="button" onclick="lfSave();">저장</button></span>
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
	ynBipay="<?=$ynBipay;?>"
	ynPartner="<?=$ynPartner;?>"
	ynStatNot="<?=$ynStatNot;?>"
	ynFamily="N"
	ynSave="<?=$ynSave;?>"
	stat="<?=$stat;?>"
	ynClose="<?=$ynClose;?>"
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
			$('#txtExtraPay1').val($('#txtExtraPay1').attr('rate'));
			$('#txtExtraPay2').val($('#txtExtraPay2').attr('rate'));
		}else{
			var liExtraPay = __str2num($('#txtExtraBath').val());
			var liRate1 = __str2num($('#txtExtraPay1').attr('rate'));
			var liRate2 = __str2num($('#txtExtraPay2').attr('rate'));
			var liAmt1 = __num2str(liExtraPay * liRate1 * 0.01);
			var liAmt2 = __num2str(liExtraPay * liRate2 * 0.01);

			$('#txtExtraPay1').val(liAmt1);
			$('#txtExtraPay2').val(liAmt2);
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

	$(document).ready(function(){
		$('#lblCNm').text($('#val').attr('cNm'));
		$('#lblPlanDt').text($('#val').attr('year')+'.'+$('#val').attr('month')+'.'+$('#val').attr('day'));
		$('#txtPlanMemCd1').attr('code',$('#val').attr('memCd1')).val($('#val').attr('memNm1'));
		$('#txtPlanMemCd2').attr('code',$('#val').attr('memCd2')).val($('#val').attr('memNm2'));
		$('#txtPlanFromH').val($('#val').attr('from').substring(0,2));
		$('#txtPlanFromM').val($('#val').attr('from').substring(3,5));
		$('#txtPlanToH').val($('#val').attr('to').substring(0,2));
		$('#txtPlanToM').val($('#val').attr('to').substring(3,5));
		$('#lblPlanSuga').html($('#val').attr('sugaNm'));
		$('#lblPlanSugaCost').text(__num2str($('#val').attr('suga')));

		//가족여부
		lfYnFamily();

		var lbMem2 = false;

		if ($('#val').attr('svcKind') == '500'){
			lbMem2 = true;
		}else if ($('#val').attr('svcCd') == '4' && $('#val').attr('svcKind') == '200'){
			lbMem2 = true;
		}

		if (!lbMem2){
			$('.clsMem2').hide();
		}

		var lsStat = $('#val').attr('stat');

		if (lsStat == '1'){
			$('#txtPlanMemCd1').hide();
			$('#txtPlanMemCd2').hide();
			$('#txtPlanFromH').hide();
			$('#txtPlanFromM').hide();
			$('#txtPlanToH').hide();
			$('#txtPlanToM').hide();

			$('#lblPlanMem').addClass('left').text($('#val').attr('memNm1')+($('#val').attr('memNm2') ? '/'+$('#val').attr('memNm2') : ''));
			$('#lblPlanTime').addClass('left').text($('#val').attr('from')+'~'+$('#val').attr('to'));

			$('#btnApply').hide();
		}else{
			$('#btnSave').hide();
		}

		if ($('#val').attr('ynClose') == 'Y'){
			$('#txtConfMemCd1').hide();
			$('#txtConfMemCd2').hide();

			$('#txtConfFromH').hide();
			$('#txtConfFromM').hide();
			$('#txtConfToH').hide();
			$('#txtConfToM').hide();

			$('#btnApply').hide();
			$('#btnSave').hide();
		}

		if ($('#val').attr('seq') != ''){
			lfGetCalendarConf();
			lfFindSuga('Plan');

			if (lsStat != '1'){
				$('#tblResult').hide();
			}else{
				$('#btnResultSet').hide();
			}
		}else{
			lfGetCalendarMem();
			lfFindSuga('Plan');
			$('.clsResult').hide();
		}

		if ($('#val').attr('ynSave') == 'N'){
			$('#btnResultSet').unbind('click').text('');
		}

		//수당입력
		if ($('#val').attr('ynClose') != 'Y'){
			if ($('#val').attr('svcKind') == '500'){
				$('#loExtrapay').show();
				$('#loExtraBath').show();
				$('#loExtraNurse').hide();

				if ($('#val').attr('sudangKind') == 'rate'){
					$('#txtExtraPay1').attr('rate',$('#val').attr('sudangVal1'));
					$('#txtExtraPay2').attr('rate',$('#val').attr('sudangVal2'));
				}else{
					$('#txtExtraPay1').attr('amt',$('#val').attr('sudangVal1'));
					$('#txtExtraPay2').attr('amt',$('#val').attr('sudangVal2'));
				}

				$('#txtExtraBath').val(__num2str($('#val').attr('sudangPay')));
				$('#txtExtraGbn').val($('#val').attr('sudangKind'));
				$('#txtExtraPay1').val($('#val').attr('sudangVal1'));
				$('#txtExtraPay2').val($('#val').attr('sudangVal2'));
			}else if ($('#val').attr('svcKind') == '800'){
				$('#loExtrapay').show();
				$('#loExtraBath').hide();
				$('#loExtraNurse').show();

				$('#txtExtraNurse').val($('#val').attr('sudangPay'));
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

				$('#lblExtraBath').addClass('left').text(__num2str($('#val').attr('sudangPay')));
				$('#lblExtraGbn').addClass('left').text($('#val').attr('sudangKind') == 'rate' ? '비율' : '금액');
				$('#lblExtraPay').addClass('left').text(__num2str($('#val').attr('sudangVal1'))+'/'+__num2str($('#val').attr('sudangVal2')));
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

		$('#txt'+laGbn[0]+'MemCd'+laGbn[1]).attr('code',val['jumin']).val(val['name']);

		if (laGbn[0] == 'Plan'){
			$('#val').attr('memCd'+laGbn[1],val['jumin']);
			$('#val').attr('memNm'+laGbn[1],val['name']);
		}

		if (lsSvcCd == '0' && laGbn[1] == '1'){
			lfYnFamily();
		}

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
								,	$('#val').attr('memCd1'));
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
	function lfFindSuga(asType){
		var lsSvcCd    = $('#val').attr('svcCd');
		var lsSvcKind  = $('#val').attr('svcKind');
		var ynFamily   = 'N';
		var ynFamily90 = 'N';
		var lsBathKind = '';
		var liMemCnt   = 1;

		if (asType == 'Plan' && $('#val').attr('stat') == '1') return;
		if (lsSvcCd == '0'){
			//동거가족 여부
			if ($('#val').attr('familyMemCd') == $('#txt'+asType+'MemCd1').attr('code')){
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

				if ($('#txt'+asType+'MemCd1').attr('code') != '') liMemCnt ++;
				if ($('#txt'+asType+'MemCd2').attr('code') != '') liMemCnt ++;
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
		,	url  : '../find/find_suga.php'
		,	data : {
				'code'     : $('#val').attr('code')
			,	'svcCd'    : lsSvcCd
			,	'svcKind'  : lsSvcKind
			,	'date'     : $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
			,	'fromTime' : $('#txt'+asType+'FromH').val()+$('#txt'+asType+'FromM').val()//$('#val').attr('from').split(':').join('')
			,	'toTime'   : $('#txt'+asType+'ToH').val()+$('#txt'+asType+'ToM').val()//$('#val').attr('to').split(':').join('')
			,	'ynFamily' : ynFamily
			,	'bathKind' : lsBathKind
			,	'svcVal'   : $('#val').attr('svcVal')
			,	'svcLvl'   : $('#val').attr('svcLvl')
			,	'memCnt'   : liMemCnt
			}
		,	success: function(result){
				var val = __parseStr(result);

				if (!result) return;

				if (lsSvcCd == 'A' || lsSvcCd == 'B' || lsSvcCd == 'C'){
					val['cost'] = __str2num($('#val').attr('svcCost'));
					val['costHoliday'] = val['cost'];
					val['costTotal']   = val['cost'] * val['procTime'];
				}

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
				$('#lbl'+asType+'SugaCost').attr('value',val['costTotal']).text(__num2str(val['costTotal']));

				//$('#loSuga').text(result).show();
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

		if (lsSvcKind == '500'){
			liExtraPay = __str2num($('#txtExtraBath').val());
		}else if (lsSvcKind == '800'){
			liExtraPay = __str2num($('#txtExtraNurse').val());
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
			.attr('sudangKind',$('#txtExtraGbn').val())
			.attr('sudangVal1',__str2num($('#txtExtraPay1').val()))
			.attr('sudangVal2',__str2num($('#txtExtraPay2').val()))
			.attr('ynSave','N');

		var lsTime = $('#val').attr('from')+'~'+$('#val').attr('to');
		var lsMem  = $('#val').attr('memNm1')+($('#val').attr('memNm2') ? '/'+$('#val').attr('memNm2') : '');

		$('#lblTimeStr', $('#'+$('#val').attr('objId'),opener.document)).text(lsTime);
		$('#lblMemStr',  $('#'+$('#val').attr('objId'),opener.document)).text(lsMem);
		$('#lblSugaStr', $('#'+$('#val').attr('objId'),opener.document)).text(lsSugaNm);
		$('#divErrorMsg',$('#'+$('#val').attr('objId'),opener.document)).text('');

		opener.lfCalClean(3);
		lfClose();
	}

	function lfSave(){
		var lsSvcKind  = $('#val').attr('svcKind');
		var liExtraPay = 0;

		if (__str2num($('#lblConfSugaCost').text()) <= 0){
			alert('등록할 수 없는 수가입니다. 확인 후 다시 시도하여 주십시오.');
			return;
		}

		if (lsSvcKind == '500'){
			liExtraPay = __str2num($('#txtExtraBath').val());
		}else if (lsSvcKind == '800'){
			liExtraPay = __str2num($('#txtExtraNurse').val());
		}

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
			,	'sudangKind' : $('#txtExtraGbn').val()
			,	'sudangVal1' : __str2num($('#txtExtraPay1').val())
			,	'sudangVal2' : __str2num($('#txtExtraPay2').val())
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');

					var obj = $('#'+$('#val').attr('objId'), opener.document);
					$('div[id="btnRemove"]', $(obj)).html('<img src="../image/img_key.jpg" onclick="" style="margin-top:3px; width:15px; height:14px;" alt="실적이 등록되었습니다.">');

					if ($('#val').attr('svcKind') == '500' || $('#val').attr('svcKind') == '800'){
						$(obj).attr('sudangPay', liExtraPay)
							  .attr('sudangKind', $('#txtExtraGbn').val())
							  .attr('sudangVal1', __str2num($('#txtExtraPay1').val()))
							  .attr('sudangVal2', __str2num($('#txtExtraPay2').val()));
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
	}

	function lfClose(){
		self.close();
	}

	function lfGetCalendarConf(){
		$.ajax({
			type : 'POST'
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

				//$('#lblConfDt').attr('value', val['dt']).text(val['dt'].split('-').join('.'));
				$('#lblConfDt').text($('#val').attr('year')+'.'+$('#val').attr('month')+'.'+$('#val').attr('day'));
				$('#lblConfSuga').attr('code',val['sugaCd']).text(val['sugaNm']);
				$('#lblConfSugaCost').attr('value',val['sugaCost']).text(__num2str(val['sugaCost']));

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
			,	'date'  : $('#val').attr('year')+$('#val').attr('month')+$('#val').attr('day')
			,	'memCd1': $('#val').attr('memCd1')
			,	'memNm1': $('#val').attr('memNm1')
			,	'memCd2': $('#val').attr('memCd2')
			,	'memNm2': $('#val').attr('memNm2')
			,	'from'	: $('#val').attr('from')
			,	'to'	: $('#val').attr('to')
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

			liTime = cut(liTo - liFrom,30);

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
</script>
<?
	include_once('../inc/_footer.php');
?>