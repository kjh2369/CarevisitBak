<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$mode  = $_POST['mode'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);


	if ($mode == 1){
		//마지막 퇴직일자
		$sql = 'select quit_dt
				  from mem_his
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				 order by seq desc
				 limit 1';

		$result = $conn->get_data($sql);
	}

	echo $result;

	include_once('../inc/_db_close.php');
?>