<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once("../inc/_page_list.php");

	$host = $myF->host();

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_REQUEST['page'];

	$orgNo = $_POST['orgNo']; //기관기호
	$orgNm = $_POST['orgNm']; //기관명
	$manager = $_POST['manager']; //대표자
	$CMSno = $_POST['CMSno']; //CMS 번호
	$CMSgbn = $_POST['CMSgbn']; //CMS구분
	$connFrom = str_replace('-','',$_POST['connFrom']); //연결일자
	$connTo = str_replace('-','',$_POST['connTo']); //연결일자
	$contFrom = str_replace('-','',$_POST['contFrom']); //계약일자
	$contTo = str_replace('-','',$_POST['contTo']); //계약일자
	$company = $_POST['company']; //관리회사
	$branch = $_POST['branch']; //관리지점
	$person = $_POST['person']; //관리지점 담당자
	$connDt	= $_POST['connDt']; //연결일자순 정렬여부
	$useCenter = $_POST['useCenter']; //사용기관여부
	$taxbillYn = $_POST['taxbillYn']; //세금계산서 발행기관여부

	if ($CMSno){
		if (StrLen($CMSno) < 8){
			$liCnt = 8 - StrLen($CMSno);

			$CMSno = '';

			for($i=1; $i<=$liCnt; $i++){
				$CMSno .= '0';
			}
			$CMSno .= IntVal($_POST['CMSno']);
		}
	}

	if ($useCenter == 'Y'){
		$joinSl1 = '
			INNER	JOIN	cv_reg_info AS a
					ON		a.org_no = m00_mcode
					AND		a.from_dt <= DATE_FORMAT(NOW(),\'%Y%m%d\')
					AND		a.to_dt	  >= DATE_FORMAT(NOW(),\'%Y%m%d\')';
	}else if ($useCenter == 'N'){
		$joinSl2 = '
			INNER	JOIN	cv_reg_info AS a
					ON		a.org_no = m00_mcode
					AND		a.org_no NOT IN (SELECT org_no FROM cv_reg_info WHERE org_no = a.org_no AND DATE_FORMAT(NOW(),\'%Y%m%d\') BETWEEN from_dt AND to_dt)';
	}else{
		$joinSl3 = '
			LEFT	JOIN	cv_reg_info AS a
					ON		a.org_no = m00_mcode';
	}

	if ($host != 'admin' || $_SESSION["userCode"] == 'geecare'){
		$tmpSql = ' INNER JOIN cv_reg_info AS z
					ON z.org_no = m00_mcode
					AND z.from_dt <= \''.Date('Ymd').'\'
					AND z.to_dt >= \''.Date('Ymd').'\'
					AND z.cont_com != \'3\' ';

		$joinSl1 .= $tmpSql;
		$joinSl2 .= $tmpSql;
		$joinSl3 .= $tmpSql;
	}

	$sl = '';

	if ($useCenter){
		//$sl = '	AND		from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
		//			AND		to_dt >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
		//$sl = '		AND		CASE WHEN from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\') AND to_dt >= DATE_FORMAT(NOW(),\'%Y-%m-%d\') THEN 1 ELSE 0 END = '.($useCenter == 'Y' ? 1 : 0);
		$sl .= '		AND		CASE WHEN DATE_FORMAT(NOW(),\'%Y%m%d\') BETWEEN a.from_dt AND a.to_dt THEN 1 ELSE 0 END = '.($useCenter == 'Y' ? 1 : 0);
	}

	//기간기호
	if ($orgNo){
		$sl .= '	AND		m00_mcode LIKE \'%'.$orgNo.'%\'';
	}

	//기관명
	if ($orgNm){
		$sl .= '	AND		m00_store_nm LIKE \'%'.$orgNm.'%\'';
	}

	//대표자
	if ($manager){
		$sl .= '	AND		m00_mname LIKE \'%'.$manager.'%\'';
	}

	//연결일자
	/*
	if ($connFrom && $connTo){
		$sl .= '	AND		b02_date BETWEEN \''.$connFrom.'\' AND \''.$connTo.'\'';
	}else if ($connFrom){
		$sl .= '	AND		b02_date >= \''.$connFrom.'\'';
	}else if ($connTo){
		$sl .= '	AND		b02_date <= \''.$connTo.'\'';
	}
	*/
	if ($connFrom && $connTo){
		$sl .= '	AND		a.start_dt BETWEEN \''.$connFrom.'\' AND \''.$connTo.'\'';
	}else if ($connFrom){
		$sl .= '	AND		a.start_dt >= \''.$connFrom.'\'';
	}else if ($connTo){
		$sl .= '	AND		a.start_dt <= \''.$connTo.'\'';
	}

	//계약일자
	/*
	if ($contFrom && $contTo){
		$sl .= '	AND		m00_cont_date BETWEEN \''.$contFrom.'\' AND \''.$contTo.'\'';
	}else if ($contFrom){
		$sl .= '	AND		m00_cont_date >= \''.$contFrom.'\'';
	}else if ($contTo){
		$sl .= '	AND		m00_cont_date <= \''.$contTo.'\'';
	}
	*/
	if ($contFrom && $contTo){
		$sl .= '	AND		a.cont_dt BETWEEN \''.$contFrom.'\' AND \''.$contTo.'\'';
	}else if ($contFrom){
		$sl .= '	AND		a.cont_dt >= \''.$contFrom.'\'';
	}else if ($contTo){
		$sl .= '	AND		a.cont_dt <= \''.$contTo.'\'';
	}

	if ($taxbillYn == 'Y'){
		$sl .= ' AND a.taxbill_yn = \'Y\'';
	}

	//CMS 등록여부
	if ($CMSgbn){
		if ($CMSgbn == 'Y'){
			#$sl .= '	AND		IFNULL(b02center.cms_cd,\'\') != \'\'';
			$sl .= '	AND		IFNULL(cms.cms_no,\'\') != \'\'';
		}else{
			#$sl .= '	AND		IFNULL(b02center.cms_cd,\'\') = \'\'';
			$sl .= '	AND		IFNULL(cms.cms_no,\'\') = \'\'';
		}
	}

	//연결회사
	if ($company){
		$sl .= '	AND		m00_domain = \''.$company.'\'';
	}

	//CMS번호
	if ($CMSno){
		#$sl .= '	AND		b02center.cms_cd >= \''.$CMSno.'\'';
		$sl .= '	AND		cms.cms_no LIKE \'%'.$CMSno.'%\'';
	}

	//연결지점
	if ($branch){
		//$sl .= '	AND		b02_branch = \''.$branch.'\'';
		$sl .= '	AND		a.link_branch = \''.$branch.'\'';
	}

	//연결담당자
	if ($person){
		//$sl .= '	AND		b02_person = \''.$person.'\'';
		$sl .= '	AND		a.link_person = \''.$person.'\'';
	}

	#echo '<tr><td colspan="20">'.nl2br($sl).'</td></tr>';

	if (Empty($page)){
		$page = 1;
	}

	//전체 카운트
	$sql = 'SELECT	COUNT(DISTINCT m00_mcode)
			FROM	m00center';

	/*
	if ($useCenter == 'Y'){
		$sql .= '
			INNER	JOIN	b02center
					ON		b02_center = m00_mcode
					AND		b02center.from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
					AND		b02center.to_dt	  >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
	}else if ($useCenter == 'N'){
		$sql .= '
			LEFT	JOIN	b02center
					ON		b02_center = m00_mcode';
	}else{
		$sql .= '
			LEFT	JOIN	b02center
					ON		b02_center = m00_mcode';
	}
	*/
	if ($useCenter == 'Y'){
		$sql .= $joinSl1;
	}else if ($useCenter == 'N'){
		$sql .= $joinSl2;
	}else{
		$sql .= $joinSl3;
	}

	/*
	$sql .= '
			LEFT	JOIN	cv_cms_list AS cms
					ON		cms.org_no = m00_mcode
			WHERE	m00_del_yn = \'N\''.$sl;
	*/

	$sql .= '
			LEFT	JOIN (
						SELECT	org_no, bill_gbn, cms_no
						FROM	cv_bill_info
						WHERE	del_flag = \'N\'
						AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
						AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')
					) AS cms
					ON		cms.org_no = m00_mcode
			WHERE	m00_del_yn = \'N\''.$sl;

	$totCnt = $conn->get_data($sql);

	#echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

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

	$sql = 'SELECT	m00_mcode AS org_no
			,		m00_store_nm AS org_nm
			,		m00_mname AS manager
			,		m00_ctel AS telno
			,		b01_name AS incharge
			/*,		GROUP_CONCAT(m00_start_date) AS start_dt*/
			/*,		GROUP_CONCAT(m00_cont_date) AS cont_dt*/
			,		GROUP_CONCAT(DISTINCT cms.cms_no) AS cms_no
			/*,		b02_date AS conn_dt*/
			,		a.start_dt AS conn_dt
			,		a.cont_dt
			,		a.taxbill_yn
			,		m97_id as id
			,		m97_pass as pw
			,		m00_domain AS domain
			FROM	m00center';

	/*
	if ($useCenter == 'Y'){
		$sql .= '
			INNER	JOIN	b02center
					ON		b02_center = m00_mcode
					AND		b02center.from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
					AND		b02center.to_dt	  >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
	}else if ($useCenter == 'N'){
		$sql .= '
			LEFT	JOIN	b02center
					ON		b02_center = m00_mcode';
	}else{
		$sql .= '
			LEFT	JOIN	b02center
					ON		b02_center = m00_mcode';
	}

	$sql .= '
			LEFT	JOIN	b01person
					ON		b01_branch = b02_branch
					AND		b01_code = b02_person
			LEFT	JOIN	m97user
					ON		m97_user = m00_mcode
			LEFT	JOIN	cv_cms_list AS cms
					ON		cms.org_no = m00_mcode
			WHERE	m00_del_yn = \'N\''.$sl.'
			GROUP	BY m00_mcode
			ORDER	BY ';
	*/

	if ($useCenter == 'Y'){
		$sql .= $joinSl1;
	}else if ($useCenter == 'N'){
		$sql .= $joinSl2;
	}else{
		$sql .= $joinSl3;
	}

	$sql .= '
			LEFT	JOIN	b01person
					ON		b01_branch = a.link_branch
					AND		b01_code = a.link_person
			LEFT	JOIN	m97user
					ON		m97_user = m00_mcode
			/*LEFT	JOIN	cv_cms_list AS cms*/
			LEFT	JOIN	(
							SELECT	org_no, bill_gbn, cms_no
							FROM	cv_bill_info
							WHERE	del_flag = \'N\'
							AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
							AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')
							) AS cms
					ON		cms.org_no = m00_mcode
			WHERE	m00_del_yn = \'N\''.$sl.'
			GROUP	BY m00_mcode
			ORDER	BY ';

	if ($CMSgbn == 'Y'){
		//$sql .= '	CAST(cms_cd AS unsigned), ';
	}

	if ($connDt == 'Y'){
		$sql .= '	conn_dt, ';
	}

	$sql .= '		org_nm
			LIMIT	'.$pageCount.','.$itemCnt;

	#echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($gDomain == _KLCF_){
			$url = 'care.'.$row['domain'];
		}else{
			$url = 'www.'.$row['domain'];
		}

		$sql = 'SELECT	stop_gbn, cls_yn, stop_yn
				FROM	stop_set
				WHERE	org_no = \''.$row['org_no'].'\'
				ORDER	BY stop_dt DESC
				LIMIT	1';

		$R = $conn->get_array($sql);

		if ($R){
			if ($R['stop_gbn'] == '1'){ //중지
				if ($R['cls_yn'] == 'N'){
					$stopGbn = '<span style="color:RED;">중지설정</span>';
				}else if ($R['cls_yn'] == 'Y'){
					$stopGbn = '<span style="color:BLUE;">중지해제</span>';
				}
			}else{ //미납
				if ($R['close_dt'] >= Date('Ymd')){
					$stopGbn = '<span style="color:RED;">미납설정</span>';
				}else{
					$stopGbn = '<span style="color:BLUE;">미납해제</span>';
				}
			}
		}else{
			$stopGbn = '';
		}

		Unset($R);?>
		<tr>
			<td class="center"><?=$pageCount + ($i + 1);?></td>
			<td class="center"><div class="left nowrap" style="width:90px;"><?=$row['org_no'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:150px;"><?=$row['org_nm'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:90px;"><?=$row['cms_no'] ? $row['cms_no'] : '';?></div></td>
			<td class="center"><div class="left nowrap" style="width:50px;"><?=$row['manager'];?></div></td>
			<td class="center"><?=$myF->dateStyle($row['conn_dt'],'.');?></td>
			<td class="center"><?=$stopGbn;?></td>
			<td class="center"><?=$myF->dateStyle($row['cont_dt'],'.');?></td>
			<td class="center"><div class="left nowrap" style="width:50px;"><?=$row['incharge'];?></div></td>
			<td class="center"><?=$row['taxbill_yn'] == 'Y' ? 'Y' : '';?></td>
			<td class="center last">
				<div class="left">
					<span class="btn_pack small"><button onclick="Selection('<?=$row['org_no'];?>');">선택</button></span><?
					if ($host == 'admin' && $_SESSION["userCode"] != 'geecare'){?>
						<span class="btn_pack small"><button onclick="ShowCenterScreen('<?=$gDomainID;?>','http://<?=$url;?>/main/login_ok.php','<?=$ed->en($row['id']);?>','<?=$ed->en($row['pw']);?>');">이동</button></span>
						<span class="btn_pack small"><button onclick="ShowPayIn('<?=$row['org_no'];?>');">입금</button></span><?
					}?>
				</div>
			</td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	$paging = new YsPaging($params);
	$pageList = $paging->returnPaging();

	include_once('../inc/_db_close.php');
?>
<script type="text/javascript">
	$('#tfootList').html('<?=$pageList;?>');
</script>