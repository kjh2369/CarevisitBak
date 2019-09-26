<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$allotCnt	= str_replace(',','',$_POST['allotCnt']);
	$allotAmt	= str_replace(',','',$_POST['allotAmt']);
	$employCnt	= str_replace(',','',$_POST['employCnt']);
	$deductAmt	= str_replace(',','',$_POST['deductAmt']);
	$memo	= AddSlashes($_POST['memo']);

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_disps
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_disps
				SET		allot_cnt	= \''.$allotCnt.'\'
				,		allot_amt	= \''.$allotAmt.'\'
				,		employ_cnt	= \''.$employCnt.'\'
				,		deduct_amt	= \''.$deductAmt.'\'
				,		memo		= \''.$memo.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$year.$month.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_disps (
				 org_no
				,yymm
				,allot_cnt
				,allot_amt
				,employ_cnt
				,deduct_amt
				,memo
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$year.$month.'\'
				,\''.$allotCnt.'\'
				,\''.$allotAmt.'\'
				,\''.$employCnt.'\'
				,\''.$deductAmt.'\'
				,\''.$memo.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>