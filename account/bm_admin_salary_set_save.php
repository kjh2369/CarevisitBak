<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$jumin	= $ed->de($_POST['jumin']);
	$job	= $_POST['job'];
	$amt	= str_replace(',','',$_POST['amt']);
	//$insu	= str_replace(',','',$_POST['insu']);
	$nps_amt = str_replace(',','',$_POST['nps_amt']);
	$nhic_amt = str_replace(',','',$_POST['nhic_amt']);
	$ei_amt = str_replace(',','',$_POST['ei_amt']);
	$insu = $nps_amt + $nhic_amt + $ei_amt;
	$retir	= str_replace(',','',$_POST['retir']);

	if (!$orgNo || !$year || !$month || !$job || !$jumin){
		$conn->close();
		echo 9;
		exit;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_salary
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'
			AND		jumin	= \''.$jumin.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_salary
				SET		job			= \''.$job.'\'
				,		salary		= \''.$amt.'\'
				,		nps_amt		= \''.$nps_amt.'\'
				,		nhic_amt	= \''.$nhic_amt.'\'
				,		ei_amt		= \''.$ei_amt.'\'
				,		insu_amt	= \''.$insu.'\'
				,		retire_amt	= \''.$retir.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$year.$month.'\'
				AND		jumin	= \''.$jumin.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_salary (
				 org_no
				,yymm
				,jumin
				,job
				,salary
				,nps_amt
				,nhic_amt
				,ei_amt
				,insu_amt
				,retire_amt
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$year.$month.'\'
				,\''.$jumin.'\'
				,\''.$job.'\'
				,\''.$amt.'\'
				,\''.$nps_amt.'\'
				,\''.$nhic_amt.'\'
				,\''.$ei_amt.'\'
				,\''.$insu.'\'
				,\''.$retir.'\'
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