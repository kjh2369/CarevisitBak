<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo		= $_POST['orgNo']; //기관기호
	$orgNm		= $_POST['orgNm']; //기관명
	$manager	= $_POST['manager']; //대표자
	$CMSno		= $_POST['CMSno']; //CMS 번호
	$CMSgbn		= $_POST['CMSgbn']; //CMS구분
	$connFrom	= str_replace('-','',$_POST['connFrom']); //연결일자
	$connTo		= str_replace('-','',$_POST['connTo']); //연결일자
	$contFrom	= str_replace('-','',$_POST['contFrom']); //계약일자
	$contTo		= str_replace('-','',$_POST['contTo']); //계약일자
	$company	= $_POST['company']; //관리회사
	$branch		= $_POST['branch']; //관리지점
	$person		= $_POST['person']; //관리지점 담당자
	$connDt		= $_POST['connDt']; //연결일자순 정렬여부
	$today		= Date('Ymd');
	$yymm		= Date('Ym');

	//일정수
	$sql = 'SELECT	t01_ccode AS org_no, COUNT(DISTINCT t01_jumin) AS cnt
			FROM	t01iljung
			WHERE	LEFT(t01_sugup_date,6) = \''.$yymm.'\'
			AND		t01_del_yn = \'N\'
			GROUP	BY t01_ccode';
	//$iljung = $conn->_fetch_array($sql, 'org_no');

	$style = 'border:0.5pt solid BLACK; background-color:#EAEAEA;';?>
