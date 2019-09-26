<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	
	$orgNo	= $_SESSION['userCenterCode'];

	$counselTypeStr = Array(
			'PROCESS'	=>'과정상담'
		,	'STRESS'	=>'불만 및 고충처리'
		,	'CASE'=>'사례관리회의'
	);

	$memberName	= $_POST['memberName'];
	$counseler	= $_POST['counseler'];
	$counselType= $_POST['counselType'];
	$fromDt		= $_POST['fromDt'];
	$toDt		= $_POST['toDt'];
	$orderBy	= $_POST['orderBy'];

	if (!$fromDt) $fromDt = $toDt;
	if (!$toDt) $toDt = $fromDt;

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_POST['page'];

	if (Empty($page)){
		$page = 1;
	}

	if ($counselType == 'ALL' || $counselType == 'PROCESS'){
		$query .= ($query ? ' UNION	ALL ' : '');
		$query .= '	SELECT	\'PROCESS\' AS type
					,		stress_dt AS dt
					,		stress_seq AS seq
					,		stress_ssn AS jumin
					,		stress_talker_nm AS mem_nm
					FROM	counsel_stress
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'';
	}

	if ($counselType == 'ALL' || $counselType == 'STRESS'){
		$query .= ($query ? ' UNION	ALL ' : '');
		$query .= '	SELECT	\'STRESS\' AS type
					,		stress_dt AS dt
					,		stress_seq AS seq
					,		stress_c_cd AS jumin
					,		stress_m_nm AS mem_nm
					FROM	counsel_client_stress
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'';
	}

	if ($counselType == 'ALL' || $counselType == 'CASE'){
		$query .= ($query ? ' UNION	ALL ' : '');
		$query .= '	SELECT	\'CASE\' AS type
					,		case_dt AS dt
					,		case_seq AS seq
					,		case_c_cd AS jumin
					,		case_m_nm AS mem_nm
					FROM	counsel_client_case
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'';
	}

	$sql = 'SELECT	DISTINCT
					counsel.type
			,		counsel.dt
			,	    counsel.seq
			,		mst.jumin AS jumin
			,		mst.name AS name
			,		counsel.mem_nm
			FROM	('.$query.') AS counsel
			INNER	JOIN	(
						SELECT	DISTINCT
								m02_yjumin AS jumin
						,		m02_yname AS name
						FROM	m02yoyangsa
						WHERE	m02_ccode = \''.$orgNo.'\'
					) AS mst
					ON		mst.jumin = counsel.jumin';

	if ($memberName){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	mst.name = \''.$memberName.'\'';
	}

	if ($counseler){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	counsel.mem_nm = \''.$counseler.'\'';
	}

	if ($fromDt && $toDt){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	counsel.dt >= \''.$fromDt.'\'';
		$wsl .= '	AND	counsel.dt <= \''.$toDt.'\'';
	}

	$sql .= $wsl;
	$wsl  = '';
	
	$conn->query($sql);
	$conn->fetch();

	$totCnt = $conn->row_count();

	$conn->row_free();

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

	$sql = 'SELECT	DISTINCT
					counsel.type
			,		counsel.dt
			,       counsel.seq
			,		mst.jumin AS jumin
			,		mst.name AS name
			,		counsel.mem_nm
			FROM	('.$query.') AS counsel
			LEFT	JOIN	(
						SELECT	DISTINCT
								m02_yjumin AS jumin
						,		m02_yname AS name
						FROM	m02yoyangsa
						WHERE	m02_ccode = \''.$orgNo.'\'
					) AS mst
					ON		mst.jumin = counsel.jumin';

	if ($memberName){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	mst.name = \''.$memberName.'\'';
	}

	if ($counseler){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	counsel.mem_nm = \''.$counseler.'\'';
	}

	if ($fromDt && $toDt){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	counsel.dt >= \''.$fromDt.'\'';
		$wsl .= '	AND	counsel.dt <= \''.$toDt.'\'';
	}

	$sql .= $wsl;
	$wsl  = '';

	$sql .= '
			ORDER	BY dt '.$orderBy.'
			LIMIT	'.$pageCount.','.$itemCnt;
	
	//if($debug) echo '<tr><td>'.nl2br($sql).'</td></tr>'; 
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			$bgcolor = '#FFFFFF';
		}else{
			$bgcolor = '#EFEFEF';
		}

		$yymm = substr(str_replace('-','', $row['dt']), 0, 6);

		?>
		<tr style="cursor:default; background-color:<?=$bgcolor;?>;" onmouseover="this.style.backgroundColor='#D9E5FF';" onmouseout="this.style.backgroundColor='<?=$bgcolor;?>';">
			<td class="center"><?=$pageCount + ($i + 1);?></td>
			<td class="center"><?=$myF->dateStyle($row['dt'],'.');?></td>
			<td class="center"><div class="left nowrap" style="width:90px;"><?=$row['name'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:90px;"><?=$row['mem_nm'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:130px;"><?=$counselTypeStr[$row['type']];?></div></td>
			<td class="center last">
				<div class="left">
					<span class="btn_pack m"><button type="button" onclick="lfReg('<?=$row['type'];?>','<?=$orgNo?>','<?=$ed->en($row['jumin']);?>','<?=$yymm;?>','<?=number_format($row['seq']);?>');">수정</button></span>
					<span class="btn_pack m"><button type="button" onclick="lfShow('<?=$orgNo?>','<?=number_format($row['seq']);?>','<?=$ed->en($row['jumin']);?>','<?=strtolower($row['type']);?>','<?=$yymm;?>','MEMBER');">출력</button></span>
					<span class="btn_pack m"><button type="button" onclick="_member_proc_counsel_delete('<?=strtolower($row['type']);?>','<?=$yymm;?>','<?=number_format($row['seq']);?>','<?=$ed->en($row['jumin'])?>','Y');">삭제</button></span>
				</div>
			</td>
		</tr><?
	}

	$conn->row_free();

	$paging = new YsPaging($params);
	$pageList = $paging->returnPaging();?>
	<tr>
		<td class="center bottom last" colspan="6"><?=$pageList;?></td>
	</tr>
	<?

	include_once('../inc/_db_close.php');
?>