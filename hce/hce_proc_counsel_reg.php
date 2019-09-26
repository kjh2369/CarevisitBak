<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$counselDt	= Date('Y-m-d');
	$counselGbn = '1';
	$procSeq	= $_GET['procSeq'];

	if (Empty($procSeq)){
		$sql = 'SELECT	IFNULL(MAX(proc_seq),0)+1
				FROM	hce_proc_counsel
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$procSeq = $conn->get_data($sql);
	}

	if (Empty($procSeq)) $procSeq = '1';

	
	$sql = 'SELECT	counsel_dt
			,		counsel_nm
			,		counsel_jumin
			,		counsel_gbn
			,		counsel_text
			,		counsel_remark
			FROM	hce_proc_counsel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		proc_seq= \''.$procSeq.'\'
			AND		del_flag= \'N\'';
	
	$row = $conn->get_array($sql);
	
	if ($row){
		$counselDt = $myF->dateStyle($row['counsel_dt']);
		$counselNm = $row['counsel_nm'];
		$counsel_jumin = $ed->en($row['counsel_jumin']);
		$counselGbn = $row['counsel_gbn'];
		$counselText = StripSlashes($row['counsel_text']);
		$counselRemark = StripSlashes($row['counsel_remark']);
	}

?>
<script type="text/javascript">
	$(document).ready(function(){
		//setTimeout('lfSearch()',200);
	});

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtCounsel').attr('jumin',obj['jumin']).val(obj['name']);
	}

	//조회
	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./hce_proc_counsel_reg_search.php'
		,	data:{
				'procSeq':$('#procSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				$('#txtProcDt').val(col['date']);
				$('#txtCounsel').attr('jumin',col['jumin']).val(col['name']);
				$('#optCounselGbn'+col['gbn']).attr('checked',true);
				$('#txtCounselText').val(col['text']);
				$('#txtRemark').val(col['remark']);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//저장
	function lfSave(){
		if (!$('#txtProcDt').val()){
			alert('작성일자를 입력하여 주십시오.');
			$('#txtProcDt').focus();
			return;
		}

		if (!$('#txtCounsel').val()){
			alert('상담자를 입력하여 주십시오.');
			return;
		}

		var data = {};

		data['counselJumin']= $('#txtCounsel').attr('jumin');

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
		,	url:'./hce_proc_counsel_reg_save.php'
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

	function lfDel(){
		
		var data = {};

		data['counselJumin']= $('#txtCounsel').attr('jumin');

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
		,	url:'./hce_proc_counsel_del.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					location.href="../hce/hce_body.php?sr=<?=$sr;?>&type=81";
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

</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="right bottom last">
				<span class="btn_pack m"><span class="add"></span><a href="#" onclick="lfSave(); return false;">저장</a></span>
				<span class="btn_pack m"><span class="delete"></span><a href="#" onclick="lfDel(); return false;">삭제</a></span>
				<span class="btn_pack m"><span class="list"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=81" target="frmBody">리스트</a></span>
			</td>
		</tr>
	</tbody>
</table>
<div class="my_border_blue" style="overflow-x:hidden; overflow-y:auto;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">일자</th>
				<td class="last"><input id="txtProcDt" type="text" value="<?=$counselDt;?>" class="date"></td>
			</tr>
			<tr>
				<th class="head">상담자</th>
				<td class="last">
					<div style="float:left; width:auto; height:25px; padding:1px 0 0 5px;"><span class="btn_pack find" onclick="lfMemFind();"></span></div>
					<div style="float:left; width:auto; padding-top:2px;"><input id="txtCounsel" type="text" value="<?=$counselNm;?>" jumin="<?=$counselJumin;?>" style="margin-left:0;" alt="not" readonly></div>
				</td>
			</tr>
			<tr>
				<th class="head">상담방법</th>
				<td class="last"><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type	= \'CT\'
							AND		use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<label><input id="optCounselGbn<?=$row['code'];?>" name="optCounselGbn" type="radio" value="<?=$row['code'];?>" class="radio" <?=($counselGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?
					}

					$conn->row_free();?>
				</td>
			</tr>
			<tr>
				<th class="head">내용</th>
				<td class="last"><textarea id="txtCounselText" style="width:100%; height:150px;"><?=$counselText;?></textarea></td>
			</tr>
			<tr>
				<th class="head bottom">비고</th>
				<td class="bottom last"><textarea id="txtRemark" style="width:100%; height:35px;"><?=$counselRemark;?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>
<input id="procSeq" type="hidden" value="<?=$procSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>