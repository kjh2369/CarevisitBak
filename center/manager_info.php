<?
	include_once('../inc/_header.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
		$('#name').focus();
	});

	function lfSave(){
		if (!$('#name').val()){
			alert('성명을 입력하여 주십시오.');
			$('#name').focus();
			return;
		}

		if (!$('#tel').val()){
			alert('연락처를 입력하여 주십시오.');
			$('#tel').focus();
			return;
		}

		$.ajax({
			type : 'POST'
		,	url  : './manager_info_save.php'
		,	data : {
				'name':$('#name').val()
			,	'tel':$('#tel').val()
			}
		,	beforeSend: function(){
			}
		,	success: function(result){
				alert('정상적으로 처리되었습니다.');
				self.close();
			}
		});
	}
</script>
<form name="f">
<div class="title title_border">기관장정보</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>성명</th>
			<td class="last"><input id="name" type="text" value=""></td>
		</tr>
		<tr>
			<th>연락처</th>
			<td class="last"><input id="tel" type="text" value="" class="phone"></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="2">
				<span class="btn_pack m"><button onclick="lfSave();">등록</button></span>
			</td>
		</tr>
		<tr>
			<td class="left bottom last" colspan="2">
				※기관장님께 긴급공지 및 빠른 연락을 위해 기관장님의 성명과 연락처를 남겨주시기 바랍니다.
			</td>
		</tr>
	</tfoot>
</table>
</form>
<?
	include_once('../inc/_footer.php');
?>