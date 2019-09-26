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

	/*$sql = 'SELECT	a.suga_cd, b.suga_nm
			,		DATE_FORMAT(a.reg_dt, \'%c\') AS month
			,		COUNT(a.suga_cd) AS rpt_cnt
			,		SUM(a.att_cnt) AS att_cnt
			FROM	care_rpt AS a
			INNER	JOIN	care_suga AS b
					ON		b.org_no	= a.org_no
					AND		b.suga_sr	= a.org_sr
					AND		CONCAT(b.suga_cd, b.suga_sub) = a.suga_cd
					AND		DATE_FORMAT(b.from_dt, \'%Y%m%d\') <= a.reg_dt
					AND		DATE_FORMAT(b.to_dt, \'%Y%m%d\') >= a.reg_dt
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.org_sr	= \''.$SR.'\'
			AND		a.del_flag	= \'N\'
			AND		LEFT(a.reg_dt, 4) = \''.$year.'\'
			AND		LEFT(a.suga_cd, '.StrLen($suga_cd).') = \''.$suga_cd.'\'
			GROUP	BY LEFT(a.reg_dt, 6), a.suga_cd
			ORDER	BY suga_cd';*/

	$sql = 'SELECT	CONCAT(a.suga_cd, a.suga_sub) AS suga_cd, a.suga_nm
			,		DATE_FORMAT(b.reg_dt, \'%c\') AS month
			,		COUNT(b.suga_cd) AS rpt_cnt
			,		SUM(b.att_cnt) AS att_cnt
			FROM	care_suga AS a
			LEFT	JOIN	care_rpt AS b
					ON		b.org_no	 = a.org_no
					AND		b.org_sr	 = a.suga_sr
					AND		b.del_flag	 = \'N\'
					AND		b.suga_cd	 = CONCAT(a.suga_cd, a.suga_sub)
					AND		b.reg_dt	>= DATE_FORMAT(a.from_dt, \'%Y%m%d\')
					AND		b.reg_dt	<= DATE_FORMAT(a.to_dt, \'%Y%m%d\')
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.suga_sr	= \''.$SR.'\'
			AND		DATE_FORMAT(a.from_dt,	\'%Y\') <= \''.$year.'\'
			AND		DATE_FORMAT(a.to_dt,	\'%Y\') >= \''.$year.'\'
			AND		LEFT(b.reg_dt, 4) = \''.$year.'\'
			AND		LEFT(a.suga_cd, '.StrLen($suga_cd).') = \''.$suga_cd.'\'
			GROUP	BY LEFT(b.reg_dt, 6), a.suga_cd, a.suga_sub
			ORDER	BY suga_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$cnt = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		//$data[] = Array('suga_cd'=>$row['suga_cd'], 'suga_nm'=>$row['suga_nm'], 'rpt_cnt'=>$row['rpt_cnt'], 'att_cnt'=>$row['att_cnt']);

		if (!$data[$row['suga_cd']]){
			 $data[$row['suga_cd']] = Array('suga_nm'=>$row['suga_nm'], 'rpt_cnt'=>0, 'att_cnt'=>0);
			 $cnt ++;
		}
		if ($row['month'] == $month){
			$data[$row['suga_cd']]['rpt_cnt'] += $row['rpt_cnt'];
			$data[$row['suga_cd']]['att_cnt'] += $row['att_cnt'];
		}

		if ($row['month'] >= 1 && $row['month'] <= 3){
			$idx = 1;
		}else if ($row['month'] >= 4 && $row['month'] <= 6){
			$idx = 2;
		}else if ($row['month'] >= 7 && $row['month'] <= 9){
			$idx = 3;
		}else{
			$idx = 4;
		}

		$sum[$idx][$row['suga_cd']]['rpt_cnt'] += $row['rpt_cnt'];
		$sum[$idx][$row['suga_cd']]['att_cnt'] += $row['att_cnt'];
	}

	$conn->row_free();

	if (!is_array($data)) exit;
	if ($cnt < 1) $cnt = 1;

	echo '<div class="title title_border">해당월 집계</div>';
	echo '<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="8%">
			<col width="'.(92 / $cnt).'%" span="'.$cnt.'">
		</colgroup>
		<thead>
			<tr>
				<th class="head">구분</th>';

	foreach($data as $suga_cd => $R){
		echo '<th class="head">'.$R['suga_nm'].'</th>';
	}

	echo '</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">횟수</th>';

	foreach($data as $suga_cd => $R){
		echo '<td class="right">'.$R['rpt_cnt'].'</td>';
	}

	echo '</tr>
		<tr>
			<th class="center">인원/건수</th>';

	foreach($data as $suga_cd => $R){
		echo '<td class="right">'.$R['att_cnt'].'</td>';
	}

	echo '</tr></tbody></table>';
	echo '<div class="title title_border">분기별 집계</div>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="4%">
				<col width="8%">
				<col width="'.(88 / $cnt).'%" span="'.$cnt.'">
			</colgroup>
			<thead>
				<tr>
					<th class="head" colspan="2">구분</th>';

	foreach($data as $suga_cd => $R){
		echo '<th class="head">'.$R['suga_nm'].'</th>';
	}

	echo '</tr></thead>
		<tbody>';

	for($i=1; $i<=4; $i++){
		echo '<tr>';
		echo '<th class="center" rowspan="2">'.$i.'분기</th>
			<th class="center">횟수</th>';

		foreach($data as $suga_cd => $R){
			echo '<td class="right">'.$sum[$i][$suga_cd]['rpt_cnt'].'</td>';
		}
		echo '</tr>';

		echo '<tr><th class="center">인원/건수</th>';
		foreach($data as $suga_cd => $R){
			echo '<td class="right">'.$sum[$i][$suga_cd]['att_cnt'].'</td>';
		}

		echo '</tr>';
	}

	echo '</tbody></table>';

	unset($data);
	unset($sum);

	include_once('../inc/_db_close.php');
?>