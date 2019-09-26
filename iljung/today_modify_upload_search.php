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
			,		COUNT(plan.sub_cd) AS cnt
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
			AND		plan.send_yn	= \'N\'
			AND		plan.del_flag	= \'N\'
			GROUP	BY plan.jumin,plan.sub_cd
			ORDER	BY name';

	$arr = $conn->_fetch_array($sql);
	$rCnt = SizeOf($arr);

	for($i=0; $i<$rCnt; $i++){
		$r = $arr[$i];
		$jumin = $ed->en($r['jumin']);

		//일정
		$plan = lfGetPlan($conn, $ed, $myF, $orgNo, $r['jumin'], $r['sub_cd'], $year.$month);

		if ($r['sub_cd'] == '200'){
			$subCd = '001';
		}else if ($r['sub_cd'] == '500'){
			$subCd = '002';
		}else if ($r['sub_cd'] == '800'){
			$subCd = '003';
		}

		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr id="rowId_<?=$i;?>" style="background-color:#<?=$bgcolor;?>;"
			jumin="<?=$jumin;?>"
			appNo="<?=$r['app_no'];?>"
			level="<?=$r['level'];?>"
			subCd="<?=$subCd;?>"
			reasonGbn="<?=$r['reason_gbn'];?>" reasonStr="<?=$r['reason_str'];?>"
			IsUpload="N">
			<td class="center"><?=$i+1;?></td>
			<td class="center"><?=$r['name'];?></td>
			<td class="center"><?=$subNm[$r['sub_cd']];?></td>
			<td class="center"><?=$r['cnt'];?></td>
			<td class="center last">
				<div id="note" class="left" upload="N"></div>
				<div id="plan" class="left" style="display:none;"><?
					for($i=1; $i<=$lastDay; $i++){?>
						<span id="lblPlan_<?=$i;?>" cnt="0" htm="" loadYn="" 200Yn="N" 500Yn="N" 800Yn="N"></span><?
					}?>
				</div>
				<div id="iljung" class="left" style="display:none;"><?=$plan;?></div>
			</td>
		</tr><?
	}

	Unset($r);

	include_once('../inc/_db_close.php');

	function lfGetPlan($conn, $ed, $myF, $orgNo, $jumin, $subCd, $yymm){
		//일정조회
		$sql = 'select	*
				from	t01iljung
				where	t01_ccode  = \''.$orgNo.'\'
				and		t01_mkind  = \'0\'
				and		t01_jumin  = \''.$jumin.'\'
				and		t01_del_yn = \'N\'
				and		t01_svc_subcode = \''.$subCd.'\'
				and		left(t01_sugup_date,6) = \''.$yymm.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$liDay = intval(substr($row['t01_sugup_date'],6));
			$liIdx = sizeof($laIljung[$liDay]);

			$lsSugaCd = $row['t01_conf_suga_code'];

			$memCd1 = $ed->en($row['t01_mem_cd1']);
			$memNm1 = $row['t01_mem_nm1'];
			$memCd2 = $ed->en($row['t01_mem_cd2']);
			$memNm2 = $row['t01_mem_nm2'];

			$strFrom = $row['t01_sugup_fmtime'];
			$strTo = $row['t01_sugup_totime'];
			$strSuga = $row['t01_suga_tot'];

			$laIljung[$liDay][$liIdx] = array(
				'day'			=>$liDay
			,	'cnt'			=>$liIdx
			,	'svcKind'		=>$row['t01_svc_subcode']
			,	'from'			=>$myF->timeStyle($strFrom)
			,	'to'			=>$myF->timeStyle($strTo)
			,	'memCd1'		=>$memCd1
			,	'memNm1'		=>$memNm1
			,	'memCd2'		=>$memCd2
			,	'memNm2'		=>$memNm2
			,	'sugaCd'		=>$lsSugaCd
			,	'cost'			=>$row['t01_suga']
			,	'costEvening'	=>$row['t01_suga_over']
			,	'costNight'		=>$row['t01_suga_night']
			,	'costTotal'		=>$strSuga
			,	'ynBipay'		=>$row['t01_bipay_umu'] == 'Y' ? 'Y' : 'N'
			,	'ynAddRow'		=>'N'
			,	'ynSave'		=>'Y'
			,	'planFrom'		=>$row['t01_sugup_fmtime']
			,	'planTo'		=>$row['t01_sugup_totime']
			,	'seq'			=>$row['t01_sugup_seq']
			);
		}

		$conn->row_free();

		$cnt = SizeOf($laIljung);
		$s = '';

		if (is_array($laIljung)){
			for($i=1; $i<=31; $i++){
				if (is_array($laIljung[$i])){
					foreach($laIljung[$i] as $row){
						$s .= '	<div id="loCal_'.$row['day'].'_'.$row['cnt'].'" style="float:left; width:auto;"
								day			="'.$row['day'].'"
								cnt			="'.$row['cnt'].'"
								svcKind		="'.$row['svcKind'].'"
								from		="'.$row['from'].'"
								to			="'.$row['to'].'"
								memCd1		="'.$row['memCd1'].'"
								memNm1		="'.$row['memNm1'].'"
								memCd2		="'.$row['memCd2'].'"
								memNm2		="'.$row['memNm2'].'"
								sugaCd		="'.$row['sugaCd'].'"
								cost		="'.$row['cost'].'"
								costEvening	="'.$row['costEvening'].'"
								costNight	="'.$row['costNight'].'"
								costTotal	="'.$row['costTotal'].'"
								ynBipay		="'.$row['ynBipay'].'"
								ynAddRow	="'.$row['ynAddRow'].'"
								ynSave		="'.$row['ynSave'].'"
								stat		="'.$row['stat'].'"
								planFrom	="'.$row['planFrom'].'"
								planTo		="'.$row['planTo'].'"
								seq			="'.$row['cnt'].'"
								svcSeq		="'.$row['seq'].'"
								>/'.$row['day'].'_'.$row['cnt'].'</div>';
					}
				}
			}
		}

		//$s = '<div>'.nl2br($sql).'</div>';

		Unset($laIljung);

		return $s;
	}
?>