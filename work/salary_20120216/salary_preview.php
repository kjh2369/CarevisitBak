<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once('../inc/_check_class.php');
	include_once('../work/salary_const.php');

	$code  = $_SESSION['userCenterCode'];
	$kind  = $_SESSION['userCenterKind'][0];
	$year  = date('Y', mktime());
	$month = intval(date('m', mktime()));
	$month = ($month < 10 ? '0' : '').intval($month);

	if ($debug){
		$year  = '2011';
		$month = '12';
	}

	$init_year = $myF->year();

	if ($_SESSION['userLevel'] == 'P'){
		//개인회원조회
		$mode = 2;
		$member_code = $_SESSION['userSSN'];
	}else{
		//기관조회
		$mode = 1;
		$member_code = $ed->de($_REQUEST['member_code']);
	}

	$sql = "select m02_yname as name
			,      m02_rank_pay as pay
			  from m02yoyangsa
			 where m02_ccode  = '$code'
			   and m02_mkind  = '$kind'
			   and m02_yjumin = '$member_code'";

	$member = $conn->get_array($sql);

	$member_name = $member['name'];
	$rank_pay    = $member['pay'];

	unset($member);
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.submit();
}

function salary_preview(month){
	var f = document.f;

	if (month != undefined) f.month.value = month;

	f.submit();
}

// 지급항목 합계
function sum_give(){
	var f = document.f;

	var pay	= __str2num(f.rank_pay.value) + sum_sub('1');

	f.tot_1_addon_pay.value = __num2str(pay);

	sum_diff();
}

// 공제항목 합계
function sum_deduct(){
	var f = document.f;

	var pay	= sum_sub('2');

	f.tot_2_addon_pay.value = __num2str(pay);

	sum_diff();
}

function sum_sub(type){
	var object	= document.getElementsByName(type+'_pay[]');
	var pay		= 0;

	for(var i=0; i<object.length; i++){
		pay += __str2num(object[i].value);
	}

	return pay;
}

// 합계금액
function sum_diff(){
	var f = document.f;

	var tot_basic_pay	= __str2num(f.tot_basic_pay.value);
	var tot_sudang_pay	= __str2num(f.tot_sudang_pay.value);
	var tot_1_addon_pay	= __str2num(f.tot_1_addon_pay.value);

	var tot_ins_pay		= __str2num(f.tot_ins_pay.value);
	var tot_tax_pay		= __str2num(f.tot_tax_pay.value);
	var tot_2_addon_pay	= __str2num(f.tot_2_addon_pay.value);

	f.tot_pay.value		= __num2str(tot_basic_pay + tot_sudang_pay + tot_1_addon_pay)
	f.tot_deduct.value	= __num2str(tot_ins_pay + tot_tax_pay + tot_2_addon_pay)
	f.tot_diff.value	= __num2str(__str2num(f.tot_pay.value) - __str2num(f.tot_deduct.value));
}

window.onload = function(){
	__init_form(document.f);
	sum_diff();
}
-->
</script>

<form name="f" method="post">

<div class="title">예상급여</div>

<table class="my_table my_border">
	<colgroup>
		<col width="70px">
		<col width="150px">
		<col width="80px">
		<col width="210px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>요양보호사</th>
			<td class="left" style="padding-top:1px;"><?
				if ($mode == 1){?>
					<span class="btn_pack find" onClick="if(__find_yoyangsa('<?=$code?>','<?=$kind?>','member_code','member_name')){salary_preview(<?=intval($month);?>);}"></span><?
				}?>
				<span id="member_name" style="height:100%; margin-left:5px; font-weight:bold;"><?=$member_name;?></span>
				<input name="member_code" type="hidden" value="<?=$ed->en($member_code);?>">
			</td>
			<th>급여계산기간</th>
			<td class="left"><?=date('Y년 m월 ', mktime());?>01일 ~ <?=date('Y년 m월 d일', mktime());?></td>
			<td class="left last bold">※기간내 실적등록된 데이타로 계산된 급액입니다.</td>
		</tr>
	</tbody>
</table>
<?
	$is_preview = true;

	if ($member_name != ''){
		// 급여함수
		include_once('../work/salary_function.php');

		$rst = change_db($conn, $code);

		// 추가수당 및 공제항목
		include_once('../work/salary_addon.php');

		// 4대보험 부담비율
		include_once('../work/salary_ins.php');

		// 요양보호사 등급별 시급 / 요양보호사 급여 방법 및 시급, 총액비율
		include_once('../work/salary_pay_list.php');

		// 기관의 법정휴일 유무와 유급여부
		include_once('../work/salary_holiday.php');

		// 주일수
		//$week_count = $myF->weekCount($year, $month);

		$week_count = 0;

		for($i=0; $i<sizeof($sunday_list); $i++){
			$is_duplicate = false;
			for($j=0; $j<sizeof($holiday_list); $j++){
				if (str_replace('-', '', $sunday_list[$i]) == $holiday_list[$j]['date'] && $holiday_list[$j]['pay'] == 'Y'){
					$is_duplicate = true;
					break;
				}
			}

			if (!$is_duplicate) $week_count ++;
		}

		// 요양보호사 급여내역
		include_once('../work/salary_detail.php');
	}

	include_once('../work/salary_items.php');
?>
<br>
<input type="hidden" name="code"   value="<?=$code;?>">
<input type="hidden" name="kind"   value="">
<input type="hidden" name="year"   value="<?=$year;?>">
<input type="hidden" name="month"  value="<?=$month;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
	include_once('../work/salary_function.php');
?>