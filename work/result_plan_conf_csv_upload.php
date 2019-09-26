<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$file = $_POST['file'];

	###################################################
	# CSV 파일업로드
		$f = $_FILES['csv'];

		if ($f['tmp_name'] != ''){
			$file_nm = $_SESSION['userCenterCode'];
			$file    = '../file/csv/'.$file_nm;

			if (is_file($file)){
				@unlink($file);
			}

			if (move_uploaded_file($f['tmp_name'], $file)){
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
	#
	###################################################

	echo '<form name=\'f\' action=\'result_plan_conf_csv_save.php\' method=\'post\'>';
	echo '<input name=\'code\' type=\'hidden\' value=\''.$code.'\'>';
	echo '<input name=\'file\' type=\'hidden\' value=\''.$file.'\'>';
	echo '</form>';

	//@unlink($file);

	include_once('../inc/_footer.php');
?>
<script language='javascript'>

window.onload = function(){
	var f = document.f;

	f.submit();
}

</script>