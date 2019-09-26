<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$month = (intval($month) < 10 ? '0': '').intval($month);

	if ($_SESSION['userStmar'] == 'M'){
		$member = 'all';
	}else{
		$member = $_SESSION['userSSN'];
	}
?>
<table class="my_table tmp_6" style="width:100%;">
	<colgroup>
		<col width="15%">
		<col width="14%" span="4">
		<col width="15%">
	</colgroup>
	<thead>
		<tr>
			<th class="head bold" style="color:#ff0000;">일</th>
			<th class="head bold" style="color:#000000;">월</th>
			<th class="head bold" style="color:#000000;">화</th>
			<th class="head bold" style="color:#000000;">수</th>
			<th class="head bold" style="color:#000000;">목</th>
			<th class="head bold" style="color:#000000;">금</th>
			<th class="head bold last" style="color:#0000ff;">토</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select t01_mkind as kind
				,      t01_svc_subcode as svc_cd
				,      t01_jumin as client_ssn
				,      m03_name as client_nm
				,      t01_sugup_date as sugup_dt
				,      t01_sugup_fmtime as sugup_from
				,      t01_sugup_totime as sugup_to
				,      t01_status_gbn as stat
				,      t01_mem_cd1 as mem_ssn1
				,      t01_mem_nm1 as mem_nm1
				,      t01_mem_cd2 as mem_ssn2
				,      t01_mem_nm2 as mem_nm2
				  from t01iljung
				 inner join m03sugupja
					on m03_ccode         = t01_ccode
				   and m03_mkind         = t01_mkind
				   and m03_jumin         = t01_jumin
				 where t01_ccode         = '$code'
				   and t01_sugup_date like '$year$month%'
				   and t01_del_yn        = 'N'";

		if ($member == 'all'){
		}else{
			$sql .= "  and t01_mem_cd1 = '$member'
					 union all
					select t01_mkind as kind
					,      t01_svc_subcode as svc_cd
					,      t01_jumin as client_ssn
					,      m03_name as client_nm
					,      t01_sugup_date as sugup_dt
					,      t01_sugup_fmtime as sugup_from
					,      t01_sugup_totime as sugup_to
					,      t01_status_gbn as stat
					,      t01_mem_cd1 as mem_ssn1
					,      t01_mem_nm1 as mem_nm1
					,      t01_mem_cd2 as mem_ssn2
					,      t01_mem_nm2 as mem_nm2
					  from t01iljung
					 inner join m03sugupja
						on m03_ccode         = t01_ccode
					   and m03_mkind         = t01_mkind
					   and m03_jumin         = t01_jumin
					 where t01_ccode         = '$code'
					   and t01_sugup_date like '$year$month%'
					   and t01_mem_cd2       = '$member'
					   and t01_del_yn        = 'N'";
		}

		$sql .= " order by sugup_dt, sugup_from, sugup_to, svc_cd";

		$kind_list = $conn->kind_list($code, true);
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$index  = sizeof($iljung[$row['sugup_dt']]);
			$svc_nm = $conn->kind_name_sub($conn->kind_name_svc($row['svc_cd']));

			$iljung[$row['sugup_dt']][$index] = array('svc_cd'=>$row['svc_cd'],'svc_nm'=>$svc_nm,'client'=>$row['client_nm'],'from_time'=>$row['sugup_from'],'to_time'=>$row['sugup_to'],'mem_m'=>$row['mem_nm1'],'mem_s'=>$row['mem_nm2'],'stat'=>$row['stat']);
		}

		$conn->row_free();

		$time       = mkTime(0, 0, 1, $month, 1, $year);
		$today      = date("Ymd", mktime());
		$last_day   = date("t", $time); //총일수 구하기
		$week_start = date("w", strtotime(date("Y-m", $time)."-01")); //시작요일 구하기
		$week_total = ceil(($last_day + $week_start) / 7); //총 몇 주인지 구하기
		$week_last  = date('w', strtotime(date("Y-m", $time)."-".$last_day)); //마지막 요일 구하기
		$day        = 1;

		for($i=1; $i<=$week_total; $i++){
			if ($i == $week_total)
				$class = 'top center bottom ';
			else
				$class = 'top center ';

			echo '<tr>';
			for ($j=0; $j<7; $j++){
				if ($j == 6) $class .= 'last';

				switch($j){
					case 0:
						$style = 'color:#ff0000;';
						break;
					case 6:
						$style = 'color:#0000ff;';
						break;
					default:
						$style = 'color:#000000;';
				}

				$style .= 'font-size:11px;';

				$str_dt = $year.$month.($day < 10 ? '0' : '').$day;

				if ($today == $str_dt) $style .= 'background-color:#ffffff;';

				echo '<td class=\''.$class.'\' style=\''.$style.'\'>';

				if (!(($i == 1 && $j < $week_start) || ($i == $week_total && $j > $week_last))){
					echo '<div class=\'left bold\'>'.$day.'</div>';
					echo '<div class=\'left\' style=\'color:#000000;\'>';

					$k_arr = $iljung[$str_dt];
					$k_cnt = sizeof($k_arr);

					for($k=0; $k<$k_cnt; $k++){
						if ($member == 'all'){
							echo '['.$k_arr[$k]['svc_nm'].']'.$k_arr[$k]['mem_m'];

							if ($k_arr[$k]['svc_cd'] == '500'){
								echo '[정] '.$myF->timeStyle($k_arr[$k]['from_time']).'<br>';
								echo '['.$k_arr[$k]['svc_nm'].']'.$k_arr[$k]['mem_s'].'[부]';
							}

							echo ' '.$myF->timeStyle($k_arr[$k]['from_time']);
						}else{
							echo '['.$k_arr[$k]['svc_nm'].']'.$k_arr[$k]['client'].' '.$myF->timeStyle($k_arr[$k]['from_time']).'~'.$myF->timeStyle($k_arr[$k]['to_time']);
						}

						echo '<br>';
					}

					unset($k_arr);

					echo '</div>';

					$day++;
				}
				echo '</td>';
			}
			echo '</tr>';
		}
	?>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>