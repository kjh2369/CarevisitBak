<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$procSeq = $_POST['procSeq'];

	$sql = 'SELECT	counsel_dt
			,		counsel_nm
			,		counsel_jumin
			,		counsel_gbn
			,		counsel_text
			,		counsel_remark
			FROM	hce_proc_counsel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		proc_seq= \''.$procSeq.'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	if ($row){
		$data .= 'date='	.$row['counsel_dt'];
		$data .= '&name='	.$row['counsel_nm'];
		$data .= '&jumin='	.$ed->en($row['counsel_jumin']);
		$data .= '&gbn='	.$row['counsel_gbn'];
		$data .= '&text='	.StripSlashes($row['counsel_text']);
		$data .= '&remark='	.StripSlashes($row['counsel_remark']);
	}

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>