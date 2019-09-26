<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
?>

<?

	//if ($_SERVER["HTTP_REFERER"] == "") exit;

	$code  = $_GET['code'];
	$year  = $_GET['year'];
	$domain = $_GET['domain'];
	
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
	
	$html = '';

	$html .= '<table class=\'list_type\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'50px\'>
					<col width=\'80px\' span=\'7\'>
					<col>
				</colgroup>
				<thead>
					<tr>
						<th rowspan=\'2\' >월</th>
						<th rowspan=\'2\' >실적<br>급여액</th>
						<th rowspan=\'2\' >공단<br>청구액</th>
						<th colspan=\'3\' >본인부담액</th>
						<th rowspan=\'2\' >입금액</th>
						<th rowspan=\'2\' >미납금액</th>
						<th rowspan=\'2\' >비고</th>
					</tr>
					<tr>
						<th >합계</th>
						<th >급여</th>
						<th >비급여</th>
					</tr>
				</thead>
				<tbody>';

	$sql = 'select mm
			, sum(suga) as suga
			, sum(public) as public
			, sum(expenses) as expenses
			, sum(bipay) as bipay
			, sum(deposit) as deposit
			  from (
			       select cast(right(t13_pay_date, 2) as unsigned) as mm
				   ,      t13_jumin as c_cd
			       ,      t13_suga_tot4 as suga
			       ,      t13_chung_amt4 as public
			       ,      t13_bonin_amt4 as expenses
			       ,      t13_bipay4 + t13_over_amt4 as bipay
			       ,      sum(ifnull(unpaid_deposit_list.deposit_amt, 0)) as deposit
			         from t13sugupja
			         left join unpaid_deposit_list
				       on unpaid_deposit_list.org_no       = t13sugupja.t13_ccode
			          and unpaid_deposit_list.unpaid_yymm  = t13sugupja.t13_pay_date
			          and unpaid_deposit_list.unpaid_jumin = t13sugupja.t13_jumin
			        where t13_ccode             = \''.$code.'\'
					  and t13_mkind             = \'0\'
			          and left(t13_pay_date, 4) = \''.$year.'\'
					  and t13_jumin             = \''.$jumin.'\'
			          and t13_type              = \'2\'
			        group by t13_pay_date, t13_jumin
			       ) as t
			 group by mm
			 order by mm';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data[$row['mm']] = array('suga'		=>$row['suga']
								 ,'public'		=>$row['public']
								 ,'expenses_tot'=>$row['expenses']+$row['bipay']
								 ,'expenses'	=>$row['expenses']
								 ,'bipay'		=>$row['bipay']
								 ,'deposit'		=>$row['deposit']
								 ,'unpaid'		=>$row['expenses']+$row['bipay']-$row['deposit']);

		$tot['suga']		+= $data[$row['mm']]['suga'];
		$tot['public']		+= $data[$row['mm']]['public'];
		$tot['expenses_tot']+= $data[$row['mm']]['expenses_tot'];
		$tot['expenses']	+= $data[$row['mm']]['expenses'];
		$tot['bipay']		+= $data[$row['mm']]['bipay'];
		$tot['deposit']		+= $data[$row['mm']]['deposit'];
		$tot['unpaid']		+= $data[$row['mm']]['unpaid'];
	}
	
	$conn->row_free();

	$html .= '<tr>
				<td >전체</td>
				<td class="cs_all_r">'.number_format($tot['suga']).'</td>
				<td class="cs_all_r">'.number_format($tot['public']).'</td>
				<td class="cs_all_r">'.number_format($tot['expenses_tot']).'</td>
				<td class="cs_all_r">'.number_format($tot['expenses']).'</td>
				<td class="cs_all_r">'.number_format($tot['bipay']).'</td>
				<td class="cs_all_r">'.number_format($tot['deposit']).'</td>
				<td class="cs_all_r">'.number_format($tot['unpaid']).'</td>
				<td class="cs_all_r">&nbsp;</td>
			  </tr>';

	for($i=1; $i<=12; $i++){
		$html .= '<tr>
					<td class="cs_c">'.$i.'월</td>
					<td class="cs_r" >'.number_format($data[$i]['suga']).'</td>
					<td class="cs_r" >'.number_format($data[$i]['public']).'</td>
					<td class="cs_r" >'.number_format($data[$i]['expenses_tot']).'</td>
					<td class="cs_r" >'.number_format($data[$i]['expenses']).'</td>
					<td class="cs_r" >'.number_format($data[$i]['bipay']).'</td>
					<td class="cs_r" >'.number_format($data[$i]['deposit']).'</td>
					<td class="cs_r" >'.number_format($data[$i]['unpaid']).'</td>
					<td class="cs_l" >';

					if ($data[$i]['suga'] > 0){
						$html .= '<span style=\'font-weight:bold;\'>[<a href=\'#\' onclick=\'showStatement('.$i.', "24ho");\'>명세서</a>]</span>&nbsp;&nbsp;';
						$html .= '<span style=\'font-weight:bold;\'>[<a href=\'#\' onclick=\'showStatement('.$i.',"detail");\'>영수증</a>]</span>';
					}else {
						$html .= '&nbsp;';
					}
		$html .= '	</td>
				  </tr>';
	}

	unset($data);

	$html .= '	</tbody>
		  </table>
		  <input id=\'code\' name=\'code\' type=\'hidden\' value=\''.$code.'\'>
		  <input id=\'key\' name=\'key\' type=\'hidden\' value=\''.$key.'\'>
		  <input id=\'month\' name=\'month\' type=\'hidden\' value=\'\'>';
	
	echo $html;

	unset($html);

	$conn->close();
?>