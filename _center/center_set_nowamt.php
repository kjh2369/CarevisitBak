<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$amt	= $_POST['amt'];
	$gbn	= $_POST['gbn'];

	if ($gbn == 'NOW'){
	}else if ($gbn == 'DFT'){
	}else{
		exit;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	cv_svc_acct_amt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	cv_svc_acct_amt
				SET		';

		if ($gbn == 'NOW'){
			$sql .= ' amt = \''.$amt.'\'';
		}else if ($gbn == 'DFT'){
			$sql .= ' dft_amt = \''.$amt.'\'';
		}

		$sql .= '
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$yymm.'\'';
	}else{
		//$sql = 'INSERT INTO cv_svc_acct_amt VALUES (\''.$orgNo.'\', \''.$yymm.'\', \''.$amt.'\', \'0\')';
		$sql = 'INSERT INTO cv_svc_acct_amt (org_no, yymm, ';

		if ($gbn == 'NOW'){
			$sql .= 'amt';
		}else if ($gbn == 'DFT'){
			$sql .= 'dft_amt';
		}

		$sql .= ') VALUES (\''.$orgNo.'\', \''.$yymm.'\', \''.$amt.'\')';
	}

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();

		/*
		for($i=1; $i<=12; $i++){
			$sql = 'SELECT	amt
					FROM	cv_svc_acct_amt
					WHERE	org_no = \''.$orgNo.'\'
					AND		yymm <= \''.$year.($i < 10 ? '0' : '').$i.'\'
					ORDER	BY yymm DESC
					LIMIT	1';
			echo ($i > 1 ? '&' : '').$i.'='.$conn->get_data($sql);
		}
		*/

		if ($gbn == 'NOW'){
			for($i=1; $i<=12; $i++){
				$sql = 'SELECT	amt, dft_amt
						FROM	cv_svc_acct_amt
						WHERE	org_no = \''.$orgNo.'\'
						AND		yymm <= \''.$year.($i < 10 ? '0' : '').$i.'\'
						ORDER	BY yymm DESC
						LIMIT	1';

				$row = $conn->get_array($sql);

				$arrAmt[$i] = $row['amt'];

				Unset($row);
			}
		}else if ($gbn == 'DFT'){
			//¹Ì³³±Ý¾×
			$sql = 'SELECT	CAST(MID(yymm, 5) AS unsigned) AS month, dft_amt
					FROM	cv_svc_acct_amt
					WHERE	org_no = \''.$orgNo.'\'
					AND		LEFT(yymm, 4) = \''.$year.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);
				$arrAmt[$row['month']] = $row['dft_amt'];
			}

			$conn->row_free();
		}

		for($i=1; $i<=12; $i++){
			echo ($i > 1 ? '&' : '').$i.'='.$arrAmt[$i];
		}
	}else{
		$conn->rollback();
	}

	include_once('../inc/_db_close.php');
?>