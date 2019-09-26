<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");

	$code	= $_SESSION['userCenterCode'];
	$type	= $_GET['type'];
	$id		= $_GET['id'];
	$seq	= $_GET['seq'];

	$sql = 'SELECT	file_name
			,		file_type
			,		file_size
			FROM	tbl_board_file
			WHERE	board_center= \''.$code.'\'
			AND		board_type	= \''.$type.'\'
			AND		board_id	= \''.$id.'\'
			AND		board_seq	= \''.$seq.'\'';

	$row = $conn->get_array($sql);

	$downFile = $myF->euckr($row['file_name']);
	$downType = $row['file_type'];
	$downSize = $row['file_size'];
	$fileName = $code.'_'.$type.'_'.$id.'_'.$seq;

	Unset($row);

	$path = $_SERVER['DOCUMENT_ROOT'].'/files/'.$fileName;

	if (file_exists($path) && is_file($path)){
		header("Content-Type: application/octet-stream");
		Header("Content-Disposition: attachment;; filename=$downFile");
		header("Content-Transfer-Encoding: binary");
		Header("Content-Length: ".(string)(filesize($path )));
		Header("Cache-Control: cache, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: 0");
		$fp = fopen($path , "rb"); //rb 읽기전용 바이러니 타입
		while ( !feof($fp) ) {
			echo fread($fp, 100*1024); //echo는 전송을 뜻함.
		}
		fclose ($fp);
		flush(); //출력 버퍼비우기 함수..

		/*
		header("Content-type: $downType");
		Header("Content-Length: ".filesize('./files'.$fileName));
		Header("Content-Disposition: attachment; filename=$downFile");
		Header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		*/
	}

	include_once("../inc/_db_close.php");
?>