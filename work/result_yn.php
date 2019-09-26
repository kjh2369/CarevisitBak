<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$conf_dt = $_GET['conf_dt'];
	$gubun	 = $_GET['gubun'];

	switch($gubun){
	case 1:
		$title = '실적일괄확정취소 획인';
		break;
	case 2:
		$title = '급여자동계산취소 획인';
		break;
	}
?>
<base target="_self">
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function cancel_ok(){
	var f = document.f;
	var gubun = f.gubun.value;
	var value = f.cancel_y.value+'-'+f.cancel_m.value+'-'+f.cancel_d.value;

	if (value.length != 10){
		alert('취소일자를 입력하여 주십시오.');
		f.cancel_y.focus();
		return;
	}

	if (!checkDate(value)){
		alert('취소일자 입력 오류입니다. 올바르게 입력하여 주십시오.');
		f.cancel_y.value = '';
		f.cancel_m.value = '';
		f.cancel_d.value = '';
		f.cancel_y.focus();
		return;
	}

	if (value < f.conf_dt.value){
		var msg = null;

		if (gubun == 1){
			msg = '일괄확정일자';
		}else{
			msg = '급여계산일자';
		}
		alert('취소일자는 '+msg+'('+f.conf_dt.value+')보다 커야합니다. 확인하여 주십시오.');
		f.cancel_y.value = '';
		f.cancel_m.value = '';
		f.cancel_d.value = '';
		f.cancel_y.focus();
		return;
	}

	result_ok(value);
}

function result_ok(value){
	window.returnValue = value;
	window.close();
}

-->
</script>

<div class="title"><?=$title;?></div>

<form name="f" method="post">

<table class="my_table my_border" style="border-bottom:none;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">취소일자</th>
			<td class="left">
				<input name="cancel_y" type="text" value="" maxlength="4" class="no_string" style="width:40px; text-align:center;" onkeydown="__onlyNumber(this);">년
				<input name="cancel_m" type="text" value="" maxlength="2" class="no_string" style="width:30px; text-align:center;" onkeydown="__onlyNumber(this);" onblur="if(this.value.length == 1){this.value = '0'+this.value;}">월
				<input name="cancel_d" type="text" value="" maxlength="2" class="no_string" style="width:30px; text-align:center;" onkeydown="__onlyNumber(this);" onblur="if(this.value.length == 1){this.value = '0'+this.value;}">일
			</td>
		</tr>
		<tr>
			<td class="center bottom" style="padding-top:10px;" colspan="2">
				<span class="btn_pack m"><button type="button" onclick="cancel_ok();">확인</button></span>
				<span class="btn_pack m"><button type="button" onclick="window.self.close();">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="conf_dt" value="<?=$ed->de($conf_dt);?>">
<input type="hidden" name="gubun"   value="<?=$gubun;?>">

</form>
<?
	include_once("../inc/_footer.php");
?>