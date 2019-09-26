<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$type  = $_POST['type'];
	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$yymm = $_POST['yymm'];
	$seq = $_POST['seq'];

	//지시내용
	$sql = 'SELECT	command
			,		sign_manager
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		seq		= \''.$seq.'\'
			AND		del_flag= \'N\'';
	$row = $conn->get_array($sql);

	$command = $row['command'];
	$sign = $row['sign_manager'];

	if ($command){
		$IsSign = true;
	}else{
		$IsSign = false;
	}

	Unset($row);
?>
<script type="text/javascript">
	function lfSave(gbn){
		
		/*
		if (!gbn){
			if (!$('#txtCommand').val()){
				alert('지시내용을 입력해 주십시오.');
				return;
			}
		}
		*/

		var data = {};

		$('input[type="hidden"]').each(function(){
			var id = $(this).attr('id');
			data[id] = $(this).val();
		});

		$('textarea').each(function(){
			var id = $(this).attr('id');
			data[id] = $(this).val();
		});

		data['signManager'] = $('#cboManager').val();

		if (gbn == 'NOT'){
			data['txtCommand'] = '';
			data['signManager'] = '';
		}


		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_sign_reg_save.php'
		,	data:data
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					opener.lfSearch();
					setTimeout('lfLoad()',100);
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}
</script>
<table class="my_table" style="width:100%; border-top:1px solid #ccc;">
<colgroup>
<col width="100px">
<col width="*">
<col width="120px">
</colgroup>
<tbody>
	<tr>
		<th class="center" colspan="2">결재 확인</th>
		<th class="center">
			<select id="cboManager" style="width:94%;" onchange="lfChkSignManager();">
				<option value="">-</option><?
				$sql = 'SELECT	DISTINCT
								m02_key AS cd_key
						,		m02_yname AS name
						FROM	m02yoyangsa
						WHERE	m02_ccode = \''.$orgNo.'\'
						AND		m02_jikwon_gbn IN (\'B\',\'A\',\'C\',\'M\')';

				if ($_SESSION['userLevel'] == 'P'){
					$sql .= '
						AND		m02_yjumin = \''.$_SESSION['userSSN'].'\'';
				}

				$sql .= '
						ORDER	BY name';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<option value="<?=$row['cd_key'];?>" <?=($sign == $row['cd_key'] ? 'selected' : '');?>><?=$row['name'];?></option><?
				}

				$conn->row_free();?>
			</select>
			<script type="text/javascript">
				function lfChkSignManager(){
					if (!$('#cboManager').val()) return;

					$.ajax({
						type:'POST'
					,	url:'./sw_work_log_sign_image.php'
					,	data:{
							'key':$('#cboManager').val()
						}
					,	beforeSend:function(){
						}
					,	success:function(result){
							if (result){
								var w = $('#divSignManager').width() * 0.97;
								var h = $('#divSignManager').height() * 0.97;
								$('#divSignManager').html('<img id="imgSignManager" src="'+result+'?number='+Math.random()*1000+'" border="0" style="display:none;">');
								setTimeout('lfDrawSingManager('+w+','+h+')',100);
							}else{
								$('#divSignManager').html('등록된 사인이 없습니다.');
							}
						}
					});
				}

				function lfDrawSingManager(w1,h1){
					var w2 = $('#imgSignManager').width();
					var h2 = $('#imgSignManager').height();

					var r = 1;

					if (w2 > w1 || h2 > h1){
						if (w2 > w1){
							r = (w2 - w1) / w2;
							w2 = w1;
							h2 = (h2 - (h2 * r));
						}

						if (h2 > h1){
							r = (h2 - h1) / h2;
							h2 = h1;
							w2 = (w2 - (w2 * r));
						}

						$('#imgSignManager').width(w2).height(h2).show();
					}
				}

				lfChkSignManager();
			</script>
		</th>
	</tr>
	<tr>
		<th class="center bottom">지시내용</th>
		<td class="bottom">
			<textarea id="txtCommand" init="Y" style="width:100%; height:50px;" ><?=StripSlashes($command);?></textarea>
		</td>
		<td class="center bottom">
			<div id="divSignManager">책임자를 선택하여 주십시오.</div>
		</td>
	</tr>
</tbody>
</table>
<div id="divBtnBody" class="center" style="height:28px; border-top:1px solid #CCCCCC;">
	<div style="margin-top:5px;">
		<span class="btn_pack small"><button type="button" onclick="lfSave();  opener.lfSearch();">저장</button></span><?
		if ($IsSign){?>
			<span class="btn_pack small"><button type="button" onclick="lfSave('NOT');" style="color:RED;">결재취소</button></span><?
		}?>
		<span class="btn_pack small"><button type="button" onclick="self.close();">닫기</button></span>
	</div>
</div>
<input id="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<input id="year" type="hidden" value="<?=$year;?>">
<input id="month" type="hidden" value="<?=$month;?>">
<input id="yymm" type="hidden" value="<?=$yymm;?>">
<input id="seq" type="hidden" value="<?=$seq;?>">
<?
	include_once('../inc/_footer.php');
?>