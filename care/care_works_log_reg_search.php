<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$date	= $_POST['date'];
	$time	= $_POST['time'];
	$seq	= $_POST['seq'];
	$jumin	= $ed->de($_POST['jumin']);
	$suga	= $_POST['suga'];
	$res	= $_POST['resource'];
	$mem	= $ed->de($_POST['mem']);

	$sql = 'SELECT	contents
			,		pic_nm
			,		file_path
			FROM	care_works_log
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$SR.'\'
			AND		date		= \''.$date.'\'
			AND		jumin		= \''.$jumin.'\'
			AND		suga_cd		= \''.$suga.'\'
			AND		resource_cd	= \''.$res.'\'
			AND		mem_cd		= \''.$mem.'\'';

	$row = $conn->get_array($sql);

	if ($row){
		$data = 'contents='.StripSlashes(str_replace('&','@', $row['contents']))
			  . '&picNm='.$row['pic_nm']
			  . '&path='.$row['file_path'];

		if ($row['file_path']){
			$size = @GetImageSize($row['file_path']);
			$data.= '&width='.$size[0].'&height='.$size[1];
		}

		$data .= '&origin=NEW';
	}else{
		$sql = 'SELECT	content
				,		picture
				,		file
				FROM	care_result
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		date	= \''.$date.'\'
				AND		time	= \''.$time.'\'
				AND		seq		= \''.$seq.'\'
				AND		del_flag= \'N\'
				ORDER	BY no
				LIMIT	1';

		$row = $conn->get_array($sql);

		if ($row){
			$data = 'contents='.StripSlashes(str_replace('&','@', $row['content']))
				  . '&picNm='.$row['picture']
				  . '&path=../care/pic/'.$row['picture'];

			if ($row['picture']){
				$size = @GetImageSize('../care/pic/'.$row['picture']);
				$data.= '&width='.$size[0].'&height='.$size[1];
			}

			$data .= '&origin=OLD';
		}
	}

	//if ($debug) echo $sql;

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>