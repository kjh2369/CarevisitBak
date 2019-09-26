<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_POST['orgNo'];
	$contDt	= $_POST['contDt'];
	$stopDt	= str_replace('-','',$_POST['stopDt']);
	$docType= Explode(',',$_POST['type']);
	$docGbn	= $_POST['gbn'];


	foreach($docType as $tmpIdx => $type){
		$sql = 'SELECT	COUNT(*)
				FROM	cv_doc
				WHERE	org_no	= \''.$orgNo.'\'
				AND		cont_dt	= \''.$contDt.'\'
				AND		doc_type= \''.$type.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	cv_doc
					SET		file_path	= NULL
					,		doc_gbn		= \''.$docGbn.'\'
					,		stop_dt		= \''.$stopDt.'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		cont_dt	= \''.$contDt.'\'
					AND		doc_type= \''.$type.'\'';
		}else{
			$sql = 'INSERT INTO cv_doc (org_no,cont_dt,doc_type,doc_gbn,stop_dt,insert_id,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$contDt.'\'
					,\''.$type.'\'
					,\''.$docGbn.'\'
					,\''.$stopDt.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}

		$query[] = $sql;
	}

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
			 exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>