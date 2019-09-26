<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['sr'];
	$year	= $_POST['year'];

	$sql = 'SELECT	plan_cd, plan_target, plan_target_gbn, plan_budget, plan_cnt, plan_cont, plan_effect, plan_eval
			FROM	care_year_plan
			WHERE	org_no		= \''.$orgNo.'\'
			AND		plan_year	= \''.($year - 1).'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($rowCnt < 1){
		echo '전월의 작성된 사업계획이 없습니다. 확인 후 다시 실행하여 주십시오.';
		exit;
	}

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i == 0){
			$sql = 'DELETE
					FROM	care_year_plan
					WHERE	org_no		= \''.$orgNo.'\'
					AND		plan_year	= \''.$year.'\'';
			$query[] = $sql;
		}

		$sql = 'INSERT INTO care_year_plan VALUES (
				 \''.$orgNo.'\'
				,\''.$year.'\'
				,\''.$SR.'\'
				,\''.$row['plan_cd'].'\'
				,\''.$row['plan_target'].'\'
				,\''.$row['plan_target_gbn'].'\'
				,\''.$row['plan_budget'].'\'
				,\''.$row['plan_cnt'].'\'
				,\''.$row['plan_cont'].'\'
				,\''.$row['plan_effect'].'\'
				,\''.$row['plan_eval'].'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				,NULL, NULL
				)';
		$query[] = $sql;
	}

	$conn->row_free();

	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();

				 if ($debug){
					 echo $conn->error_msg.chr(13).chr(10).$conn->error_query;
				 }else{
					 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
				 }
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>