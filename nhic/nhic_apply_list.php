<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code   = $_POST['code']; //기관코드
	$date   = $_POST['date']; //등록일
	$seq    = $_POST['seq'];  //순번
	$min_dt = str_replace('-', '', $_POST['min_dt']);
	$max_dt = str_replace('-', '', $_POST['max_dt']);
	$c_cd	= $ed->de($_POST['c_cd']);
	$m_cd	= $ed->de($_POST['m_cd']);

	ob_start();

	echo '<table class=\'my_table\' style=\'width:100%;\'>
			<colgroup>
				<col width=\'40px\'>
				<col width=\'70px\'>
				<col width=\'60px\'>
				<col width=\'100px\'>
				<col width=\'70px\'>
				<col width=\'50px\' span=\'4\'>
				<col width=\'50px\' span=\'3\'>
				<col width=\'40px\' span=\'2\'>
				<col>
			</colgroup>
			<tbody>';


	/*********************************************************

		QUERY
		- LOG의 내용중 일정에서 삭제되지 않은
		  DATA만 추출한다.

	*********************************************************/
	$sql = 'select mst_dt as mst_dt
			,      mst_seq as mst_seq
			,      nhic_seq as mst_no
			,      nhic_kind as kind
			,      nhic_svc_cd as svc_cd
			,      nhic_c_cd as c_cd
			,      nhic_c_nm as c_nm
			,      nhic_m_nm1 as m_nm1
			,      nhic_m_nm2 as m_nm2
			,      nhic_dt as dt
			,      nhic_plan_from as p_from
			,      nhic_plan_to as p_to
			,      nhic_plan_time as p_time
			,      nhic_plan_seq as p_seq
			,      nhic_status as status
			,      case nhic_status when \'1\' then \'완료\'
									when \'5\' then \'수행중\'
									when \'9\' then \'대기\'
									when \'0\' then \'미수행\' else \'-\' end as stat_nm
			,      nhic_conf_from as c_from
			,      nhic_conf_to as c_to
			,      nhic_conf_time as c_time
			,      nhic_suga_cd as suga_cd
			,      nhic_suga_nm as suga_nm
			,      nhic_suga_cost_t as suga_cost_tot
			,      nhic_apply_yn as apply_yn
			  from nhic_log
			 inner join t01iljung
			    on t01_ccode        = org_no
			   and t01_mkind        = nhic_kind
			   and t01_jumin        = nhic_c_cd
			   and t01_sugup_date   = nhic_dt
			   and t01_sugup_fmtime = nhic_plan_from
			   and t01_sugup_seq    = nhic_plan_seq
			   and t01_del_yn       = \'N\'
			 where org_no   = \''.$code.'\'
			   and mst_dt   = \''.$date.'\'
			   and mst_seq  = \''.$seq.'\'
			   and nhic_dt >= \''.$min_dt.'\'
			   and nhic_dt <= \''.$max_dt.'\'
			   and length(nhic_status) = 1';

	if (!empty($c_cd))
		$sql .= ' and nhic_c_cd = \''.$c_cd.'\'';

	if (!empty($m_cd))
		$sql .= ' and nhic_m_cd1 = \''.$m_cd.'\'';

	$sql .= ' order by dt, svc_cd, c_nm, m_nm1, m_nm2, p_from, p_to';


	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$key = 'mst_dt='	.$row['mst_dt']
			 .'&mst_seq='	.$row['mst_seq']
			 .'&mst_no='	.$row['mst_no'];

		if ($tmp_dt != substr(str_replace('-', '', $row['dt']), 0, 6)){
			/*********************************************************
				실적마감여부
			*********************************************************/
			$tmp_dt   = substr(str_replace('-', '', $row['dt']), 0, 6);
			$close_yn = $conn->get_closing_act($code, $tmp_dt);
		}

		echo '<tr>
				<td class=\'center\'><div class=\'center\'>'.($i+1).'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$conn->kind_name_svc($row['svc_cd']).'</div></td>
				<td class=\'center\'><div class=\'left\'>'.$row['c_nm'].'</div></td>
				<td class=\'center\'><div class=\'left\'>'.$row['m_nm1'].(!empty($row['m_nm2']) ? '/' : '').$row['m_nm2'].'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->dateStyle($row['dt'],'.').'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->timeStyle($row['p_from']).'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->timeStyle($row['p_to']).'</div></td>
				<td class=\'center\'><div class=\'right\'>'.$row['p_time'].'분</div></td>
				<td class=\'center\'><div class=\'center\'>'.$row['stat_nm'].'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->timeStyle($row['c_from']).'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->timeStyle($row['c_to']).'</div></td>
				<td class=\'center\'><div class=\'right\'>'.$row['c_time'].'분</div></td>
				<td class=\'center\'><div class=\'center\'>';

		if ($close_yn == 'Y'){
			echo '<input name=\'check[]\' type=\'checkbox\' value=\''.$key.'\' class=\'checkbox\' disabled=\'true\'></div>';
		}else{
			echo '<input name=\'check[]\' type=\'checkbox\' value=\''.$key.'\' class=\'checkbox\' '.($row['apply_yn'] != 'Y' ? 'checked' : '').'></div>';
		}

		echo '	</td>
				<td class=\'center\'><div class=\'center bold\' style=\'color:'.($row['apply_yn'] == 'Y' ? '#0000ff': '#000000').';\'>'.$row['apply_yn'].'</td>';

		if ($close_yn == 'Y'){
			echo '	<td class=\'center last\'><div class=\'left bold\' style=\'color:#ff0000;\'>실적등록마감</div></td>';
		}else{
			echo '	<td class=\'center last\'><div class=\'left\'>'.$row['suga_nm'].'['.number_format($row['suga_cost_tot']).']'.'</div></td>';
		}

		echo '</tr>';
	}

	$conn->row_free();


	echo '	</tbody>
		  </table>';

	$html = ob_get_contents();

	ob_end_clean();

	echo $html;

	include_once('../inc/_db_close.php');
?>