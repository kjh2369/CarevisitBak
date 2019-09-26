<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	$id		= $_REQUEST['id'];
	$page	= $_REQUEST['page'];
	$mode	= $_REQUEST['mode'];

	if (is_numeric($id)){
		$sql = "select subject, content, reg_dt
				  from tbl_goodeos_notice
				 where id = '$id'";
		$notice = $conn->get_array($sql);
	}
?>
<script type="text/javascript" src="../js/goodeos.js"></script>
<script language='javascript'>
<!--
window.onload = function(){
	__init_form(document.f);
}
//-->
</script>
<form name="f" method="post">
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
		<col width="150px">
		<col width="650px">
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
						<link rel="stylesheet" type="text/css" href="../editor/stylesheets/xq_ui.css" />
						<link rel="stylesheet" type="text/css" href="../editor/stylesheets/xq_custom.css" />

						<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
						<script type="text/javascript" src="../editor/javascripts/Full_merged.js"></script>
						<script type="text/javascript" src="../editor/javascripts/plugin/swfupload/AC_OETags.js"></script>
						<script type="text/javascript" src="../editor/javascripts/plugin/swfupload/swfupload.js"></script>
						<script type="text/javascript" src="../editor/javascripts/plugin/swfupload/swfupload.queue.js"></script>
						<script type="text/javascript" src="../editor/javascripts/plugin/FileUploadPlugin.js"></script>
						<script type="text/javascript" src="../editor/javascripts/jquery.xquared.0.1.2.js"></script>
						<script>
							$(function(){
								$('#content').editable({'baseUrl':'../editor/'});
							});
						</script>
						<textarea id="content" name="content" style="width:100%; height:100%; display:none;" tag="내용을 입력하여 주십시오."><?= $notice['content'];?></textarea>
					</td><?
				}else{
				?>	<td style="text-align:left; vertical-align:top; padding:5px; line-height:normal; font-size:9pt;">
						<link rel="stylesheet" type="text/css" href="../editor/stylesheets/xq_contents.css" />
						<div class="xed"><?=$notice['content'];?></div>
					</td><?
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
		<span class="btn_pack m"><button type="button" onclick="_save_notice();">저장</button></span>
		<span class="btn_pack m"><button type="button" onclick="_delete_notice();">삭제</button></span> <?
	}
?>	<span class="btn_pack m"><button type="button" onclick="_list_notice();">리스트</button></span>
</div>
<input name="id"	type="hidden" value="<?=$id;?>">
<input name="page"	type="hidden" value="<?=$page;?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>