<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (empty($_GET)) include_once('../inc/_http_home.php');

	$code   = $_GET['code'];
	$svc_cd = $_GET['svc_cd'];
	$ssn    = $ed->de($_GET['ssn']);
	$key    = $_GET['key'];
	$year   = $_GET['year'];
	$month  = $_GET['month'];
	$seq    = $_GET['seq'];

	if ($svc_cd != '2' && $svc_cd != '4'){
		$svc_cd = $conn->get_data("select min(m03_mkind) from m03sugupja where m03_ccode = '$code' and m03_mkind in ('2', '4') and m03_key = '$key' and m03_del_yn = 'N'");
	}

	if (empty($svc_cd)) $svc_cd = $_GET['svc_cd'];

	$kind_list = $conn->kind_list($code, true);
	$svc_id    = $conn->kind_code($kind_list, $svc_cd, 'id');

	if (empty($code) || empty($svc_cd)) include_once('../inc/_http_home.php');

	##########################################################
	#
	# 마감일자
	#
		$close_yn = $conn->get_closing_act($code, $year.$month);

		if ($close_yn == 'Y'){
			$msg = '※ <font color="#ff0000">'.$year.'년 '.$month.'월</font> 실적등록마감이 완료되어 <font color="#ff0000">등록/수정/삭제</font>가 불가합니다.';
		}else{
			$msg = '';
		}
	#
	##########################################################
?>

<style>
body{
	margin-left:10px;
	margin-right:10px;
}
</style>

<script type='text/javascript' src='../js/change_info_guide.js'></script>
<script type="text/javascript" src="../js/iljung.reg.js"></script>

<form name="f" method="post">

<div id="window_body">
<div id="loading"></div>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold">고객 바우처 생성 내역 등록</th>
		</tr>
	</thead>
</table>

<div id="center_info"></div>
<div id="use_service"></div>
</div>

</form>

<?
	include_once('../inc/_footer.php');
?>

<script language="javascript">
	self.focus();

	window.onload = function(){
		_set_center_info('<?=$code;?>','<?=$key;?>','<?=$year;?>','<?=$month;?>','<?=$svc_id;?>','VOUCHER_OVERTIME','<?=$seq;?>');
	}
</script>