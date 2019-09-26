<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************

		파라메타
		- mode
		  - 1 : 등록리스트
		  - 2 : 오류리스트
		  - 3 : 등록리스트

	*********************************************************/
	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$month = (intval($month) < 10 ? '0' : '').intval($month);
	$mode  = $_POST['mode'];

	if ($mode == 1 ||
		$mode == 2){
		echo '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'40px\'>
					<col width=\'70px\'>
					<col width=\'50px\'>
					<col width=\'140px\'>
					<col width=\'50px\'>
					<col width=\'50px\'>
					<col width=\'50px\'>
					<col width=\'50px\'>
					<col width=\'50px\'>
					<col>
				</colgroup>
				<thead>
					<tr>
						<th class=\'head\' rowspan=\'2\'>No</th>
						<th class=\'head\' rowspan=\'2\'>등록일</th>
						<th class=\'head\' rowspan=\'2\'>회차</th>
						<th class=\'head\' rowspan=\'2\'>일정기간</th>
						<th class=\'head\' rowspan=\'2\'>정상</th>
						<th class=\'head\' colspan=\'4\'>에러LOG</th>
						<th class=\'head last\' rowspan=\'2\'>비고</th>
					</tr>
					<tr>
						<th class=\'head\'>수급자</th>
						<th class=\'head\'>요양사</th>
						<th class=\'head\'>계획</th>
						<th class=\'head\'>실적</th>
					</tr>
				</thead>
				<tbody>';
	}else if ($mode == 3){
		echo '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'70px\'>
					<col width=\'40px\'>
					<col>
				</colgroup>
				<tbody>';
	}

	$sql = 'select nhic_dt
			,      nhic_seq
			,      nhic_from
			,      nhic_to
			  from nhic_log_mst
			 where org_no           = \''.$code.'\'
			   and left(nhic_dt, 7) = \''.$year.'-'.$month.'\'
			 order by nhic_dt desc, nhic_seq desc';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	if (!empty($row_count)){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';

			if ($mode == 1 ||
				$mode == 2){
				echo '	<td class=\'center\'>'.($i+1).'</td>
						<td class=\'center\'>'.$myF->dateStyle($row['nhic_dt'], '.').'</td>';
			}else{
				echo '	<td class=\'center\'><a href=\'#\' onclick=\'get_reg_dt("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'","'.$myF->dateStyle($row['nhic_from']).'","'.$myF->dateStyle($row['nhic_to']).'");\'>'.$myF->dateStyle($row['nhic_dt'], '.').'</a></td>';
			}

			echo '	<td class=\'center\'>'.$row['nhic_seq'].'</td>
					<td class=\'center\'>'.$myF->dateStyle($row['nhic_from'], '.').'~'.$myF->dateStyle($row['nhic_to'], '.').'</td>';

			switch($mode){
				case 1:
					echo '<td class=\'left last\'>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'show_apply_list("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'");\'>등록 리스트</button></span>
						  </td>';
					break;

				case 2:
					$sql = 'select sum(case when length(nhic_status) = 1 then 1 else 0 end) as cnt1
							,      sum(case when length(nhic_status) > 1 and length(nhic_status) != length(replace(nhic_status, \'E1/\', \'\')) then 1 else 0 end) as cnt2
							,      sum(case when length(nhic_status) > 1 and length(nhic_status) != length(replace(nhic_status, \'E2/\', \'\')) then 1 else 0 end) as cnt3
							,      sum(case when length(nhic_status) > 1 and length(nhic_status) != length(replace(nhic_status, \'E3/\', \'\')) then 1 else 0 end) as cnt4
							,      sum(case when length(nhic_status) > 1 and length(nhic_status) != length(replace(nhic_status, \'E4/\', \'\')) then 1 else 0 end) as cnt5
							  from nhic_log
							 where org_no   = \''.$code.'\'
							   and mst_dt   = \''.$row['nhic_dt'].'\'
							   and mst_seq  = \''.$row['nhic_seq'].'\'';

					$logCnt = $conn->get_array($sql);

					echo '<td class=\'right\'>'.number_format($logCnt['cnt1']).'</td>
						  <td class=\'right bold\' style=\'color:#ff0000;\'>'.number_format($logCnt['cnt2']).'</td>
						  <td class=\'right bold\' style=\'color:#ff0000;\'>'.number_format($logCnt['cnt3']).'</td>
						  <td class=\'right bold\' style=\'color:#ff0000;\'>'.number_format($logCnt['cnt4']).'</td>
						  <td class=\'right bold\' style=\'color:#ff0000;\'>'.number_format($logCnt['cnt5']).'</td>
						  <td class=\'left last\'>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'show_log_list("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'","");\'>정상</button></span>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'show_log_list("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'","E");\'>에러</button></span>
						  </td>';

					/*
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'show_log_list("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'","");\'>정상</button></span>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'show_log_list("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'","E1");\'>수급자</button></span>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'show_log_list("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'","E2");\'>요양보호사</button></span>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'show_log_list("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'","E3");\'>계획</button></span>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'show_log_list("'.$row['nhic_dt'].'","'.$row['nhic_seq'].'","E4");\'>실적</button></span>
					 */

					unset($logCnt);
					break;
			}

			echo '</tr>';
		}
	}else{
		echo '<tr>
				<td class=\'center last\' colspan=\'5\'>'.$myF->message('nodata', 'N').'</td>
			  </tr>';
	}

	$conn->row_free();

	echo '	</tbody>
		  </table>';

	include_once('../inc/_db_close.php');
?>