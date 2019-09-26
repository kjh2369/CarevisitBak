<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		$('#ID_CELL_YM').text(opener.year+'년 '+opener.month+'월');
		$('input:text').each(function(){
			__init_object(this);
		});
		$('#txtAmt').val(__num2str(opener.val));
	});

	window.onunload = function(){
		opener.win.lfSetIE('T', __str2num(opener.month), __str2num($('#txtAmt').val()));
	}

	function lfSave(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_company_target_save.php'
		,	data :{
				'year'	:opener.year
			,	'month'	:opener.month
			,	'amt'	:__str2num($('#txtAmt').val())
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					self.close();
				}else if (result == 9){
					alert('데이타 전송중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">목표금액등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="left" id="ID_CELL_YM"></td>
		</tr>
		<tr>
			<th class="center">수입</th>
			<td><input id="txtAmt" type="text" value="0" class="number"></td>
		</tr>
		<tr>
			<td class="center bottom" style="padding-top:5px;" colspan="2">
				<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
				<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>