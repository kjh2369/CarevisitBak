<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$orgNo	= $_SESSION['userCenterCode'];
	$IPIN	= $_POST['IPIN'];

	$sql = 'SELECT	*
			FROM	care_actual_research
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$sr.'\'
			AND		IPIN	= \''.$IPIN.'\'';

	$r = $conn->get_array($sql);
	$date = $r['iver_dt'];

	//대상자명
	$sql = 'SELECT	name
			FROM	care_client_normal
			WHERE	org_no		= \''.$orgNo.'\'
			AND		normal_sr	= \''.$sr.'\'
			AND		normal_seq	= \''.$IPIN.'\'';

	$name = $conn->get_data($sql);

	//가족관계
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type  = \'HR\'
			AND		code != \'99\'
			AND		use_yn= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	echo '<script type="text/javascript">';
	echo 'var familyReg = {};';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		echo 'familyReg["'.$row['code'].'"] = "'.$row['name'].'";';
	}

	echo '</script>';

	$conn->row_free();

	if (!$date) $date = Date('Y-m-d');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:checkbox[name="chkIADL"],input:checkbox[name="chkADL"]').unbind('click').click(function(){
			var name = $(this).attr('name');

			if ($(this).attr('oldVal') == $(this).val()){
				$(this).attr('oldVal','');
				$(this).attr('checked',false);
				return true;
			}

			$('input:checkbox[name="'+name+'"]').attr('checked',false);
			$(this).attr('checked',true);
			$('input:checkbox[name="'+name+'"]').attr('oldVal',$(this).val());
		});

		if ($('#txtTGer').attr('key')){
			lfTargetInfo();
			lfLoadFamily();
		}
	});

	function lfMemFind(rtn){
		var jumin = $('#txtClient').attr('jumin');
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CLIENT', option);
			win.opener = self;
			win.focus();

		if (!rtn) rtn = 'lfMemFindResult';

		var parm = new Array();
			parm = {
				'type':'member'
			,	'jumin':jumin
			,	'kind':'<?=$sr;?>'
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'return':rtn
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type','hidden');
			objs.setAttribute('name',key);
			objs.setAttribute('value',parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target','FIND_CLIENT');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtIVer').attr('jumin',obj['jumin']).val(obj['name']);
	}

	//대상자조회
	function lfTargetFind(){
		var jumin = '';
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CLIENT', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type':'sugupja'
			,	'jumin':jumin
			,	'svcCd':'<?=$sr;?>'
			,	'rtnType':'key'
			,	'wrkType':'CARE_CLIENT_NORMAL'
			,	'return':'lfTargetFindResult'
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type','hidden');
			objs.setAttribute('name',key);
			objs.setAttribute('value',parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target','FIND_CLIENT');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfTargetFindResult(obj){
		$('#txtTGer').attr('key',obj[0]).val(obj[1]);

		//성명
		$('#lblElderName').text(obj[1]);

		lfTargetInfo();
	}

	//대상자 정보조회
	function lfTargetInfo(){
		$.ajax({
			type: 'POST'
		,	url : '../hce/hce_find.php'
		,	data: {
				'type':'TARGET_INFO'
			,	'svcCd':'<?=$sr;?>'
			,	'key':$('#txtTGer').attr('key')
			,	'wrkType':'<?=$type;?>'
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				var col = __parseVal(data);

				if (col['age']) col['age'] += '세';

				$('#lblElderName').text(col['name']);		//성명
				$('#lblElderGender').text(col['gender']);	//	성별
				$('#lblElderAge').text(col['age']);			//	연령
				$('#lblElderSSN').text(col['jumin']);		//	주민번호
				$('#lblElderEdu').text(col['edu']);			//	학력
				$('#lblElderReg').text(col['rel']);			//	종교
				$('#lblElderAddr').text(col['addr']);		//	주소
				$('#lblElderPhone').text(col['telno']);		//	연락처

				if (__str2num(col['cnt']) > 0){
					$('#btnBasicInterviewLoad').attr('disabled',false);
				}else{
					$('#btnBasicInterviewLoad').attr('disabled',true);
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	//가족추가
	function lfAddFamily(col){
		var html = '';
		var cnt = $('#tbodyFamily tr').length+1;

		if (col){
			if (!col['rel'])	col['rel']		= '';
			if (!col['name'])	col['name']		= '';
			if (!col['addr'])	col['addr']		= '';
			if (!col['age'])	col['age']		= '';
			if (!col['job'])	col['job']		= '';
			if (!col['cohabit'])col['cohabit']	= '';
			if (!col['monthly'])col['monthly']	= '';
			if (!col['remark'])	col['remark']	= '';
		}else{
			var col = {};

			col['rel']		= '';
			col['name']		= '';
			col['addr']		= '';
			col['age']		= '';
			col['job']		= '';
			col['cohabit']	= '';
			col['monthly']	= '';
			col['remark']	= '';
		}

		html += '<tr>';
		html += '<td>';
		html += '<select id="cboFamilyGbn" name="cbo" style="width:auto;" onchange="lfChkFamily(this);">';
		html += '<option value="">-</option>';

		for(var i in familyReg){
			html += '<option value="'+i+'" '+(col['rel'] == i ? 'selected' : '')+'>'+familyReg[i]+'</option>';
		}

		html += '</select>';
		html += '</td>';
		html += '<td><input id="txtFamilyName" name="txt" type="text" value="'+col['name']+'" style="width:100%;"></td>';
		html += '<td><input id="txtFamilyAddr" name="txt" type="text" value="'+col['addr']+'" style="width:100%;"></td>';
		html += '<td><input id="txtFamilyAge" name="txt" type="text" value="'+col['age']+'" style="width:100%;"></td>';
		html += '<td><input id="txtFamilyJob" name="txt" type="text" value="'+col['job']+'" style="width:100%;"></td>';
		html += '<td>';
		html += '<select id="cboFamilyCohabit" name="cbo" style="width:auto;">';
		html += '<option value="Y" '+(col['cohabit'] == 'Y' ? 'selected' : '')+'>예</option>';
		html += '<option value="N" '+(col['cohabit'] != 'Y' ? 'selected' : '')+'>아니오</option>';
		html += '</select>';
		html += '</td>';
		html += '<td><input id="txtFamilyMonthly" name="txt" type="text" value="'+col['monthly']+'" style="width:100%;"></td>';
		html += '<td><input id="txtFamilyRemark" name="txt" type="text" value="'+col['remark']+'" style="width:100%;"></td>';
		html += '<td class="last">';
		html += '<div class="left"><span class="btn_pack m"><span class="delete"></span><button type="button" onclick="lfDelFamily($(this).parent().parent().parent().parent());">삭제</button></span></div>';
		html += '</td>';
		html += '</tr>';

		$('.removeRow').remove();

		if ($('#tbodyFamily tr:last').length > 0){
			$('#tbodyFamily tr:last').after(html);
		}else{
			$('#tbodyFamily').html(html);
		}

		__init_form(document.f);
	}

	//가족삭제
	function lfDelFamily(obj){
		$(obj).remove();
	}

	//가족 추가가능여부
	function lfChkFamily(obj){
		var gbn = $(obj).val();
		var rst = true;

		if (gbn != '01' && gbn != '02') return true;

		$('select[id^="cboFamilyGbn_"]').each(function(){
			if ($(this).attr('id') != $(obj).attr('id')){
				if ($(this).val() == gbn){
					rst = false;
					return false;
				}
			}
		});

		var msg = '';

		if (!rst){
			if (gbn == '01') msg = '"부"는';
			if (gbn == '02') msg = '"모"는';

			msg += ' 한명만 등록할 수 있습니다.';

			alert(msg);

			$(obj).val('');
		}

		return rst;
	}

	//초기상담기록지 데이타 로드
	function lfBasicInterviewLoad(){
		lfLoadFamily();
		lfLoadInterview();
	}

	//가족사항 조회
	function lfLoadFamily(){
		$.ajax({
			type: 'POST'
		,	url : '../hce/hce_find.php'
		,	data: {
				'type':'FAMILY'
			,	'SR':'<?=$sr;?>'
			,	'key':$('#txtTGer').attr('key')
			,	'IsBasic':'CLIENT_NORMAL'
			,	'rel':'SHOW'
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				var row = data.split(String.fromCharCode(11));

				$('#tbodyFamily tr').remove();

				/*
				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						lfAddFamily(col);
					}
				}
				*/

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var html = '';

						html += '<tr>';
						html += '<td class="center">'+col['rel']+'</td>';
						html += '<td class="center">'+col['name']+'</td>';
						html += '<td class="center">'+col['addr']+'</td>';
						html += '<td class="center">'+col['age']+'</td>';
						html += '<td class="center">'+col['job']+'</td>';
						html += '<td class="center">'+col['cohabit']+'</td>';
						html += '<td class="center">'+col['monthly']+'</td>';
						html += '<td class="center">'+col['remark']+'</td>';
						html += '<td class="last"></td>';
						html += '</tr>';

						if ($('#tbodyFamily tr:last').length > 0){
							$('#tbodyFamily tr:last').after(html);
						}else{
							$('#tbodyFamily').html(html);
						}
					}
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfLoadInterview(){
		$.ajax({
			type: 'POST'
		,	url : '../care/care_interview_load.php'
		,	data: {
				'SR':'<?=$sr;?>'
			,	'key':$('#txtTGer').attr('key')
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				var col = __parseVal(data);

				for(var i in col){
					if (i.substr(0,3) == 'opt'){
						$('input:radio[name="'+i+'"][value="'+col[i]+'"]').attr('checked',true);
					}else if (i.substr(0,3) == 'checkbox'){
						$('#'+i).attr('checked',true);
					}else if (i.substr(0,3) == 'txt'){
						$('#'+i).val(col[i]);
					}else if (i == 'chkDisease' || i == 'chkSvcReq' || i == 'chkSvcOff'){
						var val = col[i].split('/');
						for(var j=0; j<val.length; j++){
							var str = val[j].split(':');
							if (str[1] == 'Y'){
								$('#'+i+'_'+str[0]).attr('checked',true);
							}
						}
					}else{
						alert(i+':'+col[i]);
					}
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfSave(){
		if (!$('#txtIVDt').val()){
			alert('면접일을 입력하여 주십시오.');
			$('#txtIVDt').focus();
			return;
		}

		if (!$('#txtTGer').val()){
			alert('대상자를 선택하여 주십시오.');
			lfTargetFind();
			return;
		}

		if (!$('#txtIVer').val()){
			alert('담당자를 선택하여 주십시오.');
			lfMemFind();
			return;
		}

		var data = {};

		data['key'] = $('#txtTGer').attr('key');
		data['iverCd'] = $('#txtIVer').attr('jumin');

		$('input:text,input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('input:radio').each(function(){
			var name= $(this).attr('name');
			var val	= $('input:radio[name="'+name+'"]:checked').val();

			if (!val) val = '';

			data[name] = val;
		});

		$('input:checkbox').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).attr('checked') ? 'Y' : 'N';

			if (!val) val = '';

			data[id] = val;
		});

		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'../care/care_actual_research_reg_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (__resultMsg(result)){
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">지원대상 실태조사표(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="lfSave();">저장</button></span>
		<span class="btn_pack m"><span class="pdf"></span><button type="button" class="bold" onclick="">출력</button></span>
	</div>
</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="50px">
		<col width="50px">
		<col width="70px">
		<col width="20px">
		<col width="50px">
		<col width="70px">
		<col width="20px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">면접일</th>
			<td class="center">
				<input id="txtIVDt" name="txt" type="text" value="<?=$date;?>" class="date">
			</td>
			<th class="center">대상자</th>
			<td class="center last">
				<input id="txtTGer" name="txt" type="text" value="<?=$name;?>" key="<?=$IPIN;?>" style="width:100%; border:none;" alt="not" readonly>
			</td>
			<td class="center"><?
				if (!$IPIN){?>
					<span class="btn_pack find" onclick="lfTargetFind();"></span><?
				}?>
			</td>
			<th class="center">담당자</th>
			<td class="center last">
				<input id="txtIVer" name="txt" type="text" value="<?=$r['iver_nm'];?>" jumin="<?=$ed->en($r['iver_cd']);?>" style="width:100%; border:none;" alt="not" readonly>
			</td>
			<td class="center">
				<span class="btn_pack find" onclick="lfMemFind();"></span>
			</td>
			<td class="center last">
				<div class="right">
					<span class="btn_pack m"><button id="btnBasicInterviewLoad" onclick="lfBasicInterviewLoad();" disabled="true">초기상담기록지 가져오기</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="40px">
		<col width="50px">
		<col width="40px">
		<col width="50px">
		<col width="60px">
		<col width="95px">
		<col width="40px">
		<col width="110px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold last" colspan="20">1. 기본사항</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="center"><div id="lblElderName" class="left"></div></td>
			<th class="center">성별</th>
			<td class="center"><div id="lblElderGender"></div></td>
			<th class="center">연령</th>
			<td class="center"><div id="lblElderAge"></div></td>
			<th class="center">주민번호</th>
			<td class="center"><div id="lblElderSSN"></div></td>
			<th class="center">학력</th>
			<td class="center"><div id="lblElderEdu" class="left"></div></td>
			<th class="center">종교</th>
			<td class="center last"><div id="lblElderReg" class="left"></div></td>
		</tr>
		<tr>
			<th class="center">주소</th>
			<td class="center" colspan="9"><div id="lblElderAddr" class="left"></div></td>
			<th class="center">연락처</th>
			<td class="center last"><div id="lblElderPhone" class="left"></div></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="71px">
		<col width="80px">
		<col width="150px">
		<col width="50px">
		<col width="100px">
		<col width="72px">
		<col width="80px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">2. 가족사항</th>
		</tr>
		<tr>
			<th class="head">관계</th>
			<th class="head">성명</th>
			<th class="head">주소</th>
			<th class="head">연령</th>
			<th class="head">직업</th>
			<th class="head">동거여부</th>
			<th class="head">월소득액</th>
			<th class="head">비고</th>
			<th class="last">
				<!--span class="btn_pack m"><span class="add"></span><button type="button" onclick="lfAddFamily();">추가</button></span-->
			</th>
		</tr>
	</tbody>
	<tbody id="tbodyFamily">
		<!--tr class="removeRow">
			<td class="right last" colspan="10">※추가버튼을 클릭하여 가족구성원을 추가하여 주십시오.</td>
		</tr-->
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">3. 보호형태</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="left">1)사업대상</th>
			<td class="last">
				<label><input id="optBizTarget1" name="optBizTarget" type="radio" class="radio" value="1" <?=$r['biz_target'] == '1' ? 'checked' : '';?>>국민기초생활수급권자</label>
				<label><input id="optBizTarget2" name="optBizTarget" type="radio" class="radio" value="2" <?=$r['biz_target'] == '2' ? 'checked' : '';?>>기타 의료급여자(차상위)</label>
				<label><input id="optBizTarget3" name="optBizTarget" type="radio" class="radio" value="3" <?=$r['biz_target'] == '3' ? 'checked' : '';?>>전국가구 월평균소득150% 이하</label>
				<label><input id="optBizTarget4" name="optBizTarget" type="radio" class="radio" value="4" <?=$r['biz_target'] == '4' ? 'checked' : '';?>>전국가구 월평균소득150% 초과</label>
			</td>
		</tr>
		<tr>
			<th class="left">2)동거실태</th>
			<td class="last">
				<label><input id="optCohabit1" name="optCohabit" type="radio" class="radio" value="1" <?=$r['cohabit'] == '1' ? 'checked' : '';?>>독거노인</label>
				<label><input id="optCohabit2" name="optCohabit" type="radio" class="radio" value="2" <?=$r['cohabit'] == '2' ? 'checked' : '';?>>노인2인가구</label>
				<label><input id="optCohabit9" name="optCohabit" type="radio" class="radio" value="9" <?=$r['cohabit'] == '9' ? 'checked' : '';?>>기타</label>
			</td>
		</tr>
		<tr>
			<th class="left">3)주거형태</th>
			<td class="last">
				<label><input id="optDwelling1" name="optDwelling" type="radio" class="radio" value="1" <?=$r['dwelling'] == '1' ? 'checked' : '';?>>자가</label>
				<label><input id="optDwelling2" name="optDwelling" type="radio" class="radio" value="2" <?=$r['dwelling'] == '2' ? 'checked' : '';?>>전세</label>
				<label><input id="optDwelling3" name="optDwelling" type="radio" class="radio" value="3" <?=$r['dwelling'] == '3' ? 'checked' : '';?>>월세</label>
				<label><input id="optDwelling9" name="optDwelling" type="radio" class="radio" value="9" <?=$r['dwelling'] == '9' ? 'checked' : '';?>>기타</label>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="120px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">4. 건강상태</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="left">1)질환명</th>
			<td class="last" colspan="2"><?
				if ($r['disease_gbn']){
					$tmp = Explode('/',$r['disease_gbn']);
					foreach($tmp as $t){
						$s = Explode(':',$t);
						$arr[$s[0]] = $s[1];
					}
				}

				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'DT\'
						AND		use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<div style="float:left; width:23%;">
						<label><input id="chkDisease_<?=$row['code'];?>" name="chkDisease" type="checkbox" class="checkbox" value="<?=$row['code'];?>" <?=$arr[$row['code']] == 'Y' ? 'checked' : '';?>><?=$row['name'];?></label>
					</div><?
				}

				$conn->row_free();
				Unset($arr);?>
			</td>
		</tr>
		<tr>
			<th class="left" rowspan="7">2)장애상태</th>
			<td class="last" colspan="2">
				<label><input id="chkIADL1" name="chkIADL" type="checkbox" class="checkbox" value="1" <?=$r['IADL'] == '1' ? 'checked' : '';?>>IADL일부제한</label>
				<label><input id="chkIADL2" name="chkIADL" type="checkbox" class="checkbox" value="2" <?=$r['IADL'] == '2' ? 'checked' : '';?>>IADL모두제한</label>
				<label><input id="chkADL1" name="chkADL" type="checkbox" class="checkbox" value="1" <?=$r['ADL'] == '1' ? 'checked' : '';?>>ADL일부제한</label>
				<label><input id="chkADL2" name="chkADL" type="checkbox" class="checkbox" value="2" <?=$r['ADL'] == '2' ? 'checked' : '';?>>ADL모두제한</label>
			</td>
		</tr>
		<tr>
			<td class="center last" colspan="2">
				<input id="txtHandicap" name="txt" type="text" value="<?=StripSlashes($r['handicap']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">시각장애</th>
			<td class="center last">
				<input id="txtBlind" name="txt" type="text" value="<?=StripSlashes($r['blind']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">청각장애</th>
			<td class="center last">
				<input id="txtHypacusis" name="txt" type="text" value="<?=StripSlashes($r['hypacusis']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">언어장애</th>
			<td class="center last">
				<input id="txtLalopathy" name="txt" type="text" value="<?=StripSlashes($r['lalopathy']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">신체장애</th>
			<td class="center last">
				<input id="txtMaimedness" name="txt" type="text" value="<?=StripSlashes($r['maimedness']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">기&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;타</th>
			<td class="center last">
				<input id="txtHandicapOther" name="txt" type="text" value="<?=StripSlashes($r['handicap_other']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="left">3)장기요양 등급판정</th>
			<td class="last" colspan="2">
				<label><input id="optLongtermLvl1" name="optLongtermLvl" type="radio" class="radio" value="1" <?=$r['longterm_lvl'] == '1' ? 'checked' : '';?>>등급내자(1~3급)</label>
				<label><input id="optLongtermLvlA" name="optLongtermLvl" type="radio" class="radio" value="A" <?=$r['longterm_lvl'] == 'A' ? 'checked' : '';?>>등급외 A</label>
				<label><input id="optLongtermLvlB" name="optLongtermLvl" type="radio" class="radio" value="B" <?=$r['longterm_lvl'] == 'B' ? 'checked' : '';?>>등급외 B</label>
				<label><input id="optLongtermLvlC" name="optLongtermLvl" type="radio" class="radio" value="C" <?=$r['longterm_lvl'] == 'C' ? 'checked' : '';?>>등급외 C</label>
				<label><input id="optLongtermLvl9" name="optLongtermLvl" type="radio" class="radio" value="9" <?=$r['longterm_lvl'] == '9' ? 'checked' : '';?>>무등급자</label>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">5. 신청서비스내용</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="center bottom last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<tbody><?
						if ($r['svc_req']){
							$tmp = Explode('/',$r['svc_req']);
							foreach($tmp as $t){
								$s = Explode(':',$t);
								$arr[$s[0]] = $s[1];
							}
						}

						$sql = 'SELECT	DISTINCT
										care.suga_cd AS cd
								,		suga.nm1 AS mst_nm
								,		suga.nm2 AS pro_nm
								,		suga.nm3 AS svc_nm
								FROM	care_suga AS care
								INNER	JOIN	suga_care AS suga
										ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
										AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
										AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
								WHERE	care.org_no	= \''.$orgNo.'\'
								AND		care.suga_sr= \''.$sr.'\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();
						$idx = 1;

						$tmpStr1 = '';
						$tmpStr2 = '';

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);

							if ($tmpStr1 != SubStr($row['cd'],0,1)){
								$tmpStr1  = SubStr($row['cd'],0,1);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:20px;"><?=SubStr($row['cd'],0,1).'. '.Str_Replace('<br>','',$row['mst_nm']);?></th>
								</tr><?
							}

							if ($tmpStr2 != SubStr($row['cd'],0,3)){
								$tmpStr2  = SubStr($row['cd'],0,3);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:35px;"><?=SubStr($row['cd'],1,2).'. '.Str_Replace('<br>','',$row['pro_nm']);?></th>
								</tr><?
							}

							if ($idx % 3 == 1){?>
								<tr><td class="last" style="padding-left:50px;"><?
							}?>
							<div style="float:left; width:30%;"><label><input id="chkSvcReq_<?=$row['cd'];?>" name="chkSvcReq" type="checkbox" class="checkbox" value="<?=$row['cd'];?>" <?=$arr[$row['cd']] == 'Y' ? 'checked' : '';?>><?=$row['svc_nm'];?></label></div><?

							if ($idx == 3){
								$idx = 1;?>
								</td></tr><?
							}else{
								$idx ++;
							}
						}

						$conn->row_free();
						Unset($arr);?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="110px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">6. 평가결과</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="left" rowspan="2">1)서비스 제공여부</th>
			<td class="">
				<label><input id="optSvcOfferY" name="optSvcOffer" type="radio" class="radio" value="Y" <?=$r['svc_offer'] == 'Y' ? 'checked' : '';?>>적격</label>
			</td>
			<th class="center" rowspan="2">부적격사유</th>
			<td class="last" rowspan="2">
				<label><input id="optNoOfferRsn1" name="optNoOfferRsn" type="radio" class="radio" value="1" <?=$r['nooffer_rsn'] == '1' ? 'checked' : '';?>>자조능력있음</label>
				<label><input id="optNoOfferRsn2" name="optNoOfferRsn" type="radio" class="radio" value="2" <?=$r['nooffer_rsn'] == '2' ? 'checked' : '';?>>경제력있음</label>
				<label><input id="optNoOfferRsn3" name="optNoOfferRsn" type="radio" class="radio" value="3" <?=$r['nooffer_rsn'] == '3' ? 'checked' : '';?>>노읜의 요구와 기관의 서비스가 서로 맞지 않음</label>
				<label><input id="optNoOfferRsn4" name="optNoOfferRsn" type="radio" class="radio" value="4" <?=$r['nooffer_rsn'] == '4' ? 'checked' : '';?>>노인의 거부</label>
				<label><input id="optNoOfferRsn9" name="optNoOfferRsn" type="radio" class="radio" value="9" <?=$r['nooffer_rsn'] == '9' ? 'checked' : '';?>>기타</label>(<input id="txtNoOfferRsn" name="txt" type="text" value="<?=StripSlashes($r['nooffer_rsn_other']);?>" style="width:150px; background-color:#efefef;" disabled="true">)
			</td>
		</tr>
		<tr>
			<td class="">
				<label><input id="optSvcOfferN" name="optSvcOffer" type="radio" class="radio" value="N" <?=$r['svc_offer'] == 'N' ? 'checked' : '';?>>부적격</label>
			</td>
		</tr>
		<tr>
			<th class="left">2)서비스사유</th>
			<td class="last" colspan="3"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type= \'SRG\'
						AND	use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optSvcRsnGbn_<?=$row['code'];?>" name="optSvcRsnGbn" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtSvcRsnOther" <?=($r['svc_rsn_gbn'] == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?

					if ($row['code'] == '9'){?>
						(<input id="txtSvcRsnOther" name="txt" type="text" value="<?=StripSlashes($r['svc_rsn_other']);?>" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="left">3)제공서비스내용</th>
			<td class="center bottom last" colspan="3">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<tbody><?
						if ($r['svc_off']){
							$tmp = Explode('/',$r['svc_off']);
							foreach($tmp as $t){
								$s = Explode(':',$t);
								$arr[$s[0]] = $s[1];
							}
						}

						$sql = 'SELECT	DISTINCT
										care.suga_cd AS cd
								,		suga.nm1 AS mst_nm
								,		suga.nm2 AS pro_nm
								,		suga.nm3 AS svc_nm
								FROM	care_suga AS care
								INNER	JOIN	suga_care AS suga
										ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
										AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
										AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
								WHERE	care.org_no	= \''.$orgNo.'\'
								AND		care.suga_sr= \''.$sr.'\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();
						$idx = 1;

						$tmpStr1 = '';
						$tmpStr2 = '';

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);

							if ($tmpStr1 != SubStr($row['cd'],0,1)){
								$tmpStr1  = SubStr($row['cd'],0,1);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:20px;"><?=SubStr($row['cd'],0,1).'. '.Str_Replace('<br>','',$row['mst_nm']);?></th>
								</tr><?
							}

							if ($tmpStr2 != SubStr($row['cd'],0,3)){
								$tmpStr2  = SubStr($row['cd'],0,3);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:35px;"><?=SubStr($row['cd'],1,2).'. '.Str_Replace('<br>','',$row['pro_nm']);?></th>
								</tr><?
							}

							if ($idx % 3 == 1){?>
								<tr><td class="last" style="padding-left:50px;"><?
							}?>
							<div style="float:left; width:30%;"><label><input id="chkSvcOff_<?=$row['cd'];?>" name="chkSvcOff" type="checkbox" class="checkbox" value="<?=$row['cd'];?>" <?=$arr[$row['cd']] == 'Y' ? 'checked' : '';?>><?=$row['svc_nm'];?></label></div><?

							if ($idx == 3){
								$idx = 1;?>
								</td></tr><?
							}else{
								$idx ++;
							}
						}

						$conn->row_free();
						Unset($arr);?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">7. 의뢰인</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="center">
				<input id="txtReqName" name="txt" type="text" value="<?=$r['req_name'];?>" style="width:100%;">
			</td>
			<th class="center">대상자와의 관계</th>
			<td class="center">
				<input id="txtReqRel" name="txt" type="text" value="<?=$r['req_rel'];?>" style="width:100%;">
			</td>
			<th class="center">연락처</th>
			<td class="last">
				<input id="txtReqTelno" name="txt" type="text" value="<?=$myF->phoneStyle($r['rel_telno']);?>" class="phone">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">8. 비고</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="center bottom last">
				<textarea id="txtOther" style="width:100%; height:50px;"><?=StripSlashes($r['other']);?></textarea>
			</td>
		</tr>
	</tbody>
</table>
<?
	Unset($r);
	include_once('../inc/_db_close.php');
?>