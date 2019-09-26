<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	//단위
	$sql = 'SELECT	suga_cd AS cd
			,		unit_gbn AS gbn
			FROM	care_suga_unit
			WHERE	year = \''.$year.'\'';

	$unit = $conn->_fetch_array($sql,'cd');


	//수가
	$sql = 'SELECT	b.cd1 AS mst_cd, b.cd2 AS pro_cd, b.cd3 AS svc_cd, a.suga_sub AS sub_cd
			,		b.nm1 AS mst_nm, b.nm2 AS pro_nm, b.nm3 AS svc_nm, a.suga_nm AS sub_nm
			FROM	care_suga AS a
			INNER	JOIN	suga_care AS b
					ON		CONCAT(b.cd1, b.cd2, b.cd3) = a.suga_cd
					AND		\''.$yymm.'\' BETWEEN DATE_FORMAT(b.from_dt,\'%Y%m\') AND DATE_FORMAT(b.to_dt,\'%Y%m\')
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.suga_sr	= \''.$SR.'\'
			AND		\''.$yymm.'\' BETWEEN DATE_FORMAT(a.from_dt,\'%Y%m\') AND DATE_FORMAT(a.to_dt,\'%Y%m\')';

	$sql .= '
			UNION	ALL
			SELECT	\'Y\', \'OY\', \'01\', \'01\'
			,		\'\', \'\', \'공통수가\', \'요양보호사 일업무\'';

	$sql .= '
			ORDER	BY mst_cd, pro_cd, svc_cd, sub_cd';
	//if ($debug) echo '<tr><td colspan="6">'.nl2br($sql).'</td></tr>';
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$mst = $row['mst_cd'];
		$pro = $row['pro_cd'];
		$svc = $row['svc_cd'];
		$sub = $row['sub_cd'];

		if (!$DATA[$mst]) $DATA[$mst] = Array('name'=>$row['mst_nm'],'rows'=>0,'cnt'=>0);
		if (!$DATA[$mst]['LIST'][$pro]) $DATA[$mst]['LIST'][$pro] = Array('name'=>$row['pro_nm'],'rows'=>0,'cnt'=>0);
		if (!$DATA[$mst]['LIST'][$pro]['LIST'][$svc]) $DATA[$mst]['LIST'][$pro]['LIST'][$svc] = Array('name'=>$row['svc_nm'],'rows'=>0,'cnt'=>0);

		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['LIST'][$sub] = Array('name'=>$row['sub_nm'],'cnt'=>0);

		$DATA[$mst]['rows'] ++;
		$DATA[$mst]['LIST'][$pro]['rows'] ++;
		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['rows'] ++;

		if ($tmpCd1 != $mst.$pro.$svc){
			$DATA[$mst]['rows'] ++;
			$DATA[$mst]['LIST'][$pro]['rows'] ++;
			$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['rows'] ++;
			$tmpCd1 = $mst.$pro.$svc;
		}

		if ($tmpCd2 != $mst.$pro){
			$DATA[$mst]['rows'] ++;
			$DATA[$mst]['LIST'][$pro]['rows'] ++;
			$tmpCd2 = $mst.$pro;
		}

		if ($tmpCd3 != $mst){
			$DATA[$mst]['rows'] ++;
			$tmpCd3 = $mst;
		}
	}

	$conn->row_free();


	/*$sql = 'SELECT	MID(t01_suga_code1,1,1) AS mst_cd
			,		MID(t01_suga_code1,2,2) AS pro_cd
			,		MID(t01_suga_code1,4,2) AS svc_cd
			,		MID(t01_suga_code1,6,2) AS sub_cd
			,		COUNT(t01_jumin) AS itm_cnt
			,		COUNT(DISTINCT t01_jumin) AS per_cnt
			FROM	t01iljung
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
			GROUP	BY t01_suga_code1
			ORDER	BY t01_suga_code1';*/

	$sql = 'SELECT	t01_suga_code1 AS suga_cd
			,		MID(t01_suga_code1,1,1) AS mst_cd
			,		MID(t01_suga_code1,2,2) AS pro_cd
			,		MID(t01_suga_code1,4,2) AS svc_cd
			,		MID(t01_suga_code1,6,2) AS sub_cd
			,		COUNT(t01_jumin) AS itm_cnt
			,		COUNT(DISTINCT t01_jumin) AS per_cnt
			FROM	t01iljung
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \''.$SR.'\'
			AND		t01_del_yn = \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
			GROUP	BY t01_suga_code1
			UNION	ALL
			SELECT	suga_cd
			,		MID(suga_cd, 1, 1)
			,		MID(suga_cd, 2, 2)
			,		MID(suga_cd, 4, 2)
			,		MID(suga_cd, 6, 2)
			,		COUNT(suga_cd)
			,		SUM(att_cnt)
			FROM	care_rpt
			WHERE	org_no = \''.$orgNo.'\'
			AND		org_sr = \''.$SR.'\'
			AND		del_flag = \'N\'
			AND		LEFT(reg_dt, 6) = \''.$yymm.'\'
			GROUP	BY suga_cd
			ORDER	BY suga_cd';

	//if ($debug) echo '<tr><td colspan="6">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$totCnt = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($unit[$row['mst_cd'].$row['pro_cd'].$row['svc_cd']]['gbn'] == '1'){
			$cnt = $row['per_cnt'];
			$str = '명';
		}else{
			$cnt = $row['itm_cnt'];
			$str = '회';
		}

		$DATA[$row['mst_cd']]['cnt'] += $cnt;
		$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['cnt'] += $cnt;
		$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['cnt'] += $cnt;
		$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['cnt'] = number_format($cnt).$str;

		$totCnt += $cnt;
	}

	$conn->row_free();


	if (is_array($DATA)){?>
		<tr>
			<td class="bold" style="background-color:#DDEEF3;" colspan="4"><div class="right">총&nbsp;&nbsp;&nbsp;계</div></td>
			<td class="bold" style="background-color:#DDEEF3;"><div class="right"><?=$totCnt ? number_format($totCnt) : '';?></div></td>
			<td class="bold last" style="background-color:#DDEEF3;"></td>
		</tr><?
		foreach($DATA as $mstCd => $R1){?>
			<tr>
			<td class="top" rowspan="<?=$R1['rows'];?>"><div class="left"><?=$R1['name'];?></div></td>
			<td class="bold" style="background-color:#FDE9D9;" colspan="3"><div class="right">합&nbsp;&nbsp;&nbsp;계</div></td>
			<td class="bold" style="background-color:#FDE9D9;"><div class="right"><?=$R1['cnt'] ? number_format($R1['cnt']) : '';?></div></td>
			<td class="bold last" style="background-color:#FDE9D9;"></td>
			</tr><?

			foreach($R1['LIST'] as $proCd => $R2){?>
				<tr>
				<td class="top" rowspan="<?=$R2['rows'];?>"><div class="left"><?=$R2['name'];?></div></td>
				<td class="bold" style="background-color:#FFFF00;" colspan="2"><div class="right">소&nbsp;&nbsp;&nbsp;계</div></td>
				<td class="bold" style="background-color:#FFFF00;"><div class="right"><?=$R2['cnt'] ? number_format($R2['cnt']) : '';?></div></td>
				<td class="bold last" style="background-color:#FFFF00;"></td>
				</tr><?

				foreach($R2['LIST'] as $svcCd => $R3){?>
					<tr>
					<td class="top" rowspan="<?=$R3['rows'];?>"><div class="left"><?=$R3['name'];?></div></td>
					<td class="bold" style="background-color:#E4F7BA;"><div class="right">계</div></td>
					<td class="bold" style="background-color:#E4F7BA;"><div class="right"><?=$R3['cnt'] ? number_format($R3['cnt']) : '';?></div></td>
					<td class="bold last" style="background-color:#E4F7BA;"></td>
					</tr><?

					foreach($R3['LIST'] as $subCd => $R4){?>
						<tr>
						<td><div class="left"><?=$R4['name'];?></div></td>
						<td><div class="right"><?=$R4['cnt'] ? $R4['cnt'] : '';?></div></td>
						<td class="last"></td>
						</tr><?
					}
				}
			}
		}
	}else{?>
		<tr>
			<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($DATA);


	include_once('../inc/_db_close.php');
	exit;













	$sql = 'SELECT	t01_suga_code1 AS suga_cd, COUNT(DISTINCT t01_jumin) AS cnt_1, COUNT(t01_jumin) AS cnt_2
			,		a.cd1 AS mst_cd, a.cd2 AS pro_cd, a.cd3 AS svc_cd, b.suga_sub AS sub_cd
			,		a.nm1 AS mst_nm, a.nm2 AS pro_nm, a.nm3 AS svc_nm, b.suga_nm AS sub_nm
			FROM	t01iljung
			INNER	JOIN	suga_care AS a
					ON		a.cd1 = MID(t01_suga_code1,1,1)
					AND		a.cd2 = MID(t01_suga_code1,2,2)
					AND		a.cd3 = MID(t01_suga_code1,4,2)
					AND		REPLACE(a.from_dt,\'-\',\'\') <= t01_sugup_date
					AND		REPLACE(a.to_dt,\'-\',\'\') >= t01_sugup_date
			INNER	JOIN	care_suga AS b
					ON		b.org_no	= t01_ccode
					AND		b.suga_sr	= t01_mkind
					AND		b.suga_cd	= MID(t01_suga_code1,1,5)
					AND		b.suga_sub	= MID(t01_suga_code1,6,2)
					AND		REPLACE(a.from_dt,\'-\',\'\') <= t01_sugup_date
					AND		REPLACE(a.to_dt,\'-\',\'\') >= t01_sugup_date
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
			GROUP	BY t01_suga_code1
			ORDER	BY suga_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$mst = $row['mst_cd'];
		$pro = $row['pro_cd'];
		$svc = $row['svc_cd'];
		$sub = $row['sub_cd'];

		if (!$DATA[$mst]) $DATA[$mst] = Array('name'=>$row['mst_nm'], 'tot'=>0, 'cnt'=>0);
		if (!$DATA[$mst]['LIST'][$pro]) $DATA[$mst]['LIST'][$pro] = Array('name'=>$row['pro_nm'], 'tot'=>0, 'cnt'=>0);
		if (!$DATA[$mst]['LIST'][$pro]['LIST'][$svc]) $DATA[$mst]['LIST'][$pro]['LIST'][$svc] = Array('name'=>$row['svc_nm'], 'tot'=>0, 'cnt'=>0);

		$cnt = $row['cnt_'.($unit[$mst.$pro.$svc] == '1' ? '1' : '2')];

		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['LIST'][$sub] = Array(
			'name'	=>$row['sub_nm']
		,	'cnt'	=>$cnt.($unit[$mst.$pro.$svc] == '1' ? '명' : '회')
		);

		$DATA[$mst]['tot'] += $cnt;
		$DATA[$mst]['LIST'][$pro]['tot'] += $cnt;
		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['tot'] += $cnt;
		$tot += $cnt;

		$DATA[$mst]['cnt'] ++;
		$DATA[$mst]['LIST'][$pro]['cnt'] ++;
		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['cnt'] ++;

		if ($tmpCd1 != $mst.$pro.$svc){
			$DATA[$mst]['cnt'] ++;
			$DATA[$mst]['LIST'][$pro]['cnt'] ++;
			$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['cnt'] ++;
			$tmpCd1 = $mst.$pro.$svc;
		}

		if ($tmpCd2 != $mst.$pro){
			$DATA[$mst]['cnt'] ++;
			$DATA[$mst]['LIST'][$pro]['cnt'] ++;
			$tmpCd2 = $mst.$pro;
		}

		if ($tmpCd3 != $mst){
			$DATA[$mst]['cnt'] ++;
			$tmpCd3 = $mst;
		}
	}

	$conn->row_free();

	if (is_array($DATA)){
		foreach($DATA as $mstCd => $mstR){
			$IsFirst[0] = true;?>
			<tr>
			<td class="" rowspan="<?=$mstR['cnt'];?>"><div class="left"><?=$mstR['name'];?></div></td><?
			foreach($mstR['LIST'] as $proCd => $proR){
				if ($IsFirst[0]){
					$IsFirst[0] = false;
				}else{?>
					<tr><?
				}?>
				<td class="" rowspan="<?=$proR['cnt'];?>"><div class="left"><?=$proR['name'];?></div></td><?
				$IsFirst[1] = true;
				foreach($proR['LIST'] as $svcCd => $svcR){
					if ($IsFirst[1]){
						$IsFirst[1] = false;
					}else{?>
						<tr><?
					}?>
					<td class="" rowspan="<?=$svcR['cnt'];?>"><div class="left"><?=$svcR['name'];?></div></td><?
					$IsFirst[2] = true;
					foreach($svcR['LIST'] as $subCd => $subR){
						if ($IsFirst[2]){
							$IsFirst[2] = false;
						}else{?>
							<tr><?
						}?>
						<td><div class="left"><?=$subR['name'];?></div></td>
						<td><div class="right"><?=$subR['cnt'];?></div></td>
						<td class="last"></td>
						</tr><?
					}?>
					<tr>
						<td class="sum"><div class="right">소계</div></td>
						<td class="sum"><div class="right"><?=$svcR['tot'];?></div></td>
						<td class="sum last"></td>
					</tr><?
				}?>
				<tr>
					<td class="sum" colspan="2"><div class="right">소계</div></td>
					<td class="sum"><div class="right"><?=$proR['tot'];?></div></td>
					<td class="sum last"></td>
				</tr><?
			}?>
			<tr>
				<td class="sum" colspan="3"><div class="right">소계</div></td>
				<td class="sum"><div class="right"><?=$mstR['tot'];?></div></td>
				<td class="sum last"></td>
			</tr><?
		}?>
		<tr>
			<td class="sum" colspan="4"><div class="right">합계</div></td>
			<td class="sum"><div class="right"><?=$tot;?></div></td>
			<td class="sum last"></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($DATA);

	include_once('../inc/_db_close.php');
?>