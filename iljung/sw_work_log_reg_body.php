<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$type	= $_POST['type'];
	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['yymm'];
	$seq	= $_POST['seq'];

	$sql = 'SELECT	*
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		del_flag= \'N\'';

	if ($seq > 0){
		$sql .= '
			AND		yymm	= \''.$yymm.'\'
			AND		seq		= \''.$seq.'\'';

		$row = $conn->get_array($sql);

		$date = $myF->dateStyle($row['date']);
		$time = $myF->timeStyle($row['time']);
		$toTime = $myF->timeStyle($row['to_time']);
	}else if ($seq != ''){
		$date = $_POST['date'];
		$time = $_POST['time'];
		$toTime = $_POST['toTime'];

		if (!$date) $date = Date('Ymd');
		if (!$time){
			$time = Date('Hi');
			$toTime = $myF->min2time($myF->time2min($time) + 60);
		}

		$yymm = SubStr($date,0,6);

		if ($date && $time){
			$sql .= '
					AND	UNIX_TIMESTAMP(CONCAT(SUBSTR(date,1,4),\'-\',SUBSTR(date,5,2),\'-\',SUBSTR(date,7),\' \',SUBSTR(time,1,2),\':\',SUBSTR(time,3,2),\':00\')) < \''.StrToTime($date.' '.$time).'\'';
		}else{
			$yymm = $_POST['yymm'];

			$sql .= '
					AND	yymm = \''.$yymm.'\'';
		}

		$sql .= '
			ORDER	BY yymm DESC, date DESC, time DESC
			LIMIT	1';

		$row = $conn->get_array($sql);

		$date = $myF->dateStyle($date);
		$time = $myF->timeStyle($time);
		$toTime = $myF->timeStyle($toTime);
	}

	if ($row){
		$IsNew = false;
	}else{
		$IsNew = true;
	}

	/*
	if ($row['mem_jumin'] && $row['mem_name']){
		$memJumin = Explode('/',$row['mem_jumin']);
		$memName = Explode('/',$row['mem_name']);

		if (is_array($memJumin)){
			foreach($memJumin as $i => $mem){
				$idx = SizeOf($memList);
				$memList[$idx] = Array('jumin'=>$ed->en($memJumin[$i]),'name'=>$memName[$i]);
			}
		}
	}else{
		if ($yymm){
			$sql = 'SELECT	DISTINCT
							t01_mem_cd1
					,		t01_mem_nm1
					,		t01_mem_cd2
					,		t01_mem_nm2
					FROM	t01iljung
					WHERE	t01_ccode = \''.$orgNo.'\'
					AND		t01_mkind = \'0\'
					AND		t01_jumin = \''.$jumin.'\'
					AND		t01_sugup_date >= \''.$yymm.'01\'
					AND		t01_sugup_date <= \''.$yymm.'31\'
					AND		t01_del_yn = \'N\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				$idx = SizeOf($memList);
				$memList[$idx] = Array('jumin'=>$ed->en($row['t01_mem_cd1']),'name'=>$row['t01_mem_nm1']);

				if ($row['t01_mem_nm2']){
					$idx = SizeOf($memList);
					$memList[$idx] = Array('jumin'=>$ed->en($row['t01_mem_cd2']),'name'=>$row['t01_mem_nm2']);
				}
			}

			$conn->row_free();

			if (is_array($memList)){
				$memList = $myF->sortArray($memList, 'name', 1);
			}
		}
	}
	*/
	if (!$IsNew){

		//요양보호사
		$sql = 'SELECT	t01_mem_cd1 AS mem_cd1
				,		t01_mem_nm1 AS mem_nm1
				,		t01_mem_cd2 AS mem_cd2
				,		t01_mem_nm2 AS mem_nm2
				,		t01_status_gbn AS stat
				,		t01_sugup_fmtime AS plan_from
				,		t01_sugup_totime AS plan_to
				,		t01_wrk_fmtime AS work_from
				,		t01_wrk_totime AS work_to
				,		t01_conf_fmtime AS conf_from
				,		t01_conf_totime AS conf_to
				,	    t01_toge_umu as toge_yn
				,		t01_sugup_soyotime as soyotime
				FROM	t01iljung
				WHERE	t01_ccode = \''.$orgNo.'\'
				AND		t01_mkind = \'0\'
				AND		t01_jumin = \''.$jumin.'\'
				AND		t01_sugup_date = \''.$row['date'].'\'
				AND		t01_svc_subcode = \'200\'
				AND		t01_del_yn = \'N\'';
		$memRow = $conn->_fetch_array($sql);

		$rCnt = SizeOf($memRow);

		for($i=0; $i<$rCnt; $i++){
			$r = $memRow[$i];

			if ($r['stat'] == '1'){
				$stat = '완료';
				$from = $r['conf_from'];
				$to = $r['conf_to'];
			}else if ($r['stat'] == '5'){
				$stat = '진행중';
				$from = $r['work_from'];
				$to = '9999';
			}else{
				$stat = '대기';
				$from = $r['plan_from'];
				$to = $r['plan_to'];
			}


			//5등급 인지활동 가족케어일 경우 시간 60분
			if($r['mem_nm2']!='' && $r['toge_yn']=='Y'){
				$soyoTime = $myF->time2min($to) - $myF->time2min($from);

				if($soyoTime==60){
					$to = $myF->min2time($myF->time2min($to) + 60);
				}else {
					$to = $myF->min2time($myF->time2min($to) + 30);
				}
			}

			if (($row['time'] >= $from && $row['time'] <= $to) || ($row['to_time'] >= $from && $row['to_time'] <= $to)){
				$idx = SizeOf($memList);
				$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd1']),'name'=>$r['mem_nm1'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));

				if ($r['mem_nm2']){
					$idx = SizeOf($memList);
					$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd2']),'name'=>$r['mem_nm2'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));
				}
			}
		}

		/*if (is_array($memList)){
			$memList = $myF->sortArray($memList, 'name', 1);
		}*/
	}
	
	
	$memCd = $ed->en($row['reg_jumin']);
	$memNm = $row['reg_name'];
	
	//직원로그인하였을 때
	if($gHostNm == 'pr'){
		$memNm = $memNm != '' ? $memNm : $_SESSION['userName'];
		$memCd = $memCd != '' ? $memCd : $_SESSION['userSSN'];
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		if ('<?=$type;?>' == 'SIGN'){
			$('#lblSW').text('<?=$memNm;?>');
			$('#lblDate').text(__getDate('<?=$date;?>','.'));
			$('#lblTime').text('<?=$time;?>');
			$('#lblToTime').text('<?=$toTime;?>');

			//$('#lblSW').attr('jumin','<?=$memCd;?>').text('<?=$memNm;?>');
			//$('#txtDate').attr('org','<?=$date;?>').val('<?=$date;?>');
			//$('#txtTime').attr('org','<?=$time;?>').val('<?=$time;?>');
		}else{
			
			if ('<?=$IsNew;?>' != '1'){
				$('#divSW').attr('jumin','<?=$memCd;?>').text('<?=$memNm;?>');
				$('#txtDate').attr('org','<?=$date;?>').val('<?=$date;?>');
				$('#txtTime').attr('org','<?=$time;?>').val('<?=$time;?>');
				$('#txtToTime').attr('org','<?=$toTime;?>').val('<?=$toTime;?>');
			}else {
				$('#divSW').attr('jumin','<?=$memCd;?>').text('<?=$memNm;?>');
			}
			
		}
	});
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="60px" span="2">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center" rowspan="12" id="ID_CELL_3_1">욕구사정</th>
			<th class="left" rowspan="2" colspan="2">신체상태</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<label><input id="optBodyStat1" name="optBodyStat" type="radio" class="radio" value="1" <?=($row['body_stat'] == '1' ? 'checked' : '');?>>완전자립</label>
					<label><input id="optBodyStat2" name="optBodyStat" type="radio" class="radio" value="2" <?=($row['body_stat'] == '2' ? 'checked' : '');?>>부분자립</label>
					<label><input id="optBodyStat3" name="optBodyStat" type="radio" class="radio" value="3" <?=($row['body_stat'] == '3' ? 'checked' : '');?>>전적인 도움</label><?
				}else{?>
					<div class="left"><?
					switch($row['body_stat']){
						case '1':
							echo '완전자립';
							break;

						case '2':
							echo '부분자립';
							break;

						case '3':
							echo '전적인 도움';
							break;
					}?>
					</div><?
				}?>
			</td>
		</tr>
		<tr>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtBodyStat" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['body_stat_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['body_stat_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" rowspan="2" colspan="2">질병</th>
			<td class="last">
				<div class="left" style="padding-top:3px;">
					<span style="width:40px;">질병명</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style=""><input id="txtDisease" type="text" init="Y" value="<?=StripSlashes($row['disease']);?>" style="width:200px;"></span><?
					}else{?>
						<span class="left"><?=StripSlashes($row['disease']);?></span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:40px;">약복용</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optMedicationY" name="optMedication" type="radio" class="radio" value="Y" <?=($row['medication'] == 'Y' ? 'checked' : '');?>>예</label>
							<label><input id="optMedicationN" name="optMedication" type="radio" class="radio" value="N" <?=($row['medication'] == 'N' ? 'checked' : '');?>>아니오</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['medication']){
								case 'Y':
									echo '예';
									break;

								case 'N':
									echo '아니오';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:40px;">진단명</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style=""><input id="txtDiagnosis" type="text" init="Y" value="<?=StripSlashes($row['diagnosis']);?>" style="width:200px;"></span><?
					}else{?>
						<span class="left"><?=StripSlashes($row['diagnosis']);?></span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:40px;">장애명</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style=""><input id="txtDisabled" type="text" init="Y" value="<?=StripSlashes($row['disabled']);?>" style="width:200px;"></span><?
					}else{
						if (!$row['disabled']) $row['disabled'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';?>
						<span class="left"><?=StripSlashes($row['disabled']);?></span><?
					}?>
					<span style="width:50px;">장애등급</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<select id="cboDisabled" style="width:auto;">
								<option value="" selected>없음</option>
								<option value="1" <?=($row['disabled_lvl'] == '1' ? 'selected' : '');?>>1등급</option>
								<option value="2" <?=($row['disabled_lvl'] == '2' ? 'selected' : '');?>>2등급</option>
								<option value="3" <?=($row['disabled_lvl'] == '3' ? 'selected' : '');?>>3등급</option>
								<option value="4" <?=($row['disabled_lvl'] == '4' ? 'selected' : '');?>>4등급</option>
								<option value="5" <?=($row['disabled_lvl'] == '5' ? 'selected' : '');?>>5등급</option>
								<option value="6" <?=($row['disabled_lvl'] == '6' ? 'selected' : '');?>>6등급</option>
							</select>
						</span><?
					}else{?>
						<span class="left"><?
							if ($row['disabled_lvl'] >= '1' && $row['disabled_lvl'] <= '6'){
								echo $row['disabled_lvl'].'등급';
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:40px;">시력</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optEyesight1" name="optEyesight" type="radio" class="radio" value="1" <?=($row['eyesight'] == '1' ? 'checked' : '');?>>양호</label>
							<label><input id="optEyesight2" name="optEyesight" type="radio" class="radio" value="2" <?=($row['eyesight'] == '2' ? 'checked' : '');?>>보통</label>
							<label><input id="optEyesight3" name="optEyesight" type="radio" class="radio" value="3" <?=($row['eyesight'] == '3' ? 'checked' : '');?>>나쁨</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['eyesight']){
								case '1':
									echo '양호';
									break;

								case '2':
									echo '보통';
									break;

								case '3':
									echo '나쁨';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:40px;">청력</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optHearing1" name="optHearing" type="radio" class="radio" value="1" <?=($row['hearing'] == '1' ? 'checked' : '');?>>양호</label>
							<label><input id="optHearing2" name="optHearing" type="radio" class="radio" value="2" <?=($row['hearing'] == '2' ? 'checked' : '');?>>보통</label>
							<label><input id="optHearing3" name="optHearing" type="radio" class="radio" value="3" <?=($row['hearing'] == '3' ? 'checked' : '');?>>나쁨</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['hearing']){
								case '1':
									echo '양호';
									break;

								case '2':
									echo '보통';
									break;

								case '3':
									echo '나쁨';
									break;
							}?>
						</span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtDiseaseNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['disease_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['disease_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" rowspan="2" colspan="2">인지상태</th>
			<td class="last">
				<div class="left" style="padding-top:3px;">
					<span style="width:70px;">인지,기억력</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optMemory1" name="optMemory" type="radio" class="radio" value="1" <?=($row['memory'] == '1' ? 'checked' : '');?>>명확</label>
							<label><input id="optMemory2" name="optMemory" type="radio" class="radio" value="2" <?=($row['memory'] == '2' ? 'checked' : '');?>>부분도움</label>
							<label><input id="optMemory3" name="optMemory" type="radio" class="radio" value="3" <?=($row['memory'] == '3' ? 'checked' : '');?>>불가능</label>
						</span><?
					}else{?>
						<span style=""><?
							switch($row['hearing']){
								case '1':
									echo '명확';
									break;

								case '2':
									echo '부분도움';
									break;

								case '3':
									echo '불가능';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:70px;">표현력</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optExpress1" name="optExpress" type="radio" class="radio" value="1" <?=($row['express'] == '1' ? 'checked' : '');?>>명확</label>
							<label><input id="optExpress2" name="optExpress" type="radio" class="radio" value="2" <?=($row['express'] == '2' ? 'checked' : '');?>>부분도움</label>
							<label><input id="optExpress3" name="optExpress" type="radio" class="radio" value="3" <?=($row['express'] == '3' ? 'checked' : '');?>>불가능</label>
						</span><?
					}else{?>
						<span style=""><?
							switch($row['express']){
								case '1':
									echo '명확';
									break;

								case '2':
									echo '부분도움';
									break;

								case '3':
									echo '불가능';
									break;
							}?>
						</span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtMemoryNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['memory_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['memory_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" rowspan="2" colspan="2">의사소통</th>
			<td class="last">
				<div class="left" style="padding-top:3px;">
					<span style="width:65px;">정서적상태</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optFeel1" name="optFeel" type="radio" class="radio" value="1" <?=($row['feel_stat'] == '1' ? 'checked' : '');?>>활발/적극</label>
							<label><input id="optFeel2" name="optFeel" type="radio" class="radio" value="2" <?=($row['feel_stat'] == '2' ? 'checked' : '');?>>조용/내성</label>
							<label><input id="optFeel3" name="optFeel" type="radio" class="radio" value="3" <?=($row['feel_stat'] == '3' ? 'checked' : '');?>>흥분/우울</label>
						</span><?
					}else{?>
						<span style=""><?
							switch($row['feel_stat']){
								case '1':
									echo '활발/적극';
									break;

								case '2':
									echo '조용/내성';
									break;

								case '3':
									echo '흥분/우울';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:65px;">기타</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style=""><input id="txtCommOther" text="text" init="Y" value="<?=StripSlashes($row['comm_other']);?>" style="width:200px;"></span><?
					}else{?>
						<span class="left"><?=StripSlashes($row['comm_other']);?></span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtCommNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['comm_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['comm_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" rowspan="2" colspan="2">영양상태</th>
			<td class="last">
				<div class="left" style="padding-top:3px;">
					<span style="width:55px;">식사형태</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optMealType1" name="optMealType" type="radio" class="radio" value="1" <?=($row['meal_type'] == '1' ? 'checked' : '');?>>일반식</label>
							<label><input id="optMealType2" name="optMealType" type="radio" class="radio" value="2" <?=($row['meal_type'] == '2' ? 'checked' : '');?>>당뇨식</label>
							<label><input id="optMealType3" name="optMealType" type="radio" class="radio" value="3" <?=($row['meal_type'] == '3' ? 'checked' : '');?>>죽</label>
							<label><input id="optMealType4" name="optMealType" type="radio" class="radio" value="4" <?=($row['meal_type'] == '4' ? 'checked' : '');?>>경관급식</label>
						</span><?
					}else{?>
						<span style=""><?
							switch($row['meal_type']){
								case '1':
									echo '일반식';
									break;

								case '2':
									echo '당뇨식';
									break;

								case '3':
									echo '죽';
									break;

								case '4':
									echo '경관급식';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:55px;">수분섭취</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optWaterType1" name="optWaterType" type="radio" class="radio" value="1" <?=($row['water_type'] == '1' ? 'checked' : '');?>>1일 5컵이상</label>
							<label><input id="optWaterType2" name="optWaterType" type="radio" class="radio" value="2" <?=($row['water_type'] == '2' ? 'checked' : '');?>>1일 2~4컵</label>
							<label><input id="optWaterType3" name="optWaterType" type="radio" class="radio" value="3" <?=($row['water_type'] == '3' ? 'checked' : '');?>>1일 1~2컵</label>
							<label><input id="optWaterType4" name="optWaterType" type="radio" class="radio" value="4" <?=($row['water_type'] == '4' ? 'checked' : '');?>>1일 1컵</label>
							<label><input id="optWaterType5" name="optWaterType" type="radio" class="radio" value="9" <?=($row['water_type'] == '9' ? 'checked' : '');?>>거의 드시지 않음</label>
						</span><?
					}else{?>
						<span style=""><?
							switch($row['water_type']){
								case '1':
									echo '1일 5컵이상';
									break;

								case '2':
									echo '1일 2~4컵';
									break;

								case '3':
									echo '1일 1~2컵';
									break;

								case '4':
									echo '1일 1컵';
									break;

								case '9':
									echo '거의 드시지 않음';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:55px;">섭취패턴</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optIntakeType1" name="optIntakeType" type="radio" class="radio" value="1" <?=($row['intake_type'] == '1' ? 'checked' : '');?>>3식을 규칙적으로 먹는다.</label>
							<label><input id="optIntakeType2" name="optIntakeType" type="radio" class="radio" value="2" <?=($row['intake_type'] == '2' ? 'checked' : '');?>>평균 2식을 먹는다.</label>
							<label><input id="optIntakeType3" name="optIntakeType" type="radio" class="radio" value="3" <?=($row['intake_type'] == '3' ? 'checked' : '');?>>1식만 먹는다.</label>
						</span><?
					}else{?>
						<span style=""><?
							switch($row['intake_type']){
								case '1':
									echo '3식을 규칙적으로 먹는다.';
									break;

								case '2':
									echo '평균 2식을 먹는다.';
									break;

								case '3':
									echo '1식만 먹는다.';
									break;
							}?>
						</span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtNutritionNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['nutrition_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['nutrition_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">가족 및 환경</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtEnvNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['env_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['env_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">종합</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtTotalNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['total_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['total_note']));?></div><?
				}?>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center" rowspan="3">급여제공<br>계획</th>
			<th class="left" colspan="2">급여목표</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtTargetNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['target_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['target_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">필요 급여내용</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtContNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['cont_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['cont_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">제공방법</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtProvideNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['provide_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['provide_note']));?></div><?
				}?>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center" rowspan="4">인지활동<br>프로그램<br>제공계획</th>
			<th class="left" rowspan="2">인지자극</th>
			<th class="left">필요내용</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtPlanRecText" init="Y" style="width:100%; height:35px;"><?=StripSlashes($row['plan_rec_text']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['plan_rec_text']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left">제공방법</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtPlanRecWay" init="Y" style="width:100%; height:35px;"><?=StripSlashes($row['plan_rec_way']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['plan_rec_way']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" rowspan="2">신체능력<br>잔존.유지</th>
			<th class="left">필요내용</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtPlanBodyText" init="Y" style="width:100%; height:35px;"><?=StripSlashes($row['plan_body_text']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['plan_body_text']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left">제공방법</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtPlanBodyWay" init="Y" style="width:100%; height:35px;"><?=StripSlashes($row['plan_body_way']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['plan_body_way']));?></div><?
				}?>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center" colspan="3">보호자 상담</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtGuardText" init="Y" style="width:100%; height:35px;"><?=StripSlashes($row['guard_text']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['guard_text']));?></div><?
				}?>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center" rowspan="7">급여 및&nbsp;&nbsp;<br>인지활동<br>프로그램<br>제공확인</th>
			<th class="left" rowspan="2" colspan="2">확인내용</th>
			<td class="last">
				<div class="left" style="padding-top:3px;">
					<span style="width:80px;">근무일지작성</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optWriteLogY" name="optWriteLog" type="radio" class="radio" value="Y" <?=($row['write_log_yn'] == 'Y' ? 'checked' : '');?>>예</label>
							<label><input id="optWriteLogN" name="optWriteLog" type="radio" class="radio" value="N" <?=($row['write_log_yn'] == 'N' ? 'checked' : '');?>>아니오</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['write_log_yn']){
								case 'Y':
									echo '예';
									break;

								case 'N':
									echo '아니오';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:80px;">제공시간준수</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optProvideChkY" name="optProvideChk" type="radio" class="radio" value="Y" <?=($row['provide_chk_yn'] == 'Y' ? 'checked' : '');?>>예</label>
							<label><input id="optProvideChkN" name="optProvideChk" type="radio" class="radio" value="N" <?=($row['provide_chk_yn'] == 'N' ? 'checked' : '');?>>아니오</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['provide_chk_yn']){
								case 'Y':
									echo '예';
									break;

								case 'N':
									echo '아니오';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:80px;">적절 서비스</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optRightSvcY" name="optRightSvc" type="radio" class="radio" value="Y" <?=($row['right_svc_yn'] == 'Y' ? 'checked' : '');?>>예</label>
							<label><input id="optRightSvcN" name="optRightSvc" type="radio" class="radio" value="N" <?=($row['right_svc_yn'] == 'N' ? 'checked' : '');?>>아니오</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['right_svc_yn']){
								case 'Y':
									echo '예';
									break;

								case 'N':
									echo '아니오';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:80px;">주거환경청결</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optHouseEnv1" name="optHouseEnv" type="radio" class="radio" value="1" <?=($row['house_env_yn'] == '1' ? 'checked' : '');?>>상</label>
							<label><input id="optHouseEnv2" name="optHouseEnv" type="radio" class="radio" value="2" <?=($row['house_env_yn'] == '2' ? 'checked' : '');?>>중</label>
							<label><input id="optHouseEnv3" name="optHouseEnv" type="radio" class="radio" value="3" <?=($row['house_env_yn'] == '3' ? 'checked' : '');?>>하</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['house_env_yn']){
								case '1':
									echo '상';
									break;

								case '2':
									echo '중';
									break;

								case '3':
									echo '하';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:80px;">업무태도친절</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optWorkMind1" name="optWorkMind" type="radio" class="radio" value="1" <?=($row['work_mind_yn'] == '1' ? 'checked' : '');?>>상</label>
							<label><input id="optWorkMind2" name="optWorkMind" type="radio" class="radio" value="2" <?=($row['work_mind_yn'] == '2' ? 'checked' : '');?>>중</label>
							<label><input id="optWorkMind3" name="optWorkMind" type="radio" class="radio" value="3" <?=($row['work_mind_yn'] == '3' ? 'checked' : '');?>>하</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['work_mind_yn']){
								case '1':
									echo '상';
									break;

								case '2':
									echo '중';
									break;

								case '3':
									echo '하';
									break;
							}?>
						</span><?
					}?>
				</div>
				<div class="left" style="padding-top:3px; border-top:1px solid #E7E7E7;">
					<span style="width:80px;">유니폼 착용</span>
					<span style="width:4px; text-align:center;">:</span><?
					if ($type != 'SIGN'){?>
						<span style="">
							<label><input id="optUniformY" name="optUniform" type="radio" class="radio" value="Y" <?=($row['uniform_yn'] == 'Y' ? 'checked' : '');?>>예</label>
							<label><input id="optUniformN" name="optUniform" type="radio" class="radio" value="N" <?=($row['uniform_yn'] == 'N' ? 'checked' : '');?>>아니오</label>
						</span><?
					}else{?>
						<span class="left"><?
							switch($row['uniform_yn']){
								case 'Y':
									echo '예';
									break;

								case 'N':
									echo '아니오';
									break;
							}?>
						</span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtCheckNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['check_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['check_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">조치사항</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtActionNote" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['action_note']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['action_note']));?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2"><?
				if($yymm < '201701'){ ?>
					<div style="float:left; width:auto; height:25px; line-height:25px;">*요양보호사</div><?
				}else { ?>
					<div style="float:left; width:auto; height:25px; line-height:25px;">*급여제공자 성명</div><?
				} ?>
				<div style="float:left; width:auto; height:23px; margin-left:5px;"><!--span class="btn_pack find" onclick="lfMemFind();"></span--></div>
			</th>
			<td id="tdMemParent" class="left last"><?
				if (is_array($memList)){
					//$row['svcporc_yn'] = '';
					foreach($memList as $idx => $mem){?>
						<div id="divMem<?=$idx;?>" jumin="<?=$mem['jumin'];?>" style="float:left; width:auto;">
							<span><?=$mem['name'];?></span>
							<span>[<?=$mem['stat'];?>/<?=$mem['from'];?>~<?=$mem['to'];?>]</span>
							<!--span style="color:RED; cursor:pointer;" onclick="$(this).parent().remove();">X</span-->
						</div><?

						//if ($mem['stat'] == '완료' || $mem['stat'] == '진행중'){
						//	$row['svcporc_yn'] = 'Y';
						//}
					}
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">*방문장소</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<input id="txtVisitPlace" init="Y" style="width:100%;" value="<?=StripSlashes($row['visit_place'] ? $row['visit_place'] : '수급자 가정');?>"><?
				}else{?>
					<div class="left"><?=StripSlashes($row['visit_place']);?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">
				<div style="float:left; width:auto; height:25px; line-height:25px;">*급여제공 중 방문여부</div>
			</th>
			<td class="last"><?
				if ($type != 'SIGN'){?>
					<label><input id="optSvcProcY" name="optSvcProcYn" type="radio" class="radio" value="Y" <?=($row['svcporc_yn'] == 'Y' ? 'checked' : '');?>>예</label>
					<label><input id="optSvcProcN" name="optSvcProcYn" type="radio" class="radio" value="N" <?=($row['svcporc_yn'] == 'N' ? 'checked' : '');?>>아니오</label><?
				}else{?>
					<span class="left"><?
						switch($row['svcporc_yn']){
							case 'Y':
								echo '예';
								break;

							case 'N':
								echo '아니오';
								break;
						}?>
					</span><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">
				<div style="float:left; width:auto; height:25px; line-height:25px;">*방문불가 사유</div>
			</th>
			<td class="last"><?
				if ($type != 'SIGN'){
					if (!$row['notvisit_cd'] && $row['notvisit_reason']) $row['notvisit_cd'] = '9';?>
					<div>
						<label><input id="optNotVisitReason_0" name="optNotVisitReason" type="radio" class="radio" value="" <?=$row['notvisit_cd'] == '' ? 'checked' : '';?>>없음</label>
						<label><input id="optNotVisitReason_1" name="optNotVisitReason" type="radio" class="radio" value="1" <?=$row['notvisit_cd'] == '1' ? 'checked' : '';?>>사망</label>
						<label><input id="optNotVisitReason_2" name="optNotVisitReason" type="radio" class="radio" value="2" <?=$row['notvisit_cd'] == '2' ? 'checked' : '';?>>병원</label>
						<label><input id="optNotVisitReason_3" name="optNotVisitReason" type="radio" class="radio" value="3" <?=$row['notvisit_cd'] == '3' ? 'checked' : '';?>>해지</label>
						<label><input id="optNotVisitReason_4" name="optNotVisitReason" type="radio" class="radio" value="9" <?=$row['notvisit_cd'] == '9' ? 'checked' : '';?>>기타</label>
					</div>
					<script type="text/javascript">
						$('input:radio[name="optNotVisitReason"]').unbind('click').bind('click',function(){
							if ($(this).val() != ''){
								$('#txtNotVisitReason').attr('disabled',false);
							}else{
								$('#txtNotVisitReason').attr('disabled',true);
							}
						});
						$('input:radio[name="optNotVisitReason"]:checked').click();
					</script>
					<div style="margin-bottom:5px;"><input id="txtNotVisitReason" init="Y" style="width:100%;" value="<?=StripSlashes($row['notvisit_reason']);?>"></div><?
				}else{
					$str = '';

					if (!$row['notvisit_cd'] && $row['notvisit_reason']) $row['notvisit_cd'] = '9';
					if ($row['notvisit_cd']){
						if ($row['notvisit_cd'] == '1'){
							$str = '사망';
						}else if ($row['notvisit_cd'] == '2'){
							$str = '병원';
						}else if ($row['notvisit_cd'] == '3'){
							$str = '해지';
						}else if ($row['notvisit_cd'] == '9'){
							$str = '기타';
						}

						if ($str) $str .= ' - ';
					}?>
					<div class="left"><?=$str.StripSlashes($row['notvisit_reason']);?></div><?
				}?>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center bottom">총평</th>
			<td class="bottom last" colspan="3"><?
				if ($type != 'SIGN'){?>
					<textarea id="txtComment" init="Y" style="width:100%; height:50px;"><?=StripSlashes($row['comment']);?></textarea><?
				}else{?>
					<div class="left"><?=nl2br(StripSlashes($row['comment']));?></div><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
<?
	//서명
	$sql = 'SELECT	m03_key
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_jumin = \''.$jumin.'\'';

	$key = $conn->get_data($sql);

	//서명 - 수급자 및 보호자
	$targetFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-1.jpg';
	if (!is_file($targetFile)) $targetFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/_7-1.jpg';

	//서명 - 요양보호사
	$yoyFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-2.jpg';
	if (!is_file($yoyFile)) $yoyFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/_7-2.jpg';

	//서명 - 방문자
	$visitFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-3.jpg';
	if (!is_file($visitFile)) $visitFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/_7-3.jpg';

	//관리자
	if (is_numeric($row['sign_manager'])){
		$signManagerFile = '../sign/sign/manager/'.$orgNo.'/'.$row['sign_manager'].'.jpg';
	}else{
		$signManagerFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-4.jpg';
	}

	if (is_file($targetFile)){
		$tmpIf = GetImageSize($targetFile);
		$w1 = $tmpIf[0];
		$h1 = $tmpIf[1];
	}

	if (is_file($yoyFile)){
		$tmpIf = GetImageSize($yoyFile);
		$w2 = $tmpIf[0];
		$h2 = $tmpIf[1];
	}

	if (is_file($visitFile)){
		$tmpIf = GetImageSize($visitFile);
		$w3 = $tmpIf[0];
		$h3 = $tmpIf[1];
	}


	if (is_file($signManagerFile)){
		$tmpIf = GetImageSize($signManagerFile);
		$w4 = $tmpIf[0];
		$h4 = $tmpIf[1];
	}?>

	<script type="text/javascript">
		function lfResizeImg(maxW, maxH, objW, objH){
			var w1 = maxW, h1 = maxH,
				w2 = objW, h2 = objH, w = 0, h = 0, r = 1;

			if (w2 > h2){
				r = w1 / w2;
			}else if (h2 >= w2){
				r = h1 / h2;
			}

			w = w2 * r;
			h = h2 * r;

			return {'w':w,'h':h};
		}

		$('#ID_IMG_NAME_TG').text('<?=$row["sign_target"];?>');
		$('#ID_IMG_NAME_YO').text('<?=$row["sign_yoy"];?>');
		$('#ID_IMG_NAME_VS').text('<?=$row["sign_visit"];?>');

		if ('<?=$w1;?>' != '' && '<?=$h1;?>' != ''){
			var pos = lfResizeImg($('#ID_IMG_SIGN_TG').width() - 25, $('#ID_IMG_SIGN_TG').height() - 25, __str2num('<?=$w1;?>'), __str2num('<?=$h1;?>'));
			$('#ID_IMG_SIGN_TG').html('<img src="<?=$targetFile;?>" style="width:'+pos['w']+'; height:'+pos['h']+';" border="0">');
		}

		if ('<?=$w2;?>' != '' && '<?=$h2;?>' != ''){
			var pos = lfResizeImg($('#ID_IMG_SIGN_YO').width() - 25, $('#ID_IMG_SIGN_YO').height() - 25, __str2num('<?=$w2;?>'), __str2num('<?=$h2;?>'));
			$('#ID_IMG_SIGN_YO').html('<img src="<?=$yoyFile;?>" style="width:'+pos['w']+'; height:'+pos['h']+';" border="0">');
		}

		if ('<?=$w3;?>' != '' && '<?=$h3;?>' != ''){
			var pos = lfResizeImg($('#ID_IMG_SIGN_VS').width() - 25, $('#ID_IMG_SIGN_VS').height() - 25, __str2num('<?=$w3;?>'), __str2num('<?=$h3;?>'));
			$('#ID_IMG_SIGN_VS').html('<img src="<?=$visitFile;?>" style="width:'+pos['w']+'px; height:'+pos['h']+'px;" border="0">');
		}

		if ('<?=$w4;?>' != '' && '<?=$h4;?>' != ''){
			var pos = lfResizeImg($('#ID_IMG_SIGN_MG').width() - 25, $('#ID_IMG_SIGN_MG').height() - 25, __str2num('<?=$w4;?>'), __str2num('<?=$h4;?>'));
			$('#ID_IMG_SIGN_MG').html('<img src="<?=$signManagerFile;?>" style="width:'+pos['w']+'; height:'+pos['h']+';" border="0">');
		}
	</script><?

	Unset($row);

	include_once('../inc/_db_close.php');
?>