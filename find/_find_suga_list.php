<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code      = $_POST['code'];
	$svcCD     = $_POST['svcCD'];
	$date      = $_POST['date'];
	$name      = $_POST['name'];
	$over270YN = $_POST['over270YN'];

	switch($svcCD){
		case '200':
			$sugaCD = 'CCWS';
			break;

		case '500':
			$sugaCD = 'CB';
			break;

		case '800':
			$sugaCD = 'CNWS';
			break;
	}

	if (empty($date)) $date = date('Ymd', mktime());

	$date = str_replace('-', '', $date);


	$html = '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'40px\'>
					<col width=\'70px\'>
					<col width=\'130px\'>
					<col width=\'90px\'>
					<col>
				</colgroup>
				<tbody>';

	$sql = 'select suga_cd
			,      suga_nm
			,      suga_cost
			,      c_time
			,      from_dt
			,      to_dt
			  from (
				   select m01_mcode2 as suga_cd
				   ,      m01_scode as temp_cd
				   ,      m01_suga_cont as suga_nm
				   ,      m01_suga_value as suga_cost
				   ,	  m01_calc_time as c_time
				   ,      m01_sdate as from_dt
				   ,      m01_edate as to_dt
				     from m01suga
					where m01_mcode = \'goodeos\'';

	if ($over270YN == 'N'){
		$sql .= ' and m01_mcode2 != \'CCHS9\'
				  and m01_mcode2 != \'CCWS9\'';
	}

	$sql .= '		union all
				   select m11_mcode2
				   ,      m11_scode
				   ,      m11_suga_cont
				   ,      m11_suga_value
				   ,	  m11_calc_time
				   ,      m11_sdate
				   ,      m11_edate
					 from m11suga
					where m11_mcode = \'goodeos\'';

	if ($over270YN == 'N'){
		$sql .= ' and m11_mcode2 != \'CCHS9\'
				  and m11_mcode2 != \'CCWS9\'';
	}

	$sql .= '	   ) as t
			 where from_dt <= \''.$date.'\'
			   and to_dt   >= \''.$date.'\'
			   and left(suga_cd, '.strlen($sugaCD).') = \''.$sugaCD.'\'';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$html .= '<tr>
					<td class=\'center\'>'.($i+1).'</td>
					<td class=\'center\'><div class=\'left\'><a href=\'#\' onclick=\'setItem("code='.$row['suga_cd'].'&name='.$row['suga_nm'].'&cost='.$row['suga_cost'].'&time='.$row['c_time'].'");\'>'.$row['suga_cd'].'</a></div></td>
					<td class=\'center\'><div class=\'left\'>'.$row['suga_nm'].'</div></td>
					<td class=\'center\'><div class=\'right\'>'.number_format($row['suga_cost']).'</div></td>
					<td class=\'center last\'><div class=\'left\'></div></td>
				  </tr>';
	}

	$conn->row_free();

	$html .= '	</tbody>
			  </table>';

	echo $html;

	include_once('../inc/_db_close.php');
?>