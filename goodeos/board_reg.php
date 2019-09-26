<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	if ($_SESSION['userLevel'] == 'A'){
		$code = $_REQUEST['code'];
	}else{
		$code = $_SESSION['userCenterCode'];
	}

	$board_type	= $_REQUEST['board_type'];
	$board_id	= $_REQUEST['board_id'];
	$board_seq	= $_REQUEST['board_seq'];
	$reply_id	= $_REQUEST['reply_id'];
	$page		= $_REQUEST['page'];
	$mode		= $_REQUEST['mode'];
	$title		= $myF->board_name($board_type);

	$find_type  = $_REQUEST['f_type'];
	$find_text  = $_REQUEST['f_text'];


	if ($board_id > 0){
		switch($mode){
		case 1:
			$title .= ' 수정';
			break;
		case 2:

			// 카운트 증가
			$sql = "update tbl_board
					   set count        = count + 1
					 where board_center = '$code'
					   and board_type   = '$board_type'
					   and board_id     = '$board_id'";


			$conn->begin();
			if ($conn->execute($sql)){
				$conn->commit();
			}else{
				$conn->rollback();
			}
			break;
		case 3:
			$title .= ' 답변';
			break;
		}
		//$title .= ($mode == 1 ? ' 수정' : '');

		$sql = "select *
				  from tbl_board
				 where board_center = '$code'
				   and board_type   = '$board_type'
				   and board_id     = '$board_id'";
		$mst = $conn->get_array($sql);

		if ($mode == 3){
			$sql = "select *
					  from tbl_board
					 where board_center = '$code'
					   and board_type   = '$board_type'
					   and board_id     = '$reply_id'";
			$mst_re = $conn->get_array($sql);
		}
	}else{
		$title .= ' 등록';
	}


	$sql = 'select m00_ctel
			  from m00center
			 where m00_mcode = \''.$code.'\'';
	$ctel = $conn -> get_data($sql);

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

function list(){
	var f = document.f;

	f.action = 'board_list.php';
	f.submit();
}

function answer(reply_id){
	var f = document.f;

	f.reply_id.value = reply_id;
	f.mode.value = 3;
	f.action = 'board_reg.php';
	f.submit();
}

function modify(){
	var f = document.f;

	f.mode.value = 1;
	f.action = 'board_reg.php';
	f.submit();
}

function view(){
	var f = document.f;

	f.mode.value = 2;
	f.action = 'board_reg.php';
	f.submit();
}

