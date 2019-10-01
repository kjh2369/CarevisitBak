<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 *	재가지원 수가코드 조회
	 */

	$code = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$type = $_POST['type'];
	$str = $_POST['str']; //검색어

	if (!$type){
		$nowrap = 'nowrap';
	}

	$sql = 'SELECT	suga.cd1 AS mst_cd
			,		suga.nm1 AS mst_nm
			,		suga.cd2 AS pro_cd
			,		suga.nm2 AS pro_nm
			,		suga.cd3 AS svc_cd
			,		suga.nm3 AS svc_nm
			,		care.suga_cd AS suga_cd
			FROM	care_suga AS care
			INNER	JOIN	suga_care AS suga
					ON		cd1 = SUBSTR(care.suga_cd,1,1)
					AND		cd2 = SUBSTR(care.suga_cd,2,2)
					AND		cd3 = SUBSTR(care.suga_cd,4,2)
			WHERE	care.org_no = \''.$code.'\'
			AND		care.suga_sr= \''.$SR.'\'';

	if ($str){
		$sql .= '
			AND		CONCAT(suga.nm1,\'/\',suga.nm2,\'/\',suga.nm3) LIKE \'%'.$str.'%\'';
	}

	$sql .= '
			ORDER	BY mst_cd, pro_cd, svc_cd';

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

		/*if (!$data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['sub_cd'].'_'.$row['sub_seq']]){
			 $data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['sub_cd'].'_'.$row['sub_seq']] = Array(
				 'nm'=>$row['sub_nm']
				,'suga'=>$row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd']
				,'seq'=>$row['sub_seq']
				,'cost'=>$row['sub_cost']
				,'from'=>$myF->dateStyle($row['from_dt'],'.')
				,'to'=>$myF->dateStyle($row['to_dt'],'.')
			);
		}*/
	}

	$conn->row_free();

	if (!is_array($data)) exit;

	foreach($data as $mstCd => $mst){?>
		<tr>
		<td style="cursor:default;" rowspan="<?=$mst['cnt'];?>"><?=$mst['nm'];?></td><?
		foreach($mst['sub'] as $proCd => $pro){?>
			<td style="cursor:default;" rowspan="<?=$pro['cnt'];?>"><?=$pro['nm'];?></td><?
			foreach($pro['sub'] as $svcCd => $svc){?>
				<td style="cursor:default;"><?=$svc['nm'];?></td>
				<td style="cursor:default;"><?
					if ($type == 'ILJUNG' || $type == 'CUST_SEL'){?>
						<a href="#" onclick="lfSetSuga('<?=$mstCd.$proCd.$svcCd;?>','<?=$mst['nm'];?>','<?=$pro['nm'];?>','<?=$svc['nm'];?>'); return false;">선택</a><?
					}?>
				</td>
				</tr><?
			}
		}
	}

	include_once('../inc/_db_close.php');
?>