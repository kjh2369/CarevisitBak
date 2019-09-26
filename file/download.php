<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo		= $_SESSION['userCenterCode'];
	$downFile	= $_REQUEST['file'];
	$filename	= $_REQUEST['name'];

	if (!$filename) $filename = $downFile;

	if (file_exists($downFile) && is_file($downFile)){
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($myF->euckr($filename)));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($downFile));

		ob_clean();
		flush();
		readfile($downFile);
	}else {
		die("파일이 존재하지 않습니다");
	}


	include_once('../inc/_db_close.php');
?>