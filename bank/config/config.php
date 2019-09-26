<?
	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	include_once('../inc/_menu_top.php');

	$sql = 'SELECT admin_tel
			  FROM bank_config
			 WHERE bank_cd = \'003\'';
	$tel = $conn->get_data($sql);
?>
<table style="width:100%; min-width:1024px;" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr style="height:25px;">
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">관리자 연락처</th>
			<td style="border-right:1px solid #cccccc; border-bottom:1px solid #cccccc; padding-left:5px;">
				<input id="tel" name="txt" type="text" value="<?=$tel;?>" style="width:90px; ime-mode:disabled;">
			</td>
			<td style="border-right:1px solid #cccccc; border-bottom:1px solid #cccccc; padding-left:5px;">
				<button type="button" onclick="lfSave();" style="padding-top:3px;">저장</button>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_menu_foot.php');
?>
<script type="text/javascript">
	function lfSave(){
		$.ajax({
			type: 'POST'
		,	url : '../config/tel_save.php'
		,	data: {
				'cd':'003'
			,	'tel':$('#tel').val()
			}
		,	beforeSend: function (){
			}
		,	success: function (result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('저장 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>