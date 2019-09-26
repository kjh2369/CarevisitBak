<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$gbn	= '01';
	$prtYn	= $_POST['prtYn'];
	$lineCnt= $_POST['cnt'];
	$name	= $_POST['name1'].'|'.$_POST['name2'].'|'.$_POST['name3'].'|'.$_POST['name4'].'|'.$_POST['name5'];

	$sql = 'SELECT	COUNT(*)
			FROM	apprline_set
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \''.$gbn.'\'';
	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	apprline_set
				SET		prt_yn		= \''.$prtYn.'\'
				,		line_cnt	= \''.$lineCnt.'\'
				,		line_name	= \''.$name.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		gbn		= \''.$gbn.'\'';
	}else{
		$sql = 'INSERT INTO apprline_set (org_no,gbn,prt_yn,line_cnt,line_name,insert_id,insert_dt) VALUES (
				 \''.$orgNo.'\', \''.$gbn.'\', \''.$prtYn.'\', \''.$lineCnt.'\', \''.$name.'\', \''.$_SESSION['userCode'].'\', NOW()
				)';
	}

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