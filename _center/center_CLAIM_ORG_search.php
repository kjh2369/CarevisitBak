<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$mgNm	= $_POST['mgNm'];
	$popYn	= $_POST['popYn'];
	$page	= $_POST['page'];

	$itemCnt = 25;

	$bsl = 'SELECT	m00_mcode AS org_no, GROUP_CONCAT(DISTINCT m00_store_nm) AS org_nm
			FROM	m00center
			INNER	JOIN	cv_svc_acct_list AS b
					ON		b.org_no = m00_mcode
					AND		b.acct_ym= \''.$yymm.'\'
					AND		b.acct_amt != 0
			WHERE	m00_domain = \''.$company.'\'';

	if ($orgNo) $bsl .= ' AND m00_mcode LIKE \''.$orgNo.'%\'';
	if ($orgNm) $bsl .= ' AND m00_store_nm LIKE \'%'.$orgNm.'%\'';
	if ($mgNm) $bsl .= ' AND m00_mname LIKE \'%'.$mgNm.'%\'';

	$bsl .= '
			GROUP	BY m00_mcode';

	//echo '<tr><td colspan="20">'.nl2br($bsl).'</td></tr>';


	$sql = 'SELECT	COUNT(*)
			FROM	('.$bsl.') AS a';
	$totCnt = $conn->get_data($sql);

	if ($totCnt < (IntVal($page) - 1) * $itemCnt){
		//$page = 1;
		include_once('../inc/_db_close.php');
		exit;
	}

	$pageCnt = (intVal($page) - 1) * $itemCnt;


	$sql = 'SELECT	a.org_no, a.org_nm
			FROM	('.$bsl.') AS a
			GROUP	BY a.org_no
			ORDER	BY org_nm';
			//LIMIT	'.$pageCnt.','.$itemCnt;

	//echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo	= $row['org_no'];
		$data[$orgNo] = Array('orgNm'=>$row['org_nm']);
	}

	$conn->row_free();


	if (is_array($data)){
		$taxCrGbn = Array('C'=>'청구', 'R'=>'영수');

		foreach($data as $orgNo => $R){
			//당월 청구금액
			$sql = 'SELECT	SUM(acct_amt) AS acct_amt
					FROM	cv_svc_acct_list
					WHERE	org_no	= \''.$orgNo.'\'
					AND		acct_ym = \''.$yymm.'\'';

			$data[$orgNo]['acctAmt'] = $conn->get_data($sql);


			//전월까지 미납분
			$sql = 'SELECT	SUM(acct_amt) AS acct_amt
					FROM	cv_svc_acct_list
					WHERE	org_no	= \''.$orgNo.'\'
					AND		acct_ym < \''.$yymm.'\'';

			$data[$orgNo]['unpaid'] = $conn->get_data($sql);


			//입금금액
			/*
			$sql = 'SELECT	SUM(CASE WHEN acct_ym = \''.$yymm.'\' THEN link_amt ELSE 0 END) AS now_amt
					,		SUM(CASE WHEN acct_ym < \''.$yymm.'\' THEN link_amt ELSE 0 END) AS old_amt
					,		MIN(cms_dt) AS cms_dt
					,		MIN(bank_dt) AS bank_dt
					FROM	cv_cms_link AS a
					WHERE	org_no	= \''.$orgNo.'\'
					AND		acct_ym <= \''.$yymm.'\'
					AND		del_flag= \'N\'
					AND		IFNULL(link_stat,\'1\') = \'1\'';

			$row = $conn->get_array($sql);

			$data[$orgNo]['DPT']['amt'] = $row['now_amt']; //당월 입금액

			if ($data[$orgNo]['DPT']['amt'] > 0){
				$data[$orgNo]['DPT']['dt']	= ($row['cms_dt'] ? $row['cms_dt'] : $row['bank_dt']); //입금일자
				$data[$orgNo]['DPT']['gbn']	= ($row['cms_dt'] ? 'CMS' : '무통장'); //입금구분
			}
			*/

			$sql = 'SELECT	SUM(CASE WHEN LEFT(cms_dt,6) = \''.$yymm.'\' THEN in_amt ELSE 0 END) AS now_amt
					,		SUM(CASE WHEN LEFT(cms_dt,6) < \''.$yymm.'\' THEN in_amt ELSE 0 END) AS old_amt
					,		MAX(in_dt) AS in_dt
					FROM	cv_cms_reg
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'
					AND		LEFT(cms_dt,6) <= \''.$yymm.'\'';

			$row = $conn->get_array($sql);

			$data[$orgNo]['DPT']['amt'] = $row['now_amt']; //당월 입금액

			if ($data[$orgNo]['DPT']['amt'] > 0){
				$data[$orgNo]['DPT']['dt']	= $row['in_dt']; //입금일자
			}

			$data[$orgNo]['oldDpt']	 = $row['old_amt']; //전월까지 입금액
			$data[$orgNo]['unpaid']	-= $data[$orgNo]['oldDpt']; //전월까지 미납금액
			$data[$orgNo]['nonPay']	 = $R['acctAmt'] - $data[$orgNo]['oldDpt'];


			//세금계산서 발행이력
			$sql = 'SELECT	iss_dt, cr_gbn
					FROM	cv_tax_his
					WHERE	org_no	= \''.$orgNo.'\'
					AND		acct_ym	= \''.$yymm.'\'';

			$row = $conn->get_array($sql);

			$data[$orgNo]['TAX']['dt']	= $row['iss_dt'];
			$data[$orgNo]['TAX']['gbn']	= $taxCrGbn[$row['cr_gbn']];
		}



		$no = 0;

		foreach($data as $orgNo => $R){?>
			<tr>
				<td class="center"><?=$pageCnt + ($no + 1);?></td>
				<td class="center"><div class="left nowrap" style="width:150px;"><?=$R['orgNm'];?></div></td>
				<td class="center"><div class="left nowrap" style="width:90px;"><?=$orgNo;?></div></td>
				<td class="center" style="background-color:#EBF7FF;"><div class="right"><?=number_format($R['acctAmt']+$R['unpaid']);?></div></td>
				<td class="center" style="background-color:#EBF7FF;"><div class="right"><?=$R['acctAmt'] != 0 ? number_format($R['acctAmt']) : '';?></div></td>
				<td class="center" style="background-color:#EBF7FF;"><div class="right"><?=$R['unpaid'] != 0 ? number_format($R['unpaid']) : '';?></div></td>
				<td class="center" style="background-color:#ECEBFF;"><?=$myF->dateStyle($R['DPT']['dt'],'.');?></td>
				<td class="center" style="background-color:#ECEBFF;"><div class="right"><?=$R['DPT']['amt'] != 0 ? number_format($R['DPT']['amt']) : '';?></div></td>
				<td class="center" style="background-color:#FAEBFF;"><?=$myF->dateStyle($R['TAX']['dt'],'.');?></td>
				<td class="center" style="background-color:#FAEBFF;"><?=$R['TAX']['gbn'];?></td>
				<td class="last"><?
					if ($popYn != 'Y'){?>
						&nbsp;<a href="#" onclick="lfMenu('CLAIM_ORG_DTL','&orgNo=<?=$orgNo;?>&year=<?=$year;?>','Y');">▶</a><?
					}?>
				</td>
			</tr><?

			$no ++;
		}
	}

	include_once('../inc/_db_close.php');
?>