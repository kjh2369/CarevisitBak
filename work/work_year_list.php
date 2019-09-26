<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$name = $conn->MemberName($orgNo,$jumin);
	$year = $_POST['year'];
	$gbn = $_POST['gbn'];

	$day['TOTAL'] = 0;
	for($i=1; $i<=31; $i++){
		$day[$i] = 0;
	}

	$subNm = Array('200'=>'방문요양', '500'=>'방문목욕', '800'=>'방문간호');
	$color = Array(0=>'FF0000', 1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'0000FF');

	//휴일리스트
	$sql = 'SELECT	mdate AS dt
			,		holiday_name AS nm
			FROM	tbl_holiday
			WHERE	LEFT(mdate,4) = \''.$year.'\'';

	$holiday = $conn->_fetch_array($sql,'dt');

	$sql = 'SELECT	iljung.date
			,		iljung.from_time
			,		iljung.to_time
			,		iljung.jumin
			,		m03_name AS name
			,		iljung.suga_cd
			,		iljung.sub_cd
			FROM	(
					SELECT	t01_jumin AS jumin
					,		t01_sugup_date AS date';

	if ($gbn == '1'){
		$sql .= '	,		t01_sugup_fmtime AS from_time
					,		t01_sugup_totime AS to_time';
	}else{
		$sql .= '	,		t01_conf_fmtime AS from_time
					,		t01_conf_totime AS to_time';
	}

	$sql .= '		,		t01_suga_code1 AS suga_cd
					,		t01_svc_subcode AS sub_cd
					FROM	t01iljung
					WHERE	t01_ccode = \''.$orgNo.'\'
					AND		t01_mkind = \'0\'
					AND		LEFT(t01_sugup_date,4) = \''.$year.'\'
					AND		t01_mem_cd1 = \''.$jumin.'\'';

	if ($gbn == '2'){
		$sql .= '	AND		t01_status_gbn = \'1\'';
	}

	$sql .= '		UNION	ALL
					SELECT	t01_jumin AS jumin
					,		t01_sugup_date AS date';

	if ($gbn == '1'){
		$sql .= '	,		t01_sugup_fmtime AS from_time
					,		t01_sugup_totime AS to_time';
	}else{
		$sql .= '	,		t01_conf_fmtime AS from_time
					,		t01_conf_totime AS to_time';
	}

	$sql .= '		,		t01_suga_code1 AS suga_cd
					,		t01_svc_subcode AS sub_cd
					FROM	t01iljung
					WHERE	t01_ccode = \''.$orgNo.'\'
					AND		t01_mkind = \'0\'
					AND		LEFT(t01_sugup_date,4) = \''.$year.'\'
					AND		t01_mem_cd2 = \''.$jumin.'\'';

	if ($gbn == '2'){
		$sql .= '	AND		t01_status_gbn = \'1\'';
	}

	$sql .= '	) AS iljung
			INNER	JOIN	m03sugupja
					ON		m03_ccode = \''.$orgNo.'\'
					AND		m03_mkind = \'0\'
					AND		m03_jumin = iljung.jumin
			ORDER BY LEFT(date,6), from_time, to_time, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$m = IntVal(SubStr($row['date'],4,2));
		$d = IntVal(SubStr($row['date'],6));
		$cd = $row['jumin'];
		$suga = $row['suga_cd'];

		if (!$data[$m][$cd]){
			 $data[$m][$cd]['name'] = $row['name'];
			 $idx = 0;
		}

		if ($myF->time2min($row['from_time']) > $myF->time2min($row['to_time'])){
			$row['to_time'] = str_replace(':', '', $myF->min2time($myF->time2min($row['to_time']) + 1440));
		}

		if ($data[$m][$cd][$idx]['from'] != $row['from_time'] || $data[$m][$cd][$idx]['to'] != $row['to_time']){
			$idx = SizeOf($data[$m][$cd]);

			$time = $myF->cutOff($myF->time2min($row['to_time']) - $myF->time2min($row['from_time']),30);

			$data[$m][$cd][$idx]['from'] = $row['from_time'];
			$data[$m][$cd][$idx]['to'] = $row['to_time'];
			$data[$m][$cd][$idx]['time'] = $time;
			$data[$m][$cd][$idx]['sub'] = $subNm[$row['sub_cd']];
			$data[$m][$cd][$idx]['day'] = $day;
		}

		$data[$m][$cd][$idx]['day'][$d] ++;
		$data[$m][$cd][$idx]['day']['TOTAL'] ++;

		//일자
		$dt = $year.($m < 10 ? '0' : '').$m.($d < 10 ? '0' : '').$d;

		//요일
		$w = Date('w', StrToTime($dt));

		//휴일여부
		if ($holiday[$dt]['nm']){
			$w = 0;
		}

		//근무시간
		if ($time == 480){
			if ($year.($m < 10 ? '0' : '').$m < '201603'){
				$worktime = $time - 30;
			}
		}else if ($time >= 540){
			$worktime = $time - 60;
		}else if ($time >= 270){
			$worktime = $time - 30;
		}else{
			$worktime = $time;
		}

		//근무시간
		$data[$m][$cd][$idx]['day']['TIME'] += $worktime;

		//연장시간
		$fromTime = $myF->time2min($data[$m][$cd][$idx]['from']);
		$toTime = $myF->time2min($data[$m][$cd][$idx]['to']);

		if ($fromTime > $toTime){
			$toTime += 1440;
		}

		if ($fromTime >= 1080 && $toTime <= 1800){
			if ($fromTime < 1080) $fromTime = 1080;
			if ($toTime > 1800) $toTime = 1800;

			//연장시간
			$prolong = $toTime - $fromTime;

			$data[$m][$cd][$idx]['day']['PROLONG'] += $prolong;
		}

		//휴일시간
		if ($w == 0){
			$data[$m][$cd][$idx]['day']['HOLIDAY'] += $worktime;
		}

		if (!is_numeric(StrPos($data[$m][$cd][$idx]['day']['DATE'], '/'.$d))){
			$data[$m][$cd][$idx]['day']['DATE'] .= ('/'.$d);
		}
	}

	$conn->row_free();

	if (!is_array($data)){
		echo '<br><br><center>::데이타가 없습니다.::</center>';
		exit;
	}
