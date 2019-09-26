<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];


	$sql = 'SELECT MIN(m02_mkind)
			,      m02_yjumin AS jumin
			,      m02_yname AS name
			,      bank.name AS bank_nm
			,      m02_ygyeoja_no AS bank_no
			,      m02_ybank_holder AS bank_acct
			  FROM m02yoyangsa
			 INNER JOIN bank
			    ON bank.code = m02_ybank_name
			 WHERE m02_ccode = \''.$code.'\'
			   AND m02_ybank_name != \'\'
			   AND m02_ygyeoja_no != \'\'
			   AND m02_ybank_holder != \'\'
			 GROUP BY m02_yjumin
			 ORDER BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= $row['name'].chr(2)
			  .  $row['bank_nm'].chr(2)
			  .  $row['bank_no'].chr(2)
			  .  $row['bank_acct'].chr(2)
			  .  ''.chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>