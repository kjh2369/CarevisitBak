<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	//관계
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'HR\'
			AND		use_yn	= \'Y\'';

	$arrRel = $conn->_fetch_array($sql,'code');

	$isptDt	= Date('Y-m-d');
	$isptSeq = $_GET['seq'];

	if (Empty($isptSeq)){
		$sql = 'SELECT	IFNULL(MAX(ispt_seq),0)+1
				FROM	hce_re_ispt
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$isptSeq = $conn->get_data($sql);
	}

	if (Empty($isptSeq)) $isptSeq = '1';

	//담당자
	$arr = $hce->getPersonIn($orgNo);

	$perNm		= $arr['name'];
	$perJumin	= $ed->en($arr['jumin']);

	Unset($arr);

	//사례접수 및 초기면접 내용
	$sql = 'SELECT	mst.m03_name AS name
			,		mst.m03_jumin AS jumin
			,		RG.name AS rel_gbn
			,		iv.income_gbn
			,		iv.income_other
			,		iv.handicap_gbn
			,		iv.handicap_other
			,		iv.generation_gbn
			,		iv.generation_other
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		rcpt.phone
			,		rcpt.mobile
			,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode	= rcpt.org_no
					AND		mst.m03_mkind	= \'6\'
					AND		mst.m03_key		= rcpt.IPIN
			INNER	JOIN	hce_interview AS iv
					ON		iv.org_no	= rcpt.org_no
					AND		iv.org_type = rcpt.org_type
					AND		iv.IPIN		= rcpt.IPIN
					AND		iv.rcpt_seq = rcpt.rcpt_seq
			LEFT	JOIN	hce_gbn AS RG
					ON		RG.type	= \'RG\'
					AND		RG.code	= rcpt.rel_gbn
			LEFT	JOIN	mst_jumin AS jumin
					ON		jumin.org_no= m03_ccode
					AND		jumin.gbn	= \'1\'
					AND		jumin.code	= m03_jumin
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$name			= $row['name'];	//대상자명
	$jumin			= $myF->issStyle(SubStr($row['real_jumin'].'0000000',0,13));	//대상자 주민번호
	$addr			= $row['addr'].' '.$row['addr_dtl'];	//주소
	$telno			= $myF->phoneStyle(($row['phone'] ? $row['phone'] : $row['mobile']),'.');
	$relGbn			= $row['rel_gbn'];	//종교
	$incomeGbn		= $row['income_gbn'];	//경제사항
	$incomeOther	= $row['income_other'];
	$handicapGbn	= $row['handicap_gbn'];	//장애여부
	$handicapOther	= $row['handicap_other'];
	$generationGbn	= $row['generation_gbn'];	//세대유형
	$generationOther= $row['generation_other'];

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',200);
	});

	//조회
	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./hce_re_ispt_reg_search.php'
		,	data:{
				'seq':$('#isptSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data){
					$('#optIsptGbn1').attr('checked',true);
					$('#optIsptRsn1').attr('checked',true);
					$('#optIsptRstGbn1').attr('checked',true);
					return;
				}

				var col = __parseVal(data);

				$('#txtIsptDt').val(__getDate(col['date']));
				$('#txtPer').attr('jumin',col['jumin']).val(col['name']);

				$('#optIsptGbn'+col['gbn']).attr('checked',true);
				$('#optIsptRsn'+col['rsn']).attr('checked',true);
				$('#txtNeedChange').val(col['need']);
				$('#txtSvcOfferProblem').val(col['offer']);
				$('#txtWerOpion').val(col['wer']);
				$('#optIsptRstGbn'+col['rst']).attr('checked',true);
				$('#txtAfterPlan').val(col['plan']);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//저장
	function lfSave(){
		
		if($('#txtIsptDt').val() == ''){
			alert('재사정일자를 입력하여 주십시오.');
			$('#txtIsptDt').focus();
			return;
		}
		
		var data = {};

		
		data['perJumin']= $('#txtPer').attr('jumin');
		
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

		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./hce_re_ispt_reg_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();
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

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtPer').attr('jumin',obj['jumin']).val(obj['name']);
	}

	function lfRemove(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./hce_re_ispt_reg_remove.php'
		,	data:{
				'seq':$('#isptSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();

					var f = document.f;

					f.action = '../hce/hce_body.php?sr=<?=$sr;?>&type=111';
					f.target = 'frmBody';
					f.submit();
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
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="right bottom last">
				<span class="btn_pack m"><span class="add"></span><a href="#" onclick="lfSave(); return false;">저장</a></span>
				<span class="btn_pack m"><span class="delete"></span><a href="#" onclick="lfRemove(); return false;">삭제</a></span>
				<span class="btn_pack m"><span class="list"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=111" target="frmBody">리스트</a></span>
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','','<?=$isptSeq;?>');">출력</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div class="my_border_blue" style="border-bottom:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="50px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">재사정일자</th>
				<td><input id="txtIsptDt" type="text" value="<?=$isptDt;?>" class="date"></td>
				<th class="head">담당자</th>
				<td class="last">
					<div style="float:left; width:auto; height:25px; padding:1px 0 0 5px;"><span class="btn_pack find" onclick="lfMemFind();"></span></div>
					<div style="float:left; width:auto; padding-top:2px;"><input id="txtPer" type="text" value="<?=$perNm;?>" jumin="<?=$perJumin;?>" style="margin-left:0;" alt="not" readonly></div>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="90px">
			<col width="70px">
			<col width="150px">
			<col width="40px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">성명</th>
				<td class="left"><?=$name;?></td>
				<th class="head">주민번호</th>
				<td class="left"><?=$jumin;?></td>
				<th class="head">종교</th>
				<td class="left last"><?=$relGbn;?></td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head" rowspan="3">대상자<br>분류</th>
				<td class="last"><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type	= \'IG\'
							AND		use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<span style="margin-left:5px;"><?=($incomeGbn == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?

						if ($incomeGbn == '9' && $incomeGbn == $row['code'] && $incomeOther){?>
							<span style="margin-left:1px;">(<?=$incomeOther;?>)</span><?
						}
					}

					$conn->row_free();?>
				</td>
			</tr>
			<tr>
				<td class="last">
					<span style="margin-left:5px;"><?=($handicapGbn == 'Y' ? '■' : '□');?></span><span style="margin-left:2px;">유</span>(장애유형:<?=($handicapGbn == 'Y' ? $handicapOther : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')?>)
					<span style="margin-left:5px;"><?=($handicapGbn != 'Y' ? '■' : '□');?></span><span style="margin-left:2px;">무</span>
				</td>
			</tr>
			<tr>
				<td class="last"><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type	= \'GR\'
							AND		use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<span style="margin-left:5px;"><?=($generationGbn == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?

						if ($generationGbn == '9' && $generationGbn == $row['code'] && $generationOther){?>
							<span style="margin-left:1px;">(<?=$generationOther;?>)</span><?
						}
					}

					$conn->row_free();?>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="500px">
			<col width="70px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">주소</th>
				<td class="left"><?=$addr;?></td>
				<th class="head">연락처</th>
				<td class="left last"><?=$telno;?></td>
			</tr>
		</tbody>
	</table>
</div>
<div id="divBody" class="my_border_blue" style="height:200px; overflow-x:hidden; overflow-y:auto; border-top:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="71px">
			<col width="80px">
			<col width="150px">
			<col width="50px">
			<col width="100px">
			<col width="72px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="20">- 가족사항</th>
			</tr>
			<tr>
				<th class="head">관계</th>
				<th class="head">성명</th>
				<th class="head">주소</th>
				<th class="head">연령</th>
				<th class="head">직업</th>
				<th class="head">동거여부</th>
				<th class="head">월소득액</th>
				<th class="head last">비고</th>
			</tr>
		</tbody>
		<tbody><?
			$sql = 'SELECT	family_rel AS rel
					,		family_nm AS name
					,		family_addr AS addr
					,		family_age AS age
					,		family_job AS job
					,		family_cohabit AS cohabit
					,		family_monthly AS monthly
					,		family_remark AS remark
					FROM	hce_family
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);?>
				<tr>
					<td class="center"><?=$arrRel[$row['rel']]['name'];?></td>
					<td class="center"><?=$row['name'];?></td>
					<td class="center"><?=StripSlashes($row['addr']);?></td>
					<td class="center"><?=$row['age'];?></td>
					<td class="center"><?=$row['job'];?></td>
					<td class="center"><?=$row['cohabit'];?></td>
					<td class="center"><?=StripSlashes($row['monthly']);?></td>
					<td class="center last"><?=StripSlashes($row['remark']);?></td>
				</tr><?
			}

			$conn->row_free();?>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last">- 재사정 유형</th>
			</tr>
			<tr>
				<td class="last">
					<label><input id="optIsptGbn1" name="optIsptGbn" type="radio" class="radio" value="1">새로운 욕구가 발생</label>
					<label><input id="optIsptGbn2" name="optIsptGbn" type="radio" class="radio" value="2">긴급한 상황이 발생</label>
					<label><input id="optIsptGbn9" name="optIsptGbn" type="radio" class="radio" value="9">기타</label>
				</td>
			</tr>
			<tr>
				<th class="bold last">- 재사정 요인</th>
			</tr>
			<tr>
				<td class="last">
					<label><input id="optIsptRsn1" name="optIsptRsn" type="radio" class="radio" value="1">재가노인에 의한 요인</label>
					<label><input id="optIsptRsn2" name="optIsptRsn" type="radio" class="radio" value="2">기관과 사회복지사에 의한 요인</label>
					<label><input id="optIsptRsn3" name="optIsptRsn" type="radio" class="radio" value="3">자원과 환경에 의한 요인</label>
				</td>
			</tr>
			<tr>
				<th class="bold last">- Client 변화욕구</th>
			</tr>
			<tr>
				<td class="last"><textarea id="txtNeedChange" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="bold last">- 서비스 제공 및 문제</th>
			</tr>
			<tr>
				<td class="last"><textarea id="txtSvcOfferProblem" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="bold last">- wer'의 의견</th>
			</tr>
			<tr>
				<td class="last"><textarea id="txtWerOpion" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="bold last">- 재사정 결과</th>
			</tr>
			<tr>
				<td class="last">
					<label><input id="optIsptRstGbn1" name="optIsptRstGbn" type="radio" class="radio" value="1">종결</label>
					<label><input id="optIsptRstGbn2" name="optIsptRstGbn" type="radio" class="radio" value="2">서비스 재계획</label>
					<label><input id="optIsptRstGbn3" name="optIsptRstGbn" type="radio" class="radio" value="3">의뢰</label>
					<label><input id="optIsptRstGbn4" name="optIsptRstGbn" type="radio" class="radio" value="4">현상태유지</label>
				</td>
			</tr>
			<tr>
				<th class="bold last">- 향후계획</th>
			</tr>
			<tr>
				<td class="last"><textarea id="txtAfterPlan" style="width:100%; height:50px;"></textarea></td>
			</tr>
		</tbody>
	</table>
</div>
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	Unset($arrRel);
	include_once('../inc/_db_close.php');
?>