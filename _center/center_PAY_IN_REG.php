<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		$('#ID_TEST').height(__GetHeight($('#ID_TEST')) - 82);
		__fileUploadInit($('#f'), 'fileUploadCallback');
	});

	function lfUpload(){
		if (!$('#upladFile').val()){
			alert('엑셀파일을 선택하여 주십시오.');
			$('#upladFile').focus();
			return;
		}

		var exp = $('#upladFile').val().split('.');

		if (exp[exp.length-1].toLowerCase() != 'xls' && exp[exp.length-1].toLowerCase() != 'xlsx'){
			alert('EXCEL 파일을 선택하여 주십시오.');
			return;
		}

		var frm = $('#f');
			frm.attr('action', './center_<?=$menuId;?>_upload.php');
			frm.submit();

		$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
	}

	function fileUploadCallback(data, state){
		if (!data){
			alert('정상적으로 처리되었습니다.');
		}else{
			$('#ID_TEST').html(data).show();
		}
		$('#BTN_UPLOAD').attr('disabled', true);
		$('#tempLodingBar').remove();
	}
</script>

<form id="f" name="f" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">Excel 파일</th>
			<td class="left last">
				<div style="float:left; width:auto;">
					<input type="file" name="upladFile" id="upladFile" style="width:250px; margin:0;">
					<span class="btn_pack m"><button id="BTN_UPLOAD" type="button" onclick="lfUpload();">업로드</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</form>
<div id="ID_TEST" style="overflow-x:hidden; overflow-y:scroll; height:100px;"></div>