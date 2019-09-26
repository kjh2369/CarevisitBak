<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$CMSNo	= $_POST['CMSNo'];

	if (StrLen($CMSNo) < 8){
		$CMSNo = '00000000'.$CMSNo;
		$CMSNo = SubStr($CMSNo, StrLen($CMSNo) - 8, StrLen($CMSNo));
	}

	$sql = 'DELETE
			FROM	cv_cms_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$CMSNo.'\'';

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