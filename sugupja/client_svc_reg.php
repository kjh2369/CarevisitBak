<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$svcId = $_POST['svcId'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if (!empty($jumin)){
		$lbNew = false;
	}else{
		$lbNew = true;
	}

	$laSvcList = $conn->kind_list($code, $gHostSvc['voucher']);
	//$k_cnt  = sizeof($k_list);

	$__CURRENT_SVC_ID__ = $svcId;
	$__CURRENT_SVC_CD__ = $svcCd;
	$__CURRENT_SVC_NM__ = $conn->_svcNm($svcCd);
	$lbPop = true;?>
	<script type="text/javascript" src='./client.js'></script>
	<form id="f" name="f" method="post"><?

	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	switch($__CURRENT_SVC_ID__){
		case 11: //재가요양
			include_once('./client_svc_care.php');
			break;
		case 21: //가사간병
			include_once('./client_svc_nurse.php');
			break;
		case 22: //노인돌봄
			include_once('./client_svc_old.php');
			break;
		case 23: //산모신생아
			include_once('./client_svc_baby.php');
			break;
		case 24: //장애인보조
			include_once('./client_svc_dis.php');
			break;
		case 26: //재가지원
			include_once('./client_svc_support.php');
			break;
		default: //기타유료
			include('./client_svc_other.php');
	}

	if ($__CURRENT_SVC_ID__ != 26){
		include_once('./client_reg_history.php');
	}else{?>
		<span id="loPopLast" style=""></span><?
	}?>
	</div>

	<table id="loPopFoot" class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<tbody>
			<tr>
				<td class="right">
					<span class="btn_pack m"><button type="button" onclick="lfSave();">저장</button></span>
					<span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span>
				</td>
			</tr>
		</tbody>
	</table>

	<input id="code" name="code" type="hidden" value="<?=$code;?>">
	<input id="jumin" name="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
	<input id="svcCd" name="svcCd" type="hidden" value="<?=$svcCd;?>">
	<input id="svcId" name="svcId" type="hidden" value="<?=$svcId;?>">
	</form><?
	include_once('../inc/_footer.php');
?>
<script type="text/javascript">
$(window).resize(function(){
	var h = $(this).height();

	h -= $('#loPopBody').offset().top;
	h -= 28;

	$('#loPopBody').height(h);
});

$(document).ready(function(){
	__init_form(document.f);

	var h = 923; //$('#loPopLast').offset().top + 110;

	if ('<?=$__CURRENT_SVC_ID__;?>' == '21' || '<?=$__CURRENT_SVC_ID__;?>' == '22'){
		h = 455;
	}else if ('<?=$__CURRENT_SVC_ID__;?>' == '23'){
		h = 560;
	}else if ('<?=$__CURRENT_SVC_ID__;?>' == '31'){
		h = 481;
	}else if ('<?=$__CURRENT_SVC_ID__;?>' == '32' || '<?=$__CURRENT_SVC_ID__;?>' == '33'){
		h = 273;
	}

	if (h > screen.availHeight) h = screen.availHeight;

	window.resizeTo(500, h);
	$(window).resize();

	_clientSetMgmtData(); //장기요양보험
	//_clientSetKindData(); //수급자구분
	_clientSetLimitData(); //청구한도
	_clientSetNurseData(); //가사간병
	_clientSetOldData(); //노인돌봄
	_clientSetBabyData(); //산모신생아
	_clientSetDisData(); //장애인활동지원
	_clientSetLevelData('21','1');
	_clientSetLevelData('22','2');
	_clientSetLevelData('23','3');
	_clientSetLevelData('24','4');
});

//$(window).unload(function(){
//	opener.lfSvcMenu();
//});

function lfSave(){
	var para  = _clientSetPara();
	var objNm = '';

	$('.clsObjData').each(function(){
		if ($(this).attr('type') == 'radio' ||
			$(this).attr('type') == 'checkbox'){
			if ($(this).attr('checked')){
				para += (para ? '&' : '')+$(this).attr('name')+'='+$(this).val();
			}
		}else{
			para += (para ? '&' : '')+$(this).attr('id')+'='+$(this).val();
		}
	});

	$.ajax({
		type: 'POST',
		url : './client_save_use.php',
		data: {
			code :$('#code').val()
		,	jumin:$('#jumin').val()
		,	svcCd:$('#svcCd').val()
		,	svcId:$('#svcId').val()
		,   addPay1:$("input[name='addPay1']:checked").val()
		,	para :para
		},
		beforeSend: function (){
		},
		success: function (result){
			var msg = '';

			switch(result){
				case '1':
					msg = '정상적으로 처리되었습니다.';
					break;

				case '9':
					msg = '처리중 오류가 발생하였습니다.\n잠시 후 다시 시도하여 주십시오.';
					break;

				default:
					msg = result;
			}

			alert(msg);

			if (result == 1)
				lfClose();
		},
		error: function (){
		}
	}).responseXML;
}

function lfClose(){
	var lsFrom = $('#<?=$svcId;?>_gaeYakFm').val();
	var lsTo   = $('#<?=$svcId;?>_gaeYakTo').val();
	var today  = getToday();
	var liShow = 9;

	if (today >= lsFrom && today <= lsTo){
		liShow = 1;
	}


	opener.lfSvcDisplay('<?=$svcCd;?>','<?=$svcId;?>',liShow);
	self.close();
}

function clear_mem(cd, nm){
	var obj_cd = __getObject(cd);
	var obj_nm = __getObject(nm);

	obj_cd.value = '';
	obj_nm.value = '';
}
</script>