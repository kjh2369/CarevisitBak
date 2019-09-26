<?
	include_once("../inc/_header.php");
	//include_once("../inc/_http_uri.php");
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
<form name="f" method="post">
<div class="title_div">공지사항 등록</div>
<table class="my_table my_border">
	<colgroup>
		<col width="150px">
		<col width="650px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>제목</th>
			<td>
			<?
				if ($mode == 'edit'){
				?>	<input name="subject" type="text" value="<?=stripslashes($notice['subject']);?>" style="width:100%;" onFocus="this.select();" tag="제목을 입력하여 주십시오."><?
				}else{
				?>	<div><?=stripslashes($notice['subject']);?></div><?
				}
			?>
			</td>
			<td class="last_col">&nbsp;</td>
		</tr>
		<?
			if ($notice['reg_dt']){
			?>	<tr>
					<th>작성일시</th>
					<td class="left"><?=date('Y.m.d H:i:s', $notice['reg_dt']);?></td>
					<td class="last_col">&nbsp;</td>
				</tr><?
			}
		?>
		<tr>
			<th>내용</th>
			<td style="height:300px; padding:2px 5px 5px 5px;">
			<?
				if ($mode == 'edit'){
				?>	<textarea name="content" style="width:100%; height:100%;" tag="내용을 입력하여 주십시오."><?=stripslashes($notice['content']);?></textarea><?
				}else{
				?>	<div><?=stripslashes($notice['content']);?></div><?
				}
			?>
			</td>
			<td class="last_col">&nbsp;</td>
		</tr>
	</tbody>
</table>
<div style="width:100%; text-align:left; margin-top:10px;"><div style="width:800px; text-align:right;">
	<span class="btn_pack m"><button type="button" onclick="_save_notice();">저장</button></span>
	<span class="btn_pack m"><button type="button" onclick="_delete_notice();">삭제</button></span>
	<span class="btn_pack m"><button type="button" onclick="_list_notice();">리스트</button></span>
</div></div>
<input name="id"	type="hidden" value="<?=$id;?>">
<input name="page"	type="hidden" value="<?=$page;?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>