<?
	
	$sql = 'select board_center
			,	   board_id
			,      subject
			,      reg_date
			,      reg_time
			,      reg_name
			,      reply_count
		      from tbl_board
			 where board_center = \''.$_GET['code'].'\'
			   and board_type   = \''.$_GET['type'].'\'
			   and reply_id     = \'0\'
			 order by reg_date desc, reg_time desc';
	$conn -> query($sql);
	$conn -> fetch();
	$rowCount = $conn -> row_count();

	$html = '';

	$html .= '<table class="list_type" width="100%">
						<colgroup>
							<col width="40px;">
							<col width="*">
							<col width="150px;">
							<col width="100px;">
						</colgroup>
						<thead>
							<tr>
								<th>No</th>
								<th>제목</th>
								<th>작성일</th>
								<th>작성자</th>
							</tr>
						</thead>
						<tbody>';

	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn-> select_row($i);
			
		
			$html .= '  <tr>
							<td>'.($i+1).'</td>
							<td style="text-align:left; padding-left:5px;" ><div  class="nowrap" style="width:380px;"><a href="../'.$m_title.'/index.php?mtype='.$_GET['mtype'].'&mode=2&board_id='.$row['board_id'].'&logo=N">'.$row['subject'].'</a></div></td>
							<td>'.$row['reg_date'].' '.$row['reg_time'].'</td>
							<td>'.$row['reg_name'].'</td>
						</tr>';
					   
		}
	}else {
		$html .= '<tr>
					<td colspan="4">:: 검색된 데이터가 없습니다 ::</td>
				</tr>';
	}
	
	$html .= '	</tbody>
			 </table>';

	$conn -> row_free();

	echo $html;

	unset($html);

	$conn->close();
?>
