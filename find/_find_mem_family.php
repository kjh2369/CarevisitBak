<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$cCd  = $ed->de($_POST['cCd']);
	$mCd  = $ed->de($_POST['mCd']);

	$sql = 'select cf_kind as cd
			,      m81_name as nm
			  from client_family
			 inner join m81gubun
				on m81_gbn   = \'FML\'
			   and m81_code  = cf_kind
			 where org_no    = \''.$code.'\'
			   and cf_jumin  = \''.$cCd.'\'
			   and cf_mem_cd = \''.$mCd.'\'';

	$arr = $conn->get_array($sql);

	echo 'cd='.$arr['cd'].'&nm='.$arr['nm'];

	include_once('../inc/_db_close.php');
?>