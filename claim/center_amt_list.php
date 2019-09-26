<?
	include_once("../inc/_header.php");
	//include_once("../inc/_http_uri.php");
	//include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_SESSION['userCenterCode'];
	$kind	= $_SESSION['userCenterKind'][0];
	$year	= $_POST['year'] ? $_POST['year'] : date('Y', mktime());

	$init_year = $myF->year();
?>

<script src="../js/claim.js" type="text/javascript"></script>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function list(){
	var f = document.f;

	f.submit();
}

function detail(month){
	var f = document.f;

	f.month.value = month;
	f.action = 'center_amt_detail.php';
	f.submit();
}

-->
</script>

<form name="f" method="post">

<div class="title">4대보험료내역</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="80px">
		<col width="60px">
		<col >
	</colgroup>
	<tbody>
		<tr>
			<th class="head">년도</th>
			<td>
				<select name="year" style="width:auto;">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){ ?>
						<option value="<?=$i;?>" <? if($year == $i){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>년
			</td>
			<td class="left last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="list();">조회</button></span>
				
			</td>
			<td class="left last">
				<span style="color:red; font-weight:bold;">※ 산재보험료는 요양 비율로만 계산한 것이며, 실제 고지된 것과 다를 수 있습니다.</span>
			</td>
			<td class="right last">
				<span class="btn_pack m"><button type="button" onclick="printCenter('<?=$code;?>','<?=$kind;?>','<?=$year;?>','');">출력</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan=2>월</th>
			<td class="head" colspan=5>요양보호사 부담</td>
			<td class="head" colspan=6>기관 부담</td>
			<td class="last sum head" rowspan=2>합계</td>
		</tr>
		<tr>
			<th class="head">국민연금</th>
			<th class="head">건강보험</th>
			<th class="head">장기요양</th>
			<th class="head">고용보험</th>
			<th class="sum head">소계</th>
			<th class="head">국민연금</th>
			<th class="head">건강보험</th>
			<th class="head">장기요양</th>
			<th class="head">고용보험</th>
			<th class="head">산재보험</th>
			<th class="sum head">소계</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select right(salary_basic.salary_yymm, 2) as mm
			,      sum(salary_basic.pension_amt) as person_pension
			,      sum(salary_basic.health_amt) as person_health
			,      sum(salary_basic.care_amt) as person_care
			,      sum(salary_basic.employ_amt) as person_employ
			,      sum(salary_basic.pension_amt) +
				   sum(salary_basic.health_amt) +
				   sum(salary_basic.care_amt) +
				   sum(salary_basic.employ_amt) as person_tot
			,      sum(salary_center_amt.pension_amt) as center_pension
			,      sum(salary_center_amt.health_amt) as center_health
			,      sum(salary_center_amt.care_amt) as center_care
			,      sum(salary_center_amt.employ_amt) as center_employ
			,      sum(salary_center_amt.sanje_amt) as center_sanje
			,      sum(salary_center_amt.pension_amt) +
				   sum(salary_center_amt.health_amt) +
				   sum(salary_center_amt.care_amt) +
				   sum(salary_center_amt.employ_amt) +
				   sum(salary_center_amt.sanje_amt) as center_tot
			  from salary_basic
			 inner join salary_center_amt
				on salary_center_amt.org_no       = salary_basic.org_no
			   and salary_center_amt.salary_yymm  = salary_basic.salary_yymm
			   and salary_center_amt.salary_jumin = salary_basic.salary_jumin";
		/*
		$sql .= " INNER JOIN (
						SELECT jumin
						,      employ_stat
						,      DATE_FORMAT(join_dt, '%Y%m') AS join_ym
						,      DATE_FORMAT(ifnull(quit_dt,'9999-12-31'), '%Y%m') AS quit_ym
						  FROM mem_his
						 WHERE org_no = '".$code."'
						) AS his
					 ON his.jumin = salary_basic.salary_jumin
					AND his.join_ym <= salary_basic.salary_yymm
					AND his.quit_ym >= salary_basic.salary_yymm";
		*/
		$sql .= " where salary_basic.org_no = '$code'
			        and salary_basic.salary_yymm like '$year%'
			      group by right(salary_basic.salary_yymm, 2)
			      order by mm";
		
		//if($debug) echo nl2br($sql);

		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$total = $row['person_tot'] + $row['center_tot'];
			?>


			<tr>
				<td class="center"><a href="#" onclick="detail(<?=$row['mm']?>);"><?=$row['mm'];?>월</a></td>
				<td class="right"><?=number_format($row['person_pension']);?></td>
				<td class="right"><?=number_format($row['person_health']);?></td>
				<td class="right"><?=number_format($row['person_care']);?></td>
				<td class="right"><?=number_format($row['person_employ']);?></td>
				<td class="sum right"><?=number_format($row['person_tot']);?></td>
				<td class="right"><?=number_format($row['center_pension']);?></td>
				<td class="right"><?=number_format($row['center_health']);?></td>
				<td class="right"><?=number_format($row['center_care']);?></td>
				<td class="right"><?=number_format($row['center_employ']);?></td>
				<td class="right"><?=number_format($row['center_sanje']);?></td>
				<td class="sum right"><?=number_format($row['center_tot']);?></td>
				<td class="sum right last"><?=number_format($total);?></td>

				<!--td class="left last">
					<span class="btn_pack m"><button type="button" onclick="detail(<?=$row['mm']?>);">상세</button></span>
				</td-->
			</tr>

			<?
				$tot_person_pension	+= $row['person_pension'];
				$tot_person_health	+= $row['person_health'];
				$tot_person_care	+= $row['person_care'];
				$tot_person_employ	+= $row['person_employ'];
				$tot_person_tot	+= $row['person_tot'];
				$tot_center_pension	+= $row['center_pension'];
				$tot_center_health	+= $row['center_health'];
				$tot_center_care	+= $row['center_care'];
				$tot_center_employ	+= $row['center_employ'];
				$tot_center_sanje	+= $row['center_sanje'];
				$tot_center_tot	+= $row['center_tot'];
				$tot_total	+= $total;
		}

		$conn->row_free();
	?>
		<tr>
			<td class="sum center">합계</td>
			<td class="sum right"><?=number_format($tot_person_pension);?></td>
			<td class="sum right"><?=number_format($tot_person_health);?></td>
			<td class="sum right"><?=number_format($tot_person_care);?></td>
			<td class="sum right"><?=number_format($tot_person_employ);?></td>
			<td class="sum right"><?=number_format($tot_person_tot);?></td>
			<td class="sum right"><?=number_format($tot_center_pension);?></td>
			<td class="sum right"><?=number_format($tot_center_health);?></td>
			<td class="sum right"><?=number_format($tot_center_care);?></td>
			<td class="sum right"><?=number_format($tot_center_employ);?></td>
			<td class="sum right"><?=number_format($tot_center_sanje);?></td>
			<td class="sum right"><?=number_format($tot_center_tot);?></td>
			<td class="sum right last"><?=number_format($tot_total);?></td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="left last bottom" colspan="5"><?=$myF->message($row_count, 'N');?></td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"   value="<?=$code;?>">
<input type="hidden" name="kind"   value="<?=$kind;?>">
<input type="hidden" name="month"  value="">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
