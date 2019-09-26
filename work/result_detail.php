<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_mySuga.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	$code		= $_REQUEST['code'];
	$kind		= $_REQUEST['kind'];
	$year		= $_REQUEST['year'];
	$month		= $_REQUEST['month'];
	$day		= $_REQUEST['day'];
	$page		= $_REQUEST['page'];
	$find_name	= $_REQUEST['find_name'];
	$jumin		= $ed->de($_REQUEST['jumin']);
	$mode		= $_REQUEST['mode'];
	$order = $_POST['order'] != '' ? $_POST['order'] : '1'; //정렬

	#if ($debug){
	#	//print_r($conn->_find_suga_('31138000103','200','20120704','1253','2100','487'));
	#	print_r($conn->_find_suga_('31138000103','200','20120704','1553','2034','281'));
	#	echo '<br>---------------------------------------------------------------------------------------<br>';
	#	print_r($mySuga->findSugaCare('31138000103', '200', '20120704', '1553', '2034'));
	#	echo '<br>---------------------------------------------------------------------------------------<br>';
	#	print_r($mySuga->findSugaCare('31138000103', '200', '20120704', '1343', '1824'));
	#	echo '<br>---------------------------------------------------------------------------------------<br>';
	#	print_r($mySuga->findSugaCare('31138000103', '200', '20120704', '1253', '2100'));
	#}

	#if ($debug){
	#	print_r($conn->_find_suga_('31150000001', '200', '20120507', '1747', '2207', '260'));
	#	echo '<br>---------------------------------------------------------------------------------------<br>';
	#	print_r($mySuga->findSugaCare('31150000001', '200', '20120507', '1747', '2207'));
	#}

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
	case 3:
		$before_uri	= 'result_month.php';
		$title		= '월실적등록(요양보호사)';
		$member_nm	= $conn->member_name($code, $jumin, $kind);
		$find_date	= $year.$month;
		$wsl       .= " and t01_sugup_date like '$find_date%'
		                and t01_yoyangsa_id1  = '$jumin'";
		break;
	default:
		echo $myF->message('err1', 'Y', 'Y');
		exit;
	}

	/************************************************
		근무한 일수를 구한다.
	************************************************/
	if ($mode == 2 || $mode == 3){
		$sql = "select count(distinct t01_sugup_date)
				  from t01iljung $wsl";

		$plan_day_cnt = $conn->get_data($sql);

		$sql = "select count(distinct t01_conf_date)
				  from t01iljung $wsl
				   and t01_status_gbn = '1'";

		$conf_day_cnt = $conn->get_data($sql);
	}



	/*********************************************************

		고객별 비급여 수가

	$sql = 'select m03_jumin as c_cd
			,      m03_mkind as kind
			,      m03_bipay1 as bipay1
			,      m03_bipay2 as bipay2
			,      m03_bipay3 as bipay3
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			 order by m03_jumin, m03_mkind';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$ctl_bipay_if[$row['c_cd']][$row['kind']] = array('200'=>$row['bipay1'], '500'=>$row['bipay2'], '800'=>$row['bipay3']);
	}

	$conn->row_free();
	*********************************************************/



	/**********************************************

		버튼그룹

	**********************************************/
	$btn_group = '<span class=\'btn_pack m icon\'><span class=\'save\'></span><button type=\'button\' onclick=\'save();\'>저장</button></span>
				  <span class=\'btn_pack m icon\'><span class=\'before\'></span><button type=\'button\' onclick=\'before();\'>이전</button></span>';


	$btn_group .= ' <span class=\'btn_pack m icon\'><span class=\'excel\'></span><button type=\'button\' onclick=\'excel();\'>엑셀</button></span>';


