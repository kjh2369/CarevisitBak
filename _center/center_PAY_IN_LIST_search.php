<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_page_list.php');

	$orgNo	= $_POST['orgNo'];
	$orgName= $_POST['orgName'];
	$inGbn	= $_POST['inGbn'];
	$inBank	= $_POST['inBank'];
	$fromDt	= str_replace('-', '', $_POST['fromDt']);
	$toDt	= str_replace('-', '', $_POST['toDt']);
	$outStat= $_POST['outStat'];
	$contCom= $_POST['contCom'];
	$drawMode = $_POST['drawMode'];
	$page = $_POST['page'];

	if ($IsExcel) $drawMode = 'ALL';

	if (!$fromDt) $fromDt = '10000101';
	if (!$toDt) $toDt = '99991231';

	$itemCnt = 20;
	$pageCnt = 10;

	if (!$page) $page = 1;

	$sql = 'SELECT	__COLUMN__
			FROM	cv_pay_in AS a
			INNER	JOIN	m00center
					ON		m00_mcode = a.org_no
					AND		m00_mkind = (SELECT MIN(m00_mkind) FROM m00center WHERE m00_mcode = a.org_no)
			WHERE	a.issue_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
			AND		a.del_flag = \'N\'';

	if ($orgNo){//기관기호
		$sql .= ' AND a.org_no LIKE \''.$orgNo.'\'';
	}

	if ($orgName){//기관명
		$sql .= ' AND m00_store_nm LIKE \'%'.$orgName.'%\'';
	}

	if ($inGbn){//입금구분 1:CMS, 2:무통장
		$sql .= ' AND a.in_gbn = \''.$inGbn.'\'';
	}

	if ($inBank){//입금은행
		$sql .= ' AND a.in_bank = \''.$inBank.'\'';
	}

	if ($outStat == '1'){//출금상태
		$sql .= ' AND INSTR(a.out_stat, \'출금성공\') > 0';
	}else if ($outStat == '2'){
		$sql .= ' AND INSTR(a.out_stat, \'출금실패\') > 0';
	}else if ($outStat == '3'){
		$sql .= ' AND a.out_stat = \'\'';
	}

	if ($contCom){//계약회사
		$sql .= ' AND a.cont_com = \''.$contCom.'\'';
	}

	if ($drawMode != 'ALL'){
		$totCnt = $conn->get_data(str_replace('__COLUMN__', ' COUNT(*) ', $sql));

		if ($totCnt < (IntVal($page) - 1) * $itemCnt) $page = 1;
		if ($IsExcel) $style = 'border:0.5pt solid BLACK;';

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
	}


	$sql = str_replace('__COLUMN__', ' DISTINCT a.org_no, m00_store_nm AS org_name, a.issue_dt, a.issue_seq, a.claim_dt, a.issue_time, a.in_bank, a.in_amt, a.out_stat, a.out_bank, a.remark, CASE WHEN a.in_gbn = \'1\' THEN \'CMS\' ELSE \'무통장\' END AS in_gbn ', $sql).' ORDER	BY issue_dt DESC, issue_time DESC, org_name, org_no';

	if ($drawMode != 'ALL') $sql .= ' LIMIT '.$pageCount.','.$itemCnt;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:center;"' : 'class="center"';?>><?=$isExcle ? $no : $pageCount + $i + 1;?></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:left;"' : 'class="left"';?>><div class="nowrap" style="width:75px; cursor:default;"><?=$row['org_no'];?></div></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:left;"' : 'class="left"';?>><div class="nowrap" style="width:85px; cursor:default;"><?=$row['org_name'];?></div></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:center;"' : 'class="center"';?>><?=$myF->dateStyle($row['claim_dt'], '.');?></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:center;"' : 'class="center"';?>><div style="cursor:default;" onclick="lfReg('<?=$row['org_no'];?>', '<?=$row['issue_dt'];?>', '<?=$row['issue_seq'];?>');"><?=$myF->dateStyle($row['issue_dt'], '.');?></div></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:center;"' : 'class="center"';?>><?=$myF->timeStyle($row['issue_time']);?></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:center;"' : 'class="center"';?>><?=$row['in_gbn'];?></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:right;"' : 'class="right"';?>><div style="cursor:default;" onclick="lfDtl('<?=$row['org_no'];?>', '<?=$row['issue_dt'];?>', '<?=$row['issue_seq'];?>');"><?=number_format($row['in_amt']);?></div></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:left;"' : 'class="left"';?>><div class="nowrap" style="width:65px;"><?=$row['out_stat'];?></div></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:left;"' : 'class="left"';?>><div class="nowrap" style="width:65px;"><?=$row['out_bank'];?></div></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:left;"' : 'class="left"';?>><div class="nowrap" style="width:65px;"><?=$row['in_bank'];?></div></td>
		<td <?=$IsExcel ? 'style="'.$style.' text-align:left;"' : 'class="left" onclick="lfModify(this, \''.$row['org_no'].'\', \''.$row['issue_dt'].'\', \''.$row['issue_seq'].'\');"';?>><?=$row['remark'];?></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	if ($drawMode != 'ALL'){
		$paging = new YsPaging($params);
		$pageList = $paging->returnPaging();
	}

	include_once('../inc/_db_close.php');

	if ($drawMode != 'ALL'){?>
		<script type="text/javascript">
			$('#PAGE_LIST').html('<?=$pageList;?>');
		</script><?
	}
?>