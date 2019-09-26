<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$seq	= $_POST['seq'];
	$CMSNo	= $_POST['CMSNo'];
	$CMSDate= $_POST['CMSDate'];
	$CMSSeq	= $_POST['CMSSeq'];
	$prepaySeq = $_POST['prepaySeq'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');

	if ($CMSNo){
		$CMSNo	= '00000000'.$CMSNo;
		$CMSNo	= SubStr($CMSNo, StrLen($CMSNo) - 8, StrLen($CMSNo));
	}

	//연결금액
	$sql = 'SELECT	link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		seq		= \''.$seq.'\'
			AND		cms_no	= \''.$CMSNo.'\'
			AND		cms_dt	= \''.$CMSDate.'\'
			AND		cms_seq	= \''.$CMSSeq.'\'
			AND		del_flag= \'N\'';
	$linkAmt = $conn->get_data($sql);
	$msg .= 'linkAmt : '.$linkAmt.chr(13);


	//다른 연결금액
	$sql = 'SELECT	SUM(link_amt) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$CMSNo.'\'
			AND		cms_dt	= \''.$CMSDate.'\'
			AND		cms_seq	= \''.$CMSSeq.'\'
			AND		del_flag= \'N\'';
	$linkTotal = $conn->get_data($sql) - $linkAmt;
	//$linkTotal = $conn->get_data($sql);
	$msg .= 'linkTotal : '.$linkTotal.chr(13);
	$msg .= $sql.chr(13);

	if ($linkTotal < 0) $linkTotal = 0;


	//연결삭제
	$sql = 'UPDATE	cv_cms_link
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no		= \''.$orgNo.'\'
			AND		yymm		= \''.$yymm.'\'
			AND		seq			= \''.$seq.'\'';
	$query[] = $sql;


	if ($CMSNo && $CMSDate && $CMSSeq > 0){
		//입금내역
		$sql = 'SELECT	in_amt, link_stat
				FROM	cv_cms_reg
				WHERE	org_no	= \''.$orgNo.'\'
				AND		cms_no	= \''.$CMSNo.'\'
				AND		cms_dt	= \''.$CMSDate.'\'
				AND		seq		= \''.$CMSSeq.'\'';

		$row = $conn->get_array($sql);
		$inAmt = $row['in_amt'];
		$linkStat = $row['link_stat'];
		Unset($row);

		$msg .= 'inAmt : '.$inAmt.chr(13);
		$msg .= 'linkTotal : '.$linkTotal.chr(13);

		/*
		if ($inAmt == $linkTotal){
			//미연결
			if ($linkStat == '9'){
				$linkStat = '';
			}else{
				$linkStat = '9';
			}
		}else if ($inAmt > $linkTotal){
			//일부연결
			if ($linkStat == '3'){
				$linkStat = '';
			}else{
				$linkStat = '3';
			}
		}else if ($linkTotal == 0){
			//연결
			if ($linkStat == '1'){
				$linkStat = '';
			}else{
				$linkStat = '1';
			}
		}else{
			//이상오류 확인해볼것
			$conn->close();
			echo '!!! 상태확인불가능 !!!';
			exit;
		}
		*/

		if ($linkTotal < 1){
			//미연결
			if ($linkStat == '9'){
				$linkStat = '';
			}else{
				$linkStat = '9';
			}
		}else if ($linkTotal > 0){
			//일부연결
			if ($linkStat == '3'){
				$linkStat = '';
			}else{
				$linkStat = '3';
			}
		}else{
			//이상오류 확인해볼것
			$conn->close();
			echo '!!! 상태확인불가능 !!!';
			exit;
		}

		if ($linkStat){
			$sql = 'UPDATE	cv_cms_reg
					SET		link_stat = \''.$linkStat.'\'
					,		update_id = \''.$_SESSION['userCode'].'\'
					,		update_dt = NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		cms_no	= \''.$CMSNo.'\'
					AND		cms_dt	= \''.$CMSDate.'\'
					AND		seq		= \''.$CMSSeq.'\'';
			$query[] = $sql;
		}
	}else{
		//입금내역
		$sql = 'SELECT	COUNT(*)
				FROM	cv_cms_link
				WHERE	org_no		= \''.$orgNo.'\'
				AND		prepay_seq	= \''.$yymm.'_'.$seq.'\'
				AND		del_flag	= \'N\'';
		$prepayCnt = $conn->get_data($sql);


		//입금내역
		if ($prepaySeq){
			//입금내역 삭제
			$row = Explode('_',$prepaySeq);

			$sql = 'UPDATE	cv_cms_link
					SET		link_amt	= link_amt + \''.$linkAmt.'\'';

			if ($prepayCnt <= 1){
				$sql .= '
						,	prepay_seq	= NULL';
			}

			$sql .= '
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		yymm		= \''.$row[0].'\'
					AND		seq			= \''.$row[1].'\'';
			$query[] = $sql;
		}else{
			//선입금된 내역
			$sql = 'SELECT	yymm, seq
					FROM	cv_cms_link
					WHERE	org_no		= \''.$orgNo.'\'
					AND		prepay_seq	= \''.$yymm.'_'.$seq.'\'
					AND		del_flag	= \'N\'';
			$row = $conn->get_array($sql);

			$sql = 'UPDATE	cv_cms_link
					SET		link_amt	= link_amt + \''.$linkAmt.'\'';

			if ($prepayCnt <= 1){
				$sql .= '
						,	prepay_seq	= NULL';
			}

			$sql .= '
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		yymm		= \''.$row['yymm'].'\'
					AND		seq			= \''.$row['seq'].'\'';
			$query[] = $sql;
		}
	}


	if (is_array($query)){
		#echo $msg;
		#exit;
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo '!!!'.$conn->error_msg.'!!!';
				 exit;
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>