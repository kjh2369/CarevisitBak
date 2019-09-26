<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo		= $_POST['orgNo'];
	$CMSNo		= $_POST['CMS'];
	$CMSDate	= $_POST['date'];
	$CMSSeq		= $_POST['seq'];
	$nonLinkAmt	= $_POST['amt'];
	$year		= $_POST['year'];
	$month		= IntVal($_POST['month']);
	$yymm		= $year.($month < 10 ? '0' : '').$month;
	$yymm		= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');
	$acctYm		= $myF->dateAdd('month', 1, $yymm.'01', 'Ym');


	if (StrLen($CMSNo) < 8){
		$CMSNo = '00000000'.$CMSNo;
		$CMSNo = SubStr($CMSNo, StrLen($CMSNo) - 8, StrLen($CMSNo));
	}


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
			AND		del_flag= \'N\'';
	$linkAmt = $conn->get_data($sql);


	//CMS 입금금액
	$sql = 'SELECT	in_amt
			FROM	cv_cms_reg
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$CMSNo.'\'
			AND		cms_dt	= \''.$CMSDate.'\'
			AND		seq		= \''.$CMSSeq.'\'
			AND		del_flag= \'N\'';
	$inAmt = $conn->get_data($sql);

	$sql = 'SELECT	SUM(link_amt)
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$CMSNo.'\'
			AND		cms_dt	= \''.$CMSDate.'\'
			AND		cms_seq	= \''.$CMSSeq.'\'
			AND		del_flag= \'N\'';
	$outAmt = $conn->get_data($sql);

	//CMS 미연결금액
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
		$linkStat= '3'; //일부연결
	}else{
		$linkAmt = $nonLinkAmt; //연결금액
		$lfetAmt = 0; //연결 후 남는 금액
		$nonAcct = $acctAmt - $linkAmt; //미청구금액
		$linkStat= '1'; //연결
	}

	$msg .= '연결금액 : '.$linkAmt.chr(13);
	$msg .= '남는금액 : '.$leftAmt.chr(13);
	$msg .= '미청구금액 : '.$nonAcct.chr(13);


	//연결 순번
	$sql = 'SELECT	IFNULL(MAX(seq),0) + 1
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';
	$seq = $conn->get_data($sql);


	//연결내역 저장
	$sql = 'INSERT INTO cv_cms_link (
			 org_no
			,yymm
			,seq
			,acct_ym
			,cms_no
			,cms_dt
			,cms_seq
			,link_amt
			,insert_id
			,insert_dt) VALUES (
			 \''.$orgNo.'\'
			,\''.$yymm.'\'
			,\''.$seq.'\'
			,\''.$acctYm.'\'
			,\''.$CMSNo.'\'
			,\''.$CMSDate.'\'
			,\''.$CMSSeq.'\'
			,\''.$linkAmt.'\'
			,\''.$_SESSION['userCode'].'\'
			,NOW()
			)';
	$query[] = $sql;


	//CMS 등록내역 상태수정
	$sql = 'UPDATE	cv_cms_reg
			SET		link_stat = \''.$linkStat.'\'
			,		update_id = \''.$_SESSION['userCode'].'\'
			,		update_dt = NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$CMSNo.'\'
			AND		cms_dt	= \''.$CMSDate.'\'
			AND		seq		= \''.$CMSSeq.'\'';
	$query[] = $sql;


	//쿼리
	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '!!!'.$conn->error_msg.chr(13).$conn->error_query.'!!!';
			 exit;
		}
	}

	$conn->commit();

	Unset($query);


	if ($IsAll){
		//연결 총금액
		$sql = 'SELECT	SUM(link_amt)
				FROM	cv_cms_link
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$yymm.'\'
				AND		del_flag= \'N\'
				AND		CASE WHEN IFNULL(link_stat,\'\') = \'\' THEN \'1\' ELSE link_stat END = \'1\'';
		$linkAmt = $conn->get_data($sql);

		//청구금액
		$sql = 'SELECT	SUM(acct_amt)
				FROM	cv_svc_acct_list
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$yymm.'\'';
		$acctAmt = $conn->get_data($sql);

		if ($acctAmt > $linkAmt){
			$IsLoop = true;
		}else{
			$IsLoop = false;
		}
	}

	if (!$IsAll) include_once('../inc/_db_close.php');
?>