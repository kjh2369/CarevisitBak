<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	/*
		게시판 저장
	 */

	//$orgNo	= $_SESSION['userCenterCode'];
	$orgNo	= $_POST['orgNo'];
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$id		= $_POST['id'];
	$subject= AddSlashes($_POST['subject']);
	$contents= AddSlashes($_POST['contents']);

	//존재여부 확인
	$sql = 'SELECT	COUNT(*)
			FROM	board_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		brd_type= \''.$type.'\'
			AND		dom_id	= \''.$gDomainID.'\'
			AND		brd_cd	= \''.$cd.'\'
			AND		brd_id	= \''.$id.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	board_list
				SET		subject		= \''.$subject.'\'
				,		contents	= \''.$contents.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		brd_type	= \''.$type.'\'
				AND		dom_id		= \''.$gDomainID.'\'
				AND		brd_cd		= \''.$cd.'\'
				AND		brd_id		= \''.$id.'\'';
	}else{
		$sql = 'SELECT	IFNULL(MAX(brd_id),0)+1
				FROM	board_list
				WHERE	org_no	= \''.$orgNo.'\'
				AND		brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		brd_cd	= \''.$cd.'\'';

		$id = $conn->get_data($sql);

		$sql = 'INSERT INTO board_list (
				 org_no
				,brd_type
				,dom_id
				,brd_cd
				,brd_id
				,reg_name
				,reg_dt
				,subject
				,contents
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$type.'\'
				,\''.$gDomainID.'\'
				,\''.$cd.'\'
				,\''.$id.'\'
				,\''.$_SESSION['userName'].'\'
				,NOW()
				,\''.$subject.'\'
				,\''.$contents.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 'ERROR';
		 exit;
	}

	$conn->commit();
	echo $id;

	include_once('../inc/_db_close.php');
?>