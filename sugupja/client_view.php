<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code   = $_GET['code'];
	$jumin  = $ed->de($_GET['client_cd']);
	$kind   = $conn->_client_kind_cd($code, $jumin);
	$k_list = $conn->kind_list($code, true);
	$k_cnt  = sizeof($k_list);

	$sql = "select m03_mkind as kind
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$k_cnt; $i++)
		$k_list[$i]['use_yn'] = 'N';

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		for($j=0; $j<$k_cnt; $j++){
			if ($k_list[$j]['code'] == $row['kind']){
				$k_list[$j]['use_yn'] = 'Y';
				break;
			}
		}
	}

	$conn->row_free();

	$view_type = 'read'; //뷰
?>
	<div class="title title_border">수급자 정보</div>
	<div id="div_body">
	<?
		include_once('client_reg_info.php');

		echo '<div style=\'text-align:center; margin-top:10px; margin-left:10px;\'>';

		for($i=0; $i<$k_cnt; $i++){
			if ($k_list[$i]['use_yn'] == 'Y'){
				$__CURRENT_SVC_ID__ = $k_list[$i]['id'];
				$__CURRENT_SVC_CD__ = $k_list[$i]['code'];
				$__CURRENT_SVC_NM__ = $k_list[$i]['name'];

				echo '<div style=\'float:left; width:48%; margin:5px;\'>';
				include('client_reg_sub.php');
				echo '</div>';
			}
		}

		echo '</div>';
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