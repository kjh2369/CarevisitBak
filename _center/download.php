<?
	include_once('../inc/_db_open.php');
	//include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];

	ob_start();

	//파일이 있는 디렉토리
	$downfiledir = '../_center/doc/';

	//값 검증
	$downType = $_GET['downType'];

	if ($downType == '1'){
		$downfile = 'efnc_cms_agree.doc';
		$filename = $myF->euckr('효성FMS 자동이체 동의서(케어비지트).doc');
	}else if ($downType == '2'){
		$downfile = 'efnc_cms_agree.pdf';
		$filename = $myF->euckr('효성FMS 자동이체 동의서(케어비지트).pdf');
	}else if ($downType == '3'){
		$downfile = 'pop_4.jpg';
		$filename = $myF->euckr('케어비지트 사업자등록증.jpg');
	}else if ($downType == '4'){
		$downfile = 'pop_5.jpg';
		$filename = $myF->euckr('케어비지트 법인통장.jpg');
	}

	//파일 존재 유/무 체크
	if (file_exists($downfiledir.$downfile)){
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment;; filename=$filename ");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".(string)(filesize($downfiledir.$downfile )));
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: 0");

		$fp = fopen($downfiledir.$downfile , "rb"); //rb 읽기전용 바이러니 타입

		while (!feof($fp)){
			echo fread($fp, 100*1024); //echo는 전송을 뜻함.
		}

		fclose ($fp);
		flush(); //출력 버퍼비우기 함수..
	}

	include_once('../inc/_db_close.php');
?>