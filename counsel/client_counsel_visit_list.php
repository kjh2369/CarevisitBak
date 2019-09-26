<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$ssn  = $ed->de($_POST['ssn']);
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="100px">
		<col>
		<col width="145px">
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="5">고객 방문상담 기록지</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head">상담일자</th>
			<th class="head">상담자</th>
			<th class="head">상담내용</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = 'select visit_yymm as yymm
				,      visit_seq as seq
				,      visit_dt as dt
				,      visit_m_nm as m_nm
				  from counsel_client_visit
				 where org_no     = \''.$code.'\'
				   and visit_c_cd = \''.$ssn.'\'
				   and del_flag   = \'N\'
				 order by visit_dt desc';
				 
		$conn->query($sql);
		$conn->fetch();
		
		$row_count = $conn->row_count();
		
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			
			echo '<tr>';
			echo '<td class=\'center\'>'.($i+1).'</td>';
			echo '<td class=\'center\'>'.$myF->dateStyle($row['dt'],'.').'</td>';
			echo '<td class=\'left\'>'.$row['m_nm'].'</td>';
			echo '<td class=\'left\'></td>';
			echo '<td class=\'left\'>';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_visit_reg("'.$row['yymm'].'","'.$row['seq'].'");\'>수정</button></span> ';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_visit_show("'.$row['yymm'].'","'.$row['seq'].'");\'>출력</button></span> ';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_visit_delete("'.$row['yymm'].'","'.$row['seq'].'");\'>삭제</button></span> ';
			echo '</td>';
			echo '</tr>';
		}
		
		$conn->row_free();
	?>
	</tbody>
	<tfoot>
		<tr>
		<?
			if ($row_count > 0){
				echo '<td class=\'left\' colspan=\'5\'>'.$myF->message($row_count, 'N').'</td>';
			}else{
				echo '<td class=\'center\' colspan=\'5\'>'.$myF->message('nodata', 'N').'</td>';
			}
		?>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");

	echo '<input name=\'visit_yymm\'   type=\'hidden\' value=\'\'>';
	echo '<input name=\'visit_seq\'    type=\'hidden\' value=\'\'>';
?>