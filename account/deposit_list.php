<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_SESSION['userCenterCode'];
	$kind	= $_SESSION['userCenterKind'][0];

	// 총 미수금 조회
	$sql = "select (select ifnull(sum(t13_misu_amt), 0)
					  from t13sugupja
					 where t13_ccode = '$code'
					   and t13_mkind = '$kind'
					   and t13_type  = '2')
				 - (select ifnull(sum(deposit_amt), 0)
					  from unpaid_deposit
					 where org_no   = '$code'
					   and del_flag = 'N')";
	$unpaid_tot = $conn->get_data($sql);
?>

<script src="../js/account.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function month_list(year){
	var f = document.f;

	f.year.value = year;
	f.action = 'deposit_month_list.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<form name="f" method="post">

<div class="title">입금내역조회</div>

<table class="my_table my_border">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">총미수금액</th>
			<td class="left last"><?=number_format($unpaid_tot);?></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">년도</th>
			<th class="head">입금금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select date_format(deposit_reg_dt, '%Y') as yy
				,      sum(deposit_amt) as amt
				  from unpaid_deposit
				 where del_flag = 'N'
				 group by date_format(deposit_reg_dt, '%Y')";

		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch_assoc();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$row['yy'];?>년</td>
				<td class="right"><?=number_format($row['amt']);?></td>
				<td class="left last">
					<span class="btn_pack small"><button type="button" onclick="month_list('<?=$row['yy'];?>');">월별내역</button></span>
				</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr><?
		if ($row_count > 0){?>
			<td class="left last bottom" colspan="3"><?=$myF->message($row_count, 'N');?></td><?
		}else{?>
			<td class="center last bottom" colspan="3"><?=$myF->message('nodata', 'N');?></td><?
		}?>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">
<input type="hidden" name="year" value="">
<input type="hidden" name="unpaid_tot" value="<?=$unpaid_tot;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>