function del(){
	var f = document.f;

	if (!confirm('삭제후 데이타 복원이 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	f.action = 'board_delete.php';
	f.submit();
}

function reg_reply(board_seq){
	var f = document.f;
	var reply = null;

	if (board_seq == 0){
		reply = document.getElementById('reply');
	}else{
		reply = document.getElementById('reply_'+board_seq);
	}

	if (reply.value == ''){
		alert('댓글 내용을 입력하여 주십시오.');
		reply.focus();
		return;
	}

	f.board_seq.value = board_seq;
	f.action = 'board_reply_ok.php';
	f.submit();
}

function modify_reply(board_seq, mode){
	var f = document.f;
	var modify = document.getElementById('reply_modify_'+board_seq);
	var text = document.getElementById('reply_text_'+board_seq);
	var cont = document.getElementById('reply_cont_'+board_seq);

	if (mode == 1){
		modify.value = '취소';
		modify.onclick = function(){
			modify_reply(board_seq, 2);
		}
		text.style.display = 'none';
		cont.style.display = '';
	}else{
		modify.value = '수정';
		modify.onclick = function(){
			modify_reply(board_seq, 1);
		}
		text.style.display = '';
		cont.style.display = 'none';
	}
}

function delete_reply(board_seq){
	var f = document.f;

	if (!confirm('삭제후 데이타 복원이 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	f.board_seq.value = board_seq;
	f.action = 'board_reply_delete.php';
	f.submit();
}

function answer_view(answer_id){
	var list = document.getElementsByName('answer_list[]');

	for(var i=0; i<list.length; i++){
		list[i].style.display = 'none';
	}
	list[answer_id].style.display = '';
}

function answer_delete(answer_id){
	var f = document.f;

	if (!confirm('삭제후 데이타 복원이 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	f.mode.value = 3;
	f.reply_id.value = answer_id;
	f.action = 'board_delete.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}
//-->
</script>
<form name="f" method="post" enctype="multipart/form-data">
<div class="title" style="width:auto; float:left;"><?=$title;?></div>
<div style="width:auto; float:right; margin-top:8px;">
<?
	if ($mode == 1 || $mode == 3){?>
		<span class="btn_pack m"><button type="button" onclick="onSubmit();">저장</button></span><?
		if (($mode == 1 && $board_id > 0) || $mode == 3){?>
			<span class="btn_pack m"><button type="button" onclick="view();">취소</button></span><?
		}
	}else{
		if (($_SESSION['userLevel'] == 'C' && $board_type == 'noti') || $_SESSION['userLevel'] == 'A'){?>
			<span class="btn_pack m"><button type="button" onclick="answer(0);">답변</button></span><?
		}

		if ($_SESSION['userCode'] == $mst['reg_id']){ ?>
			<span class="btn_pack m"><button type="button" onclick="modify();">수정</button></span><?
			if ($board_id > 0){?>
				<span class="btn_pack m"><button type="button" onclick="del();">삭제</button></span><?
			}
		}
	}
?>	<span class="btn_pack m"><button type="button" onclick="list();">리스트</button></span>
</div>
<table class="my_table my_border">
	<colgroup>
		<col width="100px">
		<?
			if ($_SESSION['userLevel'] == 'A'){?>
				<col width="150px">
				<col width="100px">
				<col width="200px">
				<col width="100px">
				<col width="100px">
				<col width="100px"><?
			}
		?>
		<col>
	</colgroup>
	<tbody>
		<?
			if ($_SESSION['userLevel'] == 'A'){?>
				<tr>
					<th>기관코드</th>
					<td class="left"><?=$code;?></td>
					<th>기관명</th>
					<td  class="left"><?=$conn->center_name($code);?></td>
					<th>연락처</th>
					<td  class="left last"><?=$myF->phoneStyle($ctel);?></td>
				</tr><?
			}

			if (($mode > 1 || $board_id > 0) && $mode != 3){?>
				<tr>
					<th>작성자</th>
					<td class="left"><?=$mst['reg_name'];?></td>
					<th>작성일자</th>
					<td class="left last" colspan="3"><?=str_replace('-','.',$mst['reg_date']).' '.$mst['reg_time'];?></td>
				</tr><?
			}else if($mst['reg_name'] == ''){ ?>
				<tr>
					<th>작성자</th>
					<td class="last" colspan="3"><input name="reg_name" type="text" value="<?=$_SESSION['userName'];?>" style="width:15%;" onFocus="this.select();" tag="이름을 입력하여 주십시오."></td>
				</tr><?
			}

		?>
		<tr>
			<th>제목</th>
			<?
				if ($mode == 1){?>
					<td class="last" colspan="3"><input name="subject" type="text" value="<?=stripslashes($mst['subject']);?>" style="width:100%;" onFocus="this.select();" tag="제목을 입력하여 주십시오."></td><?
				}else{?>
					<td class="left last" colspan="3"><?=stripslashes($mst['subject']);?></td><?
				}
			?>
		</tr>
		<?
			if ($_SESSION['userLevel'] == 'A' && $mode == 1){?>
				<tr>
					<th>&nbsp;</th>
					<td class="left last" colspan="3"><input name="notice_yn" type="checkbox" class="checkbox" value="Y" <? if($mst['notice'] == 'Y'){?>checked<?} ?>>공지</td>
				</tr><?
			}else{?>
				<input type="hidden" name="notice_yn" value="N"><?
			}
		?>
	</tbody>
</table>

<table class="my_table" style="width:100%; border-bottom:1px solid #a6c0f3;">
	<colgroup>
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
		<?
			if ($mode == 1 || $mode == 3){
				if ($mode == 3){
					$content = $mst_re['content'];
					$content_title = '답변내용';?>
					<tr>
						<th>본문내용</th>
						<td class="top last" style="padding-top:5px;">
							<div style="overflow-y:hidden; overflow-x:scroll; width:100%; padding-left:5px;"><?=$mst['content'];?></div>
						</td>
					</tr>
					<tr>
						<th>제목</th>
						<td class="last"><input name="subject" type="text" value="<?=stripslashes($mst_re['subject']);?>" style="width:100%;" onFocus="this.select();" tag="제목을 입력하여 주십시오."></td>
					</tr><?
				}else{
					$content = $mst['content'];
					$content_title = '내용';
				}?>
				<th><?=$content_title;?></th>
				<td class="last" style="height:300px; padding:5px 5px 3px 5px;">
					<link href="../editor/css/default.css" rel="stylesheet" type="text/css" />
					<script type="text/javascript" src="../editor/js/HuskyEZCreator.js" charset="utf-8"></script>

					<textarea name="ir1" id="ir1" style="width:100%; height:100%;" tag="내용을 입력하여 주십시오."><?=$content;?></textarea>
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

							form.action = 'board_reg_ok.php';
							form.submit();
						}
					</script>
				</td><?
			}else{?>
				<td class="top last" colspan="2">
					<!--div style="height:300px; overflow-y:hidden; overflow-x:scroll; width:100%; padding-left:5px;"><?=$mst['content'];?></div-->
					<div style="height:300px; overflow:scroll; width:100%; padding-left:5px;"><?=$mst['content'];?></div>
				</td><?
			}?>
		</tr><?

		if ($board_type == 'pds'){
			$sql = 'SELECT	board_seq
					,		file_name
					,		file_size
					,		file_type
					FROM	tbl_board_file
					WHERE	board_center= \''.$code.'\'
					AND		board_type	= \''.$board_type.'\'
					AND		board_id	= \''.$board_id.'\'';

			$file = $conn->_fetch_array($sql,'board_seq');

			if ($mode == 1){?>
				<tr>
					<th rowspan="3">첨부파일</th>
					<td class="last"><input name="file1" type="file" style="width:300px;"></td>
				</tr>
				<tr>
					<td class="last"><input name="file2" type="file" style="width:300px;"></td>
				</tr>
				<tr>
					<td class="last"><input name="file3" type="file" style="width:300px;"></td>
				</tr><?
			}else{?>
				<tr>
					<th rowspan="3">첨부파일</th>
					<td class="left last"><a href="./download.php?type=<?=$board_type;?>&id=<?=$board_id;?>&seq=1"><?=$file['1']['file_name'].($file['1']['file_name'] ? '('.$myF->getFileSize($file['1']['file_size']).')' : '');?></a></td>
				</tr>
				<tr>
					<td class="left last"><a href="./download.php?type=<?=$board_type;?>&id=<?=$board_id;?>&seq=2"><?=$file['2']['file_name'].($file['2']['file_name'] ? '('.$myF->getFileSize($file['2']['file_size']).')' : '');?></a></td>
				</tr>
				<tr>
					<td class="left last"><a href="./download.php?type=<?=$board_type;?>&id=<?=$board_id;?>&seq=3"><?=$file['3']['file_name'].($file['3']['file_name'] ? '('.$myF->getFileSize($file['3']['file_size']).')' : '');?></a></td>
				</tr><?
			}
		}?>
	</tbody>
</table>

<?
	if ($mode == 2){?>
		<table class="my_table" style="width:100%; border-bottom:1px solid #a6c0f3;">
			<colgroup>
				<col width="100px">
				<col>
				<col width="99px">
			</colgroup>
			<tbody>
				<?
					$sql = "select board_seq, reg_id, reg_name, reg_date, reg_time, content
							  from tbl_board_reply
							 where board_center = '$code'
							   and board_type   = '$board_type'
							   and board_id     = '$board_id'
							 order by board_seq desc";

					$conn->query($sql);
					$conn->fetch();
					$row_count = $conn->row_count();

					for($i=0; $i<$row_count; $i++){
						$row = $conn->select_row($i);?>
						<tr>
							<td class="left bottom last" style="line-height:26px; border-bottom:1px dotted #ccc;" colspan="3">
								<div style="width:auto; float:left; line-height:26px; padding-top:3px;"><img src="../image/reply_t_btn.gif"></div>
								<div style="width:auto; float:left; line-height:26px; padding-left:5px;"><?=$row['reg_name'];?></div>
								<div style="width:auto; float:left; line-height:26px; padding-left:5px;">(<?=str_replace('-','.',$row['reg_date']).' '.$row['reg_time'];?>)</div>
								<div style="width:auto; float:right; line-height:26px; padding-top:2px;">
								<?
									if ($_SESSION['userCode'] == $row['reg_id'] || $_SESSION['userLevel'] == 'A'){
										if ($_SESSION['userCode'] == $row['reg_id']){?>
											<span class="btn_pack m"><button id="reply_modify_<?=$row['board_seq'];?>" style="display:;" type="button" onclick="modify_reply(<?=$row['board_seq'];?>,1);">수정</button></span><?
										}?>
										<span class="btn_pack m"><button id="reply_delete_<?=$row['board_seq'];?>" style="display:;" type="button" onclick="delete_reply(<?=$row['board_seq'];?>);">삭제</button></span><?
									}
								?>
								</div>
							</td>
						</tr>
						<tr>
							<td class="left last" style="line-height:26px;" colspan="3">
								<div id="reply_text_<?=$row['board_seq'];?>" style="display:;"><?=nl2br(StripSlashes($row['content']));?></div>
								<div id="reply_cont_<?=$row['board_seq'];?>" style="display:none;">
									<table class="my_table" style="width:100%; border-bottom:none;">
										<colgroup>
											<col>
											<col width="99px">
										</colgroup>
										<tbody>
											<tr>
												<td class="left bottom">
													<textarea name="reply_<?=$row['board_seq'];?>" style="width:100%; height:100%; margin:0; border:none;"><?=$row['content'];?></textarea>
												</td>
												<td class="center bottom last" style="padding-top:10px; padding-bottom:10px;"><a href="#" onclick="reg_reply(<?=$row['board_seq'];?>);"><img src="../image/btn_comment.gif"></a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</td>
						</tr><?
					}

					$conn->row_free();

					unset($row);
				?>
			</tbody>
		</table>

		<table class="my_table" style="width:100%; border-bottom:1px solid #a6c0f3;">
			<colgroup>
				<col>
				<col width="99px">
			</colgroup>
			<tbody>
				<tr>
					<td class="left">
						<textarea name="reply" style="width:100%; height:100%; margin:0; border:none;"></textarea>
					</td>
					<td class="center last" style="padding-top:10px; padding-bottom:10px;"><a href="#" onclick="reg_reply(0);"><img src="../image/btn_comment.gif"></a></td>
				</tr>
			</tbody>
		</table>

		<table class="my_table" style="width:100%; border-bottom:none;">
			<colgroup>
				<col width="50px">
				<col width="500px">
				<col width="150px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">No</th>
					<th class="head">제목</th>
					<th class="head">작성일시</th>
					<th class="head last">비고</th>
				</tr>
			</thead>
			<tbody>
			<?
				$sql = "select board_id
						,      subject
						,      content
						,      reg_date
						,      reg_time
						,      reg_id
						  from tbl_board
						 where board_center = '$code'
						   and board_type   = '$board_type'
						   and reply_id     = '$board_id'
						 order by board_id desc";

				$conn->query($sql);
				$conn->fetch();
				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);?>
					<tr>
						<td class="center"><?=$pageCount + ($i + 1);?></td>
						<td class="left"><a href="#" onclick="answer_view(<?=$i;?>); return false;">[답변]<?=stripslashes($row['subject']);?></a></td>
						<td class="center"><?=$row['reg_date'].' '.$row['reg_time'];?></td>
						<td class="left last">
						<?
							if ($_SESSION['userCode'] == $row['reg_id'] || $_SESSION['userLevel'] == 'A'){
								if ($_SESSION['userCode'] == $row['reg_id']){?>
									<span class="btn_pack m"><button type="button" onclick="answer(<?=$row['board_id'];?>);">수정</button></span><?
								}?>
								<span class="btn_pack m"><button type="button" onclick="answer_delete(<?=$row['board_id'];?>);">삭제</button></span><?
							}
						?>
						</td>
					</tr>
					<tr id="answer_list[]" style="display:none;">
						<td class="top last" style="height:300px;" colspan="4"><div style="overflow:scroll; width:100%; height:100%; padding-left:5px;"><?=$row['content'];?></div></td>
					</tr><?
				}

				$conn->row_free();

				unset($row);
			?>
			</tbody>
			<tbody>
				<tr>
					<td class="left bottom last" colspan="4">
					<?
						if ($row_count > 0){?>
							<div><?=$myF->message($row_count, 'N');?></div><?
						}else{?>
							<div style="text-align:center;">::<?=$myF->message('nodata', 'N');?>::</div><?
						}
					?>
					</td>
				</tr>
			</tbody>
		</table><?
	}

	include_once("board_var.php");
?>
<input type="hidden" name="filepath" value="upload">
</form>
<?
	unset($mst);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>