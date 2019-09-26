<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_GET['sr'];
	$name	= $_POST['txtName'];
	$telno	= str_replace('-','',$_POST['txtTelno']);
	$addr	= $_POST['txtAddr'];
	$grdNm	= $_POST['txtGrdNm'];
	$mpGbn	= $_POST['cboMPGbn'];
	$statGbn= $_POST['cboStatGbn'];


	//기관명
	$orgNm = $conn->_storeName($orgNo);

	$title = $orgNm;

	if ($SR == 'S'){
		$title .= '(재가지원)';
	}else if ($SR == 'R'){
		$title .= '(자원연계)';
	}else{
		exit;
	}

	//결혼정보
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'MR\'
			AND		use_yn	= \'Y\'';

	$hceGbn['MR'] = $conn->_fetch_array($sql,'code');

	//동거정보
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'CB\'
			AND		use_yn	= \'Y\'';

	$hceGbn['CB'] = $conn->_fetch_array($sql,'code');

	//학력정보
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'EL\'
			AND		use_yn	= \'Y\'';

	$hceGbn['EL'] = $conn->_fetch_array($sql,'code');

	//종교
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'RG\'
			AND		use_yn	= \'Y\'';

	$hceGbn['RG'] = $conn->_fetch_array($sql,'code');

	//경제
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'IG\'
			AND		use_yn	= \'Y\'';

	$hceGbn['IG'] = $conn->_fetch_array($sql,'code');
	
	//그외 정보
	$sql = 'SELECT	jumin
			,	    care_cost
			,		care_org_no
			,		care_org_nm
			,		care_no
			,       case care_lvl when \'9\' then \'일반\' when \'7\' then \'등급 외 A,B\' else concat(care_lvl,\'등급\') end as lvl_nm
			,       case care_gbn when \'3\' then \'기초\'
								  when \'2\' then \'의료\'
								  when \'4\' then \'경감\' else \'일반\' end as kind_nm
			,		care_pic_nm
			,		care_telno
			FROM	client_his_care
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \''.$SR.'\'
			ORDER	BY seq DESC';

	$other = $conn->_fetch_array($sql,"jumin");

	header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=carevisit_excel_".date('Ymd').".xls" );
