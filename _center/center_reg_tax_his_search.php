<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];

	$taxCrGbn = Array('C'=>'청구', 'R'=>'영수');

	$sql = 'SELECT	CAST(MID(acct_ym, 5) AS unsigned) AS month, iss_dt, cr_gbn
			FROM	cv_tax_his
			WHERE	org_no = \''.$orgNo.'\'
			AND		LEFT(acct_ym, 4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data[$row['month']]['dt'] = $row['iss_dt'];
		$data[$row['month']]['gbn'] = $taxCrGbn[$row['cr_gbn']];
	}

	$conn->row_free();


	for($i=1; $i<=12; $i++){?>
		<tr>
			<td class="center"><?=$i;?>월</td>
			<td class="center"><?=$myF->dateStyle($data[$i]['dt'],'.');?></td>
			<td class="center"><?=$data[$i]['gbn'];?></td>
			<td class="last"></td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>