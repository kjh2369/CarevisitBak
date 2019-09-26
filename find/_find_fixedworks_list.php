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
				<col width="60px">
				<col width="70px">
				<col width="70px">
				<col>
			</colgroup>
			<tbody>';


	$sql = 'select fw_from_dt as from_dt
			,      fw_to_dt as to_dt
			,      fw_hours as hours
			,      fw_hourly as hourly
			,      case when fw_from_dt <= date_format(now(), \'%Y%m\') and fw_to_dt >= date_format(now(), \'%Y%m\') then \'setY\' else \'setN\' end as isSet
			  from fixed_works
			 where org_no   = \''.$code.'\'
			   and fw_jumin = \''.$jumin.'\'
			   and del_flag = \'N\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		echo '<tr>
				<td class="center '.$row['isSet'].'_fromDt">'.$myF->_styleYYMM($row['from_dt'],'.').'</td>
				<td class="center '.$row['isSet'].'_toDt">'.$myF->_styleYYMM($row['to_dt'],'.').'</td>
				<td class="center"><div class="right '.$row['isSet'].'_hours">'.$row['hours'].'</div></td>
				<td class="center"><div class="right '.$row['isSet'].'_hourly">'.number_format($row['hourly']).'</div></td>
				<td class="center"><div class="left">'.($i == 0 ? '<span class="btn_pack m"><button type="button" onclick="rowDelete();">삭제</button></span>' : '').'</div></td>
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