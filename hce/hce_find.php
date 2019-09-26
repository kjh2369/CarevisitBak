<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo		= $_SESSION['userCenterCode'];
	$type		= $_POST['type'];

	if ($type == 'CHECK_RCPT_DT'){
		//접수일자 중복여부
		$rcptDt	= Str_Replace('-','',$_POST['rcptDt']);
		$jumin	= $_POST['jumin'];

		if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

		$sql = 'SELECT	IPIN
				FROM	hce_elder
				WHERE	org_no = \''.$orgNo.'\'
				AND		hce_ssn= \''.$jumin.'\'';

		$IPIN = $conn->get_data($sql);

		$sql = 'SELECT	COUNT(*)
				FROM	hce_receipt
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_dt	= \''.$rcptDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			echo 'Y';
		}else{
			echo 'N';
		}


	}else if ($type == 'IV_BASIC'){
		//기본사항
		$IPIN	= $hce->IPIN;
		$rcpt	= $hce->rcpt;

		$sql = 'SELECT	m03_name AS name
				,		m03_jumin AS jumin
				,		CASE WHEN rcpt.phone != \'\' THEN rcpt.phone ELSE rcpt.mobile END AS telno
				,		EL.name AS edu_gbn
				,		RG.name AS rel_gbn
				,		rcpt.postno
				,		rcpt.addr
				,		rcpt.addr_dtl
				,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
				FROM	hce_receipt AS rcpt
				INNER	JOIN	m03sugupja AS mst
						ON		m03_ccode = rcpt.org_no
						AND		m03_mkind = \'6\'
						AND		mst.m03_key	= rcpt.IPIN
				LEFT	JOIN	hce_gbn AS EL
						ON		EL.type		= \'EL\'
						AND		EL.use_yn	= \'Y\'
						AND		EL.code		= rcpt.edu_gbn
				LEFT	JOIN	hce_gbn AS RG
						ON		RG.type		= \'RG\'
						AND		RG.use_yn	= \'Y\'
						AND		RG.code		= rcpt.rel_gbn
				LEFT	JOIN	mst_jumin AS jumin
						ON		jumin.org_no= m03_ccode
						AND		jumin.gbn	= \'1\'
						AND		jumin.code	= m03_jumin
				WHERE	rcpt.org_no		= \''.$orgNo.'\'
				AND		rcpt.org_type	= \''.$hce->SR.'\'
				AND		rcpt.IPIN		= \''.$IPIN.'\'
				AND		rcpt.rcpt_seq	= \''.$rcpt.'\'';

		$row = $conn->get_array($sql);
		$jumin = SubStr($row['real_jumin'].'0000000',0,13);

		if (Is_Array($row)){
			$data .= 'name='	.$row['name'];
			$data .= '&gender='	.$myF->issToGender($jumin);
			$data .= '&age='	.$myF->issToAge($jumin);
			$data .= '&jumin='	.$myF->issStyle($jumin);
			$data .= '&edu='	.$row['edu_gbn'];
			$data .= '&rel='	.$row['rel_gbn'];
			$data .= '&addr='	.$row['addr'].' '.$row['addr_dtl'];
			$data .= '&telno='	.$myF->phoneStyle($row['telno'],'.');
		}

		Unset($row);

		echo $data;


	/*********************************************************
	 *	사정기록지 기본사항
	 *********************************************************/
	}else if ($type == 'ISPT_BASIC'){
		$IPIN	= $hce->IPIN;
		$rcpt	= $hce->rcpt;

		$sql = 'SELECT	m03_name AS name
				,		m03_jumin AS jumin
				,		EL.name AS edu_gbn
				,		rcpt.addr
				,		rcpt.addr_dtl
				,		rcpt.phone
				,		rcpt.mobile
				,		rcpt.grd_tel
				,		HR.name AS grd_rel
				,		rcpt.marry_gbn
				,		rcpt.cohabit_gbn
				,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
				FROM	hce_receipt AS rcpt
				INNER	JOIN	m03sugupja AS mst
						ON		mst.m03_ccode	= rcpt.org_no
						AND		mst.m03_mkind	= \'6\'
						AND		mst.m03_key		= rcpt.IPIN
						AND		mst.hce_seq		= rcpt.hce_seq
				INNER	JOIN	mst_jumin AS jumin
						ON		jumin.org_no= m03_ccode
						AND		jumin.gbn	= \'1\'
						AND		jumin.code	= m03_jumin
				INNER	JOIN	hce_gbn AS EL
						ON		EL.type	= \'EL\'
						AND		EL.code = rcpt.edu_gbn
				INNER	JOIN	hce_gbn AS HR
						ON		HR.type = \'HR\'
						AND		HR.code = rcpt.grd_rel
				WHERE	rcpt.org_no     = \''.$orgNo.'\'
				AND		rcpt.org_type	= \''.$hce->SR.'\'
				AND		rcpt.IPIN       = \''.$IPIN.'\'
				AND		rcpt.rcpt_seq   = \''.$rcpt.'\'';

		$row = $conn->get_array($sql);
		$jumin = SubStr($row['real_jumin'].'0000000',0,13);

		$data .= 'name='	.$row['name'];	//성명
		$data .= '&gender='	.$myF->issToGender($jumin);	//성별
		$data .= '&age='	.$myF->issToAge($jumin);	//연령
		$data .= '&jumin='	.$myF->issStyle($jumin);	//주민번호
		$data .= '&edu='	.$row['edu_gbn'];	//학력
		$data .= '&addr='	.$row['addr'].' '.$row['addr_dtl'];	//주소
		$data .= '&tel='	.$row['phone'];			//연락처
		$data .= '&hp='		.$row['mobile'];		//핸드폰
		$data .= '&alter='	.$row['grd_tel'];		//비상
		$data .= '&rel='	.$row['grd_rel'];		//관계
		$data .= '&marry='	.$row['marry_gbn'];		//결혼
		$data .= '&cohabit='.$row['cohabit_gbn'];	//동거

		echo $data;

		Unset($row);


	/*********************************************************
	 *	가족사항
	 *********************************************************/
	}else if ($type == 'FAMILY'){
		$SR		= $hce->SR;
		$IPIN	= $hce->IPIN;
		$rcpt	= $hce->rcpt;
		$rel	= $_POST['rel'];
		$IsBasic = $_POST['IsBasic'];

		if ($IsBasic == 'Y'){
			$rcpt = '0';
		}else if ($IsBasic == 'CLIENT_NORMAL'){
			$SR = $_POST['SR'];
			$IPIN = $_POST['key'];
			$rcpt = '-1';
		}

		if ($_POST['seq']){
			$IPIN = $_POST['seq'];
			$rcpt = '-1';
		}

		if ($rel == 'SHOW'){
			$sql = 'SELECT	code,name
					FROM	hce_gbn
					WHERE	type	= \'HR\'
					AND		use_yn	= \'Y\'';

			$arrRel = $conn->_fetch_array($sql,'code');
		}

		$sql = 'SELECT	family_rel AS rel
				,		family_nm AS name
				,		family_addr AS addr
				,		family_age AS age
				,		family_job AS job
				,		family_cohabit AS cohabit
				,		family_monthly AS monthly
				,		family_remark AS remark
				FROM	hce_family
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'rel='		.($rel == 'SHOW' ? $arrRel[$row['rel']]['name'] : $row['rel']);
			$data .= '&name='	.$row['name'];
			$data .= '&addr='	.StripSlashes($row['addr']);
			$data .= '&age='	.$row['age'];
			$data .= '&job='	.$row['job'];
			$data .= '&cohabit='.$row['cohabit'];
			$data .= '&monthly='.StripSlashes($row['monthly']);
			$data .= '&remark='	.StripSlashes($row['remark']);
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;


	/*********************************************************
	 *	초기면접기록지
	 *********************************************************/
	}else if ($type == 'INTERVIEW'){
		$IPIN	= $hce->IPIN;
		$rcpt	= $hce->rcpt;
		$IsBasic = $_POST['IsBasic'];

		if ($IsBasic == 'Y'){
			$rcpt = '0';
		}

		if ($_POST['seq']){
			$IPIN = $_POST['seq'];
			$rcpt = '-1';
		}

		$sql = 'SELECT	*
				FROM	hce_interview
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'';

		$row = $conn->get_array($sql);

		if ($row){
			$data .= 'iverDt='	.$row['iver_dt'];	//면접일
			$data .= '&iverNm='	.$row['iver_nm'];	//담당자명
			$data .= '&iverSSN='.$ed->en($row['iver_jumin']);//담당자 주민번호

			$data .= '&icGbn='		.$row['income_gbn'];		//경제상황
			$data .= '&icOther='	.$row['income_other'];		//경제상황 기타
			$data .= '&icMonthly='	.$row['income_monthly'];	//월소득
			$data .= '&icMain='		.$row['income_main'];		//주소득원

			$data .= '&grGbn='	.$row['generation_gbn'];	//세대유형
			$data .= '&grOther='.$row['generation_other'];	//세대유형 기타

			$data .= '&dlGbn='	.$row['dwelling_gbn'];		//주거형태
			$data .= '&dlOther='.$row['dwelling_other'];	//주거형태 기타

			$data .= '&hsGbn='	.$row['house_gbn'];		//주택구분
			$data .= '&hsOther='.$row['house_other'];	//주택구분 기타

			$data .= '&dpAmt='.$row['deposit_amt'];	//보증금
			$data .= '&rtAmt='.$row['rental_amt'];	//월세

			$data .= '&hlGbn='	.$row['health_gbn'];	//건강상태
			$data .= '&hlOther='.$row['health_other'];	//건강상태 기타

			$data .= '&disGbn='.$row['disease_gbn'];	//만성질횐

			$data .= '&hdGbn='	.$row['handicap_gbn'];		//장애여부
			$data .= '&hdOther='.$row['handicap_other'];	//장애유형

			$data .= '&dcGbn='	.$row['device_gbn'];	//보장구
			$data .= '&dcOther='.$row['device_other'];	//부장구 기타

			$data .= '&llGbn='	.$row['longlvl_gbn'];	//장기요양등급
			$data .= '&llOther='.$row['longlvl_other'];//등급 외

			$data .= '&orSvcNm='.$row['other_svc_nm'];	//타 서비스명
			$data .= '&orOrgNm='.$row['other_org_nm'];	//타 서비스 기관명

			$data .= '&reqSvc='.$row['req_svc_gbn'];	//신청서비스

			$data .= '&offGbn='		.$row['offer_gbn'];	//서비스 적격 여부
			$data .= '&noOffRsn='	.StripSlashes($row['nooffer_rsn']);	//부적격 사유

			$data .= '&svcRsnGbn='	.$row['svc_rsn_gbn'];	//서비스사유
			$data .= '&svcRsnOther='.$row['svc_rsn_other'];	//서비스사유 기타

			$data .= '&offSvcGbn='.$row['offer_svc_gbn'];//제공서비스 내용

			$data .= '&reqNm='	.$row['req_nm'];		//의뢰인명
			$data .= '&reqRel='	.$row['req_rel'];		//대상자와의 관계
			$data .= '&reqTel='	.$row['req_telno'];		//연락처
			$data .= '&reqGbn='	.$row['req_route_gbn'];	//의뢰경로

			$data .= '&remark='.StripSlashes($row['remark']);	//비고

			echo $data;
		}


	/*********************************************************
	 *	사례회의록
	 *********************************************************/
	}else if ($type == '51'){
		$IPIN	= $hce->IPIN;
		$rcpt	= $hce->rcpt;

		$sql = 'SELECT	meet_seq
				,		CMT.name AS meet_gbn
				,		meet_dt
				,		examiner
				,		attendee
				,		attendee_other
				,		decision_gbn
				,		decision_dt
				FROM	hce_meeting
				LEFT	JOIN	hce_gbn AS CMT
						ON		CMT.type	= \'CMT\'
						AND		CMT.code	= meet_gbn
						AND		CMT.use_yn	= \'Y\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		del_flag= \'N\'
				ORDER	BY meet_seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$attendeeCnt = SizeOf(Explode('&',$row['attendee']));

			$data .= 'seq='.$row['meet_seq'];
			$data .= '&gbn='.$row['meet_gbn'];
			$data .= '&meetDt='.$row['meet_dt'];
			$data .= '&examiner='.$row['examiner'];
			$data .= '&attendee='.$attendeeCnt;
			$data .= '&attendeeOther='.$row['attendee_other'];
			$data .= '&decisionGbn='.$row['decision_gbn'];
			$data .= '&decisionDt='.$row['decision_dt'];

			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;


	/*********************************************************
	 *	사례회의록 수정
	 *********************************************************/
	}else if ($type == '52'){
		$IPIN	= $hce->IPIN;
		$rcpt	= $_POST['r_seq'] != '' ? $_POST['r_seq'] : $hce->rcpt;
		$meet	= $_POST['seq'];
	

		$sql = 'SELECT	*
				FROM	hce_meeting
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		meet_seq= \''.$meet.'\'';
		
		$row = $conn->get_array($sql);

		if ($row){
			$tmp = Explode('&',$row['attendee']);

			foreach($tmp as $att){
				$attendee .= ($attendee ? chr(1) : '').$ed->en($att);
			}

			$data .= 'meetGbn='			.$row['meet_gbn'];
			$data .= '&meetDt='			.$row['meet_dt'];
			$data .= '&examinerJumin='	.$ed->en($row['examiner_jumin']);
			$data .= '&examiner='		.$row['examiner'];
			$data .= '&attendee='		.$attendee;
			$data .= '&attendeeOther='	.StripSlashes($row['attendee_other']);
			$data .= '&lifeLvl='		.StripSlashes($row['life_lvl']);
			$data .= '&reqRsn='			.StripSlashes($row['req_rsn']);
			$data .= '&decisionGbn='	.$row['decision_gbn'];
			$data .= '&decisionDt='		.$row['decision_dt'];
			$data .= '&decisionRsn='	.StripSlashes($row['decision_rsn']);
			$data .= '&decisionSvc='	.$row['decision_svc'];
		}

		Unset($row);

		echo $data;


	/*********************************************************
	 *	서비스계획서
	 *********************************************************/
	}else if ($type == '61'){
		$sql = 'SELECT	plan_seq
				,		plan_dt
				,		planer
				FROM	hce_plan_sheet
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		del_flag= \'N\'
				ORDER	BY plan_dt /*plan_seq*/ DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'seq='.$row['plan_seq'];
			$data .= '&dt='.$row['plan_dt'];
			$data .= '&er='.$row['planer'];
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;


	}else if ($type == '62'){
		$planSeq = $_POST['seq'];
		$rcpt	= $_POST['r_seq'] != '' ? $_POST['r_seq'] : $hce->rcpt;

		$sql = 'SELECT	meet_seq
				,		plan_dt
				,		planer_jumin
				,		planer
				,		needs
				,		problem
				,		goal
				,		svc_period
				,		svc_content
				,		svc_method
				,		remark
				FROM	hce_plan_sheet
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		plan_seq= \''.$planSeq.'\'
				AND		del_flag= \'N\'
				ORDER	BY plan_dt DESC';

		$row = $conn->get_array($sql);

		$data .= 'seq='			.$row['meet_seq'];
		$data .= '&dt='			.$row['plan_dt'];
		$data .= '&erJumin='	.$ed->en($row['planer_jumin']);
		$data .= '&er='			.$row['planer'];
		$data .= '&needs='		.StripSlashes($row['needs']);
		$data .= '&problem='	.StripSlashes($row['problem']);
		$data .= '&goal='		.StripSlashes($row['goal']);
		$data .= '&svcPeriod='	.StripSlashes($row['svc_period']);
		$data .= '&svcContent='	.StripSlashes($row['svc_content']);
		$data .= '&svcMethod='	.StripSlashes($row['svc_method']);
		$data .= '&remark='		.StripSlashes($row['remark']);

		echo $data;


	/*********************************************************
	 *	이용 안내 및 동의서
	 *********************************************************/
	}else if ($type == '71'){
		$sql = 'SELECT	cont_dt
				,		per_nm
				,		per_jumin
				FROM	hce_consent_form
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$row = $conn->get_array($sql);

		if ($row){
			$data .= 'contDt='.$row['cont_dt'];
			$data .= '&perNm='.$row['per_nm'];
			$data .= '&perJumin='.$ed->en($row['per_jumin']);
		}

		Unset($row);

		echo $data;

	}else if ($type == '71_LIST'){
		$sql = 'SELECT	cont_seq
				,		svc_nm
				,		content
				,		remark
				FROM	hce_consent_svc
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'svcNm='.StripSlashes($row['svc_nm']);
			$data .= '&cont='.StripSlashes($row['content']);
			$data .= '&other='.StripSlashes($row['remark']);
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;


	/*********************************************************
	 *	기관찾기
	 *********************************************************/
	}else if ($type == 'FIND_ORG'){
		$code = $_POST['code'];

		$sql = 'SELECT	DISTINCT
						m00_mcode AS org_no
				,		m00_code1 AS org_cd
				,		m00_store_nm AS org_nm
				FROM	m00center
				WHERE	m00_code1 = \''.$code.'\'
				ORDER	BY org_nm';


	/*********************************************************
	 *	선정기준일자
	 *********************************************************/
	}else if ($type == 'FIND_CHOICE_DATE'){
		$sql = 'SELECT	chic_dt
				FROM	hce_choice
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$chicDt	= $conn->get_data($sql);

		echo $chicDt;


	/*********************************************************
	 *	최저생계비
	 *********************************************************/
	}else if ($type == 'GET_COST_OF_LIVING'){
		$year	= $_POST['year'];
		$gbn	= $_POST['gbn'];

		$sql = 'SELECT	cost
				FROM	cost_of_living
				WHERE	year	= \''.$year.'\'
				AND		per_gbn	= \''.$gbn.'\'';

		$cost = $conn->get_data($sql);

		echo $cost;


	/*********************************************************
	 *	대상자정보
	 *********************************************************/
	}else if ($type == 'TARGET_INFO'){
		$svcCd = $_POST['svcCd'];
		$key = $_POST['key'];
		$wrkType = $_POST['wrkType'];

		if ($wrkType == 'INTERVIEW_REG'){
			$sql = 'SELECT	m03_jumin AS jumin
					,		m03_name AS name
					,		m03_juso1 AS addr
					,		m03_juso2 AS addr_dtl
					,		m03_tel AS phone
					,		m03_hp AS mobile
					,		SUBSTR(m03_yoyangsa5_nm,3,2) AS edu_gbn
					,		SUBSTR(m03_yoyangsa5_nm,5,1) AS rel_gbn
					,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
					FROM	m03sugupja
					LEFT	JOIN	mst_jumin AS jumin
							ON		jumin.org_no= m03_ccode
							AND		jumin.gbn	= \'1\'
							AND		jumin.code	= m03_jumin
					WHERE	m03_ccode	= \''.$orgNo.'\'
					AND		m03_mkind	= \''.$svcCd.'\'
					AND		m03_key		= \''.$key.'\'';
		}else{
			$sql = 'SELECT	jumin AS real_jumin,name,addr,addr_dtl,phone,mobile,edu_gbn,rel_gbn
					FROM	care_client_normal
					WHERE	org_no		= \''.$orgNo.'\'
					AND		normal_sr	= \''.$svcCd.'\'
					AND		normal_seq	= \''.$key.'\'';
		}

		$row = $conn->get_array($sql);

		//학력
		$sql = 'SELECT	name
				FROM	hce_gbn
				WHERE	type	= \'EL\'
				AND		use_yn	= \'Y\'
				AND		code	= \''.$row['edu_gbn'].'\'';

		$eduGbn = $conn->get_data($sql);

		//종교
		$sql = 'SELECT	name
				FROM	hce_gbn
				WHERE	type	= \'RG\'
				AND		use_yn	= \'Y\'
				AND		code	= \''.$row['rel_gbn'].'\'';

		$relGbn = $conn->get_data($sql);

		$name = $row['name'];

		if ($row['real_jumin']){
			if(strlen($row['real_jumin']) == '7'){ 
				$row['real_jumin'] = $row['real_jumin'].'000000';
			}

			$gender = $myF->issToGender($row['real_jumin']);
			$age = $myF->issToAge($row['real_jumin']);
			$jumin = $myF->issStyle($row['real_jumin']);
		}
		$addr = $row['addr'].' '.$row['addr_dtl'];
		$telno = $myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');

		Unset($row);

		$data .= 'gender='.$gender;
		$data .= '&name='.$name;
		$data .= '&age='.$age;
		$data .= '&jumin='.$jumin;
		$data .= '&edu='.$eduGbn;
		$data .= '&rel='.$relGbn;
		$data .= '&addr='.$addr;
		$data .= '&telno='.$telno;

		if ($wrkType == 'ACTUAL_RESEARCH_REG'){
			$sql = 'SELECT	COUNT(*)
					FROM	hce_interview
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$svcCd.'\'
					AND		IPIN	= \''.$key.'\'
					AND		rcpt_seq= \'-1\'';

			$cnt = $conn->get_data($sql);

			$data .= '&cnt='.$cnt;
		}

		echo $data;


	}else{
		echo $type;
	}

	include_once('../inc/_db_close.php');
?>