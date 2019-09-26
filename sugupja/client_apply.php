<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code   = $_SESSION['userCenterCode']; //$_POST['code'];
	$jumin  = $_POST['jumin'];
	$svcId  = $_POST['svcId'];
	$svcCd  = $_POST['svcCd'];
	$stat   = $_POST['stat'];
	$reason = $_POST['reason'];
	$fromDt = $_POST['fromDt'];
	$toDt   = $_POST['toDt'];
	$seq    = $_POST['seq'];
	$mode   = $_POST['mode'];
	$type   = $_POST['type'];
	$mp = $_POST['mp'];
	
	
	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($type == 1){
		if ($stat == '1')
			$reason = '';

		if (empty($code) || empty($jumin) || $svcCd == ''){
			$conn->close();
			echo 0;
			exit;
		}
	}else{
		if (empty($code) || empty($jumin)){
			$conn->close();
			echo 0;
			exit;
		}
	}

	/*********************************************************

		고객 상태 및 계약기간 적용

	*********************************************************/
	if ($mode == 1 || $mode == 2 || $mode == 9){
		//시작 기간 중복 확인
		$sql = 'select from_dt
				  from client_his_svc
				 where org_no   = \''.$code.'\'
				   and jumin    = \''.$jumin.'\'
				   and from_dt <= \''.$fromDt.'\'
				   and to_dt   >= \''.$fromDt.'\'
				   and svc_cd   = \''.$svcCd.'\'';

		$fromDtCnt = $conn->get_data($sql);

		//종료 기간 중복 확인
		$sql = 'select to_dt
				  from client_his_svc
				 where org_no   = \''.$code.'\'
				   and jumin    = \''.$jumin.'\'
				   and from_dt <= \''.$toDt.'\'
				   and to_dt   >= \''.$toDt.'\'
				   and svc_cd   = \''.$svcCd.'\'';

		$toDtCnt = $conn->get_data($sql);

		if ($mode == 9){
			$conn->close();
			echo 'fromDt='.$fromDtCnt.'&toDt='.$toDtCnt;
			exit;
		}else{
			if (!empty($fromDtCnt)) $fromDtCnt = 1; else $fromDtCnt = 0;
			if (!empty($toDtCnt)) $toDtCnt = 1; else $toDtCnt = 0;
		}
	}


	if ($mode == 1){
		//등록
		if (($fromDtCnt > 0 || $toDtCnt > 0) && $stat == '1'){
			$conn->close();
			echo 101;
			exit;
		}

		$sql = 'select ifnull(max(seq),0)+1
				  from client_his_svc
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'';

		$seq = $conn->get_data($sql);

		$sql = 'insert into client_his_svc (
				 org_no
				,jumin
				,seq
				,from_dt
				,to_dt
				,svc_cd
				,svc_stat
				,svc_reason
				,mp_gbn
				,insert_id
				,insert_dt) values (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$seq.'\'
				,\''.$fromDt.'\'
				,\''.$toDt.'\'
				,\''.$svcCd.'\'
				,\''.$stat.'\'
				,\''.$reason.'\'
				,\''.$mp.'\'
				,\''.$_SESSION['userCode'].'\'
				,now())';

	}else if ($mode == 2){
		if($IsClientInfo){
			
			$lastDT			= $myF->dateAdd('day', -1, $fromDt, 'Ymd');
				
			
			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_svc
					 where org_no   = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'
					   and svc_cd   = \''.$svcCd.'\'';

			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_svc
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and svc_cd		= \''.$svcCd.'\'
					   and from_dt		>= \''.$fromDt.'\'
					 order by from_dt, to_dt
					 limit 1';

			$chkDT = $conn->get_data($sql);
			
			$sql = 'select count(*)
					  from client_his_svc
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and svc_cd		= \''.$svcCd.'\'
					   and from_dt		>= \''.$fromDt.'\'';

			$svcCnt = $conn->get_data($sql);
			
			if($svcCnt >= 2){
				$sql = 'select to_dt
						from (
						select from_dt, to_dt
						  from client_his_svc
						 where org_no		= \''.$code.'\'
						   and jumin		= \''.$jumin.'\'
						   and svc_cd		= \''.$svcCd.'\'
						   and from_dt		>= \''.$fromDt.'\'
						 order by from_dt desc, to_dt desc
						 limit 2
						) as mst
						order by from_dt asc, to_dt asc
						limit 1';

				$scdToDt = $conn->get_data($sql);
			}
			

			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq, to_dt
						  from client_his_svc
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and svc_cd   = \''.$svcCd.'\'
						   and from_dt <= \''.$fromDt.'\'
						 order by from_dt desc, to_dt desc
						 limit 1';
				
				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				
				if($chkFrom >= $fromDt){
					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_svc
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and svc_cd    = \''.$svcCd.'\'
							   and seq    = \''.$chkSeq.'\'';
					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}
				
				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_svc
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd = \''.$svcCd.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_svc (
						 org_no
						,jumin
						,svc_cd
						,seq
						,from_dt
						,to_dt
						,svc_stat
						,svc_reason
						,mp_gbn
						,insert_id
						,insert_dt) values (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$fromDt.'\'
						,\''.$toDt.'\'
						,\''.$stat.'\'
						,\''.$reason.'\'
						,\''.$mp.'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}else {
				
				if($fromDt > $scdToDt){
					//새 순번
					$sql = 'select seq
							  from client_his_svc
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and svc_cd     = \''.$svcCd.'\'
							 order by from_dt desc, to_dt desc
							 limit 1';

					$seq = $conn->get_data($sql);


					$sql = 'update client_his_svc
						   set from_dt    = \''.$fromDt.'\'
						,      to_dt      = \''.$toDt.'\'
						,      svc_stat   = \''.$stat.'\'
						,      svc_reason = \''.$reason.'\'
						,		mp_gbn		= \''.$mp.'\'
						,      update_id  = \''.$_SESSION['userCode'].'\'
						,      update_dt  = now()
						 where org_no     = \''.$code.'\'
						   and jumin      = \''.$jumin.'\'
						   and svc_cd     = \''.$svcCd.'\'
						   and seq        = \''.$seq.'\'';
				}else {
				
					/*********************************************************
						일자중복
					*********************************************************/
					if ($fromDt == $chkDT){
						$conn->begin();

						//새 순번
						$sql = 'select seq
								  from client_his_svc
								 where org_no  = \''.$code.'\'
								   and jumin   = \''.$jumin.'\'
								   and svc_cd     = \''.$svcCd.'\'
								   and from_dt = \''.$fromDt.'\'';

						$seq = $conn->get_data($sql);


						$sql = 'update client_his_svc
							   set from_dt    = \''.$fromDt.'\'
							,      to_dt      = \''.$toDt.'\'
							,      svc_stat   = \''.$stat.'\'
							,      svc_reason = \''.$reason.'\'
							,		mp_gbn		= \''.$mp.'\'
							,      update_id  = \''.$_SESSION['userCode'].'\'
							,      update_dt  = now()
							 where org_no     = \''.$code.'\'
							   and jumin      = \''.$jumin.'\'
							   and svc_cd     = \''.$svcCd.'\'
							   and seq        = \''.$seq.'\'';
					}else {
						$conn->begin();
						
						//새 순번
						$sql = 'select seq
								  from client_his_svc
								 where org_no  = \''.$code.'\'
								   and jumin   = \''.$jumin.'\'
								   and svc_cd     = \''.$svcCd.'\'';

						$seq = $conn->get_data($sql);
						
						$sql = 'select count(*)
								  from client_his_svc
								 where org_no  = \''.$code.'\'
								   and jumin   = \''.$jumin.'\'
								   and svc_cd     = \''.$svcCd.'\'';

						$cnt = $conn->get_data($sql);

						if($cnt==1){
							$sql = 'update client_his_svc
								   set from_dt    = \''.$fromDt.'\'
								,      to_dt      = \''.$toDt.'\'
								,      svc_stat   = \''.$stat.'\'
								,      svc_reason = \''.$reason.'\'
								,		mp_gbn		= \''.$mp.'\'
								,      update_id  = \''.$_SESSION['userCode'].'\'
								,      update_dt  = now()
								 where org_no     = \''.$code.'\'
								   and jumin      = \''.$jumin.'\'
								   and svc_cd     = \''.$svcCd.'\'
								   and seq        = \''.$seq.'\'';
						}
					}
				}
			}
		}else {
		
			//수정
			$sql = 'select max(seq)
					  from client_his_svc
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and svc_cd = \''.$svcCd.'\'';

			$seq = $conn->get_data($sql);

			if ($seq == 0){
				$conn->close();
				echo 0;
				exit;
			}else{
				$sql = 'update client_his_svc
						   set from_dt    = \''.$fromDt.'\'
						,      to_dt      = \''.$toDt.'\'
						,      svc_stat   = \''.$stat.'\'
						,      svc_reason = \''.$reason.'\'
						,		mp_gbn		= \''.$mp.'\'
						,      update_id  = \''.$_SESSION['userCode'].'\'
						,      update_dt  = now()
						 where org_no     = \''.$code.'\'
						   and jumin      = \''.$jumin.'\'
						   and svc_cd     = \''.$svcCd.'\'
						   and seq        = \''.$seq.'\'';
			}
		}
	}else if ($mode == 3){
		$sql = 'select m03_name as nm
				,      m03_post_no as postno
				,      m03_juso1 as addr
				,      m03_juso2 as addr_dtl
				,      m03_tel as phone
				,      m03_hp as mobile
				,      m03_yboho_name as protect_nm
				,      m03_yboho_gwange as protect_rel
				,      m03_yboho_phone as protect_phone
				,      m03_client_no as client_no
				,      m03_memo as memo
				  from m03sugupja
				 where m03_ccode  = \''.$code.'\'
				   and m03_jumin  = \''.$jumin.'\'
				   and m03_del_yn = \'N\'
				 order by m03_mkind
				 limit 1';
	}else if ($mode == 4){
		$sql = 'select t01_mkind as cd
				,      date_format(min(t01_sugup_date),\'%Y-%m-%d\') as min_dt
				,      date_format(max(t01_sugup_date),\'%Y-%m-%d\') as max_dt
				  from t01iljung
				 where t01_ccode  = \''.$code.'\'
				   and t01_jumin  = \''.$jumin.'\'
				   and t01_del_yn = \'N\'
				 group by t01_mkind';

	}else if ($mode == 11){
		if($IsClientInfo){
			
			$lastDT			= $myF->dateAdd('day', -1, $_POST['mgmtFrom'], 'Ymd');
				

			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_lvl
					 where org_no      = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'
					   and svc_cd      = \''.$svcCd.'\'';

			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_lvl
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and svc_cd		= \''.$svcCd.'\'
					   and from_dt		>= \''.$_POST['mgmtFrom'].'\'
					 order by from_dt, to_dt
					 limit 1';

			$chkDT = $conn->get_data($sql);
			
			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq, to_dt
						  from client_his_lvl
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and svc_cd   = \''.$svcCd.'\'
						   and from_dt <= \''.$_POST['mgmtFrom'].'\'
						   /*and to_dt   >= \''.$_POST['mgmtTo'].'\'*/
						 order by from_dt desc, to_dt desc
						 limit 1';
	
				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				
				
				if($chkFrom >= $_POST['mgmtFrom']){
					
					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_lvl
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and svc_cd    = \''.$svcCd.'\'
							   and seq    = \''.$chkSeq.'\'';

					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}

				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_lvl
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd = \''.$svcCd.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_lvl (
						 org_no
						,jumin
						,svc_cd
						,seq
						,from_dt
						,to_dt
						,app_no
						,level
						,insert_id
						,insert_dt) values (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$_POST['mgmtFrom'].'\'
						,\''.$_POST['mgmtTo'].'\'
						,\''.$_POST['mgmtNo'].'\'
						,\''.$_POST['mgmtLvl'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}else {
				/*********************************************************
					일자중복
				*********************************************************/
				if ($_POST['mgmtFrom'] == $chkDT){
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_lvl
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and svc_cd     = \''.$svcCd.'\'
							   and from_dt = \''.$_POST['mgmtFrom'].'\'';

					$seq = $conn->get_data($sql);


					$sql = 'update client_his_lvl
						   set from_dt   = \''.$_POST['mgmtFrom'].'\'
						,      to_dt     = \''.$_POST['mgmtTo'].'\'
						,      app_no    = \''.$_POST['mgmtNo'].'\'
						,      level     = \''.$_POST['mgmtLvl'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and svc_cd    = \''.$svcCd.'\'
						   and seq       = \''.$seq.'\'';
				}else {
					//새 순번
					$sql = 'select seq
							  from client_his_lvl
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and svc_cd  = \''.$svcCd.'\'';
					$seq = $conn->get_data($sql);

					$sql = 'select count(*)
							  from client_his_lvl
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and svc_cd  = \''.$svcCd.'\'';

					$cnt = $conn->get_data($sql);

					if($cnt==1){
						$sql = 'update client_his_lvl
							   set from_dt   = \''.$_POST['mgmtFrom'].'\'
							,      to_dt     = \''.$_POST['mgmtTo'].'\'
							,      app_no    = \''.$_POST['mgmtNo'].'\'
							,      level     = \''.$_POST['mgmtLvl'].'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and svc_cd    = \''.$svcCd.'\'
							   and seq       = \''.$seq.'\'';
					}
				}
			}
		}else {

			if ($seq > 0){
				$sql = 'update client_his_lvl
						   set from_dt   = \''.$_POST['mgmtFrom'].'\'
						,      to_dt     = \''.$_POST['mgmtTo'].'\'
						,      app_no    = \''.$_POST['mgmtNo'].'\'
						,      level     = \''.$_POST['mgmtLvl'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and svc_cd    = \''.$svcCd.'\'
						   and seq       = \''.$seq.'\'';
			}else{
				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_lvl
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd = \''.$svcCd.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_lvl (
						 org_no
						,jumin
						,svc_cd
						,seq
						,from_dt
						,to_dt
						,app_no
						,level
						,insert_id
						,insert_dt) values (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$_POST['mgmtFrom'].'\'
						,\''.$_POST['mgmtTo'].'\'
						,\''.$_POST['mgmtNo'].'\'
						,\''.$_POST['mgmtLvl'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}
		}

	}else if ($mode == 12){
		/*********************************************************

			수급자 구분이력

		*********************************************************/
		if($IsClientInfo){
			
			$lastDT	= $myF->dateAdd('day', -1, $_POST['from'], 'Ymd');
				

			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_kind
					 where org_no      = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'';

			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_kind
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and from_dt		>= \''.$_POST['from'].'\'
					 order by from_dt, to_dt
					 limit 1';

			$chkDT = $conn->get_data($sql);
			
			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq, to_dt
						  from client_his_kind
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and from_dt <= \''.$_POST['from'].'\'
						   /*and to_dt   >= \''.$_POST['to'].'\'*/
						 order by from_dt desc, to_dt desc
						 limit 1';

				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				
				if($chkFrom >= $_POST['from']){

					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_kind
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and seq    = \''.$chkSeq.'\'';

					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}

				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_kind
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['kind'].'\'
						,\''.$_POST['rate'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}else {
				/*********************************************************
					일자중복
				*********************************************************/
				if ($_POST['from'] == $chkDT){
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_kind
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and from_dt = \''.$_POST['from'].'\'';

					$seq = $conn->get_data($sql);


					$sql = 'update client_his_kind
						   set from_dt   = \''.$_POST['from'].'\'
						,      to_dt     = \''.$_POST['to'].'\'
						,      kind      = \''.$_POST['kind'].'\'
						,      rate      = \''.$_POST['rate'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$seq.'\'';
				}else {
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_kind
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);
					
					$sql = 'select count(*)
							  from client_his_kind
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';

					$cnt = $conn->get_data($sql);
					
					
					if($cnt==1){	
						$sql = 'update client_his_kind
							   set from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      kind      = \''.$_POST['kind'].'\'
							,      rate      = \''.$_POST['rate'].'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and seq       = \''.$seq.'\'';
					}
					
				}
			}
		}else {

			if ($seq > 0){
				$sql = 'update client_his_kind
						   set from_dt   = \''.$_POST['from'].'\'
						,      to_dt     = \''.$_POST['to'].'\'
						,      kind      = \''.$_POST['kind'].'\'
						,      rate      = \''.$_POST['rate'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$seq.'\'';
			}else{
				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_kind
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['kind'].'\'
						,\''.$_POST['rate'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}
		}

	}else if ($mode == 13){
		/*********************************************************

			청구한도이력

		*********************************************************/
		if($IsClientInfo){
			
			$lastDT	= $myF->dateAdd('day', -1, $_POST['from'], 'Ymd');
				

			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_limit
					 where org_no      = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'';

			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_limit
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and from_dt		>= \''.$_POST['from'].'\'
					 order by from_dt, to_dt
					 limit 1';

			$chkDT = $conn->get_data($sql);
			
			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq
						  from client_his_limit
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and from_dt <= \''.$_POST['from'].'\'
						   /*and to_dt   >= \''.$_POST['to'].'\'*/
						 order by from_dt desc, to_dt desc
						 limit 1';

				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				
				if($chkFrom >= $_POST['from']){

					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_limit
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and seq    = \''.$chkSeq.'\'';
				

					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}

				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_limit
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);
				
				if ($lbLimitSet){
					$sql = 'INSERT INTO client_his_limit (
							 org_no
							,jumin
							,seq
							,from_dt
							,to_dt
							,amt_care
							,amt_bath
							,amt_nurse
							,amt
							,insert_id
							,insert_dt) VALUES (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$_POST['from'].'\'
							,\''.$_POST['to'].'\'
							,\''.$_POST['amtCare'].'\'
							,\''.$_POST['amtBath'].'\'
							,\''.$_POST['amtNurse'].'\'
							,\''.($_POST['amtCare']+$_POST['amtBath']+$_POST['amtNurse']).'\'
							,\''.$_SESSION['userCode'].'\'
							,now())';
				}else {
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
							,\''.$seq.'\'
							,\''.$_POST['from'].'\'
							,\''.$_POST['to'].'\'
							,\''.$_POST['amt'].'\'
							,\''.$_SESSION['userCode'].'\'
							,now())';
				}
			}else {
				/*********************************************************
					일자중복
				*********************************************************/
				if ($_POST['from'] == $chkDT){
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_limit
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and from_dt = \''.$_POST['from'].'\'';

					$seq = $conn->get_data($sql);

					if ($lbLimitSet){
						$sql = 'UPDATE client_his_limit
								   SET from_dt   = \''.$_POST['from'].'\'
								,      to_dt     = \''.$_POST['to'].'\'
								,      amt_care  = \''.$_POST['amtCare'].'\'
								,      amt_bath  = \''.$_POST['amtBath'].'\'
								,      amt_nurse = \''.$_POST['amtNurse'].'\'
								,      amt       = \''.($_POST['amtCare']+$_POST['amtBath']+$_POST['amtNurse']).'\'
								,      update_id = \''.$_SESSION['userCode'].'\'
								,      update_dt = now()
								 WHERE org_no    = \''.$code.'\'
								   AND jumin     = \''.$jumin.'\'
								   AND seq       = \''.$seq.'\'';
					}else {
						$sql = 'update client_his_limit
							   set from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      amt       = \''.$_POST['amt'].'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and seq       = \''.$seq.'\'';
					}
				}else {
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_limit
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);
					
					$sql = 'select count(*)
							  from client_his_limit
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';

					$cnt = $conn->get_data($sql);
					
					if($cnt == 1){
						if ($lbLimitSet){
							$sql = 'UPDATE client_his_limit
									   SET from_dt   = \''.$_POST['from'].'\'
									,      to_dt     = \''.$_POST['to'].'\'
									,      amt_care  = \''.$_POST['amtCare'].'\'
									,      amt_bath  = \''.$_POST['amtBath'].'\'
									,      amt_nurse = \''.$_POST['amtNurse'].'\'
									,      amt       = \''.($_POST['amtCare']+$_POST['amtBath']+$_POST['amtNurse']).'\'
									,      update_id = \''.$_SESSION['userCode'].'\'
									,      update_dt = now()
									 WHERE org_no    = \''.$code.'\'
									   AND jumin     = \''.$jumin.'\'
									   AND seq       = \''.$seq.'\'';
						}else {
							$sql = 'update client_his_limit
								   set from_dt   = \''.$_POST['from'].'\'
								,      to_dt     = \''.$_POST['to'].'\'
								,      amt       = \''.$_POST['amt'].'\'
								,      update_id = \''.$_SESSION['userCode'].'\'
								,      update_dt = now()
								 where org_no    = \''.$code.'\'
								   and jumin     = \''.$jumin.'\'
								   and seq       = \''.$seq.'\'';
						}
					}
				}
			}
		}else {
		
			if ($lbLimitSet){
				if ($seq > 0){
					$sql = 'UPDATE client_his_limit
							   SET from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      amt_care  = \''.$_POST['amtCare'].'\'
							,      amt_bath  = \''.$_POST['amtBath'].'\'
							,      amt_nurse = \''.$_POST['amtNurse'].'\'
							,      amt       = \''.($_POST['amtCare']+$_POST['amtBath']+$_POST['amtNurse']).'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 WHERE org_no    = \''.$code.'\'
							   AND jumin     = \''.$jumin.'\'
							   AND seq       = \''.$seq.'\'';
				}else{
					$sql = 'SELECT IFNULL(MAX(seq),0)+1
							  FROM client_his_limit
							 WHERE org_no = \''.$code.'\'
							   AND jumin  = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'INSERT INTO client_his_limit (
							 org_no
							,jumin
							,seq
							,from_dt
							,to_dt
							,amt_care
							,amt_bath
							,amt_nurse
							,amt
							,insert_id
							,insert_dt) VALUES (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$_POST['from'].'\'
							,\''.$_POST['to'].'\'
							,\''.$_POST['amtCare'].'\'
							,\''.$_POST['amtBath'].'\'
							,\''.$_POST['amtNurse'].'\'
							,\''.($_POST['amtCare']+$_POST['amtBath']+$_POST['amtNurse']).'\'
							,\''.$_SESSION['userCode'].'\'
							,now())';
				}
			}else{
				if ($seq > 0){
					$sql = 'update client_his_limit
							   set from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      amt       = \''.$_POST['amt'].'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and seq       = \''.$seq.'\'';
				}else{
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_limit
							 where org_no = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);

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
							,\''.$seq.'\'
							,\''.$_POST['from'].'\'
							,\''.$_POST['to'].'\'
							,\''.$_POST['amt'].'\'
							,\''.$_SESSION['userCode'].'\'
							,now())';
				}
			}
		}

	}else if ($mode == 14){
		/*********************************************************

			가사간병

		*********************************************************/
		if($IsClientInfo){
			
			$lastDT	= $myF->dateAdd('day', -1, $_POST['from'], 'Ymd');
				

			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_nurse
					 where org_no      = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'';

			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_nurse
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and from_dt		>= \''.$_POST['from'].'\'
					 order by from_dt, to_dt
					 limit 1';

			$chkDT = $conn->get_data($sql);
			
			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq
						  from client_his_nurse
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and from_dt <= \''.$_POST['from'].'\'
						   /*and to_dt   >= \''.$_POST['to'].'\'*/
						 order by from_dt desc, to_dt desc
						 limit 1';

				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				
				if($chkFrom >= $_POST['from']){
					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_nurse
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and seq    = \''.$chkSeq.'\'';

					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}

				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_nurse
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_nurse (
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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['val'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}else {
				/*********************************************************
					일자중복
				*********************************************************/
				if ($_POST['from'] == $chkDT){
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_nurse
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and from_dt = \''.$_POST['from'].'\'';

					$seq = $conn->get_data($sql);


					$sql = 'update client_his_nurse
						   set from_dt   = \''.$_POST['from'].'\'
						,      to_dt     = \''.$_POST['to'].'\'
						,      svc_val   = \''.$_POST['val'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$seq.'\'';
				}else {

					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_nurse
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';
					$seq = $conn->get_data($sql);
					
					$sql = 'select count(*)
							  from client_his_nurse
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';
					$cnt = $conn->get_data($sql);

					if($cnt==1){
						$sql = 'update client_his_nurse
							   set from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      svc_val   = \''.$_POST['val'].'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and seq       = \''.$seq.'\'';
					}
				}
			}
		}else {
			if ($seq > 0){
				$sql = 'update client_his_nurse
						   set from_dt   = \''.$_POST['from'].'\'
						,      to_dt     = \''.$_POST['to'].'\'
						,      svc_val   = \''.$_POST['val'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$seq.'\'';
			}else{
				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_nurse
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_nurse (
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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['val'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}
		}

	}else if ($mode == 16){
		/*********************************************************

			노인돌봄이력

		*********************************************************/
		if($IsClientInfo){
			
			$lastDT	= $myF->dateAdd('day', -1, $_POST['from'], 'Ymd');
				

			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_old
					 where org_no      = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'';

			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_old
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and from_dt		>= \''.$_POST['from'].'\'
					 order by from_dt, to_dt
					 limit 1';

			$chkDT = $conn->get_data($sql);
			
			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq
						  from client_his_old
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and from_dt <= \''.$_POST['from'].'\'
						   /*and to_dt   >= \''.$_POST['to'].'\'*/
						 order by from_dt desc, to_dt desc
						 limit 1';

				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				
				if($chkFrom >= $_POST['from']){
					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_old
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and seq    = \''.$chkSeq.'\'';

					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}

				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_old
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_old (
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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['val'].'\'
						,\''.$_POST['time'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}else {
				/*********************************************************
					일자중복
				*********************************************************/
				if ($_POST['from'] == $chkDT){
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_old
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and from_dt = \''.$_POST['from'].'\'';

					$seq = $conn->get_data($sql);


					$sql = 'update client_his_old
							   set from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      svc_val   = \''.$_POST['val'].'\'
							,      svc_tm    = \''.$_POST['time'].'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and seq       = \''.$seq.'\'';
				}else {
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_old
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'select count(*)
							  from client_his_old
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';

					$cnt = $conn->get_data($sql);
					
					if($cnt==1){
						$sql = 'update client_his_old
								   set from_dt   = \''.$_POST['from'].'\'
								,      to_dt     = \''.$_POST['to'].'\'
								,      svc_val   = \''.$_POST['val'].'\'
								,      svc_tm    = \''.$_POST['time'].'\'
								,      update_id = \''.$_SESSION['userCode'].'\'
								,      update_dt = now()
								 where org_no    = \''.$code.'\'
								   and jumin     = \''.$jumin.'\'
								   and seq       = \''.$seq.'\'';
					}
				}
			}
		}else {
			if ($seq > 0){
				$sql = 'update client_his_old
						   set from_dt   = \''.$_POST['from'].'\'
						,      to_dt     = \''.$_POST['to'].'\'
						,      svc_val   = \''.$_POST['val'].'\'
						,      svc_tm    = \''.$_POST['time'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$seq.'\'';
			}else{
				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_old
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_old (
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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['val'].'\'
						,\''.$_POST['time'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}
		}

	}else if ($mode == 15){
		/*********************************************************

			소득등급이력

		*********************************************************/
		if($IsClientInfo){
			
			$lastDT	= $myF->dateAdd('day', -1, $_POST['from'], 'Ymd');
				
			if($svcCd == '4'){
				if($type == '6'){
					$svcVal = 1;
				}else if($type == '11'){
					$svcVal = 3;
				}
			}

			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_lvl
					 where org_no      = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'
					   and svc_cd  = \''.$svcCd.'\'';
			
			if($svcCd == '4'){
				$sql .= ' and app_no = \''.$svcVal.'\'';
			}


			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_lvl
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and svc_cd		= \''.$svcCd.'\'
					   and from_dt		>= \''.$_POST['from'].'\'';

			if($svcCd == '4'){
				$sql .= ' and app_no = \''.$svcVal.'\'';
			}

			$sql .=	 ' order by from_dt, to_dt
					   limit 1';

			$chkDT = $conn->get_data($sql);
			
			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq
						  from client_his_lvl
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and svc_cd  = \''.$svcCd.'\'
						   and from_dt <= \''.$_POST['from'].'\'
						   /*and to_dt   >= \''.$_POST['to'].'\'*/
						 order by from_dt desc, to_dt desc
						 limit 1';

				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				
				if($chkFrom >= $_POST['from']){
					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_lvl
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and svc_cd  = \''.$svcCd.'\'
							   and seq    = \''.$chkSeq.'\'';

					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}

				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_lvl
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd  = \''.$svcCd.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_lvl (
						 org_no
						,jumin
						,svc_cd
						,seq
						,from_dt
						,to_dt
						,level
						,app_no
						,insert_id
						,insert_dt) values (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['lvl'].'\'
						,\''.$svcVal.'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}else {
				/*********************************************************
					일자중복
				*********************************************************/
				if ($_POST['from'] == $chkDT){
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_lvl
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and svc_cd  = \''.$svcCd.'\'
							   and from_dt = \''.$_POST['from'].'\'';

					$seq = $conn->get_data($sql);


					$sql = 'update client_his_lvl
							   set from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      level     = \''.$_POST['lvl'].'\'
							,      app_no    = \''.$svcVal.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and svc_cd    = \''.$svcCd.'\'
							   and seq       = \''.$seq.'\'';
				}else {
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_lvl
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and svc_cd  = \''.$svcCd.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'select count(*)
							  from client_his_lvl
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and svc_cd  = \''.$svcCd.'\'';
					
					$cnt = $conn->get_data($sql);

					if($cnt==1){
						$sql = 'update client_his_lvl
								   set from_dt   = \''.$_POST['from'].'\'
								,      to_dt     = \''.$_POST['to'].'\'
								,      level     = \''.$_POST['lvl'].'\'
								,      app_no    = \''.$svcVal.'\'
								,      update_id = \''.$_SESSION['userCode'].'\'
								,      update_dt = now()
								 where org_no    = \''.$code.'\'
								   and jumin     = \''.$jumin.'\'
								   and svc_cd    = \''.$svcCd.'\'
								   and seq       = \''.$seq.'\'';
					}
				}
			}
		}else {
			if ($seq > 0){
				$sql = 'update client_his_lvl
						   set from_dt   = \''.$_POST['from'].'\'
						,      to_dt     = \''.$_POST['to'].'\'
						,      level     = \''.$_POST['lvl'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and svc_cd    = \''.$svcCd.'\'
						   and seq       = \''.$seq.'\'';
			}else{
				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_lvl
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd = \''.$svcCd.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_lvl (
						 org_no
						,jumin
						,svc_cd
						,seq
						,from_dt
						,to_dt
						,level
						,insert_id
						,insert_dt) values (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$svcCd.'\'
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['lvl'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}
		}

	}else if ($mode == 17){
		/*********************************************************

			산모신생아 이력

		*********************************************************/
		if($IsClientInfo){
			
			$lastDT	= $myF->dateAdd('day', -1, $_POST['from'], 'Ymd');
				

			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_baby
					 where org_no      = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'';

			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_baby
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and from_dt		>= \''.$_POST['from'].'\'
					 order by from_dt, to_dt
					 limit 1';

			$chkDT = $conn->get_data($sql);
			
			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq
						  from client_his_baby
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and from_dt <= \''.$_POST['from'].'\'
						   /*and to_dt   >= \''.$_POST['to'].'\'*/
						 order by from_dt desc, to_dt desc
						 limit 1';

				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				
				if($chkFrom >= $_POST['from']){
					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_baby
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and seq    = \''.$chkSeq.'\'';

					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}

				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_baby
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_baby (
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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['val'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}else {
				/*********************************************************
					일자중복
				*********************************************************/
				if ($_POST['from'] == $chkDT){
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_baby
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and from_dt = \''.$_POST['from'].'\'';

					$seq = $conn->get_data($sql);


					$sql = 'update client_his_baby
							   set from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      svc_val   = \''.$_POST['val'].'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and seq       = \''.$seq.'\'';
				}else {
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_baby
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';
					$seq = $conn->get_data($sql);

					$sql = 'select count(*)
							  from client_his_baby
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';
					$cnt = $conn->get_data($sql);
					
					if($cnt==1){
						$sql = 'update client_his_baby
								   set from_dt   = \''.$_POST['from'].'\'
								,      to_dt     = \''.$_POST['to'].'\'
								,      svc_val   = \''.$_POST['val'].'\'
								,      update_id = \''.$_SESSION['userCode'].'\'
								,      update_dt = now()
								 where org_no    = \''.$code.'\'
								   and jumin     = \''.$jumin.'\'
								   and seq       = \''.$seq.'\'';
					}
				}
			}
		}else {
			if ($seq > 0){
				$sql = 'update client_his_baby
						   set from_dt   = \''.$_POST['from'].'\'
						,      to_dt     = \''.$_POST['to'].'\'
						,      svc_val   = \''.$_POST['val'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$seq.'\'';
			}else{
				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_baby
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_baby (
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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['val'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}
		}

	}else if ($mode == 18){
		/*********************************************************

			장애인활동지원 이력

		*********************************************************/
		if($IsClientInfo){
			
			$lastDT	= $myF->dateAdd('day', -1, $_POST['from'], 'Ymd');
				
			
			/*********************************************************
				처음 생성일
			*********************************************************/
			$sql = 'select min(from_dt)
					  from client_his_dis
					 where org_no      = \''.$code.'\'
					   and jumin    = \''.$jumin.'\'';

			$minDT = $conn->get_data($sql);
			

			/*********************************************************
				일자 중복 체크
			*********************************************************/
			$sql = 'select from_dt
					  from client_his_dis
					 where org_no		= \''.$code.'\'
					   and jumin		= \''.$jumin.'\'
					   and from_dt		>= \''.$_POST['from'].'\'
					   and svc_val  = \''.$_POST['val'].'\'
					 order by from_dt, to_dt
					 limit 1';

			$chkDT = $conn->get_data($sql);
			
			
			if (empty($chkDT)){
				/*********************************************************
					신규
				*********************************************************/
				$sql = 'select seq, to_dt
						  from client_his_dis
						 where org_no   = \''.$code.'\'
						   and jumin    = \''.$jumin.'\'
						   and svc_val  = \''.$_POST['val'].'\'
						   and from_dt <= \''.$_POST['from'].'\'
						   /*and to_dt   >= \''.$_POST['to'].'\'*/
						 order by from_dt desc, to_dt desc
						 limit 1';

				$tmp = $conn->get_array($sql);
				
				$chkSeq = $tmp['seq'];
				$chkFrom = $tmp['to_dt'];
				

				if($chkFrom >= $_POST['from']){
					//전 일정의 종료일을 변경한다.
					$sql = 'update client_his_dis
							   set to_dt  = \''.$lastDT.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and seq    = \''.$chkSeq.'\'';

					$conn->begin();

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();
				}

				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_dis
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_dis (
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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['val'].'\'
						,\''.$_POST['lvl'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}else {

				/*********************************************************
					일자중복
				*********************************************************/
				if ($_POST['from'] == $chkDT){
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_dis
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'
							   and from_dt = \''.$_POST['from'].'\'';

					$seq = $conn->get_data($sql);


					$sql = 'update client_his_dis
							   set from_dt   = \''.$_POST['from'].'\'
							,      to_dt     = \''.$_POST['to'].'\'
							,      svc_val   = \''.$_POST['val'].'\'
							,      svc_lvl   = \''.$_POST['lvl'].'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and jumin     = \''.$jumin.'\'
							   and seq       = \''.$seq.'\'';
				}else {
					$conn->begin();

					//새 순번
					$sql = 'select seq
							  from client_his_dis
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';
					$seq = $conn->get_data($sql);

					$sql = 'select count(*)
							  from client_his_dis
							 where org_no  = \''.$code.'\'
							   and jumin   = \''.$jumin.'\'';
					$cnt = $conn->get_data($sql);
					
					if($cnt==1){
						$sql = 'update client_his_dis
								   set from_dt   = \''.$_POST['from'].'\'
								,      to_dt     = \''.$_POST['to'].'\'
								,      svc_val   = \''.$_POST['val'].'\'
								,      svc_lvl   = \''.$_POST['lvl'].'\'
								,      update_id = \''.$_SESSION['userCode'].'\'
								,      update_dt = now()
								 where org_no    = \''.$code.'\'
								   and jumin     = \''.$jumin.'\'
								   and seq       = \''.$seq.'\'';
					}
				}
			}
		}else {
			if ($seq > 0){
				$sql = 'update client_his_dis
						   set from_dt   = \''.$_POST['from'].'\'
						,      to_dt     = \''.$_POST['to'].'\'
						,      svc_val   = \''.$_POST['val'].'\'
						,      svc_lvl   = \''.$_POST['lvl'].'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$seq.'\'';
			}else{
				$sql = 'select ifnull(max(seq),0)+1
						  from client_his_dis
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';

				$seq = $conn->get_data($sql);

				$sql = 'insert into client_his_dis (
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
						,\''.$seq.'\'
						,\''.$_POST['from'].'\'
						,\''.$_POST['to'].'\'
						,\''.$_POST['val'].'\'
						,\''.$_POST['lvl'].'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';
			}
		}
	}

	if (!empty($sql)){

		if (substr($sql,0,6) == 'select'){
			$html = '';

			if ($mode == 3){
				$data = $conn->get_array($sql);

				$html = 'nm='.$data['nm'].'
						&postNo1='.substr($data['postno'],0,3).'
						&postNo2='.substr($data['postno'],3,3).'
						&addr='.$data['addr'].'
						&addrDtl='.$data['addr_dtl'].'
						&mobile='.$data['mobile'].'
						&phone='.$data['phone'].'
						&protectNm='.$data['protect_nm'].'
						&protectRel='.$data['protect_rel'].'
						&protectPhone='.$data['protect_phone'].'
						&clientNo='.$data['client_no'].'
						&memo='.$data['memo'];

				$sql = 'select max(seq)
						,      svc_cd
						,      to_dt
						  from client_his_svc
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						 group by svc_cd, to_dt';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					$html .= '&toDt_'.$row['svc_cd'].'='.$row['to_dt'];
				}

				$conn->row_free();

			}else if ($mode == 4){
				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					$html .= (!empty($html) ? '&' : '').'min_'.$row['cd'].'='.$row['min_dt'];
					$html .= (!empty($html) ? '&' : '').'max_'.$row['cd'].'='.$row['max_dt'];
				}

				$conn->row_free();
			}
			echo $html;

			unset($data);
		}else{
			$conn->begin();

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();
			echo 1;
		}
	}

	include_once('../inc/_db_close.php');
?>