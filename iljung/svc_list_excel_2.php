<?
	include_once('../inc/_login.php');

	$lastday = $myF->lastDay($year,$month);

	for($i=1; $i<=$lastday; $i++){
		$tmpDayArr[$i] = '';
	}
?>
<table><?
	//사회복지사 업무 내역
	$sql = 'SELECT	DISTINCT a.jumin, a.date, a.time
			,		a.to_time
			,		a.reg_name AS mem_nm, b.app_no, a.rsn_cd, a.rsn_str
			FROM	(
					SELECT	jumin,date,time,to_time,reg_name
					,		CASE WHEN IFNULL(notvisit_cd,\'\') = \'\' AND IFNULL(notvisit_reason,\'\') != \'\' THEN \'9\' ELSE notvisit_cd END AS rsn_cd
					,		notvisit_reason AS rsn_str
					FROM	sw_log
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$year.$month.'\'
					AND		del_flag= \'N\'
					) AS a
			LEFT	JOIN (
					SELECT	DISTINCT jumin,app_no
					FROM	client_his_lvl
					WHERE	org_no = \''.$orgNo.'\'
					AND		svc_cd = \'0\'
					) AS b
					ON		b.jumin = a.jumin
			ORDER	BY a.jumin, a.date, a.time, a.reg_name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$swLog[$row['jumin']][$row['date']][] = Array(
			'name'		=>$row['mem_nm']
		,	'jumin'		=>$row['jumin']
		,	'appNo'		=>$row['app_no']
		,	'date'		=>$row['date']
		,	'fromStr'	=>$myF->timeStyle($row['time']).'~'.$myF->timeStyle($row['to_time'])
		,	'fromTime'	=>$row['time']
		,	'toTime'	=>$row['to_time']
		,	'reason'	=>$row['rsn_cd']
		,	'rsnStr'	=>$row['rsn_str']
		,	'link'		=>false
		,	'draw'		=>false

		,	'memNm'		=>''
		,	'workTime'	=>''
		,	'status'	=>''
		);
	}

	$conn->row_free();


	//수급자 주민번호
	$sql = 'SELECT	DISTINCT app_no, jumin
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$gender = $myF->issToGender($row['jumin']);

		if ($gender == '남'){
			$gender = '<span style="color:BLUE;">남</span>';
		}else{
			$gender = '<span style="color:RED;">여</span>';
		}

		$tgtInfo[$row['app_no']] = Array(
			'jumin'		=>$row['jumin']
		,	'birthday'	=>$myF->issToBirthday($row['jumin'],'.')
		,	'gender'	=>$gender
		);
	}

	$conn->row_free();


	if (is_array($tgtInfo)){
		foreach($tgtInfo as $appNo => $R){
			$sql = 'SELECT	to_dt
					FROM	client_his_svc
					WHERE	org_no	= \''.$orgNo.'\'
					AND		jumin	= \''.$R['jumin'].'\'
					AND		svc_cd	= \'0\'
					AND		LEFT(REPLACE(to_dt, \'-\', \'\'), 6) = \''.$year.$month.'\'
					';
			$tgtInfo[$appNo]['to_dt'] = $conn->get_data($sql);
		}
	}


	//일정내역
	$sql = 'SELECT	DISTINCT t01_jumin AS jumin
			,		t01_sugup_date AS date
			,		t01_yname1 AS mem_nm1
			,		t01_yname2 AS mem_nm2
			,		CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_fmtime ElSE t01_sugup_fmtime END AS from_tm
			,		CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_totime ElSE t01_sugup_totime END AS to_tm
			FROM	t01iljung
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \'0\'
			AND		t01_del_yn	= \'N\'
			AND		t01_svc_subcode = \'200\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			ORDER	BY t01_jumin, t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime, t01_conf_fmtime, t01_conf_totime';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (is_array($swLog[$row['jumin']][$row['date']])){
			Unset($tmpLog);

			foreach($swLog[$row['jumin']][$row['date']] as $idx => $R){
				if (!$swLog[$row['jumin']][$row['date']][$idx]['link']){
					if (!IsSet($tmpLog)){
						$tmpLog = Array(
							'jumin'	=>$row['jumin']
						,	'date'	=>$row['date']
						,	'idx'	=>$idx
						);
					}

					if (($myF->time2min($R['fromTime']) >= $myF->time2min($row['from_tm']) && $myF->time2min($R['fromTime']) <= $myF->time2min($row['to_tm'])) &&
						(($row['date'] < '20150101') || ($row['date'] >= '20150101' && $myF->time2min($R['toTime']) >= $myF->time2min($row['from_tm']) && $myF->time2min($R['toTime']) <= $myF->time2min($row['to_tm'])))){
						$swLog[$row['jumin']][$row['date']][$idx]['memNm']		= $row['mem_nm1'];
						$swLog[$row['jumin']][$row['date']][$idx]['workTime']	= $myF->timeStyle($row['from_tm']).'~'.$myF->timeStyle($row['to_tm']);
						$swLog[$row['jumin']][$row['date']][$idx]['status']		= '정상';

						for($j=0; $j<SizeOf($swLog[$row['jumin']][$row['date']]); $j++){
							$swLog[$row['jumin']][$row['date']][$j]['link'] = true;
						}

						Unset($tmpLog);

						break;
					}
				}
			}

			if (IsSet($tmpLog)){
				$swLog[$tmpLog['jumin']][$tmpLog['date']][$tmpLog['idx']]['memNm']		= $row['mem_nm1'];
				$swLog[$tmpLog['jumin']][$tmpLog['date']][$tmpLog['idx']]['workTime']	= $myF->timeStyle($row['from_tm']).'~'.$myF->timeStyle($row['to_tm']);
				$swLog[$tmpLog['jumin']][$tmpLog['date']][$tmpLog['idx']]['status']		= '시간불일치';
				//$swLog[$tmpLog['jumin']][$tmpLog['date']][$tmpLog['idx']]['link']		= true;
			}

			Unset($tmpLog);
		}
	}

	$conn->row_free();


	//업무일지 재정렬
	if (is_array($swLog)){
		$tmpR = $swLog;
		Unset($swLog);

		foreach($tmpR as $jumin => $R1){
			foreach($R1 as $date => $R2){
				foreach($R2 as $idx => $row){
					if (!$swLog[$row['appNo']][$date]){
						 $swLog[$row['appNo']][$date] = $row;
						 //if (!$swLog[$row['appNo']][$date]['status']) $swLog[$row['appNo']][$date]['status'] = '미방문';
						break;
					}
				}
			}
		}

		Unset($tmpR);
	}


	/*
		if (is_array($swLog)){
			foreach($swLog as $appNo => $R){
				foreach($R as $date => $row){
					if ($row['status'] == '정상'){
						$totLog[0] ++;
					}else if ($row['status'] == '시간불일치'){
						$totLog[2] ++;
					}else if ($row['status'] == '미방문'){
						$totLog[1] ++;
					}

					if ($row['reason'] == '1'){
						$totLog[4] ++; //사망
					}else if ($row['reason'] == '2'){
						$totLog[5] ++; //병원
					}else if ($row['reason'] == '3'){
						$totLog[6] ++; //해지
					}else if ($row['reason'] == '9'){
						$totLog[7] ++; //기타
					}
				}
			}
		}
	*/


	//공단내역
	$sql = 'SELECT	app_no
			,		name
			,		reg_dt AS date
			,		CAST(RIGHT(reg_dt,2) AS unsigned) AS day
			,		from_tm
			,		to_tm
			,		send_gbn
			,		svc_gbn
			FROM	lg2cv
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'
			AND		use_yn	= \'Y\'
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	$total = Array(
		'data'=>Array(
			'200'=>Array('total'=>0, 'data'=>Array('02'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '04'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '03'=>Array('total'=>0, 'workTime'=>$tmpDayArr)))
		,	'500'=>Array('total'=>0, 'data'=>Array('02'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '04'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '03'=>Array('total'=>0, 'workTime'=>$tmpDayArr)))
		,	'800'=>Array('total'=>0, 'data'=>Array('02'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '04'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '03'=>Array('total'=>0, 'workTime'=>$tmpDayArr)))
		)
	);

	$tmpStr1 = '';
	$tmpStr2 = '';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$appNo = $row['app_no'];

		#if ($appNo == 'L0011036737'){
		#	print_r($swLog[$appNo]);
		#}

		$svcGbn	= $row['svc_gbn'];
		$sendGbn= $row['send_gbn'];
		$day	= $row['day'];

		if ($sendGbn != '02' && $sendGbn != '04') $sendGbn = '03';

		if (!$data[$appNo]){
			 $data[$appNo] = Array(
				'name'=>$row['name'].'('.$tgtInfo[$appNo]['gender'].')'.'<br style="mso-data-placement:same-cell;">'.$tgtInfo[$appNo]['birthday']
			,	'date'=>$row['date']
			,	'data'=>Array(
					'200'=>Array('total'=>0, 'data'=>Array('02'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '04'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '03'=>Array('total'=>0, 'workTime'=>$tmpDayArr)))
				,	'500'=>Array('total'=>0, 'data'=>Array('02'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '04'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '03'=>Array('total'=>0, 'workTime'=>$tmpDayArr)))
				,	'800'=>Array('total'=>0, 'data'=>Array('02'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '04'=>Array('total'=>0, 'workTime'=>$tmpDayArr), '03'=>Array('total'=>0, 'workTime'=>$tmpDayArr)))
				)
			);

			if (is_array($swLog[$appNo])){
				foreach($swLog[$appNo] as $tmpDate => $tmpRow){
					if ($tmpRow['status'] == '정상'){
						$totLog[0] ++;
					}else if ($tmpRow['status'] == '시간불일치'){
						$totLog[2] ++;
					}else if ($tmpRow['status'] == '미방문'){
						//$totLog[3] ++;
					}

					if ($tmpRow['reason'] == '1'){
						$totLog[4] ++; //사망
					}else if ($tmpRow['reason'] == '2'){
						$totLog[5] ++; //병원
					}else if ($tmpRow['reason'] == '3'){
						$totLog[6] ++; //해지
					}else if ($tmpRow['reason'] == '9'){
						$totLog[7] ++; //기타
					}
				}
			}
		}

		if ($svcGbn == '500'){
			$addFlag = false;

			if (!is_numeric(StrPos($tmpStr2, '/'.$appNo.'_'.$row['date']))){
				$tmpStr2 .= '/'.$appNo.'_'.$row['date'];
				$addFlag = true;
			}
		}else{
			$addFlag = true;
		}

		$data[$appNo]['data'][$svcGbn]['data'][$sendGbn]['workTime'][$day] .= ($data[$appNo]['data'][$svcGbn]['data'][$sendGbn]['workTime'][$day] ? '<br style="mso-data-placement:same-cell;">' : '').SubStr($row['from_tm'],0,2).':'.SubStr($row['from_tm'],2,2).'~'.SubStr($row['to_tm'],0,2).':'.SubStr($row['to_tm'],2,2);

		if ($addFlag){
			$data[$appNo]['data'][$svcGbn]['total'] ++;
			$data[$appNo]['data'][$svcGbn]['data'][$sendGbn]['total'] ++;

			$total['data'][$svcGbn]['total'] ++;
			$total['data'][$svcGbn]['data'][$sendGbn]['total'] ++;
			$total['data'][$svcGbn]['data'][$sendGbn]['workTime'][$day] ++;
		}

		if (!$swLog[$appNo]){
			if (!is_numeric(StrPos($tmpStr1, '/'.$appNo))){
				$logStat[$appNo] = '미작성';
				$tmpStr1 .= '/'.$appNo;

				if ($tgtInfo[$appNo]['to_dt']) $logStat[$appNo] .= '<br style="mso-data-placement:same-cell;">계약만료일 : '.$myF->dateStyle($tgtInfo[$appNo]['to_dt']);

				//$totLog[3] ++;
				if ($year.$month >= '201703'){
					$totLog[1] ++;
				}else{
					if ($svcGbn == '200'){
						$totLog[1] ++;
					}else{
						$totLog[3] ++;
					}
				}
			}
		}
	}

	$conn->row_free();

	if (is_array($total)){
		//집계?>
		<tr style="background-color:#BDBDBD;">
			<th style="width:40px; text-align:center; border:0.5pt solid BLACK;" rowspan="11">합<br style="mso-data-placement:same-cell;"><br style="mso-data-placement:same-cell;">계</th>
			<th style="width:30px; border:0.5pt solid BLACK;" colspan="4">관리자 및 복지사업무</th>
			<th style="width:30px; border:0.5pt solid BLACK;" rowspan="2">분류</th>
			<th style="width:50px; border:0.5pt solid BLACK;" rowspan="2">소계</th>
			<th style="width:70px; border:0.5pt solid BLACK;" rowspan="2">전송구분</th>
			<th style="width:50px; border:0.5pt solid BLACK;" rowspan="2">소계</th>
			<th style="width:auto; border:0.5pt solid BLACK;" colspan="<?=$lastday;?>">근무일자</th>
		</tr>
		<tr style="background-color:#BDBDBD;">
			<th style="width:50px; border:0.5pt solid BLACK;" colspan="3">구분</th>
			<th style="width:80px; border:0.5pt solid BLACK;">건수</th><?
			for($i=1; $i<=$lastday; $i++){?>
				<th style="width:85px; border:0.5pt solid BLACK;"><?=$i;?></th><?
			}?>
		</tr>
		<tr>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="4">방문</td>
			<td style="text-align:left; border:0.5pt solid BLACK;" colspan="2">복지사정상방문건수</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$totLog[0];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3">요<br style="mso-data-placement:same-cell;">양</td>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3"><?=$total['data']['200']['total'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">자동전송</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['200']['data']['02']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['200']['data']['02']['workTime'][$i];?></td><?
			}?>
		</tr>
		<tr>
			<td style="text-align:left; border:0.5pt solid BLACK; color:RED;" colspan="2">시간불일치</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$totLog[2];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">오류수정</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['200']['data']['03']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['200']['data']['03']['workTime'][$i];?></td><?
			}?>
		</tr>
		<tr>
			<td style="text-align:left; border:0.5pt solid BLACK; color:RED;" colspan="2">복지사일지없음</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$totLog[1];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">직접입력</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['200']['data']['04']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['200']['data']['04']['workTime'][$i];?></td><?
			}?>
		</tr>
		<tr>
			<td style="text-align:left; border:0.5pt solid BLACK; color:RED;" colspan="2"><?=$year.$month >= '201703' ? '' : '미작성(목욕,간호)';?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$totLog[3];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3">목<br style="mso-data-placement:same-cell;">욕</td>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3"><?=$total['data']['500']['total'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">자동전송</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['500']['data']['02']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['500']['data']['02']['workTime'][$i];?></td><?
			}?>
		</tr>
		<tr>
			<td style="text-align:center; border:0.5pt solid BLACK; color:RED;" rowspan="5">방문<br style="mso-data-placement:same-cell;">불가</td>
			<td style="text-align:left; border:0.5pt solid BLACK;" colspan="2">사망</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$totLog[4];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">직접입력</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['500']['data']['04']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['500']['data']['04']['workTime'][$i];?></td><?
			}?>
		</tr>
		<tr>
			<td style="text-align:left; border:0.5pt solid BLACK;" colspan="2">병원입원</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$totLog[5];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">오류수정</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['500']['data']['03']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['500']['data']['03']['workTime'][$i];?></td><?
			}?>
		</tr>
		<tr>
			<td style="text-align:left; border:0.5pt solid BLACK;" colspan="2">계약해지</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$totLog[6];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3">간<br style="mso-data-placement:same-cell;">호</td>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3"><?=$total['data']['800']['total'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">자동전송</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['800']['data']['02']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['800']['data']['02']['workTime'][$i];?></td><?
			}?>
		</tr>
		<tr>
			<td style="text-align:left; border:0.5pt solid BLACK;" colspan="2">기타</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$totLog[7];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">직접입력</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['800']['data']['04']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['800']['data']['04']['workTime'][$i];?></td><?
			}?>
		</tr>
		<tr>
			<td style="text-align:center; border:0.5pt solid BLACK;" colspan="2">소계</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">오류수정</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['800']['data']['03']['total'];?></td><?
			for($i=1; $i<=$lastday; $i++){?>
				<td style="text-align:center; border:0.5pt solid BLACK;"><?=$total['data']['800']['data']['03']['workTime'][$i];?></td><?
			}?>
		</tr><?
	}?>
	<tr>
		<td style="height:5px; border:0;"></td>
	</tr>
	<tr style="background-color:#BDBDBD;">
		<th style="width:40px; border:0.5pt solid BLACK;" rowspan="2">순번</th>
		<th style="width:80px; border:0.5pt solid BLACK;" rowspan="2">대상자<br style="mso-data-placement:same-cell;">성명</th>
		<th style="width:auto; border:0.5pt solid BLACK;" colspan="3">관리자 및 복지사업무1</th>
		<th style="width:30px; border:0.5pt solid BLACK;" rowspan="2">분류</th>
		<th style="width:50px; border:0.5pt solid BLACK;" rowspan="2">소계</th>
		<th style="width:70px; border:0.5pt solid BLACK;" rowspan="2">전송구분</th>
		<th style="width:50px; border:0.5pt solid BLACK;" rowspan="2">소계</th>
		<th style="width:auto; border:0.5pt solid BLACK;" colspan="<?=$lastday;?>">근무일자</th>
		<th style="width:auto; border:0.5pt solid BLACK;" colspan="3">관리자 및 복지사업무2</th>
	</tr>
	<tr style="background-color:#BDBDBD;">
		<th style="width:50px; border:0.5pt solid BLACK;">일자</th>
		<th style="width:80px; border:0.5pt solid BLACK;">사회복지사</th>
		<th style="width:80px; border:0.5pt solid BLACK;">요양보호사</th><?
		for($i=1; $i<=$lastday; $i++){?>
			<th style="width:85px; border:0.5pt solid BLACK;"><?=$i;?></th><?
		}?>
		<th style="width:50px; border:0.5pt solid BLACK;">일자</th>
		<th style="width:80px; border:0.5pt solid BLACK;">사회복지사</th>
		<th style="width:80px; border:0.5pt solid BLACK;">요양보호사</th>
	</tr><?

	if (is_array($data)){
		$no = 1;

		foreach($data as $appNo => $appR){
			//업무일지 로그
			if (is_array($swLog[$appNo])){
				$idx = 0;

				foreach($swLog[$appNo] as $date => $R){
					$swData[$idx][0][0] = IntVal(SubStr($date,6,2)).'일';
					$swData[$idx][0][1] = $R['name'];
					$swData[$idx][0][2] = $R['memNm'];
					$swData[$idx][1][1] = $R['fromStr'];
					$swData[$idx][1][2] = $R['workTime'];
					$swData[$idx][2][1] = $R['status'];

					switch($R['reason']){
						case '1':
							$swData[$idx][2][1] = '사망';
							break;

						case '2':
							$swData[$idx][2][1] = '병원';
							break;

						case '3':
							$swData[$idx][2][1] = '해지';
							break;

						case '9':
							$swData[$idx][2][1] = '기타';
							break;
					}

					if ($R['rsnStr']){
						$swData[$idx][2][1] .= '('.$R['rsnStr'].')';
					}

					$idx ++;
				}
			}

			if ($no % 2 == 1){
				$bgclr = 'FFFFFF';
			}else{
				$bgclr = 'EAEAEA';
			}

			$rows = 0;

			if ($appR['data']['200']['total'] > 0) $rows += 3;
			if ($appR['data']['500']['total'] > 0) $rows += 3;
			if ($appR['data']['800']['total'] > 0) $rows += 3;?>
			<tr style="background-color:#<?=$bgclr;?>;">
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="<?=$rows;?>"><?=$no;?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="<?=$rows;?>"><?=$appR['name'];?></td><?

			$IsFirst[0] = true;

			foreach($appR['data'] as $svcGbn => $svcR){
				if ($svcR['total'] == 0) continue;
				if ($IsFirst[0]){
					$IsFirst[0] = false;
				}else{?>
					<tr style="background-color:#<?=$bgclr;?>;"><?
				}

				if ($svcGbn == '200'){
					$svcGbn = '요<br style="mso-data-placement:same-cell;">양';
				}else if ($svcGbn == '500'){
					$svcGbn = '목<br style="mso-data-placement:same-cell;">욕';
				}else if ($svcGbn == '800'){
					$svcGbn = '간<br style="mso-data-placement:same-cell;">호';
				}

				if ($swData[0]){?>
					<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3"><?=$swData[0][0][0];?></td>
					<td style="text-align:center; border:0.5pt solid BLACK;"><?=$swData[0][0][1];?></td>
					<td style="text-align:center; border:0.5pt solid BLACK;"><?=$swData[0][0][2];?></td><?
				}else{?>
					<td style="text-align:center; border:0.5pt solid BLACK; color:<?=$logStat[$appNo] ? 'RED' : '';?>;" rowspan="3" colspan="3"><?=$logStat[$appNo];?></td><?
				}?>
				<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3"><?=$svcGbn;?></td>
				<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3"><?=$svcR['total'];?></td><?

				$IsFirst[1] = true;
				$logIdx = 0;

				foreach($svcR['data'] as $sendGbn => $sendR){
					if ($IsFirst[1]){
						$IsFirst[1] = false;
					}else{?>
						<tr style="background-color:#<?=$bgclr;?>;"><?
						if ($swData[0]){
							if ($logIdx == 1){?>
								<td style="text-align:center; border:0.5pt solid BLACK;"><?=$swData[0][$logIdx][1];?></td>
								<td style="text-align:center; border:0.5pt solid BLACK;"><?=$swData[0][$logIdx][2];?></td><?
							}else{?>
								<td style="text-align:center; border:0.5pt solid BLACK; color:<?=$swData[0][$logIdx][1] != '정상' ? 'RED' : '';?>;" colspan="2"><?=$swData[0][$logIdx][1];?></td><?
							}
						}
					}

					if ($sendGbn == '02'){
						$sendGbn = '자동전송';
					}else if ($sendGbn == '04'){
						$sendGbn = '직접입력';
					}else if ($sendGbn == '03'){
						$sendGbn = '오류수정';
					}?>
					<td style="text-align:center; border:0.5pt solid BLACK;"><?=$sendGbn;?></td>
					<td style="text-align:center; border:0.5pt solid BLACK;"><?=$sendR['total'];?></td><?

					foreach($sendR['workTime'] as $day => $val){?>
						<td style="text-align:center; border:0.5pt solid BLACK;"><?=$val;?></td><?
					}

					if ($swData[1]){
						if ($logIdx == 0){?>
							<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3"><?=$swData[1][$logIdx][0];?></td><?
						}

						if ($logIdx == 2){?>
							<td style="text-align:center; border:0.5pt solid BLACK; color:<?=$swData[1][$logIdx][1] != '정상' ? 'RED' : '';?>;" colspan="2"><?=$swData[1][$logIdx][1];?></td><?
						}else{?>
							<td style="text-align:center; border:0.5pt solid BLACK;"><?=$swData[1][$logIdx][1];?></td>
							<td style="text-align:center; border:0.5pt solid BLACK;"><?=$swData[1][$logIdx][2];?></td><?
						}
					}else{
						if ($logIdx == 0){?>
							<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="3" colspan="3">-</td><?
						}
					}?>
					</tr><?

					$logIdx ++;
				}

				Unset($swData);
			}

			$no ++;
		}
	}?>
</table><?

Unset($swLog);
Unset($data);
Unset($swData);
Unset($totLog);
Unset($logStat);
Unset($tgtInfo);
?>