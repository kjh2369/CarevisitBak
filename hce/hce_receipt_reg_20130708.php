<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사례접수
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];
	$orgType = '40';

	if ($type == '11'){
		$careIPIN	= $hce->IPIN;
		$caseJumin	= $hce->jumin;
		$caseSeq	= $hce->seq;
	}else{
		$careIPIN	= '';
		$caseJumin	= '';
		$caseSeq	= 0;
	}

	//사례접수 마스터
	$sql = 'SELECT	*
			FROM	hce_elder
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$orgType.'\'
			AND		IPIN_hcelder= \''.$careIPIN.'\'';

	$row = $conn->get_array($sql);

	$elderEducLvl	= $row['educ_level'];	//학력
	$elderReligion	= $row['religion'];		//종교
	$elderMerry		= $row['merry_type'];	//결혼구분
	$elderCohabit	= $row['cohabit_type'];	//동거구분
	$elderGuardNm	= $row['guardian_nm'];	//보호자명
	$elderGuardAddr	= $row['addr_guard'];	//보호자주소
	$elderGuardTel	= $myF->phoneStyle($row['telno_guard']);	//보호자연락처

	Unset($row);

	if (!$elderReligion)	$elderReligion	= '9'; //종교

	//사례접수
	$sql = 'SELECT	*
			FROM	hce_receipt
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$orgType.'\'
			AND		hce_ssn		= \''.$caseJumin.'\'
			AND		hce_seq		= \''.$caseSeq.'\'
			AND		del_flag	= \'N\'';

	$row = $conn->get_array($sql);

	$tmpAddr = Explode(chr(11),$row['client_addr']);

	$rctDt		= $myF->dateStyle($row['rcpt_dt']);	//접수일자
	$rctSeq		= $row['seqno'];	//순번
	$rctNm		= $row['rcver_nm'];	//접수자
	$rctJumin	= $ed->en($row['rcver_ssn']);	//접수자 주민번호
	$rctGbn		= $row['counsel_type'];	//접수방법
	$elder		= $row['hce_elder_nm'];	//대상자명
	$ssn1		= SubStr($row['hce_ssn'],0,6);	//대상자 주민번호
	$ssn2		= SubStr($row['hce_ssn'],6);
	$rctNo		= $row['hce_seq'];	//차수
	$postno1	= $tmpAddr[0];	//주소
	$postno2	= $tmpAddr[1];
	$addr		= $tmpAddr[2];
	$addrDtl	= $tmpAddr[3];
	$phone		= $myF->phoneStyle($row['client_telno_loc']);	//유선
	$mobile		= $myF->phoneStyle($row['client_telno_mob']);	//무선
	$reqor		= $row['reqor_nm'];	//의뢰자
	$reqorRel	= $row['hcelder_rel'];	//관계
	$reqorTel	= $row['reqor_telno_loc'];	//의뢰자 연락처
	$text		= StripSlashes($row['counsel_text']);	//상담내용

	Unset($row);

	if (!$rctDt)		$rctDt			= Date('Y-m-d');	//접수일자
	if (!$rctGbn)		$rctGbn			= '1';				//접수구분
	if (!$reqorRel)		$reqorRel		= '99';				//의뢰인과의 관계
	if (!$rctNo)		$rctNo			= '1';				//차수
