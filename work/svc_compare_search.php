<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$duplicate = $_POST['duplicate'];
	$memName = $_POST['memName'];
	$tgtName = $_POST['tgtName'];

	if (!$duplicate) $duplicate = '1';

	$sql = 'SELECT	DISTINCT t01_jumin AS jumin, m03_name AS name, a.level, t01_svc_subcode as sub_cd, t01_yoyangsa_id1 AS mem_cd1, t01_yoyangsa_id2 AS mem_cd2, t01_yname1 AS mem_nm1, t01_yname2 AS mem_nm2, t01_mkind AS svc_cd, t01_conf_date AS date, t01_conf_fmtime AS from_time, t01_conf_totime AS to_time
			FROM	t01iljung
			INNER	JOIN	m03sugupja
					ON		m03_ccode = t01_ccode
					AND		m03_jumin = t01_jumin
					AND		m03_mkind = CASE WHEN t01_mkind = \'5\' THEN \'0\' ELSE t01_mkind END
			LEFT	JOIN	client_his_lvl AS a
					ON		a.org_no	= t01_ccode
					AND		a.svc_cd	= t01_mkind
					AND		a.jumin		= t01_jumin
					AND		REPLACE(a.from_dt, \'-\', \'\') <= t01_conf_date
					AND		REPLACE(a.to_dt, \'-\', \'\') >= t01_conf_date
			WHERE	t01_ccode  = \''.$orgNo.'\'
			AND		t01_mkind != \'5\'
			AND		t01_del_yn = \'N\'
			AND		t01_status_gbn	= \'1\'
			AND		LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
			AND		CASE WHEN IFNULL(t01_bipay_umu, \'N\') = \'Y\' THEN \'Y\' ELSE \'N\' END = \'N\'
			ORDER	BY mem_nm1, mem_cd1, date, from_time, to_time';

	//if ($debug) echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$date = $row['date'];

		for($j=1; $j<=2; $j++){
			$memCd = $row['mem_cd'.$j];

			if ($row['sub_cd'] == '200' && $j == 2) continue;
			if (!$memCd) continue;
			if (!is_array($data[$memCd])){
				$data[$memCd]['name'] = $row['mem_nm'.$j];
			}

			if (!is_array($data[$memCd][$date])){
				 $data[$memCd]['LIST'][$date]['duplicate'] = 'N'; //중복여부
			}

			$data[$memCd]['LIST'][$date]['LIST'][] = Array('name'=>$row['name'], 'fromTime'=>$row['from_time'], 'toTime'=>$row['to_time'], 'svcCd'=>$row['svc_cd'], 'duplicate'=>'N');
		}
	}

	$conn->row_free();

	if (is_array($data)){
		foreach($data as $memCd => $R1){
			foreach($R1['LIST'] as $date => $R2){
				if (is_array($R2) && count($R2['LIST']) > 1){
					foreach($R2['LIST'] as $tmpIdx => $R){
						$duplicateIndex = compareTime($R2['LIST'], $tmpIdx);
						if ($duplicateIndex > 0){
							$data[$memCd]['LIST'][$date]['duplicate'] = 'Y';
							$data[$memCd]['LIST'][$date]['LIST'][$tmpIdx]['duplicate'] = 'Y';
							$data[$memCd]['LIST'][$date]['LIST'][$duplicateIndex]['duplicate'] = 'Y';
							break;
						}
					}
				}

				if ($data[$memCd][$date]['duplicate']) break;
			}
		}

		$no = 0;
		$bgclr = '';

		foreach($data as $memCd => $R1){
			foreach($R1['LIST'] as $date => $R2){
				if ($memName){
					if (!is_numeric(StrPos($R1['name'], $memName))) continue;
				}

				if (($duplicate == '1' && $R2['duplicate'] == 'Y') || ($duplicate == '2')){
					$no ++;
					$rowspan = count($R2['LIST']);?>
					<tr style="background-color:<?=$bgclr;?>;">
					<td class="center" rowspan="<?=$rowspan;?>"><?=$no;?></td>
					<td class="center" rowspan="<?=$rowspan;?>"><?=$R1['name'];?></td>
					<td class="center" rowspan="<?=$rowspan;?>"><?=$myF->dateStyle($date, '.');?></td><?

					$IsFirst = true;
					foreach($R2['LIST'] as $tmpI => $R){
						if ($IsFirst){
							$IsFirst = false;
						}else{?>
							<tr style="background-color:<?=$bgclr;?>;"><?
						}?>
						<td class="center" style="color:<?=$R['duplicate'] == 'Y' ? 'red' : '';?>;"><?=$myF->timeStyle($R['fromTime']);?>~<?=$myF->timeStyle($R['toTime']);?></td>
						<td class="center"><?=$R['name'];?></td>
						<td class="center"><?=$conn->kind_name_svc($R['svcCd']);?></td>
						<td class="center last"></td>
						</tr><?
					}
				}
			}

			if ($bgclr){
				$bgclr = '';
			}else{
				$bgclr = '';
			}
		}

		if (!$no){?>
			<tr>
				<td class="center last" colspan="7">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}
	}

	function compareTime($list, $idx){
		GLOBAL $myF;

		$cnt = count($list);

		$orgFrom = $myF->time2min($list[$idx]['fromTime']);
		$orgTo = $myF->time2min($list[$idx]['toTime']);

		for($i=0; $i<$cnt; $i++){
			if ($i == $idx) continue;

			$fromTime = $myF->time2min($list[$i]['fromTime']);
			$toTime = $myF->time2min($list[$i]['toTime']);

			if (($fromTime >= $orgFrom && $fromTime <= $orgTo) ||
				($toTime >= $orgFrom && $toTime <= $orgTo) ||
				($orgFrom >= $fromTime && $orgFrom <= $toTime) ||
				($orgTo >= $fromTime && $orgTo <= $toTime)){
				return $i;
			}
		}

		return 0;
	}

	include_once('../inc/_db_close.php');
?>