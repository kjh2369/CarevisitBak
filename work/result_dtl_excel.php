<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$code		= $_REQUEST['code'];
	$kind		= $_REQUEST['kind'];
	$year		= $_REQUEST['year'];
	$month		= $_REQUEST['month'];
	$day		= $_REQUEST['day'];
	$page		= $_REQUEST['page'];
	$find_name	= $_REQUEST['find_name'];
	$jumin		= $ed->de($_REQUEST['jumin']);
	$mode		= $_REQUEST['mode'];
	$plan_time_tot = $_REQUEST['plan_time_tot'];
	$conf_time_tot = $_REQUEST['conf_time_tot'];
	$plan_amt_tot = $_REQUEST['plan_amt_tot'];
	$conf_amt_tot = $_REQUEST['conf_amt_tot'];
	
	/*
	 * mode 설정
	 * 1 : 일실적등록(수급자)
	 * 2 : 월실적등록(수급자)
	 * 3 : 월실적등록(요양보호사)
	 */
	

	$wsl = " where t01_ccode = '$code'
			   and t01_del_yn = 'N'";

	switch($mode){
	case 1: //일실적등록
		$before_uri	= 'result_day.php';
		$title		= '일실적등록(수급자)';
		$find_date	= $year.$month.$day;
		$wsl	   .= " and t01_sugup_date like '$find_date%'";
		break;
	case 2: //월실적등록
		$before_uri	= 'result_month.php';
		$title		= '월실적등록(수급자)';
		$client_nm	= $conn->client_name($code, $jumin, $kind);
		$find_date	= $year.$month;
		$wsl       .= " and t01_sugup_date like '$find_date%'
		                and t01_jumin         = '$jumin'";
		break;
	default:
		echo $myF->message('err1', 'Y', 'Y');
		exit;
	}
	

	/************************************************
		근무한 일수를 구한다.
	************************************************/
	if ($mode == 2){
		$sql = "select count(distinct t01_sugup_date)
				  from t01iljung $wsl";

		$plan_day_cnt = $conn->get_data($sql);

		$sql = "select count(distinct t01_conf_date)
				  from t01iljung $wsl
				   and t01_status_gbn = '1'";

		$conf_day_cnt = $conn->get_data($sql);
	}
	
	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$code'";
	$c_name = $conn -> get_data($sql);

	$r_dt = date('Y.m.d',mktime());
?>
<style>
	.head {
		background-color:#eeeeee;
		font-family:굴림;
	}

	.left {
		text-align:left;
		font-family:굴림;
	}

	.center {
		text-align:center;
		font-family:굴림;
	}
	
	.right {
		text-align:right;
		font-family:굴림;
	}

</style>
<div align="center" style="font-size:15pt; font-weight:bold;"><?=$year?>년<?=$month?>월 월실적등록(수급자)</div>
<table>
	<tr>	
		<td colspan="4" style="text-align:left; font-size:12pt; font-weight:bold;">센터명  : <?=$c_name?></td><?
		if($mode == 1){ ?>
			<td colspan="4" style="text-align:right; font-size:12pt; font-weight:bold;">일자 : <?=$r_dt?></td><?
		} ?>
	</tr>
	<tr><td></td></tr>
	<tr><?
		if($mode == 2){ ?>
			<td colspan="6" style="text-align:left; font-size:12pt; font-weight:bold;">수급자명 : <?=$client_nm?></td>
			<td colspan="4" style="text-align:right; font-size:12pt; font-weight:bold;">일자 : <?=$r_dt?></td><?
		}?>
		
	</tr>
</table>
<?
	// 요양보호사 월실적
	if ($mode == 2){
		if ($kind == '0'){
			/*********************************************************
				재가요양
			*********************************************************/
			include_once('result_detail_care_excel.php');
		}

		/*********************************************************
			장애인활동지원
		*********************************************************/
		include_once('result_detail_dis_excel.php');

		/*********************************************************
			바우처 및 기타유료
		*********************************************************/
		include_once('result_detail_other_excel.php');
	}
