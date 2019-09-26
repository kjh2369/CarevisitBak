<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	bank_no, bank_nm, bank_acct, use_yn
			,		CASE WHEN bank_gbn = \'1\' THEN \'개인\'
						 WHEN bank_gbn = \'2\' THEN \'법인\' ELSE bank_gbn END AS bank_gbn
			FROM	cv_bankbook
			WHERE	del_flag = \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td><div class="left"><?=$row['bank_nm'];?></div></td>
			<td><div class="left"><?=$row['bank_no'];?></div></td>
			<td><div class="left"><?=$row['bank_acct'];?></div></td>
			<td class="center"><?=$row['bank_gbn'];?></td>
			<td class="last"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>