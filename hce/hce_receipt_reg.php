<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사례접수
	 *********************************************************/

	if ($type == '11'){
		$IPIN	= $hce->IPIN;
		$rcpt	= $hce->rcpt;
	}else{
		$IPIN	= '';
		$rcpt	= '';
	}

	$sql = 'SELECT	*
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$sr.'\'
			AND		IPIN	= \''.$IPIN.'\'
			AND		rcpt_seq= \''.$rcpt.'\'';

	$row = $conn->get_array($sql);

	if (Is_Array($row)){
		$isNew = false;
	}else{
		$isNew = true;
	}

	$rctSeq		= $row['rcpt_seq'];	//접수순번
	$rctDt		= $row['rcpt_dt'];	//접수일자
	$rctNm		= $row['rcver_nm'];	//접수자명
	$rctJumin	= $ed->en($row['rcver_ssn']);//접수자주민번호
	$rctGbn		= $row['counsel_type'];	//접수방법
	$hceSeq		= $row['hce_seq'];	//차수

	$elderPostno	= $row['postno'];	//우편번호
	$elderAddr		= $row['addr'];//주소
	$elderAddrDtl	= $row['addr_dtl'];//상세주소
	$elderPhone		= $myF->phoneStyle($row['phone']);	//연락처
	$elderMobile	= $myF->phoneStyle($row['mobile']);	//휴대폰

	$reqorNm	= $row['reqor_nm'];		//의뢰인
	$reqorRel	= $row['reqor_rel'];	//관계
	$reqorTel	= $row['reqor_telno'];	//연락처

	$grdNm		= $row['grd_nm'];	//보호자명
	$grdRel		= $row['grd_rel'];	//관계
	$grdAddr	= $row['grd_addr'];	//보호자주소
	$grdTel		= $row['grd_tel'];	//보호자연락처

	$elderEducLvl	= $row['edu_gbn'];//학력
	$elderReligion	= $row['rel_gbn'];//종교
	$elderMerry		= $row['marry_gbn'];//결혼
	$elderCohabit	= $row['cohabit_gbn'];//동거

	$talkText	= StripSlashes($row['counsel_text']);

	if (!$rctSeq)	$rctSeq		= 0;
	if (!$rctDt)	$rctDt		= Date('Y-m-d');	//접수일자
	if (!$rctGbn)	$rctGbn		= '1';				//접수구분
	if (!$grdRel)	$grdRel		= '99';				//보호자와의 관계
	if (!$reqorRel)	$reqorRel	= '99';				//의뢰인과의 관계
	if (!$hceSeq)	$hceSeq		= '1';				//차수


	Unset($row);

	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			,		SUBSTR(m03_yoyangsa5_nm,1,1) AS marry_gbn
			,		SUBSTR(m03_yoyangsa5_nm,2,1) AS cohabit_gbn
			,		SUBSTR(m03_yoyangsa5_nm,3,2) AS edu_gbn
			,		SUBSTR(m03_yoyangsa5_nm,5,1) AS rel_gbn
			,		m03_yboho_name AS grd_nm
			,		m03_yoyangsa4_nm AS grd_addr
			,		m03_yboho_phone AS grd_tel
			,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
			FROM	m03sugupja
			LEFT	JOIN	mst_jumin AS jumin
					ON		jumin.org_no= m03_ccode
					AND		jumin.gbn	= \'1\'
					AND		jumin.code	= m03_jumin
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$IPIN.'\'';

	$row = $conn->get_array($sql);

	$elderNm	= $row['name'];
	$elderJumin	= $row['jumin'];	//대상자 주민번호
	$strJumin	= $row['real_jumin'];

	$strJumin .= '0000000';
	$strJumin = SubStr($strJumin,0,13);

	//if (!$elderMerry)	$elderMerry		= $row['marry_gbn'];//결혼
	//if (!$elderCohabit)	$elderCohabit	= $row['cohabit_gbn'];//동거
	//if (!$elderEducLvl)	$elderEducLvl	= $row['edu_gbn'];//학력
	//if (!$elderReligion)$elderReligion	= $row['rel_gbn'];//종교

	//if (!$grdNm)	$grdNm	= $row['grd_nm'];	//보호자명
	//if (!$grdAddr)	$grdAddr= $row['grd_addr'];	//보호자주소
	//if (!$grdTel)	$grdTel	= $row['grd_tel'];	//보호자연락처

	if (!$elderReligion)$elderReligion	= '9';	//종교

	Unset($row);
