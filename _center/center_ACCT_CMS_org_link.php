<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$CMSNo	= $_POST['CMSNo'];
	$CMSDt	= $_POST['CMSDt'];
	$CMSSeq	= $_POST['CMSSeq'];
	$CMS	= IntVal($CMSNo);

	$sql = 'SELECT	org_no
			FROM	cv_reg_info
			WHERE	cms_no = \''.$CMS.'\'';

	$orgNo = $conn->get_data($sql);

	if (!$orgNo){
		$sql = 'SELECT	org_no
				FROM	cv_cms_list
				WHERE	cms_no = \''.$CMS.'\'';

		$orgNo = $conn->get_data($sql);
	}

	if ($orgNo){
		$sql = 'UPDATE	cv_cms_reg
				SET		org_no	= \''.$orgNo.'\'
				WHERE	org_no	= \'\'
				AND		cms_no	= \''.$CMSNo.'\'
				AND		cms_dt	= \''.$CMSDt.'\'
				AND		seq		= \''.$CMSSeq.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'ERROR'.chr(13).$conn->error_msg.chr(13).$conn->error_query;
			 exit;
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>