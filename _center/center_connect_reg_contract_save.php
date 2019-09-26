<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$today		= Date('Ymd');
	$orgNo		= $_POST['orgNo']; //기관코드
	$orgContDt	= $_POST['orgContDt']; //원 계약일자
	$startDt	= str_replace('-','',$_POST['txtStartDt']); //시작일자
	$contDt		= str_replace('-','',$_POST['txtContDt']); //계약일자
	$fromDt		= str_replace('-','',$_POST['txtFromDt']); //적용일자
	$toDt		= str_replace('-','',$_POST['txtToDt']); //종료일자
	$acctGbn	= $_POST['optAcctGbn']; //청구구분
	$billPrt	= $_POST['optBillPrt']; //계산서발행여부
	$bankNm		= $_POST['txtBankNm']; //은행명
	$bankNo		= $_POST['txtBankNo']; //계좌번호
	$bankAcct	= $_POST['txtBankAcct']; //예금주
	$bankGbn	= $_POST['optBankGbn']; //계좌구분
	$birthday	= str_replace('-','',$_POST['txtBirthday']); //생년월일
	$bizNo		= str_replace('-','',$_POST['txtBizNo']); //사업자번호
	$transDt	= IntVal($_POST['txtTransDt']); //이체예정일
	$CMSNo		= IntVal($_POST['txtCMSNo']); //CMS 번호
	//$CMSList	= Explode(',',$_POST['txtCMSList']); //CMS 리스트
	$rqtDt		= str_replace('-','',$_POST['txtRqtDt']); //요청일자
	//$regDt		= str_replace('-','',$_POST['txtRegDt']); //등록일자
	$rqtNm		= $_POST['txtRqtNm']; //요청자
	$regNm		= $_POST['txtRegNm']; //등록자
	$company	= $_POST['cboCompany']; //연결회사
	$branch		= $_POST['cboBranch']; //연결지사
	$person		= $_POST['cboPerson']; //담당자
	$rsCd		= $_POST['cboRsCd']; //사유코드
	$rsDtlCd	= $_POST['cboRsDtlCd']; //사유상세코드
	$rsStr		= AddSlashes($_POST['txtRsStr']); //사유내용
	$areaCd		= $_POST['cboArea']; //지역코드
	$groupCd	= $_POST['cboGroup']; //그룹코드
	//$memo		= AddSlashes($_POST['txtMemo']); //메모
	$contCom	= $_POST['cboContCom']; //계약회사
	$reCont		= $_POST['reCont']; //재계약여부
	$taxbillYn	= $_POST['taxbillYn']; //세금계산서 발행여부
	$popYn		= $_POST['popYn']; //팝업해제여부
	$acctBeDt	= str_replace('-', '', $_POST['acctBeDt']); //계약예정일
	$cmsStartYm	= str_replace('-', '', $_POST['cmsStartYm']); //자동이체 시작년월
	$adjustFeeYn= $_POST['adjustFeeYn']; //조정요금관리기관 여부
	$adjustFeeNote = addslashes($_POST['adjustFeeNote']); //

	$nurseAreaCd	= $_POST['cboNurseArea']; //방문간호 지역
	$nurseGroupCd	= $_POST['cboNurseGroup']; //방문간호 지역

	//if (!$toDt) $toDt = $fromDt;

	$svcToDt = $toDt;

	//이전기관정보
	$oldOrgCd	= $_POST['oldOrgCd'];
	$oldOrgDt	= str_replace('-','',$_POST['txtOldOrgDt']);


	if ($rsCd == '2' || $rsCd == '4'){ //일시중지, 해지
		$sql = 'SELECT	to_dt
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		from_dt	 = \''.$orgContDt.'\'';

		$tmpToDt = $conn->get_data($sql);

		if (!$fromDt) $fromDt = $contDt;

		//종료일자를 해지일자 전일로 설정한다.
		$svcToDt = $myF->dateAdd('day',-1,$contDt,'Ymd');

		//종료일을 해지일자로 설정한다.
		//$svcToDt = $contDt;

		if ($tmpToDt >= $contDt){
			$sql = 'UPDATE	cv_reg_info
					SET		org_to_dt	= to_dt
					,		to_dt		= \''.$svcToDt.'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		from_dt	 = \''.$orgContDt.'\'
					AND		rs_cd	!= \'2\'
					AND		rs_cd	!= \'4\'';

			$query[] = $sql;
		}
	}else{
		$sql = 'SELECT	rs_cd, rs_dtl_cd, from_dt, to_dt
				FROM	cv_reg_info
				WHERE	org_no	= \''.$orgNo.'\'
				AND		from_dt	= \''.$orgContDt.'\'';

		$row = $conn->get_array($sql);

		if ($row['rs_cd'] == '3'){
			if ($row['from_dt'] > $fromDt ){
				$conn->close();
				echo '이전 계약시작일보다 과거일자는 입력할 수 없습니다.\n확인하여 주십시오.';
				exit;
			}else if ($row['to_dt'] >= $fromDt){
				$sql = 'UPDATE	cv_reg_info
						SET		org_to_dt	= to_dt
						,		to_dt		= \''.$myF->dateAdd('day', -1, $fromDt, 'Ymd').'\'
						,		update_id	= \''.$_SESSION['userCode'].'\'
						,		update_dt	= NOW()
						WHERE	org_no	= \''.$orgNo.'\'
						AND		from_dt	= \''.$orgContDt.'\'';

				$query[] = $sql;
			}
		}else{
			//계약기간 중복을 확인한다.
			$sql = 'SELECT	COUNT(*)
					FROM	cv_reg_info
					WHERE	org_no   = \''.$orgNo.'\'
					AND		from_dt != \''.$fromDt.'\'
					AND		CASE WHEN \''.$fromDt.'\' BETWEEN from_dt AND to_dt THEN 1
								 WHEN \''.$toDt.'\' BETWEEN from_dt AND to_dt THEN 1
								 WHEN from_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
								 WHEN to_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1 ELSE 0 END = 1';
			$duplicateCnt = $conn->get_data($sql);

			if ($duplicateCnt > 0){
				$conn->close();
				echo '입력하신 계약기간이 중복됩니다. 계약기간을 다시 확인하여 주십시오.';
				exit;
			}
		}
	}


	#if ($CMSNo){
	#	$CMSList[]	= $CMSNo;
	#}
	#$CMSList = array_unique($CMSList);

	$CMSList = Explode('?',$_POST['CMSList']);

	if (is_array($CMSList)){
		foreach($CMSList as $tmpIdx => $R){
			parse_str($R,$R);
			$CMSNo = $R['CMSNo'];
		}
	}

	if (!$CMSNo) $CMSNo = '';
	if (!$transDt) $transDt = '';


	//가상계좌
	$VrList = Explode('?',$_POST['VrList']);


	//도메인
	$sql = 'SELECT	b00_domain
			FROM	b00branch
			WHERE	b00_code = \''.$company.'\'';

	$domain = $conn->get_data($sql);


	//서비스 요금표 수정 - 계약서비스 수정을 막음(2015.11.16)
	$sql = 'UPDATE	cv_svc_fee
			SET		to_dt	 = \''.$svcToDt.'\'
			,		del_flag = \'Y\'
			,		mod_gbn	 = \'9\'
			,		update_id= \''.$_SESSION['userCode'].'\'
			,		update_dt= NOW()
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		from_dt >= \''.$svcToDt.'\'
			AND		del_flag = \'N\'';

	#$query[] = $sql;

	$sql = 'UPDATE	cv_svc_fee
			SET		to_dt	 = \''.$svcToDt.'\'
			,		mod_gbn	 = \'9\'
			,		update_id= \''.$_SESSION['userCode'].'\'
			,		update_dt= NOW()
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		to_dt	>= \''.$svcToDt.'\'
			AND		del_flag = \'N\'';

	#$query[] = $sql;

	$sql = 'UPDATE	cv_svc_fee
			SET		to_dt	 = \''.$svcToDt.'\'
			,		del_flag = CASE WHEN mod_gbn = \'9\' THEN \'N\' ELSE del_flag END
			,		mod_gbn	 = \'7\'
			,		update_id= \''.$_SESSION['userCode'].'\'
			,		update_dt= NOW()
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		to_dt	 < \''.$svcToDt.'\'';

	#$query[] = $sql;


	$sql = 'SELECT	COUNT(*)
			FROM	cv_reg_info
			WHERE	org_no = \''.$orgNo.'\'
			AND		from_dt= \''.$fromDt.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	cv_reg_info
				SET		link_company= \''.$company.'\'
				,		link_branch	= \''.$branch.'\'
				,		link_person	= \''.$person.'\'
				,		start_dt	= \''.$startDt.'\'
				,		cont_dt		= \''.$contDt.'\'
				,		to_dt		= \''.$toDt.'\'
				,		acct_gbn	= \''.$acctGbn.'\'
				,		bill_yn		= \''.$billPrt.'\'
				,		bank_nm		= \''.$bankNm.'\'
				,		bank_no		= \''.$bankNo.'\'
				,		bank_acct	= \''.$bankAcct.'\'
				,		bank_gbn	= \''.$bankGbn.'\'
				,		birthday	= \''.$birthday.'\'
				,		bizno		= \''.$bizNo.'\'
				,		trans_day	= \''.$transDt.'\'
				,		rqt_dt		= \''.$rqtDt.'\'
				,		rqt_nm		= \''.$rqtNm.'\'
				/*,		reg_dt		= \''.$regDt.'\'*/
				,		reg_nm		= \''.$regNm.'\'
				,		rs_cd		= \''.$rsCd.'\'
				,		rs_dtl_cd	= \''.$rsDtlCd.'\'
				,		rs_str		= \''.$rsStr.'\'
				,		area_cd		= \''.$areaCd.'\'
				,		group_cd	= \''.$groupCd.'\'
				,		nurse_area_cd	= \''.$nurseAreaCd.'\'
				,		nurse_group_cd	= \''.$nurseGroupCd.'\'
				,		cont_com	= \''.$contCom.'\'
				,		pop_yn		= \''.$popYn.'\'
				,		adjust_fee_yn = \''.$adjustFeeYn.'\'
				,		adjust_fee_note = \''.$adjustFeeNote.'\'
				,		cms_no		= CASE WHEN IFNULL(cms_no,\'\') != \'\'THEN cms_no ELSE \''.$CMSNo.'\' END
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		from_dt		= \''.$fromDt.'\'';
	}else{
		$sql = 'INSERT INTO cv_reg_info (
				 org_no
				,link_company
				,link_branch
				,link_person
				,start_dt
				,cont_dt
				,from_dt
				,to_dt
				,acct_gbn
				,bill_yn
				,bank_nm
				,bank_no
				,bank_acct
				,bank_gbn
				,birthday
				,bizno
				,trans_day
				,rqt_dt
				,rqt_nm
				/*,reg_dt*/
				,reg_nm
				,rs_cd
				,rs_dtl_cd
				,rs_str
				,taxbill_yn
				,pop_yn
				,area_cd
				,group_cd
				,nurse_area_cd
				,nurse_group_cd
				,cms_no
				,cont_com
				,adjust_fee_yn
				,adjust_fee_note
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$company.'\'
				,\''.$branch.'\'
				,\''.$person.'\'
				,\''.$startDt.'\'
				,\''.$contDt.'\'
				,\''.$fromDt.'\'
				,\''.$toDt.'\'
				,\''.$acctGbn.'\'
				,\''.$billPrt.'\'
				,\''.$bankNm.'\'
				,\''.$bankNo.'\'
				,\''.$bankAcct.'\'
				,\''.$bankGbn.'\'
				,\''.$birthday.'\'
				,\''.$bizNo.'\'
				,\''.$transDt.'\'
				,\''.$rqtDt.'\'
				,\''.$rqtNm.'\'
				/*,\''.$regDt.'\'*/
				,\''.$regNm.'\'
				,\''.$rsCd.'\'
				,\''.$rsDtlCd.'\'
				,\''.$rsStr.'\'
				,\''.$taxbillYn.'\'
				,\''.$popYn.'\'
				,\''.$areaCd.'\'
				,\''.$groupCd.'\'
				,\''.$nurseAreaCd.'\'
				,\''.$nurseGroupCd.'\'
				,\''.$CMSNo.'\'
				,\''.$contCom.'\'
				,\''.$adjustFeeYn.'\'
				,\''.$adjustFeeNote.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$query[] = $sql;


	if ($cnt > 0){
		$sql = 'UPDATE	cv_reg_info
				SET		taxbill_yn = \''.$taxbillYn.'\'
				WHERE	org_no = \''.$orgNo.'\'';

		$query[] = $sql;
	}


	//
	$sql = 'SELECT	COUNT(*)
			FROM	b02center
			WHERE	b02_center = \''.$orgNo.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	b02center
				SET		b02_branch	= \''.$branch.'\'
				,		b02_person	= \''.$person.'\'
				,		b02_date	= \''.$startDt.'\'
				,		cms_cd		= \''.$CMSNo.'\'
				,		from_dt		= \''.$fromDt.'\'
				,		to_dt		= \''.$toDt.'\'
				,		care_area	= \''.$areaCd.'\'
				,		care_group	= \''.$groupCd.'\'
				/*,		b02_other	= \''.$memo.'\'*/
				WHERE	b02_center	= \''.$orgNo.'\'';
	}else{
		$sql = 'INSERT INTO b02center (
				 b02_center
				,b02_kind
				,b02_branch
				,b02_person
				,b02_date
				,cms_cd
				,from_dt
				,to_dt
				,care_area
				,care_group
				/*,b02_other*/
				) VALUES (
				 \''.$orgNo.'\'
				,\'0\'
				,\''.$branch.'\'
				,\''.$person.'\'
				,\''.$startDt.'\'
				,\''.$CMSNo.'\'
				,\''.$fromDt.'\'
				,\''.$toDt.'\'
				,\''.$areaCd.'\'
				,\''.$groupCd.'\'
				/*,\''.$memo.'\'*/
				)';
	}

	$query[] = $sql;


	//
	$sql = 'UPDATE	m00center
			SET		m00_start_date	= \''.$startDt.'\'
			,		m00_cont_date	= \''.$contDt.'\'
			,		m00_domain		= \''.$domain.'\'
			WHERE	m00_mcode		= \''.$orgNo.'\'';

	$query[] = $sql;


	$sql = 'DELETE
			FROM	cv_cms_list
			WHERE	org_no = \''.$orgNo.'\'';
	$query[] = $sql;

	//CMS 리스트
	if (is_array($CMSList)){
		/*
		foreach($CMSList as $CMSListNo){
			if ($CMSListNo){
				$sql = 'INSERT INTO cv_cms_list VALUES (\''.$orgNo.'\',\''.$CMSListNo.'\')';
				$query[] = $sql;
			}
		}
		*/

		foreach($CMSList as $tmpIdx => $R){
			parse_str($R,$R);

			$sql = 'INSERT INTO cv_cms_list VALUES (\''.$orgNo.'\',\''.$R['CMSNo'].'\',\''.$R['CMSCom'].'\')';
			$query[] = $sql;
		}
	}


	//가상계좌
	$sql = 'DELETE
			FROM	cv_vr_list
			WHERE	org_no = \''.$orgNo.'\'';
	$query[] = $sql;

	if (is_array($VrList)){
		foreach($VrList as $tmpIdx => $R){
			parse_str($R,$R);

			if ($R['vrNo'] && $R['bankCd']){
				$sql = 'INSERT INTO cv_vr_list (org_no,vr_no,bank_cd,key_yn) VALUES (\''.$orgNo.'\',\''.$R['vrNo'].'\',\''.$R['bankCd'].'\',\''.$R['keyYn'].'\')';
				$query[] = $sql;
			}
		}
	}


	//이전기관정보
	if ($oldOrgCd && $oldOrgDt){
		$sql = 'SELECT	seq
				FROM	center_his
				WHERE	org_no	= \''.$orgNo.'\'
				AND		gbn		= \'01\'';
		$seq = $conn->get_data($sql);

		if ($seq > 0){
			$sql = 'UPDATE	center_his
					SET		dt	= \''.$oldOrgDt.'\'
					,		val	= \''.$oldOrgCd.'\'
					,		update_id = \''.$_SESSION['userCode'].'\'
					,		update_dt = NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		seq		= \''.$seq.'\'';
		}else{
			$sql = 'SELECT	IFNULL(MAX(seq),0)+1
					FROM	center_his
					WHERE	org_no = \''.$orgNo.'\'';
			$seq = $conn->get_data($sql);

			$sql = 'INSERT INTO center_his (org_no,seq,gbn,dt,val,insert_id,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$seq.'\'
					,\'01\'
					,\''.$oldOrgDt.'\'
					,\''.$oldOrgCd.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}

		$query[] = $sql;
	}

	//계약예정일 및 CMS 자동이체 시작일
	$sql = 'REPLACE INTO center_cont_info (org_no, acct_bedt, cms_start_ym) VALUES (
			 \''.$orgNo.'\'
			,\''.$acctBeDt.'\'
			,\''.$cmsStartYm.'\'
			)';

	$query[] = $sql;


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 if ($debug) echo $conn->error_msg.chr(13).$conn->error_query;
			 echo '!!! 에러발생 !!!';
			 exit;
		}
	}

	$conn->commit();

	//적용
	$userCode = $orgNo;
	include_once('../inc/set_val.php');
	include_once('../inc/_db_close.php');
?>