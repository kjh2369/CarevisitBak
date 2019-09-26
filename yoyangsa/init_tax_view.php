<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	

	$year = $_POST['year'] != '' ? $_POST['year'] : date('Y');
	
	$html = '';

	$tab = '<div class=\'left\' style=\'padding-top:2px;\'>
			<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_pre_out.gif\' style=\'cursor:pointer;\' onclick=\'moveYear(-1);\' onmouseover=\'this.src="../image/btn/btn_pre_over.gif";\' onmouseout=\'this.src="../image/btn/btn_pre_out.gif";\'></div>
			<div style=\'float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;\' id=\'m_year\'>'.$year.'</div>
			<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_next_out.gif\' style=\'cursor:pointer;\' onclick=\'moveYear(1);\' onmouseover=\'this.src="../image/btn/btn_next_over.gif";\' onmouseout=\'this.src="../image/btn/btn_next_out.gif";\'></div>
			</div>';
	$str = '년도';
		
	
	$html = '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'50px\'>
					<col width=\'85px\'>
					<col width=\'500px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th class=\'head\'>'.$str.'</th>
						<td class=\'last\'>'.$tab.'</td>
					</tr>
				</tbody>
			 </table>';
	
	$html .= '<table class="my_table" style="width:100%; border:1px solid #ccc;" >
				<colgroup>
					<col width="30px">
					<col width="70px">
					<col width="70px">
					<col>
				</colgroup>
				<thead>
				<tr>
					<th class="head">월</th>
					<th class="head">과세</th>
					<th class="head">누계</th>
				</tr>
				</thead>
				<tbody>';
	$html .= '<tr>
				 <td class="right">1월</td>
				 <td class="right"></td>
				 <td class="right"></td>
			  </tr>';
	$html .= '<tbody>
			  </table>';
	
	echo $myF->_gabSplitHtml($html);

	include_once('../inc/_db_close.php');
?>