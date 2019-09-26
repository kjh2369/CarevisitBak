<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = false;

		if (opener.code){
			$('#txtName').val(opener.name);
			$('#txtSeq').val(opener.seq);
		}
	});

	function lfSave(){
		if (!$('#txtName').val()){
			alert('명칭을 입력하여 주십시오.');
			$('#txtName').focus();
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_svc_category_save.php'
		,	data :{
				'SR'	:opener.SR
			,	'parent':opener.parent
			,	'code'	:opener.code
			,	'name'	:$('#txtName').val()
			,	'seq'	:$('#txtSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					opener.result = true;
					self.close();
				}else{
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">카테고리 등록 및 수정</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">카테고리명</th>
			<td><input id="txtName" type="text" value="" style="width:100%;"></td>
		</tr>
		<tr>
			<th class="center">우선순위</th>
			<td><input id="txtSeq" type="text" value="1" class="no_string" style="width:50px;"></td>
		</tr>
		<tr>
			<td class="center bottom last" style="padding-top:10px;" colspan="2">
				<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
				<span class="btn_pack m"><button onclick="self.close();">취소</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>