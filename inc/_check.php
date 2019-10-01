<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_function.php");
	include_once('../inc/_ed.php');

	if ($_GET["gubun"] == "findCenter"){
		$sql = "select m00_mkind"
			 . "  from m00center"
			 . " where m00_mcode = '".$_GET["mCode"]
			 . "'";
		if ($_GET["mKind"] != ""){
			$sql .= " and m00_mkind = '".$_GET["mKind"]."'";
		}
		$sql .= " limit 1";
		$row_data = $conn->get_data($sql);
		if ($row_data != ""){
			$requestString = $row_data;
		}else{
			$requestString = "N";
		}
	}else if($_GET["gubun"] == "getCenterName"){
		$sql = "select m00_code1"
			 . ",      m00_cname"
			 . "  from m00center"
			 . " where m00_mcode = '".$_GET["mCode"]
			 . "'  and m00_mkind = '".$_GET["mKind"]
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();
		$requestString = $row[0]."//".$row[1];
		$conn->row_free();
	}else if($_GET["gubun"] == "getBoninYul"){
		/*
		$sql = "select m81_name"
			 . "  from m81gubun"
			 . " where m81_gbn = 'SYL'"
			 . "   and m81_code = '".$_GET["code"]
			 . "'";
		*/
		$sql = 'select m92_bonin_yul'
			 . '  from m92boninyul'
			 . ' where m92_code = \''.$_GET["code"]
			 . '\' and replace(left(now(), 10), \'-\', \'\') between m92_sdate and m92_edate';
		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();
		$requestString = $row[0];
		$conn->row_free();
	}else if($_GET["gubun"] == "getMaxPay"){
		/*
		$sql = "select m81_name"
			 . "  from m81gubun"
			 . " where m81_gbn = 'MMY'"
			 . "   and m81_code = '".$_GET["code"]
			 . "'";
		*/
		/*
		 * 2011.11.24 변경
		$sql = 'select m91_kupyeo'
			 . '  from m91maxkupyeo'
			 . ' where m91_code = \''.$_GET["code"]
			 . '\' and replace(left(now(), 10), \'-\', \'\') between m91_sdate and m91_edate';
		*/

		$code = $_REQUEST['code'];
		$date = $_REQUEST['date'];

		if (empty($date)) $date = date('Ymd', mktime());

		$date = str_replace('-', '', $date);
		$date = str_replace('.', '', $date);

		$sql = 'select m91_kupyeo
				  from m91maxkupyeo
				 where m91_code   = \''.$code.'\'
				   and m91_sdate <= \''.$date.'\'
				   and m91_edate >= \''.$date.'\'';

		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();
		$requestString = $row[0];
		$conn->row_free();
	}else if($_GET["gubun"] == "getSugaPrice"){
		################################################
		# 수가
		################################################
		$ym    = $_GET['mYM'];   //년월
		$bipay = $_GET['bipay']; //비급여구분

		$sql = "select ";

		#if ($bipay == 'Y'){
		#	$sql .= "case when m01_suga_cvalue1 > 0 then m01_suga_cvalue1 else m01_suga_value end";
		#}else{
			$sql .= "m01_suga_value";
		#}

		$sql .="  from m01suga"
			 . " where m01_mcode  = '".$_GET["mCode"]
			 . "'  and m01_mcode2 = '".$_GET["mSuga"]
			 . "'";

		if ($ym == ''){
			$sql .= " and replace(left(now(), 10), '-', '') between m01_sdate and m01_edate";
		}else{
			$sql .= " and '".str_replace('-', '', $ym)."' between left(m01_sdate, ".strlen($ym).") and left(m01_edate, ".strlen($ym).")";
		}

		$conn->query($sql);
		$row = $conn->fetch();
		$requestString = $row[0];
		$conn->row_free();

		if ($requestString == ''){
			$sql = "select ";

			#if ($bipay == 'Y'){
			#	$sql .= "case when m11_suga_cvalue1 > 0 then m11_suga_cvalue1 else m11_suga_value end";
			#}else{
				$sql .= "m11_suga_value";
			#}

			$sql .="  from m11suga"
				 . " where m11_mcode  = '".$_GET["mCode"]
				 . "'  and m11_mcode2 = '".$_GET["mSuga"]
				 . "'";

			if ($ym == ''){
				$sql .= " and replace(left(now(), 10), '-', '') between m11_sdate and m11_edate";
			}else{
				$sql .= " and '".str_replace('-', '', $ym)."' between left(m11_sdate, ".strlen($ym).") and left(m11_edate, ".strlen($ym).")";
			}

			$conn->query($sql);
			$row = $conn->fetch();
			$requestString = $row[0];
			$conn->row_free();
		}



		/******************************

			비급여 수가를 구한다.

		******************************/
		if ($bipay == 'Y'){
			$sql = 'select m03_bipay1
					,      m03_bipay2
					,      m03_bipay3
					  from m03sugupja
					 where m03_ccode = \''.$_GET['mCode'].'\'
					   and m03_mkind = \'0\'
					   and m03_jumin = \''.$ed->de($_GET['c_cd']).'\'';

			$tmp = $conn->get_array($sql);

			switch($_GET['svc_cd']){
				case '200': $requestString = $tmp['m03_bipay1']; break;
				case '500': $requestString = $tmp['m03_bipay2']; break;
				case '800': $requestString = $tmp['m03_bipay3']; break;
			}

			unset($tmp);
		}
	}else if($_GET["gubun"] == "getSudangPrice"){
		$sql = "select ifnull(m21_svalue, 0)"
			 . "  from m21sudang"
			 . " where m21_mcode  = '".$_GET["mCode"]
			 . "'  and m21_mcode2 = '".$_GET["mSuga"]
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$requestString = $row[0];
		$conn->row_free();

		if ($requestString == ''){
			$requestString = 0;
		}
	}else if($_GET["gubun"] == "getSugaName"){
		$ym = $_GET['mYM'];

		$sql = "select m01_suga_cont"
			 . "  from m01suga"
			 . " where m01_mcode  = '".$_GET["mCode"]
			 . "'  and m01_mcode2 = '".$_GET["mSuga"]
			 . "'";

		if ($ym == ''){
			$sql .= " and replace(left(now(), 10), '-', '') between m01_sdate and m01_edate";
		}else{
			$sql .= " and '".str_replace('-', '', $ym)."' between left(m01_sdate, ".strlen($ym).") and left(m01_edate, ".strlen($ym).")";
		}

		$conn->query($sql);
		$row = $conn->fetch();
		$requestString = $row[0];
		$conn->row_free();

		if ($requestString == ''){
			$sql = "select m11_suga_cont"
				 . "  from m11suga"
				 . " where m11_mcode  = '".$_GET["mCode"]
				 . "'  and m11_mcode2 = '".$_GET["mSuga"]
				 . "'";

			if ($ym == ''){
				$sql .= " and replace(left(now(), 10), '-', '') between m11_sdate and m11_edate";
			}else{
				$sql .= " and '".str_replace('-', '', $ym)."' between left(m11_sdate, ".strlen($ym).") and left(m11_edate, ".strlen($ym).")";
			}

			$conn->query($sql);
			$row = $conn->fetch();
			$requestString = $row[0];
			$conn->row_free();
		}
	}else if($_GET['gubun'] == 'checkSugaTable'){
		$sql = 'select count(*)'
			 . '  from m01suga'
			 . ' where m01_mcode = \''.$_GET['mCode']
			 . '\'';
		$conn->query($sql);
		$row = $conn->fetch();
		$requestString = $row[0];
		$conn->row_free();
	}else if($_GET['gubun'] == 'backupSugaTable'){
		$conn->begin();

		// 기존 수가를 히스토리 테이블로 복사
		$sql = 'insert into m11suga (m11_mcode, m11_mcode2, m11_sdate, m11_edate, m11_scode, m11_suga_cont, m11_suga_value, m11_suga_value15, m11_suga_value75, m11_suga_cvalue1, m11_suga_cvalue2, m11_suga_cvalue3, m11_calc_time, m11_rate) '
			 . 'select m01_mcode, m01_mcode2, m01_sdate, replace(date_add(date_format(now(), \'%Y%m%d\'), interval -1 day), \'-\', \'\'), m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, m01_rate'
			 . '  from m01suga'
			 . ' where m01_mcode  = \''.$_GET['mCode']
			 . '\'';
		if ($conn->query($sql)){
			$conn->commit();
			$requestString = 'Y';
		}else{
			$conn->rollback();
			$requestString = 'N';
		}
	}else if($_GET['gubun'] == 'copySugaTable'){
		$conn->begin();

		// 기존 수가 삭제
		$sql = 'delete'
			 . '  from m01suga'
			 . ' where m01_mcode  = \''.$_GET['mCode']
			 . '\'';
		if ($conn->query($sql)){
			$requestString = 'Y';
		}else{
			$conn->rollback();
			$requestString = 'N';
		}

		if ($requestString == 'Y'){
			$sql = 'insert into m01suga (m01_mcode, m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, m01_sdate, m01_edate, m01_rate) '
				 . 'select \''.$_GET['mCode'].'\', m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, date_format(now(), \'%Y%m%d\'), \'99999999\', m01_rate'
				 . '  from m01suga'
				 . ' where m01_mcode = \'goodeos\'';
			if ($conn->query($sql)){
				$requestString = 'Y';
			}else{
				$conn->rollback();
				$requestString = 'N';
			}
		}

		if ($requestString == 'Y'){
			$conn->commit();
		}
	}else if($_GET['gubun'] == 'checkDuplicate'){
		if (ceil($_GET['mFmTime']) > ceil($_GET['mToTime'])){
			$ToTime = ceil($_GET['mToTime']) + 2400;
		}else{
			$ToTime = $_GET['mToTime'];
		}

		if (is_numeric($_GET['mYoy'])){
			$memCD = $_GET['mYoy'];
		}else{
			$memCD = $ed->de($_GET['mYoy']);
		}

		// 일정 검사시 센터구분을 제외한다.
		$sql = "select count(*) as yoyCount"
			 . "  from t01iljung"
			 . " where t01_sugup_date = '".$_GET['mDate']
			 . "'  and '".$memCD."' in (t01_mem_cd1, t01_mem_cd2)"
			 . "   and ((t01_sugup_fmtime <= '".$_GET['mFmTime']."'"
			 . "   and   case when t01_sugup_fmtime > t01_sugup_totime then cast(t01_sugup_totime as unsigned) + 2400 else t01_sugup_totime end > '".$_GET['mFmTime']."')"
			 . "    or  (t01_sugup_fmtime <  '".$ToTime."'"
			 . "   and   case when t01_sugup_fmtime > t01_sugup_totime then cast(t01_sugup_totime as unsigned) + 2400 else t01_sugup_totime end > '".$ToTime."'))"
			 //. "   and t01_svc_subcode != '800'" //간호는 중복에서 제외한다.
			 . "   and t01_del_yn = 'N'";

		if ($_GET['mYoyDT'] != ''){
			$sql .= " and concat(t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime) != '".$_GET['mYoyDT']."'";
		}

		if ($conn->get_data($sql) > 0){
			$requestString = 'Y';
		}else{
			$requestString = 'N';
		}
	}else if($_GET['gubun'] == 'checkIljungCopy'){
		$sql = 'select count(*)'
			 . '  from t01iljung'
			 . ' where t01_ccode = \''.$_GET['mCode'].'\''
			 . '   and t01_mkind = \''.$_GET['mKind'].'\''
			 . '   and t01_jumin = \''.$_GET['mJuminNo'].'\''
			 . '   and left(t01_sugup_date, 6) = \''.$_GET['mDate'].'\'';
		$conn->query($sql);
		$row = $conn->fetch();
		if ($row[0] > 0){
			$requestString = 'N';
		}else{
			$requestString = 'Y';
		}
		$conn->row_free();
	}else if($_GET['gubun'] == 'checkIljungDelete'){
		$sql = 'select count(*)'
			 . '  from t01iljung'
			 . ' where t01_ccode = \''.$_GET['mCode'].'\''
			 . '   and t01_mkind = \''.$_GET['mKind'].'\''
			 . '   and t01_jumin = \''.$_GET['mJuminNo'].'\''
			 . '   and left(t01_sugup_date, 6) = \''.$_GET['mDate'].'\''
			 . '   and t01_status_gbn != \'9\'';
		$count = $conn->get_data($sql);

		if ($count > 0){
			$requestString = 'Y';
		}else{
			$requestString = 'N';
		}
		$conn->row_free();
	}else if($_GET['gubun'] == 'changePassword'){
		$sql = "select pswd"
			 . "  from han_member"
			 . " where id = '".$_SESSION["userCode"]
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$pass = $row['m97_pass'];
		$conn->row_free();

		if ($pass != $_GET['nowPass']){
			$requestString = 'X';
		}else{
			$sql = "update han_member"
				 . "   set pswd = '".$_GET['newPass']
				 . "'"
				 . " where id = '".$_SESSION["userCode"]
				 . "'";
			$conn->query($sql);

			if ($conn->row_affect() > 0){
				$requestString = 'Y';
			}else{
				$requestString = 'N';
			}
		}
	}else if($_GET['gubun'] == 'getYoyList'){
		$sql = "select m02_yjumin"
			 . ",      m02_yname"
			 . "  from m02yoyangsa"
			 . " where m02_ccode = '".$_GET['mCode']
			 . "'  and m02_mkind = '".$_GET['mKind']
			 . "'"
			 . " order by m02_yname";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$requestString = '';

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$requestString .= $row['m02_yjumin'].'//'.$row['m02_yname'].';;';
		}

		$conn->row_free();
	}else if($_GET['gubun'] == 'getYoyIljungList'){
		/*
		$sql = "select m02_yjumin"
			 . ",      m02_yname"
			 . "  from m02yoyangsa"
			 . " where m02_ccode = '".$_GET['mCode']
			 . "'  and m02_mkind = '".$_GET['mKind']
			 . "'"
			 . " order by m02_yname";
		*/
		$sql = "select distinct"
			 . "       m02_yjumin"
			 . ",      m02_yname"
			 . "  from t01iljung"
			 . " inner join m02yoyangsa"
			 . "    on m02_ccode  = t01_ccode"
			 . "   and m02_mkind  = t01_mkind"
			 . "   and m02_yjumin = t01_yoyangsa_id1"
			 . " where t01_ccode = '".$_GET['mCode']
			 . "'";

		if ($_GET['mKind'] != ''){
			$sql .= " and t01_mkind = '".$_GET['mKind']
				 .  "'";
		}

		if ($_GET['location'] == 'Y'){
			$sql .= " and t01_status_gbn in ('1', '5')";
		}

		$sql .="   and left(t01_sugup_date, ".strLen($_GET['mDate']).") = '".$_GET['mDate']
			 . "'  and t01_del_yn = 'N'"
			 . " order by m02_yname";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$requestString = '';

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$requestString .= $row['m02_yjumin'].'//'.$row['m02_yname'].';;';
		}

		$conn->row_free();
	}else if($_GET['gubun'] == 'getSugupList'){
		$sql = "select m03_jumin"
			 . ",      m03_name"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$_GET['mCode']
			 . "'  and m03_mkind = '".$_GET['mKind']
			 . "'"
			 . " order by m03_name";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$requestString = '';

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$requestString .= $row['m03_jumin'].'//'.$row['m03_name'].';;';
		}

		$conn->row_free();
	}else if($_GET['gubun'] == 'getSugupSvcList'){
		$sql = "select distinct"
			 . "       t01_jumin"
			 . ",      m03_name"
			 . "  from t01iljung"
			 . " inner join m03sugupja"
			 . "    on t01_ccode = m03_ccode"
			 . "   and t01_mkind = m03_mkind"
			 . "   and t01_jumin = m03_jumin"
			 . " where t01_ccode = '".$_GET['mCode']
			 . "'  and t01_mkind = '".$_GET['mKind']
			 #. "'  and t01_svc_subcode = '".$_GET['mSvcCode']
			 . "'  and left(t01_sugup_date, 6) = '".$_GET['mDate']
			 . "'  and t01_del_yn = 'N'"
			 . " order by m03_name";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$requestString = '';

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$requestString .= $row['t01_jumin'].'//'.$row['m03_name'].';;';
		}

		$conn->row_free();
	}else if($_GET['gubun'] == 'getYoySvcList'){
		$sql = "select distinct"
			 . "       m02_yjumin"
			 . ",      m02_yname"
			 . "  from t01iljung"
			 . " inner join m02yoyangsa"
			 . "    on t01_ccode = m02_ccode"
			 . "   and t01_mkind = m02_mkind"
			 . "   and t01_yoyangsa_id1 = m02_yjumin"
			 . " where t01_ccode = '".$_GET['mCode']
			 . "'  and t01_mkind = '".$_GET['mKind']
			 . "'";
		if ($_GET['mSvcCode'] != ''){
			 $sql .= " and t01_svc_subcode = '".$_GET['mSvcCode']."'";
		}
		$sql .= "  and left(t01_sugup_date, 6) = '".$_GET['mDate']
			 . "'  and t01_del_yn = 'N'"
			 . " order by m02_yname";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$requestString = '';

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$requestString .= $row['m02_yjumin'].'//'.$row['m02_yname'].';;';
		}

		$conn->row_free();
	}else if($_GET['gubun'] == 'getYoySvcListAll'){
		$sql = "select distinct"
			 . "       m02_yjumin"
			 . ",      m02_yname"
			 . "  from t01iljung"
			 . " inner join m02yoyangsa"
			 . "    on t01_ccode = m02_ccode"
			 . "   and t01_mkind = m02_mkind"
			 . "   and m02_yjumin in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)"
			 . " where t01_ccode = '".$_GET['mCode']
			 . "'  and t01_mkind = '".$_GET['mKind']
			 . "'";
		if ($_GET['mSvcCode'] != ''){
			 $sql .= " and t01_svc_subcode = '".$_GET['mSvcCode']."'";
		}
		$sql .= "  and left(t01_sugup_date, 6) = '".$_GET['mDate']
			 . "'  and t01_del_yn = 'N'"
			 . " order by m02_yname";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$requestString = '';

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$requestString .= $row['m02_yjumin'].'//'.$row['m02_yname'].';;';
		}

		$conn->row_free();
	}else if($_GET['gubun'] == 'checkYoyWeekDay'){
		if (is_numeric($_GET['mYoy'])){
			$memCD = $_GET['mYoy'];
		}else{
			$memCD = $ed->de($_GET['mYoy']);
		}

		$sql = "select m02_ygunmu_mon"
			 . ",      m02_ygunmu_tue"
			 . ",      m02_ygunmu_wed"
			 . ",      m02_ygunmu_thu"
			 . ",      m02_ygunmu_fri"
			 . ",      m02_ygunmu_sat"
			 . ",      m02_ygunmu_sun"
			 . "  from m02yoyangsa"
			 . " where m02_ccode  = '".$_GET['mCode']
			 . "'  and m02_mkind  = '".$_GET['mKind']
			 . "'  and m02_yjumin = '".$_GET['mYoy']
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$requestString  = '';
		$requestString .= $row['m02_ygunmu_mon'].'//';
		$requestString .= $row['m02_ygunmu_tue'].'//';
		$requestString .= $row['m02_ygunmu_wed'].'//';
		$requestString .= $row['m02_ygunmu_thu'].'//';
		$requestString .= $row['m02_ygunmu_fri'].'//';
		$requestString .= $row['m02_ygunmu_sat'].'//';
		$requestString .= $row['m02_ygunmu_sun'].'//';
		$conn->row_free();
	}else if($_GET['gubun'] == 'checkHoliday'){
		$sql = "select ifnull(holiday_name, '')"
			 . "  from tbl_holiday"
			 . " where mdate = '".str_Replace('-', '', $_GET['mDate'])
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();

		if ($row[0] != ''){
			$requestString  = 'Y';
		}else{
			$requestString  = 'N';
		}

		$conn->row_free();
	}else if($_GET['gubun'] == 'checkHPNo'){
		$sql = "select count(*)"
			 . "  from m02yoyangsa"
			 . " where m02_ytel = '".$_GET['mHP']
			 . "'  and m02_ygoyong_stat = '1'";
		$conn->query($sql);
		$row = $conn->fetch();
		$requestString  = $row[0];
		$conn->row_free();
	}else if($_GET['gubun'] == 'getSugaTimeValue'){
		$requestString = getSugaTimeValue($conn, $_GET['mCode'], $_GET['svcKind'], $_GET['mSvcCode'], $_GET['mSugaCode'], $_GET['mTime'], $_GET['mDT'], $ed->de($_GET['client']));
	}else if($_GET['gubun'] == 'getSugaRateValue'){
		$requestString = getSugaRateValue($conn, $_GET['mCode'], $_GET['mSugaCode']);
	}else if($_GET['gubun'] == 'getHoliday'){
		$requestString = getHoliday($conn, $_GET['mDate']);
	}else if($_GET['gubun'] == 'getDepositAmount'){
		$requestString = getDepositAmount($conn, $_GET['pCode'], $_GET['pKind'], $_GET['pKey']);
	}else if($_GET['gubun'] == 'getInIljungYear'){
		$requestString = getInIljungYear($conn, $_GET['pCode'], $_GET['pKind']);
	}

	include("../inc/_db_close.php");

	echo $requestString;
?>