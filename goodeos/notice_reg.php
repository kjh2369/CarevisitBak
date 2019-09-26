<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	$id	   = $_REQUEST['id'];
	$page  = $_REQUEST['page'];
	$mode  = $_REQUEST['mode'];
	$pupYn = 'N';

	if (is_numeric($id)){
		$sql = "select subject, content, reg_dt
				  from tbl_goodeos_notice
				 where id = '$id'";
		$notice = $conn->get_array($sql);

		$sql = 'select case when org_no = \'all\' then \'Y\' else \'N\' end
				  from popup_notice
				 where notice_id = \''.$id.'\'';

		$allYn = $conn->get_data($sql);

		$sql = 'select org_no as cd
				,      ct.nm as nm
				,      from_dt
				,      to_dt
				,      read_yn
				  from popup_notice
				 inner join (
					   select m00_mcode as cd
					   ,      min(m00_mkind) as kind
					   ,      m00_store_nm as nm
					     from m00center
						where m00_del_yn = \'N\'
						group by m00_mcode
				 ) as ct
			  on ct.cd = org_no
		   where notice_id = \''.$id.'\'';

		$popup = $conn->_fetch_array($sql);

		if (is_array($popup)) $pupYn = 'Y';
	}
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script type="text/javascript" src="../js/goodeos.js"></script>
<script language='javascript'>
<!--

