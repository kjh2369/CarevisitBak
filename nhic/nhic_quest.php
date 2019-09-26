<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$date = $_POST['date'];
	$seq  = $_POST['seq'];
	$file = $_POST['file']; //템프파일명
	$f_name = $_POST['f_name'];
	$f_type = $_POST['f_type'];
	$f_size = $_POST['f_size'];
	$f_old  = $_POST['f_old'];
?>

<script language='javascript'>
<!--

function set_ok(){
	f.action = '../nhic/nhic_upload.php';
	f.submit();
}

window.onload = function(){
	window.resizeTo(350, 300);
	window.moveTo((screen.width - 350) / 2, (screen.height - 300) / 2);
	self.focus();
}

-->
</script>

<div class='title title_border'>작업선택</div>

<p style='padding:20px; font-weight:bold; text-align:justify;'>
	기존에 같은 파일이 등록되었습니다.
</p>

<form name='f' method='post' target='_self'>
	<div style='padding-left:30px;'>
		<input name='gbn' type='radio' value='1' class='radio' checked>기존의 파일을 수정하지 않습니다.<br>
		<input name='gbn' type='radio' value='2' class='radio'>기존의 파일을 현재의 파일로 변경합니다.<br>
		<input name='gbn' type='radio' value='3' class='radio'>기존의 파일과 상관없이 추가로 등록합니다.
	</div>
	<input name='code' type='hidden' value='<?=$code;?>'>
	<input name='date' type='hidden' value='<?=$date;?>'>
	<input name='seq'  type='hidden' value='<?=$seq;?>'>
	<input name='file' type='hidden' value='<?=$file;?>'>
	<input name='f_name' type='hidden' value='<?=$f_name;?>'>
	<input name='f_type' type='hidden' value='<?=$f_type;?>'>
	<input name='f_size' type='hidden' value='<?=$f_size;?>'>
	<input name='f_old'  type='hidden' value='<?=$f_old;?>'>
</form>

<div style='padding-top:20px; text-align:center;'>
	<span class='btn_pack m'><button type='button' onclick='set_ok();'>확인</button></span>
	<span class='btn_pack m'><button type='button' onclick='self.close();'>닫기</button></span>
</div>

<?
	/*********************************************************

		템프파일 삭제

	*********************************************************/
	#if (is_file($file)) @unlink($file);

	include_once('../inc/_footer.php');
?>