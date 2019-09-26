<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	
	$homepage = $_GET['homepage'];
	$code  = $_GET['code'];
	$year  = $_GET['year'];
	$month = $_GET['month'];
	$jumin  = $ed->de($_GET['jumin']);
	
	//횡성
	if($code == 'drcare'){
		if($year.$month <= '201612'){ 
			$code = '34273000017';
		} 
	}

	$sql = 'select m03_key
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_jumin = \''.$jumin.'\'
			 limit 1';

	$key = $conn->get_data($sql);

	$liLoopCnt = 2;

	/*
	if ($svcCd == '0'){
		if ($subCd == '500'){
			$liLoopCnt = 2;
		}
	}else if ($svcCd == '4'){
		if ($subCd == '200' || $subCd == '500'){
			$liLoopCnt = 2;
		}
	}
	*/

	for($i=1; $i<=$liLoopCnt; $i++){
		if ($i > 1){
			$sl .= ' UNION ALL ';
		}

		$sl .= 'SELECT t01_jumin AS jumin
				,	   t01_mkind AS svc_cd
				,	   t01_sugup_date AS date
				,	   t01_sugup_fmtime AS from_time
				,	   t01_sugup_totime AS to_time
				,	   t01_sugup_soyotime AS proc_time
				,	   t01_svc_subcode AS sub_cd
				,	   t01_toge_umu AS family_yn
				,	   t01_bipay_umu AS bipay_yn
				,	   t01_suga_code1 AS suga_cd
				,	   t01_suga_tot AS suga_tot
				,	   t01_mem_cd1 AS mem_cd
				  FROM t01iljung
				 WHERE t01_ccode = \''.$code.'\'
				   AND LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				   AND t01_mem_cd'.$i.' = \''.$jumin.'\'
				   AND t01_del_yn = \'N\'';

		/*
		if (!Empty($subCd)){
			$sl .= ' AND t01_svc_subcode = \''.$subCd.'\'';
		}
		*/

	}

	$sl .= $showSql;

	$sql = 'SELECT     DISTINCT iljung.jumin
				,      mst.m03_name AS name
				,      iljung.svc_cd
				,      iljung.date
				,      iljung.from_time
				,      iljung.to_time
				,      iljung.proc_time
				,      iljung.sub_cd
				,      iljung.family_yn
				,      iljung.bipay_yn
				,      iljung.suga_cd
				,      iljung.suga_tot
				,      iljung.mem_cd
				,      yoy.m02_yname AS mem_nm
				  FROM ('.$sl.') AS iljung
				 INNER JOIN m03sugupja AS mst
					ON mst.m03_ccode = \''.$code.'\'
				   AND mst.m03_jumin = iljung.jumin
				 INNER JOIN m02yoyangsa AS yoy
				    ON yoy.m02_ccode  = \''.$code.'\'
				   AND yoy.m02_yjumin = iljung.mem_cd';

		if (!Empty($name)){
			$sql .= ' AND yoy.m02_yname >= \''.$name.'\'';
		}

		$sql .= ' ORDER BY date, from_time, mem_nm, svc_cd';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();
	
	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		switch($row['svc_cd']){
			case '0':
				switch($row['sub_cd']){
					case '200':
						$suga = '방문요양';
						break;
					case '500':
						$suga = '방문목욕';
						break;
					case '800':
						$suga = '방문간호';
						break;
				}
				break;
			case '1':
				$suga = '가사간병';
				break;
			case '2':
				$suga = '노인돌봄';
				break;
			case '3':
				$suga = '산모신생아';
				break;
			case '4':
				switch($row['sub_cd']){
					case '200':
						$suga = '활동지원';
						break;
					case '500':
						$suga = '방문목욕';
						break;
					case '800':
						$suga = '방문간호';
						break;
				}
				break;
			case 'A':
				$suga = '산모유료';
				break;
			case 'B':
				$suga = '병원간병';
				break;
			case 'C':
				$suga = '기타유료';
				break;
		}
		
		$list[$row['date']][sizeof($list[$row['date']])] =
			array(
				 'from'	=>substr($row['from_time'],0,2).':'.substr($row['from_time'],2,2)
				,'to'	=>substr($row['to_time'],0,2).':'.substr($row['to_time'],2,2)
				,'proc'	=>$row['proc_time']
				,'svc'	=>$row['sub_cd']
				,'bipay'=>$row['bipay']
				,'name'=>$row['name']
				,'suga'	=>$suga
			);
	}

	$conn->row_free();


	$today   = date('Ymd', mktime());
	$lastday = date('t', strtotime($year.'-'.$month.'-01'));
	$lastday = ($lastday < 10 ? '0' : '').$lastday;

	if($homepage == 'fw'){ 
		$cl = '#82bf41';
	}else {
		$cl = '#cccccc'; 
	}

	echo '<div id="divStart"></div> ';
	echo '<table class=\'view_type\' style=\'width:98%;\'>
			<colgroup>
				<col span=\'7\'>
			</colgroup>
			<thead>
				<tr>
					<th><span style=\'color:#ff0000;\'>일</span></th>
					<th>월</th>
					<th>화</th>
					<th>수</th>
					<th>목</th>
					<th>금</th>
					<th><span style=\'color:#0000ff;\'>토</span></th>
				</tr>
			</thead>
			<tbody>';

	$startWeekday = date('w', strtotime($year.'-'.$month.'-01'));
	$endWeekday   = date('w', strtotime($year.'-'.$month.'-'.$lastday));

	for($i=0; $i<$startWeekday; $i++){
		if ($i == 0) echo '<tr>';

		$style = 'border-top:1px solid '.$cl.'; border-left:1px solid '.$cl.';';

		if ($i > 0 && $i % 7 == 0){
			$style .= 'border-right:1px solid '.$cl.';';
		}

		echo '<td style=\''.$style.'\'>&nbsp;</td>';
	}

	for($i=1; $i<=$lastday; $i++){
		$style = 'border-top:1px solid '.$cl.'; border-left:1px solid '.$cl.';';

		if ($startWeekday % 6 == 0){
			$style .= 'border-right:1px solid '.$cl.';';
		}

		if ($i >= $lastday - $endWeekday){
			$style .= 'border-bottom:1px solid '.$cl.';';
		}

		if ($startWeekday == 0){
			echo '<tr>';
		}else{
			if ($startWeekday % 7 == 0){
				$startWeekday = 0;
				echo '</tr>';
				echo '<tr>';
			}
		}

		if ($startWeekday % 7 == 0){
			$textColor = '#ff0000';
		}else if ($startWeekday % 7 == 6){
			$textColor = '#0000ff';
		}else{
			$textColor = '#000000';
		}

		if ($today == $year.$month.($i < 10 ? ' ' : '').$i){
			$bgcolor = '#ffffd9';
		}else{
			$bgcolor = '#ffffff';
		}
		
		//background-color:'.$bgcolor.'; 
		$day = $year.$month.($i<10 ? '0'.$i : $i);
		
		echo '<td style=\'color:'.$textColor.'; vertical-align:top; '.$style.'\'>'.getTexts($i, $list[$day]).'</td>';
		$startWeekday ++; //요일증가
	}


	for($i=$endWeekday+1; $i<7; $i++){
		$css = 'border:1px solid '.$cl.';';
		
	
		if ($i % 6 == 0){
		//	$css .= 'border-right:1px solid #cccccc;';
		}

		$day ++;

		echo '<td style=\''.$css.'\'>&nbsp;</td>';
	}

	echo '	</tbody>
		  </table>
		  <input id=\'key\' name=\'key\' type=\'hidden\' value=\''.$key.'\'>
		  <input id=\'jumin\' name=\'jumin\' type=\'hidden\' value=\''.$ed->en($jumin).'\'>';
	
	echo '<div id="divLast"></div>';

	$conn->close();


	function getTexts($day, $data){
		$html  = '<div>'.$day.'</div>';

		if (is_array($data)){
			foreach($data as $i => $arr){
				$html .= '<div>'.$data[$i]['from'].'~'.$data[$i]['to'].'</div>';
				$html .= '<div>'.$data[$i]['name'].'</div>';
				$html .= '<div>'.$data[$i]['suga'].'</div>';
			}
		}else{
			$html .= '<div>&nbsp;</div>';
		}

		return $html;
	}
?>