<?
if($_POST['excel_yn'] != 'Y'){
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
	function detail(){
		var f = document.f;

		f.action = 'income_io_detail.php';
		f.submit();
	}

	function list(){
		var f = document.f;

		f.action = 'income_io.php';
		f.submit();
	}

	function excel(){
		var f = document.f;
		
		f.excel_yn.value = 'Y';
		f.submit();

		//alert('준비중입니다.');
	}

	window.onload = function(){
		__init_form(document.f);
	}
	//-->
	</script>
	<script type="text/javascript" src="../js/acct.js"></script>
	<form name="f" method="post"><?
	include_once('income_var.php');

	$find_month = $_REQUEST['find_month'];
	$find_ym    = $find_year.'-'.$find_month;

	$center_nm = $conn->center_name($find_center_code);
	?>

	<div class="title title_border">수입/지출현황</div>

	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="50px">
			<col width="200px">
			<col width="40px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th>기관명</th>
				<td class="left"><?=$center_nm;?></td>
				<th>년월</th>
				<td class="left"><?=$find_year;?>.<?=$find_month;?></td>
				<td class="right last">
					<span class="btn_pack m icon"><span class="list"></span><button type="button" onclick="list();">리스트</button></span>
					<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="excel();">엑셀</button></span>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%; border-bottom:none;" <?=$border?>><?
}else {
	header('Content-Type: application/vnd.ms-excel');
	header( "Content-type: charset=euc-kr" );
	header('Content-Disposition: attachment;filename="test.xls"');
	header( "Content-Description: test" );
	header('Cache-Control: max-age=0');

	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");

	$find_ym    = $_POST['find_ym'];
	$find_center_code = $_POST['find_center_code'];
	$center_nm = $conn->center_name($find_center_code);

	$border = 'border=1';
	
	include_once("./income_excel_head.php");

	echo '<table border="1">';
}?>

	<colgroup>
		<col width="40px">
		<col width="50px">
		<col width="80px">
		<col>
		<col width="70px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="100px">
		<col width="70px">
		<col width="70px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">구분</th>
			<th class="head">일자</th>
			<th class="head">내용</th>
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
		$sql = "select 'i' as io_type
				,      income_acct_dt as dt
				,      income_item as item
				,      vat_yn
				,      income_amt as amt
				,      income_vat as vat
				,      income_amt + income_vat as tot
				,      taxid
				,      biz_group
				,      biz_type
				  from center_income
				 where org_no = '$find_center_code'
				   and income_acct_dt like '$find_ym%'
				   and del_flag = 'N'
				 union all
				select 'i_tot' as io_type
				,      '입금소계' as dt
				,      '' as item
				,      '' as vat_yn
				,      sum(income_amt) as amt
				,      sum(income_vat) as vat
				,      sum(income_amt + income_vat) as tot
				,      '' as taxid
				,      '' as biz_group
				,      '' as biz_type
				  from center_income
				 where org_no = '$find_center_code'
				   and income_acct_dt like '$find_ym%'
				   and del_flag = 'N'
				 union all
				select 'o' as io_type
				,      outgo_acct_dt as dt
				,      outgo_item as item
				,      vat_yn
				,      outgo_amt as amt
				,      outgo_vat as vat
				,      outgo_amt + outgo_vat as tot
				,      taxid
				,      biz_group
				,      biz_type
				  from center_outgo
				 where org_no = '$find_center_code'
				   and outgo_acct_dt like '$find_ym%'
				   and del_flag = 'N'
				 union all
				select 'o_tot' as io_type
				,      '지출소계' as dt
				,      '' as item
				,      '' as vat_yn
				,      sum(outgo_amt) as amt
				,      sum(outgo_vat) as vat
				,      sum(outgo_amt + outgo_vat) as tot
				,      '' as taxid
				,      '' as biz_group
				,      '' as biz_type
				  from center_outgo
				 where org_no = '$find_center_code'
				   and outgo_acct_dt like '$find_ym%'
				   and del_flag = 'N'
				 union all
				select 'io_tot' as io_type
				,      '합계' as dt
				,      '' as item
				,      '' as vat_yn
				,      sum(amt)
				,      sum(vat)
				,      sum(tot)
				,      '' as taxid
				,      '' as biz_group
				,      '' as biz_type
				  from (
					   select sum(income_amt) as amt
					   ,      sum(income_vat) as vat
					   ,      sum(income_amt + income_vat) as tot
					     from center_income
					    where org_no = '$find_center_code'
					      and income_acct_dt like '$find_ym%'
					      and del_flag = 'N'
					    union
					   select sum(outgo_amt) * -1 as amt
					   ,      sum(outgo_vat) * -1 as vat
					   ,      sum(outgo_amt + outgo_vat) * -1 as tot
					     from center_outgo
					    where org_no = '$find_center_code'
					      and outgo_acct_dt like '$find_ym%'
					      and del_flag = 'N'
					   ) as io_tot
				 order by case io_type when 'i'     then 1
									   when 'i_tot' then 2
									   when 'o'     then 3
									   when 'o_tot' then 4 else 5 end, dt";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$seq = 0;

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			switch($row['io_type']){
			case 'i':
				$gubun = '입금';
				break;
			case 'i_tot':
				$gubun = '';
				break;
			case 'o':
				$gubun = '지출';
				break;
			case 'i_tot':
				$gubun = '';
				break;
			default:
				$gubun = '';
				break;
			}

			if ($row['io_type'] == 'i' || $row['io_type'] == 'o'){
				if ($row['vat_yn'] == 'Y'){
					$vat_yn = '유';
				}else{
					$vat_yn = '무';
				}

				$seq ++;
				$seq_str    = $seq;
				$back_color = '';
				$font_bold  = 'normal';
			}else if ($row['io_type'] == 'io_tot'){
				$vat_yn     = '';
				$seq_str    = '';
				$back_color = '#dedede';
				$font_bold  = 'bold';
			}else{
				$vat_yn     = '';
				$seq_str    = '';
				$back_color = '#efefef;';
				$font_bold  = 'normal';
			}?>
			<tr>
				<td class="center"		style="text-align:center; background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=$seq_str;?></td>
				<td class="center"		style="text-align:center; background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=$gubun;?></td>
				<td class="center"		style="text-align:center; background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=$row['dt']?></td>
				<td class="left"		style="background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=$row['item']?></td>
				<td class="center"		style="text-align:center; background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=$vat_yn;?></td>
				<td class="right"		style="background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=number_format($row['amt'])?></td>
				<td class="right"		style="background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=number_format($row['vat'])?></td>
				<td class="right"		style="background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=number_format($row['tot'])?></td>
				<td class="center"		style="text-align:center; background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=$myF->bizStyle($row['taxid']);?></td>
				<td class="left"		style="background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=$row['biz_group']?></td>
				<td class="left last"	style="background-color:<?=$back_color;?>; font-weight:<?=$font_bold;?>;"><?=$row['biz_type']?></td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody><?
if($_POST['excel_yn'] != 'Y'){ ?>
		<tbody>
			<tr>
				<td class="bottom last" colspan="11">&nbsp;</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="mode" value="detail">
	<input type="hidden" name="excel_yn" value="">
	<input type="hidden" name="find_ym" value="<?=$find_ym;?>">
	<input type="hidden" name="find_center_code" value="<?=$find_center_code;?>">
	</form><?

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
}
?>