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
				<col width="60px">
				<col width="50px">
				<col width="50px">
				<col width="50px">
				<col width="60px">
				<col>
			</colgroup>
			<tbody>';


	$sql = 'select ms_from_dt as from_dt
			,      ms_to_dt as to_dt
			,      ms_salary as pay
			,      ms_care_yn as care_yn
			,      ms_extra_yn as extra_yn
			,      ms_20day_yn AS day20_yn
			,		ms_dealpay AS dealpay
			,      case when ms_from_dt <= date_format(now(), \'%Y%m\') and ms_to_dt >= date_format(now(), \'%Y%m\') then \'setY\' else \'setN\' end as isSet
			  from mem_salary
			 where org_no   = \''.$code.'\'
			   and ms_jumin = \''.$jumin.'\'
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
				<td class="center"><div class="right '.$row['isSet'].'_pay">'.number_format($row['pay']).'</div></td>
				<td class="center '.$row['isSet'].'_careYN">'.$row['care_yn'].'</td>
				<td class="center '.$row['isSet'].'_extraYN">'.$row['extra_yn'].'</td>
				<td class="center '.$row['isSet'].'_day20YN">'.$row['day20_yn'].'</td>
				<td class="center "><div class="right '.$row['isSet'].'_dealPay">'.number_format($row['dealpay']).'</div></td>
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