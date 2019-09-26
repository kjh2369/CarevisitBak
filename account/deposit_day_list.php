<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_REQUEST['code'];
	$kind	= $_REQUEST['kind'];
	$year	= $_REQUEST['year'];
	$month	= $_REQUEST['month'];

	// 총 미수금 조회
	$sql = "select (select ifnull(sum(t13_misu_amt), 0)
					  from t13sugupja
					 where t13_ccode = '$code'
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

	$sql = "select ifnull(sum(deposit_amt), 0)
			  from unpaid_deposit
			 where org_no = '$code'
			   and left(deposit_reg_dt, 7) = '$year-$month'
			   and del_flag                = 'N'";
	$month_tot = $conn->get_data($sql);
?>

<script src="../js/account.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function month_list(){
	var f = document.f;

	f.action = 'deposit_month_list.php';
	f.submit();
}

function delete_amt(ent_dt, ent_seq){
	var f = document.f;

	if (!confirm('선택하신 입금내역을 삭제하시겠습니까?')) return;

	f.ent_dt.value = ent_dt;
	f.ent_seq.value = ent_seq;
	f.action = 'deposit_del_ok.php';
	f.submit();
}

function modify_amt(jumin, ent_dt, ent_seq){
	var f = document.f;

	f.jumin.value = jumin;
	f.ent_dt.value = ent_dt;
	f.ent_seq.value = ent_seq;
	f.action = 'unpaid_reg.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<form name="f" method="post">

<div class="title">상세입금내역조회</div>

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
				<img src="../image/btn_prev.png" style="cursor:pointer;" onclick="month_list();">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="100px">
		<col width="95px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">일</th>
			<th class="head">입금자</th>
			<th class="head">입금금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<tr>
		<td class="right sum" colspan="2">합계</td>
		<td class="right sum"><?=number_format($month_tot);?></td>
		<td class="last sum"></td>
	</tr>
	<?
		$sql = "select deposit_ent_dt as ent_dt
				,      deposit_seq as ent_seq
				,      deposit_reg_dt as dt
				,      deposit_jumin as jumin
				,      m03_name as name
				,      deposit_amt as amt
				,      deposit_auto as auto_yn
				  from unpaid_deposit
				 inner join m03sugupja
				    on m03_ccode = org_no
				   and m03_mkind = ".$conn->_client_kind()."
				   and m03_jumin = deposit_jumin
				 where org_no = '$code'
				   and deposit_reg_dt like '$year-$month%'
				   and del_flag = 'N'
				 order by deposit_reg_dt desc, m03_name";

		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch_assoc();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$row['dt'];?></td>
				<td class="left"><?=$row['name'];?></td>
				<td class="right"><?=number_format($row['amt']);?></td>
				<td class="left last">
				<?
					if ($row['auto_yn'] == 'N'){?>
						<!--<span class="btn_pack small"><button type="button" onclick="modify_amt('<?=$ed->en($row['jumin']);?>','<?=$ed->en($row['ent_dt']);?>','<?=$ed->en($row['ent_seq']);?>');">수정</button></span>-->
						<span class="btn_pack small"><button type="button" onclick="delete_amt('<?=$ed->en($row['ent_dt']);?>','<?=$ed->en($row['ent_seq']);?>');">삭제</button></span><?
					}else{?>
						<span>본인부담금 자동입금</span><?
					}?>
				</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr><?
		if ($row_count > 0){?>
			<td class="left last bottom" colspan="4"><?=$myF->message($row_count, 'N');?></td><?
		}else{?>
			<td class="center last bottom" colspan="4"><?=$myF->message('nodata', 'N');?></td><?
		}?>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">
<input type="hidden" name="year" value="<?=$year;?>">
<input type="hidden" name="month" value="<?=$month;?>">
<input type="hidden" name="ent_dt" value="">
<input type="hidden" name="ent_seq" value="">
<input type="hidden" name="jumin" value="">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>