?>
<script src="../js/work.js" type="text/javascript"></script>
<script src="../js/iljung.js" type="text/javascript"></script>
<script src="../js/iljung.reg.js" type="text/javascript"></script>
<script src="../js/iljung.add.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function excel(){
	var f = document.f;

	f.plan_time_tot.value = document.getElementById("tot_plan_time").innerHTML;
	f.conf_time_tot.value = document.getElementById("tot_conf_time").innerHTML;
	f.plan_amt_tot.value  = document.getElementById("tot_plan_amt").innerHTML;
	f.conf_amt_tot.value  = document.getElementById("tot_conf_amt").innerHTML;

	f.action = "result_dtl_excel.php";
	f.submit();
}
function set_member(code, i, seq){
	if (seq == undefined) seq = '';

	/*
	var result = __find_member(code,'','','');

	if (result == null) return;

	var change = document.getElementsByName('change_flag[]')[i];
	var mem_cd = document.getElementsByName('conf_mem_cd'+seq+'[]')[i];
	var mem_nm = document.getElementsByName('conf_mem_nm'+seq+'[]')[i];

	mem_cd.value = result[0];
	mem_nm.value = result[1];
	change.value = 'Y';
	*/

	var mem_cd = document.getElementsByName('conf_mem_cd'+seq+'[]')[i];
	var mem_nm = document.getElementsByName('conf_mem_nm'+seq+'[]')[i];

	__find_yoyangsa(code, '', mem_cd, mem_nm);
}

function before(){
	var f = document.f;

	f.action = f.before_uri.value;
	f.submit();
}

function save(){
	alert('수정중입니다.');
	return;

	var f = document.f;
	var change_flag = false;
	var change      = document.getElementsByName('change_flag[]');
	var conf_from   = document.getElementsByName('conf_from[]');
	var conf_to     = document.getElementsByName('conf_to[]');

	for(var i=0; i<change.length; i++){
		if (change[i].value == 'Y'){
			change_flag = true;

			if (!checkDate(conf_from[i].value)){
				alert('시간입력 오류입니다. 확인하여 주십시오.');
				conf_from[i].focus();
				return;
			}

			if (!checkDate(conf_to[i].value)){
				alert('시간입력 오류입니다. 확인하여 주십시오.');
				conf_to[i].focus();
				return;
			}
		}
	}

	if (!change_flag){
		alert('수정된 일정이 없습니다.');
		return;
	}

	if (!confirm('수정하신 일정을 저장하시겠습니까?')) return;

	f.action = 'result_detail_save.php';
	f.submit();
}

function lfSearch(){
	var f = document.f;

	f.submit();
}
window.onload = function(){
	__init_form(document.f);

	if ($('#code').attr('value') == '1234'){
		_workLimitPayCheck();
	}
}

-->
</script>

<div class="title" style="width:auto; float:left;"><?=$title;?></div>
<div style="width:auto; font-weight:bold; margin-top:9px; text-align:right; color:#ff0000;" id="closing_msg">&nbsp;</div>

<form name="f" method="post">

<table class="my_table my_border">
	<colgroup>
		<col width="40px">
		<col width="50px">
		<?
			if ($mode == 2){?>
				<col width="60px">
				<col width="100px"><?
			}
		?>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td class="left"><?=$year;?>.<?=$month;?></td>
			<?
				if ($mode == 2){?>
					<th>수급자명</th>
					<td class="left"><?=$client_nm;?></td><?
				}else if ($mode == 3){?>
					<th>요양보호사</th>
					<td class="left"><?=$member_nm;?></td><?
				}
			?>
			<td class="right last"><?=$btn_group;?></td>
		</tr>
		<tr>
			<th>정렬</th>
			<td class='' colspan="4">
				<input id='order1' name='order' type='radio' value='1' class='radio' onclick='lfSearch();' <? if($order == '1'){ echo 'checked'; } ?> ><label for='order1'>수급자순별</label>
				<input id='order2' name='order' type='radio' value='2' class='radio' onclick='lfSearch();' <? if($order == '2'){ echo 'checked'; } ?> ><label for='order2'>계획시간순별</label>
				<input id='order3' name='order' type='radio' value='3' class='radio' onclick='lfSearch();' <? if($order == '3'){ echo 'checked'; } ?> ><label for='order3'>실적시간순별</label>
				<input id='order4' name='order' type='radio' value='4' class='radio' onclick='lfSearch();' <? if($order == '4'){ echo 'checked'; } ?> ><label for='order4'>담당자순별</label>
				<input id='order5' name='order' type='radio' value='5' class='radio' onclick='lfSearch();' <? if($order == '5'){ echo 'checked'; } ?> ><label for='order5'>제공서비스별</label>
			</td>
		</tr>
	</tbody>
</table>

