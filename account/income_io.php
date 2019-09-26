<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.action = 'income_io.php';
	f.submit();
}

function detail(month){
	var f = document.f;

	f.action = 'income_io_detail.php?find_month='+month;
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}
//-->
</script>
<script type="text/javascript" src="../js/acct.js"></script>
<form name="f" method="post">
<?
	include_once('income_var.php');

	$init_year = $myF->year();
?>
<div class="title title_border">수입/지출조회</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="40px">
		<col width="50px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">년도</th>
			<td>
				<select name="find_year" style="width:auto;">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
						<option value="<?=$i;?>" <? if($i == $find_year){?>selected<?} ?>><?=$i;?>년</option><?
					}
				?>
				</select>
			</td>
			<th class="head">기관명</th>
			<td>
				<input name="find_center_name" type="text" value="<?=$find_center_name;?>" maxlength="20" onkeypress="" style="width:100%;" onFocus="this.select();">
			</td>
			<td class="left last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="90px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">전화번호</th>
			<th class="head">담당자</th>
			<th class="head last">수입/지출내역</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = "";

		if ($find_center_code != ''){
			$wsl .= " and org_no = '$find_center_code'";
		}

		$sql = "select org_no as code
				,      m00_cname as name
				,      m00_ctel as tel
				,      m00_mname as inchange
				,      sum(m01) as m01
				,      sum(m02) as m02
				,      sum(m03) as m03
				,      sum(m04) as m04
				,      sum(m05) as m05
				,      sum(m06) as m06
				,      sum(m07) as m07
				,      sum(m08) as m08
				,      sum(m09) as m09
				,      sum(m10) as m10
				,      sum(m11) as m11
				,      sum(m12) as m12
				  from (
					   select org_no
					   ,      case date_format(income_acct_dt, '%m') when '01' then 1 else 0 end as m01
					   ,      case date_format(income_acct_dt, '%m') when '02' then 1 else 0 end as m02
					   ,      case date_format(income_acct_dt, '%m') when '03' then 1 else 0 end as m03
					   ,      case date_format(income_acct_dt, '%m') when '04' then 1 else 0 end as m04
					   ,      case date_format(income_acct_dt, '%m') when '05' then 1 else 0 end as m05
					   ,      case date_format(income_acct_dt, '%m') when '06' then 1 else 0 end as m06
					   ,      case date_format(income_acct_dt, '%m') when '07' then 1 else 0 end as m07
					   ,      case date_format(income_acct_dt, '%m') when '08' then 1 else 0 end as m08
					   ,      case date_format(income_acct_dt, '%m') when '09' then 1 else 0 end as m09
					   ,      case date_format(income_acct_dt, '%m') when '10' then 1 else 0 end as m10
					   ,      case date_format(income_acct_dt, '%m') when '11' then 1 else 0 end as m11
					   ,      case date_format(income_acct_dt, '%m') when '12' then 1 else 0 end as m12
					     from center_income
					    where income_acct_dt like '$find_year%'
						  and del_flag = 'N' $wsl
						union all
					   select org_no
					   ,      case date_format(outgo_acct_dt, '%m') when '01' then 1 else 0 end m01
					   ,      case date_format(outgo_acct_dt, '%m') when '02' then 1 else 0 end m02
					   ,      case date_format(outgo_acct_dt, '%m') when '03' then 1 else 0 end m03
					   ,      case date_format(outgo_acct_dt, '%m') when '04' then 1 else 0 end m04
					   ,      case date_format(outgo_acct_dt, '%m') when '05' then 1 else 0 end m05
					   ,      case date_format(outgo_acct_dt, '%m') when '06' then 1 else 0 end m06
					   ,      case date_format(outgo_acct_dt, '%m') when '07' then 1 else 0 end m07
					   ,      case date_format(outgo_acct_dt, '%m') when '08' then 1 else 0 end m08
					   ,      case date_format(outgo_acct_dt, '%m') when '09' then 1 else 0 end m09
					   ,      case date_format(outgo_acct_dt, '%m') when '10' then 1 else 0 end m10
					   ,      case date_format(outgo_acct_dt, '%m') when '11' then 1 else 0 end m11
					   ,      case date_format(outgo_acct_dt, '%m') when '12' then 1 else 0 end m12
					     from center_outgo
					    where outgo_acct_dt like '$find_year%'
						  and del_flag = 'N' $wsl
					   ) as t
				 inner join m00center
				    on m00_mcode = org_no
				   and m00_mkind = (select min(m00_mkind) from m00center where m00_mcode = org_no)
				 group by org_no";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i); ?>
			<tr>
				<td class="center"		><?=$i+1;?></td>
				<td class="left"		><?=$row['name'];?></td>
				<td class="left"		><?=$myF->phoneStyle($row['tel']);?></td>
				<td class="left"		><?=$row['inchange'];?></td>
				<td class="left last">
				<?
					for($j=1; $j<=12; $j++){
						$mon = ($j<10?'0':'').$j;

						if ($j == 1){
							$style = 'float:left;';
						}else{
							$style = 'float:left; margin-left:3px;';
						}

						if ($row['m'.$mon] > 0){
							$class = 'my_month my_month_y';
						}else{
							$class = 'my_month my_month_g';
						}?>
						<div class="<?=$class;?>" style="<?=$style;?>"><a href="#" onclick="detail('<?=$mon;?>');"><?=$j;?>월</a></div><?
					}
				?>
				</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
</table>

<table class="my_table" style="width:100%; border-bottom:none;">
	<tbody>
	<?
		if ($row_count > 0){?>
			<tr>
				<td class="left bottom last" colspan="5"><?=$myF->message($row_count, 'N');?></td>
			</tr><?
		}else{?>
			<tr>
				<td class="center bottom last" colspan="5"><?=$myF->message('nodata', 'N');?></td>
			</tr><?
		}
	?>
	</tbody>
</table>

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>