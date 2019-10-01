<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$org_no = $_SESSION['userCenterCode'];
	$SR = $_GET['sr'];
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = 9;

		var title = '';

		switch(opener.type){
			case '1_POP':
				title = '수가조회';
				break;

			default:
				self.close();
				return;
		}

		if (opener.sr == 'S'){
			title += '(재가지원)';
		}else if (opener.sr == 'R'){
			title += '(자원연계)';
		}else{
			self.close();
			return;
		}

		$('#lsTitle').text(title);

		$(':checkbox[id^="chk_"]').unbind('click').bind('click', function(){
			var id = $(this).prop('id');

			$(':checkbox[id^="'+id+'"]').prop('checked', $(this).prop('checked'));
		});
	});

	function lfSave(){
		var para = '';

		$(':checkbox[id^="chk_"]:checked').each(function(){
			if ($(this).val()) para += (para ? '/' : '')+$(this).val();
		});

		if (!para){
			alert('관항목을 선택하여 주십시오.');
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_suga_save.php'
		,	data :{
				'SR':opener.sr
			,	'para':para
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					opener.result = 1;
					self.close();
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title">
	<div>서비스 조회</div>
</div><?
$colgrp = '
	<col width="140px">
	<col width="230px">
	<col width="220px">
	<col>';?>
<table class="my_table my_border" style="width:100%;">
	<colgroup><?=$colgrp;?></colgroup>
	<thead>
		<tr>
			<th class="head">관</th>
			<th class="head">항</th>
			<th class="head">목</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:500px; border-bottom:2px solid #0e69b0;">
	<table class="my_table" style="width:100%; border-top:none;">
		<colgroup><?=$colgrp;?></colgroup>
		<tbody><?
			$sql = 'SELECT	suga_cd
					FROM	care_suga
					WHERE	org_no	= \''.$org_no.'\'
					AND		suga_sr	= \''.$SR.'\'
					';
			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				$suga[$row['suga_cd']] = true;
			}

			$conn->row_free();

			$sql = 'SELECT	*
					FROM	suga_care
					ORDER	BY cd1, cd2, cd3
					';
			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if (!is_array($data[$row['cd1']])) $data[$row['cd1']] = Array('name'=>$row['nm1'], 'rows'=>0, 'LIST'=>null);
				if (!is_array($data[$row['cd1']]['LIST'][$row['cd2']])) $data[$row['cd1']]['LIST'][$row['cd2']] = Array('name'=>$row['nm2'], 'rows'=>0, 'LIST'=>null);

				$data[$row['cd1']]['LIST'][$row['cd2']]['LIST'][$row['cd3']] = $row['nm3'];

				$data[$row['cd1']]['rows'] ++;
				$data[$row['cd1']]['LIST'][$row['cd2']]['rows'] ++;
			}

			$conn->row_free();

			if (is_array($data)){
				foreach($data as $cd1 => $R1){
					foreach($R1['LIST'] as $cd2 => $R2){
						foreach($R2['LIST'] as $cd3 => $name){?>
							<tr><?
							if ($R1['rows'] > 0){?>
								<td rowspan="<?=$R1['rows'];?>" style="vertical-align:top;"><label><input id="chk_<?=$cd1;?>" type="checkbox" value=""><?=$R1['name'];?></label></td><?
							}
							if ($R2['rows'] > 0){?>
								<td rowspan="<?=$R2['rows'];?>" style="vertical-align:top;"><label><input id="chk_<?=$cd1;?>_<?=$cd2;?>" type="checkbox" value=""><?=$R2['name'];?></label></td><?
							}?>
							<td><label><input id="chk_<?=$cd1;?>_<?=$cd2;?>_<?=$cd3;?>" type="checkbox" value="<?=$cd1;?><?=$cd2;?><?=$cd3;?>" <?=$suga[$cd1.$cd2.$cd3] ? 'checked' : '';?>><?=$name;?></label></td>
							<td>&nbsp;</td>
							</tr><?

							$R1['rows'] = 0;
							$R2['rows'] = 0;
						}
					}
				}
			}?>
		</tbody>
	</table>
</div>
<div style="text-align:center;">
	<button onclick="lfSave();">저장</button>
</div>
<?
	include_once('../inc/_footer.php');
?>