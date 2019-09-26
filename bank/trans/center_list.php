<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$sql = 'SELECT cd
			,      nm
			,      salary_cnt
			,      salary_amt
			,      insu_cnt
			,      insu_amt
			,      other_cnt
			,      other_amt
			  FROM (
				   SElECT trans.cd
				   ,      mst.nm
				   ,      SUM(CASE trans.type WHEN \'1\' THEN trans.cnt ELSE 0 END) AS salary_cnt
				   ,      SUM(CASE trans.type WHEN \'1\' THEN trans.amt ELSE 0 END) AS salary_amt
				   ,      SUM(CASE trans.type WHEN \'3\' THEN trans.cnt ELSE 0 END) AS insu_cnt
				   ,      SUM(CASE trans.type WHEN \'3\' THEN trans.amt ELSE 0 END) AS insu_amt
				   ,      SUM(CASE trans.type WHEN \'9\' THEN trans.cnt ELSE 0 END) AS other_cnt
				   ,      SUM(CASE trans.type WHEN \'9\' THEN trans.amt ELSE 0 END) AS other_amt
					 FROM (
					   SELECT org_no AS cd
					   ,      type
					   ,      COUNT(org_no) AS cnt
					   ,      SUM(amt) AS amt
						 FROM trans
						WHERE stat = \'1\'
						GROUP BY org_no, type
					   ) AS trans
					INNER JOIN (
					   SELECT MIN(m00_mkind)
					   ,      m00_mcode AS cd
					   ,      m00_store_nm AS nm
						 FROM m00center
						WHERE m00_del_yn = \'N\'
						GROUP BY m00_mcode
					   ) AS mst
					ON mst.cd = trans.cd
					GROUP BY trans.cd, mst.nm
				   ) AS t
			 ORDER BY CASE WHEN salary_amt + insu_amt + other_amt > 0 THEN 1 ELSE 2 END, nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['cd'].chr(2)
				  .  $row['nm'].chr(2)
				  .  $row['salary_cnt'].chr(2)
				  .  $row['salary_amt'].chr(2)
				  .  $row['insu_cnt'].chr(2)
				  .  $row['insu_amt'].chr(2)
				  .  $row['other_cnt'].chr(2)
				  .  $row['other_amt'].chr(2)
				  . ($row['salary_cnt']+$row['insu_cnt']+$row['other_cnt']).chr(2)
				  . ($row['salary_amt']+$row['insu_amt']+$row['other_amt']).chr(1);
		}
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>