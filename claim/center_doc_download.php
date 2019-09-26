<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$contDt	= str_replace('-','', $_POST['txtContDt']);
	$gbn	= $_POST['docType'];

	$sql = 'SELECT	file_path
			FROM	cv_doc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cont_dt	= \''.$contDt.'\'
			AND		doc_type= \''.$gbn.'\'';
	$file = $conn->get_data($sql);
	
	$tmpFile = explode('/', $file);
	$exp = explode('.', $tmpFile[4]);
	
	if($gbn == '1'){
		$filename = '케어비지트 계약서.'.$exp[1];
	}else if($gbn == '2'){
		$filename = '사업자등록증.'.$exp[1];
	}else if($gbn == '3'){
		$filename = 'CMS자동이체동의서.'.$exp[1];
	}

	if (file_exists($file)){
		header("Content-Type: doesn/matter");
		header("Content-length: ".filesize("$file"));
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");

		if (is_file("$file")){
			$fp = fopen("$file", "r");

			if (!fpassthru($fp)){
				fclose($fp);
			}
		}
	}else{
		/*
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
			  <script>
				alert('첨부파일이 존재하지 않습니다.');
				history.go(-1);
			  </script>";
		*/
	}

	Unset($row);


	include_once("../inc/_db_close.php");
?>