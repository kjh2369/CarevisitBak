<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사정기록지 - 사진첨부
	 *********************************************************/

	$isptSeq = '1';
?>
<script type="text/javascript">
	var newSeq = 0;

	$(document).ready(function(){
		__fileUploadInit($('form[name="f"]'), 'fileUploadCallback');
		lfResize();
	});

	function lfResize(){
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		$(obj).height(__GetHeight($(obj)) - 13);
	}

	function lfSaveSub(){
		var frm = $('form[name="f"]');
			frm.attr('action', './hce_ispt_10_picreg.php');
			frm.submit();
	}

	function fileUploadCallback(data, state){
		if (data == 'ERROR'){
			alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}else{
			lfLoadBody('10');
		}
	}

	function lfPicReg(seq){
		var html = '';

		html += '<tr id="ID_ROW_NEW_'+newSeq+'">'
			 +	'<td class="center" rowspan="2">-</td>'
			 +	'<td class="center">'
			 +	'<div class="left" style="float:left; width:auto;">'
			 +	'제목 :<input name="txtPicName[]" type="text" value="" style="width:200px;" onfocus="this.style.borderColor=\'#0e69b0\';" onblur="this.style.borderColor=\'\';">'
			 +	'</div>'
			 +	'<div style="float:right; width:100px; margin-left:5px; background:url(../image/find_file.gif) no-repeat left 50%;">'
			 +	'<input name="txtPicFile[]" type="file" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-7px;" onchange="">'
			 +	'</div>'
			 +	'</td>'
			 +	'<td rowspan="2">'
			 +	'<div class="left"><span class="btn_pack small"><button onclick="lfCancel(this);">취소</button></span></div>'
			 +	'<input name="txtSeq[]" type="hidden" value="0">'
			 +	'</td>'
			 +	'</tr>'
			 +	'<tr id="ID_ROW_NEW_'+newSeq+'">'
			 +	'<td></td>'
			 +	'</tr>';

		newSeq ++;

		if ($('tr', $('#ID_LIST')).length > 0){
			$('tr:first', $('#ID_LIST')).before(html);
		}else{
			$('#ID_LIST').html(html);
		}
	}

	function lfCancel(obj){
		var obj = __GetTagObject($(obj),'TR');
		var id = $(obj).attr('id');

		$('tr[id="'+id+'"]',$('#ID_LIST')).remove();
	}

	function lfRemove(obj,seq){
		if (!seq) return;

		$.ajax({
			type: 'POST'
		,	url : './hce_ispt_10_remove.php'
		,	data: {
				'isptSeq':$('#isptSeq').val()
			,	'seq':seq
			}
		,	beforeSend: function (){
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					lfCancel(obj);
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite: function(result){

			}
		,	error: function (error){
			}
		}).responseXML;
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="600px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">사진</th>
			<th class="head">
				<div style="float:right; width:auto; margin-right:5px;"><span class="btn_pack small"><button onclick="lfPicReg();">추가</button></span></div>비고
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="3">
				<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="600px">
							<col>
						</colgroup>
						<tbody id="ID_LIST"><?
							if ($_POST['hcptSeq']) $tmpHcptSeq = $_POST['hcptSeq'];
							if (!$tmpHcptSeq) $tmpHcptSeq = $hce->rcpt;

							$sql = 'SELECT	pic_seq
									,		pic_name
									,		pic_file
									,		pic_path
									FROM	hce_inspection_pic
									WHERE	org_no	= \''.$orgNo.'\'
									AND		org_type= \''.$hce->SR.'\'
									AND		IPIN	= \''.$hce->IPIN.'\'
									AND		rcpt_seq= \''.$tmpHcptSeq.'\'
									AND		ispt_seq= \''.$isptSeq.'\'
									AND		del_flag= \'N\'';

							$conn->query($sql);
							$conn->fetch();

							$rowCnt = $conn->row_count();
							$maxW = 550;
							$maxH = 250;
							$no = 1;

							for($i=0; $i<$rowCnt; $i++){
								$row = $conn->select_row($i);?>
								<tr id="ID_ROW_<?=$i;?>">
									<td class="center" rowspan="2"><?=$no;?></td>
									<td class="center">
										<div class="left" style="float:left; width:auto;">
											제목 :<input name="txtPicName[]" type="text" value="<?=$row['pic_name'];?>" style="width:200px;" onfocus="this.style.borderColor='#0e69b0';" onblur="this.style.borderColor='';">
										</div>
										<div style="float:right; width:100px; margin-left:5px; background:url(../image/find_file.gif) no-repeat left 50%;">
											<input name="txtPicFile[]" type="file" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-7px;" onchange="">
										</div>
									</td>
									<td class="center" rowspan="2">
										<div class="left"><span class="btn_pack small"><button onclick="lfRemove(this,'<?=$row['pic_seq'];?>');" style="color:RED;">삭제</button></span></div><?
										if (is_file($row['pic_path'])){?>
											<div class="left"><span class="btn_pack small"><button onclick="location.href='../file/download.php?file=<?=$row['pic_path'];?>&name=<?=$row['pic_file'];?>';">다운로드</button></span></div><?
										}?>
										<input name="txtSeq[]" type="hidden" value="<?=$row['pic_seq'];?>">
									</td>
								</tr>
								<tr id="ID_ROW_<?=$i;?>">
									<td class="center">
										<div class="left" style="border-bottom:1px solid #CCCCCC;"><?=$row['pic_file'];?></div>
										<div id="ID_PICVIEW" style="width:600px; height:300px; overflow-x:scroll; overflow-y:scroll; padding:5px;"><?
											if (is_file($row['pic_path'])){
												$imgSize = GetImageSize($row['pic_path']);
												$imgW = $imgSize[0];
												$imgH = $imgSize[1];

												if ($imgW > $imgH){
													$rate = $imgH / $imgW;
												}else if ($imgH > $imgW){
													$rate = $imgW / $imgH;
												}else{
													$rate = 1;
												}

												if ($imgW > $maxW){
													$imgW = $maxW;
													$imgH = $imgH * $rate;
												}

												if ($imgH > $maxH){
													$imgH = $maxH;
													$imgW = $imgW * $rate;
												}

												?>
												<img src="<?=$row['pic_path'];?>" style="width:<?=$imgW;?>px; height:<?=$imgH;?>px;" border="0"><?
											}?>
										</div>
									</td>
								</tr><?

								$no ++;
							}

							$conn->row_free();?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<input id="bodyIdx" type="hidden" value="10">
<input id="isptSeq" name="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>