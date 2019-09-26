<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$seq   = $_POST['seq'];
	$type  = $_POST['type'];
	$mode  = $_POST['mode'];

	parse_str($_POST['para'], $para);

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);


	if ($mode == 1){
		/*********************************************************

			일정존재여부

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		$sql = 'select count(*)
				  from t01iljung
				 where t01_ccode       = \''.$code.'\'
				   and t01_mkind       = \''.$svcCd.'\'
				   and t01_jumin       = \''.$jumin.'\'
				   and t01_sugup_date >= \''.$fromDt.'\'
				   and t01_sugup_date <= \''.$toDt.'\'
				   and t01_del_yn      = \'N\'';

		$cnt = $conn->get_data($sql);

		$conn->close();
		echo $cnt;
		exit;

	}else if ($mode == 2){
		/*********************************************************

			계약내역 삭제

		*********************************************************/
		if ($type == 1){
			$sql = 'delete
					  from client_his_svc
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and svc_cd = \''.$svcCd.'\'
					   and seq    = \''.$seq.'\'';
		}else if ($type == 2 || $type == 6 || $type == 11){
			
				/*********************************************************
					장기요양보험 이력 삭제
				*********************************************************/
				$sql = 'delete
						  from client_his_lvl
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd = \''.$svcCd.'\'
						   and seq    = \''.$seq.'\'';
				
				if($svcCd == '4'){
					if($svcVal != ''){
						$sql .= 'and app_no = \''.$svcVal.'\'';
					}
				}

		}else if ($type == 3){
			$sql = 'delete
					  from client_his_kind
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and seq    = \''.$seq.'\'';
		}else if ($type == 4){
			$sql = 'delete
					  from client_his_limit
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and seq    = \''.$seq.'\'';
		}else if ($type == 5){
			$sql = 'delete
					  from client_his_nurse
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and seq    = \''.$seq.'\'';
		}else if ($type == 7){
			$sql = 'delete
					  from client_his_old
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and seq    = \''.$seq.'\'';
		}else if ($type == 8){
			$sql = 'delete
					  from client_his_baby
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and seq    = \''.$seq.'\'';
		}else if ($type == 9){
			$sql = 'delete
					  from client_his_dis
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and seq    = \''.$seq.'\'';
		}else if ($type == 10){
			$sql = 'delete
					  from client_his_dis
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and seq    = \''.$seq.'\'';
		}else{
			$conn->close();
			exit;
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
	}else if ($mode == 3){
		/*********************************************************

			계약기간내의 일정여부 확인

		*********************************************************/
		$fromDt = str_replace('-','',$para['from']);
		$toDt   = str_replace('-','',$para['to']);

		$sql = 'select from_dt
				,      to_dt
				  from client_his_svc
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				 order by seq desc
				 limit 1';

		$tmp = $conn->get_array($sql);

		$lastFrom = str_replace('-','',$tmp['from_dt']);
		$lastTo   = str_replace('-','',$tmp['to_dt']);

		unset($tmp);

		if ($fromDt < $lastFrom){
			$conn->close();
			echo 4;
			exit;
		}

		$sql = 'select count(*)
				  from t01iljung
				 where t01_ccode      = \''.$code.'\'
				   and t01_mkind      = \''.$svcCd.'\'
				   and t01_jumin      = \''.$jumin.'\'
				   and t01_sugup_date < \''.$fromDt.'\'
				   and t01_sugup_date > \''.$lastTo.'\'
				   and t01_del_yn     = \'N\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		$sql = 'select count(*)
				  from t01iljung
				 where t01_ccode      = \''.$code.'\'
				   and t01_mkind      = \''.$svcCd.'\'
				   and t01_jumin      = \''.$jumin.'\'
				   and t01_sugup_date > \''.$toDt.'\'
				   and t01_del_yn     = \'N\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 4){
		if ($seq > 0){
			$conn->close();
			echo 1;
			exit;
		}

		/*********************************************************

			장기요양 인정기간

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		$sql = 'select count(*)
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and to_dt  > \''.$fromDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		$sql = 'select count(*)
				  from client_his_lvl
				 where org_no  = \''.$code.'\'
				   and jumin   = \''.$jumin.'\'
				   and svc_cd  = \''.$svcCd.'\'
				   and from_dt > \''.$toDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 5){
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		if (empty($fromDt)) $fromDt = date('Y-m-d');
		if (empty($toDt)) $toDt = date('Y-m-d');

		$dt = $myF->_getDt($fromDt,$toDt);

		/*********************************************************
			수급자의 현재 등급 및 한도금액
		*********************************************************/
		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      level
				  from client_his_lvl
				 where org_no   = \''.$code.'\'
				   and jumin    = \''.$jumin.'\'
				   and svc_cd   = \''.$svcCd.'\'
				   and from_dt <= \''.$dt.'\'
				   and to_dt   >= \''.$dt.'\'';

		$row    = $conn->get_array($sql);
		$seq    = $row['seq'];
		$lvl    = $row['level'];
		$fromDt = $row['from_dt'];
		$toDt   = $row['to_dt'];

		unset($row);

		$sql = 'select m91_code as cd
				,      m91_kupyeo as pay
				,      m91_sdate as f_dt
				,      m91_edate as t_dt
				  from m91maxkupyeo';

		$arrLimitPay = $conn->_fetch_array($sql);

		$dt = $myF->_getDt($fromDt,$toDt);
		$dt = str_replace('-','',$dt);

		foreach($arrLimitPay as $pay){
			if ($pay['cd'] == $lvl && $pay['f_dt'] <= $dt && $pay['t_dt'] >= $dt){
				$limit = $pay['pay'];
				break;
			}
		}

		echo 'seq='.$seq.'&level='.$lvl.'&limit='.$limit;

	}else if ($mode == 6){
		if ($seq > 0){
			$conn->close();
			echo 1;
			exit;
		}

		/*********************************************************

			장기요양 인정기간

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		$sql = 'select count(*)
				  from client_his_kind
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and to_dt  > \''.$fromDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		$sql = 'select count(*)
				  from client_his_kind
				 where org_no  = \''.$code.'\'
				   and jumin   = \''.$jumin.'\'
				   and from_dt > \''.$toDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 7){
		/*********************************************************

			수급자구분

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		if (empty($fromDt)) $fromDt = date('Y-m-d');
		if (empty($toDt)) $toDt = date('Y-m-d');

		$dt = $myF->_getDt($fromDt,$toDt);

		$sql = 'select kind
				,      rate
				  from client_his_kind
				 where org_no   = \''.$code.'\'
				   and jumin    = \''.$jumin.'\'
				   and from_dt <= \''.$dt.'\'
				   and to_dt   >= \''.$dt.'\'';

		$row = $conn->get_array($sql);

		echo 'kind='.$row['kind'].'&rate='.$row['rate'];

	}else if ($mode == 8){
		/*********************************************************

			청구한도이력 인정기간

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);
		$amt1   = intval($para['amt1']);
		$amt2   = intval($para['amt2']);

		if ($seq > 0){
			//일정등록여부
			if ($amt1 < $amt2){
				$sql = 'select count(*)
						  from t01iljung
						 where t01_ccode       = \''.$code.'\'
						   and t01_mkind       = \'0\'
						   and t01_jumin       = \''.$jumin.'\'
						   and t01_sugup_date >= \''.str_replace('-','',$fromDt).'\'
						   and t01_sugup_date <= \''.str_replace('-','',$toDt).'\'
						   and t01_del_yn      = \'N\'';

				$cnt = $conn->get_data($sql);

				if ($cnt > 0){
					$conn->close();
					echo 4;
					exit;
				}
			}

			$conn->close();
			echo 1;
			exit;
		}

		//이전의 일자 중복여부
		$sql = 'select count(*)
				  from client_his_limit
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and to_dt  > \''.$fromDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		//이후의 일자 중복여부
		$sql = 'select count(*)
				  from client_his_limit
				 where org_no  = \''.$code.'\'
				   and jumin   = \''.$jumin.'\'
				   and from_dt > \''.$toDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 9){
		$suga   = $_POST['suga'];
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);
		$dt     = $myF->_getDt($fromDt,$toDt);
		$filed  = 'service_cost';

		if (!Empty($para['stnd'])){
			$dt = $para['stnd'];
		}

		if ($svcCd == '4')
			$filed = 'service_conf_amt, service_conf_time';

		//가사간병 서비스 단가
		$sql = 'select '.$filed.'
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_kind = \''.$svcCd.'\'
				   and service_code = \''.$suga.'\'
				   and date_format(service_from_dt, \'%Y-%m-%d\') <= \''.$dt.'\'
				   and date_format(service_to_dt  , \'%Y-%m-%d\') >= \''.$dt.'\'';

		if ($svcCd == '4'){
			$row = $conn->get_array($sql);

			echo 'amt='.$row[0].'&time='.$row[1];
		}else{
			echo $conn->get_data($sql);
		}

	}else if ($mode == 10){
		if ($seq > 0){
			$conn->close();
			echo 1;
			exit;
		}

		/*********************************************************

			가사간병 인정기간

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		$sql = 'select count(*)
				  from client_his_nurse
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and to_dt  > \''.$fromDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		$sql = 'select count(*)
				  from client_his_nurse
				 where org_no  = \''.$code.'\'
				   and jumin   = \''.$jumin.'\'
				   and from_dt > \''.$toDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 11){
		/*********************************************************

			가사간병 본인부담금

		*********************************************************/
		$suga   = $_POST['suga'];
		$val    = $_POST['val'];
		$lvl    = $_POST['lvl'];
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);
		$dt     = $myF->_getDt($fromDt,$toDt);

		if ($lvl >= '1' && $lvl <= '6'){
			$sql = 'select person_amt'.$lvl.'
					  from suga_person
					 where org_no              = \'goodeos\'
					   and left(person_code,3) = \''.$suga.'\'
					   and person_id           = \''.$val.'\'
					   and person_from_dt     <= \''.$dt.'\'
					   and person_to_dt       >= \''.$dt.'\'';
			$amt = intval($conn->get_data($sql));
		}else{
			$amt = -1;
		}

		echo $amt;

	}else if ($mode == 12){
		if ($seq > 0){
			$conn->close();
			echo 1;
			exit;
		}

		/*********************************************************

			 소득등급 인정기간

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		$sql = 'select count(*)
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and to_dt  > \''.$fromDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		$sql = 'select count(*)
				  from client_his_lvl
				 where org_no  = \''.$code.'\'
				   and jumin   = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and from_dt > \''.$toDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 13){
		if ($seq > 0){
			$conn->close();
			echo 1;
			exit;
		}

		/*********************************************************

			노인돌봄 인정기간

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		$sql = 'select count(*)
				  from client_his_old
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and to_dt  > \''.$fromDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		$sql = 'select count(*)
				  from client_his_old
				 where org_no  = \''.$code.'\'
				   and jumin   = \''.$jumin.'\'
				   and from_dt > \''.$toDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 14){
		if ($seq > 0){
			$conn->close();
			echo 1;
			exit;
		}

		/*********************************************************

			산모신생아 인정기간

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		$sql = 'select count(*)
				  from client_his_baby
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and to_dt  > \''.$fromDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		$sql = 'select count(*)
				  from client_his_baby
				 where org_no  = \''.$code.'\'
				   and jumin   = \''.$jumin.'\'
				   and from_dt > \''.$toDt.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 15){
		/*
		if ($seq > 0){
			$conn->close();
			echo 1;
			exit;
		}
		*/
		

		/*********************************************************

			장애인활동지원 인정기간

		*********************************************************/
		$fromDt = str_replace('.','',$para['from']);
		$toDt   = str_replace('.','',$para['to']);

		$sql = 'select count(*)
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and to_dt  between \''.$fromDt.'\' and \''.$toDt.'\'
				   and seq    != \''.$seq.'\'';
		
		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 2;
			exit;
		}

		$sql = 'select count(*)
				  from client_his_dis
				 where org_no  = \''.$code.'\'
				   and jumin   = \''.$jumin.'\'
				   and from_dt  between \''.$fromDt.'\' and \''.$toDt.'\'
				   and seq    != \''.$seq.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo 3;
			exit;
		}

		echo 1;

	}else if ($mode == 99){
		$lvl  = $_POST['lvl'];
		$date = date('Y-m-d');

		$sql = 'select lvl_rate as rate
				,      lvl_pay as pay
				  from income_lvl_self_pay
				 where lvl_kind     = \''.$svcCd.'\'
				   and lvl_gbn      = \'2\'
				   and lvl_id       = \''.$lvl.'\'
				   and lvl_from_dt <= \''.$date.'\'
				   and lvl_to_dt   >= \''.$date.'\'';

		$row = $conn->get_array($sql);

		if ($row['pay'] > 0){
			echo $row['pay'];
		}else{
			echo $row['rate'];
		}
	}


	include_once('../inc/_db_close.php');
?>