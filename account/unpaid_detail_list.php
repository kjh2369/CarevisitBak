<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code		= $_POST['code'];
	$kind		= $_POST['kind'];
	$jumin		= $ed->de($_POST['jumin']);
	$name		= $conn->client_name($code, $jumin);
	$find_name	= $_POST['find_name'];
?>

<script src="../js/account.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function list(){
	var f = document.f;

	f.action = 'unpaid_list.php';
	f.submit();
}

function reg(jumin){
	var f = document.f;

	f.jumin.value = jumin;
	f.action = 'unpaid_reg.php';
	f.submit();
}

function detail(jumin){
	var f = document.f;

	f.jumin.value = jumin;
	f.action = 'unpaid_detail_list.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<form name="f" method="post">

<div class="title">미수금 상세내역</div>

<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">수급자명</th>
			<td class="left last"><?=$name;?></td>
			<td class="right last" style="padding-top:1px;">
				<img src="../image/btn_prev.png" style="cursor:pointer;" onclick="list();">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="120px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">년월</th>
			<th class="head">등급</th>
			<th class="head">구분</th>
			<th class="head">미수금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<?
		$sql = "select 1 as idx
				,      concat(substring(t13_pay_date, 1, 4), '.', substring(t13_pay_date, 5, 2)) as yymm
				,      t.lvl
				,      t.stp
				,      t.bonin_yul
				,      t13_bonbu_tot4 /*t13_misu_amt*/ as unpaid_amt
				  from t13sugupja
				 inner join (
					   select m03.c_code as c_code
					   ,      m03.c_kind as c_kind
					   ,      m03.jumin as jumin
					   ,      m03.name as name
					   ,      lvl.m81_name as lvl
					   ,      stp.m81_name as stp
					   ,      m03.kind as kind
					   ,      m03.bonin_yul as bonin_yul
					   ,      m03.sdate as sdt
                       ,      m03.edate as edt
						 from (
							  select m03_ccode as c_code
							  ,      m03_mkind as c_kind
							  ,      m03_jumin as jumin
							  ,      m03_name as name
							  ,      m03_ylvl as lvl
							  ,      m03_skind as kind
							  ,      m03_bonin_yul as bonin_yul
							  ,      m03_sdate as sdate
							  ,      m03_edate as edate
								from m03sugupja
							   where m03_ccode = '$code'
								 and m03_mkind = '$kind'
								 and m03_jumin = '$jumin'
							   union all
							  select m31_ccode as c_code
							  ,      m31_mkind as c_kind
							  ,      m03_jumin as jumin
							  ,      m03_name as name
							  ,      m31_level as lvl
							  ,      m31_kind as kind
							  ,      m31_bonin_yul as bonin_yul
							  ,      m31_sdate as sdate
							  ,      m31_edate as edate
								from m31sugupja
							   inner join m03sugupja
								  on m03_ccode = m31_ccode
								 and m03_mkind = m31_mkind
								 and m03_jumin = m31_jumin
							   where m31_ccode = '$code'
								 and m31_mkind = '$kind'
								 and m31_jumin = '$jumin'
							  ) as m03
						inner join m81gubun as lvl
						   on lvl.m81_gbn  = 'LVL'
						  and lvl.m81_code = m03.lvl
						inner join m81gubun as stp
						   on stp.m81_gbn  = 'STP'
						  and stp.m81_code = m03.kind
					   ) as t
					on t13_ccode = t.c_code
				   and t13_mkind = t.c_kind
				   and t13_jumin = t.jumin
				   and t13_pay_date between left(t.sdt, 6) and left(t.edt, 6)
				   and t13_bonin_yul = case when t13_bonin_yul = '1' then t.kind
											when t13_bonin_yul = '2' then t.kind
											when t13_bonin_yul = '3' then t.kind
											when t13_bonin_yul = '9' then t.kind
											else t.bonin_yul end
				   and t13_type  = '2'";

		$sql.= " union all
				select 2 as idx
				,      concat(substring(t13_pay_date, 1, 4), '년 소계') as yymm
				,      ''
				,      ''
				,      ''
				,      sum(t13_bonbu_tot4 /*t13_misu_amt*/) as unpaid_amt
				  from t13sugupja
				 where t13_ccode = '$code'
				   and t13_mkind = '$kind'
				   and t13_jumin = '$jumin'
				   and t13_type  = '2'
				 group by substring(t13_pay_date, 1, 4)";

		$sql.= " union all
				select 3 as idx
				,      '합계'
				,      ''
				,      ''
				,      ''
				,      sum(t13_bonbu_tot4 /*t13_misu_amt*/) as unpaid_amt
				  from t13sugupja
				 where t13_ccode = '$code'
				   and t13_mkind = '$kind'
				   and t13_jumin = '$jumin'
				   and t13_type  = '2'";

		$sql.= " order by idx, yymm";

		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch_assoc();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr><?
				if ($row['idx'] == 1){
					$class = ''; ?>
					<td class="center"	><?=$row['yymm'];?></td>
					<td class="center"	><?=$row['lvl'];?></td>
					<td class="left"	><?=$row['stp'];?>(<?=$row['bonin_yul'];?>)</td><?
				}else{
					if ($row['idx'] == 2)
						$class = 'sum_sub';
					else
						$class = 'sum'; ?>
					<td class="<?=$class;?> right" colspan="3"><?=$row['yymm'];?></td><?
				}?>
				<td class="<?=$class;?> right"		><?=number_format($row['unpaid_amt']);?></td>
				<td class="<?=$class;?> left last"	></td>
			</tr><?
		}

		$conn->row_free();
	?>
	<tbody>
		<tr>
			<td class="left last bottom" colspan="7">&nbsp;</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">
<input type="hidden" name="find_name" value="<?=$find_name;?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>