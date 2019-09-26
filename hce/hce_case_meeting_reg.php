<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	대상자 사례회의록
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];
	$orgType = '40';

	$meetSeq= $_GET['seq'];
	$rcptSeq = $_GET['r_seq'];
	$copyYn = $_GET['copyYn'];

	$meetDt = Date('Y-m-d');

	//선정기준작성일
	$chicDt	= $infoDt['chic_dt'];

	if (!$copyYn) $copyYn = 'N';


	//판정 - 선정 일자
	$sql = 'SELECT	meet_dt
			FROM	hce_meeting
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		meet_gbn= \'1\'
			ORDER	BY meet_dt DESC
			LIMIT	1';
	$meetDt1 = $conn->get_data($sql);

	//판정 - 제공 일자
	$sql = 'SELECT	meet_dt
			FROM	hce_meeting
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		meet_gbn= \'2\'
			ORDER	BY meet_dt DESC
			LIMIT	1';
	$meetDt2 = $conn->get_data($sql);

	//판정 - 재사정 일자
	$sql = 'SELECT	meet_dt
			FROM	hce_meeting
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		meet_gbn= \'3\'
			ORDER	BY meet_dt DESC
			LIMIT	1';
	$meetDt3 = $conn->get_data($sql);

	//판정 - 종결 일자
	$sql = 'SELECT	meet_dt
			FROM	hce_meeting
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		meet_gbn= \'4\'
			ORDER	BY meet_dt DESC
			LIMIT	1';
	$meetDt4 = $conn->get_data($sql);
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfResize();

		$('input:radio[name="optMeetGbn"]').unbind('click').bind('click',function(){
			var meetDt = $('#txtMeetDt').val().split('-').join('');
			var isYn = 'Y';

			if ($(this).val() == '9'){
				$('input:radio[name="optDecision"]').attr('disabled',false);
			}else{
				if (!meetDt){
					alert('회의일자를 입력하여 주십시오.');
					$('#txtMeetDt').focus();
					return false;
				}

				if (isYn == 'N'){
					$(this).attr('checked',false);
					//$('input:radio[name="optDecision"]').attr('checked',false);
				}

				if ($(this).val() == '1' || $(this).val() == '2' || $(this).val() == '3'){
					$('#optDecision1').attr('disabled',false).attr('checked',true);
					$('#optDecision2').attr('disabled',true);
				}else{
					$('#optDecision1').attr('disabled',true);
					$('#optDecision2').attr('disabled',false).attr('checked',true);
				}
			}
		});

		setTimeout('lfSearch()',100);
	});

	function lfLoadMem(date){
		
		if ('<?=$copyYn;?>' == 'Y') return;

		if (!date) date = $('#txtMeetDt').val();

		var strAttendee = '';

		$.ajax({
			type:'POST'
		,	async:false
		,	url:'../find/_find_mem.php'
		,	data:{
				'date':date
			,	'SR':'<?=$sr;?>'
			}
		,	beforeSend:function(){
				$('input:checkbox[id^="chkAttendee_"]:checked').each(function(){
					strAttendee += '/'+$(this).val();
				});
			}
		,	success:function(data){
				if (!data) return;

				var html = '';
				var row = data.split('?');

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var chk = '';

						if (strAttendee.indexOf('/'+col['key']) >= 0){
							chk = 'checked';
						}

						html += '<div style="float:left; width:15%;"><label><input id="chkAttendee_'+i+'" name="chkAttendee" type="checkbox" class="checkbox" value="'+col['key']+'" '+chk+'>'+col['name']+'</label></div>';
					}
				}

				$('#ID_ATTENDEE').html(html);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfChkDt(gbn,dt1,dt2){
		var str1, str2;

		if (gbn == '1'){
			str1 = '선정지준표 작성일';
			str2 = '사례회의록(선정) 작성일';
		}else if (gbn == '2'){
			str1 = '이용 안내 및 동의서 작성일';
			str2 = '사례회의록(제공) 작성일';
		}else if (gbn == '4'){
			str1 = '이용 안내 및 동의서 작성일';
			str2 = '사례회의록(종결) 작성일';
		}else if (gbn == '91'){
			str1 = '판정일';
			str2 = '회의일';
		}

		if (dt1 > dt2){
			alert('"'+str2+'('+__getDate(dt2,'.')+')"이 "'+str1+'('+__getDate(dt1,'.')+')"보다 과거일자로 등록될 수 없숩니다.\n확인 후 다시 입력하여 주십시오.');
			return false;
		}

		return true;
	}

	function lfResize(){
		var top = $('#divBody').offset().top;
		var height = $(document).height();

		var h = height - top - 10;

		$('#divBody').height(h);
	}

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtExaminer').attr('jumin',obj['jumin']).val(obj['name']);
	}
	
	function lfPicUpload(){
		
		var h = 400;
		var w = 750;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = 'hce_meeting_pic_pop.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('', 'PIC_UPLOAD', option);
			win.opener = self;
			win.focus();
		
		var parm = new Array();
			parm = {
				'type':'MEETING'
			,	'sr':'<?=$sr;?>'
			,	'meetSeq':'<?=$meetSeq;?>'
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

		form.setAttribute('target','PIC_UPLOAD');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
		
	}




	function lfSearch(){

		$.ajax({
			type:'POST'
		,	url:'./hce_find.php'
		,	data:{
				'type':'<?=$type;?>'
			,	'seq':$('#meetSeq').val()
			,	'r_seq':'<?=$rcptSeq;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				lfLoadMem(__getDate(col['meetDt']));

				$('#meetGbn').val(col['meetGbn']);


				if ('<?=$copyYn;?>' == 'Y'){
					$('#ID_MSG').text(__getDate(col['meetDt'], '.')+'에 작성된 사례회의록을 복사하였습니다.');
				}else{
					$('#txtMeetDt').val(__getDate(col['meetDt']));
				}

				$('#optMeetGbn_'+col['meetGbn']).attr('checked',true).click();
				$('#txtExaminer').attr('jumin',col['examinerJumin']).val(col['examiner']);

				var attendee = col['attendee'].split(String.fromCharCode(1));

				for(var i=0; i<attendee.length; i++){
					$('input:checkbox[name="chkAttendee"][value="'+attendee[i]+'"]').attr('checked',true);
				}

				$('#txtAttOther').val(col['attendeeOther']);
				$('#txtLifeLvl').val(col['lifeLvl']);
				$('#txtReqRsn').val(col['reqRsn']);
				$('#optDecision'+col['decisionGbn']).attr('checked',true);
				$('#txtDecisionDt').val(__getDate(col['decisionDt']));
				$('#txtDecisionRsn').val(col['decisionRsn']);

				var svcReq = __parseVal(col['decisionSvc'].split('/').join('&').split(':').join('='));

				for(var i in svcReq){
					$('#chkSvcReq_'+i).attr('checked',(svcReq[i] == 'Y' ? true : false));
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//저장
	function lfSave(){
		if (!$('#txtMeetDt').val()){
			alert('회의일자를 입력하여 주십시오.');
			$('#txtMeetDt').focus();
			return;
		}

		if (!$('#txtExaminer').val()){
			alert('조사자를 입력하여 주십시오.');
			$('#txtExaminer').focus();
			return;
		}

		if (!$('#txtDecisionDt').val()){
			alert('판정일자를 입력하여 주십시오.');
			$('#txtDecisionDt').focus();
			return;
		}

		if (!$('input:radio[name="optMeetGbn"]:checked').val()){
			alert('판정구분을 선택하여 주십시오.');
			return;
		}

		var data = {};

		data['examinerJumin']= $('#txtExaminer').attr('jumin');

		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('input:hidden').each(function(){
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

			if ($(this).attr('name') == 'chkAttendee'){
				var val	= $(this).attr('checked') ? $(this).val() : '';

				data['attendee'] = (data['attendee'] ? data['attendee'] : '')+(data['attendee'] ? '&' : '')+val;
			}else{
				var val	= $(this).attr('checked') ? 'Y' : 'N';

				data[id] = val;
			}
		});

		$('select').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (id.substring(0,12) == 'cboFamilyGbn' ||
				id.substring(0,15) == 'cboFamilyCohabit'){
			}else{
				data[id] = val;
			}
		});

		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});


		if ('<?=$copyYn;?>' == 'Y') data['meetSeq'] = '';

		$.ajax({
			type:'POST'
		,	url:'./hce_apply.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 'ERROR'){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (result == 'NOT'){
					alert('데이타 처리중 오류가 발생하였습니다. 관리자에게 연락하여 주십시오.');
				}else if (result > 0){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();
					$('#meetSeq').val(result);
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//삭제
	function lfDelete(){
		var data = {};

		data['jumin']= $('#txtExaminer').attr('jumin');

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./hce_case_meeting_delete.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();

					var form = document.createElement('form');
						form.setAttribute('target', 'frmBody');
						form.setAttribute('method', 'post');
						form.setAttribute('action', '../hce/hce_body.php?sr=<?=$sr;?>&type=51');

					document.body.appendChild(form);

					form.submit();
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfChkSet(cd){
		var obj = $('#chk_'+cd);

		if ($(obj).attr('checked')){
			$('input:checkbox[id^="chkSvcReq_'+cd+'"]').attr('checked',true);
		}else{
			$('input:checkbox[id^="chkSvcReq_'+cd+'"]').attr('checked',false);
		}
	}

	function lfCopy(){
		var objModal = new Object();
		var url      = './hce_copy.php?type=52';
		var style    = 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		//objModal.code  = $('#code').val();
		objModal.type  = '52';


		window.showModalDialog(url, objModal, style);

		var result = objModal.para;

		if (!result) return;

		var arr = result.split('&');
		var val = new Array();

		for(var i=0; i<arr.length; i++){
			var tmp = arr[i].split('=');

			val[tmp[0]] = tmp[1];
		}


		location.href = '../hce/hce_body.php?sr=S&type=52&seq='+val['seq']+'&r_seq='+val['r_seq']+'&copyYn=Y';
		
		//$('#strCname').text(val['name']);
		//$('#param').attr('value', 'jumin='+val['jumin']);
	}

</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="left bottom last" id="ID_MSG"></td>
			<td class="right bottom last">
				<span class="btn_pack m"><a href="#" onclick="lfPicUpload(); return false;">사진첨부</a></span>
				<? if ($copyYn != 'Y'){ ?>
						<span class="btn_pack m"><button type="button" onclick="lfCopy();">복사</button></span><?
					}
				?>
				<span class="btn_pack m"><span class="save"></span><a href="#" onclick="lfSave(); return false;">저장</a></span>
				<span class="btn_pack m"><span class="delete"></span><a href="#" onclick="lfDelete(); return false;">삭제</a></span>
				<span class="btn_pack m"><span class="list"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=51" target="frmBody">리스트</a></span>
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','',$('#meetSeq').val());">출력</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div class="my_border_blue" style="border-bottom:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col width="50px">
			<col width="50px">
			<col width="30px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">회의일자</th>
				<td class=""><input id="txtMeetDt" name="txt" type="text" value="<?=$meetDt;?>" class="date" onchange="lfLoadMem();"></td>
				<th class="head">조사자</th>
				<td class="center"><span class="btn_pack find" onclick="lfMemFind();"></span></td>
				<td class="last"><input id="txtExaminer" name="txt" type="text" value="" jumin="" style="width:70px;" alt="not" readonly></td>
			</tr>
			<tr>
				<th class="head">판정구분</th>
				<td class="last" colspan="4"><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type	= \'CMT\'
							AND		use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<label><input id="optMeetGbn_<?=$row['code'];?>" name="optMeetGbn" type="radio" class="radio" value="<?=$row['code'];?>"><?=$row['name'];?></label><?
					}

					$conn->row_free();?>
				</td>
			</tr>
			<tr>
				<th class="head">참석자</th>
				<td class="left last" colspan="4">
					<div id="ID_ATTENDEE" style="width:100%; height:70px; overflow-x:hidden; overflow-y:scroll;"><?
						$sql = 'SELECT	DISTINCT
										m02_key AS jumin
								,		m02_yname AS name
								FROM	m02yoyangsa
								INNER	JOIN	mem_option
										ON		mem_option.org_no	= m02_ccode
										AND		mem_option.mo_jumin = m02_yjumin';

						if ($sr == 'S'){
							$sql .= '	AND		mem_option.support_yn = \'Y\'';
						}else{
							$sql .= '	AND		mem_option.response_yn = \'Y\'';
						}

						$sql .= '
								WHERE	m02_ccode = \''.$orgNo.'\'
								AND		m02_ygoyong_stat = \'1\'
								ORDER	BY name';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<div style="float:left; width:15%;"><label><input id="chkAttendee_<?=$i;?>" name="chkAttendee" type="checkbox" class="checkbox" value="<?=$ed->en($row['jumin']);?>"><?=$row['name'];?></label></div><?
						}

						$conn->row_free();?>
					</div>
				</td>
			</tr>
			<tr>
				<th class="head">기관외<br>참석자</th>
				<td class="last" colspan="4">
					<textarea id="txtAttOther" name="multi" style="width:100%; height:35px;"></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="divBody" class="my_border_blue" style="height:200px; overflow-x:hidden; overflow-y:auto; border-top:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col width="120px">
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="10">- 회의내용</th>
			</tr>
			<tr>
				<th class="head">생활수준<br>및<br>건강상태</th>
				<td class="last" colspan="3"><textarea id="txtLifeLvl" name="multi" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="head">신청사유</th>
				<td class="last" colspan="3"><textarea id="txtReqRsn" name="multi" style="width:100%; height:100px;"></textarea></td>
			</tr>
			<tr>
				<th class="bold last" colspan="10">- 판정결과</th>
			</tr>
			<tr>
				<th class="head">제공여부</th>
				<td class="">
					<label><input id="optDecision1" name="optDecision" type="radio" class="radio" value="1">제공</label>
					<label><input id="optDecision2" name="optDecision" type="radio" class="radio" value="2">종결</label>
			</td>
				<th class="head">일자</th>
				<td class="last"><input id="txtDecisionDt" name="txt" type="text" value="" class="date"></td>
			</tr>
			<tr>
				<th class="head">판정사유</th>
				<td class="last" colspan="3"><textarea id="txtDecisionRsn" name="multi" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="head">제공할<br>서비스<br>내역</th>
				<td class="last" colspan="3">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col>
						</colgroup>
						<tbody><?
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

							if ($rowCnt > 0){?>
								<tr>
									<th class="last" style="padding-left:13px;">
										<label><input id="chk_" type="checkbox" class="checkbox" onclick="lfChkSet('');">전체</label>
									</th>
								</tr><?
							}

							for($i=0; $i<$rowCnt; $i++){
								$row = $conn->select_row($i);

								if ($tmpStr1 != SubStr($row['cd'],0,1)){
									$tmpStr1  = SubStr($row['cd'],0,1);
									$idx = 1;?>
									<tr>
										<th class="last" style="padding-left:13px;">
											<label><input id="chk_<?=SubStr($row['cd'],0,1);?>" type="checkbox" class="checkbox" onclick="lfChkSet('<?=SubStr($row['cd'],0,1);?>');"><?=SubStr($row['cd'],0,1).'. '.Str_Replace('<br>','',$row['mst_nm']);?></label>
										</th>
									</tr><?
								}

								if ($tmpStr2 != SubStr($row['cd'],0,3)){
									$tmpStr2  = SubStr($row['cd'],0,3);
									$idx = 1;?>
									<tr>
										<th class="last" style="padding-left:30px;">
											<label><input id="chk_<?=SubStr($row['cd'],0,3);?>" type="checkbox" class="checkbox" onclick="lfChkSet('<?=SubStr($row['cd'],0,3);?>');"><?=SubStr($row['cd'],1,2).'. '.Str_Replace('<br>','',$row['pro_nm']);?></label>
										</th>
									</tr><?
								}

								if ($idx % 3 == 1){?>
									<tr><td class="last" style="padding-left:50px;"><?
								}?>
								<div style="float:left; width:30%;"><label><input id="chkSvcReq_<?=$row['cd'];?>" name="chkSvcReq" type="checkbox" class="checkbox" value="<?=$row['cd'];?>"><?=$row['svc_nm'];?></label></div><?

								if ($idx == 3){
									$idx = 1;?>
									</td></tr><?
								}else{
									$idx ++;
								}
							}

							$conn->row_free();?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<input id="meetSeq" type="hidden" value="<?=$meetSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>