?>

<div style="font-weight:bold;" >서비스 내역</div>

<table id="tblDtl" class="my_table" border="1">
	<colgroup>
		<?
			if ($mode == 1){?>
				<col width="70px"><?
			}
		?>
		<col width="60px">
		<col width="110px">
		<col width="150px">
		<col>
		<col width="70px">
		<col width="70px">
		<?
			if ($mode == 1 || $mode == 2){?>
				<col width="70px"><?
			}
		?>
		<col width="100px">
	</colgroup>
	<thead>
		<tr>
			<?
				if ($mode == 1){?>
					<th class="head">수급자</th><?
				}
			?>
			<th class="head">일자</th>
			<th class="head">계획</th>
			<th class="head">실적</th>
			<th class="head">제공서비스</th>
			<th class="head">실적급여액</th>
			<th class="head">계획급여액</th>
			<?
				if ($mode == 1 || $mode == 2){?>
					<th class="head">담당자</th><?
				}
			?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="text-align:center;" <? if($mode == 1 || $mode == 3){?>colspan="2"<?} ?>>계</td>
			<td style="text-align:center;" ><?=$plan_time_tot?></td>
			<td style="text-align:center;" ><?=$conf_time_tot?></td>
			<td >
			<?
				if ($mode == 2){
					if ($kind == '0'){
						/*********************************************************
							재가한도금액
						*********************************************************/
						if ($lbTestMode){
							$li_limitPay = $liLimitAmt;
						}else{
							//수급자 등급
							$sql = 'select lvl
									  from (
										   select m03_ylvl as lvl
										   ,      m03_sdate as f_dt
										   ,      m03_edate as t_dt
											 from m03sugupja
											where m03_ccode = \''.$code.'\'
											  and m03_mkind = \'0\'
											  and m03_jumin = \''.$jumin.'\'
											union all
										   select m31_level
										   ,      m31_sdate
										   ,      m31_edate
											 from m31sugupja
											where m31_ccode = \''.$code.'\'
											  and m31_mkind = \'0\'
											  and m31_jumin = \''.$jumin.'\'
										   ) as t
									 where left(f_dt, 6) <= \''.$find_date.'\'
									   and left(t_dt, 6) >= \''.$find_date.'\'';

							$li_lvl = $conn->get_data($sql);

							//한도금액
							$li_limitPay = $conn->_limit_pay($li_lvl, $find_date);
						}

						echo '재가한도금액 : <span id=\'careLimitPay\' style=\'font-weight:bold;\'>'.number_format($li_limitPay).'</span>';
					}else{
						echo '&nbsp;';
					}
				}
			?>
			</td>
			<td class="right" ><?=$conf_amt_tot?></td>
			<td class="right" ><?=$plan_amt_tot?></td>
			<td class="center"></td>
		</tr>
	</tbody>
	<tbody>
	<?
	
		$today    = date('Ymd', mktime());
		$limit_dt = substr($today,0,6).'15';

		$sql = "select t01_mkind
				,      t01_jumin
				,      m03_name
				,      m03_key
				,      t01_sugup_date
				,      t01_sugup_fmtime
				,      t01_sugup_totime
				,      t01_sugup_soyotime
				,      t01_sugup_seq
				,      t01_suga_tot
				,      t01_wrk_fmtime
				,      t01_wrk_totime
				,      t01_conf_date
				,      t01_conf_fmtime
				,      t01_conf_totime
				,      t01_conf_soyotime
				,      t01_conf_suga_code
				,      t01_conf_suga_value
				,      t01_status_gbn
				,      t01_svc_subcode
				,      t01_yoyangsa_id1
				,      t01_yoyangsa_id2
				,      t01_yname2
				,      t01_mem_cd1
				,      t01_mem_nm1
				,      t01_mem_cd2
				,      t01_mem_nm2
				,      t01_modify_yn
				,      t01_holiday
				,      t01_toge_umu
				,      t01_bipay_umu
				,      m02_yname as worker
				,      case when date_format(now(), '%Y%m%d') >= t01_sugup_date then 'Y' else 'N' end as day_yn
				,      ifnull(act_cls_flag, 'N') as act_yn
				,      ifnull(closing_progress.act_bat_conf_flag, 'N') as conf_yn
				,      t01_bipay_kind as bipay_kind
				,      t01_bipay1 as bipay1
				,      t01_bipay2 as bipay2
				,      t01_bipay3 as bipay3
				  from t01iljung
				 inner join m03sugupja
					on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				  left join m02yoyangsa
				    on m02_ccode  = t01_ccode
				   and m02_mkind  = ".$conn->_mem_kind()."
				   and m02_yjumin = t01_yoyangsa_id1
				   and m02_del_yn = 'N'
				  left join closing_progress
				    on org_no       = t01_ccode
				   and closing_yymm = left(t01_sugup_date, 6) $wsl
				 order by m03_name, t01_sugup_date, case when t01_sugup_fmtime != '' then t01_sugup_fmtime else '9999' end, t01_sugup_totime";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if (!$closing_msg){
				if ($row['conf_yn'] == 'Y'){
					$closing_msg = '※ '.$year.'년 '.intval($month).'월은 실적등록이 완료되어 수정이 불가능합니다.';
				}else if ($row['act_yn'] == 'Y'){
					$closing_msg = '※ '.$year.'년 '.intval($month).'월은 실적등록이 마감되어 수정이 불가능합니다.';
				}else{
					$closing_msg = '';
				}
			}

			if ($today >= $row['t01_sugup_date'] || $today >= $limit_dt){
				$modify = 1;
			}else{
				$modify = 2;
			}

			// 수가정보
			$suga_name = $conn->get_suga($code, $row['t01_conf_suga_code'], $row['t01_sugup_date']); //수가명

			if ($row['t01_bipay_umu'] == 'Y'){
				$suga_name .= '[<span style=\'color:#ff0000;\'>비</span>]';
			}

			// 실적급여액
			if ($row['t01_status_gbn'] == '1'){
				$suga_value = number_format($row['t01_conf_suga_value']); //수가단가
				$conf_from  = $myF->timeStyle($row['t01_conf_fmtime']);
				$conf_to    = $myF->timeStyle($row['t01_conf_totime']);
				$conf_time  = $row['t01_conf_soyotime'];
				$font_color = '#000000';
				//$plan_copy  = 'N';
			}else{
				$conf_from  = ''; //$myF->timeStyle($row['t01_wrk_fmtime']);
				$conf_to    = ''; //$myF->timeStyle($row['t01_wrk_totime']);
				$conf_time  = '0'; //$myF->dateDiff('n', $conf_from, $conf_to);
				$suga_value = '0';
				//$plan_copy  = 'Y';

				if ($conf_time < 0){
					$conf_time = 24 * 60 + $conf_time;
					$font_color = '#ff0000';
				}else{
					if ($plan_copy == 'N'){
						$font_color = '#000000';
					}else{
						if ($row['t01_status_gbn'] == 'C'){
							$conf_from  = $myF->timeStyle($row['t01_wrk_fmtime']);
							$conf_to    = $myF->timeStyle($row['t01_wrk_totime']);
							$font_color = '#ff0000';
						}else{
							$font_color = '#0000ff';
						}
					}
				}
			}

			$plan_copy  = 'Y';

			if ($row['day_yn']  != 'Y') $plan_copy = 'N';
			if ($modify == 1)           $plan_copy = 'Y';
			if ($row['act_yn']  == 'Y') $plan_copy = 'N';
			if ($row['conf_yn'] == 'Y') $plan_copy = 'N';

			// 계획수가
			$plan_value = number_format($row['t01_suga_tot']);
			
			//담당자
			$worker = $row['worker'];					

			if ($temp_client != $row['t01_jumin']){
				if (!empty($temp_client)){
					$tmp_seq ++;

					$html[$temp_index_1][0] = '<td rowspan=\''.$temp_count_1.'\' class=\'left\'>'.$temp_name.'</td>';
				}

				$temp_client = $row['t01_jumin'];
				$temp_name   = $row['m03_name'];
				$temp_index_1= $i;

				$temp_count_1 = 1;
			}else{
				$temp_count_1 ++;
				//$temp_name = '';
				
				if ($mode == 1 ){
					$html[$i][0] = '';
				}
			}

			if ($temp_client_date != $row['t01_jumin'].'_'.$row['t01_sugup_date']){
				if (!empty($temp_client_date)){
					$html[$temp_index_2][1] = '<td rowspan=\''.$temp_count_2.'\' class=\'center\'>'.$temp_date.'</td>';
				}

				$temp_client_date  = $row['t01_jumin'].'_'.$row['t01_sugup_date'];
				$temp_date   = date('d', strtotime($myF->dateStyle($row['t01_sugup_date']))).'일('.$myF->weekday($row['t01_sugup_date']).')';
				$temp_index_2= $i;

				$temp_count_2 = 1;
			}else{
				$temp_count_2 ++;
				//$temp_date = '';
				$html[$i][1] = '';
			}

			if (!empty($row['t01_sugup_fmtime']) && !empty($row['t01_sugup_totime'])){
				$str_time = $myF->timeStyle($row['t01_sugup_fmtime']).'-'.$myF->timeStyle($row['t01_sugup_totime']).' ('.$row['t01_sugup_soyotime'].'분)';
			}else{
				$str_time = '&nbsp;';
			}
			
			if (!empty($conf_from) && !empty($conf_to)){
				$str2_time = $conf_from.'-'.$conf_to.' ('.$conf_time.'분)';
			}else{
				$str2_time = '&nbsp;';
			}

			//echo '<tr style=\'\'>';

				/*********************************************************
					장애인활동지원
				*********************************************************/
				if ($row['t01_mkind'] == '4' &&
					$row['t01_svc_subcode'] == '200' &&
					$row['t01_bipay_umu'] == 'Y'){
					$rowspan = 1;
				}else{
					$rowspan = 1;
				}
				
				$html[$i][2] = '<td class="left">'.$str_time.'</td>';
				$html[$i][3] = '<td class="left">'.$str2_time.'</td>';
				$html[$i][4] = '<td class="left">'.$suga_name.'</td>';
				$html[$i][5] = '<td class="right">'.$suga_value.'</td>';
				$html[$i][6] = '<td class="right">'.$plan_value.'</td>';
				$html[$i][7] = '<td class="right">'.$worker.'</td>';
		
			//echo '</tr>';

		}
		$tmp_seq ++;

		$html[$temp_index_1][0] = '<td rowspan=\''.$temp_count_1.'\' class=\'left\'>'.$temp_name.'</td>';
		$html[$temp_index_2][1] = '<td rowspan=\''.$temp_count_2.'\' class=\'center\'>'.$temp_date.'</td>';
	
		$html_cnt = sizeof($html);

		for($i=0; $i<$html_cnt; $i++){
			echo '<tr>';
			
			if(($mode == 1 ) && $temp_name != ''){
				echo $html[$i][0]; 
			}
			
			echo $html[$i][1];
			echo $html[$i][2];
			echo $html[$i][3];
			echo $html[$i][4];
			echo $html[$i][5];
			echo $html[$i][6];
			echo $html[$i][7];

			echo '</tr>';
		}

		$conn->row_free();

		unset($ctl_bipay_if);
	?>
	</tbody>
</table>