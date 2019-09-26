<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$code = $_POST['code'];

	$sql = 'SELECT request.yymm
			,      request.jumin
			,      request.seq
			,      mst.name
			,      request.type
			,      request.bank_nm
			,      request.bank_no
			,      request.bank_acct
			,      request.amt
			,      request.dt
			  FROM (
				   SELECT yymm
				   ,      jumin
				   ,      seq
				   ,      type
				   ,      bank.name AS bank_nm
				   ,      bank_no
				   ,      bank_acct
				   ,      amt
				   ,      request_dt AS dt
					 FROM trans
					INNER JOIN bank
					   ON bank.code = trans.bank_nm
					WHERE org_no = \''.$code.'\'
					  AND stat   = \'1\'
				   ) AS request
			  LEFT JOIN (
				   SELECT MIN(m02_mkind)
				   ,      m02_yjumin AS jumin
				   ,      m02_yname AS name
					 FROM m02yoyangsa
					WHERE m02_ccode  = \''.$code.'\'
					  AND m02_del_yn = \'N\'
					GROUP BY m02_yjumin
				   ) AS mst
				ON mst.jumin = request.jumin
			 ORDER BY dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['yymm'].chr(2)
				  .  $ed->en($row['jumin']).chr(2)
				  .  $row['seq'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['type'].chr(2)
				  .  $row['bank_nm'].chr(2)
				  .  $row['bank_no'].chr(2)
				  .  $row['bank_acct'].chr(2)
				  .  $row['amt'].chr(2)
				  .  $row['dt'].chr(1);
		}
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>