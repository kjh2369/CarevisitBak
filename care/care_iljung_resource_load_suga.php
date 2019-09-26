<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$sr		= $_POST['sr'];
	$str	= $_POST['str'];

	$sql = 'SELECT	suga.cd1 AS mst_cd
			,		suga.nm1 AS mst_nm
			,		suga.cd2 AS pro_cd
			,		suga.nm2 AS pro_nm
			,		suga.cd3 AS svc_cd
			,		suga.nm3 AS svc_nm
			,		care.suga_cd AS suga_cd
			,		care.suga_sub AS sub_cd
			,		care.suga_seq AS sub_seq
			,		care.suga_nm AS sub_nm
			,		care.suga_cost AS sub_cost
			,		care.from_dt
			,		care.to_dt
			FROM	care_suga AS care
			INNER	JOIN	suga_care AS suga
					ON		cd1 = SUBSTR(care.suga_cd,1,1)
					AND		cd2 = SUBSTR(care.suga_cd,2,2)
					AND		cd3 = SUBSTR(care.suga_cd,4,2)
			WHERE	care.org_no = \''.$code.'\'
			AND		care.suga_sr= \''.$sr.'\'
			AND		LEFT(care.from_dt,4) <= \''.$year.'\'
			AND		LEFT(care.to_dt,  4) >= \''.$year.'\'';

	if ($str){
		$sql .= '
			AND		CONCAT(suga.nm1,\'/\',suga.nm2,\'/\',suga.nm3,\'/\',care.suga_nm) LIKE \'%'.$str.'%\'';
	}

	if ($IsCareYoyAddon){
		//공통수가
		$sql .='UNION	ALL
				SELECT	LEFT(code,1)
				,		\'\'
				,		MID(code,2,2)
				,		\'\'
				,		MID(code,4,2)
				,		\'공통수가\'
				,		LEFT(code,5)
				,		MID(code,6,2)
				,		\'1\'
				,		name
				,		\'0\'
				,		from_dt
				,		to_dt
				FROM	care_suga_comm
				WHERE	LEFT(from_dt,4) <= \''.$year.'\'
				AND		LEFT(to_dt,4)	>= \''.$year.'\'';
	}

	$sql .= '
			ORDER	BY mst_cd, pro_cd, svc_cd, sub_nm';

	#if ($debug) echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$data[$row['mst_cd']]){
			 $data[$row['mst_cd']]['nm'] = $row['mst_nm'];
		}
		$data[$row['mst_cd']]['cnt'] ++;

		if (!$data[$row['mst_cd']]['sub'][$row['pro_cd']]){
			 $data[$row['mst_cd']]['sub'][$row['pro_cd']]['nm'] = $row['pro_nm'];
		}
		$data[$row['mst_cd']]['sub'][$row['pro_cd']]['cnt'] ++;

		if (!$data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]){
			 $data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['nm'] = $row['svc_nm'];
		}
		$data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['cnt'] ++;

		if (!$data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['sub_cd'].'_'.$row['sub_seq']]){
			 $data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['sub_cd'].'_'.$row['sub_seq']] = Array(
				 'nm'=>$row['sub_nm']
				,'suga'=>$row['suga_cd'].$row['sub_cd']
				,'seq'=>$row['sub_seq']
				,'cost'=>$row['sub_cost']
				,'from'=>$row['from_dt']
				,'to'=>$row['to_dt']
			);
		}
	}

	$conn->row_free();

	if (!is_array($data)) exit;

	foreach($data as $mstCd => $mst){?>
		<tr>
		<td style="cursor:default;" rowspan="<?=$mst['cnt'];?>"><div class="left"><?=$mst['nm'];?></div></td><?
		foreach($mst['sub'] as $proCd => $pro){?>
			<td style="cursor:default;" rowspan="<?=$pro['cnt'];?>"><div class="left"><?=$pro['nm'];?></div></td><?
			foreach($pro['sub'] as $svcCd => $svc){?>
				<td style="cursor:default;" rowspan="<?=$svc['cnt'];?>"><div class="left"><?=$svc['nm'];?></div></td><?
				foreach($svc['sub'] as $subCd => $sub){?>
					<td class="last" style="cursor:default;">
						<div class="left bold" style="clean:both; border-bottom:1px dotted #BDBDBD; color:BLUE;"><?=$sub['nm'];?><span style="color:#FFFFFF; font-weight:normal;">(<?=$mstCd;?><?=$proCd;?><?=$svcCd;?><?=$subCd;?>)</span></div>
						<div style="clean:both; padding:2px 0 1px 5px;"><?
							$fromYm = SubStr(str_replace('-', '', $sub['from']), 0, 6);
							$toYm	= SubStr(str_replace('-', '', $sub['to']), 0, 6);

							for($j=1; $j<=12; $j++){
								$mon  = ($j < 10 ? '0' : '').$j;
								$yymm = $year.$mon;

								if ($fromYm <= $yymm && $toYm >= $yymm){?>
									<div id="ILJUNG_<?=$sub['suga'];?>_<?=$mon;?>" from="<?=str_replace('-','',$sub['from']);?>" to="<?=str_replace('-','',$sub['to']);?>" class="my_month my_month_2" style="float:left; <?=($j > 1 ? 'margin-left:3px;' : '');?>" onclick="lfRegIljung(this);"><a href="#" style="color:#747474;" onclick="return false;"><?=$j;?>월</a></div><?
								}else{?>
									<div class="my_month my_month_1" style="float:left; color:#D8D8D8; <?=($j > 1 ? 'margin-left:3px;' : '');?>"><?=$j;?>월</div><?
								}
							}?>
						</div>
					</td>
					</tr><?
				}
			}
		}
	}

	/*
		$sql = 'SELECT	DISTINCT
						CONCAT(suga_cd,suga_sub) AS cd
				,		suga_nm
				,		from_dt
				,		to_dt
				FROM	care_suga
				WHERE	org_no  = \''.$code.'\'
				AND		suga_sr = \''.$sr.'\'
				AND		DATE_FORMAT(from_dt,\'%Y\') <= \''.$year.'\'
				AND		DATE_FORMAT(to_dt,  \'%Y\') >= \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$fromYm = SubStr(str_replace('-', '', $row['from_dt']), 0, 6);
			$toYm	= SubStr(str_replace('-', '', $row['to_dt']), 0, 6);?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td class="center"><div class="left nowrap" style="width:200px;"><?=$row['suga_nm'];?></div></td>
				<td class="left last">
					<!--div id="btnMonth_" class="my_month my_month_1" style="float:left; color:#D8D8D8;">1월</div>
					<div id="btnMonth_" class="my_month my_month_2" style="float:left; color:#A2A2A2; margin-left:3px;">1월</div>
					<div id="btnMonth_" class="my_month my_month_y" style="float:left; color:#000000; margin-left:3px;">1월</div--><?
					for($j=1; $j<=12; $j++){
						$mon  = ($j < 10 ? '0' : '').$j;
						$yymm = $year.$mon;

						if ($fromYm <= $yymm && $toYm >= $yymm){?>
							<div id="ILJUNG_<?=$row['cd'];?>_<?=$mon;?>" from="<?=str_replace('-','',$row['from_dt']);?>" to="<?=str_replace('-','',$row['to_dt']);?>" class="my_month my_month_2" style="float:left; <?=($j > 1 ? 'margin-left:3px;' : '');?>" onclick="lfRegIljung(this);"><a href="#" style="color:#A2A2A2;" onclick="return false;"><?=$j;?>월</a></div><?
						}else{?>
							<div class="my_month" style="float:left; color:#D8D8D8; <?=($j > 1 ? 'margin-left:3px;' : '');?>">&nbsp;</div><?
						}
					}?>
				</td>
			</tr><?

			$no ++;
		}

		$conn->row_free();
	*/

	include_once('../inc/_db_close.php');
?>