?>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		//__init_form(document.f);
	});

	function lfList(){
		document.f.action = '../hce/hce_body.php?sr=<?=$sr;?>&type=1';
		document.f.submit();
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
			,	'svcCd':'<?=$sr;?>'
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
			$('#txtElderNm').val(obj[1]);
			$('#txtElderPostno1').val(obj[10]);
			$('#txtElderPostno2').val(obj[11]);
			$('#txtElderAddr').val(obj[12]);
			$('#txtElderAddrDtl').val(obj[13]);
			$('#txtElderPhone').val(__getPhoneNo(obj[18]));
			$('#txtElderMobile').val(__getPhoneNo(obj[19]));
			$('#txtElderSsn').val(obj[0]);
			$('#lblEldJumin').text(obj[8].substring(0,6)+'-'+obj[8].substring(7,8)+'******');
		
			$('#txtGuardNm').val(obj[15]);
			$('#txtGuardTelNo').val(obj[17]);
			$('#txtGuardAddr').val(obj[24]);
			$('#cboReligion').val(obj[23]);
			$('#cboReligion').val(obj[23]);
			

			$('#cboMarry').val(obj[20]);
			$('#cboCohabit').val(obj[21]);
			$('#cboEducLvl').val(obj[22]);
			$('#cboReligion').val(obj[23]);
			
		}catch(e){
			var obj = __parseStr(obj);

			$('#txtRctNm').val(obj['name']);
			$('#txtRctJumin').val(obj['jumin']);
		}
	}

	function lfChkRcptDt(){
		if ($('#txtNewYn').val() != '1') return false;

		var rst = false;

		$.ajax({
			type :'POST'
		,	async:false
		,	url  :'./hce_find.php'
		,	data :{
				'type':'CHECK_RCPT_DT'
			,	'rcptDt':$('#txtRctDt').val()
			,	'jumin':$('#txtElderSsn').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 'Y'){
					rst = true;
				}

			}
		,	error:function(){
			}
		}).responseXML;

		return rst;
	}

	function lfSave(){
		if ($('#txtNewYn').val() == '1'){
			if (!$('#txtRctDt').val()){
				alert('접수일자를 입력하여 주십시오.');
				$('#txtRctDt').focus();
				return;
			}
		}

		if (!$('#txtRctJumin').val()){
			lfMemFind();
			return;
		}

		if (!$('#txtElderNm').val()){
			alert('대상자명을 입력하여 주십시오.');
			$('#txtElderNm').focus();
			return;
		}

		if ($('#txtNewYn').val() == '1'){
			if (!$('#txtElderSsn').val()){
				alert('대상자를 선택하여 주십시오. 확인하여 주십시오.');
				return;
			}
		}

		if (!$('#txtReqorNm').val()){
			alert('의뢰인명을 입력하여 주십시오.');
			$('#txtRctReqor').focus();
			return;
		}

		if (!$('#txtTalkText').val()){
			alert('상담내용을 입력하여 주십시오.');
			$('#txtRctText').focus();
			return;
		}

		if (lfChkRcptDt()){
			alert('중복되는 접수일자가 있습니다. 확인 후 다시 작성하여 주십시오.');
			return;
		}

		document.f.action = './hce_apply.php';
		document.f.submit();
	}


	//우편번호 검색
	function lfPostCode(postno, addr, addr_dtl){
		var width = 500; //팝업의 너비
		var height = 600; //팝업의 높이

		new daum.Postcode({
			oncomplete: function(data) {
				// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다.
				// 예제를 참고하여 다양한 활용법을 확인해 보세요.
				// 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

				// 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var fullRoadAddr = data.roadAddress;
				var extraRoadAddr = '';
				
				// 법정동명이 있을 경우 추가한다. (법정리는 제외)
				// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
				if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
					extraRoadAddr += data.bname;
				}
				// 건물명이 있고, 공동주택일 경우 추가한다.
				if(data.buildingName !== '' && data.apartment === 'Y'){
				   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
				if(extraRoadAddr !== ''){
					extraRoadAddr = ' (' + extraRoadAddr + ')';
				}
				// 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
				if(fullRoadAddr !== ''){
					fullRoadAddr += extraRoadAddr;
				}

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				if ($('input:text[name="'+postno+'"]').length > 0){
					$('input:text[name="'+postno+'"]').val(data.zonecode);
				}

				if ($('input:text[name="'+addr+'"]').length > 0){
					$('input:text[name="'+addr+'"]').val(fullRoadAddr);
				}

				document.getElementById(addr_dtl).focus();
			}
		}).open({
			left: (window.screen.width / 2) - (width / 2),
			top: (window.screen.height / 2) - (height / 2)
		});
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">신규 사례대상자 등록</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><button type="button" class="bold" onclick="location.href='../hce/hce_body.php?sr=<?=$sr;?>&type=12'">신규</button></span>
		<span class="btn_pack m"><button type="button" class="bold" onclick="lfSave();">저장</button></span>
		<span class="btn_pack m"><button type="button" class="bold" onclick="lfList();">리스트</button></span>
	</div>
</div>
<!-- <div id="divBody" class="my_border_blue" style="height:100px; overflow-x:hidden; overflow-y:auto;"> -->
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="150px">
			<col width="100px">
			<col width="80px">
			<col width="70px">
			<col width="80px">
			<col width="70px">
			<col width="300px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="9">1. 접수정보</th>
			</tr>
			<tr>
				<th class="center">접수일자</th>
				<td class="">
					<input id="txtRctDt" name="txtRctDt" type="text" value="<?=$myF->dateStyle($rctDt);?>" class="date" style="margin-right:0;">
				</td>
				<th class="center">접수자</th>
				<td class="center last">
					<input id="txtRctNm" name="txtRctNm" type="text" value="<?=$rctNm;?>" style="width:100%; border:none;" alt="not" readonly>
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
				<td class="center last"><div id="lblHceSeq" class="left"><?=$hceSeq;?> 차</div></td>
			</tr>
		</tbody>
	</table>

	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="50px">
			<col width="90px">
			<col width="80px">
			<col width="140px">
			<col width="50px">
			<col width="40px">
			<col width="90px">
			<col width="50px">
			<col width="50px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="11">2. 대상자정보(고유번호 : <?=$IPIN;?>)</th>
			</tr>
			<tr>
				<th class="center">성명</th>
				<td class="center">
					<input id="txtElderNm" name="txtElderNm" type="text" value="<?=$elderNm;?>" style="width:100%; border:none;" alt="not" readonly>
				</td>
				<th class="center">주민번호</th>
				<td class="">
					<div id="lblEldJumin" class="left"><?=$myF->issStyle($strJumin);?></div>
					<input id="txtElderSsn" name="txtElderSsn" type="hidden" value="<?=$ed->en($elderJumin);?>">
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
					<input id="txtElderPostno" name="txtElderPostno" type="text" value="<?=$elderPostno1;?>" maxlength="3" class="no_string" style="width:50px;">
					<span class="btn_pack small"><button type="button" onclick="lfPostCode('txtElderPostno', 'txtElderAddr', 'txtElderAddrDtl');">찾기</button></span>
					
				</td>
				<th class="center" rowspan="2">연락처</th>
				<th class="center">유선</th>
				<td class="">
					<input id="txtElderPhone" name="txtElderPhone" type="text" value="<?=$elderPhone;?>" maxlength="12" class="phone">
				</td>
				<th class="center">학력</th>
				<td class="last" colspan="3">
					<select id="cboEducLvl" name="cboEducLvl" style="width:auto;">
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
					<input id="txtElderAddr" name="txtElderAddr" type="text" value="<?=$elderAddr;?>" style="width:100%;">
				</td>
				<th class="center">무선</th>
				<td class="">
					<input id="txtElderMobile" name="txtElderMobile" type="text" value="<?=$elderMobile;?>" maxlength="12" class="phone">
				</td>
				<th class="center">종교</th>
				<td class="last" colspan="3">
					<select id="cboReligion" name="cboReligion" style="width:auto;">
						<option value="">-</option><?
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
					<input id="txtElderAddrDtl" name="txtElderAddrDtl" type="text" value="<?=$elderAddrDtl;?>" style="width:100%;">
				</td>
				<th class="center">결혼정보</th>
				<th class="center">결혼</th>
				<td class="">
					<select id="cboMarry" name="cboMarry" style="width:auto;">
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
					<select id="cboCohabit" name="cboCohabit" style="width:auto;">
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

	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="90px">
			<col width="40px">
			<col width="110px">
			<col width="60px">
			<col width="90px">
			<col width="40px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="8">3. 보호자정보</th>
			</tr>
			<tr>
				<th class="center">성명</th>
				<td class="center"><input id="txtGuardNm" name="txtGuardNm" type="text" value="<?=$grdNm;?>" style="width:100%;"></td>
				<th class="center">관계</th>
				<td class="center">
					<select id="cboGuardRel" name="cboGuardRel" style="width:auto;"><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type = \'HR\'
								AND		use_yn = \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($grdRel == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
				<th class="center">연락처</th>
				<td class="center"><input id="txtGuardTelNo" name="txtGuardTelNo" type="text" value="<?=$myF->phoneStyle($grdTel);?>" maxlength="12" class="phone"></td>
				<th class="center">주소</th>
				<td class="center last"><input id="txtGuardAddr" name="txtGuardAddr" type="text" value="<?=$grdAddr;?>" style="width:100%;"></td>
			</tr>
		</tbody>
	</table>

	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="90px">
			<col width="40px">
			<col width="110px">
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="6">4. 의뢰인정보</th>
			</tr>
			<tr>
				<th class="center">성명</th>
				<td class="center">
					<input id="txtReqorNm" name="txtReqorNm" type="text" value="<?=$reqorNm;?>" style="width:100%;">
				</td>
				<th class="center">관계</th>
				<td class="center">
					<select id="cboReqorRel" name="cboReqorRel" style="width:auto;"><?
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
					<input id="txtReqorTel" name="txtReqorTel" type="text" value="<?=$reqorTel;?>" maxlength="12" class="phone">
				</td>
			</tr>
		</tbody>
	</table>

	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="2">5. 상담내용</th>
			</tr>
			<tr>
				<th class="center bottom">상담내용</th>
				<td class="center bottom last">
					<textarea id="txtTalkText" name="txtTalkText" style="width:100%; height:150px;"><?=$talkText;?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	<input id="txtNewYn" name="txtNewYn" type="hidden" value="<?=$isNew;?>">
	<input id="txtRctJumin" name="txtRctJumin" type="hidden" value="<?=$rctJumin;?>">
	<input id="txtRctSeq" name="txtRctSeq" type="hidden" value="<?=$rctSeq;?>">
<!-- </div> -->
<?
	include_once('../inc/_db_close.php');
?>