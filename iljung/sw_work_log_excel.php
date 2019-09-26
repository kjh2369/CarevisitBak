<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['yymm'];
	$seq	= $_POST['seq'];

	//기관명 및 연락처
	$sql = 'SELECT	m00_store_nm AS name
			,		m00_ctel AS telno
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			AND		m00_mkind = \'0\'';

	$row = $conn->get_array($sql);

	$center = $row['name'];
	$telno = $myF->phoneStyle($row['telno'],'.');

	Unset($row);

	//수급자명 및 주소
	$sql = 'SELECT	m03_name AS name
			,		m03_juso1 AS addr
			,		m03_juso2 AS addr_dtl
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'0\'
			AND		m03_jumin = \''.$jumin.'\'';

	$row = $conn->get_array($sql);

	$name = $row['name'];
	$addr = $row['addr'].' '.$row['addr_dtl'];

	Unset($row);

	//등급 및 인정번호
	$sql = 'SELECT	app_no
			,		level
			FROM	client_his_lvl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \'0\'
			AND		jumin	= \''.$jumin.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$yymm.'\'
			ORDER	BY from_dt DESC, to_dt DESC
			LIMIT	1';

	$row = $conn->get_array($sql);

	$appNo = $row['app_no'];
	$lvl = $row['level'];
	/*
	if ($lvl >= '1' && $lvl <= '5'){
		$lvl .= '등급';
	}else{
		$lvl = '일반';
	}
	*/

	$row['lvl_nm'] = $myF->_lvlNm($lvl);

	Unset($row);

	//일지
	$sql = 'SELECT	*
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		seq		= \''.$seq.'\'';

	$row = $conn->get_array($sql);

	//요양보호사
	$sql = 'SELECT	t01_yoyangsa_id1 AS mem_cd1
			,		t01_yname1 AS mem_nm1
			,		t01_yoyangsa_id2
			,		t01_yname2 AS mem_nm2
			,		t01_status_gbn AS stat
			,		t01_sugup_fmtime AS plan_from
			,		t01_sugup_totime AS plan_to
			,		t01_wrk_fmtime AS work_from
			,		t01_wrk_totime AS work_to
			,		t01_conf_fmtime AS conf_from
			,		t01_conf_totime AS conf_to
			FROM	t01iljung
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \'0\'
			AND		t01_jumin = \''.$jumin.'\'
			AND		t01_sugup_date = \''.$row['date'].'\'
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

		if ($row['time'] >= $from && $row['time'] <= $to){
			$idx = SizeOf($memList);
			$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd1']),'name'=>$r['mem_nm1'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));

			if ($r['mem_nm2']){
				$idx = SizeOf($memList);
				$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd2']),'name'=>$r['mem_nm2'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));
			}
		}
	}

	if (is_array($memList)){
		$memList = $myF->sortArray($memList, 'name', 1);

		foreach($memList as $idx => $mem){
			$memStr .= ($mem['name']."[".$mem['stat']."/".$mem['from']."~".$mem['to']."]   ");
		}
	}

	Unset($memList);

	header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=carevisit_excel_".date('Ymd').".xls" );
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<div style="font-size:10px;">[별지 제24호 서식]</div>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; table-layout:fixed;">
	<tr>
		<td rowspan="2" colspan="4" style="border:0.5pt solid #000000; text-align:center; vertical-align:middle; font-size:17px; font-weight:bold;">
			프로그램 관리자 및 방문요양기관 사회복지사 업무수행 일지
		</td>
		<td rowspan="2" style="border:0.5pt solid #000000; text-align:center;">확인</td>
		<td style="border:0.5pt solid #000000; text-align:center;">방문자</td>
		<td style="border:0.5pt solid #000000; text-align:center;">요양보호사</td>
		<td style="border:0.5pt solid #000000; text-align:center;">관리책임자</td>
	</tr>
	<tr style="height:45px;">
		<td style="border:0.5pt solid #000000; text-align:center;"></td>
		<td style="border:0.5pt solid #000000; text-align:center;"></td>
		<td style="border:0.5pt solid #000000; text-align:center;"></td>
	</tr>
	<tr style="height:10px;">
		<td style="border:0.5pt solid #000000; width:60px; border:none;"></td>
		<td style="border:0.5pt solid #000000; width:70px; border:none;"></td>
		<td style="border:0.5pt solid #000000; width:70px; border:none;"></td>
		<td style="border:0.5pt solid #000000; width:130px; border:none;"></td>
		<td style="border:0.5pt solid #000000; width:50px; border:none;"></td>
		<td style="border:0.5pt solid #000000; width:85px; border:none;"></td>
		<td style="border:0.5pt solid #000000; width:85px; border:none;"></td>
		<td style="border:0.5pt solid #000000; width:85px; border:none;"></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;">수급자</td>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">장기요양등급</td>
		<td style="border:0.5pt solid #000000; text-align:center;">장기요양인정번호</td>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">방문일시</td>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">수급자(보호자) 서명</td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;"><?=$name;?></td>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2"><?=$lvl;?></td>
		<td style="border:0.5pt solid #000000; text-align:center;"><?=$appNo;?></td>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2"><?=$myF->dateStyle($row['date'],'.');?> <?=$myF->timeStyle($row['time']);?></td>
		<td style="border:0.5pt solid #000000; text-align:center;" rowspan="2" colspan="2"></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:left;" colspan="6"><?=$addr;?></td>
	</tr>
	<tr style="height:10px;">
		<td style="border:none;"></td>
		<td style="border:none;"></td>
		<td style="border:none;"></td>
		<td style="border:none;"></td>
		<td style="border:none;"></td>
		<td style="border:none;"></td>
		<td style="border:none;"></td>
		<td style="border:none;"></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" rowspan="12">욕구사정</td>
		<td style="border:0.5pt solid #000000;" rowspan="2" colspan="2">①신체상태</td>
		<td style="border:0.5pt solid #000000; border-bottom:0.5pt dotted #8C8C8C;" colspan="5">
			<?=$row['body_stat'] == '1' ? '▣' : '□';?>완전자립
			<?=$row['body_stat'] == '2' ? '▣' : '□';?>부분자립
			<?=$row['body_stat'] == '3' ? '▣' : '□';?>전적인 도움
		</td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; border-top:0.5pt dotted #8C8C8C; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['body_stat_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000;" rowspan="2" colspan="2">②질병</td>
		<td style="border:0.5pt solid #000000; border-bottom:0.5pt dotted #8C8C8C;" colspan="5">
			<div>질병명 : <?=$row['disease'];?> <?=$row['medication'] == 'Y' ? '(약복용)' : '';?></div>
			<div>진단명 : <?=$row['diagnosis'];?></div>
			<div>장애명 : <?=$row['disabled'];?> <?=$row['disabled'] ? '('.$row['disabled_lvl'].'등급)' : '';?></div>
			<div>시력 :
				<?=$row['eyesight'] == '1' ? '▣' : '□';?>양호
				<?=$row['eyesight'] == '2' ? '▣' : '□';?>보통
				<?=$row['eyesight'] == '3' ? '▣' : '□';?>나쁨
			</div>
			<div>청력 :
				<?=$row['hearing'] == '1' ? '▣' : '□';?>양호
				<?=$row['hearing'] == '2' ? '▣' : '□';?>보통
				<?=$row['hearing'] == '3' ? '▣' : '□';?>나쁨
			</div>
		</td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; border-top:0.5pt dotted #8C8C8C; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['disease_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000;" rowspan="2" colspan="2">③인지상태</td>
		<td style="border:0.5pt solid #000000; border-bottom:0.5pt dotted #8C8C8C;" colspan="5">
			<div>인지,기억력 :
				<?=$row['memory'] == '1' ? '▣' : '□';?>명확
				<?=$row['memory'] == '2' ? '▣' : '□';?>부분도움
				<?=$row['memory'] == '3' ? '▣' : '□';?>불가능
			</div>
			<div>표현력 :
				<?=$row['express'] == '1' ? '▣' : '□';?>명확
				<?=$row['express'] == '2' ? '▣' : '□';?>부분도움
				<?=$row['express'] == '3' ? '▣' : '□';?>불가능
			</div>
		</td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; border-top:0.5pt dotted #8C8C8C; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['memory_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000;" rowspan="2" colspan="2">④의사소통</td>
		<td style="border:0.5pt solid #000000; border-bottom:0.5pt dotted #8C8C8C;" colspan="5">
			<div>정서적상태 :
				<?=$row['feel_stat'] == '1' ? '▣' : '□';?>활발/적극
				<?=$row['feel_stat'] == '2' ? '▣' : '□';?>조용/내성
				<?=$row['feel_stat'] == '3' ? '▣' : '□';?>흥분/우울
			</div>
			<div>기타 : <?=StripSlashes($row['comm_other']);?></div>
		</td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; border-top:0.5pt dotted #8C8C8C; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['comm_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000;" rowspan="2" colspan="2">⑤영양상태</td>
		<td style="border:0.5pt solid #000000; border-bottom:0.5pt dotted #8C8C8C;" colspan="5">
			<div>식사형태 :
				<?=$row['meal_type'] == '1' ? '▣' : '□';?>일반식
				<?=$row['meal_type'] == '2' ? '▣' : '□';?>당뇨식
				<?=$row['meal_type'] == '3' ? '▣' : '□';?>죽
				<?=$row['meal_type'] == '4' ? '▣' : '□';?>경관급식
			</div>
			<div>수분섭취 :
				<?=$row['water_type'] == '1' ? '▣' : '□';?>1일 5컵이상
				<?=$row['water_type'] == '2' ? '▣' : '□';?>1일 2~4컵
				<?=$row['water_type'] == '3' ? '▣' : '□';?>1일 1~2컵
			</div>
			<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?=$row['water_type'] == '4' ? '▣' : '□';?>1일 1컵
				<?=$row['water_type'] == '9' ? '▣' : '□';?>거의 드시지 않음
			</div>
			<div>섭취패턴 :
				<?=$row['intake_type'] == '1' ? '▣' : '□';?>3식을 규칙적으로 먹는다.
				<?=$row['intake_type'] == '2' ? '▣' : '□';?>평균 2식을 먹는다.
			</div>
			<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?=$row['intake_type'] == '3' ? '▣' : '□';?>1식만 먹는다.
			</div>
		</td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; border-top:0.5pt dotted #8C8C8C; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['nutrition_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000;" colspan="2">⑥가족 및 환경</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['env_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">종합</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['total_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" rowspan="3">급여제공계획</td>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">급여목표</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['target_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">필요 급여내용</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['cont_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">제공방법</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['provide_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" rowspan="4">인지활동 프로그램 제공계획</td>
		<td style="border:0.5pt solid #000000; text-align:center;" rowspan="2">인지자극</td>
		<td style="border:0.5pt solid #000000; text-align:center;">필요내용</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['plan_rec_text']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;">제공방법</td>
		<td style="border:0.5pt solid #000000;" colspan="5"><?=StripSlashes($row['plan_rec_way']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" rowspan="2">신체능력 잔존,유지</td>
		<td style="border:0.5pt solid #000000; text-align:center;">필요내용</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['plan_body_text']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;">제공방법</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['plan_body_way']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="3">보호자 상담</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['guard_text']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" rowspan="7">급여제공확인</td>
		<td style="border:0.5pt solid #000000; text-align:center;" rowspan="2" colspan="2">확인내용</td>
		<td style="border:0.5pt solid #000000;" colspan="5">
			<div>근무일지작성 : <?=$row['write_log_yn'] == 'Y' ? '▣' : '□';?>예 <?=$row['write_log_yn'] == 'N' ? '▣' : '□';?>아니오</div>
			<div>제공시간준수 : <?=$row['provide_chk_yn'] == 'Y' ? '▣' : '□';?>예 <?=$row['provide_chk_yn'] == 'N' ? '▣' : '□';?>아니오</div>
			<div>적설 서비스 : <?=$row['right_svc_yn'] == 'Y' ? '▣' : '□';?>예 <?=$row['right_svc_yn'] == 'N' ? '▣' : '□';?>아니오</div>
			<div>주거환경청결 : <?=$row['house_env_yn'] == 'Y' ? '▣' : '□';?>예 <?=$row['house_env_yn'] == 'N' ? '▣' : '□';?>아니오</div>
			<div>업무태도친절 : <?=$row['work_mind_yn'] == 'Y' ? '▣' : '□';?>예 <?=$row['work_mind_yn'] == 'N' ? '▣' : '□';?>아니오</div>
			<div>유니폼 착용 : <?=$row['uniform_yn'] == 'Y' ? '▣' : '□';?>예 <?=$row['uniform_yn'] == 'N' ? '▣' : '□';?>아니오</div>
		</td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; border-top:0.5pt dotted #8C8C8C;" colspan="5"><?=StripSlashes($row['check_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">조치사항</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['action_note']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">요양보호사 성명</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=$memStr;?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">급여제공방문여부</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=$row['svcporc_yn'] == 'Y' ? '▣' : '□';?>예 <?=$row['svcporc_yn'] == 'N' ? '▣' : '□';?>아니오</td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">방문장소</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['visit_place']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="2">방문불가 사유</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['notvisit_reason']);?></td>
	</tr>
	<tr>
		<td style="border:0.5pt solid #000000; text-align:center;" colspan="3">총평</td>
		<td style="border:0.5pt solid #000000; mso-number-format:'\@';" colspan="5"><?=StripSlashes($row['comment']);?></td>
	</tr>
</table>
<div style="height:30px;">&nbsp;</div>
<div style="text-align:center; font-size:17px; font-weightL:bold;"><?=$center;?><?=$telno ? '('.$telno.')' : '';?></div>
</body>
</html>
<?
	Unset($row);
	include_once('../inc/_db_close.php');
?>