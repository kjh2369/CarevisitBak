<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_POST['code'] != '' ? $_POST['code'] : $_GET['code'];
	$kind	= $_POST['kind'] != '' ? $_POST['kind'] : $_GET['kind'];
	$year	= $_POST['year'] != '' ? $_POST['year'] : $_GET['year'];
	$month	= $_POST['month'] != '' ? $_POST['month'] : $_GET['month'];
	$result = $_GET['result'];

	// 급여마감여부
	$salary_cls_yn = $conn->get_closing_salary($code, $year.$month);
?>

<script src="../js/account.js" type="text/javascript"></script>
<script language='javascript'>
<!--

$(document).ready(function(){
	if ('<?=$result;?>' == '1'){
		var chk = $('input:checkbox[name="check[]"]');
		//var obj = $('input:checkbox[name="check[]"]:checked');

		$(chk).each(function(){
			var idx = $(chk).index(this);
			var mem = $('input:hidden[name="mem_cd[]"]').eq(idx);

			$.ajax({
				type: 'POST'
			,	url : '../salaryNew/salary_result.php'
			,	data: {
					'pos'	:'2'
				,	'code'	:'<?=$code;?>'
				,	'year'	:'<?=$year;?>'
				,	'month'	:'<?=$month;?>'
				,	'jumin'	:$(mem).val()
				,	'gubun'	:'person'
				}
			,	beforeSend: function(){
				}
			,	success: function(result){
					//alert(result);
				}
			,	complete: function(result){
				}
			,	error: function (){
				}
			}).responseXML;
		});
	}
});

function set_all(checked){
	__checkMyValue('check[]',checked);

	var check       = document.getElementsByName('check[]');

	for(var i=0; i<check.length; i++){
		set_amt(i);
	}
}

function set_amt(index){
	var check		= document.getElementsByName('check[]')[index];
	var tot_amt		= document.getElementsByName('tot_amt[]')[index];
	var deposit_amt = document.getElementsByName('deposit_amt[]')[index];
	var deposit_txt = document.getElementsByName('deposit_txt[]')[index];
	var in_mat		= document.getElementsByName('in_amt[]')[index];

	if (check.checked){
		deposit_amt.value = tot_amt.value - in_mat.value;
	}else{
		deposit_amt.value = deposit_amt.tag;
	}

	if (deposit_amt.value < 0) deposit_amt.value = 0;

	deposit_txt.innerHTML = __num2str(deposit_amt.value);
}

function year_list(){
	var f = document.f;

	f.action = 'unpaid_auto.php';
	f.submit();
}

function save(){
	if (!confirm('선택하신 데이타의 자동입금처리를 진행하시겠습니까?')) return;

	f.action = 'unpaid_auto_ok.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
	__mergeCell(document.getElementById('my_table'), 0, 0, 2, 0);
}

-->
</script>

<form name="f" method="post">

<div class="title" style="width:auto; float:left;">본인부담금공제</div>
<div style="text-align:right; font-weight:bold; padding-top:8px;">
<?
	if ($salary_cls_yn == 'Y'){
		echo $year.'년 '.intval($month).'월은 <span style="color:#ff0000;">급여마감</span>되었습니다.';
	}
?>
</div>

<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">년월</th>
			<td class="left last"><?=$year;?>년 <?=intval($month);?>월</td>
			<td class="right last" style="padding-top:1px;">
				<span class="btn_pack m icon"><span class="before"></span><button type="button" onclick="year_list();">이전</button></span>
				<?
					if ($salary_cls_yn == 'N'){?>
						<span class="btn_pack m icon"><span class="save"></span><button type="button" onclick="save();">저장</button></span><?
					}
				?>
			</td>
		</tr><?
		if ($salary_cls_yn == 'N'){?>
			<tr>
				<td class="last" colspan="3">
					<label><input id="chkReCal" name="chkReCal" type="checkbox" class="checkbox" value="Y">저장 후 급여 재계산을 실행합니다.</label>
				</td>
			</tr><?
		}?>
	</tbody>
</table>

