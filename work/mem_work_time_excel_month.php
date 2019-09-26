<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	
	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$code = $_SESSION['userCenterCode'];
	//$year = $_POST['year'] != '' ? $_POST['year'] : $_GET['year'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$fmYear  = substr($_POST['fromDt'],0, 4) ;
	$toYear  = substr($_POST['toDt'],0, 4);
	
		
	
	$tableBorderStyle = 'border=\'1\'';
	
	$month = intval(substr($fromDt, -2));
	$year = substr($fromDt,0, 4);
	$num = 0;
	
	
	for($i=$fromDt; $i<=$toDt; $i++){ 
		
		if($month>12){
			$year++;
			$month = 1;
			
		}
		$month = $month<10? '0'.$month : $month;
		
		
		
		if($toDt<$year.$month){
			break;
		}
		

		$wsl .= ', sum(200_conf_time_'.$year.$month.') as conf_200_'.$year.$month.'								
				 , sum(500_conf_time_'.$year.$month.') as conf_500_'.$year.$month.'								
				 , sum(800_conf_time_'.$year.$month.') as conf_800_'.$year.$month.'';

		
		$wsl2 .= ' , SUM(case left(t01_conf_date, 6) when \''.$year.$month.'\' then floor((case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' then t01_conf_soyotime else 0 end -								
			case when left(t01_conf_date,6) >= \'201603\' then								
			case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 480 then 0								
			when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end								
			else case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end end) - (case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' then t01_conf_soyotime else 0 end -								
			case when left(t01_conf_date,6) >= \'201603\' then								
			case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 480 then 0								
			when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end								
			else case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end end) % 30)  else 0 end ) as 200_conf_time_'.$year.$month.'								
			, sum(case left(t01_conf_date, 6) when \''.$year.$month.'\' then floor(case when t01_svc_subcode = \'500\' then t01_conf_soyotime else 0 end - (case when t01_svc_subcode = \'500\' then t01_conf_soyotime else 0 end % 30)) else 0 end) as 500_conf_time_'.$year.$month.'								
			, sum(case left(t01_conf_date, 6) when \''.$year.$month.'\' then case when t01_svc_subcode = \'800\' then t01_conf_soyotime else 0 end else 0 end) as 800_conf_time_'.$year.$month.'';


		$month++;
		
		$num++;
		
	}
	

	$month = intval(substr($fromDt, -2));
	$year = substr($fromDt,0, 4);

	$sql = 'select name								
			, jumin								
			'.$wsl.'			
			from (
				select t01_mem_nm1 as name								
			, t01_mem_cd1 as jumin								
			, left(t01_conf_date, 6) as work_mon								
			'.$wsl2.'	
			from t01iljung								
			where t01_ccode = \''.$code.'\'								
			and t01_del_yn = \'N\'								
			and t01_mem_cd1 != \'\'								
			and left(t01_conf_date,6) BETWEEN \''.$fromDt.'\' and \''.$toDt.'\'							
			group by t01_mem_cd1
			union all
			select t01_mem_nm2 as name								
			, t01_mem_cd2 as jumin								
			, left(t01_conf_date, 6) as work_mon								
			'.$wsl2.'	
			from t01iljung								
			where t01_ccode = \''.$code.'\'								
			and t01_del_yn = \'N\'								
			and t01_mem_cd2 != \'\'								
			and left(t01_conf_date,6) BETWEEN  \''.$fromDt.'\' and \''.$toDt.'\'								
			group by t01_mem_cd2
			) as t								
			group by jumin								
			order by name';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$STR = '생년월일';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		
		if (empty($tableBorderStyle)){
			$SSN = $myF->issToBirthday($row['jumin'],'.');
			$STR = '생년월일';
		}else {
			if($debug){
				$SSN = $myF->issNo($row['jumin']);
				$STR = '주민번호'; 
			}else {
				$SSN = $myF->issToBirthday($row['jumin'],'.');
				$STR = '생년월일';
			}
		}
		
		$id = $row['jumin'];
		$data[$id] = array('no'      =>$i+1
						  ,'nm'      =>$row['name']
						  ,'birthday'=>$SSN
						  ,'tot_amt' =>0
						  ,'retire_pay' =>0);
		
		
		
		for($j=1; $j<=$num; $j++){ 
			if($month>12){
				$year++;
				$month = 1;
				
			}
			
			$month = $month<10? '0'.$month : $month;
			
			
			
			$data[$id][$j] = array('conf_time_200' =>$row['conf_200_'.$year.$month]
								  ,'conf_time_500' =>$row['conf_500_'.$year.$month]
								  ,'conf_time_800' =>$row['conf_800_'.$year.$month]);
			
			
			$data[$id]['tot_conf_200']  += $row['conf_200_'.$year.$month];
			$data[$id]['tot_conf_500']  += $row['conf_500_'.$year.$month];
			$data[$id]['tot_conf_800']  += $row['conf_800_'.$year.$month];
			
			
			$month++;

			if($j==$num){
				$month = intval(substr($fromDt, -2));
				$year = substr($fromDt,0, 4);
			}
			
			
		}
	}
	
	$month = intval(substr($fromDt, -2));
	$year = substr($fromDt,0, 4);
	
	$conn->row_free();

	$html  = '';
	$html .= '<div style="font-size:20pt;">요양보호사 근무시간 '.$myF->_styleYYMM($fromDt,'.').'~'.$myF->_styleYYMM($toDt,'.').'</div>';
	$html .= '<div style="font-size:14pt;">센터명: '.$myF->euckr($_SESSION['userCenterName']).'</div>';
	$html .= '<div id=\'summlyHeadLeft\' style=\'float:left; width:50%; overflow-x:hidden; overflow-y:hidden;\'>';
		

	$html .= '	<table class=\'my_table\' style=\'width:100%;\' '.$tableBorderStyle.'>
					<colgroup>
						<col width=\'8%\'>
						<col width=\'17%\'>
						<col width=\'19%\'>
						<col width=\'14%\' span=\'2\'>
					</colgroup>
					<thead>
						<tr>
							<th class=\'head\' rowspan=\'2\'>No</th>
							<th class=\'head\' rowspan=\'2\'>직원</th>
							<th class=\'head\' rowspan=\'2\'>'.$STR.'</th>
							<th class=\'head\' colspan=\'3\'>합계</th>';
							
							
							for($i=1; $i<=$num; $i++){ 
								
								
								if($month>12){
									$year++;
									$month = 1;
								}
								

								$month = $month<10? '0'.$month : $month;
								
							
								$html .= '<th class=\'head\' colspan=\'3\'>'.$year.'년'.$month.'월</th>';

								$month++;

							}

							$month = intval(substr($fromDt, -2));
							$year = substr($fromDt,0, 4);
						

	$html .= '			</tr>
						<tr>
							<th class=\'head\'>요양</th>
							<th class=\'head\'>목욕</th>
							<th class=\'head\'>간호</th>';

							if (!empty($tableBorderStyle)){
								for($i=1; $i<=$num; $i++){ 
									if($month>12){
										$year++;
										$month = 1;
										
									}

									$month = $month<10? '0'.$month : $month;
									

									$html .= '<th class=\'head\'>요양</th>
											  <th class=\'head\'>목욕</th>
											   <th class=\'head\'>간호</th>';

									$month++;
								}

								$month = intval(substr($fromDt, -2));
								$year = substr($fromDt,0, 4);
							}

	$html .= '			</tr>
					</thead>
				</table>';

	
	$html .= '<table class=\'my_table\' style=\'width:100%;\' '.$tableBorderStyle.'>
				<colgroup>
					<col width=\'8%\'>
					<col width=\'17%\'>
					<col width=\'19%\'>
					<col width=\'14%\' span=\'2\'>
				</colgroup>
				<tbody>';

	if (is_array($data)){
		
		foreach($data as $cd => $list){
			
			$tbl2 .= '<tr>
						<td class=\'center\'>'.$list['no'].'</td>
						<td class=\'center\'><div class=\'left nowrap\' style=\'width:60px;\'>'.$myF->euckr($list['nm']).'</div></td>
						<td class=\'center\'>'.$list['birthday'].'</td>
						<td class=\'center\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($list['tot_conf_200']/ 60, 1).'</div></td>
						<td class=\'center\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($list['tot_conf_500']/ 60, 1).'</div></td>
						<td class=\'center\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($list['tot_conf_800']/ 60, 1).'</div></td>';
			
			$tot_conf_time_200 += $list['tot_conf_200'];
			$tot_conf_time_500 += $list['tot_conf_500'];
			$tot_conf_time_800 += $list['tot_conf_600'];

			if (!empty($tableBorderStyle)){
			
				for($i=1; $i<=$num; $i++){ 
								
					//echo $myF->euckr($list['nm']).'/'.$month.'/'.$list[$i]['conf_time_200'].'/';
						
					if($month>12){
						$year++;
						$month = 1;
						
					}
					

					$month = $month<10? '0'.$month : $month;
					
					
					$tbl2 .= '<td class=\'center\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($list[$i]['conf_time_200']/ 60, 1).'</div></td>
					<td class=\'center\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($list[$i]['conf_time_500']/ 60, 1).'</div></td>
					<td class=\'center\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($list[$i]['conf_time_800']/ 60, 1).'</div></td>';
					
					
					$conf_time_200[$i] += $list[$i]['conf_time_200'];
					$conf_time_500[$i] += $list[$i]['conf_time_500'];
					$conf_time_800[$i] += $list[$i]['conf_time_800'];
					
					
					$month++;

					if($i==$num){
						$year = substr($fromDt,0, 4);
						$month = substr($fromDt, -2);
					}
				}
				
			}
			
			$tbl2 .= '</tr>';
		
		}

		$tbl1 .= '<tr>
					<td class=\'center sum\' colspan=\'3\' style="text-align:right; padding-right:10px;">합계</td>
					<td class=\'center sum\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($tot_conf_time_200/ 60, 1).'</div></td>
					<td class=\'center sum\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($tot_conf_time_500/ 60, 1).'</div></td>
					<td class=\'center sum\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($tot_conf_time_800/ 60, 1).'</div></td>';
		
		if (!empty($tableBorderStyle)){
			for($i=1; $i<=$num; $i++){ 
											
				if($month>12){
					$year++;
					$month = 1;
					
				}
				
				$month = $month<10? '0'.$month : $month;
				
				$tbl1 .= '<td class=\'center sum\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($conf_time_200[$i]/ 60, 1).'</div></td>
						  <td class=\'center sum\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($conf_time_500[$i]/ 60, 1).'</div></td>
						  <td class=\'center sum\'><div class=\'right\' style=\'font-size:11px;\'>'.number_format($conf_time_800[$i]/ 60, 1).'</div></td>';
			}
		}

		$tbl1 .= '</tr>';
	}

	$html2 .= '	</tbody>
			  </table>';



	echo $html.$tbl1.$tbl2.$html2.$tbl3.$tbl4.$html3;


	include_once('../inc/_db_close.php');
	
	

?>