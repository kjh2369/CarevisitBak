<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_myImage.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];

	$val = $myF->parseGet($_GET['data']);

	if ($val['type'] == 'blank'){
		$juminVal = Explode('||',$val['jumin']);
		foreach($juminVal as $tmpI => $cd){
			$jumin[] = $ed->de64($cd);
		}
	}else{
		$jumin[] = $ed->de64($val['jumin']);
	}

	$yymm	= $val['yymm'];
	$seq	= $val['seq'];
	$mode   = $val['mode'];

	if (!$yymm) $yymm = Date('Ym');


	//기관정보
	$sql = 'SELECT	m00_store_nm AS org_nm, m00_ctel AS phone
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			AND		m00_mkind = \'0\'';

	$tmpR = $conn->get_array($sql);

	$orgNm = $tmpR['org_nm'];
	$orgTel= $myF->phoneStyle($tmpR['phone'],'.');

	Unset($tmpR);


	if ($val['type'] != 'blank'){
		//업무일지
		$sql = 'SELECT	*
				FROM	sw_log
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin[0].'\'
				AND		yymm	= \''.$yymm.'\'
				AND		seq		= \''.$seq.'\'
				AND		del_flag= \'N\'';
		$R = $conn->get_array($sql);
	}


	foreach($jumin as $tmpIdx => $cd){
		//수급자명
		$sql = 'SELECT	m03_name AS name, m03_juso1 AS addr, m03_juso2 AS addr_dtl
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_mkind = \'0\'
				AND		m03_jumin = \''.$cd.'\'';
		$tmpR = $conn->get_array($sql);

		$name[$tmpIdx] = $tmpR['name'];
		$addr[$tmpIdx] = Explode(chr(13),$tmpR['addr']);
		$addr[$tmpIdx] = $addr[$tmpIdx][0].' '.$tmpR['addr_dtl'];

		Unset($tmpR);


		//등급, 인정번호
		$sql = 'SELECT	app_no, level
				FROM	client_his_lvl
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		jumin	 = \''.$cd.'\'
				AND		svc_cd	 = \'0\'
				AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
				AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$yymm.'\'';

		$tmpR = $conn->get_array($sql);
		$appNo[$tmpIdx] = $tmpR['app_no'];
		//$level[$tmpIdx] = $tmpR['level'];


		$level[$tmpIdx] = $myF->_lvlNm($tmpR['level']);

		Unset($tmpR);



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
				AND		t01_jumin = \''.$cd.'\'
				AND		t01_sugup_date = \''.$R['date'].'\'
				AND		t01_del_yn = \'N\'';
		$memRow = $conn->_fetch_array($sql);


		$rCnt = SizeOf($memRow);

		for($i=0; $i<$rCnt; $i++){
			$tmpR = $memRow[$i];

			if ($tmpR['stat'] == '1'){
				$stat = '완료';
				$from = $tmpR['conf_from'];
				$to = $tmpR['conf_to'];
			}else if ($tmpR['stat'] == '5'){
				$stat = '진행중';
				$from = $tmpR['work_from'];
				$to = '9999';
			}else{
				$stat = '대기';
				$from = $tmpR['plan_from'];
				$to = $tmpR['plan_to'];
			}

			//5등급 인지활동 가족케어일 경우 시간 60분
			if($tmpR['mem_nm2']!='' && $tmpR['toge_yn']=='Y'){
				$soyoTime = $myF->time2min($to) - $myF->time2min($from);

				if($soyoTime==60){
					$to = $myF->min2time($myF->time2min($to) + 60);
				}else {
					$to = $myF->min2time($myF->time2min($to) + 30);
				}
			}

			if (($R['time'] >= $from && $R['time'] <= $to) || ($R['to_time'] >= $from && $R['to_time'] <= $to)){
				$idx = SizeOf($memList);
				$memList[$idx] = Array('jumin'=>$ed->en($tmpR['mem_cd1']),'name'=>$tmpR['mem_nm1'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));

				if ($tmpR['mem_nm2']){
					$idx = SizeOf($memList);
					$memList[$idx] = Array('jumin'=>$ed->en($tmpR['mem_cd2']),'name'=>$tmpR['mem_nm2'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));
				}
			}
		}

		if (is_array($memList)){
			//$memList = $myF->sortArray($memList, 'name', 1);

			foreach($memList as $idx => $mem){
				$memStr[$idx] .= ($mem['name']."[".$mem['stat']."/".$mem['from']."~".$mem['to']."]   ");
			}
		}

		Unset($memList);
	}


	if ($val['type'] != 'blank'){
		//서명
		$sql = 'SELECT	m03_key
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_jumin = \''.$jumin[0].'\'';

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
		if (is_numeric($R['sign_manager'])){
			$signManagerFile = '../sign/sign/manager/'.$orgNo.'/'.$R['sign_manager'].'.jpg';
		}else{
			$signManagerFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-4.jpg';
		}

		if (is_file($targetFile)){
			$tmpIf = GetImageSize($targetFile);
			$size = $myImage->getImgSize(90, 45, $tmpIf[0], $tmpIf[1]);
			$w1 = $size['w'];
			$h1 = $size['h'];
		}

		if (is_file($yoyFile)){
			$tmpIf = GetImageSize($yoyFile);
			$size = $myImage->getImgSize(90, 45, $tmpIf[0], $tmpIf[1]);
			$w2 = $size['w'];
			$h2 = $size['h'];
		}

		if (is_file($visitFile)){
			$tmpIf = GetImageSize($visitFile);
			$size = $myImage->getImgSize(90, 45, $tmpIf[0], $tmpIf[1]);
			$w3 = $size['w'];
			$h3 = $size['h'];
		}


		if (is_file($signManagerFile)){
			$tmpIf = GetImageSize($signManagerFile);
			$size = $myImage->getImgSize(90, 45, $tmpIf[0], $tmpIf[1]);
			$w4 = $size['w'];
			$h4 = $size['h'];
		}
	}


	$style = 'border:1px solid BLACK;'; //line-height:1.3em;
	$bold = 'font-weight:bold;';

	//다음페이지 <p style="page-break-before:always">
	//div,td,body {font:10pt/1.5; letter-spacing:-0.8px; color:#000000;}
