<?
	
	$code = $_GET['code'];
	$board_type = $_GET['type'];
	$board_id = $_GET['board_id'];

	$sql = 'select board_center
			,	   board_id
			,      subject
			,	   content
			,      reg_date
			,      reg_time
			,      reg_name
			,      reply_count
		      from tbl_board
			 where board_center = \''.$code.'\'
			   and board_type   = \''.$board_type.'\'
			   and board_id     = \''.$board_id.'\'';
	
	$mst = $conn -> get_array($sql); 
	
	$sql = 'SELECT	board_seq
			,		file_name
			,		file_size
			,		file_type
			FROM	tbl_board_file
			WHERE	board_center= \''.$code.'\'
			AND		board_type	= \''.$board_type.'\'
			AND		board_id	= \''.$board_id.'\'';

	$file = $conn->_fetch_array($sql,'board_seq');


	$html = '';

	$html .= '<table class="view_type" cellspacing="0" border="1" summary="게시판 상세내용: 제목,작성자,등록일,조회수,첨부파일">  
				<caption>게시판 상세내용</caption>  
				<colgroup>  
					<col width="100"/>  
					<col width="*" /> 
				</colgroup>  
				<thead>  
					<tr>  
						<td colspan="2" class="bbs_title"><h3>'.stripslashes($mst['subject']).'</h3></td>  
					</tr>
					<tr>
						<th>첨부파일</th>
						<td class="left last">
							<a href="./download.php?type='.$board_type.'&id='.$board_id.'&seq=1">'.$file['1']['file_name'].($file['1']['file_name'] ? '('.$myF->getFileSize($file['1']['file_size']).')' : '').'</a>
							<a href="./download.php?type='.$board_type.'&id='.$board_id.'&seq=1">'.$file['2']['file_name'].($file['2']['file_name'] ? '('.$myF->getFileSize($file['2']['file_size']).')' : '').'</a>
							<a href="./download.php?type='.$board_type.'&id='.$board_id.'&seq=1">'.$file['3']['file_name'].($file['3']['file_name'] ? '('.$myF->getFileSize($file['3']['file_size']).')' : '').'</a>
						</td>
					</tr>
				</thead>  
				<tbody>';
	$html .= ' <tr>
				<th>등록일</th>
				<td>'.$mst['reg_date'].' '.$mst['reg_time'].'</td>
			  </tr>
			  <tr>
				<th>작성자</th>
				<td>'.$mst['reg_name'].'</td>
			  </tr>
			  <tr>
				<td class="cont" colspan="2" style="height:250px; vertical-align:top;">'.$mst['content'].'</td>
			  </tr>';		
	$html .= '	</tbody>
			 </table>';
	
	if($_GET['homepage'] == 'fw'){
		$html .= '<div align="right" style="margin-top:10px;"><a href="../community/index.php?mtype='.$_GET['mtype'].'&mode=1&logo=N" class="tbl_btn1" >목록보기</a></div>';
	}else {
		$html .= '<div align="right" style="margin-top:10px;"><a href="../work/index.php?mtype='.$_GET['mtype'].'&mode=1" ><img alt="목록보기" src="/bbs/img/btn_list.gif" /></a></div>';
	}

	echo $html;

	unset($html);

	$conn->close();
?>
