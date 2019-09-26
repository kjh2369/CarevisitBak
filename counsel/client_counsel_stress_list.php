<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$ssn  = $ed->de($_POST['ssn']);
	$mode = 1;

	if ($_SERVER['PHP_SELF'] == '/yoyangsa/mem_counsel.php'){
		$mode = 2;
	}

	//[PHP_SELF] => /yoyangsa/mem_counsel.php
	//[PHP_SELF] => /counsel/client_counsel_stress_list.php
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col>
		<col width="145px">
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="6">불만 및 고충처리기록지</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head">접수일자</th>
			<th class="head">접수자</th>
			<th class="head">접수경로</th>
			<th class="head">불만 및 고충내용 / 처리방법</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = 'select stress_yymm as yymm
				,      stress_seq as seq
				,      stress_dt as dt
				,      stress_m_nm as m_nm
				,      case stress_rct_path when \'1\' then \'내방\'
											when \'2\' then \'방문\'
											when \'3\' then \'전화\'
											when \'4\' then \'홈페이지\'
											when \'5\' then \'서면\' else \'기타\' end as rct_path
				,      case stress_cont_kind when \'1\' then \'직원불친절\'
											 when \'2\' then \'서비스내용\'
											 when \'3\' then \'서비스비용\' else \'기타\' end as cont_kind
				,      case stress_proc_kind when \'1\' then \'경청 등 효과적 대화\'
											 when \'2\' then \'이용자방문\'
											 when \'3\' then \'서면\' else \'기타\' end as proc_kind
				  from counsel_client_stress
				 where org_no      = \''.$code.'\'
				   and stress_c_cd = \''.$ssn.'\'
				   and del_flag    = \'N\'
				 order by stress_dt desc';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';
			echo '<td class=\'center\'>'.($i+1).'</td>';
			echo '<td class=\'center\'>'.$myF->dateStyle($row['dt'],'.').'</td>';
			echo '<td class=\'left\'>'.$row['m_nm'].'</td>';
			echo '<td class=\'center\'>'.$row['rct_path'].'</td>';
			echo '<td class=\'left\'>'.$row['cont_kind'].' / '.$row['proc_kind'].'</td>';
			echo '<td class=\'left\'>';

			if ($mode == 2){
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselReg("'.$row['seq'].'","'.$row['yymm'].'");\'>수정</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselShow("'.$row['seq'].'","'.$row['yymm'].'");\'>출력</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselDel("'.$row['seq'].'","'.$row['yymm'].'");\'>삭제</button></span> ';
			}else{
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_stress_reg("'.$row['yymm'].'","'.$row['seq'].'");\'>수정</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_stress_show("'.$row['yymm'].'","'.$row['seq'].'");\'>출력</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_stress_delete("'.$row['yymm'].'","'.$row['seq'].'");\'>삭제</button></span> ';
			}
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

	echo '<input name=\'stress_yymm\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'stress_seq\'  type=\'hidden\' value=\'\'>';
?>