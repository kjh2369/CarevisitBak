<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');

	$year	= $_POST['year'];
	$month	= $_POST['month'];
?>
<script type="text/javascript">
	//일정정리
	function lfCalClean(asType){
		if (asType == '1'){
			//중복일정삭제
			$('.clsCal[duplicate!="1"]').remove();
		}else if (asType == '2'){
			//미저장삭제
			$('.clsCal[stat="9"]').remove();
		}
	}

	//일정저장
	function lfSave(){
		var data = '';

		$('.clsCal[duplicate="1"]').each(function(){
			data += 'day='+$(this).attr('day');
			data += '&jumin='+$(this).attr('jumin');
			data += '&from='+$(this).attr('from').split(':').join('');
			data += '&to='+$(this).attr('to').split(':').join('');
			data += '&proc='+$(this).attr('proc');
			data += String.fromCharCode(13);
		});

		$.ajax({
			type : 'POST'
		,	url  : './care_save.php'
		,	data : {
				'jumin':$('#jumin').val()
			,	'year':$('#year').val()
			,	'month':$('#month').val()
			,	'sr':$('#sr').val()
			,	'data':data
			}
		,	beforeSend: function(){
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					lfLoadIljung();
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
		<col width="500px">
	</colgroup>
	<tbody>
		<tr>
			<td id="tdYYMM" class="center last"><span class="bold"><?=intval($year);?>년 <?=intval($month);?>월</span></td>
			<td class="right last">
				<img src="./img/btn_calen7.gif" onclick="lfCalClean('1');" alt="중복일정지우기">
				<img src="./img/btn_calen8.gif" onclick="lfCalClean('2');" alt="미저장일정지우기">
				<img src="../image/btn_save_2.png" onclick="lfSave();" alt="일정저장">
				<img src="../image/btn11.gif" onclick="lfDelete();" alt="일적삭제">
				<img src="../image/btn_print_1.png" onclick="lfShowCaln('Y');" alt="금액표시된 출력물입니다.">
				<img src="../image/btn_print_2.png" onclick="lfShowCaln('N');" alt="금액 미표시된 출력물입니다.">
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>