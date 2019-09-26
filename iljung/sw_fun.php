<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$type = $_POST['type'];
	$year = $_POST['year'];
	$month = IntVal($_POST['month']);
	$yymm = $year.($month < 10 ? '0' : '').$month;

	function user_compare_data($a, $b){
		if ($a == $b){
			return 0;
		}
		return ($a < $b) ? -1 : 1;
	}

	function iljungData($memCd = '', $prtYn = 'Y'){
		global $conn, $myF, $ed, $orgNo, $yymm, $year, $month, $IsExcel, $debug;

		//일정표
		$sql = 'SELECT	DISTINCT a.reg_jumin, a.reg_name, CAST(DATE_FORMAT(a.date,\'%d\') AS unsigned) AS day, a.time, a.to_time, m03_key AS key_cd, m03_name AS name, b.level
				FROM	sw_log AS a
				INNER	JOIN	m03sugupja
						ON		m03_ccode = a.org_no
						AND		m03_mkind = \'0\'
						AND		m03_jumin = a.jumin
				LEFT	JOIN	client_his_lvl AS b
						ON		b.org_no   = a.org_no
						AND		b.jumin    = a.jumin
						AND		b.svc_cd   = \'0\'
						AND		b.from_dt <= DATE_FORMAT(a.date, \'%Y-%m-%d\')
						AND		b.to_dt   >= DATE_FORMAT(a.date, \'%Y-%m-%d\')
				WHERE	a.org_no = \''.$orgNo.'\'
				AND		a.yymm	 = \''.$yymm.'\'
				AND		a.reg_jumin = \''.$memCd.'\'
				AND		a.del_flag	= \'N\'
				ORDER	BY a.reg_name, a.reg_jumin, a.date, a.time, a.to_time';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (!$row['to_time']) $row['to_time'] = $myF->min2time($myF->time2min($row['time']) + 30);

			if (!$tmpKey[$row['key_cd']]){
				$tmpKey[$row['key_cd']] = $row['key_cd'];
				$tgCnt = 1;
			}else{
				$tgCnt = 0;
			}

			$log[$row['day']][] = Array('from'=>$myF->time2min($row['time']), 'to'=>$myF->time2min($row['to_time']), 'work'=>$row['name'], 'tgCnt'=>$tgCnt, 'level'=>$row['level'], 'bold'=>'Y');
			#$log[$row['day']][] = Array('from'=>$myF->time2min($row['time']), 'to'=>$myF->time2min($row['to_time']), 'work'=>$row['name'], 'bold'=>'Y');
		}

		$conn->row_free();

		//휴일
		$sql = 'SELECT	CAST(DATE_FORMAT(mdate, \'%d\') AS unsigned) AS day, holiday_name AS holiday
				FROM	tbl_holiday
				WHERE	LEFT(mdate,6) = \''.$yymm.'\'';

		$holiday = $conn->_fetch_array($sql,'day');
		$lastday = $myF->lastday($year, $month);

		for($i=1; $i<=$lastday; $i++){
			$w = Date('w', StrToTime($year.($month < 10 ? '0' : '').$month.($i < 10 ? '0' : '').$i));

			$iljung[$i] = $log[$i];

			if ($w == 0 || $holiday[$i]){
			}else if ($w == 6){
				//$iljung[$i][] = Array('from'=>600, 'to'=>840, 'work'=>'내근', 'bold'=>'N');
			}else{
				if ($prtYn == 'Y'){
					$iljung[$i][] = Array('from'=>540, 'to'=>720, 'work'=>'내근', 'tgCnt'=>0, 'level'=>'', 'bold'=>'N');
					$iljung[$i][] = Array('from'=>720, 'to'=>780, 'work'=>'점심', 'tgCnt'=>0, 'level'=>'', 'bold'=>'N');
					$iljung[$i][] = Array('from'=>780, 'to'=>1080, 'work'=>'내근', 'tgCnt'=>0, 'level'=>'', 'bold'=>'N');
					#$iljung[$i][] = Array('from'=>540, 'to'=>720, 'work'=>'내근', 'bold'=>'N');
					#$iljung[$i][] = Array('from'=>720, 'to'=>780, 'work'=>'점심', 'bold'=>'N');
					#$iljung[$i][] = Array('from'=>780, 'to'=>1080, 'work'=>'내근', 'bold'=>'N');
				}
			}

			if (is_array($iljung[$i])){
				usort($iljung[$i], 'user_compare_data');

				$row = $iljung[$i];
				$cnt = count($row) - 1;
				$j = 0;

				while(true){
					if ($j > 20) break;
					if (!IsSet($row[$j])) break;

					$k = $j + 1;

					if ($row[$j]['from'] < $row[$k]['from'] && $row[$j]['to'] > $row[$k]['from']){
						if ($row[$j]['bold'] == 'Y'){
							$row[$k]['from'] = $myF->cutOff($row[$j]['to'],30)+30;
						}else{
							$row[$j]['to'] = $myF->cutOff($row[$k]['from']-10,30);
						}

						if ($row[$j]['from'] >= $row[$k]['from'] || $row[$j]['from'] >= $row[$j]['to']){
							unset($row[$j]);
							usort($row, 'user_compare_data');
							$cnt = count($row) - 1;
						}
					}

					if ($w >= 1 && $w <= 5){
						if ($row[$k]['from'] - $row[$j]['to'] > 30){
							if ($prtYn == 'Y' && $myF->cutOff($row[$j]['to']+30,30) < $myF->cutOff($row[$k]['from'],30)){
								$row[] = Array('from'=>$myF->cutOff($row[$j]['to']+30,30), 'to'=>$myF->cutOff($row[$k]['from'],30), 'work'=>'내근', 'tgCnt'=>0, 'level'=>'', 'bold'=>'N');
								#$row[] = Array('from'=>$myF->cutOff($row[$j]['to']+30,30), 'to'=>$myF->cutOff($row[$k]['from'],30), 'work'=>'내근', 'bold'=>'N');
								usort($row, 'user_compare_data');
								$cnt = count($row) - 1;
							}
						}

						if ($prtYn == 'Y' && $j == $cnt && $row[$j]['to'] <= 1050){
							$row[] = Array('from'=>$myF->cutOff($row[$j]['to']+30,30), 'to'=>1080, 'work'=>'내근', 'tgCnt'=>0, 'level'=>'', 'bold'=>'N');
							#$row[] = Array('from'=>$myF->cutOff($row[$j]['to']+30,30), 'to'=>1080, 'work'=>'내근', 'bold'=>'N');
							usort($row, 'user_compare_data');
							$cnt = count($row) - 1;
						}
					}else if ($w == 6){
						if ($j == $cnt && $row[$j]['to'] <= 810){
							//$row[] = Array('from'=>$myF->cutOff($row[$j]['to']+30,30), 'to'=>840, 'work'=>'내근', 'bold'=>'N');
							//usort($row, 'user_compare_data');
							//$cnt = count($row) - 1;
						}
					}

					$j ++;
				}

				$iljung[$i] = $row;

				usort($iljung[$i], 'user_compare_data');
			}
		}

		if ($IsExcel) return $iljung;
		if (is_array($iljung)){
			$rstStr = '';

			foreach($iljung as $day => $R1){
				$rstStr .= ($rstStr ? '&' : '').$day.'=';

				if (is_array($R1)){
					foreach($R1 as $tmpIdx => $R2){
						$rstStr .= ($tmpIdx > 0 ? '|' : '').'from+'.$R2['from'].'$to+'.$R2['to'].'$work+'.$R2['work'].'$bold+'.$R2['bold'].'$tgCnt+'.$R2['tgCnt'];
						#$rstStr .= ($tmpIdx > 0 ? '|' : '').'from+'.$R2['from'].'$to+'.$R2['to'].'$work+'.$R2['work'].'$bold+'.$R2['bold'];
					}
				}
			}

			return $rstStr;
		}
	}

	if ($IsExcel) return;

	if ($type == 'SW_LIST'){
		//사회복지사 리스트
		$sql = 'SELECT	DISTINCT m02_yjumin AS mem_cd, m02_yname AS mem_nm
				FROM	m02yoyangsa
				INNER	JOIN	mem_his AS a
						ON		a.org_no = m02_ccode
						AND		a.jumin = m02_yjumin
						AND		DATE_FORMAT(a.join_dt,\'%Y%m\') <= \''.$yymm.'\'
						AND		DATE_FORMAT(IFNULL(a.quit_dt,\'9999-12-31\'),\'%Y%m\') >= \''.$yymm.'\'
				WHERE	m02_ccode = \''.$orgNo.'\'
				AND		m02_jikwon_gbn IN (\'B\',\'C\',\'D\',\'W\')
				ORDER	BY mem_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<div style="<?=$i > 0 ? 'border-top:1px solid #CCCCCC;' : '';?>" memCd="<?=$ed->en($row['mem_cd']);?>">
				<span style="padding-left:5px;"><?=$row['mem_nm'];?>(<?=$myF->issToBirthday($row['mem_cd'],'.');?>)</span>
			</div><?
		}

		$conn->row_free();


	}else if ($type == 'SW_ILJUNG'){
		$memCd = $ed->de($_POST['memCd']);
		$prtYn = $_POST["prtYn"];
		echo iljungData($memCd, $prtYn);
		/*
		//일정표
		$sql = 'SELECT	DISTINCT a.reg_jumin, a.reg_name, CAST(DATE_FORMAT(a.date,\'%d\') AS unsigned) AS day, a.time, a.to_time, m03_name AS name
				FROM	sw_log AS a
				INNER	JOIN	m03sugupja
						ON		m03_ccode = a.org_no
						AND		m03_mkind = \'0\'
						AND		m03_jumin = a.jumin
				WHERE	a.org_no = \''.$orgNo.'\'
				AND		a.yymm	 = \''.$yymm.'\'
				AND		a.reg_jumin = \''.$memCd.'\'
				AND		a.del_flag	= \'N\'
				ORDER	BY a.reg_name, a.reg_jumin, a.date, a.time, a.to_time';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (!$row['to_time']) $row['to_time'] = $myF->min2time($myF->time2min($row['time']) + 30);

			$log[$row['day']][] = Array('from'=>$myF->time2min($row['time']), 'to'=>$myF->time2min($row['to_time']), 'work'=>$row['name'], 'bold'=>'Y');
		}

		$conn->row_free();

		//휴일
		$sql = 'SELECT	CAST(DATE_FORMAT(mdate, \'%d\') AS unsigned) AS day, holiday_name AS holiday
				FROM	tbl_holiday
				WHERE	LEFT(mdate,6) = \''.$yymm.'\'';

		$holiday = $conn->_fetch_array($sql,'day');
		$lastday = $myF->lastday($year, $month);

		for($i=1; $i<=$lastday; $i++){
			$w = Date('w', StrToTime($year.($month < 10 ? '0' : '').$month.($i < 10 ? '0' : '').$i));

			$iljung[$i] = $log[$i];

			if ($w == 0 || $holiday[$i]){
			}else if ($w == 6){
				$iljung[$i][] = Array('from'=>600, 'to'=>840, 'work'=>'내근', 'bold'=>'N');
			}else{
				$iljung[$i][] = Array('from'=>540, 'to'=>720, 'work'=>'내근', 'bold'=>'N');
				$iljung[$i][] = Array('from'=>720, 'to'=>780, 'work'=>'점심', 'bold'=>'N');
				$iljung[$i][] = Array('from'=>780, 'to'=>1080, 'work'=>'내근', 'bold'=>'N');
			}

			if (is_array($iljung[$i])){
				usort($iljung[$i], 'user_compare_data');

				$row = $iljung[$i];
				$cnt = count($row) - 1;
				$j = 0;

				while(true){
					if ($j > 20) break;
					if (!IsSet($row[$j])) break;

					$k = $j + 1;

					if ($row[$j]['from'] < $row[$k]['from'] && $row[$j]['to'] > $row[$k]['from']){
						if ($row[$j]['bold'] == 'Y'){
							$row[$k]['from'] = $myF->cutOff($row[$j]['to'],30)+30;
						}else{
							$row[$j]['to'] = $myF->cutOff($row[$k]['from']-10,30);
						}

						if ($row[$j]['from'] >= $row[$k]['from'] || $row[$j]['from'] == $row[$j]['to']){
							unset($row[$j]);
							usort($row, 'user_compare_data');
							$cnt = count($row) - 1;
						}
					}

					if ($w >= 1 && $w <= 5){
						if ($row[$k]['from'] - $row[$j]['to'] > 30){
							if ($myF->cutOff($row[$j]['to']+30,30) < $myF->cutOff($row[$k]['from'],30)){
								$row[] = Array('from'=>$myF->cutOff($row[$j]['to']+30,30), 'to'=>$myF->cutOff($row[$k]['from'],30), 'work'=>'내근A', 'bold'=>'N');
								usort($row, 'user_compare_data');
								$cnt = count($row) - 1;
							}
						}

						if ($j == $cnt && $row[$j]['to'] <= 1050){
							$row[] = Array('from'=>$myF->cutOff($row[$j]['to']+30,30), 'to'=>1080, 'work'=>'내근', 'bold'=>'N');
							usort($row, 'user_compare_data');
							$cnt = count($row) - 1;
						}
					}else if ($w == 6){
						if ($j == $cnt && $row[$j]['to'] <= 810){
							$row[] = Array('from'=>$myF->cutOff($row[$j]['to']+30,30), 'to'=>840, 'work'=>'내근', 'bold'=>'N');
							usort($row, 'user_compare_data');
							$cnt = count($row) - 1;
						}
					}

					$j ++;
				}

				$iljung[$i] = $row;

				usort($iljung[$i], 'user_compare_data');
			}
		}

		if (is_array($iljung)){
			$rstStr = '';

			foreach($iljung as $day => $R1){
				$rstStr .= ($rstStr ? '&' : '').$day.'=';

				if (is_array($R1)){
					foreach($R1 as $tmpIdx => $R2){
						$rstStr .= ($tmpIdx > 0 ? '|' : '').'from+'.$R2['from'].'$to+'.$R2['to'].'$work+'.$R2['work'].'$bold+'.$R2['bold'];
					}
				}
			}

			echo $rstStr;
		}
		*/


	}else if ($type == 'CALENDAR'){
		//휴일
		$sql = 'SELECT	DATE_FORMAT(mdate, \'%d\') AS date, holiday_name AS holiday
				FROM	tbl_holiday
				WHERE	LEFT(mdate,6) = \''.$yymm.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$rstStr = '';

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$rstStr .= ($rstStr ? '?' : '').'day='.IntVal($row['date']).'&holiday='.$row['holiday'];
		}

		$conn->row_free();

		echo $rstStr;


	}else{
	}

	include_once('../inc/_db_close.php');
?>