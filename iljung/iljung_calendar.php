<?
	include_once("../inc/_header.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");
	include_once('iljung_config.php');

	define(__CHECK_ADD_TIME__, 120);
	define(__CHECK_HOUR__, 60);
	define(__FAMILY_SUGA_CD__, 'CCWC'); //동거수가코드
	define(__BATH_SUGA_CD__, 'CB'); //목욕수가코드
	define(__BATH_VOUCHER__, 'VAB'); //바우처 목욕수가
	define(__VOU_BABY_TO_TIME1__, '1700'); //산모신생아 토요일 종료시간
	define(__VOU_BABY_TO_TIME2__, '1300'); //산모신생아 토요일 종료시간

	$microTime1 = $myF->getMtime();

	$con2       = new connection();
	$_PARAM     = $_REQUEST;
	$mCode      = $_PARAM["mCode"];
	$mKind      = $_PARAM["mKind"];
	$mKey       = $_PARAM["mKey"];
	$mJuminNo   = $_PARAM["mJuminNo"];
	$calYear    = $_PARAM["calYear"];
	$calMonth   = $_PARAM["calMonth"];
	$calMonth   = (intval($calMonth) < 10 ? '0' : '').intval($calMonth);
	$calDay     = $_PARAM["calDay"];
	$calDay     = (intval($calDay) < 10 ? '0' : '').intval($calDay);
	$gubun      = $_PARAM["gubun"];
	$workType   = $_PARAM['workType'];
	$svcInType	= $_PARAM['svcInType'];	//제공서비스 기준
	$tmpSvcDate	= $_PARAM['svcDate'];	//제공일자
	$svcDate	= explode(',', $tmpSvcDate);
	$svcId		= $_PARAM['svcId'];
	$kindList   = $conn->kind_list($mCode, true);





	/*********************************************************

		요양보호사 주민번호 언코딩

	*********************************************************/
		#$_PARAM['yoy1'] = $ed->de($_PARAM['yoy1']);
		#$_PARAM['yoy2'] = $ed->de($_PARAM['yoy2']);
		$memCD[1] = $ed->de($_PARAM['yoy1']);
		$memCD[2] = $ed->de($_PARAM['yoy2']);



	/*********************************************************

		바우처(장애인활동지원) 수가

	*********************************************************/
		$sql = 'select service_code as cd
				,      service_gbn as nm
				,      service_lvl as lvl
				,      service_cost as cost
				,      service_cost_night as night_cost
				,      service_cost_holiday as holiday_cost
				  from suga_service
				 where org_no       = \''.$mCode.'\'
				   and service_kind = \'4\'
				   and left(service_from_dt, 6) <= \''.$calYear.$calMonth.'\'
				   and left(service_to_dt,   6) >= \''.$calYear.$calMonth.'\'';

		$arrSugaDis = $conn->_fetch_array($sql, 'cd');



	/*********************************************************

		방문요양 수가

	*********************************************************/
		$sql = 'select \'1\' as idx
				,      m01_mcode2 as cd
				,      m01_suga_cont as nm
				,      m01_suga_value as pay
				  from m01suga
				 where m01_mcode           = \''.$mCode.'\'
				   and left(m01_sdate, 6) <= \''.$calYear.$calMonth.'\'
				   and left(m01_edate, 6) >= \''.$calYear.$calMonth.'\'
				 union all
				select \'2\' as idx
				,      m11_mcode2 as cd
				,      m11_suga_cont as nm
				,      m11_suga_value as pay
				  from m11suga
				 where m11_mcode           = \''.$mCode.'\'
				   and left(m11_sdate, 6) <= \''.$calYear.$calMonth.'\'
				   and left(m11_edate, 6) >= \''.$calYear.$calMonth.'\'';

		$arrSugaCare = $conn->_fetch_array($sql, 'cd');



	/*********************************************************

		바우처 수가

	*********************************************************/
		$sql = 'select concat(service_kind, \'_\', service_code) as cd
				,      service_gbn as nm
				,      service_lvl as lvl
				,      service_cost as pay
				  from suga_service
				 where org_no                    = \''.$mCode.'\'
				   and left(service_from_dt, 6) <= \''.$calYear.$calMonth.'\'
				   and left(service_to_dt, 6)   >= \''.$calYear.$calMonth.'\'';

		$arrSugaVou = $conn->_fetch_array($sql, 'cd');




	/*********************************************************

		요양보호사 상태

	*********************************************************/
	/*
	$sql = 'select min(m02_mkind) as kind
			,      m02_yjumin as m_cd
			,      m02_ygoyong_stat as stat
			,      m02_ytoisail as out_dt
			  from m02yoyangsa
			 where m02_ccode = \''.$mCode.'\'
			 group by m02_yjumin';
	*/
	$sql = 'select mem.m_cd
			,      mem.stat
			,      his.join_dt
			,      his.quit_dt
			  from (
				   select m02_ccode as cd
				   ,      min(m02_mkind) as kind
				   ,      m02_yjumin as m_cd
				   ,      m02_ygoyong_stat as stat
					 from m02yoyangsa
					where m02_ccode = \''.$mCode.'\'
					group by m02_ccode, m02_yjumin
				   ) as mem
			 inner join (
				   select org_no as cd
				   ,      jumin as m_cd
				   ,      max(seq) as seq
				   ,      replace(join_dt,\'-\',\'\') as join_dt
				   ,      replace(ifnull(quit_dt,\'9999-12-31\'),\'-\',\'\') as quit_dt
					 from mem_his
					where org_no = \''.$mCode.'\'
					group by org_no, jumin
				   ) as his
				on his.cd = mem.cd
			   and his.m_cd = mem.m_cd';

	$memInfo = $conn->_fetch_array($sql, 'm_cd');



	if (!is_numeric($mJuminNo)) $mJuminNo = $ed->de($mJuminNo);

	// 목욕 일정제한을 위해 첫주 중 전달의 목욕 일정수를 구한다.
	$tmp_time       = mktime(0, 0, 0, $calMonth, '01', $calYear);
	$tmp_start_week = date('w', $tmp_time);
	$tmp_end_dt     = strtotime(($tmp_start_week == 0 ? 'this Sunday' : 'next Sunday'), $tmp_time);

	if (date('d', $tmp_end_dt) == '01'){
		$tmp_end_dt =  strtotime('+6 day', $tmp_end_dt);
	}

	$tmp_start_dt   = strtotime('-6 day', $tmp_end_dt);
	$tmp_start_dt   = date('Ymd', $tmp_start_dt);
	$tmp_end_dt     = $calYear.$calMonth.'01';

	$sql = "select count(*)
			  from t01iljung
			 where t01_ccode       = '$mCode'
			   and t01_mkind       = '$mKind'
			   and t01_jumin       = '$mJuminNo'
			   and t01_svc_subcode = '500'
			   and t01_sugup_date >= '$tmp_start_dt'
			   and t01_sugup_date <  '$tmp_end_dt'
			   and t01_del_yn      = 'N'";

	$tmp_first_week_cnt = $conn->get_data($sql);

	// 목욕 일정제한을 위해 마지막주 중 다음달의 목욕 일정수를 구한다.
	$tmp_lastday  = $myF->lastDay($calYear, $calMonth);
	$tmp_time     = mktime(0, 0, 0, $calMonth, $tmp_lastday, $calYear);
	$tmp_end_week = date('w', $tmp_time);
	$tmp_end_dt   = strtotime(($tmp_end_week == 0 ? 'this Sunday' : 'next Sunday'), $tmp_time);
	$tmp_start_dt = $calYear.$calMonth.$tmp_lastday;
	$tmp_end_dt   = date('Ymd', $tmp_end_dt);

	$sql = "select count(*)
			  from t01iljung
			 where t01_ccode       = '$mCode'
			   /*and t01_mkind       = '$mKind'*/
			   and t01_jumin       = '$mJuminNo'
			   and t01_svc_subcode = '500'
			   and t01_sugup_date >  '$tmp_start_dt'
			   and t01_sugup_date <= '$tmp_end_dt'
			   and t01_del_yn      = 'N'";

	#if ($debug) echo nl2br($sql);

	$tmp_last_week_cnt = $conn->get_data($sql);

	// 목욕 일정제한을 위해 주수를 구한다.
	$tmp_tot_weekcnt = ceil(($tmp_lastday + $tmp_start_week) / 7); //총 몇 주인지 구하기

	// 동거가족 제한(2011년 8월부터)
	if ($calYear.$calMonth >= '201108'){
		$sql = "select m03_partner
				,      m03_stat_nogood
				,      m03_yoyangsa1
				,      m03_bath_add_yn
				  from m03sugupja
				 where m03_ccode = '$mCode'
				   and m03_mkind = '$mKind'
				   and m03_jumin = '$mJuminNo'";
		#if ($debug) echo nl2br($sql);
		$row = $conn->get_array($sql);

		##################################################################
		# 주요양보호사 배우자이거나 수급자(65세이상)가 상태이상인 경우
		# 1일 90분 31일 가능하며
		# 그렇지 않을 경우 1일 60분 20일만 가능하다.
		##################################################################
		//if ($_PARAM['yoy1'] == $row['m03_yoyangsa1']){
		if ($memCD[1] == $row['m03_yoyangsa1']){
			$member_age = $myF->issToAge($row['m03_yoyangsa1']);
		}else{
			$member_age = 0;
		}

		if (($row['m03_partner'] == 'Y' && $member_age >=65) || $row['m03_stat_nogood'] == 'Y'){
			$family_min = 90;
			$family_cnt = $myF->lastDay(substr($temp_date, 0, 4), substr($temp_date, 4, 2));
		}else{
			$family_min = 60;
			$family_cnt = 20;
		}
		$care_limit_cnt = 1;
	}else{
		$family_min     = 24 * 60;
		$family_cnt     = 31;
		$care_limit_cnt = 99;
	}

	// 목욕 주간별 가능횟수
	if ($calYear.$calMonth >= '201107'){
		if ($row['m03_bath_add_yn'] == 'Y')
			$bath_week_cnt = 7;
		else
			$bath_week_cnt = 1;
	}else{
		$bath_week_cnt = 7;
	}

	# 동거가족 제한 및 목욕제한을 해제시 아래의 소스를 삭제할것.#######
	#$family_min = 24 * 60;
	#$family_cnt = 31;
	#$bath_week_cnt = 99;
	###################################################################

	// 동거가족 제한(2011년 8월부터)
	for($i=1; $i<=31; $i++){
		$family_time[$i]     = 0;
		$tmp_family_time[$i] = 0;
		$care_day_cnt[$i]    = 0;
	}

	$sql = "select cast(date_format(t01_sugup_date, '%d') as signed) as dt, t01_sugup_soyotime as time
			  from t01iljung
			 where t01_ccode         = '$mCode'
			   and t01_mkind         = '$mKind'
			   and t01_jumin         = '$mJuminNo'
			   and t01_sugup_date like '$calYear$calMonth%'
			   and t01_suga_code1 like '".__FAMILY_SUGA_CD__."%'
			   and t01_del_yn        = 'N'";
	#if ($debug) echo nl2br($sql);
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$r = $conn->select_row($i);

		$family_time[$r['dt']]     = $r['time'];
		$tmp_family_time[$r['dt']] = $r['time'];
	}

	$conn->row_free();

	// 동거가족이 등록된 일자수
	$family_days = 0;

	// 목욕 일정제한을 위해 목욕 주간 횟수
	for($i=1; $i<=$tmp_tot_weekcnt; $i++){
		switch($i){
			case 1:
				$bath_time[$i] = $tmp_first_week_cnt;
				break;
			case $tmp_tot_weekcnt:
				$bath_time[$i] = $tmp_last_week_cnt;
				break;
			default:
				$bath_time[$i] = 0;
		}
	}

	if ($workType == "modify"){
		// 확정처리시 일정을 수정하는 경우
		$mJuminNo = $ed->de($_PARAM["mJuminNo"]);
	}else if ($workType == "dayModify"){
		$mJuminNo = $ed->de($_PARAM["mJuminNo"]); //수급자
		$mYoyangsa = $ed->de($_PARAM['mYoyangsa']); //요양사(아직은 요양사별 처리는 하지 않았다.)
	}else{
	}

	$client_date = $conn->client_date($mCode, '', $mJuminNo, $calYear.$calMonth);
	$dt_min = $client_date[0];
	$dt_max = $client_date[1];

	$client_date = $conn->client_date($mCode, $mKind, $mJuminNo, $calYear.$calMonth);
	$dt_from = $client_date[0];
	$dt_to   = $client_date[1];

	if ($lbTestMode){
		/*********************************************************

			수급자 계약기간

		*********************************************************/
		$sql = 'select date_format(from_dt,\'%Y%m%d\') as from_dt
				,      date_format(to_dt,\'%Y%m%d\') as to_dt
				  from client_his_svc
				 where org_no           = \''.$mCode.'\'
				   and jumin            = \''.$mJuminNo.'\'
				   and svc_cd           = \''.$mKind.'\'
				   and left(from_dt,4) <= \''.$calYear.'\'
				   and left(to_dt,4)   >= \''.$calYear.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$idx = sizeof($period);

			$period[$idx] = array('from'=>$row['from_dt'],'to'=>$row['to_dt']);
		}

		$conn->row_free();
	}



	/**************************************************

		급여한도

	**************************************************/
	if ($mKind == '0'){
		/**************************************************
			재가요양
		**************************************************/
		if ($lbTestMode){
			/*********************************************************
				장기요양 등급
			*********************************************************/
			$sql = 'select min(level) as lvl
					  from client_his_lvl
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and svc_cd = \''.$mKind.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$ym.'\'
					   and date_format(to_dt,  \'%Y%m\') >= \''.$ym.'\'';

			$liLvlCd = $conn->get_data($sql);

			/*********************************************************
				수급자 구분
			*********************************************************/
			$sql = 'select rate
					  from client_his_kind
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$ym.'\'
					   and date_format(to_dt,  \'%Y%m\') >= \''.$ym.'\'
					 order by seq desc
					 limit 1';

			$liRate = $conn->get_data($sql);

			/*********************************************************
				청구한도
			*********************************************************/
			$sql = 'select amt
					  from client_his_limit
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$ym.'\'
					   and date_format(to_dt,  \'%Y%m\') >= \''.$ym.'\'
					 order by seq desc
					 limit 1';

			$liLimitAmt = $conn->get_data($sql);

			$bonin_yul  = $liRate;	   //본인부담율
			$client_lvl = $liLvlCd;    //수급자등급
			$max_group  = $liLimitAmt; //청구한도금액
		}else{
			$sql = "select m03_kupyeo_max, m03_kupyeo_1, m03_bonin_yul, m03_skind, m03_ylvl
					  from (
						   select m03_kupyeo_max, m03_kupyeo_1, m03_bonin_yul, m03_skind, m03_ylvl
						   ,      m03_sdate
						   ,      m03_edate
							 from m03sugupja
							where m03_ccode = '$mCode'
							  and m03_mkind = '$mKind'
							  and m03_jumin = '$mJuminNo'
							union all
						   select m31_kupyeo_max, m31_kupyeo_1, m31_bonin_yul, m31_kind, m31_level
						   ,      m31_sdate
						   ,      m31_edate
							 from m31sugupja
							where m31_ccode = '$mCode'
							  and m31_mkind = '$mKind'
							  and m31_jumin = '$mJuminNo'
						   ) as t
					 where '$calYear$calMonth' between left(m03_sdate, 6) and left(m03_edate, 6)
					 order by m03_sdate desc, m03_edate desc
					 limit 1";

			$client_array = $conn->get_array($sql);
			$max_amount   = $client_array[0];	//한도금액
			$max_group    = $client_array[1];	//정부지원금한도액
			$client_kind  = $client_array[3];	//수급자구분
			$client_lvl   = $client_array[4];	//등급

			unset($client_array);
		}

		/*********************************************************
			한도금액을 가져온다.
		*********************************************************/
		$max_amount = $conn->_limit_pay($client_lvl, $calYear.$calMonth);
		#if ($calYear.$calMonth >= '201201') $max_group = $max_amount;
		if (empty($max_group)) $max_group = $max_amount;
		if ($max_group > $max_amount) $max_group = $max_amount;



		/*
		 * 의료수급자는 정부지원한도액을 한도금액으로 설정한다.
		 */
		$max_amount = $max_group;
		$max_times  = 0;
	}else if ($mKind >= '1' && $mKind <= '3'){
		/**************************************************
			바우처(가사간병, 노인돌봄, 산모신생아)
		**************************************************/
		$sql = "select voucher_kind as svc_cd
				,      voucher_suga_cost as svc_amt
				,      voucher_totaltime as svc_time
				,      voucher_gbn as gbn
				,      voucher_gbn2 as gbn2
				,      voucher_lvl as lvl
				  from voucher_make
				 where org_no        = '$mCode'
				   and voucher_jumin = '$mJuminNo'
				   and voucher_yymm  = '$calYear$calMonth'
				   and del_flag      = 'N'";

		$client_array = $conn->get_array($sql);
		$max_amount   = $client_array['svc_amt'] * $client_array['svc_time'];	//한도금액
		$max_times    = 0;

		unset($client_array);
	}else if ($mKind == '4'){
		/**************************************************
			바우처(장애인활동지원)
		*********************************************************/

		/*
		//바우처생성내역
		$sql = 'select voucher_kind as kind
				,      voucher_overtime as overtime
				,      voucher_addtime1 + voucher_addtime2 as addtime
				,      voucher_maketime as maketime
				,      voucher_totaltime as totaltime
				,      voucher_totaltime - (voucher_overtime + voucher_addtime1 + voucher_addtime2 + voucher_maketime) as addpaytime
				,      voucher_suga_cd as suga_cd
				,      voucher_suga_cost as suga_cost
				,      voucher_gbn2 as gbn2_cd
				  from voucher_make
				 where org_no        = \''.$mCode.'\'
				   and voucher_kind  = \''.$mKind.'\'
				   and voucher_jumin = \''.$mJuminNo.'\'
				   and voucher_yymm  = \''.$calYear.$calMonth.'\'
				   and del_flag      = \'N\'';

		$vou_mst = $conn->get_array($sql);



		//생성바우처 한도금액
		$sql = 'select service_gbn as nm
				,      service_cost as cost
				,      service_cost_night as cost_night
				,      service_cost_holiday as cost_holiday
				,      service_bipay as bipay
				,      service_conf_time as conf_time
				,      service_conf_amt as conf_amt
				  from suga_service
				 where org_no                   = \''.$mCode.'\'
				   and service_kind             = \''.$mKind.'\'
				   and service_code             = \''.$vou_mst['suga_cd'].'\'
				   and left(service_from_dt,7) <= \''.$calYear.'-'.$calMonth.'\'
				   and left(service_to_dt,7)   >= \''.$calYear.'-'.$calMonth.'\'
				 order by service_from_dt desc
				 limit 1';

		$vou_pay = $conn->get_array($sql);



		//추가급여 한도금액
		$sql = 'select svc_gbn_nm as nm
				,      svc_time as time
				,      svc_pay as pay
				  from suga_service_add
				 where svc_kind             = \''.$mKind.'\'
				   and svc_gbn_cd           = \''.$vou_mst['gbn2_cd'].'\'
				   and left(svc_from_dt,7) <= \''.$calYear.'-'.$calMonth.'\'
				   and left(svc_to_dt,7)   >= \''.$calYear.'-'.$calMonth.'\'
				 order by svc_from_dt desc
				 limit 1';

		$vou_add = $conn->get_array($sql);

		$max_amount = $vou_pay['conf_amt'] + $vou_add['pay'] + $vou_mst['addtime'] * $vou_mst['suga_cost'];
		$max_times  = $vou_mst['maketime'] + $vou_mst['addpaytime'] + $vou_mst['addtime'];

		unset($vou_mst);
		unset($vou_pay);
		unset($vou_add);
		*/

		$sql = 'select voucher_totalpay as pay
				,      voucher_totaltime as time
				  from voucher_make
				 where org_no        = \''.$mCode.'\'
				   and voucher_kind  = \''.$mKind.'\'
				   and voucher_jumin = \''.$mJuminNo.'\'
				   and voucher_yymm  = \''.$calYear.$calMonth.'\'
				   and del_flag      = \'N\'';

		$vou_pay = $conn->get_array($sql);

		$max_amount = $vou_pay['pay'];
		$max_times  = $vou_pay['time'];

		unset($vou_pay);
	}else{
		/**************************************************
			기타유료
		**************************************************/
		$max_amount = 0;
		$max_times  = 0;
	}

	/*****************************************************

		계획 총 수가

	*****************************************************/
		$sql = 'select sum(t01_suga_tot), sum(ifnull(t01_sugup_soyotime, 0)) / 60
				  from t01iljung
				 where t01_ccode               = \''.$mCode.'\'
				   and t01_mkind               = \''.$mKind.'\'
				   and t01_jumin               = \''.$mJuminNo.'\'
				   and left(t01_sugup_date, 6) = \''.$calYear.$calMonth.'\'
				   and t01_bipay_umu          != \'Y\'
				   and t01_del_yn              = \'N\'';

		$suga_if = $conn->get_array($sql);


		$suga_total = $suga_if[0]; //수가총금액
		$suga_times = $suga_if[1]; //수가총시간
	/****************************************************/



	$nowYM = date("Ym", mktime()); // 현재 년월

	if ($workType == "modify"){
		// 확정처리시 일정을 수정하는 경우
		//$mJuminNo = $ed->de($_PARAM["mJuminNo"]);
		$confStartDate = getPMonth();
	}else if ($workType == "dayModify"){
		//$mJuminNo = $ed->de($_PARAM["mJuminNo"]); //수급자
		//$mYoyangsa = $ed->de($_PARAM['mYoyangsa']); //요양사(아직은 요양사별 처리는 하지 않았다.)
		$confStartDate = $calYear.$calMonth.$calDay;
	}else{
		// 센터의 계약시작일을 가져온다.
		$sql = "select m00_cont_date"
			 . "  from m00center"
			 . " where m00_mcode = '".$mCode
			 . "'  and m00_mkind = '".$mKind
			 . "'";
		$centerStartDate = $conn->get_data($sql);
		$centerStartDate = subStr($centerStartDate, 0, 6);
		$confStartDate = '999999';
	}
	if ($centerStartDate != $nowYM) $centerStartDate = '999999';

	// 마감처리여부
	$close_yn = $conn->get_closing_act($mCode, $calYear.$calMonth);

	if ($close_yn == 'N') $centerStartDate = $calYear.$calMonth;

	if (strLen($calMonth) == 1){
		$calMonth = "0".$calMonth;
	}

	$calTime  = mkTime(0, 0, 1, $calMonth, 1, $calYear);
	$boninYul = $conn->get_bonin_yul($mCode, $mKind, $mJuminNo);

	// 수급자의 등급을 찾는다.
	$sugupjaLevel = $conn->get_sugupja_level($mCode, $mKind, $mJuminNo);

	$sql = "select *
			  from t01iljung
			 where t01_ccode         = '$mCode'
			   and t01_jumin         = '$mJuminNo'
			   and t01_sugup_date like '$calYear$calMonth%'
			   and t01_del_yn        = 'N'
			 order by t01_sugup_date, t01_sugup_fmtime";

	$conn->query($sql);
	$row = $conn->fetch();
	$row_count = $conn->row_count();
?>
<input name="mWorkType" type="hidden" value="<?=$workType;?>">

<table class="my_table my_border_blue" style="width:100%; margin-top:<?=__GAB__;?>px;">
	<tbody>
	<tr>
		<th class="left bold" colspan="14">
			<table style="width:100%;">
			<tr>
				<td style="text-align:left; padding-left:5px; border:0;">
				<?
					if ($workType == "modify" || ($workType != 'dayModify' && $gubun == 'reg')){
					?>
						<table width="100%">
						<tr>
							<td style="border:none; text-align:left; padding-left:5px; font-weight:bold;">
								<?=$calYear;?>년 <?=intval($calMonth);?>월
								<input name="calYear" type="hidden" value="<?=$calYear;?>">
								<input name="calMonth" type="hidden" value="<?=$calMonth;?>">
							</td>
							<td style="border:none; text-align:right; padding-right:5px;">
							<?
								if ($workType == 'modify'){?>
									<input type="button" onClick="_iljungModify();" value="저장" class="btnSmall2" onFocus="this.blur();"><?
								}
							?>
							</td>
						</tr>
						</table>
					<?
					}else if ($workType == "dayModify"){
					?>
						<table width="100%">
						<tr>
							<td style="border:none; text-align:left; padding-left:5px; font-weight:bold;">
								<?=$calYear;?>년<?=intval($calMonth);?>월<?=intval($calDay);?>일
								<input name="calYear" type="hidden" value="<?=$calYear;?>">
								<input name="calMonth" type="hidden" value="<?=$calMonth;?>">
								<input name="calDay" type="hidden" value="<?=$calDay;?>">
							</td>
							<td style="border:none; text-align:right; padding-right:5px;">
								<input type="button" onClick="_iljungModify();" value="저장" class="btnSmall2" onFocus="this.blur();">
							</td>
						</tr>
						</table>
					<?
					}else{
					?>
						<select name="calYear" style="width:65px;" onChange="_setCalendar();">
						<?
							for($i=2010; $i<=2011; $i++){
								echo '<option value="'.$i.'" '.($calYear == $i ? "selected" : "").'>'.$i.'년</option>';
							}
						?>
						</select>
						<select name="calMonth" style="width:55px;" onChange="_setCalendar();">
							<option value="01"<? if($calMonth == "01"){echo "selected";}?>>1월</option>
							<option value="02"<? if($calMonth == "02"){echo "selected";}?>>2월</option>
							<option value="03"<? if($calMonth == "03"){echo "selected";}?>>3월</option>
							<option value="04"<? if($calMonth == "04"){echo "selected";}?>>4월</option>
							<option value="05"<? if($calMonth == "05"){echo "selected";}?>>5월</option>
							<option value="06"<? if($calMonth == "06"){echo "selected";}?>>6월</option>
							<option value="07"<? if($calMonth == "07"){echo "selected";}?>>7월</option>
							<option value="08"<? if($calMonth == "08"){echo "selected";}?>>8월</option>
							<option value="09"<? if($calMonth == "09"){echo "selected";}?>>9월</option>
							<option value="10"<? if($calMonth == "10"){echo "selected";}?>>10월</option>
							<option value="11"<? if($calMonth == "11"){echo "selected";}?>>11월</option>
							<option value="12"<? if($calMonth == "12"){echo "selected";}?>>12월</option>
						</select>
					<?
					}
				?>
				<span id="spanYear" style="display:none;"></span>
				<span id="spanMonth" style="display:none;"></span>
				</td>
				<td style="border:0; text-align:right; padding-right:5px;">
				<?
					if ($gubun == "search"){
					?>	<span class='btn_pack m icon'><span id='spanIcon' class='pdf'></span><button id='btnIljungPrint' type='button' onFocus='this.blur();' onClick="serviceCalendarShow('<?=$mCode;?>','<?=$mKind;?>','<?=$calYear;?>','<?=$calMonth;?>','<?=$mKey;?>', 's','y','pdf','y')/*_printIljung();*/" title="금액표시된 출력물입니다.">일정출력1</button></span>
						<span class='btn_pack m icon'><span id='spanIcon' class='pdf'></span><button id='btnIljungPrint' type='button' onFocus='this.blur();' onClick="serviceCalendarShow('<?=$mCode;?>','<?=$mKind;?>','<?=$calYear;?>','<?=$calMonth;?>','<?=$mKey;?>', 's','n','pdf','y')/*_printIljung();*/" title="금액 미표시된 출력물입니다.">일정출력2</button></span><?
					}else{
						echo '<div style=\'font-weight:bold;\'>아래 <img src=\'../image/btn_del.png\'>를 클릭하시면 일정이 <span style=\'color:#ff0000;\'>삭제</span>됩니다. 삭제된 일정은 복구 할 수 없습니다.</div>';
					}
				?>
				</td>
			</tr>
			</table>
		</th>
	</tr>
	<tr>
		<th class="bold" style="width:128px; color:#ff0000;" colspan="2">일</th>
		<th class="bold" style="width:128px;" colspan="2">월</th>
		<th class="bold" style="width:128px;" colspan="2">화</th>
		<th class="bold" style="width:128px;" colspan="2">수</th>
		<th class="bold" style="width:128px;" colspan="2">목</th>
		<th class="bold" style="width:128px;" colspan="2">금</th>
		<th class="bold" style="width:132px; color:#0000ff;" colspan="2">토</th>
	</tr>
	<?
		$today     = date("Ymd", mktime());
		$lastDay   = date("t", $calTime); //총일수 구하기
		$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01")); //시작요일 구하기
		$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
		$lastWeek  = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay)); //마지막 요일 구하기
		$day=1; //화면에 표시할 화면의 초기값을 1로 설정
		$index = 1;
		$dbStart = 0;
		$addFlag = false;

		$new_total_suga = 0; //수가 총금액
		$new_total_time = 0; //수가 총시간

		// 요양보호사 일정리스트
		for($t=1; $t<=2; $t++){
			if ($_PARAM['yoy'.$t] != ''){
				if (ceil($_PARAM['fmTime']) > ceil($_PARAM['ttTime'])){
					$ToTime = ceil($_PARAM['ttTime']) + 2400;
				}else{
					$ToTime = $_PARAM['ttTime'];
				}

				$sql = "select t01_sugup_date
						  from t01iljung
						 where t01_ccode = '".$mCode."'
						   and left(t01_sugup_date, 6) = '".$calYear.$calMonth."'
						   and t01_mem_cd1 = '".$memCD[$t]."'
						   and case when t01_sugup_fmtime > t01_sugup_totime then cast(t01_sugup_totime as unsigned) + 2400 else t01_sugup_totime end > '".$_PARAM['fmTime']."'
						   and t01_sugup_fmtime <  '".$ToTime."'
						   and t01_del_yn = 'N'
						 union all
						select t01_sugup_date
						  from t01iljung
						 where t01_ccode = '".$mCode."'
						   and left(t01_sugup_date, 6) = '".$calYear.$calMonth."'
						   and t01_mem_cd2 = '".$memCD[$t]."'
						   and case when t01_sugup_fmtime > t01_sugup_totime then cast(t01_sugup_totime as unsigned) + 2400 else t01_sugup_totime end > '".$_PARAM['fmTime']."'
						   and t01_sugup_fmtime <  '".$ToTime."'
						   and t01_del_yn = 'N'";

				$con2->query($sql);
				$con2->fetch();
				$row2_count = $con2->row_count();

				for($l=0; $l<$row2_count; $l++){
					$row2 = $con2->select_row($l);
					$temp_yoy_iljung[$t][$l] = $row2[0];
				}

				$con2->row_free();
			}
		}

		// 휴일리스트
		$sql = "select mdate, ifnull(holiday_name, '')
				  from tbl_holiday
				 where mdate like '".$calYear.$calMonth."%'
				 order by mdate";
		$con2->query($sql);
		$con2->fetch();
		$row2_count = $con2->row_count();

		for($l=0; $l<$row2_count; $l++){
			$row2 = $con2->select_row($l);
			$temp_holiday_list[$l]['date'] = $row2[0];
			$temp_holiday_list[$l]['name'] = $row2[1];
		}
		$temp_holiday_list_count = sizeof($temp_holiday_list);

		$con2->row_free();

		for($i=1; $i<=$lastDay; $i++){
			$dayIndex[$i] = 1;
		}

		ob_start();

		// 총 주 수에 맞춰서 세로줄 만들기
		for($i=1; $i<=$totalWeek; $i++){
			echo "<tr>";
			// 총 가로칸 만들기
			for ($j=0; $j<7; $j++){
				echo "<th class='center top' style='width:20px; height:40px; line-height:1.5em;'>";
				// 첫번째 주이고 시작요일보다 $j가 작거나 마지막주이고 $j가 마지막 요일보다 크면 표시하지 않아야하므로
				// 그 반대의 경우 -  ! 으로 표현 - 에만 날자를 표시한다.
				$subject = '';
				$subjectID = '';
				$subjectPrint = '';
				if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
					// 주간일자를구한다.
					$weekindex = $myF->weekindex($calYear.'-'.$calMonth.'-'.$day);

					$index = $dayIndex[$day];
					$iljungDate = date("Ymd", mkTime(0, 0, 1, $calMonth, $day, $calYear));

					$holidayName = '';
					$holiday	 = 'N';
					for($l=0; $l<$temp_holiday_list_count; $l++){
						if ($temp_holiday_list[$l]['date'] == $iljungDate){
							$holidayName = $temp_holiday_list[$l]['name'];
							$holiday	 = 'Y';
							break;
						}
					}

					if ($j == 0) $holiday = 'Y';

					if ($holidayName == ''){
						if($j == 0){
							echo "<font color='#FF0000'>".$day."</font>";
						}else if($j == 6){
							echo "<font color='#0000FF'>".$day."</font>";
						}else{
							echo "<font color='#000000'>".$day."</font>";
						}
					}else{
						echo "<font color='#FF0000' title='".$holidayName."'>".$day."</font>";
					}

					if ($gubun != 'search'){
						if ($workType == "dayModify"){
							echo '<br><img src="../image/btn_add.png" style="cursor:pointer;" onClick="_addDiary(\''.$_PARAM['mCode'].'\',\''.$_PARAM['mKind'].'\',\''.$_PARAM['mKey'].'\',\''.$day.'\',\''.$iljungDate.'\',\''.$j.'\');">';
						}else{
							if ($lbTestMode){
								$btnAddFlag = false;

								foreach($period as $row){
									if ($row['from'] <= $iljungDate && $row['to'] >= $iljungDate){
										$btnAddFlag = true;
										break;
									}
								}

								if ($btnAddFlag){
									$btnAddFlag = false;
									if (($centerStartDate == subStr($iljungDate, 0, 6)) or
										($confStartDate <= subStr($iljungDate, 0, 6)) or
										($today <= $iljungDate and $gubun == 'reg')){
										// 계약시작월 이거나 일자 및 시간이 현재보다 미래인경우 일정을 등록할 수 있도록 풀어준다.
										$btnAddFlag = true;
									}
								}
							}else{
								$btnAddFlag = false;

								if ($dt_from <= $iljungDate && $dt_to >= $iljungDate){
									if (($centerStartDate == subStr($iljungDate, 0, 6)) or
										($confStartDate <= subStr($iljungDate, 0, 6)) or
										($today <= $iljungDate and $gubun == 'reg')){
										// 계약시작월 이거나 일자 및 시간이 현재보다 미래인경우 일정을 등록할 수 있도록 풀어준다.
										//echo '<br><img src="../image/btn_add.png" style="cursor:pointer;" onClick="_addDiary(\''.$_PARAM['mCode'].'\',\''.$_PARAM['mKind'].'\',\''.$_PARAM['mKey'].'\',\''.$day.'\',\''.$iljungDate.'\',\''.$j.'\');">';
										$btnAddFlag = true;
									}
								}
							}

							if ($btnAddFlag)
								echo '<br><img src="../image/btn_add.png" style="cursor:pointer;" onClick="_addDiary(\''.$_PARAM['mCode'].'\',\''.$_PARAM['mKind'].'\',\''.$_PARAM['mKey'].'\',\''.$day.'\',\''.$iljungDate.'\',\''.$j.'\');">';
						}
					}

					$subjectID = "txtSubject_".$day;

					for($k=0; $k<$row_count; $k++){
						$row = $conn->select_row($k);

						if ($row['t01_sugup_date'] == $iljungDate){
							$index = $dayIndex[$day];

							if ($row['t01_mkind'] == '0'){
								#if ($debug){
									$mSugaName = $arrSugaCare[$row['t01_suga_code1']]['nm'];
								#}else{
								#	$mSugaName = GetSugaName($con2, $mCode, $row['t01_suga_code1'], $row['t01_sugup_date']);
								#}
							}else{
								#if ($debug){
									$suga_info = $arrSugaVou[$row['t01_mkind'].'_'.$row['t01_suga_code1']];
									$mSugaName = $suga_info['nm'];
								#}else{
								#	$suga_info = $conn->_suga_info(array('code'=>$mCode,'kind'=>$row['t01_mkind'],'suga'=>$row['t01_suga_code1'],'date'=>$myF->dateStyle($row['t01_sugup_date'])));
								#	$mSugaName = $suga_info[0];
								#}
							}

							if ($row['t01_suga_over'] > 0){
								$Egubun = 'Y';
							}else{
								$Egubun = 'N';
							}

							if ($row['t01_suga_night'] > 0){
								$Ngubun = 'Y';
							}else{
								$Ngubun = 'N';
							}

							$rowData[$index]['svcSubCode']	= $row['t01_svc_subcode'];
							$rowData[$index]['date']		= $row['t01_sugup_date'];
							$rowData[$index]['FmTime']		= $row['t01_sugup_fmtime'];
							$rowData[$index]['ToTime']		= $row['t01_sugup_totime'];
							$rowData[$index]['yoy1']		= $ed->en($row['t01_mem_cd1']);
							$rowData[$index]['yoy2']		= $ed->en($row['t01_mem_cd2']);
							$rowData[$index]['yoy3']		= $row['t01_yoyangsa_id3'];
							$rowData[$index]['yoy4']		= $row['t01_yoyangsa_id4'];
							$rowData[$index]['yoy5']		= $row['t01_yoyangsa_id5'];
							$rowData[$index]['sugaCode']	= $row['t01_suga_code1'];
							$rowData[$index]['bipay']   	= $row['t01_bipay_umu'];

							$subjectParam = array('svcSubCode'		=> $row['t01_svc_subcode'],
								                  'FmTime'			=> $row['t01_sugup_fmtime'],
												  'ToTime'			=> $row['t01_sugup_totime'],
												  'yoy1'			=> $ed->en($row['t01_mem_cd1']),
												  'yoy2'			=> $ed->en($row['t01_mem_cd2']),
												  'memInfo1'		=> $memInfo[$row['t01_mem_cd1']],
												  'memInfo2'		=> $memInfo[$row['t01_mem_cd2']],
												  'yoy3'			=> '',
												  'yoy4'			=> '',
												  'yoy5'			=> '',
												  'yoyName1'		=> $row['t01_mem_nm1'],
												  'yoyName2'		=> $row['t01_mem_nm2'],
												  'yoyName3'		=> '',
												  'yoyName4'		=> '',
												  'yoyName5'		=> '',
												  'sugaName'		=> $mSugaName,
												  'statusGbn'		=> $row['t01_status_gbn'],
												  'transYn'			=> $row['t01_trans_yn'],
												  'modifyPos'		=> $row['t01_modify_yn'],
												  'centerCode'		=> $mCode,
												  'centerKind'		=> $mKind,
												  'maxAmt'			=> 0,
												  'maxTime'			=> 0,
												  'sugaAmt'			=> 0,
												  'sugaTIme'		=> 0,
												  'addAmt'			=> 0,
												  'addTime'			=> 0,
												  'clientLvl'		=> '',
												  'sugaCode'		=> $row['t01_suga_code1'],
												  'family_min'		=> $family_min,
												  'family_cnt'		=> $family_cnt,
												  'bath_week_cnt'	=> $bath_week_cnt,
												  'care_limit_cnt'	=> $care_limit_cnt,
												  'bipay_yn'        => $row['t01_bipay_umu'] == 'Y' ? 'Y' : 'N',
												  'be_plan_yn'		=> $row['t01_be_plan_yn'],
												  'svcID'			=> $conn->kind_code($kindList, $row['t01_mkind'], 'id'),
												  'flag1'			=> 'row');
							$subjectTemp = GetSubject($conn, $centerStartDate, $confStartDate, 'Y', $gubun, $subjectPrint == '' ? '0' : '1', $subjectID, $day, $index, 'N', 'N', $today, $iljungDate, $subjectParam, $dt_min, $dt_max, $family_time[$day], $bath_time[$weekindex], $care_day_cnt[$day]);
							$subjectPrint .= $subjectTemp[0];

							$yoyangsaTimePay = $conn->get_time_pay($mCode, $mKind, $row['t01_mem_cd1'], $sugupjaLevel);
							if ($yoyangsaTimePay == ''){
								$yoyangsaTimePay = $row['t01_ysigup'];
							}

							$old_date = $row['t01_sugup_date'].$row['t01_sugup_fmtime'].$row['t01_sugup_totime'];

							$inputParam = array('centerStartDate'	=> $centerStartDate,
												'confStartDate'		=> $confStartDate,
												'kind'              => $row['t01_mkind'],
												'iljungDate'		=> $row['t01_sugup_date'],
												'svcSubCode'		=> $row['t01_svc_subcode'],
												'svcSubCD'			=> $row['t01_svc_subcd'],
												'fmTime'			=> $row['t01_sugup_fmtime'],
												'ttTime'			=> $row['t01_sugup_totime'],
												'procTime'			=> $row['t01_sugup_soyotime'],
												'procTimeStr'		=> $row['t01_sugup_proctime'],
												'togeUmu'			=> $row['t01_toge_umu'],
												'bipayUmu'			=> $row['t01_bipay_umu'],
												'timeDoub'			=> $row['t01_time_doub'],
												'yoy1'				=> $ed->en($row['t01_mem_cd1']),
												'yoy2'				=> $ed->en($row['t01_mem_cd2']),
												'yoy3'				=> '',
												'yoy4'				=> '',
												'yoy5'				=> '',
												'yoyNm1'			=> $row['t01_mem_nm1'],
												'yoyNm2'			=> $row['t01_mem_nm2'],
												'yoyNm3'			=> '',
												'yoyNm4'			=> '',
												'yoyNm5'			=> '',
												'yoyTA1'			=> $yoyangsaTimePay,
												'yoyTA2'			=> '0',
												'yoyTA3'			=> '0',
												'yoyTA4'			=> '0',
												'yoyTA5'			=> '0',
												'sPrice'			=> $row['t01_suga'],
												'ePrice'			=> $row['t01_suga_over'],
												'nPrice'			=> $row['t01_suga_night'],
												'tPrice'			=> $row['t01_suga_tot'],
												'sugaCode'			=> $row['t01_suga_code1'],
												'sugaName'			=> $mSugaName,
												'Egubun'			=> $Egubun,
												'Ngubun'			=> $Ngubun,
												'Etime'				=> $row['t01_e_time'],
							                    'Ntime'				=> $row['t01_n_time'],
												'duplicate'			=> 'N',
												'weekDay'			=> $row['t01_sugup_yoil'],
												'subject'			=> $subjectTemp,
												'use'				=> 'Y',
												'seq'				=> $row['t01_sugup_seq'],
												'sugupja'			=> 'N',
												'statusGbn'			=> $row['t01_status_gbn'],
												'transYn'			=> $row['t01_trans_yn'],
												'carNo'				=> $row['t01_car_no'],
												'sudangYN'			=> $row['t01_ysudang_yn'],
												'sudang'			=> $row['t01_ysudang'],
												'sudangYul1'		=> $row['t01_ysudang_yul1'],
												'sudangYul2'		=> $row['t01_ysudang_yul2'],
												'holiday'			=> $holiday,
												'oldDate'			=> $old_date,
												'modifyPos'			=> $row['t01_modify_pos'],

												'bipay_kind'		=> $row['t01_bipay_kind'],
												'bipay1'			=> $row['t01_bipay1'],
												'bipay2'			=> $row['t01_bipay2'],
												'bipay3'			=> $row['t01_bipay3'],
												'expenseYn'			=> $row['t01_expense_yn'],
												'expensePay'		=> $row['t01_expense_pay'],

												'school_not_cnt'	=> $row['t01_not_school_cnt'],
												'school_not_cost'	=> $row['t01_not_school_cost'],
												'school_cnt'		=> $row['t01_school_cnt'],
												'school_cost'		=> $row['t01_school_cost'],
												'family_cnt'		=> $row['t01_family_cnt'],
												'family_cost'		=> $row['t01_family_cost'],
												'home_in_yn'		=> $row['t01_home_in_yn'],
												'home_in_cost'		=> $row['t01_home_in_cost'],
												'holiday_cost'		=> $row['t01_holiday_cost']);
							$inputString = GetInputString($gubun, $day, $index, $inputParam);
							echo $inputString;

							// 수가총금액
							//$suga_total += $row['t01_suga_tot'];

							$dayIndex[$day]++;
						}
					}

					$index    = $dayIndex[$day];
					$newTime  = date('Hi', mkTime());
					$old_date = '';

					//if (($today > $iljungDate) or ($today == $iljungDate and $_PARAM["fmTime"] < $newTime)){
					//}else{
					if (($centerStartDate == subStr($iljungDate, 0, 6)) or
						($confStartDate <= subStr($iljungDate, 0, 6)) or
						($today < $iljungDate) or
						($today == $iljungDate and ($_PARAM["fmTime"] != null ? $_PARAM["fmTime"] : ($newTime + 1))> $newTime)){

						/*
						if ($_PARAM["weekDay".$j] == "Y"){
							$subject = "Y";
							$tempWeekDay = $j != 0 ? $j : 7;
						}else{
							$subject = "N";
							$tempWeekDay = "";
						}
						*/
						if ($svcInType == 'weekday'){
							// 제공요일
							if ($_PARAM["weekDay".$j] == "Y"){
								$subject = "Y";
								$tempWeekDay = $j != 0 ? $j : 7;
							}else{
								$subject = "N";
								$tempWeekDay = "";
							}
						}else{
							// 제공일자
							if ($svcDate[intval(subStr($iljungDate, 6))] == 'Y'){
								$subject = "Y";
								$tempWeekDay = $j != 0 ? $j : 7;
							}else{
								$subject = "N";
								$tempWeekDay = "";
							}
						}

						$sugaCode = $_PARAM["sugaCode"];

						if ($_PARAM['mKind'] == '0'){
							$sugaCode1 = subStr($sugaCode,0,2);
							$sugaCode2 = subStr($sugaCode,3,2);

							if ($_PARAM['svcSubCode'] != '500'){
								if ($_PARAM['bipayUmu'] != 'Y'){
									if (($tempWeekDay == 7 or $holiday == 'Y') and $_PARAM['togeUmu'] != 'Y'){
										$sugaCode = $sugaCode1.'H'.$sugaCode2;
									}else{
										$sugaCode = $sugaCode1.'W'.$sugaCode2;
									}
								}
							}
						}

						if ($sugaCode != $_PARAM["sugaCode"]){
							if ($_PARAM['mKind'] == '0'){
								#if ($debug){
									$sugaName  = $arrSugaCare[$sugaCode]['nm'];
									$sugaValue = $arrSugaCare[$sugaCode]['pay'];
								#}else{
								#	$sugaName  = GetSugaName($con2, $mCode, $sugaCode, $iljungDate);
								#	$sugaValue = GetSugaValue($con2, $mCode, $sugaCode, $iljungDate);
								#}
							}else{
								#if ($debug){
									$suga_info = $arrSugaVou[$_PARAM['mKind'].'_'.$_PARAM['sugaCode']];
									$mSugaName = $suga_info['nm'];
									$sugaValue = $suga_info['pay'];
								#}else{
								#	$suga_info = $conn->_suga_info(array('code'=>$mCode,'kind'=>$_PARAM['mKind'],'suga'=>$_PARAM['sugaCode'],'date'=>$myF->dateStyle($iljungDate)));
								#	$mSugaName = $suga_info[0];
								#	$sugaValue = $suga_info[2];
								#}
							}

							// 270분 이상
							if (substr($sugaCode, 4, 1) == '9'){
								$tempFH = subStr($_PARAM['fmTime'],0,2);
								$tempFM = subStr($_PARAM['fmTime'],2,2);
								$tempTH = subStr($_PARAM['ttTime'],0,2);
								$tempTM = subStr($_PARAM['ttTime'],2,2);

								if ($tempFH > $tempTH) $tempTH += 24;

								$procTime = ($tempTH * 60 + $tempTM) - ($tempFH * 60 + $tempFM);

								if ($procTime > 510) $procTime = 510;

								$tempL = $myF->cutOff($procTime, 30) / 30;
								$tempK = 0;
								$temp_first = false;

								$sugaPrice = 0;
								$tempIndex = 0;

								while(1){
									if ($tempL >= 8){
										$tempK = 8;
									}else if ($tempL == 0 || $tempK == 0){
										break;
									}else{
										$tempK = $tempL % 8;
									}
									$tempL = $tempL - $tempK;

									if (!$temp_first){
										$tempL = $tempL - 1; // 4시간후 30분을 뺀다.
										$temp_first = true;
									}

									#if ($debug){
										$tempValue[$tempIndex] = $arrSugaCare[substr($sugaCode, 0, 4).$tempK]['pay'];
									#}else{
									#	$tempValue[$tempIndex] = GetSugaValue($con2, $mCode, substr($sugaCode, 0, 4).$tempK, $iljungDate);
									#}
									$tempTime[$tempIndex]  = $tempK;

									$sugaPrice += $tempValue[$tempIndex];

									$tempIndex ++;
								}

								$sPrice = $sugaPrice;
								$ePrice = 0;
								$nPrice = 0;
								$tPrice = $sugaPrice;
							}else{
								$sPrice = $sugaValue;
								$ePrice = $_PARAM['ePrice'];
								$nPrice = $_PARAM['nPrice'];
								$tPrice = $_PARAM['tPrice'];

								if ($holiday == 'Y'){
									if ($sPrice > $tPrice){
										// 수가가 수가총합보다 크면...
										$tPrice = $sPrice;
									}

									$ePrice = 0;
									$nPrice = 0;
								}else{
									if ($_PARAM['Egubun'] == 'Y') $ePrice = $sPrice * 0.2;
									if ($_PARAM['Ngubun'] == 'Y') $nPrice = $sPrice * 0.3;

									$ePrice = $ePrice - ($ePrice % 10);
									$nPrice = $nPrice - ($nPrice % 10);

									$tPrice = $sPrice + $ePrice + $nPrice;
								}
							}
						}else{
							$sugaName = $_PARAM["sugaName"];

							if ($_PARAM['svcSubCode'] > 20 && $_PARAM['svcSubCode'] < 40){
								/***********************************************************

									산모신생아 토요일은 13시까지 입력한다.

								***********************************************************/
								if ($_PARAM['svcSubCode'] == 23){
									if ($j == 6){
										$_PARAM['ttTime'] = __VOU_BABY_TO_TIME2__;
									}else{
										$_PARAM['ttTime'] = __VOU_BABY_TO_TIME1__;
									}
								}
								/***********************************************************/

								//바우처 및 기타유료
								$sPrice = $_PARAM['sPrice'];
								$ePrice = 0;
								$nPrice = 0;
								$tPrice = $_PARAM['tPrice'];
							}else{
								if ($svcId == 24){
									/**************************************************

										장애활동지원

									**************************************************/
									$sPrice = $_PARAM['sPrice'];
									$ePrice = 0;
									$nPrice = 0;
									$tPrice = $_PARAM['tPrice'];

									if ($holiday == 'Y'){
										$tempFH = subStr($_PARAM['fmTime'],0,2);
										$tempFM = subStr($_PARAM['fmTime'],2,2);
										$tempTH = subStr($_PARAM['ttTime'],0,2);
										$tempTM = subStr($_PARAM['ttTime'],2,2);

										if ($tempFH > $tempTH) $tempTH += 24;

										$procTime = (($tempTH * 60 + $tempTM) - ($tempFH * 60 + $tempFM)) / 60;

										/**************************************************
											연장 최대시간은 4시간으로 제한한다.
										**************************************************/
										$prolongTime = $procTime; //연장시간
										if ($prolongTime > 4) $prolongTime = 4;

										$stndTime = $procTime - $prolongTime; //기본시간

										$tPrice = $stndTime * $arrSugaDis[$_PARAM["sugaCode"]]['cost'] + $prolongTime  * $arrSugaDis[$_PARAM["sugaCode"]]['holiday_cost'];
									}
								}else{
									/**************************************************

										방문재가

									**************************************************/
									if ($holiday == 'Y'){
										$sPrice = $_PARAM['sPrice'];
										$ePrice = 0;
										$nPrice = 0;
										$tPrice = $_PARAM['sPrice'];
									}else{
										$sPrice = $_PARAM['sPrice'];
										$ePrice = $_PARAM['ePrice'];
										$nPrice = $_PARAM['nPrice'];
										$tPrice = $_PARAM['tPrice'];
									}
								}
							}
						}

						$duplicate = 'N';
						$sugupja   = 'N';

						// 중복확인
						if (sizeOf($rowData) > 0){
							$rowCount = sizeOf($rowData);
						}else{
							$rowCount = 1;
						}
						for($k=1; $k<=$rowCount; $k++){
							for($t=1; $t<=2; $t++){
								if ($_PARAM['yoy'.$t] != ''){
									if (ceil($_PARAM['fmTime']) > ceil($_PARAM['ttTime'])){
										$ToTime = ceil($_PARAM['ttTime']) + 2400;
									}else{
										$ToTime = $_PARAM['ttTime'];
									}

									$temp_yoy_iljung_count = sizeof($temp_yoy_iljung[$t]);

									for($l=0; $l<$temp_yoy_iljung_count; $l++){
										if ($temp_yoy_iljung[$t][$l] == $iljungDate){
											$sugupja = 'Y';
											break;
										}
									}
								}

								#####################################################
								#
								# 중복확인
								#
								#####################################################
								if (sizeOf($rowData) > 0){
									if ($rowData[$k]['date'] == $iljungDate){

										#############################################################################################################################################
										#
										$tmpCheckToTimeFrom = intval(substr($rowData[$k]['FmTime'],0,2)) * 60 + intval(substr($rowData[$k]['FmTime'],2,2));
										$tmpCheckToTimeTo   = intval(substr($rowData[$k]['ToTime'],0,2)) * 60 + intval(substr($rowData[$k]['ToTime'],2,2));
										$newCheckFmTImeFrom = intval(substr($_PARAM["fmTime"],0,2)) * 60 + intval(substr($_PARAM["fmTime"],2,2));
										$newCheckFmTImeTo   = intval(substr($_PARAM["ttTime"],0,2)) * 60 + intval(substr($_PARAM["ttTime"],2,2));

										//echo $newCheckFmTImeFrom.'~'.$newCheckFmTImeTo.' / '.$tmpCheckToTimeFrom.'~'.$tmpCheckToTimeTo.'<br>';

										//if ($rowData[$k]['FmTime'] >= $_PARAM["fmTime"] && $rowData[$k]['ToTime'] > $_PARAM["fmTime"] && $rowData[$k]['FmTime'] < $_PARAM["ttTime"]){
										if (($newCheckFmTImeFrom > $tmpCheckToTimeFrom && $newCheckFmTImeFrom < $tmpCheckToTimeTo) ||
											($newCheckFmTImeTo   > $tmpCheckToTimeFrom && $newCheckFmTImeTo   < $tmpCheckToTimeTo) ||
											($newCheckFmTImeFrom > $tmpCheckToTimeFrom && $newCheckFmTImeFrom < $tmpCheckToTimeTo)){
											#######################################################################
											# 간호는 시간중복을 처리하지 않는다.
											#######################################################################
											if ($_PARAM['svcSubCode'] == '800'){
												if ($rowData[$k]['svcSubCode'] == '800') $duplicate = 'Y';		//간호가 중복된경우
												if ($_PARAM['yoy1'] == $rowData[$k]['yoy1']) $duplicate = 'Y';	//담당요양보호사가 중복된경우
											}else if ($_PARAM['svcSubCode'] == '200'){
												$duplicate = 'Y';
											}else if ($_PARAM['svcSubCode'] == '500'){
												$duplicate = 'Y';
											}else{
												#######################################################################
												# 그외 같은 서비스 인경우 시간 중복만 체크한다.
												#######################################################################
												if ($_PARAM['svcSubCode'] == $rowData[$k]['svcSubCode']){
													$duplicate = 'Y';
												}
											}
										}else if ($_PARAM['svcSubCode'] == '200' && $rowData[$k]['svcSubCode'] == '200' && $_PARAM['bipayUmu'] != 'Y'){
											#######################################################################
											# 요양
											# - 전일정과 2시간 이상의 시간차이를 확인한다.
											# - 다만 비급여인경우는 2시간차이를 확인하지 않는다.
											#######################################################################
											if ($svcId == 11){
												if ($duplicate != 'Y'){
													if ($rowData[$k]['bipay'] != 'Y'){
														$tmpCheckToTimeFrom = intval(substr($rowData[$k]['FmTime'],0,2)) * 60 + intval(substr($rowData[$k]['FmTime'],2,2)) - __CHECK_ADD_TIME__;
														$tmpCheckToTimeTo   = intval(substr($rowData[$k]['ToTime'],0,2)) * 60 + intval(substr($rowData[$k]['ToTime'],2,2)) + __CHECK_ADD_TIME__;
														$newCheckFmTImeFrom = intval(substr($_PARAM["fmTime"],0,2)) * 60 + intval(substr($_PARAM["fmTime"],2,2));
														$newCheckFmTImeTo   = intval(substr($_PARAM["ttTime"],0,2)) * 60 + intval(substr($_PARAM["ttTime"],2,2));

														if (($newCheckFmTImeFrom > $tmpCheckToTimeFrom && $newCheckFmTImeFrom < $tmpCheckToTimeTo) ||
															($newCheckFmTImeTo > $tmpCheckToTimeFrom && $newCheckFmTImeTo < $tmpCheckToTimeTo)){
															$duplicate = 'O';
														}
													}
												}
											}else{
												#######################################################################
												# 장애활동보조
												# - 전일정과 1시간 이상의 시간차이를 확인한ㄷ.
												#######################################################################
												/*
												if ($duplicate != 'Y'){
													$tmpCheckToTimeFrom = intval(substr($rowData[$k]['FmTime'],0,2)) * 60 + intval(substr($rowData[$k]['FmTime'],2,2)) - __CHECK_HOUR__;
													$tmpCheckToTimeTo   = intval(substr($rowData[$k]['ToTime'],0,2)) * 60 + intval(substr($rowData[$k]['ToTime'],2,2)) + __CHECK_HOUR__;
													$newCheckFmTImeFrom = intval(substr($_PARAM["fmTime"],0,2)) * 60 + intval(substr($_PARAM["fmTime"],2,2));
													$newCheckFmTImeTo   = intval(substr($_PARAM["ttTime"],0,2)) * 60 + intval(substr($_PARAM["ttTime"],2,2));

													if (($newCheckFmTImeFrom > $tmpCheckToTimeFrom && $newCheckFmTImeFrom < $tmpCheckToTimeTo) ||
														($newCheckFmTImeTo > $tmpCheckToTimeFrom && $newCheckFmTImeTo < $tmpCheckToTimeTo)){
														$duplicate = 'OVER_HOUR';
													}
												}
												*/
											}
										}
										#
										#############################################################################################################################################
									}
								}

								if ($_PARAM['svcSubCode'] == 23 && ($holiday == 'Y' || $j == 0)){
									#######################################################################
									# 산모신생아
									# - 일요일 및 휴일은 등록 불가하다.
									#######################################################################
									//$duplicate = 'VOU_BABY_HOLIDAY';
								}
							}
						}

						$subjectParam = array('svcSubCode'		=> $_PARAM['svcSubCode'],
							                  'FmTime'			=> $_PARAM["fmTime"],
											  'ToTime'			=> $_PARAM["ttTime"],
											  'yoy1'			=> $_PARAM['yoy1'],
											  'yoy2'			=> $_PARAM['yoy2'],
											  'memInfo1'		=> $memInfo[$memCD[1]],
											  'memInfo2'		=> $memInfo[$memCD[2]],
											  'yoy3'			=> '',
											  'yoy4'			=> '',
											  'yoy5'			=> '',
											  'yoyName1'		=> $_PARAM['yoyNm1'],
											  'yoyName2'		=> $_PARAM['yoyNm2'],
											  'yoyName3'		=> '',
											  'yoyName4'		=> '',
											  'yoyName5'		=> '',
											  'sugaName'		=> $sugaName,
											  'statusGbn'		=> '9',
											  'transYn'			=> 'N',
											  'modifyPos'		=> '',
											  'centerCode'		=> $mCode,
											  'centerKind'		=> $mKind,
											  'maxAmt'			=> $max_amount,
											  'maxTime'			=> $max_times,
											  'sugaAmt'			=> $suga_total,
											  'sugaTime'		=> $suga_times,
											  'addAmt'			=> $tPrice,
											  'addTime'			=> $procTime,
											  'clientLvl'		=> $client_lvl,
											  'sugaCode'		=> $_PARAM["sugaCode"],
											  'family_min'		=> $family_min,
											  'family_cnt'		=> $family_cnt,
											  'bath_week_cnt'	=> $bath_week_cnt,
											  'care_limit_cnt'	=> $care_limit_cnt,
											  'bipay_yn'		=> $_PARAM['bipayUmu'] == 'Y' ? 'Y' : 'N',
											  'be_plan_yn'		=> 'Y',
											  'svcID'			=> $svcId,
											  'flag1'			=> 'param');
						$subjectTemp = GetSubject($conn, $centerStartDate, $confStartDate, $subject, $gubun, $subjectPrint == '' ? '0' : '1', $subjectID, $day, $index, $duplicate, $sugupja, $today, $iljungDate, $subjectParam, $dt_from, $dt_to, $family_time[$day], $bath_time[$weekindex], $care_day_cnt[$day]);
						$subject     = $subjectTemp[0]; //제목
						$suga_total  = $subjectTemp[2]; //총금액
						$para_flag   = $subjectTemp[3]; //파라메타 플래그
						$suga_times  = $subjectTemp[4]; //총시간
						$duplicate   = $subjectTemp[5]; //중복구분

						if (empty($subject)) $para_flag = '';

						// 동거가족 하루 허용시간을 넘으면 경고한다.
						if ($_PARAM['svcSubCode'] == '200'){
							if ($family_time[$day] > $family_min) $duplicate = 'OVER';
						}

						$family_days = GetFamilyDays($family_time, $tmp_family_time);

						if (substr($_PARAM["sugaCode"],0,4) == __FAMILY_SUGA_CD__){
							if ($family_days > $family_cnt) $duplicate = 'OVER_DAY';
						}


						if ($svcId > 30){
							/*********************************************************
								기타유료는 한도를 체크하지 않는다.
							*********************************************************/
						}else{
							// 수가 총금액이 한도금액보다 넘으면 중복처리한다.
							//if ($max_amount < $suga_total) $duplicate = 'Y';
							if ($max_amount < $suga_total && $para_flag == 'param') $duplicate = 'OVER_LIMIT';

							if ($max_times > 0){
								if ($max_times < $suga_times && $para_flag == 'param') $duplicate = 'OVER_LIMIT';
							}
						}

						if ($_PARAM["sugaCode"] != ""){
							//if ($today <= $iljungDate){
							if (($centerStartDate == subStr($iljungDate, 0, 6)) or ($today <= $iljungDate)){
								if ($subject != ""){
									$mUse = "Y";
								}else{
									$mUse = "N";
								}
							}else{
								$mUse = "N";
							}
							$dayIndex[$day]++;
						}else{
							$mUse = "N";
						}

						if ($mUse == "N"){
							$subject  = "<div id='".$subjectID."_".$index."' style='display:; width:108px;'></div>";
							$subject .= "<div id='checkDuplicate_".$day."_".$index."' style='display:none;'>중복</div>";
						}

						if ($duplicate == 'OVER_DAY'){
							$subject  = "<div id='".$subjectID."_".$index."' style='display:; width:108px;'></div>";
						}

						if ($mUse == 'Y'){
							$eTime = $_PARAM['Etime'];
							$nTime = $_PARAM['Ntime'];
							$visitSudangCheck = $_PARAM['visitSudangCheck'];
							$visitSudang = $_PARAM['visitSudang'];
							$sudangYul1 = $_PARAM['sudangYul1'];
							$sudangYul2 = $_PARAM['sudangYul2'];
						}else{
							$eTime = 0;
							$nTime = 0;
							$visitSudangCheck = 'N';
							$visitSudang = 0;
							$sudangYul1 = 0;
							$sudangYul2 = 0;
							$sPrice = 0;
							$ePrice = 0;
							$nPrice = 0;
							$tPrice = 0;
						}

						if ($_PARAM['svcSubCode'] == '200' and $_PARAM['procTime'] == '0'){
							// 요양중 수행시간이 없을 경우 수행시간을 계산한다.
							$tempFH = subStr($_PARAM['fmTime'],0,2);
							$tempFM = subStr($_PARAM['fmTime'],2,2);
							$tempTH = subStr($_PARAM['ttTime'],0,2);
							$tempTM = subStr($_PARAM['ttTime'],2,2);

							if ($tempFH > $tempTH) $tempTH += 24;


							$procTime = ($tempTH * 60 + $tempTM) - ($tempFH * 60 - $tempFM);
						}else{
							$procTime = $_PARAM['procTime'];

							// 목욕일 경우 수행시간을 계산한다.
							if ($procTime == 'K' or $procTime == 'F'){
								$tempFH = subStr($_PARAM['fmTime'],0,2);
								$tempFM = subStr($_PARAM['fmTime'],2,2);
								$tempTH = subStr($_PARAM['ttTime'],0,2);
								$tempTM = subStr($_PARAM['ttTime'],2,2);

								if ($tempFH > $tempTH) $tempTH += 24;

								$procTime = ($tempTH * 60 + $tempTM) - ($tempFH * 60 + $tempFM);
							}
						}

						// 2시간이상 간격이 아니면 중복처리한다.
						if ($duplicate == 'O') $duplicate = 'Y';

						// 하루허용시간을 초과하면 중복처리한다.
						if ($duplicate == 'OVER') $duplicate = 'Y';

						// 한달에 등록가능한 일수를 초과하면 중복처리한다.
						if ($duplicate == 'OVER_DAY') $duplicate = 'Y';

						// 일 요양 등록 횟수 초과시 중복처리한다.
						if ($duplicate == 'OVER_CARE') $duplicate = 'Y';

						// 장애활동보조 전일정과 1시간이상 간격이 아니면 중복처리한다.
						if ($duplicate == 'OVER_HOUR') $duplicate = 'Y';

						// 산모신생아
						if ($duplicate == 'VOU_BABY_HOLIDAY') $duplicate = 'Y';

						// 목중초과
						if ($duplicate == 'OVER_BATH') $duplicate = 'Y';

						// 한도 초과
						if ($_PARAM['mKind'] != '0'){
							if ($duplicate == 'OVER_LIMIT') $duplicate = 'Y';
						}

						$inputParam = array('centerStartDate'	=> $centerStartDate,
											'confStartDate'		=> $confStartDate,
											'kind'              => $_PARAM['mKind'],
											'iljungDate'		=> $iljungDate,
											'svcSubCode'		=> $_PARAM['svcSubCode'],
											'svcSubCD'			=> $_PARAM['svcSubCD'],
											'fmTime'			=> $_PARAM['fmTime'],
											'ttTime'			=> $_PARAM['ttTime'],
											'procTime'			=> $procTime,
											'procTimeStr'		=> $_PARAM['procTime'],
											'togeUmu'			=> $_PARAM['togeUmu'],
											'bipayUmu'			=> $_PARAM['bipayUmu'],
											'timeDoub'			=> $_PARAM['timeDoub'],
											'yoy1'				=> $_PARAM['yoy1'],
											'yoy2'				=> $_PARAM['yoy2'],
											'yoy3'				=> $_PARAM['yoy3'],
											'yoy4'				=> $_PARAM['yoy4'],
											'yoy5'				=> $_PARAM['yoy5'],
											'yoyNm1'			=> $_PARAM['yoyNm1'],
											'yoyNm2'			=> $_PARAM['yoyNm2'],
											'yoyNm3'			=> $_PARAM['yoyNm3'],
											'yoyNm4'			=> $_PARAM['yoyNm4'],
											'yoyNm5'			=> $_PARAM['yoyNm5'],
											'yoyTA1'			=> $_PARAM['yoyTA1'],
											'yoyTA2'			=> $_PARAM['yoyTA2'],
											'yoyTA3'			=> $_PARAM['yoyTA3'],
											'yoyTA4'			=> $_PARAM['yoyTA4'],
											'yoyTA5'			=> $_PARAM['yoyTA5'],
											'sPrice'			=> $sPrice,
											'ePrice'			=> $ePrice,
											'nPrice'			=> $nPrice,
											'tPrice'			=> $tPrice,
											'sugaCode'			=> $sugaCode,
											'sugaName'			=> $sugaName,
											'Egubun'			=> $_PARAM['Egubun'],
											'Ngubun'			=> $_PARAM['Ngubun'],
											'Etime'				=> $eTime,
							                'Ntime'				=> $nTime,
											'duplicate'			=> $duplicate,
											'weekDay'			=> $tempWeekDay,
											'subject'			=> $subject,
											'use'				=> $mUse,
											'seq'				=> '0',
											'sugupja'			=> $sugupja,
											'statusGbn'			=> '9',
											'transYn'			=> 'N',
							                'carNo'				=> $_PARAM['carNo'],
											'sudangYN'			=> $visitSudangCheck,
											'sudang'			=> $visitSudang,
											'sudangYul1'		=> $sudangYul1,
											'sudangYul2'		=> $sudangYul2,
											'holiday'			=> $holiday,
											'oldDate'			=> $old_date,
											'modifyPos'			=> 'N',

											'bipay_kind'		=> $_PARAM['bipay_kind'],
											'bipay1'			=> $_PARAM['bipay1'],
											'bipay2'			=> $_PARAM['bipay2'],
											'bipay3'			=> $_PARAM['bipay3'],
											'expenseYn'			=> $_PARAM['expenseYn'],
											'expensePay'		=> $_PARAM['expensePay'],

											'school_not_cnt'	=> $_PARAM['school_not_cnt'],
											'school_not_cost'	=> $_PARAM['school_not_pay'],
											'school_cnt'		=> $_PARAM['school_cnt'],
											'school_cost'		=> $_PARAM['school_pay'],
											'family_cnt'		=> $_PARAM['family_cnt'],
											'family_cost'		=> $_PARAM['family_pay'],
											'home_in_yn'		=> $_PARAM['home_in_yn'],
											'home_in_cost'		=> $_PARAM['home_in_pay'],
											'holiday_cost'		=> $_PARAM['holiday_pay']);
						$inputString = GetInputString($gubun, $day, $index, $inputParam);
						echo $inputString;
					}

					if ($old_date == ''){
						// 수급자 월수급 총금액
						if (substr($sugaCode,0,4) == __FAMILY_SUGA_CD__){
							if ($family_cnt >= $family_days){
								$new_total_suga += $tPrice;
								$new_total_time += $procTime;
							}
						}else if (substr($sugaCode,0,2) == __BATH_SUGA_CD__ ||
								  substr($sugaCode,0,3) == __BATH_VOUCHER__){
							/**************************************************
								중복여부 상관없이 계산한다.
							**************************************************/
							#if ($bath_week_cnt >= $bath_time[$weekindex]){
								$new_total_suga += $tPrice;
								$new_total_time += $procTime;
							#}
						}else{
							$new_total_suga += $tPrice;
							$new_total_time += ($tPrice > 0 ? $procTime : 0);
						}
					}

					$subjectPrint .= $subject;

					$day++;
				}

				echo "</th>";
				echo "<td style='width:108px; text-align:left; vertical-align:top; line-height:1.3em;".($iljungDate == $calYear.$calMonth.$calDay ? 'border:2px solid #0000ff;' : '')."' id='".$subjectID."'>".$subjectPrint."</td>";
			}
			echo "</tr>";
		}
		//echo "<br><br><br>";
		//print_r($iljung[2]);

		$ob_value = ob_get_contents();
		ob_clean();
		echo $ob_value;

		unset($temp_holiday_list);
		unset($temp_yoy_iljung);
		unset($family_time);
		unset($bath_time);
		unset($care_day_cnt);
	?>
	</tbody>
</table>
<div id="addCalendar" style="width:900px; display:;"></div>
<input name="mLastDay"		 type="hidden" value="<?=$lastDay;?>">
<input name="boninYul"		 type="hidden" value="<?=$boninYul;?>">
<input name="new_total_suga" type="hidden" value="<?=$new_total_suga;?>">
<input name="new_total_time" type="hidden" value="<?=$new_total_time;?>">
<input name="suga_total"	 type="hidden" value="<?=$suga_total;?>">
<input name="max_amount"	 type="hidden" value="<?=$max_amount;?>">

<input name="suga_times" type="hidden" value="<?=$suga_times;?>">
<input name="max_times" type="hidden" value="<?=$max_times;?>">

<input name="bath_first_week_cnt" type="hidden" value="<?=$tmp_first_week_cnt;?>">
<input name="bath_last_week_cnt"  type="hidden" value="<?=$tmp_last_week_cnt;?>">
<?
	$conn->row_free();
	$con2->close();

	include_once("../inc/_footer.php");

	$microTime2 = $myF->getMtime();
	$microTime  = $microTime2 - $microTime1;

	//if ($debug) echo 'time : '.$microTime;

	function GetInputString($pGubun, $pDay, $pIndex, $pParam){
		if ($pGubun == 'search' and $pParam['statusGbn'] != '1'){
			$newInputString  = '';
			$newInputString .= '<input id="mUse_'       .$pDay.'_'.$pIndex.'" name="mUse_'       .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['use'].'">';
			$newInputString .= '<input id="mDelete_'    .$pDay.'_'.$pIndex.'" name="mDelete_'    .$pDay.'_'.$pIndex.'" type="hidden" value="N">';
			$newInputString .= '<input id="mDuplicate_' .$pDay.'_'.$pIndex.'" name="mDuplicate_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['duplicate'].'">';
			$newInputString .= '<input name="mYoy1_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy1'].'">';
			$newInputString .= '<input name="mStatusGbn_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['statusGbn'].'">';
		}else{
			if ($pParam['centerStartDate'] == subStr($pParam['iljungDate'], 0, 6) or
				$pParam['confStartDate'] <= subStr($pParam['confStartDate'], 0, 6)){
				$statusGbn = $pParam['statusGbn'];

				if ($statusGbn == '0') $statusGbn = '9';
			}else{
				$statusGbn = $pParam['statusGbn'];
			}
			$newInputString  = '';
			$newInputString .= '<input name="mKind_'      .$pDay.'_'.$pIndex.'" id="mKind_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['kind'].'">';
			$newInputString .= '<input name="mDate_'      .$pDay.'_'.$pIndex.'" id="mDate_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['iljungDate'].'" class="iljungID">';
			$newInputString .= '<input name="mSvcSubCode_'.$pDay.'_'.$pIndex.'" id="mSvcSubCode_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['svcSubCode'].'">';
			$newInputString .= '<input name="mSvcSubCD_'  .$pDay.'_'.$pIndex.'" id="mSvcSubCD_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['svcSubCD'].'">';
			$newInputString .= '<input name="mFmTime_'    .$pDay.'_'.$pIndex.'" id="mFmTime_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['fmTime'].'">';
			$newInputString .= '<input name="mToTime_'    .$pDay.'_'.$pIndex.'" id="mToTime_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['ttTime'].'">';
			$newInputString .= '<input name="mProcTime_'  .$pDay.'_'.$pIndex.'" id="mProcTime_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['procTime'].'">';
			$newInputString .= '<input name="mProcStr_'   .$pDay.'_'.$pIndex.'" id="mProcStr_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['procTimeStr'].'">';
			$newInputString .= '<input name="mTogeUmu_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['togeUmu'].'">';
			$newInputString .= '<input name="mBiPayUmu_'  .$pDay.'_'.$pIndex.'" id="mBiPayUmu_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['bipayUmu'].'">';
			$newInputString .= '<input name="mTimeDoub_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['timeDoub'].'">';
			$newInputString .= '<input name="mYoy1_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy1'].'">';
			$newInputString .= '<input name="mYoy2_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy2'].'">';
			$newInputString .= '<input name="mYoy3_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy3'].'">';
			$newInputString .= '<input name="mYoy4_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy4'].'">';
			$newInputString .= '<input name="mYoy5_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy5'].'">';
			$newInputString .= '<input name="mYoyNm1_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm1'].'">';
			$newInputString .= '<input name="mYoyNm2_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm2'].'">';
			$newInputString .= '<input name="mYoyNm3_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm3'].'">';
			$newInputString .= '<input name="mYoyNm4_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm4'].'">';
			$newInputString .= '<input name="mYoyNm5_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm5'].'">';
			$newInputString .= '<input name="mYoyTA1_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA1'].'">';
			$newInputString .= '<input name="mYoyTA2_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA2'].'">';
			$newInputString .= '<input name="mYoyTA3_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA3'].'">';
			$newInputString .= '<input name="mYoyTA4_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA4'].'">';
			$newInputString .= '<input name="mYoyTA5_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA5'].'">';
			$newInputString .= '<input name="mSValue_'    .$pDay.'_'.$pIndex.'" id="mSValue_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sPrice'].'">';
			$newInputString .= '<input name="mEValue_'    .$pDay.'_'.$pIndex.'" id="mEValue_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['ePrice'].'">';
			$newInputString .= '<input name="mNValue_'    .$pDay.'_'.$pIndex.'" id="mNValue_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['nPrice'].'">';
			$newInputString .= '<input name="mTValue_'    .$pDay.'_'.$pIndex.'" id="mTValue_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['tPrice'].'">';
			$newInputString .= '<input name="mSugaCode_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sugaCode'].'">';
			$newInputString .= '<input name="mSugaName_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sugaName'].'">';
			$newInputString .= '<input name="mEGubun_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['Egubun'].'">';
			$newInputString .= '<input name="mNGubun_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['Ngubun'].'">';
			$newInputString .= '<input name="mETime_'     .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['Etime'].'">';
			$newInputString .= '<input name="mNTime_'     .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['Ntime'].'">';
			$newInputString .= '<input name="mWeekDay_'   .$pDay.'_'.$pIndex.'" id="mWeekDay_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['weekDay'].'">';
			$newInputString .= '<input name="mSubject_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['subject'].'">';

			$newInputString .= '<input name="mUse_'       .$pDay.'_'.$pIndex.'" id="mUse_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['use'].'">';
			$newInputString .= '<input name="mDelete_'    .$pDay.'_'.$pIndex.'" id="mDelete_'   .$pDay.'_'.$pIndex.'" type="hidden" value="N">';
			$newInputString .= '<input name="mDuplicate_' .$pDay.'_'.$pIndex.'" id="mDuplicate_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['duplicate'].'">';

			$newInputString .= '<input name="mSeq_'       .$pDay.'_'.$pIndex.'" id="mSeq_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['seq'].'">';
			$newInputString .= '<input name="mSugupja_'   .$pDay.'_'.$pIndex.'" id="mSugupja_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sugupja'].'">';
			$newInputString .= '<input name="mTrans_'     .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['transYn'].'">';
			$newInputString .= '<input name="mStatusGbn_' .$pDay.'_'.$pIndex.'" id="mStatusGbn_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$statusGbn.'">';
			$newInputString .= '<input name="mCarNo_'     .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['carNo'].'">';
			$newInputString .= '<input name="mSudangYN_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sudangYN'].'">';
			$newInputString .= '<input name="mSudang_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sudang'].'">';
			$newInputString .= '<input name="mSudangYul1_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sudangYul1'].'">';
			$newInputString .= '<input name="mSudangYul2_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sudangYul2'].'">';
			$newInputString .= '<input name="mHoliday_'   .$pDay.'_'.$pIndex.'" id="mHoliday_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['holiday'].'">';
			$newInputString .= '<input name="mOldDate_'   .$pDay.'_'.$pIndex.'" id="mOldDate_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['oldDate'].'">';
			$newInputString .= '<input name="mModifyPos_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['modifyPos'].'">';



			/**************************************************

				비급여 실지 지급 여무 및 실비금액

			**************************************************/
				$newInputString .= '<input name="mBipay1_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['bipay1'].'">'; //비금여금액(요양, 바우처)
				$newInputString .= '<input name="mBipay2_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['bipay2'].'">'; //비금여금액(목욕)
				$newInputString .= '<input name="mBipay3_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['bipay3'].'">'; //비금여금액(간호)

				$newInputString .= '<input name="mExpenseYn_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['expenseYn'].'">';  //실비지급여부
				$newInputString .= '<input name="mExpensePay_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['expensePay'].'">'; //실비지급금액
			/*************************************************/



			/**************************************************

				산모신생아 및 산모유료 추가 단가

			**************************************************/
				$newInputString .= '<input  id="mAddPay_'.$pDay.'_'.$pIndex.'" name="mAddPay_'.$pDay.'_'.$pIndex.'" type="hidden" value="';
				$newInputString .=  'school_not_cnt='	.$pParam['school_not_cnt']; 	//미취학아동수
				$newInputString .= '&school_not_cost='	.$pParam['school_not_cost']; 	//추가단가
				$newInputString .= '&school_cnt='		.$pParam['school_cnt']; 		//취학아동수
				$newInputString .= '&school_cost='		.$pParam['school_cost']; 		//추가단가
				$newInputString .= '&family_cnt='		.$pParam['family_cnt']; 		//동거가족수
				$newInputString .= '&family_cost='		.$pParam['family_cost']; 		//추가단가
				$newInputString .= '&home_in_yn='		.$pParam['home_in_yn']; 		//입주여부
				$newInputString .= '&home_in_cost='		.$pParam['home_in_cost'];		//입주단가
				$newInputString .= '&holiday_cost='		.$pParam['holiday_cost'];		//공/휴일단가
				$newInputString .= '">';
			/*************************************************/




			/**************************************************

				기타

			**************************************************/
				$newInputString .= '<input name="mOther_'.$pDay.'_'.$pIndex.'" type="hidden" value="';
				$newInputString .=  'bipay_kind='.$pParam['bipay_kind']; //비급여구분
				$newInputString .= '">';
			/*************************************************/
		}

		return $newInputString;
	}

	function GetSubject($conn, $pCenterStartDate, $pConfStartDate, $gubun, $pGubun, $topBorder, $subjectID, $pDay, $pIndex, $pDuplicate, $pSugupja, $pToday, $pIljungDate, $pParam, $dt_from, $dt_to, &$family_time, &$bath_time, &$care_day_cnt){
		$fontColor = '#000000';
		$showBtn = 'Y';

		if ($dt_from <= $pIljungDate && $dt_to >= $pIljungDate){
			if (($pCenterStartDate == subStr($pIljungDate, 0, 6) or $pConfStartDate <= subStr($pIljungDate, 0, strLen($pConfStartDate))) && $pParam['statusGbn'] != '1' && $pParam['statusGbn'] != '5' && $pParam['statusGbn'] != 'C'){
				// 계약월인 경우 일자 및 시간 관계없이 수정 및 삭제가 가능하도록 풀어준다.
				// 단 수행중이거나 완료된 일정은 수정 및 삭제가 불가능하다.
				//echo $pParam['FmTime'].'/'.$pParam['ToTime'].'/'.$pParam['statusGbn'];
			}else{
				if ($pToday <= $pIljungDate){
					if ($pToday == $pIljungDate){
						$newTime = date('Hi', mkTime());
						if ($pParam["FmTime"] < $newTime){
							$fontColor = '#777777';
							$showBtn = 'N';
						}
					}
				}else{
					$fontColor = '#777777';
					$showBtn = 'N';
				}
			}
		}else{
			$gubun = 'N';
			$fontColor = '#777777';
			$showBtn = 'N';
		}

		$subject  = '';

		// 초과금액 체크를 해제한다.
		$over_amt = 'N';
		$amt_suga = intval($pParam["sugaAmt"]);

		#if ($pParam['centerKind'] == '0'){
			if ($pParam['svcSubCode'] > '30' && $pParam['svcSubCode'] < '40'){
				/*********************************************************
					유료서비스는 한도를 계산하지 않는다.
				*********************************************************/
			}else{
				if ($pParam['svcID'] != '11'){
					if ($pParam['clientLvl'] != '9' && $pParam['bipay_yn'] != 'Y' && $pParam['flag1'] == 'param'){
						if ($gubun == 'Y'){
							$amt_max  = intval($pParam["maxAmt"]);
							//$amt_suga = intval($pParam["sugaAmt"]) + intval($pParam["addAmt"]);
							$amt_suga += intval($pParam["addAmt"]);

							$time_max  = intval($pParam['maxTime']);

							if ($time_max > 0)
								$time_suga = intval($pParam['sugaTime']) + intval($pParam['addTime']);
						}else{
							//$amt_suga  = intval($pParam["sugaAmt"]);
							if ($time_max > 0)
								$time_suga = intval($pParam['addTime']);
						}

						if (($amt_max < $amt_suga) ||
							($time_max < $time_suga)){
							$pDuplicate = 'Y';
							$over_amt = 'Y';
						}
					}
				}
			}
		#}

		if ($pSugupja == 'Y'){
			$backGroundColor = '#ffffff';
			$displaySugupja  = '';
			$pDuplicate      = 'Y';
		}else{
			$displaySugupja = 'none';
		}

		if ($pDuplicate == 'Y'){
			$backGroundColor = '#ffffff';
			$duplicateDisplay = '';
		}else if ($pDuplicate == 'O'){ // 전일정과 2시간 간격이 아님
			$backGroundColor = '#77fd74';
			$duplicateDisplay = '';
		}else if ($pDuplicate == 'OVER'){ //하루 허용시간 초과
			$backGroundColor = '#ff9844';
			$duplicateDisplay = '';
		}else if ($pDuplicate == 'OVER_BATH'){ //목욕 주간 횟수 초과
			$backGroundColor = '#9cdbf0';
			$duplicateDisplay = '';
		}else if ($pDuplicate == 'OVER_HOUR'){ //장애활동보조
			$backGroundColor = '#77fd74';
			$duplicateDisplay = '';
		}else if ($pDuplicate == 'VOU_BABY_HOLIDAY'){ //산모신생아 휴일등록방지
			$backGroundColor = '#fcfae8';
			$duplicateDisplay = '';
		}else{
			$backGroundColor = '';
			$duplicateDisplay = 'none';
		}

		if ($pGubun == 'search'){
			switch($pParam['statusGbn']){
				case '0': //미수행
					$fontColor = '#ff0000';
					break;
				case '1': //완료
					$fontColor = '#1b8830;';
					break;
				case '5': //수행중
					$fontColor = '#0000ff';
					break;
				case '9': //준비중
					if ($showBtn == 'Y'){
						$fontColor = '#000000';
					}else{
						$fontColor = '#ff0000';
					}
					break;
			}
		}else{
			if ($pParam['statusGbn'] == '1'){
				/*
				  D : 실적관리/일실적 수동입력에서 저장
				  M : 실적관리/월실적 확정처리에서 저장
				  N : 실적관리에서 수정하지 않은데이타
				 */
				if ($pParam['modifyPos'] != 'D'){
					$showBtn = 'N';
				}
			}
		}

		if ($gubun == 'Y'){
			$subject  = "";

			if ($pParam['be_plan_yn'] == 'Y'){
				$subject .= substr($pParam["FmTime"],0,2).":".substr($pParam["FmTime"],2,2)."~";
				$subject .= substr($pParam["ToTime"],0,2).":".substr($pParam["ToTime"],2,2)."<br>";
			}else{
				$subject .= '<span style=\'color=#ff0000;\'>비급여실적</span><br>';
			}

			// 요양사의 활동여부를 조회한다.
			for($i=1; $i<=5; $i++){
				$yoyStat[$i] = '1';
			}

			/*
			if ($pParam["memCD1"] != '') $yoyStat[1] = $conn->get_array("select m02_ygoyong_stat, m02_ytoisail from m02yoyangsa where m02_ccode = '".$pParam["centerCode"]."' and m02_yjumin = '".$pParam["yoy1"]."'");
			if ($pParam["memCD2"] != '') $yoyStat[2] = $conn->get_array("select m02_ygoyong_stat, m02_ytoisail from m02yoyangsa where m02_ccode = '".$pParam["centerCode"]."' and m02_yjumin = '".$pParam["yoy2"]."'");
			for($i=1; $i<=2; $i++){
				if ($yoyStat[$i][0] == '1' || $yoyStat[$i][0] == '2'){
					$cancelLine[$i][1] = '';
					$cancelLine[$i][2] = '';
				}else{
					if ($yoyStat[$i][1] <= $pIljungDate){
						$cancelLine[$i][1] = '<s style=\'font-weight:bold; color:#7608e7;\'>';
						$cancelLine[$i][2] = '</s>';
					}else{
						$cancelLine[$i][1] = '';
						$cancelLine[$i][2] = '';
					}
				}
			}
			*/

			$yoyStat[1] = $pParam['memInfo1'];
			$yoyStat[2] = $pParam['memInfo2'];

			for($i=1; $i<=2; $i++){
				if ($yoyStat[$i]['stat'] == '1' || $yoyStat[$i]['stat'] == '2'){
					$cancelLine[$i][1] = '';
					$cancelLine[$i][2] = '';
				}else{
					if ($yoyStat[$i]['quit_dt'] <= $pIljungDate){
						$cancelLine[$i][1] = '<s style=\'font-weight:bold; color:#7608e7;\'>';
						$cancelLine[$i][2] = '</s>';
					}else{
						$cancelLine[$i][1] = '';
						$cancelLine[$i][2] = '';
					}
				}
			}


			$subject .= $pParam["yoyName1"] != "" ? $cancelLine[1][1].$pParam["yoyName1"].$cancelLine[1][2]."," : "";
			$subject .= $pParam["yoyName2"] != "" ? $cancelLine[2][1].$pParam["yoyName2"].$cancelLine[2][2]."," : "";
			$subject  = mb_substr($subject, 0, mb_strlen($subject,"UTF-8") - 1, "UTF-8")."<br>";
			$subject .= $pParam["sugaName"];

			if ($pParam['bipay_yn'] == 'Y'){
				$subject .= '<span style=\'font-size:8pt; color:#ff0000;\'>[비]</span>';
			}
		}else{
			$subject = '';
		}



		if ($subject != ""){
			// 동거가족 일별 총시간을 저장한다.
			if (substr($pParam["sugaCode"], 0, 4) == __FAMILY_SUGA_CD__){
				$tempFH = intval(subStr($pParam['FmTime'],0,2));
				$tempFM = intval(subStr($pParam['FmTime'],2,2));
				$tempTH = intval(subStr($pParam['ToTime'],0,2));
				$tempTM = intval(subStr($pParam['ToTime'],2,2));

				if ($tempFH > $tempTH) $tempTH += 24;

				$procTime = ($tempTH * 60 + $tempTM) - ($tempFH * 60 + $tempFM);

				if (intval($pParam['family_min']) > 0 && $pParam['flag1'] == 'param'){
					$family_time += $procTime;

					if ($family_time > $pParam['family_min']){
						$pDuplicate = 'OVER';
						$backGroundColor = '#ff9844';
						$duplicateDisplay = '';
					}
				}
			}

			// 일별 요양 등록 횟수
			if ($pParam['svcSubCode'] == '200' && substr($pParam["sugaCode"], 0, 4) != __FAMILY_SUGA_CD__){
				$care_day_cnt ++;
			}

			// 일별 요양 등록 횟수 초과
			if ($pParam['bipay_yn'] != 'Y' && $family_time > 0 && $care_day_cnt >= $pParam['care_limit_cnt']){
				$pDuplicate = 'OVER_CARE';
				$backGroundColor = '#f2f2f2';
				$duplicateDisplay = '';
			}

			// 목욕 주간 횟수를 저장한다.
			if ((substr($pParam["sugaCode"],0,2) == __BATH_SUGA_CD__ ||
				 substr($pParam["sugaCode"],0,3) == __BATH_VOUCHER__) &&
				($pParam['bipay_yn'] != 'Y')){

				//echo $bath_time.'/'.$pParam["bath_week_cnt"].'/'.$pParam['svcSubCode'].':'.substr($pParam["sugaCode"],0,2).'/'.__BATH_SUGA_CD__.' | '.substr($pParam["sugaCode"],0,3).'/'.__BATH_VOUCHER__.'<br>';

				$bath_time ++;

				/**************************************************
					한도를 초과했으면
				**************************************************/
				if ($over_amt != 'Y'){
					/**************************************************
						주간목욕회수를 초과했으면
					**************************************************/
					if ($bath_time > $pParam["bath_week_cnt"] && $pParam['flag1'] == 'param'){
						$pDuplicate = 'OVER_BATH';
						$backGroundColor = '#9cdbf0';
						$duplicateDisplay = '';
					}
				}
			}

			$tempSubject = $subject;
			$subject  = '';
			$subject .= "<div class='svcSubject".$pParam['svcID']."' style='display:; background-color:".$backGroundColor."; width:100%; ".($pDuplicate == 'Y' ? 'margin-top:0; margin-bottom:0; border:2px solid #ff0000;' : 'border-top:'.$topBorder.'px dotted #cccccc;')."' id='".$subjectID."_".$pIndex."'>";
			$subject .= "<table>";
			$subject .= "<tr>";
			$subject .= "<td class='noborder' style='width:100%; text-align:left; vertical-align:top; line-height:1.3em; border:none;'>";
			#$subject .= '<div style=\'position:absolute; width:100%; height:100%; text-align:right;\'></div>';
			$subject .= "<div style='position:absolute; width:100%; height:100%;'>";
			$subject .= "<div style='position:absolute; top:1px; left:80px;'>";

			if ($pGubun == 'reg'){
				//if ($pToday <= $pIljungDate and $pParam['statusGbn'] == '9' and $pParam['transYn'] == 'N'){
				if ($showBtn == 'Y'){
					$subject .= " <img class='svcSubjectBtn".$pParam['svcID']."' src='../image/btn_edit.png' style='cursor:pointer;' onClick='_modifyDiary(".$pDay.",".$pIndex.");'>";
					$subject .= " <img class='svcSubjectBtn".$pParam['svcID']."' src='../image/btn_del.png' style='cursor:pointer;' onClick='_clearDiary(".$pDay.",".$pIndex.");'>";

					switch($pParam['svcSubCode']){
						case '500':
							$subject .= '<p style=\'padding-top:2px; padding-left:5px;\'><img src=\'../image/icon_bath.png\'></p>';
							break;
						case '800':
							$subject .= '<p style=\'padding-top:2px; padding-left:5px;\'><img src=\'../image/icon_nurs.png\'></p>';
							break;
						default:
					}
				}
				//}
			}

			$subject .= "        </div>";
			$subject .= "    </div>";
			$subject .= "    <div style='color:".$fontColor.";'>".$tempSubject."</div>";

			if ($over_amt == 'Y'){
				$subject .= "<div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:; cursor:pointer;' onclick='_chk_iljung(".$pDay.",".$pIndex.");'><span style='color:#ff0000; font-weight:bold;'>한도가 초과되었습니다.저장안됨</span></div>";
				$subject .= "<div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'></div>";
			}else{
				switch($pDuplicate){
					case 'O': //전일정과의 시간차가 2시간 이내
						$subject .= "<div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay.";'><span style='color:#ff0000; font-weight:bold;'>전일정과 2시간의 간격이 필요합니다.</span></div>";
						$subject .= "<div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'></div>";
						break;
					case 'OVER': //하루 허용시간 초과
						$subject .= "<div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay.";'><span style='color:#ff0000; font-weight:bold;'>하루허용 시간을 초과하였습니다.</span></div>";
						$subject .= "<div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'></div>";
						break;
					case 'OVER_BATH': //하루 허용시간 초과
						$subject .= "<div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay.";'><span style='color:#ff0000; font-weight:bold;'>주간별 목욕 가능횟수를 초과하였습니다.</span></div>";
						$subject .= "<div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'></div>";
						break;
					case 'OVER_CARE': //하루 요양 등록 횟수 초과
						$subject .= "<div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay.";'><span style='color:#ff0000; font-weight:bold;'>동거수가가 등록된 일은 다른 방문용양일정을 등록할 수 없습니다.</span></div>";
						$subject .= "<div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'></div>";
						break;
					case 'OVER_HOUR': //장애활동보조 전일과 시간차 1시간
						$subject .= "<div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay.";'><span style='color:#ff0000; font-weight:bold;'>전일정과 1시간의 간격이 필요합니다.</span></div>";
						$subject .= "<div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'></div>";
						break;
					case 'VOU_BABY_HOLIDAY': //산모신생아 휴일 등록 방지
						$subject .= "<div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay.";'><span style='color:#ff0000; font-weight:bold;'>산모신생아는 휴일 및 일요일에 등록할 수 없습니다.</span></div>";
						$subject .= "<div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'></div>";
						break;
					default:
						$subject .= "<div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay."; cursor:pointer;' onclick='_chk_iljung(".$pDay.",".$pIndex.");'><span style='color:#ff0000; font-weight:bold;'>일정이 중복되었습니다.</span></div>";
						$subject .= "<div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'></div>";
				}
			}

			$subject .= "</td>";
			$subject .= "</tr>";
			$subject .= "</table>";
			$subject .= "</div>";
		}

		$arraySubject[0] = $subject;
		$arraySubject[1] = $showBtn;
		$arraySubject[2] = $amt_suga;
		$arraySubject[3] = $pParam['flag1'];
		$arraySubject[4] = $time_suga;
		$arraySubject[5] = $pDuplicate;

		return $arraySubject;
	}

	function GetFamilyDays($list, $r_list){
		$list_cnt = sizeof($list);
		$cnt = 0;

		for($i=0; $i<$list_cnt; $i++){
			if ($list[$i] > 0 || $r_list[$i] > 0) $cnt ++;
		}

		return $cnt;
	}
?>