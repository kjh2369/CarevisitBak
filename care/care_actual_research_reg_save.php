<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 *	�簡���� ��� ��������ǥ
	 */

	$orgNo			= $_SESSION['userCenterCode'];
	$SR				= $_POST['sr'];
	$IPIN			= $_POST['key'];//�����
	$iverDt			= $_POST['txtIVDt'];//������
	$iverCd			= $ed->de($_POST['iverCd']);//�����
	$iverNm			= $_POST['txtIVer'];//����ڸ�
	$bizTg			= $_POST['optBizTarget'];//������
	$cohabit		= $_POST['optCohabit'];//���Ž���
	$dwelling		= $_POST['optDwelling'];//�ְ�����
	$IADL1			= $_POST['chkIADL1'];//IADL �Ϻ�����
	$IADL2			= $_POST['chkIADL2'];//IADL �������
	$ADL1			= $_POST['chkADL1'];//ADL �Ϻ�����
	$ADL2			= $_POST['chkADL2'];//ADL �������
	$handicap		= AddSlashes($_POST['txtHandicap']);//��ֻ���
	$blind			= AddSlashes($_POST['txtBlind']);//�ð����
	$hypacusis		= AddSlashes($_POST['txtHypacusis']);//û�����
	$lalopathy		= AddSlashes($_POST['txtLalopathy']);//������
	$maimedness		= AddSlashes($_POST['txtMaimedness']);//��ü���
	$handicapOther	= AddSlashes($_POST['txtHandicapOther']);//��ֱ�Ÿ
	$longtermLvl	= $_POST['optLongtermLvl'];//����� �������
	$svcOffer		= $_POST['optSvcOffer'];//���ݿ���
	$noOfferRsn		= $_POST['optNoOfferRsn'];//�����ݻ���
	$noOfferRsnOther= AddSlashes($_POST['txtNoOfferRsn']);//�����ݻ��� ��Ÿ
	$svcRsnGbn		= $_POST['optSvcRsnGbn'];//���񽺻���
	$svcRsnOther	= AddSlashes($_POST['txtSvcRsnOther']);//���񽺻��� ��Ÿ
	$reqName		= AddSlashes($_POST['txtReqName']);//�Ƿ��θ�
	$reqRel			= AddSlashes($_POST['txtReqRel']);//����
	$reqTelno		= str_replace('-','',$_POST['txtReqTelno']);//����ó
	$other			= AddSlashes($_POST['txtOther']);//���

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

	//��ȯ��
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

	//��û����
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