?>
<script type="text/javascript">
	function lfList(){
		location.href = '../hce/hce.php?type=1';
		return false;
	}

	function lfFindClient(){
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

		var parm = new Array();
			parm = {
				'type':'sugupja'
			,	'jumin':jumin
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'svcCd':'6'
			,	'return':'lfMemFindResult'
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
		try{
			$('#txtRctElder').val(obj[1]);
			$('#txtRctSsn1').val(obj[8].substring(0,6));
			$('#txtRctSsn2').val(obj[8].substring(7));
			$('#txtRctPostno1').val(obj[10]);
			$('#txtRctPostno2').val(obj[11]);
			$('#txtRctAddr').val(obj[12]);
			$('#txtRctAddrDtl').val(obj[13]);
			$('#txtRctPhone').val(__getPhoneNo(obj[18]));
			$('#txtRctMobile').val(__getPhoneNo(obj[19]));
		}catch(e){
			var obj = __parseStr(obj);

			$('#txtRctNm').val(obj['name']);
			$('#txtRctJumin').val(obj['jumin']);
		}
	}

	function lfSave(){
		if ($('#txtRctSeq').val() == '0' && !checkDate($('#txtRctDt').val())){
			alert('접수일자를 입력하여 주십시오.');
			$('#txtRctDt').focus();
			return false;
		}

		if (!$('#txtRctNm').val()){
			alert('접수자를 입력하여 주십시오.');
			$('#txtRctNm').focus();
			return false;
		}

		if (!$('#txtRctElder').val()){
			alert('대상자명을 입력하여 주십시오.');
			$('#txtRctElder').focus();
			return false;
		}

		if ($('#txtRctSeq').val() == '0' && $('#txtRctSsn1').val().length != $('#txtRctSsn1').attr('maxlength')){
			alert('대상자 주민번호 입력오류입니다. 확인하여 주십시오.');
			$('#txtRctSsn1').focus();
			return false;
		}

		if ($('#txtRctSeq').val() == '0' && $('#txtRctSsn2').val().length != $('#txtRctSsn2').attr('maxlength')){
			alert('대상자 주민번호 입력오류입니다. 확인하여 주십시오.');
			$('#txtRctSsn2').focus();
			return false;
		}

		if (!$('#txtRctReqor').val()){
			alert('의뢰인명을 입력하여 주십시오.');
			$('#txtRctReqor').focus();
			return false;
		}

		if (!$('#txtRctText').val()){
			alert('상담내용을 입력하여 주십시오.');
			$('#txtRctText').focus();
			return false;
		}

		$.ajax({
			type :'POST'
		,	url  :'./hce_apply.php'
		,	data :{
				'type'		:'<?=$type;?>'
			,	'rctDt'		:$('#txtRctDt').val()
			,	'rctSeq'	:$('#txtRctSeq').val()
			,	'rctNm'		:$('#txtRctNm').val()
			,	'rctJumin'	:$('#txtRctJumin').val()
			,	'rctNo'		:$('#lblRctNo').text()
			,	'rctGbn'	:$('input:radio[name="optRctGbn"]:checked').val()
			,	'elder'		:$('#txtRctElder').val()
			,	'ssn1'		:$('#txtRctSsn1').val()
			,	'ssn2'		:$('#txtRctSsn2').val()
			,	'postno1'	:$('#txtRctPostno1').val()
			,	'postno2'	:$('#txtRctPostno2').val()
			,	'addr'		:$('#txtRctAddr').val()
			,	'addrDtl'	:$('#txtRctAddrDtl').val()
			,	'phone'		:$('#txtRctPhone').val()
			,	'mobile'	:$('#txtRctMobile').val()
			,	'reqor'		:$('#txtRctReqor').val()
			,	'reqorRel'	:$('#cboRctReqorRel option:selected').val()
			,	'reqorTel'	:$('#txtRctReqorTel').val()
			,	'text'		:$('#txtRctText').val()

			,	'educLvl'	:$('#cboEducLvl option:selected').val()
			,	'religion'	:$('#cboReligion option:selected').val()
			,	'marry'		:$('#cboMarry option:selected').val()
			,	'cohabit'	:$('#cboCohabit option:selected').val()

			,	'guardNm'	:$('#txtGuardNm').val()
			,	'guardTel'	:$('#txtGuardTelNo').val()
			,	'guardAddr'	:$('#txtGuardAddr').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				$('#tempLodingBar').remove();

				if (result == 1){
					alert('정상적으로 처리되었습니다.');

					if ('<?=$type;?>' == '2'){
						lfSetTarget();
					}
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="right last">
				<span class="btn_pack m"><span class="check"></span><button type="button" class="bold" onclick="return false;">재작성</button></span>
				<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="return lfSave();">저장</button></span>
				<span class="btn_pack m"><span class="list"></span><button type="button" class="bold" onclick="return lfList();">리스트</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="70px">
		<col width="50px">
		<col width="70px">
		<col width="20px">
		<col width="70px">
		<col width="340px">
		<col width="40px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="bold last" colspan="10">1. 접수정보</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center">접수일자</th>
			<td class="center"><?
				if ($rctSeq > 0){?>
					<div class="center"><?=$myF->dateStyle($rctDt,'.');?></div>
					<input id="txtRctDt" type="hidden" value="<?=$rctDt;?>" class="date"><?
				}else{?>
					<input id="txtRctDt" name="txt" type="text" value="<?=$rctDt;?>" class="date"><?
				}?>
			</td>
			<th class="center">접수자</th>
			<td class="center last">
				<input id="txtRctNm" name="txt" type="text" value="<?=$rctNm;?>" style="width:100%;" alt="not" readonly>
			</td>
			<td class="center">
				<span class="btn_pack find" onclick="lfMemFind();"></span>
			</td>
			<th class="center">접수방법</th>
			<td><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'CT\'
						AND		use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<input id="optRctGbn<?=$row['code'];?>" name="optRctGbn" type="radio" value="<?=$row['code'];?>" class="radio" <?=($rctGbn == $row['code'] ? 'checked' : '');?>><label for="optRctGbn<?=$row['code'];?>"><?=$row['name'];?></label><?
				}

				$conn->row_free();?>
			</td>
			<th class="center">차수</th>
			<td class="center last"><div id="lblRctNo" class="left"><?=$rctNo;?> 차</div></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="60px">
		<col width="140px">
		<col width="50px">
		<col width="40px">
		<col width="90px">
		<col width="50px">
		<col width="50px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="bold last" colspan="20">2. 대상자정보</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="center">
				<input id="txtRctElder" name="txt" type="text" value="<?=$elder;?>" style="width:100%;">
			</td>
			<th class="center">주민번호</th>
			<td class=""><?
				if ($rctSeq > 0){?>
					<div class="left"><?=$ssn1;?>-<?=SubStr($ssn2,0,1);?>******</div>
					<input id="txtRctSsn1" type="hidden" value="<?=$ed->en($ssn1);?>">
					<input id="txtRctSsn2" type="hidden" value="<?=$ed->en($ssn2);?>"><?
				}else{?>
					<input id="txtRctSsn1" name="txt" type="text" value="<?=$ssn1;?>" maxlength="6" class="no_string" style="width:50px;"> -
					<input id="txtRctSsn2" name="txt" type="text" value="<?=$ssn2;?>" maxlength="7" class="no_string" style="width:55px;"><?
				}?>
			</td>
			<td class="center last"><div class="left"><span class="btn_pack small"><button type="button" onclick="lfFindClient();">고객찾기</button></span></div></td>
			<td class="center last">&nbsp;</td>
			<td class="center last">&nbsp;</td>
			<td class="center last">&nbsp;</td>
			<td class="center last">&nbsp;</td>
			<td class="center last">&nbsp;</td>
			<td class="center last">&nbsp;</td>
		</tr>
		<tr>
			<th class="center" rowspan="3">주소</th>
			<td class="" colspan="3">
				<input id="txtRctPostno1" name="txt" type="text" value="<?=$postno1;?>" maxlength="3" class="no_string" style="width:30px;"> -
				<input id="txtRctPostno2" name="txt" type="text" value="<?=$postno2;?>" maxlength="3" class="no_string" style="width:30px; margin-right:0;">
				<span class="btn_pack small"><button type="button" onClick="__helpAddress(__getObject('txtRctPostno1'),__getObject('txtRctPostno2'),__getObject('txtRctAddr'),__getObject('txtRctAddrDtl'));">찾기</button></span>
			</td>
			<th class="center" rowspan="2">연락처</th>
			<th class="center">유선</th>
			<td class="">
				<input id="txtRctPhone" name="txt" type="text" value="<?=$phone;?>" maxlength="12" class="phone">
			</td>
			<th class="center">학력</th>
			<td class="last" colspan="3">
				<select id="cboEducLvl" name="cbo" style="width:auto;">
					<option value="">-</option><?
					$sql = 'SELECT code,name
							FROM   hce_gbn
							WHERE  type		= \'EL\'
							AND    use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>" <?=($row['code'] == $elderEducLvl ? 'selected' : '');?>><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="" colspan="3">
				<input id="txtRctAddr" name="txt" type="text" value="<?=$addr;?>" style="width:100%;">
			</td>
			<th class="center">무선</th>
			<td class="">
				<input id="txtRctMobile" name="txt" type="text" value="<?=$mobile;?>" maxlength="12" class="phone">
			</td>
			<th class="center">종교</th>
			<td class="last" colspan="3">
				<select id="cboReligion" name="cbo" style="width:auto;"><?
					$sql = 'SELECT code,name
							FROM   hce_gbn
							WHERE  type		= \'RG\'
							AND    use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>" <?=($row['code'] == $elderReligion ? 'selected' : '');?>><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="" colspan="3">
				<input id="txtRctAddrDtl" name="txt" type="text" value="<?=$addrDtl;?>" style="width:100%;">
			</td>
			<th class="center">결혼정보</th>
			<th class="center">결혼</th>
			<td class="">
				<select id="cboMarry" name="cbo" style="width:auto;">
					<option value="">-</option><?
					$sql = 'SELECT code,name
							FROM   hce_gbn
							WHERE  type		= \'MR\'
							AND    use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>" <?=($row['code'] == $elderMerry ? 'selected' : '');?>><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
			<th class="center">동거</th>
			<td class="center last">
				<select id="cboCohabit" name="cbo" style="width:auto;">
					<option value="">-</option><?
					$sql = 'SELECT code,name
							FROM   hce_gbn
							WHERE  type		= \'CB\'
							AND    use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>" <?=($row['code'] == $elderCohabit ? 'selected' : '');?>><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
			<td class="center last">&nbsp;</td>
			<td class="center last">&nbsp;</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="60px">
		<col width="90px">
		<col width="40px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="bold last" colspan="10">3. 보호자정보</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="center"><input id="txtGuardNm" name="txt" type="text" value="<?=$elderGuardNm;?>" style="width:100%;"></td>
			<th class="center">연락처</th>
			<td class="center"><input id="txtGuardTelNo" name="txt" type="text" value="<?=$elderGuardTel;?>" maxlength="12" class="phone"></td>
			<th class="center">주소</th>
			<td class="center last"><input id="txtGuardAddr" name="txt" type="text" value="<?=$elderGuardAddr;?>" style="width:100%;"></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="40px">
		<col width="50px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="bold last" colspan="10">4. 의뢰인정보</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="center">
				<input id="txtRctReqor" name="txt" type="text" value="<?=$reqor;?>" style="width:100%;">
			</td>
			<th class="center">관계</th>
			<td class="center">
				<select id="cboRctReqorRel" name="cbo" style="width:auto;"><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type = \'HR\'
							AND		use_yn = \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>" <?=($reqorRel == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
			<th class="center">연락처</th>
			<td class="last">
				<input id="txtRctReqorTel" name="txt" type="text" value="<?=$reqorTel;?>" maxlength="12" class="phone">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="bold last" colspan="10">5. 상담내용</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center">상담내용</th>
			<td class="center last">
				<textarea id="txtRctText" name="txts" style="width:100%; height:150px;"><?=$text;?></textarea>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<input id="txtRctSeq" type="hidden" value="<?=$rctSeq;?>">
<input id="txtRctJumin" type="hidden" value="<?=$rctJumin;?>">
<?
	include_once('../inc/_db_close.php');
?>