<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	
	$orgNo = $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$date	= $_POST['date'];
	$time	= $_POST['time'];
	$suga	= $_POST['suga'];
	$res	= $_POST['resource'];
	$jumin	= $ed->de($_POST['jumin']);
	
	//print_r($_POST);

	$sql = 'SELECT	file_path
			FROM	care_works_log
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$SR.'\'
			AND		date		= \''.$date.'\'
			AND		suga_cd		= \''.$suga.'\'
			AND		resource_cd	= \''.$res.'\'
			AND		jumin		= \''.$jumin.'\'';
			
	$saveFile = $conn->get_data($sql);

	$tmpFile = explode('/',$saveFile);
	$file = $tmpFile[6];
	
	$mode = 'IMG';
	
	if (file_exists($saveFile)){
		header("Content-Type: doesn/matter");
		header("Content-length: ".filesize("$saveFile"));
		header("Content-Disposition: attachment; filename=$file");
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");

		if (is_file("$saveFile")){
			$fp = fopen("$saveFile", "r");

			if (!fpassthru($fp)){
				fclose($fp);
			}
		}

		//echo 1;
	}else{
		//$mode = 9;
	}
	
	echo $mode;


	$conn->close();
?>