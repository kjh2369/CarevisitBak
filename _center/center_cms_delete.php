<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$modYn	= $_POST['modYn'];


	$conn->begin();


	//적용 이력 삭제
	$sql = 'SELECT	org_no, cms_no, cms_dt, seq
			FROM	cv_cms_reg
			WHERE	LEFT(cms_dt,6) = \''.$yymm.'\'
			AND		del_flag = \'N\'';

	if ($modYn != 'Y') $sql .= ' AND modify_yn = \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$sql = 'UPDATE	cv_cms_link
				SET		del_flag	= \'Y\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$row['org_no'].'\'
				AND		cms_no	= \''.$row['cms_no'].'\'
				AND		cms_dt	= \''.$row['cms_dt'].'\'
				AND		cms_seq	= \''.$row['seq'].'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '1.데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
			 exit;
		}
	}

	$conn->row_free();


	$sql = 'UPDATE	cv_cms_reg
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	LEFT(cms_dt,6) = \''.$yymm.'\'
			AND		del_flag = \'N\'';

	if ($modYn != 'Y') $sql .= ' AND modify_yn = \'N\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '2.데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}


	$conn->commit();


	include_once('../inc/_db_close.php');
?>