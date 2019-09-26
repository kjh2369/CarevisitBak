<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$find_year = $_POST['find_year'];
	$find_name = $_POST['find_name'];

	$page = $_POST['page'];

	$code   = $_POST['code'];
	$kind   = $_POST['kind'];
	$year   = $_POST['year'];
	$month  = $_POST['month'];
	$member = $ed->de($_POST['member']);

	$member_name = $conn->get_data("select m02_yname from m02yoyangsa where m02_ccode = '$code' and m02_mkind = '$kind' and m02_yjumin = '$member'");
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function list(){
	var f = document.f;

	f.action = 'month_member.php';
	f.submit();
}

function time_check(obj){
	alert(checkDate(obj.value));
}

window.onload = function(){
	__init_form(document.f);
}
-->
</script>

<form name="f" method="post">

<div class="title">월 실적 등록(요양보호사)</div>

<table class="my_table my_border">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="150px">
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td class="left"><?=$year;?>년 <?=$month;?>월</td>
			<th>요양보호사</th>
			<td class="left last"><?=$member_name;?></td>
			<td class="left last">&nbsp;</td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="list"></span><button type="button" onclick="list();">리스트</button></span>
				<span class="btn_pack m"><button type="button" onclick="set_plan_to_conf('all');">일괄복사</button></span>
				<span class="btn_pack m icon"><span class="save"></span><button type="button" onclick="_diary_save();">저장</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:-1px; border-bottom:none;">
	<colgroup>
		<col width="70px">
		<col width="60px">
		<col width="110px">
		<col width="150px">
		<col width="150px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">수급자</th>
			<th class="head">일자</th>
			<th class="head">계획</th>
			<th class="head">실적</th>
			<th class="head">제공서비스</th>
			<th class="head">실적급여액</th>
			<th class="head">계획급여액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$today = date('Ymd', mktime());

		$sql = "select t01_jumin
				,      m03_name
				,      t01_sugup_date
				,      t01_sugup_fmtime
				,      t01_sugup_totime
				,      t01_sugup_soyotime
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
				,     (select count(*)
				         from t13sugupja
						where t13_ccode    = t01_ccode
						  and t13_mkind    = t01_mkind
						  and t13_jumin    = t01_jumin
						  and t13_pay_date = left(t01_sugup_date, 6)
						  and t13_type     = '2') as conf_count
				  from t01iljung
				 inner join m03sugupja
					on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				 where t01_ccode = '$code'
				   and t01_mkind = '$kind'
				   and t01_sugup_date like '$year$month%'
				   and t01_del_yn = 'N'
				   and t01_yoyangsa_id1 = '$member'
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

			if ($row['conf_count'] > 0){
				$modify = 3;
			}

			// 수가정보
			$suga_name  = $conn->get_suga($code, $row['t01_conf_suga_code']); //수가명

			// 실적급여액
			if ($row['t01_status_gbn'] == '1' && $row['t01_conf_soyotime'] > 0){
				$suga_value = number_format($row['t01_conf_suga_value']); //수가단가
				$conf_from  = $myF->timeStyle($row['t01_conf_fmtime']);
				$conf_to    = $myF->timeStyle($row['t01_conf_totime']);
				$conf_time  = $row['t01_conf_soyotime'];
				$plan_copy  = 'N';
			}else{
				$conf_from  = '';
				$conf_to    = '';
				$conf_time  = '';
				$suga_value = '0';
				$plan_copy  = 'Y';
			}

			// 계획수가
			$plan_value = number_format($row['t01_suga_tot']);

			if ($temp_client != $row['t01_jumin']){
				$temp_client = $row['t01_jumin'];
				$temp_name   = $row['m03_name'];
				$temp_date   = date('d', strtotime($myF->dateStyle($row['t01_sugup_date']))).'일('.$myF->weekday($row['t01_sugup_date']).')';
				$temp_index  = $i;
				$temp_count  = 1;
				$temp_date_top = 'border-top:1px solid #ccc;';
			}else{
				$temp_count ++;
				$temp_name = '';

				if ($temp_date != date('d', strtotime($myF->dateStyle($row['t01_sugup_date']))).'일('.$myF->weekday($row['t01_sugup_date']).')'){
					$temp_date     = date('d', strtotime($myF->dateStyle($row['t01_sugup_date']))).'일('.$myF->weekday($row['t01_sugup_date']).')';
					$temp_date_top = 'border-top:1px solid #ccc;';
				}else{
					$temp_date     = '';
					$temp_date_top = 'border-top:none;';
				}?>
				<script>
					document.getElementById('row_<?=$temp_index?>').setAttribute('rowSpan', '<?=$temp_count;?>');
				</script><?
			}
			?>
			<tr><?
				if($temp_name != ''){?>
					<td class="left top" id="row_<?=$temp_index;?>"><?=$temp_name;?></td><?
				}?>
				<td class="center bottom" style="<?=$temp_date_top;?>"><?=$temp_date;?></td>
				<td class="left"><?=$myF->timeStyle($row['t01_sugup_fmtime']).'-'.$myF->timeStyle($row['t01_sugup_totime']).' ('.$row['t01_sugup_soyotime'].'분)';?></td>
				<td class="left">
					<input type="text" name="conf_from[]" value="<?=$conf_from;?>" class="no_string" style="width:40px; text-align:center; margin:0;" maxlength="4" alt="time" onkeydown="__onlyNumber(this);" onchange="set_conf_proc_time(<?=$i;?>);" <? if($modify > 1){?>readonly<?} ?>>
					<input type="text" name="conf_to[]"   value="<?=$conf_to;?>"   class="no_string" style="width:40px; text-align:center; margin:0;" maxlength="4" alt="time" onkeydown="__onlyNumber(this);" onchange="set_conf_proc_time(<?=$i;?>);" <? if($modify > 1){?>readonly<?} ?>>
					<input type="text" name="conf_time[]" value="<?=$conf_time;?>" class="no_string" style="width:40px; text-align:center; margin:0; background-color:#eee; cursor:default;" maxlength="4" alt="read" onkeydown="__onlyNumber(this);" readonly>분
				</td>
				<td class="left"  id="suga_name[]"><?=$suga_name;?></td>
				<td class="right" id="suga_value[]"><?=$suga_value;?></td>
				<td class="right"><?=$plan_value;?></td>
				<td class="left last">
				<?
					if ($plan_copy == 'Y' && $modify == 1){?>
						<span class="btn_pack small"><button type="button" onclick="set_plan_to_conf(<?=$i;?>);">복사</button></span><?
					}else if ($modify == 2){?>
						<span>변경할 수 없는 일정</span><?
					}else if ($modify == 3){?>
						<span>확정된 일정(수급자)</span><?
					}else{?>
						<span>&nbsp;</span><?
					}
				?>
				</td>

				<input type="hidden" name="svc_code[]" value="<?=$row['t01_svc_subcode'];?>">

				<input type="hidden" name="suga_code[]"  value="<?=$row['t01_conf_suga_code'];?>">
				<input type="hidden" name="suga_price[]" value="<?=$row['t01_conf_suga_value'];?>">

				<input type="hidden" name="plan_date[]" value="<?=$row['t01_sugup_date'];?>">
				<input type="hidden" name="plan_from[]" value="<?=$row['t01_sugup_fmtime'];?>">
				<input type="hidden" name="plan_to[]"   value="<?=$row['t01_sugup_totime'];?>">
				<input type="hidden" name="plan_time[]" value="<?=$row['t01_sugup_soyotime'];?>">

				<input type="hidden" name="change_flag[]" value="N">

				<input type="hidden" name="plan_copy[]" value="<?=$plan_copy;?>">
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom last" style="border-top:1px solid #ccc;" colspan="8">&nbsp;</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">

<input type="hidden" name="find_year" value="<?=$find_year;?>">
<input type="hidden" name="find_name" value="<?=$find_name;?>">
<input type="hidden" name="page"      value="<?=$page;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>