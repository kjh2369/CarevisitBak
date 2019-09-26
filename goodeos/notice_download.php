<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	$type = $_POST['type'];

	if ($type == 'NOTICE'){
		//공지사항
		$mode = $_POST['mode'];
		$id = $_POST['id'];
		$seq = $_POST['seq'];

		if ($mode == 'ATTACH_DELETE'){
			//첨부파일 삭제
			$sql = 'DELETE
					FROM	attach_file
					WHERE	id = \'NOTICE_'.$gDomainID.'\'
					AND		attach_key = \''.$id.'\'
					AND		seq = \''.$seq.'\'';

			$conn->begin();

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();

			$sql = 'SELECT	file_path
					FROM	attach_file
					WHERE	id = \'NOTICE_'.$gDomainID.'\'
					AND		attach_key = \''.$id.'\'
					AND		seq = \''.$seq.'\'';

			$file = $conn->get_data($sql);

			@Unlink('./files/'.$file);

			echo 1;

		}else if ($mode == 'ATTACH_DOWNLOAD'){
			$sql = 'SELECT	file_name
					,		file_path
					FROM	attach_file
					WHERE	id = \'NOTICE_'.$gDomainID.'\'
					AND		attach_key = \''.$id.'\'
					AND		seq = \''.$seq.'\'';

			$row = $conn->get_data($sql);

			header("Content-Type: application/octet-stream; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$row['file_name']);
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".(string)(filesize('./files/'.$row['file_path'])));
			header("Cache-Control: cache, must-revalidate");
			header("Pragma: no-cache");
			header("Expires: 0");

			Unset($row);

		}else(
		}
	}

	include_once("../inc/_db_close.php");
?>