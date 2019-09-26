<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code         = $_SESSION['userCenterCode'];
	$year	      = $_POST['year'] ? $_POST['year'] : date('Y', mktime());
	$svc_homecare = $_POST['svc_homecare'];
	$svc_voucher  = $_POST['svc_voucher'];

	if (empty($svc_homecare) && empty($svc_voucher)){
		$svc_homecare = 'Y';
		$svc_voucher  = 'Y';
	}

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
	f.action = 'my_amt_detail.php';
	f.submit();
}

-->
</script>

<form name="f" method="post">

<div class="title">본인부담금 내역</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="80px">
		<col width="150px">
		<col>
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
			<td>
			<?
				$sql = "select sum(case m00_mkind when '0' then 1 else 0 end) as homecare
						,      sum(case m00_mkind when '1' then 1
											      when '2' then 1
											      when '3' then 1
											      when '4' then 1 else 0 end) as voucher
						  from m00center
						 where m00_mcode  = '$code'
						   and m00_del_yn = 'N'";

				$svc = $conn->get_array($sql);

				if ($svc['homecare'] > 0) echo '<input name=\'svc_homecare\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' '.($svc_homecare == 'Y' ? 'checked' : '').'>재가요양';
				if ($svc['voucher']  > 0) echo '<input name=\'svc_voucher\'  type=\'checkbox\' class=\'checkbox\' value=\'Y\' '.($svc_voucher  == 'Y' ? 'checked' : '').'>바우처';
			?>
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
				 where t13_ccode       = '$code'
				   and t13_pay_date like '$year%'
				   and t13_type        = '2'";

		if ($svc_homecare == 'Y' && $svc_voucher == 'Y'){
		}else if ($svc_homecare == 'Y'){
			$sql .= " and t13_mkind = '0'";
		}else{
			$sql .= " and t13_mkind != '0'";
		}

		$sql .= " group by substring(t13_pay_date, 5, 2)
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
					<span class="btn_pack m"><button type="button" onclick="detail('<?=$row['yymm'];?>');">개인별</button></span>
					<span class="btn_pack m"><button type="button" onclick="_printPaymentsAcc('<?=$code;?>','<?=$year.$row['yymm'];?>',2,1,'<?=$svc_homecare;?>','<?=$svc_voucher;?>');">월별(가나다)</button></span>
					<span class="btn_pack m"><button type="button" onclick="_printPaymentsAcc('<?=$code;?>','<?=$year.$row['yymm'];?>',1,1,'<?=$svc_homecare;?>','<?=$svc_voucher;?>');">월별(수납일)</button></span>
					<span class="btn_pack m"><button type="button" onclick="_printPaymentIssu('<?=$code;?>','<?=$year.$row['yymm'];?>','<?=$svc_homecare;?>','<?=$svc_voucher;?>');">발급대장</button></span>
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