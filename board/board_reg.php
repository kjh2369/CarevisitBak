<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	//$orgNo	= $_SESSION['userCenterCode'];
	$orgNo	= $_POST['orgNo'];
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$id		= $_POST['id'];
	$mode	= $_POST['mode'];
	$tmpCd	= $cd;

	if (!$orgNo) $orgNo = $_SESSION['userCenterCode'];

	//unlink('./files/data/VA01/8_8__1');

	if ($id){
		$sql = 'SELECT	subject
				,		contents
				FROM	board_list
				WHERE	org_no	= \''.$orgNo.'\'
				AND		brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		brd_cd	= \''.$cd.'\'
				AND		brd_id	= \''.$id.'\'
				AND		del_yn	= \'N\'';

		$row = $conn->get_array($sql);

		$subject = StripSlashes($row['subject']);
		$contents = StripSlashes($row['contents']);

		Unset($row);

		$sql = 'SELECT	file_id
				,		file_name
				,		file_size
				FROM	board_file
				WHERE	org_no	= \''.$orgNo.'\'
				AND		brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		brd_cd	= \''.$cd.'\'
				AND		brd_id	= \''.$id.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$unit = 'B';
			$size = $row['file_size'];
			if ($size / 1204 > 1){
				$unit = 'KB';
				$size = $size / 1024;

				if ($size / 1204 > 1){
					$unit = 'MB';
					$size = $size / 1024;
				}
			}


			$file[] = Array(
				'id'	=>$row['file_id']
			,	'name'	=>$row['file_name']
			,	'size'	=>Round($size,2).$unit
			,	'path'	=>'/board/files/data/VA01'
			,	'file'	=>$gDomainID.'_'.$cd.'_'.$id.'_'.$row['file_id']
			);
		}

		$conn->row_free();
	}

	$title = '';

	while(true){
		$sql = 'SELECT	name, parent
				FROM	board_category
				WHERE	brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		cd		= \''.$tmpCd.'\'';

		$row = $conn->get_array($sql);
		$title = $row['name'].'|'.$title;

		if ($row['parent'] < 1) break;

		$tmpCd = $row['parent'];
	}

	$title = SubStr($title,0,StrLen($title)-1);
	$title = str_replace('|',' / ',$title);
