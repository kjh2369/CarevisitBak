<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$seq = $_POST['seq'];

	$sql = 'SELECT	ispt_dt,per_nm,per_jumin,ispt_gbn,ispt_rsn,client_need_change,svc_offer_problem,wer_opion,ispt_rst,after_plan
			FROM	hce_re_ispt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$seq.'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	if ($row){
		$data .= 'date='	.$row['ispt_dt'];
		$data .= '&name='	.$row['per_nm'];
		$data .= '&jumin='	.$ed->en($row['per_jumin']);
		$data .= '&gbn='	.$row['ispt_gbn'];
		$data .= '&rsn='	.$row['ispt_rsn'];
		$data .= '&need='	.StripSlashes($row['client_need_change']);
		$data .= '&offer='	.StripSlashes($row['svc_offer_problem']);
		$data .= '&wer='	.StripSlashes($row['wer_opion']);
		$data .= '&rst='	.$row['ispt_rst'];
		$data .= '&plan='	.StripSlashes($row['after_plan']);
	}

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>