<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_function.php');
	require_once('../pdf/korean.php');
	require_once('../pdf/pdf_work_table.php');
	

	$conn->set_name('euckr');

	define('__ROW_LIMIT__',14);
	
	$code = $_SESSION['userCenterCode'];

	//기관로고
	$sql = 'select m00_icon
			  from m00center
			 where m00_mcode = \''.$code.'\'';
	$icon = $conn -> get_data($sql);
	
	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$code'";
	$centerName = $conn -> get_data($sql);

	$pdf = new MYPDF(strtoupper('l'));
	$pdf->AliasNbPages();
	$pdf->debug = $debug;
	$pdf->acctBox = true;
	$pdf->font_name_kor = '바탕';
	$pdf->font_name_eng = 'Batang';
	$pdf->AddUHCFont('바탕','Batang');
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);
	
	$code = $_POST['code'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$wrt_mode = $_POST['wrt_mode'];
	$mode    = $_POST['mode'];

	$pdf->centerName    = $centerName;		//기관명
	$pdf->year			= $year;			//년
	$pdf->month			= $month;			//월

	$svcCode = $_POST['svcCode'];

	if ($svcCode == ''){
		$svcCode = 'ALL';
	}

	$sql = "select t01_mkind as kind
			,      t01_svc_subcode as svc_cd
			,      t01_yoyangsa_id1 as mem_cd1
			,      t01_yname1 as mem_nm1
			,      t01_yname2 as mem_nm2
			,      t01_jumin as client_cd
			,      cast(date_format(t01_sugup_date, '%d') as unsigned) as dt
			,      TIMEDIFF(DATE_FORMAT(CONCAT(CASE WHEN t01_sugup_totime > t01_sugup_fmtime THEN t01_sugup_date ELSE REPLACE(ADDDATE(DATE_FORMAT(t01_sugup_date,'%Y-%m-%d'),interval 1 day),'-','') END, t01_sugup_totime,'00'),'%Y-%m-%d %H:%i:%s')
						   ,DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_fmtime,'00'),'%Y-%m-%d %H:%i:%s')) as plan_time
			,      t01_sugup_fmtime as plan_from_time
			,      t01_sugup_totime as plan_to_time
			,      TIMEDIFF(DATE_FORMAT(CONCAT(CASE WHEN t01_conf_totime > t01_conf_fmtime THEN t01_sugup_date ELSE REPLACE(ADDDATE(DATE_FORMAT(t01_sugup_date,'%Y-%m-%d'),interval 1 day),'-','') END, t01_conf_totime,'00'),'%Y-%m-%d %H:%i:%s')
						   ,DATE_FORMAT(CONCAT(t01_sugup_date, t01_conf_fmtime,'00'),'%Y-%m-%d %H:%i:%s')) as conf_time
			,	   CASE WHEN t01_svc_subcode = '200' AND t01_bipay_umu != 'Y' THEN
							 t01_conf_soyotime - CASE WHEN t01_conf_soyotime >= 270 THEN 30 ELSE 0 END
						ELSE t01_conf_soyotime END as conf_soyotime
			,      t01_conf_fmtime as conf_from_time
			,      t01_conf_totime as conf_to_time
			,      t01_status_gbn as stat
			,      'm' as ms_gbn
			,      case when t01_bipay_umu = 'Y' then 'Y' else 'N' end as bipay_yn
			  from t01iljung
			 where t01_ccode  = '$code'
			   and t01_del_yn = 'N'
			   and left(t01_sugup_date,6) = '$year$month'";

	if ($svcCode != 'ALL'){
		$sql .= " AND t01_mkind = '".$svcCode."'";
	}

	$sql .= " union all
			select t01_mkind as kind
			,      t01_svc_subcode as svc_cd
			,      t01_yoyangsa_id2 as mem_cd1
			,      t01_yname2 as mem_nm1
			,      t01_yname1 as mem_nm2
			,      t01_jumin as client_cd
			,      cast(date_format(t01_sugup_date, '%d') as unsigned) as dt
			,      TIMEDIFF(DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_totime,'00'),'%Y-%m-%d %H:%i:%s')
						   ,DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_fmtime,'00'),'%Y-%m-%d %H:%i:%s')) as plan_time
			,      t01_sugup_fmtime as plan_from_time
			,      t01_sugup_totime as plan_to_time
			,      TIMEDIFF(DATE_FORMAT(CONCAT(t01_sugup_date, t01_conf_totime,'00'),'%Y-%m-%d %H:%i:%s')
						   ,DATE_FORMAT(CONCAT(t01_sugup_date, t01_conf_fmtime,'00'),'%Y-%m-%d %H:%i:%s')) as conf_time
			,	   CASE WHEN t01_svc_subcode = '200' AND t01_bipay_umu != 'Y' THEN
							 t01_conf_soyotime - CASE WHEN t01_conf_soyotime >= 270 THEN 30 ELSE 0 END
						ELSE t01_conf_soyotime END as conf_soyotime
			,      t01_conf_fmtime as conf_from_time
			,      t01_conf_totime as conf_to_time
			,      t01_status_gbn as stat
			,      's' as ms_gbn
			,      case when t01_bipay_umu = 'Y' then 'Y' else 'N' end as bipay_yn
			  from t01iljung
			 where t01_ccode         = '$code'
			   and t01_del_yn        = 'N'
			   and t01_yoyangsa_id2 != ''
			   and t01_svc_subcode = '500'
			   and left(t01_sugup_date,6) = '$year$month'";

	if ($svcCode != 'ALL'){
		$sql .= " AND t01_mkind = '".$svcCode."'";
	}

	$sql = "SELECT kind
			,      svc_cd
			,      mem_cd1
			,      mem_nm1
			,      mem_nm2
			,      client_cd
			,      client_nm
			,      dt
			,      CASE WHEN kind = '0' AND svc_cd = '200' AND bipay_yn != 'Y' THEN
								plan_time - CASE WHEN plan_time >= 540 THEN 60
												 WHEN plan_time >= 270 THEN 30 ELSE 0 END
								ELSE plan_time END AS plan_time
			,      plan_from_time
			,      plan_to_time
			,      CASE WHEN kind = '0' AND svc_cd = '200' AND bipay_yn != 'Y' THEN
							 conf_time - CASE WHEN conf_time >= 540 THEN 60
											  WHEN conf_time >= 270 THEN 30 ELSE 0 END
						ELSE conf_time END AS conf_time
			,	   CASE WHEN kind = '0' THEN conf_soyotime ELSE conf_time END AS conf_soyotime
			,      conf_from_time
			,      conf_to_time
			,      stat
			,      ms_gbn
			,      bipay_yn
			  FROM (
				   SELECT kind
				   ,      svc_cd
				   ,      mem_cd1
				   ,      mem_nm1
				   ,      mem_nm2
				   ,      client_cd
				   ,      m03_name AS client_nm
				   ,      dt
				   ,      HOUR(plan_time) * 60 + MINUTE(plan_time) as plan_time
				   ,      plan_from_time
				   ,      plan_to_time
				   ,      CASE WHEN kind = '0' AND svc_cd = '200' THEN HOUR(conf_time) * 60 + MINUTE(conf_time) - ((HOUR(conf_time) * 60 + MINUTE(conf_time)) % 30) ELSE HOUR(conf_time) * 60 + MINUTE(conf_time) END as conf_time
				   ,	   conf_soyotime
				   ,      conf_from_time
				   ,      conf_to_time
				   ,      stat
				   ,      ms_gbn
				   ,      bipay_yn
					 FROM (".$sql.") as t
					INNER JOIN m03sugupja
					   ON m03_ccode = '$code'
					  AND m03_mkind = kind
					  AND m03_jumin = client_cd
					WHERE mem_cd1 != ''
				   ) AS t";

	if ($mode == 1){
		$sql .= " order by mem_nm1, client_nm, plan_from_time, plan_to_time, dt";
	}else{
		$sql .= " order by mem_nm1, mem_cd1, kind, svc_cd, case when bipay_yn != 'Y' then 1 else 2 end, dt, plan_from_time";
	}

	//if ($debug) echo nl2br($sql);

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$new_m = false;
		$new_c = false;
		$new_w = false;
		
		if (!isset($m)){
			$m = 0;
			$c = 0;
			$w = 0;
			$new_m = true;
			$new_c = true;
			$new_w = true;
		}else{
			if ($mst['member'][$m]['cd'] != $row['mem_cd1']){
				$m ++;
				$c = 0;
				$w = 0;
				$new_m = true;
				$new_c = true;
				$new_w = true;
			}else if ($mode == 1 && $mst[$m]['client'][$c]['cd'] != $row['client_cd']){
				$c ++;
				$w = 0;
				$new_c = true;
				$new_w = true;
			}else if (($mode == 1 && $mst[$m][$c]['iljung']['plan'][$w]['from_time'] != $row['plan_from_time']) ||
					  ($mode == 1 && $mst[$m][$c]['iljung']['plan'][$w]['to_time'] != $row['plan_to_time']) ||
					  ($mode == 1 && $mst[$m][$c]['iljung']['svc_cd'][$w] != $row['svc_cd'])){
				$w ++;
				$new_w = true;
			}else if (($mode == 2 && $mst[$m][$c]['iljung']['kind_cd'][$w] != $row['kind']) ||
					  ($mode == 2 && $mst[$m][$c]['iljung']['svc_cd'][$w] != $row['svc_cd']) ||
					  ($mode == 2 && $mst[$m][$c]['iljung']['bipay'][$w] != $row['bipay_yn']) ){
				$w ++;
				$new_w = true;
			}
		}

		if ($mode != 1) $new_c = false;
		
		if(empty($row['plan_time'])){
			$plan_fromTime = mktime(substr($row['plan_from_time'],0,2),substr($row['plan_from_time'],2,2),00,$month,$year,$row['dt']);
			$plan_toTime =mktime(substr($row['plan_to_time'],0,2),substr($row['plan_to_time'],2,2),00,$month,$year,$row['dt']);

			$row['plan_time'] = (($plan_toTime-$plan_fromTime)/60);
		}

		if(empty($row['conf_time'])){
			@$conf_fromTime = mktime(substr($row['conf_from_time'],0,2),substr($row['conf_from_time'],2,2),00,$month,$year,$row['dt']);
			@$conf_toTime = mktime(substr($row['conf_to_time'],0,2),substr($row['conf_to_time'],2,2),00,$month,$year,$row['dt']);

			$row['conf_time']	  = (($conf_toTime-$conf_fromTime)/60);
			$row['conf_soyotime'] = (($conf_toTime-$conf_fromTime)/60);
		}
		
		if ($row['kind'] == '0'){
			if ($row['svc_cd'] == '200'){
				$row['conf_soyotime']	= $myF->cutOff($row['conf_soyotime'],30);
			}else if ($row['svc_cd'] == '500'){
				if ($row['conf_soyotime'] >= 60){
					$row['conf_soyotime']	= 60;
				}else if ($row['conf_soyotime'] >= 40){
					$row['conf_soyotime']	= 40;
				}
			}
		}else if ($row['kind'] == '4'){
			if($row['conf_soyotime'] > 480){
				$row['conf_soyotime'] = 480;
			}
		}

		

		if ($new_m){
			$mst['member'][$m] = array('cd'=>$row['mem_cd1'], 'nm'=>$row['mem_nm1'], 'row'=>0);
		}

		if ($new_c){
			$mst[$m]['client'][$c] = array('cd'=>$row['client_cd'], 'nm'=>$row['client_nm'], 'row'=>0);
		}

		if ($new_w){
			$mst[$m][$c]['iljung']['kind_cd'][$w] = $row['kind'];
			$mst[$m][$c]['iljung']['svc_cd'][$w]  = $row['svc_cd'];
			$mst[$m][$c]['iljung']['bipay'][$w]   = $row['bipay_yn'];

			if(!empty($row['mem_nm2']) or !empty($row['mem_nm2'])){
				$space = "";
			}else {
				$space = "\n";
			}

			if($wrt_mode == 1){
				if ($row['kind'] == '0'){
					$mst[$m][$c]['iljung']['kind'][$w] = iconv("UTF-8","EUC-KR", $conn->kind_name_sub($conn->kind_name_svc($row['kind'])).$space."[".$conn->kind_name_sub($conn->kind_name_svc($row['svc_cd'])).']');
				}else if ($row['kind'] >= '1' && $row['kind'] <= '4'){
					if ($row['kind'] == '4'){
						$mst[$m][$c]['iljung']['kind'][$w] = '장애'.$space.'['.iconv("UTF-8","EUC-KR", $conn->kind_name_sub($conn->kind_name_svc($row['svc_cd']))).']';
					}else{
						$mst[$m][$c]['iljung']['kind'][$w] = '바우처'.$space.'['.iconv("UTF-8","EUC-KR", $conn->kind_name_sub($conn->kind_name_svc($row['kind']))).']';
					}
				}else{
					$mst[$m][$c]['iljung']['kind'][$w] = "기타".$space."[".iconv("UTF-8","EUC-KR", $conn->kind_name_sub($conn->kind_name_svc($row['kind'])))."]";
				}

				if ($mst[$m][$c]['iljung']['bipay'][$w] == 'Y')
					$mst[$m][$c]['iljung']['kind'][$w] .= '[비]';
			}else {
				if ($row['kind'] == '0'){
					$mst[$m][$c]['iljung']['kind'][$w] = $conn->kind_name_sub($conn->kind_name_svc($row['kind'])).$space.'['.$conn->kind_name_sub($conn->kind_name_svc($row['svc_cd'])).']';
				}else if ($row['kind'] >= '1' && $row['kind'] <= '4'){
					if ($row['kind'] == '4'){
						$mst[$m][$c]['iljung']['kind'][$w] = '장애'.$space.'['.iconv("UTF-8","EUC-KR", $conn->kind_name_sub($conn->kind_name_svc($row['svc_cd']))).']';
					}else{
						$mst[$m][$c]['iljung']['kind'][$w] = '바우처'.$space.'['.iconv("UTF-8","EUC-KR", $conn->kind_name_sub($conn->kind_name_svc($row['kind']))).']';
					}
				}else{
					$mst[$m][$c]['iljung']['kind'][$w] = "기타".$space."[".iconv("UTF-8","EUC-KR", $conn->kind_name_sub($conn->kind_name_svc($row['kind'])))."]";
				}

				if ($mst[$m][$c]['iljung']['bipay'][$w] == 'Y'){
					$mst[$m][$c]['iljung']['kind'][$w] .= '[비]';
				}
			}



			/*********************************************************
				2명이 서비스한 경우 정/부를 표시한다.
			*********************************************************/

			if ($mode == 1 && !empty($row['mem_nm2'])){
				$mst[$m][$c]['iljung']['kind'][$w] .= "\n".($row['ms_gbn'] == "m" ? "부" : "정").' : '.$row['mem_nm2'];
			}

			$mst[$m][$c]['iljung']['plan'][$w] = array('proc_time'=>$row['plan_time'], 'from_time'=>$row['plan_from_time'], 'to_time'=>$row['plan_to_time'], 'work_dt'=>'', 'work_cnt'=>0, 'work_time'=>0);
			$mst[$m][$c]['iljung']['conf'][$w] = array('proc_time'=>$row['conf_time'], 'from_time'=>$row['conf_from_time'], 'to_time'=>$row['conf_to_time'], 'work_dt'=>'', 'work_cnt'=>0, 'work_time'=>0);

			$mst['member'][$m]['row']     += ($mode == 1 ? 2 : 1);
			$mst[$m]['client'][$c]['row'] += ($mode == 1 ? 2 : 1);

			for($j=1; $j<=31; $j++){
				$mst[$m][$c]['iljung']['plan'][$w][$j] = 0;
				$mst[$m][$c]['iljung']['conf'][$w][$j] = 0;
			}
		}

		if (!is_numeric(strpos($mst[$m][$c]['iljung']['plan'][$w]['work_dt'], '/'.$row['dt']))){
			$mst[$m][$c]['iljung']['plan'][$w]['work_cnt'] ++;
			$mst[$m][$c]['iljung']['plan'][$w]['work_dt'] .= '/'.$row['dt'];
		}

		if($row['kind'] == 0){
			$mst[$m][$c]['iljung']['plan'][$w][$row['dt']] = $row['plan_time'];
			$mst[$m][$c]['iljung']['plan'][$w]['work_time'] += $row['plan_time'];
		}else {				
			
			
			if (($row['kind'] == 1 && $year.$month >= '201402') || ($row['kind'] == 2 && $year.$month >= '201502')){
				$tmpTime = $row['plan_time'] % 60;

				if ($tmpTime >= 45){
					$planTime = $myF->cutOff($row['plan_time'],60)+60;
				}else if ($tmpTime >= 15 && $tmpTime < 45){
					$planTime = $myF->cutOff($row['plan_time'],60)+30;
				}else{
					$planTime = $myF->cutOff($row['plan_time'],60);
				}
			}else if(($row['kind'] == 4 &&  $year.$month >= '201701')){
				$tmpTime = $row['plan_time'] % 60;

				if ($tmpTime >= 45){
					$planTime = $myF->cutOff($row['plan_time'],60)+60;
				}else if ($tmpTime >= 15 && $tmpTime < 45){
					$planTime = $myF->cutOff($row['plan_time'],60)+30;
				}else{
					$planTime = $myF->cutOff($row['plan_time'],60);
				}
			}else{
				$planTime = round(round($row['plan_time'] / 60));
				$planTime = ($planTime*60);
			}

			$mst[$m][$c]['iljung']['plan'][$w][$row['dt']] = $planTime;
			$mst[$m][$c]['iljung']['plan'][$w]['work_time'] += $row['plan_time'];

			
		}

		if ($row['stat'] == '1'){
			if (!is_numeric(strpos($mst[$m][$c]['iljung']['conf'][$w]['work_dt'], '/'.$row['dt']))){
				$mst[$m][$c]['iljung']['conf'][$w]['work_cnt'] ++;
				$mst[$m][$c]['iljung']['conf'][$w]['work_dt'] .= '/'.$row['dt'];
			}
			
			if($row['kind'] == 0){
				if ($mode == 1){
					$mst[$m][$c]['iljung']['conf'][$w][$row['dt']] = $row['conf_time'];
				}else {
					$mst[$m][$c]['iljung']['conf'][$w][$row['dt']] += $row['conf_time'];
				}

				$mst[$m][$c]['iljung']['conf'][$w]['work_time'] += $row['conf_time'];
			}else {	
				if (($row['kind'] == 1 && $year.$month >= '201402') || ($row['kind'] == 2 && $year.$month >= '201502')){
					$tmpTime = $row['conf_soyotime'] % 60;

					/*
					if ($tmpTime >= 45){
						$confTime = $myF->cutOff($row['conf_soyotime']+(60-$tmpTime),30);
					}else{
						$confTime = $myF->cutOff($row['conf_soyotime'],30);
					}
					 */

					if ($tmpTime >= 45){
						$confTime = $myF->cutOff($row['conf_soyotime'],60)+60;
					}else if ($tmpTime >= 15 && $tmpTime < 45){
						$confTime = $myF->cutOff($row['conf_soyotime'],60)+30;
					}else{
						$confTime = $myF->cutOff($row['conf_soyotime'],60);
					}
				}else if(($row['kind'] == 4 && $year.$month >= '201701')){
					$tmpTime = $row['conf_soyotime'] % 60;
					 
					if ($tmpTime >= 45){
						$confTime = $myF->cutOff($row['conf_soyotime'],60)+60;
					}else if ($tmpTime >= 15 && $tmpTime < 45){
						$confTime = $myF->cutOff($row['conf_soyotime'],60)+30;
					}else{
						$confTime = $myF->cutOff($row['conf_soyotime'],60);
					}

					
				}else{
					$confTime = round(round($row['conf_soyotime'] / 60));
					$confTime = ($confTime*60);
				}
				
				$mst[$m][$c]['iljung']['conf'][$w][$row['dt']]  = $confTime;
				$mst[$m][$c]['iljung']['conf'][$w]['work_time'] += $confTime;

				

			}
		}
	}

	$conn->row_free();

	$cnt_m   = sizeof($mst['member']);
	$pdf->AddPage();


	//$pdf->SetFont('바탕','',12);
	$pdf->SetLineWidth(0.2);
	$pdf->Rect($pdf->width-$pdf->left+15.5, $pdf->top, $pdf->width*0.05, $pdf->rowHeight * 3);
	$pdf->Rect($pdf->width-($pdf->left+$pdf->width-272), $pdf->top, $pdf->width*0.05, $pdf->rowHeight * 3);
	$pdf->Rect($pdf->width-($pdf->left+$pdf->width-258.5), $pdf->top, $pdf->width*0.05, $pdf->rowHeight * 3);
	$pdf->Rect($pdf->width-($pdf->left+$pdf->width-245), $pdf->top, $pdf->width*0.05, $pdf->rowHeight * 3);

	$pdf->Line($pdf->width-($pdf->left+$pdf->width-258.5), $pdf->top + $pdf->rowHeight * 1, $pdf->width+15, $pdf->top + $pdf->rowHeight * 1);


	$pdf->SetFont($pdf->font_name_kor, "", 10);
	$pdf->Text($pdf->width-($pdf->left+$pdf->width*0.12)+(($pdf->width*0.1 - $pdf->GetStringWidth("결")) / 2), $pdf->top+4.5, "결");
	$pdf->Text($pdf->width-($pdf->left+$pdf->width*0.12)+(($pdf->width*0.1 - $pdf->GetStringWidth("결")) / 2), $pdf->top+12, "재");

	$pdf->SetFont($pdf->font_name_kor, "", 9);
	$pdf->Text($pdf->width-($pdf->left+$pdf->width*0.067)+(($pdf->width*0.1 - $pdf->GetStringWidth("담  당")) / 2), $pdf->top+4, "담  당");
	$pdf->Text($pdf->width-$pdf->left+(($pdf->width*0.165 - $pdf->GetStringWidth("기관장")) / 2), $pdf->top+4, "기관장");

	$pdf->SetFont($pdf->font_name_kor, "", 7);
	$pdf->SetXY($pdf->left, $pdf->GetY());

	$row_cnt = 0;
	$c_h = 30;

	for($m=0; $m<$cnt_m; $m++){

		$cnt_c = sizeof($mst[$m]['client']);

		$headCol = $pdf->headColWidth();

		//$m_height = $pdf->rowHeight * $mst['member'][$m]['row'];


		if($pdf->getY()>$pdf->height){
			if($icon != ''){
				$exp = explode('.',$icon);
				$exp = strtolower($exp[sizeof($exp)-1]);
				if($exp != 'bmp'){
					$pdf->Image('../mem_picture/'.$icon, 265, 190, 20, null);	//기관 로고
				}
			}
			
			$pdf->AddPage();
		}

		/*
		if($pdf->getY()>144){
			$pdf->line($pdf->left+$headCol['w'][0]+$headCol['w'][1], ($pdf->height+4), $pdf->left+$headCol['w'][0]+$headCol['w'][1]+$headCol['w'][2], ($pdf->height+4));
		}
		*/

		$get_Y = $pdf->GetY();

		$pdf->SetXY($pdf->left, $get_Y+2);
		$pdf->MultiCell($headCol['w'][0], 3.5, number_format(($m+1)), 0 , 'C');

		//요양사 첫 라인
		if($m == 0){
			$pdf->line($pdf->left, $get_Y, $pdf->left, $get_Y+10);
			$pdf->line($pdf->left+$headCol['w'][0], $get_Y, $pdf->left+$headCol['w'][0], $get_Y+10);
		}


		//요양사명/주민번호
		$pdf->SetXY($pdf->left+$headCol['w'][0], $get_Y+1.5);
		$pdf->MultiCell($headCol['w'][1], 3.5, $mst['member'][$m]['nm']."\n".substr($myF->issStyle($mst['member'][$m]['cd']),0,8)."", 0 , 'C');

		$pdf->SetY($get_Y);

		for($c=0; $c<$cnt_c; $c++){
			//$c_height = $pdf->rowHeight * $mst[$m]['client'][$c]['row'];

			$c_nm = '';
			$cnt_w = sizeof($mst[$m][$c]['iljung']['plan']);

			if($pdf->getY()>$pdf->height){
				if($icon != ''){
					$exp = explode('.',$icon);
					$exp = strtolower($exp[sizeof($exp)-1]);
					if($exp != 'bmp'){
						$pdf->Image('../mem_picture/'.$icon, 265, 190, 20, null);	//기관 로고
					}
				}
				
				$pdf->AddPage();
			}

			//수급자 첫행이아닌경우 위치정렬
			if($c == 0){
				$pdf->line($pdf->left+$headCol['w'][0]+$headCol['w'][1], $get_Y, $pdf->left+$headCol['w'][0]+$headCol['w'][1], $get_Y+10);
			}



			if($c != 0){
				$pdf -> SetX($pdf->left+$headCol['w'][0]+$headCol['w'][1]);
			}

			if($mst[$m]['client'][$c]['nm'] != ''){
				$pdf->line($pdf->left+$headCol['w'][0]+$headCol['w'][1],  $pdf->GetY(), $pdf->left+$headCol['w'][0]+$headCol['w'][1]+$headCol['w'][2], $pdf->GetY());
			}
			$pdf->text($pdf->left+$headCol['w'][0]+$headCol['w'][1]+0.6, $pdf->GetY()+6, $mst[$m]['client'][$c]['nm']);

			for($w=0; $w<$cnt_w; $w++){

				$td = 'LR';

				$row_cnt++;

				//마지막줄 라인그리기
				if(($row_cnt+$m) % __ROW_LIMIT__ == 0){
					$td = 'LRB';
					$pdf->line($pdf->left+$headCol['w'][0]+$headCol['w'][1], ($pdf->height+4), $pdf->left+$headCol['w'][0]+$headCol['w'][1]+$headCol['w'][2], ($pdf->height+4));
				}

				if($temp != $mst['member'][$m]['nm'].'_'.$mst['member'][$m]['cd']){
					$temp = $mst['member'][$m]['nm'].'_'.$mst['member'][$m]['cd'];

					$m_nm[$row_cnt] = $mst['member'][$m]['nm'];
					$m_cd[$row_cnt] = $mst['member'][$m]['cd'];

				}else {

					$m_nm[$row_cnt] = '';
					$m_cd[$row_cnt] = '';
				}


				if($temp2 != $mst[$m]['client'][$c]['nm']){
					$temp2 = $mst[$m]['client'][$c]['nm'];

					$c_nm[$row_cnt] = $mst[$m]['client'][$c]['nm'];

				}else {
					$c_nm[$row_cnt] = '';
				}


				if($pdf->getY()>$pdf->height){
					if($icon != ''){
						$exp = explode('.',$icon);
						$exp = strtolower($exp[sizeof($exp)-1]);
						if($exp != 'bmp'){
							$pdf->Image('../mem_picture/'.$icon, 265, 190, 20, null);	//기관 로고
						}
					}
					

					$pdf->AddPage();
				}

				$get_Y2 = $pdf->getY();

				if(($row_cnt+$m) % __ROW_LIMIT__ == 1 && $m_nm[$row_cnt] == ''){
					//$pdf->SetXY($pdf->left, $get_Y2+2);
					//$pdf->MultiCell($headCol['w'][0], 3.5, number_format(($m+1)), 0 , 'C');
					$pdf->SetXY($pdf->left+$headCol['w'][0], $get_Y2+1.5);
					$pdf->MultiCell($headCol['w'][1], 3.5, $mst['member'][$m]['nm']."\n".substr($myF->issStyle($mst['member'][$m]['cd']),0,8)."", 0 , 'C');
				}

				if(($row_cnt+$m) % __ROW_LIMIT__ == 1 && $c_nm[$row_cnt] == ''){
					$pdf->text($pdf->left+$headCol['w'][0]+$headCol['w'][1]+0.6, $pdf->GetY()-3, $mst[$m]['client'][$c]['nm']);
				}

				//$c_nm = $mst[$m]['client'][$c]['nm'];

				$pdf->SetXY($pdf->left, $get_Y2);
				$pdf->Cell($headCol['w'][0], $pdf->rowHeight*$rowspan, '',$td,0,'C');
				$pdf->Cell($headCol['w'][1], $pdf->rowHeight*$rowspan, '',$td,0,'C');

				$pdf->Cell($headCol['w'][2], $pdf->rowHeight*2, '',$td2,0,'C');

				$proc_time = $mst[$m][$c]['iljung']['plan'][$w]['proc_time'];	//평균시간
				$work_time = $mst[$m][$c]['iljung']['plan'][$w]['work_time'];	//근무시간
				$work_cnt  = $mst[$m][$c]['iljung']['plan'][$w]['work_cnt'];	//근무일수
				$from_time = $mst[$m][$c]['iljung']['plan'][$w]['from_time'];	//업무시작시간
				$to_time   = $mst[$m][$c]['iljung']['plan'][$w]['to_time'];		//업무종료시간
				$svc_nm    = $mst[$m][$c]['iljung']['kind'][$w];
				
				

				if (!empty($proc_time)) $proc_time = number_format(round($proc_time / 60, 1), 1); else $proc_time = '';
				if (!empty($work_time)) $work_time = number_format(round($work_time / 60, 1), 1); else $work_time = '';
				if (!empty($from_time)) $from_time = $from_time = substr($from_time, 0, 2).':'.substr($from_time, 2); else $from_time = '';
				if (!empty($to_time))   $to_time   = substr($to_time, 0, 2).':'.substr($to_time, 2); else $to_time = '';
				if (!empty($from_time)) $from_to_time = $from_time.' ~ '.$to_time; else $from_to_time = '';
				if (empty($work_cnt))   $work_cnt = '';
				if (!empty($svc_nm)) $rowspan = ($mode == 1 ? 2 : 1);

				//서비스 첫행이아닌경우 위치정렬
				if($w != 0){
					$pdf -> SetX($pdf->left+$headCol['w'][0]+$headCol['w'][1]+$headCol['w'][2]);
				}


				if ($mode == 1){

					$get_Y = $pdf->GetY();


					//서비스명
					$pdf->SetXY($pdf->left+$headCol['w'][0]+$headCol['w'][1]+$headCol['w'][2], $pdf->GetY()+1.5);
					$pdf->MultiCell($headCol['w'][3], 3.5, $svc_nm, 0 , 'C');

					$pdf->SetXY($pdf->left+$headCol['w'][0]+$headCol['w'][1]+$headCol['w'][2], $get_Y);

					$pdf->Cell($headCol['w'][3], $pdf->rowHeight*$rowspan,'' ,1,0,'C');
					$pdf->Cell($headCol['w'][4], $pdf->rowHeight, '계획',1,0,'C');
					$pdf->Cell($headCol['w'][5], $pdf->rowHeight, $proc_time,1,0,'C');
					$pdf->Cell($headCol['w'][6], $pdf->rowHeight, $from_to_time,1,0,'C');
					$pdf->Cell($headCol['w'][7], $pdf->rowHeight, "",1,0,'C');
					$pdf->Cell($headCol['w'][8], $pdf->rowHeight, $work_cnt."",1,0,'C');
					$pdf->Cell($headCol['w'][9], $pdf->rowHeight, $work_time,1,0,'C');


					for($i=1; $i<=31; $i++){
						$time  = $mst[$m][$c]['iljung']['plan'][$w][$i];
						$time  = !empty($time) ? number_format(round($time / 60, 1), 1) : '';

						$pdf->Cell($headCol['w'][($i+9)], $pdf->rowHeight, $time,1, $i == sizeOf($headCol[t]) - 1 ? 1 : ($i==31 ? 1 : 0),'C');
					}
				}


				//$pdf->Cell($headCol['w'][40], $pdf->rowHeight, '',1,1,'C');
				
				$proc_time = $mst[$m][$c]['iljung']['conf'][$w]['proc_time'];	//평균시간
				$work_time = $mst[$m][$c]['iljung']['conf'][$w]['work_time'];	//근무시간
				$work_cnt = $mst[$m][$c]['iljung']['conf'][$w]['work_cnt'];		//근무일수
				$from_time = $mst[$m][$c]['iljung']['conf'][$w]['from_time'];	//업무시작시간
				$to_time = $mst[$m][$c]['iljung']['conf'][$w]['to_time'];		//업무종료시간
				
				if (!empty($proc_time)) $proc_time = number_format(round($proc_time / 60, 1), 1); else $proc_time = '';
				if (!empty($work_time)) $work_time = number_format(round($work_time / 60, 1), 1); else $work_time = '';
				if (!empty($from_time)) $from_time = $from_time = substr($from_time, 0, 2).':'.substr($from_time, 2); else $from_time = '';
				if (!empty($to_time))   $to_time   = substr($to_time, 0, 2).':'.substr($to_time, 2); else $to_time = '';
				if (!empty($from_time)) $from_to_time = $from_time.' ~ '.$to_time; else $from_to_time = '';
				if (empty($work_cnt))   $work_cnt = '';


				$pdf->SetXY($pdf->left+$headCol['w'][0]+$headCol['w'][1]+$headCol['w'][2]+$headCol['w'][3], $pdf->GetY());
				$pdf->Cell($headCol['w'][4], $pdf->rowHeight, '실적',1,0,'C');
				$pdf->Cell($headCol['w'][5], $pdf->rowHeight, $proc_time,1,0,'C');
				$pdf->Cell($headCol['w'][6], $pdf->rowHeight, $from_to_time,1,0,'C');
				$pdf->Cell($headCol['w'][7], $pdf->rowHeight, "",1,0,'C');
				$pdf->Cell($headCol['w'][8], $pdf->rowHeight, $work_cnt."",1,0,'C');
				$pdf->Cell($headCol['w'][9], $pdf->rowHeight, $work_time,1,0,'C');

				for($i=1; $i<=31; $i++){
					$time  = $mst[$m][$c]['iljung']['conf'][$w][$i];
					$time  = !empty($time) ? number_format(round($time / 60, 1), 1) : '';

					$pdf->Cell($headCol['w'][($i+9)], $pdf->rowHeight, $time,1, $i == sizeOf($headCol[t]) - 1 ? 1 : ($i==31 ? 1 : 0),'C');
				}

				//$pdf->Cell($headCol['w'][40], $pdf->rowHeight, '',1,1,'C');


				if ($mode == 1){
					//계획 소계
					if (!is_numeric($mst[$m][$c]['iljung']['plan'][$w]['work_cnt'])){
						$arrDT = explode('/',$mst[$m][$c]['iljung']['plan'][$w]['work_cnt']);

						foreach($arrDT as $i => $dt){
							if (!is_numeric(strpos($subsum['plan']['work_dt'], '/'.$dt))){
								if (!empty($dt)){
									$subsum['work_dt'] .= '/'.$dt;
									$subsum['work_cnt'] ++;
								}
							}
						}
					}else{
						$subsum['plan']['work_cnt']  += $mst[$m][$c]['iljung']['plan'][$w]['work_cnt'];
						$subsum['plan']['work_time'] += $mst[$m][$c]['iljung']['plan'][$w]['work_time'];
					}

					for($i=1; $i<=31; $i++){
						$subsum['plan']['iljung'][$i] += $mst[$m][$c]['iljung']['plan'][$w][$i];
					}

					//실적 소계
					if (!is_numeric($mst[$m][$c]['iljung']['conf'][$w]['work_cnt'])){
						$arrDT = explode('/',$mst[$m][$c]['iljung']['conf'][$w]['work_cnt']);

						foreach($arrDT as $i => $dt){
							if (!is_numeric(strpos($subsum['conf']['work_dt'], '/'.$dt))){
								if (!empty($dt)){
									$subsum['work_dt'] .= '/'.$dt;
									$subsum['work_cnt'] ++;
								}
							}
						}
					}else{
						$subsum['conf']['work_cnt']  += $mst[$m][$c]['iljung']['conf'][$w]['work_cnt'];
						$subsum['conf']['work_time'] += $mst[$m][$c]['iljung']['conf'][$w]['work_time'];
					}

					for($i=1; $i<=31; $i++){
						$subsum['conf']['iljung'][$i] += $mst[$m][$c]['iljung']['conf'][$w][$i];
					}

				}

			}

		}


		if ($mode == 1){
			$work_cnt  = $subsum['plan']['work_cnt'];
			$work_time = $subsum['plan']['work_time'];
			
			for($i=1; $i<=31; $i++){
				if(!empty($subsum['plan']['iljung'][$i])){
					$subsum['plan']['work_day_cnt'] ++;
				}

				if(!empty($subsum['conf']['iljung'][$i])){
					$subsum['conf']['work_day_cnt'] ++;
				}
			}
			
			$work_day_cnt = $subsum['plan']['work_day_cnt'];

			if (!empty($work_time)) $work_time = number_format(round($work_time / 60, 1), 1); else $work_time = '';
			if (empty($work_cnt))   $work_cnt = '';
			$pdf->SetXY($pdf->left,$pdf->GetY());
			$pdf->Cell($headCol['w'][0], $pdf->rowHeight*$rowspan, '','LRB',0,'C');
			$pdf->Cell($headCol['w'][1], $pdf->rowHeight*$rowspan, '','LRB',0,'C');
			$pdf->Cell($headCol['w'][2]+$headCol['w'][3], $pdf->rowHeight*$rowspan, '소계',1,0,'C');
			$pdf->Cell($headCol['w'][4], $pdf->rowHeight, '계획',1,0,'C');
			$pdf->Cell($headCol['w'][5], $pdf->rowHeight, '',1,0,'C');
			$pdf->Cell($headCol['w'][6], $pdf->rowHeight, '',1,0,'C');
			$pdf->Cell($headCol['w'][7], $pdf->rowHeight, $work_day_cnt."",1,0,'C');
			$pdf->Cell($headCol['w'][8], $pdf->rowHeight, $work_cnt."",1,0,'C');
			$pdf->Cell($headCol['w'][9], $pdf->rowHeight, $work_time,1,0,'C');

			for($i=1; $i<=31; $i++){
				$time  = $subsum['plan']['iljung'][$i];
				$time  = !empty($time) ? number_format(round($time / 60, 1), 1) : '';

				$pdf->Cell($headCol['w'][($i+9)], $pdf->rowHeight, $time,1, $i == sizeOf($headCol[t]) - 1 ? 1 : ($i==31 ? 1 : 0),'C');
			}

			//$pdf->Cell($headCol['w'][40], $pdf->rowHeight, '',1,1,'C');

			$work_day_cnt = $subsum['conf']['work_day_cnt'];
			$work_cnt  = $subsum['conf']['work_cnt'];
			$work_time = $subsum['conf']['work_time'];
			
			if (!empty($work_time)) $work_time = number_format(round($work_time / 60, 1), 1); else $work_time = '';
			if (empty($work_cnt))   $work_cnt = '';

			$pdf->SetX($pdf->left+$headCol['w'][0]+$headCol['w'][1]+$headCol['w'][2]+$headCol['w'][3]);
			$pdf->Cell($headCol['w'][4], $pdf->rowHeight, '실적',1,0,'C');
			$pdf->Cell($headCol['w'][5], $pdf->rowHeight, '',1,0,'C');
			$pdf->Cell($headCol['w'][6], $pdf->rowHeight, '',1,0,'C');
			$pdf->Cell($headCol['w'][7], $pdf->rowHeight, $work_day_cnt."",1,0,'C');
			$pdf->Cell($headCol['w'][8], $pdf->rowHeight, $work_cnt."",1,0,'C');
			$pdf->Cell($headCol['w'][9], $pdf->rowHeight, $work_time,1,0,'C');

			for($i=1; $i<=31; $i++){
				$time  = $subsum['conf']['iljung'][$i];
				$time  = !empty($time) ? number_format(round($time / 60, 1), 1) : '';

				$pdf->Cell($headCol['w'][($i+9)], $pdf->rowHeight, $time,1, $i == sizeOf($headCol[t]) - 1 ? 1 : ($i==31 ? 1 : 0),'C');
			}
		}

		//$pdf->Cell($headCol['w'][40], $pdf->rowHeight, '',1,1,'C');
		if($icon != ''){
			$exp = explode('.',$icon);
			$exp = strtolower($exp[sizeof($exp)-1]);
			if($exp != 'bmp'){
				$pdf->Image('../mem_picture/'.$icon, 265, 190, 20, null);	//기관 로고
			}
		}

		unset($subsum);
	}
	
	
	$pdf->Output();

?>