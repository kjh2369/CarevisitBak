<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$memCd = $_POST['memCd'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	if (!is_numeric($memCd)) $memCd = $ed->de($memCd);

	$sql = 'select cf_mem_nm
			  from client_family
			 where org_no    = \''.$code.'\'
			   and cf_jumin  = \''.$jumin.'\'
			   and cf_mem_cd = \''.$memCd.'\'';
	$memNm = $conn->get_data($sql);

	if (!empty($memNm))
		echo 'Y';
	else
		echo 'N';

	include_once('../inc/_db_close.php');
?>