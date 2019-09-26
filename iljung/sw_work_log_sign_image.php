<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$key	= $_POST['key'];

	$file = '../sign/sign/manager/'.$orgNo.'/'.$key.'.jpg';
	$no = $myF->getMtime();

	if (is_file($file)){
		//echo '<img id="imgSignManager" src="'.$file.'?number='.$no.'" border="0">';
		echo $file;
	}else{
		echo '';
	}

	include_once('../inc/_db_close.php');
?>