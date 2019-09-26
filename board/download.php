<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$type	= $_POST['type'];
	$cd		= $_POST['brdCd'];
	$id		= $_POST['brdId'];
	$file	= $_POST['filId'];

	if (!$orgNo) $orgNo = $_SESSION['userCenterCode'];

	$sql = 'SELECT	file_name
			FROM	board_file
			WHERE	org_no	= \''.$orgNo.'\'
			AND		brd_type= \''.$type.'\'
			AND		dom_id	= \''.$gDomainID.'\'
			AND		brd_cd	= \''.$cd.'\'
			AND		brd_id	= \''.$id.'\'
			AND		file_id	= \''.$file.'\'';

	$fileName = $conn->get_data($sql);
	$downFile = './files/'.$type.'/'.$orgNo.'/'.$gDomainID.'_'.$cd.'_'.$id.'_'.$file;

	if (file_exists($downFile) && is_file($downFile)){
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($myF->euckr($fileName)));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($downFile));

		ob_clean();
		flush();
		readfile($downFile);
	}else{
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		die("파일이 존재하지 않습니다");
	}


	include_once('../inc/_db_close.php');
?>