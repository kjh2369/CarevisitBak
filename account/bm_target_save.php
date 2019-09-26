<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];

	parse_str($_POST['para'],$val);

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_target
			WHERE	org_no	= \''.$orgNo.'\'
			AND		year	= \''.$year.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_target
				SET		amt1	= \''.$val['1'].'\'
				,		amt2	= \''.$val['2'].'\'
				,		amt3	= \''.$val['3'].'\'
				,		amt4	= \''.$val['4'].'\'
				,		amt5	= \''.$val['5'].'\'
				,		amt6	= \''.$val['6'].'\'
				,		amt7	= \''.$val['7'].'\'
				,		amt8	= \''.$val['8'].'\'
				,		amt9	= \''.$val['9'].'\'
				,		amt10	= \''.$val['10'].'\'
				,		amt11	= \''.$val['11'].'\'
				,		amt12	= \''.$val['12'].'\'
				,		update_id = \''.$_SESSION['userCode'].'\'
				,		update_dt = NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		year	= \''.$year.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_target (
				 org_no
				,year
				,amt1
				,amt2
				,amt3
				,amt4
				,amt5
				,amt6
				,amt7
				,amt8
				,amt9
				,amt10
				,amt11
				,amt12
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$year.'\'
				,\''.$val['1'].'\'
				,\''.$val['2'].'\'
				,\''.$val['3'].'\'
				,\''.$val['4'].'\'
				,\''.$val['5'].'\'
				,\''.$val['6'].'\'
				,\''.$val['7'].'\'
				,\''.$val['8'].'\'
				,\''.$val['9'].'\'
				,\''.$val['10'].'\'
				,\''.$val['11'].'\'
				,\''.$val['12'].'\'
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