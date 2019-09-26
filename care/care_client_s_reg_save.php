<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['sr'];

	//개인정보
	$jumin	= $_POST['jumin'];
	$name	= $_POST['txtName'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	//일반접수 번호
	$normalSeq = $_POST['normalSeq'];


	//등록구분
	#1:대상자
	# 주민번호 7자리까지 필수
	#2:일반접수
	# 주민번호 없이 이름만으로도 저장가능
	$regGbn	= $_POST['regGbn'];


	//데이타
	$from	= $_POST['txtFrom'];	//적용일
	$to		= $_POST['txtTo'];		//종료일
	$stat	= $_POST['txtStat'];	//이용상태
	$seq	= $_POST['txtSeq'];	//계약순번
	$cost	= Str_Replace(',','',$_POST['txtSvcCost']);	//단가

	$phone	= Str_Replace('-','',$_POST['txtPhone']);	//연락처
	$mobile	= Str_Replace('-','',$_POST['txtMobile']);	//모바일

	$postno		= $_POST['txtPostno'];	//우편번호
	$addr		= $_POST['txtAddr'];	//주소
	$addrDtl	= $_POST['txtAddrDtl'];	//상세주소
	$addrMent	= $_POST['txtAddrMent']; //관리주소

	$marry	= ($_POST['cboMarry'] ? $_POST['cboMarry'] : '-');		//결혼구분
	$cohabit= ($_POST['cboCohabit'] ? $_POST['cboCohabit'] : '-');	//동거구분
	$edu	= ($_POST['cboEdu'] ? $_POST['cboEdu'] : '--');			//학력
	$rel	= ($_POST['cboRel'] ? $_POST['cboRel'] : '-');			//종교

	$grdNm	= $_POST['txtGuardNm'];		//보호자명
	$grdAddr= $_POST['txtGuardAddr'];	//보호자 주소
	$grdTel	= Str_Replace('-','',$_POST['txtGuardTel']);	//보호자 연락처

	$regDt	= $_POST['txtRegDt']; //등록일
	$endDt	= $_POST['txtEndDt']; //종료일
	$reason	= $_POST['txtEndReason']; //종료사유

	$rstDt		= $_POST['txtRstDt'];		//처리일자
	$rstReason	= $_POST['cboRstReason'];	//처리결과
	$reasonStr	= $_POST['txtReasonStr'];	//사유

	$kindGbn	= $_POST['cboKindGbn']; //유형
	$mp = $_POST['mp']; //중점, 일반


	if ($regGbn == '1'){
		//대상자 등록
		$sql = 'SELECT	code
				,		jumin
				FROM	mst_jumin
				WHERE	org_no	= \''.$orgNo.'\'
				AND		gbn		= \'1\'
				AND		code	= \''.$jumin.'\'';

		$row = $conn->get_array($sql);

		$mstCd = $row['code'];
		$mstJm = $row['jumin'];

		Unset($row);

		if (!$jumin) $jumin	= $_POST['txtJumin1'].$_POST['txtJumin2'];

		$juminNo = $_POST['txtJumin1'].$_POST['txtJumin2'];

		if ($mstCd){
			if ($mstJm != $juminNo && $juminNo){
				$sql = 'UPDATE	mst_jumin
						SET		jumin		= \''.$juminNo.'\'
						,		update_id	= \''.$userCd.'\'
						,		update_dt	= NOW()
						WHERE	org_no	= \''.$orgNo.'\'
						AND		gbn		= \'1\'
						AND		code	= \''.$mstCd.'\'';

				$query[SizeOf($query)] = $sql;
			}

			$juminNo	= $mstCd;
			$mstSaveYn	= 'N';
		}else{
			//마스터 저장
			$mstJumin	= $juminNo;
			$mstSaveYn	= 'Y';

			//주민번호의 7자리까지의 코드를 생성한다.
			$juminNo = SubStr($juminNo,0,7);
			$sql = 'SELECT	CAST(IFNULL(RIGHT(MAX(code),6),0) + 1 AS unsigned)
					FROM	mst_jumin
					WHERE	org_no		= \''.$orgNo.'\'
					AND		gbn			= \'1\'
					AND		LEFT(code,7)= \''.$juminNo.'\'';

			$juminSeq = '00000'.$conn->get_data($sql);
			$juminSeq = SubStr($juminSeq,StrLen($juminSeq)-6,6);
			$juminNo .= $juminSeq;
		}

		$jumin = $juminNo;

		//기존데이타 존재여부
		$sql = 'SELECT	m03_key
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_jumin = \''.$jumin.'\'';

		$mstKey = $conn->get_data($sql);

		//서비스 개인정보 데이타 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_jumin = \''.$jumin.'\'
				AND		m03_mkind = \'6\'';

		$svcCnt = $conn->get_data($sql);

		if ($svcCnt <= 0){
			if ($mstKey){
				$key = $mstKey;
			}else{
				$sql = 'SELECT	IFNULL(MAX(m03_key),0)+1
						FROM	m03sugupja
						WHERE	m03_ccode = \''.$orgNo.'\'';
				$key = $conn->get_data($sql);
			}

			$sql = 'INSERT INTO m03sugupja (
					 m03_ccode
					,m03_mkind
					,m03_jumin
					,m03_key) VALUES (
					 \''.$orgNo.'\'
					,\'6\'
					,\''.$jumin.'\'
					,\''.$key.'\'
					)';

			$query[SizeOf($query)] = $sql;
		}
		
		//마스터저장
		if ($mstSaveYn == 'Y'){
			$sql = 'INSERT INTO mst_jumin(
					 org_no
					,gbn
					,code
					,jumin
					,name
					,cd_key
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\'1\'
					,\''.$juminNo.'\'
					,\''.$mstJumin.'\'
					,\''.$name.'\'
					,\''.$key.'\'
					,\''.$userCd.'\'
					,NOW())';

			$query[SizeOf($query)] = $sql;
		}

		$sql = 'UPDATE	m03sugupja
				SET		m03_name			= \''.$name.'\'
				,		m03_tel				= \''.$phone.'\'
				,		m03_hp				= \''.$mobile.'\'
				,		m03_post_no			= \''.$postno.'\'
				,		m03_juso1			= \''.$addr.'\'
				,		m03_juso2			= \''.$addrDtl.'\'
				,		m03_yboho_name		= \''.$grdNm.'\'
				,		m03_yoyangsa4_nm	= \''.$grdAddr.'\'
				,		m03_yboho_phone		= \''.$grdTel.'\'
				,		m03_yoyangsa5_nm	= \''.$marry.$cohabit.$edu.$rel.'\'';

		if ($jumin != $juminNo){
			$sql .= '
				,		m03_jumin			= \''.$juminNo.'\'';
		}

		$sql .= '
				WHERE	m03_ccode			= \''.$orgNo.'\'
				AND		m03_jumin			= \''.$jumin.'\'';

		$query[SizeOf($query)] = $sql;

		
		$sql = 'SELECT count(*)
				FROM   hce_receipt
				WHERE  org_no	= \''.$orgNo.'\'
				AND	   org_type = \''.$SR.'\'
				AND	   IPIN		= \''.$mstKey.'\'';
		$rpt_cnt = $conn -> get_data($sql);
		
		
		if($rpt_cnt > 0){
			$sql = 'UPDATE	hce_receipt
					SET		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()
					,		phone		= \''.$phone.'\'
					,		mobile		= \''.$mobile.'\'
					,		postno		= \''.$postno.'\'
					,		addr		= \''.$addr.'\'
					,		addr_dtl	= \''.$addrDtl.'\'
					,		grd_nm		= \''.$grdNm.'\'
					/*,		grd_rel		= \''.$grdRel.'\'*/
					,		grd_tel		= \''.$grdTel.'\'
					,		grd_addr	= \''.$grdAddr.'\'
					,		marry_gbn	= \''.$marry.'\'
					,		cohabit_gbn	= \''.$cohabit.'\'
					,		edu_gbn		= \''.$edu.'\'
					,		rel_gbn		= \''.$rel.'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		IPIN	= \''.$mstKey.'\'';
			
			$query[SizeOf($query)] = $sql;
		}


		//서비스 계약기간
		if (!Empty($seq)){
		}else{
			$sql = 'INSERT INTO client_his_svc (
					 org_no
					,jumin
					,svc_cd
					,seq
					,from_dt
					,to_dt
					,svc_stat
					,mp_gbn
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$jumin.'\'
					,\''.$SR.'\'
					,\'1\'
					,\''.$myF->dateStyle($from).'\'
					,\''.$myF->dateStyle($to).'\'
					,\''.$stat.'\'
					,\''.$mp.'\'
					,\''.$userCd.'\'
					,NOW()
					)';

			$query[SizeOf($query)] = $sql;
		}

		//서비스정보 삭제
		$sql = 'DELETE
				FROM	care_svc_his
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'';

		$query[SizeOf($query)] = $sql;

		//서비스정보
		$his = Explode('?',$_POST['history']);

		if (is_array($his)){
			$seq = 1;

			foreach($his as $idx => $row){
				if ($row){
					parse_str($row,$col);

					$sql = 'INSERT INTO care_svc_his (
							 org_no
							,jumin
							,seq
							,org_nm
							,svc_cd
							,from_dt
							,to_dt
							,person_nm
							,telno) VALUES (
							 \''.$orgNo.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$_SESSION['userCenterName'].'\'
							,\''.$col['svcCd'].'\'
							,\''.$col['from'].'\'
							,\''.$col['to'].'\'
							,\''.$col['person'].'\'
							,\''.$col['telno'].'\'
							)';

					$query[SizeOf($query)] = $sql;
					$seq ++;
				}
			}
		}

		//
		$sql = 'SELECT	COUNT(*)
				FROM	client_option
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'';
		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	client_option
					SET		addr_ment	= \''.$addrMent.'\'
					,		reg_dt		= \''.$regDt.'\'
					,		rst_dt		= \''.$rstDt.'\'
					,		rst_reason	= \''.$rstReason.'\'
					,		reason_str	= \''.$reasonStr.'\'
					,		end_dt		= \''.$endDt.'\'
					,		end_reason	= \''.$reason.'\'
					,		kind_gbn	= \''.$kindGbn.'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		jumin	= \''.$jumin.'\'';
		}else{
			$sql = 'INSERT INTO client_option (
					 org_no
					,jumin
					,addr_ment
					,reg_dt
					,rst_dt
					,rst_reason
					,reason_str
					,end_dt
					,end_reason
					,kind_gbn) VALUES (
					 \''.$orgNo.'\'
					,\''.$jumin.'\'
					,\''.$addrMent.'\'
					,\''.$regDt.'\'
					,\''.$rstDt.'\'
					,\''.$rstReason.'\'
					,\''.$reasonStr.'\'
					,\''.$endDt.'\'
					,\''.$reason.'\'
					,\''.$kindGbn.'\'
					)';
		}

		$query[SizeOf($query)] = $sql;

		//일반접수 등록 체크
		if ($normalSeq > '0'){
			$sql = 'UPDATE	care_client_normal
					SET		link_IPIN	= \''.$key.'\'
					WHERE	org_no		= \''.$orgNo.'\'
					AND		normal_sr	= \''.$SR.'\'
					AND		normal_seq	= \''.$normalSeq.'\'';

			$query[SizeOf($query)] = $sql;
			$normalSeq = '0';
		}


	}else if ($regGbn == '2'){
		//일반접수 등록
		$sql = 'SELECT	COUNT(*)
				FROM	care_client_normal
				WHERE	org_no		= \''.$orgNo.'\'
				AND		normal_sr	= \''.$SR.'\'
				AND		normal_seq	= \''.$normalSeq.'\'';

		$liCnt = $conn->get_data($sql);

		if ($liCnt > 0){
			$new = false;
		}else{
			$new = true;
		}

		if ($new){
			$sql = 'SELECT	IFNULL(MAX(normal_seq),0)+1
					FROM	care_client_normal
					WHERE	org_no		= \''.$orgNo.'\'
					AND		normal_sr	= \''.$SR.'\'';

			$normalSeq = $conn->get_data($sql);

			$sql = 'INSERT INTO care_client_normal (
					 org_no
					,normal_sr
					,normal_seq) VALUES (
					 \''.$orgNo.'\'
					,\''.$SR.'\'
					,\''.$normalSeq.'\'
					)';

			$query[SizeOf($query)] = $sql;
		}

		$sql = 'UPDATE	care_client_normal
				SET		jumin		= \''.$_POST['txtJumin1'].$_POST['txtJumin2'].'\'
				,		name		= \''.$name.'\'
				,		postno		= \''.$postno.'\'
				,		addr		= \''.$addr.'\'
				,		addr_dtl	= \''.$addrDtl.'\'
				,		addr_ment	= \''.$addrMent.'\'
				,		phone		= \''.$phone.'\'
				,		mobile		= \''.$mobile.'\'
				,		grd_nm		= \''.$grdNm.'\'
				,		grd_addr	= \''.$grdAddr.'\'
				,		grd_telno	= \''.$grdTel.'\'
				,		marry_gbn	= \''.$marry.'\'
				,		cohabit_gbn	= \''.$cohabit.'\'
				,		edu_gbn		= \''.$edu.'\'
				,		rel_gbn		= \''.$rel.'\'
				,		reg_dt		= \''.$regDt.'\'
				,		end_dt		= \''.$endDt.'\'
				,		end_reason	= \''.$reason.'\'
				,		rst_dt		= \''.$rstDt.'\'
				,		rst_reason	= \''.$rstReason.'\'
				,		reason_str	= \''.$reasonStr.'\'
				';

		if ($new){
			$sql .= '
				,		insert_dt	= NOW()
				,		insert_id	= \''.$_SESSION['userCode'].'\'';
		}else{
			$sql .= '
				,		update_dt	= NOW()
				,		update_id	= \''.$_SESSION['userCode'].'\'';
		}

		$sql .= '
				WHERE	org_no		= \''.$orgNo.'\'
				AND		normal_sr	= \''.$SR.'\'
				AND		normal_seq	= \''.$normalSeq.'\'';

		$query[SizeOf($query)] = $sql;


	}else{
		exit;
	}


	$conn->begin();

	foreach($query as $sql){
		
		if (!$conn->execute($sql)){
			if ($orgNo == 'CN77C001'){
				echo $sql;
			}
			
			if($debug) echo nl2br($sql); exit;

			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();
	
	if (!Empty($_POST['txtSeq'])){
		echo 'OK_jumin='.$ed->en($jumin).'&normalSeq='.$normalSeq;
	}else {
		echo 'OK_jumin=&normalSeq='.$normalSeq;
	}

	include_once('../inc/_db_close.php');
?>