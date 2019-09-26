<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];//$_POST['code'];
	$jumin	= $_POST['jumin'];
	$sr	= $_POST['sr'];
	$suga = $_POST['suga'];
	$day    = $_POST['day'] < 10 ? '0'.$_POST['day'] : $_POST['day'];
	$date	= $_POST['year'].$_POST['month'].$day;
	$time	= str_replace(':','',$_POST['time']);
	$seq	= $_POST['seq'];
	$resourceCd = $_POST['resourceCd'];
	$memCd  = $ed->de($_POST['memCd']);
	$svcKind = $_POST['svcKind'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	
	$conn->begin();
	
	//기존일정 삭제	
	$sql = 'UPDATE t01iljung
			SET    t01_del_yn       = \'Y\' 
			 WHERE t01_ccode        = \''.$code.'\'
			   AND t01_mkind        = \''.$sr.'\'
			   AND t01_suga_code1	= \''.$suga.'\'
			   AND t01_sugup_date   = \''.$date.'\'
			   AND t01_sugup_fmtime = \''.$time.'\'
			   AND t01_svc_subcode  = \''.$svcKind.'\'
			   AND t01_mem_cd1      = \''.$resourceCd.'\'
			   AND t01_mem_cd2      = \''.$memCd.'\'
			   AND t01_del_yn		= \'N\'
			   AND IFNULL(t01_request,\'\') = \'SERVICE\'';
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