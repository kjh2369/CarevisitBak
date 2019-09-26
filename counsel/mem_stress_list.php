<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code	= $_POST['code'];
	$kind	= $conn->center_kind($code);
	$ssn	= $ed->de($_POST['ssn']);
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="100px">
		<col width="70px">
		<col width="375px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="6">상담이력</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head">상담일자</th>
			<th class="head">상담자</th>
			<th class="head">상담유형</th>
			<th class="head">처리결과</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select stress_ssn
				,      stress_seq
				,      stress_dt
				,      stress_talker_nm
				,      case stress_type when '1' then '내방'
										when '2' then '방문'
										when '3' then '전화'
										else '-' end as stress_type
				,      stress_result
				  from counsel_stress
				 where org_no     = '$code'
				   and stress_ssn = '$ssn'
				   and del_flag   = 'N'
				 order by stress_dt desc";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$no     = $i + 1;
			$seq    = $row['stress_seq'];
			$date   = $myF->dateStyle($row['stress_dt'],'.');
			$talker = $row['stress_talker_nm'];
			$type   = $row['stress_type'];
			$result = stripslashes($row['stress_result']);

			//$link   = '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_stress_reg('.$seq.');\'>수정</button></span>
			//		   <span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_stress_show('.$seq.');\'>출력</button></span>
			//		   <span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_stress_del('.$seq.');\'>삭제</button></span>';
			$link   = '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselReg('.$seq.');\'>수정</button></span>
					   <span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselShow('.$seq.');\'>출력</button></span>
					   <span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfCounselDel('.$seq.');\'>삭제</button></span>';

			echo "<tr>";
			echo "<td class='center'>$no</td>";
			echo "<td class='center'>$date</td>";
			echo "<td class='left'>$talker</td>";
			echo "<td class='center'>$type</td>";
			echo "<td class='left'><div class='nowrap' style='width:375px;'>$result</div></td>";
			echo "<td class='left last'>$link</td>";
			echo "</tr>";
		}

		$conn->row_free();

		if ($row_count == 0){
			echo '<tr>
					<td class=\'center\' colspan=\'6\'>'.$myF->message('nodata', 'N').'</td>
				  </tr>';
		}
	?>
	</tbody>
</table>
<?
	include_once("../inc/_db_close.php");

	echo '<input name=\'para_m_cd\'   type=\'hidden\' value=\''.$ed->en($ssn).'\'>';
	echo '<input name=\'para_seq\'    type=\'hidden\' value=\''.$seq.'\'>';
	echo '<input name=\'report_id\'   type=\'hidden\' value=\'MEMTR\'>';
?>