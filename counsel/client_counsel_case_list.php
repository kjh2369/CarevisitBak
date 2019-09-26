<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$ssn  = $ed->de($_POST['ssn']);

	$mode = 1;

	if ($myF->_self() == 'mem_counsel'){
		$mode = 2;
	}
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="80px">
		<col width="150px">
		<col>
		<col width="145px">
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="6">사례관리 회의</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head">회의일자</th>
			<th class="head">주관자</th>
			<th class="head">서비스명</th>
			<th class="head">주요문제점</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = 'select case_yymm as yymm
				,      case_seq as seq
				,      case_dt as dt
				,      case_run_nm as run_nm
				,      case_svc_kind as svc_kind
				,      case_main_quest as quest
				  from counsel_client_case
				 where org_no    = \''.$code.'\'
				   and case_c_cd = \''.$ssn.'\'
				   and del_flag  = \'N\'
				 order by case_dt desc';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$svc_kind = explode('/',$row['svc_kind']);
			$svc_nm   = '';

			foreach($svc_kind as $svc_i => $svc_cd){
				if ($svc_cd != ''){
					$svc_nm .= (!empty($svc_nm) ? ', ' : '');
					$svc_nm .= $conn->kind_name_svc($svc_cd);
				}
			}

			echo '<tr>';
			echo '<td class=\'center\'>'.($i+1).'</td>';
			echo '<td class=\'center\'>'.$myF->dateStyle($row['dt'],'.').'</td>';
			echo '<td class=\'left\'>'.$row['run_nm'].'</td>';
			echo '<td class=\'left\'><div class=\'nowrap\' style=\'width:150px;\' title=\''.$svc_nm.'\'>'.$svc_nm.'</div></td>';
			echo '<td class=\'left\'><div class=\'nowrap\' style=\'width:310px;\' title=\''.$row['quest'].'\'>'.$row['quest'].'</div></td>';
			echo '<td class=\'left\'>';

			if ($mode == 2){
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselReg("'.$row['seq'].'","'.$row['yymm'].'");\'>수정</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselShow("'.$row['seq'].'","'.$row['yymm'].'");\'>출력</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselDel("'.$row['seq'].'","'.$row['yymm'].'");\'>삭제</button></span> ';
			}else{
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_case_reg("'.$row['yymm'].'","'.$row['seq'].'");\'>수정</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_case_show("'.$row['yymm'].'","'.$row['seq'].'");\'>출력</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_case_delete("'.$row['yymm'].'","'.$row['seq'].'");\'>삭제</button></span> ';
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

	echo '<input name=\'case_yymm\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'case_seq\'  type=\'hidden\' value=\'\'>';
?>