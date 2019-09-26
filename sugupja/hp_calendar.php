<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");

	//if ($_SERVER["HTTP_REFERER"] == "") exit;
	$homepage = $_GET['homepage'];
	$code  = $_GET['code'];
	$year  = $_GET['year'];
	$month = $_GET['month'];
	
	//횡성
	if($code == 'drcare'){
		if($year.$month <= '201612'){ 
			$code = '34273000017';
		} 
	}

	if($_GET['appNo'] != ''){
		$sql = 'select DISTINCT jumin
				  from client_his_lvl 
				 where org_no = \''.$code.'\'
				   and app_no = \''.$_GET['appNo'].'\'';
		$jumin = $conn -> get_data($sql);			   
	}else {
		$jumin  = $ed->de($_GET['jumin']);
	}

	$sql = 'select m03_key
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_jumin = \''.$jumin.'\'
			 limit 1';

	$key = $conn->get_data($sql);


	$sql = 'select t01_mkind as kind
			,      t01_svc_subcode as svc_cd
			,      cast(right(t01_sugup_date, 2) as unsigned) as dt
			,      t01_sugup_fmtime as f_time
			,      t01_sugup_totime as t_time
			,      t01_sugup_proctime as p_time
			,      t01_suga_code1 as suga_cd
			,      t01_bipay_umu as bipay
			,      t01_yname1 as m_nm1
			,      t01_yname2 as m_nm2
			  from t01iljung
			 where t01_ccode  = \''.$code.'\'
			   and t01_jumin  = \''.$jumin.'\'
			   and t01_del_yn = \'N\'
			   and left(t01_sugup_date, 6) = \''.$year.$month.'\'
			 order by dt, f_time, t_time';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		switch($row['kind']){
			case '0':
				switch($row['svc_cd']){
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
				switch($row['svc_cd']){
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

		$list[$row['dt']][sizeof($list[$row['dt']])] =
			array(
				 'from'	=>substr($row['f_time'],0,2).':'.substr($row['f_time'],2,2)
				,'to'	=>substr($row['t_time'],0,2).':'.substr($row['t_time'],2,2)
				,'proc'	=>$row['p_time']
				,'svc'	=>$row['svc_cd']
				,'bipay'=>$row['bipay']
				,'suga'	=>$suga
				,'mem'	=>$row['m_nm1'].(!empty($row['m_nm2']) ? '/'.$row['m_nm2'] : '')
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

	echo '<div id="divStart"></div>';
	echo '<table class=\'view_type\' style=\'width:98%;\'>
			<colgroup>
				<col span=\'7\'>
			</colgroup>
			<thead>
				<tr>
					<th ><span style=\'color:#ff0000;\'>일</span></th>
					<th >월</th>
					<th >화</th>
					<th >수</th>
					<th >목</th>
					<th >금</th>
					<th ><span style=\'color:#0000ff;\'>토</span></th>
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
		
		echo '<td style=\'color:'.$textColor.'; vertical-align:top; '.$style.'\'>'.getTexts($i, $list[$i]).'</td>';
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
				$html .= '<div>'.$data[$i]['mem'].'</div>';
				$html .= '<div>'.$data[$i]['suga'].'</div>';
			}
		}else{
			$html .= '<div>&nbsp;</div>';
		}

		return $html;
	}
?>
