<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$acctYm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('month', -1, $acctYm.'01', 'Ym');
	$gbn	= $_POST['gbn'];

	if ($gbn == 'IN_APPLY'){
		//�̳���
		$nonpay = $_POST['nonpay'];

		//���Ա� �Ա�����
		/*
		$sql = 'SELECT	a.cms_no, a.cms_dt, a.cms_seq, b.in_amt - SUM(a.link_amt) AS over_amt
				FROM	cv_cms_link AS a
				INNER	JOIN	cv_cms_reg AS b
						ON		b.org_no	= a.org_no
						AND		b.cms_no	= a.cms_no
						AND		b.cms_dt	= a.cms_dt
						AND		b.seq		= a.cms_seq
						AND		b.del_flag	= \'N\'
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.del_flag	= \'N\'
				GROUP	BY a.cms_no, a.cms_dt, a.cms_seq
				HAVING	over_amt > 0
				ORDER	BY cms_dt, cms_seq';
		*/
		$sql = 'SELECT	a.cms_no, a.cms_dt, a.seq AS cms_seq, a.in_amt - SUM(IFNULL(b.link_amt,0)) As over_amt
				FROM	cv_cms_reg AS a
				LEFT	JOIN	cv_cms_link AS b
						ON		b.org_no	= a.org_no
						AND		b.cms_no	= a.cms_no
						AND		b.cms_dt	= a.cms_dt
						AND		b.cms_seq	= a.seq
						AND		b.del_flag	= \'N\'
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.del_flag	= \'N\'
				GROUP	BY a.cms_no, a.cms_dt, a.seq
				HAVING	over_amt > 0
				ORDER	BY cms_dt, cms_seq';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['over_amt'] >= $nonpay){
				//�Ա�����
				$linkAmt = $nonpay;
				$nonpay = 0;
			}else{
				//�Ա�����
				$linkAmt = $row['over_amt'];
				$nonpay = $row['over_amt'] - $linkAmt;
			}

			if ($row['over_amt'] - $linkAmt == 0){
				$stat = '1';
			}else{
				$stat = '3';
			}

			//����
			$sql = 'SELECT	IFNULL(MAX(seq),0)+1
					FROM	cv_cms_link
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$yymm.'\'';

			$seq = $conn->get_data($sql);

			//�Աݿ��᳻��
			$sql = 'INSERT INTO cv_cms_link (org_no,yymm,seq,acct_ym,cms_no,cms_dt,cms_seq,link_amt,link_stat,insert_id,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$yymm.'\'
					,\''.$seq.'\'
					,\''.$acctYm.'\'
					,\''.$row['cms_no'].'\'
					,\''.$row['cms_dt'].'\'
					,\''.$row['cms_seq'].'\'
					,\''.$linkAmt.'\'
					,\'1\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';

			$query[] = $sql;

			//�Աݵ�ϻ��º���
			$sql = 'UPDATE	cv_cms_reg
					SET		link_stat	= \''.$stat.'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		cms_no		= \''.$row['cms_no'].'\'
					AND		cms_dt		= \''.$row['cms_dt'].'\'
					AND		seq			= \''.$row['cms_seq'].'\'
					AND		del_flag	= \'N\'';

			$query[] = $sql;
		}

		$conn->row_free();


	}else if ($gbn == 'IN_CANCEL'){
		//�Ա��������
		$sql = 'SELECT	seq, cms_no, cms_dt, cms_seq, link_amt
				FROM	cv_cms_link
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$yymm.'\'
				AND		del_flag= \'N\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$sql = 'UPDATE	cv_cms_link
					SET		del_flag	= \'Y\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$yymm.'\'
					AND		seq		= \''.$row['seq'].'\'';

			$query[] = $sql;

			$linkAmt[$row['cms_no']][$row['cms_dt']][$row['cms_seq']]['amt'] += $row['link_amt'];
		}

		$conn->row_free();

		if (is_array($linkAmt)){
			foreach($linkAmt as $cmsNo => $R1){
				foreach($R1 as $cmsDt => $R2){
					foreach($R2 as $cmsSeq => $R3){
						$sql = 'UPDATE	cv_cms_reg
								SET		link_stat	= CASE WHEN in_amt = \''.$R3['amt'].'\' THEN \'9\'  WHEN in_amt > \''.$R3['amt'].'\' THEN \'3\' ELSE \'1\' END
								,		update_id	= \''.$_SESSION['userCode'].'\'
								,		update_dt	= NOW()
								WHERE	org_no	= \''.$orgNo.'\'
								AND		cms_no	= \''.$cmsNo.'\'
								AND		cms_dt	= \''.$cmsDt.'\'
								AND		seq		= \''.$cmsSeq.'\'
								AND		del_flag= \'N\'';

						$query[] = $sql;
					}
				}
			}
		}


	}else{
	}

	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo '����Ÿ ó�� �� ������ �߻��Ͽ����ϴ�. ��� �� �ٽ� �õ��Ͽ� �ֽʽÿ�.';
				 exit;
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>