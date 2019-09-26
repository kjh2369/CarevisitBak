<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	function getWeekInfo($_date){
		$BASIC_DOW = 1; // 1(mon) ~ 7(sun)
		list($yy, $mm, $dd) = explode('-', $_date);

		$dow = date('N', mktime(0, 0, 0, $mm, 1, $yy));

		if ($dow <= $BASIC_DOW){
			$diff = $BASIC_DOW - $dow;
			$srt_day = $diff+1;
		}else{
			$diff = 7-$dow;
			$srt_day = $diff + $BASIC_DOW + 1;
		}

		if ($dd < $srt_day){
			$new_date = date('Y-m-d', mktime(0, 0, 0, $mm, 0, $yy));
			return getWeekInfo($new_date);
		}else{
			$wom = ceil(($dd-($srt_day-1))/7);
			//$new_date = substr($yy, 2, 2). $mm. $wom;
			//$new_date = (int)$mm. '월' .$wom . '주차';
			$new_date = $wom;
			return $new_date;
		}
	}


	/*********************************************************

		파라메타

	*********************************************************/
	$code = $_POST['code'];
	$yymm = $_POST['yymm'];
	$seq  = $_POST['seq'];
	$no = $_POST['no'];

	parse_str($_POST['para'], $para);

	$subject  = addslashes($para['subject']);
	$contents = addslashes($para['contents']);

	$fromTmp = explode(' ', $para['from']);
	$toTom   = explode(' ', $para['to']);

	$fromDate = $fromTmp[0];
	$fromTime = $fromTmp[1].':00';
	$toDate   = $toTom[0];
	$toTime   = $toTom[1].':00';
	$fulltime = $para['fulltime'];

	if ($fulltime == 'Y'){
		$fromTime = null;
		$toTime   = null;
	}



	$regID = $_SESSION['userCode'];
	$regNM = $_SESSION['userName'];



	/*********************************************************

		시작일이 바뀐경우 다시 작성한ㄷ.

	*********************************************************/
	if ($yymm != str_replace('-', '', substr($fromDate, 0, 7))){
		$delete_query = 'update calendar
							set del_flag = \'Y\'
						  where org_no   = \''.$code.'\'
							and cld_yymm = \''.$yymm.'\'
							and cld_seq  = \''.$seq.'\'';

		$yymm = str_replace('-', '', substr($fromDate, 0, 7));
		$seq  = 0;
	}



	/*********************************************************

		다음순번을 찾는다.

	*********************************************************/
	if (empty($seq)){
		$workType = 'insert';
		$sql = 'select ifnull(max(cld_seq), 0) + 1
				  from calendar
				 where org_no   = \''.$code.'\'
				   and cld_yymm = \''.$yymm.'\'';

		$seq = $conn->get_data($sql);

		$date = $fromDate;
		$sql  = '';
		$i    = 0;

		while(true){
			$addFlag = false;

			if (($para['weekly1'] == 'Y' && Date('w',StrToTime($date)) == 1) ||
				($para['weekly2'] == 'Y' && Date('w',StrToTime($date)) == 2) ||
				($para['weekly3'] == 'Y' && Date('w',StrToTime($date)) == 3) ||
				($para['weekly4'] == 'Y' && Date('w',StrToTime($date)) == 4) ||
				($para['weekly5'] == 'Y' && Date('w',StrToTime($date)) == 5) ||
				($para['weekly6'] == 'Y' && Date('w',StrToTime($date)) == 6) ||
				($para['weekly0'] == 'Y' && Date('w',StrToTime($date)) == 0)){
				$weeklyFlag = true;
			}else{
				$weeklyFlag = false;
			}

			#if ($debug) echo $date.'/'.getWeekInfo($date).' | ';

			if ($para['loopGbn'] == 'A'){
				$addFlag = $weeklyFlag;
			}else if ($para['loopGbn'] == '1'){
				if (getWeekInfo($date) == 1) $addFlag = $weeklyFlag;
			}else if ($para['loopGbn'] == '2'){
				if (getWeekInfo($date) == 2) $addFlag = $weeklyFlag;
			}else if ($para['loopGbn'] == '3'){
				if (getWeekInfo($date) == 3) $addFlag = $weeklyFlag;
			}else if ($para['loopGbn'] == '4'){
				if (getWeekInfo($date) == 4) $addFlag = $weeklyFlag;
			}else{
				$addFlag = true;
			}

			if ($addFlag){
				if (empty($sql)){
					$sql = 'insert into calendar (org_no,cld_yymm,cld_seq,cld_no,cld_dt,cld_from,cld_to,cld_fulltime,cld_subject,cld_contents,cld_reg_id,cld_reg_nm,insert_dt,insert_id) values ';
				}else{
					$sql .= ',';
				}
				$i ++;
				$sql .= '(\''.$code.'\'
						 ,\''.SubStr(str_replace('-','',$date),0,6)/*$yymm*/.'\'
						 ,\''.$seq.'\'
						 ,\''.$i.'\'
						 ,\''.$date.'\'
						 ,\''.$fromTime.'\'
						 ,\''.$toTime.'\'
						 ,\''.$fulltime.'\'
						 ,\''.$subject.'\'
						 ,\''.$contents.'\'
						 ,\''.$regID.'\'
						 ,\''.$regNM.'\'
						 ,now()
						 ,\''.$code.'\')';
			}

			$date = $myF->dateAdd('day', 1, $date, 'Y-m-d');

			if ($date > $toDate) break;
		}
	}else{
		$workType = 'update';
		$date = $fromDate;
		$i    = 0;

		while(true){
			$i ++;
			$sqls[$i] = 'update calendar
						   set cld_dt       = \''.$date.'\'
						,      cld_from     = \''.$fromTime.'\'
						,      cld_to       = \''.$toTime.'\'
						,      cld_fulltime = \''.$fulltime.'\'
						,      cld_subject  = \''.$subject.'\'
						,      cld_contents = \''.$contents.'\'
						,      update_dt    = now()
						,      update_id    = \''.$regID.'\'
						 where org_no       = \''.$code.'\'
						   and cld_yymm     = \''.$yymm.'\'
						   and cld_seq      = \''.$seq.'\'
						   and cld_no       = \''.$no.'\'
						   and del_flag     = \'N\'';

			$date = $myF->dateAdd('day', 1, $date, 'Y-m-d');

			if ($date > $toDate) break;
		}
	}

	$conn->begin();

	if (!empty($del_query)){
		if (!$conn->execute($del_query)){
			$conn->rollback();
			$result = 'error';
		}
	}


	if ($workType == 'insert'){
		if (!$conn->execute($sql)){
			$conn->rollback();
			$result = 'error';
		}
	}else{
		foreach($sqls as $i => $query){
			if (!$conn->execute($query)){
				$conn->rollback();
				$result = 'error';
			}
		}
	}


	if (empty($resutl)){
		$conn->commit();
		$result = 'ok';
	}

	echo $result;


	include_once('../inc/_db_close.php');
?>