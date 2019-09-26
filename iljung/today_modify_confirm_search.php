<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//기관
	$orgNo = $_SESSION['userCenterCode'];

	//TODAY
	$today = Date('Ymd');

	//서비스
	$subNm = Array('200'=>'방문요양','500'=>'방문목욕','800'=>'방문간호');

	//사유
	$reason = Array('01'=>'천재지변','02'=>'응급상황','03'=>'자격변동 처리 지연','04'=>'기타사유');

	$year = Date('Y');
	$month = Date('m');

	$lastDay = $myF->lastday($year,$month);

	$sql = 'SELECT	plan.jumin
			,		m03_name AS name
			,		plan.sub_cd
			,		plan.reason_gbn
			,		plan.reason_str
			,		lvl.app_no
			,		lvl.level
			,		plan.sub_cd
			,		plan.date
			,		plan.from_time
			,		plan.to_time
			,		plan.mem_cd1
			,		plan.mem_nm1
			,		plan.mem_cd2
			,		plan.mem_nm2
			,		plan.time
			,		plan.seq
			,		plan.idx
			FROM	plan_change_request AS plan
			INNER	JOIN	m03sugupja
					ON		m03_ccode = plan.org_no
					AND		m03_mkind = plan.svc_cd
					AND		m03_jumin = plan.jumin
			INNER	JOIN	client_his_lvl AS lvl
					ON		lvl.org_no	= plan.org_no
					AND		lvl.svc_cd	= plan.svc_cd
					AND		lvl.jumin	= plan.jumin
					AND		DATE_FORMAT(lvl.from_dt,\'%Y%m%d\') <= plan.date
					AND		DATE_FORMAT(lvl.to_dt,	\'%Y%m%d\') >= plan.date
			WHERE	plan.org_no		= \''.$orgNo.'\'
			AND		plan.svc_cd		= \'0\'
			AND		plan.date		= \''.$today.'\'
			AND		plan.result_yn	= \'Y\'
			AND		plan.send_yn	= \'Y\'
			AND		complete_yn		= \'N\'
			AND		plan.del_flag	= \'N\'
			ORDER	BY name';

	$arr = $conn->_fetch_array($sql);
	$rCnt = SizeOf($arr);

	for($i=0; $i<$rCnt; $i++){
		$r = $arr[$i];
		$jumin = $r['jumin'];
		$subCd = $r['sub_cd'];

		if (!$data[$jumin]){
			 $data[$jumin]['name'] = $r['name'];
			 $data[$jumin]['appNo'] = $r['app_no'];
			 $data[$jumin]['level'] = $r['level'];
		}

		$idx = SizeOf($data[$jumin][$subCd]);

		$data[$jumin][$subCd][$idx]['time'] = $r['time'];
		$data[$jumin][$subCd][$idx]['seq'] = $r['seq'];
		$data[$jumin][$subCd][$idx]['idx'] = $r['idx'];

		$data[$jumin][$subCd][$idx]['from'] = $r['from_time'];
		$data[$jumin][$subCd][$idx]['to'] = $r['to_time'];
		$data[$jumin][$subCd][$idx]['memCd1'] = $ed->en($r['mem_cd1']);
		$data[$jumin][$subCd][$idx]['memNm1'] = $r['mem_nm1'];
		$data[$jumin][$subCd][$idx]['memCd2'] = $ed->en($r['mem_cd2']);
		$data[$jumin][$subCd][$idx]['memNm2'] = $r['mem_nm2'];
	}

	Unset($r);

	if (!is_array($data)){
		$conn->close();
		exit;
	}

	$no = 1;
	$subList = Array('200','500','800');

	if ($no % 2 == 1){
		$bgcolor = 'FFFFFF';
	}else{
		$bgcolor = 'EFEFEF';
	}

	foreach($data as $jumin => $row){
		$rows['200'] = SizeOf($row['200']);
		$rows['500'] = SizeOf($row['500']);
		$rows['800'] = SizeOf($row['800']);
		$rows['ALL'] = $rows['200'] + $rows['500'] + $rows['800'];?>

		<tr id="rowId_<?=$no;?>" style="background-color:<?=$bgcolor;?>;"
			jumin="<?=$ed->en($jumin);?>"
			appNo="<?=$row['appNo'];?>"
			level="<?=$row['level'];?>"
			cnt="<?=$rows['ALL'];?>">
			<td class="center" rowspan="<?=$rows['ALL'];?>"><?=$no;?></td>
			<td class="center" rowspan="<?=$rows['ALL'];?>"><?=$row['name'];?></td><?

			$tmpSubCd = '';

			foreach($subList as $subCd){
				if ($rows[$subCd] > 0){
					switch($subCd){
						case '200': $subNm = '방문요양'; break;
						case '500': $subNm = '방문목욕'; break;
						case '800': $subNm = '방문간호'; break;
					}

					if ($tmpSubCd != $subCd){
						if ($tmpSubCd){?>
							<tr><?
						}

						$tmpSubCd = $subCd;
					}?>
					<td id="rowId_<?=$no;?>_<?=$subCd;?>" subCd="<?=$subCd;?>" class="center" rowspan="<?=$rows[$subCd];?>"><?=$subNm;?></td><?

					$trIdx = 0;

					foreach($row[$subCd] as $idx => $r){
						if ($trIdx > 0){?>
							<tr><?
						}?>
						<td class="center"><?=$myF->timeStyle($r['from']);?></td>
						<td class="center"><?=$myF->timeStyle($r['to']);?></td>
						<td class="center"><?=$r['memNm1'].($r['memNm1'] ? '/'.$r['memNm2'] : '');?></td>
						<td class="center last">
							<div id="rowId_<?=$no;?>_<?=$subCd;?>_<?=$idx;?>" class="left"
								time="<?=$r['time'];?>"
								seq="<?=$r['seq'];?>"
								idx="<?=$r['idx'];?>"
								subCd="<?=$subCd;?>"
								from="<?=$r['from'];?>"
								to="<?=$r['to'];?>"
								memCd1="<?=$r['memCd1'];?>"
								memCd2="<?=$r['memCd2'];?>"
								memNm1="<?=$r['memNm1'];?>"
								memNm2="<?=$r['memNm2'];?>">
							</div>
						</td><?

						if ($rows[$subCd] > 1){?>
							</tr><?
						}

						$trIdx ++;
					}
				}
			}?>
		</tr><?

		$no ++;
	}

	Unset($data);

	include_once('../inc/_db_close.php');
?>