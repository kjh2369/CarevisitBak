<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$kind  = $_POST['kind'];
	$seq   = $_POST['seq'];
	$today = date('Y-m-d', mktime());

	$sql = 'select ies_seq as seq
			,      ies_ins_cd as cd
			,      ies_ins_nm as nm
			,      ies_from_dt as f_dt
			,      ies_to_dt as t_dt
			  from ies_center
			 where org_no   = \''.$code.'\'
			   and ies_kind = \''.$kind.'\'
			   and del_flag = \'N\'
			 order by f_dt desc, t_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	echo '<table class=\'my_table\' style=\'width:100%;\'>
			<colgroup>
				<col width=\'40px\'>
				<col width=\'200\'>
				<col width=\'130px\'>
				<col>
			</colgroup>
			<tbody>';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		echo '<tr>
				<td class=\'center\'>'.($i+1).'</td>
				<td class=\'center\'><div class=\'left nowrap\' style=\'width:200px;\'>'.$row['nm'].'</div></td>
				<td class=\'center\'>'.$myF->dateStyle($row['f_dt'],'.').'~'.$myF->dateStyle($row['t_dt'],'.').'</td>
				<td class=\'center last\'><div class=\'left\' style=\'color:#ff0000; font-size:9px;\'>'.($seq == $row['seq'] ? 'now' : '').'</div></td>
			  </tr>';

		if ($today >= $row['f_dt'] && $today <= $row['t_dt']){
			echo '<input id=\'nowIesCD\' name=\'nowIesCD\' type=\'hidden\' value=\''.$row['cd'].'\'>
				  <input id=\'nowIesNm\' name=\'nowIesNm\' type=\'hidden\' value=\''.$row['nm'].'\'>
				  <input id=\'nowIesFrom\' name=\'nowIesFrom\' type=\'hidden\' value=\''.$row['f_dt'].'\'>
				  <input id=\'nowIesTo\' name=\'nowIesTo\' type=\'hidden\' value=\''.$row['t_dt'].'\'>';
		}
	}

	$conn->row_free();

	echo '</tbody>
		  </table>';

	include_once('../inc/_db_close.php');
?>