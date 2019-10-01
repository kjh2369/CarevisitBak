<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$org_no = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$data = Explode('/', $_POST['para']);

	$sql = 'DELETE
			FROM	care_suga
			WHERE	org_no = \''.$org_no.'\'
			';
	$query[] = $sql;

	if (is_array($data)){
		foreach($data as $suga){
			$sql = 'REPLACE INTO care_suga (org_no, suga_sr, suga_cd, insert_id, insert_dt) VALUES (
					 \''.$org_no.'\'
					,\''.$SR.'\'
					,\''.$suga.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
			$query[] = $sql;
		}

		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
				 exit;
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>