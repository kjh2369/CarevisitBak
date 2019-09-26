<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$date	= str_replace('-','',$_POST['date']);
	$today	= Date('Ymd');
	$now	= Date('Hi');

	if ($today != $date){
		$now = '9999';
	}

	//미계획 실적
	$sql = 'SELECT	sub_cd
			,		conf_from
			,		conf_to
			,		conf_time
			,		jumin
			,		name
			,		mem_cd1
			,		mem_cd2
			,		mem_nm1
			,		mem_nm2
			FROM	plan_longterm_conf
			WHERE	org_no	= \''.$orgNo.'\'
			AND		date	= \''.$date.'\'
			AND		plan_seq= \'0\'
			ORDER	BY conf_from, conf_to, name';

	$conn->query($sql);
	$conn->fetch();

	$no = 1;
	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['sub_cd'] == '200'){
			$subNm = '방문요양';
		}else if ($row['sub_cd'] == '500'){
			$subNm = '<span style="color:#0000FF;">방문목욕</span>';
		}else if ($row['sub_cd'] == '800'){
			$subNm = '방문간호';
		}else{
			$subNm = '<span style="color:red;">NULL</span>';
		}

		if (!$row['jumin']){
			$stat = '<span style="color:red;">수급자오류</span>';
		}else if (!$row['mem_cd1']){
			$stat = '<span style="color:red;">요양보호사오류</span>';
		}else{
			$stat = '';
		}

		if ($no % 2 == 1){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$subNm;?></td>
			<td class="center"></td>
			<td class="center"></td>
			<td class="center"></td>
			<td class="center"></td>
			<td class="center bottom"></td>
			<td class="center"><?=$myF->timeStyle($row['conf_from']);?></td>
			<td class="center"><?=$myF->timeStyle($row['conf_to']);?></td>
			<td class="center"><div class="nowrap left" style="width:60px;"><?=$row['name'];?></div></td>
			<td class="center"><div class="nowrap left" style="width:110px;"><?=$row['mem_nm1'].($row['mem_nm2'] ? '/'.$row['mem_nm2'] : '');?></div></td>
			<td class="center"><span style="color:red; font-weight:bold;">미계획</span></td>
			<td class="center last"><div class="nowrap left" style="width:100%;"><?=$stat;?></div></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();


	//조히
	$sql = 'SELECT	plan_from
			,		plan_to
			,		plan_seq
			,		plan_time
			,		plan_jumin
			,		plan_sub_cd
			,		plan_name
			,		plan_mem_cd1
			,		plan_mem_cd2
			,		plan_mem_nm1
			,		plan_mem_nm2
			,		work_from
			,		work_to
			,		long_sub_cd
			,		long_jumin
			,		long_name
			,		CASE WHEN long_from != \'\' THEN long_from ELSE work_from END AS long_from
			,		CASE WHEN long_to != \'\' THEN long_to ELSE work_to END AS long_to
			,		long_time
			,		long_mem_cd1
			,		long_mem_cd2
			,		long_mem_nm1
			,		long_mem_nm2
			,		CASE WHEN plan_jumin != long_jumin THEN CASE WHEN plan_from > \''.$now.'\' THEN 3 ELSE 9 END
						 WHEN plan_name != long_name THEN 99
						 WHEN plan_mem_cd1 != long_mem_cd1 THEN 99
						 WHEN plan_mem_cd2 != long_mem_cd2 THEN 99
						 WHEN plan_mem_nm1 != long_mem_nm1 THEN 99
						 WHEN plan_mem_nm2 != long_mem_nm2 THEN 99
						 WHEN long_from = \'\' AND long_to = \'\' THEN CASE WHEN plan_from > \''.$now.'\' THEN 3 ELSE 9 END
						 WHEN long_from = \'\' THEN CASE WHEN plan_from > \''.$now.'\' THEN 3 ELSE 9 END
						 WHEN long_to = \'\' THEN 5
						 ElSE 1 END AS stat
			FROM	(
					SELECT	t01_sugup_fmtime AS plan_from
					,		t01_sugup_totime AS plan_to
					,		t01_sugup_seq AS plan_seq
					,		t01_sugup_soyotime AS plan_time
					,		t01_jumin AS plan_jumin
					,		t01_svc_subcode AS plan_sub_cd
					,		IFNULL(t01_wrk_fmtime,\'\') AS work_from
					,		IFNULL(t01_wrk_totime,\'\') AS work_to
					,		m03_name AS plan_name
					,		t01_mem_cd1 AS plan_mem_cd1
					,		CASE WHEN t01_svc_subcode = \'500\' THEN t01_mem_cd2 ELSE \'\' END AS plan_mem_cd2
					,		t01_mem_nm1 AS plan_mem_nm1
					,		CASE WHEN t01_svc_subcode = \'500\' THEN t01_mem_nm2 ELSE \'\' END AS plan_mem_nm2
					,		IFNULL(lt.sub_cd,\'\') AS long_sub_cd
					,		IFNULL(lt.jumin,\'\') AS long_jumin
					,		IFNULL(lt.name,\'\') AS long_name
					,		IFNULL(lt.conf_from,\'\') AS long_from
					,		IFNULL(lt.conf_to,\'\') AS long_to
					,		IFNULL(lt.conf_time,\'\') AS long_time
					,		IFNULL(lt.mem_cd1,\'\') AS long_mem_cd1
					,		IFNULL(lt.mem_cd2,\'\') AS long_mem_cd2
					,		IFNULL(lt.mem_nm1,\'\') AS long_mem_nm1
					,		IFNULL(lt.mem_nm2,\'\') AS long_mem_nm2
					FROM	t01iljung
					INNER	JOIN	m03sugupja
							ON		m03_ccode = t01_ccode
							AND		m03_mkind = t01_mkind
							AND		m03_jumin = t01_jumin
					LEFT	JOIN	plan_longterm_conf AS lt
							ON		lt.org_no = t01_ccode
							AND		lt.date = t01_sugup_date
							AND		lt.jumin = t01_jumin
							AND		lt.plan_from = t01_sugup_fmtime
							AND		lt.plan_seq = t01_sugup_seq
					WHERE	t01_ccode		= \''.$orgNo.'\'
					AND		t01_mkind		= \'0\'
					AND		t01_sugup_date	= \''.$date.'\'
					AND		t01_del_yn		= \'N\'
					) AS t
			ORDER	BY stat DESC, plan_from, plan_to, plan_name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$other1 = '';
		$other2 = '';
		$color1 = '';
		$color2 = '';

		if ($row['stat'] == 5 || $row['stat'] == 1){
			$compareTime = Abs($myF->time2min($row['long_from']) - $myF->time2min($row['plan_from']));

			if ($compareTime > 10){
				$color1 = '#FF0000';
				$other1 = '시작(<span style="color:#FF0000;">'.$compareTime.'분</span>)';
			}
		}

		if ($row['plan_sub_cd'] == '200'){
			$subNm = '방문요양';
		}else if ($row['plan_sub_cd'] == '500'){
			$subNm = '<span style="color:#0000FF;">방문목욕</span>';
		}else if ($row['plan_sub_cd'] == '800'){
			$subNm = '방문간호';
		}else{
			$subNm = '<span style="color:red;">NULL</span>';
		}

		if ($row['stat'] == 3){
			$stat = '<span>대기</span>';
		}else if ($row['stat'] == 5){
			$stat = '<span style="color:blue;">수행중</span>';
		}else if ($row['stat'] == 9){
			$stat = '<span style="color:red;">미수행</span>';
		}else if ($row['stat'] == 99){
			$stat = '<span style="color:red;">불일치</span>';
		}else{
			$stat = '정상';

			$planTime = $myF->cutOff($myF->time2min($row['plan_to']) - $myF->time2min($row['plan_from']), 30);
			$longTime = $myF->cutOff($myF->time2min($row['long_to']) - $myF->time2min($row['long_from']), 30);

			if ($planTime != $longTime){
				$color2 = '#0000FF';
				$other2 = ($other1 ? '/' : '').'실적(<span style="color:'.$color2.';">'.($longTime - $planTime).'분</span>)';
			}
		}

		if ($no % 2 == 1){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$subNm;?></td>
			<td class="center" style="color:<?=$color1;?>;"><?=$myF->timeStyle($row['plan_from']);?></td>
			<td class="center" style="color:<?=$color2;?>;"><?=$myF->timeStyle($row['plan_to']);?></td>
			<td class="center"><div class="nowrap left" style="width:60px;"><?=$row['plan_name'];?></div></td>
			<td class="center"><div class="nowrap left" style="width:110px;"><?=$row['plan_mem_nm1'].($row['plan_mem_nm2'] ? '/'.$row['plan_mem_nm2'] : '');?></div></td>
			<td class="center bottom"></td>
			<td class="center" style="color:<?=$color1;?>;"><?=$myF->timeStyle($row['long_from']);?></td>
			<td class="center" style="color:<?=$color2;?>;"><?=$myF->timeStyle($row['long_to']);?></td>
			<td class="center"><div class="nowrap left" style="width:60px;"><?=$row['long_name'];?></div></td>
			<td class="center"><div class="nowrap left" style="width:110px;"><?=$row['long_mem_nm1'].($row['long_mem_nm2'] ? '/'.$row['long_mem_nm2'] : '');?></div></td>
			<td class="center"><?=$stat;?></td>
			<td class="center last"><div class="nowrap left"><?=$other1;?><?=$other2;?></div></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>