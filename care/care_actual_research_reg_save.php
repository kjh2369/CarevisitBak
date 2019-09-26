<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 *	재가지원 대상 실태조사표
	 */

	$orgNo			= $_SESSION['userCenterCode'];
	$SR				= $_POST['sr'];
	$IPIN			= $_POST['key'];//대상자
	$iverDt			= $_POST['txtIVDt'];//면접일
	$iverCd			= $ed->de($_POST['iverCd']);//담당자
	$iverNm			= $_POST['txtIVer'];//담당자명
	$bizTg			= $_POST['optBizTarget'];//사업대상
	$cohabit		= $_POST['optCohabit'];//동거실태
	$dwelling		= $_POST['optDwelling'];//주거형태
	$IADL1			= $_POST['chkIADL1'];//IADL 일부제한
	$IADL2			= $_POST['chkIADL2'];//IADL 모두제한
	$ADL1			= $_POST['chkADL1'];//ADL 일부제한
	$ADL2			= $_POST['chkADL2'];//ADL 모두제한
	$handicap		= AddSlashes($_POST['txtHandicap']);//장애상태
	$blind			= AddSlashes($_POST['txtBlind']);//시각장애
	$hypacusis		= AddSlashes($_POST['txtHypacusis']);//청각장애
	$lalopathy		= AddSlashes($_POST['txtLalopathy']);//언어장애
	$maimedness		= AddSlashes($_POST['txtMaimedness']);//신체장애
	$handicapOther	= AddSlashes($_POST['txtHandicapOther']);//장애기타
	$longtermLvl	= $_POST['optLongtermLvl'];//장기요양 등급판정
	$svcOffer		= $_POST['optSvcOffer'];//적격여부
	$noOfferRsn		= $_POST['optNoOfferRsn'];//부적격사유
	$noOfferRsnOther= AddSlashes($_POST['txtNoOfferRsn']);//부적격사유 기타
	$svcRsnGbn		= $_POST['optSvcRsnGbn'];//서비스사유
	$svcRsnOther	= AddSlashes($_POST['txtSvcRsnOther']);//서비스사유 기타
	$reqName		= AddSlashes($_POST['txtReqName']);//의뢰인명
	$reqRel			= AddSlashes($_POST['txtReqRel']);//관계
	$reqTelno		= str_replace('-','',$_POST['txtReqTelno']);//연락처
	$other			= AddSlashes($_POST['txtOther']);//비고

	if ($IADL1 == 'Y'){
		$IADL = '1';
	}else if ($IADL2 == 'Y'){
		$IADL = '2';
	}else{
		$IADL = '';
	}

	if ($ADL1 == 'Y'){
		$ADL = '1';
	}else if ($ADL2 == 'Y'){
		$ADL = '2';
	}else{
		$ADL = '';
	}

	//질환명
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

	//신청서비스
	$sql = 'SELECT	DISTINCT suga_cd AS cd
			FROM	care_suga
			WHERE	org_no	= \''.$orgNo.'\'
			AND		suga_sr	= \''.$SR.'\'';

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

	$sql = 'SELECT	COUNT(*)
			FROM	care_actual_research
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		IPIN	= \''.$IPIN.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	care_actual_research
				SET		iver_dt				= \''.$iverDt.'\'
				,		iver_cd				= \''.$iverCd.'\'
				,		iver_nm				= \''.$iverNm.'\'
				,		biz_target			= \''.$bizTg.'\'
				,		cohabit				= \''.$cohabit.'\'
				,		dwelling			= \''.$dwelling.'\'
				,		disease_gbn			= \''.$disease.'\'
				,		IADL				= \''.$IADL.'\'
				,		ADL					= \''.$ADL.'\'
				,		handicap			= \''.$handicap.'\'
				,		blind				= \''.$blind.'\'
				,		hypacusis			= \''.$hypacusis.'\'
				,		lalopathy			= \''.$lalopathy.'\'
				,		maimedness			= \''.$maimedness.'\'
				,		handicap_other		= \''.$handicapOther.'\'
				,		longterm_lvl		= \''.$longtermLvl.'\'
				,		svc_req				= \''.$svcReq.'\'
				,		svc_offer			= \''.$svcOffer.'\'
				,		nooffer_rsn			= \''.$noOfferRsn.'\'
				,		nooffer_rsn_other	= \''.$noOfferRsnOther.'\'
				,		svc_rsn_gbn			= \''.$svcRsnGbn.'\'
				,		svc_rsn_other		= \''.$svcRsnOther.'\'
				,		svc_off				= \''.$svcOff.'\'
				,		req_name			= \''.$reqName.'\'
				,		req_rel				= \''.$reqRel.'\'
				,		rel_telno			= \''.$reqTelno.'\'
				,		other				= \''.$other.'\'
				,		update_id			= \''.$_SESSION['userCode'].'\'
				,		update_dt			= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		IPIN	= \''.$IPIN.'\'';
	}else{
		$sql = 'INSERT INTO care_actual_research(
				 org_no
				,org_type
				,IPIN
				,iver_dt
				,iver_cd
				,iver_nm
				,biz_target
				,cohabit
				,dwelling
				,disease_gbn
				,IADL
				,ADL
				,handicap
				,blind
				,hypacusis
				,lalopathy
				,maimedness
				,handicap_other
				,longterm_lvl
				,svc_req
				,svc_offer
				,nooffer_rsn
				,nooffer_rsn_other
				,svc_rsn_gbn
				,svc_rsn_other
				,svc_off
				,req_name
				,req_rel
				,rel_telno
				,other
				,insert_id
				,insert_dt
				) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$IPIN.'\'
				,\''.$iverDt.'\'
				,\''.$iverCd.'\'
				,\''.$iverNm.'\'
				,\''.$bizTg.'\'
				,\''.$cohabit.'\'
				,\''.$dwelling.'\'
				,\''.$disease.'\'
				,\''.$IADL.'\'
				,\''.$ADL.'\'
				,\''.$handicap.'\'
				,\''.$blind.'\'
				,\''.$hypacusis.'\'
				,\''.$lalopathy.'\'
				,\''.$maimedness.'\'
				,\''.$handicapOther.'\'
				,\''.$longtermLvl.'\'
				,\''.$svcReq.'\'
				,\''.$svcOffer.'\'
				,\''.$noOfferRsn.'\'
				,\''.$noOfferRsnOther.'\'
				,\''.$svcRsnGbn.'\'
				,\''.$svcRsnOther.'\'
				,\''.$svcOff.'\'
				,\''.$reqName.'\'
				,\''.$reqRel.'\'
				,\''.$reqTelno.'\'
				,\''.$other.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>