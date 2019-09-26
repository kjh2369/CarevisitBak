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

			$row = $conn->get_array($sql);

			/*
			$tmpInfo = pathInfo($row['file_path']);
			$exp2 = StrToLower($tmpInfo['extension']);

			if (!$exp2){
				$tmpInfo = pathInfo($row['file_name']);
				$exp1 = StrToLower($tmpInfo['extension']);
				@rename('./files/'.$row['file_path'], './files/'.$row['file_path'].'.'.$exp1);
				$row['file_path'] = $row['file_path'].'.'.$exp1;
			}
			*/

			header("Content-Type: application/octet-stream; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$myF->euckr($row['file_name']));
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".(string)(filesize('./files/'.$row['file_path'])));
			header("Cache-Control: cache, must-revalidate");
			header("Pragma: no-cache");
			header("Expires: 0");

			$fp = fopen('./files/'.$row['file_path'], 'rb');
			while(!feof($fp)){
				echo fread($fp,100*1024);
			}
			fclose($fp);
			flush();

			Unset($row);
		}
	}

	include_once("../inc/_db_close.php");
?>