<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	서비스 연계 및 의뢰서
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];

	//대상자 정보
	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			,		rcpt.phone
			,		rcpt.mobile
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		m03_ccode	= rcpt.org_no
					AND		m03_mkind	= \'6\'
					AND		m03_key		= rcpt.IPIN
			LEFT	JOIN	mst_jumin AS jumin
					ON		jumin.org_no= m03_ccode
					AND		jumin.gbn	= \'1\'
					AND		jumin.code	= m03_jumin
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];
	$jumin	= SubStr($row['real_jumin'].'0000000',0,13);
	$gender	= $myF->issToGender($jumin);
	$jumin	= $myF->issStyle($jumin);
	$telno	= $myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');
	$addr	= $row['addr'].' '.$row['addr_dtl'];

	Unset($row);

	//의뢰일자
	$reqDt	= Date('Y-m-d');

	//담당자
	$arr = $hce->getPersonIn($orgNo);

	$perNm		= $arr['name'];
	$perJumin	= $ed->en($arr['jumin']);

	Unset($arr);

	//순번
	$connSeq = $_GET['connSeq'];

	//다음 순번
	if (Empty($connSeq)){
		$sql = 'SELECT	IFNULL(MAX(conn_seq),0)+1
				FROM	hce_svc_connect
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$connSeq = $conn->get_data($sql);
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',200);
		lfResize();
	});

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtPer').attr('jumin',obj['jumin']).val(obj['name']);
	}

	//조회
	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./hce_svc_connection_reg_search.php'
		,	data:{
				'connSeq':$('#connSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				$('#txtConnNo').val(col['orgNo']);
				$('#txtConnNm').val(col['orgNm']);
				$('#txtPer').attr('jumin',col['perJumin']).val(col['perNm']);
				$('#txtReqDt').val(__getDate(col['date']));
				$('#txtReqor').val(col['name']);
				$('#cboReqRel').val(col['rel']);
				$('#txtReqRsn').val(col['rsn']);
				$('#txtReqText').val(col['text']);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		/*
		if (!$('#txtConnNo').val()){
			alert('수신 기관기호를 입력하여 주십시오.');
			$('#txtConnNo').focus();
			return;
		}

		if (!$('#txtConnNm').val()){
			alert('수신 기관명을 입력하여 주십시오.');
			$('#txtConnNm').focus();
			return;
		}
		*/

		if (!$('#txtReqDt').val()){
			alert('의뢰일자를 입력하여 주십시오.');
			$('#txtReqDt').focus();
			return;
		}

		if (!$('#txtPer').val()){
			alert('담당자를 입력하여 주십시오.');
			$('#txtPer').focus();
			return;
		}

		if (!$('#txtReqor').val()){
			alert('의뢰인명을 입력하여 주십시오.');
			$('#txtReqor').focus();
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

		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});

		$('select').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./hce_svc_connection_reg_save.php'
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

	function lfDelete(){
		$.ajax({
			type:'POST'
		,	url:'./hce_svc_connection_reg_delete.php'
		,	data:{
				'connSeq':$('#connSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if(!result){
					alert('정상적으로 처리되었습니다.');
					location.href="../hce/hce_body.php?sr=<?=$sr;?>&type=91";
					top.frames['frmTop'].lfTarget();
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
				<span class="btn_pack m"><span class="save"></span><a href="#" onclick="lfSave(); return false;">저장</a></span>
				<span class="btn_pack m"><span class="delete"></span><a href="#" onclick="lfDelete(); return false;">삭제</a></span>
				<span class="btn_pack m"><span class="list"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=91" target="frmBody">리스트</a></span>
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','','<?=$connSeq;?>');">출력</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div class="my_border_blue" style="border-bottom:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="100px">
			<col width="40px">
			<col width="30px">
			<col width="60px">
			<col width="130px">
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="20">- 대상자</th>
			</tr>
			<tr>
				<th class="head">성명</th>
				<td class="left"><?=$name;?></td>
				<th class="head">성별</th>
				<td class="left"><?=$gender;?></td>
				<th class="head">주민번호</th>
				<td class="left"><?=$jumin;?></td>
				<th class="head">연락처</th>
				<td class="left last"><?=$telno;?></td>
			</tr>
			<tr>
				<th class="head">주소</th>
				<td class="left last" colspan="7"><?=$addr;?></td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="150px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="20">- 수신기관</th>
			</tr>
			<tr>
				<th class="head">기관기호</th>
				<td class=""><input id="txtConnNo" type="text" value="" maxlength="15" style="width:100%;"></td>
				<th class="head">기관명</th>
				<td class="last"><input id="txtConnNm" type="text" value="" style="width:250px;"></td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="50px">
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="20">- 작성자</th>
			</tr>
			<tr>
				<th class="head">의뢰일자</th>
				<td><input id="txtReqDt" type="text" value="<?=$reqDt;?>" class="date"></td>
				<th class="head">담당자</th>
				<td class="last">
					<div style="float:left; width:auto; height:25px; padding:1px 0 0 5px;"><span class="btn_pack find" onclick="lfMemFind();"></span></div>
					<div style="float:left; width:auto; padding-top:2px;"><input id="txtPer" type="text" value="<?=$perNm;?>" jumin="<?=$perJumin;?>" style="margin-left:0;" alt="not" readonly></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="divBody" class="my_border_blue" style="height:200px; overflow-x:hidden; overflow-y:auto; border-top:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="100px">
			<col width="110px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="20">- 의뢰</th>
			</tr>
			<tr>
				<th class="head">의뢰인</th>
				<td><input id="txtReqor" type="text" value="" style="width:100%;"></td>
				<th class="head">대상자와의 관계</th>
				<td class="last">
					<select id="cboReqRel" name="cboReqRel" style="width:auto;"><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'HR\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($reqRel == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
			</tr>
			<tr>
				<th class="head">의뢰사유</th>
				<td class="last" colspan="3"><textarea id="txtReqRsn" style="width:100%; height:100px;"></textarea></td>
			</tr>
			<tr>
				<th class="head">의뢰자의<br>요청사항</th>
				<td class="last" colspan="3"><textarea id="txtReqText" style="width:100%; height:150px;"></textarea></td>
			</tr>
		</tbody>
	</table>
</div>
<input id="connSeq" type="hidden" value="<?=$connSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>