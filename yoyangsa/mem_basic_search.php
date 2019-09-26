<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code = $_SESSION['userCenterCode'];

	$sql = 'SELECT jumin
			,      stat
			  FROM insu
			 WHERE org_no = \''.$code.'\'
			   AND start_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
			   AND IFNULL(end_dt,\'9999-12-31\') >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
	$insu = $conn->_fetch_array($sql,'jumin');

	$sql = 'SELECT mst.jumin
			,      mst.name
			,      his.join_dt
			,      his.quit_dt
			,      his.employ_type
			,      his.employ_stat
			,      his.weekly
			,      his.bank_acct
			,      his.bank_no
			,      his.bank_nm
			,      his.prolong_rate
			,      his.holiday_rate_gbn
			,      his.holiday_rate
			,      his.annuity_yn
			,      his.health_yn
			,      his.sanje_yn
			,      his.employ_yn
			,      his.paye_yn
			,      his.annuity_amt
			  FROM (
				   SELECT DISTINCT
						  m02_yjumin AS jumin
				   ,      m02_yname AS name
					 FROM m02yoyangsa
					WHERE m02_ccode = \''.$code.'\'
				   ) AS mst
			 INNER JOIN (
				   SELECT jumin
				   ,      seq
				   ,      join_dt
				   ,      quit_dt
				   ,      employ_type
				   ,      employ_stat
				   ,      weekly
				   ,      bank_acct
				   ,      bank_no
				   ,      bank_nm
				   ,      prolong_rate
				   ,      holiday_rate_gbn
				   ,      holiday_rate
				   ,      annuity_yn
				   ,      health_yn
				   ,      sanje_yn
				   ,      employ_yn
				   ,      paye_yn
				   ,      annuity_amt
					 FROM mem_his
					WHERE org_no = \''.$code.'\'
				   ) AS his
				ON his.jumin = mst.jumin
			   AND his.seq = (SELECT MAX(seq)
								FROM mem_his
							   WHERE org_no = \''.$code.'\'
								 AND jumin  = his.jumin)
			 ORDER BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= $ed->en($row['jumin']).chr(2)
			  .  $myF->issStyle($row['jumin']).chr(2)
			  .  $row['name'].chr(2)
			  .  $row['join_dt'].chr(2)
			  .  $row['quit_dt'].chr(2)
			  .  $row['employ_type'].chr(2)
			  .  $row['employ_stat'].chr(2)
			  .  $row['weekly'].chr(2)
			  .  $row['bank_acct'].chr(2)
			  .  $row['bank_no'].chr(2)
			  .  $row['bank_nm'].chr(2)
			  .  $row['prolong_rate'].chr(2)
			  .  $row['holiday_rate_gbn'].chr(2)
			  .  $row['holiday_rate'].chr(2)
			  .  $row['annuity_yn'].chr(2)
			  .  $row['health_yn'].chr(2)
			  .  $row['sanje_yn'].chr(2)
			  .  $row['employ_yn'].chr(2)
			  .  $row['paye_yn'].chr(2)
			  .  $row['annuity_amt'].chr(2)
			  .  $insu[$row['jumin']]['stat'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once("../inc/_db_close.php");
?>