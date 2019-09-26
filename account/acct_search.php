<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$code = $_SESSION['userCenterCode'];
	$type = $_POST['type'];

	if ($type == '1_3' || $type == '2_3' || $type == '11_3' || $type == '12_3'){
		//증빙서번호 찾기
		$date   = $_POST['date'];
		$year   = SubStr($_POST['date'],0,4);
		$itemCd = $_POST['itemCd'];
		$gbn  = $_POST['gbn'];

		if (Empty($year) || Empty($itemCd)){
			exit;
		}

		if ($gbn == 'I'){
			$gbn = 'income';
		}else{
			$gbn = 'outgo';
		}

		$proofNo = $conn->_proofNo($code, $date, $itemCd, $gbn);

		echo $proofNo;

	}else if ($type == '2' || $type == '12'){
		$year  = $_POST['year'];
		$month = IntVal($_POST['month']);
		$month = ($month < 10 ? '0' : '').$month;

		if ($type == '2'){
			$field = 'income';
			$gbn = 'I';
		}else if ($type == '12'){
			$field = 'outgo';
			$gbn = 'E';
		}else{
			$conn->close();
			echo 9;
			exit;
		}

		$sql = 'SELECT '.$field.'_ent_dt AS ent_dt
				,      '.$field.'_seq AS seq
				,      '.$field.'_acct_dt AS reg_dt
				,      CONCAT(DATE_FORMAT('.$field.'_acct_dt,\'%Y%m%d\'),proof_no) AS proof_no
				,      '.$field.'_item_cd AS item_cd
				,      '.$field.'_amt AS amt
				,      '.$field.'_vat AS vat
				,      '.$field.'_item AS item
				,      cate.nm3 AS item_nm
				  FROM center_'.$field.' AS ie
				  LEFT JOIN ie_category AS cate
					ON cate.gbn = \''.$gbn.'\'
				   AND CONCAT(cate.cd1,cate.cd2,cate.cd3) = ie.'.$field.'_item_cd
				 WHERE org_no = \''.$code.'\'
				   AND DATE_FORMAT('.$field.'_acct_dt,\'%Y%m\') = \''.$year.$month.'\'
				   AND del_flag = \'N\'
				 ORDER BY reg_dt';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['ent_dt'].chr(2)
				  .  $row['seq'].chr(2)
				  .  $myF->dateStyle($row['reg_dt'],'.').chr(2)
				  .  $row['proof_no'].chr(2)
				  .  $row['item_cd'].chr(2)
				  .  $row['item_nm'].chr(2)
				  .  $row['item'].chr(2)
				  .  $row['amt'].chr(2)
				  .  $row['vat'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == '2_1'
		   || $type == '12_1'
		   || $type == '3_1'
		   || $type == '13_1'){
		$year  = $_POST['year'];
		$laCnt = Array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0);

		if ($type == '2_1'
		 || $type == '3_1'){
			$field = 'income';
		}else if ($type == '12_1'
			   || $type == '13_1'){
			$field = 'outgo';
		}else{
			$conn->close();
			echo 9;
			exit;
		}

		$sql = 'SELECT CAST(mon AS unsigned) AS mon
				,      COUNT(mon) AS cnt
				  FROM (
					   SELECT DATE_FORMAT('.$field.'_acct_dt,\'%m\') AS mon
						 FROM center_'.$field.'
						WHERE org_no = \''.$code.'\'
						  AND DATE_FORMAT('.$field.'_acct_dt,\'%Y\') = \''.$year.'\'
						  AND del_flag = \'N\'
					   ) AS t
				 GROUP BY mon';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$laCnt[$row['mon']] = $row['cnt'];
		}

		$conn->row_free();

		echo $laCnt[1].chr(2)
			.$laCnt[2].chr(2)
			.$laCnt[3].chr(2)
			.$laCnt[4].chr(2)
			.$laCnt[5].chr(2)
			.$laCnt[6].chr(2)
			.$laCnt[7].chr(2)
			.$laCnt[8].chr(2)
			.$laCnt[9].chr(2)
			.$laCnt[10].chr(2)
			.$laCnt[11].chr(2)
			.$laCnt[12];

	}else if ($type == '2_2' || $type == '12_2'){
		$entDt = $_POST['entDt'];
		$seq   = $_POST['seq'];

		if ($type == '2_2'){
			$field = 'income';
			$gbn = 'I';
		}else if ($type == '12_2'){
			$field = 'outgo';
			$gbn = 'E';
		}else{
			$conn->close();
			echo 9;
			exit;
		}

		$sql = 'SELECT *
				  FROM center_'.$field.'
				 WHERE org_no        = \''.$code.'\'
				   AND '.$field.'_ent_dt = \''.$entDt.'\'
				   AND '.$field.'_seq    = \''.$seq.'\'
				   AND del_flag = \'N\'';

		$row = $conn->get_array($sql);

		$sql = 'SELECT cd1 AS cd
				,      nm1 AS nm
				  FROM ie_category
				 WHERE gbn = \''.$gbn.'\'
				   AND cd1 = \''.SubStr($row[$field.'_item_cd'],0,2).'\'
				 LIMIT 1';
		$cate1 = $conn->get_array($sql);

		$sql = 'SELECT cd2 AS cd
				,      nm2 AS nm
				  FROM ie_category
				 WHERE gbn = \''.$gbn.'\'
				   AND cd2 = \''.SubStr($row[$field.'_item_cd'],2,2).'\'
				 LIMIT 1';
		$cate2 = $conn->get_array($sql);

		$sql = 'SELECT cd3 AS cd
				,      nm3 AS nm
				  FROM ie_category
				 WHERE gbn = \''.$gbn.'\'
				   AND cd3 = \''.SubStr($row[$field.'_item_cd'],4,3).'\'';
		$cate3 = $conn->get_array($sql);

		echo $row[$field.'_acct_dt'].chr(2)
			.$row['proof_printno'].chr(2)
			.$cate1['cd'].chr(2)
			.$cate2['cd'].chr(2)
			.$cate3['cd'].chr(2)
			.$cate1['nm'].chr(2)
			.$cate2['nm'].chr(2)
			.$cate3['nm'].chr(2)
			.$row['vat_yn'].chr(2)
			.$row[$field.'_amt'].chr(2)
			.$row[$field.'_vat'].chr(2)
			.$row['taxid'].chr(2)
			.$row['biz_group'].chr(2)
			.$row['biz_type'].chr(2)
			.$row[$field.'_item'].chr(2)
			.$row['other'];

	}else if ($type == '3' || $type == '13'){
		//입,출금내역집계
		$year  = $_POST['year'];
		$month = $_POST['month'];
		$month = (IntVal($month) < 10 ? '0' : '').IntVal($month);

		if ($type == '3'){
			$field = 'income';
			$gbn = 'I';
		}else if ($type == '13'){
			$field = 'outgo';
			$gbn = 'E';
		}else{
			$conn->close();
			echo 9;
			exit;
		}

		$sql = 'SELECT '.$field.'_acct_dt AS acct_dt
				,      proof_no
				,      '.$field.'_item_cd AS item_cd
				,      cate.nm3 AS item_nm
				,      SUM('.$field.'_amt) AS amt
				,      SUM('.$field.'_vat) AS vat
				  FROM center_'.$field.' AS ie
				 INNER JOIN ie_category AS cate
					ON cate.gbn = \''.$gbn.'\'
				   AND CONCAT(cate.cd1,cate.cd2,cate.cd3) = ie.'.$field.'_item_cd
				 WHERE org_no = \''.$code.'\'
				   AND DATE_FORMAT('.$field.'_acct_dt,\'%Y%m\') = \''.$year.$month.'\'
				   AND del_flag = \'N\'
				 GROUP BY '.$field.'_acct_dt, proof_no, '.$field.'_item_cd
				 ORDER BY CASE WHEN IFNULL(proof_no,\'\') != \'\' THEN 1 ELSE 2 END, proof_no, item_cd';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['acct_dt'].chr(2)
				  .  $row['proof_no'].chr(2)
				  .  $row['item_nm'].chr(2)
				  .  $row['amt'].chr(2)
				  .  $row['vat'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else{
		$conn->close();
		echo 9;
		exit;
	}

	include_once('../inc/_db_close.php');
?>