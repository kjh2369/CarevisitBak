<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$yymm = $_POST['year'].$_POST['month']; 
	$memCd = $ed->de($_POST['memCd']);
	
		
	$sql = 'SELECT sum(case substring(date,7,2) when \'01\' then 1 else 0 end) as \'d1\'
		,      sum(case substring(date,7,2) when \'02\' then 1 else 0 end) as \'d2\'
		,      sum(case substring(date,7,2) when \'03\' then 1 else 0 end) as \'d3\'
		,      sum(case substring(date,7,2) when \'04\' then 1 else 0 end) as \'d4\'
		,      sum(case substring(date,7,2) when \'05\' then 1 else 0 end) as \'d5\'
		,      sum(case substring(date,7,2) when \'06\' then 1 else 0 end) as \'d6\'
		,      sum(case substring(date,7,2) when \'07\' then 1 else 0 end) as \'d7\'
		,      sum(case substring(date,7,2) when \'08\' then 1 else 0 end) as \'d8\'
		,      sum(case substring(date,7,2) when \'09\' then 1 else 0 end) as \'d9\'
		,      sum(case substring(date,7,2) when \'10\' then 1 else 0 end) as \'d10\'
		,      sum(case substring(date,7,2) when \'11\' then 1 else 0 end) as \'d11\'
		,      sum(case substring(date,7,2) when \'12\' then 1 else 0 end) as \'d12\'
		,      sum(case substring(date,7,2) when \'13\' then 1 else 0 end) as \'d13\'
		,      sum(case substring(date,7,2) when \'14\' then 1 else 0 end) as \'d14\'
		,      sum(case substring(date,7,2) when \'15\' then 1 else 0 end) as \'d15\'
		,      sum(case substring(date,7,2) when \'16\' then 1 else 0 end) as \'d16\'
		,      sum(case substring(date,7,2) when \'17\' then 1 else 0 end) as \'d17\'
		,      sum(case substring(date,7,2) when \'18\' then 1 else 0 end) as \'d18\'
		,      sum(case substring(date,7,2) when \'19\' then 1 else 0 end) as \'d19\'
		,      sum(case substring(date,7,2) when \'20\' then 1 else 0 end) as \'d20\'
		,      sum(case substring(date,7,2) when \'21\' then 1 else 0 end) as \'d21\'
		,      sum(case substring(date,7,2) when \'22\' then 1 else 0 end) as \'d22\'
		,      sum(case substring(date,7,2) when \'23\' then 1 else 0 end) as \'d23\'
		,      sum(case substring(date,7,2) when \'24\' then 1 else 0 end) as \'d24\'
		,      sum(case substring(date,7,2) when \'25\' then 1 else 0 end) as \'d25\'
		,      sum(case substring(date,7,2) when \'26\' then 1 else 0 end) as \'d26\'
		,      sum(case substring(date,7,2) when \'27\' then 1 else 0 end) as \'d27\'
		,      sum(case substring(date,7,2) when \'28\' then 1 else 0 end) as \'d28\'
		,      sum(case substring(date,7,2) when \'29\' then 1 else 0 end) as \'d29\'
		,      sum(case substring(date,7,2) when \'30\' then 1 else 0 end) as \'d30\'
		,      sum(case substring(date,7,2) when \'31\' then 1 else 0 end) as \'d31\'
		 FROM  sw_log
		WHERE  org_no		= \''.$orgNo.'\'
		AND	   reg_jumin   = \''.$memCd.'\'
		AND	   del_flag= \'N\'
		AND	   left(date, 6) = \''.$yymm.'\'';	
	
	$cnt = $conn -> get_array($sql);

	$sql = 'SELECT	jumin
			,		date
			,		time
			,	    to_time
			,		reg_name
			,		yymm
			,		seq
			,	    comment
			,	    command
			,		clt_name
			FROM	sw_log
			INNER JOIN ( SELECT m03_jumin
              ,     m03_name as clt_name
               FROM m03sugupja               
              WHERE m03_ccode = \''.$orgNo.'\'
			  GROUP BY m03_jumin) as mst              
			ON      m03_jumin = jumin
			WHERE	org_no		= \''.$orgNo.'\'
			AND		reg_jumin   = \''.$memCd.'\'
			AND		del_flag= \'N\'
			AND		left(date, 6) = \''.$yymm.'\''; 

	$sql .=	'ORDER	BY date, time, clt_name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$seq = 1;
	
	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
			
		$day[$i] = intval(substr($row['date'], -2));
		
	
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
				AND		t01_jumin = \''.$row['jumin'].'\'
				AND		t01_sugup_date = \''.$row['date'].'\'
				AND		t01_del_yn = \'N\'';
						
		$mem = $conn->get_array($sql);
		
		if ($r['stat'] == '1'){
			$from = $mem['conf_from'];
			$to = $mem['conf_to'];
		}else if ($r['stat'] == '5'){
			$from = $mem['work_from'];
			$to = '9999';
		}else{
			$from = $mem['plan_from'];
			$to = $mem['plan_to'];
		}
		
		$mem_nm = '';

		//if ($row['time'] >= $from && $row['time'] <= $to){
			if($mem['mem_nm1'] != ''){
				$mem_nm = $mem['mem_nm1'];
			}

			/*
			if ($mem['mem_nm2']){
				$mem_nm = $mem['mem_nm2'];
			}
			*/
			
		//}
		
		if($row['command'] != ''){
			$sign_yn = 'Y';
		}else {
			$sign_yn = '';                                                                                                                         
		}                                                                                                                                                             
		

 
		if($day[$i-1] != $day[$i]){
			$new = true;
		}else {
			$new = false;
		}
		
		$tmp[$i] = $row;
			
		if($day[$i-1] == $day[$i]){
			$confTo = $tmp[$i-1]['to_time'];
			$confFrom = $tmp[$i]['time'];
			
			$diff_mins=((strtotime($confFrom)-strtotime($confTo))/60).'분'; 
		}
		
		if($new) $diff_mins = '';
		
		if($tmp[$i-1]['conf_to'] > $tmp[$i]['conf_from']){
			$color = '#FF00DD';
		}else {
			$color = '';
		}
		
		if($mem['conf_from'] > $row['time'] || $mem['conf_to'] < $row['to_time']){
			$color2 = '#C4B73B';
		}else {
			$color2 = '';
		}

		?>
		<tr><?
			if($new){ 	?>
				<td class="left" rowspan="<?=$cnt['d'.$day[$i]];?>" ><?=$day[$i];?>일</td><?
			} ?>
			<td class="left"><?=$row['clt_name'];?></td>
			<td class="left"><div class="nowrap" style="width:70px;"><?=$mem_nm;?></div></td>
			<td class="center"><?=$myF->timeStyle($mem['conf_from']);?> ~ <?=$myF->timeStyle($mem['conf_to']);?></td>
			<td class="center" style="color:<?=$color2;?>;"><?=$myF->timeStyle($row['time'],'.');?> ~ <?=$myF->timeStyle($row['to_time']);?></td>
			<td class="center" style="color:<?=$color;?>;"><?=$diff_mins;?></td>
			<td class="center last" ></td>
		</tr><?
		
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
