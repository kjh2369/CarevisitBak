<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo $myF->header_script();

	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$day   = $_POST['day'];

	$f = $_FILES['csv'];

	if ($f['tmp_name'] != ''){
		$file_nm = mktime();
		$file    = '../upFile/'.$file_nm;
		if (move_uploaded_file($f['tmp_name'], '../upFile/'.$file_nm)){
			// 업로드 성공
			$upload = true;
		}else{
			// 업로드 실패
			$upload = false;
		}
	}else{
		// 업로드 실패
		$upload = false;
	}

	if (!$upload){
		echo '<script language="javascript">
				alert(\'파일업로드중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.\');
				history.back();
			  </script>';
		exit;
	}

	###################################################
	#
	# CSV 파일읽기
	if (($handle = fopen($file, "r")) !== FALSE) {
		while(true){
			$str = fgets($handle);

			if ($row_no > 2){
				$data = explode(',', $str);

				for($i=0; $i<sizeof($data); $i++){
					$row[$row_id][$i] = $myF->utf($data[$i]);
				}

				$row_id ++;
			}

			$row_no ++;

			if (feof($handle)) break;
		}
		fclose($handle);
	}else{
		echo $myF->message('업로드하신 파일을 찾을 수 없습니다. 잠시후 다시 시도하여 주십시오.', 'Y', 'Y');
		exit;
	}

	print_r($row);

	unset($row);

	@unlink($file);

	include_once('../inc/_db_close.php');
?>