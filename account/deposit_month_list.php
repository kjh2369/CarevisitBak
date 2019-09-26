<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];

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

	$sql = "select ifnull(sum(deposit_amt), 0)
			  from unpaid_deposit
			 where org_no = '$code'
			   and deposit_reg_dt like '$year%'
			   and del_flag          = 'N'";
	$year_tot = $conn->get_data($sql);
?>

<script src="../js/account.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function year_list(){
	var f = document.f;

	f.action = 'deposit_list.php';
	f.submit();
}

function day_list(month){
	var f = document.f;

	f.month.value = month;
	f.action = 'deposit_day_list.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<form name="f" method="post">

<div class="title">월별입금내역조회</div>

<table class="my_table my_border">
	<colgroup>
		<col width="80px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">총미수금액</th>
			<td class="left"><?=number_format($unpaid_tot);?></td>
			<th class="head"><?=$year;?>년 입금금액</th>
			<td class="left last"><?=number_format($year_tot);?></td>
			<td class="right last" style="padding-top:1px;">
				<img src="../image/btn_prev.png" style="cursor:pointer;" onclick="year_list();">
			</td>
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
			<th class="head">월</th>
			<th class="head">입금금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select date_format(deposit_reg_dt, '%m') as mm
				,      sum(deposit_amt) as amt
				  from unpaid_deposit
				 where deposit_reg_dt like '$year%'
				   and del_flag = 'N'
				 group by date_format(deposit_reg_dt, '%m')";

		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch_assoc();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$row['mm'];?>월</td>
				<td class="right"><?=number_format($row['amt']);?></td>
				<td class="left last">
					<span class="btn_pack small"><button type="button" onclick="day_list('<?=$row['mm'];?>');">상세내역</button></span>
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
<input type="hidden" name="year" value="<?=$year;?>">
<input type="hidden" name="month" value="">
<input type="hidden" name="unpaid_tot" value="<?=$unpaid_tot;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>