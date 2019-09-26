<?
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_db_open.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_function.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_myFun.php");

	$orgNo = $_SESSION['userCenterCode'];
	$dir = $_POST['dir'];

	//$contFile = checkFile('./contract', $orgNo);
	//$bizFile = checkFile('./registration', $orgNo);

	$file = checkFile($dir, $orgNo);

	function checkFile($dir, $search){
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) != false) {
					if ($file != "." && $file != "..") {
						if (filetype($dir ."/". $file) == "file") {
							$pattern = '/'.$search.'/';
							if (preg_match($pattern,$file)) {
								//$orgFile = $dir."/".$file;
								$orgFile = $file;
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

	echo $file;

	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_db_close.php");
?>