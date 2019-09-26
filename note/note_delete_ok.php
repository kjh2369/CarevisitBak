<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->mode = 1;

	$code = $_GET['code'];
	$yymm = $_GET['yymm'];
	$seq  = $_GET['seq'];
	$fcd  = $_GET['fcd'];
	$mode = $_GET['mode'];

	echo $myF->header_script();

	switch($mode){
		case 'from':
			$tbl = 'msg_send';
			$col = 'del_flag';
			$fol = '';
			break;
		case 'to':
			$tbl = 'msg_receipt';
			$col = 'msg_del_flag';
			$fol = 'from_no';
			break;
	}

	$conn->begin();

	$sql = 'update '.$tbl.'
			   set '.$col.' = \'Y\'
			 where org_no   = \''.$code.'\'
			   and msg_yymm = \''.$yymm.'\'
			   and msg_seq  = \''.$seq.'\'';

	if (!empty($fol))
		$sql .= ' and '.$fol.' = \''.$fcd.'\'';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');

	/*
	echo '<script>
			alert(\''.$myF->message('ok','N').'\');';

	if ($conn->mode == 1)
		echo 'location.replace(\'note_list.php?mode='.$mode.'&page=1\');';

	echo '</script>';
	*/
?>