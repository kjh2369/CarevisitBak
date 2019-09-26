<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.worklog = false; //업무일지 작성여부

		lfLoad();
		lfSearch();
	});

	function lfLoad(){
		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_load.php'
		,	data:{
				'svcCd'	:opener.svcCd
			,	'date'	:opener.date
			,	'suga'	:opener.suga
			,	'res'	:opener.resource
			,	'mem'	:opener.mem
			}
		,	success: function(data){
				var obj = __parseVal(data);

				$('#ID_DATE').text(__getDate(opener.date,'.')+' '+__styleTime(opener.time));
				$('#ID_SERVICE').text(obj['service']);
				$('#ID_RESOURCE').text(obj['resource']);
				$('#ID_MEM').text(obj['mem']);
			}
		});
	}

	function lfSave(){
		$('#BTN_SAVE').attr('disabled', true);

		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_all_save.php'
		,	data:{
				'svcCd'	:opener.svcCd
			,	'date'	:opener.date
			,	'time'	:opener.time
			,	'suga'	:opener.suga
			,	'res'	:opener.resource
			,	'mem'	:opener.mem
			,	'request':opener.request
			,	'cont'	:$('#txtContents').val()
			}
		,	success: function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					$('#BTN_SAVE').attr('disabled', false);
					opener.worklog = true;
					self.close();
				}else{
					alert(result);
				}
			}
		});
	}
</script>
<div class="title title_border">업무내용</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">일시</th>
			<td class="left" id="ID_DATE"></td>
		</tr>
		<tr>
			<th class="center">서비스</th>
			<td class="left" id="ID_SERVICE"></td>
		</tr>
		<tr>
			<th class="center">자원</th>
			<td class="left" id="ID_RESOURCE"></td>
		</tr>
		<tr>
			<th class="center">담당자</th>
			<td class="left" id="ID_MEM"></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">업무내용</th>
		</tr>
		<tr>
			<td class="center">
				<textarea id="txtContents" style="width:100%; height:200px;"></textarea>
			</td>
		</tr>
		<tr>
			<td class="center bottom" style="padding-top:10px;">
				<span id="BTN_SAVE" class="btn_pack m"><button onclick="lfSave();">저장</button></span>
				<span class="btn_pack m"><button onclick="self.close();">취소</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>