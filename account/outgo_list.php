<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	/*
	 * 기능		: 기관등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 */

	include_once('income_var.php');
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
//-->
</script>
<script type="text/javascript" src="../js/acct.js"></script>
<form name="f" method="post">
<div class="title">지출내역조회</div>
<?
	include('income_head.php');
?>
<table class="my_table my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="50px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">지출일자</th>
			<th class="head">지출내용</th>
			<th class="head">부가세구분</th>
			<th class="head">금액</th>
			<th class="head">부가세</th>
			<th class="head">합계</th>
			<th class="head">사업자등록번호</th>
			<th class="head">업태</th>
			<th class="head last">업종</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select income_ent_dt
				,      income_seq
				,      income_acct_dt
				,      income_item
				,      income_amt
				  from center_income
				 where org_no = '$find_center_code'
				   and income_acct_dt between '$find_from_date' and '$find_to_date'
				   and del_flag = 'N'
				 order by income_acct_dt desc";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
			?>	<tr>
					<td class="center"><?=$i+1;?></td>
					<td class="center"><?=$row['income_acct_dt'];?></td>
					<td class="left"><?=$row['income_item'];?></td>
					<td class="right"><?=number_format($row['income_amt']);?></td>
					<td class="other">&nbsp;</td>
				</tr><?
			}
		}else{
		?>	<tr>
				<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
</table>
<?
	include('income_head.php');
?>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>