<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	define(__CENTER__, 'goodeos');

	if ($_SESSION['userLevel'] == 'A'){
		$board_center = $_REQUEST['board_center'];
	}else{
		$board_center = $_SESSION['userCenterCode'];
	}

	$board_type = $_REQUEST['board_type'];
	$title = $myF->board_name($board_type);
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script type="text/javascript" src="../js/goodeos.js"></script>
<script language='javascript'>
<!--
window.onload = function(){
	__init_form(document.f);
}
//-->
</script>
<form name="f" method="post">
<div class="title"><?=$title;?></div>
<table class="my_table my_border">
	<colgroup>
		<col width="50px">
		<col width="600px">
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">제목</th>
			<th class="head">작성일시</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$item_count = 20;
		$page_count = 10;
		$page = $_REQUEST["page"];

		if (!is_numeric($page)) $page = 1;

		$sql = "select count(*)
				  from tbl_goodeos_notice";
		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'get',
			'curPage'		=> '',
			'curPageNum'	=> $page,
			'pageVar'		=> 'page',
			'extraVar'		=> '',
			'totalItem'		=> $total_count,
			'perPage'		=> $page_count,
			'perItem'		=> $item_count,
			'prevPage'		=> '[이전]',
			'nextPage'		=> '[다음]',
			'prevPerPage'	=> '[이전'.$page_count.'페이지]',
			'nextPerPage'	=> '[다음'.$page_count.'페이지]',
			'firstPage'		=> '[처음]',
			'lastPage'		=> '[끝]',
			'pageCss'		=> 'page_list_1',
			'curPageCss'	=> 'page_list_2'
		);

		$pageCount = $page;

		if ($pageCount == ""){
			$pageCount = "1";
		}

		$pageCount = (intVal($pageCount) - 1) * $item_count;

		$sql = "select board_id, subject, reg_date, reg_time
				  from tbl_board
				 where board_center in ('".__CENTER__."', '$board_center')
				   and board_type   = '$board_type'
				 order by reg_date, reg_time desc";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				?>
				<tr>
					<td class="center"><?=$pageCount + ($i + 1);?></td>
					<td class="left"><a href="#" onclick="return false;"><?=stripslashes($row['subject']);?></a></td>
					<td class="center"><?=str_replace('-', '.', $row['reg_date']);?> <?=$row['reg_time'];?></td>
					<td class="other">&nbsp;</td>
				</tr>	<?
			}
		}else{
		?>	<tr>
				<td colspan="4" class="center last">::검색된 데이타가 없습니다.::</td>
			</tr>	<?
		}

		$conn->row_free();
	?>
	</tbody>
</table>
<div style="width:800px; text-align:right; padding:5px;">
<?
	if ($_SESSION["userLevel"] == 'A'){
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_reg_commnuity("'.$board_center.'","'.$board_type.'",0,"'.$page.'","edit");\'>등록</button></span>';
	}
?>
</div>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>