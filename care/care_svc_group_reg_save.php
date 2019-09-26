<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$sugaCd = $_POST['sugaCd'];
	$resCd	= $_POST['resCd'];
	$memCd	= $ed->de($_POST['memCd']);
	$seq	= $_POST['seq'];
	$groupNm= $_POST['groupNm'];
	$target	= $_POST['target'];
	$category = $_POST['category'];

	if ($category == 'ALL') $category = '';

	$sql = 'SELECT	COUNT(*)
			FROM	care_svc_group
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd = \''.$sugaCd.'\'
			AND		seq		= \''.$seq.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	care_svc_group
				SET		group_nm	= \''.$groupNm.'\'
				,		target		= \''.$target.'\'
				,		mem_cd		= \''.$memCd.'\'';

		if ($category){
			$sql .= '
				,		category	= \''.$category.'\'';
		}else{
			$sql .= '
				,		category	= NULL';
		}

		$sql .= '
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		suga_cd = \''.$sugaCd.'\'
				AND		seq		= \''.$seq.'\'';
	}else{
		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	care_svc_group
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		suga_cd = \''.$sugaCd.'\'';

		$seq = $conn->get_data($sql);

		$sql = 'INSERT INTO care_svc_group (org_no,org_type,suga_cd,seq,res_cd,mem_cd,group_nm,target,category,insert_id,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$sugaCd.'\'
				,\''.$seq.'\'
				,\''.$resCd.'\'
				,\''.$memCd.'\'
				,\''.$groupNm.'\'
				,\''.$target.'\'
				,'.($category ? '\''.$category.'\'' : 'NULL').'
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