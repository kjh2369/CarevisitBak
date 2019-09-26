<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$find_type = $_REQUEST['f_type'];
	$find_text = $_REQUEST['f_text'];

?>

<script language='javascript'>
<!--
function view(id){
	var f = document.f;

	f.id.value = id;
	f.action = 'visit_quest_view.php';
	f.submit();
}

function list(page){
	var f = document.f;

	f.page.value = page;
	f.f_type.value = f.f_type.value;
	f.f_text.value = f.f_text.value;
	f.action = 'visit_quest_list.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

function _delete_visit_list(){
	var f = document.f;

	if (!__checkRowCount()){
		return;
	}
	if (!confirm('선택하신 데이타를 삭제하시겠습니까?')){
		return;
	}

	f.action = '../goodeos/visit_quest_delete.php';
	f.submit();
}

//-->
</script>
<form name="f" method="post">
<div class="title">비회원문의 리스트</div>
<table class="my_table my_border">
	<colgroup>
		<col width="50px">
		<col width="120px">
		<col width="150px">
		<col width="100px">
		<col width="250px">
		<col width="40px">
		<col width="120px">
	</colgroup>
	<thead>
		<tr>
			<th class="head"><input name="check_all" type="checkbox" class="checkbox" onclick="__checkMyValue('check[]', this.checked);"></th>
			<th class="head">일시</th>
			<th class="head">이름</th>
			<th class="head">연락처</th>
			<th class="head">메일주소</th>
			<th class="head">읽음</th>
			<th class="head last">답변</th>
		</tr>
	</thead>
	<tbody>
	<?

		if ($find_text != ''){
			switch($find_type){
			case '1':
				$wsl .= "where c_name >= '$find_text'";
				break;
			case '2':
				$wsl .= "where c_phone like '%$find_text%'";
				break;
			case '3':
				$wsl .= "where c_mail like '%$find_text%'";
				break;
			case '4':
				$wsl .= "where c_content like '%$find_text%'";
				break;
			}
		}

		if (!empty($wsl))
			$wsl .= " and c_domain_id = '$gDomainID'";
		else
			$wsl .= " where c_domain_id = '$gDomainID'";

		$sql = "select count(*)
				   from counsel $wsl";

		$total_count = $conn->get_data($sql);

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


		$sql = "select c_id as id
			 ,	   c_name as name
			 ,	   c_dt as date
			 ,	   c_phone as phone
			 ,	   c_mail as mail
			 ,	   c_count as count
			 ,	   case c_answer_gbn when '1' then '전화'
									 when '2' then '메일'
									 when '3' then '전화, 메일'
									 when '4' then '종합서비스신청'
									 when '5' then '재무회계신청' else '' end as answergbn
			 ,	   c_content as content
			   from counsel $wsl
			  order by c_dt desc
			  limit $pageCount, $item_count";

		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();


		if ($rowCount > 0){
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);
				$id = $row['id'];
				
				#전화,이메일 둘중체크 시 
				if($row['answergbn'] != ''){
					if($row['answergbn'] == '5'){
						//재무회계신청일때
						if($row['count'] > 0){
							$count = 'Y';
						}else{
							$count = '';
						}
					}else {	
						$count = 'Y';
					}
				}else {
					$count = '';
				}

				/*
				if($row['count'] > 0){
					$count = 'Y';
				}else{
					$count = '';
				}
				*/
				
				?>
				<tr>
					<td class="center">
						<input name="check[]" type="checkbox" class="checkbox" value="<?=$row['id'];?>">
					</td>
					<td class="center"><?=$row['date']?></td>
					<td class="left"><div class="nowrap" style="width:130px;"><a href="#" onFocus='this.blur();' onClick="view(<?=$row['id'];?>);"><?=$row['name'];?></a></div></td>
					<td class="left"><?=$myF->phoneStyle($row['phone'])?></td>
					<td class="left"><?=$row['mail']?></td>
					<td class="center"><?=$count?></td>
					<td class="center last"><?=$row['answergbn']?></td>
				</tr>
			<?
			}
		}else{
		?>
			<tr>
				<td class="last center" colspan="7">::검색된 데이타가 없습니다.::</td>
			</tr>
		<?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left bottom last" colspan="6">
				<div style="text-align:left;">
				<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
				<div style="width:108%; text-align:center;">
				<?
					$paging = new YsPaging($params);
					$paging->printPaging();
				?>
				</div>
				<div style="position:absolute; width:auto; padding-top:6.5px; padding-left:800px;"><span class="btn_pack m" ><button type="button" onclick="_delete_visit_list(<?=$id?>);">삭제</button></span></div>
			</td>
		</tr>

	</tbody>
</table>
<table class="my_table" align="center" style="margin:5px;">
<tbody>
<div class="last" style="padding-bottom:5px;">
	<select name="f_type" style="width:auto;">
		<option value="1" <? if($find_type == '1'){?>selected<?} ?>>이름</option>
		<option value="2" <? if($find_type == '2'){?>selected<?} ?>>연락처</option>
		<option value="3" <? if($find_type == '3'){?>selected<?} ?>>메일주소</option>
		<option value="4" <? if($find_type == '4'){?>selected<?} ?>>내용</option>
	</select>
	<input name="f_text" type="text" value="<?=$find_text;?>" onkeypress="if(event.keyCode==13){list('<?=$page;?>');}" onFocus="this.select();" style="width:150px; height:21px;">
	<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="list('1');">조회</button></span>
</div>
</tbody>
</table>


<input name="id" type="hidden" value="<?=$id?>">
<input name="page"	type="hidden" value="<?=$page;?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
