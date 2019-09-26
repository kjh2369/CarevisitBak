<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$seq	= $_POST['seq'];
	$date	= $_POST['date'];
	$memo	= AddSlashes($_POST['memo']);
	$yymm	= SubStr(str_replace('-','',$date),0,6);

	if ($year.$month != $yymm){
		$sql = 'UPDATE	iljung_memo
				SET		del_flag	= \'Y\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		jumin		= \''.$jumin.'\'
				AND		yymm		= \''.$year.$month.'\'
				AND		seq			= \''.$seq.'\'';

		$query[] = $sql;

		$sql = 'SELECT	IFNULL(MAX(seq), 0) + 1
				FROM	iljung_memo
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$yymm.'\'';

		$seq = $conn->get_data($sql);
	}else{
		if (!$seq){
			$sql = 'SELECT	IFNULL(MAX(seq), 0) + 1
					FROM	iljung_memo
					WHERE	org_no	= \''.$orgNo.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		yymm	= \''.$yymm.'\'';

			$seq = $conn->get_data($sql);
		}
	}

	$sql = 'SELECT	COUNT(*)
			FROM	iljung_memo
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		seq		= \''.$seq.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	iljung_memo
				SET		reg_dt		= \''.$date.'\'
				,		memo		= \''.$memo.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		jumin		= \''.$jumin.'\'
				AND		yymm		= \''.$yymm.'\'
				AND		seq			= \''.$seq.'\'';
	}else{
		$sql = 'INSERT INTO iljung_memo (
				 org_no
				,jumin
				,yymm
				,seq
				,svc_cd
				,reg_dt
				,memo
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$jumin.'\'
				,\''.$yymm.'\'
				,\''.$seq.'\'
				,\''.$svcCd.'\'
				,\''.$date.'\'
				,\''.$memo.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$query[] = $sql;


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>