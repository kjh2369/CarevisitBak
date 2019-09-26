<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	$code  = $_REQUEST['code'];
	$kind  = $_REQUEST['kind'];
	$year  = $_REQUEST['year'];
	$month = $_REQUEST['month'];
	$day   = $_REQUEST['day'];
	$page  = $_REQUEST['page'];
	$jumin = $ed->de($_REQUEST['jumin']);
	$mode  = $_REQUEST['mode'];

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

	//and t01_yoyangsa_id1 = '$member'
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function before(){
	var f = document.f;

	f.action = f.before_uri.value;
	f.submit();
}

function save(){
	var f = document.f;
	var change_flag = false;
	var change = document.getElementsByName('change_flag[]');

	for(var i=0; i<change.length; i++){
		if (change[i].value == 'Y'){
			change_flag = true;
			break;
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

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<div class="title"><?=$title;?></div>

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
			<td class="right last">
				<span class="btn_pack m icon"><span class="save"></span><button type="button" onclick="save();">저장</button></span>
				<span class="btn_pack m icon"><span class="before"></span><button type="button" onclick="before();">이전</button></span>
			</td>
		</tr>
	</tbody>
</table>

<?
	// 요양보호사 월실적
	if ($mode == 2){
		/*
		$sql = "select m03_name as name
				,      LVL.m81_name as lvl
				,      m03_skind as bonin_yul
				,      m03_bonin_yul as bonin_rate
				,      m03_kupyeo_max as max_pay
				,      m03_jumin as jumin
				,      m03_key as client_key
				  from m03sugupja
				 inner join m81gubun as LVL
					on LVL.m81_gbn  = 'LVL'
				   and LVL.m81_code = m03_ylvl
				 where m03_ccode = '$code'
				   and m03_mkind = '$kind'
				   and m03_jumin = '$jumin'";
		$client = $conn->get_array($sql);
		*/?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="50px">
				<col width="40px">
				<col width="70px">
				<col width="60px">
				<col width="70px">
				<col width="70px">
				<col width="70px">
				<col width="70px">
				<col width="70px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head" rowspan="2">등급</th>
					<th class="head" rowspan="2">부담율</th>
					<th class="head" rowspan="2">급여한도액</th>
					<th class="head" rowspan="2">서비스</th>
					<th class="head" rowspan="2">급여총액</th>
					<th class="head" colspan="3">본인부담액</th>
					<th class="head" rowspan="2">공단청구액</th>
					<th class="head last" rowspan="2">비고</th>
				</tr>
				<tr>
					<th class="head">초과+비급여</th>
					<th class="head">순수</th>
					<th class="head">계</th>
				</tr>
			</thead>
			<tbody>
			<?
				$sql = "select jumin, LVL.m81_name as lvl, client_key, skind, max_pay, bonin_yul
						  from (
							   select m03_jumin as jumin
							   ,      m03_name as name
							   ,      m03_key as client_key
							   ,      m03_ylvl as ylvl
							   ,      m03_skind as skind
							   ,      m03_kupyeo_max as max_pay
							   ,      m03_bonin_yul as bonin_yul
							   ,      m03_sdate as sdate
							   ,      m03_edate as edate
								 from m03sugupja
								where m03_ccode = '$code'
								  and m03_mkind = '$kind'
								  and m03_jumin = '$jumin'
								union all
							   select m31_jumin
							   ,      m03_name
							   ,      m03_key
							   ,      m31_level
							   ,      m31_kind
							   ,      m31_kupyeo_max
							   ,      m31_bonin_yul
							   ,      m31_sdate as sdate
							   ,      m31_edate as edate
								 from m31sugupja
								inner join m03sugupja
								   on m03_ccode = m31_ccode
								  and m03_mkind = m31_mkind
								  and m03_jumin = m31_jumin
								where m31_ccode = '$code'
								  and m31_mkind = '$kind'
								  and m31_jumin = '$jumin'
							   ) as t
						 inner join m81gubun as LVL
							on LVL.m81_gbn  = 'LVL'
						   and LVL.m81_code = ylvl
						 where '$year$month' between left(sdate, 6) and left(edate, 6)
						 order by sdate, edate";

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$client[$i] = $conn->select_row($i);
				}

				$conn->row_free();

				$client_count = $row_count;

				if ($client_count > 0){
					$tot_amt     = 0; //급여총액
					$over_amt    = 0; //초과, 비급여
					$bonin_amt   = 0; //순수
					$tot_sub_amt = 0; //계
					$gongdan_amt = 0; //공단청구액

					for($client_i=0; $client_i<$client_count; $client_i++){
						$sql = "";
						$skind     = $client[$client_i]['skind'];
						$bonin_yul = $client[$client_i]['bonin_yul'];

						for($i=1; $i<=3; $i++){
							switch($i){
							case 1:
								$gubun = '방문요양';
								$serviceCode = '200';
								break;
							case 2:
								$gubun = '방문목욕';
								$serviceCode = '500';
								break;
							case 3:
								$gubun = '방문간호';
								$serviceCode = '800';
								break;
							}

							if ($i > 1) $sql .= " union all ";

							$sql .="select '$gubun' as gubun
									,      '$serviceCode' as service_code
									,      t13_bonin_yul as bonin_yul
									,      t13_max_amt as max_amt
									,      t13_suga_tot$i as suga_tot
									,      t13_over_amt$i as over_amt
									,      t13_bipay$i as bipay
									,      t13_bonin_amt$i as bonin_amt
									,      t13_bonbu_tot$i as bonin_tot
									,      t13_chung_amt$i as chung_amt
									  from t13sugupja
									 where  t13_ccode     = '$code'
									   and  t13_mkind     = '$kind'
									   and  t13_jumin     = '$jumin'
									   and  t13_pay_date  = '$year$month'
									   and (t13_bonin_yul = '$skind' or t13_bonin_yul = '$bonin_yul')
									   and  t13_type      = '2'";
						}
						$sql .= "order by bonin_yul, service_code";

						$conn->query($sql);
						$conn->fetch();
						$row_count = $conn->row_count();

						if ($row_count > 0){
							for($i=0; $i<$row_count; $i++){
								$row = $conn->select_row($i);?>
								<tr>
									<?
										if ($i == 0){?>
											<td class="center" rowspan="3"><?=$client[$client_i]['lvl'];?></td>
											<td class="right" rowspan="3"><?=$client[$client_i]['bonin_yul'];?></td>
											<td class="right" rowspan="3"><?=number_format($client[$client_i]['max_pay']);?></td><?
										}
									?>
									<td class="center"><?=$row['gubun'];?></td>
									<td class="right"><?=number_format($row['suga_tot']);?></td>
									<td class="right"><?=number_format($row['over_amt']+$row['bipay']);?></td>
									<td class="right"><?=number_format($row['bonin_amt']);?></td>
									<td class="right"><?=number_format($row['bonin_tot']);?></td>
									<td class="right"><?=number_format($row['chung_amt']);?></td>
									<td class="left last">&nbsp;</td>
								</tr><?

								$tot_amt	 += ($row['suga_tot']);
								$over_amt    += ($row['over_amt']+$row['bipay']);
								$bonin_amt   += ($row['bonin_amt']);
								$tot_sub_amt += ($row['bonin_tot']);
								$gongdan_amt += ($row['chung_amt']);
							}
						}

						$conn->row_free();
					}
					unset($client);
				}
			?>
			</tbody>
			<tbody>
				<tr>
					<th class="right" colspan="4">합계</th>
					<td class="right"><?=number_format($tot_amt);?></td>
					<td class="right"><?=number_format($over_amt);?></td>
					<td class="right"><?=number_format($bonin_amt);?></td>
					<td class="right"><?=number_format($tot_sub_amt);?></td>
					<td class="right"><?=number_format($gongdan_amt);?></td>
					<td class="last">&nbsp;</td>
				</tr>
			</tbody>
		</table><?
	}
?>

<table class="my_table" style="width:100%;">
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
		<col width="60px">
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
			<th class="head last">
				<a href="#" onclick="_set_plan_to_conf('all');"><img src="../image/btn_copy_3.png"></a>
			</th>
		</tr>
	</thead>
	<tbody>
	<?
		$today = date('Ymd', mktime());

		$sql = "select t01_jumin
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
				,      t01_modify_yn
				,      m02_yname as worker
				,      case when date_format(now(), '%Y%m%d') < ifnull(replace(".($mode != 3 ? 'act_cls_dt_from' : 'salary_cls_dt_from').", '-', ''), '99999999') then 'Y' else 'N' end as act_yn
				  from t01iljung
				 inner join m03sugupja
					on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				 inner join m02yoyangsa
				    on m02_ccode  = t01_ccode
				   and m02_mkind  = t01_mkind
				   and m02_yjumin = t01_yoyangsa_id1
				  left join closing_progress
				    on org_no       = t01_ccode
				   and closing_yymm = left(t01_sugup_date, 6) $wsl
				 order by m03_name, t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($today >= $row['t01_sugup_date']){
				$modify = 1;
			}else{
				$modify = 2;
			}

			// 수가정보
			$suga_name  = $conn->get_suga($code, $row['t01_conf_suga_code']); //수가명

			// 실적급여액
			if ($row['t01_status_gbn'] == '1'){
				$suga_value = number_format($row['t01_conf_suga_value']); //수가단가
				$conf_from  = $myF->timeStyle($row['t01_conf_fmtime']);
				$conf_to    = $myF->timeStyle($row['t01_conf_totime']);
				$conf_time  = $row['t01_conf_soyotime'];
				$font_color = '#000000';
				$plan_copy  = 'N';
			}else{
				$conf_from  = $myF->timeStyle($row['t01_wrk_fmtime']);
				$conf_to    = $myF->timeStyle($row['t01_wrk_totime']);
				$conf_time  = $myF->dateDiff('n', $conf_from, $conf_to);
				$suga_value = '0';
				$plan_copy  = 'Y';

				if ($conf_time < 0){
					$conf_time = 24 * 60 + $conf_time;
					$font_color = '#ff0000';
				}else{
					if ($plan_copy == 'N'){
						$font_color = '#000000';
					}else{
						if ($row['t01_status_gbn'] == 'C'){
							$font_color = '#ff0000';
						}else{
							$font_color = '#0000ff';
						}
					}
				}
			}

			if ($row['act_yn'] != 'Y') $plan_copy = 'N';

			// 계획수가
			$plan_value = number_format($row['t01_suga_tot']);

			if ($temp_client != $row['t01_jumin']){
				$temp_client = $row['t01_jumin'];
				$temp_name   = $row['m03_name'];
				$temp_index_1= $i;
				$temp_count_1= 1;
			}else{
				$temp_count_1 ++;
				$temp_name = '';

				if ($mode == 1 || $mode == 3){?>
					<script>
						document.getElementById('col_1_<?=$temp_index_1?>').setAttribute('rowSpan', '<?=$temp_count_1;?>');
					</script><?
				}
			}

			if ($temp_client_date != $row['t01_jumin'].'_'.$row['t01_sugup_date']){
				$temp_client_date  = $row['t01_jumin'].'_'.$row['t01_sugup_date'];
				$temp_date   = date('d', strtotime($myF->dateStyle($row['t01_sugup_date']))).'일('.$myF->weekday($row['t01_sugup_date']).')';
				$temp_index_2= $i;
				$temp_count_2= 1;
			}else{
				$temp_count_2 ++;
				$temp_date = '';?>
				<script>
					document.getElementById('col_2_<?=$temp_index_2?>').setAttribute('rowSpan', '<?=$temp_count_2;?>');
				</script><?
			}?>
			<tr><?
				if(($mode == 1 || $mode == 3) && $temp_name != ''){?>
					<td id="col_1_<?=$temp_index_1;?>" class="left"><?=$temp_name;?></td><?
				}
				if($temp_date != ''){?>
					<td id="col_2_<?=$temp_index_2;?>" class="center"><?=$temp_date;?></td><?
				}?>
				<td class="left"><?=$myF->timeStyle($row['t01_sugup_fmtime']).'-'.$myF->timeStyle($row['t01_sugup_totime']).' ('.$row['t01_sugup_soyotime'].'분)';?></td>
				<td class="left">
					<input type="text" name="conf_from[]" value="<?=$conf_from;?>" class="no_string" style="width:40px; text-align:center; margin:0; color:<?=$font_color;?>;r " maxlength="4" alt="time" onkeydown="__onlyNumber(this);" onchange="_set_conf_proc_time(<?=$i;?>);" <? if($modify > 1 || $row['act_yn'] != 'Y'){?>readonly<?} ?>>
					<input type="text" name="conf_to[]"   value="<?=$conf_to;?>"   class="no_string" style="width:40px; text-align:center; margin:0; color:<?=$font_color;?>;" maxlength="4" alt="time" onkeydown="__onlyNumber(this);" onchange="_set_conf_proc_time(<?=$i;?>);"   <? if($modify > 1 || $row['act_yn'] != 'Y'){?>readonly<?} ?>>
					<input type="text" name="conf_time[]" value="<?=$conf_time;?>" class="no_string" style="width:40px; text-align:center; margin:0; color:<?=$font_color;?>; background-color:#eee; cursor:default;" maxlength="4" readonly>분
				</td>
				<td class="left"  id="suga_name[]"><?=$suga_name;?></td>
				<td class="right" id="suga_value[]"><?=$suga_value;?></td>
				<td class="right"><?=$plan_value;?></td>
				<?
					if ($mode == 1 || $mode == 2){?>
						<td class="left"><?=$row['worker'];?></td><?
					}
				?>
				<td class="left last">
				<?
					if ($row['act_yn'] == 'Y'){//수정가능
						if ($row['t01_status_gbn'] == 'C'){
							$iljung_img = 'btn_error_2.png';
						}else{
							$iljung_img = 'btn_diary_2.png';
						}

						if ($modify == 1){
							if ($row['t01_modify_yn'] == 'N'){?>
								<a href="#" onclick="_set_plan_to_conf(<?=$i;?>); return false;"><img src="../image/btn_copy_2.png"></a><?
							}else{?>
								<a href="#" onclick="_set_conf_to_cancel(<?=$i;?>); return false;"><img src="../image/btn_cancel_2.png"></a><?
							}
							if ($mode == 1){?>
								<a href="#" onclick="_setSugupjaDayReg('<?=$code;?>','<?=$kind;?>','<?=$year;?>','<?=$month;?>','<?=$day;?>','<?=$ed->en($row['t01_jumin']);?>','<?=$ed->en($row['t01_yoyangsa_id1']);?>','<?=$row['m03_key'];?>'); return false;"><img src="../image/<?=$iljung_img;?>"></a><?
							}else{?>
								<a href="#" onclick="_setSugupjaDiaryReg('<?=$code;?>','<?=$kind;?>','<?=$year;?>','<?=$month;?>','<?=$ed->en($row['t01_jumin']);?>','<?=$row['m03_key'];?>'); return false;"><img src="../image/<?=$iljung_img;?>"></a><?
							}
						}
					}else{ //수정불가능
					}
				?>
				</td>

				<input type="hidden" name="client[]" value="<?=$ed->en($row['t01_jumin']);?>">

				<input type="hidden" name="svc_code[]" value="<?=$row['t01_svc_subcode'];?>">

				<input type="hidden" name="suga_code[]"  value="<?=$row['t01_conf_suga_code'];?>">
				<input type="hidden" name="suga_price[]" value="<?=$row['t01_conf_suga_value'];?>">

				<input type="hidden" name="plan_date[]" value="<?=$row['t01_sugup_date'];?>">
				<input type="hidden" name="plan_from[]" value="<?=$row['t01_sugup_fmtime'];?>">
				<input type="hidden" name="plan_to[]"   value="<?=$row['t01_sugup_totime'];?>">
				<input type="hidden" name="plan_time[]" value="<?=$row['t01_sugup_soyotime'];?>">
				<input type="hidden" name="plan_seq[]"  value="<?=$row['t01_sugup_seq'];?>">

				<input type="hidden" name="status_gbn[]"  value="<?=$row['t01_status_gbn'];?>">

				<input type="hidden" name="change_flag[]" value="N">
				<input type="hidden" name="cancel_flag[]" value="N">

				<input type="hidden" name="plan_copy[]" value="<?=$plan_copy;?>">
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left bottom last" style="border-top:1px solid #ccc;" colspan="8">
				* 색상정보 ( <font color="#000000">검정색 : 완료된 일정</font>, <font color="#0000ff">파란색 : 입력한 일정</font>, <font color="#ff0000">붉은색 : 에러난 일정</font> )
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="before_uri"	value="<?=$before_uri;?>">
<input type="hidden" name="code"		value="<?=$code;?>">
<input type="hidden" name="kind"		value="<?=$kind;?>">
<input type="hidden" name="year"		value="<?=$year;?>">
<input type="hidden" name="month"		value="<?=$month;?>">
<input type="hidden" name="day"			value="<?=$day;?>">
<input type="hidden" name="mode"		value="<?=$mode;?>">
<input type="hidden" name="jumin"		value="<?=$ed->en($jumin);?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>