?>
<style type="text/css">
	div,td,body {font:9pt/1.5; letter-spacing:-0.8px; color:#000000; line-height:1em;}
	td,th{height:25px;}
</style><?

foreach($jumin as $tmpI => $cd){
	if ($tmpI > 0){?>
		<p style="page-break-before:always;">&nbsp;</p><?
	}?>
	<div style="color:BLACK;">
	<div style="font-size:11px;">[별지 제24호서식]</div>
<?
	if($mode == '1'){ ?>
		<table style="width:100%;">
			<colgroup>
				<col>
				<col width="50px">
				<col width="90px" span="3">
			</colgroup>
			<tbody><?
				if($yymm < '201701'){ ?>
					<tr>
						<td rowspan="2" style="<?=$style;?> font-size:19px;">프로그램 관리자 및 방문요양기관<br>사회복지사 업무수행 일지</td>
						<td rowspan="2" style="<?=$style;?>">확<br>인</td>
						<td style="<?=$style;?>">방문자</td>
						<td style="<?=$style;?>">요양보호사</td>
						<td style="<?=$style;?>">관리책임자</td>
					</tr><?
				}else { ?>
					<tr>
						<td rowspan="2" style="<?=$style;?> font-size:19px;">프로그램 관리자 및<br>사회복지사 업무수행 일지</td>
						<td rowspan="2" style="<?=$style;?>">확<br>인</td>
						<td style="<?=$style;?>">방문자</td>
						<td style="<?=$style;?>">요양보호사</td>
						<td style="<?=$style;?>">관리책임자</td>
					</tr>
				<? } ?>
				<tr>
					<td style="<?=$style;?> height:50px;"><?if (is_file($visitFile)){?><img src="<?=$visitFile;?>" style="width:<?=$w3;?>px; height:<?=$h3;?>px;"><?};?></td>
					<td style="<?=$style;?>"><?if (is_file($yoyFile)){?><img src="<?=$yoyFile;?>" style="width:<?=$w2;?>px; height:<?=$h2;?>px;"><?};?></td>
					<td style="<?=$style;?>"><?if (is_file($signManagerFile)){?><img src="<?=$signManagerFile;?>" style="width:<?=$w4;?>px; height:<?=$h4;?>px;"><?};?></td>
				</tr>
				<tr>
					<td style="height:10px; border:none;"></td>
				</tr>
			</tbody>
		</table>

		<table style="width:100%;">
			<colgroup>
				<col width="100px">
				<col width="110px">
				<col width="130px">
				<col width="170px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td style="<?=$style;?>">수급자 성명</td>
					<td style="<?=$style;?>">장기요양등급</td>
					<td style="<?=$style;?>">장기요양인정번호</td>
					<td style="<?=$style;?>">방문일시</td>
					<td style="<?=$style;?>">수급자(보호자)</td>
				</tr>
				<tr>
					<td style="<?=$style;?>"><?=$name[$tmpI];?></td>
					<td style="<?=$style;?>"><?=$level[$tmpI];?></td>
					<td style="<?=$style;?>"><?=$appNo[$tmpI];?></td>
					<td style="<?=$style;?>"><?=$myF->dateStyle($R['date'],'.')?> <?=$myF->timeStyle($R['time']);?> ~ <?=$myF->timeStyle($R['to_time']);?></td>
					<td style="<?=$style;?>" rowspan="2"><?if (is_file($targetFile)){?><img src="<?=$targetFile;?>" style="width:<?=$w1;?>; height:<?=$h1;?>;"><?};?></td>
				</tr>
				<tr>
					<td style="<?=$style;?>" colspan="4"><?=$addr[$tmpI];?></td>
				</tr>
				<tr>
					<td style="height:10px; border:none;"></td>
				</tr>
			</tbody>
		</table>
		<?
	}else { ?>

		<table style="width:100%;">
			<colgroup>
				<col>
				<col width="50px">
				<col width="140px" span="2">
			</colgroup>
			<tbody>
				<tr>
					<td rowspan="2" style="<?=$style;?> font-size:19px;">프로그램 관리자 및<br>사회복지사 업무수행 일지</td>
					<td rowspan="2" style="<?=$style;?>">확<br>인</td>
					<td style="<?=$style;?>">수급자(보호자) 성명(인)</td>
					<!--<td style="<?=$style;?>">요양보호사</td>-->
					<td style="<?=$style;?>">관리책임자 성명(인)</td>
				</tr>
				<tr>
					<td style="<?=$style;?> height:50px;"><?if (is_file($targetFile)){?><img src="<?=$targetFile;?>" style="width:<?=$w1;?>; height:<?=$h1;?>;"><?};?></td>
					<!--<td style="<?=$style;?>"><?if (is_file($yoyFile)){?><img src="<?=$yoyFile;?>" style="width:<?=$w2;?>px; height:<?=$h2;?>px;"><?};?></td>-->
					<td style="<?=$style;?>"><?if (is_file($signManagerFile)){?><img src="<?=$signManagerFile;?>" style="width:<?=$w4;?>px; height:<?=$h4;?>px;"><?};?></td>
				</tr>
				<tr>
					<td style="height:10px; border:none;"></td>
				</tr>
			</tbody>
		</table>

		<table style="width:100%;">
			<colgroup>
				<col width="100px">
				<col width="110px">
				<col width="130px">
				<col width="170px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td style="<?=$style;?>">수급자 성명</td>
					<td style="<?=$style;?>">장기요양등급</td>
					<td style="<?=$style;?>">장기요양인정번호</td>
					<td style="<?=$style;?>">방문(상담)일시</td>
					<td style="<?=$style;?>">방문자 성명(인)</td>
				</tr>
				<tr>
					<td style="<?=$style;?>"><?=$name[$tmpI];?></td>
					<td style="<?=$style;?>"><?=$level[$tmpI];?></td>
					<td style="<?=$style;?>"><?=$appNo[$tmpI];?></td>
					<td style="<?=$style;?>"><?=$myF->dateStyle($R['date'],'.')?> <?=$myF->timeStyle($R['time']);?> ~ <?=$myF->timeStyle($R['to_time']);?></td>
					<td style="<?=$style;?>" rowspan="2"><?if (is_file($visitFile)){?><img src="<?=$visitFile;?>" style="width:<?=$w3;?>px; height:<?=$h3;?>px;"><?};?></td>
				</tr>
				<tr>
					<td style="<?=$style;?>" colspan="4"><?=$addr[$tmpI];?></td>
				</tr>
				<tr>
					<td style="height:10px; border:none;"></td>
				</tr>
			</tbody>
		</table><?
	} ?>

	<table style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="60px" span="2">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<td style="<?=$style;?>" rowspan="12">욕구사정</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" rowspan="2" colspan="2">①신체상태</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">
					<?=$R['body_stat'] == '1' ? '●' : '○';?>완전자립
					<?=$R['body_stat'] == '2' ? '●' : '○';?>부분자립
					<?=$R['body_stat'] == '3' ? '●' : '○';?>전적인 도움
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['body_stat_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" rowspan="2" colspan="2">②질병</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">
					<div style="">질병명 : <?=$R['disease'];?> <?=$R['disease'] ? '약복용('.($R['medication'] == 'Y' ? '예' : '아니오').')' : '';?></div>
					<div style="">진단명 : <?=stripslashes($R['diagnosis']);?></div>
					<div style="float:left; width:95%;">장애명 : <?=stripslashes($R['disabled']).' '.$R['disabled_lvl'].($R['disabled_lvl'] ? '등급' : '');?></div>
					<div style="float:left; width:45%;">시력&nbsp;&nbsp;&nbsp;&nbsp;:
						<?=$R['eyesight'] == '1' ? '●' : '○';?>양호
						<?=$R['eyesight'] == '2' ? '●' : '○';?>보통
						<?=$R['eyesight'] == '3' ? '●' : '○';?>나쁨
					</div>
					<div style="float:left; width:45%;">청력 :
						<?=$R['hearing'] == '1' ? '●' : '○';?>양호
						<?=$R['hearing'] == '2' ? '●' : '○';?>보통
						<?=$R['hearing'] == '3' ? '●' : '○';?>나쁨
					</div>
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(stripslashes($R['disease_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" rowspan="2" colspan="2">③인지상태</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">
					<div>인지,기억력 :
						<?=$R['memory'] == '1' ? '●' : '○';?>명확
						<?=$R['memory'] == '2' ? '●' : '○';?>부분도움
						<?=$R['memory'] == '3' ? '●' : '○';?>불가능
					</div>
					<div>표현력&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
						<?=$R['express'] == '1' ? '●' : '○';?>명확
						<?=$R['express'] == '2' ? '●' : '○';?>부분도움
						<?=$R['express'] == '3' ? '●' : '○';?>불가능
					</div>
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['memory_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" rowspan="2" colspan="2">④의사소통</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">
					<div>정서적상태 :
						<?=$R['feel_stat'] == '1' ? '●' : '○';?>활발/적극
						<?=$R['feel_stat'] == '2' ? '●' : '○';?>조용/내성
						<?=$R['feel_stat'] == '3' ? '●' : '○';?>흥분/우울
					</div>
					<div>기타&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?=StripSlashes($R['comm_other']);?></div>
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['comm_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" rowspan="2" colspan="2">⑤영양상태</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">
					<div>식사형태 :
						<?=$R['meal_type'] == '1' ? '●' : '○';?>일반식
						<?=$R['meal_type'] == '2' ? '●' : '○';?>당뇨식
						<?=$R['meal_type'] == '3' ? '●' : '○';?>죽
						<?=$R['meal_type'] == '4' ? '●' : '○';?>경관급식
					</div>
					<div>수분섭취 :
						<?=$R['water_type'] == '1' ? '●' : '○';?>1일5컵이상
						<?=$R['water_type'] == '2' ? '●' : '○';?>1일2~4컵
						<?=$R['water_type'] == '3' ? '●' : '○';?>1일1~2컵
						<?=$R['water_type'] == '4' ? '●' : '○';?>1일1컵
						<?=$R['water_type'] == '9' ? '●' : '○';?>거의드시지않음
					</div>
					<div>섭취패턴 :
						<?=$R['intake_type'] == '1' ? '●' : '○';?>3식을 규칙적으로 먹는다.
						<?=$R['intake_type'] == '2' ? '●' : '○';?>평균 2식을 먹는다.
						<?=$R['intake_type'] == '3' ? '●' : '○';?>1식만 먹는다.
					</div>
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['nutrition_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">⑥가족 및 환경</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['env_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> padding-left:5px;" colspan="2">종합</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['total_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?>" rowspan="3">급여<br>제공<br>계획</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">급여목표</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['target_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">필요 급여내용</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['cont_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">제공방법</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['provide_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?>" rowspan="4">인지활동<br>프로그램<br>제공계획</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" rowspan="2">인지자극</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">필요내용</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['plan_rec_text']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">제공방법</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['plan_rec_way']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" rowspan="2">신체능력<br>잔존.유지</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">필요내용</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['plan_body_text']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">제공방법</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['plan_body_way']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> padding-left:5px;" colspan="3">보호자 상담</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['guard_text']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> padding-left:5px;" rowspan="7">급여 및<br>인지활동<br>프로그램<br>제공확인</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" rowspan="2" colspan="2">확인내용</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">
					<div style="float:left; width:49%;" >근무일지작성 :
						<?=$R['write_log_yn'] == 'Y' ? '●' : '○';?>예
						<?=$R['write_log_yn'] == 'N' ? '●' : '○';?>아니오
					</div>
					<div style="float:left; width:49%;">적절서비스&nbsp;&nbsp;&nbsp;&nbsp;:
						<?=$R['provide_chk_yn'] == 'Y' ? '●' : '○';?>예
						<?=$R['provide_chk_yn'] == 'N' ? '●' : '○';?>아니오
					</div>
					<div style="float:left; width:49%;">제공시간준수 :
						<?=$R['right_svc_yn'] == 'Y' ? '●' : '○';?>예
						<?=$R['right_svc_yn'] == 'N' ? '●' : '○';?>아니오
					</div>
					<div style="float:left; width:49%;">유니폼 착용&nbsp;&nbsp;&nbsp;:
						<?=$R['uniform_yn'] == 'Y' ? '●' : '○';?>예
						<?=$R['uniform_yn'] == 'N' ? '●' : '○';?>아니오
					</div>
					<div style="float:left; width:49%;">주거환경청결 :
						<?=$R['house_env_yn'] == '1' ? '●' : '○';?>상
						<?=$R['house_env_yn'] == '2' ? '●' : '○';?>중
						<?=$R['house_env_yn'] == '3' ? '●' : '○';?>하
					</div>
					<div style="float:left; width:49%;">업무태도친절 :
						<?=$R['work_mind_yn'] == '1' ? '●' : '○';?>상
						<?=$R['work_mind_yn'] == '2' ? '●' : '○';?>중
						<?=$R['work_mind_yn'] == '3' ? '●' : '○';?>하
					</div>
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['check_note']));?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">조치사항</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=nl2br(StripSlashes($R['action_note']));?></td>
			</tr>
			<tr><?
				if($yymm < '201701'){ ?>
					<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">*요양보호사)</td><?
				}else { ?>
					<?
					if($mode == '1'){ ?>
						<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">*급여제공자 성명</td>
						<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=$memStr[$tmpI];?></td><?
					}else { ?>
						<td style="<?=$style;?> text-align:left; padding-left:5px; height:35px;" colspan="2">*급여제공자 성명(인)</td>
						<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=$memStr[$tmpI];?><?if (is_file($yoyFile)){?><img src="<?=$yoyFile;?>" style="width:<?=($w2/2);?>px; height:<?=($h2/2);?>px;"><?};?></td><?
					}
				}
				?>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">*방문장소</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?=StripSlashes($R['visit_place']);?></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">*급여제공 중 방문여부</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;">
					<span class="left"><?
						switch($R['svcporc_yn']){
							case 'Y':
								echo '예';
								break;

							case 'N':
								echo '아니오';
								break;
						}?>
					</span>
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:left; padding-left:5px;" colspan="2">*방문불가사유</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px;"><?
					$str = '';

					if (!$R['notvisit_cd'] && $R['notvisit_reason']) $R['notvisit_cd'] = '9';
					if ($R['notvisit_cd']){
						if ($R['notvisit_cd'] == '1'){
							$str = '사망';
						}else if ($R['notvisit_cd'] == '2'){
							$str = '병원';
						}else if ($R['notvisit_cd'] == '3'){
							$str = '해지';
						}else if ($R['notvisit_cd'] == '9'){
							$str = '기타';
						}

						if ($str) $str .= ' - ';
					}?>
					<div class="left"><?=$str.StripSlashes($R['notvisit_reason']);?></div>
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?>">총평</td>
				<td style="<?=$style;?> text-align:left; padding-left:5px; height:60px;" colspan="3"><?=nl2br(StripSlashes($R['comment']));?></td>
			</tr>
		</tbody>
	</table>
	</div><?
}

Unset($R);
include_once('../inc/_footer.php');
?>




