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

	$code	= $_SESSION['userCenterCode'];
	$kind	= '0';
	$year	= $_POST['year'] ? $_POST['year'] : date('Y', mktime());

	$init_year = $myF->year();
?>

<script src="../js/claim.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function list(){
	var f = document.f;

	f.submit();
}

function detail(month){
	var f = document.f;

	f.month.value = month;
	f.action = 'public_amt_detail.php';
	f.submit();
}

-->
</script>

<form name="f" method="post">

<div class="title">공단부담금 내역</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
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
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="120px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">실적급여액</th>
			<th class="head">공단청구액</th>
			<th class="head">본인부담액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select substring(t13_pay_date, 5, 2) as yymm
				,      sum(t13_suga_tot4) as suga_amt
				,      sum(t13_chung_amt4) as chung_amt
				,      sum(t13_bonbu_tot4) as bonin_amt
				from t13sugupja
				where t13_ccode     = '$code'
				and t13_mkind       = '$kind'
				and t13_pay_date like '$year%'
				and t13_type        = '2'
				group by substring(t13_pay_date, 5, 2)
				order by substring(t13_pay_date, 5, 2)";

		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$row['yymm'];?>월</td>
				<td class="right"><?=number_format($row['suga_amt']);?></td>
				<td class="right"><?=number_format($row['chung_amt']);?></td>
				<td class="right"><?=number_format($row['bonin_amt']);?></td>
				<td class="left last">
					<span class="btn_pack m"><button type="button" onclick="detail('<?=$row['yymm'];?>');">상세</button></span>
					<span class="btn_pack m"><button type="button" onclick="printPerson('<?=$code;?>','<?=$kind;?>','<?=$year.$row['yymm'];?>','all','all');">일별수급내역 전체출력</button></span>
					<span class="btn_pack m"><button type="button" onclick="document.getElementById('month').value = '<?=intval($row['yymm']);?>'; _show_person_print(100,2);">대상자현황표 출력</button></span>
				</td>
			</tr><?
		}

		$conn->row_free();
	?>
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