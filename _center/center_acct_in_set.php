<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$inAmt	= str_replace(',','',$_POST['inAmt']); //금액
	$inGbn	= $_POST['inGbn'];
	$mode	= $_POST['mode'];


	$sql = 'SELECT	m00_store_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			ORDER	BY m00_mkind
			LIMIT	1';

	$orgNm = $conn->get_data($sql);


	if ($mode == 'MANUAL'){
		$cmsNo	= $_POST['cmsNo']; //CMS 번호
		$cmsCom	= $_POST['cmsCom']; //CMS 기관
		$date	= str_replace('-','',$_POST['cmsDt']); //청구일자
		$inDt	= str_replace('-','',$_POST['inDt']); //입금일자
		$cmsMem	= $_POST['memNo']; //회원번호
		$memo	= $_POST['memo'];

		if ($inGbn == '2'){
			$cmsNo = 'BANK'.SubStr($date,0,6);
		}else{
			$sql = 'SELECT	COUNT(*)
					FROM	cv_cms_reg
					WHERE	org_no	= \''.$orgNo.'\'
					AND		cms_no	= \''.$cmsNo.'\'
					AND		cms_dt	= \''.$date.'\'
					AND		in_dt	= \''.$inDt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){
				$conn->close();
				echo '입력하신 청구일자와 입금일자에 등록된 CMS내역이 있습니다.\n입금내역 수정은 입금조회에서 가능합니다.\n\n확인 후 다시 입력하여 주십시오.\n';
				exit;
			}
		}
	}else{
		$cmsNo	= 'BANK'.Date('Ym');
		$date	= str_replace('-','',$_POST['date']); //입금일자
		$bankNm	= $_POST['bankNm']; //은행명
		$acctNm	= $_POST['acctNm']; //입금자
		$stat	= $_POST['stat']; //상태 1:연결, 5:입금, 9:결손
	}


	//청구금액 및 적용금액
	$sql = 'SELECT	org_no, yymm, SUM(acct_amt) AS acct_amt, SUM(link_amt) AS link_amt
			FROM	(
					SELECT	a.org_no, a.yymm, a.acct_amt, 0 AS link_amt
					FROM	(
							SELECT	org_no, yymm, SUM(acct_amt) AS acct_amt
							FROM	cv_svc_acct_list
							WHERE	org_no = \''.$orgNo.'\'
							GROUP	BY org_no, yymm
							) AS a
					GROUP	BY a.org_no, a.yymm
					UNION	ALL
					SELECT	org_no, yymm, 0, SUM(link_amt)
					FROM	cv_cms_link
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'
					GROUP	BY org_no, yymm
					) AS a
			GROUP	BY org_no, yymm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgList[$row['yymm']] = Array('acctAmt'=>$row['acct_amt'], 'linkAmt'=>$row['link_amt']);
	}

	$conn->row_free();





	$conn->begin();


	if ($mode == 'MANUAL'){
		//순번
		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	cv_cms_reg
				WHERE	org_no	= \''.$orgNo.'\'
				AND		cms_no	= \''.$cmsNo.'\'
				AND		cms_dt	= \''.$date.'\'';

		$seq = $conn->get_data($sql);

		$sql = 'INSERT INTO cv_cms_reg (org_no,cms_no,cms_dt,seq,org_nm,in_gbn,in_amt,in_stat,in_dt,cms_com,cms_mem_no,link_stat,excel_yn,memo,insert_id,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$cmsNo.'\'
				,\''.$date.'\'
				,\''.$seq.'\'
				,\''.$orgNm.'\'
				,\'1\'
				,\''.$inAmt.'\'
				,\'Y\'
				,\''.$inDt.'\'
				,\''.$cmsCom.'\'
				,\''.$cmsMem.'\'
				,\'9\'
				,\'N\'
				,\''.$memo.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}else{
		//순번
		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	cv_cms_reg
				WHERE	org_no	= \''.$orgNo.'\'
				AND		cms_no	= \''.$cmsNo.'\'';

		$seq = $conn->get_data($sql);

		//입금등록
		$sql = 'INSERT INTO cv_cms_reg (org_no,cms_no,cms_dt,seq,org_nm,in_gbn,in_amt,in_stat,in_dt,link_stat,bank_nm,bank_acct,insert_id,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$cmsNo.'\'
				,\''.$date.'\'
				,\''.$seq.'\'
				,\''.$orgNm.'\'
				,\'2\'
				,\''.$inAmt.'\'
				,\'Y\'
				,\''.$date.'\'
				,\'9\'
				,\''.$bankNm.'\'
				,\''.$acctNm.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '1.데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}


	if (is_array($orgList)){
		$tmpInAmt = $inAmt;
		foreach($orgList as $tmpYm => $R1){
			if ($tmpInAmt < 1) break;

			$misuAmt = $R1['acctAmt'] - $orgList[$tmpYm]['linkAmt']; //미수금액

			if ($misuAmt > 0){
				if ($tmpInAmt >= $misuAmt){
					$linkAmt = $misuAmt; //연결금액
				}else{
					$linkAmt = $tmpInAmt; //연결금액
				}

				$orgList[$tmpYm]['linkAmt'] = $linkAmt;
				$tmpInAmt -= $linkAmt;

				if ($tmpInAmt > 0){
					$linkStat = '3'; //일부연결
				}else{
					$linkStat = '1'; //연결완료
				}

				//순번
				$sql = 'SELECT	IFNULL(MAX(seq),0)+1
						FROM	cv_cms_link
						WHERE	org_no	= \''.$orgNo.'\'
						AND		yymm	= \''.$tmpYm.'\'';

				$tmpSeq = $conn->get_data($sql);
				$tmpAcctYm = $myF->dateAdd('month', 1, $tmpYm.'01', 'Ym'); //청구년월

				//입금연결내역
				$sql = 'INSERT INTO cv_cms_link (org_no,yymm,seq,acct_ym,cms_no,cms_dt,cms_seq,link_amt,link_stat,org_amt,insert_id,insert_dt) VALUES (
						 \''.$orgNo.'\'
						,\''.$tmpYm.'\'
						,\''.$tmpSeq.'\'
						,\''.$tmpAcctYm.'\'
						,\''.$cmsNo.'\'
						,\''.$date.'\'
						,\''.$seq.'\'
						,\''.$linkAmt.'\'
						,\'1\'
						,\''.$inAmt.'\'
						,\''.$_SESSION['userCode'].'\'
						,NOW()
						)';

				//$query[] = $sql;
				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo '2.데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
					 exit;
				}


				//CMS 등록내역 상태 변경
				$sql = 'UPDATE	cv_cms_reg
						SET		link_stat = \''.$linkStat.'\'
						WHERE	org_no	= \''.$orgNo.'\'
						AND		cms_no	= \''.$cmsNo.'\'
						AND		cms_dt	= \''.$date.'\'
						AND		seq		= \''.$seq.'\'';

				//$query[] = $sql;
				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo '3.데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
					 exit;
				}
			}
		}
	}


	$conn->commit();


	Unset($orgList);

	include_once('../inc/_db_close.php');
	exit;




	if ($year == '0000'){
		$yymm	= '000000';
		$acctYm	= '000000';
	}else{
		$year	= $_POST['year'];
		$month	= IntVal($_POST['month']);
		$yymm	= $year.($month < 10 ? '0' : '').$month;
		$acctYm	= $myF->dateAdd('month', 1, $yymm.'01', 'Ym');
	}

	if (StrLen($CMS) < 8){
		$CMS = '00000000'.$CMS;
		$CMS = SubStr($CMS, StrLen($CMS) - 8, StrLen($CMS));
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

	$msg .= '연결금액 : '.$linkAmt.chr(13);

	//미연결금액
	$nonLinkAmt = $acctAmt - $linkAmt;
	if ($nonLinkAmt < 0) $nonLinkAmt = 0;

	$msg .= '미연결금액 : '.$nonLinkAmt.chr(13);


	if ($nonLinkAmt >= $inAmt){
		//연결처리
		$linkAmt	= $inAmt; //연결금액
		$prepayYn	= 'N'; //선입금아님
		$leftAmt	= 0;
	}else{
		//연결 및 선입금처리
		$linkAmt	= $nonLinkAmt; //연결금액
		$prepayYn	= 'N'; //선입금여부
		$leftAmt	= $inAmt - $nonLinkAmt;
	}

	//상태
	if ($stat == '5'){
		$linkStat = '1'; //연결
		$inStat = '1';
	}else{
		$linkStat = '1';
		$inStat = $stat;
	}

	//다음 순번
	$sql = 'SELECT	IFNULL(MAX(seq),0) + 1
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';

	$seq = $conn->get_data($sql);

	//연결 처리
	if ($nonLinkAmt > 0){
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
				,in_stat
				,org_amt
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$yymm.'\'
				,\''.$seq.'\'
				,\''.$acctYm.'\'
				,\''.$date.'\'
				,\''.$bankNm.'\'
				,\''.$acctNm.'\'
				,\''.$linkAmt.'\'
				,\''.$linkStat.'\'
				,\''.$inStat.'\'
				,\''.$inAmt.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
		$query[] = $sql;
		$prepaySeq = $yymm.'_'.$seq;
		$seq ++;
	}else{
		$prepaySeq = '';
	}


	if ($leftAmt != 0){
		//선입금처리
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
				,in_stat
				,prepay_seq
				,org_amt
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$yymm.'\'
				,\''.$seq.'\'
				,\''.$acctYm.'\'
				,\''.$date.'\'
				,\''.$bankNm.'\'
				,\''.$acctNm.'\'
				,\''.$leftAmt.'\'
				,\'5\'
				,\''.$inStat.'\'
				,\''.$prepaySeq.'\'
				,\''.(!$prepaySeq ? $inAmt : 0).'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
		$query[] = $sql;
	}


	if (is_array($query)){
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