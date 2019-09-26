<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];


	$sql = 'SELECT *
			  FROM acct_no
			 INNER JOIN bank
			    ON bank.code = acct_no.bank_nm
			 WHERE org_no = \''.$code.'\'
			 ORDER BY acct_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= $row['acct_nm'].chr(2)
			  .  $row['name'].chr(2)
			  .  $row['bank_no'].chr(2)
			  .  $row['bank_acct'].chr(2)
			  .  $row['other'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>