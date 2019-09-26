<?
	exit;
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	//담당자
	$sql = 'SELECT	DISTINCT m00_mcode AS org_no, b00_code AS c_cd, b02_branch AS b_cd, b02_person AS p_cd, m00_domain AS domain
			FROM	m00center
			INNER	JOIN	b00branch
					ON		b00_domain = m00_domain
					AND		b00_com_yn = \'Y\'
			INNER	JOIN	b02center
					ON		b02_center = m00_mcode';

	$P = $conn->_fetch_array($sql, 'org_no');


	$sql = 'SELECT	DISTINCT b02_center AS org_no
			,		b02_homecare AS homecare_yn
			,		MID(b02_voucher,1,1) AS nurse_yn
			,		MID(b02_voucher,2,1) AS old_yn
			,		MID(b02_voucher,3,1) AS baby_yn
			,		MID(b02_voucher,4,1) AS dis_yn
			,		a.care_support AS s_yn
			,		a.care_resource AS r_yn
			,		a.care_area AS area_cd, a.care_group AS group_cd
			,		REPLACE(a.from_dt,\'-\',\'\') AS from_dt
			,		REPLACE(a.to_dt,\'-\',\'\') AS to_dt
			,		basic_cost, client_cost, client_cnt
			FROM	b02center AS a
			LEFT	JOIN cv_reg_info AS b
					ON   b.org_no = b02_center
			WHERE	b.org_no	IS NULL
			AND		a.from_dt	<= DATE_FORMAT(NOW(), \'%Y-%m-%d\')
			AND		a.to_dt		>= DATE_FORMAT(NOW(), \'%Y-%m-%d\')
			AND		b02_center	!= \'T_31150000001\'
			AND		b02_center	!= \'test\'
			AND		b02_center	!= \'klcf1\'
			AND		b02_center	!= \'12345\'
			AND		b02_center	!= \'A1234\'
			AND		b02_center	!= \'123\'
			AND		b02_center	!= \'dasom-001\'
			AND		b02_center	!= \'drcre\'
			AND		b02_center	!= \'geecare\'
			AND		b02_center	!= \'test-001\'
			AND		b02_center	!= \'carevisit\'
			AND		INSTR(CONCAT(b02_homecare, b02_voucher, care_support, care_resource), \'Y\') > 0';

	$C = $conn->_fetch_array($sql);

	if (is_array($C)){
		foreach($C as $tmpI => $R){
			//기관정보
			$sql = 'SELECT	m00_start_date, m00_cont_date, m00_ccode
					FROM	m00center
					WHERE	m00_mcode = \''.$R['org_no'].'\'
					ORDER	BY m00_mkind
					LIMIT	1';

			$row = $conn->get_array($sql);

			$startDt = str_replace('-','',$row['m00_start_date']);
			$contDt	 = str_replace('-','',$row['m00_cont_date']);
			$bizno	 = $row['m00_ccode'];

			Unset($row);

			if ($P[$R['org_no']]['domain'] == 'dolvoin.net'){
				$contCom = '1'; //굿이오스
			}else if ($P[$R['org_no']]['domain'] == 'dwcare.com'){
				$contCom = '3'; //케어비지트
				$contDt = '20150501';
			}else{
				$contCom = '';
			}

			//기관 등록정보
			$sql = 'INSERT INTO cv_reg_info (org_no,link_company,link_branch,link_person,start_dt,cont_dt,from_dt,to_dt,bizno,rs_cd,rs_dtl_cd,area_cd,group_cd,cont_com,insert_dt) VALUES (
					 \''.$R['org_no'].'\'
					,\''.$P[$R['org_no']]['c_cd'].'\'
					,\''.$P[$R['org_no']]['b_cd'].'\'
					,\''.$P[$R['org_no']]['p_cd'].'\'
					,\''.$startDt.'\'
					,\''.$contDt.'\'
					,\''.$R['from_dt'].'\'
					,\''.$R['to_dt'].'\'
					,\''.$bizno.'\'
					,\'1\'
					,\'01\'
					,\''.$R['area_cd'].'\'
					,\''.$R['group_cd'].'\'
					,\''.$contCom.'\'
					,NOW()
					)';

			$query[] = $sql;

			$seq = 0;

			//재가요양
			if ($R['homecare_yn'] == 'Y'){
				$svcGbn = '1';
				$svcCd = '11';
				$seq ++;

				$sql = 'INSERT INTO cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,insert_dt) VALUES (
						 \''.$R['org_no'].'\'
						,\''.$svcGbn.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$R['from_dt'].'\'
						,\''.$R['to_dt'].'\'
						,\'Y\'
						,\'1\'
						,\''.$R['basic_cost'].'\'
						,\'500\'
						,\'30\'
						,NOW()
						)';
				$query[] = $sql;
			}

			//가사간병
			if ($R['nurse_yn'] == 'Y'){
				$svcGbn = '1';
				$svcCd = '21';
				$seq ++;

				$sql = 'INSERT INTO cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,insert_dt) VALUES (
						 \''.$R['org_no'].'\'
						,\''.$svcGbn.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$R['from_dt'].'\'
						,\''.$R['to_dt'].'\'
						,\'Y\'
						,\'2\'
						,\'10000\'
						,\'500\'
						,\'30\'
						,NOW()
						)';
				$query[] = $sql;
			}

			//노인돌봄
			if ($R['old_yn'] == 'Y'){
				$svcGbn = '1';
				$svcCd = '22';
				$seq ++;

				$sql = 'INSERT INTO cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,insert_dt) VALUES (
						 \''.$R['org_no'].'\'
						,\''.$svcGbn.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$R['from_dt'].'\'
						,\''.$R['to_dt'].'\'
						,\'Y\'
						,\'2\'
						,\'10000\'
						,\'500\'
						,\'30\'
						,NOW()
						)';
				$query[] = $sql;
			}

			//산모신생아
			if ($R['baby_yn'] == 'Y'){
				$svcGbn = '1';
				$svcCd = '23';
				$seq ++;

				$sql = 'INSERT INTO cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,insert_dt) VALUES (
						 \''.$R['org_no'].'\'
						,\''.$svcGbn.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$R['from_dt'].'\'
						,\''.$R['to_dt'].'\'
						,\'Y\'
						,\'2\'
						,\'10000\'
						,\'500\'
						,\'30\'
						,NOW()
						)';
				$query[] = $sql;
			}

			//장애인활동지원
			if ($R['dis_yn'] == 'Y'){
				$svcGbn = '1';
				$svcCd = '24';
				$seq ++;

				$sql = 'INSERT INTO cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,insert_dt) VALUES (
						 \''.$R['org_no'].'\'
						,\''.$svcGbn.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$R['from_dt'].'\'
						,\''.$R['to_dt'].'\'
						,\'Y\'
						,\'2\'
						,\'10000\'
						,\'500\'
						,\'30\'
						,NOW()
						)';
				$query[] = $sql;
			}

			//SMS
			$sql = 'SELECT	REPLACE(from_dt, \'-\' , \'\') AS from_dt
					,		REPLACE(to_dt, \'-\' , \'\') AS to_dt
					FROM	sms_acct
					WHERE	org_no = \''.$R['org_no'].'\'
					AND		from_dt <= DATE_FORMAT(NOW(), \'%Y-%m-%d\')
					AND		to_dt >= DATE_FORMAT(NOW(), \'%Y-%m-%d\')';

			$row = $conn->get_array($sql);

			if ($row){
				$svcGbn = '2';
				$svcCd = '21';
				$seq ++;

				$sql = 'INSERT INTO cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,insert_dt) VALUES (
						 \''.$R['org_no'].'\'
						,\''.$svcGbn.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$row['from_dt'].'\'
						,\''.$row['to_dt'].'\'
						,\'Y\'
						,\'1\'
						,\'5000\'
						,\'20\'
						,\'300\'
						,NOW()
						)';
				$query[] = $sql;
			}

			Unset($row);

			//스마트폰
			$sql = 'SELECT	REPLACE(from_dt, \'-\' , \'\') AS from_dt
					,		REPLACE(to_dt, \'-\' , \'\') AS to_dt
					FROM	smart_acct
					WHERE	org_no = \''.$R['org_no'].'\'
					AND		from_dt <= DATE_FORMAT(NOW(), \'%Y-%m-%d\')
					AND		to_dt >= DATE_FORMAT(NOW(), \'%Y-%m-%d\')';

			$row = $conn->get_array($sql);

			if ($row){
				$svcGbn = '2';
				$svcCd = '11';
				$seq ++;

				$sql = 'INSERT INTO cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,insert_dt) VALUES (
						 \''.$R['org_no'].'\'
						,\''.$svcGbn.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$row['from_dt'].'\'
						,\''.$row['to_dt'].'\'
						,\'Y\'
						,\'2\'
						,\'10000\'
						,\'0\'
						,\'0\'
						,NOW()
						)';
				$query[] = $sql;

				$svcCd = '13';
				$seq ++;

				$sql = 'INSERT INTO cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,insert_dt) VALUES (
						 \''.$R['org_no'].'\'
						,\''.$svcGbn.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$row['from_dt'].'\'
						,\''.$row['to_dt'].'\'
						,\'Y\'
						,\'1\'
						,\'1000\'
						,\'0\'
						,\'0\'
						,NOW()
						)';
				$query[] = $sql;
			}

			Unset($row);
		}
	}


	Unset($P);
	Unset($C);

	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 echo $conn->error_msg.'<br>'.$conn->error_query;
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>