<?
	include_once('../inc/_header.php');
?>
<div>
<?
	if(isset($_COOKIE['code']) or isset($_SESSION['user_id']) or isset($_SESSION['user_pass'])){
		include('../member/body.php');
	}else {
		include('../member/login.php');
	}

	include_once('../inc/_footer.php');
?>
</div>