?>
<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>
<table border="1">
	<tr>
		<td style="height:50px; font-size:17px;" colspan="26"><?=$title;?> 대상자리스트</td>
	</tr>
	<tr>
		<th style="width:50px; background-color:#EAEAEA;" rowspan="2">No</th>
		<th style="width:70px; background-color:#EAEAEA;" rowspan="2">성명</th>
		<th style="width:110px; background-color:#EAEAEA;" rowspan="2">주민번호</th>
		<th style="background-color:#EAEAEA;" colspan="2">연락처</th>
		<th style="background-color:#EAEAEA;" rowspan="2">주소</th>
		<th style="background-color:#EAEAEA;" rowspan="2">생활구분</th>
		<th style="background-color:#EAEAEA;" colspan="2">결혼정보</th>
		<th style="background-color:#EAEAEA;" colspan="2">기타정보</th>
		<th style="background-color:#EAEAEA;" colspan="4">이용정보</th>
		<th style="background-color:#EAEAEA;" colspan="3">보호자</th>
		<th style="background-color:#EAEAEA;" colspan="7">기관정보</th>
		<th style="width:100px; background-color:#EAEAEA;" rowspan="2">비고</th>
	</tr>
	<tr>
		<th style="width:90px; background-color:#EAEAEA;">유선</th>
		<th style="width:90px; background-color:#EAEAEA;">무선</th>
		<th style="width:50px; background-color:#EAEAEA;">결혼</th>
		<th style="width:50px; background-color:#EAEAEA;">동거</th>
		<th style="width:100px; background-color:#EAEAEA;">학력</th>
		<th style="width:50px; background-color:#EAEAEA;">종교</th>
		<th style="width:80px; background-color:#EAEAEA;">시작일자</th>
		<th style="width:80px; background-color:#EAEAEA;">종료일자</th>
		<th style="width:70px; background-color:#EAEAEA;">상태</th>
		<th style="width:70px; background-color:#EAEAEA;">관리구분</th>
		<th style="width:70px; background-color:#EAEAEA;">성명</th>
		<th style="width:90px; background-color:#EAEAEA;">연락처</th>
		<th style="width:100px; background-color:#EAEAEA;">주소</th>
		<th style="background-color:#EAEAEA;">기관기호</th>
		<th style="background-color:#EAEAEA;">기관명</th>
		<th style="background-color:#EAEAEA;">인정번호</th>
		<th style="background-color:#EAEAEA;">등급</th>
		<th style="background-color:#EAEAEA;">구분</th>
		<th style="background-color:#EAEAEA;">담당자명</th>
		<th style="background-color:#EAEAEA;">연락처</th>
	</tr><?
		

		if($SR == 'R'){
			$sql = 'SELECT	mst.m03_name AS name
					,		IFNULL(CASE WHEN LENGTH(jumin.jumin) = 7 THEN CONCAT(jumin.jumin,\'000000\') ELSE jumin.jumin END, m03_jumin) AS jumin
					,		mst.m03_tel AS phone
					,		mst.m03_hp AS mobile
					,		mst.m03_juso1 AS addr
					,		mst.m03_juso2 AS addr_dtl
					,		SUBSTR(m03_yoyangsa5_nm,1,1) AS marry_gbn
					,		SUBSTR(m03_yoyangsa5_nm,2,1) AS cohabit_gbn
					,		SUBSTR(m03_yoyangsa5_nm,3,2) AS edu_gbn
					,		SUBSTR(m03_yoyangsa5_nm,5,1) AS rel_gbn
					,		his.from_dt
					,		his.to_dt
					,		IFNULL(his.svc_stat,\'9\') AS stat
					,		IFNULL(his.mp_gbn,\'N\') AS mp
					,		m03_yboho_name AS grd_nm
					,		m03_yoyangsa4_nm AS grd_addr
					,		m03_yboho_phone AS grd_tel
					,		IFNULL(iv.income_gbn,\'\') AS income_gbn
					,		MAX(svc.seq) AS seq
					,	    jumin.code
					FROM	m03sugupja AS mst
					INNER	JOIN	client_his_svc AS svc
							ON		svc.org_no = mst.m03_ccode
							AND		svc.jumin = mst.m03_jumin
							AND		svc.svc_cd = \''.$SR.'\'
					LEFT	JOIN	mst_jumin AS jumin
							ON		jumin.org_no= m03_ccode
							AND		jumin.gbn	= \'1\'
							AND		jumin.code	= m03_jumin
					INNER JOIN ( SELECT DISTINCT jumin
								 ,      svc_cd
								 ,      from_dt
								 ,      to_dt
								 ,      svc_stat
								 ,      mp_gbn
								 FROM   client_his_svc
								 WHERE  org_no = \''.$orgNo.'\'
								 ORDER  BY from_dt desc
								) as his
					ON		his.jumin = mst.m03_jumin
					AND		his.svc_cd	 = \''.$SR.'\'';

			if ($statGbn != ''){
				if ($statGbn == '1')
					$sql .= ' AND	DATE_FORMAT(his.from_dt, \'%Y%m%d\')	<= DATE_FORMAT(NOW(),\'%Y%m%d\')
							  AND	DATE_FORMAT(his.to_dt, \'%Y%m%d\')	>= DATE_FORMAT(NOW(),\'%Y%m%d\')
							  AND	his.svc_stat = \'1\'
							  ';
				else
					$sql .= ' AND	his.svc_stat != \'1\'';
			}

			$sql .= 'LEFT	JOIN	hce_interview AS iv
							ON		iv.org_no	= mst.m03_ccode
							AND		iv.org_type	= \''.$SR.'\'
							AND		iv.IPIN		= mst.m03_key
							AND		iv.rcpt_seq	= \'0\'
					WHERE	mst.m03_ccode	= \''.$orgNo.'\'
					AND		mst.m03_mkind	= \'6\'
					AND		mst.m03_del_yn	= \'N\'';

			if ($name){
				$sql .= '
					AND		mst.m03_name >= \''.$name.'\'';
			}

			if ($telno){
				$sql .= '
					AND		CONCAT(mst.m03_tel,\'_\',mst.m03_hp) LIKE \'%'.$telno.'%\'';
			}

			if ($addr){
				$sql .= '
					AND		CONCAT(mst.m03_juso1,\'_\',mst.m03_juso1) LIKE \'%'.$addr.'%\'';
			}

			if ($grdNm){
				$sql .= '
					AND		mst.m03_yboho_name >= \''.$grdNm.'\'';
			}

			if ($mpGbn){
				$sql .= '
					AND		IFNULL(his.mp_gbn,\'N\') = \''.$mpGbn.'\'';
			}

			if ($statGbn){
				$sql .= '
					AND		IFNULL(his.svc_stat,\'9\') = \''.$statGbn.'\'';
			}

			$sql .= '
					GROUP	BY mst.m03_jumin
					ORDER	BY name';

		}else {


			$wsl = '';

			if ($name){
				$wsl .= '
					AND		name >= \''.$name.'\'';
			}

			if ($telno){
				$wsl .= '
					AND		CONCAT(phone,\'_\',mobile) LIKE \'%'.$telno.'%\'';
			}

			if ($addr){
				$wsl .= '
					AND		CONCAT(addr,\'_\',addr_dtl) LIKE \'%'.$addr.'%\'';
			}

			if ($grdNm){
				$wsl .= '
					AND		grd_nm >= \''.$grdNm.'\'';
			}

			if ($mpGbn && ($mpGbn != 'X')){
				$wsl .= '
					AND		IFNULL(mp_gbn,\'X\') = \''.$mpGbn.'\'';
			}

			if ($statGbn != 'all'){
				$wsl .= '
					AND		IFNULL(stat,\'X\') = \''.$statGbn.'\'';
			}

			if ($addrMent){
				$wsl .= '
					AND		addr_ment LIKE \'%'.$addrMent.'%\'';
			}

			if ($rcptFrom && $rcptTo){
				$wsl .= '
					AND		reg_dt BETWEEN \''.$rcptFrom.'\' AND \''.$rcptTo.'\'';
			}

			if ($gender){
				$wsl .= ' AND substr(real_jumin,7,1) = \''.$gender.'\'';
			}

			if ($income){
				$wsl .= ' AND income_gbn = \''.$income.'\'';
			}

			if ($generation){
				$wsl .= ' AND generation_gbn = \''.$generation.'\'';
			}

			$bql = '/* 대상자 */
					SELECT	case when IFNULL(c.mp_gbn,\'N\') = \'Y\' then 1 else 2 end as mgbn
					,       0 AS gbn
					,		IFNULL(CASE WHEN LENGTH(d.jumin) = 7 THEN CONCAT(d.jumin,\'000000\') ELSE d.jumin END, m03_jumin) AS jumin
					,		m03_name AS name
					,		m03_tel AS phone
					,		m03_hp AS mobile
					,		m03_post_no AS postno
					,		m03_juso1 AS addr
					,		m03_juso2 AS addr_dtl
					,		m03_yboho_name AS grd_nm
					,		m03_yboho_phone AS grd_tel
					,		m03_yoyangsa4_nm AS grd_addr
					,		SUBSTR(m03_yoyangsa5_nm,1,1) AS marry_gbn
					,		SUBSTR(m03_yoyangsa5_nm,2,1) AS cohabit_gbn
					,		SUBSTR(m03_yoyangsa5_nm,3,2) AS edu_gbn
					,		SUBSTR(m03_yoyangsa5_nm,5,1) AS rel_gbn
					,		MAX(b.seq) AS seq
					,		IFNULL(c.mp_gbn,\'N\') AS mp_gbn
					,		IFNULL(c.svc_stat,\'9\') AS stat
					,		IFNULL(d.jumin, m03_jumin) AS real_jumin
					,		e.addr_ment
					,		e.reg_dt
					,       f.income_gbn
					,		f.income_other
					,		f.generation_gbn
					,		f.generation_other
					,	    m03_jumin as code
					,		c.from_dt
					,		c.to_dt
					FROM	m03sugupja
					INNER	JOIN	client_his_svc AS b
							ON		b.org_no = m03_ccode
							AND		b.svc_cd = \''.$SR.'\'
							AND		b.jumin	 = m03_jumin
					LEFT	JOIN	client_his_svc AS c
							ON		c.org_no	 = m03_ccode
							AND		c.svc_cd	 = \''.$SR.'\'
							AND		c.jumin		 = m03_jumin
							AND		DATE_FORMAT(c.from_dt, \'%Y%m%d\')	<= DATE_FORMAT(NOW(),\'%Y%m%d\')
							AND		DATE_FORMAT(c.to_dt, \'%Y%m%d\')	>= DATE_FORMAT(NOW(),\'%Y%m%d\')
					LEFT	JOIN	mst_jumin AS d
							ON		d.org_no = m03_ccode
							AND		d.gbn	 = \'1\'
							AND		d.code	 = m03_jumin
					LEFT	JOIN	client_option AS e
							ON		e.org_no	= m03_ccode
							AND		e.jumin		= m03_jumin
					LEFT JOIN ( SELECT   IPIN, income_gbn, income_other, generation_gbn, generation_other
								FROM     hce_interview
								WHERE    org_no = \''.$orgNo.'\'
								AND      org_type = \''.$SR.'\'
								ORDER BY rcpt_seq desc ) as f
					ON		f.IPIN	= m03_key
					WHERE	m03_ccode	= \''.$orgNo.'\'
					AND		m03_mkind	= \'6\'
					AND		m03_del_yn	= \'N\'
					GROUP	BY m03_jumin

					UNION	ALL

					/* 일반접수 */
					SELECT	3 as mgbn
					,		a.normal_seq
					,		a.jumin
					,		a.name
					,		a.phone
					,		a.mobile
					,		a.postno
					,		a.addr
					,		a.addr_dtl
					,		a.grd_nm
					,		a.grd_telno as grd_tel
					,	    a.grd_addr
					,		NULL AS marry_gbn
					,		NULL AS cohabit_gbn
					,		NULL AS edu_gbn
					,		NULL AS rel_gbn
					,		NULL, NULL, NULL, a.jumin
					,		a.addr_ment
					,		a.reg_dt
					,		f.income_gbn
					,		f.income_other
					,		f.generation_gbn
					,		f.generation_other
					,	    a.jumin as code, \'\', \'\'
					FROM	care_client_normal AS a
					LEFT	JOIN	m03sugupja
							ON		m03_ccode = a.org_no
							AND		m03_mkind = \'6\'
							AND		m03_jumin = a.jumin
					LEFT JOIN ( SELECT   IPIN, income_gbn, income_other, generation_gbn, generation_other
								FROM     hce_interview
								WHERE    org_no = \''.$orgNo.'\'
								AND      org_type = \''.$SR.'\'
								AND      rcpt_seq= \'-1\') as f
					ON		f.IPIN	= m03_key
					WHERE	a.org_no	= \''.$orgNo.'\'
					AND		a.normal_sr	= \''.$SR.'\'
					AND		a.del_flag	= \'N\'
					AND		a.link_IPIN	IS NULL
					AND		m03_jumin	IS NULL';

			$sql = 'SELECT	*
					FROM	('.$bql.') AS a
					WHERE	gbn IS NOT NULL'.$wsl;

			$sql .= '
					ORDER	BY name';
		}
		//if ($debug){
		//	echo nl2br($sql);
		//	exit;
		//}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['stat'] == '1'){
				$row['stat'] = '사용';
			}else if ($row['stat'] == '7'){
				$row['stat'] = '<span style="color:BLUE;">미등록</span>';
			}else if ($row['stat'] == '9'){
				$row['stat'] = '<span style="color:RED;">중지</span>';
			}
			
			$sql = 'SELECT  jumin, from_dt, to_dt    
					FROM	client_his_svc
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		svc_cd	 = \''.$SR.'\'
					AND     jumin    = \''.$row['code'].'\'
					ORDER   BY from_dt desc
					limit   1';
			$svc = $conn->get_array($sql);
			
			?>
			<tr>
				<td style="text-align:center;"><?=$no;?></td>
				<td style=""><?=$row['name'];?></td>
				<td style="text-align:center;"><?=$myF->issNo($row['jumin']);?></td>
				<td style="text-align:center;"><?=$myF->phoneStyle($row['phone'],'.');?></td>
				<td style="text-align:center;"><?=$myF->phoneStyle($row['mobile'],'.');?></td>
				<td style=""><?=$row['addr'].' '.$row['addr_dtl'];?></td>
				<td style="text-align:center;"><?=$hceGbn['IG'][$row['income_gbn']]['name'];?></td>
				<td style="text-align:center;"><?=$hceGbn['MR'][$row['marry_gbn']]['name'];?></td>
				<td style="text-align:center;"><?=$hceGbn['CB'][$row['cohabit_gbn']]['name'];?></td>
				<td style="text-align:center;"><?=$hceGbn['EL'][$row['edu_gbn']]['name'];?></td>
				<td style="text-align:center;"><?=$hceGbn['RG'][$row['rel_gbn']]['name'];?></td>
				<td style="text-align:center;"><?=$myF->dateStyle($svc['from_dt'],'.');?></td>
				<td style="text-align:center;"><?=$myF->dateStyle($svc['to_dt'],'.');?></td>
				<td style="text-align:center;"><?=$row['stat'];?></td>
				<td style="text-align:center;"><?=$row['mp_gbn'] == 'Y' ? '중점관리' : '일반';?></td>
				<td style=""><?=$row['grd_nm'];?></td>
				<td style="text-align:center;"><?=$myF->phoneStyle($row['grd_tel'],'.');?></td>
				<td style=""><?=$row['grd_addr'];?></td>
				<td style="text-align:left;"><?=$other[$row['code']]['care_org_no'];?></td>
				<td style=""><?=$other[$row['code']]['care_org_nm'];?></td>
				<td style="text-align:center;"><?=$other[$row['code']]['care_no'];?></td>
				<td style="text-align:center;"><?=$other[$row['code']]['lvl_nm'];?></td>
				<td style="text-align:center;"><?=$other[$row['code']]['kind_nm'];?></td>
				<td style=""><?=$other[$row['code']]['care_pic_nm'];?></td>
				<td style=""><?=$myF->phoneStyle($other[$row['code']]['care_telno'],'.');?></td>
				<td></td>
			</tr><?

			$no ++;
		}

		$conn->row_free();
	?>
</table>
<?
	include_once('../inc/_db_close.php');
?>