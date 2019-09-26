<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if ($_SESSION['userLevel'] == 'C' || $_SESSION['userSmart'] != 'Y'){
		$lbEdit	= true;
	}else{
		$lbEdit	= false;
	}

	$today	= Date('Ymd');

	$code	= $_POST['code'];
	$jumin	= $_POST['jumin'];
	$svcCd	= $_POST['svcCd'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$type	= $_POST['type'];
	$sr		= $_POST['sr'];

	parse_str($_POST['para'],$para);

	if ($svcCd == '0'){
		//과거일정 등록 가능여부
		$pastDateYn = false;
	}else{
		$pastDateYn = true;
	}

	if ($IsLongtermCng2016) $pastDateYn = true;

	if ($svcCd == 'S' || $svcCd == 'R'){
		$lbEdit	= true;
	}

	if ($code == '31141000184'){
		//예사랑
		$pastDateYn = true;
	}

	if ($gDomain == 'dolvoin.net'){
		//돌보인 과거일정 막기를 해제함.
		$pastDateYn = true;
		$lbTodayPlanReg = false;
	}

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	//주야간보호
	if ($para['DayAndNight'] == 'Y'){
		//수급자등급
		$sql = 'SELECT	MIN(level)
				FROM	client_his_lvl
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		svc_cd	= \''.$svcCd.'\'
				AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$year.$month.'\'';

		$svcLvl = $conn->get_data($sql);

		//비급여내역
		$sql = 'SELECT	date
				,		time
				,		seq
				,		code
				,		amt
				FROM	dan_nonpayment_iljung
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		LEFT(date,6) = \''.$year.$month.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$nonpayment[$row['date']][$row['time']][$row['seq']][] = Array('code'=>$row['code'],'amt'=>$row['amt']);
		}

		$conn->row_free();

		$svcCd = '5';
	}

	//마감처리여부
	$ynClose = $conn->_isCloseResult($code, $year.$month);

	//주차 계산
	$liLastDay	= $myF->lastDay($year, $month);
	$liWeekGbn	= 0;

	for($i=1; $i<=$liLastDay; $i++){
		$lsDt	= $year.$month.($i < 10 ? '0' : '').$i;

		if (Date('w',StrToTime($lsDt)) == 1){
			$liWeekGbn ++;
		}

		//일자별 주차를 입력
		$arrWeekGbn[$lsDt]	= $liWeekGbn;
	}

	//수가리스트
	if ($svcCd == '0'){
		$sql = 'select m01_mcode2 as code
				,      m01_suga_cont as name
				,      m01_suga_value as cost
				  from m01suga
				 where m01_mcode  = \'goodeos\'
				   and left(m01_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
				   and left(m01_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'
				 union all
				select m11_mcode2 as code
				,      m11_suga_cont as name
				,      m11_suga_value as cost
				  from m11suga
				 where m11_mcode  = \'goodeos\'
				   and left(m11_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
				   and left(m11_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'';
	}else if ($svcCd == '6' || $svcCd == 'S' || $svcCd == 'R'){
		$sql = 'SELECT	DISTINCT
						CONCAT(suga_cd,suga_sub) AS code
				,		suga_nm AS name
				,		suga_cost AS cost
				FROM	care_suga
				WHERE	org_no	= \''.$code.'\'
				AND		suga_sr	= \''.$sr.'\'
				AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.substr($lsDt, 0,6).'\'
				AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) >= \''.substr($lsDt, 0,6).'\'
				/*
				AND		LEFT(REPLACE(from_dt,\'-\',\'\'),'.strlen($lsDt).') <= \''.$lsDt.'\'
				AND		LEFT(REPLACE(to_dt,\'-\',\'\'),'.strlen($lsDt).') >= \''.$lsDt.'\'
				*/';
		
		if ($IsCareYoyAddon){
			//공통항목
			$sql .= '
				UNION	ALL
				SELECT	code, name, 0 AS cost
				FROM	care_suga_comm
				WHERE	LEFT(REPLACE(from_dt,\'-\',\'\'),'.strlen($lsDt).') <= \''.$lsDt.'\'
				AND		LEFT(REPLACE(to_dt,\'-\',\'\'),'.strlen($lsDt).') >= \''.$lsDt.'\'';
		}
	}else if ($svcCd == '5'){
		//주야간보호 수가
		$sql = 'SELECT	code
				,		name
				,		cost
				FROM	suga_dan
				WHERE	lv_gbn	= \''.$svcLvl.'\'
				AND		DATE_FORMAT(from_dt	,\'%Y%m\') <= \''.$year.$month.'\'
				AND		DATE_FORMAT(to_dt	,\'%Y%m\') >= \''.$year.$month.'\'';
	}else{
		$sql = 'select service_code as code
				,      service_gbn as name
				,      service_lvl as gbn
				,      service_cost as cost
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_kind = \''.$svcCd.'\'
				   and left(replace(service_from_dt,\'-\',\'\'),'.strlen($lsDt).') <= \''.$lsDt.'\'
				   and left(replace(service_to_dt,\'-\',\'\'),  '.strlen($lsDt).') >= \''.$lsDt.'\'';
	}

	$laSugaList = $conn->_fetch_array($sql, 'code');


	/*
	if ($svcCd == '0' || $svcCd == '5'){
		//주야간보호 시 재가요양과 일정중복을 확인한다.
		$sql = 'SELECT	t01_sugup_date
				,		t01_sugup_fmtime
				,		t01_sugup_totime
				,		t01_sugup_seq
				,		t01_suga_code1
				,		t01_status_gbn
				FROM	t01iljung
				WHERE	t01_ccode	= \''.$code.'\'
				AND		t01_mkind	= \''.($svcCd == '0' ? '5' : '0').'\'
				AND		t01_jumin	= \''.$jumin.'\'
				AND		t01_del_yn	= \'N\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$otherData[$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']] = Array(
				'sugaCd'=>$row['t01_suga_code1']
			,	'stat'=>$row['t01_status_gbn']
			,	'from'=>$row['t01_sugup_fmtime']
			,	'to'=>$row['t01_sugup_totime']
			);
		}

		$conn->row_free();
	}
	*/

	//재가지원의 업무일지의 작성여부
	if ($svcCd == 'S'){
		$sql = 'SELECT	date
				,		suga_cd
				,		resource_cd AS res_cd
				,		mem_cd
				FROM	care_works_log
				WHERE	org_no	= \''.$code.'\'
				AND		org_type= \''.$svcCd.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		LEFT(date, 6) = \''.$year.$month.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$arrCSWorkLogIs['NEW'][$row['date']][$row['suga_cd']][$row['res_cd']][$row['mem_cd']] = true;
		}

		$conn->row_free();

		$sql = 'SELECT	date
				,		time
				,		seq
				FROM	care_result
				WHERE	org_no	= \''.$code.'\'
				AND		org_type= \''.$svcCd.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		no		= \'1\'
				AND		LEFT(date, 6) = \''.$year.$month.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$arrCSWorkLogIs['OLD'][$row['date']][$row['time']][$row['seq']] = true;
		}

		$conn->row_free();
	}

	//일정조회
	$sql = 'select *
			  from t01iljung
			 where t01_ccode  = \''.$code.'\'
			   and t01_mkind  = \''.$svcCd.'\'
			   and t01_jumin  = \''.$jumin.'\'
			   and t01_del_yn = \'N\'
			   and left(t01_sugup_date,6) = \''.$year.$month.'\'';

	if ($svcCd == '6'){
		$sql .= ' AND t01_svc_subcd = \''.$sr.'\'';
	}

	if ($type == 'CONF'){
		if ($code == '31138000044' /*엔젤*/ ||
			$code == '31174000065' /*웃음드림방문요양센터*/){
		}else{
			$sql .= ' and t01_status_gbn = \'1\'';
		}
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$liDay = intval(substr($row['t01_sugup_date'],6));
		$liIdx = sizeof($laIljung[$liDay]);

		if ($type == 'CONF'){
			$lsSugaCd = $row['t01_conf_suga_code'];
		}else{
			if ($row['t01_request'] != 'LOG'){
				$lsSugaCd = $row['t01_suga_code1'];
			}else{
				$lsSugaCd = $row['t01_conf_suga_code'];
			}
		}

		$lsSugaNm = $laSugaList[$lsSugaCd]['name'];

		if ($svcCd == '4'){
			if ($row['t01_svc_subcode'] == '200'){
				if ($row['t01_modify_pos'] == 'E'){
					//바우처 엑셀 입력
					$lsSugaNm .= '/'.$row['t01_conf_soyotime'].'분';
				}else{
					$lsSugaNm .= '/'.$row['t01_sugup_soyotime'].'분';
				}
			}else{
				$lsSugaNm .= '/'.$laSugaList[$lsSugaCd]['gbn'];
			}
		}

		if ($row['t01_bipay_umu'] == 'Y'){
			$lsSugaNm .= '(<span style=\'color:#ff0000;\'>비</span>)';
		}else if ($row['t01_holiday'] == 'Y'){
			if ($svcCd == '0'){
				if ($row['t01_svc_subcode'] != '500' && $row['t01_toge_umu'] != 'Y'){
					$lsSugaNm  = str_replace('(30%)','',$lsSugaNm);
					$lsSugaNm .= '(<span style=\'color:#ff0000;\'>30%</span>)';
				}
			}
		}else if ($row['t01_mkind'] == '5' && $row['t01_sugup_yoil'] == '6'){
			//if ($myF->cutOff($row['t01_suga'] * 1.2) == $row['t01_suga_tot']){
			if ($year.$month >= '201701'){
				$rate = 1.3;
				$rateStr = '30%';
			}else{
				$rate = 1.2;
				$rateStr = '20%';
			}

			if ((Round($row['t01_suga'] * $rate / 10) * 10) == $row['t01_suga_tot']){
				$lsSugaNm  = str_replace($rateStr,'',$lsSugaNm);
				$lsSugaNm .= '(<span style=\'color:#ff0000;\'>'.$rateStr.'</span>)';
			}
		}

		//수당 금액지급액
		switch($row['t01_yname5']){
			case 'PERSON':
				$lsExtraKind = 'PERSON';
				$liExtraVal1 = intval($row['t01_yoyangsa_id3']);
				$liExtraVal2 = intval($row['t01_yoyangsa_id4']);
				break;

			case 'AMT':
				$lsExtraKind = 'AMT';
				$liExtraVal1 = intval($row['t01_yoyangsa_id3']);
				$liExtraVal2 = intval($row['t01_yoyangsa_id4']);
				break;

			default:
				$lsExtraKind = 'RATE';
				$liExtraVal1 = intval($row['t01_ysudang_yul1']);
				$liExtraVal2 = intval($row['t01_ysudang_yul2']);
		}

		//비급여단가
		if ($row['t01_svc_subcode'] == '200'){
			$liBipayCost = $row['t01_bipay1'];
		}else if ($row['t01_svc_subcode'] == '200'){
			$liBipayCost = $row['t01_bipay2'];
		}else{
			$liBipayCost = $row['t01_bipay3'];
		}
		$liBipayCost = intval($liBipayCost);

		if ($svcCd == '3' || $svcCd == 'A'){
			$lsBabyAdd = intval($row['t01_not_school_cnt']).'/'.
						 intval($row['t01_not_school_cost']).'/'.
						 intval($row['t01_not_school_pay']).'/'.
						 intval($row['t01_school_cnt']).'/'.
						 intval($row['t01_school_cost']).'/'.
						 intval($row['t01_school_pay']).'/'.
						 intval($row['t01_family_cnt']).'/'.
						 intval($row['t01_family_cost']).'/'.
						 intval($row['t01_family_pay']).'/'.
						 intval($row['t01_home_in_cost']).'/'.
						 intval($row['t01_holiday_cost']).'/';

			$lsBabyAdd .= (intval($row['t01_not_school_pay'])+intval($row['t01_school_pay'])+intval($row['t01_family_pay'])+intval($row['t01_home_in_cost'])+intval($row['t01_holiday_cost']));
		}else{
			$lsBabyAdd = '';
		}

		if ($type == 'CONF'){
			//실적
			$liProcTime = $row['t01_conf_soyotime'];
		}else{
			if ($row['t01_modify_pos'] == 'E'){
				//바우처 엑셀 입력
				$liProcTime = $row['t01_conf_soyotime'];
			}else{
				if ($svcCd == 'B' || $svcCd == 'C'){
					$liProcTime = $row['t01_sugup_soyotime'] / 60;
				}else{
					$liProcTime = $row['t01_sugup_soyotime'];
				}
			}
		}

		if ($row['t01_status_gbn'] == '1' || $row['t01_status_gbn'] == '5'){
			$lsStat = '1';
		}else{
			$lsStat = '9';
		}

		if ($svcCd == 'S' || $svcCd == 'R'){
			$memCd1 = $row['t01_yoyangsa_id1'];
			$memNm1 = $row['t01_yname1'];
			$memCd2 = $ed->en($row['t01_yoyangsa_id2']);
			$memNm2 = $row['t01_yname2'];
		}else{
			if ($row['t01_modify_pos'] == 'E'){
				//바우처 엑셀 입력
				$memCd1 = $ed->en($row['t01_yoyangsa_id1']);
				$memNm1 = $row['t01_yname1'];
				$memCd2 = $ed->en($row['t01_yoyangsa_id2']);
				$memNm2 = $row['t01_yname2'];
			}else{
				$memCd1 = $ed->en($row['t01_mem_cd1']);
				$memNm1 = $row['t01_mem_nm1'];
				$memCd2 = $ed->en($row['t01_mem_cd2']);
				$memNm2 = $row['t01_mem_nm2'];
			}
		}

		if ($type == 'CONF'){
			if ($code == '31138000044'){
				//엔젤
				$row['t01_conf_fmtime'] = ($row['t01_conf_fmtime'] ? $row['t01_conf_fmtime'] : $row['t01_sugup_fmtime']);
				$row['t01_conf_totime'] = ($row['t01_conf_totime'] ? $row['t01_conf_totime'] : $row['t01_sugup_totime']);
			}
		}

		if ($type == 'PLAN'){
			$strFrom = $row['t01_sugup_fmtime'];
			$strTo = $row['t01_sugup_totime'];
			$strSuga = $row['t01_suga_tot'];
		}else{
			if ($row['t01_conf_fmtime']){
				$strFrom = $row['t01_conf_fmtime'];
			}else{
				$strFrom = $row['t01_sugup_fmtime'];
			}

			if ($row['t01_conf_totime']){
				$strTo = $row['t01_conf_totime'];
			}else{
				$strTo = $row['t01_sugup_totime'];
			}

			$strSuga = $row['t01_conf_suga_value'];
		}

		$npmtInfo = '';
		$npmtAmt = 0;
		if (is_array($nonpayment[$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']])){
			foreach($nonpayment[$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']] as $tmp){
				if ($npmtInfo) $npmtInfo .= '@';
				$npmtInfo .= $tmp['code'].'#'.$tmp['amt'];
				$npmtAmt += $tmp['amt'];
			}
		}

		//업무일지 작성여부
		$IsWorkLog = false;
		if ($svcCd == 'S'){
			if ($arrCSWorkLogIs['NEW'][$row['t01_sugup_date']][$row['t01_suga_code1']][$row['t01_yoyangsa_id1']][$row['t01_yoyangsa_id2']]){
				$IsWorkLog = true;
			}else if ($arrCSWorkLogIs['OLD'][$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']]){
				$IsWorkLog = true;
			}
		}

		$laIljung[$liDay][$liIdx] = array(
			'day'			=>$liDay
		,	'cnt'			=>$liIdx
		,	'week'			=>$arrWeekGbn[$row['t01_sugup_date']] //date('w', strtotime($row['t01_sugup_date']))
		,	'svcKind'		=>$row['t01_svc_subcode']
		,	'from'			=>$myF->timeStyle($strFrom) //$myF->timeStyle($type == 'PLAN' ? $row['t01_sugup_fmtime'] : $row['t01_conf_fmtime'])
		,	'to'			=>$myF->timeStyle($strTo) //$myF->timeStyle($type == 'PLAN' ? $row['t01_sugup_totime'] : $row['t01_conf_totime'])
		,	'memCd1'		=>$memCd1 //$ed->en($row['t01_mem_cd1'])
		,	'memNm1'		=>$memNm1 //$row['t01_mem_nm1']
		,	'memCd2'		=>$memCd2 //$ed->en($row['t01_mem_cd2'])
		,	'memNm2'		=>$memNm2 //$row['t01_mem_nm2']
		,	'duplicate'		=>'N'
		,	'sugaName'		=>$laSugaList[$lsSugaCd]['name']
		,	'sugaCd'		=>$lsSugaCd
		,	'sugaNm'		=>$lsSugaNm
		,	'procTime'		=>$liProcTime
		,	'cost'			=>$row['t01_suga']
		,	'costEvening'	=>$row['t01_suga_over']
		,	'costNight'		=>$row['t01_suga_night']
		,	'costTotal'		=>$strSuga //$type == 'PLAN' ? $row['t01_suga_tot'] : $row['t01_conf_suga_value']
		,	'sudangPay'		=>$row['t01_ysudang']
		,	'sudangKind'	=>$lsExtraKind
		,	'sudangVal1'	=>$liExtraVal1
		,	'sudangVal2'	=>$liExtraVal2
		,	'timeEvening'	=>$row['t01_e_time']
		,	'timeNight'		=>$row['t01_n_time']
		,	'ynNight'		=>$row['t01_e_time'] > 0 ? 'Y' : 'N'
		,	'ynEvening'		=>$row['t01_n_time'] > 0 ? 'Y' : 'N'
		,	'ynHoliday'		=>$row['t01_holiday'] == 'Y' ? 'Y' : 'N'
		,	'ynFamily'		=>$row['t01_toge_umu'] == 'Y' ? 'Y' : 'N'
		,	'ynBipay'		=>$row['t01_bipay_umu'] == 'Y' ? 'Y' : 'N'
		,	'extraKind'		=>$row['t01_bipay_kind']
		,	'bipayCost'		=>$liBipayCost
		,	'bipayInfo'		=>$npmtInfo
		,	'nonpayment'	=>$npmtAmt
		,	'ynRealPay'		=>$row['t01_expense_yn']
		,	'realPay'		=>$row['t01_expense_pay']
		,	'babyAddPay'	=>$lsBabyAdd
		,	'ynAddRow'		=>'N'
		,	'ynSave'		=>'Y'
		,	'stat'			=>$lsStat
		,	'planFrom'		=>$row['t01_sugup_fmtime']
		,	'planTo'		=>$row['t01_sugup_totime']
		,	'seq'			=>$row['t01_sugup_seq']
		,	'request'		=>$row['t01_request']
		,	'modifyPos'		=>$row['t01_modify_pos']
		,	'togetherYn'	=>$row['t01_time_doub'] == 'Y' ? 'Y' : 'N'
		,	'worklog'		=>$IsWorkLog
		,	'ynDementia'	=>$row['t01_dementia_yn']
		);
	}

	$conn->row_free();

	//휴일리스트
	$sql = 'select cast(substring(mdate,7) as unsigned) as day
			,      holiday_name as nm
			  from tbl_holiday
			 where left(mdate,6) = \''.$year.$month.'\'';
	$loHolidayList = $conn->_fetch_array($sql,'day');

	//if (intval($month) == 5){
	//	$loHolidayList[1] = array('day'=>1, 'nm'=>'근로자의 날', 'holiday'=>'N');
	//}?>
	<style>
		.thStyle{
			border-bottom:2px solid #a6c0f3;
		}
		.divCalDay{
			float:left;
			width:25px;
			background-color:#efefef;
			border-right:1px solid #cccccc;
			border-bottom:1px solid #cccccc;
		}
		.divCalTxt{
			float:left;
			width:auto;
			color:#ff0000;
			font-size:11px;
			height:15px;
			line-height:15px;
		}
		.divCalObj{
			clear:both;
			width:100%;
		}
	</style>
	<table id="tblCalBody" ynLoad="N" class="my_table" style="width:100%; border-bottom:none;">
		<colgroup>
			<col width="15%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold thStyle clsCalCol0"><div style="cursor:default; color:ff0000;">일</div></th>
				<th class="head bold thStyle clsCalCol1"><div style="cursor:default; color:000000;">월</div></th>
				<th class="head bold thStyle clsCalCol2"><div style="cursor:default; color:000000;">화</div></th>
				<th class="head bold thStyle clsCalCol3"><div style="cursor:default; color:000000;">수</div></th>
				<th class="head bold thStyle clsCalCol4"><div style="cursor:default; color:000000;">목</div></th>
				<th class="head bold thStyle clsCalCol5"><div style="cursor:default; color:000000;">금</div></th>
				<th class="head bold thStyle clsCalCol6 last"><div style="cursor:default; color:0000ff;">토</div></th>
			</tr>
		</thead>
		<tbody><?
			$liFirstWeekly = date('w', strtotime($year.$month.'01'));
			$liLastDay = intval($myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $year.$month.'01', 'Y-m-d'), 'd'));
			$liChkWeek = ceil(($liLastDay + $liFirstWeekly) / 7);
			$liWeekday = 0;
			$liWeekly  = 0;
			$lbLastWeek= false;

			for($i=0; $i<$liFirstWeekly; $i++){
				if ($liWeekday % 7 == 0){?>
					<tr class="clsCalRow" week="<?=$liWeekly;?>"><?
				}?>
				<td class="center clsCalCol<?=$liWeekday;?>" style="border-bottom:2px solid #afafaf;">
					<div week="<?=$liWeekly;?>" class="divCalObj"></div>
				</td><?
				$liWeekday ++;
			}

			if ($liWeekday > 0)
				$liWeekly = 1;
			else
				$liWeekly = 0;

			for($i=1; $i<=$liLastDay; $i++){
				if ($liWeekday % 7 == 0){
					$liWeekday = 0;
					$liWeekly ++;

					if ($liChkWeek <= $liWeekly) $lbLastWeek = true;
					if ($liFirstWeekly != 0){?>
						</tr><?
					}?>
					<tr class="clsCalRow" week="<?=$liWeekly;?>"><?
				}

				switch($liWeekday){
					case 0: $lsFontClr = '#ff0000'; break;
					case 6: $lsFontClr = '#0000ff'; break;
					default: $lsFontClr = '#000000';
				}

				if (!empty($loHolidayList[$i]['nm'])){
					if ($loHolidayList[$i]['holiday'] != 'N'){
						$lsFontClr = '#ff0000';
					}
				}

				if ($liWeekly % 2 == 0){
					$lsBackClr = '#f9fcff';
				}else{
					$lsBackClr = '#ffffff';
				}

				if ($pastDateYn){
					$pastEditYn = true;
				}else{
					$dt = $year.$month.($i < 10 ? '0' : '').$i;

					if ($lbTodayPlanReg){
						if ($dt > $today){
							$pastEditYn = true;
						}else if ($dt == $today){
							//$diffTime = $myF->dateDiff('n', $myF->dateStyle($dt).' '.$myF->timeStyle($row['from']), Date('Y-m-d H:i'));
							$pastEditYn = true;
						}else{
							$pastEditYn = false;
						}
					}else{
						if ($dt > $today){
							$pastEditYn = true;
						}else{
							$pastEditYn = false;
						}
					}
				}?>
				<td class="center top clsCalCol<?=$liWeekday;?> <?=$liWeekday == 6 ? 'last' : '';?>" style="height:50px; border-bottom:2px solid #afafaf; background-color:<?=$lsBackClr;?>;">
					<div id="divCalCngYn_<?=$i;?>" yn="N" class="center bold divCalDay" style="color:<?=$lsFontClr;?>;"><?=$i;?></div><?
					if ($liWeekday == 0)
						$ynHoliday = 'Y';
					else
						$ynHoliday = 'N';

					if (!empty($loHolidayList[$i]['nm'])){
						$ynHoliday = 'Y';?>
						<div class="left divCalTxt" style="margin-top:3px;"><?=$loHolidayList[$i]['nm'];?></div><?
					}?>
					<div id="loCal_<?=$i;?>" ynHoliday="<?=$ynHoliday;?>" week="<?=$liWeekly;?>" class="divCalObj"><?
					if (is_array($laIljung[$i])){
						if ($lbEdit){
							if ($svcCd == '5'){
								//주야간보호
								$evtClick = '';
							}else if ($svcCd == 'S'){
								//재가지원
								$evtClick = 'lfWorkLogReg(this,\''.$ed->en($jumin).'\');';
							}else{
								if ($pastEditYn){
									$evtClick	= 'lfShowCalendar(this,\'1\')';
								}else{
									if ($row['ynBipay'] == 'Y'){
										$evtClick	= 'lfShowCalendar(this,\'1\')';
									}else{
										if ($type == 'CONF'){
											$evtClick	= 'lfShowCalendar(this,\'1\')';
										}else{
											//$evtClick	= 'lfModifyCalendar(this)';
											$evtClick	= 'alert(\'과거은 수정할 수 없습니다.\');';
											//$evtClick	= 'lfShowCalendar(this,\'1\')';
										}
									}
								}
							}
						}else{
							$evtClick = '';
						}

						foreach($laIljung[$i] as $row){
							if ($lbTodayPlanReg){
								if ($dt == $today){
									//현재와 일정의 시간차이
									$diffTime = $myF->dateDiff('n', Date('Y-m-d H:i'), $myF->dateStyle($dt).' '.$myF->timeStyle($row['to']));

									if ($diffTime > 30){
										$pastEditYn = true;
									}else{
										$pastEditYn = false;
									}
								}

								if ($svcCd == '5' || $svcCd == 'S'){
								}else{
									if (!$pastEditYn) $evtClick = 'alert(\'과거은 수정할 수 없습니다.\');';
								}
							}

							if ($row['cnt'] > 0){
								$lsBorderTop = 'border-top:1px dotted #666666;';
							}else{
								$lsBorderTop = '';
							}?>
							<div id="loCal_<?=$row['day'];?>_<?=$row['cnt'];?>" class="clsCal" onclick="<?=$evtClick;?>; return false;" style="clear:both; text-align:left; padding-left:3px;<?=$lsBorderTop;?>" onmouseover="_planMouseOver(this);" onmouseout="_planMouseOut(this);"
								day			="<?=$row['day'];?>"
								cnt			="<?=$row['cnt'];?>"
								week		="<?=$row['week'];?>"
								svcKind		="<?=$row['svcKind'];?>"
								from		="<?=$row['from'];?>"
								to			="<?=$row['to'];?>"
								memCd1		="<?=$row['memCd1'];?>"
								memNm1		="<?=$row['memNm1'];?>"
								memCd2		="<?=$row['memCd2'];?>"
								memNm2		="<?=$row['memNm2'];?>"
								duplicate	="<?=$row['duplicate'];?>"
								sugaName	="<?=$row['sugaName'];?>"
								sugaCd		="<?=$row['sugaCd'];?>"
								sugaNm		="<?=$row['sugaNm'];?>"
								procTime	="<?=$row['procTime'];?>"
								cost		="<?=$row['cost'];?>"
								costEvening	="<?=$row['costEvening'];?>"
								costNight	="<?=$row['costNight'];?>"
								costTotal	="<?=$row['costTotal'];?>"
								sudangPay	="<?=$row['sudangPay'];?>"
								sudangKind	="<?=$row['sudangKind'];?>"
								sudangVal1	="<?=$row['sudangVal1'];?>"
								sudangVal2	="<?=$row['sudangVal2'];?>"
								timeEvening	="<?=$row['timeEvening'];?>"
								timeNight	="<?=$row['timeNight'];?>"
								ynNight		="<?=$row['ynNight'];?>"
								ynEvening	="<?=$row['ynEvening'];?>"
								ynHoliday	="<?=$row['ynHoliday'];?>"
								ynFamily	="<?=$row['ynFamily'];?>"
								ynBipay		="<?=$row['ynBipay'];?>"
								extraKind	="<?=$row['extraKind'];?>"
								bipayCost	="<?=$row['bipayCost'];?>"
								bipayInfo	="<?=$row['bipayInfo'];?>"
								ynRealPay	="<?=$row['ynRealPay'];?>"
								realPay		="<?=$row['realPay'];?>"
								babyAddPay	="<?=$row['babyAddPay'];?>"
								ynAddRow	="<?=$row['ynAddRow'];?>"
								ynSave		="<?=$row['ynSave'];?>"
								stat		="<?=$row['stat'];?>"
								planFrom	="<?=$row['planFrom'];?>"
								planTo		="<?=$row['planTo'];?>"
								seq			="<?=$row['cnt'];?>"
								svcSeq		="<?=$row['seq'];?>"
								request		="<?=$row['request'];?>"
								modifyPos	="<?=$row['modifyPos'];?>"
								togetherYn	="<?=$row['togetherYn'];?>"
								ynDementia	="<?=$row['ynDementia'];?>"
								longtermYn	="Y"
								flag1		="0">
								<div class="divCalCont" style="font-weight:bold; cursor:default;">
									<div id="btnRemove" style="float:right; width:auto; margin-right:3px;"><?
										if ($lbEdit && $type == 'PLAN'){
											if ($svcCd == 'S' || $svcCd == 'R'){
												if (!$row['request']) $row['request'] = 'PERSON';

												if ($svcCd == 'S'){?>
													<span id="ID_WORKLOG" style="color:#FF5E00; height:20px; font-weight:bold; padding-right:2px;"><?
													if ($row['worklog']){
														echo '일지';
													}?>
													</span><?
												}

												if ($row['request'] == 'PERSON'){
													if ($svcCd == 'S'){?>
														<img id="btnPlanClose" src="../image/btn_close.gif" onclick="return lfCalRemove('<?=$row['day'];?>','<?=$row['cnt'];?>');" style="margin-top:3px; display:<?=!$pastEditYn && $row['ynBipay'] != 'Y' ? 'none' : '';?>;"><?
													}else{
														if ($row['stat'] == '1'){?>
															<img src="../image/img_key.jpg" onclick="" style="margin-top:3px; width:15px; height:14px;" alt="실적이 등록되었습니다.">
															<img id="btnPlanClose" src="../image/btn_close.gif" onclick="lfRemoveWork('<?=$row['day'];?>','<?=$row['cnt'];?>'); " style="margin-top:3px; display:<?=!$pastEditYn && $row['ynBipay'] != 'Y' ? 'none' : '';?>;"><?
														}else{?>
															<img id="btnPlanClose" src="../image/btn_close.gif" onclick="return lfCalRemove('<?=$row['day'];?>','<?=$row['cnt'];?>');" style="margin-top:3px; display:<?=!$pastEditYn && $row['ynBipay'] != 'Y' ? 'none' : '';?>;"><?
														}
													}
												}else if ($row['request'] == 'SERVICE'){?>
													<span style="font-size:11px; height:20px; color:BLUE;">서비스</span><?
												}else if ($row['request']){?>
													<span style="font-size:11px; height:20px; color:BLUE;">묶음</span><?
												}else{?>
													<img id="btnPlanClose" src="../image/btn_close.gif" onclick="return lfCalRemove('<?=$row['day'];?>','<?=$row['cnt'];?>');" style="margin-top:3px; display:<?=!$pastEditYn && $row['ynBipay'] != 'Y' ? 'none' : '';?>;"><?
												}
											}else{
												if ($ynClose != 'Y' && $row['stat'] != '1'){?>
													<img id="btnPlanClose" src="../image/btn_close.gif" onclick="return lfCalRemove('<?=$row['day'];?>','<?=$row['cnt'];?>');" style="margin-top:3px; display:<?=!$pastEditYn && $row['ynBipay'] != 'Y' ? 'none' : '';?>;"><?
												}else{?>
													<img src="../image/img_key.jpg" onclick="" style="margin-top:3px; width:15px; height:14px;" alt="실적이 등록되었습니다.">
													<img id="btnPlanClose" src="../image/btn_close.gif" onclick="lfRemoveWork('<?=$row['day'];?>','<?=$row['cnt'];?>'); " style="margin-top:3px; display:<?=!$pastEditYn && $row['ynBipay'] != 'Y' ? 'none' : '';?>;"><?
												}
											}
										}?>
									</div>
									<div id="lblLGSaveYn" style="width:auto; cursor:default; color:BLUE; display:none;" lgSaveYn="N">공단확정</div><?
									if ($row['modifyPos'] == 'L'){?>
										<div style="width:auto; cursor:default; color:#0000ff;">RFID입력</div>
										<div id="lblTimeStr" style="width:auto; cursor:default;"><?=$row['from'].'~'.$row['to'];?></div><?
									}else if ($row['modifyPos'] == 'P'){?>
										<div style="width:auto; cursor:default; color:#0000ff;">공단계획</div>
										<div id="lblTimeStr" style="width:auto; cursor:default;"><?=$row['from'].'~'.$row['to'];?></div><?
									}else if ($row['modifyPos'] == 'E'){?>
										<div style="width:auto; cursor:default; color:#0000ff;">EXCEL실적</div><?
									}else{
										if ($svcCd == 'S' || $svcCd == 'R'){?>
											<div id="lblTimeStr" style="float:left; width:auto; cursor:default;"><?=$row['from'];?></div><?
										}else{?>
											<div id="lblTimeStr" style="float:left; width:auto; cursor:default; color:<?=$row['togetherYn'] == 'Y' ? '#0000ff' : '';?>;"><?=$row['from'].'~'.$row['to'];?></div><?
										}
									}?>
								</div><?

								if ($svcCd == 'S' || $svcCd == 'R'){?>
									<div id="lblMemStr" class="divCalCont" style="cursor:default;"><?=$row['memNm1'];?></div><?
									if ($row['memCd2']){?>
										<div id="lblSupplyStr" class="divCalCont" style="cursor:default;">담당직원:<?=$row['memNm2'];?></div><?
									}
								}else if ($svcCd == '5'){?>
									<div class="divCalCont" style="cursor:default;">비급여:<span id="lblNonpayment"><?=number_format($row['nonpayment']);?></span></div><?
								}else{?>
									<div id="lblMemStr" class="divCalCont" style="cursor:default;"><?=$row['memNm1'].(!empty($row['memNm2']) ? '/'.$row['memNm2'] : '');?></div><?
								}?>
								<div id="lblSugaStr" class="divCalCont" style="cursor:default;"><?
									if ($svcCd == '5'){?>
										<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;"><?=$row['sugaNm'].($row['ynHoliday'] == 'Y' ? '(<span style="color:RED;">30%</span>)' : '');?></div><?
									}else{?>
										<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;"><?=$row['sugaNm'];?></div><?
									}

									if (substr($row['sugaCd'],0,2) == 'CB'){?>
										<div style="float:left; width:auto;"><img src="../image/icon_bath.png" style="width:15px; height:14px;"></div><?
									}else if (substr($row['sugaCd'],0,2) == 'CN'){?>
										<div style="float:left; width:auto;"><img src="../image/icon_nurs.png" style="width:15px; height:14px;"></div><?
									}?>
								</div><?
								if ($_SESSION['userLevel'] == 'C'){
									if ($svcCd != 'S' && $svcCd != 'R'){?>
										<div class="divCalCont" style="cursor:default;">산정수가:<?=number_format($row['costTotal']);?></div><?
									}
								}?>
								<div class="divCalCont" style="cursor:default; display:none;"><span id="divErrorMsg" style="color:#ff0000; font-size:11px; font-weight:bold;"></span></div>
								<div id="divLongtermMsg" style="color:#ff0000; font-size:11px; font-weight:bold; display:none;"></div><?
								if ($svcCd == '0' && IntVal($row['costTotal']) == 0){?>
									<div style="color:#ff0000; font-size:11px; font-weight:bold;">수가금액오류</div><?
								}?>
							</div><?
						}
					}?>
					</div>
				</td><?
				$liWeekday ++;
			}

			if ($liWeekday % 7 == 0){?>
				</tr><?
			}else{
				for($i=$liWeekday+1; $i<=7; $i++){?>
					<td class="center clsCalCol<?=$liWeekday;?> <?=$liWeekday == 6 ? 'last' : '';?>" style="border-bottom:2px solid #afafaf;">
						<div week="<?=$liWeekly;?>" class="divCalObj"></div>
					</td><?
					$liWeekday ++;
				}?>
				</tr><?
			}?>
		</tbody>
	</table>
	<div class="clean" style="display:none;"><?
		if (is_array($otherData)){
			foreach($otherData as $tmpDt => $arrDt){
				$tmpIdx = 101;

				foreach($arrDt as $tmpTm => $arrTm){
					foreach($arrTm as $tmpSeq => $arrSeq){
						$tmpDay = intval(substr($tmpDt,6));?>
						<div id="loCal_<?=$tmpDay;?>_<?=$tmpIdx;?>" class="clsCal"
							from="0000"
							to="2400"
							svcKind="DAY_AND_NIGHT"
							cnt="1"></div><?
						$tmpIdx ++;
					}
				}
			}
		}?>
	</div>
	<div id="calCont" style="clear:both; width:100%;"></div><?
	include_once('../inc/_db_close.php');?>
	<script type="text/javascript">
		var loTimerCalCont = null;

		$(document).ready(function(){
			loTimerCalCont = setInterval('lfSetCalendarData()', 50);
		});

		function lfSetCalendarData(){
			var lsSvcCd = $('#planInfo').attr('svcCd');

			if (lsSvcCd == '4'){
				try{
					if (lfCalendarDisData(2)){
						clearTimeout(loTimerCalCont);
						loTimerCalCont = null;
					}
				}catch(e){
				}
			}else{
				try{
					if (lfCalendarData(2)){
						clearTimeout(loTimerCalCont);
						loTimerCalCont = null;
					}
				}catch(e){
				}
			}
		}

		function lfRemoveWork(asDay, asCnt){
			if (!confirm('실적이 등록된 일정입니다.\n\n삭제후 복구가 불가능합니다.\n삭제를 진행하시겠습니까?')){
				$('#loCal_'+asDay+'_'+asCnt).attr('flag1','1');
				return false;
			}

			//삭제진행
			$.ajax({
				type : 'POST'
			,	url  : './plan_unit_delete.php'
			,	data : {
					'jumin':$('#clientInfo').attr('value')
				,	'svcCd':$('#planInfo').attr('svcCd')
				,	'date':$('#planInfo').attr('year')+$('#planInfo').attr('month')+(__str2num(asDay) < 10 ? '0' : '')+__str2num(asDay)
				,	'time':$('#loCal_'+asDay+'_'+asCnt).attr('from').split(':').join('')
				,	'seq':$('#loCal_'+asDay+'_'+asCnt).attr('svcSeq')
				}
			,	success: function(result){
					if (result == 1){
					}else if (result == 9){
						alert('일정삭제중 오류가 발생하였습니다.\n잠시후 다시 시도하여 주십시오.');
					}else{
						alert(result);
					}
				}
			});

			return lfCalRemove(asDay,asCnt);
		}


		//재가지원 업무내용등록
		function lfWorkLogReg(obj,jumin){
			var objModal = new Object();
			var url = '../care/care_works_log_reg.php';
			var style = 'dialogWidth:800px; dialogHeight:550px; dialogHide:yes; scroll:no; status:no';

			objModal.svcCd = '<?=$svcCd;?>';
			objModal.date = __str2num($(obj).attr('day'));
			objModal.date = '<?=$year;?><?=$month;?>'+(objModal.date < 10 ? '0' : '')+objModal.date;
			objModal.time = $(obj).attr('from').replace(':','');
			objModal.seq = $(obj).attr('svcSeq');
			objModal.jumin = jumin;
			objModal.suga = $(obj).attr('sugaCd');
			objModal.resource = $(obj).attr('memCd1');
			objModal.mem = $(obj).attr('memCd2');
			objModal.result = true;

			/*if ('<?=$debug;?>' == '1'){
				alert(objModal.svcCd
					+'\n'+objModal.date
					+'\n'+objModal.time
					+'\n'+objModal.seq
					+'\n'+objModal.jumin
					+'\n'+objModal.suga
					+'\n'+objModal.resource
					+'\n'+objModal.mem);
			}*/

			window.showModalDialog(url, objModal, style);

			if (objModal.worklog){
				$('#ID_WORKLOG',obj).text('일지');
			}else{
				$('#ID_WORKLOG',obj).text('');
			}
		}
	</script>