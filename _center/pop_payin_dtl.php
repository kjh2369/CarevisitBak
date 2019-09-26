<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_GET['orgNo'];
	$issueDt = $_GET['issueDt'];
	$issueSeq = $_GET['issueSeq'];

	$sql = 'SELECT	m00_store_nm
			FROm	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'';

	$orgName = $conn->get_data($sql);


	$sql = 'SELECT	in_amt
			FROM	cv_pay_in
			WHERE	org_no		= \''.$orgNo.'\'
			AND		issue_dt	= \''.$issueDt.'\'
			AND		issue_seq	= \''.$issueSeq.'\'
			AND		del_flag	= \'N\'';

	$inAmt = $conn->get_data($sql);
?>
<div class="title title_border">입금상세내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td class="left"><?=$orgNo;?></td>
			<th class="center">기관명</th>
			<td class="left"><?=$orgName;?></td>
		</tr>
		<tr>
			<th class="center">입금일자</th>
			<td class="left"><?=$myF->dateStyle($issueDt, '.');?></td>
			<th class="center">입금액</th>
			<td class="left"><?=number_format($inAmt);?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">사용년월</th>
			<th class="head">청구년월</th>
			<th class="head">입금금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div style="overflow-x:hidden; overflow-y:scroll; height:280px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col>
		</colgroup>
		<tbody><?
		$sql = 'SELECT	use_yymm, claim_yymm, in_amt
				FROM	cv_pay_in_dtl
				WHERE	org_no		= \''.$orgNo.'\'
				AND		issue_dt	= \''.$issueDt.'\'
				AND		issue_seq	= \''.$issueSeq.'\'
				AND		del_flag	= \'N\'
				ORDER	BY use_yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr>
			<td class="center"><?=$myF->_styleYymm($row['use_yymm'], '.');?></td>
			<td class="center"><?=$myF->_styleYymm($row['claim_yymm'], '.');?></td>
			<td class="center"><div class="right"><?=number_format($row['in_amt']);?></div></td>
			<td class="center"></td>
			</tr><?
		}

		$conn->row_free();?>
		</tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>