?>
<script type="text/javascript">
	//var opener = null;

	$(document).ready(function(){
		//opener = window.dialogArguments;
		__fileUploadInit($('#f'), 'fileUploadCallback');
		$('input:text').each(function(){
			__init_object(this);
		});
	});

	window.onunload = function(){
		opener.lfSearch();
	}

	function lfAttachFile(){
		var html = '<div id="divAttachFile"><input id="file" name="file[]" type="file" style="width:300px;"></div>';

		if ($('div[id="divAttachFile"]',$('#divAttachList')).length > 0){
			$('div[id="divAttachFile"]:last',$('#divAttachList')).after(html);
		}else{
			$('#divAttachList').html(html);
		}
	}

	function lfSave(){
		if (!$('#txtSubject').val()){
			alert('제목을 입력하여 주십시오.');
			$('#txtSubject').focus();
			return;
		}

		oEditors.getById["ir1"].exec("UPDATE_IR_FIELD", []);

		if (!$('#ir1').val()){
			alert('내용을 입력하여 주십시오.');
			$('#ir1').focus();
			return;
		}

		$.ajax({
			type:'POST',
			url:'./board_save.php',
			data:{
				'orgNo'		:'<?=$orgNo;?>'
			,	'type'		:$('#type').val()
			,	'cd'		:$('#cd').val()
			,	'id'		:$('#seq').val()
			,	'subject'	:$('#txtSubject').val()
			,	'contents'	:$('#ir1').val()
			},
			beforeSend: function (){
			},
			success:function(result){
				if (result == 'ERROR'){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
					return;
				}else{
					$('#seq').val(result);

					if ($('div[id="divAttachFile"]',$('#divAttachList')).length > 0){
						var frm = $('#f');
							frm.attr('action', './board_file_upload.php');
							frm.submit();
					}else{
						alert('정상적으로 처리되었습니다.');
						self.close();
					}
				}
			},
			error: function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function fileUploadCallback(data, state){
		if (data == 1){
			alert('정상적으로 처리되었습니다.');
			self.close();
		}else if (data == 'ERROR'){
			alert('저장중 오류가 발생하였습니다.\n관리자에게 문의하여 주십시오.');
		}else{
			alert(data);
		}
	}

	function lfAttachRemove(id){
		if (!confirm('첨부파일 삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST',
			url:'./attach_remove.php',
			data:{
				'type'	:$('#type').val()
			,	'cd'	:$('#cd').val()
			,	'id'	:$('#seq').val()
			,	'file'	:id
			},
			beforeSend: function (){
			},
			success:function(result){
				if (__resultMsg(result)){
					$('#divAttachList_'+id).remove();
				}
			},
			error: function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}
</script>
<div id="lblTitle" class="title title_border"><?=$title;?></div>
<form id="f" name="f" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">제목</th>
			<td><?
				if ($mode == 'MOD'){?>
					<input id="txtSubject" type="text" value="<?=$subject;?>" style="width:100%;"><?
				}else{?>
					<span class="left"><?=$subject;?></span><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="center">내용</th>
			<td style=""><?
				if ($mode == 'MOD'){?>
					<div style="padding:5px;">
						<link href="../editor/css/default.css" rel="stylesheet" type="text/css" />
						<script type="text/javascript" src="../editor/js/HuskyEZCreator.js" charset="utf-8"></script>

						<textarea name="ir1" id="ir1" style="width:100%; height:230px;" tag="내용을 입력하여 주십시오."><?=$contents;?></textarea>
						<textarea id="backContent" name="back_content" style="display:none;"></textarea>

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

								//form.action = 'board_reg_ok.php';
								//form.submit();
							}
						</script>
					</div><?
				}else{?>
					<div style="width:100%; height:300px; overflow:scroll; padding:5px;"><?=$contents;?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="center">첨부파일</th>
			<td><?
				if ($mode == 'MOD'){?>
					<div style="padding:5px; border-bottom:1px solid #CCCCCC;">
						<span class="btn_pack small"><button onclick="lfAttachFile();">첨부파일 추가</button></span>
					</div><?
				}?>
				<div id="divAttachList" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;"><?
				if (is_array($file)){?>
					<script type="text/javascript">
						function lfDownload(id){
							var parm = new Array();
								parm = {
									'orgNo'	:'<?=$orgNo;?>'
								,	'type'	:$('#type').val()
								,	'brdCd'	:$('#cd').val()
								,	'brdId'	:$('#seq').val()
								,	'filId'	:id
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
							form.setAttribute('action', './download.php');

							document.body.appendChild(form);

							form.submit();
						}
					</script>
					<div id="divAttachFile" style="padding:5px;"><?
						/*
						Array ( [id] => 1 [name] => caption_2_vaerp_com.gif [size] => 407B [path] => /board/files/data/VA01 [file] => 8_8_1_1 ) Array ( [id] => 2 [name] => caption_3_vaerp_com.gif [size] => 477B [path] => /board/files/data/VA01 [file] => 8_8_1_2 ) Array ( [id] => 3 [name] => caption_7_vaerp_com.gif [size] => 598B [path] => /board/files/data/VA01 [file] => 8_8_1_3 )
						 */
						foreach($file as $f){?>
							<div id="divAttachList_<?=$f['id'];?>" style="line-height:1em;">
								<span style=""><?=$f['name'];?></span>
								<span style="">[<?=$f['size'];?>]</span>
								<span style="margin-left:5px;" onclick="lfDownload('<?=$f['id'];?>');"><a href="#" onclick="return false;">다운로드</a></span><?
								if ($mode == 'MOD'){?>
									| <span style="" onclick="lfAttachRemove('<?=$f['id'];?>');"><a href="#" onclick="return false;">삭제</a></span><?
								}?>
							</div><?
						}?>
					</div><?
				}?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="center bottom" colspan="2"><?
				if ($mode == 'MOD'){?>
					<span class="btn_pack small"><button onclick="lfSave();">저장</button></span>
					<span class="btn_pack small"><button onclick="self.close();">취소</button></span><?
				}else{?>
					<span class="btn_pack small"><button onclick="self.close();">닫기</button></span><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="filepath" value="upload">
<input type="hidden" name="type" id="type" value="<?=$type;?>">
<input type="hidden" name="cd" id="cd" value="<?=$cd;?>">
<input type="hidden" name="seq" id="seq" value="<?=$id;?>">
</form>
<?
	Unset($file);
	include_once('../inc/_footer.php');
?>