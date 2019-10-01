<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	서비스 종결 안내서
	 *********************************************************/
	$sql = 'SELECT	m03_name AS name
			,		m03_juso1 AS addr
			,		m03_juso2 AS addr_dtl
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$row = $conn->get_array($sql);

	$name = $row['name'];
	$addr = $row['addr'].' '.$row['addr_dtl'];

	Unset($row);

	$sql = 'SELECT	end_flag
			,		end_dt
			,		end_rsn
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$endYn	= $row['end_flag'];
	$endDt	= $myF->dateStyle($row['end_dt']);
	$endRsn	= StripSlashes($row['end_rsn']);

	Unset($row);

	if (!$endDt) $endDt	= Date('Y-m-d');
?>
<script type="text/javascript">
	$(document).ready(function(){
		if ($('#endYn').val() == 'Y'){
			$('#btnSave').hide();
			$('#btnEnd').css('color','red');
			$('#btnCancel').show();
		}

		$('#divBtn').show();
	});

	//저장
	function lfSave(){
		if (!$('#txtEndDt').val()){
			alert('종결일자를 입력하여 주십시오.');
			$('#txtEndDt').focus();
			return;
		}

		if (!$('#txtEndRsn').val()){
			alert('종결사유를 입력하여 주십시오.');
			$('#txtEndRsn').focus();
			return;
		}

		var data = {};

		data['perJumin']= $('#txtPer').attr('jumin');
		data['iptJumin']= $('#txtIpt').attr('jumin');

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

		$.ajax({
			type:'POST'
		,	url:'./hce_end_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
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

	//종결
	function lfEndRun(){
		$.ajax({
			type:'POST'
		,	url:'./hce_end_run.php'
		,	data:{
				'endDt':$('#txtEndDt').val()
			,	'endRsn':$('#txtEndRsn').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					top.frames['frmTop'].lfTarget();
					$('#btnSave').hide();
					$('#btnEnd').css('color','red');
					$('#btnCancel').show();
					alert('정상적으로 처리되었습니다.');
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

	//종결취소
	function lfEndCancel(){
		$.ajax({
			type:'POST'
		,	url:'./hce_end_cancel.php'
		,	data:{
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					top.frames['frmTop'].lfTarget();
					$('#btnSave').show();
					$('#btnEnd').css('color','');
					$('#btnCancel').hide();
					alert('정상적으로 처리되었습니다.');
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

<div class="title title_border">
	<div style="float:left; width:auto;">서비스종결</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m" id="btnSave"><button type="button" class="bold" onclick="lfSave();">저장</button></span>
		<span class="btn_pack m"><button type="button" class="bold" id="btnEnd" onclick="lfEndRun();">종결</button></span>
		<span class="btn_pack m" id="btnCancel" style="display:none;"><button type="button" class="bold" onclick="lfEndCancel();">종결취소</button></span>
		<span class="btn_pack m"><button type="button" class="bold" onclick="lfPDF('<?=$type;?>');">출력</button></span>
	</div>
</div>


<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">성&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;명</th>
			<td class="left last"><?=$name;?></td>
		</tr>
		<tr>
			<th class="head">주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소</th>
			<td class="left last"><?=$addr;?></td>
		</tr>
		<tr>
			<th class="head">종결일자</th>
			<td class="last"><input id="txtEndDt" type="text" value="<?=$endDt;?>" class="date"></td>
		</tr>
		<tr>
			<th class="head bottom">종결사유</th>
			<td class="bottom last"><input id="txtEndRsn" type="text" value="<?=$endRsn;?>" style="width:100%;"></td>
		</tr>
	</tbody>
</table>

<input id="endYn" type="hidden" value="<?=$endYn;?>">
<?
	include_once('../inc/_db_close.php');
?>