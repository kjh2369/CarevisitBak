<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$orgNo = $_SESSION['userCenterCode'];

	$sql = 'SELECT	LEFT(from_dt, 6) AS from_ym, LEFT(to_dt, 6) AS to_ym, bill_kind
			FROM	cv_bill_info
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		del_flag = \'N\'
			';
	$bill_info = $conn->_fetch_array($sql);

	$sql = 'SELECT	yymm, dft_amt
			FROM	tmp_dft_amt
			WHERE	org_no = \''.$orgNo.'\'
			ORDER	BY yymm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$claimYm = $myF->_styleYymm($myF->dateAdd('month', 1, $row['yymm'].'01', 'Ym'), '.');
		$useYm = $myF->_styleYymm($row['yymm'], '.');

		for($j=0; $j<count($bill_info); $j++){
			if ($bill_info[$j]['from_ym'] <= $row['yymm'] && $bill_info[$j]['to_ym'] >= $row['yymm']){
				if ($bill_info[$j]['bill_kind'] == '1'){
					$useYm = $claimYm;
					break;
				}
			}
		}

		?>
		<tr>
		<td class="center bold"><?=$claimYm;?></td>
		<td class="center bold"><?=$useYm;?></td>
		<td class="center bold"><div class="right"><?=number_format($row['dft_amt']);?></div></td>
		<td class="left"><?
			if ($_SESSION['USER_LOC'] == 'admin'){?>
				<span class="btn_pack small"><button onclick="lfDelete('<?=$row['yymm'];?>');">삭제</button></span><?
			}?>
		</td>
		</tr><?
	}

	$conn->row_free();

	include("../inc/_db_close.php");
?>