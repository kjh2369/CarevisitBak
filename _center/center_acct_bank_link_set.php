<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$seq	= $_POST['seq'];
	$year	= $_POST['year'];
	$ym		= $_POST['yymm'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');
	$acctYm	= $myF->dateAdd('month', 1, $yymm.'01', 'Ym');


	//은행정보
	$sql = 'SELECT	bank_dt, bank_nm, bank_acct
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$ym.'\'
			AND		seq		= \''.$seq.'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	$bankDt		= $row['bank_dt'];
	$bankNm		= $row['bank_nm'];
	$bankAcct	= $row['bank_acct'];

	Unset($row);


	//청구금액
	$sql = 'SELECT	SUM(acct_amt)
			FROM	cv_svc_acct_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';
	$acctAmt = $conn->get_data($sql);

	$msg .= '원청구금액 : '.$acctAmt.chr(13);


	//연결금액
	$sql = 'SELECT	SUM(link_amt) As link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		IFNULL(link_stat, \'1\') = \'1\'
			AND		del_flag= \'N\'';
	$linkAmt = $conn->get_data($sql);


	//입금금액
	$sql = 'SELECT	link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$ym.'\'
			AND		seq		= \''.$seq.'\'
			AND		del_flag= \'N\'';
	$inAmt = $conn->get_data($sql);

	$sql = 'SELECT	link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$ym.'\'
			AND		seq		= \''.$seq.'\'
			AND		link_stat = \'1\'
			AND		del_flag= \'N\'';
	$outAmt = $conn->get_data($sql);

	//미연결금액
	$nonLinkAmt = $inAmt - $outAmt;

	//청구금액
	$acctAmt -= $linkAmt;

	$msg .= '청구금액 : '.$acctAmt.chr(13);
	$msg .= '미연결금액 : '.$nonLinkAmt.chr(13);


	//연결금액
	if ($acctAmt < $nonLinkAmt){ //청구금액보다 미연결금액이 많을 경우
		$linkAmt = $acctAmt; //연결금액
		$leftAmt = $nonLinkAmt - $linkAmt; //연결 후 남는 금액
		$nonAcct = 0; //미청구금액
		$linkStat= '5'; //일부연결
	}else{
		$linkAmt = $nonLinkAmt; //연결금액
		$lfetAmt = 0; //연결 후 남는 금액
		$nonAcct = $acctAmt - $linkAmt; //미청구금액
		$linkStat= '1'; //연결
	}

	$msg .= '연결금액 : '.$linkAmt.chr(13);
	$msg .= '남는금액 : '.$leftAmt.chr(13);
	$msg .= '미청구금액 : '.$nonAcct.chr(13);


	if ($linkStat == '5'){
		//연결 순번
		$sql = 'SELECT	IFNULL(MAX(seq),0) + 1
				FROM	cv_cms_link
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$yymm.'\'';
		$newSeq = $conn->get_data($sql);

		//연결내역 저장
		$sql = 'INSERT INTO cv_cms_link (
				 org_no
				,yymm
				,seq
				,acct_ym
				,bank_dt
				,bank_nm
				,bank_acct
				,link_amt
				,link_stat
				,prepay_seq
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$yymm.'\'
				,\''.$newSeq.'\'
				,\''.$acctYm.'\'
				,\''.$bankDt.'\'
				,\''.$bankNm.'\'
				,\''.$bankAcct.'\'
				,\''.$linkAmt.'\'
				,\'1\'
				,\''.$ym.'_'.$seq.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
		$query[] = $sql;


		$sql = 'UPDATE	cv_cms_link
				SET		link_amt	= \''.$leftAmt.'\'
				,		link_stat	= \'5\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$ym.'\'
				AND		seq		= \''.$seq.'\'';
		$query[] = $sql;
	}else{
		if ($yymm == $ym){
			$sql = 'UPDATE	cv_cms_link
					SET		link_stat	= \'1\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$ym.'\'
					AND		seq		= \''.$seq.'\'';
			$query[] = $sql;
		}else{
			$sql = 'UPDATE	cv_cms_link
					SET		link_amt	= \'0\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$ym.'\'
					AND		seq		= \''.$seq.'\'';
			$query[] = $sql;

			//연결 순번
			$sql = 'SELECT	IFNULL(MAX(seq),0) + 1
					FROM	cv_cms_link
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$yymm.'\'';
			$newSeq = $conn->get_data($sql);

			//연결내역 저장
			$sql = 'INSERT INTO cv_cms_link (
					 org_no
					,yymm
					,seq
					,acct_ym
					,bank_dt
					,bank_nm
					,bank_acct
					,link_amt
					,link_stat
					,prepay_seq
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$yymm.'\'
					,\''.$newSeq.'\'
					,\''.$acctYm.'\'
					,\''.$bankDt.'\'
					,\''.$bankNm.'\'
					,\''.$bankAcct.'\'
					,\''.$linkAmt.'\'
					,\'1\'
					,\''.$ym.'_'.$seq.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
			$query[] = $sql;
		}
	}


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '!!!ERROR!!!';
			 exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>