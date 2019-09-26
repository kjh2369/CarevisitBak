<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_GET['orgNo'];
	$contDt	= str_replace('-','',$_POST['txtContDt']);
	$docGbn = '1';

	$docFile['1'] = $_FILES['docFile1'];
	$docFile['2'] = $_FILES['docFile2'];
	$docFile['3'] = $_FILES['docFile3'];

	$path = '../doc/center/'.$orgNo;
	if (!is_dir($path)) mkdir($path);

	foreach($docFile as $docType => $F){
		if ($F['tmp_name']){
			$exp = Explode('.',$F['name']);
			$exp = $exp[SizeOf($exp)-1];

			if (move_uploaded_file($F['tmp_name'], $path.'/'.$docType.'.'.$exp)){
				$sql = 'SELECT	COUNT(*)
						FROM	cv_doc
						WHERE	org_no	= \''.$orgNo.'\'
						AND		cont_dt	= \''.$contDt.'\'
						AND		doc_type= \''.$docType.'\'';
				$cnt = $conn->get_data($sql);

				/*
				if (!$contDt && $cnt > 1){
					$cnt = 0;
					$sql = 'SELECT	IFNULL(MAX(cont_dt),0)+1
							FROM	cv_doc
							WHERE	org_no	= \''.$orgNo.'\'
							AND		LEFT(cont_dt,1) = \'0\'';
				}
				*/

				if ($cnt > 0){
					$sql = 'UPDATE	cv_doc
							SET		file_path = \''.$path.'/'.$docType.'.'.$exp.'\'
							,		update_id = \''.$_SESSION['userCode'].'\'
							,		update_dt = NOW()
							WHERE	org_no	= \''.$orgNo.'\'
							AND		cont_dt	= \''.$contDt.'\'
							AND		doc_type= \''.$docType.'\'';
				}else{
					$sql = 'INSERT INTO cv_doc (org_no,cont_dt,doc_type,doc_gbn,file_path,insert_id,insert_dt) VALUES (
							 \''.$orgNo.'\'
							,\''.$contDt.'\'
							,\''.$docType.'\'
							,\''.$docGbn.'\'
							,\''.$path.'/'.$docType.'.'.$exp.'\'
							,\''.$_SESSION['userCode'].'\'
							,NOW()
							)';
				}

				$query[] = $sql;
			}
		}
	}

	if (is_array($query)){
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
	}

	include_once('../inc/_db_close.php');
?>