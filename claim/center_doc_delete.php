<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$contDt	= $_POST['contDt'];
	$gbn	= $_POST['gbn'];

	$sql = 'SELECT	file_path
			FROM	cv_doc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cont_dt	= \''.$contDt.'\'
			AND		doc_type= \''.$gbn.'\'';
	$filePath = $conn->get_data($sql);

	$sql = 'DELETE
			FROM	cv_doc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cont_dt	= \''.$contDt.'\'
			AND		doc_type= \''.$gbn.'\'	';
	
	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();

		 echo $conn->error_msg.chr(13).$conn->error_query;
		 exit;
	}

	$conn->commit();

	if (is_file($filePath)) @unlink($filePath);

	include_once('../inc/_db_close.php');
?>