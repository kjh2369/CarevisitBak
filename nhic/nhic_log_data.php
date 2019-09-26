<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$date = $_POST['date'];
	$seq  = $_POST['seq'];

	$from_dt = str_replace('-', '', $_POST['from_dt']);
	$to_dt   = str_replace('-', '', $_POST['to_dt']);
	$log_gbn = $_POST['log_gbn'];


	echo '<table class=\'my_table\' style=\'width:100%;\'>
			<colgroup>
				<col width=\'40px\'>
				<col width=\'30px\'>
				<col width=\'60px\'>
				<col width=\'50px\'>
				<col width=\'100px\'>
				<col width=\'30px\'>
				<col width=\'70px\'>
				<col width=\'50px\' span=\'6\'>
				<col width=\'30px\' span=\'2\'>
				<col width=\'45px\' span=\'4\'>
				<col>
			</colgroup>
			<tbody>';


	/*********************************************************

		LOG 조회

	*********************************************************/
	$sql  = 'select nhic_claim_yn as claim_yn
			,       nhic_svc_cd as svc_cd
			,       nhic_c_nm as c_nm
			,       nhic_m_nm1 as m_nm1
			,       nhic_m_nm2 as m_nm2
			,       nhic_join_yn as join_yn
			,       nhic_dt as dt
			,       nhic_plan_from as p_from
			,       nhic_plan_to as p_to
			,       nhic_plan_time as p_time
			,       nhic_conf_from as c_from
			,       nhic_conf_to as c_to
			,       nhic_conf_time as c_time
			,       nhic_90_yn as 90_yn
			,       nhic_family_yn as family_yn
			,       nhic_status as stat
			   from nhic_log
			  where org_no   = \''.$code.'\'
				and mst_dt   = \''.$date.'\'
				and mst_seq  = \''.$seq.'\'
				and nhic_dt >= \''.$from_dt.'\'
				and nhic_dt <= \''.$to_dt.'\'';

	if ($log_gbn == '1'){
		$sql .= ' and length(nhic_status) = 1';
	}else{
		$sql .= ' and length(nhic_status) > 1';
	}


	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		echo '<tr>
				<td class=\'center\'><div class=\'center\'>'.($i+1).'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$row['claim_yn'].'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$conn->kind_name_svc($row['svc_cd']).'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$row['c_nm'].'</div></td>
				<td class=\'center\'><div class=\'left\'>'.$row['m_nm1'].(!empty($row['m_nm2']) ? '/' : '').$row['m_nm2'].'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$row['join_yn'].'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->dateStyle($row['dt'], '.').'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->timeStyle($row['p_from']).'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->timeStyle($row['p_to']).'</div></td>
				<td class=\'center\'><div class=\'right\'>'.$row['p_time'].'분</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->timeStyle($row['c_from']).'</div></td>
				<td class=\'center\'><div class=\'center\'>'.$myF->timeStyle($row['c_to']).'</div></td>
				<td class=\'center\'><div class=\'right\'>'.$row['c_time'].'분</div></td>
				<td class=\'center\'><div class=\'center\'>'.$row['90_yn'].'</div></td>
				<td class=\'center\'><div class=\'center\'>'.($row['family_yn'] == 'Y' ? 'Y' : 'N').'</div></td>
				<td class=\'center\'><div class=\'center\'>'.(is_numeric(strpos($row['stat'],'E1/')) ? 'Y' : '').'</div></td>
				<td class=\'center\'><div class=\'center\'>'.(is_numeric(strpos($row['stat'],'E2/')) ? 'Y' : '').'</div></td>
				<td class=\'center\'><div class=\'center\'>'.(is_numeric(strpos($row['stat'],'E3/')) ? 'Y' : '').'</div></td>
				<td class=\'center\'><div class=\'center\'>'.(is_numeric(strpos($row['stat'],'E4/')) ? 'Y' : '').'</div></td>
				<td class=\'center last\'><div class=\'left\'>&nbsp;</div></td>
			  </tr>';
	}

	$conn->row_free();


	if (empty($row_count)){
		echo '<tr>
				<td class=\'center last\'><div class=\'center\'>'.$myF->message('nodata','N').'</div></td>
			  </tr>';
	}


	echo '	</tbody>
		  </table>';

	include_once('../inc/_db_close.php');
?>