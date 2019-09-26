<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_mySuga.php');

	$yymm	= $_POST['yymm'];
	$kind	= $_POST['kind'];
	$upYn	= $_POST['upYn'];
	$test	= $_POST['test'];
?>
<script type="text/javascript" src="../longcare/longcare.js"></script>
<script type="text/javascript" src="./iljung.longcare.js"></script>
<script type="text/javascript" src="./iljung.longcare.result.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		chgSayuChange($('#chgSayu'));
	});

	function chgSayuChange(obj){
		if ($(obj).val() == '04'){
			$('#chgSayuEtc').css('background-color','#FFFFFF').attr('disabled',false);
		}else{
			$('#chgSayuEtc').css('background-color','#EFEFEF').attr('disabled',true);
		}
	}
</script>
<div class="title title_border">변경사유 선택</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>변경사유</th>
			<td>
				<select id="chgSayu" style="width:auto;" onchange="chgSayuChange(this)">
					<option value="">-선택-</option>
					<option value="01">1.천재지변</option>
					<option value="02">2.응급상황</option>
					<option value="03">3.자격변동 처리 지연</option>
					<option value="04">4.기타사유</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>기타사유</th>
			<td>
				<input id="chgSayuEtc" type="text" style="width:100%;">
			</td>
		</tr>
		<tr>
			<td class="center bottom last" style="padding-top:20px;" colspan="2">
				<span class="btn_pack m"><button type="button" onclick="opener.lfLongcareUpload('<?=$yymm;?>', '<?=$kind;?>', '<?=$upYn;?>', '<?=$test;?>',$('#chgSayu').val(),$('#chgSayuEtc').val()); self.close();">업로드</button></span>
				<span class="btn_pack m"><button type="button" onclick="self.close();">취소</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>