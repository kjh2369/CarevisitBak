<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$date = Date('Ymd');

	$sql = 'SELECT bank.name AS bank_nm
			,      trans.bank_no
			,      trans.bank_acct
			,      trans.amt
			,      trans.stat
			,      trans.result_other AS other
			,      DATE_FORMAT(trans.request_dt,\'%H:%i:%s\') AS request_dt
			,      DATE_FORMAT(trans.result_dt,\'%H:%i:%s\') AS result_dt
			  FROM trans
			 INNER JOIN bank
			    ON bank.code = trans.bank_nm
			 WHERE trans.org_no = \''.$code.'\'
			   AND trans.type   = \'9\'
			   AND DATE_FORMAT(trans.request_dt,\'%Y%m%d\') = \''.$date.'\'
			 ORDER BY request_dt DESC, bank_acct';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= $row['bank_nm'].chr(2)
			  .  $row['bank_no'].chr(2)
			  .  $row['bank_acct'].chr(2)
			  .  $row['amt'].chr(2)
			  .  $row['stat'].chr(2)
			  .  $row['request_dt'].chr(2)
			  .  $row['result_dt'].chr(2)
			  .  $row['other'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>