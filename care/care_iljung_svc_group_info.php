<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$grpCd	= $_POST['grpCd'];
	$cd		= $_POST['suga'];
	$seq	= $_POST['seq'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];


	//그룹명 및 대상자
	$sql = 'SELECT	group_nm
			,		res_cd
			,		mem_cd
			,		target
			FROM	care_svc_group
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd	= \''.$cd.'\'
			AND		seq		= \''.$seq.'\'';

	$row = $conn->get_array($sql);

	$grpNm = $row['group_nm'];
	$target = $row['target'];
	$resCd = $row['res_cd'];
	$memCd = $row['mem_cd'];

	Unset($row);

	//직원명
	$sql = 'SELECT	DISTINCT m02_yname
			FROM	m02yoyangsa
			WHERE	m02_ccode  = \''.$orgNo.'\'
			AND		m02_yjumin = \''.$memCd.'\'';

	$memNm = $conn->get_data($sql);

	//자원명
	$sql = 'SELECT	cust_nm
			FROM	care_cust
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cust_cd	= \''.$resCd.'\'';

	$resNm = $conn->get_data($sql);

	//서비스명
	$sql = 'SELECT	suga_nm
			FROM	care_suga
			WHERE	org_no	= \''.$orgNo.'\'
			AND		suga_sr	= \''.$SR.'\'
			AND		CONCAT(suga_cd,suga_sub) = \''.$cd.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

	$sugaNm = $conn->get_data($sql);

	if ($IsCareYoyAddon){
		//공통수가
		if (!$sugaNm){
			$sql = 'SELECT	name
					FROM	care_suga_comm
					WHERE	code = \''.$cd.'\'
					AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

			$sugaNm	= $conn->get_data($sql);
		}
	}

	//대상자
	if ($grpCd){
		$sql = 'SELECT	tg_info
				FROM	care_svc_iljung
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		grp_cd	= \''.$grpCd.'\'';

		$tmp = Explode('?',$conn->get_data($sql));
		foreach($tmp as $t)if ($t) $tg[] = $t;

		$sql = '';

		foreach($tg as $row){
			parse_str($row,$val);

			$jumin = $ed->de($val['jumin']);

			/*
			if ($tmpJumin != $jumin){
				$tmpJumin = $jumin;
			}else{
				continue;
			}
			*/

			if (!$jumin){
				$tmp = 'SELECT	m03_jumin
						FROM	m03sugupja
						WHERE	m03_ccode	= \''.$orgNo.'\'
						AND		m03_mkind	= \'6\'
						AND		m03_key		= \''.$val['key'].'\'';

				$jumin = $conn->get_data($tmp);
			}

			if (is_numeric(StrPos($tmpJumin, '/'.$jumin))){
				continue;
			}else{
				$tmpJumin .= ('/'.$jumin);

				if ($sql) $sql .= '	UNION	ALL	';

				$sql.= 'SELECT	t01_ccode
						,		t01_jumin
						,		t01_sugup_date
						,		t01_sugup_fmtime
						,		t01_sugup_seq
						,		t01_status_gbn
						,		t01_mem_cd2
						,		t01_mem_nm2
						,		t01_suga_code1
						,		t01_yoyangsa_id1
						,		t01_yoyangsa_id2
						FROM	t01iljung
						WHERE	t01_ccode		= \''.$orgNo.'\'
						AND		t01_mkind		= \''.$SR.'\'
						AND		t01_jumin		= \''.$jumin.'\'
						AND		t01_sugup_date	= \''.$val['date'].'\'
						AND		t01_request		= \''.$grpCd.'\'
						AND		t01_del_yn		= \'N\'';
			}
		}

		$sql = 'SELECT	mst_jumin.jumin AS real_jumin
				,		m03_jumin AS jumin
				,		m03_name AS nm
				,		m03_key AS cd
				,		t01_sugup_date AS date
				,		t01_sugup_fmtime AS time
				,		MIN(t01_sugup_seq) AS seq
				,		t01_status_gbn AS conf
				,		t01_mem_cd2 AS mem_cd
				,		t01_mem_nm2 AS mem_nm
				,		t01_suga_code1 AS suga
				,		t01_yoyangsa_id1 AS res
				,		t01_yoyangsa_id2 AS mem
				FROM	('.$sql.') AS t
				INNER	JOIN	m03sugupja
						ON		m03_ccode	= t01_ccode
						AND		m03_mkind	= \'6\'
						AND		m03_jumin	= t01_jumin
				INNER	JOIN	mst_jumin
						ON		mst_jumin.org_no= t01_ccode
						AND		mst_jumin.gbn	= \'1\'
						AND		mst_jumin.code	= t01_jumin
				GROUP	BY m03_jumin, t01_sugup_date, t01_sugup_fmtime, t01_sugup_seq
				ORDER	BY nm, jumin';
	}else{
		$sql = 'SELECT	DISTInCT m03_name AS nm
				,		m03_key AS cd
				,		m03_jumin AS jumin
				,		m03_jumin AS real_jumin
				FROM	m03sugupja
				INNER	JOIN	client_his_svc
						ON		org_no	= m03_ccode
						AND		svc_cd	= \''.$SR.'\'
						AND		jumin	= m03_jumin
						AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
						AND		DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$year.$month.'\'
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_mkind = \'6\'
				AND		m03_key IN (\''.str_replace('/','\',\'',$target).'\')
				ORDER	BY nm';
	}
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$target = '';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		//업무로그 작성여부
		$sql = 'SELECT	COUNT(*)
				FROM	care_works_log
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		date		= \''.$row['date'].'\'
				AND		jumin		= \''.$row['jumin'].'\'
				AND		suga_cd		= \''.$row['suga'].'\'
				AND		resource_cd = \''.$row['res'].'\'
				AND		mem_cd		= \''.$row['mem'].'\'';

		$IsWorkLog = $conn->get_data($sql);

		if ($IsWorkLog > 0){
			$IsWorkLog = 'Y';
		}else{
			$sql = 'SELECT	COUNT(*)
					FROM	care_result
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		jumin	= \''.$row['jumin'].'\'
					AND		date	= \''.$row['date'].'\'
					AND		time	= \''.$row['time'].'\'
					/*AND	seq		= \''.$row['seq'].'\'*/
					AND		no		= \'1\'';

			$IsWorkLog = $conn->get_data($sql);

			if ($IsWorkLog > 0){
				$IsWorkLog = 'Y';
			}else{
				$IsWorkLog = 'N';
			}
		}

		$birthday = $myF->issToBirthday($row['real_jumin'],'.');
		$gender = $myF->issToGender($row['real_jumin']);
		$target .= '/'.($row['cd'].':'.$row['nm'].':'.$birthday.':'.$gender.':'.$ed->en($row['jumin']).':'.$myF->dateStyle($row['date']).':'.($row['time'] == '1300' ? '2' : '1').':'.($row['conf'] == '1' ? 'Y' : 'N').':'.$ed->en($row['mem_cd']).':'.$row['mem_nm'].':'.$row['seq'].':'.$row['time'].':'.$IsWorkLog);
	}

	$conn->row_free();

	$data .= 'grpNm='.$grpNm;
	$data .= '&svcNm='.$sugaNm;
	$data .= '&resNm='.$resNm;
	$data .= '&memNm='.$memNm;
	$data .= '&target='.$target;

	echo $data;

	include_once('../inc/_db_close.php');
?>