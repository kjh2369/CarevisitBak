<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$type   = $_POST['type'] != '' ? $_POST['type'] : 'Y';

?>

<script src="../js/claim.js" type="text/javascript"></script>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function list(){
	var f = document.f;

	f.submit();
}

function before(){
	var f = document.f;

	f.action = 'center_amt_list.php';
	f.submit();
}

-->
</script>

<form name="f" method="post">

<div class="title">4대보험료내역</div>

<table class="my_table my_border">
	<colgroup>
		<col width="50px">
		<col width="70px">
		<col width="90px">
		<col width="140px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td class="left"><?=$year;?>년 <?=$month;?>월</td>
			<th>부담금내역조회</th>
			<td>
				<select name="type" style="width:auto;">
					<option value="all">전체</option>
					<option value="Y"  <? if($type == 'Y'){?>selected<?} ?>>유</option>
				</select>
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="list();">조회</button></span>
			</td>
			<td class="right last">
				<span class="btn_pack m"><button type="button" onclick="printCenter('<?=$code;?>','<?=$kind;?>','<?=$year;?>','<?=$month?>','<?=$type;?>');">출력</button></span>
				<img src="../image/btn_prev.png" style="cursor:pointer;" onclick="before();">
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan=2>요양사명</th>
			<th class="head" colspan=5>요양보호사 부담</th>
			<th class="head" colspan=6>기관 부담</th>
			<th class="sum last head" rowspan=2>합계</th>
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
		$sql = "select m02_yname as name
				,      salary_basic.salary_jumin as jumin
				,      salary_basic.pension_amt as person_pension
				,      salary_basic.health_amt as person_health
				,      salary_basic.care_amt as person_care
				,      salary_basic.employ_amt as person_employ
				,      salary_basic.pension_amt +
					   salary_basic.health_amt +
					   salary_basic.care_amt +
					   salary_basic.employ_amt as person_tot
				,      salary_center_amt.pension_amt as center_pension
				,      salary_center_amt.health_amt as center_health
				,      salary_center_amt.care_amt as center_care
				,      salary_center_amt.employ_amt as center_employ
				,      salary_center_amt.sanje_amt as center_sanje
				,      salary_center_amt.pension_amt +
					   salary_center_amt.health_amt +
					   salary_center_amt.care_amt +
					   salary_center_amt.employ_amt +
					   salary_center_amt.sanje_amt as center_tot
				  from salary_basic
				 inner join salary_center_amt
					on salary_center_amt.org_no       = salary_basic.org_no
				   and salary_center_amt.salary_yymm  = salary_basic.salary_yymm
				   and salary_center_amt.salary_jumin = salary_basic.salary_jumin
				 inner join m02yoyangsa
					on m02_ccode = salary_basic.org_no
				   and m02_mkind = '0'
				   and m02_yjumin = salary_basic.salary_jumin
				   and m02_del_yn = 'N'";

		//퇴사자 걸러내기
		$sql .= " INNER JOIN (
						SELECT jumin
						,      employ_stat
						  FROM mem_his
						 WHERE org_no = '".$code."'
						   AND DATE_FORMAT(join_dt, '%Y%m') <= '".$year.($month < 10 ? '0'.$month : $month)."'
						   AND DATE_FORMAT(ifnull(quit_dt,'9999-12-31'), '%Y%m') >= '".$year.($month < 10 ? '0'.$month : $month)."'
						) AS his
					 ON his.jumin = salary_basic.salary_jumin";

		$sql .= " where salary_basic.org_no = '$code'
				    and salary_basic.salary_yymm = '".$year.($month < 10 ? '0'.$month : $month)."'";

		if($type == 'Y'){
			$sql .= "and salary_basic.pension_amt
					   + salary_basic.health_amt
					   + salary_basic.care_amt
					   + salary_basic.employ_amt
					   + salary_center_amt.pension_amt
					   + salary_center_amt.health_amt
					   + salary_center_amt.care_amt
					   + salary_center_amt.employ_amt
					   + salary_center_amt.sanje_amt > 0";
		}
		$sql .= " GROUP BY jumin 
		          ORDER BY name  ";

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
				<td class="left"><?=$row['name'];?></td>
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
			</tr><?

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
	</tbody>
	<tbody>
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
<input type="hidden" name="year"   value="<?=$year;?>">
<input type="hidden" name="month"  value="<?=$month;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>