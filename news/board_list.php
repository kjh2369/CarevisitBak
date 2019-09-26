<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code		= $_SESSION['userCenterCode'];
	$board_type = $_REQUEST['board_type'];
	$board_id   = $_REQUEST['board_id'];
	$board_seq	= $_REQUEST['board_seq'];
	$reply_id	= $_REQUEST['reply_id'];
	
	if($board_type == 'L'){
		$title  = '인사노무소식 리스트';
	}else {
		$title	= '재무회계소식 리스트';
	}


	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;
	if (!is_numeric($board_id)) $board_id = 0;
	if (!is_numeric($board_seq)) $board_seq = 0;
	if (!is_numeric($reply_id)) $reply_id = 0;

	$find_type = $_REQUEST['f_type'];
	$find_text = $_REQUEST['f_text'];
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
function list(page){
	var f = document.f;

	f.page.value = page;
	f.submit();
}

function reg(){
	var f = document.f;

	f.mode.value = '1';
	f.board_seq.value = '0';
	f.action = 'board_reg.php';
	f.submit();
}

function view(board_type, board_seq){
	var f = document.f;

	f.board_type.value = board_type;
	f.board_seq.value = board_seq;
	
	f.mode.value = '2';
	f.action = 'board_reg.php';
	f.submit();
}

function search(){
	var f = document.f;

	f.f_type.value = f.find_type.value;
	f.f_text.value = f.find_text.value;
	f.action = 'board_list.php';
	f.submit();
}

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
		<col width="*">
		<col width="120px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">제목</th>
			<th class="head last">작성일시</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl .= " where news_gbn   = '$board_type'";

		if ($find_text != ''){
			switch($find_type){
			case '1':
				$wsl .= " and news_subject like '%$find_text%'";
				break;
			case '2':
				$wsl .= " and news_contents like '%$find_text%'";
				break;
			}
		}

		$sql = "select count(*)
				  from news_list $wsl";
		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:list',
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

		$sql = "select news_gbn
				,	   news_seq
				,	   news_subject
				,      news_reg_dt
				  from news_list $wsl
				 order by news_reg_dt desc
				 limit $pageCount, $item_count";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			
			//댓글 카운트
			$sql = 'select count(*)
					  from news_reply
					 where news_gbn = \''.$board_type.'\'
					   and news_seq = \''.$row['news_seq'].'\'';
			$reply_cnt = $conn -> get_data($sql);

		?>
			<tr>
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<?
					//if ($_SESSION['userLevel'] == 'A'){
					$subject = $myF->splits(stripslashes($row['news_subject']), 43);
					
				?>
				<td class="left"><a href="#" onclick="view('<?=$row['news_gbn'];?>','<?=$row['news_seq'];?>');"><?=$subject;?>[<?=$reply_cnt?>]</a></td>
				<td class="center last"><?=str_replace('-','.', $row['news_reg_dt'])?></td>			
			</tr>
			
		<?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left bottom last" colspan="4">
			<?
				if ($row_count > 0){?>
					<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
					<div style="width:100%; text-align:center;">
					<?
						$paging = new YsPaging($params);
						$paging->printPaging();
					?>
					</div><?
				}else{?>
					<div style="text-align:center;"><?=$myF->message('nodata', 'N');?></div><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<div style="width:auto; float:left; margin:5px;">
	<select name="find_type" style="width:auto;">
		<option value="1" <? if($find_type == '1'){?>selected<?} ?>>제목</option>
		<option value="2" <? if($find_type == '2'){?>selected<?} ?>>내용</option>
	</select>
	<input name="find_text" type="text" value="<?=$find_text;?>" style="width:150px; height:21px;">
	<span class="btn_pack m"><button type="button" onclick="search();">조회</button></span>
</div><?
if($_SESSION['userLevel'] == 'A'){ ?>
	<div style="width:auto; float:right; margin:5px;">
		<span class="btn_pack m"><button type="button" onclick="reg();">등록</button></span>
	</div>
<?
}

	include_once("board_var.php");
?>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>