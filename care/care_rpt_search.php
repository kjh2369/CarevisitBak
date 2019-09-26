<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$suga_cd = $_POST['suga_cd'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$yymm = $year.($month < 10 ? '0' : '').$month;
	$lastday = $myF->lastday($year, $month);

	//$classIdx = Array('B9901'=>1, 'B9902'=>2, 'B9903'=>3, 'B9904'=>4);


	//휴일
	$sql = 'SELECT	mdate AS date, holiday_name AS name
			FROM	tbl_holiday
			WHERE	LEFT(mdate, 6) = \''.$yymm.'\'';

	$holiday = $conn->_fetch_array($sql, 'date');


	//일정
	$sql = 'SELECT	DATE_FORMAT(a.reg_dt, \'%e\') AS day, a.reg_dt, a.seq, a.suga_cd, b.suga_nm, a.att_cnt
			FROM	care_rpt AS a
			INNER	JOIN	care_suga AS b
					ON		b.org_no	 = a.org_no
					AND		b.suga_sr	 = a.org_sr
					AND		b.suga_cd	 = MID(a.suga_cd, 1, 5)
					AND		b.suga_sub	 = MID(a.suga_cd, 6)
					AND		b.from_dt	<= DATE_FORMAT(a.reg_dt, \'%Y-%m-%d\')
					AND		b.to_dt		>= DATE_FORMAT(a.reg_dt, \'%Y-%m-%d\')
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.org_sr	= \''.$SR.'\'
			AND		a.del_flag	= \'N\'
			AND		LEFT(a.reg_dt, 6) = \''.$yymm.'\'
			AND		LEFT(a.suga_cd, '.StrLen($suga_cd).') = \''.$suga_cd.'\'
			ORDER	BY reg_dt, suga_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$rpt[$row['day']][] = Array('reg_dt'=>$row['reg_dt'], 'seq'=>$row['seq'], 'suga_cd'=>$row['suga_cd'], 'suga_nm'=>$row['suga_nm'], 'att_cnt'=>$row['att_cnt']);
	}

	$conn->row_free();


	$html = '<tr>';
	$tmpIdx = 1;

	$dowidx = $myF->dowidx($yymm.'01');
	for($i=0; $i<$dowidx; $i++){
		$html .= '<td>&nbsp;</td>';
	}

	for($i=1; $i<=$lastday; $i++){
		$dt = $yymm.($i < 10 ? '0' : '').$i;
		$dowidx = $myF->dowidx($dt);

		if ($dowidx == 6){
			$color = 'blue';
		}else if ($dowidx == 0 || $holiday[$dt]['name']){
			$color = 'red';
		}else{
			$color = '';
		}

		$id = 'CELL_'.$i;

		$str = '<div style="clear:both; color:'.$color.';">';
		$str .= '<div class="left" style="float:left; width:auto;">'.$i.'</div>';

		if ($holiday[$dt]['name']) $str .= '<div class="right" style="float:right; width:auto;">'.$holiday[$dt]['name'].'</div>';

		for($j=0; $j<count($rpt[$i]); $j++){
			if (!$classIdx[$rpt[$i][$j]['suga_cd']]){
				 $classIdx[$rpt[$i][$j]['suga_cd']] = $tmpIdx;
				 $tmpIdx ++;
			}
			$str .= '<div class="rpt'.$classIdx[$rpt[$i][$j]['suga_cd']].'" style="clear:both;"><a href="#" onclick="lfRptReg(\''.$rpt[$i][$j]['suga_cd'].'\', \''.$rpt[$i][$j]['seq'].'\')">'.$rpt[$i][$j]['suga_nm'].'/'.$rpt[$i][$j]['att_cnt'].'</a></div>';
		}

		$str .= '</div>';

		if ($dowidx == 6){
			$html .= '<td id="'.$id.'" class="top last">'.$str.'</td>';
			$html .= '</tr>';
		}else if ($dowidx == 0){
			$html .= '<tr>';
			$html .= '<td id="'.$id.'" class="top">'.$str.'</td>';
		}else{
			$html .= '<td id="'.$id.'" class="top">'.$str.'</td>';
		}
	}

	$dowidx = $myF->dowidx($yymm.$lastday);
	for($i=$dowidx+1; $i<=6; $i++){
		$html .= '<td class="'.($i == 6 ? 'last' : '').'">&nbsp;</td>';
	}

	echo $html;

	unset($holiday);

	include_once('../inc/_db_close.php');
?>