function findCenter(){
	var code      = 'GE01';
	var send_type = 'center';
	var param     = 'code='+code+'&send_type='+send_type+'&result=2';

	var modal = showModalDialog('../note/note_list_find_1.php?'+param, window, 'dialogWidth:400px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (!modal) return;

	var no   = $('#centerList tr').length;
	var val  = modal.split('//');
	var html = '';

	no ++;

	for(var i=0; i<val.length-1; i++){
		var str = val[i].split('|');

		if ($('#id_'+str[0]).length == 0){
			html += '<tr id=\'id_'+str[0]+'\'>'
				 +  '<td class=\'center\'>'+no+'</td>'
				 +  '<td class=\'center\'><div class=\'left popCd\'>'+str[0]+'</div></td>'
				 +  '<td class=\'center\'><div class=\'left\'>'+str[1]+'</div></td>'
				 +  '<td class=\'center\'><div class=\'left\'><span class=\'btn_pack small\'><button type=\'button\' onclick=\'removeCenter("'+str[0]+'");\'>삭제</button></span></div></td>'
				 +  '</tr>';

			no ++;
		}
	}

	if ($('#centerList tr').length == 0){
		$('#centerList').html(html);
	}else{
		$('#centerList tr:last-child').after(html);
	}
}

function removeCenter(id){
	$('#id_'+id).remove();
	setRowNo();
}

function setRowNo(){
	var no = 1;
	$('#centerList tr').each(function(){
		$('td:first', this).text(no);
		no ++;
	});
}

function popupUse(useYn){
	$('#fDt').attr('disabled', useYn == 'Y' ? false : true);
	$('#tDt').attr('disabled', useYn == 'Y' ? false : true);
	$('#allYn').attr('disabled', useYn == 'Y' ? false : true);
	$('#btnCt').attr('disabled', useYn == 'Y' ? false : true);

	if (useYn == 'Y'){
		$('.popupUse').css('border-bottom','1px solid');
		$('.popupOption').css('display', '');
	}else{
		$('.popupUse').css('border-bottom','none');
		$('.popupOption').css('display', 'none');
	}
}

window.onload = function(){
	popupUse($('input:radio[name="useYn"]:checked').val());
	__init_form(document.f);
}
//-->
</script>
<form name="f" method="post" enctype="multipart/form-data">
<div class="title">
<?
	if ($mode == 'edit'){
		echo '공지사항 등록';
	}else{
		echo '공지사항';
	}
?>
</div>
<table class="my_table my_border">
	<colgroup>
		<col width="70px">
		<col width="700px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>제목</th>
			<?
				if ($mode == 'edit'){
				?>	<td><input name="subject" type="text" value="<?=stripslashes($notice['subject']);?>" style="width:100%;" onFocus="this.select();" tag="제목을 입력하여 주십시오."></td><?
				}else{
				?>	<td class="left"><?=stripslashes($notice['subject']);?></td><?
				}
			?>
			<td class="other">&nbsp;</td>
		</tr>
		<?
			if ($notice['reg_dt']){
			?>	<tr>
					<th>작성일시</th>
					<td class="left"><?=date('Y.m.d H:i:s', $notice['reg_dt']);?></td>
					<td class="other">&nbsp;</td>
				</tr><?
			}

			//팝업관리
			if ($mode == 'edit'){?>
				<tr>
					<th>팝업관리</th>
					<td>
						<table class="my_table" style="width:100%; border-bottom:none;">
							<colgroup>
								<col width="70px">
								<col>
							</colgroup>
							<tbody>
								<tr>
									<th class="popupUse">사용여부</th>
									<td class="popupUse last">
										<input id="useY" name="useYn" type="radio" value="Y" class="radio" onclick="popupUse(this.value);" <?=$pupYn == 'Y' ? 'checked' : '';?>><label for="useY">사용</label>
										<input id="useN" name="useYn" type="radio" value="N" class="radio" onclick="popupUse(this.value);" <?=$pupYn != 'Y' ? 'checked' : '';?>><label for="useN">미사용</label>
									</td>
								</tr>
								<tr class="popupOption" style="display:none;">
									<th class="">적용기간</th>
									<td class="last">
										<input id="fDt" name="fDt" type="text" value="<?=$popup[0]['from_dt'];?>" class="date"> ~
										<input id="tDt" name="tDt" type="text" value="<?=$popup[0]['to_dt'];?>" class="date">
									</td>
								</tr>
								<tr class="popupOption" style="display:none;">
									<th class="bottom" rowspan="2">적용기관</th>
									<td class="last">
										<div style="float:right; width:auto; margin-right:5px;"><span class="btn_pack m"><button id="btnCt" type="button" onclick="findCenter();">기관찾기</button></span></div>
										<div style="float:left; width:auto;"><input id="allYn" name="allYn" type="checkbox" value="Y" class="checkbox" <?=$popup[0]['org_no'] == 'all' ? 'checked' : '';?>><label for="allYn">전체기관</label></div>
									</td>
								</tr>
								<tr class="popupOption" style="display:none;">
									<td class="bottom last">
										<table class="my_table" style="width:100%;">
											<colgroup>
												<col width="40px">
												<col width="100px">
												<col width="300px">
												<col>
											</colgroup>
											<thead>
												<tr>
													<th class="head">No</th>
													<th class="head">기관기호</th>
													<th class="head">기관명</th>
													<th class="head last">비고</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="center top bottom last" colspan="4">
														<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:150px;">
															<table class="my_table" style="width:100%;">
																<colgroup>
																	<col width="40px">
																	<col width="100px">
																	<col width="300px">
																	<col>
																</colgroup>
																<tbody id="centerList">
																<?
																	if (is_array($popup)){
																		$no = 1;
																		foreach($popup as $row){?>
																			<tr id="id_<?=$row['cd'];?>">
																				<td class="center"><?=$no;?></td>
																				<td class="center"><div class="left popCd"><?=$row['cd'];?></div></td>
																				<td class="center"><div class="left"><?=$row['nm'];?></div></td>
																				<td class="center"><div class="left"><span class="btn_pack small"><button type="button" onclick="removeCenter('<?=$row['cd'];?>');">삭제</button></span></div></td>
																			</tr><?
																			$no ++;
																		}
																	}
																?>
																</tbody>
															</table>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr><?
			}
		?>
		<tr>
			<th>내용</th>
			<?
				if ($mode == 'edit'){
				?>	<td style="height:200px; text-align:center; vertical-align:top; padding-top:2px;">
						<!--textarea name="content" style="width:100%; height:96%;" tag="내용을 입력하여 주십시오."><?=stripslashes($notice['content']);?></textarea-->
						<link href="../editor/css/default.css" rel="stylesheet" type="text/css" />
						<script type="text/javascript" src="../editor/js/HuskyEZCreator.js" charset="utf-8"></script>

						<textarea name="ir1" id="ir1" style="width:696px; height:100%;" tag="내용을 입력하여 주십시오."><?=$notice['content'];?></textarea>
						<textarea id="back_content" name="back_content" style="display:none;"></textarea>

						<script>
							var form = document.f;

							var oEditors = [];

							nhn.husky.EZCreator.createInIFrame(oEditors, "ir1", "../editor/SEditorSkin.html", "createSEditorInIFrame", null, false);

							function insertIMG(fname){
								var filepath = form.filepath.value;
								var sHTML = "<img src='" + filepath + "/" + fname + "' style='cursor:hand;' border='0'>";
								oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
							}

							function pasteHTMLDemo(){
								sHTML = "<span style='color:#FF0000'>이미지 등도 이렇게 삽입하면 됩니다.</span>";
								oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
							}

							function showHTML(){
								alert(oEditors.getById["ir1"].getIR());
							}

							function onSubmit(){
								if (form.subject.value == ''){
									alert('제목을 입력하여 주십시오.');
									form.subject.focus();
									return;
								}

								oEditors.getById["ir1"].exec("UPDATE_IR_FIELD", []);

								form.back_content.value = document.getElementById("ir1").value;

								if(form.back_content.value == ""){
									alert("\'내용\'을 입력해 주세요");
									return;
								}

								if ($('input:radio[name="useYn"]:checked').val() == 'Y'){
									var popCd = '';

									if ($('#centerList tr').length == 0 && !$('#allYn').attr('checked')){
										alert('팝업공지를 적용할 기관을 선택하여 주십시오.');
										return;
									}

									if ($('#allYn').attr('checked')){
										popCd = 'all';
									}else{
										$('.popCd').each(function(){
											popCd += '/'+$(this).text();
										});
									}

									$('#popCd').val(popCd);
								}else{
									$('#popCd').val('');
								}

								form.action = './notice_save.php';
								form.submit();
							}
						</script>
					</td><?
				}else{
					if (date('Ymd',$notice['reg_dt']) >= '20120301'){
						$content = $notice['content'];
					}else{
						$content = '<p style="text-align:justify; vertical-align:top; padding:5px;">'.nl2br(stripslashes($notice['content'])).'</p>';
					}
				?>	<td><?=$content;?></td><?
				}
			?>
			</td>
			<td class="other">&nbsp;</td>
		</tr>
		<tr>
			<th>첨부파일</th>
			<td class="">
				<script type="text/javascript">
					function lfAddFile(){
						var html = '';

						html = '<div><input name="attachFile[]" type="file" style="width:200px;"><span class="btn_pack m"><button onclick="$(this).parent().parent().remove();">삭제</button></span></div>';

						if ($('input[type="file"]',$('#divAttachBody')).length > 0){
							$('input[type="file"]:last',$('#divAttachBody')).parent().after(html);
						}else{
							$('#divAttachBody').html(html);
						}
					}

					function lfAttachDownload(seq){
						var parm = new Array();
							parm = {
								'type'	:'NOTICE'
							,	'mode'	:'ATTACH_DOWNLOAD'
							,	'id'	:$('input[name="id"]').val()
							,	'seq'	:seq
							};

						var form = document.createElement('form');
						var objs;
						for(var key in parm){
							objs = document.createElement('input');
							objs.setAttribute('type', 'hidden');
							objs.setAttribute('name', key);
							objs.setAttribute('value', parm[key]);

							form.appendChild(objs);
						}

						form.setAttribute('target', '_self');
						form.setAttribute('method', 'post');
						form.setAttribute('action', './notice_fun.php');

						document.body.appendChild(form);

						form.submit();
					}

					function lfAttachDelete(obj, seq){
						if (!seq) return;
						if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

						$.ajax({
							type:'POST'
						,	url:'./notice_fun.php'
						,	data:{
								'type':'NOTICE'
							,	'mode':'ATTACH_DELETE'
							,	'id':$('input[name="id"]').val()
							,	'seq':seq
							}
						,	beforeSend:function(){
							}
						,	success:function(result){
								if (result == 1){
									$(obj).parent().parent().parent().remove();
								}else if (result == 9){
									alert('파일 삭제중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
								}else{
									alert(result);
								}
							}
						,	complete:function(){
							}
						,	error:function(request, status, error){
								alert('[ERROR No.02]'
									 +'\nCODE : ' + request.status
									 +'\nSTAT : ' + status
									 +'\nMESSAGE : ' + request.responseText);
							}
						});
					}
				</script>
				<?
				if ($_SESSION['userLevel'] == 'A'){?>
					<div class="left" style="padding-top:3px;">
						<span class="btn_pack m"><span class="add"></span><button onclick="lfAddFile();">추가</button></span>
					</div><?
				}
				$sql = 'SELECT	seq
						,		file_path
						,		file_name
						,		file_type
						,		file_size
						FROM	attach_file
						WHERE	id = \'NOTICE_'.$gDomainID.'\'
						AND		attach_key = \''.$id.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);
					$size = $row['file_size'];

					$depth = 0;
					while(true){
						if ($size > 1000){
							$size /= 1000;
							$depth ++;
						}else{
							break;
						}
					}

					$size = Round($size,2);

					if ($depth == 1){
						$gbn = 'KB';
					}else if ($depth == 2){
						$gbn = 'MB';
					}else if ($depth == 3){
						$gbn = 'GB';
					}else if ($depth == 4){
						$gbn = 'TB';
					}else{
						$gbn = 'Byte';
					}?>
					<div class="clear left">
						<div style="float:left; width:auto;"><a href="#" onclick="lfAttachDownload('<?=$row['seq'];?>'); return false;"><?=$row['file_name'];?></a>[<?=$size.$gbn;?>]</div><?
						if ($_SESSION['userLevel'] == 'A'){?>
							<div style="float:left; width:auto;"><span class="btn_pack m"><span class="delete"></span><button onclick="lfAttachDelete(this,'<?=$row['seq'];?>');">삭제</button></span></div><?
						}?>
					</div><?
				}

				$conn->row_free();?>
				<div id="divAttachBody"></div>
			</td>
		</tr>
	</tbody>
</table>
<div style="width:100%; text-align:left; padding:5px;"><div style="width:100%; text-align:right;">
<?
	if ($mode == 'edit'){ ?>
		<span class="btn_pack m"><button type="button" onclick="onSubmit();">저장</button></span>
		<span class="btn_pack m"><button type="button" onclick="_delete_notice();">삭제</button></span> <?
	}
?>	<span class="btn_pack m"><button type="button" onclick="_list_notice();">리스트</button></span>
</div></div>
<input name="id" type="hidden" value="<?=$id;?>">
<input name="page" type="hidden" value="<?=$page;?>">
<input id="popCd" name="popCd" type="hidden" value="">
<input type="hidden" name="filepath" value="upload">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>