<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$teamCd	= $ed->de($_POST['teamCd']);
	$fromYm	= $_POST['fromYm'];
	$toYm	= $_POST['toYm'];
	$deAmt	= str_replace(',','',$_POST['deAmt']); //기본공제금액
	$deRate	= str_replace(',','',$_POST['deRate']); //기보공제율
	$seq	= $_POST['seq'];

	$sql = 'SELECT	COUNT(*)
			FROM	client_his_team
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_cd	 = \''.$svcCd.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		seq		!= \''.$seq.'\'
			AND		del_flag = \'N\'
			AND		CASE WHEN from_ym BETWEEN \''.$fromYm.'\' AND \''.$toYm.'\' THEN 1
						 WHEN to_ym BETWEEN \''.$fromYm.'\' AND \''.$toYm.'\' THEN 1
						 WHEN \''.$fromYm.'\' BETWEEN from_ym AND to_ym THEN 1
						 WHEN \''.$toYm.'\' BETWEEN from_ym AND to_ym THEN 1 ELSE 0 END = 1';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$conn->close();
		echo '입력하신 적용기간이 중복됩니다. 확인하여 주십시오.';
		exit;
	}

	if ($seq){
		$sql = 'UPDATE	client_his_team
				SET		team_cd		= \''.$teamCd.'\'
				,		from_ym		= \''.$fromYm.'\'
				,		to_ym		= \''.$toYm.'\'
				,		deduct_amt	= \''.$deAmt.'\'
				,		deduct_rate	= \''.$deRate.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		svc_cd	= \''.$svcCd.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$seq.'\'';
	}else{
		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	client_his_team
				WHERE	org_no	= \''.$orgNo.'\'
				AND		svc_cd	= \''.$svcCd.'\'
				AND		jumin	= \''.$jumin.'\'';
		$seq = $conn->get_data($sql);

		$sql = 'INSERT INTO client_his_team (
				 org_no,jumin,svc_cd,seq,team_cd,from_ym,to_ym,deduct_amt,deduct_rate,insert_id,insert_dt
				) VALUES (
				 \''.$orgNo.'\'
				,\''.$jumin.'\'
				,\''.$svcCd.'\'
				,\''.$seq.'\'
				,\''.$teamCd.'\'
				,\''.$fromYm.'\'
				,\''.$toYm.'\'
				,\''.$deAmt.'\'
				,\''.$deRate.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}

	$conn->commit();

	include_once("../inc/_db_close.php");
?>