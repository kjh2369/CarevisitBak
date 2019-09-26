<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
?>
<script type="text/javascript">
<!--

$(document).ready(function(){
	__fileUploadInit($('#frmFile'), 'fileUploadCallback');
});

function fileUpload(){
	if (!$('#filename').val()){
		alert('');
		$('#filename').focus();
		return;
	}

	var exp = $('#filename').val().split('.');

	if (exp[exp.length-1].toLowerCase() != 'xls'){
		alert('EXCEL 파일을 선택하여 주십시오.');
		return;
	}

	var frm = $('#frmFile');
		frm.attr('action', './iljung_excel_upload.php');
		frm.submit();

	$('#msgBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'>'+__get_loading()+'</div></center></div>');
}

function fileUploadCallback(data, state){
	if (__fileUploadCallback(data, state)){
		alert('정상적으로 처리되었습니다.');
	}else{
		alert('저장중 오류가 발생하였습니다.\n관리자에게 문의하여 주십시오.');
	}
	$('#msgBody').html(data).show();
}

-->
</script>

<div class="title title_border">일정계획 업로드(엑셀)</div>

<form id="frmFile" name="frmFile" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">파일선택</th>
			<td class="left last">
				<input type="file" name="filename" id="filename" style="width:250px; margin:0;">
				<span class="btn_pack m"><button type="button" onclick="fileUpload();">업로드</button></span>
			</td>
		</tr>
		<tr>
			<td class="last" colspan="2">
				<input id="duplicateYn" name="duplicateYn" type="checkbox" value="Y" class="checkbox" checked><label for="">중복일정 발생시 수정하지 않습니다.</label>
				<div id="msgBody" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<td class="left last" colspan="2">
				- 이지케어의 "수급자 서비스제공 현황"을 "Excel 97 - 2003 통합 문서"로 저장 후 업로드 하여 주십시오.
			</td>
		</tr>
	</tbody>
</table>
</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>