<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지 - 사진첨부
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];
	$meetSeq = $_REQUEST['meetSeq'];

?>
<script type="text/javascript">
	var newSeq = 0;

	$(document).ready(function(){
		__fileUploadInit($('#f'), 'fileUploadCallback');
		lfResize();
	});

	function lfResize(){
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		$(obj).height(__GetHeight($(obj)) - 13);
	}

	function lfSave(){
		/*
		if ($('tr', $('#ID_LIST')).length > 0){
			for(var i=0; i<$('tr', $('#ID_LIST')).length; i++){
				//alert($('tr', $('#ID_LIST')).length);
			}
		}
		*/
		
		var frm = $('#f');
			frm.attr('action', './hce_meeting_pic_save.php');
			frm.submit();
		
	}

	function fileUploadCallback(data, state){
		if (data == '1'){
			alert('정상적으로 처리되었습니다');
			location.href("hce_meeting_pic_pop.php?meetSeq=<?=$meetSeq;?>");
		}else if (data == 'ERROR'){
			alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}else{
			alert('사진을 추가하여주십시오.');
		}
	}

	function lfPicReg(seq){
		var html = '';
		
		html += '<tr id="ID_ROW_NEW_'+newSeq+'">'
			 +	'<td class="center" rowspan="2">-</td>'
			 +	'<td class="center">'
			 +	'<div class="left" style="float:left; width:auto;">'
			 +	'제목 :<input id="txtPicName'+newSeq+'" name="txtPicName[]" type="text" value="" style="width:200px;" onfocus="this.style.borderColor=\'#0e69b0\';" onblur="this.style.borderColor=\'\';">'
			 +	'</div>'
			 +	'<div style="float:right; width:100px; margin-left:5px;">'
			 +	'<input name="txtPicFile[]" type="file" style="width:250px; height:18px; onchange="">'
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
		,	url : './hce_meeting_pic_remove.php'
		,	data: {
				'meetSeq':$('#meetSeq').val()
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
<form id="f" name="f" method="post" >
<div class="title title_border">
	<div style="float:left;width:auto;">사례회의록 첨부사진등록</div>
	<div style="float:right; width:auto; margin-top:10px; margin-right:5px;"><span class="btn_pack m"><button type="button" onclick="lfSave();">등록</button></span></div>
</div>
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
									FROM	hce_meeting_pic
									WHERE	org_no	= \''.$orgNo.'\'
									AND		org_type= \''.$hce->SR.'\'
									AND		IPIN	= \''.$hce->IPIN.'\'
									AND		rcpt_seq= \''.$tmpHcptSeq.'\'
									AND		meet_seq= \''.$meetSeq.'\'
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
										<div style="float:right; width:100px; margin-left:5px;">
											<input name="txtPicFile[]" type="file" style="width:250px; height:18px;" onchange="">
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
<input id="meetSeq" name="meetSeq" type="hidden" value="<?=$meetSeq;?>">
</form>
<?
	include_once('../inc/_db_close.php');
?>