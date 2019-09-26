<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	$code	= $_POST['code'] != '' ? $_POST['code'] : $_SESSION['userCenterCode'];
	$year	= $_POST['year'] != '' ? $_POST['year'] : date('Y', mktime());
	$month	= $_POST['month'] != '' ? $_POST['month'] : date('m', mktime());
	$month	= (intval($month) < 10 ? '0' : '').intval($month);
	$name	= $ed->de($_POST['name']);
	$member	= $ed->de($_POST['member']);

	$init_year = $myF->year();
?>
<script language='javascript'>
<!--

function search_month(month){
	var f = document.f;

	f.month.value = month;
	f.submit();
}

function search_list(){
	var f = document.f;

	f.action = 'member_iljung.php';
	f.submit();
}

-->
</script>
<div class="title">수급내역(요양보호사)_상세</div>

<form name="f" method="post">

<table class="my_table my_border">
	<colgroup>
		<col width="70px">
		<col width="111px">
		<col width="50px">
		<col width="90px">
		<col>
		<col width="10px">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">요양보호사</th>
			<td class="left"><?=$name;?></td>
			<th class="head">년월</th>
			<td class="last">
				<select name="year" style="width:auto;">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
						<option value="<?=$i;?>" <? if($year == $i){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>년
			</td>
			<td class="last">
			<?
				for($i=1; $i<=12; $i++){
					$class = 'my_month ';

					if ($i == intval($month)){
						$class .= 'my_month_y ';
						$text = '<span style="cursor:default;">'.$i.'월</span>';
					}else{
						$class .= 'my_month_g ';
						$text = '<a href="#" onclick="search_month(\''.$i.'\');">'.$i.'월</a>';
					}

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:3px;';
					}?>
					<div class="<?=$class;?>" style="<?=$style;?>"><?=$text;?></div><?
				}
			?>
			</td>
			<td class="right last" style="padding-top:1px;">
				<a href="#" onclick="search_list();"><img src="../image/btn_prev.png"></a>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="50px">
		<col width="40px">
		<col width="90px">
		<col width="90px" span="6">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">계획<br>일자</th>
			<th class="head" rowspan="2">계획일정</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" colspan="3">실적</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">요양</th>
			<th class="head">목욕</th>
			<th class="head">간호</th>
			<th class="head">요양</th>
			<th class="head">목욕</th>
			<th class="head">간호</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select center_code
				,      center_kind
				,      member_code
				,      m02_yname as member_name
				,      cast(date_format(plan_date, '%d') as unsigned) as plan_date
				,      date_format(concat(plan_date, plan_start_time, '01'), '%H:$i') as plan_start_time
				,      date_format(concat(plan_date, plan_to_time, '01'), '%H:$i') as plan_to_time
				,      sum(plan2) as plan2
				,      sum(plan5) as plan5
				,      sum(plan8) as plan8
				,      sum(conf2) as conf2
				,      sum(conf5) as conf5
				,      sum(conf8) as conf8
				  from (
					   select t01_ccode as center_code
					   ,      t01_mkind as center_kind
					   ,      t01_yoyangsa_id1 as member_code
					   ,      t01_sugup_date as plan_date
					   ,      t01_sugup_fmtime as plan_start_time
					   ,      t01_sugup_totime as plan_to_time
					   ,      sum(case when t01_svc_subcode = '200' then t01_sugup_soyotime else 0 end) as plan2
					   ,      sum(case when t01_svc_subcode = '500' then 1 else 0 end) as plan5
					   ,      sum(case when t01_svc_subcode = '800' then 1 else 0 end) as plan8
					   ,      sum(case when t01_svc_subcode = '200' and t01_status_gbn = '1' then t01_conf_soyotime else 0 end) as conf2
					   ,      sum(case when t01_svc_subcode = '500' and t01_status_gbn = '1' then 1 else 0 end) as conf5
					   ,      sum(case when t01_svc_subcode = '800' and t01_status_gbn = '1' then 1 else 0 end) as conf8
						 from t01iljung
						where t01_ccode         = '$code'
						  and t01_sugup_date like '$year$month%'
						  and t01_yoyangsa_id1  = '$member'
						  and t01_del_yn        = 'N'
						group by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime, t01_yoyangsa_id1
						union all
					   select t01_ccode as center_code
					   ,      t01_mkind as center_kind
					   ,      t01_yoyangsa_id2 as member_code
					   ,      t01_sugup_date as plan_date
					   ,      t01_sugup_fmtime as plan_start_time
					   ,      t01_sugup_totime as plan_to_time
					   ,      sum(case when t01_svc_subcode = '200' then t01_sugup_soyotime else 0 end) as plan2
					   ,      sum(case when t01_svc_subcode = '500' then 1 else 0 end) as plan5
					   ,      sum(case when t01_svc_subcode = '800' then 1 else 0 end) as plan8
					   ,      sum(case when t01_svc_subcode = '200' and t01_status_gbn = '1' then t01_conf_soyotime else 0 end) as conf2
					   ,      sum(case when t01_svc_subcode = '500' and t01_status_gbn = '1' then 1 else 0 end) as conf5
					   ,      sum(case when t01_svc_subcode = '800' and t01_status_gbn = '1' then 1 else 0 end) as conf8
						 from t01iljung
						where t01_ccode         = '$code'
						  and t01_sugup_date like '$year$month%'
						  and t01_yoyangsa_id2  = '$member'
						  and t01_del_yn        = 'N'
						group by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime, t01_yoyangsa_id2
					   ) as t
				 inner join m02yoyangsa
					on m02_ccode  = center_code
				   and m02_mkind  = center_kind
				   and m02_yjumin = member_code
				 group by center_code, center_kind, member_code, m02_yname, plan_date, plan_start_time, plan_to_time
				 order by plan_date, plan_start_time, plan_to_time";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$i+1;?></td>
				<td class="right"><?=$row['plan_date'];?>일&nbsp;</td>
				<td class="center"><?=$row['plan_start_time'];?>~<?=$row['plan_to_time'];?></td>
				<td class="right"><?=$myF->getMinToHM($row['plan2']);?></td>
				<td class="right"><?=$row['plan5'];?>회</td>
				<td class="right"><?=$row['plan8'];?>회</td>
				<td class="right"><?=$myF->getMinToHM($row['conf2']);?></td>
				<td class="right"><?=$row['conf5'];?>회</td>
				<td class="right"><?=$row['conf8'];?>회</td>
				<td class="last">&nbsp;</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom left last" colspan="10"><?=$myF->message($row_count, 'N');?></td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"	value="<?=$code;?>">
<input type="hidden" name="month"	value="<?=$month;?>">
<input type="hidden" name="name"	value="<?=$ed->en($name);?>">
<input type="hidden" name="member"	value="<?=$ed->en($member);?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>