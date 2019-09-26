<?
	############################################################
	#
	# 고객 초기상담기록 리스트를 작성한다.
	#
	############################################################

	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	if (empty($name)) $name = $conn->center_name($code);
	$jumin = $ed->de($_POST['jumin']);

	if (empty($jumin))
		$counsel_list_mode = 1;
	else
		$counsel_list_mode = 2;

	$find_nm = $_POST['find_nm'];

	if (is_array($_POST['find_type'])){
		foreach($_POST['find_type'] as $i => $f){
			$f_type[$f] = $f;
		}
	}

?>
<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="70px">
		<col width="60px">
		<col>
		<?
			if ($h_ref == 'report'){
				echo '<col width=\'150px\'>';
			}else{
				echo '<col width=\'150\'>';
			}
		?>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$code;?></td>
			<th>기관명</th>
			<td class="left last"><?=$name;?></td>
			<td class="right last">
			<?
				if ($counsel_list_mode == 1){
					echo '<span class="btn_pack m icon"><span class="add"></span><button type="button" onFocus="this.blur();" onclick=\'counsel_reg("","");\'>등록</button></span>';
					echo ' <span class="btn_pack m icon"><span class="excel"></span><button type="button" onFocus="this.blur();" onclick=\'lfExcel();\'>엑셀</button></span>';

					if ($h_ref == 'report'){
						echo ' <span class=\'btn_pack m icon\'><span class=\'list\'></span><button type=\'button\' onclick=\'location.replace("../reportMenu/report.php?report_menu=30");\'>메뉴</button></span>';
					}
				}else{
					echo '<a href=\'#\' onclick=\'counsel_close();\'>뒤로</a>';
				}
			?>
			</td>
		</tr>
		<?
			if ($counsel_list_mode == 1){
				echo '<tr>';
				echo '<th>고객명</th>';
				echo '<td class=\'\'><input name=\'find_nm\' type=\'text\' value=\''.$find_nm.'\' style=\'width:100%;\'></td>';
				echo '<th>상담구분</th>';
				echo '<td class=\'last\'>';

				$kind_list = $conn->kind_list($code, true);

				if (is_array($kind_list)){
					foreach($kind_list as $k => $k_list){
						if ($k_list['id'] < 30){
							echo '<input name=\'find_type[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$k_list['code'].'\' '.($f_type[$k_list['code']] == $k_list['code'] ? 'checked' : '').'>'.$k_list['name'];
						}
					}
				}

				unset($kind_list);

				echo '<input name=\'find_type[]\' type=\'checkbox\' class=\'checkbox\' value=\'9\' '.($f_type['9'] == '9' ? 'checked' : '').'>기타';
				echo '</td>';
				echo '<td class=\'last right\'>';
					echo '<span class=\'btn_pack m icon\' ><span class=\'refresh\'></span><button type=\'button\' onclick=\'counsel_find();\'>조회</button></span>';
					echo '<span class=\'btn_pack m\' style="margin-left:3px;"><button type=\'button\' onclick=\'counsel_print("'.$code.'","","","")\'>빈양식출력</button></span>';
				echo '</td>';

				echo '</tr>';
			}
		?>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="90px">
		<col width="280px">
		<col width="100px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">연락처</th>
			<th class="head">상담구분</th>
			<th class="head">서비스</th>
			<th class="head">상담일자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = " where org_no   = '$code'
				   AND del_flag = 'N'";

		if ($counsel_list_mode == 2)
			$wsl .= " and client_ssn = '$jumin'";

		if (!empty($find_nm))
			$wsl .= " and client_nm like '%$find_nm%'";

		if (is_array($_POST['find_type'])){
			$first = true;
			$wsl .= " and client_counsel in (";
			foreach($_POST['find_type'] as $i => $f){
				$wsl .= (!$first ? ',' : '')."'$f'";
				$first = false;
			}
			$wsl .= ")";
		}

		$sql = "select count(*)
				  from counsel_client $wsl";

		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:counsel_list',
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

		$pageCount   = (intVal($pageCount) - 1) * $item_count;
		$limit_query = "limit $pageCount, $item_count";
		$is_path     = 'counsel';

		include_once('client_counsel_list_sub.php');
	?>
	</tbody>
</table>

<div style="text-align:left;">
	<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
	<div style="width:100%; text-align:center;">
	<?
		if ($row_count > 0){
			$paging = new YsPaging($params);
			$paging->printPaging();
		}else{
			echo $myF->message('nodata', 'N');
		}
	?>
	</div>
</div>