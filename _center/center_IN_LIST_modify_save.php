<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$cmsNo	= $_POST['cmsNo'];
	$cmsDt	= $_POST['cmsDt'];
	$cmsSeq	= $_POST['cmsSeq'];
	$inAmt	= str_replace(',','',$_POST['inAmt']);
	$mode	= $_POST['mode'];

	/*
	 * mode 1 : 수정, 2 : 삭제
	 */


	$conn->begin();

	//입금적용
	if ($mode == '1'){
		$sql = 'UPDATE	cv_cms_reg
				SET		in_amt		= \''.$inAmt.'\'
				,		link_stat	= \'9\'
				,		modify_yn	= \'Y\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		cms_no	= \''.$cmsNo.'\'
				AND		cms_dt	= \''.$cmsDt.'\'
				AND		seq		= \''.$cmsSeq.'\'';
	}else{
		$sql = 'UPDATE	cv_cms_reg
				SET		del_flag	= \'Y\'
				,		modify_yn	= \'Y\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		cms_no	= \''.$cmsNo.'\'
				AND		cms_dt	= \''.$cmsDt.'\'
				AND		seq		= \''.$cmsSeq.'\'';
	}

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '0.데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}


	//입금적용 삭제
	$sql = 'UPDATE	cv_cms_link
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$cmsNo.'\'
			AND		cms_dt	= \''.$cmsDt.'\'
			AND		cms_seq	= \''.$cmsSeq.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '1.데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}


	if ($mode == '1'){
		//청구금액 및 적용금액
		/*
		$sql = 'SELECT	org_no, yymm, SUM(acct_amt) AS acct_amt, SUM(link_amt) AS link_amt
				FROM	(
						SELECT	a.org_no, a.yymm, CASE WHEN a.yymm > \'201508\' THEN a.acct_amt ELSE SUM(IFNULL((SELECT amt FROM cv_svc_acct_amt WHERE org_no = a.org_no AND yymm <= a.yymm AND amt > 0 ORDER BY yymm LIMIT 1),0)) END AS acct_amt, 0 AS link_amt
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
		*/
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
							,\''.$cmsDt.'\'
							,\''.$cmsSeq.'\'
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
						 if ($debug) echo $conn->error_msg.chr(13).$conn->error_query;
						 exit;
					}


					//CMS 등록내역 상태 변경
					$sql = 'UPDATE	cv_cms_reg
							SET		link_stat = \''.$linkStat.'\'
							WHERE	org_no	= \''.$orgNo.'\'
							AND		cms_no	= \''.$cmsNo.'\'
							AND		cms_dt	= \''.$cmsDt.'\'
							AND		seq		= \''.$cmsSeq.'\'';

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
	}


	$conn->commit();

	include_once('../inc/_db_close.php');
?>