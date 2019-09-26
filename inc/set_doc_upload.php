<?
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_db_open.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_http_uri.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$contDt = $_GET['contDt'];
	$docType = $_GET['docType'];
	$FILE = $_FILES['docFile'];

	$path = '../doc/center/'.$orgNo;
	if (!is_dir($path)) mkdir($path);

	$exp = Explode('.',$FILE['name']);
	$exp = $exp[SizeOf($exp)-1];

	if (move_uploaded_file($FILE['tmp_name'], $path.'/'.$contDt.'_'.$docType.'.'.$exp)){
		$sql = 'UPDATE	cv_doc
				SET		file_path = \''.$path.'/'.$contDt.'_'.$docType.'.'.$exp.'\'
				,		update_id = \''.$_SESSION['userCode'].'\'
				,		update_dt = NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		cont_dt	= \''.$contDt.'\'
				AND		doc_type= \''.$docType.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
			 exit;
		}

		$conn->commit();
	}else{
		echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
	}

	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_db_close.php');
?>