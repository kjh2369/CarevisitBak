<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$debug_mdoe = 1;

	//if ($debug) $debug_mdoe = 2;

	$conn->mode = $debug_mdoe;

	$write_mode = $_POST['write_mode']; //1:등록, 2:수정

	$code	   = $_POST['code'];		//기관코드
	$kind	   = $_POST['kind'];		//기관분류코드
	//$kind_list = $_POST['kind_list'];	//기관리스트
	//$kind_cnt  = sizeof($kind_list);	//기관리스트갯수

	$current_menu = $_POST['current_menu']; //선택메뉴
	$record_menu  = $_POST['record_menu'];  //선택메뉴


	$lbTestMode = $_POST['lbTestMode']; //테스트 모드 구분

	//고객주민번호
	if ($write_mode == 1){
		//if ($debug){
			$jumin = $_POST['jumin1'].$_POST['gender'].'A';
			$sql = 'SELECT	IFNULL(MAX(REPLACE(m03_jumin, \''.$jumin.'\', \'\')), 0) + 1
					FROM	m03sugupja
					WHERE	m03_ccode = \''.$code.'\'
					AND		LEFT(m03_jumin, '.StrLen($jumin).') = \''.$jumin.'\'';

			$tgtCd = '00000'.$conn->get_data($sql);
			$tgtCd = $jumin.SubStr($tgtCd, StrLen($tgtCd) - 5, StrLen($tgtCd));
			$jumin = $tgtCd;
		//}else{
		//	$jumin = $_POST['jumin1'].$_POST['jumin2'];
		//}

		$sql = 'select m03_key
				  from m03sugupja
				 where m03_ccode = \''.$code.'\'
				   and m03_jumin = \''.$jumin.'\'
				 order by m03_mkind
				 limit 1';

		$key = $conn->get_data($sql);

		if (empty($key)){
			// 다음 키
			$sql = 'select ifnull(max(m03_key), 0) + 1
					  from m03sugupja
					 where m03_ccode = \''.$code.'\'';

			$key = $conn->get_data($sql);
		}
	}else{
		$jumin = $ed->de($_POST['jumin']);

		$sql = 'select m03_key
				  from m03sugupja
				 where m03_ccode = \''.$code.'\'
				   and m03_jumin = \''.$jumin.'\'
				 order by m03_mkind
				 limit 1';

		$key = $conn->get_data($sql);
	}

	if (Empty($code) || Empty($jumin)){
		include('../inc/_http_home.php');
		exit;
	}


	/* 파라메타 */
	parse_str($_POST['para'], $val);

	$lsSvcList = substr($val['svcList'],1);
	$loSvcList = explode('/',$lsSvcList); //기관리스트
	$liSvcCnt  = sizeof($loSvcList); //기관리스트갯수

	$lsUseList = substr($val['useList'],1);
	$loUseList = explode('/',$lsUseList); //기관리스트

	foreach($loUseList as $i => $row){
		$loRow = explode('_',$row);
		$loUseSvc[$loRow[1]] = $loRow[0];
	}

	//트랜젝션 시작
	$conn->begin();

	//이용서비스 리스트
	for($i=0; $i<$liSvcCnt; $i++){
		$tmp_kind = explode('_',$loSvcList[$i]);
		$svc_cd = $tmp_kind[0];
		$svc_id = $tmp_kind[1];

		$useSvc = explode('_',$loSvcList[$i]);

		//이용서비스코드
		//$kind_svc_cd = $_POST['use_svc_'.$svc_id];
		$lsSvcCd = $loUseSvc[$svc_id];

		if ($lsSvcCd == '') $lsSvcCd = $svc_cd;
		if ($lsSvcCd == $svc_cd){
			$mode = $_POST[$svc_id.'_writeMode'];

			if ($mode == 1){
				//고객 이력
				if (!empty($val['txtFrom_'.$svc_id]) && !empty($val['txtTo_'.$svc_id]) && !empty($val['txtStat_'.$svc_id])){
					$sql = 'insert into client_his_svc (
							 org_no
							,jumin
							,svc_cd
							,seq
							,from_dt
							,to_dt
							,svc_stat
							,svc_reason
							,insert_id
							,insert_dt) values (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\''.$svc_cd.'\'
							,\'1\'
							,\''.$val['txtFrom_'.$svc_id].'\'
							,\''.$val['txtTo_'.$svc_id].'\'
							,\''.$val['txtStat_'.$svc_id].'\'
							,\''.$val['txtReason_'.$svc_id].'\'
							,\''.$_SESSION['userCode'].'\'
							,now()
							)';

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 echo '1';
						 echo $conn->err_back();
						 if ($conn->mode == 1) exit;
					}
				}

				//장기요양보험 이력
				if ($svc_cd == '0'){
					if (!empty($val['mgmtFrom']) && !empty($val['mgmtTo']) && !empty($val['mgmtNo']) && !empty($val['mgmtLvl'])){
						$sql = 'insert into client_his_lvl(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,app_no
								,level
								,svc_cd
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['mgmtFrom'].'\'
								,\''.$val['mgmtTo'].'\'
								,\''.$val['mgmtNo'].'\'
								,\''.$val['mgmtLvl'].'\'
								,\''.$svc_cd.'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '2';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

					//수급자구분 이력
					if (!empty($val['expenseFrom_'.$svc_id]) && !empty($val['expenseTo_'.$svc_id]) && !empty($val['expenseKind_'.$svc_id]) && !empty($val['expenseRate_'.$svc_id])){
						$sql = 'insert into client_his_kind (
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,kind
								,rate
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['expenseFrom_'.$svc_id].'\'
								,\''.$val['expenseTo_'.$svc_id].'\'
								,\''.$val['expenseKind_'.$svc_id].'\'
								,\''.$val['expenseRate_'.$svc_id].'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';


						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '3';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

					//청구한도 이력
					if (!empty($val['claimFrom_'.$svc_id]) && !empty($val['claimTo_'.$svc_id]) && !empty($val['claimAmt_'.$svc_id])){
						$sql = 'insert into client_his_limit (
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,amt
								,insert_id
								,insert_dt) values (
								\''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['claimFrom_'.$svc_id].'\'
								,\''.$val['claimTo_'.$svc_id].'\'
								,\''.str_replace(',', '', $val['claimAmt_'.$svc_id]).'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';
						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '4';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

				//가사간병
				}else if ($svc_cd == '1'){
					//서비스시간
					if (!empty($val['nusreFrom']) && !empty($val['nusreTo']) && !empty($val['nusreVal'])){
						$sql = 'insert into client_his_nurse(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,svc_val
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['nusreFrom'].'\'
								,\''.$val['nusreTo'].'\'
								,\''.$val['nusreVal'].'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '5';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

					//소득등급
					if (!empty($val['nusreLvlFrom']) && !empty($val['nusreLvlTo']) && !empty($val['nusreLvl'])){
						$sql = 'insert into client_his_lvl(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,level
								,svc_cd
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['nusreLvlFrom'].'\'
								,\''.$val['nusreLvlTo'].'\'
								,\''.$val['nusreLvl'].'\'
								,\''.$svc_cd.'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '6';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

				//노인돌봄
				}else if ($svc_cd == '2'){
					//서비스시간
					if (!empty($val['oldFrom']) && !empty($val['oldTo']) && !empty($val['oldVal'])){
						$sql = 'replace into client_his_old(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,svc_val
								,svc_tm
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['oldFrom'].'\'
								,\''.$val['oldTo'].'\'
								,\''.$val['oldVal'].'\'
								,\''.$val['oldTm'].'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '7';
							 echo $conn->error_msg;
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

					//소득등급
					if (!empty($val['oldLvlFrom']) && !empty($val['oldLvlTo']) && !empty($val['oldLvl'])){
						$sql = 'replace into client_his_lvl(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,level
								,svc_cd
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['oldLvlFrom'].'\'
								,\''.$val['oldLvlTo'].'\'
								,\''.$val['oldLvl'].'\'
								,\''.$svc_cd.'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '8';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

				//산모신생아
				}else if ($svc_cd == '3'){
					//서비스시간
					if (!empty($val['babyFrom']) && !empty($val['babyTo']) && !empty($val['babyVal'])){
						$sql = 'insert into client_his_baby(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,svc_val
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['babyFrom'].'\'
								,\''.$val['babyTo'].'\'
								,\''.$val['babyVal'].'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '9';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

					//소득등급
					if (!empty($val['babyLvlFrom']) && !empty($val['babyLvlTo']) && !empty($val['babyLvl'])){
						$sql = 'insert into client_his_lvl(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,level
								,svc_cd
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['babyLvlFrom'].'\'
								,\''.$val['babyLvlTo'].'\'
								,\''.$val['babyLvl'].'\'
								,\''.$svc_cd.'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '10';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

				//장애인활동지원
				}else if ($svc_cd == '4'){
					//서비스시간
					if (!empty($val['disFrom']) && !empty($val['disTo']) && !empty($val['disVal']) && !empty($val['disSpt'])){
						$sql = 'insert into client_his_dis(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,svc_val
								,svc_lvl
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['disFrom'].'\'
								,\''.$val['disTo'].'\'
								,\''.$val['disVal'].'\'
								,\''.$val['disSpt'].'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '11';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

					//소득등급
					if (!empty($val['disLvlFrom']) && !empty($val['disLvlTo']) && !empty($val['disLvl'])){
						$sql = 'insert into client_his_lvl(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,level
								,svc_cd
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['disLvlFrom'].'\'
								,\''.$val['disLvlTo'].'\'
								,\''.$val['disLvl'].'\'
								,\''.$svc_cd.'\'
								,\''.$_SESSION['userCode'].'\'
								,now()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo '12';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}

				}else if ($svc_cd == '6'){
					//재가지원
					/*
					if (!Empty($val['txtFrom_26']) && !Empty($val['txtTo_26'])){
						$supportCd	= $_POST['txtResourceCd'];
						$supportNm	= $_POST['txtResourceNm'];
						$supportNo	= ($_POST['txtResourceNo'] ? 'L'.$_POST['txtResourceNo'] : '');
						$supportLvl	= $_POST['cboResourceLvl'];
						$supportGbn	= $_POST['cboResourceGbn'];
						$supportPic	= $_POST['txtResourcePicNm'];
						$supportTel	= Str_Replace('-','',$_POST['txtResourceTelno']);

						$sql = 'INSERT INTO client_his_care(
								 org_no
								,jumin
								,seq
								,from_dt
								,to_dt
								,care_cost
								,care_org_no
								,care_org_nm
								,care_no
								,care_lvl
								,care_gbn
								,care_pic_nm
								,care_telno
								,insert_id
								,insert_dt) VALUES (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'1\'
								,\''.$val['txtFrom_26'].'\'
								,\''.$val['txtTo_26'].'\'
								,\''.$_POST['svcCost_26'].'\'
								,\''.$supportCd.'\'
								,\''.$supportNm.'\'
								,\''.$supportNo.'\'
								,\''.$supportLvl.'\'
								,\''.$supportGbn.'\'
								,\''.$supportPic.'\'
								,\''.$supportTel.'\'
								,\''.$_SESSION['userCode'].'\'
								,NOW()
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 echo 'ERROR_CARE';
							 echo $conn->err_back();
							 if ($conn->mode == 1) exit;
						}
					}
					*/

				}else{
				}
			}

			//기타유료
			if ($svc_cd >= 'A' && $svc_cd <= 'C'){
				$sql = 'select ifnull(max(seq), 0)
						  from client_his_svc
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd = \''.$svc_cd.'\'';

				//$svcSeq = $conn->get_data($sql);
				$svcSeq = $val['svcSeq_'.$svc_id];

				$sql = 'select count(*)
						  from client_his_other
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd = \''.$svc_cd.'\'
						   and seq    = \''.$svcSeq.'\'';

				$svcCost = str_replace(',','',$_POST['svcCost_'.$svc_id]);
				$svcCnt  = str_replace(',','',$_POST['svcCnt_'.$svc_id]);

				$recomNm  = $_POST['recomNm_'.$svc_cd];
				$recomTel = str_replace('-', '', $_POST['recomTel_'.$svc_cd]);
				$recomAmt = intval(str_replace(',', '', $_POST['recomAmt_'.$svc_cd]));

				if (empty($svcCost)) $svcCost = 0;
				if (empty($svcCnt)) $svcCnt = 0;

				if ($conn->get_data($sql) > 0){
					$sql = 'update client_his_other
							   set svc_val   = \''.$_POST['svcGbn_'.$svc_id].'\'
							,      svc_cost  = \''.$svcCost.'\'
							,      svc_cnt   = \''.$svcCnt.'\'
							,      recom_nm  = \''.$recomNm.'\'
							,      recom_tel = \''.$recomTel.'\'
							,      recom_amt = \''.$recomAmt.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and svc_cd = \''.$svc_cd.'\'
							   and seq    = \''.$svcSeq.'\'';
				}else{
					$sql = 'insert into client_his_other (
							 org_no
							,jumin
							,svc_cd
							,seq
							,svc_val
							,svc_cost
							,svc_cnt
							,recom_nm
							,recom_tel
							,recom_amt
							,insert_id
							,insert_dt) values (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\''.$svc_cd.'\'
							,\''.$svcSeq.'\'
							,\''.$_POST['svcGbn_'.$svc_id].'\'
							,\''.$svcCost.'\'
							,\''.$svcCnt.'\'
							,\''.$recomNm.'\'
							,\''.$recomTel.'\'
							,\''.$recomAmt.'\'
							,\''.$_SESSION['userCode'].'\'
							,now())';
				}

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 echo '13';
					 echo $conn->error_msg;
					 echo $conn->err_back();
					 if ($conn->mode == 1) exit;
				}
			}

			//재가지원
			if ($svc_cd == '6'){
				/*
				$svcSeq = $val['svcSeq_26'];
				$sql = 'SELECT	COUNT(*)
						FROM	client_his_care
						WHERE	org_no	= \''.$code.'\'
						AND		jumin	= \''.$jumin.'\'
						AND		seq		= \''.$svcSeq.'\'';

				$liCnt = $conn->get_data($sql);

				$supportCd	= $_POST['txtResourceCd'];
				$supportNm	= $_POST['txtResourceNm'];
				$supportNo	= ($_POST['txtResourceNo'] ? 'L'.$_POST['txtResourceNo'] : '');
				$supportLvl	= $_POST['cboResourceLvl'];
				$supportGbn	= $_POST['cboResourceGbn'];
				$supportPic	= $_POST['txtResourcePicNm'];
				$supportTel	= Str_Replace('-','',$_POST['txtResourceTelno']);

				if ($liCnt > 0){
					$sql = 'UPDATE	client_his_care
							SET		care_cost	= \''.$_POST['svcCost_26'].'\'
							,		care_org_no	= \''.$supportCd.'\'
							,		care_org_nm	= \''.$supportNm.'\'
							,		care_no		= \''.$supportNo.'\'
							,		care_lvl	= \''.$supportLvl.'\'
							,		care_gbn	= \''.$supportGbn.'\'
							,		care_pic_nm	= \''.$supportPic.'\'
							,		care_telno	= \''.$supportTel.'\'
							WHERE	org_no	= \''.$code.'\'
							AND		jumin	= \''.$jumin.'\'
							AND		seq		= \''.$svcSeq.'\'';
				}else{
					$sql = 'INSERT INTO client_his_care(
							 org_no
							,jumin
							,seq
							,from_dt
							,to_dt
							,care_cost
							,care_org_no
							,care_org_nm
							,care_no
							,care_lvl
							,care_gbn
							,care_pic_nm
							,care_telno
							,insert_id
							,insert_dt) VALUES (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\'1\'
							,\''.$val['txtFrom_26'].'\'
							,\''.$val['txtTo_26'].'\'
							,\''.$_POST['svcCost_26'].'\'
							,\''.$supportCd.'\'
							,\''.$supportNm.'\'
							,\''.$supportNo.'\'
							,\''.$supportLvl.'\'
							,\''.$supportGbn.'\'
							,\''.$supportPic.'\'
							,\''.$supportTel.'\'
							,\''.$_SESSION['userCode'].'\'
							,NOW()
							)';
				}

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 echo '13';
					 echo $conn->error_msg;
					 echo $conn->err_back();
					 if ($conn->mode == 1) exit;
				}
				*/
			}

			if ($svc_cd != '0'){
				//마지막적용일을 찾는다.
				$sql = "select adddate(date_format(max(m31_edate), '%Y-%m-%d'), interval 1 day)
						  from m31sugupja
						 where m31_ccode = '$code'
						   and m31_mkind = '$svc_cd'
						   and m31_jumin = '$jumin'";

				$last_app_dt = $conn->get_data($sql);

				if (empty($last_app_dt)) $last_app_dt = $_POST[$svc_id.'_startDt'];

				if ($_POST[$svc_id.'_startDt'] != $last_app_dt){
					#$_POST[$svc_id.'_startDt']  = $last_app_dt;
					$first_app_dt_modify = false;
				}else{
					$first_app_dt_modify = true;
				}

				//적용시작일 오료여부를 수정한다.
				$sql = "select m31_sdate, m31_edate, date_format(adddate(date_format(max('".str_replace('-', '', $_POST[$svc_id.'_startDt'])."'), '%Y-%m-%d'), interval -1 day), '%Y%m%d') as app_dt
						  from m31sugupja
						 where m31_ccode  = '$code'
						   and m31_mkind  = '$svc_cd'
						   and m31_jumin  = '$jumin'
						   and m31_edate >= '".str_replace('-', '', $_POST[$svc_id.'_startDt'])."'
						 group by m31_sdate, m31_edate
						 order by m31_sdate desc
						 limit 1";

				$app_dt = $conn->get_array($sql);

				if ($app_dt[0] != '' && $app_dt[1] != '' && $app_dt[2] != ''){
					if ($app_dt[0] > $app_dt[2]){
						$app_dt[2] = $app_dt[0];
					}

					$sql = "update m31sugupja
							   set m31_edate = '".$app_dt[2]."'
							 where m31_ccode = '$code'
							   and m31_mkind = '$svc_cd'
							   and m31_jumin = '$jumin'
							   and m31_sdate = '".$app_dt[0]."'";

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo '14';
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}

					if ($_POST[$svc_id.'_startDt'] <= $app_dt[2]){
						$_POST[$svc_id.'_startDt']  = $myF->dateAdd('day', '1', $myF->dateStyle($app_dt[2]), 'Ymd');
					}
				}
			}

			//담당요양보호사
			$yoy_jumin1 = $_POST[$svc_id.'_mem_cd1'];
			$yoy_jumin2 = $_POST[$svc_id.'_mem_cd2'];

			if (!is_numeric($yoy_jumin1)) $yoy_jumin1 = $ed->de($yoy_jumin1);
			if (!is_numeric($yoy_jumin2)) $yoy_jumin2 = $ed->de($yoy_jumin2);

			//히스토리 괸리 여부
			$history_yn	= $_POST[$svc_id.'_historyYn'];

			if ($history_yn == 'Y'){
				if (!history_save($conn, $code, $svc_cd, $jumin, $myF->dateStyle(str_replace('-', '', $_POST[$svc_id.'_startDt'])))){
					$conn->rollback();
					echo '15';
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
			}

			// 현재 담당요양사를 찾는다.
			if ($svc_cd == '0'){
				$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind, m03_sdate
						  from m03sugupja
						 inner join m02yoyangsa
							on m02_ccode = m03_ccode
						   and m02_mkind = m03_mkind
						   and m02_yjumin = m03_yoyangsa1
						 where m03_ccode = '$code'
						   and m03_mkind = '$svc_cd'
						   and m03_jumin = '$jumin'";
				$mem_array = $conn->get_array($sql);

				if ($mem_array != null){
					$yoy_jumin = $mem_array[0];
					$beforeDate = $conn->get_data("select ifnull(max(m32_a_date), '')
													 from m32jikwon
													where m32_ccode   = '$code'
													  and m32_mkind   = '$svc_cd'
													  and m32_jumin   = '$jumin'
													  and m32_a_jumin = '$yoy_jumin'");

					if ($beforeDate == ''){
						$beforeDate = $mem_array[5];
					}
					$beforeJumin	= $mem_array[0];
					$beforeName		= $mem_array[1];
					$beforeGenger	= $mem_array[2];
					$beforeTel		= $mem_array[3];
					$beforeLicense  = (strLen($mem_array[4]) == 1 ? '0' : '').$mem_array[4];
				}
			}else{
				unset($beforeJumin);
			}

			//저장시작
			$sql = "select count(*)
					  from m03sugupja
					 where m03_ccode = '$code'
					   and m03_mkind = '$svc_cd'
					   and m03_jumin = '$jumin'";
			if ($conn->get_data($sql) == 0){
				// 저장
				$sql = "insert into m03sugupja (m03_ccode, m03_mkind, m03_jumin, m03_key) values ('$code', '$svc_cd', '$jumin', '$key')";
				if (!$conn->execute($sql)){
					$conn->rollback();
					echo '16';
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
			}

			//저장
			//$sql = CareQueryExec($ed, $code, $svc_cd, $jumin, $svc_id, $_POST);
			$yoy_jumin1 = $_POST[$svc_id.'_mem_cd1'];
			$yoy_jumin2 = $_POST[$svc_id.'_mem_cd2'];

			if (!is_numeric($yoy_jumin1)) $yoy_jumin1 = $ed->de($yoy_jumin1);
			if (!is_numeric($yoy_jumin2)) $yoy_jumin2 = $ed->de($yoy_jumin2);

			parse_str($_POST['para'], $val);

			$sql = 'update m03sugupja
					   set m03_name			= \''.$_POST['name'].'\'
					,      m03_hp			= \''.str_replace('-', '', $_POST['mobile']).'\'
					,      m03_tel			= \''.str_replace('-', '', $_POST['phone']).'\'
					,      m03_post_no		= \''.($_POST['txtPostNo'] ? $_POST['txtPostNo'] : $_POST['postno1'].$_POST['postno2']).'\'
					,      m03_juso1		= \''.($_POST['txtAddr'] ? $_POST['txtAddr'] : $_POST['addr']).'\'
					,      m03_juso2		= \''.($_POST['txtAddrDtl'] ? $_POST['txtAddrDtl'] : $_POST['addr_dtl']).'\'
					,      m03_gaeyak_fm	= \''.str_replace('-', '', $_POST[$svc_id.'_gaeYakFm']).'\'
					,      m03_gaeyak_to	= \''.str_replace('-', '', $_POST[$svc_id.'_gaeYakTo']).'\'
					,      m03_yboho_name	= \''.$_POST['protect_nm'].'\'
					,      m03_yboho_juminno = \''.str_replace('-', '', $_POST['protect_jumin']).'\'
					,      m03_yboho_gwange	= \''.$_POST['protect_rel'].'\'
					,      m03_yboho_phone	= \''.str_replace('-', '', $_POST['protect_tel']).'\'
					,      m03_yboho_addr	= \''.$_POST['protect_addr'].'\'
					,      m03_sugup_status	= \''.$_POST[$svc_id.'_sugupStatus'].'\'
					,      m03_vlvl         = \''.$_POST[$svc_id.'_gbn'].'\'
					,      m03_ylvl			= \''.$_POST[$svc_id.'_lvl'].'\'
					,      m03_kupyeo_max	= \''.str_replace(',', '', $_POST[$svc_id.'_kupyeoMax']).'\'
					,      m03_skind		= \''.$_POST[$svc_id.'_kind'].'\'
					,      m03_bonin_yul	= \''.$_POST[$svc_id.'_boninYul'].'\'
					,      m03_kupyeo_1		= \''.str_replace(',', '', $_POST[$svc_id.'_kupyeo1']).'\'
					,      m03_kupyeo_2		= \''.str_replace(',', '', $_POST[$svc_id.'_kupyeo2']).'\'
					,      m03_baby_svc_cnt = \''.str_replace(',', '', $_POST[$svc_id.'_svcdays']).'\'
					,      m03_injung_no	= \''.$_POST[$svc_id.'_injungNo'].'\'
					,      m03_injung_from	= \''.str_replace('-', '', $_POST[$svc_id.'_injungFrom']).'\'
					,      m03_injung_to	= \''.str_replace('-', '', $_POST[$svc_id.'_injungTo']).'\'
					,      m03_byungmung	= \''.$_POST[$svc_id.'_byungMung'].'\'
					,      m03_disease_nm   = \''.$_POST[$svc_id.'_diseaseNm'].'\'
					,      m03_stat_nogood  = \''.$_POST[$svc_id.'_statNogood'].'\'
					,      m03_yoyangsa1	= \''.$yoy_jumin1.'\'
					,      m03_yoyangsa2	= \''.$yoy_jumin2.'\'
					,      m03_yoyangsa1_nm	= \''.$_POST[$svc_id.'_mem_nm1'].'\'
					,      m03_yoyangsa2_nm	= \''.$_POST[$svc_id.'_mem_nm2'].'\'
					,      m03_partner      = \''.$_POST[$svc_id.'_partner'].'\'
					,      m03_bath_add_yn  = \''.$_POST[$svc_id.'_bathAddYn'].'\'
					,      m03_stop_reason  = \''.$_POST[$svc_id.'_stopReason'].'\'
					,      m03_overtime     = \''.$_POST[$svc_id.'_overTime'].'\'
					,      m03_sgbn         = \''.$_POST['addPay1'].'\'
					,      m03_add_pay_gbn  = \''.$val['addPay2'].'\'
					,      m03_add_time1    = \''.$_POST['sidoTime'].'\'
					,      m03_add_time2    = \''.$_POST['jachTime'].'\'
					,      m03_client_no    = \''.$_POST['client_no'].'\'
					,      m03_memo         = \''.addslashes($_POST['memo']).'\'
					,      m03_bipay1       = \''.str_replace(',', '', $_POST[$svc_id.'_bipay1']).'\'
					,      m03_bipay2       = \''.str_replace(',', '', $_POST[$svc_id.'_bipay2']).'\'
					,      m03_bipay3       = \''.str_replace(',', '', $_POST[$svc_id.'_bipay3']).'\'
					,      m03_expense_yn   = \''.$_POST[$svc_id.'_expense_yn'].'\'
					,      m03_expense_pay  = \''.str_replace(',', '', $_POST[$svc_id.'_expense_pay']).'\'
					,      m03_sdate		= \''.str_replace('-', '', $_POST[$svc_id.'_startDt']).'\'
					,      m03_edate		= \'99991231\'
					,      m03_del_yn       = \'N\'
					,	   m03_cms_yn       = \''.$_POST['optCmsYn'].'\'
					 where m03_ccode		= \''.$code.'\'
					   and m03_mkind		= \''.$svc_cd.'\'
					   and m03_jumin		= \''.$jumin.'\'';
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo '17';
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}

			/**************************************************

				산모신생아, 산모유료 추가 금액

			**************************************************/
				if ($svc_id == '23' || $svc_id == '31'){
					//순번
					$sql = 'select ifnull(max(svc_seq), 0)
							  from client_svc_addpay
							 where org_no   = \''.$code.'\'
							   and svc_kind = \''.$svc_cd.'\'
							   and svc_ssn  = \''.$jumin.'\'
							   and del_flag = \'N\'';
					$addpay_seq = $conn->get_data($sql);

					//저장
					if ($addpay_seq == 0){
						$addpay_seq    = 1;
						$addpay_update = false;
						$sql = 'insert into client_svc_addpay (
								 org_no
								,svc_kind
								,svc_ssn
								,svc_seq
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$svc_cd.'\'
								,\''.$jumin.'\'
								,\''.$addpay_seq.'\'
								,\''.$_SESSION['userCode'].'\'
								,\''.date('Y-m-d', mktime()).'\')';

						if (!$conn->execute($sql)){
							$conn->rollback();
							echo '18';
							echo $conn->err_back();
							if ($conn->mode == 1) exit;
						}
					}else{
						$addpay_update = true;
					}

					$sql = 'update client_svc_addpay
							   set school_not_cnt	= \''.str_replace(',', '', $_POST[$svc_id.'_not_school_cnt']).'\'
							,      school_not_pay	= \''.str_replace(',', '', $_POST[$svc_id.'_not_school_pay']).'\'
							,      school_cnt		= \''.str_replace(',', '', $_POST[$svc_id.'_school_cnt']).'\'
							,      school_pay		= \''.str_replace(',', '', $_POST[$svc_id.'_school_pay']).'\'
							,      family_cnt		= \''.str_replace(',', '', $_POST[$svc_id.'_family_cnt']).'\'
							,      family_pay		= \''.str_replace(',', '', $_POST[$svc_id.'_family_pay']).'\'
							,      home_in_yn		= \''.$_POST[$svc_id.'_home_in_yn'].'\'
							,      home_in_pay		= \''.str_replace(',', '', $_POST[$svc_id.'_home_in_pay']).'\'
							,      holiday_pay		= \''.str_replace(',', '', $_POST[$svc_id.'_holiday_pay']).'\'';

					if ($addpay_update){
						$sql .= ', update_id = \''.$_SESSION['userCode'].'\'
								 , update_dt = \''.date('Y-m-d', mktime()).'\'';
					}

					$sql .= ' where org_no	 = \''.$code.'\'
								and svc_kind = \''.$svc_cd.'\'
								and svc_ssn  = \''.$jumin.'\'
								and svc_seq  = \''.$addpay_seq.'\'';

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo '19';
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
			/*************************************************/

			$sql = "delete
					  from m03sugupja
					 where m03_ccode = '$code'
					   and m03_mkind = ''
					   and m03_jumin = '$jumin'";
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo '20';
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}


			/************************************************************************

				계약이 종료되면 계약종료일 이후의 일정을 삭제처리한다.

			*************************************************************************/
				if ($_POST[$svc_id.'_sugupStatus'] == '2' ||
					$_POST[$svc_id.'_sugupStatus'] == '4' ||
					$_POST[$svc_id.'_sugupStatus'] == '5'){

					if (!client_iljung_del($conn, $code, $svc_cd, $jumin, str_replace('-', '', $_POST[$svc_id.'_gaeYakTo']))){
						$conn->rollback();
						echo '21';
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
			/*************************************************************************/

			if ($first_app_dt_modify){
				if (str_replace('-', '', $_POST[$svc_id.'_gaeYakFm']) != str_replace('-', '', $_POST[$svc_id.'_startDt'])){
					$sql = "select min(m31_sdate)
							  from m31sugupja
							 where m31_ccode = '$code'
							   and m31_mkind = '$svc_cd'
							   and m31_jumin = '$jumin'";
					$his_start_dt = $conn->get_data($sql);

					if ($his_start_dt == ''){
						$sql = "update m03sugupja
								   set m03_sdate = m03_gaeyak_fm
								 where m03_ccode = '$code'
								   and m03_mkind = '$svc_cd'
								   and m03_jumin = '$jumin'";

						if (!$conn->execute($sql)){
							$conn->rollback();
							echo '22';
							echo $conn->err_back();
							if ($conn->mode == 1) exit;
						}
					}else{
						if (str_replace('-', '', $_POST[$svc_id.'_gaeYakFm']) != $his_start_dt){
							$new_date = str_replace('-', '', $_POST[$svc_id.'_gaeYakFm']);

							$sql = "select count(*)
									  from m31sugupja
									 where m31_ccode  = '$code'
									   and m31_mkind  = '$svc_cd'
									   and m31_sdate <= '$new_date'
									   and m31_edate >= '$new_date'";

							if ($conn->get_data($sql) == 0){
								$sql = "update m31sugupja
										   set m31_sdate = '$new_date'
										 where m31_ccode = '$code'
										   and m31_mkind = '$svc_cd'
										   and m31_jumin = '$jumin'
										   and m31_sdate = '$his_start_dt'";

								if (!$conn->execute($sql)){
									$conn->rollback();
									echo '23';
									echo $conn->err_back();
									if ($conn->mode == 1) exit;
								}
							}
						}
					}
				}
			}

			// 담당요양사 변경여부
			if ($beforeJumin != $yoy_jumin1){
				$gubun = '1';

				// 변경된 요양사 정보
				$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind
						  from m02yoyangsa
						 where m02_ccode  = '$code'
						   and m02_mkind  = '$svc_cd'
						   and m02_yjumin = '$yoy_jumin1'";
				$mem_array = $conn->get_array($sql);

				if ($mem_array != null){
					//$afterDate		= str_replace('-', '', $_POST[$svc_cd.'_memChangeDt']);
					$afterDate		= str_replace('-', '', $_POST[$svc_id.'_startDt']);
					$afterJumin		= $mem_array[0];
					$afterName		= $mem_array[1];
					$afterGenger	= $mem_array[2];
					$afterTel		= $mem_array[3];
					$afterLicense	= (strLen($mem_array[4]) == 1 ? '0' : '').$mem_array[4];
				}

				// 센터정보
				$sql = "select m00_mname, m00_ctel"
					 . "  from m00center"
					 . " where m00_mcode = '".$code
					 . "'  and m00_mkind = '".$svc_cd
					 . "'";
				$centerArray = $conn->get_array($sql);
				$centerMname = $centerArray[0];
				$centerTel = $centerArray[1];

				$sql = "replace into m32jikwon values ("
					 . "  '".$code
					 . "','".$svc_cd
					 . "','".$jumin
					 . "','".$gubun
					 . "','".$beforeDate
					 . "','".$beforeJumin
					 . "','".$beforeName
					 . "','".$beforeGenger
					 . "','".$beforeTel
					 . "','".$beforeLicense
					 . "','".$afterDate
					 . "','".$afterJumin
					 . "','".$afterName
					 . "','".$afterGenger
					 . "','".$afterTel
					 . "','".$afterLicense
					 . "','".$centerMname
					 . "','"
					 . "','".$centerTel
					 . "')";
				if (!$conn->execute($sql)){
					$conn->rollback();
					echo '24';
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
			}
		}else{
			if (!$lbTestMode){
				if ($_POST[$svc_id.'_writeMode'] == 2){
					//히스토리 괸리 여부
					$history_yn	= $_POST[$svc_id.'_historyYn'];

					if ($history_yn == 'Y'){
						if (!history_save($conn, $code, $svc_cd, $jumin, $_POST[$svc_id.'_startDt'], 'Y')){
							$conn->rollback();
							echo '25';
							echo $conn->err_back();
							if ($conn->mode == 1) exit;
						}
					}
				}
			}

			$arrDelSvcCD[$svc_cd] = 'Y';

			//이용하지 않는 서비스는 삭제한다.
			$sql = "update m03sugupja
					   set m03_name		= '".$_POST['name']."'
					,      m03_hp		= '".str_replace('-', '', $_POST['mobile'])."'
					,      m03_tel		= '".str_replace('-', '', $_POST['phone'])."'
					,      m03_post_no	= '".($_POST['txtPostNo'] ? $_POST['txtPostNo'] : $_POST['postno1'].$_POST['postno2'])."'
					,      m03_juso1	= '".($_POST['txtAddr'] ? $_POST['txtAddr'] : $_POST['addr'])."'
					,      m03_juso2	= '".($_POST['txtAddrDtl'] ? $_POST['txtAddrDtl'] : $_POST['addr_dtl'])."'
					,      m03_del_yn	= 'Y'
					 where m03_ccode	= '".$code."'
					   and m03_mkind	= '".$svc_cd."'
					   and m03_jumin	= '".$jumin."'";
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo '26';
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}
	}



	/*********************************************************

		가족요양보호사

		*****************************************************/
		# 1.기존데이타 삭제
		$sql = 'delete
				  from client_family
				 where org_no   = \''.$code.'\'
				   and cf_jumin = \''.$jumin.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 echo '27';
			 echo $conn->err_back();
			 if ($conn->mode == 1) exit;
		}


		# 2.작성된 데이타 저장
		$objMem    = $_POST['objFamilyCD'];
		$objMemCnt = sizeof($objMem);
		$objSeq    = 1;

		for($i=0; $i<$objMemCnt; $i++){
			parse_str($objMem[$i], $memInfo);

			if (!empty($memInfo['jumin'])){
				$sql = 'insert into client_family (
						 org_no
						,cf_jumin
						,cf_seq
						,cf_mem_cd
						,cf_mem_nm
						,cf_kind) values (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$objSeq.'\'
						,\''.$ed->de($memInfo['jumin']).'\'
						,\''.$memInfo['name'].'\'
						,\''.$_POST['objFamilyGbn'][$i].'\')';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 echo '28';
					 echo $conn->err_back();
					 if ($conn->mode == 1) exit;
				}

				$objSeq ++;
			}
		}
	/********************************************************/



	/*********************************************************

		추천인 등록

		*****************************************************/

		$sql = 'select cr_kind as kind
				,      cr_name as name
				  from client_recom
				 where org_no   = \''.$code.'\'
				   and cr_jumin = \''.$jumin.'\'';

		$recomList = $conn->_fetch_array($sql, 'kind');

		for($i=0; $i<$liSvcCnt; $i++){
			$tmp_kind = explode('_',$loSvcList[$i]);
			$svc_cd = $tmp_kind[0];
			$svc_id = $tmp_kind[1];

			if ($svc_id > 30 && $svc_id < 40){
				if ($_POST['recomNm_'.$svc_cd] != ''){
					if ($recomList[$svc_cd] == ''){
						$sql = 'insert into client_recom (
								 org_no
								,cr_jumin
								,cr_kind
								,cr_name
								,cr_tel
								,cr_amt
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\''.$svc_cd.'\'
								,\''.$_POST['recomNm_'.$svc_cd].'\'
								,\''.str_replace('-', '', $_POST['recomTel_'.$svc_cd]).'\'
								,\''.str_replace(',', '', $_POST['recomAmt_'.$svc_cd]).'\'
								,\''.$_SESSION['userCode'].'\'
								,now())';
					}else{
						$sql = 'update client_recom
								   set cr_name   = \''.$_POST['recomNm_'.$svc_cd].'\'
								,      cr_tel    = \''.str_replace('-', '', $_POST['recomTel_'.$svc_cd]).'\'
								,      cr_amt    = \''.str_replace(',', '', $_POST['recomAmt_'.$svc_cd]).'\'
								,      cr_use_yn = \''.($arrDelSvcCD[$svc_cd] != 'Y' ? 'Y' : 'N').'\'
								,      update_id = \''.$_SESSION['userCode'].'\'
								,      update_dt = now()
								 where org_no    = \''.$code.'\'
								   and cr_jumin  = \''.$jumin.'\'
								   and cr_kind   = \''.$svc_cd.'\'';
					}

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 echo '29';
						 echo $conn->err_back();
						 if ($conn->mode == 1) exit;
					}
				}
			}
		}

	/********************************************************/


	/*********************************************************
	 * 옵션 저장
	 *********************************************************/
		$lsLimitYn = ($_POST['chkOverApp'] == 'Y' ? 'Y' : 'N'); //한도초과 처리 여부
		$lsDayNightYn = ($_POST['optDANYN'] == 'Y' ? 'Y' : 'N'); //주야간보호 여부
		$contType = $_POST['optContType']; //계약유형
		$billPhone = str_replace('-','',$_POST['txtBillPhone']);
		$realJumin = $ed->en($_POST['realJumin']);

		$sql = 'SELECT COUNT(*)
				  FROM client_option
				 WHERE org_no = \''.$code.'\'
				   AND jumin  = \''.$jumin.'\'';
		$liCnt = $conn->get_data($sql);

		if ($liCnt > 0){
			$sql = 'UPDATE	client_option
					SET		limit_yn	= \''.$lsLimitYn.'\'
					,		day_night_yn= \''.$lsDayNightYn.'\'
					,		cont_type	= \''.$contType.'\'
					,		bill_phone	= \''.$billPhone.'\'
					,		real_jumin  = \''.$realJumin.'\'
					WHERE	org_no = \''.$code.'\'
					AND		jumin  = \''.$jumin.'\'';
		}else{
			$sql = 'INSERT INTO client_option (
					 org_no
					,jumin
					,limit_yn
					,day_night_yn
					,cont_type
					,bill_phone) VALUES (
					 \''.$code.'\'
					,\''.$jumin.'\'
					,\''.$lsLimitYn.'\'
					,\''.$lsDayNightYn.'\'
					,\''.$contType.'\'
					,\''.$billPhone.'\'
					)';
		}
		$conn->execute($sql);



	/**************************************************
		초기상담기록지
		**********************************************/
		include_once('../counsel/client_counsel_save_sub.php');
	/**************************************************
		방문상담기록지
		**********************************************/
		include_once('../counsel/client_counsel_visit_save.php');
	/**************************************************
		전화상담기록지
		**********************************************/
		include_once('../counsel/client_counsel_phone_save.php');
	/**************************************************
		불만 및 고충러리기록지
		**********************************************/
		include_once('../counsel/client_counsel_stress_save.php');
	/**************************************************
		사례관리 회의
		**********************************************/
		include_once('../counsel/client_counsel_case_save.php');
	/*************************************************/

	//상태변화
		$statDt = $_POST['statDt'];
		$statBackDt = $_POST['statBackDt'];

		$sql = 'DELETE
				  FROM counsel_client_state
				 WHERE org_no = \''.$code.'\'
				   AND jumin  = \''.$jumin.'\'
				   AND reg_dt = \''.$statBackDt.'\'';
		$conn->execute($sql);

		if (!Empty($statDt)){
			$statTime  = Str_Replace(':','',$_POST['statTm']);
			$statRegCd = $ed->de($_POST['statRegCd']);
			$statRegNm = $conn->member_name($code, $statRegCd);
			$statYoyCd = $ed->de($_POST['statYoyCd']);
			$statYoyNm = $conn->member_name($code, $statYoyCd);
			$statText  = AddSlashes($_POST['statText']);
			$statTake  = AddSlashes($_POST['statTake']);

			$sql = 'REPLACE INTO counsel_client_state (
					 org_no
					,jumin
					,reg_dt
					,reg_tm
					,reg_cd
					,reg_nm
					,yoy_cd
					,yoy_nm
					,stat
					,take) VALUES (
					 \''.$code.'\'
					,\''.$jumin.'\'
					,\''.$statDt.'\'
					,\''.$statTime.'\'
					,\''.$statRegCd.'\'
					,\''.$statRegNm.'\'
					,\''.$statYoyCd.'\'
					,\''.$statYoyNm.'\'
					,\''.$statText.'\'
					,\''.$statTake.'\')';
			$conn->execute($sql);
		}

	//트랜젝션 종료
	$conn->commit();

	include_once("../inc/_db_close.php");

	//재가요양 수정쿼리
	/*function CareQueryExec($ed, $code, $kind, $jumin, $svc_id, $_POST, $del_flag = 'N'){
		//담당요양보호사
		$yoy_jumin1 = $_POST[$svc_id.'_mem_cd1'];
		$yoy_jumin2 = $_POST[$svc_id.'_mem_cd2'];

		if (!is_numeric($yoy_jumin1)) $yoy_jumin1 = $ed->de($yoy_jumin1);
		if (!is_numeric($yoy_jumin2)) $yoy_jumin2 = $ed->de($yoy_jumin2);

		parse_str($_POST['para'], $val);

		$sql = 'update m03sugupja
				   set m03_name			= \''.$_POST['name'].'\'
				,      m03_hp			= \''.str_replace('-', '', $_POST['mobile']).'\'
				,      m03_tel			= \''.str_replace('-', '', $_POST['phone']).'\'
				,      m03_post_no		= \''.($_POST['txtPostNo'] ? $_POST['txtPostNo'] : $_POST['postno1'].$_POST['postno2']).'\'
				,      m03_juso1		= \''.($_POST['txtAddr'] ? $_POST['txtAddr'] : $_POST['addr']).'\'
				,      m03_juso2		= \''.($_POST['txtAddrDtl'] ? $_POST['txtAddrDtl'] : $_POST['addr_dtl']).'\'
				,      m03_gaeyak_fm	= \''.str_replace('-', '', $_POST[$svc_id.'_gaeYakFm']).'\'
				,      m03_gaeyak_to	= \''.str_replace('-', '', $_POST[$svc_id.'_gaeYakTo']).'\'
				,      m03_yboho_name	= \''.$_POST['protect_nm'].'\'
				,      m03_yboho_juminno = \''.str_replace('-', '', $_POST['protect_jumin']).'\'
				,      m03_yboho_gwange	= \''.$_POST['protect_rel'].'\'
				,      m03_yboho_phone	= \''.str_replace('-', '', $_POST['protect_tel']).'\'
				,      m03_yboho_addr	= \''.$_POST['protect_addr'].'\'
				,      m03_sugup_status	= \''.$_POST[$svc_id.'_sugupStatus'].'\'
				,      m03_vlvl         = \''.$_POST[$svc_id.'_gbn'].'\'
				,      m03_ylvl			= \''.$_POST[$svc_id.'_lvl'].'\'
				,      m03_kupyeo_max	= \''.str_replace(',', '', $_POST[$svc_id.'_kupyeoMax']).'\'
				,      m03_skind		= \''.$_POST[$svc_id.'_kind'].'\'
				,      m03_bonin_yul	= \''.$_POST[$svc_id.'_boninYul'].'\'
				,      m03_kupyeo_1		= \''.str_replace(',', '', $_POST[$svc_id.'_kupyeo1']).'\'
				,      m03_kupyeo_2		= \''.str_replace(',', '', $_POST[$svc_id.'_kupyeo2']).'\'
				,      m03_baby_svc_cnt = \''.str_replace(',', '', $_POST[$svc_id.'_svcdays']).'\'
				,      m03_injung_no	= \''.$_POST[$svc_id.'_injungNo'].'\'
				,      m03_injung_from	= \''.str_replace('-', '', $_POST[$svc_id.'_injungFrom']).'\'
				,      m03_injung_to	= \''.str_replace('-', '', $_POST[$svc_id.'_injungTo']).'\'
				,      m03_byungmung	= \''.$_POST[$svc_id.'_byungMung'].'\'
				,      m03_disease_nm   = \''.$_POST[$svc_id.'_diseaseNm'].'\'
				,      m03_stat_nogood  = \''.$_POST[$svc_id.'_statNogood'].'\'
				,      m03_yoyangsa1	= \''.$yoy_jumin1.'\'
				,      m03_yoyangsa2	= \''.$yoy_jumin2.'\'
				,      m03_yoyangsa1_nm	= \''.$_POST[$svc_id.'_mem_nm1'].'\'
				,      m03_yoyangsa2_nm	= \''.$_POST[$svc_id.'_mem_nm2'].'\'
				,      m03_partner      = \''.$_POST[$svc_id.'_partner'].'\'
				,      m03_bath_add_yn  = \''.$_POST[$svc_id.'_bathAddYn'].'\'
				,      m03_stop_reason  = \''.$_POST[$svc_id.'_stopReason'].'\'
				,      m03_overtime     = \''.$_POST[$svc_id.'_overTime'].'\'
				,      m03_sgbn         = \''.$_POST['addPay1'].'\'
				,      m03_add_pay_gbn  = \''.$val['addPay2'].'\'
				,      m03_add_time1    = \''.$_POST['sidoTime'].'\'
				,      m03_add_time2    = \''.$_POST['jachTime'].'\'
				,      m03_client_no    = \''.$_POST['client_no'].'\'
				,      m03_memo         = \''.addslashes($_POST['memo']).'\'
				,      m03_bipay1       = \''.str_replace(',', '', $_POST[$svc_id.'_bipay1']).'\'
				,      m03_bipay2       = \''.str_replace(',', '', $_POST[$svc_id.'_bipay2']).'\'
				,      m03_bipay3       = \''.str_replace(',', '', $_POST[$svc_id.'_bipay3']).'\'
				,      m03_expense_yn   = \''.$_POST[$svc_id.'_expense_yn'].'\'
				,      m03_expense_pay  = \''.str_replace(',', '', $_POST[$svc_id.'_expense_pay']).'\'
				,      m03_sdate		= \''.str_replace('-', '', $_POST[$svc_id.'_startDt']).'\'
				,      m03_edate		= \'99991231\'
				,      m03_del_yn       = \''.$del_flag.'\'
				 where m03_ccode		= \''.$code.'\'
				   and m03_mkind		= \''.$kind.'\'
				   and m03_jumin		= \''.$jumin.'\'';

		return $sql;
	}*/

	//히스토리 저장
	function history_save($conn, $code, $kind, $jumin, $start_dt, $end_yn = 'N'){
		if ($end_yn == 'N'){
			$end_dt = 'replace(date_add(date_format(\''.$start_dt.'\', \'%Y%m%d\'), interval -1 day), \'-\', \'\')';
		}else{
			$end_dt = str_replace('-','',$start_dt);
		}

		$sql = "insert into m31sugupja (m31_ccode, m31_mkind, m31_jumin, m31_sdate, m31_edate, m31_level, m31_kind, m31_bonin_yul, m31_kupyeo_max, m31_kupyeo_1, m31_kupyeo_2, m31_status, m31_gaeyak_fm, m31_gaeyak_to, m31_vlvl, m31_sgbn, m31_stop_reason, m31_overtime, m31_add_time1,m31_add_time2,m31_add_pay_gbn)
				select m03_ccode, m03_mkind, m03_jumin, m03_sdate, $end_dt, m03_ylvl, m03_skind, m03_bonin_yul, m03_kupyeo_max, m03_kupyeo_1, m03_kupyeo_2, m03_sugup_status, m03_gaeyak_fm, m03_gaeyak_to, m03_vlvl, m03_sgbn, m03_stop_reason, m03_overtime, m03_add_time1,m03_add_time2,m03_add_pay_gbn
				  from m03sugupja
				 where m03_ccode = '$code'
				   and m03_mkind = '$kind'
				   and m03_jumin = '$jumin'";
		if (!$conn->execute($sql)){
			return false;
		}

		return true;
	}

	/******************************************************************

		수급상태가 계약해지, 사망, 타기관이전인 경우 일정상태 변경

	******************************************************************/
		function client_iljung_del($conn, $code, $kind, $jumin, $end_dt){
			$sql = "update t01iljung
					   set t01_del_yn     = 'Y'
					 where t01_ccode      = '$code'
					   and t01_mkind      = '$kind'
					   and t01_jumin      = '$jumin'
					   and t01_sugup_date > '$end_dt'
					   and t01_del_yn     = 'N'";

			return $conn->execute($sql);
		}
	/******************************************************************/

	echo '<script>';
	echo 'alert(\''.$myF->message('ok','N').'\');';

	if ($conn->mode == 1)
		echo 'location.replace(\'client_new.php?code='.$code.'&kind='.$kind.'&jumin='.$ed->en($jumin).'&page='.$page.'&current_menu='.$current_menu.'&record_menu='.$record_menu.'\');';

	echo '</script>';
?>