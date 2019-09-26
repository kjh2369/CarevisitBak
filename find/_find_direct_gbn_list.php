<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);


	ob_start();


	echo '<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="60px">
				<col width="90px">
				<col>
			</colgroup>
			<tbody>';


	$sql = 'select yymm
			,      gbn
			  from mem_direct_gbn
			 where org_no   = \''.$code.'\'
			   and jumin = \''.$jumin.'\'
			 order by yymm desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		
		$isSet = ($i==0 ? 'setY' : 'setN');

		echo '<tr>
				<td class="center '.$isSet.'_yymm">'.$myF->_styleYYMM($row['yymm'],'.').'</td>
				<td class="center" ><div class="left '.$isSet.'_gbn">'.($row['gbn']=='1'?'직접인건비':'간접인건비').'</div></td>
				<td class="center"><div class="left">'.($i == 0 ? '<span class="btn_pack m"><button type="button" onclick="rowDelete(\''.$row['yymm'].'\');">삭제</button></span>' : '').'</div></td>
			  </tr>';
	}

	$conn->row_free();

	echo '	</tbody>
		  </table>';


	$html = ob_get_contents();

	ob_clean();

	$html = $myF->_gabSplitHtml($html);

	echo $html;


	include_once('../inc/_db_close.php');
?>