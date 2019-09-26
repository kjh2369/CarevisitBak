<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once("../inc/_page_list.php");

	$today	= Date('Ymd');
	$yymm	= $_POST['yymm'];
	$domain = $_POST['domain'];

	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$mgNm	= $_POST['mgNm'];
	$yymm	= str_replace('-','',$_POST['yymm']);
	$CMSno	= $_POST['CMSno'];
	//$CMS2	= $_POST['CMS2'];
	//$CMS1	= $_POST['CMS1'];
	//$CMS3	= $_POST['CMS3'];
	//$CMSX	= $_POST['CMSX'];
	$connGbn= $_POST['connGbn'];
	$use	= $_POST['use'];
	$contCom= $_POST['contCom'];
	$taxbill= $_POST['taxbill'];

	$contDT = $_POST['contDt'];

	$page	= $_POST['page'];

	$itemCnt = 20;
	$pageCnt = 20;

	if (!$yymm) $yymm = Date('Ym');
	if (!$page) $page = 1;

	$bfYm = $myF->dateAdd('month', -1, $yymm.'01', 'Ym');


	//과금내역
	$sql = 'SELECT	a.*, CASE WHEN acct_amt + tmp_amt > 0 THEN 1 ELSE (SELECT COUNT(*) FROM cv_svc_fee WHERE org_no = a.org_no AND use_yn = \'Y\' AND acct_yn = \'Y\' AND del_flag = \'N\' AND LEFT(from_dt,6) <= \''.$yymm.'\' AND LEFT(to_dt,6) >= \''.$yymm.'\') END AS fee_cnt
			FROM	(
					SELECT	org_no, SUM(acct_amt) AS acct_amt, SUM(tmp_amt) AS tmp_amt
					FROM	cv_svc_acct_list
					WHERE	yymm = \''.$yymm.'\'
					GROUP	BY org_no
			) AS a';

	$feeList = $conn->_fetch_array($sql, 'org_no');


	$wsl = '';

	if ($orgNo) $wls .= ' AND m00_mcode LIKE \''.$orgNo.'%\'';
	if ($orgNm) $wls .= ' AND m00_store_nm LIKE \'%'.$orgNm.'%\'';
	if ($mgNm) $wls .= ' AND m00_mname LIKE \'%'.$mgNm.'%\'';

	$sl = '	SELECT	DISTINCT m00_mcode AS org_no
			,		m00_store_nm AS org_nm
			,		m00_domain AS domain
			,		m00_mname AS mg_nm
			,		e.mobile AS phone
			,		m00_caddr1 AS addr
			,		m00_caddr2 AS addr_dtl
			,		m00_start_date AS start_dt
			,		m00_cont_date AS cont_dt
			,		m00_ccode AS biz_no
			,		m00_email AS email
			,		a.from_dt
			,		a.to_dt
			,		a.rs_cd, a.rs_dtl_cd, a.pop_yn, a.adjust_fee_yn, a.taxbill_yn
			FROM	m00center
			INNER	JOIN	cv_reg_info AS a
					ON		a.org_no = m00_mcode
					AND		CASE WHEN a.rs_cd = \'4\' AND LEFT(cont_dt, 6) > \''.$yymm.'\' THEN 0 ELSE 1 END = 1';

	if ($taxbill){
		$sl .= '	AND		a.taxbill_yn = \''.$taxbill.'\'';
	}

	if ($contDt){
		$sl .= ' AND a.from_dt <= \''.$contDt.'\'
				 AND a.to_dt >= \''.$contDt.'\'';
	}else{
		if ($use == 'USE'){
			/*$sl .= '	AND		a.from_dt <= DATE_FORMAT(NOW(),\'%Y%m%d\')
						AND		a.to_dt >= DATE_FORMAT(NOW(),\'%Y%m%d\')';*/
			$sl .= '	AND		\''.$yymm.'\' BETWEEN LEFT(a.from_dt, 6) AND LEFT(a.to_dt, 6)';
		}
	}

	if ($connGbn == 'N'){
		$sl .= '	AND		a.rs_cd = \'3\'';
	}else if ($connGbn == 'S'){
		$sl .= '	AND		a.rs_cd != \'3\'';
	}

	if ($CMSno || $CMS2 == 'Y' || $CMS1 == 'Y' || $CMS3 == 'Y'){
		/*
		$sl .= '
			INNER	JOIN	cv_cms_list AS b
					ON		b.org_no = m00_mcode';

		if ($CMSno) $sl .= ' AND b.cms_no = \''.$CMSno.'\'';

		if ($CMS2 == 'Y' || $CMS1 == 'Y' || $CMS3 == 'Y'){
			$sl .= ' AND CASE ';

			if ($CMS2 == 'Y') $sl .= ' WHEN cms_com = \'2\' THEN 1 ';
			if ($CMS1 == 'Y') $sl .= ' WHEN cms_com = \'1\' THEN 1 ';
			if ($CMS3 == 'Y') $sl .= ' WHEN cms_com = \'3\' THEN 1 ';

			$sl .= ' ELSE 0 END > 0';
		}
		*/
		$sl .= '
			INNER	JOIN (
					SELECT	org_no, cms_no, cms_com
					FROM	cv_bill_info
					WHERE	del_flag = \'N\'
					AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
					AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')';

			if ($CMSno) $sl .= ' AND cms_no = \''.$CMSno.'\'';

			if ($CMS2 == 'Y' || $CMS1 == 'Y' || $CMS3 == 'Y'){
				$sl .= ' AND CASE ';

				if ($CMS2 == 'Y') $sl .= ' WHEN cms_com = \'2\' THEN 1 ';
				if ($CMS1 == 'Y') $sl .= ' WHEN cms_com = \'1\' THEN 1 ';
				if ($CMS3 == 'Y') $sl .= ' WHEN cms_com = \'3\' THEN 1 ';

				$sl .= ' ELSE 0 END > 0';
			}

		$sl .= '	) AS b
					ON		b.org_no = m00_mcode';
	}

	if ($CMSX == 'Y'){
		/*
		$sl .= '
			LEFT	JOIN	cv_cms_list AS c
					ON		c.org_no = m00_mcode';

		$wls .= ' AND IFNULL(c.cms_no,\'\') = \'\'';
		*/
		$sl .= '
			LEFT	JOIN (
					SELECT	org_no, bill_gbn
					FROM	cv_bill_info
					WHERE	del_flag = \'N\'
					AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
					AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')
					) AS c
					ON		c.org_no = m00_mcode';
		$wls .= ' AND IFNULL(c.bill_gbn,\'2\') = \'2\'';
	}

	if ($contCom){
		if ($contCom == 'X'){
			$sl .= '
			LEFT	JOIN	cv_reg_info AS d
					ON		d.org_no	 = m00_mcode
					AND		d.from_dt	<= \''.$today.'\'
					AND		d.to_dt		>= \''.$today.'\'';

			$wls .= ' AND d.cont_com IS NULL';
		}else{
			$sl .= '
			INNER	JOIN	cv_reg_info AS d
					ON		d.org_no	 = m00_mcode
					AND		d.from_dt	<= \''.$today.'\'
					AND		d.to_dt		>= \''.$today.'\'
					AND		d.cont_com	 = \''.$contCom.'\'';
		}
	}

	$sl .= '
			LEFT	JOIN	mst_manager AS e
					ON		e.org_no = m00_mcode';

	if ($domain){
		$sl .= '
			WHERE	m00_domain = \''.$domain.'\''.$wls;
	}else{
		$sl .= '
			WHERE	m00_mcode != \'\''.$wls;
	}

	$sl .= ' AND m00_mkind = \'0\'';

	$sql = 'SELECT	COUNT(DISTINCT org_no)
			FROM	('.$sl.') AS a';

	$totCnt = $conn->get_data($sql);

	#echo '<tr><td colspan="10">'.nl2br($sql).'</td><tr>';

	// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
	if ($totCnt < (IntVal($page) - 1) * $itemCnt){
		$page = 1;
	}


	$params = array(
		'curMethod'		=> 'post',
		'curPage'		=> 'javascript:lfSearch',
		'curPageNum'	=> $page,
		'pageVar'		=> 'page',
		'extraVar'		=> '',
		'totalItem'		=> $totCnt,
		'perPage'		=> $pageCnt,
		'perItem'		=> $itemCnt,
		'prevPage'		=> '[이전]',
		'nextPage'		=> '[다음]',
		'prevPerPage'	=> '[이전'.$pageCnt.'페이지]',
		'nextPerPage'	=> '[다음'.$pageCnt.'페이지]',
		'firstPage'		=> '[처음]',
		'lastPage'		=> '[끝]',
		'pageCss'		=> 'page_list_1',
		'curPageCss'	=> 'page_list_2'
	);

	$pageCount = (intVal($page) - 1) * $itemCnt;


	$sql = 'SELECT	org_no, org_nm, domain, mg_nm, phone, addr, addr_dtl, GROUP_CONCAT(DISTINCT cont_dt) AS cont_dt, start_dt, from_dt, to_dt, cont_com, rs_cd, rs_dtl_cd, biz_no, pop_yn, email, adjust_fee_yn, taxbill_yn
			FROM	(
					SELECT	a.org_no, a.org_nm, a.domain, a.mg_nm, a.phone, a.addr, a.addr_dtl, a.biz_no
					,		CASE WHEN b.cont_dt != \'\' THEN b.cont_dt ELSE a.cont_dt END AS cont_dt
					,		CASE WHEN b.start_dt != \'\' THEN b.start_dt ELSE a.start_dt END AS start_dt
					,		CASE WHEN b.from_dt != \'\' AND b.to_dt != \'\' THEN b.from_dt ELSE a.from_dt END AS from_dt
					,		CASE WHEN b.from_dt != \'\' AND b.to_dt != \'\' THEN b.to_dt ELSE a.to_dt END AS to_dt
					,		b.cont_com, a.rs_cd, a.rs_dtl_cd, a.pop_yn, a.email, a.adjust_fee_yn, a.taxbill_yn
					FROM	('.$sl.') AS a
					LEFT	JOIN	cv_reg_info AS b
							ON		b.org_no	 = a.org_no
							AND		b.from_dt	<= \''.($contDt ? $contDt : $today).'\'
							AND		b.to_dt		>= \''.($contDt ? $contDt : $today).'\'
					) AS a
			GROUP	BY	org_no
			ORDER	BY	org_nm';

	if (!$IsExcelClass && !$IsExcel){
		$sql .= '
			LIMIT	'.$pageCount.','.$itemCnt;
	}

	//echo '<tr><td colspan="16">'.nl2br($sql).'</td></tr>';

	$contCom = Array('1'=>'굿이오스', '2'=>'지케어', '3'=>'케어비지트');
	$data = $conn->_fetch_array($sql);

	if (is_array($data)){
		$no = 1;

		foreach($data as $tmpIdx => $R){
			$orgNo = $R['org_no'];

			//CMS
			/*
			$sql = 'SELECT	cms_no, cms_com
					FROM	cv_cms_list
					WHERE	org_no = \''.$orgNo.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count($sql);

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['cms_no']) $cmsNo[$row['cms_com']] .= ($cmsNo[$row['cms_com']] ? ', ' : '').$row['cms_no'];
			}

			$conn->row_free();
			*/
			$sql = 'SELECT	bill_gbn, cms_no, cms_com
					FROM	cv_bill_info
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		del_flag = \'N\'
					AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
					AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count($sql);

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['bill_gbn'] == '1'){
					$cmsNo[$row['cms_com']] .= ($cmsNo[$row['cms_com']] ? ', ' : '').$row['cms_no'];
				}else{
					$cmsNo['4'] = '무';
				}
			}

			$conn->row_free();

			/*
			//기본요금
			$sql = 'SELECT	SUM(stnd_cost)
					FROM	cv_svc_fee
					WHERE	org_no = \''.$orgNo.'\'
					AND		LEFT(from_dt,6) <= \''.$yymm.'\'
					AND		LEFT(to_dt,6)	>= \''.$yymm.'\'
					AND		acct_yn = \'Y\'
					AND		use_yn	= \'Y\'
					AND		del_flag= \'N\'';
			$stndAmt = $conn->get_data($sql);
			*/
			$sql = 'SELECT	SUM(CASE WHEN CASE WHEN tmp_amt > 0 THEN tmp_amt ELSE acct_amt END - dis_amt > 0 THEN CASE WHEN tmp_amt > 0 THEN tmp_amt ELSE acct_amt END - dis_amt ELSE 0 END) AS amt
					FROM	cv_svc_acct_list
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$yymm.'\'';
			$stndAmt = $conn->get_data($sql);

			//수급자수
			$sql = 'SELECT	COUNT(DISTINCT m03_jumin)
					FROM	m03sugupja
					WHERE	m03_ccode = \''.$orgNo.'\'';
			$clientCnt = $conn->get_data($sql);

			//일정수
			/*
			$sql = 'SELECT	COUNT(DISTINCT t01_jumin)
					FROM	t01iljung
					WHERE	t01_ccode = \''.$orgNo.'\'
					AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
					AND		t01_del_yn = \'N\'';
			$iljungCnt = $conn->get_data($sql);
			*/
			$sql = 'SELECT	stnd AS tot, stnd - family - bath - nurse AS stnd, family, bath, nurse
					FROM	(
							SELECT	COUNT(jumin) AS stnd
							,		SUM(CASE WHEN care_family_cnt = care_stnd_cnt + care_family_cnt + bath_cnt + nurse_cnt THEN 1 ELSE 0 END) AS family
							,		SUM(CASE WHEN bath_cnt = care_stnd_cnt + care_family_cnt + bath_cnt + nurse_cnt THEN 1 ELSE 0 END) AS bath
							,		SUM(CASE WHEN nurse_cnt = care_stnd_cnt + care_family_cnt + bath_cnt + nurse_cnt THEN 1 ELSE 0 END) AS nurse
							FROM	(
									SELECT	t01_jumin AS jumin
									,		SUM(CASE WHEN t01_svc_subcode = \'200\' AND t01_toge_umu = \'Y\' THEN 1 ELSE 0 END) AS care_stnd_cnt
									,		SUM(CASE WHEN t01_svc_subcode = \'200\' AND t01_toge_umu != \'Y\' THEN 1 ELSE 0 END) AS care_family_cnt
									,		SUM(CASE WHEN t01_svc_subcode = \'500\' THEN 1 ELSE 0 END) AS bath_cnt
									,		SUM(CASE WHEN t01_svc_subcode = \'800\' THEN 1 ELSE 0 END) AS nurse_cnt
									FROM	t01iljung
									WHERE	t01_ccode  = \''.$orgNo.'\'
									AND		t01_mkind  = \'0\'
									AND		t01_del_yn = \'N\'
									AND		LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
									GROUP	BY t01_jumin
									) AS a
							) AS a';

			$iljungCnt = $conn->get_array($sql);

			$sql = 'SELECT	SUM(CASE WHEN svc_gbn = \'1\' AND svc_cd = \'14\' THEN 1 ELSE 0 END) AS dan
					,		SUM(CASE WHEN svc_gbn = \'1\' AND svc_cd = \'21\' THEN 1 ELSE 0 END) AS care
					,		SUM(CASE WHEN svc_gbn = \'1\' AND svc_cd = \'22\' THEN 1 ELSE 0 END) AS old
					,		SUM(CASE WHEN svc_gbn = \'1\' AND svc_cd = \'23\' THEN 1 ELSE 0 END) AS baby
					,		SUM(CASE WHEN svc_gbn = \'1\' AND svc_cd = \'24\' THEN 1 ELSE 0 END) AS dis
					,		SUM(CASE WHEN svc_gbn = \'1\' AND svc_cd = \'41\' THEN 1 ELSE 0 END) AS care_s
					,		SUM(CASE WHEN svc_gbn = \'1\' AND svc_cd = \'42\' THEN 1 ELSE 0 END) AS care_r
					,		SUM(CASE WHEN svc_gbn = \'2\' AND svc_cd = \'11\' THEN 1 ELSE 0 END) AS smart
					,		SUM(CASE WHEN svc_gbn = \'2\' AND svc_cd = \'21\' THEN 1 ELSE 0 END) AS sms
					FROM	cv_svc_fee
					WHERE	org_no = \''.$orgNo.'\'
					AND		del_flag = \'N\'
					AND		\''.$yymm.'\' BETWEEN LEFT(from_dt, 6) AND LEFT(to_dt, 6)';

			$svcFee = $conn->get_array($sql);

			//현재청구
			$sql = 'SELECT	amt
					FROM	cv_svc_acct_amt
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		yymm	<= \''.$yymm.'\'
					ORDER	BY yymm DESC
					LIMIT	1';
			$nowAmt = $conn->get_data($sql);


			if ($IsExcelClass){
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH);
				lfDrawText($sheet, 'A', $rowNo, $pageCount + ($tmpIdx + 1));
				lfDrawText($sheet, 'B', $rowNo, $R['org_no'], Array('H'=>'L'));
				lfDrawText($sheet, 'C', $rowNo, $R['org_nm'], Array('H'=>'L'));
				lfDrawText($sheet, 'D', $rowNo, $R['domain'], Array('H'=>'L'));
				lfDrawText($sheet, 'E', $rowNo, $R['mg_nm'], Array('H'=>'L'));
				lfDrawText($sheet, 'F', $rowNo, $myF->phoneStyle($R['phone'],'.'));
				lfDrawText($sheet, 'G', $rowNo, $R['addr'].' '.$R['addr_dtl'], Array('H'=>'L'));
				lfDrawText($sheet, 'H', $rowNo, $myF->dateStyle($R['start_dt'],'.'));
				lfDrawText($sheet, 'I', $rowNo, $myF->dateStyle($R['cont_dt'],'.'));
				lfDrawText($sheet, 'J', $rowNo, $myF->dateStyle($R['from_dt'],'.'));
				lfDrawText($sheet, 'K', $rowNo, $myF->dateStyle($R['to_dt'],'.'));
				lfDrawText($sheet, 'L', $rowNo, $contCom[$R['cont_com']], Array('H'=>'L'));
				lfDrawText($sheet, 'M', $rowNo, $cmsNo['2']);
				lfDrawText($sheet, 'N', $rowNo, $cmsNo['1']);
				lfDrawText($sheet, 'O', $rowNo, $cmsNo['3']);
				lfDrawText($sheet, 'P', $rowNo, !$cmsNo ? '무' : '');
				lfDrawText($sheet, 'Q', $rowNo, $R['org_no'], Array('H'=>'L'));
				lfDrawText($sheet, 'R', $rowNo, $R['org_no'], Array('H'=>'L', 'format'=>'text'));
				lfDrawText($sheet, 'S', $rowNo, $myF->bizStyle($R['biz_no']), Array('H'=>'L'));
				lfDrawText($sheet, 'T', $rowNo, number_format($iljungCnt['stnd'] + $iljungCnt['family']), Array('H'=>'R'));
				lfDrawText($sheet, 'U', $rowNo, number_format($iljungCnt['stnd']), Array('H'=>'R'));
				lfDrawText($sheet, 'V', $rowNo, number_format($iljungCnt['family']), Array('H'=>'R'));
				lfDrawText($sheet, 'W', $rowNo, number_format($iljungCnt['bath']), Array('H'=>'R'));
				lfDrawText($sheet, 'X', $rowNo, number_format($iljungCnt['nurse']), Array('H'=>'R'));
				lfDrawText($sheet, 'Y', $rowNo, $svcFee['sms'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'Z', $rowNo, $svcFee['dan'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'AA', $rowNo, $svcFee['care'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'AB', $rowNo, $svcFee['old'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'AC', $rowNo, $svcFee['baby'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'AD', $rowNo, $svcFee['dis'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'AE', $rowNo, $svcFee['care_s'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'AF', $rowNo, $svcFee['care_r'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'AG', $rowNo, $svcFee['smart'] > 0 ? 'Y' : '');
				lfDrawText($sheet, 'AH', $rowNo, number_format($nowAmt), Array('H'=>'R'));
				lfDrawText($sheet, 'AI', $rowNo, number_format($stndAmt), Array('H'=>'R'));
				lfDrawText($sheet, 'AJ', $rowNo, number_format($nowAmt - $stndAmt), Array('H'=>'R', 'color'=>$nowAmt - $stndAmt < 0 ? 'FF0000' : '000000'));
				//lfDrawText($sheet, 'AK', $rowNo, $R['pop_yn']);
				if ($feeList[$orgNo]['acct_amt'] + $feeList[$orgNo]['tmp_amt'] > 0){
					lfDrawText($sheet, 'AK', $rowNo, '과금');
				}else if ($feeList[$orgNo]['fee_cnt'] > 0){
					lfDrawText($sheet, 'AK', $rowNo, '기간 외', Array('color'=>'0000FF'));
				}else{
					lfDrawText($sheet, 'AK', $rowNo, '미과금', Array('color'=>'FF0000'));
				}
				lfDrawText($sheet, 'AL', $rowNo, $R['adjust_fee_yn'] == 'Y' ? 'Y' : '');
				lfDrawText($sheet, 'AM', $rowNo, $R['email'], Array('H'=>'L'));
				lfDrawText($sheet, 'AN', $rowNo, $R['taxbill_yn'] == 'Y' ? '발행' : '미발행', Array('H'=>'L'));
			}else{
				if ($IsExcel){?>
					<tr><?
					$cls = '';
					$style = '';
				}else{?>
					<tr onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='WHITE';"><?
					$cls = 'bottom';

					if ($tmpIdx > 0){
						$style = 'border-top:1px solid #CCCCCC;';
					}else{
						$style = '';
					}
				}?>
				<td class="center <?=$cls;?>" style="<?=$style?>"><?=$pageCount + ($tmpIdx + 1);?></td>
				<td class="<?=$cls;?>" style="<?=$style?>"><div class="left nowrap" style="width:90px; text-align:left;"><?=$R['org_no'];?></div></td>
				<td class="<?=$cls;?>" style="<?=$style?>"><div class="left"><?=$R['org_nm'];?></div></td>
				<td class="<?=$cls;?>" style="<?=$style?>"><div class="left"><?=$R['domain'];?></div></td>
				<td class="<?=$cls;?>" style="<?=$style?>"><div class="left"><?=$R['mg_nm'];?></div></td>
				<td class="<?=$cls;?>" style="<?=$style?>"><div class="left"><?=$myF->phoneStyle($R['phone'],'.');?></div></td><?
				if ($IsExcel){?>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="left"><?=$R['addr'].' '.$R['addr_dtl'];?></div></td><?
				}?>
				<td class="center <?=$cls;?>" style="<?=$style?>"><?=$myF->dateStyle($R['start_dt'],'.');?></td>
				<td class="center <?=$cls;?>" style="<?=$style?>"><?=$myF->dateStyle($R['cont_dt'],'.');?></td><?
				if ($IsExcel){?>
					<td class="center <?=$cls;?>" style="<?=$style?>"><?=$myF->dateStyle($R['from_dt'],'.');?></td>
					<td class="center <?=$cls;?>" style="<?=$style?>"><?=$myF->dateStyle($R['to_dt'],'.');?></td>
					<td class="center <?=$cls;?>" style="<?=$style?>"><?=$contCom[$R['cont_com']];?></td><?
				}else{
					if ($R['rs_cd'] == '4'){
						$period = '<span style="color:red;">';
						if ($R['rs_dtl_cd'] != '06'){
							$period .= '해지';
						}else{
							$period .= $myF->dateStyle($R['from_dt'],'.').' ~ '.$myF->dateStyle($R['to_dt'],'.');
						}
						$period .= '</span>';
					}else{
						$period = $myF->dateStyle($R['from_dt'],'.').' ~ '.$myF->dateStyle($R['to_dt'],'.');
					}?>
					<td class="center <?=$cls;?>" style="<?=$style?>"><?=$period;?></td><?
				}?>
				<td class="center <?=$cls;?>" style="<?=$style?>"><?=$cmsNo['2'];?></td>
				<td class="center <?=$cls;?>" style="<?=$style?>"><?=$cmsNo['1'];?></td>
				<td class="center <?=$cls;?>" style="<?=$style?>"><?=$cmsNo['3'];?></td>
				<td class="center <?=$cls;?>" style="<?=$style?>"><?=!$cmsNo ? '무' : '';?></td><?
				if ($IsExcel){?>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$R['org_no'];?></td>
					<td class="<?=$cls;?>" style="<?=$style?> mso-number-format:\@;"><?=$R['org_no'];?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$myF->bizStyle($R['biz_no']);?></td>
					<!--td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($clientCnt);?></div></td-->
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($iljungCnt['stnd'] + $iljungCnt['family']);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($iljungCnt['stnd']);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($iljungCnt['family']);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($iljungCnt['bath']);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($iljungCnt['nurse']);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['sms'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['dan'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['care'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['old'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['baby'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['dis'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['care_s'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['care_r'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$svcFee['smart'] > 0 ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($nowAmt);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($stndAmt);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right" style="color:<?=$nowAmt - $stndAmt == 0 ? '' : $nowAmt - $stndAmt > 0 ? '' : 'RED';?>;"><?=number_format($nowAmt - $stndAmt);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?> text-align:center;"><?=$R['pop_yn'];?></td>
					<td class="<?=$cls;?>" style="<?=$style?> text-align:center;"><?=$R['adjust_fee_yn'] == 'Y' ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$R['email'];?></td><?
				}else{?>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($stndAmt);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($nowAmt);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right" style="color:<?=$nowAmt - $stndAmt == 0 ? '' : $nowAmt - $stndAmt > 0 ? '' : 'RED';?>;"><?=number_format($nowAmt - $stndAmt);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($clientCnt);?></div></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"><?=number_format($iljungCnt['tot']);?></div></td>
					<td class="center <?=$cls;?>" style="<?=$style?>"><?//=$R['pop_yn'];
						if ($feeList[$orgNo]['acct_amt'] + $feeList[$orgNo]['tmp_amt'] > 0){
							echo '과금';
						}else if ($feeList[$orgNo]['fee_cnt'] > 0){
							echo '<span style="color:blue;">기간 외</span>';
						}else{
							echo '<span style="color:red;">미과금</span>';
						}?>
					</td>
					<td class="center <?=$cls;?>" style="<?=$style?>"><?=$R['adjust_fee_yn'] == 'Y' ? 'Y' : '';?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><?=$R['email'];?></td>
					<td class="<?=$cls;?>" style="<?=$style?>"><div class="right"></div></td><?
				}?>
				</tr><?
			}

			Unset($cmsNo);

			$no ++;
		}

		if (!$IsExcelClass && !$IsExcel){
			$paging = new YsPaging($params);
			$pageList = $paging->returnPaging();
		}
	}

	include_once('../inc/_db_close.php');

	if ($IsExcel) exit;
	if (!$IsExcelClass){?>
		<script type="text/javascript">
			$('#ID_ROW_PAGELIST').html('<?=$pageList;?>');
		</script><?
	}?>