<?
	// 요양보호사 월실적
	if ($mode == 2){
		if ($kind == '0'){
			/*********************************************************
				재가요양
			*********************************************************/
			if ($lbTestMode){
				include_once('result_detail_care_new.php');
			}else{
				include_once('result_detail_care.php');
			}
		}

		/*********************************************************
			장애인활동지원
		*********************************************************/
		include_once('result_detail_dis.php');

		/*********************************************************
			바우처 및 기타유료
		*********************************************************/
		include_once('result_detail_other.php');
	}
?>

<div class="title title_border">서비스 내역</div>

<table id="tblDtl" class="my_table" style="width:100%;">
	<colgroup>
		<?
			if ($mode == 1 || $mode == 3){?>
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
				if ($mode == 1 || $mode == 3){?>
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
			<th class="head last" id="btn_copy_all">
				<a href="#" onclick="_set_plan_to_conf('all'); return false;"><img src="../image/btn_copy_2.png"></a>
				<a href="#" onclick="_set_conf_to_cancel('all'); return false;"><img src="../image/btn_cancel_2.png"></a>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="border_b_gray right" <? if($mode == 1 || $mode == 3){?>colspan="2"<?} ?>>계</td>
			<td class="border_b_gray center" id="tot_plan_time"></td>
			<td class="border_b_gray center" id="tot_conf_time"></td>
			<td class="border_b_gray center">
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
			<td class="border_b_gray right" id="tot_conf_amt">0</td>
			<td class="border_b_gray right" id="tot_plan_amt">0</td>
			<td class="border_b_gray center"></td>
			<td class="border_b_gray last"></td>
		</tr>
	</tbody>
	<tbody>
	<?
		/*
		$today = date('Ymd', mktime());

		// 말일부터 근무일수 -3일을 구한다.
		$tmp_dt = $myF->lastDay(date('Y', mktime()),date('m', mktime()));
		$tmp_dt = date('Y-m-', mktime()).(intval($tmp_dt) < 10 ? '0' : '').intval($tmp_dt);

		$loop_cnt = 0;

		while(1){
			$tmp_weekday = date('w', strtotime($tmp_dt));

			if ($tmp_weekday > 0 && $tmp_weekday < 6){
				if ($loop_cnt > 2) break;

				$loop_cnt ++;
			}
			$tmp_dt = $myF->dateAdd('day', -1, $tmp_dt, 'Y-m-d');
		}

		$limit_dt = str_replace('-', '', $tmp_dt);
		*/
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
				   and closing_yymm = left(t01_sugup_date, 6) $wsl";

		if($order == '1'){
			$sql .=  " order by m03_name, t01_sugup_date, case when t01_sugup_fmtime != '' then t01_sugup_fmtime else '9999' end, t01_sugup_totime";
		}else if($order == '2'){
			$sql .=  " order by t01_sugup_date, case when t01_sugup_fmtime != '' then t01_sugup_fmtime else '9999' end, t01_sugup_totime";
		}else if($order == '3'){
			$sql .=  " order by t01_conf_date, case when t01_sugup_fmtime != '' then t01_sugup_fmtime else '9999' end, t01_sugup_totime";
		}else if($order == '4'){
			$sql .=  " order by m02_yname, t01_sugup_date, case when t01_sugup_fmtime != '' then t01_sugup_fmtime else '9999' end, t01_sugup_totime";
		}else if($order == '5'){
			$sql .=  " order by t01_conf_suga_code, t01_sugup_date, case when t01_sugup_fmtime != '' then t01_sugup_fmtime else '9999' end, t01_sugup_totime";
		}

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$row['act_yn'] = 'N';
			$row['conf_yn'] = 'N';

			/*
			if (!$closing_msg){
				if ($row['conf_yn'] == 'Y'){
					$closing_msg = '※ '.$year.'년 '.intval($month).'월은 실적등록이 완료되어 수정이 불가능합니다.';
				}else if ($row['act_yn'] == 'Y'){
					$closing_msg = '※ '.$year.'년 '.intval($month).'월은 실적등록이 마감되어 수정이 불가능합니다.';
				}else{
					$closing_msg = '';
				}
			}
			*/

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

			if ($temp_client != $row['t01_jumin']){
				if (!empty($temp_client)){
					/*
					echo '<script>
							document.getElementById(\'col_1_'.$temp_index_1.'\').setAttribute(\'rowSpan\', \''.$temp_count_1.'\');
						  </script>';
					*/
				}

				$temp_client = $row['t01_jumin'];
				$temp_name   = $row['m03_name'];
				$temp_index_1= $i;

				/*
				if ($row['t01_mkind'] == '4' &&
					$row['t01_svc_subcode'] == '200' &&
					$row['t01_bipay_umu'] == 'Y'){
					$temp_count_1 = 2;
				}else{
					$temp_count_1 = 1;
				}
				*/
				$temp_count_1 = 1;
			}else{
				$temp_count_1 ++;
				$temp_name = '';

				if ($mode == 1 || $mode == 3){
					if ($row['t01_mkind'] == '4' &&
						$row['t01_svc_subcode'] == '200' &&
						$row['t01_bipay_umu'] == 'Y'){
					//	$temp_count_1 ++;
					}?>
					<script>
						document.getElementById('col_1_<?=$temp_index_1?>').setAttribute('rowSpan', '<?=$temp_count_1;?>');
					</script><?
				}
			}

			if ($temp_client_date != $row['t01_jumin'].'_'.$row['t01_sugup_date']){
				if (!empty($temp_client_date)){
					/*
					echo '<script>
							document.getElementById(\'col_2_'.$temp_index_2.'\').setAttribute(\'rowSpan\', \''.$temp_count_2.'\');
						  </script>';
					*/
				}

				$temp_client_date  = $row['t01_jumin'].'_'.$row['t01_sugup_date'];
				$temp_date   = date('d', strtotime($myF->dateStyle($row['t01_sugup_date']))).'일('.$myF->weekday($row['t01_sugup_date']).')';
				$temp_index_2= $i;

				/*
				if ($row['t01_mkind'] == '4' &&
					$row['t01_svc_subcode'] == '200' &&
					$row['t01_bipay_umu'] == 'Y'){
					$temp_count_2 = 2;
				}else{
					$temp_count_2 = 1;
				}
				*/
				$temp_count_2 = 1;
			}else{
				$temp_count_2 ++;
				$temp_date = '';

				if ($row['t01_mkind'] == '4' &&
					$row['t01_svc_subcode'] == '200' &&
					$row['t01_bipay_umu'] == 'Y'){
					//$temp_count_2 ++;
				}?>
				<script>
					document.getElementById('col_2_<?=$temp_index_2?>').setAttribute('rowSpan', '<?=$temp_count_2;?>');
				</script><?
			}

			if (!empty($row['t01_sugup_fmtime']) && !empty($row['t01_sugup_totime'])){
				$str_time = $myF->timeStyle($row['t01_sugup_fmtime']).'-'.$myF->timeStyle($row['t01_sugup_totime']).' ('.$row['t01_sugup_soyotime'].'분)';
			}else{
				$str_time = '&nbsp;';
			}

			echo '<tr style=\'\'>';

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

				if(($mode == 1 || $mode == 3) && $temp_name != ''){?>
					<td id="col_1_<?=$temp_index_1;?>" class="left"><?=$temp_name;?></td><?
				}
				if($temp_date != ''){?>
					<td id="col_2_<?=$temp_index_2;?>" class="center"><?=$temp_date;?></td><?
				}?>
				<td class="left" rowspan="<?=$rowspan;?>"><?=$str_time;?></td>
				<td class="left">
					<input type="text" name="conf_from[]" value="<?=$conf_from;?>" tag="<?=$conf_from;?>" class="no_string" style="width:40px; text-align:center; margin:0; color:<?=$font_color;?>;" maxlength="4" alt="time" onkeydown="__onlyNumber(this);" onchange="_set_conf_proc_time(<?=$i;?>);" <? if($modify > 1 || $row['act_yn'] == 'Y' || $row['conf_yn'] == 'Y'){?>readonly<?} ?>>
					<input type="text" name="conf_to[]"   value="<?=$conf_to;?>"   tag="<?=$conf_to;?>"   class="no_string" style="width:40px; text-align:center; margin:0; color:<?=$font_color;?>;" maxlength="4" alt="time" onkeydown="__onlyNumber(this);" onchange="_set_conf_proc_time(<?=$i;?>);" <? if($modify > 1 || $row['act_yn'] == 'Y' || $row['conf_yn'] == 'Y'){?>readonly<?} ?>>
					<input type="text" name="conf_time[]" value="<?=$conf_time;?>" tag="<?=$conf_time;?>" class="no_string" style="width:40px; text-align:center; margin:0; color:<?=$font_color;?>; background-color:#eee; cursor:default;" maxlength="4" readonly>분
				</td>
				<td class="left"  id="suga_name[]"><?=$suga_name;?></td>
				<td class="right <?=($row['t01_bipay_umu'] != 'Y' ? 'clsSuga' : '');?>" id="suga_value[]"><?=$suga_value;?></td>
				<td class="right" id="plan_value[]"><?=$plan_value;?></td>
				<?
					if ($mode == 1 || $mode == 2){?>
						<td class="center">
						<?
							if ($modify > 1 || $row['act_yn'] == 'Y' || $row['conf_yn'] == 'Y'){
								echo '<input name=\'conf_mem_nm[]\' type=\'text\' value=\''.$row['worker'].'\' style=\'width:100%; cursor:default;\' disabled=\'true\'>';
							}else{
								echo '<input name=\'conf_mem_nm[]\' type=\'text\' value=\''.$row['worker'].'\' style=\'width:100%;\' onclick=\'set_member("'.$code.'",'.$i.');\' alt=\'hand\' readonly>';
							}
							echo '<input name=\'conf_mem_cd[]\' type=\'hidden\' value=\''.$ed->en($row['t01_yoyangsa_id1']).'\'>';
						?>
						</td><?
					}
				?>
				<td class="left last">
				<?
					if ($row['act_yn'] == 'N'){//수정가능
						if ($modify > 1 || $row['act_yn'] == 'Y' || $row['conf_yn'] == 'Y'){
						}else{?>
							<a href="#" onclick="_set_plan_to_conf(<?=$i;?>); return false;"><img src="../image/btn_copy_2.png"></a>
							<a href="#" onclick="_set_conf_to_cancel(<?=$i;?>); return false;"><img src="../image/btn_cancel_2.png"></a>
							<!--<a href="#" onclick="_set_iljung('<?=$i;?>'); return false;"><img src="../image/btn_diary_2.png"></a>--><?
						}
					}else{ //수정불가능
					}
				?>
				</td>

				<input type="hidden" name="svc_kind[]" value="<?=$row['t01_mkind'];?>">

				<input type="hidden" name="client[]" value="<?=$ed->en($row['t01_jumin']);?>">

				<input type="hidden" name="svc_code[]" value="<?=$row['t01_svc_subcode'];?>">

				<input type="hidden" name="suga_code[]"  value="<?=$row['t01_conf_suga_code'];?>">
				<input type="hidden" name="suga_price[]" value="<?=$row['t01_conf_suga_value'];?>">

				<input type="hidden" name="plan_date[]" value="<?=$row['t01_sugup_date'];?>">
				<input type="hidden" name="plan_from[]" value="<?=$row['t01_sugup_fmtime'];?>">
				<input type="hidden" name="plan_to[]"   value="<?=$row['t01_sugup_totime'];?>">
				<input type="hidden" name="plan_time[]" value="<?=$row['t01_sugup_soyotime'];?>">
				<input type="hidden" name="plan_seq[]"  value="<?=$row['t01_sugup_seq'];?>">

				<input type="hidden" name="work_from[]" value="<?=$row['t01_wrk_fmtime'];?>">
				<input type="hidden" name="work_to[]"   value="<?=$row['t01_wrk_totime'];?>">

				<input type="hidden" name="status_gbn[]"  value="<?=$row['t01_status_gbn'];?>">

				<input type="hidden" name="change_flag[]" value="N">
				<input type="hidden" name="cancel_flag[]" value="N">

				<input type="hidden" name="plan_copy[]" value="<?=$plan_copy;?>">

				<input type="hidden" name="holiday[]" value="<?=$row['t01_holiday'];?>">
				<input type="hidden" name="family[]"  value="<?=$row['t01_toge_umu'];?>">
				<input type="hidden" name="bipay[]"   value="<?=$row['t01_bipay_umu'];?>">

				<input type="hidden" name="plan_mem_cd[]" value="<?=$ed->en($row['t01_mem_cd1']);?>">
				<input type="hidden" name="plan_mem_nm[]" value="<?=$row['t01_mem_nm1'];?>">
			</tr><?


			/*********************************************************

				비급여단가

			*********************************************************/
			if ($row['t01_bipay_umu'] == 'Y'){
				/*********************************************************
				$sql = 'select service_cost as cost
						,      service_bipay as bipay
						  from suga_service
						 where org_no                                 = \''.$code.'\'
						   and service_code                           = \''.$row['t01_conf_suga_code'].'\'
						   and replace(service_from_dt, \'-\', \'\') <= \''.$row['t01_sugup_date'].'\'
						   and replace(service_to_dt  , \'-\', \'\') >= \''.$row['t01_sugup_date'].'\'';

				$bipay_if = $conn->get_array($sql);

				if ($row['bipay_kind'] == '1'){
					$bipay_cost = $bipay_if['cost'];
				}else if ($row['bipay_kind'] == '2'){
					$bipay_cost = $bipay_if['bipay'];
				}else{
					$bipay_cost = $ctl_bipay_if[$row['t01_jumin']][$row['t01_mkind']][$row['t01_svc_subcode']];
				}

				echo '<input type=\'hidden\' name=\'bipay_pay[]\' value=\''.str_replace(',', '', $bipay_cost).'\'>';

				unset($bipay_if);
				*********************************************************/

				switch($row['t01_svc_subcode']){
					case '200':
						$bipay_cost = $row['bipay1'];
						break;
					case '500':
						$bipay_cost = $row['bipay2'];
						break;
					case '800':
						$bipay_cost = $row['bipay3'];
						break;
				}

				echo '<input type=\'hidden\' name=\'bipay_pay[]\' value=\''.$bipay_cost.'\'>';
			}else{
				echo '<input type=\'hidden\' name=\'bipay_pay[]\' value=\'0\'>';
			}


			/*********************************************************

				부요양보호사

			*********************************************************/
			echo '<input type=\'hidden\' name=\'conf_mem_cd2[]\' value=\''.$ed->en($row['t01_mem_cd2']).'\'>
				  <input type=\'hidden\' name=\'conf_mem_nm2[]\' value=\''.$row['t01_mem_nm2'].'\'>';

			/*
			if ($rowspan == 2){
				echo '<tr style=\'\'>
						<td class=\'\' colspan=\'4\'>';

				//공단 및 기관 비급여 수가

				$sql = 'select service_cost as cost
						,      service_bipay as bipay
						  from suga_service
						 where org_no                                 = \''.$code.'\'
						   and service_code                           = \''.$row['t01_conf_suga_code'].'\'
						   and replace(service_from_dt, \'-\', \'\') <= \''.$row['t01_sugup_date'].'\'
						   and replace(service_to_dt  , \'-\', \'\') >= \''.$row['t01_sugup_date'].'\'';

				$bipay_if = $conn->get_array($sql);

				echo '<input name=\'bipay_gbn_'.$i.'[]\' type=\'radio\' class=\'radio\' value=\'1\' '.($row['bipay_kind'] == '1' ? 'checked' : '').' onclick=\'document.getElementsByName("bipay_pay_'.$i.'[]")[2].disabled=__object_get_value("bipay_gbn_'.$i.'[]")=="3"?false:true; _set_conf_proc_time('.$i.');\'>공단수가
					  <input name=\'bipay_pay_'.$i.'[]\' type=\'text\' class=\'number\' value=\''.number_format($bipay_if['cost']).'\' style=\'width:50px;\' readonly>
					  <input name=\'bipay_gbn_'.$i.'[]\' type=\'radio\' class=\'radio\' value=\'2\' '.($row['bipay_kind'] == '2' ? 'checked' : '').' onclick=\'document.getElementsByName("bipay_pay_'.$i.'[]")[2].disabled=__object_get_value("bipay_gbn_'.$i.'[]")=="3"?false:true; _set_conf_proc_time('.$i.');\'>기관비급여수가
					  <input name=\'bipay_pay_'.$i.'[]\' type=\'text\' class=\'number\' value=\''.number_format($bipay_if['bipay']).'\' style=\'width:50px;\' readonly>
				 	  <input name=\'bipay_gbn_'.$i.'[]\' type=\'radio\' class=\'radio\' value=\'3\' '.($row['bipay_kind'] == '3' ? 'checked' : '').' onclick=\'document.getElementsByName("bipay_pay_'.$i.'[]")[2].disabled=__object_get_value("bipay_gbn_'.$i.'[]")=="3"?false:true; _set_conf_proc_time('.$i.');\'>고객개별수가
					  <input name=\'bipay_pay_'.$i.'[]\' type=\'text\' class=\'number\' value=\''.number_format($ctl_bipay_if[$row['t01_jumin']][$row['t01_mkind']][$row['t01_svc_subcode']]).'\' style=\'width:50px;\' '.($row['bipay_kind'] != '3' ? 'disabled=true' : '').' onchange=\'_set_conf_proc_time('.$i.');\'>';

				unset($bipay_if);

				echo '	</td>
						<td class=\'\'>';

				if ($modify > 1 || $row['act_yn'] == 'Y' || $row['conf_yn'] == 'Y'){
					echo '<input name=\'conf_mem_nm2[]\' type=\'text\' value=\''.$row['t01_yname2'].'\' style=\'width:100%; cursor:default;\' disabled=\'true\'>';
				}else{
					echo '<input name=\'conf_mem_nm2[]\' type=\'text\' value=\''.$row['t01_yname2'].'\' style=\'width:100%;\' onclick=\'set_member("'.$code.'",'.$i.',"2");\' alt=\'hand\' readonly>';
				}
				echo '<input name=\'conf_mem_cd2[]\' type=\'hidden\' value=\''.$ed->en($row['t01_yoyangsa_id2']).'\'>';

				echo '	</td>
						<td class=\'last\'></td>
					  </tr>';
			}else{
				echo '<input type=\'hidden\' name=\'conf_mem_cd2[]\' value=\''.$ed->en($row['t01_mem_cd2']).'\'>
					  <input type=\'hidden\' name=\'conf_mem_nm2[]\' value=\''.$row['t01_mem_nm2'].'\'>
					  <input type=\'hidden\' name=\'bipay_gbn_'.$i.'[]\' value=\''.$row['bipay_kind'].'\'>
					  <input type=\'hidden\' name=\'bipay_pay_'.$i.'[]\' value=\'0\'>';
			}
			*/
		}

		$conn->row_free();

		unset($ctl_bipay_if);

		if($mode == 1){
			$colspan = 7;
		}else {
			$colspan = 6;
		}
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left bottom last" style="border-top:1px solid #ccc;" colspan="<?=$colspan?>">
				* 색상정보 ( <font color="#000000">검정색 : 완료된 일정</font>, <font color="#0000ff">파란색 : 입력한 일정</font>, <font color="#ff0000">붉은색 : 에러난 일정</font> )
				<span style='margin-left:10px;'>실적급여액이 <font color="#ff0000">붉은색</font>이면 <font color="#ff0000">한도금액초과</font></span>
			</td>
			<td class="right bottom last" style="width:190px; border-top:1px solid #ccc;" colspan="3"><?=$btn_group;?></td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="before_uri"	value="<?=$before_uri;?>">
<input type="hidden" name="code" id="code" value="<?=$code;?>">
<input type="hidden" name="kind" id="kind" value="<?=$kind;?>">
<input type="hidden" name="year"		value="<?=$year;?>">
<input type="hidden" name="month"		value="<?=$month;?>">
<input type="hidden" name="day"			value="<?=$day;?>">
<input type="hidden" name="mode" id="mode" value="<?=$mode;?>">
<input type="hidden" name="jumin"		value="<?=$ed->en($jumin);?>">
<input type="hidden" name="page"		value="<?=$page;?>">
<input type="hidden" name="find_name"	value="<?=$find_name;?>">
<input type="hidden" name="find_year"	value="<?=$year;?>">
<input type="hidden" name="plan_day_cnt"value="<?=$plan_day_cnt;?>">
<input type="hidden" name="conf_day_cnt"value="<?=$conf_day_cnt;?>">
<input type="hidden" name="plan_time_tot" value="">
<input type="hidden" name="conf_time_tot" value="">
<input type="hidden" name="plan_amt_tot" value="">
<input type="hidden" name="conf_amt_tot" value="">
</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
<script language='javascript'>
	if ('<?=$closing_msg;?>' != ''){
		document.getElementById('btn_copy_all').innerHTML = '';
	}

	document.getElementById('closing_msg').innerHTML = '<?=$closing_msg;?>';
	_sum_tot_data();
</script>