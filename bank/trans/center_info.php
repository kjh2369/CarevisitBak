<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$code = $_POST['code'];

	$sql = 'SELECT MIN(m00_mkind)
			,      m00_store_nm AS nm
			,      m00_mname AS manager
			,      m00_ctel AS phone
			,      m00_cpostno AS postno
			,      m00_caddr1 AS addr
			,      m00_caddr2 AS addr_dtl
			,      m00_bank_name AS bank_cd
			,      m00_bank_no AS bank_no
			,      m00_bank_depos AS bank_acct
			  FROM m00center
			 WHERE m00_mcode  = \''.$code.'\'
			   AND m00_del_yn = \'N\'';

	$row = $conn->get_array($sql);
	$data = $row['nm'].chr(2)
		  . $row['manager'].chr(2)
		  . $myF->phoneStyle($row['phone'],'.').chr(2)
		  . $row['addr'].chr(2)
		  . $row['bank_no'].chr(2)
		  . $row['bank_acct'];

	echo $data;

	include_once('../inc/_db_close.php');
?>