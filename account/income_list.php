<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
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
<?
	include_once('income_var.php');
?>
<div class="title"><?=$io_title;?>내역조회</div>
<?
	include('income_head.php');
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="80px">
		<col>
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="110px">
		<col width="70px">
		<col width="70px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head"><?=$io_title;?>일자</th>
			<th class="head"><?=$io_title;?>내용</th>
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
		$sql = "select *
				  from center_".$io_table."
				 where org_no = '$find_center_code'
				   and ".$io_table."_acct_dt like '".$year."-".$month."%'
				   and del_flag = 'N'
				 order by ".$io_table."_acct_dt desc, ".$io_table."_item";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
		?>	<tr>
				<td class="center"		><?=$i+1;?></td>
				<td class="center"		><?=$row[$io_table.'_acct_dt'];?></td>
				<td class="left"		><?=$row[$io_table.'_item'];?></td>
				<td class="center"		><?=$row['vat_yn'] == 'Y' ? '유' : '무';?></td>
				<td class="right"		><?=number_format($row[$io_table.'_amt']);?></td>
				<td class="right"		><?=number_format($row[$io_table.'_vat']);?></td>
				<td class="right"		><?=number_format($row[$io_table.'_amt']+$row[$io_table.'_vat']);?></td>
				<td class="center"		><?=$myF->bizStyle($row['taxid']);?></td>
				<td class="left"		><?=$row['biz_group'];?></td>
				<td class="left last"	><?=$row['biz_type'];?></td>
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
<?
	//include('income_head.php');
?>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>