<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);


	/** 청구액 및 본인부담금 *******************************************************/
		$sql = 'SELECT	t13_pay_date AS yymm
				,		SUM(t13_suga_tot4) AS longterm /*t13_chung_amt4*/
				,		SUM(t13_bonbu_tot4) AS expense
				FROM	t13sugupja
				WHERE	t13_ccode	= \''.$orgNo.'\'
				AND		t13_mkind	= \'0\'
				AND		t13_type	= \'2\'
				AND		t13_pay_date BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
				GROUP	BY t13_ccode, t13_pay_date';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$yymm = $row['yymm'];

			$data[$yymm]['LONGTERM'] = $row['longterm']; //청구액
			$data[$yymm]['EXPENSE']	 = $row['expense']; //본인부담액
		}

		$conn->row_free();


	/** 입금내역 *******************************************************/
		$sql = 'SELECT	deposit_yymm AS yymm
				,		SUM(deposit_amt) AS payment
				FROM	unpaid_deposit
				WHERE	org_no	= \''.$orgNo.'\'
				AND		del_flag= \'N\'
				AND		deposit_yymm BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
				GROUP	BY deposit_yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$yymm = $row['yymm'];

			$data[$yymm]['PAYMENT'] = $row['payment'];
		}

		$conn->row_free();


	/******************************************************************************************************************/


	if (is_array($data)){
		foreach($data as $yymm => $row){
			$unpaid = $row['EXPENSE'] - $row['PAYMENT'];?>
			<tr>
				<td class="center"><?=$myF->_styleYYMM($yymm,'.');?></td>
				<td class="center"><div class="right"><?=$row['LONGTERM'] != 0 ? number_format($row['LONGTERM']) : '';?></div></td>
				<td class="center"><div class="right"><?=$row['EXPENSE'] != 0 ? number_format($row['EXPENSE']) : '';?></div></td>
				<td class="center"><div class="right"><?=$row['PAYMENT'] != 0 ? number_format($row['PAYMENT']) : '';?></div></td>
				<td class="center"><div class="right"><?=$unpaid != 0 ? number_format($unpaid) : '';?></div></td>
				<td class="center last"></td>
			</tr><?

			$total['LONGTERM'] += $row['LONGTERM'];
			$total['EXPENSE'] += $row['EXPENSE'];
			$total['PAYMENT'] += $row['PAYMENT'];
		}

		$unpaid = $total['EXPENSE'] - $total['PAYMENT'];?>
		<!--CUT_LINE-->
		<tr>
			<td class="sum center">합계</td>
			<td class="sum center"><div class="right"><?=$total['LONGTERM'] != 0 ? number_format($total['LONGTERM']) : '';?></div></td>
			<td class="sum center"><div class="right"><?=$total['EXPENSE'] != 0 ? number_format($total['EXPENSE']) : '';?></div></td>
			<td class="sum center"><div class="right"><?=$total['PAYMENT'] != 0 ? number_format($total['PAYMENT']) : '';?></div></td>
			<td class="sum center"><div class="right"><?=$unpaid != 0 ? number_format($unpaid) : '';?></div></td>
			<td class="sum center last"></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center last" colspan="6">::검색된 데이타가 없습니다.::</td>
		</tr>
		<!--CUT_LINE--><?
	}


	Unset($data);
	Unset($total);

	include_once('../inc/_db_close.php');
?>