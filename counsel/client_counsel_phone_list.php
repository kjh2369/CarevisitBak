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
		<col width="70px">
		<col>
		<col width="145px">
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="6">전화 방문상담 기록지</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head">상담일자</th>
			<th class="head">상담자</th>
			<th class="head">상담유형</th>
			<th class="head">상담내용</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = 'select phone_yymm as yymm
				,      phone_seq as seq
				,      phone_dt as dt
				,      phone_m_nm as m_nm
				,      case phone_kind when \'1\' then \'고객\'
									   when \'2\' then \'직원\'
									   when \'3\' then \'관리자\' else \'-\' end as kind
				,      phone_contents as cont
				  from counsel_client_phone
				 where org_no     = \''.$code.'\'
				   and phone_c_cd = \''.$ssn.'\'
				   and del_flag   = \'N\'
				 order by phone_dt desc';
				 
		$conn->query($sql);
		$conn->fetch();
		
		$row_count = $conn->row_count();
		
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			
			echo '<tr>';
			echo '<td class=\'center\'>'.($i+1).'</td>';
			echo '<td class=\'center\'>'.$myF->dateStyle($row['dt'],'.').'</td>';
			echo '<td class=\'left\'>'.$row['m_nm'].'</td>';
			echo '<td class=\'center\'>'.$row['kind'].'</td>';
			echo '<td class=\'left\'>'.stripslashes($row['cont']).'</td>';
			echo '<td class=\'left\'>';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_phone_reg("'.$row['yymm'].'","'.$row['seq'].'");\'>수정</button></span> ';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_phone_show("'.$row['yymm'].'","'.$row['seq'].'");\'>출력</button></span> ';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_phone_delete("'.$row['yymm'].'","'.$row['seq'].'");\'>삭제</button></span> ';
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
				echo '<td class=\'left\' colspan=\'6\'>'.$myF->message($row_count, 'N').'</td>';
			}else{
				echo '<td class=\'center\' colspan=\'6\'>'.$myF->message('nodata', 'N').'</td>';
			}
		?>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");

	echo '<input name=\'phone_yymm\'   type=\'hidden\' value=\'\'>';
	echo '<input name=\'phone_seq\'    type=\'hidden\' value=\'\'>';
?>