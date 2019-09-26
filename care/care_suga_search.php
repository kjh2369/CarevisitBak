<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 *	재가지원 수가코드 조회
	 */

	$code = $_SESSION['userCenterCode'];
	$SR = $_POST['sr'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$type = $_POST['type'];
	$str = $_POST['str']; //검색어

	if (!$type){
		$nowrap = 'nowrap';
	}

	$sql = 'SELECT	DISTINCT
					suga.cd1 AS mst_cd
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
			AND		care.suga_sr= \''.$SR.'\'';

	if ($type == 'ILJUNG'){
		$sql .= '
			AND		DATE_FORMAT(care.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(care.to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
	}else{
		$sql .= '
			AND		DATE_FORMAT(care.from_dt,\'%Y%m%d\') <= DATE_FORMAT(NOW(), \'%Y%m%d\')
			AND		DATE_FORMAT(care.to_dt,  \'%Y%m%d\') >= DATE_FORMAT(NOW(), \'%Y%m%d\')';
	}

	if ($str){
		$sql .= '
			AND		CONCAT(suga.nm1,\'/\',suga.nm2,\'/\',suga.nm3,\'/\',care.suga_nm) LIKE \'%'.$str.'%\'';
	}

	$sql .= '
			ORDER	BY mst_cd, pro_cd, svc_cd, sub_nm,sub_cd,sub_seq DESC';

	//if ($debug) echo '<tr><td colspan="6">'.nl2br($sql).'</td></tr>';

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
				,'suga'=>$row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd']
				,'seq'=>$row['sub_seq']
				,'cost'=>$row['sub_cost']
				,'from'=>$myF->dateStyle($row['from_dt'],'.')
				,'to'=>$myF->dateStyle($row['to_dt'],'.')
			);
		}
	}

	$conn->row_free();

	if (!is_array($data)) exit;

	foreach($data as $mstCd => $mst){?>
		<tr>
		<td style="cursor:default;" rowspan="<?=$mst['cnt'];?>"><div class="left"><?=$mst['nm'];?></div></td><?
		foreach($mst['sub'] as $proCd => $pro){?>
			<td style="cursor:default;" rowspan="<?=$pro['cnt'];?>"><div class="left" <?=$nowrap;?>><?=$pro['nm'];?></div></td><?
			foreach($pro['sub'] as $svcCd => $svc){?>
				<td style="cursor:default;" rowspan="<?=$svc['cnt'];?>"><div class="left <?=$nowrap;?>"><?=$svc['nm'];?></div></td><?
				foreach($svc['sub'] as $subCd => $sub){
					if ($type == 'ILJUNG' || $type == 'CUST_SEL'){?>
						<td style="cursor:default;"><div class="left <?=$nowrap;?>"><a href="#" onclick="lfSetSuga('<?=$sub['suga'];?>','<?=$mst['nm'];?>','<?=$pro['nm'];?>','<?=$svc['nm'];?>','<?=str_replace('"','',$sub['nm']);?>'); return false;"><?=$sub['nm'];?></a></div></td>
						<td class="last" style="cursor:default;"><div class="left"><a href="#" onclick="lfSetSuga('<?=$sub['suga'];?>','<?=$mst['nm'];?>','<?=$pro['nm'];?>','<?=$svc['nm'];?>','<?=str_replace('"','',$sub['nm']);?>'); return false;">선택</a></div></td><?
					}else{?>
						<td style="cursor:default;"><div class="left <?=$nowrap;?>"><?=$sub['nm'];?></div></td>
						<!--td style="cursor:default;"><div class="right"><?=$sub['cost'];?></div></td-->
						<td class="center" style="cursor:default;"><?=$sub['from'];?>~<?=$sub['to'];?></td>
						<td class="last" style="cursor:default;"></td><?
					}?>
					</tr><?
				}
			}
		}
	}

	if ($IsCareYoyAddon){
		//공통수가
		$sql = 'SELECT	code
				,		name
				,		from_dt
				,		to_dt
				FROM	care_suga_comm
				WHERE	code != \'\'';

		if ($type == 'ILJUNG'){
			$sql .= '
				AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
		}

		if ($str){
			$sql .= '
				AND		name LIKE \'%'.$str.'%\'';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr><?
				if ($i == 0){?>
					<td style="cursor:default;" rowspan="<?=$rowCnt;?>" colspan="3">&nbsp;공통항목</td><?
				}

				if ($type == 'ILJUNG' || $type == 'CUST_SEL'){?>
					<td style="cursor:default;"><div class="left"><a href="#" onclick="lfSetSuga('<?=$row['code'];?>','','','','<?=$row['name'];?>'); return false;"><?=$row['name'];?></a></div></td>
					<td class="last" style="cursor:default;"><div class="left"><a href="#" onclick="lfSetSuga('<?=$row['code'];?>','','','','<?=$row['name'];?>'); return false;">선택</a></div></td><?
				}else{?>
					<td style="cursor:default;"><div class="left <?=$nowrap;?>"><?=$row['name'];?></div></td>
					<!--td style="cursor:default;"><div class="right"><?=$sub['cost'];?></div></td-->
					<td class="center" style="cursor:default;"><?=$myF->dateStyle($row['from_dt'],'.');?>~<?=$myF->dateStyle($row['to_dt'],'.');?></td>
					<td class="last" style="cursor:default;"></td><?
				}?>
			</tr><?
		}

		$conn->row_free();
	}

	include_once('../inc/_db_close.php');
?>