<?
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_db_open.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_http_uri.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$path = $_POST['path'];
	$file = $_FILES['upfile'];

	#echo $_SESSION['userCenterCode'].chr(13);
	#echo 'PATH : '.$path.chr(13);

	$regFile = checkFile($path, $orgNo);

	if ($regFile) @unlink($regFile);

	function checkFile($dir, $search){
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) != false) {
					if ($file != "." && $file != "..") {
						if (filetype($dir ."/". $file) == "file") {
							$pattern = '/'.$search.'/';
							if (preg_match($pattern,$file)) {
								$orgFile = $dir."/".$file;
								break;
							}
						}
					}
				}
			}
			closedir($dh);
		}

		return $orgFile;
	}

	$exp = Explode('.',$file['name']);
	$exp = $exp[SizeOf($exp)-1];
	$movePath = './'.$path.'/'.$orgNo.'.'.$exp;

	if (move_uploaded_file($file['tmp_name'], $movePath)){
		echo 1;
	}else{
		echo 9;
	}

	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_db_close.php');
?>