<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_mySalary.php");
	include_once('../inc/_ed.php');

	$code  = $_GET['code'];
	$jumin = $ed->de($_GET['member_cd']);
	$kind  = $conn->_mem_kind_cd($code, $jumin);

	// 기본기관구분
	$basic_kind = $kind;

	// 직원정보 조회
	$sql = "select *
			  from m02yoyangsa
			 where m02_ccode  = '$code'
			   and m02_yjumin = '$jumin'
			   and m02_del_yn = 'N'
			 order by m02_mkind";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$mst[$row['m02_mkind']] = $row;
	}

	$conn->row_free();

	$view_type = 'read'; //뷰
	
	// 기관분류 리스트
	$k_list = $conn->kind_list($code, true);
	$k_cnt  = sizeof($k_list);

	#########################################################
	#
	# 메뉴설정

		$use_menu[0] = false;
		$use_menu[1] = false;
		$use_menu[2] = false;
		$use_menu[3] = false;
		$use_menu[4] = false;

		for($i=0; $i<$k_cnt; $i++){
			if ($k_list[$i]['id'] > 10 && $k_list[$i]['id'] < 20){
				$use_menu[0] = true;
			}else if ($k_list[$i]['id'] == 21){
				$use_menu[1] = true;
			}else if ($k_list[$i]['id'] == 22){
				$use_menu[2] = true;
			}else if ($k_list[$i]['id'] == 23){
				$use_menu[3] = true;
			}else if ($k_list[$i]['id'] == 24){
				$use_menu[4] = true;
			}
		}
	#
	#########################################################

?>
	<div class="title title_border">요양보호사 정보</div>
	<div id="div_body">
	<?
		include_once('../counsel/mem_counsel_info.php');
		include_once('mem_basic_info.php');
		include_once('service_view.php');	
		include_once('mem_memo.php');
	?>
	</div>
	<div style="height:30px; text-align:center;">
		<span class="btn_pack m" style="margin-top:5px;"><button type="button" onclick="window.self.close();">확인</button></span>
	</div>
<?
	include_once("../inc/_footer.php");
?>
<script language='javascript'>
window.onload = function(){
	var body = document.getElementById('div_body');

	body.style.height = document.body.clientHeight - 70;
}
window.self.focus();
</script>