<table id="my_table" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="80px">
		<col width="90px" span="4">
		<col width="40px">
		<col width="90px" span="2">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">요양보호사</th>
			<th class="head">수급자</th>
			<th class="head">본인부담금</th>
			<th class="head">초과금액</th>
			<th class="head">비급여금액</th>
			<th class="head">합계금액</th>
			<th class="head">
			<?
				if ($salary_cls_yn == 'N'){?>
					<input name="checkAll" type="checkbox" class="checkbox" value="Y" onclick="set_all(this.checked);"><?
				}
			?>
			</th>
			<th class="head">입금금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$deposit_ym = $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Ym');

		/*if ($debug){
			$sql = "SELECT cf_mem_cd as mem_cd
					,      cf_mem_nm as mem_nm
					,      m03_jumin as per_cd
					,      m03_name as per_nm
					,      t13_bonin_amt4 as bonin_amt
					,      t13_over_amt4 as over_amt
					,      t13_bipay4 as bi_amt
					,      t13_bonbu_tot4 as tot_amt
					,     (select ifnull(sum(deposit_amt), 0)
							 from unpaid_deposit
							where org_no        = t13_ccode
							  and deposit_jumin = t13_jumin
							  and deposit_auto  = 'Y'
							  and deposit_mem   = cf_mem_cd
							  and left(replace(deposit_reg_dt, '-', ''), 6) = '$deposit_ym') as deposit_amt
					,     0 as deposit_ahead
					,     0 as tot_deposit
					  FROM t13sugupja
					 INNER JOIN m03sugupja
						ON m03_ccode = t13_ccode
					   AND m03_mkind = t13_mkind
					   AND m03_jumin = t13_jumin
					 INNER JOIN client_family
						ON org_no   = t13_ccode
					   AND cf_jumin = t13_jumin
					 WHERE t13_ccode = '1234'
					   AND t13_mkind = '0'
					   AND t13_type  = '2'
					   AND t13_bonbu_tot4 > '0'
					   AND t13_pay_date = '$year$month'";
		}else{*/
			$sql = "select distinct m02_yjumin as mem_cd
					,      m02_yname as mem_nm
					,      m03_jumin as per_cd
					,      m03_name as per_nm
					,      m02_family_pay_yn as fam_pay_yn
					,      t13_bonin_amt4 as bonin_amt
					,      t13_over_amt4 as over_amt
					,      t13_bipay4 as bi_amt
					,      t13_bonbu_tot4 as tot_amt

					,     (select ifnull(sum(deposit_amt), 0)
							 from unpaid_deposit
							where org_no        = t01_ccode
							  and deposit_jumin = t13_jumin
							  and deposit_auto  = 'Y'
							  and deposit_mem   = t01_yoyangsa_id1
							  /*and left(replace(deposit_reg_dt, '-', ''), 6) = '$deposit_ym'*/
							  and deposit_yymm = '$year$month'
							  and del_flag = 'N'
							) as deposit_amt

					,     0 as deposit_ahead
					,     0 as tot_deposit

					  from t01iljung

					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin

					 inner join m02yoyangsa
						on m02_ccode  = t01_ccode
					   and m02_mkind  = t01_mkind
					   and m02_yjumin = t01_yoyangsa_id1

					 inner join t13sugupja
						on t13_ccode      = m03_ccode
					   and t13_mkind      = m03_mkind
					   and t13_jumin      = m03_jumin
					   and t13_type       = '2'
					   and t13_bonbu_tot4 > 0
					   and t13_pay_date   = '$year$month'

					 inner join salary_basic
						on salary_basic.org_no       = t13_ccode
					   and salary_basic.salary_yymm  = t13_pay_date
					   and salary_basic.salary_jumin = m02_yjumin

					 where t01_ccode    = '$code'
					   and t01_mkind    = '$kind'
					   and t01_del_yn   = 'N'
					   and t01_toge_umu = 'Y'
					   and left(t01_sugup_date,6) = '$year$month'
					 order by mem_nm, per_nm";
		#}

		//if ($debug) echo nl2br($sql);

		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch_assoc();

		$row_count = $conn->row_count();

		$seq = 0;

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($mem_cd != $row['mem_cd']){
				$mem_cd  = $row['mem_cd'];
				$seq ++;
			}

			if ($row['deposit_amt'] == 0 && $row['deposit_ahead'] == 0){
				$in_amt = $row['tot_deposit'];

				if ($in_amt < 0) $in_amt = 0;
				if ($in_amt > $row['tot_amt']) $in_amt = $row['tot_amt'];

				$is_checked = '';
			}else{
				$in_amt = $row['deposit_ahead'];
				$is_checked = 'checked';
			}
			$in_amt = 0;?>
			<tr>
				<td class="center"	><?=$seq;?></td>
				<td class="left"	><?=$row['mem_nm'];?></td>
				<td class="left"	><?=$row['per_nm'];?></td>
				<td class="right"	><?=number_format($row['bonin_amt']);?></td>
				<td class="right"	><?=number_format($row['over_amt']);?></td>
				<td class="right"	><?=number_format($row['bi_amt']);?></td>
				<td class="right"	><?=number_format($row['tot_amt']);?></td>
				<td class="center"	>
				<?
					if ($salary_cls_yn == 'N'){?>
						<input name="check[]" type="checkbox" class="checkbox" value="<?=$i;?>" onclick="set_amt(<?=$i;?>);" <?=$is_checked;?>><?
					}else{?>
						<input name="check[]" type="hidden" value="-1"><?
					}
				?>
				</td>
				<td class="right"	id="deposit_txt[]"><?=number_format($row['deposit_amt']);?></td>
				<td class="right last"><? /*=number_format($in_amt);*/ ?></td>

				<input name="mem_cd[]"	type="hidden" value="<?=$ed->en($row['mem_cd']);?>">
				<input name="per_cd[]"	type="hidden" value="<?=$ed->en($row['per_cd']);?>">
				<input name="per_nm[]"	type="hidden" value="<?=$ed->en($row['per_nm']);?>">

				<input name="bonin_amt[]"	type="hidden" value="<?=$row['bonin_amt'];?>">
				<input name="over_amt[]"	type="hidden" value="<?=$row['over_amt'];?>">
				<input name="bi_amt[]"		type="hidden" value="<?=$row['bi_amt'];?>">
				<input name="tot_amt[]"		type="hidden" value="<?=$row['tot_amt'];?>">
				<input name="deposit_amt[]" type="hidden" value="<?=$row['deposit_amt'];?>" tag="0">
				<input name="in_amt[]"		type="hidden" value="<?=$in_amt;?>">
				<input name="is_update[]"	type="hidden" value="<? if($row['deposit_amt']>0){?>Y<?}else{?>N<?} ?>">

				<input name="fam_pay_yn[]" type="hidden" value="<?=$row['fam_pay_yn'];?>">
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
		<?
			if ($row_count > 0){?>
				<td class="left last bottom" colspan="10"><?=$myF->message($row_count, 'N');?></td><?
			}else{?>
				<td class="center last bottom" colspan="10"><?=$myF->message('nodata', 'N');?></td><?
			}
		?>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">

<input type="hidden" name="year" value="<?=$year;?>">
<input type="hidden" name="month" value="<?=$month;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>