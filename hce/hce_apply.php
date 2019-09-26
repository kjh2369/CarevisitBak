<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$userCd = $_SESSION['userCode'];
	$userArea = $_SESSION['userArea'];
	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$yymm	= Date('Ym');

	if (!$sr){
		echo 9;
		exit;
	}


	
	/*********************************************************
	 *	사례접수
	 *********************************************************/
	if ($type == '11' || $type == '12'){
		//접수자
		$rcptSeq	= $_POST['txtRctSeq'];	//순번
		$rcptDt		= Str_Replace('-','',$_POST['txtRctDt']);	//일자
		$rcptNm		= $_POST['txtRctNm'];	//성명
		$rcptJumin	= $ed->de($_POST['txtRctJumin']);//주민번호
		$rcptGbn	= $_POST['optRctGbn'];	//접수방법

		//대상자
		$elderNm		= $_POST['txtElderNm'];		//성명
		$elderJumin		= $_POST['txtElderSsn'];	//주민번호
		$elderPostNo	= $_POST['txtElderPostno1'].$_POST['txtElderPostno2'];	//우편번호
		$elderAddr		= $_POST['txtElderAddr'];	//주소
		$elderAddrDtl	= $_POST['txtElderAddrDtl'];//상세주소
		$elderPhone		= Str_Replace('-','',$_POST['txtElderPhone']);	//연락처
		$elderMobile	= Str_Replace('-','',$_POST['txtElderMobile']);	//휴대폰
		$elderEducLvl	= $_POST['cboEducLvl'];		//학력
		$elderReligion	= $_POST['cboReligion'];	//종교
		$elderMarry		= $_POST['cboMarry'];		//결혼여부
		$elderCohabit	= $_POST['cboCohabit'];		//동거여부

		if (!Is_Numeric($elderJumin)) $elderJumin = $ed->de($elderJumin);

		//보호자
		$guardNm	= $_POST['txtGuardNm'];		//성명
		$guardRel	= $_POST['cboGuardRel'];	//관계
		$guardTelno	= Str_Replace('-','',$_POST['txtGuardTelNo']);	//연락처
		$guardAddr	= $_POST['txtGuardAddr'];	//주소

		//의뢰자
		$reqorNm	= $_POST['txtReqorNm'];	//성명
		$reqorRel	= $_POST['cboReqorRel'];//대상자와의 관계
		$reqorTel	= Str_Replace('-','',$_POST['txtReqorTel']);//연락처

		//상담내용
		$talkText	= AddSlashes($_POST['txtTalkText']);

		//신규,기존여부
		$newYn	= $_POST['txtNewYn'];

		//IPIN
		if ($type == '11'){
			$IPIN = $hce->IPIN;
		}else{
			$IPIN = '';
		}


		if (!$IPIN){
			$sql = 'SELECT	m03_key
					FROM	m03sugupja
					WHERE	m03_ccode	= \''.$orgNo.'\'
					AND		m03_mkind	= \'6\'
					AND		m03_jumin	= \''.$elderJumin.'\'';

			$IPIN = $conn->get_data($sql);
		}

		//기존데이타 존재여부
		if ($newYn){
			$sql = 'SELECT	COUNT(*)
					FROM	hce_receipt
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$sr.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcptSeq.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){?>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<script type="text/javascript">
					alert('중복되는 접수일자가 있습니다. 확인 후 다시 작성하여 주십시오.');
					history.back();
				</script><?
				exit;
			}
		}

		//차수
		if ($newYn){
			$sql = 'SELECT	IFNULL(MAX(hce_seq),0)
					FROM	hce_receipt
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$sr.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		del_flag= \'N\'';
		}else{
			$sql = 'SELECT	hce_seq
					FROM	hce_receipt
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$sr.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcptSeq.'\'
					AND		del_flag= \'N\'';
		}


		$hceSeq = $conn->get_data($sql);

		if (Empty($hceSeq)) $hceSeq = 0;
		if ($newYn) $hceSeq ++;

		//마지막 차수
		$sql = 'SELECT	hce_seq
				FROM	m03sugupja
				WHERE	m03_ccode	= \''.$orgNo.'\'
				AND		m03_mkind	= \'6\'
				AND		m03_key		= \''.$IPIN.'\'';

		$rcptMax = $conn->get_data($sql);

		if ($hceSeq > $rcptMax) $rcptMax = $hceSeq;

		$elderGender= SubStr($elderJumin,6,1);	//성별
		$elderAge	= $myF->issToAge($elderJumin);

		$conn->begin();

		//접수대장 hce_receipt
		if ($newYn){
			$sql = 'SELECT	IFNULL(MAX(rcpt_seq),0)+1
					FROM	hce_receipt
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$sr.'\'
					AND		IPIN	= \''.$IPIN.'\'';

			$rcptSeq = $conn->get_data($sql);

			$sql = 'INSERT INTO hce_receipt(
					 org_no
					,org_type
					,IPIN
					,rcpt_seq
					,rcpt_dt
					,create_id
					,create_dt
					,counsel_type
					,hce_seq
					,phone
					,mobile
					,postno
					,addr
					,addr_dtl
					,grd_nm
					,grd_rel
					,grd_tel
					,grd_addr
					,reqor_rel
					,reqor_nm
					,reqor_telno
					,rcver_nm
					,rcver_ssn
					,marry_gbn
					,cohabit_gbn
					,edu_gbn
					,rel_gbn
					,counsel_text) VALUES (
					 \''.$orgNo.'\'			/*org_no*/
					,\''.$sr.'\'			/*org_type*/
					,\''.$IPIN.'\'			/*IPIN*/
					,\''.$rcptSeq.'\'		/*rcpt_seq*/
					,\''.$rcptDt.'\'		/*rcpt_dt*/
					,\''.$userCd.'\'		/*create_id*/
					,NOW()					/*create_dt*/
					,\''.$rcptGbn.'\'		/*counsel_type*/
					,\''.$hceSeq.'\'		/*hce_seq*/
					,\''.$elderPhone.'\'	/*phone*/
					,\''.$elderMobile.'\'	/*mobile*/
					,\''.$elderPostNo.'\'	/*postno*/
					,\''.$elderAddr.'\'		/*addr*/
					,\''.$elderAddrDtl.'\'	/*addr_dtl*/
					,\''.$guardNm.'\'		/*grd_nm*/
					,\''.$guardRel.'\'		/*grd_rel*/
					,\''.$guardTelno.'\'	/*grd_tel*/
					,\''.$guardAddr.'\'		/*grd_addr*/
					,\''.$reqorRel.'\'		/*reqor_rel*/
					,\''.$reqorNm.'\'		/*reqor_nm*/
					,\''.$reqorTel.'\'		/*reqor_telno*/
					,\''.$rcptNm.'\'		/*rcver_nm*/
					,\''.$rcptJumin.'\'		/*rcver_ssn*/
					,\''.$elderMarry.'\'	/*marry_gbn*/
					,\''.$elderCohabit.'\'	/*cohabit_gbn*/
					,\''.$elderEducLvl.'\'	/*edu_gbn*/
					,\''.$elderReligion.'\'	/*rel_gbn*/
					,\''.$talkText.'\'		/*counsel_text*/
					)';
		}else{
			$sql = 'UPDATE	hce_receipt
					SET		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()
					,		rcpt_dt		= \''.$rcptDt.'\'
					,		counsel_type= \''.$rcptGbn.'\'
					,		phone		= \''.$elderPhone.'\'
					,		mobile		= \''.$elderMobile.'\'
					,		postno		= \''.$elderPostNo.'\'
					,		addr		= \''.$elderAddr.'\'
					,		addr_dtl	= \''.$elderAddrDtl.'\'
					,		grd_nm		= \''.$guardNm.'\'
					,		grd_rel		= \''.$guardRel.'\'
					,		grd_tel		= \''.$guardTelno.'\'
					,		grd_addr	= \''.$guardAddr.'\'
					,		reqor_rel	= \''.$reqorRel.'\'
					,		reqor_nm	= \''.$reqorNm.'\'
					,		reqor_telno	= \''.$reqorTel.'\'
					,		rcver_nm	= \''.$rcptNm.'\'
					,		rcver_ssn	= \''.$rcptJumin.'\'
					,		marry_gbn	= \''.$elderMarry.'\'
					,		cohabit_gbn	= \''.$elderCohabit.'\'
					,		edu_gbn		= \''.$elderEducLvl.'\'
					,		rel_gbn		= \''.$elderReligion.'\'
					,		counsel_text= \''.$talkText.'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$sr.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcptSeq.'\'';
		}

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();?>
			 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			 <script type="text/javascript">
				alert('1.데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				history.back();
			 </script><?
			 exit;
		}
		
		
		$sql = 'UPDATE	m03sugupja
				SET		m03_tel				= \''.$elderPhone.'\'
				,		m03_hp				= \''.$elderMobile.'\'
				,		m03_post_no			= \''.$guardNm.'\'
				,		m03_juso1			= \''.$elderAddr.'\'
				,		m03_juso2			= \''.$elderAddrDtl.'\'
				,		m03_yboho_name		= \''.$guardNm.'\'
				,		m03_yoyangsa4_nm	= \''.$guardAddr.'\'
				,		m03_yboho_phone		= \''.$grdTel.'\'
				,		m03_yoyangsa5_nm	= \''.$elderMarry.$elderCohabit.$elderEducLvl.$elderReligion.'\'';
		
		$sql .= '
				WHERE	m03_ccode			= \''.$orgNo.'\'
				AND		m03_jumin			= \''.$elderJumin.'\'';
		

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();?>
			 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			 <script type="text/javascript">
				alert('1.데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				history.back();
			 </script><?
			 exit;
		}

		//접수순번 수정
		$sql = 'UPDATE	m03sugupja
				SET		hce_seq		= \''.$hceSeq.'\'
				WHERE	m03_ccode	= \''.$orgNo.'\'
				AND		m03_mkind	= \'6\'
				AND		m03_key		= \''.$IPIN.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();?>
			 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			 <script type="text/javascript">
				alert('2.데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				history.back();
			 </script><?
			 exit;
		}

		//진행저장
		$sql = 'SELECT	COUNT(*)
				FROM	hce_proc
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$sr.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcptSeq.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt == 0){
			$sql = 'INSERT INTO hce_proc (org_no,org_type,IPIN,rcpt_seq,hce_seq) VALUES (\''.$orgNo.'\',\''.$sr.'\',\''.$IPIN.'\',\''.$rcptSeq.'\',\''.$hceSeq.'\')';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();?>
				 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				 <script type="text/javascript">
					alert('3.데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
					history.back();
				 </script><?
				 exit;
			}
		}

		$sql = 'UPDATE	hce_proc
				SET		rcpt_dt = \''.$rcptDt.'\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$sr.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcptSeq.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();?>
			 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			 <script type="text/javascript">
				alert('4.데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				history.back();
			 </script><?
			 exit;
		}

		$conn->commit();

		$hce->IPIN	= $IPIN;
		$hce->rcpt	= $rcptSeq;
		$hce->let();?>
		<script type="text/javascript">
			location.replace('../hce/hce_body.php?sr=<?=$sr;?>&type=11');
			top.frames['frmTop'].lfTarget('<?=$IPIN;?>','<?=$rcptSeq;?>');
			top.frames['frmLeft'].lfShowMenu('<?=$hce->IPIN;?>');
		</script><?



	/*********************************************************
	 *	초기면접기록지
	 *********************************************************/
	}else if ($type == '21'){
		$IPIN	= $hce->IPIN;	//고유번호
		$rcpt	= $hce->rcpt;	//접수일자
		$sr		= $hce->SR;		//재가지원, 자원연계구분

		if ($_POST['IsHCE'] == 'N'){
			$IPIN = $_POST['IPIN'];
			$sr = $_POST['sr'];

			if ($_POST['wrkType'] == 'INTERVIEW_REG'){
				$rcpt = '0';
			}else{
				$rcpt = '-1';
			}
		}

		//가족관계
		$family	= Explode(chr(11),$_POST['family']);

		//기존 가족내역 삭제
		$sql = 'DELETE
				FROM	hce_family
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$sr.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'';

		$query[SizeOf($query)] = $sql;
		$seq = 1;

		foreach($family as $tmp){
			if ($tmp){
				Parse_Str($tmp,$col);

				$sql = 'INSERT INTO hce_family (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,seq
						,family_rel
						,family_nm
						,family_addr
						,family_age
						,family_job
						,family_cohabit
						,family_monthly
						,family_remark) VALUES (
						 \''.$orgNo.'\'
						,\''.$sr.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$seq.'\'
						,\''.$col['rel'].'\'
						,\''.$col['name'].'\'
						,\''.AddSlashes($col['addr']).'\'
						,\''.$col['age'].'\'
						,\''.$col['job'].'\'
						,\''.$col['cohabit'].'\'
						,\''.AddSlashes($col['monthly']).'\'
						,\''.AddSlashes($col['remark']).'\'
						)';
				$seq ++;

				$query[SizeOf($query)] = $sql;
			}
		}

		//데이타 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	hce_interview
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$sr.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'';

		$ivCnt = $conn->get_data($sql);

		if ($ivCnt == 0){
			$new = true;
			$sql = 'INSERT INTO hce_interview(
					 org_no
					,org_type
					,IPIN
					,rcpt_seq
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$sr.'\'
					,\''.$IPIN.'\'
					,\''.$rcpt.'\'
					,\''.$userCd.'\'
					,NOW()
					)';

			$query[SizeOf($query)] = $sql;
		}else{
			$new = false;
		}

		//만성질환
		$sql = 'SELECT	code,name
				FROM	hce_gbn
				WHERE	type	= \'DT\'
				AND		use_yn	= \'Y\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($disease) $disease .= '/';

			$disease .= $row['code'].':'.$_POST['chkDisease_'.$row['code']];
		}

		$conn->row_free();

		//보장구
		$sql = 'SELECT	code,name
				FROM	hce_gbn
				WHERE	type= \'DV\'
				AND	use_yn	= \'Y\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($device) $device .= '/';

			$device .= $row['code'].':'.$_POST['chkDevice_'.$row['code']];
		}

		$conn->row_free();

		//신청서비스
		$sql = 'SELECT	DISTINCT suga_cd AS cd
				FROM	care_suga
				WHERE	org_no	= \''.$orgNo.'\'
				AND		suga_sr	= \''.$sr.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$idx = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($svcReq) $svcReq .= '/';
			if ($svcOff) $svcOff .= '/';

			$svcReq .= $row['cd'].':'.$_POST['chkSvcReq_'.$row['cd']];
			$svcOff .= $row['cd'].':'.$_POST['chkSvcOff_'.$row['cd']];
		}

		$conn->row_free();

		$iverDt		= Str_Replace('-','',$_POST['txtIVDt']);		//면접일자
		$iverJumin	= $ed->de($_POST['txtIVerJumin']);				//담당자
		$monthly	= Str_Replace(',','',$_POST['txtMonthly']);		//월소득
		$depositAmt	= Str_Replace(',','',$_POST['txtDepositAmt']);	//보증금
		$rentalAmt	= Str_Replace(',','',$_POST['txtRentalAmt']);	//월세
		$reqTel		= Str_Replace('-','',$_POST['txtReqTel']);		//의뢰인 연락처
		$noOfferRsn	= AddSlashes($_POST['txtNoOfferRsn']);			//부적격사유
		$remark		= AddSlashes($_POST['txtRemark']);				//비고


		$sql = 'UPDATE	hce_interview
				SET		iver_dt			= \''.$iverDt.'\'
				,		iver_nm			= \''.$_POST['txtIVer'].'\'
				,		iver_jumin		= \''.$iverJumin.'\'
				,		income_gbn		= \''.$_POST['optIncomeGbn'].'\'
				,		income_other	= \''.$_POST['txtIncomeOther'].'\'
				,		income_monthly	= \''.$monthly.'\'
				,		income_main		= \''.$_POST['txtIncomeMain'].'\'
				,		generation_gbn	= \''.$_POST['optGenGbn'].'\'
				,		generation_other= \''.$_POST['txtGenOther'].'\'
				,		dwelling_gbn	= \''.$_POST['optDwellingGbn'].'\'
				,		dwelling_other	= \''.$_POST['txtDwellingOther'].'\'
				,		house_gbn		= \''.$_POST['optHouseGbn'].'\'
				,		house_other		= \''.$_POST['txtHouseOther'].'\'
				,		deposit_amt		= \''.$depositAmt.'\'
				,		rental_amt		= \''.$rentalAmt.'\'
				,		health_gbn		= \''.$_POST['optHealthGbn'].'\'
				,		health_other	= \''.$_POST['txtHealthOther'].'\'
				,		disease_gbn		= \''.$disease.'\'
				,		handicap_gbn	= \''.$_POST['optHandicap'].'\'
				,		handicap_other	= \''.$_POST['txtHandicap'].'\'
				,		device_gbn		= \''.$device.'\'
				,		device_other	= \''.$_POST['txtDeviceOther'].'\'
				,		longlvl_gbn		= \''.$_POST['optLongLvlGbn'].'\'
				,		longlvl_other	= \''.$_POST['txtLongLvlOther'].'\'
				,		other_svc_nm	= \''.$_POST['txtOtherSvcNm'].'\'
				,		other_org_nm	= \''.$_POST['txtOtherOrgNm'].'\'
				,		req_svc_gbn		= \''.$svcReq.'\'
				,		offer_gbn		= \''.$_POST['optSvcOffer'].'\'
				,		nooffer_rsn		= \''.$noOfferRsn.'\'
				,		svc_rsn_gbn		= \''.$_POST['optSvcRsnGbn'].'\'
				,		svc_rsn_other	= \''.$_POST['txtSvcRsnOther'].'\'
				,		offer_svc_gbn	= \''.$svcOff.'\'
				,		req_nm			= \''.$_POST['txtReqNm'].'\'
				,		req_rel			= \''.$_POST['cboReqRel'].'\'
				,		req_telno		= \''.$reqTel.'\'
				,		req_route_gbn	= \''.$_POST['cboReqRoute'].'\'
				,		remark			= \''.$remark.'\'';

		if (!$new){
			$sql .= '
				,		update_id	= \''.$userCd.'\'
				,		update_dt	= NOW()';
		}

		$sql .= '
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$sr.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'';

		$query[SizeOf($query)] = $sql;

		if ($_POST['IsHCE'] == 'Y'){
			$sql = 'UPDATE	hce_proc
					SET		itvw_dt = \''.$iverDt.'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$sr.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'';

			$query[SizeOf($query)] = $sql;
		}

		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
		}

		$conn->commit();

		echo 1;


	/*********************************************************
	 *	사정기록지
	 *********************************************************/
	}else if ($type == '31'){
		$IPIN	= $hce->IPIN;	//고유번호
		$rcpt	= $hce->rcpt;	//접수일자
		$ispt	= $_POST['isptSeq'];	//사정순번

		//메뉴 인덱스
		$menuIdx = $_POST['bodyIdx'];

		/*********************************************************
		 *	기본
		 *********************************************************/
		if ($menuIdx == '1'){
			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_inspection
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$ispt= 1;
				$sql = 'INSERT INTO hce_inspection (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			//신체적문제
			$sql = 'SELECT	code,name
					FROM	hce_gbn
					WHERE	type= \'PP\'
					AND	use_yn	= \'Y\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($physical) $physical .= '/';

				$physical .= $row['code'].':'.$_POST['chkPhysical_'.$row['code']];
			}

			$conn->row_free();

			//정신적문제
			$sql = 'SELECT	code,name
					FROM	hce_gbn
					WHERE	type= \'MP\'
					AND	use_yn	= \'Y\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($mental) $mental .= '/';

				$mental .= $row['code'].':'.$_POST['chkMental_'.$row['code']];
			}

			$conn->row_free();

			$sql = 'UPDATE	hce_inspection
					SET		ispt_dt					= \''.Str_Replace('-','',$_POST['txtIsptDt']).'\'
					,		ispt_from				= \''.Str_Replace(':','',$_POST['txtIsptFrom']).'\'
					,		ispt_to					= \''.Str_Replace(':','',$_POST['txtIsptTo']).'\'
					,		counsel_type			= \''.$_POST['optCounselType'].'\'
					,		iver_nm					= \''.$_POST['txtIVer'].'\'
					,		iver_jumin				= \''.$ed->de($_POST['txtIVerJumin']).'\'
					,		work_amt				= \''.Str_Replace(',','',$_POST['txtIcWorkAmt']).'\'
					,		live_aid_amt			= \''.Str_Replace(',','',$_POST['txtIcAidAmt']).'\'
					,		basic_old_amt			= \''.Str_Replace(',','',$_POST['txtIcOldAmt']).'\'
					,		ext_aid_amt				= \''.Str_Replace(',','',$_POST['txtIcExtAmt']).'\'
					,		support_amt				= \''.Str_Replace(',','',$_POST['txtIcSupportAmt']).'\'
					,		support_aid_amt			= \''.Str_Replace(',','',$_POST['txtIcSpAidAmt']).'\'
					,		dwelling_env			= \''.$_POST['optDwellingEnv'].'\'
					,		dwelling_env_other		= \''.AddSlashes($_POST['txtDwellingEnvOther']).'\'
					,		elv_yn					= \''.$_POST['optElv'].'\'
					,		house_stat				= \''.$_POST['optHouseStat'].'\'
					,		house_stat_fault		= \''.AddSlashes($_POST['txtHouseStatOther']).'\'
					,		clean_stat				= \''.$_POST['optCleanStat'].'\'
					,		clean_stat_fault		= \''.AddSlashes($_POST['txtCleanStatOther']).'\'
					,		heat_gbn				= \''.$_POST['optHeat'].'\'
					,		heat_material			= \''.$_POST['optHeatMaterial'].'\'
					,		heat_other				= \''.AddSlashes($_POST['txtHeatOther']).'\'
					,		toilet_gbn				= \''.$_POST['optToilet'].'\'
					,		toilet_type				= \''.$_POST['optToiletType'].'\'
					,		moving_stat				= \''.$_POST['optMovingStat'].'\'
					,		physical_problem_gbn	= \''.$physical.'\'
					,		physical_problem_other	= \''.AddSlashes($_POST['txtPhysicalOther']).'\'
					,		mental_problem_gbn		= \''.$mental.'\'
					,		mental_problem_other	= \''.AddSlashes($_POST['txtMentalOther']).'\'
					,		past_medi_his			= \''.AddSlashes($_POST['txtPastMediHis']).'\'
					,		curr_medi_his			= \''.AddSlashes($_POST['txCurrMediHis']).'\'
					,		per_family_cnt			= \''.$_POST['txtPerCnt'].'\'
					,		per_cost_gbn			= \''.$_POST['optCostOfLiving'].'\'
					,		per_medical_gbn			= \''.$_POST['optMedical'].'\'
					,		remark					= \''.AddSlashes($_POST['txtRemark']).'\'
					,		hsp_nm					= \''.AddSlashes($_POST['txtHspNm']).'\'
					,		dis_nm					= \''.AddSlashes($_POST['txtDisNm']).'\'
					,		hsp_go					= \''.AddSlashes($_POST['txtHspGo']).'\'
					,		hsp_fre					= \''.AddSlashes($_POST['txtHspFre']).'\'
					,		hsp_med					= \''.AddSlashes($_POST['txtHspMed']).'\'
					,		hsp_tel					= \''.AddSlashes($_POST['txtHspTel']).'\'
					,		hsp_nm_2				= \''.AddSlashes($_POST['txtHspNm_2']).'\'
					,		dis_nm_2				= \''.AddSlashes($_POST['txtDisNm_2']).'\'
					,		hsp_go_2				= \''.AddSlashes($_POST['txtHspGo_2']).'\'
					,		hsp_fre_2				= \''.AddSlashes($_POST['txtHspFre_2']).'\'
					,		hsp_med_2				= \''.AddSlashes($_POST['txtHspMed_2']).'\'
					,		hsp_tel_2				= \''.AddSlashes($_POST['txtHspTel_2']).'\'
					,		hsp_nm_3				= \''.AddSlashes($_POST['txtHspNm_3']).'\'
					,		dis_nm_3				= \''.AddSlashes($_POST['txtDisNm_3']).'\'
					,		hsp_go_3				= \''.AddSlashes($_POST['txtHspGo_3']).'\'
					,		hsp_fre_3				= \''.AddSlashes($_POST['txtHspFre_3']).'\'
					,		hsp_med_3				= \''.AddSlashes($_POST['txtHspMed_3']).'\'
					,		hsp_tel_3				= \''.AddSlashes($_POST['txtHspTel_3']).'\'
					,		hsp_nm_4				= \''.AddSlashes($_POST['txtHspNm_4']).'\'
					,		dis_nm_4				= \''.AddSlashes($_POST['txtDisNm_4']).'\'
					,		hsp_go_4				= \''.AddSlashes($_POST['txtHspGo_4']).'\'
					,		hsp_fre_4				= \''.AddSlashes($_POST['txtHspFre_4']).'\'
					,		hsp_med_4				= \''.AddSlashes($_POST['txtHspMed_4']).'\'
					,		hsp_tel_4				= \''.AddSlashes($_POST['txtHspTel_4']).'\'
					';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		/*********************************************************
		 *	가계도 및 생태도
		 *********************************************************/
		}else if ($menuIdx == '2'){
			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_map
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				$sql = 'INSERT INTO hce_map (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$hce->IPIN.'\'
						,\''.$hce->rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}

			$sql = 'UPDATE	hce_map
					SET		family_remark	= \''.AddSlashes($_POST['txtFamiyRemark']).'\'
					,		ecomap_remark	= \''.AddSlashes($_POST['txtEcoRemark']).'\'
					,		remark			= \''.AddSlashes($_POST['txtMapText']).'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		/*********************************************************
		 *	일상생활 동작정도(ADL)
		 *********************************************************/
		}else if ($menuIdx == '3'){
			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_inspection_adl
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$ispt= 1;
				$sql = 'INSERT INTO hce_inspection_adl (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			$sql = 'UPDATE	hce_inspection_adl
					SET		base_door		= \''.$_POST['1'].'\'
					,		base_shoes		= \''.$_POST['2'].'\'
					,		base_shoes_put	= \''.$_POST['3'].'\'
					,		base_chair		= \''.$_POST['4'].'\'
					,		per_bath		= \''.$_POST['5'].'\'
					,		per_wash		= \''.$_POST['6'].'\'
					,		per_groom		= \''.$_POST['7'].'\'
					,		per_in_dress	= \''.$_POST['8'].'\'
					,		per_out_dress	= \''.$_POST['9'].'\'
					,		wc_bedpan		= \''.$_POST['10'].'\'
					,		wc_after		= \''.$_POST['11'].'\'
					,		wc_feces		= \''.$_POST['12'].'\'
					,		wc_urine		= \''.$_POST['13'].'\'
					,		eat_spoon		= \''.$_POST['14'].'\'
					,		eat_stick		= \''.$_POST['15'].'\'
					,		eat_poke		= \''.$_POST['16'].'\'
					,		eat_cup			= \''.$_POST['17'].'\'
					,		eat_grip_cup	= \''.$_POST['18'].'\'
					,		walk_100m		= \''.$_POST['19'].'\'
					,		walk_hand		= \''.$_POST['20'].'\'
					,		walk_stair		= \''.$_POST['21'].'\'
					,		bed_sitdown		= \''.$_POST['22'].'\'
					,		bed_standup		= \''.$_POST['23'].'\'
					,		bed_lie			= \''.$_POST['24'].'\'
					,		bed_turn		= \''.$_POST['25'].'\'
					,		bed_tidy		= \''.$_POST['26'].'\'';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		/*********************************************************
		 *	도구적 일상생활 동작정도(IADL)
		 *********************************************************/
		}else if ($menuIdx == '4'){
			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_inspection_iadl
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$ispt= 1;
				$sql = 'INSERT INTO hce_inspection_iadl (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			$sql = 'UPDATE	hce_inspection_iadl
					SET		phone		= \''.$_POST['1'].'\'
					,		outdoor		= \''.$_POST['2'].'\'
					,		buying		= \''.$_POST['3'].'\'
					,		eating		= \''.$_POST['4'].'\'
					,		homework	= \''.$_POST['5'].'\'
					,		cleaning	= \''.$_POST['6'].'\'
					,		medicine	= \''.$_POST['7'].'\'
					,		money		= \''.$_POST['8'].'\'
					,		repair		= \''.$_POST['9'].'\'';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		/*********************************************************
		 *	정서적측면
		 *********************************************************/
		}else if ($menuIdx == '5'){
			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_inspection_feel
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$ispt= 1;
				$sql = 'INSERT INTO hce_inspection_feel (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			$sql = 'UPDATE	hce_inspection_feel
					SET		feel1_yn	= \''.$_POST['opt1'].'\'
					,		feel2_yn	= \''.$_POST['opt2'].'\'
					,		feel2_rsn	= \''.($_POST['opt2'] == 'Y' ? AddSlashes($_POST['txt2Rsn']) : '').'\'
					,		feel3_yn	= \''.$_POST['opt3'].'\'
					,		feel4_yn	= \''.$_POST['opt4'].'\'
					,		feel4_rsn	= \''.($_POST['opt4'] == 'Y' ? AddSlashes($_POST['txt4Rsn']) : '').'\'
					,		feel5_yn	= \''.$_POST['opt5'].'\'
					,		feel6_yn	= \''.$_POST['opt6'].'\'
					,		feel6_eft	= \''.($_POST['opt6'] == 'Y' ? AddSlashes($_POST['txt6Eft']) : '').'\'
					,		feel7_yn	= \''.$_POST['opt7'].'\'
					,		feel7_cnt	= \''.($_POST['opt7'] == 'Y' ? AddSlashes($_POST['txt7Cnt']) : '').'\'
					,		feel7_whn	= \''.($_POST['opt7'] == 'Y' ? AddSlashes($_POST['txt7Whn']) : '').'\'
					,		feel7_rsn	= \''.($_POST['opt7'] == 'Y' ? AddSlashes($_POST['txt7Rsn']) : '').'\'';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		/*********************************************************
		 *	사회적측면
		 *********************************************************/
		}else if ($menuIdx == '6'){
			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_inspection_social
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$ispt= 1;
				$sql = 'INSERT INTO hce_inspection_social (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			$sql = 'UPDATE	hce_inspection_social
					SET		social1			= \''.$_POST['opt1'].'\'
					,		social2			= \''.$_POST['opt2'].'\'
					,		social2_rsn		= \''.AddSlashes($_POST['txt2Rsn']).'\'
					,		social3			= \''.$_POST['opt3'].'\'
					,		social4			= \''.$_POST['opt4'].'\'
					,		social4_rsn		= \''.AddSlashes($_POST['txt4Rsn']).'\'
					,		social5			= \''.$_POST['opt5'].'\'
					,		social6			= \''.$_POST['opt6'].'\'
					,		social6_rsn		= \''.AddSlashes($_POST['txt6Rsn']).'\'
					,		social7			= \''.$_POST['opt7'].'\'
					,		social7_nm		= \''.AddSlashes($_POST['txt7Nm']).'\'
					,		social7_tel		= \''.Str_Replace('-','',$_POST['txt7Tel']).'\'
					,		social8			= \''.$_POST['opt8'].'\'
					,		social8_other	= \''.AddSlashes($_POST['txt8Other']).'\'
					,		social9			= \''.$_POST['opt9'].'\'
					,		social9_other	= \''.AddSlashes($_POST['txt9Other']).'\'';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		/*********************************************************
		 *	욕구
		 *********************************************************/
		}else if ($menuIdx == '7'){
			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_inspection_needs
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$ispt= 1;
				$sql = 'INSERT INTO hce_inspection_needs (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			$sql = 'UPDATE	hce_inspection_needs
					SET		lifedays		= \''.AddSlashes($_POST['txtLifedays']).'\'
					,		faircopy		= \''.AddSlashes($_POST['txtFaircopy']).'\'
					,		dwelling		= \''.AddSlashes($_POST['txtDwelling']).'\'
					,		leisure			= \''.AddSlashes($_POST['txtLeisure']).'\'
					,		interview		= \''.AddSlashes($_POST['txtInterview']).'\'
					,		local			= \''.AddSlashes($_POST['txtLocal']).'\'
					,		link			= \''.AddSlashes($_POST['txtLink']).'\'
					,		educ			= \''.AddSlashes($_POST['txtEduc']).'\'
					,		emergency		= \''.AddSlashes($_POST['txtEmergency']).'\'
					,		ext				= \''.AddSlashes($_POST['txtExt']).'\'
					,		social_opinion	= \''.AddSlashes($_POST['txtSocial']).'\'
					/*,		rough_text		= \''.AddSlashes($_POST['txtRough']).'\'*/
					,		rough_file		= \'\'';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		/*********************************************************
		 *	노인인지능력평가
		 *********************************************************/
		}else if ($menuIdx == '8'){
			$ispt= 1;

			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_inspection_mmseds
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$sql = 'INSERT INTO hce_inspection_mmseds (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			$sql = 'UPDATE	hce_inspection_mmseds
					SET		edu_training= \''.AddSlashes($_POST['txtEduTraining']).'\'
					,		kor_decode	= \''.AddSlashes($_POST['txtKorDecode']).'\'
					,		life_job	= \''.AddSlashes($_POST['txtLifeJob']).'\'
					,		check_dt	= \''.str_replace('-','',$_POST['txtCheckDt']).'\'
					,		evl_place	= \''.AddSlashes($_POST['txtEvlPlace']).'\'
					,		Q1	= \''.$_POST['Q1'].'\'
					,		Q2	= \''.$_POST['Q2'].'\'
					,		Q3	= \''.$_POST['Q3'].'\'
					,		Q4	= \''.$_POST['Q4'].'\'
					,		Q5	= \''.$_POST['Q5'].'\'
					,		Q6	= \''.$_POST['Q6'].'\'
					,		Q7	= \''.$_POST['Q7'].'\'
					,		Q8	= \''.$_POST['Q8'].'\'
					,		Q9	= \''.$_POST['Q9'].'\'
					,		Q10	= \''.$_POST['Q10'].'\'
					,		Q11	= \''.$_POST['Q11'].'\'
					,		Q12	= \''.$_POST['Q12'].'\'
					,		Q13	= \''.$_POST['Q13'].'\'
					,		Q14	= \''.$_POST['Q14'].'\'
					,		Q15	= \''.$_POST['Q15'].'\'
					,		Q16	= \''.$_POST['Q16'].'\'
					,		Q17	= \''.$_POST['Q17'].'\'
					,		Q18	= \''.$_POST['Q18'].'\'
					,		Q19	= \''.$_POST['Q19'].'\'
					,		Q20	= \''.$_POST['Q20'].'\'
					,		Q21	= \''.$_POST['Q21'].'\'
					,		Q22	= \''.$_POST['Q22'].'\'
					,		Q23	= \''.$_POST['Q23'].'\'
					,		Q24	= \''.$_POST['Q24'].'\'
					,		Q25	= \''.$_POST['Q25'].'\'
					,		Q26	= \''.$_POST['Q26'].'\'
					,		Q27	= \''.$_POST['Q27'].'\'
					,		Q28	= \''.$_POST['Q28'].'\'
					,		Q29	= \''.$_POST['Q29'].'\'
					,		Q30	= \''.$_POST['Q30'].'\'
					';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		/*********************************************************
		 *	노인우울척도
		 *********************************************************/
		}else if ($menuIdx == '9'){
			$ispt= 1;

			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_inspection_sgds
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$sql = 'INSERT INTO hce_inspection_sgds (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,ispt_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$ispt.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			$sql = 'UPDATE	hce_inspection_sgds
					SET		Q1	= \''.$_POST['Q1'].'\'
					,		Q2	= \''.$_POST['Q2'].'\'
					,		Q3	= \''.$_POST['Q3'].'\'
					,		Q4	= \''.$_POST['Q4'].'\'
					,		Q5	= \''.$_POST['Q5'].'\'
					,		Q6	= \''.$_POST['Q6'].'\'
					,		Q7	= \''.$_POST['Q7'].'\'
					,		Q8	= \''.$_POST['Q8'].'\'
					,		Q9	= \''.$_POST['Q9'].'\'
					,		Q10	= \''.$_POST['Q10'].'\'
					,		Q11	= \''.$_POST['Q11'].'\'
					,		Q12	= \''.$_POST['Q12'].'\'
					,		Q13	= \''.$_POST['Q13'].'\'
					,		Q14	= \''.$_POST['Q14'].'\'
					,		Q15	= \''.$_POST['Q15'].'\'
					';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		ispt_seq= \''.$ispt.'\'';

			$query[SizeOf($query)] = $sql;


		}else{
			$conn->close();
			echo 9;
			exit;
		}

		if ($menuIdx == '1'){
			$sql = 'UPDATE	hce_proc
					SET		ispt_dt = \''.Str_Replace('-','',$_POST['txtIsptDt']).'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'';

			$query[SizeOf($query)] = $sql;
		}

		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
		}

		$conn->commit();

		echo 1;


	/*********************************************************
	 *	선정기준표
	 *********************************************************/
	}else if ($type == '41'){
		$IPIN	= $hce->IPIN;		//고유번호
		$rcpt	= $hce->rcpt;		//접수일자
		$chic	= $_POST['chicSeq'];//선정순번
		
		//2018년부터 충재협 사정기준표 변경
		if($userArea == '05' && Str_Replace('-','',$_POST['choiceDt']) >= '20180101'){
			
			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_choice_cn
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		chic_seq= \''.$chic.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$chic= 1;
				$sql = 'INSERT INTO hce_choice_cn (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,chic_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$chic.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			if ($_POST['total'] >= 30){
				$rst = '1';
			}else if ($_POST['total'] >= 25){
				$rst = '2';
			}else{
				$rst = '3';
			}

			$sql = 'UPDATE	hce_choice_cn
					SET		chic_dt				= \''.Str_Replace('-','',$_POST['choiceDt']).'\'
					,		income_gbn			= \''.$_POST['lblK1'].'\'
					,		income_point		= \''.$_POST['lblK1Val'].'\'
					,		dwelling_gbn		= \''.$_POST['lblL1'].'\'
					,		dwelling_point		= \''.$_POST['lblL1Val'].'\'
					,		gross_gbn			= \''.$_POST['lblM1'].'\'
					,		gross_point			= \''.$_POST['lblM1Val'].'\'
					,		disease_gbn			= \''.$_POST['lblN1'].'\'
					,		disease_point		= \''.$_POST['lblN1Val'].'\'
					,		handicap_gbn		= \''.$_POST['lblO1'].'\'
					,		handicap_point		= \''.$_POST['lblO1Val'].'\'
					,		adl_gbn				= \''.$_POST['lblP1'].'\'
					,		adl_point			= \''.$_POST['lblP1Val'].'\'
					,		care_gbn			= \''.$_POST['lblQ1'].'\'
					,		care_point			= \''.$_POST['lblQ1Val'].'\'
					,		life_gbn			= \''.$_POST['lblR1'].'\'
					,		life_point			= \''.$_POST['lblR1Val'].'\'
					,		social_rel_gbn		= \''.$_POST['lblS1'].'\'
					,		social_rel_point	= \''.$_POST['lblS1Val'].'\'
					,		feel_gbn			= \''.$_POST['lblT1'].'\'
					,		feel_point			= \''.$_POST['lblT1Val'].'\'
					,		free_gbn			= \''.$_POST['lblU1'].'\'
					,		free_point			= \''.$_POST['lblU1Val'].'\'
					,		total_point			= \''.$_POST['total'].'\'
					,		choice_rst			= \''.$rst.'\'
					,		comment				= \''.AddSlashes($_POST['comment']).'\'';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		chic_seq= \''.$chic.'\'';

			$query[SizeOf($query)] = $sql;

		}else {


			//데이타 존재여부
			$sql = 'SELECT	COUNT(*)
					FROM	hce_choice
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		chic_seq= \''.$chic.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt == 0){
				//신규
				$new = true;
				$chic= 1;
				$sql = 'INSERT INTO hce_choice (
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,chic_seq) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$IPIN.'\'
						,\''.$rcpt.'\'
						,\''.$chic.'\'
						)';

				$query[SizeOf($query)] = $sql;
			}else{
				//수정
				$new = false;
			}

			if ($_POST['total'] >= 25){
				$rst = '1';
			}else if ($_POST['total'] >= 20){
				$rst = '2';
			}else{
				$rst = '';
			}

			$sql = 'UPDATE	hce_choice
					SET		chic_dt				= \''.Str_Replace('-','',$_POST['choiceDt']).'\'
					,		income_gbn			= \''.$_POST['lblA1'].'\'
					,		income_point		= \''.$_POST['lblA1Val'].'\'
					,		nonfamily_gbn		= \''.$_POST['lblA2'].'\'
					,		nonfamily_point		= \''.$_POST['lblA2Val'].'\'
					,		dwelling_gbn		= \''.$_POST['lblB1'].'\'
					,		dwelling_point		= \''.$_POST['lblB1Val'].'\'
					,		rental_gbn			= \''.$_POST['lblB2'].'\'
					,		rental_point		= \''.$_POST['lblB2Val'].'\'
					,		gross_gbn			= \''.$_POST['lblC1'].'\'
					,		gross_point			= \''.$_POST['lblC1Val'].'\'
					,		public_gbn			= \''.$_POST['lblC2'].'\'
					,		public_point		= \''.$_POST['lblC2Val'].'\'
					,		help_gbn			= \''.$_POST['lblD2'].'\'
					,		help_poing			= \''.$_POST['lblD2Val'].'\'
					,		body_gbn			= \''.$_POST['lblE1'].'\'
					,		body_point			= \''.$_POST['lblE1Val'].'\'
					,		body_patient_gbn	= \''.$_POST['lblE2'].'\'
					,		body_patient_point	= \''.$_POST['lblE2Val'].'\'
					,		feel_gbn			= \''.$_POST['lblF1'].'\'
					,		feel_point			= \''.$_POST['lblF1Val'].'\'
					,		feel_patient_gbn	= \''.$_POST['lblF2'].'\'
					,		feel_patient_point	= \''.$_POST['lblF2Val'].'\'
					,		handicap_gbn		= \''.$_POST['lblG1'].'\'
					,		handicap_point		= \''.$_POST['lblG1Val'].'\'
					,		handi_dup_gbn		= \''.$_POST['lblG2'].'\'
					,		handi_dup_point		= \''.$_POST['lblG2Val'].'\'
					,		handi_2per_gbn		= \''.$_POST['lblG3'].'\'
					,		handi_2per_point	= \''.$_POST['lblG3Val'].'\'
					,		adl_gbn				= \''.$_POST['lblH1'].'\'
					,		adl_point			= \''.$_POST['lblH1Val'].'\'
					,		care_gbn			= \''.$_POST['lblI1'].'\'
					,		care_point			= \''.$_POST['lblI1Val'].'\'
					,		free_gbn			= \''.$_POST['lblJ1'].'\'
					,		free_point			= \''.$_POST['lblJ1Val'].'\'
					,		total_point			= \''.$_POST['total'].'\'
					,		choice_rst			= \''.$rst.'\'
					,		comment				= \''.AddSlashes($_POST['comment']).'\'';

			if ($new){
				$sql .= '
					,		insert_id	= \''.$userCd.'\'
					,		insert_dt	= NOW()';
			}else{
				$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()';
			}

			$sql .= '
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		chic_seq= \''.$chic.'\'';

			$query[SizeOf($query)] = $sql;

			
		}
		
		$sql = 'UPDATE	hce_proc
				SET		chic_dt = \''.Str_Replace('-','',$_POST['choiceDt']).'\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'';

		$query[SizeOf($query)] = $sql;

		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
		}

		$conn->commit();

		echo 1;


	/*********************************************************
	 *	사례회의록 등록
	 *********************************************************/
	}else if ($type == '52'){
		$IPIN	= $hce->IPIN;		//고유번호
		$rcpt	= $hce->rcpt;		//접수일자
		$meet	= $_POST['meetSeq'];//회의순번

		if ($rcpt < '1'){
			$rcpt = $hce->backRcptNo;

			if ($rcpt < '1'){
				echo 'NOT';
				exit;
			}
		}

		//데이타 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	hce_meeting
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		meet_seq= \''.$meet.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt == 0){
			//신규
			$new = true;

			$sql = 'SELECT	IFNULL(MAX(meet_seq),0)+1
					FROM	hce_meeting
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'';
			$meet= $conn->get_data($sql);

			$sql = 'INSERT INTO hce_meeting (
					 org_no
					,org_type
					,IPIN
					,rcpt_seq
					,meet_seq) VALUES (
					 \''.$orgNo.'\'
					,\''.$hce->SR.'\'
					,\''.$IPIN.'\'
					,\''.$rcpt.'\'
					,\''.$meet.'\'
					)';

			$query[SizeOf($query)] = $sql;
		}else{
			//수정
			$new = false;
		}

		$tmp = Explode('&',$_POST['attendee']);

		Unset($attendee);

		foreach($tmp as $att){
			$str = $ed->de($att);

			if ($str){
				$attendee .= ($attendee ? '&' : '').$str;
			}
		}

		//신청서비스
		$sql = 'SELECT	DISTINCT suga_cd AS cd
				FROM	care_suga
				WHERE	org_no	= \''.$orgNo.'\'
				AND		suga_sr	= \''.$sr.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$idx = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($svcReq) $svcReq .= '/';

			$svcReq .= $row['cd'].':'.$_POST['chkSvcReq_'.$row['cd']];
		}

		$conn->row_free();

		$sql = 'UPDATE	hce_meeting
				SET		meet_gbn		= \''.$_POST['optMeetGbn'].'\'
				,		meet_dt			= \''.Str_Replace('-','',$_POST['txtMeetDt']).'\'
				,		examiner_jumin	= \''.$ed->de($_POST['examinerJumin']).'\'
				,		examiner		= \''.$_POST['txtExaminer'].'\'
				,		attendee		= \''.$attendee.'\'
				,		attendee_other	= \''.AddSlashes($_POST['txtAttOther']).'\'
				,		life_lvl		= \''.AddSlashes($_POST['txtLifeLvl']).'\'
				,		req_rsn			= \''.AddSlashes($_POST['txtReqRsn']).'\'
				,		decision_gbn	= \''.$_POST['optDecision'].'\'
				,		decision_dt		= \''.Str_Replace('-','',$_POST['txtDecisionDt']).'\'
				,		decision_rsn	= \''.AddSlashes($_POST['txtDecisionRsn']).'\'
				,		decision_svc	= \''.$svcReq.'\'';

		if ($new){
			$sql .= '
				,		insert_id	= \''.$userCd.'\'
				,		insert_dt	= NOW()';
		}else{
			$sql .= '
				,		update_id	= \''.$userCd.'\'
				,		update_dt	= NOW()';
		}

		$sql .= '
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		meet_seq= \''.$meet.'\'';

		$query[SizeOf($query)] = $sql;

		$sql = 'SELECT	meet_gbn,meet_dt
				FROM	hce_meeting
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		meet_gbn= \''.$_POST['optMeetGbn'].'\'
				ORDER	BY meet_dt DESC
				LIMIT	1';

		$row = $conn->get_array($sql);

		if ($row){
			$meetDt	= $row['meet_dt'];
			$meetGbn= $row['meet_gbn'];
		}else{
			$meetDt	= Str_Replace('-','',$_POST['txtMeetDt']);
			$meetGbn= $_POST['optMeetGbn'];
		}

		$sql = 'UPDATE	hce_proc
				SET		meet_dt'.$meetGbn.' = \''.$meetDt.'\'
				,		meet_gbn= \''.$meetGbn.'\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'';

		$query[SizeOf($query)] = $sql;

		Unset($row);

		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 'ERROR';
				 exit;
			}
		}

		$conn->commit();

		echo $meet;


	/*********************************************************
	 *	서비스계획서 등록
	 *********************************************************/
	}else if ($type == '62'){
		$IPIN	= $hce->IPIN;		//고유번호
		$rcpt	= $hce->rcpt;		//접수일자
		$plan	= $_POST['planSeq'];//계획순번

		//데이타 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	hce_plan_sheet
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		plan_seq= \''.$plan.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt == 0){
			$sql = 'SELECT	IFNULL(MAX(plan_seq),0)+1
					FROM	hce_plan_sheet
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'';

			$plan = $conn->get_data($sql);

			//신규
			$new = true;
			$sql = 'INSERT INTO hce_plan_sheet (
					 org_no
					,org_type
					,IPIN
					,rcpt_seq
					,plan_seq) VALUES (
					 \''.$orgNo.'\'
					,\''.$hce->SR.'\'
					,\''.$IPIN.'\'
					,\''.$rcpt.'\'
					,\''.$plan.'\'
					)';

			$query[SizeOf($query)] = $sql;
		}else{
			//수정
			$new = false;
		}

		$sql = 'UPDATE	hce_plan_sheet
				SET		meet_seq	= \''.$_POST['meetSeq'].'\'
				,		plan_dt		= \''.Str_Replace('-','',$_POST['txtPlanDt']).'\'
				,		planer_jumin= \''.$ed->de($_POST['planerJumin']).'\'
				,		planer		= \''.$_POST['txtPlaner'].'\'
				,		needs		= \''.AddSlashes($_POST['txtNeeds']).'\'
				,		problem		= \''.AddSlashes($_POST['txtProblem']).'\'
				,		goal		= \''.AddSlashes($_POST['txtGoal']).'\'
				,		svc_period	= \''.AddSlashes($_POST['txtSvcPeriod']).'\'
				,		svc_content	= \''.AddSlashes($_POST['txtSvcContent']).'\'
				,		svc_method	= \''.AddSlashes($_POST['txtSvcMethod']).'\'
				,		remark		= \''.AddSlashes($_POST['txtRemark']).'\'';

		if ($new){
			$sql .= '
				,		insert_id	= \''.$userCd.'\'
				,		insert_dt	= NOW()';
		}else{
			$sql .= '
				,		update_id	= \''.$userCd.'\'
				,		update_dt	= NOW()';
		}

		$sql .= '
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		plan_seq= \''.$plan.'\'';

		$query[SizeOf($query)] = $sql;


		if ($hce->SR == 'S'){
			//현재 항목을 모두 삭제처리한다.
			$sql = 'UPDATE	hce_plan_sheet_item
					SET		del_flag	= \'Y\'
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$rcpt.'\'
					AND		plan_seq= \''.$plan.'\'';
			$query[SizeOf($query)] = $sql;

			$itemList = Explode('?',$_POST['itemList']);

			if (is_array($itemList)){
				//다음 순번
				$sql = 'SELECT	IFNULL(MAX(plan_idx),0)+1
						FROM	hce_plan_sheet_item
						WHERE	org_no	= \''.$orgNo.'\'
						AND		org_type= \''.$hce->SR.'\'
						AND		IPIN	= \''.$IPIN.'\'
						AND		rcpt_seq= \''.$rcpt.'\'
						AND		plan_seq= \''.$plan.'\'';
				$planIdx = $conn->get_data($sql);

				foreach($itemList as $idx => $itemRow){
					parse_str($itemRow, $item);

					$sql = 'SELECT	COUNT(*)
							FROM	hce_plan_sheet_item
							WHERE	org_no	= \''.$orgNo.'\'
							AND		org_type= \''.$hce->SR.'\'
							AND		IPIN	= \''.$IPIN.'\'
							AND		rcpt_seq= \''.$rcpt.'\'
							AND		plan_seq= \''.$plan.'\'
							AND		plan_idx= \''.$item['idx'].'\'';

					$cnt = $conn->get_data($sql);

					if ($cnt > 0){
						//수정
						$sql = 'UPDATE	hce_plan_sheet_item
								SET		contents= \''.AddSlashes($item['contents']).'\'
								,		period	= \''.AddSlashes($item['period']).'\'
								,		times	= \''.AddSlashes($item['times']).'\'
								,		method	= \''.AddSlashes($item['method']).'\'
								,		del_flag= \'N\'
								WHERE	org_no	= \''.$orgNo.'\'
								AND		org_type= \''.$hce->SR.'\'
								AND		IPIN	= \''.$IPIN.'\'
								AND		rcpt_seq= \''.$rcpt.'\'
								AND		plan_seq= \''.$plan.'\'
								AND		plan_idx= \''.$item['idx'].'\'';
					}else{
						//신규
						$sql = 'INSERT INTO hce_plan_sheet_item (
								 org_no
								,org_type
								,IPIN
								,rcpt_seq
								,plan_seq
								,plan_idx
								,contents
								,period
								,times
								,method
								,insert_id
								,insert_dt) VALUES (
								 \''.$orgNo.'\'
								,\''.$hce->SR.'\'
								,\''.$IPIN.'\'
								,\''.$rcpt.'\'
								,\''.$plan.'\'
								,\''.$planIdx.'\'
								,\''.AddSlashes($item['contents']).'\'
								,\''.AddSlashes($item['period']).'\'
								,\''.AddSlashes($item['times']).'\'
								,\''.AddSlashes($item['method']).'\'
								,\''.$userCd.'\'
								,NOW()
								)';

						$planIdx ++;
					}

					$query[SizeOf($query)] = $sql;
				}
			}
		}


		/*
		$sql = 'SELECT	COUNT(*)
				FROM	hce_meeting
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'
				AND		meet_seq= \''.$_POST['meetSeq'].'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$planDt	= Str_Replace('-','',$_POST['txtPlanDt']);
		}else{
			$planDt = '';
		}
		*/
		$planDt	= Str_Replace('-','',$_POST['txtPlanDt']);

		$sql = 'UPDATE	hce_proc
				SET		plan_dt	= \''.$planDt.'\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$rcpt.'\'';

		$query[SizeOf($query)] = $sql;



		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
		}

		$conn->commit();

		echo 1;


	/*********************************************************
	 *	이용 안내 및 동의서
	 *********************************************************/
	}else if ($type == '71'){
		//데이타 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	hce_consent_form
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt == 0){
			//신규
			$new = true;
			$sql = 'INSERT INTO hce_consent_form (
					 org_no
					,org_type
					,IPIN
					,rcpt_seq) VALUES (
					 \''.$orgNo.'\'
					,\''.$hce->SR.'\'
					,\''.$hce->IPIN.'\'
					,\''.$hce->rcpt.'\'
					)';

			$query[SizeOf($query)] = $sql;
		}else{
			//수정
			$new = false;
		}

		$sql = 'UPDATE	hce_consent_form
				SET		cont_dt		= \''.Str_Replace('-','',$_POST['txtConsentDt']).'\'
				,		per_nm		= \''.$_POST['txtPer'].'\'
				,		per_jumin	= \''.$ed->de($_POST['perJumin']).'\'';

		if ($new){
			$sql .= '
				,		insert_id	= \''.$userCd.'\'
				,		insert_dt	= NOW()';
		}else{
			$sql .= '
				,		update_id	= \''.$userCd.'\'
				,		update_dt	= NOW()';
		}

		$sql .= '
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$query[SizeOf($query)] = $sql;

		//상담삭제
		if (!$new){
			$sql = 'DELETE
					FROM	hce_consent_svc
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'';

			$query[SizeOf($query)] = $sql;
		}

		$data = Explode(chr(11),$_POST['data']);

		$seq = 1;

		foreach($data as $row){
			if ($row){
				parse_str($row,$col);

				
				$sql = 'INSERT INTO hce_consent_svc(
						 org_no
						,org_type
						,IPIN
						,rcpt_seq
						,cont_seq
						,svc_nm
						,content
						,remark) VALUES (
						 \''.$orgNo.'\'
						,\''.$hce->SR.'\'
						,\''.$hce->IPIN.'\'
						,\''.$hce->rcpt.'\'
						,\''.$seq.'\'
						,\''.AddSlashes($col['svcNm']).'\'
						,\''.AddSlashes($col['content']).'\'
						,\''.AddSlashes($col['other']).'\'
						)';

				$query[SizeOf($query)] = $sql;
				$seq ++;
			}
		}

		//진행이력
		$sql = 'UPDATE	hce_proc
				SET		cont_dt	= \''.Str_Replace('-','',$_POST['txtConsentDt']).'\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$query[SizeOf($query)] = $sql;

		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
		}

		$conn->commit();

		echo 1;


	/*********************************************************
	 *	과정사담 등록
	 *********************************************************/
	}else if ($type == '82'){


	/*********************************************************
	 *	사진삭제
	 *********************************************************/
	}else if ($type == 'PICTURE_DEL'){
		$file = $_SERVER['DOCUMENT_ROOT'].'/sugupja/picture/'.$orgNo.'_'.$_POST['cd'].'.jpg';

		if (is_file($file)){
			if (unlink($file)){
				echo 1;
			}else{
				echo 9;
			}
		}else{
			echo 7;
		}


	}else{
		echo $type;
	}

	include_once('../inc/_db_close.php');
?>