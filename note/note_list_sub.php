<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");

	$code  = $_SESSION['userCenterCode'];
	$mode  = $_REQUEST['mode'];

	switch($mode){
		case 'from':
			$title = '보낸쪽지함';
			break;
		case 'to':
			$title = '받은쪽지함';
			break;
	}

	$item_count = 10;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;
?>

<table id="body_list" class="my_table" style="width:100%;">
	<colgroup>
	<?
		switch($mode){
			case 'from':
				echo '<col width=\'30px\'>
					  <col width=\'80px\'>
					  <col width=\'100px\'>
					  <col width=\'300px\'>
					  <col width=\'80px\'>
					  <col width=\'60px\'>
					  <col width=\'60px\'>';
				break;
			case 'to':
				echo '<col width=\'30px\'>
					  <col width=\'50px\'>
					  <col width=\'100px\'>
					  <col width=\'300px\'>
					  <col width=\'80px\'>';
				break;
		}

		echo '<col>';
	?>
	</colgroup>
	<thead>
		<tr>
		<?
			echo '<th class=\'head\'><input name=\'check_all\' type=\'checkbox\' class=\'checkbox\'></th>';

			switch($mode){
				case 'from':
					echo '<th class=\'head\'>전송구분</th>
						  <th class=\'head\'>받는사람</th>
						  <th class=\'head\'>제목</th>
						  <th class=\'head\'>일자</th>
						  <th class=\'head\'>발송</th>
						  <th class=\'head\'>개봉</th>';
					break;
				case 'to':
					echo '<th class=\'head\'>상태</th>
						  <th class=\'head\'>보낸사람</th>
						  <th class=\'head\'>제목</th>
						  <th class=\'head\'>일자</th>';
					break;
			}

			echo '<th class=\'head last\'>비고</th>';
		?>
		</tr>
	</thead>
	<tbody>
	<?
		switch($mode){
			case 'from':
				$tbl = ' msg_send ';
				$wsl = ' where msg_send.org_no      = \''.$code.'\'
						   and msg_send.msg_send_id = \''.$_SESSION['userNo'].'\'
						   and msg_send.del_flag    = \'N\'';
				break;
			case 'to':
				$tbl = ' msg_receipt ';
				$wsl = ' where msg_receipt.org_no       = \''.$code.'\'
						   and msg_receipt.msg_mem      = \''.$_SESSION['userNo'].'\'
						   and msg_receipt.msg_del_flag = \'N\'';
				break;
		}

		$sql = 'select count(*)
				  from '.$tbl.$wsl;

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

		switch($mode){
			case 'from':
				$sql = 'select cd, yymm, seq
						,      send_type
						,      concat(mem, case when mem_cnt > 1 then concat(\'외 \', mem_cnt - 1, \'명\') else \'\' end) as mem_nm
						,      subject
						,      date_format(send_dt, \'%Y.%m.%d\') as send_dt
						,      mem_cnt
						,      open_cnt
						  from (
							   select msg_send.org_no as cd
							   ,      msg_send.msg_yymm as yymm
							   ,      msg_send.msg_seq as seq
							   ,      case msg_send.msg_send_type when \'all\'    then \'전체발송\'
																  when \'branch\' then \'지사별발송\'
																  when \'center\' then \'가맹점별발송\'
																  when \'dept\'   then \'부서별발송\' else \'개인별발송\' end as send_type
							   ,      min(msg_receipt.msg_mem_nm) as mem
							   ,      count(msg_receipt.msg_mem) as mem_cnt
							   ,      msg_send.msg_subject as subject
							   ,      msg_send.msg_send_dt as send_dt
							   ,      sum(case msg_receipt.msg_open_flag when \'Y\' then 1 else 0 end) as open_cnt
								 from msg_send
								inner join msg_receipt
								   on msg_receipt.from_no  = msg_send.org_no
								  and msg_receipt.msg_yymm = msg_send.msg_yymm
								  and msg_receipt.msg_seq  = msg_send.msg_seq '.$wsl.'
								group by msg_send.org_no
							   ,         msg_send.msg_yymm
							   ,         msg_send.msg_seq
							   ,         msg_send.msg_send_type
							   ,         msg_send.msg_subject
							   ,         msg_send.msg_send_dt
							   ) as send
						 order by seq desc
						 limit '.$pageCount.','.$item_count;
				break;

			case 'to':
				$sql = 'select msg_receipt.from_no as fcd
						,      msg_receipt.org_no as cd
						,      msg_receipt.msg_yymm as yymm
						,      msg_receipt.msg_seq as seq
						,      msg_receipt.msg_open_flag as open_yn
						,      msg_send.msg_send_nm as send_nm
						,      msg_send.msg_subject as subject
						,      date_format(msg_send.msg_send_dt, \'%Y.%m.%d\') as send_dt
						  from msg_receipt
						 inner join msg_send
							on msg_send.org_no   = msg_receipt.from_no
						   and msg_send.msg_yymm = msg_receipt.msg_yymm
						   and msg_send.msg_seq  = msg_receipt.msg_seq '.$wsl.'
						 order by fcd, yymm desc, seq desc
						 limit '.$pageCount.','.$item_count;
				break;
		}

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			switch($mode){
				case 'from':
					echo '<tr>
							<td class=\'center\'><input name=\'check[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$row['seq'].'\'></td>
							<td class=\'left\'>'.$row['send_type'].'</td>
							<td class=\'left\'>'.$row['mem_nm'].'</td>
							<td class=\'left\'>'.stripslashes($row['subject']).'</td>
							<td class=\'center\'>'.$row['send_dt'].'</td>
							<td class=\'center\'>'.$row['mem_cnt'].'</td>
							<td class=\'center\'>'.$row['open_cnt'].'</td>
							<td class=\'left last\'>';
							echo '<a href=\'#\' onclick=\'msg_show("'.$row['yymm'].'","'.$row['seq'].'","");\'>보기</a> | ';
							echo '<a href=\'#\' onclick=\'msg_delete("'.$row['yymm'].'","'.$row['seq'].'","");\'>삭제</a>';
					echo '	</td>
						  </tr>';
					break;

				case 'to':
					echo '<tr>
							<td class=\'center\'><input name=\'check[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$row['seq'].'\'></td>
							<td class=\'center\'><img id=\'img_'.$row['yymm'].'_'.$row['seq'].'_'.$row['fcd'].'\' src=\'../image/msg'.($row['open_yn'] == 'Y' ? '2' : '1').'.gif\'>'.'</td>
							<td class=\'left\'>'.$row['send_nm'].'</td>
							<td class=\'left\'>'.stripslashes($row['subject']).'</td>
							<td class=\'center\'>'.$row['send_dt'].'</td>
							<td class=\'left last\'>';
							echo '<a href=\'#\' onclick=\'msg_show("'.$row['yymm'].'","'.$row['seq'].'","'.$row['fcd'].'");\'>보기</a> | ';
							echo '<a href=\'#\' onclick=\'msg_delete("'.$row['yymm'].'","'.$row['seq'].'","'.$row['fcd'].'");\'>삭제</a>';
					echo '	</td>
						  </tr>';
					break;
			}
		}

		$conn->row_free();

		if ($row_count == 0){
			echo '<tr>
					<td class=\'center last\' colspan=\'8\'>'.$myF->message('nodata','N').'</td>
				  </tr>';
		}
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
		}
	?>
	</div>
</div>

<?
	include_once("../inc/_footer.php");
?>