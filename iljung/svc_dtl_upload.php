<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$appNo	= $_POST['appNo'];
	$yymm	= $_GET['yymm'];

	//@unlink('./files/34273000017/201411/303_1.png');
	//@unlink('./files/34273000017/201411/303_2.png');
	//exit;

	$path = './files/'.$orgNo;
	if (!is_dir($path)) mkdir($path);

	$path = './files/'.$orgNo.'/'.$yymm;
	if (!is_dir($path)) mkdir($path);

	for($i=1; $i<=5; $i++){
		$f = $_FILES['file_'.$i];

		if ($f['tmp_name']){
			$tmpInfo = pathinfo($f['name']);
			$exp = strtolower($tmpInfo['extension']);

			if ($exp == 'jpg' || $exp == 'png' || $exp == 'gif' || $exp == 'bmp'){
				$file = $path.'/F_'.$appNo.'_'.$i.'.'.$exp;
				@move_uploaded_file($f['tmp_name'], $file);
			}
		}
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>