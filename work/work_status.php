<?
	/***************************************************************************
	 *
	 * 개요 : 수급자별 요양보호사의 근무현황을 작성함.
	 *
	 ***************************************************************************/
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_SESSION['userCenterCode'];
	$kind	= $conn->center_kind($code);
	$year	= $_POST['year'] ? $_POST['year'] : date('Y', mktime());
	$month	= $_POST['month'] ? $_POST['month'] : date('m', mktime());
	$month	= (intval($month) < 10 ? '0' : '').intval($month);

	$init_year = $myF->year();
?>
<script language='javascript'>
<!--

function month_search(month){
	var f = document.f;

	f.month.value = month;
	f.submit();
}

function set_row_span(row, seq, cnt){
	var row = document.getElementsByName(row+'_'+seq+'[]');

	for(var i=0; i<row.length; i++){
		row[i].setAttribute('rowSpan',cnt);
	}
}

function print_excel(){
	var f = document.f;

	location.href='work_status_excel.php?excel=true&code='+f.code.value+'&kind='+f.kind.value+'&year='+f.year.value+'&month='+f.month.value;
}

function print_pdf(){
	var width  = 900;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	window.open('work_status_pdf.php?code='+f.code.value+'&kind='+f.kind.value+'&year='+f.year.value+'&month='+f.month.value, 'WORK_STATUS', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

-->
</script>

<div class="title">근무현황표</div>

<form name="f" method="post">

<table class="my_table my_border">
	<colgroup>
		<col width="40px">
		<col width="75px">
		<col width="40px">
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">년도</th>
			<td class="left">
				<select name="year" style="width:auto; margin-left:0;"><?
				for($i=$init_year[0]; $i<= $init_year[1]; $i++){?>
					<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option>><?
				}?>
				</select>년
			</td>
			<th class="head">월별</th>
			<td class="left last"><?
				for($i=1; $i<=12; $i++){
					$class = 'my_month ';

					if ($i == intval($month)){
						$class .= 'my_month_y ';
						$color  = 'color:#000000;';
					}else{
						$class .= 'my_month_1 ';
						$color  = 'color:#666666;';
					}

					$text = '<a href="#" style="'.$color.'" onclick="month_search(\''.$i.'\');">'.$i.'월</a>';

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:3px;';
					}?>
					<div class="<?=$class;?>" style="<?=$style;?>"><?=$text;?></div><?
				}?>
			</td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="print_excel();">엑셀</button></span>
				<span class="btn_pack m icon"><span class="pdf"></span><button type="button" onclick="print_pdf();">PDF</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('./work_status_sub.php');
?>
<input name="code" type="hidden" value="<?=$code;?>">
<input name="kind" type="hidden" value="<?=$kind;?>">
<input name="month" type="hidden" value="<?=$month;?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>