<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$deptCd	= $_POST['deptCd'];
	$jumin	= $ed->de64($_POST['jumin']);

	$sql = 'SELECT	dept_cd
			FROM	dept_attach_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'';
	$tmpCd = $conn->get_data($sql);

	if ($tmpCd){
		$sql = 'SELECT	dept_nm
				FROM	dept
				WHERE	org_no = \''.$orgNo.'\'
				AND		dept_cd= \''.$tmpCd.'\'';
		$tmpNm = $conn->get_data($sql);
		$conn->close();
		echo '선택하신 직원은 이미 '.$tmpNm.'부서에 소속되어 있습니다.';
		exit;
	}

	$sql = 'INSERT INTO dept_attach_list VALUES (\''.$orgNo.'\',\''.$deptCd.'\',\''.$jumin.'\',\'N\')';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>