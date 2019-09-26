<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$gbn	= '01'; //서비스 묶음별 카테고리
	$parent	= $_POST['parent'];
	$code	= $_POST['code'];
	$name	= $_POST['name'];
	$seq	= $_POST['seq'];

	if (!$seq) $seq = 1;

	if (!$code){
		//다음 코드
		$sql = 'SELECT	IFNULL(MAX(code), 0) + 1
				FROM	mst_category
				WHEre	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		gbn		= \''.$gbn.'\'';
		$code = $conn->get_data($sql);


		//저장
		$sql = 'INSERT INTO mst_category (
				 org_no
				,org_type
				,gbn
				,code
				,name
				,parent
				,seq
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$gbn.'\'
				,\''.$code.'\'
				,\''.$name.'\'
				,\''.$parent.'\'
				,\''.$seq.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}else{
		$sql = 'UPDATE	mst_category
				SET		name		= \''.$name.'\'
				,		seq			= \''.$seq.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		gbn		= \''.$gbn.'\'
				AND		code	= \''.$code.'\'';
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