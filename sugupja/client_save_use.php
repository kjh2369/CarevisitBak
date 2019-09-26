<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$svcId = $_POST['svcId'];
	
	
	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($code  == '' ||
		$jumin == '' ||
		$svcCd == '' ||
		$svcId == ''){
		$conn->close();
		echo 0;
		exit;
	}

	$sql = 'SELECT COUNT(*)
			  FROM m03sugupja
			 WHERE m03_ccode = \''.$code.'\'
			   AND m03_mkind = \''.$svcCd.'\'
			   AND m03_jumin = \''.$jumin.'\'';

	if ($conn->get_data($sql) == 0){
		$sql = 'SELECT m03_key
				,      m03_name
				,      m03_tel
				,      m03_hp
				,      m03_post_no
				,      m03_juso1
				,      m03_juso2
				  FROM m03sugupja
				 WHERE m03_ccode = \''.$code.'\'
				   AND m03_jumin = \''.$jumin.'\'
				 ORDER BY m03_mkind
				 LIMIT 1';
		$arr = $conn->get_array($sql);

		$sql = 'INSERT INTO m03sugupja (
				 m03_ccode
				,m03_mkind
				,m03_jumin
				,m03_name
				,m03_tel
				,m03_hp
				,m03_post_no
				,m03_juso1
				,m03_juso2
				,m03_key) VALUES (
				 \''.$code.'\'
				,\''.$svcCd.'\'
				,\''.$jumin.'\'
				,\''.$arr['m03_name'].'\'
				,\''.$arr['m03_tel'].'\'
				,\''.$arr['m03_hp'].'\'
				,\''.$arr['m03_post_no'].'\'
				,\''.$arr['m03_juso1'].'\'
				,\''.$arr['m03_juso2'].'\'
				,\''.$arr['m03_key'].'\')';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

		UnSet($arr);
	}

	parse_str($_POST['para'], $val);


	if ($svcCd == '0'){
		$sql = 'update m03sugupja
				   set m03_bipay1       = \''.str_replace(',','',$val['11_bipay1']).'\'
				,      m03_bipay2       = \''.str_replace(',','',$val['11_bipay2']).'\'
				,      m03_bipay3       = \''.str_replace(',','',$val['11_bipay3']).'\'
				,      m03_expense_yn   = \''.$val['11_expense_yn'].'\'
				,      m03_expense_pay  = \''.str_replace(',','',$val['11_expense_pay']).'\'
				,      m03_yoyangsa1    = \''.$ed->de($val['memCd1_'.$svcCd]).'\'
				,      m03_yoyangsa2    = \''.$ed->de($val['memCd2_'.$svcCd]).'\'
				,      m03_yoyangsa1_nm = \''.$val['memNm1_'.$svcCd].'\'
				,      m03_yoyangsa2_nm = \''.$val['memNm2_'.$svcCd].'\'
				,      m03_byungmung    = \''.$val['11_byungMung'].'\'
				,      m03_disease_nm   = \''.$val['11_diseaseNm'].'\'
				,      m03_partner		= \''.$val['11_partner'].'\'
				,      m03_stat_nogood  = \''.$val['11_statNogood'].'\'
				,      m03_bath_add_yn  = \''.$val['11_bathAddYn'].'\'
				 where m03_ccode        = \''.$code.'\'
				   and m03_mkind        = \''.$svcCd.'\'
				   and m03_jumin        = \''.$jumin.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		//가족요양보호사
		// 1.기존데이타 삭제
		$sql = 'delete
				  from client_family
				 where org_no   = \''.$code.'\'
				   and cf_jumin = \''.$jumin.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}


		# 2.작성된 데이타 저장
		$laFamilyList = explode('/',$val['familyList']);
		$liSeq = 1;

		if (is_array($laFamilyList)){
			foreach($laFamilyList as $laList){
				$laFamily = explode(';',$laList);

				if (!empty($laFamily[0])){
					$sql = 'insert into client_family (
							 org_no
							,cf_jumin
							,cf_seq
							,cf_mem_cd
							,cf_mem_nm
							,cf_kind) values (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\''.$liSeq.'\'
							,\''.$ed->de($laFamily[0]).'\'
							,\''.$laFamily[1].'\'
							,\''.$laFamily[2].'\')';

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$liSeq ++;
				}
			}
		}

		/*********************************************************
		 * 옵션 저장
		 *********************************************************/
			$lsLimitYn = ($val['chkOverApp'] == 'Y' ? 'Y' : 'N'); //한도초과 처리 여부
			$lsDayNightYn = ($val['optDANYN'] == 'Y' ? 'Y' : 'N'); //주야간보호 여부
			$contType = $val['optContType']; //계약유형

			$sql = 'SELECT COUNT(*)
					  FROM client_option
					 WHERE org_no = \''.$code.'\'
					   AND jumin  = \''.$jumin.'\'';
			$liCnt = $conn->get_data($sql);

			if ($liCnt > 0){
				$sql = 'UPDATE client_option
						   SET limit_yn = \''.$lsLimitYn.'\'
						,		day_night_yn= \''.$lsDayNightYn.'\'
						,		cont_type	= \''.$contType.'\'
						 WHERE org_no = \''.$code.'\'
						   AND jumin  = \''.$jumin.'\'';
			}else{
				$sql = 'INSERT INTO client_option (
						 org_no
						,jumin
						,limit_yn
						,day_night_yn
						,cont_type) VALUES (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$lsLimitYn.'\'
						,\''.$lsDayNightYn.'\'
						,\''.$contType.'\'
						)';
			}
			$conn->execute($sql);

		$conn->commit();
		$conn->close();
		echo 1;
		exit;
	}else if ($svcCd == '1'){
		$sql = 'update m03sugupja
				   set m03_bipay1      = \''.str_replace(',','',$val['21_bipay1']).'\'
				,      m03_bipay2      = \''.str_replace(',','',$val['21_bipay2']).'\'
				,      m03_bipay3      = \''.str_replace(',','',$val['21_bipay3']).'\'
				,      m03_expense_yn  = \''.$val['21_expense_yn'].'\'
				,      m03_expense_pay = \''.str_replace(',','',$val['21_expense_pay']).'\'
				,      m03_yoyangsa1    = \''.$ed->de($val['memCd1_'.$svcCd]).'\'
				,      m03_yoyangsa2    = \''.$ed->de($val['memCd2_'.$svcCd]).'\'
				,      m03_yoyangsa1_nm = \''.$val['memNm1_'.$svcCd].'\'
				,      m03_yoyangsa2_nm = \''.$val['memNm2_'.$svcCd].'\'
				 where m03_ccode       = \''.$code.'\'
				   and m03_mkind       = \''.$svcCd.'\'
				   and m03_jumin       = \''.$jumin.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		$conn->close();
		echo 1;
		exit;
	}else if ($svcCd == '2'){
		$sql = 'update m03sugupja
				   set m03_bipay1      = \''.str_replace(',','',$val['22_bipay1']).'\'
				,      m03_bipay2      = \''.str_replace(',','',$val['22_bipay2']).'\'
				,      m03_bipay3      = \''.str_replace(',','',$val['22_bipay3']).'\'
				,      m03_expense_yn  = \''.$val['22_expense_yn'].'\'
				,      m03_expense_pay = \''.str_replace(',','',$val['22_expense_pay']).'\'
				,      m03_yoyangsa1    = \''.$ed->de($val['memCd1_'.$svcCd]).'\'
				,      m03_yoyangsa2    = \''.$ed->de($val['memCd2_'.$svcCd]).'\'
				,      m03_yoyangsa1_nm = \''.$val['memNm1_'.$svcCd].'\'
				,      m03_yoyangsa2_nm = \''.$val['memNm2_'.$svcCd].'\'
				 where m03_ccode       = \''.$code.'\'
				   and m03_mkind       = \''.$svcCd.'\'
				   and m03_jumin       = \''.$jumin.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		$conn->close();
		echo 1;
		exit;
	}else if ($svcCd == '3'){
		$sql = 'update m03sugupja
				   set m03_bipay1      = \''.str_replace(',','',$val['23_bipay1']).'\'
				,      m03_bipay2      = \''.str_replace(',','',$val['23_bipay2']).'\'
				,      m03_bipay3      = \''.str_replace(',','',$val['23_bipay3']).'\'
				,      m03_expense_yn  = \''.$val['23_expense_yn'].'\'
				,      m03_expense_pay = \''.str_replace(',','',$val['23_expense_pay']).'\'
				,      m03_yoyangsa1    = \''.$ed->de($val['memCd1_'.$svcCd]).'\'
				,      m03_yoyangsa2    = \''.$ed->de($val['memCd2_'.$svcCd]).'\'
				,      m03_yoyangsa1_nm = \''.$val['memNm1_'.$svcCd].'\'
				,      m03_yoyangsa2_nm = \''.$val['memNm2_'.$svcCd].'\'
				 where m03_ccode       = \''.$code.'\'
				   and m03_mkind       = \''.$svcCd.'\'
				   and m03_jumin       = \''.$jumin.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
	}else if ($svcCd == '4'){
		$sql = 'update m03sugupja
				   set m03_bipay1      = \''.str_replace(',','',$val['24_bipay1']).'\'
				,      m03_bipay2      = \''.str_replace(',','',$val['24_bipay2']).'\'
				,      m03_bipay3      = \''.str_replace(',','',$val['24_bipay3']).'\'
				,      m03_expense_yn  = \''.$val['24_expense_yn'].'\'
				,      m03_expense_pay = \''.str_replace(',','',$val['24_expense_pay']).'\'
				,      m03_yoyangsa1    = \''.$ed->de($val['memCd1_'.$svcCd]).'\'
				,      m03_yoyangsa2    = \''.$ed->de($val['memCd2_'.$svcCd]).'\'
				,      m03_yoyangsa1_nm = \''.$val['memNm1_'.$svcCd].'\'
				,      m03_yoyangsa2_nm = \''.$val['memNm2_'.$svcCd].'\'
				,      m03_bath_add_yn  = \''.$_POST['24_bathAddYn'].'\'
				,      m03_sgbn         = \''.$_POST['addPay1'].'\'
				,      m03_add_pay_gbn  = \''.$val['addPay2'].'\'
				 where m03_ccode       = \''.$code.'\'
				   and m03_mkind       = \''.$svcCd.'\'
				   and m03_jumin       = \''.$jumin.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		$conn->close();
		echo 1;
		exit;
	}else if ($svcCd == '6'){
		/*
		//재가지원
		$svcSeq = $val['svcSeq_26'];
		$sql = 'SELECT	COUNT(*)
				FROM	client_his_care
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$svcSeq.'\'';

		$liCnt = $conn->get_data($sql);

		$supportCd	= $val['txtResourceCd'];
		$supportNm	= $val['txtResourceNm'];
		$supportNo	= ($val['txtResourceNo'] ? 'L'.$val['txtResourceNo'] : '');
		$supportLvl	= $val['cboResourceLvl'];
		$supportGbn	= $val['cboResourceGbn'];
		$supportPic	= $val['txtResourcePicNm'];
		$supportTel	= Str_Replace('-','',$val['txtResourceTelno']);

		if ($liCnt > 0){
			$sql = 'UPDATE	client_his_care
					SET		care_cost	= \''.$val['svcCost_26'].'\'
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
					,\''.$val['svcCost_26'].'\'
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
		*/

	}else if ($svcCd == 'A' || $svcCd == 'B' || $svcCd == 'C'){
		//기타유료
		$svcSeq   = intval($val['svcSeq_'.$svcId]);
		$svcVal   = $val['svcGbn_'.$svcId];
		$svcCost  = intval(str_replace(',','',$val['svcCost_'.$svcId]));
		$svcCnt   = intval(str_replace(',','',$val['svcCnt_'.$svcId]));
		$recomNm  = $val['recomNm_'.$svcCd];
		$recomTel = str_replace('-','',$val['recomTel_'.$svcCd]);
		$recomAmt = intval(str_replace(',','',$val['recomAmt_'.$svcCd]));

		$sql = 'update m03sugupja
				   set m03_yoyangsa1    = \''.$ed->de($val['memCd1_'.$svcCd]).'\'
				,      m03_yoyangsa2    = \''.$ed->de($val['memCd2_'.$svcCd]).'\'
				,      m03_yoyangsa1_nm = \''.$val['memNm1_'.$svcCd].'\'
				,      m03_yoyangsa2_nm = \''.$val['memNm2_'.$svcCd].'\'
				 where m03_ccode       = \''.$code.'\'
				   and m03_mkind       = \''.$svcCd.'\'
				   and m03_jumin       = \''.$jumin.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

		if ($svcSeq > 0){
			$lbSvcNew = true;
			$sql = 'update client_his_other
					   set svc_val   = \''.$svcVal.'\'
					,      svc_cost  = \''.$svcCost.'\'
					,      svc_cnt   = \''.$svcCnt.'\'
					,      recom_nm  = \''.$recomNm.'\'
					,      recom_tel = \''.$recomTel.'\'
					,      recom_amt = \''.$recomAmt.'\'
					,      update_id = \''.$_SESSION['userCode'].'\'
					,      update_dt = now()
					 where org_no    = \''.$code.'\'
					   and jumin     = \''.$jumin.'\'
					   and svc_cd    = \''.$svcCd.'\'
					   and seq       = \''.$svcSeq.'\'';
		}else{
			$lbSvcNew = false;
			$sql = 'select ifnull(max(seq),0)+1
					  from client_his_other
					 where org_no    = \''.$code.'\'
					   and jumin     = \''.$jumin.'\'
					   and svc_cd    = \''.$svcCd.'\'';
			$svcSeq = $conn->get_data($sql);

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
					,\''.$svcCd.'\'
					,\''.$svcSeq.'\'
					,\''.$svcVal.'\'
					,\''.$svcCost.'\'
					,\''.$svcCnt.'\'
					,\''.$recomNm.'\'
					,\''.$recomTel.'\'
					,\''.$recomAmt.'\'
					,\''.$_SESSION['userCode'].'\'
					,now()
					)';
		}
	}

	//산모 추가요금항목
	if ($svcCd == '3' ||
		$svcCd == 'A'){

		if (!empty($sql)){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
		}

		$sql = 'select ifnull(max(svc_seq), 0)
				  from client_svc_addpay
				 where org_no   = \''.$code.'\'
				   and svc_kind = \''.$svcCd.'\'
				   and svc_ssn  = \''.$jumin.'\'
				   and del_flag = \'N\'';

		$svcSeq = $conn->get_data($sql);

		if ($svcSeq == 0){
			$svcSeq = 1;
			$sql = 'insert into client_svc_addpay (
					 org_no
					,svc_kind
					,svc_ssn
					,svc_seq
					,insert_id
					,insert_dt) values (
					 \''.$code.'\'
					,\''.$svcCd.'\'
					,\''.$jumin.'\'
					,\''.$svcSeq.'\'
					,\''.$_SESSION['userCode'].'\'
					,now())';

			if (!$conn->execute($sql)){
				$conn->rollback();
				echo 9;
				exit;
			}
		}

		$sql = 'update client_svc_addpay
				   set school_not_cnt	= \''.str_replace(',', '', $val['notSchoolCnt_'.$svcCd]).'\'
				,      school_not_pay	= \''.str_replace(',', '', $val['notSchoolPay_'.$svcCd]).'\'
				,      school_cnt		= \''.str_replace(',', '', $val['schoolCnt_'.$svcCd]).'\'
				,      school_pay		= \''.str_replace(',', '', $val['schoolPay_'.$svcCd]).'\'
				,      family_cnt		= \''.str_replace(',', '', $val['familyCnt_'.$svcCd]).'\'
				,      family_pay		= \''.str_replace(',', '', $val['familyPay_'.$svcCd]).'\'
				,      home_in_yn		= \''.$val[$svcId.'_home_in_yn'].'\'
				,      home_in_pay		= \''.str_replace(',', '', $val['homeInPay_'.$svcCd]).'\'
				,      holiday_pay		= \''.str_replace(',', '', $val['holidayPay_'.$svcCd]).'\'';

		if ($lbAddpay){
			$sql .= ', update_id = \''.$_SESSION['userCode'].'\'
					 , update_dt = now()';
		}

		$sql .= ' where org_no	 = \''.$code.'\'
					and svc_kind = \''.$svcCd.'\'
					and svc_ssn  = \''.$jumin.'\'
					and svc_seq  = \''.$svcSeq.'\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo 9;
			exit;
		}
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