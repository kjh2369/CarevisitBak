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
	$optAnswer  = $_REQUEST['optAnswer'];
	$optReply   = $_REQUEST['optReply'];

	if ($gDomain == 'vaerp.com' && $board_type == '1'){
		$title = 'VA문의';
	}else{
		$title = $myF->board_name($board_type).' 리스트';
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
	f.board_id.value = '0';
	f.action = 'board_reg.php';
	f.submit();
}

function view(border_code, board_id){
	var f = document.f;

	f.code.value = border_code;
	f.board_id.value = board_id;
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

function showCenterScreen(did, url, id, pw){
	var tgt = 'WINDOW_CENTER_'+did;
	var win = window.open('about:blank',tgt);
	var frm = document.createElement('form');

	frm.appendChild(__create_input('loc', 'admin'));
	frm.appendChild(__create_input('uCode', id));
	frm.appendChild(__create_input('uPass', pw));
	frm.setAttribute('method', 'post');
	
	document.body.appendChild(frm);

	frm.target = tgt;
	frm.action = url;
	frm.submit();
}

window.onload = function(){
	__init_form(document.f);
}
//-->
</script>
<form name="f" method="post">

<div class="title"><?=$title;?></div>
<table class="my_table my_border" style="width:100%;">
	<colgroup><?
		if($_SESSION['userLevel'] == 'A'){ ?>
			<col width="50px">
			<col width="50px">
			<col width="50px">
			<col width="50px">
			<col width="50px"><?
		} ?>
			<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr><?
			if($_SESSION['userLevel'] == 'A'){
				?>
				<th class="center">검색조건</th>
				<th class="center">답변</th>
				<td class="left">
					<select name="optAnswer" style="width:auto;">
						<option value="all" <? if($optAnswer == 'all'){ ?> selected <? }?>>-선택-</option>
						<option value="Y" <? if($optAnswer == 'Y'){ ?> selected <? }?>>예</option>
						<option value="N" <? if($optAnswer == 'N'){ ?> selected <? }?>>아니오</option>
					</select>
					<!--<label><input id="optAnswer1" name="optAnswer" type="checkbox" value="Y" class="checkbox" onclick="search();" <? if($optAnswer == 'Y'){ ?> checked <? }?> ></label>-->
				</td>
				<th class="center">댓글</th>
				<td class="left last">
					<select name="optReply" style="width:auto;">
						<option value="all" <? if($optReply == 'all'){ ?> selected <? }?>>-선택-</option>
						<option value="Y" <? if($optReply == 'Y'){ ?> selected <? }?>>예</option>
						<option value="N" <? if($optReply == 'N'){ ?> selected <? }?>>아니오</option>
					</select>
					<!--<label><input id="optReply" name="optReply" type="checkbox" value="Y" class="checkbox" onclick="search();" <? if($optReply == 'Y'){ ?> checked <? }?> ></label>-->
				</td><?
			} ?>
			<th class="center">검색</th>
			<td class="last">
				<select name="find_type" style="width:auto;">
					<?
						if ($_SESSION['userLevel'] == 'A'){?>
							<option value="B" <? if($find_type == 'B'){?>selected<?} ?>>기관명</option>
							<option value="A" <? if($find_type == 'A'){?>selected<?} ?>>기관코드</option><?
						}
					?>
					<option value="1" <? if($find_type == '1'){?>selected<?} ?>>제목</option>
					<option value="2" <? if($find_type == '2'){?>selected<?} ?>>내용</option>
					<option value="3" <? if($find_type == '3'){?>selected<?} ?>>작성자</option>
				</select>
				<input name="find_text" type="text" value="<?=$find_text;?>" style="width:150px; height:21px;">
				<span class="btn_pack m"><button type="button" onclick="search();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table my_border" style="border-top:0;">
	<colgroup>
		<col width="45px">
		<?
			if ($_SESSION['userLevel'] == 'A'){?>
				<col width="95px">
				<col width="170px">
				<col><?
			}else{?>
				<col><?
			}
		?>
		<col width="120px">
		<col width="70px">
		<col width="50px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<?
				if ($_SESSION['userLevel'] == 'A'){?>
					<th class="head">기관코드</th>
					<th class="head">기관명</th>
					<th class="head">제목</th><?
				}else{?>
					<th class="head">제목</th><?
				}
			?>
			<th class="head">작성일시</th>
			<th class="head">작성자</th>
			<th class="head last">답변</th>
		</tr>
	</thead>
	<tbody>
	<?


		$sql = "select board_center
				,	   board_id
				,      subject
				,      reg_date
				,      reg_time
				,      reg_name
				,      reply_count
				  from tbl_board
				 where board_center = '".$conn->m_center."'
				   and board_type   = '$board_type'
				   and reply_id     = '0'
				   and notice       = 'Y'
				 order by reg_date desc, reg_time desc
				 limit 5";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center">공지</td>
				<?
					if ($_SESSION['userLevel'] == 'A'){?>
						<td class="left"><?=$row['board_center'];?></td>
						<td class="left"><?=$conn->center_name($row['board_center']);?></td><?
					}
				?>
				<td class="left"><a href="#" onclick="view('<?=$row['board_center'];?>',<?=$row['board_id'];?>);"><?=stripslashes($row['subject']);?></a>[<?=$row['reply_count'];?>]</td>
				<td class="center"><?=$row['reg_date'].' '.$row['reg_time'];?></td>
				<td class="left"><?=$row['reg_name'];?></td>
				<td class="center last">-</td>
			</tr><?
		}

		$conn->row_free();

		if ($_SESSION['userLevel'] == 'A'){
			$wsl = "where board_center != ''";
		}else{
			$wsl = "where board_center = '$code'";
		}

		$wsl .= " and board_type = '$board_type'
				  and reply_id   = '0'
				  and notice     = 'N'";

		if ($gDomain != 'carevisit.net'){
			$wsl .= " and domain_id  = '$gDomainID'";
		}

		if ($find_text != ''){
			switch($find_type){
			case '1':
				$wsl .= " and subject like '%$find_text%'";
				break;
			case '2':
				$wsl .= " and content like '%$find_text%'";
				break;
			case '3':
				$wsl .= " and reg_name like '%$find_text%'";
				break;
			case 'A':
				$wsl .= " and board_center like '$find_text%'";
				break;
			case 'B':
				$wsl .= " and board_center in (select m00_mcode from m00center where m00_cname like '%$find_text%' and m00_del_yn = 'N')";
				break;
			}
		}

		$sql = "select count(*)
				  from tbl_board $wsl";
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


		$sql = "select board_center
				,	   board_id
				,      subject
				,      reg_date
				,      reg_time
				,      reg_name
				,      case when answer_id is not null then 'Y' else 'N' end as answer_yn
				,      reply_count
				  from tbl_board $wsl
				";

				if($optAnswer == 'Y') $sql .= " and answer_id is not null ";
				if($optReply  == 'Y') $sql .= " and reply_count > 0 ";
				if($optAnswer == 'N') $sql .= " and answer_id is null ";
				if($optReply  == 'N') $sql .= " and reply_count = 0 ";

				$sql .=	" order by reg_date desc, reg_time desc
						  limit $pageCount, $item_count";
				/*
				$sql .=	" order by case when answer_id is null then 1 else 2 end, reg_date desc, reg_time desc
						  limit $pageCount, $item_count";
				*/

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			/*
			if($row['answer_yn'] != 'Y'){
				$ans_y = $row['reply_count'] > 0 ? 'Y' : 'N';
			}else {
				$ans_y = 'Y';
			}
			*/
			
			$sql = 'SELECT	m97_id as id
					,	    m97_pass as pw
					,       m00_domain as domain
					FROM m00center
					INNER JOIN m97user
					ON m97_user = m00_mcode
					WHERE m00_mcode = \''.$row['board_center'].'\'
					GROUP BY m00_mcode';
			
			$user = $conn->get_array($sql);
			
			if ($gDomain == _KLCF_){
				$url = 'care.'.$user['domain'];
			}else{
				$url = 'www.'.$user['domain'];
			}
			
			?>

			<tr>
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<?
					if ($_SESSION['userLevel'] == 'A'){
						$subject = $myF->splits(stripslashes($row['subject']), 20);?>
						<td class="left"><?=$row['board_center'];?></td>
						<td class="left"><?
							if ($host == 'admin' && $_SESSION["userCode"] != 'geecare'){?>
								<a href="#" onclick="showCenterScreen('<?=$gDomainID;?>','http://<?=$url;?>/main/login_ok.php','<?=$ed->en($user['id']);?>','<?=$ed->en($user['pw']);?>');"><?=$conn->center_name($row['board_center']);?></a><?
							}else{
								echo $conn->center_name($row['board_center']);
							}?>
						</td><?
					}else{
						$subject = $myF->splits(stripslashes($row['subject']), 43);
					}
				?>
				<td class="left"><a href="#" onclick="view('<?=$row['board_center'];?>',<?=$row['board_id'];?>);"><?=$subject;?></a>[<?=$row['reply_count'];?>]</td>
				<td class="center"><?=$row['reg_date'].' '.$row['reg_time'];?></td>
				<td class="left"><?=$row['reg_name'];?></td>
				<td class="center last"><?=$row['reply_count'] > 0 ? 'Y' : $row['answer_yn'];?></td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left bottom last" colspan="6">
			<?
				if ($row_count > 0){?>
					<div style="position:absolute; width:auto; padding-left:10px; ">검색된 전체 갯수 : <?=number_format($total_count);?></div>
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


<div style="width:auto; float:right; margin:5px;">
<?
	if ($_SESSION['userLevel'] == 'A'){?>
		<span class="btn_pack m"><button type="button" onclick="reg();">등록</button></span><?
	}else {
		if ($board_type == 'noti'){
			if (($_SESSION['userLevel'] != 'P') || ($_SESSION['userLevel'] == 'P' && $_SESSION['userStmar'] == 'M')){?>
				<span class="btn_pack m"><button type="button" onclick="reg();">등록</button></span><?
			}
		}else{?>
			<span class="btn_pack m"><button type="button" onclick="reg();">등록</button></span><?
		}
	}
?>
</div>
<?
	include_once("board_var.php");
?>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>