?>	<style>
		.yymm{
			width:auto;
			height:25px;
			line-height:25px;
			font-weight:bold;
			padding-left:15px;
		}
	</style>
	<script type="text/javascript">
		function lfExcel(){
			var parm = new Array();
				parm = {
					'jumin':'<?=$ed->en($jumin);?>'
				,	'year':'<?=$year;?>'
				,	'gbn':'<?=$gbn;?>'
				};

			var form = document.createElement('form');
			var objs;
			for(var key in parm){
				objs = document.createElement('input');
				objs.setAttribute('type', 'hidden');
				objs.setAttribute('name', key);
				objs.setAttribute('value', parm[key]);

				form.appendChild(objs);
			}

			form.setAttribute('target', '_self');
			form.setAttribute('method', 'post');
			form.setAttribute('action', './work_year_excel.php');

			document.body.appendChild(form);

			form.submit();
		}
	</script>
	<div class="title_border">
		<div class="title" style="float:left; width:auto;"><?=$name;?> 연간 근무현황표</div><?
		if (!$IsExcel){?>
			<div style="float:left; width:auto; margin-left:10px; margin-top:7px;">
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">Excel</button></span>
			</div><?
		}?>
	</div><?

	$first = true;

	foreach($data as $m => $month){
		$m = ($m < 10 ? '0' : '').$m;?>
		<div class="yymm" style="<?=(!$first ? 'margin-top:20px;' : '');?>"><?=$year;?>년 <?=IntVal($m);?>월</div>
		<div>
			<table class="my_table" style="width:<?=40 + 70 + 80 + 60 + 50*33 + 70*4 + 100;?>px;" <?=($IsExcel ? 'border="1"' : 'style="border-top:1px solid #a6c0f3;"');?>>
				<colgroup>
					<col width="40px">
					<col width="70px">
					<col width="80px">
					<col width="60px">
					<col width="50px" span="33">
					<col width="70px" span="4">
					<col>
				</colgroup>
				<thead>
					<tr>
						<th class="head">No</th>
						<th class="head">수급자</th>
						<th class="head">방문시간</th>
						<th class="head">서비스</th>
						<th class="head">시간</th>
						<th class="head">횟수</th><?
						for($i=1; $i<=31; $i++){
							$dt = $year.$m.($i < 10 ? '0' : '').$i;
							$w = Date('w', StrToTime($dt));

							if ($holiday[$dt]['nm']){
								$w = 0;
							}?>
							<th class="head" style="color:#<?=$color[$w];?>;" title="<?=$holiday[$dt]['nm'];?>"><?=$i;?></th><?
						}?>
						<th class="head">근무일수</th>
						<th class="head">근무시간</th>
						<th class="head">야간시간</th>
						<th class="head">휴일시간</th>
						<th class="head">비고</th>
					</tr>
				</thead>
				<tbody><?
					$no = 1;
					$monTotal = 0;
					$monTime = $day;
					$monDate = '';
					$monWork = 0;
					$monProlong = 0;
					$monHoliday = 0;

					foreach($month as $jumin => $client){
						foreach($client as $idx => $iljung){
							if ($idx == 'name'){
								$rows = SizeOf($client)-1;?>
								<tr>
								<td class="center" rowspan="<?=$rows;?>"><?=$no;?></td>
								<td class="left" rowspan="<?=$rows;?>"><?=$client['name'];?></td><?
								continue;
							}?>
							<td class="center"><?=$myF->timeStyle($iljung['from']);?>~<?=$myF->timeStyle($iljung['to']);?></td>
							<td class="center"><?=$iljung['sub'];?></td>
							<td class="center"><?=$iljung['time'];?>분</td>
							<td class="center"><div class="right"><?=$iljung['day']['TOTAL'];?></div></td><?

							$monTotal += $iljung['day']['TOTAL'];

							for($i=1; $i<=31; $i++){
								$dt = $year.$m.($i < 10 ? '0' : '').$i;
								$w = Date('w', StrToTime($dt));

								if ($holiday[$dt]['nm']){
									$w = 0;
								}

								$monTime[$i] += ($iljung['day'][$i] * $iljung['time']);
								$monDate .= $iljung['day']['DATE'];?>
								<td class="center" style="color:#<?=$color[$w];?>;" title="<?=$holiday[$dt]['nm'];?>"><?=($iljung['day'][$i] > 0 ? '●' : '');?></td><?
							}

							$monWork += $iljung['day']['TIME'];
							$monProlong += $iljung['day']['PROLONG'];
							$monHoliday += $iljung['day']['HOLIDAY'];?>
							<td class="center"><div class="right"><?=substr_count($iljung['day']['DATE'],'/');?></div></td>
							<td class="center"><div class="right"><?=($iljung['day']['TIME'] > 0 ? Round($iljung['day']['TIME'] / 60,1) : '');?></div></td>
							<td class="center"><div class="right"><?=($iljung['day']['PROLONG'] > 0 ? Round($iljung['day']['PROLONG'] / 60,1) : '');?></div></td>
							<td class="center"><div class="right"><?=($iljung['day']['HOLIDAY'] > 0 ? Round($iljung['day']['HOLIDAY'] / 60,1) : '');?></div></td>
							<td class="center"></td>
							</tr><?
						}
						$no ++;
					}

					//근무일수
					$monDate = Explode('/',$monDate);
					$monDate = array_unique($monDate);
					$monDate = Implode('/',$monDate);?>
					<tr>
						<th colspan="5"><div class="right bold">소계</div></th>
						<th class="center"><div class="right bold"><?=$monTotal;?></div></th><?
						for($i=1; $i<=31; $i++){?>
							<th class="center"><div class="right bold"><?=($monTime[$i] > 0 ? Round($monTime[$i] / 60,1) : '');?></div></th><?
						}?>
						<th class="center"><div class="right bold"><?=substr_count($monDate,'/');?></div></th>
						<th class="center"><div class="right bold"><?=($monWork > 0 ? $monWork / 60 : '');?></div></th>
						<th class="center"><div class="right bold"><?=($monProlong > 0 ? $monProlong / 60 : '');?></div></th>
						<th class="center"><div class="right bold"><?=($monHoliday > 0 ? $monHoliday / 60 : '');?></div></th>
						<th class="center"></th>
					</tr>
				</tbody>
			</table>
		</div><?
		$first = false;
	}

	include_once('../inc/_footer.php');
?>