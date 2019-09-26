<?
	
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");

?>
<!--<link href="../css/selectbox.css" rel="stylesheet" type="text/css" />
<script src="../js/jcombox-1.0b.packed.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(function(){
			$('.select').jcombox();
		});
	});
</script>
-->
<style>
	.page_list_1 {font-family: "돋움","굴림";font-size: 12px ;color: #FF7E00; font-weight : bold;}
	.page_list_2 {font-family: "돋움","굴림";font-size: 13px ;color: red; font-weight : bold;}
</style>
<?
	$workerGbn = $_GET['workerGbn'];
	$m_id = $_REQUEST['m_id'];
	$find_text = $_GET['find_text'];
	$find_type = $_GET['find_type'];

	$item_count = 10;
	$page_count = 10;
	$page = $_REQUEST["page"] != '' ? $_REQUEST["page"] : '1';
	
	if (!is_numeric($page)) $page = 1;
	if (!is_numeric($board_id)) $board_id = 0;
	if (!is_numeric($board_seq)) $board_seq = 0;
	if (!is_numeric($reply_id)) $reply_id = 0;
?>
<div class="bbs_search">
	<div class="searchbox">
		<fieldset>  
			<legend>검색영역</legend>  
			<!--select -->
			<select name="find_type" class="select">
			<option value="1" <? if($find_type == '1'){?>selected="selected"<?}?> >이름</option>
			<option value="2" <? if($find_type == '2'){?>selected="selected"<?}?> >아이디</option>
			</select>
			<label for="find_text" class="hidden">검색어입력</label>
			<input accesskey="s" id="find_text" name="find_text" title="검색어입력" type="text" value="<?=$find_text;?>"/>
			<input type="image" class="btn_s" alt="검색" src="/bbs/img/btn_srch.gif" />
		</fieldset>
	</div>
</div>
<?
	
	$wsl .= " where m_worker_gbn = '$workerGbn'
				and m_del_flag   = 'N'";
		
	if($find_text != ''){
		if($find_type == 1){
			$wsl .= " and m_nm like '%$find_text%'";
		}

		if($find_type == 2){
			$wsl .= " and m_id like '%$find_text%'";
		}
	}
	
	
	$sql = 'select count(*)
			  from m02yoyangsa
			  left join member
			    on org_no = m02_ccode
			   and jumin  = m02_yjumin
			 where m02_ccode = \''.$_GET['code'].'\'
			   and m02_mkind = \'0\'
			 order by m02_yname';
	
	$total_count = $conn->get_data($sql);
	

	// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
	if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;
	
	$prevPage = '‹';	
	$nextPage = '›';
	$params = array(
		'curMethod'		=> 'post',
		'curPage'		=> 'javascript:mem_list',
		'curPageNum'	=> $page,
		'pageVar'		=> 'page',
		'extraVar'		=> '',
		'totalItem'		=> $total_count,
		'perPage'		=> $page_count,
		'perItem'		=> $item_count,
		'prevPage'		=> $prevPage,
		'nextPage'		=> $nextPage,
	//	'prevPerPage'	=> '[이전'.$page_count.'페이지]',
	//	'nextPerPage'	=> '[다음'.$page_count.'페이지]',
	//	'firstPage'		=> '[처음]',
	//	'lastPage'		=> '[끝]',
		'pageCss'		=> 'page_list_1',
		'curPageCss'	=> 'page_list_2',
		'direction'		=> 'direction',
	);

	$pageCount = $page;
		
	if ($pageCount == ""){
		$pageCount = "1";
	}

	$pageCount = (intVal($pageCount) - 1) * $item_count;
	

	$sql = 'select DISTINCT	m02_yjumin
			,	   m02_yname as yname
			,	   code
			,	   m02_ytel
			,	   m02_email
			,	   m02_yipsail
			,	   m02_ytoisail
			,	   concat(m02_yjuso1,\' \', m02_yjuso2) as juso
			,	   m02_yjuso2
		      from m02yoyangsa
			  left join member
			    on org_no = m02_ccode
			   and jumin  = m02_yjumin
			 where m02_ccode = \''.$_GET['code'].'\'
			   and m02_mkind = \'0\'
			 order by m02_yname
			 limit '.$pageCount.', '.$item_count.'';
	$conn -> query($sql);
	$conn -> fetch();
	$rowCount = $conn -> row_count();

	$html = '';

	$html .= '<table class="list_type" width="100%">
						<colgroup>
							<col width="40px;">
							<col width="80px">
							<col width="90px;">
							<col width="100px;">
							<col width="200px;">
							<col width="250px;">
							<col width="200px;">
							<col width="*">
						</colgroup>
						<thead>
							<tr>
								<th>No</th>
								<th>이름</th>
								<th>아이디</th>
								<th>연락처</th>
								<th>이메일</th>
								<th>주소</th>
								<th>계약기간</th>
								<th>비고</th>
							</tr>
						</thead>
						<tbody>';

	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn-> select_row($i);
			
			if($row['m02_ytoisail'] != ''){
				$toisail = $row['m02_ytoisail']; 
			}else {
				$toisail = '99991231'; 
			}
		
			$html .= '  <tr>
							<td>'.($i+1).'</td>
							<td style="text-align:left; padding-left:5px;" ><a href="#" onclick="lfView(\''.$_GET['code'].'\', \''.$ed->en($row['m02_yjumin']).'\');">'.$row['yname'].'</a></td>
							<td>'.$row['code'].'</td>
							<td>'.$myF->phoneStyle($row['m02_ytel']).'</td>
							<td>'.$row['m02_email'].'</td>
							<td style="text-align:left;"><div class="nowrap" style="width:230px;" align:center;">'.$row['juso'].'</div></td>
							<td>'.$myF->dateStyle($row['m02_yipsail'], '.').' ~ '.$myF->dateStyle($toisail,'.').'</td>
							<td></td>
						</tr>';
					   
		}
	}else {
		$html .= '<tr>
					<td colspan="6">:: 검색된 데이터가 없습니다 ::</td>
				</tr>';
	}
	
	$html .= '	</tbody>
			 </table>';
	
	$html .= ' <input type="hidden" name="page" id="page" value="'.$page.'">';
	$html .= ' <input type="hidden" name="code" id="code" value="">';
	$html .= ' <input type="hidden" name="jumin" id="jumin" value="">';
			
	$conn -> row_free();

	echo $html;
	
	echo '<div class="paginate_simple">';
		
			if($rowCount > 0){
				$paging = new YsPaging($params);
				$paging->printPaging();
			}

	echo '	</div>';


	unset($html);

	$conn->close();
?>