<table>
	<tr>
		<th style="width:50px; text-align:center;<?=$style;?>">No</th>
		<th style="width:100px; text-align:center;<?=$style;?>">기관기호</th>
		<th style="width:200px; text-align:center;<?=$style;?>">기관명</th>
		<th style="width:80px; text-align:center;<?=$style;?>">대표자</th>
		<th style="width:100px; text-align:center;<?=$style;?>">전화번호</th>
		<th style="width:100px; text-align:center;<?=$style;?>">휴대폰번호</th>
		<th style="width:100px; text-align:center;<?=$style;?>">사용시작일</th>
		<th style="width:100px; text-align:center;<?=$style;?>">계약담당</th>
		<th style="width:100px; text-align:center;<?=$style;?>">당월일정수</th>
		<th style="width:100px; text-align:center;<?=$style;?>">장기요양</th>
		<th style="width:100px; text-align:center;<?=$style;?>">가사간병</th>
		<th style="width:100px; text-align:center;<?=$style;?>">노인돌봄</th>
		<th style="width:100px; text-align:center;<?=$style;?>">산모신생아</th>
		<th style="width:100px; text-align:center;<?=$style;?>">장애인활동지원</th>
		<th style="width:100px; text-align:center;<?=$style;?>">주야간보호</th>
		<th style="width:100px; text-align:center;<?=$style;?>">복지용품</th>
		<th style="width:100px; text-align:center;<?=$style;?>">스마트폰</th>
		<th style="width:100px; text-align:center;<?=$style;?>">SMS</th>
		<th style="width:500px; text-align:center;<?=$style;?>">주소</th>
	</tr><?
	$sql = 'SElECT	org_no, cms_com
			FROM	cv_cms_list
			WHERE	cms_com = \'3\'';

	$cmsCom = $conn->_fetch_array($sql, 'org_no');

	$sql = 'SELECT	a.*
			,		b.mobile
			,		CASE WHEN IFNULL(c.org_no,\'\') != \'\' THEN \'Y\' ELSE \'\' END AS dan_yn
			,		CASE WHEN IFNULL(d.org_no,\'\') != \'\' THEN \'Y\' ELSE \'\' END AS wmd_yn
			,		CASE WHEN IFNULL(e.org_no,\'\') != \'\' THEN \'Y\' ELSE \'\' END AS smart_yn
			,		CASE WHEN IFNULL(f.org_no,\'\') != \'\' THEN \'Y\' ELSE \'\' END AS sms_yn
			,		b01_name AS pc_nm
			,		CASE WHEN IFNULL(g.start_dt,\'\') != \'\' THEN g.start_dt ELSE a.start_dt END as start_dt
			FROM	(
					SELECT	DISTINCT
							m00_mcode AS org_no
					,		m00_store_nm AS org_nm
					,		m00_mname AS mg_nm
					,		m00_ctel AS phone
					,		b02_date AS start_dt
					,		m00_caddr1 AS addr
					,		m00_caddr2 AS addr_dtl
					,		b02_branch AS b_cd
					,		b02_person AS p_cd
					,		CASE WHEN b02_homecare = \'Y\'THEN \'Y\' ElSE \'\' END AS homecare_yn
					,		CASE WHEN SUBSTR(b02_voucher,1,1) = \'Y\'THEN \'Y\' ElSE \'\' END AS nurse_yn
					,		CASE WHEN SUBSTR(b02_voucher,2,1) = \'Y\'THEN \'Y\' ElSE \'\' END AS old_yn
					,		CASE WHEN SUBSTR(b02_voucher,3,1) = \'Y\'THEN \'Y\' ElSE \'\' END AS baby_yn
					,		CASE WHEN SUBSTR(b02_voucher,4,1) = \'Y\'THEN \'Y\' ElSE \'\' END AS dis_yn
					,		CASE WHEN care_support = \'Y\'THEN \'Y\' ElSE \'\' END AS care_s
					,		CASE WHEN care_resource = \'Y\'THEN \'Y\' ElSE \'\' END AS care_r
					FROM	m00center
					INNER	JOIN	b02center
							ON		b02_center = m00_mcode
					WHERE	m00_mcode != \'\'';

	if ($company) $sql .= '	AND		m00_domain = \''.$company.'\'';

	$sql .= '		) AS a
			LEFT	JOIN	cv_reg_info AS g
					ON		g.org_no = a.org_no
			LEFT	JOIN	mst_manager AS b
					ON		b.org_no = a.org_no
			LEFT	JOIN	sub_svc AS c
					ON		c.org_no	 = a.org_no
					AND		c.svc_cd	 = \'5\'
					AND		c.del_flag	 = \'N\'
					AND		c.from_dt	<= NOW()
					AND		c.to_dt		>= NOW()
			LEFT	JOIN	sub_svc AS d
					ON		d.org_no	 = a.org_no
					AND		d.svc_cd	 = \'7\'
					AND		d.del_flag	 = \'N\'
					AND		d.from_dt	<= NOW()
					AND		d.to_dt		>= NOW()
			LEFT	JOIN	smart_acct AS e
					ON		e.org_no	 = a.org_no
					AND		e.from_dt	<= NOW()
					AND		e.to_dt		>= NOW()
			LEFT	JOIN	sms_acct AS f
					ON		f.org_no	 = a.org_no
					AND		f.from_dt	<= NOW()
					AND		f.to_dt		>= NOW()
			LEFT	JOIN	b01person
					ON		b01_branch	= a.b_cd
					AND		b01_code	= a.p_cd

			WHERE	CASE WHEN g.org_no IS NULL THEN 1 ELSE 0 END = 1
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;
	$style = 'border:0.5pt solid BLACK;';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($cmsCom[$row['org_no']]['cms_com'] == '3') continue;

		$sql = 'SELECT	COUNT(DISTINCT t01_jumin)
				FROM	t01iljung
				WHERE	t01_ccode	= \''.$row['org_no'].'\'
				AND		t01_del_yn	= \'N\'
				AND		LEFT(t01_sugup_date,6) = DATE_FORMAT(NOW(),\'%Y%m\')';
		$cnt = $conn->get_data($sql);
		if ($cnt == 0) $cnt = '';?>
		<tr>
			<td style="text-align:center; <?=$style;?>"><?=$no;?></td>
			<td style="text-align:left; <?=$style;?>"><?=$row['org_no'];?></td>
			<td style="mso-number-format:'\@'; <?=$style;?>"><?=$row['org_nm'];?></td>
			<td style="<?=$style;?>"><?=$row['mg_nm'];?></td>
			<td style="mso-number-format:'\@'; <?=$style;?>"><?=$myF->phoneStyle($row['phone']);?></td>
			<td style="mso-number-format:'\@'; <?=$style;?>"><?=$myF->phoneStyle($row['mobile']);?></td>
			<td style="text-align:center; <?=$style;?>"><?=$myF->dateStyle($row['start_dt']);?></td>
			<td style="<?=$style;?>"><?=$row['pc_nm'];?></td>
			<td style="text-align:right; <?=$style;?>"><?=$cnt;?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['homecare_yn'];?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['nurse_yn'];?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['old_yn'];?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['baby_yn'];?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['dis_yn'];?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['dan_yn'];?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['wmd_yn'];?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['smart_yn'];?></td>
			<td style="text-align:center; <?=$style;?>"><?=$row['sms_yn'];?></td>
			<td style="<?=$style;?>"><?=$row['addr'];?> <?=$row['addr_dtl'];?></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();?>
</table>
<?
	include_once('../inc/_db_close.php');
?>