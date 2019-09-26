<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	$board_center	= $_REQUEST['board_center'];
	$board_type		= $_REQUEST['board_type'];
	$board_id		= $_REQUEST['board_id'];
	$title			= $myF->board_name($board_type);
	$page			= $_REQUEST['page'];
	$mode			= $_REQUEST['mode'];

	if ($mode == 'edit') $action = 'comm_save.php';
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
window.onload = function(){
	__init_form(document.f);
}
//-->
</script>
<form name="f" method="post" action="<?=$action;?>">
<div class="title"><?=$title;?></div>
<table class="my_table my_border">
	<colgroup>
		<col width="100px">
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
		?>
		<tr>
			<th>내용</th>
			<?
				if ($mode == 'edit'){
				?>	<td style="height:300px; padding:2px 5px 5px 5px;">
						<!--textarea name="content" style="width:100%; height:100%;" tag="내용을 입력하여 주십시오."><?=stripslashes($notice['content']);?></textarea-->
						<link href="../editor/css/default.css" rel="stylesheet" type="text/css" />
						<script type="text/javascript" src="../editor/js/HuskyEZCreator.js" charset="utf-8"></script>

						<textarea name="content" id="content" style="width:100%; height:100%;" tag="내용을 입력하여 주십시오."><?= $notice['content'];?></textarea>
						<textarea id="back_content" name="back_content" style="display:none;"></textarea>

						<script>
							var form = document.f;

							var oEditors = [];

							nhn.husky.EZCreator.createInIFrame(oEditors, "content", "../editor/SEditorSkin.html", "createSEditorInIFrame", null, true);

							function insertIMG(fname){
								var filepath = form.filepath.value;
								var sHTML = "<img src='" + filepath + "/" + fname + "' style='cursor:hand;' border='0'>";
								oEditors.getById["content"].exec("PASTE_HTML", [sHTML]);
								}

							function pasteHTMLDemo(){
								sHTML = "<span style='color:#FF0000'>이미지 등도 이렇게 삽입하면 됩니다.</span>";
								oEditors.getById["content"].exec("PASTE_HTML", [sHTML]);
							}

							function showHTML(){
								alert(oEditors.getById["content"].getIR());
							}

							function onSubmit(){
								oEditors.getById["content"].exec("UPDATE_IR_FIELD", []);

								form.back_content.value = document.getElementById("content").value;

								if(form.back_content.value == ""){
									alert("\'내용\'을 입력해 주세요");
									return;
								}

								form.submit();
							}
						</script>
					</td><?
				}else{
				?>	<td style="text-align:left; vertical-align:top; padding:5px;"></td><?
				}
			?>
			</td>
			<td class="other">&nbsp;</td>
		</tr>
	</tbody>
</table>
<div style="width:800px; text-align:right; padding:5px;">
<?
	if ($mode == 'edit'){ ?>
		<span class="btn_pack m"><button type="button" onclick="_save_commnuity();">저장</button></span>
		<span class="btn_pack m"><button type="button" onclick="">삭제</button></span> <?
	}
?>	<span class="btn_pack m"><button type="button" onclick="_list_commnuity('<?=$board_center;?>','<?=$board_type;?>','<?=$page;?>');">리스트</button></span>
</div>
<input name="board_center"	type="hidden" value="<?=$board_center;?>">
<input name="board_type"	type="hidden" value="<?=$board_type;?>">
<input name="board_id"		type="hidden" value="<?=$board_id;?>">
<input name="page"			type="hidden" value="<?=$page;?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>