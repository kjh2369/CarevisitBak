<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year = Date('Y');
	$month = Date('n');
?>
<script type="text/javascript">
	$(document).ready(function(){
		__fileUploadInit($('#frmFile'), 'fileUploadCallback');

		$(':radio[name="acctType"]').bind('click', function(){
			if ($(this).val() == '1'){
				$(':checkbox[name="deleteFlag"]').attr('disabled', false);
			}else{
				$(':checkbox[name="deleteFlag"]').attr('disabled', true);
			}
		});
	});

	function fileUpload(){
		if (!$('#filename').val()){
			alert('엑셀파일을 선택하여 주십시오.');
			$('#filename').focus();
			return;
		}

		var exp = $('#filename').val().split('.');

		if (exp[exp.length-1].toLowerCase() != 'xlsx'){
			alert('EXCEL 파일을 선택하여 주십시오.');
			return;
		}

		var frm = $('#frmFile');
			frm.attr('action', '../_center/fee_excel_upload.php?year='+$('#lblYYMM').attr('year')+'&month='+$('#lblYYMM').attr('month'));
			frm.submit();
		//$('#msgBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'>'+__get_loading()+'</div></center></div>');
	}

	function fileUploadCallback(result, state){
		alert(result);

		/*if (!result){
			alert('정상적으로 처리되었습니다.');
		}else{
			if ('<?=$debug;?>' == '1'){
				$('#tblList').html(result).show();
			}else{
				if (result == 'ERROR'){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					$('#tblList').html(result);
				}
			}
		}
		//$('#tblList').html(html);*/
		//$('#tempLodingBar').remove();
	}
</script>
<form id="frmFile" name="frmFile" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">청구년월</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last">
				<div style="float:left; width:auto;"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM"));');?></div>
			</td>
		</tr>
		<tr>
			<th class="center">엑셀구분</th>
			<td class="last" colspan="2">
				<label><input name="acctType" type="radio" class="radio" value="1" checked>청구내역</label>
				(<label><input name="deleteFlag" type="checkbox" class="checkbox" value="Y">기존내역삭제</label>)
				<label><input name="acctType" type="radio" class="radio" value="2">미납내역</label>
			</td>
		</tr>
		<tr>
			<th class="center">엑셀파일</th>
			<td class="last" colspan="2">
				<input type="file" name="filename" id="filename" style="width:250px; margin-right:0;">
				<span class="btn_pack m icon"><span class="excel"></span><button onclick="fileUpload();">Excel 업로드</button></span>
			</td>
		</tr>
	</tbody>
</table>
</form>
<div id="divTitle" class="title title_border" style="width:100%;">업로드결과</div>