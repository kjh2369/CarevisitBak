<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	$code = $_SESSION['userCenterCode'];
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
<div class="title">공지사항 리스트</div>
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
		$sql = 'select id
				,      subject
				,      reg_dt
				  from tbl_goodeos_notice
				 where notice_yn = \'Y\'
				 order by reg_dt desc';

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if (!$row['subject']) $row['subject'] = '제목없음';?>
			<tr>
				<td class="center">공지</td>
				<td class="left"><a href="#" onclick="_reg_notice('<?=$row['id'];?>','<?=$page;?>','<?=$_SESSION["userLevel"] == 'A' ? 'edit' : 'view';?>'); return false;"><?=stripslashes($row['subject']);?></a></td>
				<td class="center"><?=date('Y.m.d H:i:s', $row['reg_dt']);?></td>
				<td class="other">&nbsp;</td>
			</tr>	<?
		}

		$conn->row_free();


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

		/*
		$sql = "select id, subject, reg_dt
				  from tbl_goodeos_notice
				 where domain_id = '$gDomainID'
				 order by reg_dt desc";
		*/

		if ($_SESSION['userLevel'] == 'A'){
			$sql = 'select id
					,      subject
					,      reg_dt
					  from tbl_goodeos_notice
					 where domain_id = \''.$gDomainID.'\'
					 order by reg_dt desc';
		}else{
			$sql = 'select id
					,      subject
					,      reg_dt
					  from tbl_goodeos_notice as mst
					  left join popup_notice as pop
						on pop.notice_id = mst.id
					 where domain_id                        = \''.$gDomainID.'\'
					   and ifnull(pop.org_no,\''.$code.'\') = \''.$code.'\'
					 order by reg_dt desc';
		}

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				if (!$row['subject']) $row['subject'] = '제목없음';?>
				<tr>
					<td class="center"><?=$pageCount + ($i + 1);?></td>
					<td class="left"><a href="#" onclick="_reg_notice('<?=$row['id'];?>','<?=$page;?>','<?=$_SESSION["userLevel"] == 'A' ? 'edit' : 'view';?>'); return false;"><?=stripslashes($row['subject']);?></a></td>
					<td class="center"><?=date('Y.m.d H:i:s', $row['reg_dt']);?></td>
					<td class="other">&nbsp;</td>
				</tr>	<?
			}
		}else{
		?>	<tr>
				<td colspan="3" class="center">::등록된 공지사항이 없습니다.::</td>
				<td class="last">&nbsp;</td>
			</tr>	<?
		}

		$conn->row_free();
	?>
	</tbody>
</table>
<div style="width:100%; text-align:right; margin-top:10px;">
<?
	if ($_SESSION["userLevel"] == 'A'){?>
		<span class="btn_pack m"><button type="button" onclick="_reg_notice('','<?=$page;?>','edit');">등록</button></span><?
	}
?>
</div>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>