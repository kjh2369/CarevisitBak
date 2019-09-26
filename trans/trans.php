<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$type  = $_GET['type'];

	switch($type){
		case 'other':
			$title = '기타이체';
			break;

		case 'acctno':
			$title = '이체계좌관리';
			break;

		case 'result':
			$title = '이체결과';
			break;

		default:
			include_once('../inc/_http_home.php');
			exit;
	}
?>
<div class="title title_border"><?=$title;?></div>
<form id="f" name="f">
<?
	include_once('./trans_'.$type.'.php');
?>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>