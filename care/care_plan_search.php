<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$type	= $_POST['type'];
	$year	= $_POST['year'];

	//데이타
	$sql = 'SELECT	plan_cd AS cd
			,		plan_target AS target
			,		plan_target_gbn AS gbn
			,		plan_budget AS budget
			,		plan_cnt AS cnt
			,		plan_cont AS cont
			,		plan_effect AS effect
			,		plan_eval AS eval
			FROM	care_year_plan
			WHERE	org_no		= \''.$orgNo.'\'
			AND		plan_year	= \''.$year.'\'
			AND		plan_sr		= \''.$SR.'\'';
	//if($debug) echo '<tr><td colspan="11">'.nl2br($sql).'</td></tr>';
	$plan = $conn->_fetch_array($sql,'cd');

	$sql = 'SELECT	DISTINCT
					care.suga_cd
			,		suga.cd1 AS mst_cd
			,		suga.cd2 AS pro_cd
			,		suga.cd3 AS svc_cd
			,		suga.nm1 AS mst_nm
			,		suga.nm2 AS pro_nm
			,		suga.nm3 AS svc_nm
			,		care.suga_sub AS sub_cd
			,		care.suga_nm AS sub_nm
			FROM	care_suga AS care
			INNER	JOIN suga_care AS suga
					ON	CONCAT(suga.cd1,suga.cd2,suga.cd3) = care.suga_cd
			WHERE	care.org_no	 = \''.$orgNo.'\'
			AND		care.suga_sr = \''.$SR.'\'
			AND		LEFT(care.from_dt,4) <= \''.$year.'\'
			AND		LEFT(care.to_dt,4)	 >= \''.$year.'\'';

	//if($debug) echo '<tr><td colspan="11">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$mst = $row['mst_cd'];
		$pro = $row['pro_cd'];
		$svc = $row['svc_cd'];
		$sub = $row['sub_cd'];

		if (!$DATA[$mst]) $DATA[$mst] = Array('name'=>$row['mst_nm'], 'cnt'=>0);
		if (!$DATA[$mst]['LIST'][$pro]) $DATA[$mst]['LIST'][$pro] = Array('name'=>$row['pro_nm'], 'cnt'=>0);
		if (!$DATA[$mst]['LIST'][$pro]['LIST'][$svc]) $DATA[$mst]['LIST'][$pro]['LIST'][$svc] = Array('name'=>$row['svc_nm'], 'cnt'=>0);

		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['LIST'][$sub] = Array(
			'name'	=>$row['sub_nm']
		,	'target'=>$plan[$mst.$pro.$svc.$sub]['target']
		,	'gbn'	=>$plan[$mst.$pro.$svc.$sub]['gbn']
		,	'budget'=>$plan[$mst.$pro.$svc.$sub]['budget']
		,	'cnt'	=>$plan[$mst.$pro.$svc.$sub]['cnt']
		,	'cont'	=>$plan[$mst.$pro.$svc.$sub]['cont']
		,	'effect'=>$plan[$mst.$pro.$svc.$sub]['effect']
		,	'eval'	=>$plan[$mst.$pro.$svc.$sub]['eval']
		);

		$DATA[$mst]['cnt'] ++;
		$DATA[$mst]['LIST'][$pro]['cnt'] ++;
		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['cnt'] ++;
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
						<td><div class="right"><?=$subR['target'];?><?=$subR['target'] ? $subR['gbn'] == '1' ? '명' : '회' : '';?></div></td>
						<td><div class="right"><?=$subR['budget'];?></div></td>
						<td><div class="right"><?=$subR['cnt'];?></div></td>
						<td><div class="left nowrap" style="width:80px;" title="<?=$subR['cont'];?>"><?=$subR['cont'];?></div></td>
						<td><div class="left nowrap" style="width:80px;" title="<?=$subR['effect'];?>"><?=$subR['effect'];?></div></td>
						<td><div class="left nowrap" style="width:80px;" title="<?=$subR['eval'];?>"><?=$subR['eval'];?></div></td>
						<td class="last">
							<span class="btn_pack small" style="margin-left:5px;"><button style="color:BLUE;" onclick="lfPlanReg(__GetTagObject($(this),'TR'),'<?=$mstCd.$proCd.$svcCd.$subCd;?>');">수정</button></span>
							<span class="btn_pack small"><button style="color:RED;" onclick="lfPlanDel(__GetTagObject($(this),'TR'),'<?=$mstCd.$proCd.$svcCd.$subCd;?>');">삭제</button></span>
						</td>
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
?>