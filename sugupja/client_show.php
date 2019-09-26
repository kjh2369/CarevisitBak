<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code   = $_POST['code'];
	$jumin  = $ed->de($_POST['jumin']);

	$view_type = 'read'; //뷰
?>
	<script language='javascript' src='./client.js'></script>
	<div class="title title_border">수급자 정보</div>
	<div id="div_body"><?
		include_once('./client_reg_info.php');
		include('./client_menu.php');?>
	</div>
	<div style="height:30px; text-align:center;">
		<span class="btn_pack m" style="margin-top:5px;"><button type="button" onclick="window.self.close();">확인</button></span>
	</div>
	<input id="code" name="code" type="hidden" value="<?=$code;?>">
	<input id="jumin" name="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
	<input id="viewType" name="viewType" type="hidden" value="<?=$view_type;?>">
<?
	include_once("../inc/_footer.php");
?>
<script language='javascript'>

window.self.focus();
</script>